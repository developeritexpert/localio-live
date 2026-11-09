<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\SitemapService;

class CategoryObserver
{
    /**
     * Handle the Category "saved" event.
     */
    public function saved(Category $category): void
    {
        $sitemapService = app(SitemapService::class);
        $sitemapService->generateCategoriesSitemap();
        $sitemapService->generateCategoryBusinessSitemaps((int) $category->id);
        $sitemapService->updateIndex();
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $sitemapService = app(SitemapService::class);
        $sitemapService->generateCategoriesSitemap();
        $sitemapService->deleteCategoryBusinessSitemaps((int) $category->id);
        $sitemapService->updateIndex();
    }
}

