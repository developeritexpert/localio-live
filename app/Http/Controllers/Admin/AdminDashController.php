<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
use App\Models\User;
use App\Models\WhoWeAre;
use App\Models\ExpertGuides;
use App\Models\GetListed;
use App\Models\ContactContent;
use App\Models\PageTile;
use App\Models\Language;
use App\Models\PageTileTranslation;
use App\Models\Product;
use App\Models\HelpCenterContent;
use App\Models\HelpCenterCategory;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;

// use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Services\LanguageTranslationService;
class AdminDashController extends Controller
{
    protected $translationService;

    public function __construct(LanguageTranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    public function index()
    {
        $user = User::where('user_type', 'user')->where('status', 'active')->get();
        $categories = Category::with(['businesses', 'products'])->get();
        $businesses= Business::all();
        $products =Product::all();
        return view('Admin.dashboard.index', compact('user', 'categories', 'businesses', 'products'));

    }

    public function profile()
    {
        return view('Admin.profile.index');
    }

    //update profile
    public function ProfileUpdateProcc(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id,
        ]);

        $user = User::find(Auth::user()->id);
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Your profile has been updated');
    }

    public function updatePasswordProcc(Request $request)
    {
        $request->validate(
            [
                'old_password' => 'required',
                'new_password' => 'required|confirmed|min:6',
                'new_password_confirmation' => 'required',
            ],
            [
                'old_password.required' => 'The password field is required.',
                'new_password.required' => 'The password field is required.',
                'new_password.confirmed' => 'The password confirmation does not match.',
                'new_password.min' => 'The password must be at least :min characters.',
                'new_password_confirmation.required' => 'The password confirmation field is required.',
            ]
        );

        $user = User::find(Auth::user()->id);
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
            return redirect()
                ->back()
                ->with('success', 'Password updated successfully.');
        }
        return redirect()
            ->back()
            ->with(['error' => 'The old password is incorrect.']);
    }

    public function whoWeAreContent()
    {
        $lang = getCurrentLocale();

        $redis_lang_code = Redis::get('admin_lang_code'); // Fetch lang_code from Redis
        $redis_lang = $redis_lang_code ? Language::where('lang_code', $redis_lang_code)->first() : null; // Fetch Language model if lang_code exists

        $lang_id = optional($redis_lang)->id ?? getCurrentLanguageID();

        $whoWeAre = WhoWeAre::where('lang_id', $lang_id)->first(); // Get the record to edit

        $pageTileTranslationPopular = PageTile::where('source', 'popularItem')->where('lang_id', $lang_id)
            ->with('translations') // Eager load translations
            ->get();
        // dd($pageTileTranslationPopular);
        $specilistTileTranslation = PageTile::where('source', 'specialists')->where('lang_id', $lang_id)
            ->with('translations') // Eager load translations
            ->get();

        // dd($pageTileTranslationPopular,$specilistTileTranslation);

        return view('Admin.site-content.who_we_are', compact('whoWeAre', 'pageTileTranslationPopular', 'specilistTileTranslation'));
    }

    public function MPSsectionUpdate(Request $request)
    {
        try {
            $pageTileTranslation = PageTileTranslation::find($request->id);

            if (!$pageTileTranslation) {
                return response()->json(['error' => false, 'msg' => 'Item not found.'], 404);
            }

            $pageTileTranslation->title = $request->title;
            $pageTileTranslation->description = $request->des;
            $pageTileTranslation->status = $request->input('status', 1);

            // Handle Image Upload (Only if a new image is provided)
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_popular_item.' . $image->getClientOriginalExtension();
                $image->move(public_path('front/img/'), $filename);
                $pageTileTranslation->image = 'front/img/' . $filename;
            }

            $pageTileTranslation->save();

            return response()->json(['success' => true, 'msg' => 'Popular item updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'msg' => 'Error updating item: ' . $e->getMessage()], 500);
        }
    }
    public function SpecialistUpdate(Request $request)
    {
        //    return response()->json($request->all());
        //    dd($request->all());
        try {
            $pageTileTranslation = PageTileTranslation::find($request->id);

            if (!$pageTileTranslation) {
                return response()->json(['success' => false, 'msg' => 'Item not found.'], 404);
            }

            $pageTileTranslation->title = $request->title;
            $pageTileTranslation->description = $request->desc;
            // $pageTileTranslation->type = $request->input('specialists');
            // $pageTileTranslation->source = $request->input('specialists');
            $pageTileTranslation->status = $request->input('status', 1);

            // Handle Image Upload (Only if a new image is provided)
            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $filename = time() . '_img_.' . $image->getClientOriginalExtension();
                $image->move(public_path('front/img/'), $filename);
                $pageTileTranslation->img = 'front/img/' . $filename;
            }

            if ($request->hasFile('small_img')) {
                $image = $request->file('small_img');
                $filename = time() . '_small_img_.' . $image->getClientOriginalExtension();
                $image->move(public_path('front/img/'), $filename);
                $pageTileTranslation->small_img = 'front/img/' . $filename;
            }

            $pageTileTranslation->save();

            return response()->json([
                'success' => true,
                'msg' => 'Specialist item updated successfully!',
                'image_path' => asset($pageTileTranslation->img), // Return new image URL
                'small_image_path' => asset($pageTileTranslation->small_img),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Error updating item: ' . $e->getMessage()], 500);
        }
    }

//   update WHo we are page

    public function updateWhoWeAre(Request $request)
    {
        // dd($request->all());
        // Ensure lang_id is retrieved from Redis
        $lang_id = Redis::get('admin_lang_id') ?? 1;

        // Validate incoming request
        $validatedData = $request->validate([
            'main_heading' => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:600',
            'mp_heading' => 'nullable|string|max:600',
            'mp_sub_heading' => 'nullable|string|max:600',
            'top_card_title' => 'nullable|string|max:255',
            'top_card_desc' => 'nullable|string',
            'specialists_heading' => 'nullable|string|max:255',
            'ss_heading' => 'nullable|string|max:255',
            'ss_sub_desc' => 'nullable|string',
            'protfolio_btn' => 'nullable|string|max:255',
            'meta_title' => 'required|string|max:255',
            'permanent_url'=>'nullable',
            'meta_description' => 'required|string',
            'status' => 'required|integer|in:0,1',
            'bg_top_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'top_left_section_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'top_right_section_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'top_card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'popular_items.title.*' => 'nullable|string|max:255',
            'popular_items.description.*' => 'nullable|string',
            'specialists_items.title.*' => 'nullable|string|max:255',
            'specialists_items.description.*' => 'nullable|string',
            'specialists_items.img.*' => 'nullable|string', // base64
            'specialists_items.small_img.*' => 'nullable|string', // base64

        ]);

        // Retrieve or create the WhoWeAre section by lang_id
        $whoWeAre = WhoWeAre::firstOrNew(['lang_id' => $lang_id]);
        $whoWeAre->fill(collect($validatedData)->except([
            'bg_top_img', 'top_left_section_img', 'top_right_section_img', 'top_card_image'
        ])->toArray());
        $whoWeAre->lang_id = $lang_id;

        // Handle Image Uploads
        foreach (['bg_top_img', 'top_left_section_img', 'top_right_section_img', 'top_card_image'] as $field) {
            if ($request->hasFile($field)) {
                $whoWeAre->$field = $this->uploadImage($request->file($field), $field);
            }
        }

        // Save changes
        $whoWeAre->save();

        // Process Popular Items & Specialists Items
        foreach (['popular_items' => 'popularItem', 'specialists_items' => 'specialists'] as $inputKey => $type) {
            $this->processPageTiles($request->input($inputKey, []), $type, $lang_id);
        }

        return redirect()->back()->with('success', 'Updated successfully!');
    }
    /**
     * Handles image uploads.
     */
    private function uploadImage($file, $prefix)
    {
        $filename = now()->format('YmdHis') . "_{$prefix}." . $file->getClientOriginalExtension();
        $file->move(public_path('front/img/'), $filename);
        return 'front/img/' . $filename;
    }

    /**
     * Processes page tile items.
     */
    private function processPageTiles($items, $type, $lang_id)
    {
        if (empty($items['title']) || !is_array($items['title'])) return;
        foreach ($items['title'] as $index => $title) {

            // dd(['img' => $items['img'][$index] ,
            // 'small_img' => $items['small_img'][$index] ]);


            $description = $items['description'][$index] ?? null;
            $imagePath = $this->handleBase64Image($items['image'][$index] ?? null, "{$type}_{$index}");

             // Handle all images (supports base64)
            $imgPath = $this->handleBase64Image($items['img'][$index] ?? null, "{$type}_img_{$index}");
            $smallImgPath = $this->handleBase64Image($items['small_img'][$index] ?? null, "{$type}_small_img_{$index}");

            // dd($imgPath,$smallImgPath);

            $pageTile = PageTile::create([
                'lang_id' => $lang_id,
                'image' => $imagePath,
                'type' => $type,
                'source' => $type,
                // 'img'=>$imgPath,
                // 'small_img'=>$smallImgPath,
            ]);

            $translationData = [
                'page_tile_id' => $pageTile->id,
                'title' => $title,
                'description' => $description,
                'image' => $imagePath,
                'status' => request()->input('status', 1),
            ];

            if ($type === 'specialists') {
                $translationData['img'] = $imgPath;
                $translationData['small_img'] = $smallImgPath;

            //    dd($imgPath,$smallImgPath);
            //     dd($translationData['img'], $translationData['small_img']);
            }


            PageTileTranslation::create($translationData);

        }
    }

    /**
     * Handles base64 image decoding and saving.
     */
    // private function handleBase64Image($imageData, $prefix)
    // {
    //     if (!$imageData || !preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
    //         return null;
    //     }


    //     if (!preg_match('/^data:image\/([^;]+);base64,/', $imageData, $type)) {
    //       dd("Invalid base64 image for $prefix");
    //         return null;
    //     }


    //     $extension = $type[1];
    //     $imageData = base64_decode(substr($imageData, strpos($imageData, ',') + 1));
    //     $filename = now()->format('YmdHis') . "_{$prefix}.{$extension}";
    //     file_put_contents(public_path('front/img/') . $filename, $imageData);
    //     dd($imageData);
    //     return 'front/img/' . $filename;
    // }


    private function handleBase64Image($imageData, $prefix)
    {
        // Special case: if it's already a file path (e.g., starts with 'front/img/')
        if (is_string($imageData) && str_starts_with($imageData, 'front/img/')) {
            return $imageData; // return as-is
        }

        // Base64 image handling
        if (!$imageData || !preg_match('/^data:image\/([^;]+);base64,/', $imageData, $type)) {
            \Log::warning("Invalid base64 image for {$prefix}");
            return null;
        }

        $extension = $type[1]; // e.g., svg+xml, png, jpeg
        $base64String = substr($imageData, strpos($imageData, ',') + 1);
        $decoded = base64_decode($base64String);

        if ($decoded === false) {
            \Log::error("Base64 decode failed for {$prefix}");
            return null;
        }

        $safeExtension = str_replace('+xml', '', $extension); // e.g., svg+xml → svg
        $filename = now()->format('YmdHis') . "_{$prefix}.{$safeExtension}";
        $path = public_path('front/img/') . $filename;

        file_put_contents($path, $decoded);

        return 'front/img/' . $filename;
    }



    public function deletePageTileTranslation($id)
    {
        $pageTileTranslation = PageTileTranslation::where('page_tile_id', $id)->first();
        //dd($pageTileTranslation);
        if (!$pageTileTranslation) {
            return redirect()
                ->back()
                ->with('error', 'PageTileTranslation not found.');
        }

        $pageTile = PageTile::find($pageTileTranslation->page_tile_id);
        // dd($pageTile);

        if (!$pageTile) {
            return redirect()
                ->back()
                ->with('error', 'Associated PageTile not found.');
        }

        if ($pageTileTranslation->image && file_exists(public_path($pageTileTranslation->image))) {
            unlink(public_path($pageTileTranslation->image));
        }

        if ($pageTileTranslation->img && file_exists(public_path($pageTileTranslation->img))) {
            unlink(public_path($pageTileTranslation->img));
        }

        if ($pageTileTranslation->small_img && file_exists(public_path($pageTileTranslation->small_img))) {
            unlink(public_path($pageTileTranslation->small_img));
        }

        // Delete the PageTileTranslation record

        $pageTileTranslation->delete();
        $pageTile->delete();
        return redirect()
            ->back()
            ->with('success', 'Item and its associated page tile deleted successfully!');
    }
//   update WHo we are page


public function ExpertGuide()
{
    $lang = getCurrentLocale();

    $redis_lang_code = Redis::get('admin_lang_code'); // Fetch lang_code from Redis
    $redis_lang = $redis_lang_code ? Language::where('lang_code', $redis_lang_code)->first() : null; // Fetch Language model if lang_code exists

    $lang_id = optional($redis_lang)->id ?? getCurrentLanguageID();

    // Create translations if they don't exist
    $this->ensureTranslationsExist($lang_id);

    // Now fetch the data (which should exist now)
    $expertGuide = ExpertGuides::where('lang_id', $lang_id)->first();
    $pageTileTranslationEducation = PageTile::where('source', 'educationItem')->where('lang_id', $lang_id)
        ->with('translations') // Eager load translations
        ->get();
    $pageTileTranslationRightTools = PageTile::where('source', 'righttools')->where('lang_id', $lang_id)
        ->with('translations') // Eager load translations
        ->get();

    return view('Admin.site-content.experts_guides', compact('expertGuide', 'pageTileTranslationEducation','pageTileTranslationRightTools'));
}

/**
 * Ensure translations exist for all required models
 *
 * @param int $langId Current language ID
 * @return void
 */
protected function ensureTranslationsExist(int $langId): void
{
    // Skip for default language
    if ($langId === 1) {
        return;
    }

    // Define models and their translatable fields
    $modelsToTranslate = [
        [
            'model' => \App\Models\ExpertGuides::class,
            'fields' => ['title', 'description', 'meta_title', 'meta_description']
        ]
    ];

    // Create translations for main models
    $this->translationService->createMultipleTranslations($modelsToTranslate, $langId);

    // Define relationships for PageTile and their translations
    $pageTileRelations = [
        'parent_fields' => ['title', 'description'],
        'related' => [
            [
                'relation' => 'translations',
                'model' => \App\Models\PageTileTranslation::class,
                'fields' => ['content', 'title', 'description'],
                'foreign_key' => 'page_tile_id'
            ]
        ]
    ];

    // Create PageTile translations for educationItem
    $this->translationService->createRelatedTranslations(
        \App\Models\PageTile::class,
        $pageTileRelations,
        $langId
    );
}
    public function ESsectionUpdate(Request $request)
    {
        //return response()->json($request->all());
        try {
            $pageTileTranslation = PageTileTranslation::find($request->id);

            // return response()->json($pageTileTranslation);

            if (!$pageTileTranslation) {
                return response()->json(['error' => false, 'msg' => 'Item not found.']);
            }

            $pageTileTranslation->title = $request->title;
            $pageTileTranslation->description = $request->description;
            $pageTileTranslation->status = $request->input('status', 1);
            // Handle image upload

            // Handle Image Upload (Only if a new image is provided)
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_education_item_.' . $image->getClientOriginalExtension();
                $image->move(public_path('front/img/'), $filename);
                $pageTileTranslation->image = 'front/img/' . $filename;
            }

            $pageTileTranslation->save();

            return response()->json(['success' => true, 'msg' => 'Popular item updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'msg' => 'Error updating item: ' . $e->getMessage()], 500);
        }
    }

    public function RTsectionUpdate(Request $request)
    {
        //return response()->json($request->all());
        try {
            $pageTileTranslation = PageTileTranslation::find($request->id);

            // return response()->json($pageTileTranslation);

            if (!$pageTileTranslation) {
                return response()->json(['error' => false, 'msg' => 'Item not found.']);
            }

            $pageTileTranslation->title = $request->title;
            $pageTileTranslation->description = $request->description;
            $pageTileTranslation->status = $request->input('status', 1);
            // Handle image upload

            // Handle Image Upload (Only if a new image is provided)
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_right_tools_.' . $image->getClientOriginalExtension();
                $image->move(public_path('front/img/'), $filename);
                $pageTileTranslation->image = 'front/img/' . $filename;
            }

            $pageTileTranslation->save();

            return response()->json(['success' => true, 'msg' => 'Popular item updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'msg' => 'Error updating item: ' . $e->getMessage()], 500);
        }
    }


    // expert guide update
    public function ExperGuideUpdate(Request $request)
    {
        // Ensure lang_id is retrieved from Redis
        $lang_id = Redis::get('admin_lang_id') ?? 1;

        // Validate incoming request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'education_title' => 'required|string|max:255',
            'education_description' => 'required|string',
            'smart_search' => 'required|string|max:255',
            'smart_search_description' => 'required|string',
            'how_to_check_email' => 'required|string',
            'overview' => 'required|string|max:255',
            'email_description' => 'nullable|string',
            'webmail' => 'required|string|max:255',
            'webmail_description' => 'nullable|string',
            'email_application' => 'required|string|max:255',
            'email_app_description' => 'nullable|string',
            'imap' => 'required|string|max:255',
            'imap_pop' => 'nullable|string',
            'right_tool_heading' => 'nullable|string',
            'get_start_button' => 'nullable|string',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            'assistant' => 'nullable|string',
            'permanent_url'=>'nullable',
        ]);

        // Retrieve or create the Expert Guide by lang_id
        $expertGuide = ExpertGuides::firstOrNew(['lang_id' => $lang_id]);
        $expertGuide->fill($validatedData);
        $expertGuide->lang_id = $lang_id;

        // Save changes
        $expertGuide->save();

        // Process Education Items & Right Tools
        foreach (['education_items' => 'educationItem', 'right_tools' => 'righttools'] as $inputKey => $type) {
            $this->processPageTiles($request->input($inputKey, []), $type, $lang_id);
        }

        return redirect()->back()->with('success', 'Updated successfully!');
    }

         // expert guide update

    protected function handleEducationFileUpload(Request $request, $pageTile)
    {

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_education_item_.' . $file->getClientOriginalExtension();
            $file->move(public_path('front/img/'), $filename);
            $pageTile->image = 'front/img/' . $filename;
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_right_tool_.' . $file->getClientOriginalExtension();
            $file->move(public_path('front/img/'), $filename);
            $pageTile->image = 'front/img/' . $filename;
        }
    }

    public function RightToolItemUpdate(Request $request){

        try {
            $pageTileTranslation = PageTileTranslation::find($request->id);

            // return response()->json($pageTileTranslation);

            if (!$pageTileTranslation) {
                return response()->json(['error' => false, 'msg' => 'Item not found.']);
            }

            $pageTileTranslation->title = $request->title;
            $pageTileTranslation->description = $request->description;
            $pageTileTranslation->status = $request->input('status', 1);
            // Handle image upload

            // Handle Image Upload (Only if a new image is provided)
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_right_tools_item_.' . $image->getClientOriginalExtension();
                $image->move(public_path('front/img/'), $filename);
                $pageTileTranslation->image = 'front/img/' . $filename;
            }

            $pageTileTranslation->save();

            return response()->json(['success' => true, 'msg' => 'Popular item updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'msg' => 'Error updating item: ' . $e->getMessage()], 500);
        }

    }
    public function Contact(){

    $lang = getCurrentLocale();

    $redis_lang_code = Redis::get('admin_lang_code');// Fetch lang_code from Redis
    $redis_lang = $redis_lang_code ? Language::where('lang_code', $redis_lang_code)->first() : null; // Fetch Language model if lang_code exists

    $lang_id = optional($redis_lang)->id ?? getCurrentLanguageID();

        $contact = ContactContent::where('lang_id', $lang_id)->first();
        $pageTileTranslationRightTool = PageTile::where('source', 'right_tool_item')->where('lang_id', $lang_id)
        ->with('translations') // Eager load translations
        ->get();
        return view('Admin.site-content.contact2',compact('contact','pageTileTranslationRightTool'));
    }
    // contract update
    public function ContactUpdate(Request $request)
    {
        // dd($request->all());
        // Ensure lang_id is retrieved from Redis
        $lang_id = Redis::get('admin_lang_id') ?? 1;

        // Validate incoming request
        $validatedData = $request->validate([
            'contact_heading' => 'required|string|max:255',
            'contact_description' => 'nullable|string',
            'image_first' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_second' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'footer_heading' => 'required|string|max:255',
            'g_button' => 'required|string|max:255',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            'permanent_url'=>'nullable'
        ]);

        // Retrieve or create the Contact Content by lang_id
        $contact = ContactContent::firstOrNew(['lang_id' => $lang_id]);
        $contact->fill(collect($validatedData)->except(['image_first', 'image_second'])->toArray());
        $contact->lang_id = $lang_id;

        // Handle Image Uploads
        foreach (['image_first', 'image_second'] as $field) {
            if ($request->hasFile($field)) {
                $contact->$field = $this->uploadImage($request->file($field), $field);
            }
        }

        // Save Contact Content
        $contact->save();

        // Process Right Tools Items
        $this->processPageTiles($request->input('right_tool', []), 'right_tool_item', $lang_id);

        return redirect()->back()->with('success', 'Contact content updated successfully.');
    }
    // contract update
    protected function handleRightFileUpload(Request $request, $pageTile)
    {

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_right_tools_item_.' . $file->getClientOriginalExtension();
            $file->move(public_path('front/img/'), $filename);
            $pageTile->image = 'front/img/' . $filename;
        }
    }

    public function helpCenter(){
        $lang = getCurrentLocale();

        $redis_lang_code = Redis::get('admin_lang_code'); // Fetch lang_code from Redis
        $redis_lang = $redis_lang_code ? Language::where('lang_code', $redis_lang_code)->first() : null;

        $lang_id = optional($redis_lang)->id ?? getCurrentLanguageID();

        $help = HelpCenterContent::where('lang_id', $lang_id)
            ->with('categories')
            ->first();
        // dd($help);

        return view('Admin.site-content.help_center_page', compact('help'));
    }


    public function HelpCenterUpdate(Request $request)
    {
        $lang_id = Redis::get('admin_lang_id') ?? 1;
        $content = HelpCenterContent::firstOrNew(['lang_id' => $lang_id]);
        $isUpdate = $content->exists;

        $validatedData = $request->validate([
            'banner_headline' => 'required|string|max:255',
            'banner_description' => 'required|string',
            'banner_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'main_heading' => 'required|string|max:255',
            'left_section_title' => 'required|string|max:255',
            'left_section_description' => 'required|string',
            'faq_section_title' => 'required|string|max:255',
            'faq_section_description' => 'required|string',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:255',
            'permanent_url' => 'nullable|string|max:255',
            'knowledge_base_title' => 'required|string|max:255',
            'knowledge_base_description' => 'required|string',
            'knowledge_base_image' => ($isUpdate ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'help_center_title' => 'required|string|max:255',
            'help_center_description' => 'required|string',
            'help_center_image' => ($isUpdate ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Fill content except image fields
        $content->fill(collect($validatedData)->except([
            'banner_img',
            'knowledge_base_image',
            'help_center_image'
        ])->toArray());

        $content->lang_id = $lang_id;

        // Upload images with separate folders to avoid overwrite
        if ($request->hasFile('banner_img')) {
            $content->banner_img = $this->uploadImage($request->file('banner_img'), 'help_banner');
        }

        if ($request->hasFile('knowledge_base_image')) {
            $content->knowledge_base_image = $this->uploadImage($request->file('knowledge_base_image'), 'knowledge_base');
        }

        if ($request->hasFile('help_center_image')) {
            $content->help_center_image = $this->uploadImage($request->file('help_center_image'), 'help_center');
        }

        $content->save();

        if ($request->has('categories')) {
            $this->processHelpCenterCategories($request->input('categories'), $request->file('categories.image', []), $content->id, false);
        }

        if ($request->has('RTT') && !empty($request->input('RTT.title'))) {
            $this->processHelpCenterCategories($request->input('RTT'), $request->file('RTT.image'), $content->id, true);
        }

        return redirect()->back()->with('success', 'Help center content updated successfully.');
    }


    public function updateHelpCenterCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:help_center_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = HelpCenterCategory::findOrFail($request->id);
            $category->title = $request->title;
            $category->description = $request->description;

            if ($request->hasFile('image')) {
                // Handle image upload
                $imagePath = $request->file('image')->store('help-center-categories', 'public');
                $category->image = 'storage/' . $imagePath;
            }

            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Category update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    protected function processHelpCenterCategories($categoriesData, $imageFiles = null, int $help_center_id, bool $isSingleCategory = false)
    {
        if ($isSingleCategory) {
            // Handle single category case (RTT)
            // Skip if we don't have a title
            if (empty($categoriesData['title'])) {
                return;
            }

            $categoryId = !empty($categoriesData['id']) ? $categoriesData['id'] : null;

            $category = $categoryId
                ? HelpCenterCategory::find($categoryId)
                : null;

            // If category doesn't exist, create a new one
            if (!$category) {
                $category = new HelpCenterCategory();
            }

            $category->help_center_content_id = $help_center_id;
            $category->title = $categoriesData['title'];
            $category->description = $categoriesData['description'] ?? '';

            // Handle image - either base64 or file upload
            if (!empty($categoriesData['image']) && is_string($categoriesData['image']) && str_starts_with($categoriesData['image'], 'data:image')) {
                $category->image = $this->handleBase64Image($categoriesData['image'], 'category_single');
            } elseif (!empty($imageFiles)) {
                $category->image = $this->uploadImage($imageFiles, 'category_single');
            }

            $category->save();
        } else {
            // Handle multiple categories case
            if (empty($categoriesData['title']) || !is_array($categoriesData['title'])) {
                return;
            }

            foreach ($categoriesData['title'] as $index => $title) {
                // Skip empty titles
                if (empty($title)) {
                    continue;
                }

                $categoryId = !empty($categoriesData['id'][$index]) ? $categoriesData['id'][$index] : null;

                $category = $categoryId
                    ? HelpCenterCategory::find($categoryId)
                    : null;

                // If category doesn't exist, create a new one
                if (!$category) {
                    $category = new HelpCenterCategory();
                }

                $category->help_center_content_id = $help_center_id;
                $category->title = $title;
                $category->description = $categoriesData['description'][$index] ?? '';

                // Handle image - either base64 or file upload
                if (!empty($categoriesData['image'][$index]) && is_string($categoriesData['image'][$index]) && str_starts_with($categoriesData['image'][$index], 'data:image')) {
                    $category->image = $this->handleBase64Image($categoriesData['image'][$index], 'category_' . $index);
                } elseif (!empty($imageFiles[$index])) {
                    $category->image = $this->uploadImage($imageFiles[$index], 'category_upload_' . $index);
                }

                $category->save();
            }
        }
    }


    public function deleteHomeCenterCategory($id)
    {
        $category = HelpCenterCategory::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }




}
