<?php

namespace App\Services;

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
            'label' => 'Header Introduction Text',
            'type' => 'combined',
            'field1_label' => 'Header Question (ends paragraph 1)',
            'field2_label' => 'Second Header Text (paragraph 2)',
            'description' => 'Combines individual category intro with question and second overview paragraph.',
        ],
        'explore_link' => [
            'label' => '"Explore [business]" Link Text',
            'type' => 'single',
            'field1_label' => 'Link Text',
            'description' => 'Used on top-rated block link (e.g. See all [entities1_cap]).',
        ],
        'compare_top_rated' => [
            'label' => 'Compare Top-Rated',
            'type' => 'combined',
            'field1_label' => 'Compare Top-Rated Headline (H2)',
            'field2_label' => 'Compare Top-Rated Subtext',
            'description' => 'Headline and subtext for the top-rated providers section.',
        ],
        'top_10' => [
            'label' => 'Top 10',
            'type' => 'combined',
            'field1_label' => 'Top 10 Headline (H2)',
            'field2_label' => 'Top 10 Subtext',
            'description' => 'Headline and subtext for the Top 10 comparison table section.',
        ],
        'pros_cons' => [
            'label' => 'Pros and Cons',
            'type' => 'combined',
            'field1_label' => 'Pros and Cons Headline (H2)',
            'field2_label' => 'Pros and Cons Subtext',
            'description' => 'Headline and subtext for the community feedback section.',
        ],
        'popular_comparisons' => [
            'label' => 'Popular Comparisons',
            'type' => 'combined',
            'field1_label' => 'Popular Comparisons Headline (H2)',
            'field2_label' => 'Popular Comparisons Subtext',
            'description' => 'Headline and subtext for the popular side-by-side comparisons section.',
        ],
        'compare_tool' => [
            'label' => 'Comparison Tool',
            'type' => 'combined',
            'field1_label' => 'Comparison Tool Headline (H2)',
            'field2_label' => 'Comparison Tool Subtext',
            'description' => 'Headline and subtext for interactive comparison tool box.',
        ],
        'explore_subcategories' => [
            'label' => 'Explore Subcategories',
            'type' => 'combined',
            'field1_label' => 'Explore Subcategories Headline (H2)',
            'field2_label' => 'Explore Subcategories Subtext',
            'description' => 'Headline and subtext for explore subcategories/categories section.',
        ],
    ];

    public const CATEGORY_FAQ_FIELDS = [
        'category_page_faqs' => [
            'label' => 'Category Page FAQs',
            'type' => 'combined',
            'field1_label' => 'Category Page FAQs Headline (H2)',
            'field2_label' => 'Category Page FAQs Subtext',
            'description' => 'Headline and subtext for the FAQ section on main Category page.',
        ],
        'faq_about' => [
            'label' => 'FAQ Page Second Headline & Content',
            'type' => 'combined',
            'field1_label' => 'FAQ Page Second Headline (H2)',
            'field2_label' => 'FAQ Page Content Text (Rich Editor)',
            'description' => 'Second section intro headline and rich text on FAQ page.',
        ],
        'faq_explore' => [
            'label' => 'FAQ Explore Categories',
            'type' => 'combined',
            'field1_label' => 'FAQ Explore Categories Headline (H2)',
            'field2_label' => 'FAQ Explore Categories Subtext',
            'description' => 'Headline and subtext above the explore subcategories on FAQ page.',
        ],
    ];

    public const FEATURE_FIELDS = [
        'feature_h1' => [
            'label' => 'Feature Page H1 Headline',
            'type' => 'single',
            'field1_label' => 'Feature H1 Headline',
            'description' => 'Main page title for Feature pages (e.g. Best [category_tc] with [feature_tc] [country2] [year]).',
        ],
    ];

    /**
     * Get or randomly assign & permanently lock a template for an entity.
     */
    public static function getOrAssignTemplate(string $entityType, int $entityId, string $fieldKey, int $langId = 1): ?ContentTemplate
    {
        // 1. Check if an assignment is already locked in DB
        $assignment = ContentTemplateAssignment::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('lang_id', $langId)
            ->where('field_key', $fieldKey)
            ->with('template')
            ->first();

        if ($assignment && $assignment->template && $assignment->template->is_active) {
            return $assignment->template;
        }

        // 2. Fetch active templates for this field_key & lang_id
        $templates = ContentTemplate::where('field_key', $fieldKey)
            ->where('lang_id', $langId)
            ->where('is_active', true)
            ->get();

        // Fallback to lang_id = 1 if current language has no templates yet
        if ($templates->isEmpty() && $langId !== 1) {
            $templates = ContentTemplate::where('field_key', $fieldKey)
                ->where('lang_id', 1)
                ->where('is_active', true)
                ->get();
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
    public static function assignMissingForCategories(int $langId = 1): int
    {
        $allCategoryIds = Category::pluck('id');
        $allFieldKeys = array_merge(
            array_keys(self::CATEGORY_FIELDS),
            array_keys(self::CATEGORY_FAQ_FIELDS)
        );

        $assignedCount = 0;
        foreach ($allCategoryIds as $catId) {
            foreach ($allFieldKeys as $fieldKey) {
                $exists = ContentTemplateAssignment::where('entity_type', 'category')
                    ->where('entity_id', $catId)
                    ->where('lang_id', $langId)
                    ->where('field_key', $fieldKey)
                    ->exists();

                if (!$exists) {
                    $template = self::getOrAssignTemplate('category', $catId, $fieldKey, $langId);
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
        $assignedCount = 0;

        foreach ($allFeatureIds as $featId) {
            $exists = ContentTemplateAssignment::where('entity_type', 'feature')
                ->where('entity_id', $featId)
                ->where('lang_id', $langId)
                ->where('field_key', 'feature_h1')
                ->exists();

            if (!$exists) {
                $template = self::getOrAssignTemplate('feature', $featId, 'feature_h1', $langId);
                if ($template) {
                    $assignedCount++;
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
    public static function ensureTemplatesExistForLanguage(string $sectionGroup, int $langId): void
    {
        if ($langId <= 0) {
            return;
        }

        $existingCount = ContentTemplate::where('section_group', $sectionGroup)
            ->where('lang_id', $langId)
            ->count();

        if ($existingCount === 0) {
            $defaultTemplates = ContentTemplate::where('section_group', $sectionGroup)
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

    public static function replacePlaceholders(?string $text, ?Category $category, ?Feature $feature = null, int $langId = 1): string
    {
        if (empty($text)) {
            return '';
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
        }

        // Feature tags
        if ($feature) {
            $featTrans = $feature->translations;
            if ($featTrans instanceof \Illuminate\Database\Eloquent\Collection) {
                $featTrans = $featTrans->where('lang_id', $langId)->first() ?? $featTrans->first();
            }
            $rawFeatName = $featTrans?->name ?? $feature->name ?? '';
            $featCap = ucfirst(mb_strtolower($rawFeatName));
            $featTc  = mb_convert_case($rawFeatName, MB_CASE_TITLE, 'UTF-8');
            $featLc  = mb_strtolower($rawFeatName);

            $featReplacements = [
                '[feature_cap]'  => $featCap,
                '[feature_tc]'   => $featTc,
                '[feature_lc]'   => $featLc,
                '[feature]'      => $featLc,
                '[Feature]'      => $featCap,
                '[feature name]' => $featLc,
                '[Feature Name]' => $featCap,
                '{feature}'      => $featLc,
                '{Feature}'      => $featCap,
            ];

            foreach ($featReplacements as $search => $replace) {
                $text = str_replace($search, $replace, $text);
            }
        }

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
        $template = self::getOrAssignTemplate('category', $category->id, 'header_intro', $langId);

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
    public static function resolveCategorySection(Category $category, string $fieldKey, int $langId = 1): array
    {
        $catTrans = self::getCategoryTranslation($category, $langId);

        $template = self::getOrAssignTemplate('category', $category->id, $fieldKey, $langId);

        $headline = '';
        $subtext = '';
        $optionNumber = null;

        if ($template) {
            $headline = self::replacePlaceholders($template->headline, $category, null, $langId);
            $subtext = self::replacePlaceholders($template->subtext, $category, null, $langId);
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
                case 'category_page_faqs':
                    $headline = $catTrans->category_faqs_headline ?? 'Frequently asked questions';
                    $subtext = $catTrans->category_faqs_subtext ?? '';
                    break;
                case 'faq_about':
                    $headline = $catTrans->faq_about_headline ?? ('About ' . ($catTrans->name ?? ''));
                    $subtext = $catTrans->faq_about_description ?? '';
                    break;
                case 'faq_explore':
                    $headline = $catTrans->faq_explore_headline ?? ('Explore ' . ($catTrans->name ?? '') . ' categories');
                    $subtext = $catTrans->faq_explore_subtext ?? '';
                    break;
            }
        }

        return [
            'headline' => $headline,
            'subtext' => $subtext,
            'template' => $template,
            'option_number' => $optionNumber,
        ];
    }

    /**
     * Resolves Feature H1 headline using assigned template.
     */
    public static function resolveFeatureH1(Feature $feature, ?Category $category = null, int $langId = 1): array
    {
        $template = self::getOrAssignTemplate('feature', $feature->id, 'feature_h1', $langId);

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
            'headline' => $headline,
            'template' => $template,
            'option_number' => $optionNumber,
        ];
    }
}