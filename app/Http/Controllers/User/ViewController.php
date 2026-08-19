<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cookie;
use App;
use App\Models\{Business, Category, SiteLanguages, CategoryTranslation, ExpertGuideArticle, Product, WebSetting, BusinessFaq, BusinessFaqCategory, BusinessFaqFeedback};
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
use Illuminate\Support\Str;

class ViewController extends Controller
{
    public function home()
    {
        $langCode = getCurrentLocale();
        $lang_id = getCurrentLanguageID();
        session(['lang_id' => $lang_id]); // Required for translationForCurrentLang to work

        $language_id = Language::where('lang_code', $langCode)->value('id');


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
        ])->where('status','active')
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
            ->where('status','active')
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
            $country_id=getCurrentCountry();
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
            $default_image=WebSetting::where('key','user_default_image')->value('value');
        return view('User.home.index', compact('default_image','exclusive_products','homeContents', 'latestReviews', 'categories', 'homeContantImages', 'reviews', 'expertGuide', 'pageTileTranslationEducation', 'pageTileTranslationRightTools', 'articles'));
    }


    public function changeLanguage(Request $request, $lang_code)
    {
        dd($lang_code);
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
    }    public function allReview(Request $request, $locale, $slug)
    {
        $lang_id = getCurrentLanguageID();

        // Get the business from the business_translations table using the slug
        $businessTranslation = \App\Models\BusinessTranslation::where('slug', $slug)
            ->where('lang_id', $lang_id)
            ->first();

        if (!$businessTranslation) {
            $businessTranslation = \App\Models\BusinessTranslation::where('slug', $slug)->first();
        }

        if (!$businessTranslation) {
            abort(404, 'Business not found');
        }

        $business = \App\Models\Business::where('id', $businessTranslation->business_id)
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'category.translations' => fn($q) => $q->where('lang_id', $lang_id),
                'category.parent.translations' => fn($q) => $q->where('lang_id', $lang_id),
            ])->firstOrFail();

        // Calculate average rating based on active reviews
        $allReviews = Review::where('business_id', $business->id)->get();
        $activeReviews = $allReviews->where('status', 'active');
        $ratingCount = $activeReviews->count();
        $averageRating = $ratingCount > 0 ? round($activeReviews->avg('rating'), 1) : 0;

        // Dynamic Rating Criteria Breakdown
        $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : collect();
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
            $selectedStars = explode(',', $selectedStars);
        }
        $selectedStars = array_filter(array_map('intval', (array) $selectedStars));

        if (!empty($selectedStars)) {
            $reviewsQuery->where(function($q) use ($selectedStars) {
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

        $totalReviews = $ratingsCount->sum();

        if ($request->ajax()) {
            return view('User.review.partials.reviews_list', compact('reviews', 'business'))->render();
        }

        return view('User.review.user_review2', compact('totalReviews', 'ratingsCount', 'reviews', 'business', 'averageRating', 'ratingCount', 'criteria', 'recommendPercent'));
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
            return response()->json(['error' => 'Review not found'], 404);
        }

        if($request->type == 'original') {
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

        $languageObj = \App\Models\Language::where('lang_code', $locale)->first();
        $expectedSlug = !empty($languageObj->faq_slug) ? $languageObj->faq_slug : 'faqs';

        $businessTranslation = \App\Models\BusinessTranslation::where('slug', $business_slug)
            ->where('lang_id', $lang_id)
            ->first();

        if (!$businessTranslation) {
            $businessTranslation = \App\Models\BusinessTranslation::where('slug', $business_slug)->first();
        }

        if (!$businessTranslation) {
            abort(404, 'Business not found');
        }

        $business = \App\Models\Business::where('id', $businessTranslation->business_id)
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'reviews' => fn($q) => $q->where('status', 'active'),
                'products' => fn($q) => $q->with(['prices']),
                'faqs' => function ($query) use ($lang_id) {
                    $query->where('status', 1)
                        ->orderBy('position', 'asc')
                        ->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)]);
                },
                'usps',
            ])->firstOrFail();

        $activeReviews = $business->reviews;
        $totalReviews = $activeReviews->count();
        $averageRating = $totalReviews > 0 ? round($activeReviews->avg('rating'), 1) : 0;
        $recommendCount = $activeReviews->where('recommend', 1)->count();
        $recommendPercent = $totalReviews > 0 ? round(($recommendCount / $totalReviews) * 100) : 0;

        // Calculate Criteria ratings
        $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : collect();
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

        // Top Highlighted Reviews
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
        $faqCategories = \App\Models\BusinessFaqCategory::where('business_id', $business->id)
            ->where('status', 1)
            ->ordered()
            ->with(['faqs' => function($q) use ($lang_id) {
                $q->where('status', 1)
                  ->orderedByHelpful()
                  ->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)]);
            }])
            ->get();

        $uncategorizedFaqs = \App\Models\BusinessFaq::where('business_id', $business->id)
            ->whereNull('business_faq_category_id')
            ->where('status', 1)
            ->orderedByHelpful()
            ->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)])
            ->get();

        $allFaqs = \App\Models\BusinessFaq::where('business_id', $business->id)
            ->where('status', 1)
            ->orderedByHelpful()
            ->with(['translations' => fn($t) => $t->where('lang_id', $lang_id), 'category'])
            ->get();

        $userVotes = [];
        if (auth()->check()) {
            $userVotes = \App\Models\BusinessFaqFeedback::where('user_id', auth()->id())
                ->whereNotNull('is_helpful')
                ->pluck('is_helpful', 'business_faq_id')
                ->toArray();
        }

        return view('User.product.business_faqs', compact(
            'business', 'averageRating', 'totalReviews', 'recommendPercent', 
            'criteria', 'startingPrice', 'currency', 'timeUnit', 'additional_info', 
            'topReviews', 'expectedSlug', 'faqCategories', 'uncategorizedFaqs', 'allFaqs', 'userVotes'
        ));
    }

    public function handleBusinessSubPage(Request $request, $locale, $business_slug, $second_segment)
    {
        $languageObj = \App\Models\Language::where('lang_code', $locale)->first();
        $expectedFaqSlug = !empty($languageObj->faq_slug) ? $languageObj->faq_slug : 'faqs';
        $expectedAlternativesSlug = !empty($languageObj->alternatives_slug) ? $languageObj->alternatives_slug : 'alternatives';
        $expectedReviewsSlug = !empty($languageObj->reviews_slug) ? $languageObj->reviews_slug : 'reviews';
        $expectedComparisonsSlug = !empty($languageObj->comparisons_slug) ? $languageObj->comparisons_slug : 'comparisons';

        if ($second_segment === $expectedAlternativesSlug || $second_segment === 'alternatives') {
            return $this->businessAlternatives($request, $locale, $business_slug, $second_segment);
        }

        if ($second_segment === $expectedFaqSlug || $second_segment === 'faqs') {
            return $this->businessFaqs($request, $locale, $business_slug, $second_segment);
        }

        if ($second_segment === $expectedReviewsSlug || $second_segment === 'reviews' || $second_segment === 'all-review') {
            return $this->allReview($request, $locale, $business_slug);
        }

        if ($second_segment === $expectedComparisonsSlug || $second_segment === 'comparisons') {
            return app(\App\Http\Controllers\User\ProductController::class)->allBusinessComparisons($locale, $business_slug);
        }

        abort(404);
    }

    public function businessAlternatives(Request $request, $locale, $business_slug, $alternatives_slug)
    {
        $lang_id = getCurrentLanguageID();

        $businessTranslation = \App\Models\BusinessTranslation::where('slug', $business_slug)
            ->where('lang_id', $lang_id)
            ->first();

        if (!$businessTranslation) {
            $businessTranslation = \App\Models\BusinessTranslation::where('slug', $business_slug)->first();
        }

        if (!$businessTranslation) {
            abort(404, 'Business not found');
        }

        $business = \App\Models\Business::where('id', $businessTranslation->business_id)
            ->with([
                'translations',
            ])->firstOrFail();

        return view('User.product.business_alternatives', compact('business'));
    }

    public function writeReviewPage(Request $request, $locale)
    {
        $lang_id = getCurrentLanguageID();

        // 1. Trending Businesses: top 8 businesses by active reviews count and average rating
        $trendingBusinesses = \App\Models\Business::where('status', 1)
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
                'reviews' => fn($q) => $q->where('status', 'active'),
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
        $recentBusinessIds = \App\Models\Review::where('status', 'active')
            ->orderByDesc('created_at')
            ->pluck('business_id')
            ->unique()
            ->take(8)
            ->toArray();

        $recentlyReviewed = \App\Models\Business::whereIn('id', $recentBusinessIds)
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
                'reviews' => fn($q) => $q->where('status', 'active')->orderByDesc('created_at'),
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
        $unreviewedBusinesses = \App\Models\Business::where('status', 1)
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
        $lang_id = getCurrentLanguageID();

        // 1. Check if slug matches a Category or Subcategory
        $category = \App\Models\Category::whereHas('translations', function ($query) use ($slug, $lang_id) {
            $query->where('slug', $slug)->where('lang_id', $lang_id);
        })->first();

        if (!$category) {
            $category = \App\Models\Category::whereHas('translations', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })->first();
        }

        if ($category) {
            return app(\App\Http\Controllers\User\CategoryController::class)->categoryDetail($locale, $slug);
        }

        // 2. Check if slug matches a Business / Product
        $business = \App\Models\Business::whereHas('translations', function ($query) use ($slug, $lang_id) {
            $query->where('slug', $slug)->where('lang_id', $lang_id);
        })->first();

        if (!$business) {
            $business = \App\Models\Business::whereHas('translations', function ($query) use ($slug) {
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
                'message' => 'Please sign in to vote.'
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
                'message' => 'Please sign in to report an issue.'
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
            'message' => 'Thank you! Your report has been submitted.'
        ]);
    }

}
