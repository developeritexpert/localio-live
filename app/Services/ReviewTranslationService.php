<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Review;
use App\Models\ReviewTranslation;
use App\Models\TranslationBatch;
use App\Models\WebSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReviewTranslationService
{
    /**
     * Resolve numeric language_id for a given BCP47 locale string.
     */
    public static function resolveLanguageIdForLocale(string $locale): int
    {
        $norm = strtolower(trim($locale));
        $lang = \App\Models\Language::where('lang_code', $norm)->first();
        if ($lang) {
            return (int)$lang->id;
        }
        $prefix = explode('-', $norm)[0] ?? '';
        if ($prefix) {
            $fallback = \App\Models\Language::where('lang_code', 'LIKE', $prefix . '-%')->first();
            if ($fallback) {
                return (int)$fallback->id;
            }
        }
        return 1;
    }

    /**
     * Get list of all supported target Localio BCP 47 locales.
     * Optionally exclude a specific source locale.
     */
    public static function getSupportedTargetLocales(?string $excludeLocale = null): array
    {
        $locales = config('openai.supported_locales', [
            'en-US', 'en-GB', 'en-AU', 'en-CA',
            'de-DE',
            'es-ES', 'es-419',
            'pt-BR', 'pt-PT',
            'fr-FR',
            'it-IT',
            'nl-NL',
            'pl-PL',
            'ru-RU',
            'ja-JP',
            'zh-CN', 'zh-TW',
            'ar-SA',
            'ko-KR',
            'tr-TR',
            'sv-SE',
            'da-DK',
            'fi-FI',
            'nb-NO',
        ]);

        if ($excludeLocale) {
            $normExclude = ReviewModerationService::normalizeLocale($excludeLocale);
            $locales = array_values(array_filter($locales, function ($loc) use ($normExclude) {
                return strcasecmp(ReviewModerationService::normalizeLocale($loc), $normExclude) !== 0;
            }));
        }

        return $locales;
    }

    /**
     * Queue WAITING translation records for a published review across all other supported locales.
     */
    public static function queueTranslationsForReview(Review $review): int
    {
        if (!in_array($review->status, [Review::STATUS_PUBLISHED, 'active'])) {
            return 0;
        }

        $sourceLocale = ReviewModerationService::normalizeLocale($review->source_locale ?: ($review->submitted_locale ?: 'en-US'));
        if (empty($review->source_locale)) {
            $review->update(['source_locale' => $sourceLocale]);
        }

        $targetLocales = self::getSupportedTargetLocales($sourceLocale);
        $originalTrans = $review->original ?? $review->translations()->first();
        $origTitle = $originalTrans?->title ?? '';
        $origText = $originalTrans?->description ?? '';

        $queuedCount = 0;
        $model = config('openai.review_translation_model', 'gpt-5.6-luna');
        $promptId = config('openai.review_translation_prompt_id', 'Localio Review Translation');
        $promptVersion = config('openai.review_translation_prompt_version', '1');

        foreach ($targetLocales as $targetLocale) {
            $existing = ReviewTranslation::where('reviews_id', $review->id)
                ->where('target_locale', $targetLocale)
                ->first();

            if ($existing) {
                // If it was outdated or failed, reset to WAITING for regeneration
                if (in_array($existing->status, [ReviewTranslation::STATUS_OUTDATED, ReviewTranslation::STATUS_FAILED])) {
                    $existing->update([
                        'status'         => ReviewTranslation::STATUS_WAITING,
                        'source_locale'  => $sourceLocale,
                        'model'          => $model,
                        'prompt_id'      => $promptId,
                        'prompt_version' => $promptVersion,
                    ]);
                    $queuedCount++;
                }
            } else {
                ReviewTranslation::create([
                    'reviews_id'     => $review->id,
                    'review_id'      => $review->id,
                    'language_id'    => self::resolveLanguageIdForLocale($targetLocale),
                    'source_locale'  => $sourceLocale,
                    'target_locale'  => $targetLocale,
                    'title'          => $origTitle,
                    'description'    => $origText,
                    'status'         => ReviewTranslation::STATUS_WAITING,
                    'model'          => $model,
                    'prompt_id'      => $promptId,
                    'prompt_version' => $promptVersion,
                    'retry_count'    => 0,
                ]);
                $queuedCount++;
            }
        }

        return $queuedCount;
    }

    /**
     * Invalidate existing translations when a review is edited.
     * Sets them to OUTDATED. They will only re-translate once moderation re-publishes.
     */
    public static function invalidateTranslationsForReview(Review $review): void
    {
        ReviewTranslation::where('reviews_id', $review->id)
            ->whereNotNull('target_locale')
            ->update([
                'status' => ReviewTranslation::STATUS_OUTDATED,
            ]);
    }

    /**
     * Handle removed or rejected reviews:
     * Halt any waiting translations and prevent them from being batched.
     */
    public static function handleRemovedOrRejectedReview(Review $review): void
    {
        ReviewTranslation::where('reviews_id', $review->id)
            ->whereIn('status', [ReviewTranslation::STATUS_WAITING, ReviewTranslation::STATUS_BATCHED])
            ->update([
                'status' => ReviewTranslation::STATUS_OUTDATED,
            ]);
    }

    /**
     * Add a new Localio locale later:
     * Generates WAITING translation records for all existing published reviews.
     */
    public static function queueLocaleForExistingReviews(string $newLocale): int
    {
        $normLocale = ReviewModerationService::normalizeLocale($newLocale);
        $publishedReviews = Review::whereIn('status', [Review::STATUS_PUBLISHED, 'active'])->get();
        $created = 0;

        foreach ($publishedReviews as $review) {
            $src = ReviewModerationService::normalizeLocale($review->source_locale ?: 'en-US');
            if (strcasecmp($src, $normLocale) === 0) {
                continue; // Skip source locale
            }

            $exists = ReviewTranslation::where('reviews_id', $review->id)
                ->where('target_locale', $normLocale)
                ->exists();

            if (!$exists) {
                $orig = $review->original ?? $review->translations()->first();
                ReviewTranslation::create([
                    'reviews_id'     => $review->id,
                    'review_id'      => $review->id,
                    'language_id'    => self::resolveLanguageIdForLocale($normLocale),
                    'source_locale'  => $src,
                    'target_locale'  => $normLocale,
                    'title'          => $orig?->title ?? '',
                    'description'    => $orig?->description ?? '',
                    'status'         => ReviewTranslation::STATUS_WAITING,
                    'model'          => config('openai.review_translation_model', 'gpt-5.6-luna'),
                    'prompt_id'      => config('openai.review_translation_prompt_id', 'Localio Review Translation'),
                    'prompt_version' => config('openai.review_translation_prompt_version', '1'),
                    'retry_count'    => 0,
                ]);
                $created++;
            }
        }

        return $created;
    }

    /**
     * Build clean JSON batch payload for manual ChatGPT execution or OpenAI Batch API.
     */
    public static function buildCleanBatchPayload(TranslationBatch $batch): array
    {
        $translations = ReviewTranslation::with(['review.business'])
            ->where('batch_id', $batch->id)
            ->get();

        $requests = [];
        foreach ($translations as $tr) {
            $orig = $tr->review?->original ?? $tr->review?->translations?->first();
            $customId = "review_{$tr->reviews_id}_{$tr->target_locale}";

            $requests[] = [
                'custom_id'     => $customId,
                'review_id'     => (int)$tr->reviews_id,
                'source_locale' => $tr->source_locale ?: 'en-US',
                'target_locale' => $tr->target_locale,
                'review_title'  => $orig?->title ?? $tr->title ?? '',
                'review_text'   => $orig?->description ?? $tr->description ?? '',
                'prompt_name'   => $batch->prompt_id ?: 'Localio Review Translation',
                'prompt_version'=> $batch->prompt_version ?: '1',
                'model'         => $batch->model ?: 'gpt-5.6-luna',
            ];
        }

        return [
            'batch_id'      => $batch->id,
            'type'          => 'reviews',
            'model'         => $batch->model,
            'prompt_id'     => $batch->prompt_id,
            'prompt_version'=> $batch->prompt_version,
            'request_count' => count($requests),
            'requests'      => $requests,
        ];
    }

    /**
     * Create a new TranslationBatch from all pending WAITING records.
     */
    public static function createBatchFromWaiting(?int $limit = 5000): ?TranslationBatch
    {
        $waitingRecords = ReviewTranslation::where('status', ReviewTranslation::STATUS_WAITING)
            ->whereHas('review', function ($q) {
                $q->whereIn('status', [Review::STATUS_PUBLISHED, 'active']);
            })
            ->take($limit)
            ->get();

        if ($waitingRecords->isEmpty()) {
            return null;
        }

        $model = config('openai.review_translation_model', 'gpt-5.6-luna');
        $promptId = config('openai.review_translation_prompt_id', 'Localio Review Translation');
        $promptVersion = config('openai.review_translation_prompt_version', '1');

        $batch = TranslationBatch::create([
            'type'            => 'reviews',
            'model'           => $model,
            'prompt_id'       => $promptId,
            'prompt_version'  => $promptVersion,
            'request_count'   => $waitingRecords->count(),
            'status'          => TranslationBatch::STATUS_WAITING,
            'submitted_at'    => now(),
        ]);

        foreach ($waitingRecords as $rec) {
            $customId = "review_{$rec->reviews_id}_{$rec->target_locale}";
            $rec->update([
                'batch_id'         => $batch->id,
                'openai_custom_id' => $customId,
                'status'           => ReviewTranslation::STATUS_BATCHED,
            ]);
        }

        return $batch;
    }

    /**
     * Process result items uploaded via manual JSON or returned from OpenAI Batch.
     * Expects an array of items or result objects with custom_id, translated_title, translated_text.
     */
    public static function processBatchResultItems(TranslationBatch $batch, array $items): array
    {
        $successCount = 0;
        $failedCount = 0;

        foreach ($items as $item) {
            $customId = $item['custom_id'] ?? null;
            $reviewId = $item['review_id'] ?? null;
            $targetLocale = $item['target_locale'] ?? null;

            if (!$customId && $reviewId && $targetLocale) {
                $customId = "review_{$reviewId}_{$targetLocale}";
            }

            if (!$customId) {
                continue;
            }

            // Parse customId if needed: review_{id}_{locale}
            $translation = null;
            if ($customId) {
                $translation = ReviewTranslation::where('batch_id', $batch->id)
                    ->where('openai_custom_id', $customId)
                    ->first();
            }

            if (!$translation && preg_match('/^review_(\d+)_([A-Za-z0-9\-]+)$/', $customId, $m)) {
                $rId = (int)$m[1];
                $tLoc = $m[2];
                $translation = ReviewTranslation::where('reviews_id', $rId)
                    ->where('target_locale', $tLoc)
                    ->first();
            }

            if (!$translation) {
                continue;
            }

            $title = $item['translated_title'] ?? ($item['title'] ?? null);
            $text  = $item['translated_text'] ?? ($item['text'] ?? ($item['description'] ?? null));
            $isError = !empty($item['error']) || (empty($title) && empty($text));

            if ($isError) {
                $newRetry = ($translation->retry_count ?? 0) + 1;
                $newStatus = ($newRetry >= 3) ? ReviewTranslation::STATUS_NEEDS_REVIEW : ReviewTranslation::STATUS_FAILED;
                $translation->update([
                    'status'      => $newStatus,
                    'retry_count' => $newRetry,
                ]);
                $failedCount++;
            } else {
                $translation->update([
                    'translated_title' => $title,
                    'translated_text'  => $text,
                    'status'           => ReviewTranslation::STATUS_COMPLETED,
                    'translated_at'    => now(),
                ]);
                $successCount++;
            }
        }

        $batch->update([
            'successful_count' => $batch->successful_count + $successCount,
            'failed_count'     => $batch->failed_count + $failedCount,
            'status'           => TranslationBatch::STATUS_COMPLETED,
            'completed_at'     => now(),
        ]);

        return [
            'success'    => $successCount,
            'successful' => $successCount,
            'failed'     => $failedCount,
            'processed'  => $successCount + $failedCount,
        ];
    }
}
