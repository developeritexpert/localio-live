<?php

namespace App\Console\Commands;

use App\Models\AiBatchJob;
use App\Models\Review;
use App\Models\WebSetting;
use App\Services\ReviewModerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchReviewModerationBatch extends Command
{
    protected $signature   = 'moderation:dispatch-batch';
    protected $description = 'Dispatch a GPT-5.6-Luna Batch API job for reviews awaiting Layer 3 moderation.';

    public function handle(): int
    {
        $apiKey    = config('openai.api_key') ?: optional(WebSetting::where('key', 'openai_api_key')->first())->value;
        $model     = config('openai.review_ai_model', 'gpt-5.6-luna');
        $promptId  = config('openai.review_prompt_id');
        $promptVer = config('openai.review_prompt_version', '1');
        $reasoning = config('openai.review_reasoning_effort', 'low');
        $supportedLocales = config('openai.supported_locales', []);
        $localesText = implode("\n", $supportedLocales);

        // Gather reviews ready for Layer 3
        $reviews = Review::readyForBatch()->with(['translations', 'business.translations', 'user'])->get();

        if ($reviews->isEmpty()) {
            $this->info('No reviews ready for Layer 3 batch. Nothing to dispatch.');
            return self::SUCCESS;
        }

        $this->info("Found {$reviews->count()} review(s) to include in batch.");

        $useStoredPrompt = !empty($promptId);
        $endpoint = $useStoredPrompt ? '/v1/responses' : '/v1/chat/completions';

        // Build system prompt for fallback / direct execution
        $systemPrompt = $this->buildFallbackSystemPrompt($localesText);

        // Build JSONL batch payload
        $lines = [];
        $payloadItems = [];

        foreach ($reviews as $review) {
            $translation  = $review->translations()->first();
            $bizName      = $review->business?->translations?->first()?->name ?? 'Unknown';
            $category     = $review->business?->category?->name ?? 'Software';
            $reviewTitle  = $translation?->title ?? '';
            $reviewText   = $translation?->description ?? '';
            $submittedLocale = ReviewModerationService::normalizeLocale($review->submitted_locale ?? 'en-US');

            $accountAge      = $review->user ? now()->diffInDays($review->user->created_at) : 0;
            $previousReviews = Review::where('user_id', $review->user_id)
                ->where('id', '!=', $review->id)
                ->whereIn('status', [Review::STATUS_PUBLISHED, Review::STATUS_REMOVED, Review::STATUS_REJECTED, 'active'])
                ->count();

            // Calculate heuristic risk signals for GPT context
            $sameIpReviewsCount = 0;
            if (!empty($review->ip_address)) {
                $sameIpReviewsCount = Review::where('ip_address', $review->ip_address)
                    ->where('id', '!=', $review->id)
                    ->count();
            }
            $ipRiskSignal = $sameIpReviewsCount >= 3 ? 'HIGH' : ($sameIpReviewsCount >= 1 ? 'MEDIUM' : 'LOW');

            // Business relationship signal
            $bizRelSignal = 'NONE';
            if ($review->user) {
                if ($review->user->business_id == $review->business_id) {
                    $bizRelSignal = 'OWNER_OR_STAFF';
                } elseif (in_array($review->user->user_type ?? '', ['vendor', 'admin', 'super_admin'])) {
                    $bizRelSignal = 'VENDOR_ACCOUNT';
                }
            }

            // Duplicate similarity signal check
            $duplicateSignal = 'NONE';
            if (mb_strlen($reviewText) >= 20) {
                $duplicateExists = Review::where('id', '!=', $review->id)
                    ->whereHas('translations', function ($q) use ($reviewText) {
                        $q->where('description', 'like', mb_substr($reviewText, 0, 50) . '%');
                    })
                    ->exists();
                if ($duplicateExists) {
                    $duplicateSignal = 'HIGH_SIMILARITY_MATCH';
                }
            }

            $variables = [
                'business_name'                => $bizName,
                'category'                     => $category,
                'rating'                       => (string) $review->rating,
                'review_title'                 => $reviewTitle,
                'review_text'                  => $reviewText,
                'submitted_locale'             => $submittedLocale,
                'supported_locales'            => $localesText,
                'reviewer_account_age'         => (string) $accountAge,
                'previous_review_count'        => (string) $previousReviews,
                'invitation_source'            => 'organic',
                'duplicate_similarity_signal'  => $duplicateSignal,
                'ip_risk_signal'               => $ipRiskSignal,
                'device_risk_signal'           => 'NORMAL',
                'business_relationship_signal' => $bizRelSignal,
            ];

            if ($useStoredPrompt) {
                $requestBody = [
                    'model'     => $model,
                    'reasoning' => ['effort' => $reasoning],
                    'prompt'    => [
                        'id'        => $promptId,
                        'version'   => $promptVer,
                        'variables' => $variables,
                    ],
                ];
            } else {
                // Fallback structured system prompt on /v1/chat/completions
                $requestBody = [
                    'model'            => $model,
                    'reasoning_effort' => $reasoning,
                    'response_format'  => ['type' => 'json_object'],
                    'messages'         => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => json_encode($variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)],
                    ],
                ];
            }

            $lineData = [
                'custom_id' => 'review-' . $review->id,
                'method'    => 'POST',
                'url'       => $endpoint,
                'body'      => $requestBody,
            ];

            $lines[] = json_encode($lineData);
            $payloadItems[] = [
                'review_id'  => $review->id,
                'business'   => $bizName,
                'custom_id'  => 'review-' . $review->id,
                'variables'  => $variables,
            ];
        }

        $jsonl = implode("\n", $lines);

        // If no API key configured, store batch locally in pending status for manual download/offline run
        if (!$apiKey) {
            $localBatchId = 'batch_local_' . strtoupper(bin2hex(random_bytes(6)));
            AiBatchJob::create([
                'provider'      => 'openai',
                'batch_id'      => $localBatchId,
                'prompt_type'   => 'review_moderation',
                'status'        => 'pending',
                'submitted_at'  => now(),
                'total_items'   => $reviews->count(),
                'input_payload' => json_encode([
                    'batch_id'    => $localBatchId,
                    'endpoint'    => $endpoint,
                    'model'       => $model,
                    'prompt_type' => 'review_moderation',
                    'review_ids'  => $reviews->pluck('id')->toArray(),
                    'jsonl_lines' => $lines,
                    'items'       => $payloadItems,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);

            foreach ($reviews as $review) {
                $review->update([
                    'localio_ai_status'   => Review::AI_STATUS_PROCESSING,
                    'localio_ai_batch_id' => $localBatchId,
                ]);
            }

            $this->warn("OPENAI_API_KEY is not set. Created local offline batch: {$localBatchId} with {$reviews->count()} reviews.");
            $this->info("You can download the input JSON from Admin > Reviews > Batch Jobs to test in ChatGPT or upload results.");
            return self::SUCCESS;
        }

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
                    'input_file_id'     => $fileId,
                    'endpoint'          => $endpoint,
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

        // Save batch job record with full payload and metadata
        AiBatchJob::create([
            'provider'      => 'openai',
            'batch_id'      => $batchId,
            'prompt_type'   => 'review_moderation',
            'status'        => 'processing',
            'submitted_at'  => now(),
            'total_items'   => $reviews->count(),
            'input_payload' => json_encode([
                'batch_id'      => $batchId,
                'endpoint'      => $endpoint,
                'model'         => $model,
                'file_id'       => $fileId,
                'prompt_type'   => 'review_moderation',
                'review_ids'    => $reviews->pluck('id')->toArray(),
                'jsonl_lines'   => $lines,
                'items'         => $payloadItems,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);

        // Mark all reviews as processing in Layer 3
        foreach ($reviews as $review) {
            $review->update([
                'localio_ai_status'   => Review::AI_STATUS_PROCESSING,
                'localio_ai_batch_id' => $batchId,
            ]);
        }

        $this->info("✔ Batch dispatched: {$batchId} with {$reviews->count()} reviews.");
        Log::info("moderation:dispatch-batch: Dispatched batch {$batchId} with {$reviews->count()} reviews.");

        return self::SUCCESS;
    }

    protected function buildFallbackSystemPrompt(string $localesText): string
    {
        return <<<PROMPT
You are the Localio Review Integrity Moderation system.
Your job is Localio review integrity analysis and review-locale verification. Harmful content has already been checked by a separate safety moderation model.

You must evaluate:
- suspected fake review
- spam
- promotional review
- rating/text mismatch
- conflict of interest
- review appears to concern another business
- suspiciously duplicated language
- potentially synthetic/AI-generated review patterns
- unverifiable/extreme factual allegations
- serious legal/criminal allegations that deserve human review
- suspicious manipulation signals (IP risk, business relationship, duplicate similarity)
- whether the language of the review matches the submitted Localio locale

Supported Localio Locales:
{$localesText}

Locale Verification Rules:
1. Always return an exact supported Localio locale for the review.
2. If review language matches submitted_locale, return:
   - "locale_status": "MATCH"
   - "detected_locale": submitted_locale
3. If review language does not match submitted_locale, but clearly matches another supported Localio locale, return:
   - "locale_status": "REASSIGNED"
   - "detected_locale": exact supported Localio locale (e.g. de-DE, es-ES, pt-BR)
4. If you cannot confidently determine which supported Localio locale applies, return:
   - "locale_status": "HUMAN_REVIEW"
   - "decision": "HUMAN_REVIEW"
5. NEVER return generic 2-letter language codes like "en", "de", "es". Return full Localio locale codes (e.g. en-US, de-DE).
6. A locale mismatch by itself does not make a review suspicious if the correct locale can be confidently identified.

Decision Rules:
- If integrity checks pass and locale_status is MATCH or REASSIGNED, return "decision": "PASS".
- If integrity issues are detected OR locale_status is HUMAN_REVIEW, return "decision": "HUMAN_REVIEW".

You must output valid JSON matching this exact structure:
{
  "decision": "PASS" | "HUMAN_REVIEW",
  "risk_level": "LOW" | "MEDIUM" | "HIGH" | "CRITICAL",
  "flags": [
    "SUSPECTED_FAKE",
    "SPAM",
    "PROMOTIONAL",
    "RATING_MISMATCH",
    "CONFLICT_OF_INTEREST",
    "WRONG_BUSINESS",
    "DUPLICATE_LANGUAGE",
    "SYNTHETIC_CONTENT",
    "LEGAL_ALLEGATION",
    "SUSPICIOUS_ACTIVITY",
    "OTHER"
  ],
  "submitted_locale": "en-US",
  "detected_locale": "en-US",
  "locale_status": "MATCH" | "REASSIGNED" | "HUMAN_REVIEW",
  "confidence": 95,
  "reason": "short internal moderation explanation"
}
PROMPT;
    }
}
