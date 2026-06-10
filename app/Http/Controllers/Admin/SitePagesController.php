<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\Language;
use App\Models\PolicyTranslation;
use Illuminate\Support\Str;
use App\Models\RuleTranslation;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\FaqCategoryTranslation;
use App\Models\Term;
use App\Models\TermsTranslation;
use App\Models\FaqTranslation;
use App\Models\GetListed;
use App\Models\HowItWork;
use App\Models\PageTile;
use App\Models\StaticContentKey;
use App\Models\PageTileTranslation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class SitePagesController extends Controller
{
    public function policies()
    {
        $locale = getCurrentLocale();
        $lang_code = Language::where('lang_code', $locale)->first();

        if (!$lang_code) {
            $lang_code = Language::where('lang_code', 'en-us')->first();
        }

        $privacy_policy = PolicyTranslation::where('lang_id', $lang_code->id)->pluck('title', 'id');
        return view('Admin.site-content.privacy-policy.privacy-policy', compact('privacy_policy'));
    }

    public function policiesAddShow($id = null)
    {
        if ($id == null) {
            return view('Admin.site-content.privacy-policy.privacy-policy-add');
        } else {
            $policy_data = PolicyTranslation::where('id', $id)
                ->first()
                ->toArray();
            return view('Admin.site-content.privacy-policy.privacy-policy-add', compact('policy_data'));
        }
    }

    public function policiesadd(Request $request  )
    {

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'policy_description' => 'required|string',
        ]);

        if($request->policy_id){
            $existingTranslation = PolicyTranslation::where('id', $request->policy_id)->first();
            if (!$existingTranslation) {
                return redirect()
                    ->back()
                    ->with('error', 'Policy not found');
            }

            $existingTranslation->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['policy_description'],
                'status' => 'active',
            ]);

            $message = 'Policy successfully updated in the selected language.';
            return redirect()
            ->route('admin.policies')
            ->with('success', $message);
        }

        $locale = 'en-us' ;
        $lang_code = Language::where('lang_code', $locale)->first();
        if (!$lang_code) {
            return redirect()
                ->back()
                ->withErrors('Language not found.');
        }

        $policy = new Policy;
        $policy->type='pp';
        $policy->lang_id = $lang_code->id;
        $policy->save();

        $policyId = $policy->id;

        $existingTranslation = PolicyTranslation::where('policy_id', $policyId)
            ->where('lang_id', $lang_code->id)
            ->first();

        if ($existingTranslation) {
            $existingTranslation->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['policy_description'],
                'status' => 'active',
            ]);
            $message = 'Policy successfully updated in the selected language.';
        } else {

            PolicyTranslation::create([
                'title' => $validatedData['title'],
                'lang_id' => $lang_code->id,
                'description' => $validatedData['policy_description'],
                'key' => $policyId,
                'policy_id' => $policyId,
                'status' => 'active',
            ]);
            $message = 'Policy successfully added in the selected language.';
        }

        return redirect()
            ->route('admin.policies')
            ->with('success', $message);
    }
    public function pulicyRemove($id)
    {

        $policy = PolicyTranslation::find($id);
       // dd($policy);
        if (!$policy) {
            return redirect()
                ->back()
                ->with('error', 'policy not found');
        }
        $policy->delete();
        // PolicyTranslation::where('policy_id', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'policy remove successfully');
    }

    // end remove policy function
    public function rules()
    {
        $rules = Rule::with('policy')->get();

        $locale = getCurrentLocale();

        $siteLanguage = Language::where('lang_code', $locale)->first();

        $rules = Rule::with([
            'translations' => function ($query) use ($siteLanguage) {
                $query->where('language_id', $siteLanguage->id);
            },
        ])->get();

        return view('Admin.policy.rules.index', compact('rules'));
    }

    public function ruleEdit($id)
    {
        $rule = Rule::with('policy')->find($id);
        if (!$rule) {
            return redirect()
                ->back()
                ->with('error', 'not found rule');
        }
        $locale = getCurrentLocale();

        $siteLanguage = Language::where('lang_code', $locale)->first();

        // Check if site language exists
        if ($siteLanguage) {
            // If the language is not primary, fetch the translation for the policy
            if ($siteLanguage->primary !== 1) {
                $ruleTranslation = RuleTranslation::with('language')
                    ->where('rule_id', $id)
                    ->where('language_id', $siteLanguage->id)
                    ->first();
            } else {
                // If it's the primary language or no translation found, use the main policy
                $ruleTranslation = null;
            }
        } else {
            // Handle the case when no matching site language is found
            $ruleTranslation = null;
        }

        $policies = Policy::all();

        return view('Admin.policy.rules.add-rule', compact('rule', 'policies', 'ruleTranslation'));
    }

    public function ruleAdd()
    {
        $policies = Policy::all();

        return view('Admin.policy.rules.add-rule', compact('policies'));
    }

    public function ruleAddProcc(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $lang_code = Language::where('lang_code', $request->handle)->first();
        if ($lang_code) {
            if ($request->rule_tr_id) {
                $ruleTranslation = RuleTranslation::find($request->rule_tr_id);
                if (!$ruleTranslation) {
                    return redirect()
                        ->back()
                        ->with('error', 'Rule translation not found');
                } else {
                    $ruleTranslation->title = $request->title;
                    $ruleTranslation->description = $request->description;
                    $ruleTranslation->rule_id = $request->rule_tr_id;
                    $ruleTranslation->language_id = $lang_code->id;
                    $ruleTranslation->update();
                    return redirect()
                        ->back()
                        ->with('success', 'Rule translation update successfully');
                }
            } else {
                $ruleTranslation = new RuleTranslation();
                $ruleTranslation->title = $request->title;
                $ruleTranslation->description = $request->description;
                $ruleTranslation->rule_id = $request->id;
                $ruleTranslation->language_id = $lang_code->id;
                $ruleTranslation->save();
                return redirect()
                    ->back()
                    ->with('success', 'Rule translation add successfully');
            }
        }
        if ($request->has('id') && $request->id) {
            // Attempt to find the existing policy
            $rule = Rule::find($request->id);

            if (!$rule) {
                // If the policy is not found, redirect back with an error message
                return redirect()
                    ->back()
                    ->with('error', 'Policy not found');
            }

            // Update the policy with the new data
            $rule->title = $request->title;
            $rule->slug = Str::slug($request->title);
            $rule->policy_id = $request->policy_id;
            $rule->description = $request->description;
            $rule->save();

            return redirect()
                ->back()
                ->with('success', 'Rule updated successfully');
        }

        // If no id, create a new policy
        $rule = new Rule();
        $rule->title = $request->title;
        $rule->slug = Str::slug($request->title);
        $rule->policy_id = $request->policy_id;
        $rule->description = $request->description;
        $rule->save();

        return redirect()
            ->back()
            ->with('success', 'Rule added successfully');
    }
    public function ruleRemove($id)
    {
        $rule = Rule::find($id);
        if (!$rule) {
            return redirect()
                ->back()
                ->with('error', 'rule not found');
        }
        $rule->delete();
        return redirect()
            ->back()
            ->with('success', 'rule remove successfully');
    }
    public function faqs()
    {
        $locale = getCurrentLocale();
        $lang_code = Language::where('lang_code', $locale)->first();


        $faqs = Faq::with([
            'translations' => function ($query) use ($lang_code) {
                $query->where('lang_id', $lang_code->id);
            },
            'category.translations' => function ($query) use ($lang_code) {
                $query->where('lang_id', $lang_code->id);
            },
        ])
        ->orderBy('position', 'asc')
        ->get();

        // Group by type -> then by category name
        $groupedFaqs = $faqs->groupBy(function ($faq) {
            return $faq->type; // 'user' or 'vendor'
        })->map(function ($faqsByType) {
            return $faqsByType->groupBy(function ($faq) {
                return $faq->category && $faq->category->translations->isNotEmpty()
                    ? $faq->category->translations->first()->name
                    : 'Uncategorized';
            });
        });

        //Get static content values for the current language
        $faq_title = StaticContentKey::where('key', 'faq_title')->first();
        $faq_description = StaticContentKey::where('key', 'faq_description')->first();
        // dd($faq_description);
        return view('Admin.faqs.index', compact('groupedFaqs','faq_title','faq_description'));
        }

    public function reorderFaqs(Request $request)
    {
        // Step 1: Validate input
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'faqs' => 'required|array',
            'faqs.*.id' => 'required|integer|exists:faqs,id',
            'faqs.*.position' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Step 2: Check for duplicate positions
        $positions = collect($request->input('faqs'))->pluck('position');
        if ($positions->count() !== $positions->unique()->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate positions detected'
            ], 422);
        }

        // Step 3: Perform the updates in a transaction
        try {
            DB::beginTransaction();

            foreach ($request->input('faqs') as $faqData) {
                DB::table('faqs')
                    ->where('id', $faqData['id'])
                    ->update(['position' => $faqData['position']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'FAQ order updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function category(){
        $category = Category::all();
        $lang_id = getCurrentLanguageID();
        $faqs = FaqCategory::with([
            'translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            },
        ],'faqs')->get();
        return view('Admin.faqs.faqs_categories', compact('faqs'));
    }
    public function faqAdd()
    {
        $lang_id=getCurrentLanguageID();
        $categories = FaqCategory::with(['translation' =>function ($query) use ($lang_id){
            $query->where('lang_Id', $lang_id);
        }])->where('status',1)->get();
        return view('Admin.faqs.faq_add',compact('categories'));
    }
    public function categoryAdd(){

        return view('Admin.faqs.add_faq_category');
    }

    public function faqEdit($id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return redirect()
                ->back()
                ->with('error', 'faq not found');
        }

        $lang_id = getCurrentLanguageID();
        $categories = FaqCategory::with(['translation' =>function ($query) use ($lang_id){
            $query->where('lang_Id', $lang_id);
        }])->where('status',1)->get();
            $faqTranslation = FaqTranslation::with('language')
                ->where('faq_id', $id)
                ->where('lang_id', $lang_id)
                ->first();
        return view('Admin.faqs.faq_add', compact('faq', 'categories','faqTranslation'));
    }

    public function faqAddProcc(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'faq_category' => 'required',
            'type' => 'required|in:user,vendor',
        ]);
        // dd($validatedData['type']);

        $lang_code = getCurrentLocale();
        $language_id = Language::where('lang_code', $lang_code)->value('id');

        // CASE: Updating existing FAQ
        if ($request->faq_id) {
            // Update main FAQ base data
            $faq = Faq::findOrFail($request->faq_id);
            $faq->update([
                'faqs_category_id' => $validatedData['faq_category'], // Optionally update category
                'type' => $validatedData['type'], // <-- NEW
            ]);

            // Update or create translation
            $faq_translation = FaqTranslation::where('faq_id', $request->faq_id)
                ->where('lang_id', $language_id)
                ->first();

            if ($faq_translation) {
                $faq_translation->update([
                    'question' => $validatedData['question'],
                    'answer' => $validatedData['answer'],
                ]);
            } else {
                FaqTranslation::create([
                    'faq_id' => $faq->id,
                    'lang_id' => $language_id,
                    'question' => $validatedData['question'],
                    'answer' => $validatedData['answer'],
                ]);
            }
        } else {
            // CASE: Creating new FAQ
            $faq = Faq::create([
                'faqs_category_id' => $validatedData['faq_category'],
                'type' => $validatedData['type'], // <-- NEW
            ]);

            FaqTranslation::create([
                'faq_id' => $faq->id,
                'lang_id' => $language_id,
                'question' => $validatedData['question'],
                'answer' => $validatedData['answer'],
            ]);
        }

        return redirect()
            ->route('faqs')
            ->with('success', 'FAQ Created or Updated Successfully!');
    }



    public function categoryaddprocc(Request $request){

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faq_category_translations', 'name')
                    ->where('lang_id', $request->lang_id)
                    ->ignore($request->faq_id, 'faq_category_id'),
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);
        $status = $request->has('status') ? 1 : 0;
        if ($request->faq_id) {
            $faq = FaqCategory::findOrFail($request->faq_id);
            $faq->update([
                'status' => $status, // update status of main category
            ]);
            $faq_translation = FaqCategoryTranslation::where('faq_category_id', $request->faq_id)
                ->where('lang_id', $request->lang_id)
                ->first();

            if ($faq_translation) {
                $faq_translation->update([
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'],
                ]);
            } else {
                $faq = FaqCategory::create([
                    'status' => $status,
                ]);
                $faq->translation()->create([
                    'faq_category_id' => $faq->id,
                    'lang_id' => $request->lang_id,
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'],
                ]);
            }
        } else {
            $faq = FaqCategory::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'status'=> $status,
            ]);
         $faq->translation()->create([
                'faq_category_id' => $faq->id,
                'lang_id' => $request->lang_id,
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
            ]);
        }

        return redirect()
            ->route('faqs-category')
            ->with('success', 'FAQ Created or Updated Successfully!');
    }
    public function faqcategoryEdit($id)
    {
        $lang_id = getCurrentLanguageID();

        $faq = FaqCategory::with(['translation' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        }])
        ->where('id', $id)
        ->first();
        if (!$faq) {
            return redirect()
                ->back()
                ->with('error', 'faq not found');
        }
        return view('Admin.faqs.add_faq_category', compact('faq'));
    }
    public function faqRemove($id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return redirect()
                ->back()
                ->with('error', 'faq not found');
        }
        $faqtrans=FaqTranslation::where('faq_id',$id)->first();
        $faq->delete();
        $faqtrans->delete();
        return redirect()
            ->back()
            ->with('success', 'faq remove successfully');
    }
    public function faqcategoryRemove($id){
        $faq = FaqCategory::find($id);
        if (!$faq) {
            return redirect()
            ->back()
            ->with('error', 'Faq Category Not Found');
        }
        $faqtrans=FaqCategoryTranslation::where('faq_category_id',$id)->first();
        $faq->delete();
        $faqtrans->delete();
        return redirect()
        ->back()
        ->with('success', 'faq remove successfully');
    }

    // terms and condition
    public function terms_show()
    {
        $locale = getCurrentLocale();
        $lang_code = Language::where('lang_code', $locale)->first();

        if (!$lang_code) {
            $lang_code='en-us';
        }
        $terms = TermsTranslation::where('lang_id', $lang_code->id)->pluck('title', 'id');
        return view('Admin.site-content.terms-condition.terms', compact('terms'));
    }

    public function termsAdd_show($id = null)
    {
        if ($id == null) {
            return view('Admin.site-content.terms-condition.terms-add');
        } else {
            $terms_data = TermsTranslation::where('id', $id)
                ->first()
                ->toArray();
            return view('Admin.site-content.terms-condition.terms-add', compact('terms_data'));
        }
    }

    public function terms_add_process(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'policy_description' => 'required|string',
        ]);


        if($request->term_id){
            $existingTranslation = TermsTranslation::where('id', $request->term_id)
            ->first();

            $existingTranslation->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['policy_description'],
                'status' => 'active',
            ]);

            $message = 'Policy successfully updated in the selected language.';

            return redirect()
            ->route('terms')
            ->with('success', 'Successfully created policy');

        }

        $locale = 'en-us';
        $lang_code = Language::where('lang_code', $locale)->first();


        $terms = new Term;
        $terms->type = 'tac';
        $terms->lang_id = $lang_code->id;
        $terms->save();

        $terms_id = $terms->id;

        $existingTranslation = TermsTranslation::where('terms_id', $terms_id)
        ->where('lang_id', $lang_code->id)
        ->first();

    if ($existingTranslation) {
        $existingTranslation->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['policy_description'],
            'status' => 'active',
        ]);
        $message = 'Policy successfully updated in the selected language.';
        } else {

        TermsTranslation::create([
            'title' => $validatedData['title'],
            'lang_id' => $lang_code->id,
            'description' => $validatedData['policy_description'],
            'key' => $terms_id,
            'terms_id' => $terms_id,
            'status' => 'active',
        ]);
        $message = 'Policy successfully added in the selected language.';
    }

        return redirect()
            ->route('terms')
            ->with('success', 'Successfully created policy');
    }

    public function terms_remove($id)
    {
        $term = TermsTranslation::find($id);
        if (!$term) {
            return redirect()
                ->back()
                ->with('error', 'Terms  not found');
        }
        $term->delete();
        // TermsTranslation::where('terms_id', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'poliTerms cy remove successfully');
    }

    public function vendorListed(){
        $lang = getCurrentLocale();

        $redis_lang_code = Redis::get('admin_lang_code'); // Fetch lang_code from Redis
        $redis_lang = $redis_lang_code ? Language::where('lang_code', $redis_lang_code)->first() : null; // Fetch Language model if lang_code exists

        $lang_id = optional($redis_lang)->id ?? getCurrentLanguageID();


        $vendorList=GetListed::where('lang_id', $lang_id)->first();
        // dd($vendorList);

        return view('Admin.site-content.vendor_listed',compact('vendorList'));
    }

    public function vendorWorkUpdate(Request $request)
    {
        $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
        $language = Language::where('lang_code', $lang_code)->firstOrFail();

        // Validate request
        $validated = $request->validate([
            'banner_title' => 'required|string|max:255',
            'banner_description' => 'nullable|string',
            'main_heading' => 'nullable|string|max:255',

            'section_1_title' => 'nullable|string|max:255',
            'section_1_description' => 'nullable|string',
            'section_2_title' => 'nullable|string|max:255',
            'section_2_description' => 'nullable|string',
            'section_2_button' => 'nullable|string|max:255',
            'section_3_title' => 'nullable|string|max:255',
            'section_3_description' => 'nullable|string',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',

            'banner_image_left' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            'banner_image_right' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            'section_1_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            'section_2_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            'section_3_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',

            'heading_section' => 'nullable|array',
            'heading_section.*.icon' => 'nullable|string',
            'heading_section.*.title' => 'nullable|string',
            'heading_section.*.description' => 'nullable|string',
            'heading_section_delete' => 'nullable|array',
        ]);

        // Fetch or create record
        $content = HowItWork::firstOrNew(['lang_id' => $language->id]);

        // Assign simple fields
        $content->fill(collect($validated)->except([
            'banner_image_left', 'banner_image_right',
            'section_1_image', 'section_2_image', 'section_3_image',
            'heading_section',
        ])->toArray());
        $content->lang_id = $language->id;

        // Handle main image uploads
        foreach ([
            'banner_image_left',
            'banner_image_right',
            'section_1_image',
            'section_2_image',
            'section_3_image'
        ] as $field) {
            if ($request->hasFile($field)) {
                $content->$field = $this->uploadImage($request->file($field), $field);
            }
        }

        // // Process heading_section array
        // $headingSection = [];

        // if ($request->has('heading_section') && is_array($request->heading_section)) {
        //     foreach ($request->heading_section as $index => $item) {
        //         $iconInputKey = "heading_section.$index.icon_file";
        //         $iconFileName = null;

        //         if ($request->hasFile($iconInputKey)) {
        //             $iconFileName = $this->uploadImage($request->file($iconInputKey), 'heading_icon');
        //         } elseif (!empty($item['icon'])) {
        //             $iconFileName = $item['icon'];
        //         }

        //         if (
        //             $iconFileName &&
        //             isset($item['title'], $item['description']) &&
        //             trim($item['title']) !== '' &&
        //             trim($item['description']) !== ''
        //         ) {
        //             $headingSection[] = [
        //                 'icon' => $iconFileName,
        //                 'title' => trim($item['title']),
        //                 'description' => trim($item['description']),
        //             ];
        //         }
        //     }
        // }

        // // Remove items marked for deletion
        // if ($request->has('heading_section_delete') && is_array($request->heading_section_delete)) {
        //     foreach ($request->heading_section_delete as $deleteIndex) {
        //         unset($headingSection[$deleteIndex]);
        //     }
        //     $headingSection = array_values($headingSection);
        // }

        // // Save as JSON
        // $content->heading_section = json_encode($headingSection);

        $content->save();

        // Process new how_it_work items
        if ($request->has('how_it_work') && is_array($request->how_it_work)) {
            $this->processPageTiles($request->how_it_work, 'how_it_work', $language->id);
        }

        // // Update existing RTT items
        // if ($request->has('RTT') && is_array($request->RTT)) {
        //     foreach ($request->RTT['id'] ?? [] as $index => $id) {
        //         $title = $request->RTT['title'][$index] ?? null;
        //         $description = $request->RTT['description'][$index] ?? null;

        //         if ($id && ($title || $description)) {
        //             $translation = PageTileTranslation::find($id);
        //             if ($translation) {
        //                 $translation->title = $title;
        //                 $translation->description = $description;

        //                 if ($request->hasFile("RTT.image.$index")) {
        //                     $translation->image = $this->uploadImage($request->file("RTT.image.$index"), "rtt_{$index}");
        //                 }

        //                 $translation->save();
        //             }
        //         }
        //     }
        // }

        return back()->with('success', 'How It Works content updated successfully.');
    }

    public function vendorListedUpdate(Request $request)
    {
        // Uncomment for debugging
        // dd($request->all());

        $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
        $language = Language::where('lang_code', $lang_code)->firstOrFail();

        $validated = $request->validate([
            'banner_heading' => 'required|string|max:255',
            'banner_sub_heading' => 'nullable|string',
            'banner_button' => 'nullable|string|max:255',

            'section_1_title' => 'nullable|string|max:255',
            'section_1_description' => 'nullable|string',
            'section_2_title' => 'nullable|string|max:255',
            'section_2_description' => 'nullable|string',
            'section_3_title' => 'nullable|string|max:255',
            'section_3_description' => 'nullable|string',
            'section_3_button' => 'nullable|string|max:255',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',

            'banner_image_left' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'banner_image_right' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'section_1_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'section_2_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'section_3_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',

            'claim_section' => 'nullable|array',
            'claim_section.*.title' => 'nullable|string',
            'claim_section.*.description' => 'nullable|string',
            'claim_section_delete' => 'nullable|array',
        ]);

        $content = GetListed::firstOrNew(['lang_id' => $language->id]);

        $content->fill([
            'banner_heading' => $request->banner_heading,
            'banner_sub_heading' => $request->banner_sub_heading,
            'banner_button' => $request->banner_button,

            'section_1_title' => $request->section_1_title,
            'section_1_description' => $request->section_1_description,
            'section_2_title' => $request->section_2_title,
            'section_2_description' => $request->section_2_description,
            'section_3_title' => $request->section_3_title,
            'section_3_description' => $request->section_3_description,
            'section_3_button' => $request->section_3_button,

            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle image uploads
        $imageFields = [
            'banner_image_left',
            'banner_image_right',
            'section_1_image',
            'section_2_image',
            'section_3_image',
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $content->$field = $this->uploadImage($request->file($field), $field);
            }
        }

        // Handle claim_section data
        $claimSection = [];

        if ($request->has('claim_section') && is_array($request->claim_section)) {
            foreach ($request->claim_section as $index => $item) {
                // Only include items that have both title and description
                if (isset($item['title'], $item['description']) &&
                    !empty(trim($item['title'])) && !empty(trim($item['description']))) {

                    $claimSection[] = [
                        'title' => trim($item['title']),
                        'description' => trim($item['description'])
                    ];
                }
            }
        }

        // Handle deletions if you're using the delete functionality
        if ($request->has('claim_section_delete') && is_array($request->claim_section_delete)) {
            $deleteIndices = $request->claim_section_delete;
            // Remove items marked for deletion
            foreach ($deleteIndices as $deleteIndex) {
                unset($claimSection[$deleteIndex]);
            }
            // Re-index the array
            $claimSection = array_values($claimSection);
        }

        // Save claim_section as JSON
        $content->claim_section = json_encode($claimSection);

        $content->lang_id = $language->id;
        $content->save();

        return back()->with('success', 'Get Listed content updated successfully.');
    }


    public function vendorWork(){
        $lang = getCurrentLocale();

        $redis_lang_code = Redis::get('admin_lang_code'); // Fetch lang_code from Redis
        $redis_lang = $redis_lang_code ? Language::where('lang_code', $redis_lang_code)->first() : null; // Fetch Language model if lang_code exists

        $lang_id = optional($redis_lang)->id ?? getCurrentLanguageID();


        $howItWorks=HowItWork::where('lang_id', $lang_id)->first();
        // dd($vendorList);

        $pageTileTranslationRightTool = PageTile::where('source', 'how_it_work')->where('lang_id', $lang_id)
        ->with('translations') // Eager load translations
        ->get();

        return view('Admin.site-content.vendor_how_it_work',compact('howItWorks','pageTileTranslationRightTool'));
    }




    private function processPageTiles($items, $type, $lang_id)
    {
        if (empty($items['title']) || !is_array($items['title'])) return;
        foreach ($items['title'] as $index => $title) {
            $description = $items['description'][$index] ?? null;
            $imagePath = $this->handleBase64Image($items['image'][$index] ?? null, "{$type}_{$index}");

            $pageTile = PageTile::create([
                'lang_id' => $lang_id,
                'image' => $imagePath,
                'type' => $type,
                'source' => $type
            ]);

            PageTileTranslation::create([
                'page_tile_id' => $pageTile->id,
                'title' => $title,
                'description' => $description,
                'image' => $imagePath,
                'status' => request()->input('status', 1),
            ]);
        }
    }

    /**
     * Handles base64 image decoding and saving.
     */
    private function handleBase64Image($imageData, $prefix)
    {
        if (!$imageData || !preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            return null;
        }

        $extension = $type[1];
        $imageData = base64_decode(substr($imageData, strpos($imageData, ',') + 1));
        $filename = now()->format('YmdHis') . "_{$prefix}.{$extension}";
        file_put_contents(public_path('front/img/') . $filename, $imageData);

        return 'front/img/' . $filename;
    }



    private function uploadImage($file, $prefix)
    {
        $filename = now()->format('YmdHis') . "_{$prefix}." . $file->getClientOriginalExtension();
        $file->move(public_path('front/img/'), $filename);
        return 'front/img/' . $filename;
    }






//     public function FaqUpdate(Request $request, $id)
// {
//     $request->validate([
//         'title' => 'required|string|max:255',
//         'description' => 'required|string',
//     ]);

//     $faq = Faq::findOrFail($id);
//     $translation = $faq->translations()->where('locale', app()->getLocale())->first();

//     if ($translation) {
//         $translation->update([
//             'title' => $request->title,
//             'description' => $request->description,
//         ]);

//         return response()->json(['success' => true]);
//     }

//     return response()->json(['success' => false, 'message' => 'Translation not found.']);
//     }


// Update FAQs
public function updateLine(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'description' => 'required|string',
    ]);
    // dd($request->all());

    try {
        StaticContentKey::updateOrCreate(
            ['key' => 'faq_title'],
            ['default_value' => $request->title]
        );

        StaticContentKey::updateOrCreate(
            ['key' => 'faq_description'],
            ['default_value' => $request->description]
        );

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}
