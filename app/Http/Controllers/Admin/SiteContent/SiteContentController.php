<?php

namespace App\Http\Controllers\Admin\SiteContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeaderContent;
use Illuminate\Support\Facades\File;
use App\Models\FooterContent;
use App\Models\HomeContent;
use App\Models\HomeContentMedia;
use App\Models\CategoryPageContent;
use App\Models\TopProductContent;
use App\Models\SiteLanguages;
use App\Models\Language;
use Illuminate\Support\Facades\Redis;
use Session;

class SiteContentController extends Controller
{
    public function homeContent()
    {
        $lang = getCurrentLocale();
        $redis_lang_code = Redis::get('admin_lang_code');
        $redis_lang =Language::where('lang_code',$redis_lang_code)->first();
        $lang_id = $redis_lang ? $redis_lang->id : getCurrentLanguageID();

        if (session()->has('lang_code')) {
            $lang = session()->get('lang_code');
        }
        $allHomeContents = homeContent::where('lang_id', $lang_id)->get();
        // if ($allHomeContents->isEmpty()) {
        //     $englishHomeContents = homeContent::where('lang_id', 1)->get();
        //     foreach ($englishHomeContents as $content) {
        //         $newHomeContent = new homeContent;
        //         $newHomeContent->meta_key = $content->meta_key;
        //         $newHomeContent->meta_value = $content->meta_value;
        //         $newHomeContent->lang_id = $lang_id;
        //         $newHomeContent->type = $content->type;
        //         $newHomeContent->save();
        //     }
        //     $allHomeContents = homeContent::where('lang_id', $lang_id)->get();
        // }
        $homeContents = HomeContent::where('type', 'file')->Where('lang_id', $lang_id)->get();
        // dd($allHomeContents,$homeContents);
        return view('Admin.site-content.home_page', compact('allHomeContents', 'homeContents'));
    }
    public function updateLangFile(Request $request)
    {
        $textVal = $request->data['textVal'];
        $textID = $request->data['textID'];
        $admin_lang = getCurrentLocale();

        $filepath = base_path('resources/lang/' . $admin_lang . '/home.php');

        if (!File::exists($filepath)) {
            return response()->json(['error' => 'Language file not found.'], 404);
        }

        $content = File::get($filepath);

        $pattern = "/'$textID'\s*=>\s*'(.*?)'/";
        $replacement = "'$textID' => '" . addslashes($textVal) . "'";


        if (!preg_match($pattern, $content)) {

            $content = preg_replace("/\];/", "    '$textID' => '" . addslashes($textVal) . "',\n];", $content);
        } else {

            $content = preg_replace($pattern, $replacement, $content);
        }

        File::put($filepath, $content);

        return response()->json(['success' => 'Localization updated successfully.'], 200);
    }

