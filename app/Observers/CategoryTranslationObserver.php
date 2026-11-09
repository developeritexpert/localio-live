<?php

namespace App\Observers;

use App\Models\CategoryTranslation;
use App\Services\SitemapService;

class CategoryTranslationObserver
{
    /**
     * Handle the CategoryTranslation "saved" event.
     */
    public function saved(CategoryTranslation $translation): void
    {
        if ($translation->wasChanged('slug') || $translation->wasChanged('status')) {
            $sitemapService = app(SitemapService::class);
            $sitemapService->generateCategoriesSitemap();

            if ($translation->category_id) {
                $sitemapService->generateCategoryBusinessSitemaps((int) $translation->category_id);
            }

            $sitemapService->updateIndex();
        }
    }
}

