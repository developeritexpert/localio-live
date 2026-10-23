<?php

namespace App\Console\Commands;

use App\Models\TranslationBatch;
use App\Models\WebSetting;
use App\Services\ReviewTranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchReviewTranslationBatch extends Command
{
    protected $signature   = 'translations:dispatch-batch {--limit=500}';
    protected $description = 'Dispatch pending review translations to OpenAI Batch API (gpt-5.6-luna) or create local batch for ChatGPT.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit') ?: 500;
        $batch = ReviewTranslationService::createBatchFromWaiting($limit);

        if (!$batch) {
            $this->info('No waiting review translations found to dispatch.');
            return self::SUCCESS;
        }

        $this->info("Created translation batch #{$batch->id} with {$batch->request_count} item(s).");

        $apiKey = config('openai.api_key') ?: optional(WebSetting::where('key', 'openai_api_key')->first())->value;
        if (!$apiKey) {
            $this->warn('OPENAI_API_KEY is not configured. Batch #{$batch->id} is ready for manual JSON download/export in Admin.');
            return self::SUCCESS;
        }

        $this->info('Submitting batch to OpenAI Batch API...');
        try {
            $payload = ReviewTranslationService::buildCleanBatchPayload($batch);
            $jsonlLines = [];

            foreach ($payload['requests'] as $req) {
                $messages = [
                    ['role' => 'system', 'content' => "You are a professional localizer. Translate the provided review title and text accurately from {$req['source_locale']} to {$req['target_locale']}. Respond ONLY in valid JSON: {\"translated_title\": \"...\", \"translated_text\": \"...\"}"],
                    ['role' => 'user', 'content' => "Review Title: {$req['review_title']}\n\nReview Text: {$req['review_text']}"],
                ];

                $jsonlLines[] = json_encode([
                    'custom_id' => $req['custom_id'],
                    'method'    => 'POST',
                    'url'       => '/v1/chat/completions',
                    'body'      => [
                        'model'       => $batch->model ?: 'gpt-5.6-luna',
                        'messages'    => $messages,
                        'temperature' => 0.3,
                    ]
                ]);
            }

            $jsonlContent = implode("\n", $jsonlLines);
            $tempFile = tempnam(sys_get_temp_dir(), 'trans_batch_') . '.jsonl';
            file_put_contents($tempFile, $jsonlContent);

            $fileResp = Http::withToken($apiKey)
                ->timeout(60)
                ->attach('file', fopen($tempFile, 'r'), 'batch_input.jsonl')
                ->post('https://api.openai.com/v1/files', ['purpose' => 'batch']);

            @unlink($tempFile);

            if (!$fileResp->successful()) {
                $this->error('Failed to upload JSONL file to OpenAI: ' . $fileResp->body());
                return self::FAILURE;
            }

            $fileId = $fileResp->json('id');

            $batchResp = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/batches', [
                    'input_file_id'     => $fileId,
                    'endpoint'          => '/v1/chat/completions',
                    'completion_window' => '24h',
                    'metadata'          => [
                        'batch_id' => (string) $batch->id,
                        'type'     => 'review_translation',
                    ]
                ]);

            if ($batchResp->successful()) {
                $openaiBatchId = $batchResp->json('id');
                $batch->update([
                    'openai_batch_id' => $openaiBatchId,
                    'status'          => 'submitted',
                    'submitted_at'    => now(),
                ]);
                $this->info("Successfully submitted batch to OpenAI. OpenAI Batch ID: {$openaiBatchId}");
                return self::SUCCESS;
            } else {
                $this->error('Failed to create batch on OpenAI: ' . $batchResp->body());
                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error('Exception submitting batch: ' . $e->getMessage());
            Log::error('Review translation batch dispatch failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}