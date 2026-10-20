<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\CategoryProduct;
use App\Models\CategoryTranslation;
use App\Models\FeatureTransalte;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\TopProductContent;
use App\Models\ProConsTranslation;
use App\Models\ProductFeature;
use App\Models\ProductFeatureTranslate;
use App\Models\ProCons;
use App\Models\ProductPrice;
use App\Models\ProductTranslation;
use App\Models\Review;
use App\Models\WebSetting;
use App\Models\Wishlist;
use App\Models\FeatureBusinessReview;// Key Feature Review
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function productDetail($locale, $slug, Request $request)
    {
        $matchedLang = \App\Models\Language::where('lang_code', $locale)->first();
        $lang_id = $matchedLang ? $matchedLang->id : getCurrentLanguageID();
        $user = auth()->user();

        $business = Business::whereHas('translations', function ($q) use ($slug, $lang_id) {
            $q->where('slug', $slug)->where('lang_id', $lang_id);
        })->orWhereHas('translations', function ($q) use ($slug) {
            $q->where('slug', $slug);
        })->with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'reviews' => fn($q) => $q->with('translations')
                ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
                ->whereIn('status', ['published', 'active']),
            'products' => fn($q) => $q->with([
                'prices',
                'pricingOptions.translations' => fn($q) => $q->where('lang_id', $lang_id),
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
            ]) ->where(function ($query) {
                $query->where('active_all_countries', 1)
                      ->orWhere(function ($q) {
                          $q->where('active_all_countries', 0)
                            ->whereHas('countries', function ($countryQuery) {
                                $countryQuery->where('country_id', getCurrentCountry());
                            });
                      });
            }),
            'category.translation' => fn($q) => $q->where('lang_id', $lang_id),
            'category.parent.translation' => fn($q) => $q->where('lang_id', $lang_id),
            'category.Topics.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'pricingOptions.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'features.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'websites' => fn($q) => $q->where('country_id', getCurrentCountry()),
            'faqs' => function ($query) use ($lang_id) {
                    $query->where('status', 1)
                        ->orderBy('position', 'asc')
                        ->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)]);
                },
            'usps',
        ])->firstOrFail();

        if ($business->translations->isEmpty()) {
            $business->load(['translations' => fn($q) => $q->where('lang_id', 1)->orWhereRaw('1=1')]);
        }
        if ($business->category && !$business->category->translation) {
            $business->category->load(['translation' => fn($q) => $q->where('lang_id', 1)->orWhereRaw('1=1')]);
        }

        $languages = Language::all();
        // 10-Review Threshold Rule:
        // As long as a business has < 10 approved reviews, show admin_rating (and no detailed rating).
        // As soon as review #10 is approved, show user rating and user pros & cons.
        $approvedReviewsCount = $business->reviews->whereIn('status', ['published', 'active'])->count();
        $hasUserRatings = $approvedReviewsCount >= 10;
        $hasUserReviews = $approvedReviewsCount > 0;
        if ($hasUserRatings) {
            $averageRating = round($business->reviews->whereIn('status', ['published', 'active'])->avg('rating'), 1);
        } elseif ($business->admin_rating !== null && (float)$business->admin_rating > 0) {
            $averageRating = (float)$business->admin_rating;
        } else {
            $averageRating = 0;
        }

        // Inactive review message (if applicable)
        $userInactiveReviewMessage = null;
        if ($user) {
            $userReview = Review::where('business_id', $business->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending_review', 'rejected', 'removed', 'inactive'])
                ->first();

            if ($userReview) {
                $userInactiveReviewMessage = 'Your review for this product has been deactivated by the admin.';
            }
        }

        // Filter: stars (can be array or comma-separated string)
        $selectedStars = $request->get('stars');
        if (is_string($selectedStars)) {
            $selectedStars = explode(',', $selectedStars ?? '');
        }
        $selectedStars = array_filter(array_map('intval', (array) $selectedStars));

        $sort = $request->get('sort', 'recent');

        $applyFiltersAndSort = function ($query) use ($selectedStars, $sort, $lang_id) {
            $query->whereIn('status', ['published', 'active']);
            
            if (!empty($selectedStars)) {
                $query->where(function($q) use ($selectedStars) {
                    foreach ($selectedStars as $star) {
                        $min = $star - 0.5;
                        $max = $star + 0.5;
                        $q->orWhere(function($sub) use ($min, $max) {
                            $sub->where('rating', '>=', $min)
                                ->where('rating', '<', $max);
                        });
                    }
                });
            }
            
            switch ($sort) {
                case 'best':
                case 'high-to-low':
                    $query->orderByDesc('rating')->orderByDesc('created_at');
                    break;
                case 'low-to-high':
                    $query->orderBy('rating')->orderByDesc('created_at');
                    break;
                case 'recent':
                default:
                    $query->orderByDesc('created_at');
                    break;
            }
            return $query;
        };

        // Build reviews
        $reviewsQuery = Review::with([
    'user',
    'selectedProCons',
    'translations' => fn($q) => $q->where('language_id', $lang_id)
])->where('business_id', $business->id);

$reviews = $applyFiltersAndSort($reviewsQuery)->get();

        $topReviews = Review::with([
            'user',
            'business',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $allReviewsQuery = Review::with([
            'user',
            'ratings.criteria',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id));
        $allReviews = $applyFiltersAndSort($allReviewsQuery)->take(10)->get();

        $ourReviewsQuery = Review::with([
            'user',
            'ratings.criteria',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->where('user_id', auth()->id())
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id));
        $ourReviews = $applyFiltersAndSort($ourReviewsQuery)->take(10)->get();

        $trustpilotReviewsQuery = Review::with([
            'user',
            'business',
            'ratings.criteria',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id));
        $trustpilotReviews = $applyFiltersAndSort($trustpilotReviewsQuery)->take(10)->get();

        $ratingCount = $business->reviews->whereIn('status', ['published', 'active'])->count();

        $default_image = WebSetting::where('key','user_default_image')->value('value');

        $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : (\App\Models\Category::whereNull('parent_id')->orderBy('id')->first()?->getEffectiveRatingCriteria() ?? collect());

        if ($request->ajax()) {
            return view('User.product.partials.reviews_list', compact(
                'business',
                'allReviews',
                'ourReviews',
                'trustpilotReviews',
                'default_image',
                'averageRating',
                'ratingCount',
                'criteria',
                'hasUserRatings'
            ))->render();
        }

        $startingPrice = null;
        $currency = '$';
        $timeUnit = 'Month';
        $additional_info= 'NA';

        $price = getBusinessesWithStartingPrice($business);
        if (!empty($price) && isset($price[0]['starting_price'])) {
            $businessprice = $price[0]['starting_price'];
            $startingPrice = $businessprice['amount'];
            $currency = $businessprice['currency'] ?? '$';
            $timeUnit = ucfirst($businessprice['time_unit'] ?? 'month');
            $additional_info=$businessprice['additional_info'] ?? 'NA';
        }
        // dd($business);
        // Get paginated reviews
        $reviews = $reviewsQuery->paginate(10)->appends($request->query());
        // Calculate rating breakdown
        $ratingCounts = $business->reviews
            ->whereIn('status', ['published', 'active'])
            ->groupBy(function ($review) {
                return (int) round($review->rating);
            })
            ->map(fn($group) => $group->count());

        // Total active reviews
        $totalReviews = $business->reviews->whereIn('status', ['published', 'active'])->count();

        // Ensure all 1–5 stars are represented

        $userRatingCounts = Review::where('business_id', $business->id)
            ->selectRaw('ROUND(rating) as rounded_rating, COUNT(DISTINCT user_id) as user_count')
            ->groupBy('rounded_rating')
            ->pluck('user_count', 'rounded_rating');

        $ratingBreakdown = collect(range(1, 5))->mapWithKeys(function ($i) use ($userRatingCounts) {
            return [$i => $userRatingCounts->get($i, 0)];
        });

        // dd($ratingBreakdown);
        $parentCatId = $business->category ? $business->category->parent_id : null;
        $bizCategoryIds = array_filter(array_unique(array_merge(
            [$business->category_id],
            $parentCatId ? [$parentCatId] : [],
            $business->subCategories ? $business->subCategories->pluck('id')->toArray() : []
        )));

        $alternativeBusiness = !empty($bizCategoryIds)
            ? Business::where(function ($q) use ($bizCategoryIds) {
                    $q->whereIn('category_id', $bizCategoryIds)
                      ->orWhereHas('subCategories', function ($subQ) use ($bizCategoryIds) {
                          $subQ->whereIn('categories.id', $bizCategoryIds);
                      });
                })
                ->where('id', '!=', $business->id)
                ->where('is_affiliate', 1)
                ->where('status', 1)
                ->where(function ($query) {
                    $query->where('active_all_countries', 1)
                          ->orWhereHas('countries', function ($q) {
                              $q->where('country_id', getCurrentCountry());
                          });
                })
                ->whereHas('languages', function ($query) use ($lang_id) {
                    $query->where('language_id', $lang_id);
                })
                ->with([
                    'translations' => fn($q) => $q->where('lang_id', $lang_id),
                    'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
                    'websites' => fn($q) => $q->where('country_id', getCurrentCountry()),
                ])
                ->withCount([
                    'reviews as average_rating' => function ($query) {
                        $query->select(DB::raw('coalesce(avg(rating),0)'));
                    }
                ])
                ->orderByDesc('average_rating')
                ->limit(3)
                ->get()
            : collect();

        $peerComparisons = !empty($bizCategoryIds)
            ? Business::where(function ($q) use ($bizCategoryIds) {
                    $q->whereIn('category_id', $bizCategoryIds)
                      ->orWhereHas('subCategories', function ($subQ) use ($bizCategoryIds) {
                          $subQ->whereIn('categories.id', $bizCategoryIds);
                      });
                })
                ->where('id', '!=', $business->id)
                ->where('status', 1)
                ->where(function ($query) {
                    $query->where('active_all_countries', 1)
                          ->orWhereHas('countries', function ($q) {
                              $q->where('country_id', getCurrentCountry());
                          });
                })
                ->whereHas('languages', function ($query) use ($lang_id) {
                    $query->where('language_id', $lang_id);
                })
                ->with([
                    'translations' => fn($q) => $q->where('lang_id', $lang_id),
                    'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
                ])
                ->withCount([
                    'reviews as average_rating' => function ($query) {
                        $query->select(DB::raw('coalesce(avg(rating),0)'));
                    }
                ])
                ->limit(4)
                ->get()
            : collect();
        $link = $business->websites->first()->website_url ?? $business->affiliate_link ?? $business->permanent_url ?? '#';
        $reviews = Review::where('business_id', $business->id)->get();

        $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : (\App\Models\Category::whereNull('parent_id')->orderBy('id')->first()?->getEffectiveRatingCriteria() ?? collect());
        $activeReviews = $reviews->whereIn('status', ['published', 'active']);
        $activeReviewIds = $activeReviews->pluck('id')->toArray();

        foreach ($criteria as $criterion) {
            $totalScore = 0;
            $count = 0;

            $matchingCriteriaIds = \App\Models\CategoryRatingCriteria::where('id', $criterion->id)
                ->orWhere(function ($q) use ($criterion) {
                    if ($criterion->default_key) {
                        $q->where('default_key', $criterion->default_key);
                    }
                    $q->orWhereRaw('LOWER(name) = ?', [strtolower($criterion->name)]);
                })
                ->pluck('id')
                ->toArray();

            $ratingRecords = \App\Models\ReviewRating::whereIn('review_id', $activeReviewIds)
                ->whereIn('criteria_id', $matchingCriteriaIds)
                ->get();

            if ($ratingRecords->isNotEmpty()) {
                $totalScore = $ratingRecords->sum('rating');
                $count = $ratingRecords->count();
            } else {
                foreach ($activeReviews as $review) {
                    $legacyVal = null;
                    $cName = strtolower($criterion->name);
                    if ($cName === 'ease of use') {
                        $legacyVal = $review->ease_of_use_rating;
                    } elseif ($cName === 'customer service') {
                        $legacyVal = $review->customer_service_rating;
                    } elseif ($cName === 'features') {
                        $legacyVal = $review->exclusive_service_rating;
                    } elseif ($cName === 'value for money') {
                        $legacyVal = $review->value_for_money_rating;
                    }
                    if (!is_null($legacyVal) && $legacyVal > 0) {
                        $totalScore += $legacyVal;
                        $count++;
                    }
                }
            }
            $criterion->average_rating = $count > 0 ? round($totalScore / $count, 1) : 0;
        }

        $activeReviewsCount = $reviews->whereIn('status', ['published', 'active'])->count();
        if ($activeReviewsCount > 0) {
            $recommendCount = $reviews->whereIn('status', ['published', 'active'])->where('recommend', 1)->count();
            $recommendPercent = round(($recommendCount / $activeReviewsCount) * 100);
        } else {
            $recommendPercent = 0;
        }

        $currentLangId = getCurrentLanguageID();
        $resolvedRatingTemplates = \App\Services\ContentTemplateService::resolveBusinessRatingTexts($business, $currentLangId);
        $ratingTextsRecords = \App\Models\BusinessRatingText::where('business_id', $business->id)->get();
        $ratingTexts = $resolvedRatingTemplates;
        foreach ($ratingTextsRecords as $rTxt) {
            if (!empty($rTxt->intro_text) || !empty($rTxt->end_text)) {
                $ratingTexts[$rTxt->criteria_key] = [
                    'intro_text'    => $rTxt->intro_text ?: ($ratingTexts[$rTxt->criteria_key]['intro_text'] ?? ''),
                    'end_text'      => $rTxt->end_text ?: ($ratingTexts[$rTxt->criteria_key]['end_text'] ?? ''),
                    'under_10_text' => $ratingTexts[$rTxt->criteria_key]['under_10_text'] ?? '',
                ];
            }
        }
        $featuresUnder10Text = $ratingTexts['features']['under_10_text'] ?? \App\Services\ContentTemplateService::resolveCriteriaUnder10Text($business, 1, $currentLangId);        // Aggregate User Selected Pros and Cons for this business from active reviews
        $aggregatedProCons = \Illuminate\Support\Facades\DB::table('review_pro_cons')
            ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
            ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
            ->where('reviews.business_id', $business->id)
            ->whereIn('reviews.status', ['published', 'active'])
            ->select('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text', 'category_pro_cons.description', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as review_count'))
            ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text', 'category_pro_cons.description')
            ->orderByDesc('review_count')
            ->get();

        $aggregatedPros = $aggregatedProCons->where('type', 'pro')->values();
        $aggregatedCons = $aggregatedProCons->where('type', 'con')->values();

        // 10-Review Threshold Rule:
        // As long as business has < 10 reviews, show ONLY uploaded/selected pros & cons from business_pro_cons ordered by weight DESC (importance)
        if ($totalReviews < 10) {
            $business->loadMissing(['proCons.categoryProCon']);
            $aggregatedPros = $business->proCons->where('type', 'pro')
                ->sortByDesc(fn($item) => $item->weight ?? 5)
                ->map(function($item) {
                    return (object)[
                        'id'          => $item->id,
                        'type'        => 'pro',
                        'text'        => $item->text,
                        'description' => !empty($item->description) ? $item->description : optional($item->categoryProCon)->description,
                        'weight'      => $item->weight ?? 5,
                    ];
                })->values();

            $aggregatedCons = $business->proCons->where('type', 'con')
                ->sortByDesc(fn($item) => $item->weight ?? 5)
                ->map(function($item) {
                    return (object)[
                        'id'          => $item->id,
                        'type'        => 'con',
                        'text'        => $item->text,
                        'description' => !empty($item->description) ? $item->description : optional($item->categoryProCon)->description,
                        'weight'      => $item->weight ?? 5,
                    ];
                })->values();
        } else {
            // When >= 10 reviews, show user-aggregated pros and cons
            if ($aggregatedProCons->isEmpty() && $business->proCons->isNotEmpty()) {
                $business->loadMissing(['proCons.categoryProCon']);
                $aggregatedPros = $business->proCons->where('type', 'pro')
                    ->sortByDesc(fn($item) => $item->weight ?? 5)
                    ->map(function($item) {
                        return (object)[
                            'id'           => $item->id,
                            'type'         => 'pro',
                            'text'         => $item->text,
                            'description'  => !empty($item->description) ? $item->description : optional($item->categoryProCon)->description,
                            'weight'       => $item->weight ?? 5,
                            'review_count' => 1,
                        ];
                    })->values();

                $aggregatedCons = $business->proCons->where('type', 'con')
                    ->sortByDesc(fn($item) => $item->weight ?? 5)
                    ->map(function($item) {
                        return (object)[
                            'id'           => $item->id,
                            'type'         => 'con',
                            'text'         => $item->text,
                            'description'  => !empty($item->description) ? $item->description : optional($item->categoryProCon)->description,
                            'weight'       => $item->weight ?? 5,
                            'review_count' => 1,
                        ];
                    })->values();
            }
        }

        $default_image=WebSetting::where('key','user_default_image')->value('value');
        return view('User.product.product_detail', compact(
            'business','alternativeBusiness','additional_info','link','default_image',
            'ratingCounts',
            'totalReviews',
            'ratingBreakdown',
            'startingPrice',
            'currency',
            'timeUnit',
            'averageRating',
            'hasUserReviews',
            'hasUserRatings',
            'reviews',
            'userInactiveReviewMessage',
            'languages',
            'topReviews',
            'allReviews',
            'ourReviews',
            'trustpilotReviews',
            'criteria',
            'recommendPercent',
            'peerComparisons',
            'ratingTexts',
            'featuresUnder10Text',
            'aggregatedPros',
            'aggregatedCons'
        ));
    }

    public function topRatedFaq($lang)
    {
        $lang_id = getCurrentLanguageID();
        
        $faqs = [];
        $content = \App\Models\TopProductContent::where('meta_key', 'top_rated_faqs')
            ->where('lang_id', $lang_id)
            ->first();

        if ($content && !empty($content->meta_value)) {
            $decoded = json_decode($content->meta_value, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $faqs = $decoded;
            }
        }

        if (empty($faqs) && $lang_id != 1) {
            $enContent = \App\Models\TopProductContent::where('meta_key', 'top_rated_faqs')
                ->where('lang_id', 1)
                ->first();
            if ($enContent && !empty($enContent->meta_value)) {
                $decoded = json_decode($enContent->meta_value, true);
                if (is_array($decoded) && count($decoded) > 0) {
                    $faqs = $decoded;
                }
            }
        }

        if (empty($faqs)) {
            $faqs = [
                [
                    'category' => 'Rankings & Calculation',
                    'question' => 'How are top-rated products and businesses chosen?',
                    'answer' => 'Top-rated listings are determined by verified community ratings, authentic review scores, user satisfaction metrics, and overall reliability across each industry category.'
                ],
                [
                    'category' => 'Rankings & Calculation',
                    'question' => 'How often are the top-rated rankings updated?',
                    'answer' => 'Our rankings are updated continuously as new community reviews, verified feedback, and rating submissions are received.'
                ],
                [
                    'category' => 'Rankings & Calculation',
                    'question' => 'Can businesses pay to be featured as top-rated?',
                    'answer' => 'No. Placement in top-rated rankings cannot be bought. Rankings strictly reflect actual community ratings and verified review performance.'
                ],
                [
                    'category' => 'Reviews & Community',
                    'question' => 'How can I submit a review for a business?',
                    'answer' => 'Simply search for the business or visit its page on Localio, click "Write a review", rate the criteria, and share your experience.'
                ],
                [
                    'category' => 'Reviews & Community',
                    'question' => 'What makes Localio ratings different from other review sites?',
                    'answer' => 'Localio relies on genuine community feedback with strict moderation to eliminate fake reviews, ensuring honest ratings from real users.'
                ],
                [
                    'category' => 'General & Availability',
                    'question' => 'Are all listed products available in my region?',
                    'answer' => 'Most top-rated products and businesses provide regional availability indicators and localized support information on their detail pages.'
                ]
            ];
        }

        $groupedFaqs = [];
        $faqCategories = [];
        foreach ($faqs as $f) {
            $cat = !empty($f['category']) ? trim($f['category']) : 'General';
            if (!in_array($cat, $faqCategories)) {
                $faqCategories[] = $cat;
            }
            $groupedFaqs[$cat][] = $f;
        }

        $recentReviews = \App\Models\Review::with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $lang_id),
            'business.translations' => fn($q) => $q->where('lang_id', $lang_id)
        ])
            ->whereIn('status', ['published', 'active'])
            ->orderByDesc('created_at')
            ->take(2)
            ->get();

                $topProductContents = \App\Models\TopProductContent::where('lang_id', $lang_id)
            ->pluck('meta_value', 'meta_key')
            ->toArray();

return view('User.product.top_rated_faqs', compact('topProductContents', 'faqs', 'faqCategories', 'groupedFaqs', 'recentReviews', 'lang_id'));
    }

    public function topRatedDiscussions(Request $request, $locale)
    {
        $lang_id = getCurrentLanguageID();

        $topProductContents = \App\Models\TopProductContent::where('lang_id', $lang_id)
            ->pluck('meta_value', 'meta_key')
            ->toArray();

        $query = \App\Models\BusinessDiscussion::where('is_top_rated', 1)
            ->whereIn('status', ['published', 'active'])
            ->with([
                'user.country',
                'comments' => fn($q) => $q->whereNull('parent_id')
                    ->orderByDesc('is_pinned')
                    ->orderBy('created_at', 'asc')
                    ->with(['user', 'replies.user', 'replies.replies.user'])
            ]);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('is_pinned');

        $sort = $request->get('sort', 'popular');
        if ($sort === 'recent') {
            $query->latest();
        } elseif ($sort === 'commented') {
            $query->orderByDesc('comments_count')->latest();
        } else {
            $query->orderByRaw('(upvotes_count - downvotes_count) DESC')->latest();
        }

        $totalDiscussionsCount = (clone $query)->count();
        $discussions = $query->paginate(10);

        $discIds = \App\Models\BusinessDiscussion::where('is_top_rated', 1)->whereIn('status', ['published', 'active'])->pluck('id');
        $totalComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->count();
        $activeTodayDiscs = \App\Models\BusinessDiscussion::where('is_top_rated', 1)->whereIn('status', ['published', 'active'])->where('updated_at', '>=', now()->startOfDay())->count();
        $activeTodayComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->where('created_at', '>=', now()->startOfDay())->count();
        $totalActiveToday = $activeTodayDiscs + $activeTodayComments;

        return view('User.product.top_rated_discussions', compact(
            'topProductContents',
            'discussions',
            'totalDiscussionsCount',
            'totalComments',
            'totalActiveToday',
            'sort',
            'lang_id'
        ));
    }

    public function topRatedProduct($lang, $category = null, $page = null)
    {
        if (is_numeric($category)) {
            $page = (int)$category;
            $category = null;
        }
        return view('User.product.top_rated_product', compact('category', 'page'));
    }
    public function ExclusiveBusinessDeals(){
        return view('User.product.exclusive_business_deals');
    }
    public function productComparison()
    {
        $lang_id=getCurrentLanguageID();
        $comparedProductIds = session()->get('compared_products', []);
        $businesses = Business::with([
            'category.parent.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'category.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'category.ratingCriteria',
            'products.prices',
            'usps',
            'proCons',
            'translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'reviews' => function ($query) {
                $query->whereIn('status', ['published', 'active']);
            },
            'reviews.ratings.criteria',
            'reviews.selectedProCons',
            'reviews.user',
            'reviews.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'features.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'offerProducts.plans.planFeatures',
        ])
        ->whereIn('id', $comparedProductIds)
        ->get();
            // dd($businesses);
        $firstBiz = $businesses->first();
        $catId = $firstBiz ? $firstBiz->category_id : null;
        $availableCategoryBusinesses = collect();
        if ($catId) {
            $catObj = \App\Models\Category::find($catId);
            $catIds = [$catId];
            if ($catObj) {
                if ($catObj->parent_id === null) {
                    $childIds = \App\Models\Category::where('parent_id', $catObj->id)->pluck('id')->toArray();
                    $catIds = array_merge($catIds, $childIds);
                } else {
                    $catIds[] = $catObj->parent_id;
                    $siblingIds = \App\Models\Category::where('parent_id', $catObj->parent_id)->pluck('id')->toArray();
                    $catIds = array_merge($catIds, $siblingIds);
                }
            }
            $catIds = array_unique(array_filter($catIds));

            $availableCategoryBusinesses = Business::where(function ($q) use ($catIds) {
                    $q->whereIn('category_id', $catIds)
                      ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                          $subQ->whereIn('categories.id', $catIds);
                      });
                })
                ->where('status', 1)
                ->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)])
                ->get()
                ->map(function($b) use ($lang_id) {
                    $t = $b->translations->firstWhere('lang_id', $lang_id) ?? $b->translations->first();
                    return [
                        'id' => $b->id,
                        'name' => $t->name ?? $b->name ?? $b->slug,
                        'slug' => $t->slug ?? $b->slug,
                        'icon' => $b->getRawOriginal('icon_id') ?? $b->icon_id ?? null,
                    ];
                })
                ->values();
        }

        return view('User.product.product_comparison', compact('businesses', 'availableCategoryBusinesses'));
    }

    public function productComparisonSeo($locale = null, $comparison_slug = null, $comparison_businesses = null)
    {
        // If route is group prefixed with '{locale?}', the first param is $locale.
        // We only really need $comparison_slug and $comparison_businesses.
        if (!$comparison_businesses) {
            $comparison_businesses = $comparison_slug;
            $comparison_slug = $locale;
        }
        
        $lang_id = getCurrentLanguageID();
        
        // Find the category based on comparison_slug to validate
        $categoryTranslation = \App\Models\CategoryTranslation::where('comparison_slug', $comparison_slug)
            ->where('lang_id', $lang_id)
            ->first();
            
        if (!$categoryTranslation) {
            return app(\App\Http\Controllers\User\ViewController::class)->handleBusinessSubPage(request(), $locale, $comparison_slug, $comparison_businesses);
        }

        // The "vs" keyword is defined per country/language in site content
        $vs_keyword = static_text('vs_keyword');
        if (empty($vs_keyword) || $vs_keyword === 'vs_keyword') {
            $vs_keyword = 'vs';
        }
        $vs_keyword = \Illuminate\Support\Str::slug($vs_keyword);
        
        $separator = "-{$vs_keyword}-";
        
        if (strpos($comparison_businesses, $separator) !== false) {
            $slugs = explode($separator, $comparison_businesses);
        } else {
            $slugs = preg_split('/-vs-|-versus-/i', $comparison_businesses);
            if (count($slugs) < 2) {
                $slugs = explode('-vs-', $comparison_businesses);
            }
        }
        
        // Support comparing from 2 up to 4 businesses
        if (count($slugs) < 2 || count($slugs) > 4) {
            return app(\App\Http\Controllers\User\ViewController::class)->handleBusinessSubPage(request(), $locale, $comparison_slug, $comparison_businesses);
        }
        
        $businesses = Business::with([
            'category.ratingCriteria',
            'category.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'products.prices',
            'usps',
            'proCons',
            'translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'reviews' => function ($query) {
                $query->whereIn('status', ['published', 'active']);
            },
            'reviews.ratings.criteria',
            'reviews.selectedProCons',
            'reviews.user',
            'reviews.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'features.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'offerProducts.plans.planFeatures',
        ])
        ->whereHas('translations', function($query) use ($slugs) {
            $query->whereIn('slug', $slugs);
        })
        ->get();
        
        // Ensure businesses are ordered in the same way they were requested
        $orderedBusinesses = collect();
        foreach ($slugs as $slug) {
            $business = $businesses->first(function($b) use ($slug) {
                return $b->translations->first() && $b->translations->first()->slug === $slug;
            });
            if ($business) {
                $orderedBusinesses->push($business);
            }
        }
        $businesses = $orderedBusinesses;

        // We deliberately do not store compare session on SEO comparison pages so compare-bar doesn't leak to category/top-rated pages

        $firstBiz = $businesses->first();
        $catId = $firstBiz ? $firstBiz->category_id : ($categoryTranslation ? $categoryTranslation->category_id : null);
        $availableCategoryBusinesses = collect();
        if ($catId) {
            $catObj = \App\Models\Category::find($catId);
            $catIds = [$catId];
            if ($catObj) {
                if ($catObj->parent_id === null) {
                    $childIds = \App\Models\Category::where('parent_id', $catObj->id)->pluck('id')->toArray();
                    $catIds = array_merge($catIds, $childIds);
                } else {
                    $catIds[] = $catObj->parent_id;
                    $siblingIds = \App\Models\Category::where('parent_id', $catObj->parent_id)->pluck('id')->toArray();
                    $catIds = array_merge($catIds, $siblingIds);
                }
            }
            $catIds = array_unique(array_filter($catIds));

            $availableCategoryBusinesses = Business::where(function ($q) use ($catIds) {
                    $q->whereIn('category_id', $catIds)
                      ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                          $subQ->whereIn('categories.id', $catIds);
                      });
                })
                ->where('status', 1)
                ->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)])
                ->get()
                ->map(function($b) use ($lang_id) {
                    $t = $b->translations->firstWhere('lang_id', $lang_id) ?? $b->translations->first();
                    return [
                        'id' => $b->id,
                        'name' => $t->name ?? $b->name ?? $b->slug,
                        'slug' => $t->slug ?? $b->slug,
                        'icon' => $b->getRawOriginal('icon_id') ?? $b->icon_id ?? null,
                    ];
                })
                ->values();
        }

        return view('User.product.product_comparison', compact('businesses', 'availableCategoryBusinesses', 'comparison_slug', 'categoryTranslation'));
    }
    protected function getComparisonRedirectUrl(array $productIds, $compSlug = null)
    {
        $locale = app()->getLocale();
        $count = count($productIds);

        if ($count >= 2 && $count <= 4) {
            $lang_id = getCurrentLanguageID();
            $businesses = Business::with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id)
            ])
            ->whereIn('id', $productIds)
            ->get()
            ->sortBy(fn($m) => array_search($m->id, $productIds))
            ->values();

            if ($businesses->count() >= 2) {
                if (empty($compSlug)) {
                    $firstWithCat = $businesses->firstWhere('category_id', '!=', null);
                    $categoryId = $firstWithCat ? $firstWithCat->category_id : $businesses->first()->category_id;
                    $catTrans = $categoryId ? \App\Models\CategoryTranslation::where('category_id', $categoryId)
                        ->where('lang_id', $lang_id)
                        ->first() : null;
                    $compSlug = $catTrans ? $catTrans->comparison_slug : null;
                }

                $allHaveSlugs = $businesses->every(function($b) use ($lang_id) {
                    return !empty($b->translations->firstWhere('lang_id', $lang_id)?->slug ?? $b->translations->first()?->slug);
                });

                if (!empty($compSlug) && $allHaveSlugs) {
                    $vs_keyword = static_text('vs_keyword');
                    if (empty($vs_keyword) || $vs_keyword === 'vs_keyword') {
                        $vs_keyword = 'vs';
                    }
                    $vs_keyword = \Illuminate\Support\Str::slug($vs_keyword);

                    $slugs = $businesses->map(function($b) use ($lang_id) {
                        return $b->translations->firstWhere('lang_id', $lang_id)?->slug ?? $b->translations->first()?->slug;
                    })->toArray();

                    $compBusinesses = implode("-{$vs_keyword}-", $slugs);

                    return route('product-comparison.seo', [
                        'locale' => $locale,
                        'comparison_slug' => $compSlug,
                        'comparison_businesses' => $compBusinesses
                    ]);
                }
            }
        }

        // When count is 1 or fewer, persist remaining in session so product-comparison page displays them
        if (!empty($productIds)) {
            session()->put('compared_products', $productIds);
        } else {
            session()->forget('compared_products');
        }

        return route('product-comparison', $locale);
    }

    public function addToComparison(Request $request, $locale, $productId = null)
    {
        $productId = (int) ($productId ?: $request->input('productId'));
        $currentIds = $request->input('currentIds');

        if (is_array($currentIds) && !empty($currentIds)) {
            $comparedProducts = array_values(array_unique(array_map('intval', $currentIds)));
        } else {
            $comparedProducts = session()->get('compared_products', []);
            if (!is_array($comparedProducts)) {
                $comparedProducts = [];
            }
        }

        if (!in_array($productId, $comparedProducts)) {
            if (count($comparedProducts) >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => static_text('error_compare_max_limit') ?: 'You can compare a maximum of 4 products.'
                ], 422);
            }
            $comparedProducts[] = $productId;
            $comparedProducts = array_values($comparedProducts);
        }

        $comparison_slug = $request->input('comparison_slug');
        $redirectUrl = $this->getComparisonRedirectUrl($comparedProducts, $comparison_slug);

        return response()->json([
            'success' => true,
            'redirect' => $redirectUrl,
            'redirect_url' => $redirectUrl
        ]);
    }

    public function removeFromComparison(Request $request, $locale, $productId = null)
    {
        $productId = (int) ($productId ?: $request->input('productId'));
        $currentIds = $request->input('currentIds');

        if (is_array($currentIds) && !empty($currentIds)) {
            $comparedProducts = array_values(array_unique(array_map('intval', $currentIds)));
        } else {
            $comparedProducts = session()->get('compared_products', []);
            if (!is_array($comparedProducts)) {
                $comparedProducts = [];
            }
        }

        // Remove only the requested product ID from the array
        $comparedProducts = array_values(array_filter($comparedProducts, function ($id) use ($productId) {
            return (int)$id !== $productId;
        }));

        $comparison_slug = $request->input('comparison_slug');
        $redirectUrl = $this->getComparisonRedirectUrl($comparedProducts, $comparison_slug);

        return response()->json([
            'success' => true,
            'redirect' => $redirectUrl,
            'redirect_url' => $redirectUrl
        ]);
    }

    public function clearComparison()
    {
        session()->forget('compared_products');
        return response()->json(['success' => true]);
    }
    public function fetchProduct(Request $request)
    {
        try {

            $locale = getCurrentLocale();
            $searchQuery = $request->searchQuery;
            $min = $request->min;
            $max = $request->max;
            $topProductContents = $this->getTopProductContents($locale);
            $files = $this->getFiles();


            // $formattedProductRelations = $this->mapProductRelations($productPriceFilter);

            if ($searchQuery) {
                // $searchResults = $this->getSearchResults($searchQuery, $siteLanguage);

                // foreach ($searchResults as $product) {
                //     $product->average_rating = $product->reviews->avg('rating') ?: 0;
                //     $product->reviews_count = $product->reviews->count();
                // }

                // $formattedProductRelations = $this->mapProductRelations($searchResults);
                // return response()->json([
                //     'products' => $searchResults,
                //     'topProductContents' => $topProductContents,
                //     'files' => $files,
                //     'formattedProductRelations' => $formattedProductRelations
                // ]);
            }

            // return response()->json([
            //     'products' => $productPriceFilter,
            //     'topProductContents' => $topProductContents,
            //     'files' => $files,
            //     'formattedProductRelations' => $formattedProductRelations
            // ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getTopProductContents($locale)
    {
        $topProductContents = TopProductContent::where([['lang_code', $locale], ['type', 'text']])
            ->pluck('meta_value', 'meta_key');

        if ($topProductContents->isEmpty()) {
            $topProductContents = TopProductContent::where([['lang_code', 'en'], ['type', 'text']])
                ->pluck('meta_value', 'meta_key');
        }

        return $topProductContents;
    }

    private function getFiles()
    {
        return TopProductContent::where([['lang_id', 1], ['type', 'file']])
            ->pluck('meta_value', 'meta_key')
            ->mapWithKeys(function ($value, $key) {
                return [$key => asset($value)];
            });
    }

    private function getProductPriceFilter($min, $max, $siteLanguage)
    {
        if ($min || $max) {
            return Product::whereBetween('product_price', [$min, $max])
                ->with([
                    'translations' => function ($query) use ($siteLanguage) {
                        $query->where('language_id', $siteLanguage->id);
                    },
                    'reviews'
                ])
                ->orderBy('product_price', 'desc')
                ->get();
        }
        return collect();
    }

    private function getSearchResults($searchQuery, $siteLanguage)
    {
        return Product::where('name', 'like', '%' . $searchQuery . '%')
            ->with([
                'translations' => function ($query) use ($siteLanguage, $searchQuery) {
                    $query->where('language_id', $siteLanguage->id)
                        ->where('name', 'like', '%' . $searchQuery . '%');
                },
                'keyFeatures.translations' => function ($query) use ($siteLanguage) {
                    $query->where('language_id', $siteLanguage->id);
                },
                'reviews'
            ])
            ->orderBy('name', 'desc')
            ->get();
    }

    private function mapProductRelations($products)
    {
        return $products->map(function ($productRelation) {
            $keyFeaturesForProduct = $productRelation->keyFeatures->map(function ($keyFeature) {
                return [
                    'feature' => $keyFeature->translations->isNotEmpty()
                        ? $keyFeature->translations->first()->feature
                        : ($keyFeature->feature ?? 'No key feature'),
                ];
            });

            return [
                'product' => $productRelation,
                'keyFeatures' => $keyFeaturesForProduct
            ];
        });
    }

    public function addToWishlist(Request $request)

    {
        $id = $request->id;
        $userId = Auth::id(); // Get the authenticated user ID

        // Check if user is authenticated
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Check if product exists
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => static_text('error_product_not_found') ?: 'Product not found'], 404);
        }

        // Check if product is already in wishlist
        $existingWishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existingWishlist) {
            return response()->json(['info' => 'Product already in wishlist'], 200);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'status' => 1 // Adding status field

        ]);

        return response()->json(['success' => 'Product added to wishlist'], 200);
    }

    public function destroyWishlist($locale, $id)
    {
        //   return response()->json(['id' => $id]);

        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userId =  Auth::user()->id;
        // return response()->json(['userId' => $userId]);
        $wishlistItem = Wishlist::where('id', $id)->where('user_id', $userId)->first();

        if (!$wishlistItem) {
            return response()->json(['error' => 'Wishlist item not found'], 404);
        }

        // Delete wishlist item
        $wishlistItem->delete();
        return response()->json(['success' => 'Item removed'], 200);
    }


    public function allBusinessComparisons($locale, $business_slug)
    {
        $lang_id = getCurrentLanguageID();

        $business = Business::whereHas('translations', function ($q) use ($business_slug, $lang_id) {
            $q->where('slug', $business_slug)->where('lang_id', $lang_id);
        })->with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.translation' => fn($q) => $q->where('lang_id', $lang_id),
            'category.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.parent.translation' => fn($q) => $q->where('lang_id', $lang_id),
            'category.parent.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
        ])->first();

        if (!$business) {
            $business = Business::whereHas('translations', function ($q) use ($business_slug) {
                $q->where('slug', $business_slug);
            })->with([
                'translations',
                'category.translation',
                'category.translations',
                'category.parent.translation',
                'category.parent.translations',
                'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
            ])->first();
        }

        if (!$business) {
            abort(404, 'Business not found');
        }

        $totalReviews = $business->reviews->whereIn('status', ['published', 'active'])->count();
        $hasUserRatings = $totalReviews >= 10;
        if ($hasUserRatings) {
            $averageRating = round($business->reviews->whereIn('status', ['published', 'active'])->avg('rating'), 1);
        } elseif ($business->admin_rating !== null && (float)$business->admin_rating > 0) {
            $averageRating = (float)$business->admin_rating;
        } else {
            $averageRating = null;
        }
        $businessRating = $averageRating ?? 0;
        
        $recommendCount = $business->reviews->whereIn('status', ['published', 'active'])->where('recommend', 1)->count();
        $recommendPercent = ($hasUserRatings && $totalReviews > 0) ? round(($recommendCount / $totalReviews) * 100) : 0;

        $reviews = Review::where('business_id', $business->id)->get();
        $activeReviews = $reviews->whereIn('status', ['published', 'active']);
        $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : (\App\Models\Category::whereNull('parent_id')->orderBy('id')->first()?->getEffectiveRatingCriteria() ?? collect());
        $activeReviewIds = $activeReviews->pluck('id')->toArray();

        foreach ($criteria as $criterion) {
            $totalScore = 0;
            $count = 0;

            $matchingCriteriaIds = \App\Models\CategoryRatingCriteria::where('id', $criterion->id)
                ->orWhere(function ($q) use ($criterion) {
                    if ($criterion->default_key) {
                        $q->where('default_key', $criterion->default_key);
                    }
                    $q->orWhereRaw('LOWER(name) = ?', [strtolower($criterion->name)]);
                })
                ->pluck('id')
                ->toArray();

            $ratingRecords = \App\Models\ReviewRating::whereIn('review_id', $activeReviewIds)
                ->whereIn('criteria_id', $matchingCriteriaIds)
                ->get();

            if ($ratingRecords->isNotEmpty()) {
                $totalScore = $ratingRecords->sum('rating');
                $count = $ratingRecords->count();
            } else {
                foreach ($activeReviews as $review) {
                    $legacyVal = null;
                    $cName = strtolower($criterion->name);
                    if ($cName === 'ease of use') {
                        $legacyVal = $review->ease_of_use_rating;
                    } elseif ($cName === 'customer service') {
                        $legacyVal = $review->customer_service_rating;
                    } elseif ($cName === 'features') {
                        $legacyVal = $review->exclusive_service_rating;
                    } elseif ($cName === 'value for money') {
                        $legacyVal = $review->value_for_money_rating;
                    }
                    if (!is_null($legacyVal) && $legacyVal > 0) {
                        $totalScore += $legacyVal;
                        $count++;
                    }
                }
            }
            $criterion->average_rating = $count > 0 ? round($totalScore / $count, 1) : 0;
        }

        $parentCat = $business->category ? ($business->category->parent ?? $business->category) : null;
        $subCatIds = $parentCat ? $parentCat->children->pluck('id')->toArray() : [];
        $parentCatId = $parentCat ? $parentCat->id : $business->category_id;
        $bizCategoryIds = array_filter(array_unique(array_merge([$business->category_id, $parentCatId], $subCatIds, $business->subCategories ? $business->subCategories->pluck('id')->toArray() : [])));

        // Base peer query
        $basePeerQuery = Business::where('id', '!=', $business->id)
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                      ->orWhereHas('countries', function ($q) {
                          $q->where('country_id', getCurrentCountry());
                      });
            })
            ->whereHas('languages', function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            });

        if (!empty($bizCategoryIds)) {
            $basePeerQuery->where(function ($q) use ($bizCategoryIds) {
                $q->whereIn('category_id', $bizCategoryIds)
                  ->orWhereHas('subCategories', function ($subQ) use ($bizCategoryIds) {
                      $subQ->whereIn('categories.id', $bizCategoryIds);
                  });
            });
        }

        // Popular comparisons (6 items)
        $popularComparisons = (clone $basePeerQuery)
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
            ])
            ->limit(6)
            ->get();

        # Subcategories that actually have comparisons for this business
        $subCategoriesWithComparisons = \App\Models\Category::whereIn('id', $bizCategoryIds)
            ->whereHas('businesses', function($q) use ($business, $lang_id) {
                $q->where('id', '!=', $business->id)
                  ->where('status', 1)
                  ->where(function ($query) {
                      $query->where('active_all_countries', 1)
                            ->orWhereHas('countries', function ($c) {
                                $c->where('country_id', getCurrentCountry());
                            });
                  })
                  ->whereHas('languages', function ($query) use ($lang_id) {
                      $query->where('language_id', $lang_id);
                  });
            })
            ->with(['translation' => fn($q) => $q->where('lang_id', $lang_id)])
            ->get();

        # Filter by selected subcategory if present in request
        $selectedSubcatSlug = request()->get('subcategory');
        $filteredPeerQuery = clone $basePeerQuery;
        if ($selectedSubcatSlug) {
            $subcatObj = \App\Models\Category::whereHas('translations', function($q) use ($selectedSubcatSlug) {
                $q->where('slug', $selectedSubcatSlug);
            })->first();
            if ($subcatObj) {
                $targetCatIds = array_filter([$subcatObj->id, $subcatObj->parent_id]);
                $filteredPeerQuery->where(function ($q) use ($targetCatIds, $subcatObj) {
                    $q->whereIn('category_id', $targetCatIds)
                      ->orWhereHas('subCategories', function ($subQ) use ($subcatObj) {
                          $subQ->where('categories.id', $subcatObj->id);
                      });
                });
            }
        }

        $peerComparisons = $filteredPeerQuery
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
            ])
            ->paginate(12)
            ->appends(request()->query());

        # All searchable businesses for live comparison search box
        $allSearchableBusinesses = Business::where('status', 1)
            ->where('id', '!=', $business->id)
            ->with(['translations'])
            ->get()
            ->map(function($b) use ($lang_id) {
                $trans = $b->translations->firstWhere('lang_id', $lang_id) ?? $b->translations->first();
                return [
                    'id' => $b->id,
                    'name' => trim($trans->name ?? $b->slug),
                    'slug' => $trans->slug ?? $b->slug,
                    'icon' => !empty($b->icon_id) ? asset($b->icon_id) : '',
                ];
            })
            ->values()
            ->toArray();

        # Recently viewed comparisons (from session with fallback to popular)
        $recentIds = session()->get('recently_viewed_biz_' . $business->id, []);
        $recentlyViewedComparisons = collect();
        if (!empty($recentIds)) {
            $recentlyViewedComparisons = Business::whereIn('id', $recentIds)
                ->where('id', '!=', $business->id)
                ->with(['translations' => fn($q) => $q->where('lang_id', $lang_id), 'reviews' => fn($q) => $q->whereIn('status', ['published', 'active'])])
                ->get();
        }
        if ($recentlyViewedComparisons->count() < 3) {
            $additional = $popularComparisons->reject(fn($item) => $recentlyViewedComparisons->pluck('id')->contains($item->id))->take(6 - $recentlyViewedComparisons->count());
            $recentlyViewedComparisons = $recentlyViewedComparisons->merge($additional);
        }

                # Explore subcategories of parent category (matching category-page)
        $parentCat = $business->category ? ($business->category->parent ?? $business->category) : null;
        $exploreSubcategories = ['title' => '', 'items' => collect()];
        if ($parentCat) {
            $parentTrans = $parentCat->translations->firstWhere('lang_id', $lang_id) ?? $parentCat->translations->first();
            $parentName = $parentTrans->name ?? $parentCat->name ?? '';
            $subcats = \App\Models\Category::where('parent_id', $parentCat->id)
                ->where('status', 1)
                ->whereHas('translations', function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
                })
                ->with([
                    'translations' => function ($q) use ($lang_id) {
                        $q->where('lang_id', $lang_id);
                    },
                    'topRatedImageMedia'
                ])
                ->withCount(['businesses' => function ($q) {
                    $q->where('status', 1);
                }])
                ->get();
            $exploreSubcategories = [
                'title' => 'Explore ' . (!empty($parentName) ? $parentName : ($business->category->translations->firstWhere('lang_id', $lang_id)->name ?? '')) . ' categories',
                'items' => $subcats
            ];
        }

        return view('User.product.all_comparisons', compact(
            'business',
            'businessRating',
            'peerComparisons',
            'popularComparisons',
            'recentlyViewedComparisons',
            'subCategoriesWithComparisons',
            'exploreSubcategories',
            'allSearchableBusinesses',
            'criteria',
            'averageRating',
            'totalReviews',
            'recommendPercent'
        ));
    }

    // Key Feature Review Controller
    public function storeFeatureReview(Request $request)
{
    // Check if user is logged in
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => static_text('error_review_login_required') ?: 'You must be logged in to submit a review.'
        ], 401);
    }

    // Validate input
    $request->validate([
        'business_id' => 'required|integer|exists:businesses,id',
        'feature_id' => 'required|integer|exists:features,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    // Create review
    $review = FeatureBusinessReview::create([
        'business_id' => $request->business_id,
        'feature_id' => $request->feature_id,
        'user_id' => Auth::id(),
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Feature review added successfully!',
        'data' => $review,
    ]);

}

}
