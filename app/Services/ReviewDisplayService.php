<?php

namespace App\Services;

use App\Models\Bcp47Language;
use App\Models\Language;
use App\Models\Review;
use App\Models\ReviewTranslation;
use Illuminate\Support\Collection;

class ReviewDisplayService
{
    /**
     * Resolve the active BCP 47 code and primary language tag for a given locale string or language ID.
     * Returns ['bcp47' => 'en-US', 'primary' => 'en', 'lang_id' => 1]
     */
    public static function resolveActiveLocaleContext($locale = null, $languageId = null): array
    {
        $lang = null;
        if ($languageId) {
            $lang = Language::with(['bcp47Language', 'baseLanguage.bcp47Language'])->find($languageId);
        }
        if (!$lang && $locale) {
            $norm = strtolower(trim((string)$locale));
            $lang = Language::with(['bcp47Language', 'baseLanguage.bcp47Language'])
                ->where('lang_code', $norm)
                ->first();
        }
        if (!$lang && function_exists('getCurrentLanguageID')) {
            $currentId = getCurrentLanguageID();
            if ($currentId) {
                $lang = Language::with(['bcp47Language', 'baseLanguage.bcp47Language'])->find($currentId);
            }
        }

        $bcp47 = 'en-US';
        $primary = 'en';
        $resolvedLangId = 1;

        if ($lang) {
            $resolvedLangId = $lang->id;
            $code = $lang->bcp47Language?->code ?? $lang->baseLanguage?->bcp47Language?->code;
            if (!$code && !empty($lang->lang_code)) {
                $code = ReviewModerationService::normalizeLocale($lang->lang_code);
            }
            if ($code) {
                $bcp47 = ReviewModerationService::normalizeLocale($code);
            }
        }

        $parts = explode('-', $bcp47);
        $primary = strtolower($parts[0] ?? 'en');

        return [
            'bcp47'    => $bcp47,
            'primary'  => $primary,
            'lang_id'  => $resolvedLangId,
            'language' => $lang,
        ];
    }

    /**
     * Resolve the review's own source BCP47 locale and primary language.
     */
    public static function resolveReviewSourceLocale(Review $review): array
    {
        $locale = $review->source_locale ?: $review->submitted_locale;
        if (!$locale && $review->relationLoaded('translations') && $review->translations && $review->translations->isNotEmpty()) {
            $orig = $review->translations->first(fn($t) => $t->status === 'ORIGINAL') ?? $review->translations->first();
            $locale = $orig?->source_locale;
            if (!$locale && $orig?->language_id) {
                $lang = Language::with(['bcp47Language', 'baseLanguage.bcp47Language'])->find($orig->language_id);
                $locale = $lang?->bcp47Language?->code ?? $lang?->baseLanguage?->bcp47Language?->code ?? $lang?->lang_code;
            }
        }
        if (!$locale && $review->lang_id) {
            $lang = Language::with(['bcp47Language', 'baseLanguage.bcp47Language'])->find($review->lang_id);
            $locale = $lang?->bcp47Language?->code ?? $lang?->baseLanguage?->bcp47Language?->code ?? $lang?->lang_code;
        }

        $normalized = ReviewModerationService::normalizeLocale($locale ?: 'en-US');
        $parts = explode('-', $normalized);
        $primary = strtolower($parts[0] ?? 'en');

        return [
            'bcp47'   => $normalized,
            'primary' => $primary,
        ];
    }

    /**
     * Classify and prepare a collection of reviews with Tier metadata and translated content.
     */
    public static function processReviewsForDisplay(Collection $reviews, array $context): Collection
    {
        $activeBcp47 = $context['bcp47'];
        $activePrimary = $context['primary'];

        // Preload translations for active locale from DB if any
        $reviewIds = $reviews->pluck('id')->filter()->toArray();
        $translations = collect();
        if (!empty($reviewIds)) {
            $translations = ReviewTranslation::where(function ($q) use ($reviewIds) {
                    $q->whereIn('reviews_id', $reviewIds)->orWhereIn('review_id', $reviewIds);
                })
                ->where('target_locale', $activeBcp47)
                ->where('status', ReviewTranslation::STATUS_COMPLETED)
                ->get()
                ->keyBy(fn($t) => $t->review_id ?: $t->reviews_id);
        }

        return $reviews->map(function (Review $review) use ($activeBcp47, $activePrimary, $translations) {
            $src = self::resolveReviewSourceLocale($review);
            $review->source_bcp47 = $src['bcp47'];
            $review->source_primary = $src['primary'];

            // Retrieve original title & description
            $originalTrans = null;
            if ($review->relationLoaded('translations') && $review->translations) {
                $originalTrans = $review->translations->first(fn($t) => $t->status === 'ORIGINAL') 
                    ?? $review->translations->first();
            }
            $origTitle = $originalTrans?->title ?? $review->title ?? '';
            $origText = $originalTrans?->description ?? $review->review ?? $review->description ?? '';

            $review->original_title = $origTitle;
            $review->original_text = $origText;

            // Classify Tier:
            // 1 = Same base language code of active country/region (e.g. en-US == en-US)
            // 2 = Same language combination tag, different code (e.g. en-GB vs en-US)
            // 3 = Different language (e.g. de-DE, es-ES vs en-US)
            if (strcasecmp($src['bcp47'], $activeBcp47) === 0) {
                $review->tier = 1;
                $review->display_title = $origTitle;
                $review->display_text = $origText;
                $review->is_translated = false;
                $review->can_toggle_original = false;
            } elseif (strcasecmp($src['primary'], $activePrimary) === 0) {
                $review->tier = 2;
                $review->display_title = $origTitle;
                $review->display_text = $origText;
                $review->is_translated = false;
                $review->can_toggle_original = false;
            } else {
                $review->tier = 3;
                $targetTrans = $translations->get($review->id);
                // Also check eager-loaded translations collection on the review model
                if (!$targetTrans && $review->relationLoaded('translations') && $review->translations) {
                    $targetTrans = $review->translations->first(function ($t) use ($activeBcp47) {
                        return strcasecmp($t->target_locale ?? '', $activeBcp47) === 0 
                            && $t->status === ReviewTranslation::STATUS_COMPLETED;
                    });
                }

                if ($targetTrans && (!empty($targetTrans->translated_text) || !empty($targetTrans->description))) {
                    $review->display_title = $targetTrans->translated_title ?: ($targetTrans->title ?: $origTitle);
                    $review->display_text = $targetTrans->translated_text ?: $targetTrans->description;
                    $review->is_translated = true;
                    $review->can_toggle_original = true;
                } else {
                    // Fallback to original if translation is still waiting or not ready
                    $review->display_title = $origTitle;
                    $review->display_text = $origText;
                    $review->is_translated = false;
                    $review->can_toggle_original = false;
                }
            }

            return $review;
        });
    }

