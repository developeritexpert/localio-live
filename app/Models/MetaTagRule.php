<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaTagRule extends Model
{
    use HasFactory;

    protected $table = 'meta_tag_rules';

    protected $fillable = [
        'entity_type',
        'lang_id',
        'title_char_limit',
        'title_primary',
        'title_secondary',
        'title_tertiary',
        'description_char_limit',
        'description_primary',
        'description_secondary',
        'description_tertiary',
    ];

    /**
     * Get or create standard default rule for an entity type and language.
     */
    public static function getRuleFor(string $entityType, int $langId = 1): self
    {
        $rule = self::where('entity_type', $entityType)
            ->where('lang_id', $langId)
            ->first();

        if (!$rule) {
            // Check fallback for lang_id 1
            if ($langId !== 1) {
                $fallback = self::where('entity_type', $entityType)->where('lang_id', 1)->first();
                if ($fallback) {
                    return self::create([
                        'entity_type' => $entityType,
                        'lang_id' => $langId,
                        'title_char_limit' => $fallback->title_char_limit,
                        'title_primary' => $fallback->title_primary,
                        'title_secondary' => $fallback->title_secondary,
                        'title_tertiary' => $fallback->title_tertiary,
                        'description_char_limit' => $fallback->description_char_limit,
                        'description_primary' => $fallback->description_primary,
                        'description_secondary' => $fallback->description_secondary,
                        'description_tertiary' => $fallback->description_tertiary,
                    ]);
                }
            }

            // Create initial defaults based on entity type
            $defaults = self::getDefaultsFor($entityType);
            $rule = self::create(array_merge([
                'entity_type' => $entityType,
                'lang_id' => $langId,
            ], $defaults));
        }

        return $rule;
    }

    /**
     * Initial template defaults per entity type.
     */
    public static function getDefaultsFor(string $entityType): array
    {
        switch ($entityType) {
            case 'categories':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => 'Best [category name] [Year]',
                    'title_secondary' => 'Best [category name]',
                    'title_tertiary' => '[category name]',
                    'description_char_limit' => 160,
                    'description_primary' => 'Looking for the best [category name] in [Year]? Compare top providers, features, pricing, and authentic user reviews on Localio.',
                    'description_secondary' => 'Discover top [category name] solutions. Compare features, pricing, and authentic user reviews on Localio.',
                    'description_tertiary' => 'Compare top [category name] services and software on Localio.',
                ];
            case 'businesses':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[business name] Reviews & Alternatives [Year]',
                    'title_secondary' => '[business name] Reviews [Year]',
                    'title_tertiary' => '[business name] on Localio',
                    'description_char_limit' => 160,
                    'description_primary' => 'Is [business name] right for you? Read authentic verified user reviews, compare pricing, key pros & cons, and top alternatives on Localio.',
                    'description_secondary' => 'Read verified user reviews, compare pricing, features, and alternatives for [business name] on Localio.',
                    'description_tertiary' => 'Discover ratings, pricing, and verified reviews for [business name] on Localio.',
                ];
            case 'category_faqs':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[category name] FAQs & Expert Answers [Year]',
                    'title_secondary' => '[category name] FAQs [Year]',
                    'title_tertiary' => '[category name] Frequently Asked Questions',
                    'description_char_limit' => 160,
                    'description_primary' => 'Got questions about [category name]? Find expert answers to frequently asked questions, tips, and key considerations on Localio.',
                    'description_secondary' => 'Frequently asked questions and expert answers about [category name] on Localio.',
                    'description_tertiary' => 'All your [category name] questions answered on Localio.',
                ];
            case 'features':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => 'Best [category name] with [feature name] [Year]',
                    'title_secondary' => '[category name] with [feature name]',
                    'title_tertiary' => '[feature name] [category name]',
                    'description_char_limit' => 160,
                    'description_primary' => 'Find the best [category name] services offering [feature name] in [Year]. Compare pricing, plans, and authentic user reviews on Localio.',
                    'description_secondary' => 'Compare [category name] providers with [feature name] on Localio.',
                    'description_tertiary' => 'Top [category name] featuring [feature name] on Localio.',
                ];
            default:
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[name] [Year]',
                    'title_secondary' => '[name]',
                    'title_tertiary' => '[name] on Localio',
                    'description_char_limit' => 160,
                    'description_primary' => 'Explore [name] on Localio. Compare features, pricing, and authentic user reviews.',
                    'description_secondary' => 'Find out more about [name] on Localio.',
                    'description_tertiary' => '[name] overview and ratings on Localio.',
                ];
        }
    }
}
