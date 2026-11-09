<?php

namespace App\Observers;

use App\Models\Business;
use App\Services\SitemapService;

class BusinessObserver
{
    /**
     * Handle the Business "saved" event.
     */
    public function saved(Business $business): void
    {
        $catId = $business->category_id ? (int) $business->category_id : null;
        $origCatId = $business->getOriginal('category_id') ? (int) $business->getOriginal('category_id') : null;

        $needsUpdate = false;
        $categoriesToUpdate = [];

        // 1. Primary category / subcategory changed
        if ($business->wasChanged('category_id')) {
            if ($origCatId) {
                $categoriesToUpdate[] = $origCatId;
            }
            if ($catId) {
                $categoriesToUpdate[] = $catId;
            }
            $needsUpdate = true;
        } elseif ($business->wasChanged('status') || $business->wasChanged('active_all_countries')) {
            // Status or country eligibility changed
            if ($catId) {
                $categoriesToUpdate[] = $catId;
            }
            $needsUpdate = true;
        }

        if ($needsUpdate && !empty($categoriesToUpdate)) {
            $sitemapService = app(SitemapService::class);
            foreach (array_unique($categoriesToUpdate) as $cId) {
                $sitemapService->generateCategoryBusinessSitemaps($cId);
            }
            $sitemapService->updateIndex();
        }
    }

    /**
     * Handle the Business "deleted" event.
     */
    public function deleted(Business $business): void
    {
        if ($business->category_id) {
            $sitemapService = app(SitemapService::class);
            $sitemapService->generateCategoryBusinessSitemaps((int) $business->category_id);
            $sitemapService->updateIndex();
        }
    }

    /**
     * Handle the Business "restored" event.
     */
    public function restored(Business $business): void
    {
        if ($business->category_id) {
            $sitemapService = app(SitemapService::class);
            $sitemapService->generateCategoryBusinessSitemaps((int) $business->category_id);
            $sitemapService->updateIndex();
        }
    }
}

