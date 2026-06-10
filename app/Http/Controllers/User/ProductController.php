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
            'category.Topics.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'pricingOptions.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'features.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'websites' => fn($q) => $q->where('country_id', getCurrentCountry()),
            'faqs' => function ($query) use ($lang_id) {
                    $query->where('status', 1)
                        ->orderBy('position', 'asc')
                        ->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)]);
                },
        ])->firstOrFail();
        // dd($business);
        $languages = Language::all();
        // Calculate average rating
        $averageRating = $business->reviews->count() > 0
            ? $business->reviews->avg('rating')
            : 0;

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

        // Build review query
        $reviewsQuery = Review::where('business_id', $business->id)
            ->where('status', 'active')
            ->whereHas('user', fn($q) => $q->where('user_type', '!=', 'admin')) // exclude admin users
            ->with([
                'user',
                'translations' => fn($q) => $q->where('language_id', $lang_id),
            ]);

        // Filter: stars
        if ($request->has('stars') && $request->stars !== 'all') {
            $reviewsQuery->where('rating', $request->stars);
        }

        $sort = $request->get('sort', 'best');

        $reviewsQuery = Review::with(['user', 'translations'])->where('business_id', $business->id);

        switch ($sort) {
            case 'high-to-low':
                $reviewsQuery->orderByDesc('rating');
                break;
            case 'low-to-high':
                $reviewsQuery->orderBy('rating');
                break;
            case 'best':
            default:
                $reviewsQuery->orderByDesc('rating'); // or any default logic
                break;
        }
        $reviews = $reviewsQuery->get();
        $topReviews = Review::with([
            'user',
            'business',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
            ->orderByDesc('rating')
            ->take(3)
            ->get();

        $allReviews = Review::with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
            // ->whereHas('user', fn($q) => $q->where('user_type', '!=', 'admin')) //  exclude admin reviews
            ->latest()
            ->get();

        $ourReviews = Review::with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->where('user_id', auth()->id())
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
            // ->whereHas('user', fn($q) => $q->where('user_type', '!=', 'admin')) // exclude admin reviews
            ->latest()
            ->get();

        $trustpilotReviews = Review::with([
            'user',
            'business',
            'translations' => fn($q) => $q->where('language_id', $lang_id)
        ])
            ->where('business_id', $business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
            // ->whereHas('user', fn($q) => $q->where('user_type', '!=', 'admin')) //  exclude admin reviews
            ->latest()
            ->get();

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
            ->groupBy('rating')
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
        $alternativeBusiness = Business::where('category_id', $business->category_id)
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
            'websites' => fn($q) => $q->where('country_id', getCurrentCountry()),
        ])
        ->withCount([
            'reviews as average_rating' => function ($query) {
                $query->select(DB::raw('coalesce(avg(rating),0)'));
            }
        ])
        ->orderByDesc('average_rating')
        ->limit(3)
        ->get();
        $link = $business->websites->first()->website_url ?? $business->affiliate_link ?? $business->permanent_url ?? '#';
        $reviews = Review::where('business_id', $business->id)->get();
        $easeOfUseAvg = round($reviews->avg('ease_of_use_rating'), 1);
        $valueForMoneyAvg = round($reviews->avg('value_for_money_rating'), 1);
        $customerServiceAvg = round($reviews->avg('customer_service_rating'), 1);
        $exclusiveFeatureAvg = round($reviews->avg('exclusive_service_rating'), 1);
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
            'reviews',
            'userInactiveReviewMessage',
            'languages',
            'topReviews',
            'allReviews',
            'ourReviews',
            'trustpilotReviews',
            'easeOfUseAvg',
            'valueForMoneyAvg',
            'customerServiceAvg',
            'exclusiveFeatureAvg'
        ));
    }

    public function topRatedProduct($lang, $category_slug = null)
    {
        return view('User.product.top_rated_product');
    }
    public function ExclusiveBusinessDeals(){
        return view('User.product.exclusive_business_deals');
    }
    public function productComparison()
    {
        $lang_id=getCurrentLanguageID();
        $comparedProductIds = session()->get('compared_products', []);
        $businesses = Business::with([
            'products.prices',
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
    public function removeFromComparison($locale, $productId)
    {
        \Log::info('Removing product from comparison: ' . $productId);

        $productId = (int) $productId;
        $comparedProducts = session()->get('compared_products', []);

        \Log::info('Before removal, compared products: ', $comparedProducts);

        // Remove the product ID from the array
        $comparedProducts = array_values(array_filter($comparedProducts, function ($id) use ($productId) {
            return (int)$id != $productId;
        }));
        \Log::info('After removal, compared products: ', $comparedProducts);

        // Update the session
        session()->put('compared_products', $comparedProducts);

        // Make sure to save the session
        session()->save();

        return response()->json([
            'success' => true
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
