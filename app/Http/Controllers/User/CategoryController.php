<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Session;
use App\Models\CategoryTranslation;
use App\Models\SiteLanguages;
use App\Models\Language;
use App\Models\CategoryPageContent;
use App\Models\Media;
use App\Models\Review;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $lang = Session::get('current_lang');
        $lang_id = getCurrentLanguageID();
        $categoryImages = CategoryPageContent::where('lang_id', 1)
            ->whereIn('meta_key', ['header_image', 'header_bg_image'])
            ->get();

        $headerImage = $categoryImages->where('meta_key', 'header_image')->first();
        $backgroundImage = $categoryImages->where('meta_key', 'header_bg_image')->first();

        $categoriesContents = CategoryPageContent::where('lang_id', $lang_id)->where('type', 'text')->pluck('meta_value', 'meta_key');

        if ($categoriesContents->isEmpty()) {
            $categoriesContents = CategoryPageContent::where('lang_id', 1)->where('type', 'text')->pluck('meta_value', 'meta_key');
        }
        // $categories = Category::whereHas('translations', function ($query) use ($lang_id) {
        //     $query->where('lang_id', $lang_id);
        // })
        // ->with([
        //     'translations',
        //     'iconMedia:id,dir_path,file_name',
        //     'imageMedia:id,dir_path,file_name',
        // ])
        // ->get();
        // $categories = Category::onlyParents()
        // ->where('status', 1)
        // ->with('translations', 'imageMedia')
        // ->get();
        $categories = Category::onlyParents()
    ->where('status', 1)
    ->withCount('subCategories')
    ->orderByDesc('sub_categories_count')
    ->with('translations', 'imageMedia')
    ->get();


        // dd($categories);

        // Return the view with all necessary data
        return view('User.category.index', compact(
            'categories', 'categoriesContents', 'categoryImages', 'backgroundImage', 'headerImage',
        ));
    }
     public function categoryDetail($lang_code,$slug){
       return view('User.category.category_detail', compact('slug'));
     }



    // Business Category Translation

    // public function BusinessCategoryTranslationStore(Request $request)
    // {
    //     $request->validate([
    //         'category_id' => 'required|integer',
    //         'source_language' => 'required|integer',
    //         'target_languages' => 'required|array',
    //     ]);

    //     $categoryId = $request->category_id;
    //     $sourceLangId = $request->source_language;
    //     $targetLangIds = $request->target_languages;

    //     foreach ($targetLangIds as $langId) {
    //         if ($langId == $sourceLangId) continue;

    //         CategoryTranslation::updateOrCreate(
    //             ['category_id' => $categoryId, 'lang_id' => $langId],
    //             [
    //                 'name' => 'Translated name for ' . $langId,
    //                 'updated_at' => now()
    //             ]
    //         );
    //     }

    //     return response()->json(['success' => true]);
    // }



    // public function BusinessCategoryTranslationStore(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'category_id' => 'required|integer',
    //             'source_lang_id' => 'required|integer',
    //             'target_lang_ids' => 'required|array|min:1',
    //             'target_lang_ids.*' => 'integer',
    //         ]);

    //         $categoryId   = $request->category_id;
    //         $sourceLangId = $request->source_lang_id;
    //         $targetLangIds = $request->target_lang_ids;

    //         // Fetch original category data
    //         // $category = Category::with('categoryTranslations')->where('id', '11')->first();
    //         $category = CategoryTranslation::where('category_id', $categoryId)
    //         ->where('lang_id', 1)
    //         ->first();
    //         // $category=CategoryTranslation::find();

    //         // dd($category);

    //         $targetLangIds = array_map('intval', $request->target_lang_ids);

    //         // dd($targetLangIds[0]); // 2 (int)

    //         $lang_code_current=Language::where('id',$targetLangIds[0])->first();
    //         // getLanguageCode($targetLangIds[0]);

    //         // dd($lang_code_current->lang_code);

    //         $translatedName = website_translator($category->name, $lang_code_current->lang_code);
    //         dd($translatedName);

    //         $translatedName = website_translator($category->name, getLanguageCode($targetLangId));
    //         $translatedDescription = website_translator($category->description, getLanguageCode($targetLangId));

    //         dd($translatedName , $translatedDescription);


    //         if (!$category) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Category not found.'
    //             ]);
    //         }

    //         foreach ($targetLangIds as $targetLangId) {

    //             // Use helper function to translate name
    //             $translatedName = website_translator($category->name, getLanguageCode($targetLangId));
    //             $translatedDescription = website_translator($category->description, getLanguageCode($targetLangId));

    //            dd($translatedName , $translatedDescription);
    //             // Generate slug (optional: you can use Str::slug)
    //             $slug = \Str::slug($translatedName) . '-' . $targetLangId;

    //             CategoryTranslation::updateOrCreate(
    //                 [
    //                     'category_id' => $categoryId,
    //                     'lang_id' => $targetLangId
    //                 ],
    //                 [
    //                     'source_lang_id' => $sourceLangId,
    //                     'name' => $translatedName,
    //                     'slug' => $slug,
    //                     'description' => $category->categoryTranslations[0]->description, // You can also translate description similarly
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ]
    //             );
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Translations saved successfully.'
    //         ]);

    //     } catch (\Throwable $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong: ' . $e->getMessage()
    //         ]);
    //     }
    // }



    public function BusinessCategoryTranslationStore(Request $request)
    {
        try {
            $request->validate([
                'category_id'       => 'required|integer',
                'source_lang_id'    => 'required|integer',
                'target_lang_ids'   => 'required|array|min:1',
                'target_lang_ids.*' => 'integer',
            ]);

            $categoryId    = (int) $request->category_id;
            $sourceLangId  = (int) $request->source_lang_id;
            $targetLangIds = array_map('intval', $request->target_lang_ids);

            // Get source translation or fallback to Category table
            $sourceTranslation = CategoryTranslation::where('category_id', $categoryId)
                ->where('lang_id', $sourceLangId)
                ->first();

            if ($sourceTranslation) {
                $sourceName = (string) ($sourceTranslation->name ?? '');
                $sourceDescription = (string) ($sourceTranslation->description ?? '');
            } else {
                $category = Category::find($categoryId);
                if (!$category) {
                    return response()->json([
                        'success' => false,
                        'message' => "Category not found for ID {$categoryId}"
                    ]);
                }
                $sourceName = (string) ($category->name ?? '');
                $sourceDescription = (string) ($category->description ?? '');
            }

            foreach ($targetLangIds as $targetLangId) {
                $langCode = getLanguageCode($targetLangId);

                if (!$langCode) {
                    \Log::error("Missing language code for lang ID: {$targetLangId}");
                    continue;
                }

                // Translate using helper
                $translatedNameRaw = website_translator($sourceName, $langCode);
                $translatedDescRaw = website_translator($sourceDescription ?? '', $langCode);

                \Log::info("Translating category {$categoryId} to {$langCode}", [
                    'name' => $translatedNameRaw,
                    'desc' => $translatedDescRaw,
                ]);

                // Fallbacks
                $translatedName = trim((string) $translatedNameRaw);
                if ($translatedName === '' || $translatedName === '0') {
                    $translatedName = $sourceName ?: "category-{$categoryId}";
                }

                $translatedDescription = trim((string) $translatedDescRaw);
                if ($translatedDescription === '' || $translatedDescription === '0') {
                    $translatedDescription = $sourceDescription ?? '';
                }

                // Slug
                $baseSlug = \Str::slug($translatedName);
                if (empty($baseSlug)) {
                    $baseSlug = 'category';
                }

                $slug = "{$baseSlug}-{$categoryId}-{$targetLangId}";

                // Ensure uniqueness
                $exists = CategoryTranslation::where('slug', $slug)
                    ->where(function ($q) use ($categoryId, $targetLangId) {
                        $q->where('category_id', '!=', $categoryId)
                          ->orWhere('lang_id', '!=', $targetLangId);
                    })->exists();

                if ($exists) {
                    $slug .= '-' . substr(uniqid(), -6);
                }

                // Save or update
                CategoryTranslation::updateOrCreate(
                    [
                        'category_id' => $categoryId,
                        'lang_id'     => $targetLangId
                    ],
                    [
                        'source_lang_id' => $sourceLangId,
                        'name'           => $translatedName,
                        'slug'           => $slug,
                        'description'    => $translatedDescription,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Translations saved successfully.'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Translation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }




}