    /**
     * Get sorted and tiered reviews for frontend review boxes.
     * Orders: Tier 1 -> Tier 2 -> Tier 3 (preserving the specified sort order within each tier).
     */
    public static function getTieredReviews(Collection $reviews, $locale = null, $languageId = null, $sort = 'most_recent'): Collection
    {
        $context = self::resolveActiveLocaleContext($locale, $languageId);
        $processed = self::processReviewsForDisplay($reviews, $context);

        // Sort callback within each tier
        $sortFn = function ($a, $b) use ($sort) {
            switch ($sort) {
                case 'highest_rating':
                    if ($a->rating != $b->rating) {
                        return $b->rating <=> $a->rating;
                    }
                    return $b->created_at <=> $a->created_at;
                case 'lowest_rating':
                    if ($a->rating != $b->rating) {
                        return $a->rating <=> $b->rating;
                    }
                    return $b->created_at <=> $a->created_at;
                case 'oldest':
                    return $a->created_at <=> $b->created_at;
                case 'most_recent':
                default:
                    return $b->created_at <=> $a->created_at;
            }
        };

        $tier1 = $processed->filter(fn($r) => $r->tier === 1)->sort($sortFn)->values();
        $tier2 = $processed->filter(fn($r) => $r->tier === 2)->sort($sortFn)->values();
        $tier3 = $processed->filter(fn($r) => $r->tier === 3)->sort($sortFn)->values();

        return $tier1->concat($tier2)->concat($tier3);
    }

    /**
     * Get Highlighted Reviews following the 3-Tier cascade:
     * 1. Always first show reviews in base language of active country/region
     * 2. If not enough reviews, then show same language, different code
     * 3. If not enough reviews, then show all other reviews translated (without "View original" link).
     */
    public static function getHighlightedReviews(Collection $reviews, $locale = null, $languageId = null, int $limit = 2): Collection
    {
        $context = self::resolveActiveLocaleContext($locale, $languageId);
        $processed = self::processReviewsForDisplay($reviews, $context);

        // Highlighted reviews prefer highest rated, then most recent
        $highlightSort = function ($a, $b) {
            if ($a->rating != $b->rating) {
                return $b->rating <=> $a->rating;
            }
            return $b->created_at <=> $a->created_at;
        };

        $tier1 = $processed->filter(fn($r) => $r->tier === 1)->sort($highlightSort)->values();
        $tier2 = $processed->filter(fn($r) => $r->tier === 2)->sort($highlightSort)->values();
        $tier3 = $processed->filter(fn($r) => $r->tier === 3)->sort($highlightSort)->values();

        $selected = collect();

        // 1. Take Tier 1
        foreach ($tier1 as $r) {
            if ($selected->count() >= $limit) break;
            $selected->push($r);
        }

        // 2. If not enough, take Tier 2
        if ($selected->count() < $limit) {
            foreach ($tier2 as $r) {
                if ($selected->count() >= $limit) break;
                $selected->push($r);
            }
        }

        // 3. If not enough, take Tier 3
        if ($selected->count() < $limit) {
            foreach ($tier3 as $r) {
                if ($selected->count() >= $limit) break;
                // In highlighted reviews, users should NOT see the "View original" link
                $r->can_toggle_original = false;
                $selected->push($r);
            }
        }

        return $selected;
    }
    /**
     * Paginate a collection of reviews preserving tiered order.
     */
    public static function paginateCollection(Collection $items, int $perPage = 5, ?int $page = null, array $options = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
        $total = $items->count();
        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            $options ?: ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }
}
