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
        'title_quaternary',
        'description_char_limit',
        'description_primary',
        'description_secondary',
        'description_tertiary',
        'description_quaternary',
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
                        'title_quaternary' => $fallback->title_quaternary,
                        'description_char_limit' => $fallback->description_char_limit,
                        'description_primary' => $fallback->description_primary,
                        'description_secondary' => $fallback->description_secondary,
                        'description_tertiary' => $fallback->description_tertiary,
                        'description_quaternary' => $fallback->description_quaternary,
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
            case 'business_alternatives':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => 'Best [business] Alternatives & Competitors [year]',
                    'title_secondary' => 'Top [business] Alternatives [year]',
                    'title_tertiary' => '[business] Alternatives',
                    'title_quaternary' => '[business] Alternatives',
                    'description_char_limit' => 160,
                    'description_primary' => 'Looking for alternatives to [business]? Compare top competitors, key features, pricing, pros & cons, and authentic verified user reviews on Localio.',
                    'description_secondary' => 'Discover top alternatives to [business]. Compare features, pricing, and authentic user reviews on Localio.',
                    'description_tertiary' => 'Compare the best [business] alternatives and competitors on Localio.',
                    'description_quaternary' => '[business] alternatives on Localio.',
                ];
            case 'business_faqs':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[business] FAQs & Expert Answers [year]',
                    'title_secondary' => '[business] FAQs [year]',
                    'title_tertiary' => '[business] Frequently Asked Questions',
                    'title_quaternary' => '[business] FAQs',
                    'description_char_limit' => 160,
                    'description_primary' => 'Have questions about [business]? Find comprehensive answers to frequently asked questions about pricing, features, integrations, and support on Localio.',
                    'description_secondary' => 'Frequently asked questions and verified answers about [business] on Localio.',
                    'description_tertiary' => 'All your questions about [business] answered on Localio.',
                    'description_quaternary' => '[business] FAQs on Localio.',
                ];
            case 'business_compare':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => 'Compare [business] with Top Alternatives [year]',
                    'title_secondary' => 'Compare [business] [year]',
                    'title_tertiary' => '[business] Comparisons',
                    'title_quaternary' => '[business] Compare',
                    'description_char_limit' => 160,
                    'description_primary' => 'Compare [business] head-to-head with top competitors. See side-by-side comparisons of features, pricing, ratings, and user feedback on Localio.',
                    'description_secondary' => 'Side-by-side comparison of [business] and top competitors on Localio.',
                    'description_tertiary' => 'Compare [business] features, pricing, and ratings on Localio.',
                    'description_quaternary' => 'Compare [business] on Localio.',
                ];
            case 'business_reviews':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[business] Reviews, Ratings & Feedback [year]',
                    'title_secondary' => '[business] Reviews [year]',
                    'title_tertiary' => '[business] Verified Reviews',
                    'title_quaternary' => '[business] Reviews',
                    'description_char_limit' => 160,
                    'description_primary' => 'Read authentic, verified user reviews and ratings for [business]. Learn about real customer experiences, pros, cons, and performance on Localio.',
                    'description_secondary' => 'Read authentic customer reviews, ratings, and feedback for [business] on Localio.',
                    'description_tertiary' => 'Verified reviews and user ratings for [business] on Localio.',
                    'description_quaternary' => '[business] reviews on Localio.',
                ];
            case 'business_discussions':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[business] Community Discussions & Q&A [year]',
                    'title_secondary' => '[business] Discussions [year]',
                    'title_tertiary' => '[business] Community',
                    'title_quaternary' => '[business] Discussions',
                    'description_char_limit' => 160,
                    'description_primary' => 'Join the [business] community discussion on Localio. Ask questions, share experiences, get peer advice, and discuss tips with active users.',
                    'description_secondary' => 'Community discussions, tips, and peer advice for [business] on Localio.',
                    'description_tertiary' => 'Discuss [business] questions and insights on Localio.',
                    'description_quaternary' => '[business] discussions on Localio.',
                ];
            case 'category_discussions':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[category_cap] Discussions, Advice & Q&A [year]',
                    'title_secondary' => '[category_cap] Discussions [year]',
                    'title_tertiary' => '[category_cap] Community',
                    'title_quaternary' => '[category_cap] Discussions',
                    'description_char_limit' => 160,
                    'description_primary' => 'Join the [category_cap] community discussions on Localio. Ask questions, exchange recommendations, and get peer advice from verified professionals.',
                    'description_secondary' => 'Community discussions, tips, and recommendations for [category_cap] on Localio.',
                    'description_tertiary' => 'Discuss [category_cap] tips and questions on Localio.',
                    'description_quaternary' => '[category_cap] discussions on Localio.',
                ];
            case 'categories':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => 'Best [category_cap] [Year]',
                    'title_secondary' => 'Best [category_cap]',
                    'title_tertiary' => '[category_cap]',
                    'title_quaternary' => '[category_cap]',
                    'description_char_limit' => 160,
                    'description_primary' => 'Looking for the best [category_cap] in [Year]? Compare top providers, features, pricing, and authentic user reviews on Localio.',
                    'description_secondary' => 'Discover top [category_cap] solutions. Compare features, pricing, and authentic user reviews on Localio.',
                    'description_tertiary' => 'Compare top [category_cap] services and software on Localio.',
                    'description_quaternary' => '[category_cap] on Localio.',
                ];
            case 'businesses':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[business] Reviews & Alternatives [Year]',
                    'title_secondary' => '[business] Reviews [Year]',
                    'title_tertiary' => '[business] on Localio',
                    'title_quaternary' => '[business]',
                    'description_char_limit' => 160,
                    'description_primary' => 'Is [business] right for you? Read authentic verified user reviews, compare pricing, key pros & cons, and top alternatives on Localio.',
                    'description_secondary' => 'Read verified user reviews, compare pricing, features, and alternatives for [business] on Localio.',
                    'description_tertiary' => 'Discover ratings, pricing, and verified reviews for [business] on Localio.',
                    'description_quaternary' => '[business] on Localio.',
                ];
            case 'category_faqs':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[category_cap] FAQs & Expert Answers [Year]',
                    'title_secondary' => '[category_cap] FAQs [Year]',
                    'title_tertiary' => '[category_cap] Frequently Asked Questions',
                    'title_quaternary' => '[category_cap] FAQs',
                    'description_char_limit' => 160,
                    'description_primary' => 'Got questions about [category_cap]? Find expert answers to frequently asked questions, tips, and key considerations on Localio.',
                    'description_secondary' => 'Frequently asked questions and expert answers about [category_cap] on Localio.',
                    'description_tertiary' => 'All your [category_cap] questions answered on Localio.',
                    'description_quaternary' => '[category_cap] FAQs on Localio.',
                ];
            case 'features':
                return [
                    'title_char_limit' => 60,
                    'title_primary' => 'Best [category_cap] with [feature_cap] [Year]',
                    'title_secondary' => '[category_cap] with [feature_cap]',
                    'title_tertiary' => '[feature_cap] [category_cap]',
                    'title_quaternary' => '[feature_cap]',
                    'description_char_limit' => 160,
                    'description_primary' => 'Find the best [category_cap] services offering [feature_cap] in [Year]. Compare pricing, plans, and authentic user reviews on Localio.',
                    'description_secondary' => 'Compare [category_cap] providers with [feature_cap] on Localio.',
                    'description_tertiary' => 'Top [category_cap] featuring [feature_cap] on Localio.',
                    'description_quaternary' => '[feature_cap] on Localio.',
                ];
            default:
                return [
                    'title_char_limit' => 60,
                    'title_primary' => '[name] [Year]',
                    'title_secondary' => '[name]',
                    'title_tertiary' => '[name] on Localio',
                    'title_quaternary' => '[name]',
                    'description_char_limit' => 160,
                    'description_primary' => 'Explore [name] on Localio. Compare features, pricing, and authentic user reviews.',
                    'description_secondary' => 'Find out more about [name] on Localio.',
                    'description_tertiary' => '[name] overview and ratings on Localio.',
                    'description_quaternary' => '[name] on Localio.',
                ];
        }
    }
}
