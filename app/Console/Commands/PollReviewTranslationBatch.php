<?php

namespace App\Console\Commands;

use App\Models\TranslationBatch;
use App\Models\WebSetting;
use App\Services\ReviewTranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PollReviewTranslationBatch extends Command
{
    protected $signature   = 'translations:poll-batch';
    protected $description = 'Poll OpenAI Batch API for completed review translation batches and apply translations.';

    public function handle(): int
    {
        $apiKey = config('openai.api_key') ?: optional(WebSetting::where('key', 'openai_api_key')->first())->value;
        if (!$apiKey) {
            $this->warn('OPENAI_API_KEY is not configured. Skipping automated poll.');
            return self::SUCCESS;
        }

        $batches = TranslationBatch::whereIn('status', ['submitted', 'in_progress'])
            ->whereNotNull('openai_batch_id')
            ->get();

        if ($batches->isEmpty()) {
            $this->info('No active translation batches awaiting completion.');
            return self::SUCCESS;
        }

        $this->info("Found {$batches->count()} batch(es) to check.");

        foreach ($batches as $batch) {
            try {
                $resp = Http::withToken($apiKey)
                    ->timeout(30)
                    ->get("https://api.openai.com/v1/batches/{$batch->openai_batch_id}");

                if (!$resp->successful()) {
                    $this->warn("Failed to retrieve status for batch #{$batch->id}: " . $resp->body());
                    continue;
                }

                $status = $resp->json('status');
                $outputFileId = $resp->json('output_file_id');
                $errorFileId = $resp->json('error_file_id');

                $this->info("Batch #{$batch->id} ({$batch->openai_batch_id}) status: {$status}");

                if ($status === 'completed' && $outputFileId) {
                    $fileResp = Http::withToken($apiKey)->get("https://api.openai.com/v1/files/{$outputFileId}/content");
                    if ($fileResp->successful()) {
                        $lines = explode("\n", trim($fileResp->body()));
                        $items = [];
                        foreach ($lines as $line) {
                            $data = json_decode($line, true);
                            if (!$data) continue;
                            $customId = $data['custom_id'] ?? '';
                            $contentStr = $data['response']['body']['choices'][0]['message']['content'] ?? '';
                            $parsed = json_decode($contentStr, true);
                            $items[] = [
                                'custom_id'        => $customId,
                                'translated_title' => $parsed['translated_title'] ?? null,
                                'translated_text'  => $parsed['translated_text'] ?? null,
                                'status'           => (!empty($parsed['translated_text'])) ? 'COMPLETED' : 'FAILED',
                            ];
                        }

                        $res = ReviewTranslationService::processBatchResultItems($batch, $items);
                        $this->info("Batch #{$batch->id} completed. Applied: {$res['successful']} success, {$res['failed']} failed.");
                    }
                } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
                    $batch->update([
                        'status'    => 'failed',
                        'failed_at' => now(),
                    ]);
                    $this->error("Batch #{$batch->id} marked as failed on OpenAI ({$status}).");
                }
            } catch (\Throwable $e) {
                $this->error("Exception polling batch #{$batch->id}: " . $e->getMessage());
                Log::error("Translation batch poll exception: " . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}