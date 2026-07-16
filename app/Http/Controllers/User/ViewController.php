<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cookie;
use App;
use App\Models\{Business, Category, SiteLanguages, CategoryTranslation, ExpertGuideArticle, Product, WebSetting};
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

        $categories = Category::whereHas('businesses.reviews') // only include categories with reviewed businesses
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
        ->take(3);


        $categories->each(function ($category) use ($lang_id) {
            $businesses = $category->businesses()
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
                ->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC') // ⭐ FIX: handle nulls
                ->take(3)
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
    }



    public function allReview(Request $request, $locale, $slug)
    {
        $lang_id = getCurrentLanguageID();

        // Get the business from the business_translations table using the slug
        $businessTranslation = \App\Models\BusinessTranslation::where('slug', $slug)
            ->where('lang_id', $lang_id)
            ->first();

        if (!$businessTranslation) {
            abort(404, 'Business not found');
        }

        $business = $businessTranslation->business; // Get actual Business model

        // Calculate average rating based on active reviews
        $averageRating = $business->reviews->where('status', 'active')->count() > 0
            ? $business->reviews->where('status', 'active')->avg('rating')
            : 0;

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

        return view('User.review.user_review2', compact('totalReviews', 'ratingsCount', 'reviews', 'business', 'averageRating'));
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

}


