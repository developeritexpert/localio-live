<?php

namespace App\Observers;

use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Services\SitemapService;

class BusinessTranslationObserver
{
    /**
     * Handle the BusinessTranslation "saved" event.
     */
    public function saved(BusinessTranslation $translation): void
    {
        if ($translation->wasChanged('slug') || $translation->wasChanged('status')) {
            $business = $translation->business ?? Business::find($translation->business_id);
            if ($business && $business->category_id) {
                $sitemapService = app(SitemapService::class);
                $sitemapService->generateCategoryBusinessSitemaps((int) $business->category_id);
                $sitemapService->updateIndex();
            }
        }
    }
}

