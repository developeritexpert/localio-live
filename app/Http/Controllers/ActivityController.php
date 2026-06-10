<?php

namespace App\Http\Controllers;
use App\Services\InterestTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Crypt;



class ActivityController extends Controller
{
    //

    private $tracker;
   

    public function __construct(InterestTracker $tracker)
    {
        $this->tracker = $tracker;

    }

    public function track(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'session_id' => 'required|string',
            'activity' => 'required|array',
            'activity.type' => 'required|string',
            'activity.product_id' => 'nullable|integer',
            'activity.category_id' => 'nullable|string',
            'activity.business_id' => 'nullable|integer',
            'activity.duration' => 'nullable|integer',
            'activity.metadata' => 'nullable|array'
        ]);
  
        // dd($validated);

        //    $country= getCurrentLocale('lang_code');
        //     dd($country);

        $activity = $validated['activity'];

        // Extract and decrypt lang_code from cookie
        $encrypted = Cookie::get('lang_code');
        $langCode = 'en-us'; // default
        
        try {
            $cookieVal = Crypt::decryptString($encrypted);
            $langCode = str_contains($cookieVal, '|') ? explode('|', $cookieVal)[1] : $cookieVal;
        } catch (\Exception $e) {
            // fallback stays as 'en-us'
        }
        
        // Now fetch the language record
        $language = Language::where('lang_code', $langCode)->first();
        
        // Extract country (you may need to adjust the field name below)
        $country = $language->name ?? 'Unknown';
        // dd($country);

            // Inject lang_code, lang_id, and country into metadata
        $activity['metadata']['lang_code'] = $langCode;
        $activity['metadata']['lang_id'] = $language->id ?? null;
        $activity['metadata']['country'] = $country;
                
        $this->tracker->trackActivity(
            $validated['user_id'],
            $validated['session_id'],
            $activity

        );

        
        // dd($validated);

        return response()->json(['status' => 'success']);
    }

    public function getPersonalizedDeals(Request $request)
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['deals' => [], 'error' => 'User not authenticated'], 401);
        }
    
        $userId = $user->id;
    
        // Get user interests
        $interests = $this->tracker->getUserInterests($userId);
        
 
        
        // Get personalized deals based on interests
        $deals = $this->tracker->getPersonalizedDeals($interests, $userId);
    
        // Ensure deals are properly formatted
        $formattedDeals = collect($deals)->map(function ($deal) {
            // Check if deal is already formatted (has 'product_price_id' key)
            if (isset($deal['product_price_id'])) {
                return $deal; // Already formatted
            }
            
            // If not formatted, it's raw model data - format it
            $product = $deal->product ?? null;
            if (!$product) {
                return null; // Skip if no product
            }
    
            return [
                'product_price_id' => $deal->id,
                'original_price' => $deal->price,
                'discounted_price' => $deal->discount_price,
                'currency' => $deal->currency,
                'discount_expiration_date' => $deal->discount_expiration_date,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name ?? 'Unknown Product',
                    'image' => $product->product_image,
                    'categories' => $product->categories ? $product->categories->pluck('name')->toArray() : [],
                ],
                'businesses' => $product->businesses ? $product->businesses->map(function ($b) {
                    $websiteUrl = $b->websites->first()?->website_url;
                    $reviews = $b->reviews;
                    $totalReviews = $reviews->count();
                    $averageRating = $totalReviews ? round($reviews->avg('rating'), 1) : null;

                    return [
                        'id' => $b->id,
                        'icon_id' => $b->icon_id,
                        'name' => $b->translations->first()?->name ?? $b->name ?? 'Unknown Business',
                        'description' => $b->translations->first()?->description ?? null,
                        'logo' => $b->icon_id ? asset($b->icon_id) : null,
                        'link' => $websiteUrl ?? $b->affiliate_link ?? $b->permanent_url,
                        'reviews' => [
                            'count' => $totalReviews,
                            'average' => $averageRating,
                        ],
                    ];
                })->toArray() : [],

                'is_personalized' => !empty($interests)
            ];
        })->filter()->values(); // Remove null entries and reindex
    
        return response()->json([
            'success' => true,
            'deals' => $formattedDeals,
            'debug' => [
                'interests' => $interests,
                'user_id' => $userId,
                'total_deals' => $formattedDeals->count()
            ]
        ]);
    }


    // In your API controller, you can use it like this:
    public function getPersonalizedDealsApi(Request $request)
    {
        $userId = 17;
        
        if (!$userId) {
            return response()->json(['dealheres' => []]);
        }

        // Get user interests
        $interests = $this->tracker->getUserInterests($userId);
        
        
        // Get personalized deals based on interests
        $deals = $this->tracker->getPersonalizedDeals($interests, $userId);
        
        // For debugging, you can return the debug info
        if ($request->has('debug')) {
            return response()->json($deals);
        }
        
        // For production, return only the deals
        return response()->json([
            'success' => true,
            'deals' => $deals // This is already the list of deals
        ]);
        
    }
    public function deleteUserInterestDeal(Request $request)
    {
        // dd('here');
        $user = Auth::user();
        $userId= $user->id; // or pass from request
        // dd($userId);
        // $userId = 17;
        // $dealId = $request->input('deal_id');
        $dealId = $request->deal_id;
        $interestType = $request->interest_type;
        // dd($userId,$dealId ,$interestType);
     // $dealId=10;
        $deleted = $this->tracker->deleteSpecificDeal($userId, $dealId,$interestType);
    
        return response()->json([
            'deleted' => (bool)$deleted,
            'message' => $deleted ? 'Deal removed successfully.' : 'Deal not found.'
        ]);
    }

    public function deleteTestUserInterestDeal(Request $request)
    {
        // dd('here');
        

        $userId =16;

       // or pass from request
        // dd($userId);
        // $userId = 17;
        // $dealId = $request->input('deal_id');
        $dealId = 10 ;
        $interestType = "category";
        // dd($dealId ,$interestType);
     // $dealId=10;
        $deleted = $this->tracker->deleteSpecificDeal($userId, $dealId,$interestType);
    
        return response()->json([
            'deleted' => (bool)$deleted,
            'message' => $deleted ? 'Deal removed successfully.' : 'Deal not found.'
        ]);
    }




    public function deleteAllUserInterst(){
        $userId=16;
        $deleted = $this->tracker->clearUserData($userId);
        return response()->json([
            'deleted' => (bool)$deleted,
            'message' => $deleted ? 'Deal removed successfully.' : 'Deal not found.'
        ]);
    }
    
}