    public function homeContentUpdate(Request $request)
{
    // Handle image uploads (remains unchanged)
    // $this->uploadImages($request, 'ai_send_img');
    // $this->uploadImages($request, 'header_image');
    // $this->uploadImages($request, 'header_backgound_image');
    // $this->uploadImages($request, 'ai_left_image');
    // $this->uploadImages($request, 'ai_right_image');
    // $this->uploadImages($request, 'review_section_right_img');
    // $this->uploadImages($request, 'review_section_left_img');
    // $this->uploadImages($request, 'find_tool_left_image');
    // $this->uploadImages($request, 'find_tool_right_image');
    // $this->uploadImages($request, 'verified_reviews_image');
    // $this->uploadImages($request, 'feature_price_image');
    // $this->uploadImages($request, 'independ_image');

    $this->uploadImages($request, 'ai_send_img', 'home/ai' );
    $this->uploadImages($request, 'header_image', 'home/header' );
    $this->uploadImages($request, 'header_backgound_image', 'home/header/background' );
    $this->uploadImages($request, 'ai_left_image', 'home/ai/left' );
    $this->uploadImages($request, 'ai_right_image', 'home/ai/right' );
    $this->uploadImages($request, 'review_section_right_img', 'home/review/right' );
    $this->uploadImages($request, 'review_section_left_img', 'home/review/left' );
    $this->uploadImages($request, 'find_tool_left_image', 'home/find' );
    $this->uploadImages($request, 'find_tool_right_image', 'home/find' );
    $this->uploadImages($request, 'verified_reviews_image', 'home/review' );
    $this->uploadImages($request, 'feature_price_image', 'home' );
    $this->uploadImages($request, 'independ_image', 'home' );
    $this->uploadBrandImages($request);

    // Handle text fields (same as before)
    $textFields = [
        'meta_home_title',
        'Meta_home_description',
        'header_title',
        'header_description',
        'placeholder_text',
        'trusted_brand',
        'most_popular',
        'campare_business',
        'visit_website',
        'exclusive_deals',
        'all_exclusive',
        'get_this_deal',
        'ai_title',
        'ai_description',
        'ai_placeholder',
        'top_product',
        'all_top_product',
        'latest_reviews',
        'write_review',
        'read_article',
        'view_all_article',
        'find_tool',
        'verify_user_review',
        'verify_review_description',
        'feature_price',
        'feature_price_description',
        'independent',
        'independent_description',
        'get_button_lable'
    ];

    // Loop through text fields and update them
    foreach ($textFields as $field) {
        if ($request->has($field) && is_array($request->get($field))) {
            $data = $request->get($field);
            foreach ($data as $id => $value) {
                // Ensure the ID is valid
                if (!empty($id) && is_numeric($id)) {
                    HomeContent::where('id', $id)->update(['meta_value' => $value]);
                }
            }
        }
    }

    // Handle the permanent_url field
    if ($request->has('permanent_url') && !empty($request->get('permanent_url'))) {
        $langId = $request->get('language'); // Assuming the language is sent in the request
        // dd($langId);
        // Check if a record exists for the given lang_id and meta_key
        $homeContent = HomeContent::where('meta_key', 'permanent_url')
                                  ->where('lang_id', $langId)
                                  ->first();
                                //   dd($homeContent);
        if ($homeContent) {
            // Update if it exists
            $homeContent->meta_value = $request->get('permanent_url');
            $homeContent->save();
        } else {
            // Create if it does not exist
            HomeContent::create([
                'meta_key' => 'permanent_url',
                'meta_value' => $request->get('permanent_url'),
                'lang_id' => $langId
            ]);
        }
    }
    return redirect()->back()->with('success', 'Home content updated successfully.');
}


