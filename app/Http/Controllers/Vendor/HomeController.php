<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\HomeContent;
use App\Models\GetListed;
use App\Models\HowItWork;
use App\Models\PageTile;
use App\Models\Language;
use App\Models\Review;
use App\Models\UserActivity;
use App\Models\WebSetting;
// use App\Models\VendorRegisterList;
use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class HomeController extends Controller
{

    public function vendorGetListed()
    {
        $lang_code = getCurrentLocale();
        $lang_id = Language::where('lang_code', $lang_code)->value('id');
        $pageTileTranslationEducation = PageTile::where('source', 'Glimage')
        ->with('translations') // Eager load translations
        ->get();
        $getListed = GetListed::first();

        return view('Vendor.vendor_get_listed',compact('getListed','pageTileTranslationEducation'));
    }
    public function vendorProfile()
    {

        return view('vendor_dashboard.vendor_profile');
    }

    public function dash(Request $request)
    {
        $businessId = 20;

        $business = Business::with('translations', 'reviews.translations', 'products.translations')->findOrFail($businessId);
        $user_activity = UserActivity::where('business_id', $businessId)->get();

        // Grouped views for THIS MONTH
        $thisMonthViews = DB::table('user_activities')
            ->select(DB::raw('DAY(created_at) as day'), DB::raw('count(*) as total'))
            ->where('business_id', $businessId)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('day')
            ->pluck('total', 'day'); // [day => total]

        // Grouped views for LAST MONTH
        $lastMonthViews = DB::table('user_activities')
            ->select(DB::raw('DAY(created_at) as day'), DB::raw('count(*) as total'))
            ->where('business_id', $businessId)
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->groupBy('day')
            ->pluck('total', 'day');

        // Fill day values for 1 to 30
        $thisMonthData = [];
        $lastMonthData = [];
        for ($i = 1; $i <= 30; $i++) {
            $thisMonthData[] = $thisMonthViews[$i] ?? 0;
            $lastMonthData[] = $lastMonthViews[$i] ?? 0;
        }

        // Totals for stats section
        $thisMonthViewsTotal = array_sum($thisMonthData);
        $lastMonthViewsTotal = array_sum($lastMonthData);

        // Engagements based on activity_type = 'view'
        $thisMonthEngagement = DB::table('user_activities')
            ->where('business_id', $businessId)
            ->where('activity_type', 'view')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $lastMonthEngagement = DB::table('user_activities')
            ->where('business_id', $businessId)
            ->where('activity_type', 'view')
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->count();

        return view('vendor_dashboard.dash', compact(
            'business',
            'user_activity',
            'thisMonthData',
            'lastMonthData',
            'thisMonthViewsTotal',
            'lastMonthViewsTotal',
            'thisMonthEngagement',
            'lastMonthEngagement'
        ));
    }

    public function productOffer(){
        $businessId = Auth::user()->business_id; // logged-in vendor's business

        $business = Business::where('id', $businessId)
            ->with('products.translations')
            ->first();

        $products = $business ? $business->products : collect();
        // dd($products);

        return view('vendor_dashboard.product_offer', compact('products'));
    }


    public function addList(){

        return view('vendor_dashboard.add_new_list');
    }


    public function advertising(){

        return view('vendor_dashboard.advertising');
    }

    public function analytic(){
        $currentYear = now()->year;

        // Monthly Page Views
        $monthlyViews = DB::table('user_activities')
            ->where('business_id',20)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->where('activity_type', 'view')
            ->whereYear('created_at', $currentYear)

            ->groupBy('month')
            ->pluck('total', 'month');

        // Monthly Clicks (Visit Website or Free Trial)
        $monthlyClicks = DB::table('user_activities')
            ->where('business_id',20)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->where('activity_type', 'click')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month');

            $countryEngagements = DB::table('user_activities')
            ->where('business_id',20)
            ->selectRaw("
                CASE
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.country')) IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.country')) = ''
                    THEN 'Unknown'
                    ELSE JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.country'))
                END as country,
                COUNT(*) as total
            ")
            ->where('activity_type', 'engagement')
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(6)
            ->pluck('total', 'country');


        // Total Metrics
        $totalViews = DB::table('user_activities')->where('business_id',20)->count();
        $totalClicks = DB::table('user_activities')->where('activity_type', 'click')->where('business_id',20)->count();
        $wishlist = DB::table('wishlists')->where('business_id',20)->where('business_id',20)->count(); // or use engagement type

        // dd($countryEngagements);

        // dd($monthlyViews,$this->fillMissingMonths($monthlyViews));
        return view('vendor_dashboard.analytics',[
            'monthlyViews' => $this->fillMissingMonths($monthlyViews),
            'monthlyClicks' => $this->fillMissingMonths($monthlyClicks),
            'countryEngagements' => $countryEngagements,
            'totalViews' => $totalViews,
            'totalClicks' => $totalClicks,
            'wishlist' => $wishlist,
        ]);
    }

    // Ensure data for all 12 months
    private function fillMissingMonths($data)
    {
        $filled = [];
        foreach (range(1, 12) as $month) {
            $filled[] = $data[$month] ?? 0;
        }
        return $filled;
    }

    public function compaign(){

        return view('vendor_dashboard.campaign');
    }

    // public function editList(){
    //     $lang_id = getCurrentLanguageID();
    //     $businessId = 20;

    //     $business = Business::where('id', $businessId)
    //         ->whereHas('translations', function ($q) use ($lang_id) {
    //             $q->where('lang_id', $lang_id);
    //         })
    //         ->with([
    //             'translations' => fn($q) => $q->where('lang_id', $lang_id),
    //             'reviews' => fn($q) => $q->with('translations')
    //                 ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
    //                 ->where('status', 'active'),
    //             'products' => fn($q) => $q->with([
    //                 'prices',
    //                 'translations' => fn($q) => $q->where('lang_id', $lang_id),
    //             ])->where(function ($query) {
    //                 $query->where('active_all_countries', 1)
    //                       ->orWhere(function ($q) {
    //                           $q->where('active_all_countries', 0)
    //                             ->whereHas('countries', function ($countryQuery) {
    //                                 $countryQuery->where('country_id', getCurrentCountry());
    //                             });
    //                       });
    //             }),
    //             'category.translation' => fn($q) => $q->where('lang_id', $lang_id),
    //             'pricingOptions.translations' => fn($q) => $q->where('lang_id', $lang_id),
    //             'features.translations' => fn($q) => $q->where('lang_id', $lang_id),
    //             'websites' => fn($q) => $q->where('country_id', getCurrentCountry()),
    //         ])
    //         ->first();

    //     // dd($business);


    //     return view('vendor_dashboard..edit_listing',compact('business'));
    // }

    public function editList()
{
    $lang_id = getCurrentLanguageID();
    $businessId = 20;

    $user = auth()->user();

    $business = Business::where('id', $businessId)
        ->whereHas('translations', fn($q) => $q->where('lang_id', $lang_id))
        ->with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'reviews' => fn($q) => $q->with('translations')
                ->whereHas('translations', fn($q) => $q->where('language_id', $lang_id))
                ->where('status', 'active'),
            'products' => fn($q) => $q->with([
                'prices',
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
            ])->where(function ($query) {
                $query->where('active_all_countries', 1)
                      ->orWhere(function ($q) {
                          $q->where('active_all_countries', 0)
                            ->whereHas('countries', fn($q) => $q->where('country_id', getCurrentCountry()));
                      });
            }),
            'category.translation' => fn($q) => $q->where('lang_id', $lang_id),
            'pricingOptions.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'features.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'websites' => fn($q) => $q->where('country_id', getCurrentCountry()),
        ])
        ->firstOrFail();

    $default_image = WebSetting::where('key', 'user_default_image')->value('value');

    // Price
    $price = getBusinessesWithStartingPrice($business);
    $startingPrice = $price[0]['starting_price']['amount'] ?? null;
    $currency = $price[0]['starting_price']['currency'] ?? '$';
    $timeUnit = ucfirst($price[0]['starting_price']['time_unit'] ?? 'Month');
    $additional_info = $price[0]['starting_price']['additional_info'] ?? 'NA';

    // Rating averages
    $reviews = Review::where('business_id', $businessId)->get();
    $averageRating = $reviews->avg('rating') ?? 0;
    $easeOfUseAvg = round($reviews->avg('ease_of_use_rating'), 1);
    $valueForMoneyAvg = round($reviews->avg('value_for_money_rating'), 1);
    $customerServiceAvg = round($reviews->avg('customer_service_rating'), 1);
    $exclusiveFeatureAvg = round($reviews->avg('exclusive_service_rating'), 1);

    // Review breakdown
    $userRatingCounts = Review::where('business_id', $businessId)
        ->selectRaw('ROUND(rating) as rounded_rating, COUNT(DISTINCT user_id) as user_count')
        ->groupBy('rounded_rating')
        ->pluck('user_count', 'rounded_rating');

    $ratingBreakdown = collect(range(1, 5))->mapWithKeys(fn($i) => [$i => $userRatingCounts->get($i, 0)]);

    // Total active reviews
    $totalReviews = $business->reviews->where('status', 'active')->count();

    // Default link
    $link = $business->websites->first()->website_url
        ?? $business->affiliate_link
        ?? $business->permanent_url
        ?? '#';

    // dd($link);

    return view('vendor_dashboard.edit_listing', compact(
        'business',
        'default_image',
        'startingPrice',
        'currency',
        'timeUnit',
        'additional_info',
        'averageRating',
        'easeOfUseAvg',
        'valueForMoneyAvg',
        'customerServiceAvg',
        'exclusiveFeatureAvg',
        'ratingBreakdown',
        'totalReviews',
        'link'
    ));
}

    public function m_Campaign(){
        return view('vendor_dashboard.M_campaign');
    }

    public function myListing(){
        return view('vendor_dashboard.my_listing');
    }

    public function review(){
        $businessId = 20;

        $business = Business::with('translations', 'reviews.translations', 'products.translations')->findOrFail($businessId);

        return view('vendor_dashboard.review' , compact('business'));
    }

    public function reviewManagment(){

        return view('vendor_dashboard.review_managment');
    }

    public function allProduct(){
        $businessId = 20;

        $business = Business::with('translations', 'reviews.translations', 'products.translations')->findOrFail($businessId);

        return view('vendor_dashboard.product.all_product',compact('business'));
    }

    public function addProduct($locale, $product_id = null)
    {
        return view('vendor_dashboard.product.add_product', compact('product_id'));
    }

    public function businessView()
    {
        $businessId = 20;

        $business = Business::with('translations', 'reviews.translations', 'products.translations')->findOrFail($businessId);

        $user_activity = UserActivity::where('business_id', $businessId)->get();

        // Summaries
        $totalVisits = $user_activity->count();
        $totalEngagements = $user_activity->where('activity_type', 'engagement')->count();
        $avgDuration = round($user_activity->avg('duration') / 60, 1); // convert to minutes

        // Anonymous Activity
        $anonymousViews = $user_activity->whereNull('user_id')->where('activity_type', 'view')->count();
        $anonymousEngagements = $user_activity->whereNull('user_id')->where('activity_type', 'engagement')->count();

        return view('vendor_dashboard.profile_view', compact(
            'business',
            'user_activity',
            'totalVisits',
            'totalEngagements',
            'avgDuration',
            'anonymousViews',
            'anonymousEngagements'
        ));
    }
    public function vendorHelp(){

        return view('Vendor.vendor_help');
    }
    public function vendorHowItWork(){
        $lang_code = getCurrentLocale();
        $lang_id = Language::where('lang_code', $lang_code)->value('id');

        $work = HowItWork::where('lang_id', $lang_id)->first();

        $pageTileTranslationRightTool = PageTile::where('source', 'how_it_work')
            ->where('lang_id', $lang_id)
            ->with('translations')
            ->get();

        //Fetch top 3 highest rated businesses globally
        $topBusinesses = Business::with([
                'translations',
                'reviews.translations'
            ])
            ->withAvg('reviews', 'rating')
            ->get()
            ->sortByDesc('reviews_avg_rating')
            ->values()//resets the keys to 0, 1, 2
            ->take(3);
        // dd($topBusinesses);

        return view('Vendor.vendor_how_it_work', compact(
            'work',
            'pageTileTranslationRightTool',
            'topBusinesses'
        ));
    }

    //Vendor Configuration
    public function vendorConfiguration()
    {
        return view('vendor_dashboard.vendor_configuration');
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();
        if(!Hash::check($request->old_password, $user->password)) {
            return back()->withError(['old_password' => 'Current password is incorrect']);

        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password update sucessfully!');
    }



    //vendor register list  page
    public function vendorRegisterList()
    {
        return view('Vendor.vendor_register_list');
    }

    // Handle POST Request
    public function vendorRegisterListStore(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:vendor_register_lists,email',
            'password'    => 'required|string|min:6',
            'business_id' => 'required|exists:businesses,id',
        ]);


        //save vendor register list page
        $vendor = User::create([
            'first_name'  => $validated['first_name'],
            'last_name'   => $validated['last_name'],
            'email'       => $validated['email'],
            'password'    => bcrypt($validated['password']), // hash password
            'business_id' => $validated['business_id'] ?? null,
            'user_type'   => 'vendor',
            'status'      => 'pending',
        ]);


        //redirect back
        // return redirect()->route('vendor-register-list')->with('success', 'Vendor registered successfully!');

        return redirect()->route('vendor.request', ['locale' => getCurrentLocale()])->with('success', 'Vendor registered successfully!');
        }

        //Show Vendor Request
        public function showVendorRequest()
        {
            return view('Vendor.vendor_request');
        }

}


