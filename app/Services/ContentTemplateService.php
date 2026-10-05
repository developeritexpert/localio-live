<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\ContentTemplate;
use App\Models\ContentTemplateAssignment;
use App\Models\Feature;
use App\Models\StaticContentKey;
use Illuminate\Support\Facades\DB;

class ContentTemplateService
{
    /**
     * Field definitions categorized by subsection.
     */
    public const CATEGORY_FIELDS = [
        'header_intro' => [
            'label' => 'HEADER',
            'type' => 'combined',
            'field1_label' => 'Header Question (ends paragraph 1)',
            'field2_label' => 'Second Header Text (paragraph 2)',
            'description' => 'Combines individual category intro with question and second overview paragraph.',
        ],
        'compare_top_rated' => [
            'label' => 'LISTING',
            'type' => 'combined',
            'field1_label' => 'Compare Top-Rated Headline (H2)',
            'field2_label' => 'Compare Top-Rated Subtext',
            'description' => 'Headline and subtext for the top-rated providers section.',
        ],
        'top_10' => [
            'label' => 'TOP 10',
            'type' => 'combined',
            'field1_label' => 'Top 10 Headline (H2)',
            'field2_label' => 'Top 10 Subtext',
            'description' => 'Headline and subtext for the Top 10 comparison table section.',
        ],
        'pros_cons' => [
            'label' => 'PROS & CONS',
            'type' => 'combined',
            'field1_label' => 'Pros and Cons Headline (H2)',
            'field2_label' => 'Pros and Cons Subtext',
            'description' => 'Headline and subtext for the community feedback section.',
        ],
        'popular_comparisons' => [
            'label' => 'POPULAR COMP.',
            'type' => 'combined',
            'field1_label' => 'Popular Comparisons Headline (H2)',
            'field2_label' => 'Popular Comparisons Subtext',
            'description' => 'Headline and subtext for the popular side-by-side comparisons section.',
        ],
        'compare_tool' => [
            'label' => 'COMP. TOOL',
            'type' => 'combined',
            'field1_label' => 'Comparison Tool Headline (H2)',
            'field2_label' => 'Comparison Tool Subtext',
            'description' => 'Headline and subtext for interactive comparison tool box.',
        ],
        'explore_subcategories' => [
            'label' => 'CATEGORIES',
            'type' => 'combined',
            'field1_label' => 'Explore Subcategories Headline (H2)',
            'field2_label' => 'Explore Subcategories Subtext',
            'description' => 'Headline and subtext for explore subcategories/categories section.',
        ],
        'explore_link' => [
            'label' => 'EXPLORE LINK',
            'type' => 'single',
            'field1_label' => 'Link Text',
            'description' => 'Used on top-rated block link (e.g. See all [entities1_cap]).',
        ],
        'category_page_faqs' => [
            'label' => 'FAQS',
            'type' => 'combined',
            'field1_label' => 'Category FAQs Headline (H2)',
            'field2_label' => 'Category FAQs Subtext',
            'description' => 'Headline and subtext for the FAQs section on category and subcategory pages.',
        ],
        'category_faq_search' => [
            'label' => 'FAQ SEARCH',
            'type' => 'single',
            'field1_label' => 'Search Input Placeholder',
            'description' => 'Placeholder text for the category/subcategory FAQ search input.',
        ],
        'category_page_discussions' => [
            'label' => 'DISCUSSIONS',
            'type' => 'combined',
            'field1_label' => 'Discussions Headline (H2)',
            'field2_label' => 'Discussions Subtext',
            'description' => 'Headline and subtext for the community discussions section on category/subcategory pages.',
        ],
    ];

    public const CATEGORY_FAQ_FIELDS = [
        'faq_h1' => [
            'label' => 'H1 HEADLINE',
            'type' => 'single',
            'field1_label' => 'H1 Headline',
            'description' => 'Main H1 headline for the Category FAQs page.',
        ],
        'faq_header_intro' => [
            'label' => 'HEADER INTRO',
            'type' => 'combined',
            'field1_label' => 'Header1',
            'field2_label' => 'Header2',
            'description' => 'Main introductory paragraphs directly below the H1 headline.',
        ],
        'faq_search_headline' => [
            'label' => 'SEARCH HEADLINE',
            'type' => 'single',
            'field1_label' => 'Search Headline',
            'description' => 'Headline above the FAQ search box on the Category FAQs page.',
        ],
        'faq_search_placeholder' => [
            'label' => 'SEARCH PLACEHOLDER',
            'type' => 'single',
            'field1_label' => 'Search Input Placeholder',
            'description' => 'Placeholder text inside the FAQ search box on the Category FAQs page.',
        ],
        'faq_accordion_headline' => [
            'label' => 'FAQ LIST HEADLINE',
            'type' => 'single',
            'field1_label' => 'FAQ List Headline (H2)',
            'description' => 'Headline (H2) directly above the FAQ accordion list on the Category FAQs page.',
        ],
        'faq_explore' => [
            'label' => 'EXPLORE CATEGORIES',
            'type' => 'combined',
            'field1_label' => 'FAQ Explore Categories Headline (H2)',
            'field2_label' => 'FAQ Explore Categories Subtext',
            'description' => 'Headline and subtext above the explore subcategories on FAQ page.',
        ],
        'faq_page_discussions' => [
            'label' => 'DISCUSSIONS',
            'type' => 'combined',
            'field1_label' => 'Discussions Headline (H2)',
            'field2_label' => 'Discussions Subtext',
            'description' => 'Discussion section headline and subtext at the bottom of the Category FAQs page.',
        ],
    ];


    public const CATEGORY_DISCUSSIONS_FIELDS = [
        'discussions_h1' => [
            'label' => 'H1 headline',
            'type' => 'single',
            'field1_label' => 'H1 Headline',
            'description' => 'Main H1 headline for the Category Discussions page.',
        ],
        'discussions_subtext' => [
            'label' => 'Sub-headline',
            'type' => 'single',
            'field1_label' => 'Sub-headline',
            'description' => 'Sub-headline text below the H1.',
        ],
        'category_discussions_prompt_placeholder' => [
            'label' => 'Prompt Placeholder',
            'type' => 'single',
            'field1_label' => 'Prompt Placeholder',
            'description' => 'Placeholder text for the ask question / prompt input box.',
        ],
        'category_discussions_empty_headline' => [
            'label' => 'Empty Headline',
            'type' => 'single',
            'field1_label' => 'Empty State Headline',
            'description' => 'Headline displayed when no discussions have been started yet.',
        ],
    ];

    public const BUSINESS_PROFILE_FIELDS = [
        'profile_h1' => [
            'label' => 'H1 Headline',
            'type' => 'single',
            'field1_label' => 'H1 Headline',
            'description' => 'Main H1 headline for the Business Profile page.',
        ],
        'profile_subtext' => [
            'label' => 'Sub-headline',
            'type' => 'single',
            'field1_label' => 'Sub-headline',
            'description' => 'Sub-headline text below the H1.',
        ],
        'profile_what_is' => [
            'label' => 'WHAT IS HEADLINE',
            'type' => 'single',
            'field1_label' => 'Headline',
            'description' => 'Headline for the What is [business] section.',
        ],
        'profile_what_offers' => [
            'label' => 'WHAT OFFERS HEADLINE',
            'type' => 'single',
            'field1_label' => 'Headline',
            'description' => 'Headline for the What [business] offers section.',
        ],
        'profile_pros_cons' => [
            'label' => 'PROS & CONS',
            'type' => 'combined',
            'field1_label' => 'Pros & Cons Headline (H2)',
            'field2_label' => 'Upper text of Pros & Cons section',
            'description' => 'Headline and upper text for the Pros & Cons section.',
        ],
        'profile_pros_cons_under_10' => [
            'label' => 'PROS & CONS (< 10 REVIEWS)',
            'type' => 'combined',
            'field1_label' => 'Pros & Cons Headline (H2)',
            'field2_label' => 'Upper text of Pros & Cons section',
            'description' => 'Headline and upper text for the Pros & Cons section when a business has less than 10 published reviews (pros & cons from public available sources).',
        ],
        'profile_review_prompt' => [
            'label' => 'RECOMMEND BANNER',
            'type' => 'combined',
            'field1_label' => 'Headline',
            'field2_label' => 'Subtext',
            'description' => 'Headline and subtext for the review prompt / recommend banner.',
        ],
        'profile_alternatives' => [
            'label' => 'ALTERNATIVES SECTION',
            'type' => 'combined',
            'field1_label' => 'Alternatives Headline (H2)',
            'field2_label' => 'Alternatives Subtext',
            'description' => 'Headline and subtext for the Alternatives & Competitors section on the Business Profile page.',
        ],
        'profile_faqs' => [
            'label' => 'FAQS SECTION',
            'type' => 'combined',
            'field1_label' => 'FAQs Headline (H2)',
            'field2_label' => 'FAQs Subtext',
            'description' => 'Headline and subtext for the Frequently Asked Questions section on the Business Profile page.',
        ],
        'profile_compare' => [
            'label' => 'COMPARE SECTION',
            'type' => 'combined',
            'field1_label' => 'Compare Headline',
            'field2_label' => 'Compare Subtext',
            'description' => 'Headline and subtext for the comparison section.',
        ],
        'profile_reviews' => [
            'label' => 'REVIEWS SECTION',
            'type' => 'combined',
            'field1_label' => 'Reviews Headline (H2)',
            'field2_label' => 'Reviews Subtext',
            'description' => 'Headline and subtext for the User Reviews section on the Business Profile page.',
        ],
        'profile_discussions' => [
            'label' => 'DISCUSSIONS SECTION',
            'type' => 'combined',
            'field1_label' => 'Discussions Headline (H2)',
            'field2_label' => 'Discussions Subtext',
            'description' => 'Headline and subtext for the Discussions section on the Business Profile page.',
        ],
    ];

    public const BUSINESS_ALTERNATIVES_FIELDS = [
        'alternatives_h1' => [
            'label' => 'H1 Headline',
            'type' => 'single',
            'field1_label' => 'H1 Headline',
            'description' => 'Main H1 headline for the Business Alternatives page.',
        ],
        'alternatives_subtext' => [
            'label' => 'Sub-headline',
            'type' => 'single',
            'field1_label' => 'Sub-headline',
            'description' => 'Sub-headline text below the H1.',
        ],
    ];

    public const BUSINESS_FAQS_FIELDS = [
        'faqs_h1' => [
            'label' => 'H1 Headline',
            'type' => 'single',
            'field1_label' => 'H1 Headline',
            'description' => 'Main H1 headline for the Business FAQs page.',
        ],
        'faqs_subtext' => [
            'label' => 'Sub-headline',
            'type' => 'single',
            'field1_label' => 'Sub-headline',
            'description' => 'Sub-headline text below the H1.',
        ],
        'faq_search_headline' => [
            'label' => 'SEARCH HEADLINE',
            'type' => 'single',
            'field1_label' => 'Search Headline',
            'description' => 'Headline above the FAQ search box on the Business FAQs page.',
        ],
        'faq_search_placeholder' => [
            'label' => 'SEARCH PLACEHOLDER',
            'type' => 'single',
            'field1_label' => 'Search Input Placeholder',
            'description' => 'Placeholder text inside the FAQ search box on the Business FAQs page.',
        ],
        'faq_accordion_headline' => [
            'label' => 'FAQ LIST HEADLINE',
            'type' => 'single',
            'field1_label' => 'FAQ List Headline (H2)',
            'description' => 'Headline (H2) directly above the FAQ accordion list on the Business FAQs page.',
        ],
        'faq_explore' => [
            'label' => 'EXPLORE CATEGORIES',
            'type' => 'combined',
            'field1_label' => 'Explore Categories Headline (H2)',
            'field2_label' => 'Explore Categories Subtext',
            'description' => 'Headline and subtext above the explore related categories on the Business FAQs page.',
        ],
        'faq_page_discussions' => [
            'label' => 'DISCUSSIONS SECTION',
            'type' => 'combined',
            'field1_label' => 'Discussions Headline (H2)',
            'field2_label' => 'Discussions Subtext',
            'description' => 'Discussions section headline and subtext on the Business FAQs page.',
        ],
        'faq_discussions_prompt_placeholder' => [
            'label' => 'PROMPT PLACEHOLDER',
            'type' => 'single',
            'field1_label' => 'Prompt Placeholder',
            'description' => 'Placeholder text for the ask question / prompt input box on the Business FAQs page.',
        ],
    ];