    private function uploadImages(Request $request, $imageField , $path)
    {
        if ($request->hasFile($imageField) && is_array($request->file($imageField))) {
            foreach ($request->file($imageField) as $id => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($path), $fileName);

                $homeContent = HomeContent::find($id);
                if ($homeContent) {
                    $homeContent->update([
                        'meta_value' => $path . '/' . $fileName,
                    ]);
                }
            }
        }
    }

    private function uploadBrandImages(Request $request)
    {
        if ($request->has('brand_image')) {
            foreach ($request->brand_image as $contentId => $files) {
                $content = HomeContent::find($contentId);
                if ($content && !empty($files)) {
                    $imageIds = [];
                    foreach ($files as $file) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('home/brand_image'), $fileName);

                        $media = new HomeContentMedia;
                        $media->home_content_id = $content->id;
                        $media->file_path = 'home/brand_image/' . $fileName;
                        $media->file_name = $fileName;
                        $media->save();

                        $imageIds[] = $media->id;
                    }

                    // Update with the image IDs
                    $content->update([
                        'meta_value' => json_encode($imageIds),
                    ]);
                }
            }
        }
    }

    public function headerPage()
    {
        // $headerContents = HeaderContent::all();
        $lang = getCurrentLocale();
        if (session()->has('lang_code')) {
            $lang = session()->get('lang_code');
        }
        $lang_id = getCurrentLanguageID();
        $headerContents = HeaderContent::where('lang_id', $lang_id)->get();

        if ($headerContents->isEmpty()) {
            $englishHeaderContents = HeaderContent::where('lang_id', 1)->get();
            if ($englishHeaderContents) {
                foreach ($englishHeaderContents as $content) {
                    $HeaderContent = new HeaderContent;
                    $HeaderContent->meta_key = $content->meta_key;
                    $HeaderContent->meta_value = $content->meta_value;
                    $HeaderContent->lang_id = $lang_id;
                    $HeaderContent->type = $content->type;
                    $HeaderContent->save();
                }
            }
            $headerContents = HeaderContent::where('lang_id', $lang_id)->get();
        }
        // dd($headerContents);
       $headerLogo = HeaderContent::Where([['lang_id', $lang_id],['type','file'],['meta_key','header_logo']])->first();
       $favicon = \App\Models\HeaderContent::where([
        ['lang_id', $lang_id],
        ['type', 'file']
        ])->where('meta_key', 'favicon_icon')->first();
        //  dd($favicon);
if (!$headerLogo) {
    $headerLogo = HeaderContent::Where('lang_id', 1)->first();
}
        return view('Admin.site-content.header_page', compact('headerContents', 'headerLogo','favicon'));
    }
    // public function headerContentUpdate(Request $request)
    // {
    //         dd($request->all());
    //         $lang_id = getCurrentLanguageID();

    //     $this->updateHeaderMetaValues($request, 'header_logo');
    //     $this->updateHeaderMetaValues($request, 'favicon_icon');
    //     $textFields = [
    //         'header_search_placeholder',
    //         'login_btn_lable',
    //         'sign_up_btn_lable',
    //         'sign_out_btn_lable',
    //         'exclusive',
    //         'categories',
    //         'top_rated_product',
    //         'expert_guide',
    //         'help_center',
    //         'header_logo',
    //         'code_at_beginning_of_head_tag',
    //         'code_at_end_of_head_tag'
    //     ];

    //     foreach ($textFields as $field) {
    //         if ($request->has($field) && is_array($request->get($field))) {
    //             $data = $request->get($field);

    //             foreach ($data as $id => $val) {
    //                 $headerContent = HeaderContent::find($id);
    //                 if ($headerContent) {
    //                     $headerContent->update([
    //                         'meta_value' => $val,  // Update the meta_value for the respective content
    //                     ]);
    //                 }
    //                 else{
    //                     HeaderContent::create([
    //                         'meta_key' => $field, // Make sure this matches your key
    //                         'meta_value' => $val,
    //                         'language' => $lang_id,
    //                         'type' =>'text'
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    //     return redirect()->back()->with('success', 'Header content updated successfully.');
    // }

    public function headerContentUpdate(Request $request)
    {
        $lang_id = getCurrentLanguageID();

        // $this->updateHeaderMetaValues($request, 'header_logo');
        // $this->updateHeaderMetaValues($request, 'favicon_icon');

        $this->updateHeaderMetaValues($request, 'header_logo' , 'header/logo');
        $this->updateHeaderMetaValues($request, 'favicon_icon', 'header/icon');

        $textFields = [
            'header_search_placeholder',
            'login_btn_lable',
            'sign_up_btn_lable',
            'sign_out_btn_lable',
            'exclusive',
            'categories',
            'top_rated_product',
            'expert_guide',
            'help_center',
            'header_logo',
            'code_at_beginning_of_head_tag',
            'code_at_end_of_head_tag'
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                $data = $request->get($field);

                if (is_array($data)) {
                    foreach ($data as $id => $val) {
                        if ($id !== 'new' && is_numeric($id)) {
                            $headerContent = HeaderContent::find($id);
                            if ($headerContent) {
                                $headerContent->update([
                                    'meta_value' => $val,
                                ]);
                            }
                        } else {
                            HeaderContent::create([
                                'meta_key' => $field,
                                'meta_value' => $val,
                                'language' => $lang_id,
                                'type' => 'text'
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Header content updated successfully.');
    }

    private function updateHeaderMetaValues(Request $request, $imageField , $path)
    {
        $lang_id = getCurrentLanguageID();
        if ($request->hasFile($imageField) && is_array($request->file($imageField))) {
            foreach ($request->file($imageField) as $id => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($path), $fileName);

                $homeContent = HeaderContent::find($id);
                if ($homeContent) {
                    $homeContent->update([
                        'meta_value' => $path . '/' . $fileName,
                    ]);
                }
                else {
                    // Create new entry if not found
                    HeaderContent::create([
                        'meta_key' => $imageField, // Make sure this matches your key
                        'meta_value' => $fileName . '/' . $fileName,
                        'language' => $lang_id,
                        'type' =>'file'
                    ]);
                }
            }
        }
    }

    public function footerPage()
    {
        $lang = getCurrentLocale();
        $lang_id = getCurrentLanguageID();

        if (session()->has('lang_code')) {
            $lang = session()->get('lang_code');
        }
        $footerContents = FooterContent::where('lang_id', $lang_id)->get();
        // dd($footerContents);
        if ($footerContents->isEmpty()) {
            $englishFooterContents = FooterContent::where('lang_id', 1)->get();
            if ($englishFooterContents) {
                foreach ($englishFooterContents as $content) {
                    $footerContents = new FooterContent;
                    $footerContents->meta_key = $content->meta_key;
                    $footerContents->meta_value = $content->meta_value;
                    $footerContents->lang_id = $lang_id;
                    $footerContents->type = $content->type;
                    $footerContents->save();
                }
            }
            // $footerContents = FooterContent::where('lang_id', $lang_id)->get();
        }
        $footerFiles = FooterContent::where('type', 'file')->orWhere('type', 'url')->Where('lang_id', $lang_id)->get();
        //Add this associative array for Blade use
        $footerFilesAssoc = $footerFiles->keyBy('meta_key');
        $footerLogo = FooterContent::where('meta_key', 'footer_logo')->Where('lang_id', $lang_id)->first();
        //Send $expectedFields to blade
        $expectedFields = [
            'facebook_icon',
            'instagram_icon',
            'twitter_icon',
            'pinterest_icon',
            'facebook_url',
            'instagram_url',
            'twitter_url',
            'pinterest_url',
            // Add any more as needed
        ];
            //  dd($footerContents);
        return view('Admin.site-content.footer_page', compact('footerContents', 'footerFiles', 'footerLogo'));
    }
    public function footerPageUpdate(Request $request)
{
    // Upload images for footer icons
    $this->uploadImagesFooter($request, 'footer_logo' , 'footer/logo');
    $this->uploadImagesFooter($request, 'facebook_icon' , 'footer/icon/facebook');
    $this->uploadImagesFooter($request, 'instagram_icon' , 'footer/icon/instagram');
    $this->uploadImagesFooter($request, 'twitter_icon' , 'footer/icon/twitter');
    $this->uploadImagesFooter($request, 'pinterest_icon' , 'footer/icon/pinterest');

    // $this->uploadImagesFooter($request, 'footer/logo');
    // $this->uploadImagesFooter($request, 'footer/icon/facebook');
    // $this->uploadImagesFooter($request, 'footer/icon/instagram');
    // $this->uploadImagesFooter($request, 'footer/icon/twitter');

    $textFields = [
      'discover',
                'categories',
                'top_rated_product',
                'exclusive_deal',
                'company',
                'who_we_are',
                'privacy_policy',
                'terms_and_conditions',
                'vendors',
                'get_listed',
                'vendor_login',
                'help',
                'expert_guides',
                'help_center',
                'contact',
                'follow_us',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'facebook',
                'instagram',
                'twitter',
                'code_at_beginning_of_footer_tag',
                'code_at_end_of_footer_tag',
                'footer_copyright_text'
    ];

    $lang_id = getCurrentLanguageID();

    foreach ($textFields as $field) {
        if ($request->has($field)) {
            $data = $request->get($field);

            foreach ($data as $key => $val) {
                if (!empty($val)) {
                    // Check if a record already exists for the specific field and language
                    $footerContent = FooterContent::where('meta_key', $field)
                        ->where('lang_id', $lang_id)
                        ->first();

                    if ($footerContent) {
                        // Update existing record
                        $footerContent->update([
                            'meta_value' => $val,
                        ]);
                    } else {
                        // Create new record if not found
                        FooterContent::create([
                            'meta_key'   => $field,
                            'meta_value' => $val,
                            'lang_id'    => $lang_id,
                        ]);
                    }
                }
            }
        }
    }

    return redirect()->back()->with('success', 'Footer content updated successfully.');
}

    private function uploadImagesFooter(Request $request, $imageField, $path)
    {
        if ($request->hasFile($imageField)) {
            foreach ($request->file($imageField) as $id => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($path), $fileName);

                // $homeContent = FooterContent::find($id);
                // if ($homeContent) {
                //     $homeContent->update([
                //         'meta_value' => $path . '/' . $fileName,
                //     ]);
                // }

            //Try finding existing entry by ID
            $footerContent = is_numeric($id) ? FooterContent::find($id) : null;

            if ($footerContent) {
                // Update existing record
                $footerContent->update([
                    'meta_value' => $path . '/' . $fileName,
                    'type' => 'file',
                ]);
            } else {
                // Create new record if it doesn't exist
                FooterContent::create([
                    'meta_key'   => $imageField,
                    'meta_value' => $path . '/' . $fileName,
                    'lang_id'    => getCurrentLanguageID(),
                    'type'       => 'file',
                ]);
              }
           }
        }
    }

    public function categoriesPage()
    {
        $lang = getCurrentLocale();
        $lang_id = getCurrentLanguageID();
        if (session()->has('lang_code')) {
            $lang = session()->get('lang_code');
        }
        $categoryPageContents = CategoryPageContent::where('lang_id', $lang_id)->get();
        if ($categoryPageContents->isEmpty()) {
            $englishCategoryPageContents = CategoryPageContent::where('lang_id', 1)->get();
            if ($englishCategoryPageContents) {
                foreach ($englishCategoryPageContents as $content) {
                    $categoryContents = new CategoryPageContent;
                    $categoryContents->meta_key = $content->meta_key;
                    $categoryContents->meta_value = $content->meta_value;
                    $categoryContents->lang_id = $lang_id;
                    $categoryContents->type = $content->type;
                    $categoryContents->save();
                }
            }
            $categoryPageContents = CategoryPageContent::where('lang_id', $lang_id)->get();
        }
        return view('Admin.site-content.categories_page', compact('categoryPageContents'));
    }
    public function categoryPageContentUpdate(Request $request)
    {

        $this->uploadImagesCategoryPage($request, 'category_header_image');
        $this->uploadImagesCategoryPage($request, 'category_background_image');
        $textFields = [
            'heading',
            'description',
            'search_placeholder_text',
            'main_heading',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                $datas = $request->get($field);
                foreach ($datas as $id => $val) {
                    $categoryContent = CategoryPageContent::find($id);
                    if ($categoryContent) {
                        $categoryContent->update([
                            'meta_value' => $val,
                        ]);
                    }
                }
            }
        }
        return redirect()->back()->with('success', 'Category content updated successfully.');
    }
    private function uploadImagesCategoryPage(Request $request, $imageField)
    {
        if ($request->hasFile($imageField)) {
            foreach ($request->file($imageField) as $id => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($imageField), $fileName);

                $homeContent = CategoryPageContent::find($id);
                if ($homeContent) {
                    $homeContent->update([
                        'meta_value' => $imageField . '/' . $fileName,
                    ]);
                }
            }
        }
    }

    public function topProductPageContent()
    {
        $lang = getCurrentLocale();
        $lang_id = getCurrentLanguageID();
        if (session()->has('lang_code')) {
            $lang = session()->get('lang_code');
        }
        $topProductContents = TopProductContent::where('lang_id', $lang_id)->get();
        if ($topProductContents->isEmpty()) {
            $englishTopProductContents = TopProductContent::where('lang_id', 1)->get();
            if ($englishTopProductContents) {
                foreach ($englishTopProductContents as $content) {
                    $topProductContents = new TopProductContent;
                    $topProductContents->meta_key = $content->meta_key;
                    $topProductContents->meta_value = $content->meta_value;
                    $topProductContents->lang_id = $lang_id;
                    $topProductContents->type = $content->type;
                    $topProductContents->save();
                }
            }
            $topProductContents = TopProductContent::where('lang_id', $lang_id)->get();
        }
        $productFiles = TopProductContent::where('type', 'file')->Where('lang_id', 1)->get();
        return view('Admin.site-content.top_product_page', compact('topProductContents', 'productFiles'));
    }
    public function topProductPageUpdate(Request $request)
    {
        $this->uploadImagesTopProductPage($request, 'top_pro_banner_image');
        $this->uploadImagesTopProductPage($request, 'top_pro_banner_bg_image');
        $this->uploadImagesTopProductPage($request, 'top_pro_facebook_icon');
        $this->uploadImagesTopProductPage($request, 'top_pro_pinterest_icon');
        $this->uploadImagesTopProductPage($request, 'top_pro_twitter_icon');
        $this->uploadImagesTopProductPage($request, 'top_pro_copylink_icon');
        $this->uploadImagesTopProductPage($request, 'top_pro_more_icon');
        $this->uploadImagesTopProductPage($request, 'top_pro_mail_image');
        $this->uploadImagesTopProductPage($request, 'top_pro_green_tick_img');
        $textFields = [
            'header_title',
            'header_sub_title',
            'header_bottom_text',
            'learn_more',
            'search_placeholder',
            'user_rating',
            'price',
            'price_option',
            'features',
            'show_more',
            'deployment',
            'company_size',
            'drop_down_text',
            'visit_website',
            'read_more',
            'key_features',
            'starting_price',
            'month',
            'compare_products',
            'rating',
            'footer_title',
            'footer_sub_title',
            'email_placeholder',
            'subscribe_lable',
            'you_agree',
            'terms_of_use',
            'privacy_policy',
            'and'
        ];
        foreach ($textFields as $field) {
            if ($request->has($field)) {
                $datas = $request->get($field);
                foreach ($datas as $id => $val) {
                    $categoryContent = TopProductContent::find($id);
                    if ($categoryContent) {
                        $categoryContent->update([
                            'meta_value' => $val,
                        ]);
                    }
                }
            }
        }
        return redirect()->back()->with('success', 'Top Product content updated successfully.');
    }
    private function uploadImagesTopProductPage(Request $request, $imageField)
    {
        if ($request->hasFile($imageField)) {
            foreach ($request->file($imageField) as $id => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($imageField), $fileName);

                $homeContent = TopProductContent::find($id);
                if ($homeContent) {
                    $homeContent->update([
                        'meta_value' => $imageField . '/' . $fileName,
                    ]);
                }
            }
        }
    }

    public function contentByLanguage(Request $request){
        // return $request->all();

        $lang_id = $request->language_id;
        $lang =Language::where('id',$lang_id)->first();
        if ($lang) {
            // Store both lang_code and lang_id in Redis
            Redis::set('admin_lang_code', $lang->lang_code);
            Redis::set('admin_lang_id', $lang->id);
        }



        return response()->json([
            'success' => true,
            'lang_code'=>$lang->lang_code
        ]);

    }


}
