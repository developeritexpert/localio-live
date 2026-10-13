<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductTranslation;
use App\Services\MediaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Business, Category, Language, CategoryTranslation, Media, Product, ProductPrice};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\BusinessCategoryTopic;
use App\Models\CategoryRatingCriteria;
use App\Models\BusinessCategoryTopicTranslation;
use App;
use Illuminate\Validation\Rule;
use Session;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;



class CategoriesController extends Controller
{
    protected $mediaservice;
    public function __construct(MediaService $mediaservice)
    {
        $this->mediaservice = $mediaservice;
    }


    public function index(Request $request)
    {
        $locale = session('category_lang_code', 'en-us');
        App::setLocale($locale);

        $siteLanguage = Language::where('lang_code', $locale)->value('id') ?? 1;
        $englishLangId = Language::where('lang_code', 'en-us')->value('id') ?? 1;

        $allCategories = Category::with(['parent', 'subCategories', 'categoryTranslations'])->get();

        foreach ($allCategories as $category) {
            $englishTrans = $category->categoryTranslations->where('lang_id', $englishLangId)->first()
                ?? $category->categoryTranslations->first();
            $selectedTrans = $category->categoryTranslations->where('lang_id', $siteLanguage)->first();

            $category->english_name = $englishTrans ? $englishTrans->name : 'Unnamed Category';
            $category->translated_name = $selectedTrans ? $selectedTrans->name : null;
            $category->translation_id = $selectedTrans ? $selectedTrans->id : ($englishTrans ? $englishTrans->id : null);
            $category->is_active_for_country = $selectedTrans ? ($selectedTrans->status ?? 1) : 1;
        }

        // Group categories hierarchically: Parent categories followed by their sub-categories
        $orderedCategories = collect();
        $parentCategories = $allCategories->whereNull('parent_id')->sortBy('english_name');

        foreach ($parentCategories as $parent) {
            $orderedCategories->push($parent);
            $subCategories = $allCategories->where('parent_id', $parent->id)->sortBy('english_name');
            foreach ($subCategories as $sub) {
                $orderedCategories->push($sub);
            }
        }

        // Append any orphan sub-categories if any exist
        $addedIds = $orderedCategories->pluck('id')->toArray();
        $orphanCategories = $allCategories->whereNotIn('id', $addedIds)->sortBy('english_name');
        foreach ($orphanCategories as $orphan) {
            $orderedCategories->push($orphan);
        }

        $categories = $orderedCategories;

        return view('Admin.categories.index', compact('categories', 'siteLanguage', 'englishLangId'));
    }

    public function setLanguage($lang_id)
    {
        $language = Language::find($lang_id);
        if ($language) {
            // dd($language);
            session([
                'category_lang_code' => $language->lang_code,
                'category_lang_name' => $language->name,
            ]);
        }

        return redirect()->route('categories');  // Redirect to the categories page after language change
    }


