<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
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
    public function business()
    {
        $lang_id = getCurrentLanguageID();
        $siteLanguage = Language::where('id', $lang_id)->first();
        return view('Admin.business.index');
    }

    public function BusinessListingLivewire(){
        return view('Admin.business.index_listing');
    }

    public function BusinessImagesLivewire(){
        return view('Admin.business.index_images');
    }

    public function EditBusiness($id = null){
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

            $business->translations()->delete();
            $business->websites()->delete();
            $business->wishlists()->delete();
            $business->reviews()->delete();

            if ($business->icon_id && Storage::disk('public')->exists($business->icon_id)) {
                Storage::disk('public')->delete($business->icon_id);
            }

            if ($business->image_id && Storage::disk('public')->exists($business->image_id)) {
                Storage::disk('public')->delete($business->image_id);
            }

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

    public function priceoptions(Request $request)
    {
        $selectedLangCode = $request->get('lang');
        if ($selectedLangCode) {
            $selectedLang = Language::where('lang_code', $selectedLangCode)->first();
            $lang_id = $selectedLang ? $selectedLang->id : getCurrentLanguageID();
        } else {
            $lang_id = getCurrentLanguageID();
            $selectedLang = Language::find($lang_id);
        }

        $langCode = $selectedLang ? $selectedLang->lang_code : 'en-us';

        $price_options = PricingOption::with(['translations', 'categories.categoryTranslations'])->get();

        if ($request->ajax()) {
            $formatted = $price_options->map(function ($opt) use ($lang_id) {
                $translated = $opt->translations->where('lang_id', $lang_id)->first();
                $english = $opt->translations->where('lang_id', 1)->first();

                $categoryNames = $opt->categories->map(function ($cat) use ($lang_id) {
                    $trans = $cat->categoryTranslations->where('lang_id', $lang_id)->first() 
                          ?? $cat->categoryTranslations->where('lang_id', 1)->first();
                    return $trans->name ?? 'Category #' . $cat->id;
                })->toArray();

                return [
                    'id'                     => $opt->id,
                    'name'                   => $english->name ?? ($opt->slug ?? ''),
                    'translated_name'        => $translated->name ?? ($opt->slug ?? ''),
                    'english_name'           => $english->name ?? ($opt->slug ?? ''),
                    'button_text'            => $translated->button_text ?? ($english->button_text ?? 'Claim now'),
                    'translated_button_text' => $translated->button_text ?? ($english->button_text ?? 'Claim now'),
                    'english_button_text'    => $english->button_text ?? 'Claim now',
                    'scope'                  => $opt->scope ?? 'global',
                    'categories'             => $categoryNames,
                ];
            });

            return response()->json([
                'price_options' => $formatted,
                'lang_code'     => $langCode,
            ]);
        }

        $countries = Language::where('status', 1)->get();
        $languages = Language::where('status', 1)->get()->toArray();

        return view('Admin.pricing_option.index', compact('price_options', 'langCode', 'countries', 'lang_id', 'languages'));
    }

    public function priceoptionsAdd(Request $request, $id = null){
        $selectedLangCode = $request->get('lang');
        if ($selectedLangCode) {
            $selectedLang = Language::where('lang_code', $selectedLangCode)->first();
            $lang_id = $selectedLang ? $selectedLang->id : getCurrentLanguageID();
        } else {
            $lang_id = getCurrentLanguageID();
            $selectedLang = Language::find($lang_id);
        }
        $langCode = $selectedLang ? $selectedLang->lang_code : 'en-us';
        $languages = Language::where('status', 1)->get();

        // Fetch parent categories with subcategories for selector
        $allCategories = Category::with(['categoryTranslations', 'subCategories.categoryTranslations'])
            ->where(function($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->get();

        if ($id != null) {
            $pricing_data = PricingOption::with(['translations', 'categories'])->where('id', $id)->first();
            return view('Admin.pricing_option.add', compact('pricing_data', 'lang_id', 'langCode', 'languages', 'allCategories'));
        }
        return view('Admin.pricing_option.add', compact('lang_id', 'langCode', 'languages', 'allCategories'));
    }

    public function priceoptionsAddprocess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->pricing_option_id) {
            $price_option = PricingOption::find($request->pricing_option_id);
        } else {
            $price_option = new PricingOption();
        }

        // Scope handling
        $price_option->scope = $request->input('scope', 'global');

        // Auto-generate slug from name if creating or slug is empty
        if (!$price_option->slug) {
            $price_option->slug = Str::slug($request->name);
        }
        $price_option->save();

        // Sync Categories if category_specific, else detach all
        if ($price_option->scope === 'category_specific') {
            $price_option->categories()->sync($request->input('categories', []));
        } else {
            $price_option->categories()->detach();
        }

        $langId = getCurrentLanguageID() ?: 1;
        $buttonText = $request->input('button_text', 'Claim now');

        PricingOptionTranslation::updateOrCreate(
            ['pricing_option_id' => $price_option->id, 'lang_id' => $langId],
            [
                'name' => $request->name,
                'button_text' => $buttonText,
            ]
        );

        return redirect()->route('priceoptions')->with('success', 'Pricing Option saved successfully.');
    }

    public function priceoptionsremove($id){
        $price_option = PricingOption::find($id);
        if ($price_option) {
            $price_option->categories()->detach();
            $price_option->translations()->delete();
            $price_option->delete();
        }
        return redirect()->route('priceoptions')->with('success','Price Option Removed Successfully');
    }

    public function getOfferTranslations($id)
    {
        $pricingOption = PricingOption::with('translations')->find($id);
        if (!$pricingOption) {
            return response()->json(['success' => false, 'message' => 'Offer option not found.']);
        }

        $languages = Language::where('status', 1)->get();
        $translations = [];

        foreach ($languages as $lang) {
            $trans = $pricingOption->translations->where('lang_id', $lang->id)->first();
            $translations[$lang->id] = [
                'name' => $trans ? $trans->name : '',
                'button_text' => $trans ? ($trans->button_text ?? '') : '',
            ];
        }

        return response()->json([
            'success' => true,
            'offer_id' => $pricingOption->id,
            'slug' => $pricingOption->slug,
            'translations' => $translations,
        ]);
    }

    public function saveOfferTranslation(Request $request)
    {
        $offerId = $request->input('offer_id');
        $sourceLangId = $request->input('source_lang_id');
        $targetLangIds = $request->input('target_lang_ids', []);
        $manualTranslations = $request->input('manual_translations', []);

        $pricingOption = PricingOption::with('translations')->find($offerId);
        if (!$pricingOption) {
            return response()->json(['success' => false, 'message' => 'Offer option not found.']);
        }

        // 1. Process manual edits for any language passed
        if (!empty($manualTranslations) && is_array($manualTranslations)) {
            foreach ($manualTranslations as $langId => $data) {
                $name = trim($data['name'] ?? '');
                $buttonText = trim($data['button_text'] ?? '');

                if ($name !== '' || $buttonText !== '') {
                    PricingOptionTranslation::updateOrCreate(
                        ['pricing_option_id' => $offerId, 'lang_id' => $langId],
                        [
                            'name' => $name !== '' ? $name : ($pricingOption->slug ?? ''),
                            'button_text' => $buttonText !== '' ? $buttonText : 'Claim now',
                        ]
                    );
                }
            }
        }

        // 2. Process auto-translations for target languages if selected
        if (!empty($targetLangIds)) {
            $sourceTranslation = $pricingOption->translations->where('lang_id', $sourceLangId)->first();
            $sourceName = $sourceTranslation->name ?? $pricingOption->slug;
            $sourceButtonText = $sourceTranslation->button_text ?? 'Claim now';

            if (!empty($sourceName)) {
                $sourceLang = Language::find($sourceLangId);
                $sourceCode = $sourceLang ? explode('-', $sourceLang->lang_code)[0] : 'en';

                foreach ($targetLangIds as $targetLangId) {
                    if ($targetLangId == $sourceLangId) {
                        continue;
                    }

                    $targetLang = Language::find($targetLangId);
                    $targetCode = $targetLang ? explode('-', $targetLang->lang_code)[0] : 'en';

                    $translatedName = $this->translateText($sourceName, $sourceCode, $targetCode);
                    $translatedButtonText = $this->translateText($sourceButtonText, $sourceCode, $targetCode);

                    PricingOptionTranslation::updateOrCreate(
                        ['pricing_option_id' => $offerId, 'lang_id' => $targetLangId],
                        [
                            'name' => $translatedName,
                            'button_text' => $translatedButtonText,
                        ]
                    );
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Translations saved successfully.']);
    }

    private function translateText($text, $sourceCode, $targetCode)
    {
        try {
            $url = 'https://translate.googleapis.com/translate_a/single?client=gtx'
                . '&sl=' . urlencode($sourceCode)
                . '&tl=' . urlencode($targetCode)
                . '&dt=t'
                . '&q=' . urlencode($text);

            $response = file_get_contents($url);
            $result = json_decode($response, true);

            if (isset($result[0][0][0])) {
                return $result[0][0][0];
            }
        } catch (\Exception $e) {
            // Fall back to source text on failure
        }

        return $text;
    }
}