    public const BUSINESS_COMPARE_FIELDS = [
        'compare_h1' => [
            'label' => 'H1 Headline',
            'type' => 'single',
            'field1_label' => 'H1 Headline',
            'description' => 'Main H1 headline for the Business Compare page.',
        ],
        'compare_subtext' => [
            'label' => 'Sub-headline',
            'type' => 'single',
            'field1_label' => 'Sub-headline',
            'description' => 'Sub-headline text below the H1.',
        ],
        'compare_overview' => [
            'label' => 'OVERVIEW / INTRO SECTION',
            'type' => 'combined',
            'field1_label' => 'Overview Headline (H2)',
            'field2_label' => 'Overview Description / Text',
            'description' => 'Headline and description text for the comparison intro section.',
        ],
        'compare_interactive_tool' => [
            'label' => 'COMPARE WITH ANY PROVIDER',
            'type' => 'combined',
            'field1_label' => 'Compare Tool Headline (H3)',
            'field2_label' => 'Compare Tool Subtext',
            'description' => 'Headline and subtext for the interactive compare tool box.',
        ],
        'compare_search_placeholder' => [
            'label' => 'COMPARE SEARCH PLACEHOLDER',
            'type' => 'single',
            'field1_label' => 'Search Input Placeholder',
            'description' => 'Placeholder text inside the interactive compare search box.',
        ],
        'compare_popular' => [
            'label' => 'POPULAR COMPARISONS',
            'type' => 'combined',
            'field1_label' => 'Popular Comparisons Headline (H2)',
            'field2_label' => 'Popular Comparisons Subtext',
            'description' => 'Headline and subtext for the Popular comparisons section.',
        ],
        'compare_recent' => [
            'label' => 'RECENTLY VIEWED COMPARISONS',
            'type' => 'combined',
            'field1_label' => 'Recently Viewed Headline (H2)',
            'field2_label' => 'Recently Viewed Subtext',
            'description' => 'Headline and subtext for the Recently viewed comparisons section.',
        ],
        'compare_explore' => [
            'label' => 'EXPLORE CATEGORIES',
            'type' => 'combined',
            'field1_label' => 'Explore Categories Headline (H2)',
            'field2_label' => 'Explore Categories Subtext',
            'description' => 'Headline and subtext for the Explore subcategories section.',
        ],
        'compare_page_discussions' => [
            'label' => 'DISCUSSIONS SECTION',
            'type' => 'combined',
            'field1_label' => 'Discussions Headline (H2)',
            'field2_label' => 'Discussions Subtext',
            'description' => 'Discussions section headline and subtext on the Business Comparisons page.',
        ],
        'compare_discussions_prompt_placeholder' => [
            'label' => 'DISCUSSIONS PROMPT PLACEHOLDER',
            'type' => 'single',
            'field1_label' => 'Prompt Placeholder',
            'description' => 'Placeholder text for the ask question / prompt input box on the Business Comparisons page.',
        ],
    ];

    public const COMPARISON_FIELDS = [
        'comparison_h1' => [
            'label' => 'H1 HEADLINE & SUBTEXT',
            'type' => 'combined',
            'field1_label' => 'Comparison Headline (H1)',
            'field2_label' => 'Sub-headline / Description',
            'description' => 'Main headline and subtext displayed at the top of the A vs B comparison page.',
        ],
        'comparison_pros_cons' => [
            'label' => 'PROS & CONS SECTION',
            'type' => 'single',
            'field1_label' => 'Pros & Cons Headline (H2)',
            'description' => 'Headline displayed directly above the pros and cons comparison grid.',
        ],
        'comparison_reviews' => [
            'label' => 'REVIEWS SECTION',
            'type' => 'single',
            'field1_label' => 'Reviews Headline (H2)',
            'description' => 'Headline displayed above the customer reviews section on the comparison page.',
        ],
        'comparison_alternatives' => [
            'label' => 'ALTERNATIVES SECTION',
            'type' => 'single',
            'field1_label' => 'Alternatives Headline (H2)',
            'description' => 'Headline displayed above the alternatives section on the comparison page.',
        ],
    ];

    public const BUSINESS_REVIEWS_FIELDS = [
        'reviews_h1' => [
            'label' => 'H1 Headline',
            'type' => 'single',
            'field1_label' => 'H1 Headline',
            'description' => 'Main H1 headline for the Business Reviews page.',
        ],
        'reviews_subtext' => [
            'label' => 'Sub-headline',
            'type' => 'single',
            'field1_label' => 'Sub-headline',
            'description' => 'Sub-headline text below the H1.',
        ],
    ];

    public const BUSINESS_DISCUSSIONS_FIELDS = [
        'discussions_h1' => [
            'label' => 'H1 Headline',
            'type' => 'single',
            'field1_label' => 'H1 Headline',
            'description' => 'Main H1 headline for the Business Discussions page.',
        ],
        'discussions_subtext' => [
            'label' => 'Sub-headline',
            'type' => 'single',
            'field1_label' => 'Sub-headline',
            'description' => 'Sub-headline text below the H1.',
        ],
        'discussions_prompt_placeholder' => [
            'label' => 'Prompt Placeholder',
            'type' => 'single',
            'field1_label' => 'Prompt Placeholder',
            'description' => 'Placeholder text for the ask question / prompt input box.',
        ],
        'discussions_empty_headline' => [
            'label' => 'Empty Headline',
            'type' => 'single',
            'field1_label' => 'Empty State Headline',
            'description' => 'Headline displayed when no discussions have been started yet.',
        ],
    ];

    public const FEATURE_FIELDS = [
        'feature_h1' => [
            'label' => 'H1',
            'type' => 'single',
            'field1_label' => 'Feature H1 Headline',
            'description' => 'Main page title for Feature pages (e.g. Best [category_tc] with [feature_tc] [country2] [year]).',
        ],
        'header_intro' => [
            'label' => 'HEADER',
            'type' => 'combined',
            'field1_label' => 'Header Question (ends paragraph 1)',
            'field2_label' => 'Second Header Text (paragraph 2)',
            'description' => 'Combines individual feature intro with question and second overview paragraph.',
        ],
        'compare_top_rated' => [
            'label' => 'LISTING',
            'type' => 'combined',
            'field1_label' => 'Compare Top-Rated Headline (H2)',
            'field2_label' => 'Compare Top-Rated Subtext',
            'description' => 'Headline and subtext for the top-rated providers section on feature page.',
        ],
        'top_10' => [
            'label' => 'TOP 10',
            'type' => 'combined',
            'field1_label' => 'Top 10 Headline (H2)',
            'field2_label' => 'Top 10 Subtext',
            'description' => 'Headline and subtext for the Top 10 comparison table section on feature page.',
        ],
        'pros_cons' => [
            'label' => 'PROS & CONS',
            'type' => 'combined',
            'field1_label' => 'Pros and Cons Headline (H2)',
            'field2_label' => 'Pros and Cons Subtext',
            'description' => 'Headline and subtext for the community feedback section on feature page.',
        ],
        'popular_comparisons' => [
            'label' => 'POPULAR COMP.',
            'type' => 'combined',
            'field1_label' => 'Popular Comparisons Headline (H2)',
            'field2_label' => 'Popular Comparisons Subtext',
            'description' => 'Headline and subtext for the popular side-by-side comparisons section on feature page.',
        ],
        'compare_tool' => [
            'label' => 'COMP. TOOL',
            'type' => 'combined',
            'field1_label' => 'Comparison Tool Headline (H2)',
            'field2_label' => 'Comparison Tool Subtext',
            'description' => 'Headline and subtext for interactive comparison tool box on feature page.',
        ],
        'explore_subcategories' => [
            'label' => 'CATEGORIES',
            'type' => 'combined',
            'field1_label' => 'Explore Subcategories Headline (H2)',
            'field2_label' => 'Explore Subcategories Subtext',
            'description' => 'Headline and subtext for explore subcategories/categories section on feature page.',
        ],
    ];

    /**
     * Get or randomly assign & permanently lock a template for an entity.
     */
    /**
     * Determine the section_group based on entityType and fieldKey.
     */
    public static function determineSectionGroup(string $entityType, string $fieldKey, ?string $explicitSectionGroup = null): ?string
    {
        if ($explicitSectionGroup) {
            return $explicitSectionGroup;
        }

        if ($entityType === 'feature') {
            return 'features';
        }

        if ($entityType === 'business') {
            if (array_key_exists($fieldKey, self::BUSINESS_DISCUSSIONS_FIELDS)) {
                return 'business_discussions';
            }
            if (array_key_exists($fieldKey, self::BUSINESS_PROFILE_FIELDS)) {
                return 'business_profile';
            }
            if (array_key_exists($fieldKey, self::BUSINESS_ALTERNATIVES_FIELDS)) {
                return 'business_alternatives';
            }
            if (array_key_exists($fieldKey, self::BUSINESS_FAQS_FIELDS)) {
                return 'business_faqs';
            }
            if (array_key_exists($fieldKey, self::BUSINESS_COMPARE_FIELDS)) {
                return 'business_compare';
            }
            if (array_key_exists($fieldKey, self::BUSINESS_REVIEWS_FIELDS)) {
                return 'business_reviews';
            }
        }

        if ($entityType === 'category') {
            if (array_key_exists($fieldKey, self::COMPARISON_FIELDS)) {
                return 'comparison';
            }
            if (array_key_exists($fieldKey, self::CATEGORY_DISCUSSIONS_FIELDS)) {
                return 'category_discussions';
            }
            if (array_key_exists($fieldKey, self::CATEGORY_FAQ_FIELDS)) {
                return 'category_faqs';
            }
            if (array_key_exists($fieldKey, self::CATEGORY_FIELDS)) {
                return 'category';
            }
        }

        return null;
    }

    /**
     * Retrieves the locked template assignment for frontend display.
     * If no assignment exists (never assigned, or previously assigned option was deleted):
     * Defaults to Option #1 for that section_group and field_key WITHOUT creating an assignment in DB.
     * This ensures backend continues to show "-" while frontend safely displays Option 1.
     */
    public static function getTemplateForResolution(string $entityType, int $entityId, string $fieldKey, int $langId = 1, ?string $sectionGroup = null): ?ContentTemplate
    {
        $sectionGroup = self::determineSectionGroup($entityType, $fieldKey, $sectionGroup);

        // 1. Check if a valid, active assignment exists in the database
        $assignment = ContentTemplateAssignment::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('lang_id', $langId)
            ->where('field_key', $fieldKey)
            ->with('template')
            ->first();

        // Fallback for business entity: if no assignment under 'business', check category assignment for business's subcategories / category
        if (!$assignment && $entityType === 'business') {
            $biz = Business::find($entityId);
            if ($biz) {
                $candidateCatIds = [];
                if ($biz->subCategories && $biz->subCategories->isNotEmpty()) {
                    foreach ($biz->subCategories as $sc) {
                        $candidateCatIds[] = $sc->id;
                    }
                }
                if ($biz->category_id) {
                    $candidateCatIds[] = $biz->category_id;
                }

                foreach ($candidateCatIds as $cId) {
                    $candidateAsgn = ContentTemplateAssignment::where('entity_type', 'category')
                        ->where('entity_id', $cId)
                        ->where('lang_id', $langId)
                        ->where('field_key', $fieldKey)
                        ->with('template')
                        ->first();
                    if ($candidateAsgn && $candidateAsgn->template && $candidateAsgn->template->is_active) {
                        if (!$sectionGroup || $candidateAsgn->template->section_group === $sectionGroup) {
                            $assignment = $candidateAsgn;
                            break;
                        }
                    }
                }
            }
        }

        if ($assignment && $assignment->template && $assignment->template->is_active) {
            // Verify that the assigned template actually belongs to the required section_group
            if (!$sectionGroup || $assignment->template->section_group === $sectionGroup) {
                return $assignment->template;
            }
        }

        // 2. Default to Option #1 on frontend if not assigned or deleted
        // Note: We DO NOT persist this to ContentTemplateAssignment so backend continues to show "-"
        $query = ContentTemplate::where('field_key', $fieldKey)
            ->where('option_number', 1)
            ->where('lang_id', $langId)
            ->where('is_active', true);
        if ($sectionGroup) {
            $query->where('section_group', $sectionGroup);
        }
        $template = $query->first();

        // Fallback to lang_id = 1 if current language has no Option #1
        if (!$template && $langId !== 1) {
            $fallbackQuery = ContentTemplate::where('field_key', $fieldKey)
                ->where('option_number', 1)
                ->where('lang_id', 1)
                ->where('is_active', true);
            if ($sectionGroup) {
                $fallbackQuery->where('section_group', $sectionGroup);
            }
            $template = $fallbackQuery->first();
        }

        // If specifically option_number 1 doesn't exist, pick the lowest available option
        if (!$template) {
            $lowestQuery = ContentTemplate::where('field_key', $fieldKey)
                ->where('lang_id', $langId)
                ->where('is_active', true)
                ->orderBy('option_number', 'asc');
            if ($sectionGroup) {
                $lowestQuery->where('section_group', $sectionGroup);
            }
            $template = $lowestQuery->first();

            if (!$template && $langId !== 1) {
                $lowestFallback = ContentTemplate::where('field_key', $fieldKey)
                    ->where('lang_id', 1)
                    ->where('is_active', true)
                    ->orderBy('option_number', 'asc');
                if ($sectionGroup) {
                    $lowestFallback->where('section_group', $sectionGroup);
                }
                $template = $lowestFallback->first();
            }
        }

        return $template;
    }

