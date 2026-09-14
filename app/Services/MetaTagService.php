<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\MetaTagRule;

class MetaTagService
{
    /**
     * Resolve full meta tags for a Category, including cascade rules and manual overrides.
     */
    public static function resolveCategoryMeta($category, int $langId = 1, ?MetaTagRule $rule = null): array
    {
        if (!$rule) {
            $rule = MetaTagRule::getRuleFor('categories', $langId);
        }

        // 1. Get translation specifically for $langId
        $targetTrans = null;
        if ($category->relationLoaded('translations')) {
            $targetTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection
                ? $category->translations->firstWhere('lang_id', $langId)
                : $category->translations;
        }

        if (!$targetTrans) {
            $targetTrans = CategoryTranslation::where('category_id', $category->id)
                ->where('lang_id', $langId)
                ->first();
        }

        // 2. Get fallback translation for category name if target translation doesn't have a name
        $fallbackTrans = null;
        if ((!$targetTrans || empty($targetTrans->name)) && $langId !== 1) {
            $fallbackTrans = CategoryTranslation::where('category_id', $category->id)
                ->where('lang_id', 1)
                ->first();
        }

        $catName = !empty($targetTrans?->name) 
            ? $targetTrans->name 
            : (!empty($fallbackTrans?->name) ? $fallbackTrans->name : ($category->name ?? ('Category #' . $category->id)));

        // Manual overrides MUST be specific to $targetTrans so languages do NOT cross-contaminate
        $manualTitle = trim($targetTrans?->meta_title ?? '');
        $manualDescription = trim($targetTrans?->meta_description ?? '');

        // Placeholders map
        $context = [
            'category_name' => $catName,
            'name' => $catName,
            'year' => date('Y'),
            'site_name' => 'Localio',
        ];

        // 1. Resolve Dynamic Title using Cascade Rules
        list($autoTitle, $autoTitleSource) = self::evaluateCascade(
            $rule->title_primary,
            $rule->title_secondary,
            $rule->title_tertiary,
            $rule->title_char_limit,
            $context
        );

        // 2. Resolve Dynamic Description using Cascade Rules
        list($autoDesc, $autoDescSource) = self::evaluateCascade(
            $rule->description_primary,
            $rule->description_secondary,
            $rule->description_tertiary,
            $rule->description_char_limit,
            $context
        );

        // Determine active Title
        if (!empty($manualTitle)) {
            $finalTitle = self::replacePlaceholders($manualTitle, $context);
            $titleSource = 'manual';
        } else {
            $finalTitle = $autoTitle;
            $titleSource = $autoTitleSource;
        }

        // Determine active Description
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
     * Evaluates Primary -> Secondary -> Tertiary cascade against a character limit.
     */
    public static function evaluateCascade(?string $primary, ?string $secondary, ?string $tertiary, int $limit, array $context): array
    {
        // 1. Try Primary
        if (!empty($primary)) {
            $evaluated = self::replacePlaceholders($primary, $context);
            if (mb_strlen($evaluated) <= $limit) {
                return [$evaluated, 'primary'];
            }
        }

        // 2. Try Secondary
        if (!empty($secondary)) {
            $evaluated = self::replacePlaceholders($secondary, $context);
            if (mb_strlen($evaluated) <= $limit) {
                return [$evaluated, 'secondary'];
            }
        }

        // 3. Try Tertiary
        if (!empty($tertiary)) {
            $evaluated = self::replacePlaceholders($tertiary, $context);
            return [$evaluated, 'tertiary'];
        }

        // Fallback if none defined
        $fallback = $context['category_name'] ?? ($context['name'] ?? '');
        return [$fallback, 'fallback'];
    }

    /**
     * Replace dynamic placeholders with given context values.
     */
    public static function replacePlaceholders(string $text, array $context): string
    {
        $catName = $context['category_name'] ?? ($context['name'] ?? '');
        $year = $context['year'] ?? date('Y');
        $site = $context['site_name'] ?? 'Localio';

        $search = [
            '[category name]', '[category_name]', '[category]', '[Category Name]', '[Category]',
            '{category_name}', '{category}', '{Category Name}', '{Category}',
            '[Year]', '[year]', '{year}', '{Year}', '%%year%%', '%%currentyear%%',
            '[Site Name]', '[site_name]', '[brand]', '[Brand]', '{site_name}',
            '[name]', '[business name]', '[feature name]'
        ];

        $replace = [
            $catName, $catName, $catName, $catName, $catName,
            $catName, $catName, $catName, $catName,
            $year, $year, $year, $year, $year, $year,
            $site, $site, $site, $site, $site,
            $catName, $catName, $catName
        ];

        $clean = str_ireplace($search, $replace, $text);

        if (function_exists('format_meta_text')) {
            $clean = format_meta_text($clean);
        }

        return trim(preg_replace('/\s+/', ' ', $clean));
    }
}
