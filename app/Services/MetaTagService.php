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
     * Get global context placeholders (year, country1, country2, website).
     */
    public static function getGlobalContext(int $langId = 1): array
    {
        $currentYear = date('Y');
        $c1Obj = \App\Models\StaticContentKey::where('key', 'country1')->first();
        $c2Obj = \App\Models\StaticContentKey::where('key', 'country2')->first();
        $c1 = $c1Obj ? $c1Obj->getTranslation($langId) : '';
        $c2 = $c2Obj ? $c2Obj->getTranslation($langId) : '';

        return [
            'year'     => $currentYear,
            'country1' => $c1,
            'country2' => $c2,
            'website'  => 'Localio',
            'site_name'=> 'Localio',
        ];
    }

    /**
     * Get rich category placeholders (category_cap, category_tc, category_lc, entity1_*, entity2_*).
     */
    public static function getCategoryContext(?Category $category, int $langId = 1): array
    {
        if (!$category) {
            return [];
        }

        $catTrans = self::getCategoryTranslation($category, $langId);
        $rawName = $catTrans?->name ?? $category->name ?? '';
        $catCap = !empty($catTrans?->category_cap) ? $catTrans->category_cap : ucfirst(mb_strtolower($rawName));
        $catTc  = !empty($catTrans?->category_tc)  ? $catTrans->category_tc  : mb_convert_case($rawName, MB_CASE_TITLE, 'UTF-8');
        $catLc  = !empty($catTrans?->category_lc)  ? $catTrans->category_lc  : mb_strtolower($rawName);

        $e1Cap        = !empty($catTrans?->entity1_cap)        ? $catTrans->entity1_cap        : ($catCap ? $catCap . ' provider' : '');
        $e1Lc         = !empty($catTrans?->entity1_lc)         ? $catTrans->entity1_lc         : mb_strtolower($e1Cap);
        $es1Cap       = !empty($catTrans?->entities1_cap)      ? $catTrans->entities1_cap      : ($catCap ? $catCap . ' providers' : '');
        $es1Lc        = !empty($catTrans?->entities1_lc)       ? $catTrans->entities1_lc       : mb_strtolower($es1Cap);
        $e1ShortCap   = !empty($catTrans?->entity1_short_cap)  ? $catTrans->entity1_short_cap  : 'Provider';
        $e1ShortLc    = !empty($catTrans?->entity1_short_lc)   ? $catTrans->entity1_short_lc   : 'provider';
        $es1ShortCap  = !empty($catTrans?->entities1_short_cap) ? $catTrans->entities1_short_cap : 'Providers';
        $es1ShortLc   = !empty($catTrans?->entities1_short_lc)  ? $catTrans->entities1_short_lc  : 'providers';

        $e2Cap        = !empty($catTrans?->entity2_cap)        ? $catTrans->entity2_cap        : $e1Cap;
        $e2Lc         = !empty($catTrans?->entity2_lc)         ? $catTrans->entity2_lc         : $e1Lc;
        $es2Cap       = !empty($catTrans?->entities2_cap)      ? $catTrans->entities2_cap      : $es1Cap;
        $es2Lc        = !empty($catTrans?->entities2_lc)       ? $catTrans->entities2_lc       : $es1Lc;
        $e2ShortCap   = !empty($catTrans?->entity2_short_cap)  ? $catTrans->entity2_short_cap  : $e1ShortCap;
        $e2ShortLc    = !empty($catTrans?->entity2_short_lc)   ? $catTrans->entity2_short_lc   : $e1ShortLc;
        $es2ShortCap  = !empty($catTrans?->entities2_short_cap) ? $catTrans->entities2_short_cap : $es1ShortCap;
        $es2ShortLc   = !empty($catTrans?->entities2_short_lc)  ? $catTrans->entities2_short_lc  : $es1ShortLc;

        return [
            'category_name'        => (!empty($rawName) ? $rawName : 'Category #' . $category->id),
            'category_cap'         => $catCap,
            'category_tc'          => $catTc,
            'category_lc'          => $catLc,
            'entity1_cap'          => $e1Cap,
            'entity1_lc'           => $e1Lc,
            'entities1_cap'        => $es1Cap,
            'entities1_lc'         => $es1Lc,
            'entity1_short_cap'    => $e1ShortCap,
            'entity1_short_lc'     => $e1ShortLc,
            'entities1_short_cap'  => $es1ShortCap,
            'entities1_short_lc'   => $es1ShortLc,
            'entity2_cap'          => $e2Cap,
            'entity2_lc'           => $e2Lc,
            'entities2_cap'        => $es2Cap,
            'entities2_lc'         => $es2Lc,
            'entity2_short_cap'    => $e2ShortCap,
            'entity2_short_lc'     => $e2ShortLc,
            'entities2_short_cap'  => $es2ShortCap,
            'entities2_short_lc'   => $es2ShortLc,
        ];
    }

    /**
     * Get feature placeholders (feature_cap, features_tc, feature_lc).
     */
    public static function getFeatureContext($feature, int $langId = 1): array
    {
        if (!$feature) {
            return [];
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

        $rawFeatName = !empty($targetTrans?->name) 
            ? $targetTrans->name 
            : (!empty($fallbackTrans?->name) ? $fallbackTrans->name : ($feature->name ?? ''));

        $featCap = !empty($targetTrans?->feature_cap) 
            ? $targetTrans->feature_cap 
            : (!empty($targetTrans?->category_cap) ? $targetTrans->category_cap : ucfirst(mb_strtolower($rawFeatName)));

        $featTc  = !empty($targetTrans?->features_tc) 
            ? $targetTrans->features_tc 
            : (!empty($targetTrans?->feature_tc) ? $targetTrans->feature_tc : (!empty($targetTrans?->category_tc) ? $targetTrans->category_tc : mb_convert_case($rawFeatName, MB_CASE_TITLE, 'UTF-8')));

        $featLc  = !empty($targetTrans?->feature_lc) 
            ? $targetTrans->feature_lc 
            : (!empty($targetTrans?->category_lc) ? $targetTrans->category_lc : mb_strtolower($rawFeatName));

        return [
            'feature_name' => $rawFeatName,
            'feature_cap'  => $featCap,
            'features_tc'  => $featTc,
            'feature_tc'   => $featTc,
            'feature_lc'   => $featLc,
        ];
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

        $context = array_merge(
            self::getGlobalContext($langId),
            self::getCategoryContext($category, $langId),
            [
                'category_name' => $catName,
                'category' => $catName,
                'name' => $catName,
            ]
        );

        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_quaternary ?? null,
            $rule->title_char_limit,
            $context
        );

        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_quaternary ?? null,
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

        $context = array_merge(
            self::getGlobalContext($langId),
            self::getCategoryContext($category, $langId),
            [
                'category_name' => $catName,
                'category' => $catName,
                'name' => $catName,
            ]
        );

        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_quaternary ?? null,
            $rule->title_char_limit,
            $context
        );

        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_quaternary ?? null,
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

        $manualTitle = trim($targetTrans?->meta_title ?? '');
        $manualDescription = trim($targetTrans?->meta_description ?? '');

        if (!$targetTrans && $langId === 1) {
            $manualTitle = trim($business->meta_title ?? '');
            $manualDescription = trim($business->meta_description ?? '');
        }

        $categoryObj = null;
        if ($business->relationLoaded('category') && $business->category) {
            $categoryObj = $business->category;
        } elseif ($business->category_id) {
            $categoryObj = Category::find($business->category_id);
        }

        $context = array_merge(
            self::getGlobalContext($langId),
            self::getCategoryContext($categoryObj, $langId),
            [
                'business_name' => $bizName,
                'business' => $bizName,
                'name' => $bizName,
                'category_name' => $catName,
                'category' => $catName,
            ]
        );

        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_quaternary ?? null,
            $rule->title_char_limit,
            $context
        );

        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_quaternary ?? null,
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

        $context = array_merge(
            self::getGlobalContext($langId),
            self::getCategoryContext($catObj, $langId),
            self::getFeatureContext($feature, $langId),
            [
                'feature_name' => $featName,
                'feature' => $featName,
                'name' => $featName,
                'category_name' => $catName,
                'category' => $catName,
            ]
        );

        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_quaternary ?? null,
            $rule->title_char_limit,
            $context
        );

        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_quaternary ?? null,
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
     * Evaluates Primary -> Secondary -> Tertiary -> Quaternary cascade against a character limit.
     * Quaternary (4°) is the worst-case fallback used even if it exceeds the character limit.
     */
    public static function evaluateCascade(?string $primary, ?string $secondary, ?string $tertiary, ?string $quaternary, int $limit, array $context): array
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
            if (mb_strlen($evaluated) <= $limit) {
                return [$evaluated, 'tertiary'];
            }
        }

        if (!empty($quaternary)) {
            $evaluated = self::replacePlaceholders($quaternary, $context);
            return [$evaluated, 'quaternary'];
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
        $site = $context['website'] ?? ($context['site_name'] ?? 'Localio');
        $c1 = $context['country1'] ?? '';
        $c2 = $context['country2'] ?? '';

        $catCap = $context['category_cap'] ?? ($catName ? ucfirst(mb_strtolower($catName)) : '');
        $catTc  = $context['category_tc'] ?? ($catName ? mb_convert_case($catName, MB_CASE_TITLE, 'UTF-8') : '');
        $catLc  = $context['category_lc'] ?? ($catName ? mb_strtolower($catName) : '');

        $e1Cap        = $context['entity1_cap'] ?? ($catCap ? $catCap . ' provider' : '');
        $e1Lc         = $context['entity1_lc'] ?? ($e1Cap ? mb_strtolower($e1Cap) : '');
        $es1Cap       = $context['entities1_cap'] ?? ($catCap ? $catCap . ' providers' : '');
        $es1Lc        = $context['entities1_lc'] ?? ($es1Cap ? mb_strtolower($es1Cap) : '');
        $e1ShortCap   = $context['entity1_short_cap'] ?? 'Provider';
        $e1ShortLc    = $context['entity1_short_lc'] ?? 'provider';
        $es1ShortCap  = $context['entities1_short_cap'] ?? 'Providers';
        $es1ShortLc   = $context['entities1_short_lc'] ?? 'providers';

        $e2Cap        = $context['entity2_cap'] ?? $e1Cap;
        $e2Lc         = $context['entity2_lc'] ?? $e1Lc;
        $es2Cap       = $context['entities2_cap'] ?? $es1Cap;
        $es2Lc        = $context['entities2_lc'] ?? $es1Lc;
        $e2ShortCap   = $context['entity2_short_cap'] ?? $e1ShortCap;
        $e2ShortLc    = $context['entity2_short_lc'] ?? $e1ShortLc;
        $es2ShortCap  = $context['entities2_short_cap'] ?? $es1ShortCap;
        $es2ShortLc   = $context['entities2_short_lc'] ?? $es1ShortLc;

        $featCap = $context['feature_cap'] ?? ($featName ? ucfirst(mb_strtolower($featName)) : '');
        $featTc  = $context['features_tc'] ?? ($context['feature_tc'] ?? ($featName ? mb_convert_case($featName, MB_CASE_TITLE, 'UTF-8') : ''));
        $featLc  = $context['feature_lc'] ?? ($featName ? mb_strtolower($featName) : '');

        $search = [
            // Exact requested tags
            '[business]',
            '[category_cap]', '[category_tc]', '[category_lc]',
            '[entity1_cap]', '[entity1_lc]', '[entities1_cap]', '[entities1_lc]',
            '[entity1_short_cap]', '[entity1_short_lc]', '[entities1_short_cap]', '[entities1_short_lc]',
            '[entity2_cap]', '[entity2_lc]', '[entities2_cap]', '[entities2_lc]',
            '[entity2_short_cap]', '[entity2_short_lc]', '[entities2_short_cap]', '[entities2_short_lc]',
            '[feature_cap]', '[features_tc]', '[feature_tc]', '[feature_lc]',
            '[country1]', '[country2]',
            '[year]', '[Year]',
            '[website]',

            // Legacy & convenience aliases
            '[category name]', '[category_name]', '[category]', '[Category Name]', '[Category]',
            '{category_name}', '{category}', '{Category Name}', '{Category}',
            '[business name]', '[business_name]', '[Business Name]', '[Business]',
            '{business_name}', '{business}', '{Business Name}', '{Business}',
            '[feature name]', '[feature_name]', '[Feature Name]', '[Feature]',
            '{feature_name}', '{feature}', '{Feature Name}', '{Feature}',
            '{year}', '{Year}', '%%year%%', '%%currentyear%%',
            '[Site Name]', '[site_name]', '[brand]', '[Brand]', '{site_name}',
            '[name]', '{name}'
        ];

        $replace = [
            $bizName,
            $catCap, $catTc, $catLc,
            $e1Cap, $e1Lc, $es1Cap, $es1Lc,
            $e1ShortCap, $e1ShortLc, $es1ShortCap, $es1ShortLc,
            $e2Cap, $e2Lc, $es2Cap, $es2Lc,
            $e2ShortCap, $e2ShortLc, $es2ShortCap, $es2ShortLc,
            $featCap, $featTc, $featTc, $featLc,
            $c1, $c2,
            $year, $year,
            $site,

            // Legacy replacements
            $catName, $catName, $catName, $catName, $catName,
            $catName, $catName, $catName, $catName,
            $bizName, $bizName, $bizName, $bizName,
            $bizName, $bizName, $bizName, $bizName,
            $featName, $featName, $featName, $featName,
            $featName, $featName, $featName, $featName,
            $year, $year, $year, $year,
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
