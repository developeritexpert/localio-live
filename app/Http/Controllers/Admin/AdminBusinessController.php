<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Language;
use App\Models\PricingOption;
use App\Models\PricingOptionTranslation;
use App\Models\BusinessChangeRequest;
use App\Models\ProductChangeRequest;
use App\Models\BusinessIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductPrice;
use App\Models\VendorReviewFeedback;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AdminBusinessController extends Controller
{
    //
    public function business()
    {
        $lang_id = getCurrentLanguageID();
        $siteLanguage = Language::where('id', $lang_id)->first();
        return view('Admin.business.index');
    }

    public function BusinessListingLivewire(){

        return view('Admin.business.index_listing');
    }


    public function EditBusiness($id = null){
        // dd('business Id ' , $id);

        if(isset($id) && $id != null){
            return view('Admin.business.index_edit' , compact('id'));
        }

        return view('Admin.business.index_edit');
    }

    public function DeleteBusiness($id){

        try {
            DB::beginTransaction();

            $business = Business::findOrFail($id);
            $business->countries()->detach();
            $business->languages()->detach();
            $business->features()->detach();
            $business->pricingOptions()->detach();
            $business->products()->detach();


            // Delete one-to-many related models
            $business->translations()->delete();
            $business->websites()->delete();
            $business->wishlists()->delete();
            $business->reviews()->delete();

            // Delete media files
            if ($business->icon_id && Storage::disk('public')->exists($business->icon_id)) {
                Storage::disk('public')->delete($business->icon_id);
            }

            if ($business->image_id && Storage::disk('public')->exists($business->image_id)) {
                Storage::disk('public')->delete($business->image_id);
            }

            // Delete the business
            $business->delete();

            DB::commit();

            session()->flash('success', 'Business deleted successfully.');
            return redirect()->route('business.listing.livewire');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }

    }

    public function businessWithFAQ($business_id)
    {
        return view('Admin.business.index', ['faqBusinessId' => $business_id]);
    }

    public function businessEditFAQ($faq_id)
    {
        return view('Admin.business.index', ['faqEditId' => $faq_id]);
    }

    public function businessAdd($id = null)
    {
        $lang_id = getCurrentLanguageID();
        $siteLanguage = Language::where('id', $lang_id)->first();

        if ($id != null) {
            $business_data = Business::where('id', $id)->first()->toArray();
            $business_image = Business::where('id', $id)->first(['image_id', 'icon_id']);
            return view('Admin.business.add', compact(['business_data', 'business_image']));
        } else {
            return view('Admin.business.add');
        }
    }


    // public function priceoptions(){
    //     $lang_id=getCurrentLanguageID();
    //     $price_options = PricingOption::with(['translations' => function($query) use ($lang_id) {
    //         $query->where('lang_id', $lang_id);
    //     }]) ->whereHas('translations', function($query) use ($lang_id) {
    //         $query->where('lang_id', $lang_id);
    //     })->get();

    //     return view('Admin.pricing_option.index', compact('price_options'));
    // }


    public function priceoptions(Request $request)
    {
    // Handle AJAX request
    if ($request->ajax()) {
        $lang_id = $request->lang;

        $price_options = PricingOption::with(['translations' => function ($q) use ($lang_id) {
            $q->where('lang_id', $lang_id);
        }])->get();

        // Format data for JSON
        $formatted = $price_options->map(function ($opt) {
            return [
                'id'   => $opt->id,
                'name' => $opt->translations->first()->name ?? $opt->slug,
            ];
        });

        return response()->json([
            'price_options' => $formatted,
        ]);
     }

    //  Handle normal (non-AJAX) request
    $lang_id = getCurrentLanguageID();
    $lang    = Language::find($lang_id);
    $langCode = $lang ? $lang->lang_code : null;

    $price_options = PricingOption::with(['translations' => function ($query) use ($lang_id) {
        $query->where('lang_id', $lang_id);
    }])->whereHas('translations', function ($query) use ($lang_id) {
        $query->where('lang_id', $lang_id);
    })->get();

    $countries = Language::where('status', 1)->get();

    return view('Admin.pricing_option.index', compact('price_options', 'langCode', 'countries'));
}


    public function priceoptionsAdd($id = null){
        $lang_id = getCurrentLanguageID();
        if ($id != null) {
            $pricing_data = PricingOption::with('translations')->findOrFail($id);
              return view('Admin.pricing_option.add', compact('pricing_data'));
        } else {
            return view('Admin.pricing_option.add');
        }
    }
    public function priceoptionsAddprocess(Request $request)
    {
        $pricingOptionId = $request->pricing_option_id;
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pricing_option_translations', 'name')
                    ->where('lang_id', getCurrentLanguageID())
                    ->when($pricingOptionId, fn($query) => $query->where('pricing_option_id', '!=', $pricingOptionId))
            ],
            'status' => 'nullable|in:on', // toggle sends "on" if checked
            'pricing_option_id' => 'nullable|exists:pricing_options,id',
        ]);
        $slug = Str::slug($request->name);
        // Determine if it's an update or create
        $pricingOption = $request->pricing_option_id
            ? PricingOption::findOrFail($request->pricing_option_id)
            : new PricingOption();

        // Set status
        $pricingOption->slug=$slug;
        $pricingOption->status = $request->has('status');
        $pricingOption->save();

        // Handle translation (assumes current language context is available)
        $langId = getCurrentLanguageID();// Fallback to default language ID = 1

        PricingOptionTranslation::updateOrCreate(
            ['pricing_option_id' => $pricingOption->id, 'lang_id' => $langId],
            ['name' => $request->name]
        );

        return redirect()->route('priceoptions')->with('success', 'Pricing Option saved successfully.');

    }
    public function priceoptionsremove($id){
        $lang_id = getCurrentLanguageID();
        $price_option=PricingOption::with('translations')->findOrFail($id);
        $price_option->delete();
        return redirect()->route('priceoptions')->with('success','Price Option Removed Successfully');
    }
    public function add_process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'affiliate_partner' => 'nullable|string|max:191',
            'affiliate_link' => 'nullable|url',
            'activate_all_countries' => 'required|in:0,1',
            'headquaters' => 'nullable|string|max:191',
            'year_found' => 'nullable|integer',
            'languages_supported' => 'nullable|string|max:191',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_icon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $business = Business::find($request->business_id) ?? new Business();
        $business->fill($request->only([
            'name',
            'affiliate_partner',
            'affiliate_link',
            'activate_all_countries',
            'headquaters',
            'year_found',
            'languages_supported',
        ]));

        if ($request->hasFile('image')) {
            $business->image_id = $request->file('image')->store('business_images', 'public');
        }

        if ($request->hasFile('category_icon')) {
            $business->icon_id = $request->file('category_icon')->store('business_icons', 'public');
        }

        $business->save();
        return redirect()->route('business')->with('success', $business->wasRecentlyCreated ? 'Business added successfully!' : 'Business updated successfully!');

    }

    // Offer option transalation function
    public function saveOfferTranslation(Request $request)
    {
        $request->validate([
            'offer_id' => 'required|integer|exists:pricing_options,id',
            'source_lang_id' => 'required|integer|exists:languages,id',
            'target_lang_ids' => 'required|array|min:1',
            'target_lang_ids.*' => 'integer|exists:languages,id',
        ]);

        $offerId = $request->offer_id;
        $sourceLangId = $request->source_lang_id;
        $targetLangIds = $request->target_lang_ids;

        //Get the source translation name
        $original = PricingOptionTranslation::where('pricing_option_id', $offerId)
            ->where('lang_id', $sourceLangId)
            ->first();

        if (!$original) {
            return response()->json([
                'success' => false,
                'message' => 'Original translation not found for this offer.',
            ]);
        }

        //Duplicate or update the translation
        foreach ($targetLangIds as $langId) {
            PricingOptionTranslation::updateOrCreate(
                [
                    'pricing_option_id' => $offerId,
                    'lang_id' => $langId,
                ],
                [
                    'name' => $original->name, // copy the original text
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Offer translation saved successfully!',
        ]);
    }



    public function allVendorRequest()
    {
        $lang_id = getCurrentLanguageID();

        $changeRequests = BusinessChangeRequest::with([
            'business' => function ($query) use ($lang_id) {
                $query->with(['translations' => function ($q) use ($lang_id) {
                    $q->whereIn('lang_id', [$lang_id, 1]);
                }]);
            },
            'user'
        ])
        ->where('status', 'pending')
        ->where(function ($q) use ($lang_id) {
            $q->where('lang_id', $lang_id)
            ->orWhereNull('lang_id');
        })
        ->latest()
        ->get();

        // dd($changeRequests);


        return view('Admin.vendor-changes.all_request', compact('changeRequests'));
    }




    public function handleRequest(Request $request, $id)
    {
        $change = BusinessChangeRequest::findOrFail($id);

        if ($request->action === 'approve') {
            $model = match ($change->type) {
                'business' => \App\Models\Business::find($change->business_id),
                'translation' => \App\Models\BusinessTranslation::where('business_id', $change->business_id)
                    ->where('lang_id', $change->lang_id)
                    ->first(),
                default => null,
            };

            if ($model) {
                // === Handle icon upload ===
                if ($change->field === 'icon_id') {
                    $filename = basename($change->value);
                    $pendingPath = public_path('business_icon/pending/' . $filename);
                    $finalPath = public_path('business_icon/' . $filename);
                    $relativePath = 'business_icon/' . $filename;

                    if (File::exists($pendingPath)) {
                        // Delete old icon
                        $oldPath = public_path($model->{$change->column});
                        if ($oldPath && File::exists($oldPath)) {
                            File::delete($oldPath);
                        }

                        // Move to final location
                        File::move($pendingPath, $finalPath);
                        $model->{$change->column} = $relativePath;
                    } else {
                        \Log::warning("Pending icon not found: $pendingPath");
                    }
                }

                // === Handle business images ===
                elseif ($change->field === 'business_images') {
                    $existing = $model->{$change->column} ?? [];
                    if (!is_array($existing)) {
                        $existing = json_decode($existing, true) ?? [];
                    }

                    $images = $existing;
                    $changeData = json_decode($change->value, true) ?? [];

                    switch ($change->action_type) {
                        case 'delete':
                            // Format: {"0": "path1", "2": "path2"}
                            foreach ($changeData as $index => $imagePath) {
                                $index = (int)$index;

                                // Delete the physical file
                                $fullPath = public_path($imagePath);
                                if (File::exists($fullPath)) {
                                    File::delete($fullPath);
                                }

                                // Remove from array
                                if (isset($images[$index])) {
                                    unset($images[$index]);
                                }
                            }

                            // Re-index the array to avoid gaps
                            $images = array_values($images);
                            break;

                        case 'replace':
                            // Format: {"1": {"old": "old_path", "new": "new_path"}}
                            foreach ($changeData as $index => $replaceInfo) {
                                $index = (int)$index;

                                if (!isset($replaceInfo['new'])) {
                                    continue;
                                }

                                $newImagePath = $replaceInfo['new'];
                                $filename = basename($newImagePath);
                                $pendingPath = public_path('business_gallery/pending/' . $filename);
                                $finalPath = public_path('business_gallery/' . $filename);
                                $relativePath = 'business_gallery/' . $filename;

                                if (File::exists($pendingPath)) {
                                    // Move new image to final location
                                    File::move($pendingPath, $finalPath);

                                    // Delete old image if it exists
                                    if (isset($images[$index])) {
                                        $oldImagePath = public_path($images[$index]);
                                        if (File::exists($oldImagePath)) {
                                            File::delete($oldImagePath);
                                        }
                                    }

                                    // Update the array with new image path
                                    $images[$index] = $relativePath;
                                } else {
                                    \Log::warning("Pending replacement image not found: $pendingPath");
                                }
                            }
                            break;

                        case 'add':
                            // Format: ["new_path1", "new_path2"]
                            if (is_array($changeData)) {
                                foreach ($changeData as $newImagePath) {
                                    $filename = basename($newImagePath);
                                    $pendingPath = public_path('business_gallery/pending/' . $filename);
                                    $finalPath = public_path('business_gallery/' . $filename);
                                    $relativePath = 'business_gallery/' . $filename;

                                    if (File::exists($pendingPath)) {
                                        File::move($pendingPath, $finalPath);
                                        $images[] = $relativePath;
                                    } else {
                                        \Log::warning("Pending new image not found: $pendingPath");
                                    }
                                }
                            }
                            break;

                        default:
                            \Log::warning("Unknown action_type for business_images: " . $change->action_type);
                            break;
                    }

                    // Save updated images (ensure it's properly indexed)
                    $model->{$change->column} = array_values($images);
                }

                // === Handle normal fields ===
                else {
                    $model->{$change->column} = $change->value;
                }

                $model->save();
            }

            $change->update(['status' => 'approved']);
            return back()->with('success', 'Change approved and applied.');
        }

        // === Reject Logic ===
        if ($request->action === 'reject') {
            if ($change->field === 'icon_id') {
                $filename = basename($change->value);
                $pendingPath = public_path('business_icon/pending/' . $filename);
                if (File::exists($pendingPath)) {
                    File::delete($pendingPath);
                }

            } elseif ($change->field === 'business_images') {
                $changeData = json_decode($change->value, true) ?? [];

                switch ($change->action_type) {
                    case 'delete':
                        // No pending files to delete for delete operations
                        break;

                    case 'replace':
                        // Format: {"1": {"old": "old_path", "new": "new_path"}}
                        foreach ($changeData as $index => $replaceInfo) {
                            if (isset($replaceInfo['new'])) {
                                $filename = basename($replaceInfo['new']);
                                $pendingPath = public_path('business_gallery/pending/' . $filename);
                                if (File::exists($pendingPath)) {
                                    File::delete($pendingPath);
                                }
                            }
                        }
                        break;

                    case 'add':
                        // Format: ["new_path1", "new_path2"]
                        if (is_array($changeData)) {
                            foreach ($changeData as $imagePath) {
                                $filename = basename($imagePath);
                                $pendingPath = public_path('business_gallery/pending/' . $filename);
                                if (File::exists($pendingPath)) {
                                    File::delete($pendingPath);
                                }
                            }
                        }
                        break;

                    default:
                        // Fallback for old format - try to handle as array of paths
                        if (is_array($changeData)) {
                            foreach ($changeData as $path) {
                                if (is_string($path)) {
                                    $filename = basename($path);
                                    $pendingPath = public_path('business_gallery/pending/' . $filename);
                                    if (File::exists($pendingPath)) {
                                        File::delete($pendingPath);
                                    }
                                }
                            }
                        }
                        break;
                }
            }

            $change->update(['status' => 'rejected']);
            return back()->with('info', 'Change rejected. Pending file(s) deleted.');
        }

        return back()->with('error', 'Invalid action.');
    }




    public function allVendorProductRequest(){
        $productRequests=ProductChangeRequest::with('user')->where('status','pending')->get();
        return view('Admin.vendor-changes.product_request', compact('productRequests'));
    }


    public function handleProductRequest($id, $action)
    {
        $request = ProductChangeRequest::findOrFail($id);

        if ($action === 'approve') {
            $data = json_decode($request->value, true);

            DB::transaction(function () use ($data, $request) {
                if ($request->field === 'edit_product' && isset($data['product_id'])) {
                    // 🔄 Edit existing product
                    $product = Product::findOrFail($data['product_id']);
                    $product->update([
                        'product_link' => $data['link'] ?? '',
                    ]);

                    // Update or create translation for the given lang_id
                    ProductTranslation::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'lang_id' => $data['lang_id'],
                        ],
                        [
                            'name' => $data['name'],
                            'slug' => Str::slug($data['name']),
                            'product_link' => $data['link'],
                            // 'is_affiliate' => $data['is_affiliate'] ?? false,
                        ]
                    );

                    // Optionally delete old price or just update (depends on your logic)
                    $price = $data['price'] ?? [];

                    ProductPrice::updateOrCreate(
                        ['product_id' => $product->id],
                        [
                            'price' => $price['price'] ?? 0,
                            'currency' => $price['currency'] ?? '',
                            'time_unit' => $price['time_unit'] ?? '',
                            'additional_info' => $price['additional_info'] ?? '',
                            'discount_price' => $price['discount_price'] ?? null,
                            'discount_time_units' => $price['discount_time_unit'] ?? null,
                            'discount_expiration_date' => $price['discount_expiration_date'] ?? null,
                            'renewal_price' => $price['renewal_price'] ?? null,
                            'renewal_time_units' => $price['renewal_time_unit'] ?? null,
                        ]
                    );
                } else {
                    // 🆕 Create new product
                    $product = Product::create([
                        'product_link' => $data['link'] ?? '',
                    ]);

                    ProductTranslation::create([
                        'product_id' => $product->id,
                        'lang_id' => $data['lang_id'],
                        'name' => $data['name'],
                        'slug' => Str::slug($data['name']),
                        'product_link' => $data['link'],
                        // 'is_affiliate' => $data['is_affiliate'] ?? false,
                    ]);

                    $price = $data['price'] ?? [];

                    ProductPrice::create([
                        'product_id' => $product->id,
                        'price' => $price['price'] ?? 0,
                        'currency' => $price['currency'] ?? '',
                        'time_unit' => $price['time_unit'] ?? '',
                        'additional_info' => $price['additional_info'] ?? '',
                        'discount_price' => $price['discount_price'] ?? null,
                        'discount_time_units' => $price['discount_time_unit'] ?? null,
                        'discount_expiration_date' => $price['discount_expiration_date'] ?? null,
                        'renewal_price' => $price['renewal_price'] ?? null,
                        'renewal_time_units' => $price['renewal_time_unit'] ?? null,
                    ]);
                }

                // Update request status
                $request->status = 'approved';
                $request->save();
            });

            return redirect()->back()->with('success', 'Product request approved successfully.');
        }

        if ($action === 'reject') {
            $request->status = 'rejected';
            $request->save();

            return redirect()->back()->with('info', 'Product request rejected.');
        }

        abort(404);
    }

    public function allUserReviewFeedback(){
        $vendorFeedback = VendorReviewFeedback::with('user')
        ->where('status', 'pending')
        ->latest()
        ->get();
        // dd($vendorFeedback);
        return view('Admin.vendor-changes.user_review_feedback',compact('vendorFeedback'));
    }

    public function handleReviewFeedback($id, $action){

        $feedback = VendorReviewFeedback::with('review')->findOrFail($id);

        if ($action === 'approve') {
            // Delete the associated review if it exists
            if ($feedback->review) {
                $feedback->review->delete();
            }

            $feedback->status = 'approved';
            $feedback->save();

            return back()->with('success', 'Feedback has been approved and review deleted.');
        }

        if ($action === 'reject') {
            $feedback->status = 'rejected';
            $feedback->save();

            return back()->with('success', 'Feedback has been rejected.');
        }

        return back()->with('error', 'Invalid action.');
    }

    // Added Function Business Integration
    public function editIntegration($businessId)
    {
        // Fetch the business
        $business = Business::findOrFail($businessId);

        // Fetch existing integration or null
        $integration = BusinessIntegration::where('business_id', $businessId)->first();

        return view('Admin.businessintegration.integration', compact('business', 'integration', 'businessId'));

    }

    /**
     * Update or create the Business Integration
     */
    public function updateIntegration(Request $request, $businessId)
    {
        $business = Business::findOrFail($businessId);

        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sub_heading' => 'nullable|string|max:255',
            'icon.*' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'name.*' => 'nullable|string|max:255',
            'link.*' => 'nullable|url',
        ]);

        $items = [];

        // Handle multiple icons, names, and links
        if ($request->has('name')) {
            foreach ($request->name as $index => $name) {
                $iconPath = null;

                if ($request->hasFile("icon.$index")) {
                    // Store directly in public/integration-icons
                    $file = $request->file("icon.$index");
                    $filename = time() . "_$index." . $file->getClientOriginalExtension();
                    $file->move(public_path('integration-icons'), $filename);
                    $iconPath = 'integration-icons/' . $filename;
                } else {
                    $iconPath = $request->input("existing_icon.$index");
                }

                $items[] = [
                    'icon' => $iconPath,
                    'name' => $name,
                    'link' => $request->link[$index] ?? null,
                ];
            }
        }

        // Update or create integration
        BusinessIntegration::updateOrCreate(
            ['business_id' => $business->id],
            [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'sub_heading' => $validated['sub_heading'] ?? null,
                'items' => json_encode($items),
            ]
        );

        return redirect()->back()->with('success', 'Integration updated successfully!');
    }

}
