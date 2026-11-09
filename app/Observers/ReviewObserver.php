<?php

namespace App\Observers;

use App\Models\Business;
use App\Models\Review;
use App\Services\SitemapService;

class ReviewObserver
{
    /**
     * Handle the Review "saved" event.
     */
    public function saved(Review $review): void
    {
        $this->handleReviewChange($review);
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        $this->handleReviewChange($review);
    }

    /**
     * Handle review changes that might impact business indexability.
     */
    protected function handleReviewChange(Review $review): void
    {
        if (!$review->business_id) {
            return;
        }

        $business = Business::find($review->business_id);
        if ($business && $business->category_id) {
            $sitemapService = app(SitemapService::class);
            $sitemapService->generateCategoryBusinessSitemaps((int) $business->category_id);
            $sitemapService->updateIndex();
        }
    }
}

