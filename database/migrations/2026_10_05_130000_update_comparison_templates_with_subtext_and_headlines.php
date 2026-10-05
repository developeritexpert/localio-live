<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ContentTemplate;
use App\Models\Language;
use App\Services\ContentTemplateService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Seed default options for new comparison fields (comparison_what_is, comparison_usps)
        ContentTemplateService::seedDefaultComparisonOptionsIfMissing();

        // 2. Populate default subtexts for existing records where subtext is currently null
        $prosConsSubtexts = [
            1 => 'Compare the key advantages and disadvantages of [business_vs].',
            2 => 'Side-by-side strengths and limitations based on verified customer feedback.',
            3 => 'Explore what real users like and dislike about [business_vs].',
            4 => 'Detailed breakdown of benefits and drawbacks for each [category_lc] provider.',
        ];

        foreach ($prosConsSubtexts as $opt => $sub) {
            ContentTemplate::where('section_group', 'comparison')
                ->where('field_key', 'comparison_pros_cons')
                ->where('option_number', $opt)
                ->where(function ($q) {
                    $q->whereNull('subtext')->orWhere('subtext', '');
                })
                ->update(['subtext' => $sub]);
        }

        $reviewsSubtexts = [
            1 => 'Authentic customer reviews and ratings for [business_vs].',
            2 => 'Discover what verified users have experienced with each [category_lc] solution.',
            3 => 'Unbiased ratings and feedback from real customers.',
            4 => 'Compare overall satisfaction scores and in-depth user impressions.',
        ];

        foreach ($reviewsSubtexts as $opt => $sub) {
            ContentTemplate::where('section_group', 'comparison')
                ->where('field_key', 'comparison_reviews')
                ->where('option_number', $opt)
                ->where(function ($q) {
                    $q->whereNull('subtext')->orWhere('subtext', '');
                })
                ->update(['subtext' => $sub]);
        }

        $alternativesSubtexts = [
            1 => 'Explore top-rated alternatives to [business_vs] in [category_lc].',
            2 => 'Looking for other options? Check out leading [category_lc] alternatives.',
            3 => 'Browse comparable tools and services that fit your requirements and budget.',
            4 => 'Discover other top providers worth evaluating alongside [business_vs].',
        ];

        foreach ($alternativesSubtexts as $opt => $sub) {
            ContentTemplate::where('section_group', 'comparison')
                ->where('field_key', 'comparison_alternatives')
                ->where('option_number', $opt)
                ->where(function ($q) {
                    $q->whereNull('subtext')->orWhere('subtext', '');
                })
                ->update(['subtext' => $sub]);
        }

        // 3. Ensure options exist for all other active languages
        $languages = Language::where('status', 1)->get();
        foreach ($languages as $lang) {
            if ($lang->id > 1) {
                ContentTemplateService::ensureTemplatesExistForLanguage('comparison', $lang->id);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ContentTemplate::where('section_group', 'comparison')
            ->whereIn('field_key', [
                'comparison_what_is',
                'comparison_usps',
            ])
            ->delete();
    }
};
