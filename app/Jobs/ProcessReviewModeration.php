<?php

namespace App\Jobs;

use App\Models\Review;
use App\Services\ReviewModerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessReviewModeration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public readonly int $reviewId)
    {
    }

    public function handle(): void
    {
        $review = Review::find($this->reviewId);

        if (!$review) {
            Log::warning("ProcessReviewModeration: Review #{$this->reviewId} not found. Job skipped.");
            return;
        }

        // Only process reviews that are still in PROCESSING state
        if ($review->status !== Review::STATUS_PROCESSING) {
            Log::info("ProcessReviewModeration: Review #{$this->reviewId} is no longer PROCESSING (status={$review->status}). Skipping.");
            return;
        }

        Log::info("ProcessReviewModeration: Starting Layer 1 for review #{$this->reviewId}");

        // ── LAYER 1: Laravel deterministic checks ────────────────────────────────
        $layer1Result = ReviewModerationService::runLayer1Checks($review);
        ReviewModerationService::applyLayer1($review, $layer1Result);

        // Reload to get the latest status
        $review->refresh();

        if (!$layer1Result['pass']) {
            Log::info("ProcessReviewModeration: Review #{$this->reviewId} failed Layer 1 ({$layer1Result['reason']}). Sent to PENDING_REVIEW.");
            return;
        }

        // ── LAYER 2: OpenAI Moderation API ──────────────────────────────────────
        Log::info("ProcessReviewModeration: Starting Layer 2 for review #{$this->reviewId}");

        $layer2Result = ReviewModerationService::runLayer2Moderation($review);
        ReviewModerationService::applyLayer2($review, $layer2Result);

        $review->refresh();

        if ($review->status === Review::STATUS_PENDING_REVIEW) {
            Log::info("ProcessReviewModeration: Review #{$this->reviewId} flagged by Layer 2. Sent to PENDING_REVIEW.");
            return;
        }

        // Review passed both layers — now waits for the next batch (Layer 3)
        Log::info("ProcessReviewModeration: Review #{$this->reviewId} passed Layers 1+2. Waiting for next AI batch.");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessReviewModeration: Job failed for review #{$this->reviewId}: " . $exception->getMessage());

        // If the job fails completely, don't leave the review stuck — flag for human review
        $review = Review::find($this->reviewId);
        if ($review && $review->status === Review::STATUS_PROCESSING) {
            $review->update([
                'status'           => Review::STATUS_PENDING_REVIEW,
                'escalation_source' => Review::ESCALATION_MANUAL,
                'localio_ai_reason' => 'Automated moderation job failed: ' . $exception->getMessage(),
            ]);
        }
    }
}
