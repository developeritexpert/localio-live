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
            ->where(function ($q) {
                $q->has('subCategories')->orWhereHas('businesses');
            })
            ->whereHas('translations', function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
            })
            ->withCount('subCategories')
            ->orderByDesc('sub_categories_count')
            ->with(['translations' => function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id);
            }, 'imageMedia'])
            ->get();


        // dd($categories);

        // Return the view with all necessary data
        return view('User.category.index', compact(
            'categories', 'categoriesContents', 'categoryImages', 'backgroundImage', 'headerImage',
        ));
    }
     public function categoryDetail($lang_code, $slug, $page = null){
       $lang_id = getCurrentLanguageID();
       $categoryExists = Category::whereHas('translations', function ($query) use ($slug, $lang_id) {
           $query->where('slug', $slug)->where('lang_id', $lang_id);
       })->orWhereHas('translations', function ($query) use ($slug) {
           $query->where('slug', $slug);
       })->exists();

       if (!$categoryExists) {
           abort(404);
       }

       return view('User.category.category_detail', compact('slug', 'page'));
     }

     public function subCategoryFeatureDetail($lang_code, $category_slug, $subcategory_slug, $feature_slug){
       return view('User.category.category_detail', [
           'slug' => $subcategory_slug,
           'feature_slug' => $feature_slug,
           'page' => 1
       ]);
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





    public function categoryFaqs($locale, $slug)
    {
        $lang_id = getCurrentLanguageID();

        $category = Category::whereHas('translations', function ($query) use ($slug, $lang_id) {
            $query->where('slug', $slug)->where('lang_id', $lang_id);
        })->first();

        if (!$category) {
            $category = Category::whereHas('translations', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })->first();
        }

        if (!$category) {
            abort(404, 'Category not found');
        }

        $categoryTranslation = $category->translations->firstWhere('lang_id', $lang_id) ?? $category->translations->first();
        $parentCategory = $category->parent;
        $parentTrans = $parentCategory ? ($parentCategory->translations->firstWhere('lang_id', $lang_id) ?? $parentCategory->translations->first()) : null;
        $catName = $categoryTranslation->name ?? $category->name ?? 'Category';

        $faqs = [];
        if (!empty($categoryTranslation->faqs)) {
            $decoded = is_array($categoryTranslation->faqs) ? $categoryTranslation->faqs : json_decode($categoryTranslation->faqs, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $faqs = $decoded;
            }
        }

        if (empty($faqs)) {
            $faqs = [
                [
                    'question' => "How do I choose the best " . strtolower($catName) . " provider?",
                    'answer' => "Compare essential features, user ratings, transparent pricing, and reliability. Assess whether the provider aligns with your specific technical and budgetary requirements."
                ],
                [
                    'question' => "What factors influence " . strtolower($catName) . " rankings on Localio?",
                    'answer' => "Rankings are based on authentic customer reviews, verified user satisfaction scores, pricing value, and overall feature completeness."
                ],
                [
                    'question' => "Are all " . strtolower($catName) . " providers verified by Localio?",
                    'answer' => "We verify listing details and user reviews through automated checks and community moderation to ensure honest recommendations."
                ],
                [
                    'question' => "Can I leave a review for a " . strtolower($catName) . " solution I use?",
                    'answer' => "Yes, search for the provider on Localio, navigate to its profile, and submit your detailed rating and review."
                ]
            ];
        }

        $catIds = [$category->id];
        if ($category->parent_id === null) {
            $childIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
            $catIds = array_merge($catIds, $childIds);
        }

        $recentReviews = \App\Models\Review::whereHas('business', function ($q) use ($catIds) {
            $q->whereIn('category_id', $catIds)
              ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                  $subQ->whereIn('categories.id', $catIds);
              });
        })
        ->where('status', 'active')
        ->with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $lang_id),
            'business.translations' => fn($q) => $q->where('lang_id', $lang_id)
        ])
        ->orderByDesc('created_at')
        ->take(2)
        ->get();

        if ($recentReviews->isEmpty()) {
            $recentReviews = \App\Models\Review::where('status', 'active')
                ->with([
                    'user',
                    'translations' => fn($q) => $q->where('language_id', $lang_id),
                    'business.translations' => fn($q) => $q->where('lang_id', $lang_id)
                ])
                ->orderByDesc('created_at')
                ->take(2)
                ->get();
        }

        return view('User.category.category_faqs', compact(
            'category', 'categoryTranslation', 'parentCategory', 'parentTrans', 
            'catName', 'faqs', 'recentReviews', 'lang_id'
        ));
    }

    public function categoryComparisons($locale, $slug)
    {
        $lang_id = getCurrentLanguageID();

        $category = Category::whereHas('translations', function ($query) use ($slug, $lang_id) {
            $query->where('slug', $slug)->where('lang_id', $lang_id);
        })->first();

        if (!$category) {
            $category = Category::whereHas('translations', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })->first();
        }

        if (!$category) {
            abort(404, 'Category not found');
        }

        $categoryTranslation = $category->translations->firstWhere('lang_id', $lang_id) ?? $category->translations->first();
        $parentCategory = $category->parent;
        $parentTrans = $parentCategory ? ($parentCategory->translations->firstWhere('lang_id', $lang_id) ?? $parentCategory->translations->first()) : null;
        $catName = $categoryTranslation->name ?? $category->name ?? 'Category';

        $catIds = [$category->id];
        if ($category->parent_id === null) {
            $childIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
            $catIds = array_merge($catIds, $childIds);
        }

        $businesses = \App\Models\Business::where(function ($q) use ($catIds) {
            $q->whereIn('category_id', $catIds)
              ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                  $subQ->whereIn('categories.id', $catIds);
              });
        })
        ->where('status', 1)
        ->whereHas('translations', function ($q) use ($lang_id) {
            $q->where('lang_id', $lang_id);
        })
        ->withCount(['reviews as active_reviews_count' => fn($q) => $q->where('status', 'active')])
        ->withAvg(['reviews as average_rating' => fn($q) => $q->where('status', 'active')], 'rating')
        ->with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'products.prices'
        ])
        ->orderByDesc('is_affiliate')
        ->orderByDesc('average_rating')
        ->orderByDesc('active_reviews_count')
        ->get();

        $vsKey = static_text('vs_keyword') ?: 'vs';
        $vsKeySlug = \Str::slug($vsKey);
        $compSlug = $categoryTranslation->comparison_slug ?? 'compare';

        // Build pairwise comparisons
        $allPairs = [];
        $count = $businesses->count();
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $b1 = $businesses[$i];
                $b2 = $businesses[$j];
                $b1Trans = $b1->translations->first();
                $b2Trans = $b2->translations->first();
                $b1Name = $b1Trans->name ?? $b1->name ?? 'Business 1';
                $b2Name = $b2Trans->name ?? $b2->name ?? 'Business 2';
                $b1Slug = $b1Trans->slug ?? $b1->slug;
                $b2Slug = $b2Trans->slug ?? $b2->slug;

                $url = route('product-comparison.seo', [
                    'locale' => app()->getLocale(),
                    'comparison_slug' => $compSlug,
                    'comparison_businesses' => \Str::slug($b1Name) . '-' . $vsKeySlug . '-' . \Str::slug($b2Name)
                ]);

                $allPairs[] = [
                    'business_1' => $b1,
                    'business_1_name' => $b1Name,
                    'business_1_rating' => (float)($b1->average_rating ?? 0),
                    'business_1_reviews' => (int)($b1->active_reviews_count ?? 0),
                    'business_2' => $b2,
                    'business_2_name' => $b2Name,
                    'business_2_rating' => (float)($b2->average_rating ?? 0),
                    'business_2_reviews' => (int)($b2->active_reviews_count ?? 0),
                    'url' => $url,
                ];
            }
        }

        // Paginate pairs
        $page = request()->get('page', 1);
        $perPage = 12;
        $total = count($allPairs);
        $slice = array_slice($allPairs, ($page - 1) * $perPage, $perPage);
        $paginatedComparisons = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice, $total, $perPage, $page, ['path' => request()->url(), 'query' => request()->query()]
        );

        $recentReviews = \App\Models\Review::whereHas('business', function ($q) use ($catIds) {
            $q->whereIn('category_id', $catIds)
              ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                  $subQ->whereIn('categories.id', $catIds);
              });
        })
        ->where('status', 'active')
        ->with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $lang_id),
            'business.translations' => fn($q) => $q->where('lang_id', $lang_id)
        ])
        ->orderByDesc('created_at')
        ->take(2)
        ->get();

        if ($recentReviews->isEmpty()) {
            $recentReviews = \App\Models\Review::where('status', 'active')
                ->with([
                    'user',
                    'translations' => fn($q) => $q->where('language_id', $lang_id),
                    'business.translations' => fn($q) => $q->where('lang_id', $lang_id)
                ])
                ->orderByDesc('created_at')
                ->take(2)
                ->get();
        }

        return view('User.category.category_comparisons', compact(
            'category', 'categoryTranslation', 'parentCategory', 'parentTrans',
            'catName', 'paginatedComparisons', 'recentReviews', 'businesses', 'lang_id'
        ));
    }

}
