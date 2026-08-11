<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductComparisonTest extends TestCase
{
    use DatabaseTransactions;

    public function test_product_comparison_view_includes_non_affiliated_businesses()
    {
        // Fetch or create an affiliated business and a non-affiliated business
        $affiliateBiz = Business::where('is_affiliate', 1)->first();
        $nonAffiliateBiz = Business::where('is_affiliate', 0)->first();

        if (!$affiliateBiz || !$nonAffiliateBiz) {
            $this->markTestSkipped('Database does not have both affiliate and non-affiliate businesses.');
        }

        // Put both into session
        $response = $this->withSession(['compared_products' => [$affiliateBiz->id, $nonAffiliateBiz->id]])
            ->get('/en-us/product-comparison');

        $response->assertStatus(200);
        $response->assertViewHas('businesses', function ($businesses) use ($affiliateBiz, $nonAffiliateBiz) {
            $ids = $businesses->pluck('id')->toArray();
            return in_array($affiliateBiz->id, $ids) && in_array($nonAffiliateBiz->id, $ids);
        });
    }

    public function test_seo_comparison_url_loads_non_affiliated_businesses()
    {
        $affiliateBiz = Business::where('is_affiliate', 1)->has('translations')->first();
        $nonAffiliateBiz = Business::where('is_affiliate', 0)->has('translations')->first();

        if (!$affiliateBiz || !$nonAffiliateBiz) {
            $this->markTestSkipped('Database does not have both affiliate and non-affiliate businesses with translations.');
        }

        $affiliateSlug = $affiliateBiz->translations->first()->slug;
        $nonAffiliateSlug = $nonAffiliateBiz->translations->first()->slug;

        $catTranslation = CategoryTranslation::where('category_id', $affiliateBiz->category_id)
            ->whereNotNull('comparison_slug')
            ->where('comparison_slug', '!=', '')
            ->first();

        $compSlug = $catTranslation ? $catTranslation->comparison_slug : 'product-comparison';

        $url = "/en-us/{$compSlug}/{$affiliateSlug}-vs-{$nonAffiliateSlug}";
        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertViewHas('businesses', function ($businesses) use ($affiliateBiz, $nonAffiliateBiz) {
            $ids = $businesses->pluck('id')->toArray();
            return in_array($affiliateBiz->id, $ids) && in_array($nonAffiliateBiz->id, $ids);
        });
    }
}