    public static function getOrAssignTemplate(string $entityType, int $entityId, string $fieldKey, int $langId = 1, ?string $sectionGroup = null): ?ContentTemplate
    {
        $sectionGroup = self::determineSectionGroup($entityType, $fieldKey, $sectionGroup);

        // 1. Check if an assignment is already locked in DB
        $assignment = ContentTemplateAssignment::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('lang_id', $langId)
            ->where('field_key', $fieldKey)
            ->with('template')
            ->first();

        if ($assignment && $assignment->template && $assignment->template->is_active) {
            if (!$sectionGroup || $assignment->template->section_group === $sectionGroup) {
                return $assignment->template;
            }
        }

        // 2. Fetch active templates for this field_key & lang_id (and section_group if known)
        $query = ContentTemplate::where('field_key', $fieldKey)
            ->where('lang_id', $langId)
            ->where('is_active', true);
        if ($sectionGroup) {
            $query->where('section_group', $sectionGroup);
        }
        $templates = $query->get();

        // Fallback to lang_id = 1 if current language has no templates yet
        if ($templates->isEmpty() && $langId !== 1) {
            $fallbackQuery = ContentTemplate::where('field_key', $fieldKey)
                ->where('lang_id', 1)
                ->where('is_active', true);
            if ($sectionGroup) {
                $fallbackQuery->where('section_group', $sectionGroup);
            }
            $templates = $fallbackQuery->get();
        }

        if ($templates->isEmpty()) {
            return null;
        }

        // 3. Select randomly and lock PERMANENTLY into database
        $chosen = $templates->random();

        ContentTemplateAssignment::updateOrCreate(
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'lang_id' => $langId,
                'field_key' => $fieldKey,
            ],
            [
                'template_id' => $chosen->id,
            ]
        );

