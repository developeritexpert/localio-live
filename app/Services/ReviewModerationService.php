<?php

namespace App\Services;

use App\Models\Review;
use App\Models\ReviewModerationLog;
use App\Models\ReviewReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReviewModerationService
{
    // --- Spam/link patterns ------------------------------------------------------
    protected static array $spamPatterns = [
        '/\b(buy now|click here|free money|earn \$|make money fast|casino|viagra|cialis|porn|xxx|crypto investment|forex trading|whatsapp \+\d+)\b/i',
        '/(\[url=|<a\s+href=)/i',
    ];

    protected static array $prohibitedLinkPattern = [
        '/https?:\/\/\S+/i',
        '/www\.\S+\.\S+/i',
    ];

    // --- Locale normalization & validation ----------------------------------------

    public static function normalizeLocale(?string $locale): string
    {
        if (!$locale) {
            return 'en-US';
        }
        $parts = explode('-', trim($locale), 2);
        if (count($parts) === 2) {
            return strtolower($parts[0]) . '-' . strtoupper($parts[1]);
        }
        return strtolower($locale);
    }

    public static function isSupportedLocale(?string $locale): bool
    {
        if (!$locale) {
            return false;
        }
        $normalized = static::normalizeLocale($locale);
        $supported = config('openai.supported_locales', []);
        return in_array($normalized, $supported, true);
    }

    // --- Layer 1: Laravel deterministic checks -----------------------------------

    /**
     * Run Layer 1 synchronous deterministic checks.
     * Returns ['pass' => bool, 'reason' => string|null]
     */
    public static function runLayer1Checks(Review $review): array
    {
        // Get the review's text from its first translation
        $translation = $review->translations()->first();
        $text = trim(($translation->title ?? '') . ' ' . ($translation->description ?? ''));
        $userId = $review->user_id;
        $bizId  = $review->business_id;

        // 1. Text length
        $strippedText = trim(strip_tags($text));
        $length = mb_strlen($strippedText);
        if ($length < 10) {
            return ['pass' => false, 'reason' => 'too_short'];
        }
        if ($length > 5000) {
            return ['pass' => false, 'reason' => 'too_long'];
        }

        // 2. Duplicate review by same user for same business (excluding this review's own ID)
        $duplicate = Review::where('user_id', $userId)
            ->where('business_id', $bizId)
            ->where('id', '!=', $review->id)
            ->whereIn('status', [Review::STATUS_PUBLISHED, Review::STATUS_PROCESSING, Review::STATUS_PENDING_REVIEW, 'active'])
            ->exists();
        if ($duplicate) {
            return ['pass' => false, 'reason' => 'duplicate_review'];
        }

        // 3. Submission frequency: > 5 reviews in last 24 hours by same user
        $recentCount = Review::where('user_id', $userId)
            ->where('id', '!=', $review->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
        if ($recentCount >= 5) {
            return ['pass' => false, 'reason' => 'submission_frequency'];
        }

        // 4. Prohibited links in text
        foreach (static::$prohibitedLinkPattern as $pattern) {
            if (preg_match($pattern, $strippedText)) {
                return ['pass' => false, 'reason' => 'prohibited_links'];
            }
        }

        // 5. Blocked / inactive account
        $user = $review->user;
        if (!$user || (isset($user->status) && $user->status === 'blocked') || (isset($user->is_active) && $user->is_active === 0)) {
            return ['pass' => false, 'reason' => 'blocked_account'];
        }

        // 6. Spam patterns
        foreach (static::$spamPatterns as $pattern) {
            if (preg_match($pattern, $strippedText)) {
                return ['pass' => false, 'reason' => 'spam_pattern'];
            }
        }

        // 7. Conflict of interest: user is vendor/owner of the reviewed business
        if ($user) {
            if (isset($user->business_id) && $user->business_id == $bizId) {
                return ['pass' => false, 'reason' => 'conflict_of_interest'];
            }
            if (isset($user->user_type) && in_array($user->user_type, ['admin', 'super_admin'])) {
                return ['pass' => false, 'reason' => 'admin_submitting_review'];
            }
        }

        return ['pass' => true, 'reason' => null];
    }

    /**
     * Apply Layer 1 result to the review and log it.
     */
    public static function applyLayer1(Review $review, array $result): void
    {
        $fromStatus = $review->status;

        if ($result['pass']) {
            $review->laravel_check_status = 'pass';
            $review->laravel_fail_reason  = null;
            $review->save();

            ReviewModerationLog::logSystem(
                $review->id,
                ReviewModerationLog::ACTION_LAYER1_CHECK,
                $fromStatus,
                $review->status,
                ReviewModerationLog::LAYER_1,
                ['result' => 'pass']
            );
        } else {
            $review->laravel_check_status = 'fail';
            $review->laravel_fail_reason  = $result['reason'];
            $review->status               = Review::STATUS_PENDING_REVIEW;
            $review->escalation_source    = Review::ESCALATION_LARAVEL;
            $review->localio_ai_status    = Review::AI_STATUS_SKIPPED;
            $review->save();

            ReviewModerationLog::logSystem(
                $review->id,
                ReviewModerationLog::ACTION_LAYER1_CHECK,
                $fromStatus,
                Review::STATUS_PENDING_REVIEW,
                ReviewModerationLog::LAYER_1,
                ['result' => 'fail', 'reason' => $result['reason']]
            );
        }
    }

    // --- Layer 2: OpenAI Moderation API -----------------------------------------

    /**
     * Run the free OpenAI omni-moderation-latest check on a review's text.
     */
    public static function runLayer2Moderation(Review $review): array
    {
        $apiKey = config('openai.api_key') ?: optional(\App\Models\WebSetting::where('key', 'openai_api_key')->first())->value;

        if (!$apiKey) {
            Log::warning("ReviewModerationService: No OPENAI_API_KEY set. Skipping Layer 2 for review #{$review->id}.");
            return ['flagged' => false, 'categories' => [], 'skipped' => true];
        }

        $translation = $review->translations()->first();
        $textToCheck = trim(($translation->title ?? '') . "\n" . ($translation->description ?? ''));

        if (empty($textToCheck)) {
            Log::warning("ReviewModerationService: No text to moderate for review #{$review->id}.");
            return ['flagged' => false, 'categories' => [], 'skipped' => true];
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/moderations', [
                    'model' => config('openai.moderation_model', 'omni-moderation-latest'),
                    'input' => $textToCheck,
                ]);

            if (!$response->successful()) {
                Log::error("ReviewModerationService: Moderation API error for review #{$review->id}: " . $response->body());
                return ['flagged' => false, 'categories' => [], 'error' => $response->status()];
            }

            $data    = $response->json();
            $result  = $data['results'][0] ?? [];
            $flagged = $result['flagged'] ?? false;

            // Collect flagged categories for storage
            $flaggedCategories = [];
            if (!empty($result['categories'])) {
                foreach ($result['categories'] as $cat => $isFlagged) {
                    if ($isFlagged) {
                        $flaggedCategories[] = $cat;
                    }
                }
            }

            return [
                'flagged'    => $flagged,
                'categories' => $flaggedCategories,
                'raw'        => $result,
                'skipped'    => false,
            ];
        } catch (\Throwable $e) {
            Log::error("ReviewModerationService: Exception during Layer 2 for review #{$review->id}: " . $e->getMessage());
            return ['flagged' => false, 'categories' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Apply Layer 2 result to the review and log it.
     */
    public static function applyLayer2(Review $review, array $result): void
    {
        $fromStatus = $review->status;

        // If skipped (no API key configured), mark as waiting for batch
        if (!empty($result['skipped'])) {
            $review->moderation_api_passed     = true;
            $review->moderation_api_checked_at = now();
            $review->moderation_api_result     = ['skipped' => true, 'reason' => 'no_api_key'];
            $review->localio_ai_status         = Review::AI_STATUS_WAITING;
            $review->save();
            return;
        }

        if ($result['flagged']) {
            $review->moderation_api_passed     = false;
            $review->moderation_api_result     = $result['raw'] ?? [];
            $review->moderation_api_checked_at = now();
            $review->status                    = Review::STATUS_PENDING_REVIEW;
            $review->escalation_source         = Review::ESCALATION_MODERATION_API;
            $review->localio_ai_status         = Review::AI_STATUS_SKIPPED;
            $review->save();

            ReviewModerationLog::logSystem(
                $review->id,
                ReviewModerationLog::ACTION_LAYER2_CHECK,
                $fromStatus,
                Review::STATUS_PENDING_REVIEW,
                ReviewModerationLog::LAYER_2,
                ['flagged' => true, 'categories' => $result['categories']]
            );
        } else {
            $review->moderation_api_passed     = true;
            $review->moderation_api_result     = $result['raw'] ?? [];
            $review->moderation_api_checked_at = now();
            $review->localio_ai_status         = Review::AI_STATUS_WAITING;
            $review->save();

            ReviewModerationLog::logSystem(
                $review->id,
                ReviewModerationLog::ACTION_LAYER2_CHECK,
                $fromStatus,
                $review->status,
                ReviewModerationLog::LAYER_2,
                ['flagged' => false]
            );
        }
    }

    // --- Layer 3: GPT-5.6-Luna Batch Result Application --------------------------

    /**
     * Apply GPT-5.6-Luna batch result to a review.
     * Called by PollReviewModerationBatch command after batch completes.
     */
    public static function applyLayer3(
        Review $review,
        array $gptResult,
        string $batchId,
        string $model,
        string $promptId,
        string $promptVersion
    ): void {
        $fromStatus = $review->status;

        $decision       = strtoupper($gptResult['decision'] ?? 'HUMAN_REVIEW');
        $riskLevel      = strtoupper($gptResult['risk_level'] ?? 'MEDIUM');
        $flags          = $gptResult['flags'] ?? [];
        $confidence     = isset($gptResult['confidence']) ? (float) $gptResult['confidence'] : null;
        $reason         = $gptResult['reason'] ?? null;
        $localeStatus   = strtoupper($gptResult['locale_status'] ?? Review::LOCALE_HUMAN_REVIEW);
        $detectedLocale = isset($gptResult['detected_locale']) ? static::normalizeLocale($gptResult['detected_locale']) : null;
        $submittedLocale= isset($gptResult['submitted_locale']) ? static::normalizeLocale($gptResult['submitted_locale']) : ($review->submitted_locale ?: 'en-US');

        // Validate detected locale if reassigned
        if ($localeStatus === Review::LOCALE_REASSIGNED) {
            if (!$detectedLocale || !static::isSupportedLocale($detectedLocale)) {
                $localeStatus = Review::LOCALE_HUMAN_REVIEW;
                $decision     = 'HUMAN_REVIEW';
            }
        }

        // Update all AI fields
        $review->localio_ai_decision       = $decision;
        $review->localio_ai_risk_level     = $riskLevel;
        $review->localio_ai_flags          = is_array($flags) ? $flags : [$flags];
        $review->localio_ai_confidence     = $confidence;
        $review->localio_ai_reason         = $reason;
        $review->localio_ai_batch_id       = $batchId;
        $review->localio_ai_model          = $model;
        $review->localio_ai_prompt_id      = $promptId;
        $review->localio_ai_prompt_version = $promptVersion;
        $review->localio_ai_processed_at   = now();
        $review->localio_ai_status         = Review::AI_STATUS_DONE;
        $review->locale_status             = $localeStatus;
        $review->submitted_locale          = $submittedLocale;

        if ($decision === 'PASS') {
            // Process locale result first
            if ($localeStatus === Review::LOCALE_MATCH) {
                $review->source_locale = $submittedLocale;
                $review->status        = Review::STATUS_PUBLISHED;
            } elseif ($localeStatus === Review::LOCALE_REASSIGNED && $detectedLocale) {
                $review->source_locale = $detectedLocale;
                $review->status        = Review::STATUS_PUBLISHED;
            } else {
                // HUMAN_REVIEW on locale � cannot publish without moderator locale assignment
                $review->status            = Review::STATUS_PENDING_REVIEW;
                $review->escalation_source = Review::ESCALATION_LOCALIO_AI;
            }
        } else {
            // HUMAN_REVIEW decision
            $review->status            = Review::STATUS_PENDING_REVIEW;
            $review->escalation_source = Review::ESCALATION_LOCALIO_AI;
        }

        $review->save();

        ReviewModerationLog::logSystem(
            $review->id,
            ReviewModerationLog::ACTION_LAYER3_RESULT,
            $fromStatus,
            $review->status,
            ReviewModerationLog::LAYER_3,
            [
                'decision'        => $decision,
                'risk_level'      => $riskLevel,
                'flags'           => $flags,
                'confidence'      => $confidence,
                'reason'          => $reason,
                'locale_status'   => $localeStatus,
                'detected_locale' => $detectedLocale,
                'batch_id'        => $batchId,
                'model'           => $model,
                'prompt_id'       => $promptId,
                'prompt_version'  => $promptVersion,
            ]
        );
    }

    // --- Re-moderation for reported reviews -------------------------------------

    /**
     * Re-run Layer 2 + optionally queue for Layer 3 on a reported review.
     */
    public static function triggerRemoderation(Review $review, ReviewReport $report): void
    {
        $report->remoderation_triggered = true;
        $report->save();

        $layer2Result = static::runLayer2Moderation($review);

        if (!empty($layer2Result['flagged'])) {
            // Flagged by moderation API � send to human review
            $fromStatus = $review->status;
            $review->moderation_api_passed     = false;
            $review->moderation_api_result     = $layer2Result['raw'] ?? [];
            $review->moderation_api_checked_at = now();
            $review->status                    = Review::STATUS_PENDING_REVIEW;
            $review->escalation_source         = Review::ESCALATION_USER_REPORT;
            $review->save();

            $report->remoderation_result = 'flagged_by_moderation_api';
            $report->save();

            ReviewModerationLog::logSystem(
                $review->id,
                ReviewModerationLog::ACTION_RE_MODERATED,
                $fromStatus,
                Review::STATUS_PENDING_REVIEW,
                ReviewModerationLog::LAYER_2,
                ['triggered_by' => 'report_id_' . $report->id, 'categories' => $layer2Result['categories']]
            );
        } else {
            // Not flagged by Layer 2 � queue for next GPT batch
            $review->moderation_api_passed     = true;
            $review->moderation_api_result     = $layer2Result['raw'] ?? [];
            $review->moderation_api_checked_at = now();
            $review->localio_ai_status         = Review::AI_STATUS_WAITING;
            $review->save();

            $report->remoderation_result = 'escalated_to_batch';
            $report->save();

            ReviewModerationLog::logSystem(
                $review->id,
                ReviewModerationLog::ACTION_RE_MODERATED,
                $review->status,
                $review->status,
                ReviewModerationLog::LAYER_2,
                ['triggered_by' => 'report_id_' . $report->id, 'result' => 'passed_to_batch']
            );
        }
    }
}
