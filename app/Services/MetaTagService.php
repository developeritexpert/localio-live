<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\CategoryFeatureContent;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\Feature;
use App\Models\FeatureTransalte;
use App\Models\MetaTagRule;

class MetaTagService
{
    /**
     * Helper to reliably get a CategoryTranslation for a category and langId.
     */
    public static function getCategoryTranslation($category, int $langId = 1): ?CategoryTranslation
    {
        if (!$category) {
            return null;
        }

        if ($category->relationLoaded('translations')) {
            if ($category->translations instanceof \Illuminate\Database\Eloquent\Collection) {
                $found = $category->translations->firstWhere('lang_id', $langId);
                if ($found) return $found;
            } elseif ($category->translations instanceof CategoryTranslation && $category->translations->lang_id == $langId) {
                return $category->translations;
            }
        }

        if ($category->relationLoaded('categoryTranslations')) {
            $found = $category->categoryTranslations->firstWhere('lang_id', $langId);
            if ($found) return $found;
        }

        return CategoryTranslation::where('category_id', $category->id)
            ->where('lang_id', $langId)
            ->first()
            ?? CategoryTranslation::where('category_id', $category->id)->where('lang_id', 1)->first()
            ?? CategoryTranslation::where('category_id', $category->id)->first();
    }

    /**
     * Resolve full meta tags for a Category or Subcategory.
     */
    public static function resolveCategoryMeta($category, int $langId = 1, ?MetaTagRule $rule = null): array
    {
        if (!$rule) {
            $rule = MetaTagRule::getRuleFor('categories', $langId);
        }

        $targetTrans = self::getCategoryTranslation($category, $langId);

        $fallbackTrans = null;
        if ((!$targetTrans || empty($targetTrans->name)) && $langId !== 1) {
            $fallbackTrans = CategoryTranslation::where('category_id', $category->id)
                ->where('lang_id', 1)
                ->first();
        }

        $catName = !empty($targetTrans?->name) 
            ? $targetTrans->name 
            : (!empty($fallbackTrans?->name) ? $fallbackTrans->name : ('Category #' . $category->id));

        $manualTitle = trim($targetTrans?->meta_title ?? '');
        $manualDescription = trim($targetTrans?->meta_description ?? '');

        $context = [
            'category_name' => $catName,
            'category' => $catName,
            'name' => $catName,
            'year' => date('Y'),
            'site_name' => 'Localio',
        ];

        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_char_limit,
            $context
        );

        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_char_limit,
            $context
        );

        if (!empty($manualTitle)) {
            $finalTitle = self::replacePlaceholders($manualTitle, $context);
            $titleSource = 'manual';
        } else {
            $finalTitle = $autoTitle;
            $titleSource = $autoTitleSource;
        }

        if (!empty($manualDescription)) {
            $finalDesc = self::replacePlaceholders($manualDescription, $context);
            $descSource = 'manual';
        } else {
            $finalDesc = $autoDesc;
            $descSource = $autoDescSource;
        }

        return [
            'name' => $catName,
            'category_name' => $catName,
            'title' => $finalTitle,
            'title_source' => $titleSource,
            'title_length' => mb_strlen($finalTitle),
            'title_len' => mb_strlen($finalTitle),
            'title_tier' => $titleSource,
            'title_limit' => $rule->title_char_limit,
            'auto_title' => $autoTitle,
            'auto_title_source' => $autoTitleSource,
            'manual_title' => $manualTitle,
            'has_custom_title' => !empty($manualTitle),

            'description' => $finalDesc,
            'description_source' => $descSource,
            'description_length' => mb_strlen($finalDesc),
            'description_len' => mb_strlen($finalDesc),
            'description_tier' => $descSource,
            'description_limit' => $rule->description_char_limit,
            'auto_description' => $autoDesc,
            'auto_description_source' => $autoDescSource,
            'manual_description' => $manualDescription,
            'has_custom_description' => !empty($manualDescription),
        ];
    }

    /**
     * Resolve full meta tags for a Category FAQ page.
     */
    public static function resolveCategoryFaqMeta($category, int $langId = 1, ?MetaTagRule $rule = null): array
    {
        if (!$rule) {
            $rule = MetaTagRule::getRuleFor('category_faqs', $langId);
        }

        $targetTrans = self::getCategoryTranslation($category, $langId);

        $fallbackTrans = null;
        if ((!$targetTrans || empty($targetTrans->name)) && $langId !== 1) {
            $fallbackTrans = CategoryTranslation::where('category_id', $category->id)
                ->where('lang_id', 1)
                ->first();
        }

        $catName = !empty($targetTrans?->name) 
            ? $targetTrans->name 
            : (!empty($fallbackTrans?->name) ? $fallbackTrans->name : ('Category #' . $category->id));

        $manualTitle = trim($targetTrans?->faq_meta_title ?? '');
        $manualDescription = trim($targetTrans?->faq_meta_description ?? '');

        $context = [
            'category_name' => $catName,
            'category' => $catName,
            'name' => $catName,
            'year' => date('Y'),
            'site_name' => 'Localio',
        ];

        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_char_limit,
            $context
        );

        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_char_limit,
            $context
        );

        if (!empty($manualTitle)) {
            $finalTitle = self::replacePlaceholders($manualTitle, $context);
            $titleSource = 'manual';
        } else {
            $finalTitle = $autoTitle;
            $titleSource = $autoTitleSource;
        }

        if (!empty($manualDescription)) {
            $finalDesc = self::replacePlaceholders($manualDescription, $context);
            $descSource = 'manual';
        } else {
            $finalDesc = $autoDesc;
            $descSource = $autoDescSource;
        }

        return [
            'name' => $catName,
            'category_name' => $catName,
            'title' => $finalTitle,
            'title_source' => $titleSource,
            'title_length' => mb_strlen($finalTitle),
            'title_len' => mb_strlen($finalTitle),
            'title_tier' => $titleSource,
            'title_limit' => $rule->title_char_limit,
            'auto_title' => $autoTitle,
            'auto_title_source' => $autoTitleSource,
            'manual_title' => $manualTitle,
            'has_custom_title' => !empty($manualTitle),

            'description' => $finalDesc,
            'description_source' => $descSource,
            'description_length' => mb_strlen($finalDesc),
            'description_len' => mb_strlen($finalDesc),
            'description_tier' => $descSource,
            'description_limit' => $rule->description_char_limit,
            'auto_description' => $autoDesc,
            'auto_description_source' => $autoDescSource,
            'manual_description' => $manualDescription,
            'has_custom_description' => !empty($manualDescription),
        ];
    }

    /**
     * Resolve full meta tags for a Business Profile.
     */
    public static function resolveBusinessMeta($business, int $langId = 1, ?MetaTagRule $rule = null): array
    {
        if (!$rule) {
            $rule = MetaTagRule::getRuleFor('businesses', $langId);
        }

        $targetTrans = null;
        if ($business->relationLoaded('translations')) {
            $targetTrans = $business->translations instanceof \Illuminate\Database\Eloquent\Collection
                ? $business->translations->firstWhere('lang_id', $langId)
                : $business->translations;
        }

        if (!$targetTrans) {
            $targetTrans = BusinessTranslation::where('business_id', $business->id)
                ->where('lang_id', $langId)
                ->first();
        }

        $fallbackTrans = null;
        if ((!$targetTrans || empty($targetTrans->name)) && $langId !== 1) {
            $fallbackTrans = BusinessTranslation::where('business_id', $business->id)
                ->where('lang_id', 1)
                ->first();
        }

        $bizName = !empty($targetTrans?->name) 
            ? $targetTrans->name 
            : (!empty($fallbackTrans?->name) ? $fallbackTrans->name : ('Business #' . $business->id));

        $catName = '';
        if ($business->relationLoaded('category') && $business->category) {
            $catTrans = self::getCategoryTranslation($business->category, $langId);
            $catName = $catTrans?->name ?? $business->category->name ?? '';
        } elseif ($business->category_id) {
            $cat = Category::find($business->category_id);
            if ($cat) {
                $catTrans = self::getCategoryTranslation($cat, $langId);
                $catName = $catTrans?->name ?? $cat->name ?? '';
            }
        }

        $manualTitle = trim($targetTrans?->meta_title ?? ($business->meta_title ?? ''));
        $manualDescription = trim($targetTrans?->meta_description ?? ($business->meta_description ?? ''));

        $context = [
            'business_name' => $bizName,
            'business' => $bizName,
            'name' => $bizName,
            'category_name' => $catName,
            'category' => $catName,
            'year' => date('Y'),
            'site_name' => 'Localio',
        ];

        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_char_limit,
            $context
        );

        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_char_limit,
            $context
        );

        if (!empty($manualTitle)) {
            $finalTitle = self::replacePlaceholders($manualTitle, $context);
            $titleSource = 'manual';
        } else {
            $finalTitle = $autoTitle;
            $titleSource = $autoTitleSource;
        }

        if (!empty($manualDescription)) {
            $finalDesc = self::replacePlaceholders($manualDescription, $context);
            $descSource = 'manual';
        } else {
            $finalDesc = $autoDesc;
            $descSource = $autoDescSource;
        }

        return [
            'name' => $bizName,
            'business_name' => $bizName,
            'category_name' => $catName,
            'title' => $finalTitle,
            'title_source' => $titleSource,
            'title_length' => mb_strlen($finalTitle),
            'title_len' => mb_strlen($finalTitle),
            'title_tier' => $titleSource,
            'title_limit' => $rule->title_char_limit,
            'auto_title' => $autoTitle,
            'auto_title_source' => $autoTitleSource,
            'manual_title' => $manualTitle,
            'has_custom_title' => !empty($manualTitle),

            'description' => $finalDesc,
            'description_source' => $descSource,
            'description_length' => mb_strlen($finalDesc),
            'description_len' => mb_strlen($finalDesc),
            'description_tier' => $descSource,
            'description_limit' => $rule->description_char_limit,
            'auto_description' => $autoDesc,
            'auto_description_source' => $autoDescSource,
            'manual_description' => $manualDescription,
            'has_custom_description' => !empty($manualDescription),
        ];
    }

    /**
     * Resolve full meta tags for a Feature / Subcategory Feature page.
     */
    public static function resolveFeatureMeta($feature, $category = null, int $langId = 1, ?MetaTagRule $rule = null): array
    {
        if (!$rule) {
            $rule = MetaTagRule::getRuleFor('features', $langId);
        }

        $targetTrans = null;
        if ($feature->relationLoaded('translations')) {
            $targetTrans = $feature->translations instanceof \Illuminate\Database\Eloquent\Collection
                ? $feature->translations->firstWhere('lang_id', $langId)
                : $feature->translations;
        }

        if (!$targetTrans) {
            $targetTrans = FeatureTransalte::where('feature_id', $feature->id)
                ->where('lang_id', $langId)
                ->first();
        }

        $fallbackTrans = null;
        if ((!$targetTrans || empty($targetTrans->name)) && $langId !== 1) {
            $fallbackTrans = FeatureTransalte::where('feature_id', $feature->id)
                ->where('lang_id', 1)
                ->first();
        }

        $featName = !empty($targetTrans?->name) 
            ? $targetTrans->name 
            : (!empty($fallbackTrans?->name) ? $fallbackTrans->name : ('Feature #' . $feature->id));

        // Get category name using reliable translation resolver
        $catName = '';
        $catObj = $category ?? $feature->category;
        if (!$catObj && $feature->category_id) {
            $catObj = Category::find($feature->category_id);
        }

        if ($catObj) {
            $catTrans = self::getCategoryTranslation($catObj, $langId);
            $catName = $catTrans?->name ?? $catObj->name ?? '';
        }

        // Check for manual overrides:
        // 1. Check CategoryFeatureContent if category is present
        $manualTitle = '';
        $manualDescription = '';
        if ($catObj) {
            $featContent = CategoryFeatureContent::where('category_id', $catObj->id)
                ->where('feature_id', $feature->id)
                ->where('lang_id', $langId)
                ->first();
            if (!$featContent && $catObj->parent_id) {
                $featContent = CategoryFeatureContent::where('category_id', $catObj->parent_id)
                    ->where('feature_id', $feature->id)
                    ->where('lang_id', $langId)
                    ->first();
            }
            if ($featContent) {
                $manualTitle = trim($featContent->meta_title ?? '');
                $manualDescription = trim($featContent->meta_description ?? '');
            }
        }

        // 2. Fall back to feature_translations.meta_title / meta_description if not set in CategoryFeatureContent
        if (empty($manualTitle)) {
            $manualTitle = trim($targetTrans?->meta_title ?? '');
        }
        if (empty($manualDescription)) {
            $manualDescription = trim($targetTrans?->meta_description ?? '');
        }

        $context = [
            'feature_name' => $featName,
            'feature' => $featName,
            'name' => $featName,
            'category_name' => $catName,
            'category' => $catName,
            'year' => date('Y'),
            'site_name' => 'Localio',
        ];

        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_char_limit,
            $context
        );

        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_char_limit,
            $context
        );

        if (!empty($manualTitle)) {
            $finalTitle = self::replacePlaceholders($manualTitle, $context);
            $titleSource = 'manual';
        } else {
            $finalTitle = $autoTitle;
            $titleSource = $autoTitleSource;
        }

        if (!empty($manualDescription)) {
            $finalDesc = self::replacePlaceholders($manualDescription, $context);
            $descSource = 'manual';
        } else {
            $finalDesc = $autoDesc;
            $descSource = $autoDescSource;
        }

        return [
            'name' => $featName,
            'feature_name' => $featName,
            'category_name' => $catName,
            'title' => $finalTitle,
            'title_source' => $titleSource,
            'title_length' => mb_strlen($finalTitle),
            'title_len' => mb_strlen($finalTitle),
            'title_tier' => $titleSource,
            'title_limit' => $rule->title_char_limit,
            'auto_title' => $autoTitle,
            'auto_title_source' => $autoTitleSource,
            'manual_title' => $manualTitle,
            'has_custom_title' => !empty($manualTitle),

            'description' => $finalDesc,
            'description_source' => $descSource,
            'description_length' => mb_strlen($finalDesc),
            'description_len' => mb_strlen($finalDesc),
            'description_tier' => $descSource,
            'description_limit' => $rule->description_char_limit,
            'auto_description' => $autoDesc,
            'auto_description_source' => $autoDescSource,
            'manual_description' => $manualDescription,
            'has_custom_description' => !empty($manualDescription),
        ];
    }

    /**
     * Evaluates Primary -> Secondary -> Tertiary cascade against a character limit.
     */
    public static function evaluateCascade(?string $primary, ?string $secondary, ?string $tertiary, int $limit, array $context): array
    {
        if (!empty($primary)) {
            $evaluated = self::replacePlaceholders($primary, $context);
            if (mb_strlen($evaluated) <= $limit) {
                return [$evaluated, 'primary'];
            }
        }

        if (!empty($secondary)) {
            $evaluated = self::replacePlaceholders($secondary, $context);
            if (mb_strlen($evaluated) <= $limit) {
                return [$evaluated, 'secondary'];
            }
        }

        if (!empty($tertiary)) {
            $evaluated = self::replacePlaceholders($tertiary, $context);
            return [$evaluated, 'tertiary'];
        }

        $fallback = $context['category_name'] ?? ($context['business_name'] ?? ($context['feature_name'] ?? ($context['name'] ?? '')));
        return [$fallback, 'fallback'];
    }

    /**
     * Replace dynamic placeholders with given context values.
     */
    public static function replacePlaceholders(string $text, array $context): string
    {
        $catName = $context['category_name'] ?? ($context['category'] ?? '');
        $bizName = $context['business_name'] ?? ($context['business'] ?? '');
        $featName = $context['feature_name'] ?? ($context['feature'] ?? '');
        $primaryName = $bizName ?: ($featName ?: ($catName ?: ($context['name'] ?? '')));
        $year = $context['year'] ?? date('Y');
        $site = $context['site_name'] ?? 'Localio';

        $search = [
            '[category name]', '[category_name]', '[category]', '[Category Name]', '[Category]',
            '{category_name}', '{category}', '{Category Name}', '{Category}',
            '[business name]', '[business_name]', '[business]', '[Business Name]', '[Business]',
            '{business_name}', '{business}', '{Business Name}', '{Business}',
            '[feature name]', '[feature_name]', '[feature]', '[Feature Name]', '[Feature]',
            '{feature_name}', '{feature}', '{Feature Name}', '{Feature}',
            '[Year]', '[year]', '{year}', '{Year}', '%%year%%', '%%currentyear%%',
            '[Site Name]', '[site_name]', '[brand]', '[Brand]', '{site_name}',
            '[name]', '{name}'
        ];

        $replace = [
            $catName, $catName, $catName, $catName, $catName,
            $catName, $catName, $catName, $catName,
            $bizName, $bizName, $bizName, $bizName, $bizName,
            $bizName, $bizName, $bizName, $bizName,
            $featName, $featName, $featName, $featName, $featName,
            $featName, $featName, $featName, $featName,
            $year, $year, $year, $year, $year, $year,
            $site, $site, $site, $site, $site,
            $primaryName, $primaryName
        ];

        $clean = str_ireplace($search, $replace, $text);

        if (function_exists('format_meta_text')) {
            $clean = format_meta_text($clean);
        }

        return trim(preg_replace('/\s+/', ' ', $clean));
    }
}