        return $chosen;
    }

    /**
     * Assign missing templates for all categories and subcategories.
     */
    public static function assignMissingForCategories(int $langId = 1, ?string $sectionGroup = null): int
    {
        $allCategoryIds = Category::pluck('id');

        if ($sectionGroup === 'category_discussions') {
            $allFieldKeys = array_keys(self::CATEGORY_DISCUSSIONS_FIELDS);
        } elseif ($sectionGroup === 'category_faqs') {
            $allFieldKeys = array_keys(self::CATEGORY_FAQ_FIELDS);
        } elseif ($sectionGroup === 'category') {
            $allFieldKeys = array_keys(self::CATEGORY_FIELDS);
        } elseif ($sectionGroup === 'comparison') {
            $allFieldKeys = array_keys(self::COMPARISON_FIELDS);
        } else {
            $allFieldKeys = array_merge(
                array_keys(self::CATEGORY_FIELDS),
                array_keys(self::CATEGORY_FAQ_FIELDS),
                array_keys(self::CATEGORY_DISCUSSIONS_FIELDS),
                array_keys(self::COMPARISON_FIELDS)
            );
        }

        $assignedCount = 0;
        foreach ($allCategoryIds as $catId) {
            foreach ($allFieldKeys as $fieldKey) {
                $exists = ContentTemplateAssignment::where('entity_type', 'category')
                    ->where('entity_id', $catId)
                    ->where('lang_id', $langId)
                    ->where('field_key', $fieldKey)
                    ->exists();

                if (!$exists) {
                    $template = self::getOrAssignTemplate('category', $catId, $fieldKey, $langId, $sectionGroup);
                    if ($template) {
                        $assignedCount++;
                    }
                }
            }
        }

        return $assignedCount;
    }

    /**
     * Assign missing templates for all businesses.
     */
    public static function assignMissingForBusinesses(int $langId = 1, ?string $sectionGroup = null): int
    {
        $allBusinessIds = \App\Models\Business::pluck('id');

        if ($sectionGroup === 'business_profile') {
            $allFieldKeys = array_keys(self::BUSINESS_PROFILE_FIELDS);
        } elseif ($sectionGroup === 'business_alternatives') {
            $allFieldKeys = array_keys(self::BUSINESS_ALTERNATIVES_FIELDS);
        } elseif ($sectionGroup === 'business_faqs') {
            $allFieldKeys = array_keys(self::BUSINESS_FAQS_FIELDS);
        } elseif ($sectionGroup === 'business_compare') {
            $allFieldKeys = array_keys(self::BUSINESS_COMPARE_FIELDS);
        } elseif ($sectionGroup === 'business_reviews') {
            $allFieldKeys = array_keys(self::BUSINESS_REVIEWS_FIELDS);
        } elseif ($sectionGroup === 'business_discussions') {
            $allFieldKeys = array_keys(self::BUSINESS_DISCUSSIONS_FIELDS);
        } else {
            $allFieldKeys = array_merge(
                array_keys(self::BUSINESS_PROFILE_FIELDS),
                array_keys(self::BUSINESS_ALTERNATIVES_FIELDS),
                array_keys(self::BUSINESS_FAQS_FIELDS),
                array_keys(self::BUSINESS_COMPARE_FIELDS),
                array_keys(self::BUSINESS_REVIEWS_FIELDS),
                array_keys(self::BUSINESS_DISCUSSIONS_FIELDS)
            );
        }

        $assignedCount = 0;
        foreach ($allBusinessIds as $bizId) {
            foreach ($allFieldKeys as $fieldKey) {
                $exists = ContentTemplateAssignment::where('entity_type', 'business')
                    ->where('entity_id', $bizId)
                    ->where('lang_id', $langId)
                    ->where('field_key', $fieldKey)
                    ->exists();

                if (!$exists) {
                    $template = self::getOrAssignTemplate('business', $bizId, $fieldKey, $langId, $sectionGroup);
                    if ($template) {
                        $assignedCount++;
                    }
                }
            }
        }

        return $assignedCount;
    }

    /**
     * Assign missing templates for all features.
     */
    public static function assignMissingForFeatures(int $langId = 1): int
    {
        $allFeatureIds = Feature::pluck('id');
        $allFieldKeys = array_keys(self::FEATURE_FIELDS);
        $assignedCount = 0;

        foreach ($allFeatureIds as $featId) {
            foreach ($allFieldKeys as $fieldKey) {
                $exists = ContentTemplateAssignment::where('entity_type', 'feature')
                    ->where('entity_id', $featId)
                    ->where('lang_id', $langId)
                    ->where('field_key', $fieldKey)
                    ->exists();

                if (!$exists) {
                    $template = self::getOrAssignTemplate('feature', $featId, $fieldKey, $langId);
                    if ($template) {
                        $assignedCount++;
                    }
                }
            }
        }

        return $assignedCount;
    }

    /**
     * Manually assign/lock a specific template to an entity.
     */
    public static function manuallyAssignTemplate(string $entityType, int $entityId, string $fieldKey, int $templateId, int $langId = 1): ContentTemplateAssignment
    {
        return ContentTemplateAssignment::updateOrCreate(
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'lang_id' => $langId,
                'field_key' => $fieldKey,
            ],
            [
                'template_id' => $templateId,
            ]
        );
    }

    /**
     * Replace all dynamic placeholders using category entity values.
     */
    /**
     * Safely resolve CategoryTranslation for a category and language.
     */
    /**
     * Ensure templates exist for a given section group and language.
     * If the language has 0 templates for this section, clones the base templates from lang_id = 1.
     */

    public static function getCriteriaRanges(): array
    {
        $tExcellent = floatval(static_text('rating_threshold_excellent') ?: 4.5);
        $tGreat = floatval(static_text('rating_threshold_great') ?: 4.0);
        $tGood = floatval(static_text('rating_threshold_good') ?: 3.0);
        $tSatisfactory = floatval(static_text('rating_threshold_satisfactory') ?: 2.0);
        $tPoor = floatval(static_text('rating_threshold_poor') ?: 1.0);

        return [
            '1' => [
                'min' => $tPoor,
                'max' => round($tSatisfactory - 0.1, 1),
                'label' => sprintf('Rating %.1f - %.1f', $tPoor, $tSatisfactory - 0.1),
            ],
            '2' => [
                'min' => $tSatisfactory,
                'max' => round($tGood - 0.1, 1),
                'label' => sprintf('Rating %.1f - %.1f', $tSatisfactory, $tGood - 0.1),
            ],
            '3' => [
                'min' => $tGood,
                'max' => round($tGreat - 0.1, 1),
                'label' => sprintf('Rating %.1f - %.1f', $tGood, $tGreat - 0.1),
            ],
            '4' => [
                'min' => $tGreat,
                'max' => round($tExcellent - 0.1, 1),
                'label' => sprintf('Rating %.1f - %.1f', $tGreat, $tExcellent - 0.1),
            ],
            '5' => [
                'min' => $tExcellent,
                'max' => 5.0,
                'label' => sprintf('Rating %.1f - 5.0', $tExcellent),
            ],
        ];
    }

    public static function getCriteriaRangeKeyForRating(float $score): string
    {
        $ranges = self::getCriteriaRanges();
        if ($score >= $ranges['5']['min']) {
            return '5';
        }
        if ($score >= $ranges['4']['min']) {
            return '4';
        }
        if ($score >= $ranges['3']['min']) {
            return '3';
        }
        if ($score >= $ranges['2']['min']) {
            return '2';
        }
        return '1';
    }

    public static function getCriteriaFields(int $criteriaId = null): array
    {
        $fields = [];
        $ranges = self::getCriteriaRanges();

        if ($criteriaId) {
            foreach ($ranges as $rKey => $rData) {
                $rLabel = $rData['label'];
                $fields["criteria_{$criteriaId}_range_{$rKey}"] = [
                    'label' => $rLabel,
                    'type' => 'combined',
                    'field1_label' => 'Upper text',
                    'field2_label' => 'Lower text',
                    'description' => "Template for {$rLabel}",
                ];
            }
        }
        return $fields;
    }

    public static function copyCriteriaTemplates(string $fromSectionGroup, string $toSectionGroup, int $langId): int
    {
        $sourceTemplates = ContentTemplate::where('section_group', $fromSectionGroup)
            ->where('lang_id', $langId)
            ->get();

        if ($sourceTemplates->isEmpty() && $langId !== 1) {
            $sourceTemplates = ContentTemplate::where('section_group', $fromSectionGroup)
                ->where('lang_id', 1)
                ->get();
        }

        $copiedCount = 0;
        foreach ($sourceTemplates as $src) {
            ContentTemplate::create([
                'section_group' => $toSectionGroup,
                'field_key'     => $src->field_key,
                'option_number' => $src->option_number,
                'headline'      => $src->headline,
                'subtext'       => $src->subtext,
                'is_active'     => $src->is_active,
                'lang_id'       => $langId,
            ]);
            $copiedCount++;
        }

        return $copiedCount;
    }

    /**
     * Seeds default category template options for FAQ and Discussions if missing.
     */
    public static function seedDefaultCategoryOptionsIfMissing(): void
    {
        $defaults = [
            'category_page_faqs' => [
                1 => [
                    'headline' => 'Frequently asked questions',
                    'subtext' => 'Common questions and answers about [category_lc] solutions and comparison criteria.',
                ],
                2 => [
                    'headline' => 'Frequently Asked Questions About [category_cap]',
                    'subtext' => 'Find answers to common questions about choosing and comparing [entities1_lc].',
                ],
                3 => [
                    'headline' => '[category_cap] FAQs',
                    'subtext' => 'Everything you need to know about [category_lc] features, pricing, and provider comparisons.',
                ],
                4 => [
                    'headline' => 'Questions & Answers on [category_cap]',
                    'subtext' => 'Key insights and answers to frequent questions regarding [entities1_lc] on Localio.',
                ],
            ],
            'category_faq_search' => [
                1 => [
                    'headline' => 'Search [category_lc] FAQs...',
                    'subtext' => null,
                ],
                2 => [
                    'headline' => 'Search [category_cap] FAQs...',
                    'subtext' => null,
                ],
                3 => [
                    'headline' => 'Search FAQs for [category_lc]...',
                    'subtext' => null,
                ],
                4 => [
                    'headline' => 'Search [entities1_lc] FAQs...',
                    'subtext' => null,
                ],
            ],
            'category_page_discussions' => [
                1 => [
                    'headline' => '[category_cap] discussions ([count])',
                    'subtext' => 'Explore questions, experiences, and opinions shared by Localio users.',
                ],
                2 => [
                    'headline' => 'Join the [category_lc] discussion ([count])',
                    'subtext' => 'Ask questions, share advice, and connect with other users exploring [entities1_lc].',
                ],
                3 => [
                    'headline' => 'Community discussions on [category_cap] ([count])',
                    'subtext' => 'Discover real experiences, troubleshooting tips, and user opinions about [entities1_lc].',
                ],
                4 => [
                    'headline' => '[category_tc] Community Discussions ([count])',
                    'subtext' => 'Explore community feedback, questions, and insights from verified Localio members.',
                ],
            ],
        ];

        foreach ($defaults as $fieldKey => $options) {
            $hasAny = ContentTemplate::where('section_group', 'category')
                ->where('field_key', $fieldKey)
                ->where('lang_id', 1)
                ->exists();

            if (!$hasAny) {
                foreach ($options as $optNum => $optData) {
                    ContentTemplate::create([
                        'section_group' => 'category',
                        'field_key'     => $fieldKey,
                        'option_number' => $optNum,
                        'headline'      => $optData['headline'],
                        'subtext'       => $optData['subtext'],
                        'is_active'     => true,
                        'lang_id'       => 1,
                    ]);
                }
            }
        }
    }

    /**
     * Seeds default category FAQ template options if missing.
     */
    public static function seedDefaultCategoryFaqOptionsIfMissing(): void
    {
        $defaults = [
            'faq_search_headline' => [
                1 => 'Search [category_cap] FAQs',
                2 => 'Search [category_tc] Questions & Answers',
                3 => 'Find Answers in [category_cap] FAQs',
                4 => 'Quick Search: [category_cap] FAQs',
            ],
            'faq_search_placeholder' => [
                1 => 'Search questions...',
                2 => 'Search FAQs for [category_lc]...',
                3 => 'Search questions about [category_lc]...',
                4 => 'Type a question or topic...',
            ],
            'faq_accordion_headline' => [
                1 => 'Frequently asked questions',
                2 => 'Frequently Asked Questions About [category_cap]',
                3 => '[category_cap] FAQs & Answers',
                4 => 'Common Questions About [category_tc]',
            ],
            'faq_page_discussions' => [
                1 => [
                    'headline' => '[category_cap] discussions ([count])',
                    'subtext' => 'Explore questions, experiences, and opinions shared by Localio users.',
                ],
                2 => [
                    'headline' => 'Join the [category_lc] discussion ([count])',
                    'subtext' => 'Ask questions, share advice, and connect with other users exploring [entities1_lc].',
                ],
                3 => [
                    'headline' => 'Community discussions on [category_cap] ([count])',
                    'subtext' => 'Discover real experiences, troubleshooting tips, and user opinions about [entities1_lc].',
                ],
                4 => [
                    'headline' => '[category_tc] Community Discussions ([count])',
                    'subtext' => 'Explore community feedback, questions, and insights from verified Localio members.',
                ],
            ],
        ];

        foreach ($defaults as $fieldKey => $options) {
            $hasAny = ContentTemplate::where('section_group', 'category_faqs')
                ->where('field_key', $fieldKey)
                ->where('lang_id', 1)
                ->exists();

            if (!$hasAny) {
                foreach ($options as $optNum => $data) {
                    $headlineText = is_array($data) ? ($data['headline'] ?? '') : $data;
                    $subtextText = is_array($data) ? ($data['subtext'] ?? null) : null;
                    ContentTemplate::create([
                        'section_group' => 'category_faqs',
                        'field_key'     => $fieldKey,
                        'option_number' => $optNum,
                        'headline'      => $headlineText,
                        'subtext'       => $subtextText,
                        'is_active'     => true,
                        'lang_id'       => 1,
                    ]);
                }
            }
        }
    }

    /**
     * Seeds default Business Profile template options for reviews and discussions if missing.
     */
    /**
     * Seeds default Category Discussions template options if missing.
     */
    public static function seedDefaultCategoryDiscussionsOptionsIfMissing(): void
    {
        $defaults = [
            'category_discussions_prompt_placeholder' => [
                1 => 'Ask a question or share something about [category_cap]',
                2 => 'Have a question about [category_cap]? Ask the community',
                3 => 'Share your thoughts or ask a question about [category_cap]',
                4 => 'Start a conversation or ask about [category_cap]...',
            ],
            'category_discussions_empty_headline' => [
                1 => 'No discussions about [category_cap].',
                2 => 'There are no discussions about [category_cap] yet.',
                3 => 'No questions or discussions for [category_cap] yet.',
                4 => 'Be the first to discuss [category_cap].',
            ],
        ];

        foreach ($defaults as $fieldKey => $options) {
            $hasAny = ContentTemplate::where('section_group', 'category_discussions')
                ->where('field_key', $fieldKey)
                ->where('lang_id', 1)
                ->exists();

            if (!$hasAny) {
                foreach ($options as $optNum => $headlineText) {
                    ContentTemplate::create([
                        'section_group' => 'category_discussions',
                        'field_key'     => $fieldKey,
                        'option_number' => $optNum,
                        'headline'      => $headlineText,
                        'subtext'       => null,
                        'is_active'     => true,
                        'lang_id'       => 1,
                    ]);
                }
            }
        }
    }

    /**
     * Seeds default Business Discussions template options if missing.
     */
    public static function seedDefaultBusinessDiscussionsOptionsIfMissing(): void
    {
        $defaults = [
            'discussions_prompt_placeholder' => [
                1 => 'Ask a question or share something about [business]',
                2 => 'Have a question about [business]? Ask the community',
                3 => 'Share your thoughts or ask a question about [business]',
                4 => 'Start a conversation or ask about [business]...',
            ],
            'discussions_empty_headline' => [
                1 => 'No discussions about [business].',
                2 => 'There are no discussions about [business] yet.',
                3 => 'No questions or discussions for [business] yet.',
                4 => 'Be the first to discuss [business].',
            ],
        ];

        foreach ($defaults as $fieldKey => $options) {
            $hasAny = ContentTemplate::where('section_group', 'business_discussions')
                ->where('field_key', $fieldKey)
                ->where('lang_id', 1)
                ->exists();

            if (!$hasAny) {
                foreach ($options as $optNum => $headlineText) {
                    ContentTemplate::create([
                        'section_group' => 'business_discussions',
                        'field_key'     => $fieldKey,
                        'option_number' => $optNum,
                        'headline'      => $headlineText,
                        'subtext'       => null,
                        'is_active'     => true,
                        'lang_id'       => 1,
                    ]);
                }
            }
        }
    }

    public static function seedDefaultBusinessProfileOptionsIfMissing(): void
    {
        $defaults = [
            'profile_pros_cons' => [
                1 => [
                    'headline' => 'Pros & cons users mention most',
                    'subtext' => '<p>See the strengths and drawbacks Localio users mention most often in their reviews. We group the recurring pros and cons in one place to show what users love and what could be improved.</p>',
                ],
                2 => [
                    'headline' => 'Pros & cons users mention most',
                    'subtext' => '<p>These are the strengths and drawbacks mentioned most often in Localio user reviews. We bring the recurring pros and cons together to show what stands out.</p>',
                ],
                3 => [
                    'headline' => 'Pros & cons users mention most',
                    'subtext' => '<p>The strengths and drawbacks that appear most often in Localio user reviews. We group the recurring pros and cons so you can quickly see what users mention most.</p>',
                ],
                4 => [
                    'headline' => 'Pros & cons users mention most',
                    'subtext' => '<p>The strengths and drawbacks mentioned most often about [business], based on Localio user reviews. We bring the recurring pros and cons together in one place.</p>',
                ],
            ],
            'profile_pros_cons_under_10' => [
                1 => [
                    'headline' => 'Pros & cons from public sources',
                    'subtext' => '<p>These strengths and drawbacks are gathered from publicly available sources across the web, as this business currently has fewer than 10 published community reviews.</p>',
                ],
                2 => [
                    'headline' => 'Key pros & cons from public feedback',
                    'subtext' => '<p>Based on public feedback and web sources, here are the most frequently highlighted advantages and potential drawbacks of [business].</p>',
                ],
                3 => [
                    'headline' => 'Summary of pros & cons from public sources',
                    'subtext' => '<p>Compiled from publicly available online feedback and reviews to highlight the recurring strengths and limitations of [business].</p>',
                ],
                4 => [
                    'headline' => 'Pros & cons from web sources',
                    'subtext' => '<p>A curated overview of recurring pros and cons gathered from public web sources to help you evaluate [business] while community reviews grow.</p>',
                ],
            ],
            'profile_review_prompt' => [
                1 => [
                    'headline' => 'Would you recommend [business] to others?',
                    'subtext' => '<p>Share your experience with others.</p>',
                ],
                2 => [
                    'headline' => 'Have you used [business]?',
                    'subtext' => '<p>Share your experience with others.</p>',
                ],
                3 => [
                    'headline' => 'How was your experience with [business]?',
                    'subtext' => '<p>Share your experience with others.</p>',
                ],
                4 => [
                    'headline' => 'Would you suggest [business] to a friend or colleague?',
                    'subtext' => '<p>Share your experience with others.</p>',
                ],
            ],
            'profile_alternatives' => [
                1 => [
                    'headline' => '[business] Alternatives & Competitors',
                    'subtext' => '<p>Based on other buyer\'s searches, these are the products that could be a good fit for you.</p>',
                ],
                2 => [
                    'headline' => 'Best Alternatives to [business]',
                    'subtext' => '<p>Explore top-rated competitors and alternative solutions that match your business needs.</p>',
                ],
                3 => [
                    'headline' => '[business] Competitors & Similar Software',
                    'subtext' => '<p>Compare popular alternatives to [business] based on verified reviews and features.</p>',
                ],
            ],
            'profile_faqs' => [
                1 => [
                    'headline' => 'Frequently asked questions',
                    'subtext' => '<p>Everything you need to know about [business] and its services.</p>',
                ],
                2 => [
                    'headline' => 'Frequently Asked Questions About [business]',
                    'subtext' => '<p>Find quick answers to common questions about [business] features, pricing, and support.</p>',
                ],
                3 => [
                    'headline' => '[business] FAQs & Common Questions',
                    'subtext' => '<p>Got questions about [business]? Explore answers to the most frequently asked questions.</p>',
                ],
            ],
            'profile_what_offers' => [
                1 => 'What [business] offers',
                2 => 'What does [business] offer?',
            ],
            'profile_reviews' => [
                1 => [
                    'headline' => '[business] user reviews ([count])',
                    'subtext' => '<p>Explore verified ratings, pros & cons, and detailed customer experiences for [business].</p>',
                ],
                2 => [
                    'headline' => 'Customer reviews for [business] ([count])',
                    'subtext' => '<p>Read honest feedback and performance ratings submitted by verified [business] users.</p>',
                ],
                3 => [
                    'headline' => '[business] reviews & ratings ([count])',
                    'subtext' => '<p>See what real users have to say about [business] features, pricing, and reliability.</p>',
                ],
                4 => [
                    'headline' => 'Verified [business] user reviews ([count])',
                    'subtext' => '<p>Comprehensive evaluations and feedback shared by community members using [business].</p>',
                ],
            ],
            'profile_discussions' => [
                1 => [
                    'headline' => '[business] Discussions ([count])',
                    'subtext' => '<p>Join the conversation, ask questions, and share experiences about [business].</p>',
                ],
                2 => [
                    'headline' => 'Community discussions on [business] ([count])',
                    'subtext' => '<p>Connect with other users to discuss troubleshooting, features, and tips for [business].</p>',
                ],
                3 => [
                    'headline' => '[business] Community questions & opinions ([count])',
                    'subtext' => '<p>Explore questions, experiences, and opinions shared by Localio members.</p>',
                ],
                4 => [
                    'headline' => 'Ask & discuss: [business] ([count])',
                    'subtext' => '<p>Have a question about [business]? Ask the community or share your advice.</p>',
                ],
            ],
        ];

        foreach ($defaults as $fieldKey => $options) {
            $hasAny = ContentTemplate::where('section_group', 'business_profile')
                ->where('field_key', $fieldKey)
                ->where('lang_id', 1)
                ->exists();

            if (!$hasAny) {
                foreach ($options as $optNum => $data) {
                    $headlineText = is_array($data) ? ($data['headline'] ?? '') : $data;
                    $subtextText = is_array($data) ? ($data['subtext'] ?? null) : null;
                    ContentTemplate::create([
                        'section_group' => 'business_profile',
                        'field_key'     => $fieldKey,
                        'option_number' => $optNum,
                        'headline'      => $headlineText,
                        'subtext'       => $subtextText,
                        'is_active'     => true,
                        'lang_id'       => 1,
                    ]);
                }
            }
        }
    }

    public static function seedDefaultBusinessFaqOptionsIfMissing(): void
    {
        $defaults = [
            'faq_search_headline' => [
                1 => 'Search [business] FAQs',
                2 => 'Looking for specific [business] answers?',
                3 => 'Find answers in [business] FAQs',
                4 => 'Quick search: [business] FAQs',
            ],
            'faq_search_placeholder' => [
                1 => 'Search questions...',
                2 => 'Search FAQs for [business]...',
                3 => 'Search questions about [business]...',
                4 => 'Type your question here...',
            ],
            'faq_accordion_headline' => [
                1 => 'Frequently asked questions about [business]',
                2 => 'Frequently asked questions',
                3 => '[business] FAQs & answers',
                4 => 'Common questions about [business]',
            ],
            'faq_explore' => [
                1 => [
                    'headline' => 'Explore more [category_lc] FAQs',
                    'subtext'  => '<p>Looking into a more specific [category_lc] type? Open the FAQ pages for related [category_lc] categories and continue directly with questions and answers for that category.</p>',
                ],
                2 => [
                    'headline' => 'Browse related [category_cap] FAQs',
                    'subtext'  => '<p>Browse specialized categories to find the exact tools and solutions for your requirements.</p>',
                ],
                3 => [
                    'headline' => 'More [category_cap] categories & FAQs',
                    'subtext'  => '<p>Discover FAQ guides across related [category_lc] niches and topics.</p>',
                ],
                4 => [
                    'headline' => 'Explore other [category_cap] categories',
                    'subtext'  => '<p>Find questions and answers for specialized [category_lc] tools and services.</p>',
                ],
            ],
            'faq_page_discussions' => [
                1 => [
                    'headline' => '[business] Community questions & opinions ([count])',
                    'subtext'  => '<p>Explore questions, experiences, and opinions shared by Localio members.</p>',
                ],
                2 => [
                    'headline' => '[business] Discussions ([count])',
                    'subtext'  => '<p>Join the conversation, ask questions, and share experiences about [business].</p>',
                ],
                3 => [
                    'headline' => 'Community discussions on [business] ([count])',
                    'subtext'  => '<p>Connect with other users to discuss troubleshooting, features, and tips for [business].</p>',
                ],
                4 => [
                    'headline' => 'Ask & discuss: [business] ([count])',
                    'subtext'  => '<p>Have a question about [business]? Ask the community or share your advice.</p>',
                ],
            ],
            'faq_discussions_prompt_placeholder' => [
                1 => 'Ask a question or share something about [business]...',
                2 => 'Have a question about [business]? Ask the community...',
                3 => 'Share your thoughts or ask a question about [business]...',
                4 => 'Start a conversation or ask about [business]...',
            ],
        ];

        foreach ($defaults as $fieldKey => $options) {
            $hasAny = ContentTemplate::where('section_group', 'business_faqs')
                ->where('field_key', $fieldKey)
                ->where('lang_id', 1)
                ->exists();

            if (!$hasAny) {
                foreach ($options as $optNum => $data) {
                    $headlineText = is_array($data) ? ($data['headline'] ?? '') : $data;
                    $subtextText = is_array($data) ? ($data['subtext'] ?? null) : null;
                    ContentTemplate::create([
                        'section_group' => 'business_faqs',
                        'field_key'     => $fieldKey,
                        'option_number' => $optNum,
                        'headline'      => $headlineText,
                        'subtext'       => $subtextText,
                        'is_active'     => true,
                        'lang_id'       => 1,
                    ]);
                }
            }
        }
    }

    public static function seedDefaultBusinessCompareOptionsIfMissing(): void
    {
        $defaults = [
            'compare_h1' => [
                1 => 'Compare [business]',
                2 => '[business] Comparisons & Alternatives',
                3 => 'Compare [business] with competitors',
                4 => 'How [business] compares to other providers',
            ],
            'compare_subtext' => [
                1 => 'See how [business] compares to other [category_lc] providers.',
                2 => 'Compare [business] with top competitors on features, ratings, and pricing.',
                3 => 'Side-by-side analysis of [business] and alternative providers.',
                4 => 'Discover how [business] measures up against leading competitors.',
            ],
            'compare_overview' => [
                1 => [
                    'headline' => 'Compare [business]',
                    'subtext'  => '<p>Explore how [business] compares to other providers in [category_lc], evaluating key features, performance, pricing, and user ratings to help you find the right fit.</p>',
                ],
                2 => [
                    'headline' => 'About [business] comparisons',
                    'subtext'  => '<p>See how [business] stacks up against competing [category_lc] solutions. Review side-by-side differences to make an informed decision for your needs.</p>',
                ],
                3 => [
                    'headline' => '[business] versus the competition',
                    'subtext'  => '<p>Find out how [business] performs when compared to alternatives in the market, highlighting strengths, weaknesses, and key differentiators.</p>',
                ],
                4 => [
                    'headline' => 'Evaluating [business] against alternatives',
                    'subtext'  => '<p>Compare [business] side-by-side with leading options in [category_lc] to choose the solution that best aligns with your goals.</p>',
                ],
            ],
            'compare_interactive_tool' => [
                1 => [
                    'headline' => 'Compare [business] with any provider',
                    'subtext'  => 'Select another provider from the search box below to generate an instant side-by-side comparison.',
                ],
                2 => [
                    'headline' => 'Compare [business] with competitors',
                    'subtext'  => 'Choose any alternative provider from the search box to view a detailed side-by-side comparison.',
                ],
                3 => [
                    'headline' => 'Side-by-side comparison with [business]',
                    'subtext'  => 'Search and select any provider below to see how they measure up against [business].',
                ],
                4 => [
                    'headline' => 'Find the best alternative to [business]',
                    'subtext'  => 'Select another provider using the search box to compare features, pricing, and ratings directly.',
                ],
            ],
            'compare_search_placeholder' => [
                1 => 'Search business to compare...',
                2 => 'Search provider to compare...',
                3 => 'Type a business name...',
                4 => 'Find a competitor to compare...',
            ],
            'compare_popular' => [
                1 => [
                    'headline' => 'Popular [business] comparisons',
                    'subtext'  => 'See how [business] compares to top competitors.',
                ],
                2 => [
                    'headline' => 'Top comparisons for [business]',
                    'subtext'  => 'Explore the most viewed side-by-side comparisons involving [business].',
                ],
                3 => [
                    'headline' => 'Most compared against [business]',
                    'subtext'  => 'Check out how [business] compares to the most popular alternatives.',
                ],
                4 => [
                    'headline' => 'Frequently compared with [business]',
                    'subtext'  => 'Discover the top competitors users compare against [business].',
                ],
            ],
            'compare_recent' => [
                1 => [
                    'headline' => 'Recently viewed comparisons',
                    'subtext'  => 'Comparisons of [business] recently viewed by users.',
                ],
                2 => [
                    'headline' => 'Recent [business] comparisons',
                    'subtext'  => 'See comparisons involving [business] that were recently checked by visitors.',
                ],
                3 => [
                    'headline' => 'Latest comparisons with [business]',
                    'subtext'  => 'Browse recently generated comparisons featuring [business].',
                ],
                4 => [
                    'headline' => 'Recently compared providers',
                    'subtext'  => 'Recent head-to-head comparisons with [business] explored by users.',
                ],
            ],
            'compare_explore' => [
                1 => [
                    'headline' => 'Explore [category_cap] categories',
                    'subtext'  => 'Browse specialized categories to find the exact tools and solutions for your requirements.',
                ],
                2 => [
                    'headline' => 'Explore more [category_lc] categories',
                    'subtext'  => 'Browse related categories to discover more specialized providers and alternatives.',
                ],
                3 => [
                    'headline' => 'Browse related [category_cap] categories',
                    'subtext'  => 'Discover specialized niches and tools related to [category_lc].',
                ],
                4 => [
                    'headline' => 'More [category_cap] categories',
                    'subtext'  => 'Find specific providers and tools tailored to your exact industry needs.',
                ],
            ],
            'compare_page_discussions' => [
                1 => [
                    'headline' => '[business] Community questions & opinions ([count])',
                    'subtext'  => '<p>Explore questions, experiences, and opinions shared by Localio members.</p>',
                ],
                2 => [
                    'headline' => '[business] Discussions ([count])',
                    'subtext'  => '<p>Join the conversation, ask questions, and share experiences about [business].</p>',
                ],
                3 => [
                    'headline' => 'Community discussions on [business] ([count])',
                    'subtext'  => '<p>Connect with other users to discuss troubleshooting, features, and tips for [business].</p>',
                ],
                4 => [
                    'headline' => 'Ask & discuss: [business] ([count])',
                    'subtext'  => '<p>Have a question about [business]? Ask the community or share your advice.</p>',
                ],
            ],
            'compare_discussions_prompt_placeholder' => [
                1 => 'Ask a question or share something about [business]...',
                2 => 'Have a question about [business]? Ask the community...',
                3 => 'Share your thoughts or ask a question about [business]...',
                4 => 'Ask questions or share insights about [business]...',
            ],
        ];

        foreach ($defaults as $fieldKey => $options) {
            foreach ($options as $optNum => $data) {
                $exists = ContentTemplate::where('section_group', 'business_compare')
                    ->where('field_key', $fieldKey)
                    ->where('option_number', $optNum)
                    ->where('lang_id', 1)
                    ->exists();

                if (!$exists) {
                    $headlineText = is_array($data) ? ($data['headline'] ?? '') : $data;
                    $subtextText = is_array($data) ? ($data['subtext'] ?? null) : null;
                    ContentTemplate::create([
                        'section_group' => 'business_compare',
                        'field_key'     => $fieldKey,
                        'option_number' => $optNum,
                        'headline'      => $headlineText,
                        'subtext'       => $subtextText,
                        'is_active'     => true,
                        'lang_id'       => 1,
                    ]);
                }
            }
        }
    }

    public static function seedDefaultComparisonOptionsIfMissing(): void
    {
        $defaults = [
            'comparison_h1' => [
                1 => [
                    'headline' => '[business_vs]: Comparison',
                    'subtext'  => 'Learn more from our team about [category] pricing, features, and user ratings in our side-by-side comparison guide.',
                ],
                2 => [
                    'headline' => '[business1] vs [business2] Comparison: Which is Better?',
                    'subtext'  => 'Compare [business_vs] side by side on pricing, features, user ratings, and pros & cons.',
                ],
                3 => [
                    'headline' => '[business_vs]: Side-by-Side Comparison',
                    'subtext'  => 'Find out which [category] solution is right for you in our detailed [business_vs] comparison.',
                ],
                4 => [
                    'headline' => 'Comparing [business1] and [business2] ([category_cap])',
                    'subtext'  => 'Detailed side-by-side analysis of [business_vs] covering features, pricing, and authentic customer feedback.',
                ],
            ],
            'comparison_pros_cons' => [
                1 => 'Pros and cons',
                2 => '[business_vs] pros and cons',
                3 => 'Advantages and disadvantages',
                4 => 'Pros and cons comparison',
            ],
            'comparison_reviews' => [
                1 => 'Reviews',
                2 => '[business_vs] reviews',
                3 => 'Customer reviews & ratings',
                4 => 'User feedback & ratings',
            ],
            'comparison_alternatives' => [
                1 => 'Alternatives',
                2 => 'Top alternatives to consider',
                3 => 'Similar [category] alternatives',
                4 => 'Explore more alternatives',
            ],
        ];

        foreach ($defaults as $fieldKey => $options) {
            foreach ($options as $optNum => $data) {
                $exists = ContentTemplate::where('section_group', 'comparison')
                    ->where('field_key', $fieldKey)
                    ->where('option_number', $optNum)
                    ->where('lang_id', 1)
                    ->exists();

                if (!$exists) {
                    $headlineText = is_array($data) ? ($data['headline'] ?? '') : $data;
                    $subtextText = is_array($data) ? ($data['subtext'] ?? null) : null;
                    ContentTemplate::create([
                        'section_group' => 'comparison',
                        'field_key'     => $fieldKey,
                        'option_number' => $optNum,
                        'headline'      => $headlineText,
                        'subtext'       => $subtextText,
                        'is_active'     => true,
                        'lang_id'       => 1,
                    ]);
                }
            }
        }
    }

    public static function ensureTemplatesExistForLanguage(string $sectionGroup, int $langId): void
    {
        if ($langId <= 0) {
            return;
        }

        if ($sectionGroup === 'category') {
            self::seedDefaultCategoryOptionsIfMissing();
        } elseif ($sectionGroup === 'category_faqs') {
            self::seedDefaultCategoryFaqOptionsIfMissing();
        } elseif ($sectionGroup === 'category_discussions') {
            self::seedDefaultCategoryDiscussionsOptionsIfMissing();
        } elseif ($sectionGroup === 'business_profile') {
            self::seedDefaultBusinessProfileOptionsIfMissing();
        } elseif ($sectionGroup === 'business_discussions') {
            self::seedDefaultBusinessDiscussionsOptionsIfMissing();
        } elseif ($sectionGroup === 'business_faqs') {
            self::seedDefaultBusinessFaqOptionsIfMissing();
        } elseif ($sectionGroup === 'business_compare') {
            self::seedDefaultBusinessCompareOptionsIfMissing();
        }

        $fieldDefs = [];
        if ($sectionGroup === 'category') {
            $fieldDefs = self::CATEGORY_FIELDS;
        } elseif ($sectionGroup === 'category_faqs') {
            $fieldDefs = self::CATEGORY_FAQ_FIELDS;
        } elseif ($sectionGroup === 'category_discussions') {
            $fieldDefs = self::CATEGORY_DISCUSSIONS_FIELDS;
        } elseif ($sectionGroup === 'business_profile') {
            $fieldDefs = self::BUSINESS_PROFILE_FIELDS;
        } elseif ($sectionGroup === 'business_alternatives') {
            $fieldDefs = self::BUSINESS_ALTERNATIVES_FIELDS;
        } elseif ($sectionGroup === 'business_faqs') {
            $fieldDefs = self::BUSINESS_FAQS_FIELDS;
        } elseif ($sectionGroup === 'business_compare') {
            $fieldDefs = self::BUSINESS_COMPARE_FIELDS;
        } elseif ($sectionGroup === 'comparison') {
            $fieldDefs = self::COMPARISON_FIELDS;
        } elseif ($sectionGroup === 'business_reviews') {
            $fieldDefs = self::BUSINESS_REVIEWS_FIELDS;
        } elseif ($sectionGroup === 'business_discussions') {
            $fieldDefs = self::BUSINESS_DISCUSSIONS_FIELDS;
        } elseif ($sectionGroup === 'features') {
            $fieldDefs = self::FEATURE_FIELDS;
        } elseif (str_starts_with($sectionGroup, 'criteria_ratings_')) {
            preg_match('/^criteria_ratings_(\d+)/', $sectionGroup, $m);
            $critId = (int) ($m[1] ?? 0);
            $fieldDefs = self::getCriteriaFields($critId);
        }

        if ($langId > 1) {
            foreach (array_keys($fieldDefs) as $fKey) {
                $hasFieldTemplate = ContentTemplate::where('section_group', $sectionGroup)
                    ->where('field_key', $fKey)
                    ->where('lang_id', $langId)
                    ->exists();

                if (!$hasFieldTemplate) {
                    $defaultTemplates = ContentTemplate::where('section_group', $sectionGroup)
                        ->where('field_key', $fKey)
                        ->where('lang_id', 1)
                        ->get();

                    foreach ($defaultTemplates as $defTpl) {
                        ContentTemplate::create([
                            'section_group' => $defTpl->section_group,
                            'field_key' => $defTpl->field_key,
                            'option_number' => $defTpl->option_number,
                            'headline' => $defTpl->headline,
                            'subtext' => $defTpl->subtext,
                            'is_active' => $defTpl->is_active,
                            'lang_id' => $langId,
                        ]);
                    }
                }
            }
        }
    }

    public static function getCategoryTranslation(?Category $category, int $langId = 1): ?CategoryTranslation
    {
        if (!$category) {
            return null;
        }

        if ($category->relationLoaded('categoryTranslations')) {
            return $category->categoryTranslations->where('lang_id', $langId)->first()
                ?? $category->categoryTranslations->first();
        }

        if ($category->relationLoaded('translations')) {
            $trans = $category->translations;
            if ($trans instanceof \Illuminate\Database\Eloquent\Collection) {
                return $trans->where('lang_id', $langId)->first() ?? $trans->first();
            } elseif ($trans instanceof CategoryTranslation) {
                if ($trans->lang_id == $langId) {
                    return $trans;
                }
            }
        }

        return $category->categoryTranslations()->where('lang_id', $langId)->first()
            ?? $category->categoryTranslations()->where('lang_id', 1)->first()
            ?? $category->categoryTranslations()->first();
    }

        /**
     * Cleans heading text from CKEditor by decoding entities, stripping block tags (<p>, <div>, etc.)
     * and preserving inline styling (<b>, <strong>, <i>, <em>, <span>, <mark>, <small>).
     */
    public static function cleanHeading(?string $text): string
    {
        if (empty($text)) {
            return '';
        }
        $decoded = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $cleaned = strip_tags($decoded);
        return trim($cleaned);
    }

    /**
     * Cleans input placeholder text by stripping all HTML tags and decoding entities.
     */
    public static function cleanPlaceholder(?string $text): string
    {
        if (empty($text)) {
            return '';
        }
        $decoded = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        return trim(strip_tags($decoded));
    }

    public static function replacePlaceholders(?string $text, ?Category $category, ?Feature $feature = null, int $langId = 1, ?\App\Models\Business $business = null, array $extraTags = []): string
    {
        if (empty($text)) {
            return '';
        }

        foreach ($extraTags as $tag => $val) {
            $text = str_replace($tag, (string) $val, $text);
        }

        // Global tags
        $currentYear = date('Y');
        $c1Obj = StaticContentKey::where('key', 'country1')->first();
        $c2Obj = StaticContentKey::where('key', 'country2')->first();
        $c1 = $c1Obj ? $c1Obj->getTranslation($langId) : '';
        $c2 = $c2Obj ? $c2Obj->getTranslation($langId) : '';

        $text = str_replace('[year]', $currentYear, $text);
        $text = str_replace('[Year]', $currentYear, $text);
        $text = str_replace('[country1]', $c1, $text);
        $text = str_replace('[country2]', $c2, $text);
        $site = config('app.name', 'Localio');
        $text = str_replace(['[website]', '[Site Name]', '[site_name]', '{site_name}'], $site, $text);

        if ($business) {
            $bizName = $business->translations?->where('lang_id', $langId)->first()?->name 
                ?? $business->translations?->first()?->name 
                ?? $business->name ?? '';
            $text = str_replace(['[business]', '[business_name]', '[business_tc]', '[entity_name]', '[entity]', '[entity_tc]'], $bizName, $text);
            $text = str_replace(['[business_cap]', '[entity_name_cap]'], ucfirst($bizName), $text);
            $text = str_replace(['[business_lc]', '[entity_name_lc]'], mb_strtolower($bizName), $text);
        } else {
            $text = str_replace(['[business]', '[business_name]', '[business_tc]', '[business_cap]', '[business_lc]', '[entity_name]', '[entity]', '[entity_tc]', '[entity_name_cap]', '[entity_name_lc]'], '', $text);
        }

        // Category tags
        if ($category) {
            $catTrans = self::getCategoryTranslation($category, $langId);

            $rawName = $catTrans?->name ?? $category->name ?? '';
            $catCap = !empty($catTrans?->category_cap) ? $catTrans->category_cap : ucfirst(mb_strtolower($rawName));
            $catTc  = !empty($catTrans?->category_tc)  ? $catTrans->category_tc  : mb_convert_case($rawName, MB_CASE_TITLE, 'UTF-8');
            $catLc  = !empty($catTrans?->category_lc)  ? $catTrans->category_lc  : mb_strtolower($rawName);

            // Entity 1 defaults if not explicitly set
            $e1Cap        = !empty($catTrans?->entity1_cap)        ? $catTrans->entity1_cap        : ($catCap . ' provider');
            $e1Lc         = !empty($catTrans?->entity1_lc)         ? $catTrans->entity1_lc         : mb_strtolower($e1Cap);
            $es1Cap       = !empty($catTrans?->entities1_cap)      ? $catTrans->entities1_cap      : ($catCap . ' providers');
            $es1Lc        = !empty($catTrans?->entities1_lc)       ? $catTrans->entities1_lc       : mb_strtolower($es1Cap);
            $e1ShortCap   = !empty($catTrans?->entity1_short_cap)  ? $catTrans->entity1_short_cap  : 'Provider';
            $e1ShortLc    = !empty($catTrans?->entity1_short_lc)   ? $catTrans->entity1_short_lc   : 'provider';
            $es1ShortCap  = !empty($catTrans?->entities1_short_cap) ? $catTrans->entities1_short_cap : 'Providers';
            $es1ShortLc   = !empty($catTrans?->entities1_short_lc)  ? $catTrans->entities1_short_lc  : 'providers';

            // Entity 2 defaults if not explicitly set
            $e2Cap        = !empty($catTrans?->entity2_cap)        ? $catTrans->entity2_cap        : $e1Cap;
            $e2Lc         = !empty($catTrans?->entity2_lc)         ? $catTrans->entity2_lc         : $e1Lc;
            $es2Cap       = !empty($catTrans?->entities2_cap)      ? $catTrans->entities2_cap      : $es1Cap;
            $es2Lc        = !empty($catTrans?->entities2_lc)       ? $catTrans->entities2_lc       : $es1Lc;
            $e2ShortCap   = !empty($catTrans?->entity2_short_cap)  ? $catTrans->entity2_short_cap  : $e1ShortCap;
            $e2ShortLc    = !empty($catTrans?->entity2_short_lc)   ? $catTrans->entity2_short_lc   : $e1ShortLc;
            $es2ShortCap  = !empty($catTrans?->entities2_short_cap) ? $catTrans->entities2_short_cap : $es1ShortCap;
            $es2ShortLc   = !empty($catTrans?->entities2_short_lc)  ? $catTrans->entities2_short_lc  : $es1ShortLc;

            $replacements = [
                '[category_cap]'         => $catCap,
                '[category_tc]'          => $catTc,
                '[category_lc]'          => $catLc,
                '[entity1_cap]'          => $e1Cap,
                '[entity1_lc]'           => $e1Lc,
                '[entities1_cap]'        => $es1Cap,
                '[entities1_lc]'         => $es1Lc,
                '[entity1_short_cap]'    => $e1ShortCap,
                '[entity1_short_lc]'     => $e1ShortLc,
                '[entities1_short_cap]'  => $es1ShortCap,
                '[entities1_short_lc]'   => $es1ShortLc,
                '[entity2_cap]'          => $e2Cap,
                '[entity2_lc]'           => $e2Lc,
                '[entities2_cap]'        => $es2Cap,
                '[entities2_lc]'         => $es2Lc,
                '[entity2_short_cap]'    => $e2ShortCap,
                '[entity2_short_lc]'     => $e2ShortLc,
                '[entities2_short_cap]'  => $es2ShortCap,
                '[entities2_short_lc]'   => $es2ShortLc,

                // Legacy & convenience aliases
                '[category]'             => $catLc,
                '[Category]'             => $catCap,
                '[category name]'        => $catLc,
                '[Category Name]'        => $catCap,
                '{category}'             => $catLc,
                '{Category}'             => $catCap,
            ];

            foreach ($replacements as $search => $replace) {
                $text = str_replace($search, $replace, $text);
            }
        } else {
            $catFallback = '';
            if ($business) {
                $catFallback = $business->translations?->where('lang_id', $langId)->first()?->name 
                    ?? $business->translations?->first()?->name 
                    ?? $business->name ?? '';
            }
            $catFallbackCap = ucfirst(mb_strtolower($catFallback));
            $catFallbackTc  = mb_convert_case($catFallback, MB_CASE_TITLE, 'UTF-8');
            $catFallbackLc  = mb_strtolower($catFallback);

            $text = str_replace([
                '[category_cap]', '[Category]', '[Category Name]', '{Category}',
                '[category_tc]',
                '[category_lc]', '[category]', '[category name]', '{category}',
                '[entity1_cap]', '[entity1_lc]', '[entities1_cap]', '[entities1_lc]',
                '[entity1_short_cap]', '[entity1_short_lc]', '[entities1_short_cap]', '[entities1_short_lc]',
                '[entity2_cap]', '[entity2_lc]', '[entities2_cap]', '[entities2_lc]',
                '[entity2_short_cap]', '[entity2_short_lc]', '[entities2_short_cap]', '[entities2_short_lc]',
            ], [
                $catFallbackCap, $catFallbackCap, $catFallbackCap, $catFallbackCap,
                $catFallbackTc,
                $catFallbackLc, $catFallbackLc, $catFallbackLc, $catFallbackLc,
                $catFallbackCap, $catFallbackLc, $catFallbackCap, $catFallbackLc,
                $catFallbackCap, $catFallbackLc, $catFallbackCap, $catFallbackLc,
                $catFallbackCap, $catFallbackLc, $catFallbackCap, $catFallbackLc,
                $catFallbackCap, $catFallbackLc, $catFallbackCap, $catFallbackLc,
            ], $text);
        }

        // Feature tags
        if ($feature) {
            $featTrans = $feature->translations;
            if ($featTrans instanceof \Illuminate\Database\Eloquent\Collection) {
                $featTrans = $featTrans->where('lang_id', $langId)->first() ?? $featTrans->first();
            }
            $rawFeatName = $featTrans?->name ?? $feature->name ?? '';
            $featCap = !empty($featTrans?->category_cap) ? $featTrans->category_cap : (!empty($featTrans?->feature_cap) ? $featTrans->feature_cap : ucfirst(mb_strtolower($rawFeatName)));
            $featTc  = !empty($featTrans?->features_tc) ? $featTrans->features_tc : (!empty($featTrans?->feature_tc) ? $featTrans->feature_tc : (!empty($featTrans?->category_tc) ? $featTrans->category_tc : mb_convert_case($rawFeatName, MB_CASE_TITLE, 'UTF-8')));
            $featLc  = !empty($featTrans?->category_lc)  ? $featTrans->category_lc  : (!empty($featTrans?->feature_lc)  ? $featTrans->feature_lc  : mb_strtolower($rawFeatName));

            $featReplacements = [
                '[feature_cap]'  => $featCap,
                '[feature_tc]'   => $featTc,
                '[features_tc]'  => $featTc,
                '[feature_lc]'   => $featLc,
                '[feature]'      => $featLc,
                '[Feature]'      => $featCap,
                '[feature name]' => $featLc,
                '[Feature Name]' => $featCap,
                '{feature}'      => $featLc,
                '{Feature}'      => $featCap,
            ];

            // If no category was passed, also replace [category_cap], etc. with feature values
            if (!$category) {
                $featReplacements['[category_cap]'] = $featCap;
                $featReplacements['[category_tc]']  = $featTc;
                $featReplacements['[category_lc]']  = $featLc;
            }

            foreach ($featReplacements as $search => $replace) {
                $text = str_replace($search, $replace, $text);
            }
        }

        // Clean up remaining unreplaced comparison placeholders
        $text = str_replace([
            '[business1]', '[business2]', '[business3]', '[business4]',
            '[business1_cap]', '[business2_cap]',
            '[business_vs]', '[businesses]', '[business_list]',
        ], '', $text);
        $text = preg_replace('/\s+vs\s*(?::|$)/i', ': ', $text);

        // Clean up redundant spaces
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Resolves the Category introduction paragraphs merging individual text with template.
     */
    public static function resolveCategoryIntro(Category $category, int $langId = 1): string
    {
        $catTrans = self::getCategoryTranslation($category, $langId);

        $individualIntro = trim($catTrans?->description ?? '');
        $template = self::getTemplateForResolution('category', $category->id, 'header_intro', $langId, 'category');

        if (!$template) {
            return $individualIntro ?: '<p>Browse and compare the best options</p>';
        }

        $question = self::replacePlaceholders($template->headline, $category, null, $langId);
        $secondText = self::replacePlaceholders($template->subtext, $category, null, $langId);

        // Format paragraph 1: individual intro text + space + question
        // Strip outer <p>...</p> tags if present in individualIntro to merge smoothly
        $cleanedIntro = preg_replace('/^<p>(.*?)<\/p>$/s', '$1', $individualIntro);

        $p1 = trim($cleanedIntro);
        if ($question) {
            if ($p1 !== '') {
                $p1 .= ' ' . $question;
            } else {
                $p1 = $question;
            }
        }

        $output = '';
        if ($p1 !== '') {
            $output .= '<p>' . $p1 . '</p>';
        }

        // Format paragraph 2: second header text
        if ($secondText) {
            $output .= '<p>' . $secondText . '</p>';
        }

        return $output ?: '<p>Browse and compare the best options</p>';
    }

    /**
     * Resolves a category section headline and subtext using assigned template with fallback.
     */
    public static function resolveCategorySection(Category $category, string $fieldKey, int $langId = 1, array $extraTags = []): array
    {
        if ($fieldKey === 'discussions_prompt_placeholder') {
            $fieldKey = 'category_discussions_prompt_placeholder';
        }
        if ($fieldKey === 'discussions_empty_headline') {
            $fieldKey = 'category_discussions_empty_headline';
        }
        $catTrans = self::getCategoryTranslation($category, $langId);

        $template = self::getTemplateForResolution('category', $category->id, $fieldKey, $langId);

        $headline = '';
        $subtext = '';
        $optionNumber = null;

        if ($template) {
            $headline = self::replacePlaceholders($template->headline, $category, null, $langId, null, $extraTags);
            $subtext = self::replacePlaceholders($template->subtext, $category, null, $langId, null, $extraTags);
            $optionNumber = $template->option_number;
        }

        // Fallback to legacy database column if template is empty or not yet assigned
        if (empty($headline) && $catTrans) {
            switch ($fieldKey) {
                case 'explore_link':
                    $headline = $catTrans->see_all_text ?? ('See all ' . ($catTrans->name ?? ''));
                    break;
                case 'compare_top_rated':
                    $headline = $catTrans->top_rated_listing_headline ?? ('Best ' . mb_strtolower($catTrans->name ?? '') . ' services');
                    $subtext = $catTrans->top_rated_listing_subtext ?? '';
                    break;
                case 'top_10':
                    $headline = $catTrans->compare_alternatives_headline ?? ('Compare ' . ($catTrans->name ?? '') . ' alternatives');
                    $subtext = $catTrans->compare_alternatives_subtext ?? '';
                    break;
                case 'pros_cons':
                    $headline = $catTrans->community_says_headline ?? 'What the Localio community says';
                    $subtext = $catTrans->community_says_subtext ?? '';
                    break;
                case 'popular_comparisons':
                    $headline = $catTrans->popular_comparisons_headline ?? 'Popular comparisons';
                    $subtext = $catTrans->popular_comparisons_subtext ?? '';
                    break;
                case 'compare_tool':
                    $headline = $catTrans->compare_tool_headline ?? ('Compare ' . ($catTrans->name ?? '') . ' with any provider');
                    $subtext = $catTrans->compare_tool_subtext ?? '';
                    break;
                case 'explore_subcategories':
                    $headline = $catTrans->explore_subcategories_headline ?? ('Explore ' . ($catTrans->name ?? '') . ' categories');
                    $subtext = $catTrans->explore_subcategories_subtext ?? '';
                    break;
                case 'faq_h1':
                    $headline = !empty($catTrans->faq_h1_title) ? $catTrans->faq_h1_title : (($catTrans->name ?? 'Category') . ' FAQs');
                    $subtext = '';
                    break;
                case 'faq_header_intro':
                    $headline = $catTrans->faq_subtext ?? '';
                    $subtext = '';
                    break;
                case 'category_page_faqs':
                    $headline = $catTrans->category_faqs_headline ?? 'Frequently asked questions';
                    $subtext = $catTrans->category_faqs_subtext ?? '';
                    break;
                case 'category_faq_search':
                    $headline = 'Search ' . strtolower($catTrans->name ?? 'category') . ' FAQs...';
                    $subtext = '';
                    break;
                case 'category_page_discussions':
                    $countVal = $extraTags['[count]'] ?? ($extraTags['count'] ?? null);
                    $countSuffix = $countVal !== null ? " ({$countVal})" : '';
                    $headline = ($catTrans->name ?? 'Category') . ' discussions' . $countSuffix;
                    $subtext = 'Explore questions, experiences, and opinions shared by Localio users.';
                    break;
                case 'faq_explore':
                    $headline = $catTrans->faq_explore_headline ?? ('Explore ' . ($catTrans->name ?? '') . ' categories');
                    $subtext = $catTrans->faq_explore_subtext ?? '';
                    break;
            }
        }

        if (in_array($fieldKey, ['category_faq_search', 'faq_search_placeholder', 'category_discussions_prompt_placeholder', 'discussions_prompt_placeholder'], true)) {
            $headline = self::cleanPlaceholder($headline);
            return [
                'headline' => $headline,
                'subtext' => $subtext,
                'template' => $template,
                'option_number' => $optionNumber,
            ];
        } else {
            $headline = self::cleanHeading($headline);
            return [
                'headline' => $headline,
                'subtext' => $subtext,
                'template' => $template,
                'option_number' => $optionNumber,
            ];
        }
    }

    /**
     * Resolves Feature H1 headline using assigned template.
     */
    public static function resolveFeatureH1(Feature $feature, ?Category $category = null, int $langId = 1): array
    {
        $template = self::getTemplateForResolution('feature', $feature->id, 'feature_h1', $langId, 'features');

        $headline = '';
        $optionNumber = null;

        if ($template) {
            $headline = self::replacePlaceholders($template->headline, $category, $feature, $langId);
            $optionNumber = $template->option_number;
        }

        // Fallback default
        if (empty($headline)) {
            $catName = $category ? (self::getCategoryTranslation($category, $langId)?->name ?? $category->name ?? '') : '';
            $featName = $feature->translations?->name ?? $feature->name ?? '';
            $headline = 'Best ' . $catName . ' with ' . $featName . ' ' . date('Y');
        }

        return [
            'headline' => self::cleanHeading($headline),
            'template' => $template,
            'option_number' => $optionNumber,
        ];
    }

    /**
     * Resolves the Feature introduction paragraphs merging individual text with template.
     */
    public static function resolveFeatureIntro(Feature $feature, ?Category $category = null, ?string $individualIntro = null, int $langId = 1): string
    {
        $template = self::getTemplateForResolution('feature', $feature->id, 'header_intro', $langId, 'features');

        if (!$template) {
            if (!empty($individualIntro)) {
                return $individualIntro;
            }
            return $category ? self::resolveCategoryIntro($category, $langId) : '<p>Browse and compare the best options</p>';
        }

        $question = self::replacePlaceholders($template->headline, $category, $feature, $langId);
        $secondText = self::replacePlaceholders($template->subtext, $category, $feature, $langId);

        $cleanedIntro = preg_replace('/^<p>(.*?)<\/p>$/s', '$1', trim($individualIntro ?? ''));

        $p1 = trim($cleanedIntro);
        if ($question) {
            if ($p1 !== '') {
                $p1 .= ' ' . $question;
            } else {
                $p1 = $question;
            }
        }

        $output = '';
        if ($p1 !== '') {
            $output .= '<p>' . $p1 . '</p>';
        }

        if ($secondText) {
            $output .= '<p>' . $secondText . '</p>';
        }

        return $output ?: '<p>Browse and compare the best options</p>';
    }

    /**
     * Resolves a feature section headline and subtext using assigned template with fallback.
     */
    public static function resolveFeatureSection(Feature $feature, ?Category $category, string $fieldKey, int $langId = 1): array
    {
        $template = self::getTemplateForResolution('feature', $feature->id, $fieldKey, $langId, 'features');

        $headline = '';
        $subtext = '';
        $optionNumber = null;

        if ($template) {
            $headline = self::replacePlaceholders($template->headline, $category, $feature, $langId);
            $subtext = self::replacePlaceholders($template->subtext, $category, $feature, $langId);
            $optionNumber = $template->option_number;
        }

        return [
            'headline' => $headline,
            'subtext' => $subtext,
            'template' => $template,
            'option_number' => $optionNumber,
        ];
    }

    /**
     * Resolves rating texts for a business across all default criteria,
     * checking primary subcategory first, then parent category, then global default.
     */
    public static function resolveBusinessRatingTexts(\App\Models\Business $business, int $langId = 1): array
    {
        $result = [];
        $defaultCriteria = \App\Models\DefaultRatingCriteria::orderBy('sort_order', 'asc')->get();
        $existingManualTexts = \App\Models\BusinessRatingText::where('business_id', $business->id)->get()->keyBy('criteria_key');

        $primarySubcat = $business->subCategories->first();
        $parentCat = $business->category;
        if (!$parentCat && $primarySubcat?->parent_id) {
            $parentCat = $primarySubcat->parent;
        }

        $reviews = $business->reviews ? $business->reviews->whereIn('status', ['published', 'active']) : collect();
        $averageRating = floatval($business->admin_rating > 0 ? $business->admin_rating : 4.0);

        foreach ($defaultCriteria as $crit) {
            $critKey = $crit->key;
            $critId = $crit->id;

            // 1. If manual override exists, preserve it
            $manual = $existingManualTexts->get($critKey);
            if ($manual && (!empty($manual->intro_text) || !empty($manual->end_text))) {
                $result[$critKey] = [
                    'intro_text' => $manual->intro_text,
                    'end_text'   => $manual->end_text,
                ];
                continue;
            }

            // 2. Determine criterion score
            $critScore = 0.0;
            if ($reviews->isNotEmpty()) {
                $scores = [];
                foreach ($reviews as $rev) {
                    $rMatch = $rev->ratings->first(fn($r) => optional($r->criteria)->default_key === $critKey || strtolower((string) optional($r->criteria)->name) === strtolower($crit->name));
                    if ($rMatch && $rMatch->rating > 0) {
                        $scores[] = floatval($rMatch->rating);
                    }
                }
                if (!empty($scores)) {
                    $critScore = array_sum($scores) / count($scores);
                }
            }
            if ($critScore <= 0) {
                $critScore = $averageRating > 0 ? $averageRating : 4.0;
            }

            // 3. Map score to range
            $rangeKey = self::getCriteriaRangeKeyForRating($critScore);
            $fieldKey = "criteria_{$critId}_range_{$rangeKey}";

            // 4. Resolve Template with Hierarchy:
            // a) Subcategory override
            $template = null;
            if ($primarySubcat) {
                $subcatSection = "criteria_ratings_{$critId}_cat_{$primarySubcat->id}";
                $template = ContentTemplate::where('section_group', $subcatSection)
                    ->where('field_key', $fieldKey)
                    ->where('lang_id', $langId)
                    ->where('is_active', true)
                    ->orderBy('option_number', 'asc')
                    ->first();
                if (!$template && $langId !== 1) {
                    $template = ContentTemplate::where('section_group', $subcatSection)
                        ->where('field_key', $fieldKey)
                        ->where('lang_id', 1)
                        ->where('is_active', true)
                        ->orderBy('option_number', 'asc')
                        ->first();
                }
            }

            // b) Parent Category
            if (!$template && $parentCat) {
                $parentSection = "criteria_ratings_{$critId}_cat_{$parentCat->id}";
                $template = ContentTemplate::where('section_group', $parentSection)
                    ->where('field_key', $fieldKey)
                    ->where('lang_id', $langId)
                    ->where('is_active', true)
                    ->orderBy('option_number', 'asc')
                    ->first();
                if (!$template && $langId !== 1) {
                    $template = ContentTemplate::where('section_group', $parentSection)
                        ->where('field_key', $fieldKey)
                        ->where('lang_id', 1)
                        ->where('is_active', true)
                        ->orderBy('option_number', 'asc')
                        ->first();
                }
            }

            // c) Global Default
            if (!$template) {
                $globalSection = "criteria_ratings_{$critId}";
                $template = ContentTemplate::where('section_group', $globalSection)
                    ->where('field_key', $fieldKey)
                    ->where('lang_id', $langId)
                    ->where('is_active', true)
                    ->orderBy('option_number', 'asc')
                    ->first();
                if (!$template && $langId !== 1) {
                    $template = ContentTemplate::where('section_group', $globalSection)
                        ->where('field_key', $fieldKey)
                        ->where('lang_id', 1)
                        ->where('is_active', true)
                        ->orderBy('option_number', 'asc')
                        ->first();
                }
            }

            // 5. Replace placeholders
            $activeCategory = $primarySubcat ?? $parentCat;
            $headline = '';
            $subtext = '';
            if ($template) {
                $headline = self::replacePlaceholders($template->headline, $activeCategory, null, $langId, $business);
                $subtext = self::replacePlaceholders($template->subtext, $activeCategory, null, $langId, $business);
            }

            $result[$critKey] = [
                'intro_text' => $headline,
                'end_text'   => $subtext,
            ];
        }

        return $result;
    }

    /**
     * Resolves a business page template section headline and subtext using assigned/default template.
     */
    public static function resolveBusinessSection(\App\Models\Business $business, string $fieldKey, int $langId = 1, array $extraTags = []): array
    {
        if ($fieldKey === 'business_discussions_prompt_placeholder') {
            $fieldKey = 'discussions_prompt_placeholder';
        }
        if ($fieldKey === 'business_discussions_empty_headline') {
            $fieldKey = 'discussions_empty_headline';
        }
        $category = $business->category ?? $business->subCategories->first();

        // Check if there is an explicit assignment for this specific field_key
        $hasExplicitAsgn = ContentTemplateAssignment::where('entity_type', 'business')
            ->where('entity_id', $business->id)
            ->where('lang_id', $langId)
            ->where('field_key', $fieldKey)
            ->exists();

        // Cross-section fallback: if profile_faqs has no explicit lock, check faqs_h1 from business_faqs
        if (!$hasExplicitAsgn && $fieldKey === 'profile_faqs') {
            $hasFaqAsgn = ContentTemplateAssignment::where('entity_type', 'business')
                ->where('entity_id', $business->id)
                ->where('lang_id', $langId)
                ->where('field_key', 'faqs_h1')
                ->exists();
            if ($hasFaqAsgn) {
                $faqH1Tpl = self::getTemplateForResolution('business', $business->id, 'faqs_h1', $langId, 'business_faqs');
                $faqSubTpl = self::getTemplateForResolution('business', $business->id, 'faqs_subtext', $langId, 'business_faqs');
                if ($faqH1Tpl) {
                    $headline = self::replacePlaceholders($faqH1Tpl->headline, $category, null, $langId, $business, $extraTags);
                    $subtext = $faqSubTpl ? self::replacePlaceholders($faqSubTpl->headline ?: $faqSubTpl->subtext, $category, null, $langId, $business, $extraTags) : '';
                    return [
                        'headline' => self::cleanHeading($headline),
                        'subtext' => $subtext,
                        'template' => $faqH1Tpl,
                        'option_number' => $faqH1Tpl->option_number,
                    ];
                }
            }
        }

        // Cross-section fallback: if faq_page_discussions has no explicit lock, check profile_discussions
        if (!$hasExplicitAsgn && $fieldKey === 'faq_page_discussions') {
            $hasProfDisc = ContentTemplateAssignment::where('entity_type', 'business')
                ->where('entity_id', $business->id)
                ->where('lang_id', $langId)
                ->where('field_key', 'profile_discussions')
                ->exists();
            if ($hasProfDisc) {
                $profDiscTpl = self::getTemplateForResolution('business', $business->id, 'profile_discussions', $langId, 'business_profile');
                if ($profDiscTpl) {
                    $headline = self::replacePlaceholders($profDiscTpl->headline, $category, null, $langId, $business, $extraTags);
                    $subtext = self::replacePlaceholders($profDiscTpl->subtext, $category, null, $langId, $business, $extraTags);
                    return [
                        'headline' => self::cleanHeading($headline),
                        'subtext' => $subtext,
                        'template' => $profDiscTpl,
                        'option_number' => $profDiscTpl->option_number,
                    ];
                }
            }
        }

        // Cross-section fallback: if faq_discussions_prompt_placeholder has no explicit lock, check discussions_prompt_placeholder
        if (!$hasExplicitAsgn && $fieldKey === 'faq_discussions_prompt_placeholder') {
            $hasPromptDisc = ContentTemplateAssignment::where('entity_type', 'business')
                ->where('entity_id', $business->id)
                ->where('lang_id', $langId)
                ->where('field_key', 'discussions_prompt_placeholder')
                ->exists();
            if ($hasPromptDisc) {
                $promptTpl = self::getTemplateForResolution('business', $business->id, 'discussions_prompt_placeholder', $langId, 'business_discussions');
                if ($promptTpl) {
                    $headline = self::replacePlaceholders($promptTpl->headline, $category, null, $langId, $business, $extraTags);
                    return [
                        'headline' => self::cleanPlaceholder($headline),
                        'subtext' => '',
                        'template' => $promptTpl,
                        'option_number' => $promptTpl->option_number,
                    ];
                }
            }
        }

        // Cross-section fallback: if compare_page_discussions has no explicit lock, check profile_discussions
        if (!$hasExplicitAsgn && $fieldKey === 'compare_page_discussions') {
            $hasProfDisc = ContentTemplateAssignment::where('entity_type', 'business')
                ->where('entity_id', $business->id)
                ->where('lang_id', $langId)
                ->where('field_key', 'profile_discussions')
                ->exists();
            if ($hasProfDisc) {
                $profDiscTpl = self::getTemplateForResolution('business', $business->id, 'profile_discussions', $langId, 'business_profile');
                if ($profDiscTpl) {
                    $headline = self::replacePlaceholders($profDiscTpl->headline, $category, null, $langId, $business, $extraTags);
                    $subtext = self::replacePlaceholders($profDiscTpl->subtext, $category, null, $langId, $business, $extraTags);
                    return [
                        'headline' => self::cleanHeading($headline),
                        'subtext' => $subtext,
                        'template' => $profDiscTpl,
                        'option_number' => $profDiscTpl->option_number,
                    ];
                }
            }
        }

        // Cross-section fallback: if compare_discussions_prompt_placeholder has no explicit lock, check discussions_prompt_placeholder
        if (!$hasExplicitAsgn && $fieldKey === 'compare_discussions_prompt_placeholder') {
            $hasPromptDisc = ContentTemplateAssignment::where('entity_type', 'business')
                ->where('entity_id', $business->id)
                ->where('lang_id', $langId)
                ->where('field_key', 'discussions_prompt_placeholder')
                ->exists();
            if ($hasPromptDisc) {
                $promptTpl = self::getTemplateForResolution('business', $business->id, 'discussions_prompt_placeholder', $langId, 'business_discussions');
                if ($promptTpl) {
                    $headline = self::replacePlaceholders($promptTpl->headline, $category, null, $langId, $business, $extraTags);
                    return [
                        'headline' => self::cleanPlaceholder($headline),
                        'subtext' => '',
                        'template' => $promptTpl,
                        'option_number' => $promptTpl->option_number,
                    ];
                }
            }
        }

        // Cross-section fallback: if profile_alternatives has no explicit lock, check alternatives_h1 from business_alternatives
        if (!$hasExplicitAsgn && $fieldKey === 'profile_alternatives') {
            $hasAltAsgn = ContentTemplateAssignment::where('entity_type', 'business')
                ->where('entity_id', $business->id)
                ->where('lang_id', $langId)
                ->where('field_key', 'alternatives_h1')
                ->exists();
            if ($hasAltAsgn) {
                $altH1Tpl = self::getTemplateForResolution('business', $business->id, 'alternatives_h1', $langId, 'business_alternatives');
                $altSubTpl = self::getTemplateForResolution('business', $business->id, 'alternatives_subtext', $langId, 'business_alternatives');
                if ($altH1Tpl) {
                    $headline = self::replacePlaceholders($altH1Tpl->headline, $category, null, $langId, $business, $extraTags);
                    $subtext = $altSubTpl ? self::replacePlaceholders($altSubTpl->headline ?: $altSubTpl->subtext, $category, null, $langId, $business, $extraTags) : '';
                    return [
                        'headline' => self::cleanHeading($headline),
                        'subtext' => $subtext,
                        'template' => $altH1Tpl,
                        'option_number' => $altH1Tpl->option_number,
                    ];
                }
            }
        }

        $template = self::getTemplateForResolution('business', $business->id, $fieldKey, $langId);

        $headline = '';
        $subtext = '';
        $optionNumber = null;

        if ($template) {
            $headline = self::replacePlaceholders($template->headline, $category, null, $langId, $business, $extraTags);
            $subtext = self::replacePlaceholders($template->subtext, $category, null, $langId, $business, $extraTags);
            $optionNumber = $template->option_number;
        }

        if (in_array($fieldKey, ['faq_search', 'faq_search_placeholder', 'discussions_prompt_placeholder', 'faq_discussions_prompt_placeholder', 'business_discussions_prompt_placeholder', 'compare_search_placeholder', 'compare_discussions_prompt_placeholder'], true)) {
            return [
                'headline' => self::cleanPlaceholder($headline),
                'subtext' => $subtext,
                'template' => $template,
                'option_number' => $optionNumber,
            ];
        }

        return [
            'headline' => self::cleanHeading($headline),
            'subtext' => $subtext,
            'template' => $template,
            'option_number' => $optionNumber,
        ];
    }
    /**
     * Resolves an A vs B comparison page template section with placeholders replaced.
     */
    public static function resolveComparisonSection(?Category $category, $businesses, string $fieldKey, int $langId = 1, array $extraTags = []): array
    {
        $bizCollection = is_iterable($businesses) ? collect($businesses) : collect();

        $bizNames = [];
        foreach ($bizCollection as $b) {
            $trans = $b->translations ?? collect();
            $t = is_iterable($trans) ? collect($trans)->firstWhere('lang_id', $langId) : null;
            if (!$t && is_iterable($trans)) {
                $t = collect($trans)->first();
            }
            $name = $t->name ?? ($b->name ?? '');
            if (!empty($name)) {
                $bizNames[] = $name;
            }
        }

        $b1 = $bizNames[0] ?? '';
        $b2 = $bizNames[1] ?? '';
        $b3 = $bizNames[2] ?? '';
        $b4 = $bizNames[3] ?? '';
        $bVs = !empty($bizNames) ? implode(' vs ', $bizNames) : '';
        $bList = !empty($bizNames) ? implode(', ', $bizNames) : '';

        $firstBiz = $bizCollection->first();
        if (!$category && $firstBiz && $firstBiz->category) {
            $category = $firstBiz->category;
        }

        $template = null;
        $optionNumber = 1;
        if ($category) {
            $template = self::getTemplateForResolution('category', $category->id, $fieldKey, $langId, 'comparison');
        } else {
            $template = ContentTemplate::where('section_group', 'comparison')
                ->where('field_key', $fieldKey)
                ->where('lang_id', $langId)
                ->where('option_number', 1)
                ->first();
            if (!$template && $langId !== 1) {
                $template = ContentTemplate::where('section_group', 'comparison')
                    ->where('field_key', $fieldKey)
                    ->where('lang_id', 1)
                    ->where('option_number', 1)
                    ->first();
            }
        }

        if ($template) {
            $optionNumber = $template->option_number;
        }

        $compExtraTags = array_merge([
            '[business1]'      => $b1,
            '[business2]'      => $b2,
            '[business3]'      => $b3,
            '[business4]'      => $b4,
            '[business1_cap]'  => ucfirst($b1),
            '[business2_cap]'  => ucfirst($b2),
            '[business_vs]'    => $bVs,
            '[businesses]'     => $bVs,
            '[business_list]'  => $bList,
        ], $extraTags);

        if ($template) {
            $headline = self::replacePlaceholders($template->headline, $category, null, $langId, $firstBiz, $compExtraTags);
            $subtext  = self::replacePlaceholders($template->subtext, $category, null, $langId, $firstBiz, $compExtraTags);

            // Clean up possible dangling "vs " if business2 is missing
            if (empty($b2)) {
                $headline = preg_replace('/\s+vs\s*$/i', '', $headline);
                $subtext = preg_replace('/\s+vs\s*$/i', '', $subtext);
            }

            return [
                'headline'      => self::cleanHeading($headline),
                'subtext'       => $subtext,
                'template'      => $template,
                'option_number' => $optionNumber,
            ];
        }

        // Hardcoded fallbacks if no template exists in DB
        $headline = '';
        $subtext = '';
        if ($fieldKey === 'comparison_h1') {
            $headline = !empty($bVs) ? "{$bVs}: Comparison" : 'Businesses Comparison';
            $catName = $category ? ($category->translations?->firstWhere('lang_id', $langId)?->name ?? $category->name) : '';
            $subtext = !empty($catName)
                ? "Learn more from our team about {$catName} pricing, features, and user ratings in our side-by-side comparison guide."
                : "Learn more from our team about top software and services pricing, features, and user ratings in our side-by-side comparison guide.";
        } elseif ($fieldKey === 'comparison_pros_cons') {
            $headline = 'Pros and cons';
        } elseif ($fieldKey === 'comparison_reviews') {
            $headline = 'Reviews';
        } elseif ($fieldKey === 'comparison_alternatives') {
            $headline = 'Alternatives';
        }

        return [
            'headline'      => self::cleanHeading($headline),
            'subtext'       => $subtext,
            'template'      => null,
            'option_number' => 1,
        ];
    }
}
