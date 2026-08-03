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

        $categories = Category::with(['parent', 'subCategories', 'categoryTranslations'])->get();

        foreach ($categories as $category) {
            $englishTrans = $category->categoryTranslations->where('lang_id', $englishLangId)->first()
                ?? $category->categoryTranslations->first();
            $selectedTrans = $category->categoryTranslations->where('lang_id', $siteLanguage)->first();

            $category->english_name = $englishTrans ? $englishTrans->name : 'Unnamed Category';
            $category->translated_name = $selectedTrans ? $selectedTrans->name : null;
            $category->translation_id = $selectedTrans ? $selectedTrans->id : ($englishTrans ? $englishTrans->id : null);
            $category->is_active_for_country = $selectedTrans ? ($selectedTrans->status ?? 1) : 1;
        }

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
        $rating_criteria = [];

        if ($id != null) {
            $category_data = CategoryTranslation::where('id', $id)->first()->toArray();
            $categoryId = $category_data['category_id'];
            $category = Category::where('id', $categoryId)->first();
            if ($category) {
                $hasSubcategories = $category->subCategories()->exists();
                $hasItems = $category->businesses()->exists() || $category->products()->exists();
                $category_image = Media::where('id', $category->image)->first();
                $category_icon = Media::where('id', $category->category_icon)->first();
                $category_image_url = $category_image ? asset($category_image->dir_path . '/' . $category_image->file_name) : null;
                $category_icon_url = $category_icon ? asset($category_icon->dir_path . '/' . $category_icon->file_name) : null;
                $rating_criteria = $category->ratingCriteria()->get();
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

        return view('Admin.categories.add', compact('category_data', 'category', 'category_image_url', 'category_icon_url', 'parentCategories', 'hasSubcategories', 'hasItems', 'rating_criteria'));
    }

    // public function add_process(Request $request)
    // {

    //     $language_id = Language::where('lang_code', getCurrentLocale())->value('id');
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
    //         while (CategoryTranslation::where('slug', $slug)->where('lang_id', $language_id)->where('category_id', '!=', $category->id)->exists()) {
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

        $language_id = Language::where('lang_code', getCurrentLocale())->value('id');
        // dd($request->all());
        $isNewCategory = !$request->category_id;
        
        $rules = [
            'name' => [
                'required',
                'min:3',
                'max:255'
                
            ],
            'title' => 'nullable|string|max:255',
            'comparison_slug' => 'nullable|string|max:255',
            'description' => 'required|string|min:10',
            'image' => 'nullable|mimes:svg,png,jpg,jpeg,webp|max:2048',
            'category_icon' => $isNewCategory
            ? 'required|mimes:svg,png,jpg,jpeg,webp|max:2048'
            : 'nullable|mimes:svg,png,jpg,jpeg,webp|max:2048',
            'is_parent' => 'nullable',
            'parent_id' => 'nullable|required_without:is_parent|exists:categories,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $is_parent = $request->has('is_parent');
            $parent_id = $request->parent_id;



            if ($request->category_id) {
                $categoryTranslation = CategoryTranslation::find($request->category_id);
                if ($categoryTranslation) {
                    $category_id = $categoryTranslation->category_id;
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
                    $category = Category::find($request->category_id);
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

        if ($request->hasFile('image')) {
            $media = $this->mediaservice->uploadMedia($request->file('image'), 'category/images');
            $category->image = $media->id;
        }
        if ($request->hasFile('category_icon')) {
            $mediaIcon = $this->mediaservice->uploadMedia($request->file('category_icon'), 'category/icon');
            $category->category_icon = $mediaIcon->id;  
        }       
        $category->save();
        if ($category) {
            $slug = Str::slug($validate['name']);
            $originalSlug = $slug;
            $count = 1;
            while (CategoryTranslation::where('slug', $slug)->where('lang_id', $language_id)->where('category_id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            // dump($category->id);
            // dump($language_id);
            // dd();
           CategoryTranslation::updateOrCreate(
                [
                    'lang_id' => (int) $language_id,
                    'category_id' => $category_id
                ],
                [
                    'category_id'  => $category->id,
                    'lang_id'      => $language_id,
                    'name'         => $validate['name'],
                    'title'        => $validate['title'] ?? null,
                    'description'  => $validate['description'],
                    'slug'         => $slug,
                    'comparison_slug' => $validate['comparison_slug'] ?? null,
                    'is_important' => $request->has('is_important') ?? 0,
                ]
            );

            // Handle rating criteria
            $submittedExistingCriteria = $request->input('existing_rating_criteria', []) ?? null;
            $submittedNewCriteria = $request->input('new_rating_criteria', []) ?? null;
            
            // Delete criteria not in the submitted list
            $existingIds = array_keys($submittedExistingCriteria);
            $category->ratingCriteria()->whereNotIn('id', $existingIds)->delete();

            // Update existing criteria (preserves historical reviews!)
            foreach ($submittedExistingCriteria as $criteriaId => $name) {
                if (trim($name) !== '') {
                    CategoryRatingCriteria::where('id', $criteriaId)->where('category_id', $category->id)->update(['name' => trim($name)]);
                }
            }

            // Add new criteria
            foreach ($submittedNewCriteria as $name) {
                if (trim($name) !== '') {
                    CategoryRatingCriteria::create([
                        'category_id' => $category->id,
                        'name' => trim($name)
                    ]);
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
            $categoryTranslation = CategoryTranslation::findOrFail($id);
            $category = Category::findOrFail($categoryTranslation->category_id);

            if ($category->parent_id === null && $category->subCategories()->exists()) {
                return redirect()->back()->with('error', 'Cannot delete a parent category that contains active sub-categories. Please delete or re-assign sub-categories first.');
            }

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
            $categoryTranslation->delete();
            $category->features()->delete();
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
            $category->delete();
            return redirect()->back()->with('success', 'Category and related products (if unused elsewhere) deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
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

