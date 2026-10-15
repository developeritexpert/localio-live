<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Language;
use App\Models\Feature;
use App\Models\ContentTemplate;
use App\Services\ContentTemplateService;

class FeatureContentTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds for Feature Content Templates.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Starting Feature Content Templates Seeder...');

        // 29 base template variations across all 9 sections
        $templates = [
            // 1. H1 Headline (5 options)
            [
                'field_key'     => 'feature_h1',
                'option_number' => 1,
                'headline'      => 'Best [category_tc] with [feature_tc] [country2] [year]',
                'subtext'       => null,
            ],
            [
                'field_key'     => 'feature_h1',
                'option_number' => 2,
                'headline'      => '[category_tc] with [feature_tc] in [year]',
                'subtext'       => null,
            ],
            [
                'field_key'     => 'feature_h1',
                'option_number' => 3,
                'headline'      => 'Top [category_tc] featuring [feature_tc] [country2]',
                'subtext'       => null,
            ],
            [
                'field_key'     => 'feature_h1',
                'option_number' => 4,
                'headline'      => 'Compare [category_tc] with [feature_tc] ([year])',
                'subtext'       => null,
            ],
            [
                'field_key'     => 'feature_h1',
                'option_number' => 5,
                'headline'      => 'Best [entities1_cap] with [feature_tc] [country2] ([year])',
                'subtext'       => null,
            ],

            // 2. Header Introduction Text (3 options)
            [
                'field_key'     => 'header_intro',
                'option_number' => 1,
                'headline'      => 'Looking for top-rated [category_lc] solutions that offer [feature_lc]?',
                'subtext'       => 'Compare leading [entities1_lc] featuring [feature_lc] to find dependable performance, great value, and the right capabilities for your project.',
            ],
            [
                'field_key'     => 'header_intro',
                'option_number' => 2,
                'headline'      => 'Need a reliable [category_lc] service with verified [feature_lc]?',
                'subtext'       => 'Explore our curated selection of [entities1_lc] that include [feature_lc], backed by real user reviews and detailed technical comparisons.',
            ],
            [
                'field_key'     => 'header_intro',
                'option_number' => 3,
                'headline'      => 'Want to know which [category_lc] providers deliver the best [feature_lc]?',
                'subtext'       => 'Review the highest-ranked [entities1_lc] with [feature_lc] across [country1] and find the right match for your specific requirements.',
            ],

            // 3. Compare Top-Rated / Listing (3 options)
            [
                'field_key'     => 'compare_top_rated',
                'option_number' => 1,
                'headline'      => 'Compare Top [category_cap] with [feature_cap] Services',
                'subtext'       => 'Explore ratings, verified reviews, and key specifications of leading providers offering [feature_lc].',
            ],
            [
                'field_key'     => 'compare_top_rated',
                'option_number' => 2,
                'headline'      => 'Best [category_tc] Providers with [feature_tc]',
                'subtext'       => 'Compare top-performing [entities1_lc] offering [feature_lc] [country2] [year].',
            ],
            [
                'field_key'     => 'compare_top_rated',
                'option_number' => 3,
                'headline'      => 'Top-Rated [feature_cap] [category_cap] Options',
                'subtext'       => 'Hand-picked [entities1_lc] verified for quality [feature_lc] and high customer satisfaction.',
            ],

            // 4. Top 10 (3 options)
            [
                'field_key'     => 'top_10',
                'option_number' => 1,
                'headline'      => 'Top 10 [category_cap] with [feature_cap] Comparison',
                'subtext'       => 'Side-by-side comparison of the 10 best [entities1_lc] offering [feature_lc].',
            ],
            [
                'field_key'     => 'top_10',
                'option_number' => 2,
                'headline'      => 'Top 10 Rated [entities1_cap] with [feature_tc]',
                'subtext'       => 'Evaluate pricing, performance, and key highlights across top 10 providers.',
            ],
            [
                'field_key'     => 'top_10',
                'option_number' => 3,
                'headline'      => '10 Best [category_tc] Solutions for [feature_cap]',
                'subtext'       => 'Compare top 10 choices to identify the right [entity1_lc] with [feature_lc].',
            ],

            // 4b. Top 10 (< 10 Reviews) (3 options)
            [
                'field_key'     => 'top_10_under_10',
                'option_number' => 1,
                'headline'      => 'Top 10 [category_cap] with [feature_cap] Comparison',
                'subtext'       => 'Side-by-side comparison of the 10 best [entities1_lc] offering [feature_lc].',
            ],
            [
                'field_key'     => 'top_10_under_10',
                'option_number' => 2,
                'headline'      => 'Top 10 [entities1_cap] with [feature_tc]',
                'subtext'       => 'Compare pricing, capabilities, and early ratings across top providers with [feature_lc].',
            ],
            [
                'field_key'     => 'top_10_under_10',
                'option_number' => 3,
                'headline'      => '10 Best [category_tc] Solutions for [feature_cap]',
                'subtext'       => 'Compare top choices to find the right [entity1_lc] offering [feature_lc].',
            ],

            // 5. Pros and Cons (3 options)
            [
                'field_key'     => 'pros_cons',
                'option_number' => 1,
                'headline'      => 'What Users Say About [feature_cap] in [category_cap]',
                'subtext'       => 'Real user feedback, pros, and cons from customers using [feature_lc].',
            ],
            [
                'field_key'     => 'pros_cons',
                'option_number' => 2,
                'headline'      => 'Community Insights on [category_tc] with [feature_tc]',
                'subtext'       => 'Authentic reviews highlighting strengths and trade-offs of [feature_lc] providers.',
            ],
            [
                'field_key'     => 'pros_cons',
                'option_number' => 3,
                'headline'      => 'Pros and Cons of [category_cap] [feature_cap] Providers',
                'subtext'       => 'Verified user opinions on speed, support, and reliability with [feature_lc].',
            ],

            // 5b. Pros and Cons (< 10 Reviews) (4 options)
            [
                'field_key'     => 'pros_cons_under_10',
                'option_number' => 1,
                'headline'      => 'Pros & cons from public sources',
                'subtext'       => 'These strengths and drawbacks are gathered from publicly available sources across the web, as one or more businesses currently have fewer than 10 published Localio reviews.',
            ],
            [
                'field_key'     => 'pros_cons_under_10',
                'option_number' => 2,
                'headline'      => 'Key pros & cons from public feedback',
                'subtext'       => 'Based on public feedback and web sources, here are the most frequently highlighted advantages and potential drawbacks for providers with [feature_lc].',
            ],
            [
                'field_key'     => 'pros_cons_under_10',
                'option_number' => 3,
                'headline'      => 'Summary of pros & cons from public sources',
                'subtext'       => 'Compiled from publicly available online feedback and reviews to highlight recurring strengths and limitations of [feature_lc] options.',
            ],
            [
                'field_key'     => 'pros_cons_under_10',
                'option_number' => 4,
                'headline'      => 'Pros & cons from web sources',
                'subtext'       => 'A curated overview of recurring pros and cons gathered from public web sources while community reviews grow.',
            ],

            // 6. Popular Comparisons (3 options)
            [
                'field_key'     => 'popular_comparisons',
                'option_number' => 1,
                'headline'      => 'Popular Comparisons with [feature_cap]',
                'subtext'       => 'Compare leading [entities1_lc] with [feature_lc] head-to-head.',
            ],
            [
                'field_key'     => 'popular_comparisons',
                'option_number' => 2,
                'headline'      => 'Head-to-Head: Top [category_tc] with [feature_tc]',
                'subtext'       => 'Direct side-by-side feature comparisons between top providers.',
            ],
            [
                'field_key'     => 'popular_comparisons',
                'option_number' => 3,
                'headline'      => 'Compare Leading [feature_cap] Providers',
                'subtext'       => 'Detailed showdowns between popular [entities1_lc] offering [feature_lc].',
            ],

            // 7. Comparison Tool (3 options)
            [
                'field_key'     => 'compare_tool',
                'option_number' => 1,
                'headline'      => 'Compare Any [category_cap] with [feature_cap]',
                'subtext'       => 'Select two or more providers to compare specifications, pricing, and [feature_lc] support.',
            ],
            [
                'field_key'     => 'compare_tool',
                'option_number' => 2,
                'headline'      => 'Interactive [feature_tc] Comparison Tool',
                'subtext'       => 'Pick any [entities1_lc] to see how their [feature_lc] capabilities stack up.',
            ],
            [
                'field_key'     => 'compare_tool',
                'option_number' => 3,
                'headline'      => 'Side-by-Side [category_tc] Provider Comparison',
                'subtext'       => 'Customize your comparison to find the best provider with [feature_lc].',
            ],

            // 8. Explore Subcategories / Categories (3 options)
            [
                'field_key'     => 'explore_subcategories',
                'option_number' => 1,
                'headline'      => 'Explore Related [category_cap] Categories',
                'subtext'       => 'Discover other specialized [entities1_lc] and complementary solutions.',
            ],
            [
                'field_key'     => 'explore_subcategories',
                'option_number' => 2,
                'headline'      => 'Browse Related [category_tc] Features & Options',
                'subtext'       => 'Check out other popular features and categories in [category_lc].',
            ],
            [
                'field_key'     => 'explore_subcategories',
                'option_number' => 3,
                'headline'      => 'More Categories & Solutions in [category_cap]',
                'subtext'       => 'Find tailored [category_lc] categories and related toolsets.',
            ],

            // 9. View All Link
            [
                'field_key'     => 'view_all_link',
                'option_number' => 1,
                'headline'      => 'View all [entities1_lc]',
                'subtext'       => null,
            ],


        ];

        // Target languages: ID 1 (standard base) + en-us
        $targetLangIds = [1];

        $enUsLang = DB::table('languages')->where('lang_code', 'en-us')->first();
        if ($enUsLang && !in_array((int)$enUsLang->id, $targetLangIds)) {
            $targetLangIds[] = (int) $enUsLang->id;
        }

        $totalSeeded = 0;
        foreach ($targetLangIds as $langId) {
            $langInfo = DB::table('languages')->where('id', $langId)->first();
            $langName = $langInfo ? "{$langInfo->name} ({$langInfo->lang_code})" : "ID {$langId}";

            $countForLang = 0;
            foreach ($templates as $t) {
                $existing = DB::table('content_templates')
                    ->where('section_group', 'features')
                    ->where('field_key', $t['field_key'])
                    ->where('option_number', $t['option_number'])
                    ->where('lang_id', $langId)
                    ->first();

                if ($existing) {
                    DB::table('content_templates')
                        ->where('id', $existing->id)
                        ->update([
                            'headline'   => $t['headline'],
                            'subtext'    => $t['subtext'],
                            'is_active'  => 1,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('content_templates')->insert([
                        'section_group' => 'features',
                        'field_key'     => $t['field_key'],
                        'option_number' => $t['option_number'],
                        'headline'      => $t['headline'],
                        'subtext'       => $t['subtext'],
                        'lang_id'       => $langId,
                        'is_active'     => 1,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
                $countForLang++;
                $totalSeeded++;
            }

            $this->command->info("Seeded {$countForLang} template options for {$langName} [lang_id: {$langId}].");

            // Assign missing templates and lock them across all features
            $newAssignments = ContentTemplateService::assignMissingForFeatures($langId);
            $this->command->info("Assigned & locked {$newAssignments} missing feature section slots for {$langName}.");
        }

        $this->command->info("Feature Content Templates Seeder finished successfully! ({$totalSeeded} template entries processed).");
    }
}
