<?php

namespace App\Console\Commands;

use App\Models\AiBatchJob;
use App\Models\Review;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchReviewModerationBatch extends Command
{
    protected $signature   = 'moderation:dispatch-batch';
    protected $description = 'Dispatch a GPT-5.6-Luna Batch API job for reviews awaiting Layer 3 moderation.';

    public function handle(): int
    {
        $apiKey    = config('openai.api_key');
        $model     = config('openai.review_ai_model', 'gpt-5.6-luna');
        $promptId  = config('openai.review_prompt_id');
        $promptVer = config('openai.review_prompt_version', '1');
        $reasoning = config('openai.review_reasoning_effort', 'low');
        $locales   = implode("\n", config('openai.supported_locales', []));

        if (!$apiKey) {
            $this->error('OPENAI_API_KEY is not set in .env. Aborting batch dispatch.');
            return self::FAILURE;
        }

        if (!$promptId) {
            $this->warn('OPENAI_REVIEW_PROMPT_ID is not set. Aborting batch dispatch.');
            return self::FAILURE;
        }

        // Gather reviews ready for Layer 3
        $reviews = Review::readyForBatch()->with(['translations', 'business.translations', 'user'])->get();

        if ($reviews->isEmpty()) {
            $this->info('No reviews ready for Layer 3 batch. Nothing to dispatch.');
            return self::SUCCESS;
        }

        $this->info("Found {$reviews->count()} review(s) to include in batch.");

        // Build JSONL batch payload
        $lines = [];
        foreach ($reviews as $review) {
            $translation  = $review->translations()->first();
            $bizName      = $review->business?->translations?->first()?->name ?? 'Unknown';
            $category     = $review->business?->category?->name ?? 'Software';
            $reviewTitle  = $translation?->title ?? '';
            $reviewText   = $translation?->description ?? '';
            $submittedLocale = static::normalizeLocale($review->submitted_locale ?? 'en-us');

            $accountAge      = $review->user ? now()->diffInDays($review->user->created_at) : 0;
            $previousReviews = Review::where('user_id', $review->user_id)
                ->where('id', '!=', $review->id)
                ->whereIn('status', [Review::STATUS_PUBLISHED, Review::STATUS_REMOVED, Review::STATUS_REJECTED])
                ->count();

            $lines[] = json_encode([
                'custom_id' => 'review-' . $review->id,
                'method'    => 'POST',
                'url'       => '/v1/responses',
                'body'      => [
                    'model'    => $model,
                    'reasoning' => ['effort' => $reasoning],
                    'prompt'   => [
                        'id'      => $promptId,
                        'version' => $promptVer,
                        'variables' => [
                            'business_name'              => $bizName,
                            'category'                   => $category,
                            'rating'                     => (string) $review->rating,
                            'review_title'               => $reviewTitle,
                            'review_text'                => $reviewText,
                            'submitted_locale'           => $submittedLocale,
                            'supported_locales'          => $locales,
                            'reviewer_account_age_days'  => (string) $accountAge,
                            'previous_review_count'      => (string) $previousReviews,
                            'invitation_source'          => 'organic',
                        ],
                    ],
                ],
            ]);
        }

        $jsonl = implode("\n", $lines);

        // Upload JSONL file to OpenAI
        try {
            $uploadResponse = Http::withToken($apiKey)
                ->attach('file', $jsonl, 'reviews_batch.jsonl', ['Content-Type' => 'application/jsonl'])
                ->post('https://api.openai.com/v1/files', ['purpose' => 'batch']);

            if (!$uploadResponse->successful()) {
                $this->error('Failed to upload batch file: ' . $uploadResponse->body());
                return self::FAILURE;
            }

            $fileId = $uploadResponse->json('id');
            $this->info("Uploaded batch file: {$fileId}");
        } catch (\Throwable $e) {
            $this->error('Exception uploading batch file: ' . $e->getMessage());
            Log::error('moderation:dispatch-batch upload error: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Submit the batch job
        try {
            $batchResponse = Http::withToken($apiKey)
                ->post('https://api.openai.com/v1/batches', [
                    'input_file_id'    => $fileId,
                    'endpoint'         => '/v1/responses',
                    'completion_window' => '24h',
                ]);

            if (!$batchResponse->successful()) {
                $this->error('Failed to create batch: ' . $batchResponse->body());
                return self::FAILURE;
            }

            $batchId = $batchResponse->json('id');
            $this->info("Batch created: {$batchId}");
        } catch (\Throwable $e) {
            $this->error('Exception creating batch: ' . $e->getMessage());
            Log::error('moderation:dispatch-batch create error: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Save batch job record
        $batchJob = AiBatchJob::create([
            'provider'      => 'openai',
            'batch_id'      => $batchId,
            'prompt_type'   => 'review_moderation',
            'status'        => 'processing',
            'submitted_at'  => now(),
            'total_items'   => $reviews->count(),
        ]);

        // Mark all reviews as processing in Layer 3
        foreach ($reviews as $review) {
            $review->update([
                'localio_ai_status'  => Review::AI_STATUS_PROCESSING,
                'localio_ai_batch_id' => $batchId,
            ]);
        }

        $this->info("✅ Batch dispatched: {$batchId} with {$reviews->count()} reviews.");
        Log::info("moderation:dispatch-batch: Dispatched batch {$batchId} with {$reviews->count()} reviews.");

        return self::SUCCESS;
    }

    /**
     * Normalize Localio internal locale (en-us) to BCP-47 (en-US).
     */
    protected static function normalizeLocale(string $locale): string
    {
        $parts = explode('-', $locale, 2);
        if (count($parts) === 2) {
            return strtolower($parts[0]) . '-' . strtoupper($parts[1]);
        }
        return strtolower($locale);
    }
}