    public function add($id = null)
    {
        $locale = getCurrentLocale();
        $lang_id = Language::where('lang_code', $locale)->value('id') ?? 1;
        $categoryId = null;
        $category = null;
        $hasSubcategories = false;
        $hasItems = false;
        $category_image_url = null;
        $category_icon_url = null;
        $category_data = null;
        $default_criteria = [];
        $inherited_criteria = [];
        $custom_criteria = [];

        $defaultMaster = \App\Models\DefaultRatingCriteria::orderBy('sort_order')->get();
        $text_sections = [];
        $category_faqs = [];

        if ($id != null) {
            $catTransObj = CategoryTranslation::with('category')->where('id', $id)->first();
            if (!$catTransObj) {
                $catTransObj = CategoryTranslation::with('category')->where('category_id', $id)->first();
            }
            $category_data = $catTransObj ? $catTransObj->toArray() : null;
            if ($catTransObj) {
                if (!empty($catTransObj->text_sections)) {
                    $text_sections = is_array($catTransObj->text_sections) ? $catTransObj->text_sections : json_decode($catTransObj->text_sections, true);
                }
                if (!empty($catTransObj->faqs)) {
                    $category_faqs = is_array($catTransObj->faqs) ? $catTransObj->faqs : json_decode($catTransObj->faqs, true);
                }
            }
            $categoryId = $category_data ? ($category_data['category_id'] ?? null) : null;
            $category = $categoryId ? Category::where('id', $categoryId)->first() : null;
            if ($category) {
                $hasSubcategories = $category->subCategories()->exists();
                $hasItems = $category->businesses()->exists() || $category->products()->exists();
                $category_image = Media::where('id', $category->image)->first();
                $category_icon = Media::where('id', $category->category_icon)->first();
                $category_image_url = $category_image ? asset($category_image->dir_path . '/' . $category_image->file_name) : null;
                $category_icon_url = $category_icon ? asset($category_icon->dir_path . '/' . $category_icon->file_name) : null;

                $effective = $category->getEffectiveRatingCriteria();

                foreach ($defaultMaster as $def) {
                    $item = $effective->firstWhere('default_key', $def->key);
                    $default_criteria[] = [
                        'key' => $def->key,
                        'name' => $def->name,
                        'description' => $item ? $item->description : $def->default_description,
                    ];
                }

                foreach ($effective as $item) {
                    if (!$item->is_default) {
                        if (!empty($item->is_inherited)) {
                            $inherited_criteria[] = $item;
                        } else {
                            $custom_criteria[] = $item;
                        }
                    }
                }
            }
        }

        if (empty($default_criteria)) {
            foreach ($defaultMaster as $def) {
                $default_criteria[] = [
                    'key' => $def->key,
                    'name' => $def->name,
                    'description' => $def->default_description,
                ];
            }
        }

        $parentCategories = Category::onlyParents()
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('id', '!=', $categoryId);
            })
            ->with(['translation' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            }])
            ->get();

        return view('Admin.categories.add', compact(
            'text_sections',
            'category_faqs',
            'category_data',
            'category',
            'category_image_url',
            'category_icon_url',
            'parentCategories',
            'hasSubcategories',
            'hasItems',
            'default_criteria',
            'inherited_criteria',
            'custom_criteria'
        ));
    }

    // public function add_process(Request $request)
    // {

    //     $language_id = Language::where('lang_code', getCurrentLocale())->value('id') ?? 1;
    //     $isNewCategory = !$request->category_id;
        
    //     $rules = [
    //         'name' => [
    //             'required',
    //             'min:3',
    //             'max:255'
                
    //         ],
    //         'title' => 'nullable|string|max:255',
    //         'homepage_link_text' => 'nullable|string|max:255',
    //         'show_on_homepage' => 'nullable',
    //         'homepage_order' => 'nullable|integer',
    //         'homepage_product_limit' => 'nullable|integer|min:1|max:50',
    //         'comparison_slug' => 'nullable|string|max:255',
    //         'description' => 'required|string|min:10',
    //         'image' => 'nullable|mimes:svg,png,jpg,jpeg,webp|max:2048',
    //         'category_icon' => $isNewCategory
    //         ? 'required|mimes:svg,png,jpg,jpeg,webp|max:2048'
    //         : 'nullable|mimes:svg,png,jpg,jpeg,webp|max:2048',
    //         'is_parent' => 'nullable',
    //         'parent_id' => 'nullable|required_without:is_parent|exists:categories,id',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);

    //     $validator->after(function ($validator) use ($request) {
    //         // $is_parent = $request->boolean('is_parent');
    //         $is_parent = $request->has('is_parent');
    //         $parent_id = $request->parent_id;
    //         $category_id = null;



    //         if ($request->category_id) {
    //             // $categoryTranslation = CategoryTranslation::find($request->category_id);
    //              $categoryTranslation = CategoryTranslation::where('category_id', $request->category_id)->first();
    //             if ($categoryTranslation) {
    //                 $category_id = $categoryTranslation->category_id;
    //             }

    //         }

    //         if (!$is_parent) {               

    //             if ($category_id && $parent_id == $category_id) {
    //                 $validator->errors()->add('parent_id', 'A category cannot be its own parent.');
    //                 return;
    //             }

    //             $parentCategory = Category::find($parent_id);
    //             if ($parentCategory && $parentCategory->parent_id !== null) {
    //                 $validator->errors()->add('parent_id', 'The selected parent category must not be a sub-category itself.');
    //             }

    //             if ($category_id) {
    //                 $category = Category::find($request->category_id);
    //                 if ($category) {
    //                     if ($category->subCategories()->where('id', $parent_id)->exists()) {
    //                         $validator->errors()->add('parent_id', 'Circular reference detected: The selected parent category is a sub-category of this category.');
    //                     }
    //                     if ($category->subCategories()->exists()) {
    //                         $validator->errors()->add('is_parent', 'This category cannot be converted to a sub-category because it contains active sub-categories.');
    //                     }
    //                 }
    //             }
    //         } else {
    //             if ($category_id) {
    //                 $category = Category::find($category_id);
    //                 if ($category && $category->parent_id !== null) {
    //                     $hasBusinesses = $category->businesses()->exists();
    //                     $hasProducts = $category->products()->exists();
    //                     if ($hasBusinesses || $hasProducts) {
    //                         $validator->errors()->add('is_parent', 'This category cannot be converted to a parent category because it contains active businesses or products.');
    //                     }
    //                 }
    //             }
    //         }
    //     });

    //             // dd($request->all());


    //     if ($validator->fails()) {

    //             // dd($validator->errors()->toArray());


    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     $validate = $validator->validated();

    //     $category_id = null;
    //     if ($request->category_id) {
    //         $categoryTranslation = CategoryTranslation::where('category_id', $request->category_id)->first();
    //         if ($categoryTranslation) {
    //             $category_id = $categoryTranslation->category_id;
    //         }
    //     }
    //     $category = $category_id ? Category::find($request->category_id) : new Category();
    //     if (!$category) {
    //         $category = new Category();
    //     }

    //     $category->parent_id = $request->boolean('is_parent') ? null : $request->parent_id;
    //     $category->show_on_homepage = $request->has('show_on_homepage') ? 1 : 0;
    //     $category->homepage_order = (int) ($request->input('homepage_order', 0) ?? 0);
    //     $category->homepage_product_limit = (int) ($request->input('homepage_product_limit', 6) ?? 6);

    //     if ($request->hasFile('image')) {
    //         $media = $this->mediaservice->uploadMedia($request->file('image'), 'category/images');
    //         $category->image = $media->id;
    //     }
    //     if ($request->hasFile('category_icon')) {
    //         $mediaIcon = $this->mediaservice->uploadMedia($request->file('category_icon'), 'category/icon');
    //         $category->category_icon = $mediaIcon->id;  
    //     }       
    //     $category->save();
    //     if ($category) {
    //         $slug = Str::slug($validate['name']);
    //         $originalSlug = $slug;
    //         $count = 1;
    //         while (CategoryTranslation::where('slug', $slug)->where('category_id', '!=', $category->id)->exists()) {
    //             $slug = $originalSlug . '-' . $count++;
    //         }
    //        CategoryTranslation::updateOrCreate(
    //             [
    //                 'lang_id' => (int) $language_id,
    //                 'category_id' => $category->id
    //             ],
    //             [
    //                 'category_id'  => $category->id,
    //                 'lang_id'      => $language_id,
    //                 'name'         => $validate['name'],
    //                 'title'        => $validate['title'] ?? null,
    //                 'homepage_link_text' => $validate['homepage_link_text'] ?? null,
    //                 'description'  => $validate['description'],
    //                 'slug'         => $slug,
    //                 'comparison_slug' => $validate['comparison_slug'] ?? null,
    //                 'is_important' => $request->has('is_important') ? 1 : 0,
    //             ]
    //         );

    //         // Handle rating criteria
    //         $submittedExistingCriteria = $request->input('existing_rating_criteria', []) ?? null;
    //         $submittedNewCriteria = $request->input('new_rating_criteria', []) ?? null;
            
    //         // Delete criteria not in the submitted list
    //         $existingIds = array_keys($submittedExistingCriteria);
    //         $category->ratingCriteria()->whereNotIn('id', $existingIds)->delete();

    //         // Update existing criteria (preserves historical reviews!)
    //         foreach ($submittedExistingCriteria as $criteriaId => $name) {
    //             if (trim($name) !== '') {
    //                 CategoryRatingCriteria::where('id', $criteriaId)->where('category_id', $category->id)->update(['name' => trim($name)]);
    //             }
    //         }

    //         // Add new criteria
    //         foreach ($submittedNewCriteria as $name) {
    //             if (trim($name) !== '') {
    //                 CategoryRatingCriteria::create([
    //                     'category_id' => $category->id,
    //                     'name' => trim($name)
    //                 ]);
    //             }
    //         }

    //         return redirect()->route('categories')->with('success', 'Category saved successfully');
    //     } else {
    //         return redirect()->route('categories')->with('error', 'Failed to save the category.');
    //     }
    // }
    /** new code 31-07-2026 */
     public function add_process(Request $request)
    {

        $language_id = Language::where('lang_code', getCurrentLocale())->value('id') ?? 1;
        // dd($request->all());
        $isNewCategory = !$request->category_id;

        $categoryName = $request->input('category_cap') ?: $request->input('name');
        if ($categoryName) {
            $request->merge([
                'name' => $categoryName,
                'category_cap' => $categoryName,
            ]);
        }
        
        $rules = [
            'name' => [
                'required',
                'min:2',
                'max:255'
            ],
            'category_cap' => 'nullable|string|max:255',
            'category_tc' => 'nullable|string|max:255',
            'category_lc' => 'nullable|string|max:255',
            'entity1_cap' => 'nullable|string|max:255',
            'entity1_lc' => 'nullable|string|max:255',
            'entities1_cap' => 'nullable|string|max:255',
            'entities1_lc' => 'nullable|string|max:255',
            'entity1_short_cap' => 'nullable|string|max:255',
            'entity1_short_lc' => 'nullable|string|max:255',
            'entities1_short_cap' => 'nullable|string|max:255',
            'entities1_short_lc' => 'nullable|string|max:255',
            'entity2_cap' => 'nullable|string|max:255',
            'entity2_lc' => 'nullable|string|max:255',
            'entities2_cap' => 'nullable|string|max:255',
            'entities2_lc' => 'nullable|string|max:255',
            'entity2_short_cap' => 'nullable|string|max:255',
            'entity2_short_lc' => 'nullable|string|max:255',
            'entities2_short_cap' => 'nullable|string|max:255',
            'entities2_short_lc' => 'nullable|string|max:255',
            'comparison_name' => 'nullable|string|max:255',
            'see_all_text' => 'nullable|string|max:255',
            'page_title' => 'nullable|string|max:255',
            'page_title_addition' => 'nullable|string|max:255',
            'top_rated_listing_headline_addition' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'faq_meta_title' => 'nullable|string|max:255',
            'faq_meta_description' => 'nullable|string',
            'comparison_slug' => 'nullable|string|max:255',
            'description' => 'required|string|min:10',
            'top_rated_description' => 'nullable|string',
            'image' => 'nullable',
            'category_icon' => 'nullable',
            'is_parent' => 'nullable',
            'parent_id' => 'nullable|required_without:is_parent|exists:categories,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $is_parent = $request->has('is_parent');
            $parent_id = $request->parent_id;
            $category_id = null;



            if ($request->category_id) {
                $categoryTranslation = CategoryTranslation::find($request->category_id);
                if ($categoryTranslation) {
                    $category_id = $categoryTranslation->category_id;
                    $language_id = $categoryTranslation->lang_id;
                } else {
                    $category_id = $request->category_id;
                }
            }

            if (!$is_parent) {               

                if ($category_id && $parent_id == $category_id) {
                    $validator->errors()->add('parent_id', 'A category cannot be its own parent.');
                    return;
                }

                $parentCategory = Category::find($parent_id);
                if ($parentCategory && $parentCategory->parent_id !== null) {
                    $validator->errors()->add('parent_id', 'The selected parent category must not be a sub-category itself.');
                }

                if ($category_id) {
                    $category = Category::find($category_id);
                    if ($category) {
                        if ($category->subCategories()->where('id', $parent_id)->exists()) {
                            $validator->errors()->add('parent_id', 'Circular reference detected: The selected parent category is a sub-category of this category.');
                        }
                        if ($category->subCategories()->exists()) {
                            $validator->errors()->add('is_parent', 'This category cannot be converted to a sub-category because it contains active sub-categories.');
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validate = $validator->validated();

        $category_id = null;
        if ($request->category_id) {
            $categoryTranslation = CategoryTranslation::find($request->category_id);
            if ($categoryTranslation) {
                $category_id = $categoryTranslation->category_id;
                $language_id = $categoryTranslation->lang_id;
            } else {
                $category_id = $request->category_id;
            }
        }
            // dd($Category_ID);

        $category = Category::find($category_id);

            // dumb($category->array());
            // dd($category->toArray());
            // dump($request->category_id);


        if (!$category) {
            $category = new Category();
        }

        $category->parent_id = $request->parent_id ?? null;
        $category->page_title_addition = $request->page_title_addition;
        if ($request->has('top_rated_listing_headline_addition')) {
            $category->top_rated_listing_headline_addition = $request->top_rated_listing_headline_addition;
        }

        if ($request->hasFile('image')) {
            $media = $this->mediaservice->uploadMedia($request->file('image'), 'category/images');
            $category->image = $media->id;
        }
        if ($request->hasFile('category_icon')) {
            $mediaIcon = $this->mediaservice->uploadMedia($request->file('category_icon'), 'category/icon');
            $category->category_icon = $mediaIcon->id;  
        }       
        if ($request->hasFile('top_rated_image')) {
            $mediaTopRated = $this->mediaservice->uploadMedia($request->file('top_rated_image'), 'category/top-rated');
            $category->top_rated_image = $mediaTopRated->id;
        }

        // Category Page Overlay SVG (150x150)
        if ($request->hasFile('category_overlay_image')) {
            $mediaCatOverlay = $this->mediaservice->uploadMedia($request->file('category_overlay_image'), 'category/overlays');
            $category->category_overlay_image = $mediaCatOverlay->id;
        } elseif ($request->has('remove_category_overlay_image')) {
            $category->category_overlay_image = null;
        }
        if ($request->has('category_overlay_position')) {
            $category->category_overlay_position = $request->input('category_overlay_position', 'right');
        }

        // FAQ Page Overlay SVG (150x150)
        if ($request->hasFile('faq_overlay_image')) {
            $mediaFaqOverlay = $this->mediaservice->uploadMedia($request->file('faq_overlay_image'), 'category/overlays');
            $category->faq_overlay_image = $mediaFaqOverlay->id;
        } elseif ($request->has('remove_faq_overlay_image')) {
            $category->faq_overlay_image = null;
        }
        if ($request->has('faq_overlay_position')) {
            $category->faq_overlay_position = $request->input('faq_overlay_position', 'right');
        }

        // Comparison Page Overlay SVG (150x150)
        if ($request->hasFile('comparison_overlay_image')) {
            $mediaCompOverlay = $this->mediaservice->uploadMedia($request->file('comparison_overlay_image'), 'category/overlays');
            $category->comparison_overlay_image = $mediaCompOverlay->id;
        } elseif ($request->has('remove_comparison_overlay_image')) {
            $category->comparison_overlay_image = null;
        }
        if ($request->has('comparison_overlay_position')) {
            $category->comparison_overlay_position = $request->input('comparison_overlay_position', 'right');
        }

        $category->save();
        if ($category) {
            $slug = Str::slug($validate['name']);
            $originalSlug = $slug;
            $count = 1;
            while (CategoryTranslation::where('slug', $slug)
                ->where(function($query) use ($category, $language_id) {
                    $query->where('category_id', '!=', $category->id)
                          ->orWhere('lang_id', '!=', $language_id);
                })
                ->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            // dump($category->id);
            // dump($language_id);
            // dd();
            $comparisonSlug = !empty($validate['comparison_slug']) 
                ? Str::slug($validate['comparison_slug']) 
                : ($slug . '-comparison');

           // Process Dynamic Text Sections (H2 & H3)
            $textSectionsInput = $request->input('text_sections', []);
            $cleanTextSections = [];
            if (is_array($textSectionsInput)) {
                foreach ($textSectionsInput as $sec) {
                    if (!empty($sec['h2_title']) || !empty($sec['h2_text'])) {
                        $subList = [];
                        if (isset($sec['sub_sections']) && is_array($sec['sub_sections'])) {
                            foreach ($sec['sub_sections'] as $sub) {
                                if (!empty($sub['h3_title']) || !empty($sub['h3_text'])) {
                                    $subList[] = [
                                        'h3_title' => $sub['h3_title'] ?? '',
                                        'h3_text' => $sub['h3_text'] ?? ''
                                    ];
                                }
                            }
                        }
                        $cleanTextSections[] = [
                            'h2_title' => $sec['h2_title'] ?? '',
                            'h2_text' => $sec['h2_text'] ?? '',
                            'sub_sections' => $subList
                        ];
                    }
                }
            }

            // Process Category FAQs
            $catTransData = [
                'category_id'  => $category->id,
                'lang_id'      => $language_id,
                'name'         => $categoryName ?: ($validate['name'] ?? ''),
                'category_cap' => $categoryName ?: ($validate['name'] ?? ''),
                'category_tc'  => $request->input('category_tc'),
                'category_lc'  => $request->input('category_lc'),
                'entity1_cap'  => $request->input('entity1_cap'),
                'entity1_lc'   => $request->input('entity1_lc'),
                'entities1_cap'=> $request->input('entities1_cap'),
                'entities1_lc' => $request->input('entities1_lc'),
                'entity1_short_cap' => $request->input('entity1_short_cap'),
                'entity1_short_lc'  => $request->input('entity1_short_lc'),
                'entities1_short_cap' => $request->input('entities1_short_cap'),
                'entities1_short_lc'  => $request->input('entities1_short_lc'),
                'entity2_cap'  => $request->input('entity2_cap'),
                'entity2_lc'   => $request->input('entity2_lc'),
                'entities2_cap'=> $request->input('entities2_cap'),
                'entities2_lc' => $request->input('entities2_lc'),
                'entity2_short_cap' => $request->input('entity2_short_cap'),
                'entity2_short_lc'  => $request->input('entity2_short_lc'),
                'entities2_short_cap' => $request->input('entities2_short_cap'),
                'entities2_short_lc'  => $request->input('entities2_short_lc'),
                'comparison_name' => $validate['comparison_name'] ?? null,
                'glance_option_3_label' => $request->input('glance_option_3_label'),
                'glance_option_4_label' => $request->input('glance_option_4_label'),
                'page_title'   => $validate['page_title'] ?? null,
                'description'  => $validate['description'],
                'faq_h1_title' => $request->input('faq_h1_title'),
                'faq_subtext'  => $request->input('faq_subtext'),
                // 'top_rated_description' is now managed centrally on the Top Rated page
                'text_sections'=> !empty($cleanTextSections) ? json_encode($cleanTextSections) : null,
                'meta_title'   => $validate['meta_title'] ?? null,
                'meta_description' => $validate['meta_description'] ?? null,
                'faq_meta_title'   => $validate['faq_meta_title'] ?? null,
                'faq_meta_description' => $validate['faq_meta_description'] ?? null,
                'slug'         => $slug,
                'comparison_slug' => $comparisonSlug,
                'is_important' => $request->has('is_important') ? 1 : 0,
            ];

            // Only update template fields if explicitly passed in the request (e.g. from content templates / legacy)
            $optionalTemplateFields = [
                'see_all_text', 'title',
                'top_rated_listing_headline', 'top_rated_listing_subtext',
                'compare_alternatives_headline', 'compare_alternatives_subtext',
                'community_says_headline', 'community_says_subtext',
                'explore_subcategories_headline', 'explore_subcategories_subtext',
                'popular_comparisons_headline', 'popular_comparisons_subtext',
                'compare_tool_headline', 'compare_tool_subtext',
                'category_faqs_headline', 'category_faqs_subtext',
                'faq_about_headline', 'faq_about_description',
                'faq_explore_headline', 'faq_explore_subtext',
            ];
            foreach ($optionalTemplateFields as $otf) {
                if ($request->has($otf)) {
                    $catTransData[$otf] = $request->input($otf);
                }
            }

            CategoryTranslation::updateOrCreate(
                [
                    'lang_id' => (int) $language_id,
                    'category_id' => $category->id
                ],
                $catTransData
            );

            // Handle rating criteria
            // 1. Process Default Criteria Descriptions (Only for Main Categories; Subcategories inherit from parent)
            if (!$category->parent_id) {
                $submittedDefaultCriteria = $request->input('default_rating_criteria', []);
                $defaultMaster = \App\Models\DefaultRatingCriteria::all();
                foreach ($defaultMaster as $def) {
                    $desc = isset($submittedDefaultCriteria[$def->key]['description'])
                        ? trim($submittedDefaultCriteria[$def->key]['description'])
                        : null;

                    CategoryRatingCriteria::updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'default_key' => $def->key,
                        ],
                        [
                            'name' => $def->name,
                            'description' => $desc,
                            'is_default' => true,
                        ]
                    );
                }
            } else {
                // If it is a subcategory, clean up any subcategory-level default criteria records so it inherits directly from parent
                CategoryRatingCriteria::where('category_id', $category->id)
                    ->where('is_default', true)
                    ->delete();
            }

            // 2. Process Custom Criteria (Category or Subcategory specific)
            $submittedExistingCriteria = $request->input('existing_rating_criteria', []) ?? [];
            $submittedNewCriteria = $request->input('new_rating_criteria', []) ?? [];

            $existingIds = array_keys($submittedExistingCriteria);
            $category->ratingCriteria()
                ->where('is_default', false)
                ->whereNotIn('id', $existingIds)
                ->delete();

            // Update existing custom criteria
            foreach ($submittedExistingCriteria as $criteriaId => $data) {
                $name = is_array($data) ? ($data['name'] ?? '') : $data;
                $desc = is_array($data) ? ($data['description'] ?? null) : null;

                if (trim($name) !== '') {
                    CategoryRatingCriteria::where('id', $criteriaId)
                        ->where('category_id', $category->id)
                        ->where('is_default', false)
                        ->update([
                            'name' => trim($name),
                            'description' => $desc ? trim($desc) : null,
                        ]);
                }
            }

            // Add new custom criteria
            if (is_array($submittedNewCriteria)) {
                foreach ($submittedNewCriteria as $newCrit) {
                    $name = is_array($newCrit) ? ($newCrit['name'] ?? '') : $newCrit;
                    $desc = is_array($newCrit) ? ($newCrit['description'] ?? null) : null;

                    if (trim($name) !== '') {
                        CategoryRatingCriteria::create([
                            'category_id' => $category->id,
                            'name' => trim($name),
                            'description' => $desc ? trim($desc) : null,
                            'is_default' => false,
                        ]);
                    }
                }
            }

            return redirect()->route('categories')->with('success', 'Category saved successfully');
        } else {
            return redirect()->route('categories')->with('error', 'Failed to save the category.');
        }
    }
    public function remove(Request $request, $id)
    {
        try {
            // Resolve Category model by either Category ID or CategoryTranslation ID
            $category = Category::find($id);
            if (!$category) {
                $categoryTranslation = CategoryTranslation::find($id);
                if ($categoryTranslation) {
                    $category = Category::find($categoryTranslation->category_id);
                }
            }

            if (!$category) {
                return redirect()->back()->with('error', 'Category not found.');
            }

            if ($category->parent_id === null && $category->subCategories()->exists()) {
                return redirect()->back()->with('error', 'Cannot delete a parent category that contains active sub-categories. Please delete or re-assign sub-categories first.');
            }

            DB::beginTransaction();

            // 1. Reassign businesses and clean up subcategory pivot relationships
            $this->reassignBusinessesBeforeCategoryDelete($category);

            // 2. Clean up media files
            if ($category->image) {
                $imagePath = public_path('CategoryImages/' . $category->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
                $imagePath = public_path('category/images/' . $category->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            if ($category->category_icon) {
                $iconPath = public_path('CategoryIcon/' . $category->category_icon);
                if (File::exists($iconPath)) {
                    File::delete($iconPath);
                }
                $iconPath = public_path('category/icon/' . $category->category_icon);
                if (File::exists($iconPath)) {
                    File::delete($iconPath);
                }
            }

            // 3. Clean up category-specific records
            CategoryTranslation::where('category_id', $category->id)->delete();
            $category->ratingCriteria()->delete();
            $category->proCons()->delete();
            $category->features()->delete();

            // Clean up category topics and their translations
            $topics = BusinessCategoryTopic::where('category_id', $category->id)->get();
            foreach ($topics as $topic) {
                BusinessCategoryTopicTranslation::where('business_category_topic_id', $topic->id)->delete();
                $topic->delete();
            }

            // 4. Detach products
            $products = $category->products;
            foreach ($products as $product) {
                $product->categories()->detach($category->id);
                if ($product->categories()->count() === 0) {
                    $product->businesses()->detach();
                    $product->countries()->detach();
                    $product->translations()->delete();
                    $product->delete();
                }
            }

            // 5. Delete category
            $category->delete();

            DB::commit();

            return redirect()->route('categories')->with('success', 'Category deleted successfully. All associated businesses have been preserved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Safely reassign businesses when their primary category/subcategory is deleted.
     */
    protected function reassignBusinessesBeforeCategoryDelete(Category $category)
    {
        $englishLangId = Language::where('lang_code', 'en-us')->value('id') ?? 1;

        // 1. Get all businesses where primary category_id is this category
        $businesses = Business::where('category_id', $category->id)->get();

        foreach ($businesses as $business) {
            // Find other subcategories this business belongs to in the pivot table (excluding the one being deleted)
            $remainingSubcategories = $business->subCategories()
                ->where('categories.id', '!=', $category->id)
                ->with(['categoryTranslations'])
                ->get();

            if ($remainingSubcategories->isNotEmpty()) {
                // Sort remaining subcategories alphabetically by English name (fallback to first translation name)
                $nextSubcategory = $remainingSubcategories->sort(function ($a, $b) use ($englishLangId) {
                    $nameA = $a->categoryTranslations->firstWhere('lang_id', $englishLangId)?->name
                        ?? $a->categoryTranslations->first()?->name
                        ?? ('Category #' . $a->id);
                    $nameB = $b->categoryTranslations->firstWhere('lang_id', $englishLangId)?->name
                        ?? $b->categoryTranslations->first()?->name
                        ?? ('Category #' . $b->id);
                    return strnatcasecmp(trim($nameA), trim($nameB));
                })->first();

                $business->update(['category_id' => $nextSubcategory->id]);
            } else {
                // No other subcategories: leave business without a primary subcategory
                $business->update(['category_id' => null]);
            }
        }

        // 2. Remove all pivot entries in business_sub_category for this category
        DB::table('business_sub_category')->where('category_id', $category->id)->delete();
    }

    public function faqs($id)
    {
        $langId = Language::where('lang_code', getCurrentLocale())->value('id');
        $category = Category::with([
            'categoryTranslations' => function ($query) use ($langId) {
                $query->where('lang_id', $langId);
            }
        ])->findOrFail($id);
        
        $translation = $category->categoryTranslations->first();
        $category_faqs = [];
        if ($translation && !empty($translation->faqs)) {
            $category_faqs = json_decode($translation->faqs, true) ?? [];
        }
        
        return view('Admin.categories.faqs', compact('category', 'category_faqs', 'translation'));
    }

    public function addTopic($id = null)
    {
        $topic_data = null;
        $langId = Language::where('lang_code', getCurrentLocale())->value('id');

        if ($id) {
            $category = Category::with([
                'topics.translations' => function ($query) use ($langId) {
                    $query->where('lang_id', $langId);
                },
                'categoryTranslations' => function ($query) use ($langId) {
                    $query->where('lang_id', $langId);
                }
            ])->findOrFail($id);

            $topic_data = $category;
        }

        return view('Admin.categories.add_topic', compact('topic_data'));
    }

    public function storeTopic(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
        ]);

        try {
            // Get current language ID
            $langId = Language::where('lang_code', getCurrentLocale())->value('id');

            // Create the category topic
            $topic = BusinessCategoryTopic::create([
                'category_id' => $request->category_id,
            ]);

            // Create the translation
            BusinesscategoryTopicTranslation::create([
                'business_category_topic_id' => $topic->id,
                'lang_id' => $langId,
                'title' => $request->title,
            ]);

            // Load the translation for response
            $topic->load(['translations' => function ($query) use ($langId) {
                $query->where('lang_id', $langId);
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Topic added successfully!',
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->translations->first()?->title ?? 'No title',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add topic. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTopic(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:business_category_topics,id',
            'title' => 'required|string|max:255',
        ]);

        try {
            // Get current language ID
            $langId = Language::where('lang_code', getCurrentLocale())->value('id');

            // Find the topic
            $topic = BusinessCategoryTopic::findOrFail($request->topic_id);

            // Update or create the translation
            $translation = BusinesscategoryTopicTranslation::updateOrCreate([
                'business_category_topic_id' => $topic->id,
                'lang_id' => $langId,
            ], [
                'title' => $request->title,
            ]);

            // Load the translation for response
            $topic->load(['translations' => function ($query) use ($langId) {
                $query->where('lang_id', $langId);
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Topic updated successfully!',
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->translations->first()?->title ?? 'No title',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update topic. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTopic(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:business_category_topics,id',
        ]);

        try {
            // Find the topic
            $topic = BusinessCategoryTopic::findOrFail($request->topic_id);

            // Delete all translations first
            BusinesscategoryTopicTranslation::where('business_category_topic_id', $topic->id)->delete();

            // Delete the topic
            $topic->delete();

            return response()->json([
                'success' => true,
                'message' => 'Topic deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete topic. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTopicDetails($id)
    {
        try {
            // Get current language ID
            $langId = Language::where('lang_code', getCurrentLocale())->value('id');

            // Find the topic with translations
            $topic = BusinessCategoryTopic::with(['translations' => function ($query) use ($langId) {
                $query->where('lang_id', $langId);
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->translations->first()?->title ?? 'No title',
                    'category_id' => $topic->category_id,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Topic not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'lang_id' => 'required|exists:languages,id',
            'status' => 'required|in:0,1',
        ]);

        $categoryTranslation = CategoryTranslation::where('category_id', $request->category_id)
            ->where('lang_id', $request->lang_id)
            ->first();

        if (!$categoryTranslation) {
            $englishLangId = Language::where('lang_code', 'en-us')->value('id') ?? 1;
            $englishTrans = CategoryTranslation::where('category_id', $request->category_id)
                ->where('lang_id', $englishLangId)
                ->first();

            $categoryTranslation = CategoryTranslation::create([
                'category_id' => $request->category_id,
                'lang_id' => $request->lang_id,
                'status' => (int) $request->status,
                'name' => $englishTrans ? $englishTrans->name : 'Category #' . $request->category_id,
                'description' => $englishTrans ? $englishTrans->description : '',
                'slug' => $englishTrans ? ($englishTrans->slug . '-' . $request->lang_id) : ('category-' . $request->category_id . '-' . $request->lang_id),
            ]);
        } else {
            $categoryTranslation->status = (int) $request->status;
            $categoryTranslation->save();
        }

        return response()->json([
            'success' => true,
            'status' => $categoryTranslation->status,
            'message' => 'Category status updated successfully for this country.',
        ]);
    }
}

