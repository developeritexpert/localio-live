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
        $lang_id = getCurrentLanguageID();
        $user = auth()->user();

        $business = Business::whereHas('translations', function ($q) use ($slug, $lang_id) {
            $q->where('slug', $slug)->where('lang_id', $lang_id);
        })->with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'reviews' => fn($q) => $q->with('translations')
                ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
                ->where('status', 'active'),
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
        // dd($business);
        $languages = Language::all();
        // Calculate average rating: User rating if active user reviews exist; fallback to admin_rating if set
        $approvedReviewsCount = $business->reviews->where('status', 'active')->count();
        $hasUserReviews = $approvedReviewsCount > 0;
        if ($hasUserReviews) {
            $averageRating = round($business->reviews->where('status', 'active')->avg('rating'), 1);
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
                ->where('status', 'inactive')
                ->first();

            if ($userReview) {
                $userInactiveReviewMessage = 'Your review for this product has been deactivated by the admin.';
            }
        }

        // Filter: stars (can be array or comma-separated string)
        $selectedStars = $request->get('stars');
        if (is_string($selectedStars)) {
            $selectedStars = explode(',', $selectedStars);
        }
        $selectedStars = array_filter(array_map('intval', (array) $selectedStars));

        $sort = $request->get('sort', 'recent');

        $applyFiltersAndSort = function ($query) use ($selectedStars, $sort, $lang_id) {
            $query->where('status', 'active');
            
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
        $reviewsQuery = Review::where('business_id', $business->id);
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
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id));
        $allReviews = $applyFiltersAndSort($allReviewsQuery)->take(10)->get();

        $ourReviewsQuery = Review::with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->where('user_id', auth()->id())
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id));
        $ourReviews = $applyFiltersAndSort($ourReviewsQuery)->take(10)->get();

        $trustpilotReviewsQuery = Review::with([
            'user',
            'business',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id));
        $trustpilotReviews = $applyFiltersAndSort($trustpilotReviewsQuery)->take(10)->get();

        $ratingCount = $business->reviews->where('status', 'active')->count();

        $default_image = WebSetting::where('key','user_default_image')->value('value');

        if ($request->ajax()) {
            return view('User.product.partials.reviews_list', compact(
                'business',
                'allReviews',
                'ourReviews',
                'trustpilotReviews',
                'default_image',
                'averageRating',
                'ratingCount'
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
            ->where('status', 'active')
            ->groupBy(function ($review) {
                return (int) round($review->rating);
            })
            ->map(fn($group) => $group->count());

        // Total active reviews
        $totalReviews = $business->reviews->where('status', 'active')->count();

        // Ensure all 1–5 stars are represented

        $userRatingCounts = Review::where('business_id', $business->id)
            ->selectRaw('ROUND(rating) as rounded_rating, COUNT(DISTINCT user_id) as user_count')
            ->groupBy('rounded_rating')
            ->pluck('user_count', 'rounded_rating');

        $ratingBreakdown = collect(range(1, 5))->mapWithKeys(function ($i) use ($userRatingCounts) {
            return [$i => $userRatingCounts->get($i, 0)];
        });

        // dd($ratingBreakdown);
        $bizCategoryIds = array_filter(array_merge([$business->category_id], $business->subCategories ? $business->subCategories->pluck('id')->toArray() : []));

        $alternativeBusiness = !empty($bizCategoryIds)
            ? Business::whereIn('category_id', $bizCategoryIds)
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
                    'reviews' => fn($q) => $q->where('status', 'active'),
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
            ? Business::whereIn('category_id', $bizCategoryIds)
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
                    'reviews' => fn($q) => $q->where('status', 'active'),
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

        $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : collect();
        $activeReviews = $reviews->where('status', 'active');
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

        $activeReviewsCount = $reviews->where('status', 'active')->count();
        if ($activeReviewsCount > 0) {
            $recommendCount = $reviews->where('status', 'active')->where('recommend', 1)->count();
            $recommendPercent = round(($recommendCount / $activeReviewsCount) * 100);
        } else {
            $recommendPercent = 0;
        }

        $ratingTextsRecords = \App\Models\BusinessRatingText::where('business_id', $business->id)->get();
        $ratingTexts = [];
        foreach ($ratingTextsRecords as $rTxt) {
            $ratingTexts[$rTxt->criteria_key] = [
                'intro_text' => $rTxt->intro_text,
                'end_text' => $rTxt->end_text,
            ];
        }

        // Aggregate User Selected Pros and Cons for this business from active reviews
        $aggregatedProCons = \Illuminate\Support\Facades\DB::table('review_pro_cons')
            ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
            ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
            ->where('reviews.business_id', $business->id)
            ->where('reviews.status', 'active')
            ->select('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as review_count'))
            ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text')
            ->orderByDesc('review_count')
            ->get();

        $aggregatedPros = $aggregatedProCons->where('type', 'pro')->values();
        $aggregatedCons = $aggregatedProCons->where('type', 'con')->values();

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
            'aggregatedPros',
            'aggregatedCons'
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
            'products.prices',
            'usps',
            'translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'reviews.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'features.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
        ])
        ->whereIn('id', $comparedProductIds)
        ->get();
            // dd($businesses);
        return view('User.product.product_comparison', compact('businesses'));
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
            abort(404);
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
            // Regex fallback to split by -vs- or -<any-vs-word>-
            if (preg_match('/^(.*?)-([a-zA-Z0-9]+)-(.*?)$/', $comparison_businesses, $matches)) {
                $slugs = [$matches[1], $matches[3]];
            } else {
                $slugs = explode('-vs-', $comparison_businesses);
            }
        }
        
        if (count($slugs) != 2) {
            abort(404); // Invalid format
        }
        
        $businesses = Business::with([
            'products.prices',
            'usps',
            'translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
            'reviews.translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            },
            'features.translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
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

        // Populate session with compared product IDs so single removal works correctly
        session()->put('compared_products', $businesses->pluck('id')->toArray());
        session()->save();

        return view('User.product.product_comparison', compact('businesses'));
    }
    public function removeFromComparison($locale, $productId)
    {
        $productId = (int) $productId;
        $comparedProducts = session()->get('compared_products', []);

        // Remove only the requested product ID from the array
        $comparedProducts = array_values(array_filter($comparedProducts, function ($id) use ($productId) {
            return (int)$id !== $productId;
        }));

        // Update session
        session()->put('compared_products', $comparedProducts);
        session()->save();

        return response()->json([
            'success' => true,
            'redirect' => route('product-comparison', app()->getLocale())
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
            return response()->json(['error' => 'Product not found'], 404);
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
            'reviews' => fn($q) => $q->where('status', 'active'),
        ])->firstOrFail();

        $totalReviews = $business->reviews->count();
        $averageRating = $totalReviews > 0 ? round($business->reviews->avg('rating'), 1) : 0;
        $businessRating = $averageRating;
        
        $recommendCount = $business->reviews->where('recommend', 1)->count();
        $recommendPercent = $totalReviews > 0 ? round(($recommendCount / $totalReviews) * 100) : 0;

        $reviews = Review::where('business_id', $business->id)->get();
        $activeReviews = $reviews->where('status', 'active');
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

        $bizCategoryIds = array_filter(array_merge([$business->category_id], $business->subCategories ? $business->subCategories->pluck('id')->toArray() : []));

        $peerComparisonsQuery = !empty($bizCategoryIds)
            ? Business::whereIn('category_id', $bizCategoryIds)
            : Business::whereRaw('1 = 0');

        $peerComparisons = $peerComparisonsQuery
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
                'reviews' => fn($q) => $q->where('status', 'active'),
            ])
            ->paginate(12);

        return view('User.product.all_comparisons', compact('business', 'businessRating', 'peerComparisons', 'criteria', 'averageRating', 'totalReviews', 'recommendPercent'));
    }

    // Key Feature Review Controller
    public function storeFeatureReview(Request $request)
{
    // Check if user is logged in
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => 'You must be logged in to submit a review.'
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
