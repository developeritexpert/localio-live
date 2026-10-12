<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cookie;
use App;
use App\Models\{Business, BusinessDiscussion, BusinessDiscussionComment, BusinessDiscussionVote, Category, SiteLanguages, CategoryTranslation, ExpertGuideArticle, Product, WebSetting, BusinessFaq, BusinessFaqCategory, BusinessFaqFeedback, BusinessTranslation, CategoryFeatureContent, CategoryRatingCriteria, Feature, ReviewRating, ReviewReport};
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Support\Facades\Session;
use App\Models\HomeContent;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Review;
use App\Models\ExpertGuides;
use App\Models\PolicyTranslation;
use App\Models\ContactContent;
use App\Models\ExpertGuideCategory;
use App\Models\PageTile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ViewController extends Controller
{
    public function home()
    {
        $langCode = getCurrentLocale();
        $lang_id = getCurrentLanguageID();
        session(['lang_id' => $lang_id]); // Required for translationForCurrentLang to work

        try {
            $homeContents = HomeContent::where('lang_id', $lang_id)->pluck('meta_value', 'meta_key');
            if ($homeContents->isEmpty()) {
                $homeContents = HomeContent::where('lang_id', 1)->pluck('meta_value', 'meta_key');
            }

            $homepageCategories = Category::where('show_on_homepage', 1)
                ->with([
                    'translations' => fn($q) => $q->where('lang_id', $lang_id),
                    'businesses.translations' => function ($query) use ($lang_id) {
                        $query->where('lang_id', $lang_id);
                    },
                    'businesses.reviews'
                ])
                ->orderBy('homepage_order', 'asc')
                ->get();

            if ($homepageCategories->isEmpty()) {
                $categories = Category::whereHas('businesses.reviews') // fallback: only include categories with reviewed businesses
                    ->with([
                        'translations' => fn($q) => $q->where('lang_id', $lang_id),
                        'businesses.translations' => function ($query) use ($lang_id) {
                            $query->where('lang_id', $lang_id);
                        },
                        'businesses.reviews'
                    ])
                    ->get()
                    ->map(function ($category) {
                        // Combine all ratings across all businesses
                        $ratings = $category->businesses->flatMap->reviews->pluck('rating');
                        $category->average_rating = $ratings->isNotEmpty() ? $ratings->avg() : 0;
                        return $category;
                    })
                    ->sortByDesc('average_rating') // sort by calculated average
                    ->values()
                    ->take(10);
            } else {
                $categories = $homepageCategories;
            }


            $categories->each(function ($category) use ($lang_id) {
                $limit = $category->homepage_product_limit ?? 6;
                if ($limit <= 0) $limit = 6;

                $subCategoryIds = $category->subCategories ? $category->subCategories->pluck('id')->toArray() : [];

                $businesses = Business::where(function ($query) use ($category, $subCategoryIds) {
                    $query->where('category_id', $category->id);
                    if (!empty($subCategoryIds)) {
                        $query->orWhereIn('category_id', $subCategoryIds)
                            ->orWhereHas('subCategories', function ($subQ) use ($subCategoryIds) {
                                $subQ->whereIn('categories.id', $subCategoryIds);
                            });
                    }
                    $query->orWhereHas('subCategories', function ($subQ) use ($category) {
                        $subQ->where('categories.id', $category->id);
                    });
                })
                    ->where('is_affiliate', 1)
                    ->where(function ($query) {
                        $query->where('active_all_countries', 1)
                            ->orWhereHas('countries', function ($q) {
                                $q->where('country_id', getCurrentCountry());
                            });
                    })
                    ->whereHas('languages', function ($query) use ($lang_id) {
                        $query->where('language_id', $lang_id);
                    })
                    ->withAvg('reviews', 'rating')
                    ->with([
                        'translations' => fn($q) => $q->where('lang_id', $lang_id),
                        'reviews.translations' => fn($q) => $q->where('language_id', $lang_id),
                        'usps',
                    ])
                    ->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC')
                    ->take($limit)
                    ->get();

                $category->setRelation('businesses', $businesses);
            });



            // dd($categories);
            $latestReviews = Review::with([
                'user',
                'business.translations' => fn($q) => $q->where('lang_id', $lang_id),
                'business.reviews.translations' => fn($q) => $q->where('language_id', $lang_id),
                'translations' => fn($q) => $q->where('language_id', $lang_id),
            ])->where('status', 'active')
                ->whereHas('user', function ($q) {
                    $q->where('user_type', '!=', 'admin');
                })
                ->latest()
                ->take(3)
                ->get();

            $articles = ExpertGuideArticle::with([
                'category.translations' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                },
                'translationForCurrentLang',  // This will load the article translations for the current language
            ])
                ->latest() // Get the latest articles
                ->take(3)  // Limit to 3 articles
                ->get();
            // dd($articles);
            $homeContantImages = HomeContent::where('lang_id', 1)
                ->whereIn('meta_key', [
                    'header_background_img',
                    'header_img',
                    'trusted_brands_img',
                    'ai_section_left_img',
                    'ai_section_right_img',
                    'ai_send_img',
                    'review_section_right_img',
                    'review_section_left_img',
                    'find_tool_left_img',
                    'find_tool_right_img',
                    'user_reviews_img',
                    'price_compare_img',
                    'independent_img',
                ])
                ->get()
                ->keyBy('meta_key');
            $reviews = Review::with([
                'user',
                'translations' => fn($q) => $q->where('language_id', $lang_id),
                'business.translations' => fn($q) => $q->where('lang_id', $lang_id),
                'business.reviews',
                'business.category',
            ])
                ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
                ->whereHas('business')
                ->whereHas('business.category')
                ->orderByDesc('rating')
                ->where('status', 'active')
                ->latest()
                ->get()
                ->groupBy(fn($review) => $review->business->category->id);

            $expertGuide = ExpertGuides::where('lang_id', $lang_id)->first()  ?? ExpertGuides::where('lang_id', 1)->first();
            $pageTileTranslationEducation = PageTile::where('source', 'educationItem')
                ->with('translations')->where('lang_id', $lang_id)
                ->get();
            $pageTileTranslationRightTools = PageTile::where('source', 'righttools')
                ->with('translations')->where('lang_id', $lang_id)
                ->get();

            $articles = ExpertGuideArticle::with(['translationForCurrentLang'])
                ->latest() // orders by `created_at` descending by default
                ->take(3)
                ->get();
            //  dd($categories);
            $country_id = getCurrentCountry();
            $exclusive_products = Product::whereHas('prices', function ($query) {
                $query->whereNotNull('discount_price')
                    ->whereDate('discount_expiration_date', '>=', now());
            })
                ->whereHas('translations', function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id)
                        ->where('product_translations.status', 'active');
                })
                ->whereHas('businesses', function ($query) use ($lang_id, $country_id) {
                    $query->where(function ($q) use ($country_id) {
                        $q->where('active_all_countries', 1)
                            ->orWhereHas('countries', function ($q2) use ($country_id) {
                                $q2->where('country_id', $country_id);
                            });
                    })
                        ->whereHas('translations', function ($q) use ($lang_id) {
                            $q->where('lang_id', $lang_id);
                        });
                })
                ->where(function ($query) use ($country_id) {
                    $query->where('active_all_countries', 1)
                        ->orWhereHas('countries', function ($countryQuery) use ($country_id) {
                            $countryQuery->where('country_id', $country_id);
                        });
                })
                ->where('products.status', 'public')
                ->with([
                    'translations' => function ($query) use ($lang_id) {
                        $query->where('lang_id', $lang_id);
                    },
                    'prices' => function ($query) {
                        $query->whereNotNull('discount_price')
                            ->whereDate('discount_expiration_date', '>=', now())
                            ->orderBy('price');
                    },
                    'businesses.translations' => function ($query) use ($lang_id) {
                        $query->where('lang_id', $lang_id);
                    }
                ])
                ->limit(10)
                ->get()
                ->map(function ($product) {
                    $price = $product->prices->first();
                    if ($price && $price->price > 0) {
                        $discount_percentage = round((($price->price - $price->discount_price) / $price->price) * 100);
                        $product->discount_percentage = $discount_percentage;
                    } else {
                        $product->discount_percentage = 0;
                    }
                    return $product;
                });
            $default_image = WebSetting::where('key', 'user_default_image')->value('value');
            return view('User.home.index', compact('default_image', 'exclusive_products', 'homeContents', 'latestReviews', 'categories', 'homeContantImages', 'reviews', 'expertGuide', 'pageTileTranslationEducation', 'pageTileTranslationRightTools', 'articles'));
        } catch (\Throwable $e) {
            return view('welcome');
        }
    }


    public function changeLanguage(Request $request, $lang_code)
    {

        $cacheKey = "userDetails";
        $languages = getLanguages();
        $languageRecord = $languages->firstWhere('lang_code', $lang_code);
        if (!$languageRecord) {
            $lang_code = 'en-us';
            $languageRecord = $languages->firstWhere('lang_code', $lang_code); // Fetch the default language record
        }
        $userDetails = [
            'lang_code' => $languageRecord->lang_code,
            'lang_name' => $languageRecord->name,
            'lang_id' => $languageRecord->id,
        ];


        storePrefrences($userDetails);
        $currentRoute = $request->route();
        return redirect()->route('home', ['locale' => $lang_code])
            ->with('success', 'Language changed successfully');
    }

    public function Faqs($slug = null)
    {
        $lang_id = getCurrentLanguageID();

        $faqCategories = FaqCategory::with([
            'translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'faqs' => function ($query) {
                $query->where('type', 'user')
                    ->where('status', 'active')
                    ->orderBy('position', 'asc'); // ✅ Order by position
            },
            'faqs.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            }
        ])->where('status', 1)->get();

        $activeCategory = null;

        if ($slug) {
            $activeCategory = $faqCategories->first(function ($cat) use ($slug) {
                return Str::slug($cat->translations->first()?->name ?? '') === $slug;
            });
        }

        $activeCategory ??= $faqCategories->first();

        return view('User.faq.faq', compact('faqCategories', 'activeCategory'));
    }
    public function allReview(Request $request, $locale, $slug)
    {
        $lang_id = getCurrentLanguageID();

        // Get the business from the business_translations table using the slug
        $businessTranslation = BusinessTranslation::where('slug', $slug)
            ->where('lang_id', $lang_id)
            ->first();

        if (!$businessTranslation) {
            $businessTranslation = BusinessTranslation::where('slug', $slug)->first();
        }

        if (!$businessTranslation) {
            abort(404, 'Business not found');
        }

        $business = Business::where('id', $businessTranslation->business_id)
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'category.translations' => fn($q) => $q->where('lang_id', $lang_id),
                'category.parent.translations' => fn($q) => $q->where('lang_id', $lang_id),
            ])->firstOrFail();

        // Calculate average rating based on active reviews
        $allReviews = Review::where('business_id', $business->id)->get();
        $activeReviews = $allReviews->whereIn('status', ['published', 'active']);
        $ratingCount = $activeReviews->count();
        $hasUserRatings = $ratingCount >= 10;
        if ($hasUserRatings) {
            $averageRating = round($activeReviews->avg('rating'), 1);
        } elseif ($business->admin_rating !== null && (float)$business->admin_rating > 0) {
            $averageRating = (float)$business->admin_rating;
        } else {
            $averageRating = null;
        }

        // Dynamic Rating Criteria Breakdown
        $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : collect();
        $activeReviewIds = $activeReviews->pluck('id')->toArray();

        foreach ($criteria as $criterion) {
            $totalScore = 0;
            $count = 0;

            $matchingCriteriaIds = CategoryRatingCriteria::where('id', $criterion->id)
                ->orWhere(function ($q) use ($criterion) {
                    if ($criterion->default_key) {
                        $q->where('default_key', $criterion->default_key);
                    }
                    $q->orWhereRaw('LOWER(name) = ?', [strtolower($criterion->name)]);
                })
                ->pluck('id')
                ->toArray();

            $ratingRecords = ReviewRating::whereIn('review_id', $activeReviewIds)
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

        if ($ratingCount > 0) {
            $recommendCount = $activeReviews->where('recommend', 1)->count();
            $recommendPercent = round(($recommendCount / $ratingCount) * 100);
        } else {
            $recommendPercent = 0;
        }

        // Build reviews query
       $reviewsQuery = Review::with([
    'user',
    'business.translations' => function ($query) use ($lang_id) {
        $query->where('lang_id', $lang_id);
    },
    'translations' => function ($query) use ($lang_id) {
        $query->where('language_id', $lang_id);
    },
    'selectedProCons',
])
            ->where('business_id', $business->id)
            ->whereHas('translations', function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            })->whereHas('user', function ($query) {
                $query->where('user_type', '!=', 'admin');
            });

        // Filter by rating (stars)
        $selectedStars = $request->get('stars');
        if (is_string($selectedStars)) {
            $selectedStars = explode(',', $selectedStars ?? '');
        }
        $selectedStars = array_filter(array_map('intval', (array) $selectedStars));

        if (!empty($selectedStars)) {
            $reviewsQuery->where(function ($q) use ($selectedStars) {
                foreach ($selectedStars as $star) {
                    $min = $star - 0.5;
                    $max = $star + 0.5;
                    $q->orWhere(function ($sub) use ($min, $max) {
                        $sub->where('rating', '>=', $min)
                            ->where('rating', '<', $max);
                    });
                }
            });
        }

        // Sort reviews
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'best':
            case 'high-to-low':
                $reviewsQuery->orderByDesc('rating')->orderByDesc('created_at');
                break;
            case 'low-to-high':
                $reviewsQuery->orderBy('rating')->orderByDesc('created_at');
                break;
            case 'recent':
            default:
                $reviewsQuery->orderByDesc('created_at');
                break;
        }

        $reviews = $reviewsQuery->paginate(5)->appends($request->query());

        // Calculate rating breakdown
        $ratingsCount = Review::where('business_id', $business->id)
            ->where('status', 'active')
            ->selectRaw('ROUND(rating) as rounded_rating, COUNT(*) as count')
            ->groupBy('rounded_rating')
            ->pluck('count', 'rounded_rating');

        $totalReviews = $ratingsCount->sum();        // Aggregate User Selected Pros and Cons for this business from active reviews
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
        } elseif ($aggregatedProCons->isEmpty() && $business->proCons->isNotEmpty()) {
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

        if ($request->ajax()) {
            return view('User.review.partials.reviews_list', compact('reviews', 'business'))->render();
        }

        return view('User.review.user_review2', compact('totalReviews', 'ratingsCount', 'reviews', 'business', 'averageRating', 'ratingCount', 'criteria', 'recommendPercent', 'aggregatedPros', 'aggregatedCons'));
    }

    //Review Transalation function
    public function ReviewTranslation(Request $request)
    {
        if (!$request->review_id || !$request->language_id) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }


        $review = Review::find($request->review_id);

        // return $review;
        if (!$review) {
            return response()->json(['error' => static_text('error_review_not_found') ?: 'Review not found'], 404);
        }

        if ($request->type == 'original') {
            $translation = $review->original;
        } else {
            $translation = $review->translation($request->language_id);
        }

        return response()->json([
            'review' => $translation,
        ]);
    }

    public function businessFaqs(Request $request, $locale, $business_slug, $faq_slug)
    {
        $lang_id = getCurrentLanguageID();

        $languageObj = Language::where('lang_code', $locale)->first();
        $expectedSlug = !empty($languageObj->faq_slug) ? $languageObj->faq_slug : 'faqs';

        $businessTranslation = BusinessTranslation::where('slug', $business_slug)
            ->where('lang_id', $lang_id)
            ->first();

        if (!$businessTranslation) {
            $businessTranslation = BusinessTranslation::where('slug', $business_slug)->first();
        }

        if (!$businessTranslation) {
            abort(404, 'Business not found');
        }

        $business = Business::where('id', $businessTranslation->business_id)
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
                'products' => fn($q) => $q->with(['prices']),
                'faqs' => function ($query) use ($lang_id) {
                    $query->where('status', 1)
                        ->orderBy('position', 'asc')
                        ->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)]);
                },
                'usps',
            ])->firstOrFail();

        $activeReviews = $business->reviews->whereIn('status', ['published', 'active']);
        $totalReviews = $activeReviews->count();
        $hasUserRatings = $totalReviews >= 10;
        if ($hasUserRatings) {
            $averageRating = round($activeReviews->avg('rating'), 1);
        } elseif ($business->admin_rating !== null && (float)$business->admin_rating > 0) {
            $averageRating = (float)$business->admin_rating;
        } else {
            $averageRating = null;
        }
        $recommendCount = $activeReviews->where('recommend', 1)->count();
        $recommendPercent = ($hasUserRatings && $totalReviews > 0) ? round(($recommendCount / $totalReviews) * 100) : 0;

        // Calculate Criteria ratings
        $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : collect();
        $activeReviewIds = $activeReviews->pluck('id')->toArray();

        foreach ($criteria as $criterion) {
            $totalScore = 0;
            $count = 0;

            $matchingCriteriaIds = CategoryRatingCriteria::where('id', $criterion->id)
                ->orWhere(function ($q) use ($criterion) {
                    if ($criterion->default_key) {
                        $q->where('default_key', $criterion->default_key);
                    }
                    $q->orWhereRaw('LOWER(name) = ?', [strtolower($criterion->name)]);
                })
                ->pluck('id')
                ->toArray();

            $ratingRecords = ReviewRating::whereIn('review_id', $activeReviewIds)
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

        // Starting Price
        $startingPrice = null;
        $currency = '$';
        $timeUnit = 'Month';
        $additional_info = 'NA';
        $price = getBusinessesWithStartingPrice($business);
        if (!empty($price) && isset($price[0]['starting_price'])) {
            $bPrice = $price[0]['starting_price'];
            $startingPrice = $bPrice['amount'];
            $currency = $bPrice['currency'] ?? '$';
            $timeUnit = ucfirst($bPrice['time_unit'] ?? 'month');
            $additional_info = $bPrice['additional_info'] ?? 'NA';
        }

        $topReviews = Review::with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->where('status', 'active')
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->take(2)
            ->get();

        // FAQ Categories & Grouped FAQs
        $faqCategories = BusinessFaqCategory::where('business_id', $business->id)
            ->where('status', 1)
            ->ordered()
            ->with(['faqs' => function ($q) use ($lang_id) {
                $q->where('status', 1)
                    ->orderedByHelpful()
                    ->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)]);
            }])
            ->get();

        $uncategorizedFaqs = BusinessFaq::where('business_id', $business->id)
            ->whereNull('business_faq_category_id')
            ->where('status', 1)
            ->orderedByHelpful()
            ->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)])
            ->get();

        $allFaqs = BusinessFaq::where('business_id', $business->id)
            ->where('status', 1)
            ->orderedByHelpful()
            ->with(['translations' => fn($t) => $t->where('lang_id', $lang_id), 'category'])
            ->get();

        $userVotes = [];
        if (auth()->check()) {
            $userVotes = BusinessFaqFeedback::where('user_id', auth()->id())
                ->whereNotNull('is_helpful')
                ->pluck('is_helpful', 'business_faq_id')
                ->toArray();
        }

        // Explore subcategories of category or parent category
        $catForExplore = $business->category ? ($business->category->parent ?? $business->category) : null;
        $exploreSubcategories = ['title' => '', 'items' => collect(), 'category' => $catForExplore];
        if ($catForExplore) {
            $catForTrans = $catForExplore->translations->firstWhere('lang_id', $lang_id) ?? $catForExplore->translations->first();
            $catForName = $catForTrans->name ?? $catForExplore->name ?? '';
            $subcats = \App\Models\Category::where('parent_id', $catForExplore->id)
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
                'title' => 'Explore ' . (!empty($catForName) ? $catForName : ($business->category->translations->firstWhere('lang_id', $lang_id)->name ?? '')) . ' categories',
                'items' => $subcats,
                'category' => $catForExplore,
            ];
        }

        return view('User.product.business_faqs', compact(
            'business',
            'averageRating',
            'totalReviews',
            'recommendPercent',
            'criteria',
            'startingPrice',
            'currency',
            'timeUnit',
            'additional_info',
            'topReviews',
            'expectedSlug',
            'faqCategories',
            'uncategorizedFaqs',
            'allFaqs',
            'userVotes',
            'exploreSubcategories'
        ));
    }

    public function handleBusinessSubPage(Request $request, $locale, $business_slug, $second_segment)
    {
        $languageObj = Language::where('lang_code', $locale)->first();
        $lang_id = $languageObj ? $languageObj->id : getCurrentLanguageID();
        $expectedFaqSlug = !empty($languageObj->faq_slug) ? $languageObj->faq_slug : 'faqs';
        $expectedAlternativesSlug = !empty($languageObj->alternatives_slug) ? $languageObj->alternatives_slug : 'alternatives';
        $expectedReviewsSlug = !empty($languageObj->reviews_slug) ? $languageObj->reviews_slug : 'reviews';
        $expectedComparisonsSlug = !empty($languageObj->comparisons_slug) ? $languageObj->comparisons_slug : 'comparisons';
        $expectedDiscussionsSlug = !empty($languageObj->discussions_slug) ? $languageObj->discussions_slug : 'discussions';

        // 1. Check if slug matches a Category or Subcategory
        $category = Category::whereHas('translations', function ($query) use ($business_slug, $lang_id) {
            $query->where('slug', $business_slug)->where('lang_id', $lang_id);
        })->first();

        if (!$category) {
            $category = Category::whereHas('translations', function ($query) use ($business_slug) {
                $query->where('slug', $business_slug);
            })->first();
        }

        if ($category) {
            if ($second_segment === $expectedFaqSlug || $second_segment === 'faqs' || $second_segment === 'faq') {
                return app(\App\Http\Controllers\User\CategoryController::class)->categoryFaqs($locale, $business_slug);
            }
            if ($second_segment === $expectedDiscussionsSlug || $second_segment === 'discussions') {
                return $this->categoryDiscussions($request, $locale, $business_slug, $category);
            }
            if ($second_segment === $expectedComparisonsSlug || $second_segment === 'comparisons') {
                abort(404);
            }

            // Check if second_segment is a child subcategory of this category
            $subCategory = Category::where('parent_id', $category->id)
                ->whereHas('translations', function ($query) use ($second_segment, $lang_id) {
                    $query->where('slug', $second_segment)->where('lang_id', $lang_id);
                })->first();

            if (!$subCategory) {
                $subCategory = Category::where('parent_id', $category->id)
                    ->whereHas('translations', function ($query) use ($second_segment) {
                        $query->where('slug', $second_segment);
                    })->first();
            }

            if ($subCategory) {
                return app(\App\Http\Controllers\User\CategoryController::class)->categoryDetail($locale, $second_segment);
            }

            // Check if second_segment is a Feature for this category
            $featureExists = Feature::whereHas('translations', function ($query) use ($second_segment) {
                $query->where('name', 'LIKE', str_replace('-', ' ', $second_segment))
                    ->orWhere('name', 'LIKE', $second_segment)
                    ->orWhereRaw("REPLACE(LOWER(name), ' ', '-') = ?", [$second_segment]);
            })->exists();

            if (!$featureExists) {
                $featureExists = CategoryFeatureContent::where('category_id', $category->id)
                    ->exists();
            }

            if ($featureExists) {
                $catTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection
                    ? $category->translations->first()
                    : ($category->translations ?? $category->translation);

                if (!$catTrans) {
                    $catTrans = $category->categoryTranslations()->where('lang_id', $lang_id)->first()
                        ?? $category->categoryTranslations()->where('lang_id', 1)->first()
                        ?? $category->categoryTranslations()->first();
                }

                $metaTitle = !empty($catTrans?->meta_title)
                    ? $catTrans->meta_title
                    : ($catTrans?->name ?? 'Category Page');
                $metaDescription = !empty($catTrans?->meta_description)
                    ? $catTrans->meta_description
                    : strip_tags($catTrans?->description ?? '');

                // Check Feature Meta Tags (Cascade or Custom Override)
                if (!empty($second_segment) && $category) {
                    $matchedFeat = \App\Models\Feature::whereHas('translations', function($q) use ($second_segment) {
                        $q->where('name', 'LIKE', str_replace('-', ' ', $second_segment))
                          ->orWhere('name', 'LIKE', $second_segment)
                          ->orWhereRaw("REPLACE(LOWER(name), ' ', '-') = ?", [$second_segment]);
                    })->first();

                    if ($matchedFeat) {
                        $resolvedFeatMeta = \App\Services\MetaTagService::resolveFeatureMeta($matchedFeat, $category, $lang_id);
                        $metaTitle = $resolvedFeatMeta['title'];
                        $metaDescription = $resolvedFeatMeta['description'];
                    } else {
                        $resolvedCatMeta = \App\Services\MetaTagService::resolveCategoryMeta($category, $lang_id);
                        $metaTitle = $resolvedCatMeta['title'];
                        $metaDescription = $resolvedCatMeta['description'];
                    }
                }

                if (function_exists('format_meta_text')) {
                    $metaTitle = format_meta_text($metaTitle, 'Category Page');
                    $metaDescription = format_meta_text($metaDescription);
                }

                return view('User.category.category_detail', [
                    'slug' => $catTrans?->slug ?? $business_slug,
                    'feature_slug' => $second_segment,
                    'page' => 1,
                    'metaTitle' => $metaTitle,
                    'metaDescription' => $metaDescription,
                ]);
            }
        }

        // 2. Business Subpages
        if ($second_segment === $expectedAlternativesSlug || $second_segment === 'alternatives') {
            return $this->businessAlternatives($request, $locale, $business_slug, $second_segment);
        }

        if ($second_segment === $expectedFaqSlug || $second_segment === 'faqs' || $second_segment === 'faq') {
            return $this->businessFaqs($request, $locale, $business_slug, $second_segment);
        }

        if ($second_segment === $expectedReviewsSlug || $second_segment === 'reviews' || $second_segment === 'all-review') {
            return $this->allReview($request, $locale, $business_slug);
        }

        if ($second_segment === $expectedComparisonsSlug || $second_segment === 'comparisons') {
            return app(\App\Http\Controllers\User\ProductController::class)->allBusinessComparisons($locale, $business_slug);
        }

        if ($second_segment === $expectedDiscussionsSlug || $second_segment === 'discussions' || $second_segment === 'discussion' || $second_segment === 'community') {
            return $this->businessDiscussions($request, $locale, $business_slug);
        }

        abort(404);
    }

    public function businessAlternatives(Request $request, $locale, $business_slug, $alternatives_slug)
    {
        $lang_id = getCurrentLanguageID();

        $businessTranslation = BusinessTranslation::where('slug', $business_slug)
            ->where('lang_id', $lang_id)
            ->first();

        if (!$businessTranslation) {
            $businessTranslation = BusinessTranslation::where('slug', $business_slug)->first();
        }

        if (!$businessTranslation) {
            abort(404, 'Business not found');
        }

        $business = Business::where('id', $businessTranslation->business_id)
            ->with([
                'translations',
            ])->firstOrFail();

        return view('User.product.business_alternatives', compact('business'));
    }

    public function writeReviewPage(Request $request, $locale)
    {
        $lang_id = getCurrentLanguageID();

        // 1. Trending Businesses: top 8 businesses by active reviews count and average rating
        $trendingBusinesses = Business::where('status', 1)
            ->whereHas('languages', function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            })
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhereHas('countries', function ($q) {
                        $q->where('country_id', getCurrentCountry());
                    });
            })
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
            ])
            ->withCount(['reviews as active_reviews_count' => function ($query) {
                $query->where('status', 'active');
            }])
            ->withAvg(['reviews as average_rating' => function ($query) {
                $query->where('status', 'active');
            }], 'rating')
            ->orderByDesc('active_reviews_count')
            ->orderByDesc('average_rating')
            ->take(8)
            ->get();

        // 2. Recently Reviewed: 8 businesses that have active reviews, ordered by review date
        $recentBusinessIds = Review::whereIn('status', ['published', 'active'])
            ->orderByDesc('created_at')
            ->pluck('business_id')
            ->unique()
            ->take(8)
            ->toArray();

        $recentlyReviewed = Business::whereIn('id', $recentBusinessIds)
            ->where('status', 1)
            ->whereHas('languages', function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            })
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhereHas('countries', function ($q) {
                        $q->where('country_id', getCurrentCountry());
                    });
            })
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'reviews' => fn($q) => $q->whereIn('status', ['published', 'active'])->orderByDesc('created_at'),
            ])
            ->withCount(['reviews as active_reviews_count' => function ($query) {
                $query->where('status', 'active');
            }])
            ->withAvg(['reviews as average_rating' => function ($query) {
                $query->where('status', 'active');
            }], 'rating')
            ->get()
            ->sortBy(function ($business) use ($recentBusinessIds) {
                return array_search($business->id, $recentBusinessIds);
            })
            ->values();

        // 3. Unreviewed Businesses: 8 businesses with 0 active reviews (chosen randomly)
        $unreviewedBusinesses = Business::where('status', 1)
            ->whereHas('languages', function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            })
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhereHas('countries', function ($q) {
                        $q->where('country_id', getCurrentCountry());
                    });
            })
            ->whereDoesntHave('reviews', function ($query) {
                $query->where('status', 'active');
            })
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
            ])
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('User.product.write_review_landing', compact('trendingBusinesses', 'unreviewedBusinesses', 'recentlyReviewed'));
    }

    public function resolveSlug(Request $request, $locale, $slug)
    {
        $matchedLang = Language::where('lang_code', $locale)->first();
        $lang_id = $matchedLang ? $matchedLang->id : getCurrentLanguageID();

        // 1. Check if slug matches a Category or Subcategory
        $category = Category::whereHas('translations', function ($query) use ($slug, $lang_id) {
            $query->where('slug', $slug)->where('lang_id', $lang_id);
        })->first();

        if (!$category) {
            $category = Category::whereHas('translations', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })->first();
        }

        if ($category) {
            return app(\App\Http\Controllers\User\CategoryController::class)->categoryDetail($locale, $slug);
        }

        // 2. Check if slug matches a Business / Product
        $business = Business::whereHas('translations', function ($query) use ($slug, $lang_id) {
            $query->where('slug', $slug)->where('lang_id', $lang_id);
        })->first();

        if (!$business) {
            $business = Business::whereHas('translations', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })->first();
        }

        if ($business) {
            return app(\App\Http\Controllers\User\ProductController::class)->productDetail($locale, $slug, $request);
        }

        // 3. Neither category nor business found -> 404
        abort(404);
    }


    public function voteBusinessFaq(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => static_text('error_discussion_login_vote') ?: 'Please sign in to vote.'
            ], 401);
        }

        $faqId = $request->input('faq_id');
        $isHelpful = filter_var($request->input('is_helpful'), FILTER_VALIDATE_BOOLEAN);
        $userId = auth()->id();

        $faq = BusinessFaq::findOrFail($faqId);
        $existing = BusinessFaqFeedback::where('business_faq_id', $faqId)
            ->where('user_id', $userId)
            ->whereNotNull('is_helpful')
            ->first();

        if ($existing) {
            if ($existing->is_helpful === $isHelpful) {
                return response()->json([
                    'success' => true,
                    'already_voted' => true,
                    'helpful_count' => (int) $faq->helpful_count,
                    'not_helpful_count' => (int) $faq->not_helpful_count,
                    'user_vote' => $isHelpful ? 'yes' : 'no'
                ]);
            }

            // Switch vote
            if ($existing->is_helpful) {
                $faq->decrement('helpful_count');
                $faq->increment('not_helpful_count');
            } else {
                $faq->decrement('not_helpful_count');
                $faq->increment('helpful_count');
            }
            $existing->update(['is_helpful' => $isHelpful]);
        } else {
            BusinessFaqFeedback::create([
                'business_faq_id' => $faqId,
                'user_id' => $userId,
                'is_helpful' => $isHelpful,
                'ip_address' => $request->ip()
            ]);

            if ($isHelpful) {
                $faq->increment('helpful_count');
            } else {
                $faq->increment('not_helpful_count');
            }
        }

        $faq->refresh();

        return response()->json([
            'success' => true,
            'helpful_count' => (int) $faq->helpful_count,
            'not_helpful_count' => (int) $faq->not_helpful_count,
            'user_vote' => $isHelpful ? 'yes' : 'no'
        ]);
    }

    public function reportBusinessFaq(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => static_text('error_report_login_issue') ?: 'Please sign in to report an issue.'
            ], 401);
        }

        $request->validate([
            'faq_id' => 'required|exists:business_faqs,id',
            'report_reason' => 'required|string',
            'report_details' => 'nullable|string|max:1000'
        ]);

        $faqId = $request->input('faq_id');
        $reason = $request->input('report_reason');
        $details = $request->input('report_details');

        BusinessFaqFeedback::create([
            'business_faq_id' => $faqId,
            'user_id' => auth()->id(),
            'report_reason' => $reason,
            'report_details' => $details,
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => static_text('msg_report_submitted_success') ?: 'Thank you! Your report has been submitted.'
        ]);
    }

    public function reportReview(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => static_text('error_report_login_review') ?: 'Please sign in to report a review.'
            ], 401);
        }

        $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'report_reason' => 'required|string',
            'report_details' => 'nullable|string|max:1000'
        ]);

        $user = auth()->user();
        $reporterType = ($user && $user->business_id) ? 'business' : 'user';

        $report = ReviewReport::create([
            'review_id'     => $request->input('review_id'),
            'user_id'       => $user->id,
            'report_reason' => $request->input('report_reason'),
            'report_type'   => $request->input('report_type') ?: strtolower(str_replace(' ', '_', $request->input('report_reason'))),
            'reporter_type' => $reporterType,
            'report_details'=> $request->input('report_details'),
            'status'        => \App\Models\ReviewReport::STATUS_PENDING,
        ]);

        // Trigger automated reassessment pipeline (omni-moderation -> GPT-5.6 Luna batch)
        $review = \App\Models\Review::find($request->input('review_id'));
        if ($review) {
            try {
                \App\Services\ReviewModerationService::triggerRemoderation($review, $report);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Error triggering review re-moderation: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => static_text('msg_review_report_success') ?: 'Review reported successfully. Thank you for your feedback!'
        ]);
    }


    public function goBusiness($locale, $slug = null)
    {
        if ($slug === null) {
            $slug = $locale;
            $locale = request()->segment(1) ?: 'en-us';
            if ($locale === 'go') {
                $locale = 'en-us';
            }
        }

        // Find business translation by slug
        $translation = BusinessTranslation::where('slug', $slug)->first();
        if ($translation) {
            $business = $translation->business;
        } else {
            $business = Business::where('id', $slug)->first();
            if (!$business) {
                $business = Business::whereHas('translations', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                })->first();
            }
        }

        if (!$business || !$business->status) {
            abort(404);
        }

        // Resolve country ID for locale
        $countryId = null;
        if (!empty($locale)) {
            $matchedLang = \App\Models\Language::where('lang_code', strtolower($locale))->first();
            if ($matchedLang) {
                $cl = \App\Models\CountryLanguage::where('language_id', $matchedLang->id)->first();
                if ($cl) {
                    $countryId = $cl->country_id;
                } elseif ($matchedLang->country_id) {
                    $countryId = $matchedLang->country_id;
                }
            }
        }

        // Get the target destination URL for current country
        $targetUrl = $business->getEffectiveWebsiteUrl($countryId);

        if (!$targetUrl) {
            abort(404);
        }

        // 301 Permanent Redirect directly to the clean business website URL
        return redirect()->away($targetUrl, 301);
    }
    public function businessDiscussions(Request $request, $locale, $business_slug)
    {
        $lang_id = getCurrentLanguageID();
        $businessTranslation = BusinessTranslation::where('slug', $business_slug)
            ->where('lang_id', $lang_id)
            ->first();

        if (!$businessTranslation) {
            $businessTranslation = BusinessTranslation::where('slug', $business_slug)->first();
        }

        if (!$businessTranslation) {
            abort(404, 'Business not found');
        }

        $business = Business::where('id', $businessTranslation->business_id)
            ->with([
                'category.translation',
                'category.parent.translation',
                'translations',
                'usps',
            ])->firstOrFail();

        // Discussions for this business
        $query = BusinessDiscussion::where('business_id', $business->id)
            ->where('status', 'active')
            ->with([
                'user.country',
                'comments' => fn($q) => $q->whereNull('parent_id')
                    ->orderByDesc('is_pinned')
                    ->orderBy('created_at', 'asc')
                    ->with(['user', 'replies.user', 'replies.replies.user'])
            ]);

        // Search query
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Pinned discussions first
        $query->orderByDesc('is_pinned');

        // Sort
        $sort = $request->get('sort', 'popular');
        if ($sort === 'recent') {
            $query->latest();
        } elseif ($sort === 'commented') {
            $query->orderByDesc('comments_count');
        } else {
            $query->orderByDesc('upvotes_count')->latest();
        }

        $discussions = $query->paginate(15);
        $totalDiscussionsCount = $discussions->total();

        return view('User.product.business_discussions', compact(
            'business',
            'discussions',
            'totalDiscussionsCount'
        ));
    }

    public function categoryDiscussions(Request $request, $locale, $category_slug, $category = null)
    {
        $lang_id = getCurrentLanguageID();
        if (!$category) {
            $category = Category::whereHas('translations', function ($query) use ($category_slug, $lang_id) {
                $query->where('slug', $category_slug)->where('lang_id', $lang_id);
            })->first();

            if (!$category) {
                $category = Category::whereHas('translations', function ($query) use ($category_slug) {
                    $query->where('slug', $category_slug);
                })->first();
            }
        }

        if (!$category) {
            abort(404, 'Category not found');
        }

        $catTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection 
            ? $category->translations->first() 
            : ($category->translations ?? $category->translation);
        $catName = $catTrans->name ?? $category->name ?? 'Category';

        $query = BusinessDiscussion::where('category_id', $category->id)
            ->where('status', 'active')
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

        $resolvedH1Data = \App\Services\ContentTemplateService::resolveCategorySection($category, 'discussions_h1', $lang_id);
        $resolvedH1 = !empty($resolvedH1Data['headline']) ? $resolvedH1Data['headline'] : null;

        $resolvedSubtextData = \App\Services\ContentTemplateService::resolveCategorySection($category, 'discussions_subtext', $lang_id);
        $resolvedSubtext = !empty($resolvedSubtextData['headline']) ? $resolvedSubtextData['headline'] : null;

        $resolvedPromptData = \App\Services\ContentTemplateService::resolveCategorySection($category, 'category_discussions_prompt_placeholder', $lang_id);
        $resolvedPrompt = !empty($resolvedPromptData['headline']) ? $resolvedPromptData['headline'] : null;

        $resolvedEmptyData = \App\Services\ContentTemplateService::resolveCategorySection($category, 'category_discussions_empty_headline', $lang_id);
        $resolvedEmpty = !empty($resolvedEmptyData['headline']) ? $resolvedEmptyData['headline'] : null;

        return view('User.category.category_discussions', compact(
            'category',
            'catTrans',
            'catName',
            'discussions',
            'totalDiscussionsCount',
            'sort',
            'resolvedH1',
            'resolvedSubtext',
            'resolvedPrompt',
            'resolvedEmpty'
        ));
    }

    public function storeDiscussion(Request $request)
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => static_text('error_discussion_login_post') ?: 'Please sign in to post a discussion.', 'require_login' => true], 401);
            }
            return redirect()->back()->with('error', static_text('error_discussion_login_post') ?: 'Please sign in to post a discussion.');
        }

        $request->validate([
            'business_id' => 'nullable|required_without_all:category_id,is_top_rated|exists:businesses,id',
            'category_id' => 'nullable|required_without_all:business_id,is_top_rated|exists:categories,id',
            'is_top_rated' => 'nullable|boolean',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:50',
            'author_name' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();

        $discussion = BusinessDiscussion::create([
            'business_id' => $request->business_id,
            'category_id' => $request->category_id,
            'is_top_rated' => $request->boolean('is_top_rated'),
            'user_id' => $user->id,
            'author_name' => $user->displayName(),
            'author_title' => $user->job_title ?: 'Community Member',
            'category' => $request->category ?: 'General',
            'title' => $request->title,
            'content' => $request->content,
            'status' => 'active',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => static_text('msg_discussion_posted_success') ?: 'Discussion posted successfully!', 'discussion' => $discussion]);
        }

        return redirect()->back()->with('success', static_text('msg_discussion_posted_success') ?: 'Discussion posted successfully!');
    }

    public function storeDiscussionComment(Request $request)
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => static_text('error_discussion_login_comment') ?: 'Please sign in to post a comment.', 'require_login' => true], 401);
            }
            return redirect()->back()->with('error', static_text('error_discussion_login_comment') ?: 'Please sign in to post a comment.');
        }
        $request->validate([
            'business_discussion_id' => 'required|exists:business_discussions,id',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:business_discussion_comments,id',
            'author_name' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $discussion = BusinessDiscussion::findOrFail($request->business_discussion_id);

        $comment = BusinessDiscussionComment::create([
            'business_discussion_id' => $discussion->id,
            'user_id' => $user?->id,
            'parent_id' => $request->parent_id,
            'author_name' => $user ? $user->displayName() : ($request->author_name ?: 'Anonymous'),
            'author_title' => $user?->job_title ?: 'Community Member',
            'content' => $request->content,
            'status' => 'active',
        ]);

        $discussion->increment('comments_count');

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            $comment->load('replies');
            $html = view('User.product.partials.comment_item', [
                'comment' => $comment,
                'disc' => $discussion,
                'level' => $request->parent_id ? 1 : 0
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'parent_id' => $comment->parent_id,
                'discussion_id' => $discussion->id,
                'comments_count' => $discussion->comments_count,
            ]);
        }

        return redirect()->back();
    }

    public function voteDiscussion(Request $request)
    {
        $request->validate([
            'type' => 'required|in:discussion,comment',
            'id' => 'required|integer',
            'vote' => 'required|in:up,down',
        ]);

        $type = $request->type === 'discussion' ? BusinessDiscussion::class : BusinessDiscussionComment::class;
        $item = $type::find($request->id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $userId = auth()->id();
        $ip = $request->ip();

        $existingVote = BusinessDiscussionVote::where('voteable_type', $type)
            ->where('voteable_id', $item->id)
            ->where(function ($q) use ($userId, $ip) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('ip_address', $ip);
                }
            })->first();

        if ($existingVote) {
            if ($existingVote->vote_type === $request->vote) {
                if ($request->vote === 'up') {
                    $item->decrement('upvotes_count');
                } else {
                    $item->decrement('downvotes_count');
                }
                $existingVote->delete();
                $votedState = null;
            } else {
                if ($request->vote === 'up') {
                    $item->increment('upvotes_count');
                    $item->decrement('downvotes_count');
                } else {
                    $item->decrement('upvotes_count');
                    $item->increment('downvotes_count');
                }
                $existingVote->update(['vote_type' => $request->vote]);
                $votedState = $request->vote;
            }
        } else {
            BusinessDiscussionVote::create([
                'user_id' => $userId,
                'ip_address' => $ip,
                'voteable_type' => $type,
                'voteable_id' => $item->id,
                'vote_type' => $request->vote,
            ]);

            if ($request->vote === 'up') {
                $item->increment('upvotes_count');
            } else {
                $item->increment('downvotes_count');
            }
            $votedState = $request->vote;
        }

        $item->refresh();

        return response()->json([
            'success' => true,
            'upvotes' => max(0, $item->upvotes_count),
            'downvotes' => max(0, $item->downvotes_count),
            'score' => max(0, $item->upvotes_count - $item->downvotes_count),
            'voted' => $votedState,
        ]);
    }

    public function pinDiscussionOrComment(Request $request)
    {
        $request->validate([
            'type' => 'required|in:discussion,comment',
            'id' => 'required|integer',
        ]);

        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => static_text('error_discussion_login_pin') ?: 'Kindly login to pin items.'], 401);
        }

        if ($request->type === 'discussion') {
            $item = BusinessDiscussion::find($request->id);
            $businessId = $item?->business_id;
        } else {
            $item = BusinessDiscussionComment::find($request->id);
            $businessId = $item?->discussion?->business_id;
        }

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $user = auth()->user();
        $isAdminOrOwner = $user && ($user->user_type === 'admin' || ($user->business_id && $user->business_id == $businessId));

        if ($isAdminOrOwner) {
            $item->is_pinned = !$item->is_pinned;
            $item->save();

            return response()->json([
                'success' => true,
                'is_pinned' => (bool)$item->is_pinned,
                'is_global' => true,
                'message' => $item->is_pinned ? 'Globally pinned!' : 'Globally unpinned!'
            ]);
        } else {
            // Regular User Personal Pin (Session / Account storage)
            $sessionKey = ($request->type === 'discussion') ? 'user_pinned_discussions' : 'user_pinned_comments';
            $userPinnedItems = session()->get($sessionKey, []);

            if (in_array($item->id, $userPinnedItems)) {
                $userPinnedItems = array_diff($userPinnedItems, [$item->id]);
                $isPinned = false;
            } else {
                $userPinnedItems[] = $item->id;
                $isPinned = true;
            }
            session()->put($sessionKey, array_values(array_unique($userPinnedItems)));

            return response()->json([
                'success' => true,
                'is_pinned' => $isPinned,
                'is_global' => false,
                'message' => $isPinned ? 'Item pinned to your account!' : 'Item unpinned from your account!'
            ]);
        }
    }
    public function destroyDiscussion(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in.'], 401);
        }
        $discussion = BusinessDiscussion::findOrFail($id);
        $user = auth()->user();
        $isAdminOrOwner = ($user->user_type === 'admin') || ($user->business_id && $user->business_id == $discussion->business_id);
        if ($discussion->user_id !== $user->id && !$isAdminOrOwner) {
            return response()->json(['success' => false, 'message' => static_text('error_permission_denied') ?: 'Permission denied.'], 403);
        }
        $discussion->update(['status' => 'deleted']);
        return response()->json(['success' => true, 'message' => static_text('msg_discussion_deleted_success') ?: 'Discussion deleted successfully!']);
    }

    public function destroyDiscussionComment(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in.'], 401);
        }
        $comment = BusinessDiscussionComment::findOrFail($id);
        $user = auth()->user();
        $isAdminOrOwner = ($user->user_type === 'admin') || ($user->business_id && $comment->discussion && $user->business_id == $comment->discussion->business_id);
        if ($comment->user_id !== $user->id && !$isAdminOrOwner) {
            return response()->json(['success' => false, 'message' => static_text('error_permission_denied') ?: 'Permission denied.'], 403);
        }
        $comment->update(['status' => 'deleted']);
        if ($comment->discussion) {
            $comment->discussion->decrement('comments_count');
        }
        return response()->json(['success' => true, 'message' => static_text('msg_comment_deleted_success') ?: 'Comment deleted successfully!']);
    }

}
