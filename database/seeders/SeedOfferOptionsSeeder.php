<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PricingOption;
use App\Models\PricingOptionTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SeedOfferOptionsSeeder extends Seeder
{
    public function run(): void
    {
        // Find existing non-compliant pricing options and remove them & their pivot/translations
        $options = PricingOption::all();
        foreach ($options as $opt) {
            $opt->translations()->delete();
            $opt->categories()->detach();
            $opt->delete();
        }

        // Global Offer Options
        $globalOffers = [
            'Free trial',
            'Money-back guarantee',
            'Discount available',
            'Special offer'
        ];

        foreach ($globalOffers as $name) {
            $opt = PricingOption::create([
                'slug' => Str::slug($name),
                'scope' => 'global',
                'status' => 1,
            ]);

            // Create English translation (lang_id = 1)
            PricingOptionTranslation::create([
                'pricing_option_id' => $opt->id,
                'lang_id' => 1,
                'name' => $name,
                'button_text' => 'Claim now',
            ]);
        }

        // Web Hosting Category & Subcategories
        $webHostingCategory = Category::whereHas('categoryTranslations', function ($q) {
            $q->where('name', 'LIKE', '%Web hosting%');
        })->first();

        $categoryIds = [];
        if ($webHostingCategory) {
            $categoryIds[] = $webHostingCategory->id;
            // Include subcategories
            $subCategoryIds = Category::where('parent_id', $webHostingCategory->id)->pluck('id')->toArray();
            $categoryIds = array_merge($categoryIds, $subCategoryIds);
        } else {
            // Fallback to category 11 & subcategories 31, 32, 33
            $categoryIds = [11, 31, 32, 33];
        }

        $specificOffers = [
            'Free domain',
            'Free migration',
            'Free SSL',
            'Website builder',
            'Starting price'
        ];

        foreach ($specificOffers as $name) {
            $opt = PricingOption::create([
                'slug' => Str::slug($name),
                'scope' => 'category_specific',
                'status' => 1,
            ]);

            PricingOptionTranslation::create([
                'pricing_option_id' => $opt->id,
                'lang_id' => 1,
                'name' => $name,
                'button_text' => 'Claim now',
            ]);

            // Assign categories
            $opt->categories()->sync($categoryIds);
        }
    }
}
