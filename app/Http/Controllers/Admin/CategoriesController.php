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

        $siteLanguage = Language::where('lang_code', $locale)->value('id');
        $categories = collect();

        if ($siteLanguage) {
            $categories = CategoryTranslation::where('lang_id', $siteLanguage)->get();
            // defult language set
            if ($categories->isEmpty() && $locale !== 'en-us') {
                $englishLangId = Language::where('lang_code', 'en-us')->value('id');
                $categories = CategoryTranslation::where('lang_id', $englishLangId)->get();
            }
        }

        return view('Admin.categories.index', compact('categories', 'siteLanguage'));
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
        if ($id != null) {
            $category_data = CategoryTranslation::where('id', $id)->first()->toArray();
            $category = Category::where('id', $category_data['category_id'])->first(['image', 'category_icon']);
            $category_image = Media::where('id', $category->image)->first();
            $category_icon = Media::where('id', $category->category_icon)->first();
            $category_image_url = $category_image ? asset($category_image->dir_path . '/' . $category_image->file_name) : null;
            $category_icon_url = $category_icon ? asset($category_icon->dir_path . '/' . $category_icon->file_name) : null;
            return view('Admin.categories.add', compact(['category_data', 'category_image_url','category_icon_url']));
        } else {
            return view('Admin.categories.add');
        }
    }
    public function add_process(Request $request)
    {
        $language_id = Language::where('lang_code', getCurrentLocale())->value('id');
        $isNewCategory = !$request->category_id;
        $rules = [
            'name' => [
                'required',
                'min:3',
                'max:255',
                Rule::unique('category_translations', 'name')
                    ->where(function ($query) use ($language_id) {
                        return $query->where('lang_id', $language_id);
                    })
                    ->ignore($request->category_id),
            ],

            'description' => 'required|string|min:10',
            'image' => 'nullable|mimes:svg,png,jpg,jpeg,webp|max:2048',
            'category_icon' => $isNewCategory
            ? 'required|mimes:svg,png,jpg,jpeg,webp|max:2048'
            : 'nullable|mimes:svg,png,jpg,jpeg,webp|max:2048',

        ];
        $validate = $request->validate($rules);
        $category_id = null;
        if ($request->category_id) {
            $categoryTranslation = CategoryTranslation::where('id', $request->category_id)->first();
            if ($categoryTranslation) {
                $category_id = $categoryTranslation->category_id;
            }
        }
        $category = $category_id ? Category::find($category_id) : new Category();
        if (!$category) {
            $category = new Category();
        }
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
            while (CategoryTranslation::where('slug', $slug)->where('lang_id', $language_id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            CategoryTranslation::updateOrCreate(
                ['lang_id' => (int)$language_id, 'category_id' => $category->id],
                [
                    'category_id' => $category->id,
                    'lang_id' => $language_id,
                    'name' => $validate['name'],
                    'description' => $validate['description'],
                    'slug' => $slug
                ]
            );
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

}
