<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\MetaTagService;
use Illuminate\Support\Facades\Session;
use App\Models\CategoryTranslation;
use App\Models\SiteLanguages;
use App\Models\Language;
use App\Models\CategoryPageContent;
use App\Models\Media;
use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


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
       $matchedLang = Language::where('lang_code', $lang_code)->first();
       $lang_id = $matchedLang ? $matchedLang->id : getCurrentLanguageID();
       $category = Category::whereHas('translations', function ($query) use ($slug, $lang_id) {
           $query->where('slug', $slug)->where('lang_id', $lang_id);
       })->orWhereHas('translations', function ($query) use ($slug) {
           $query->where('slug', $slug);
       })->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)])->first();

       if (!$category) {
           abort(404);
       }

       $catTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection
           ? $category->translations->first()
           : ($category->translations ?? $category->translation);

       if (!$catTrans) {
           $catTrans = $category->categoryTranslations()->where('lang_id', $lang_id)->first()
               ?? $category->categoryTranslations()->where('lang_id', 1)->first()
               ?? $category->categoryTranslations()->first();
       }

       $resolvedMeta = MetaTagService::resolveCategoryMeta($category, $lang_id);
       $metaTitle = $resolvedMeta['title'];
       $metaDescription = $resolvedMeta['description'];

       $feature_slug = null;
       if ($page && !is_numeric($page)) {
           $feature_slug = $page;
           $page = 1;
       }

       return view('User.category.category_detail', compact('slug', 'page', 'feature_slug', 'metaTitle', 'metaDescription'));
     }

     public function subCategoryFeatureDetail($lang_code, $category_slug, $subcategory_slug, $feature_slug){
       if ($category_slug === $subcategory_slug) {
           return redirect()->to('/' . $lang_code . '/' . $category_slug . '/' . $feature_slug, 301);
       }

       $matchedLang = Language::where('lang_code', $lang_code)->first();
       $lang_id = $matchedLang ? $matchedLang->id : getCurrentLanguageID();
       $category = Category::whereHas('translations', function ($query) use ($subcategory_slug, $lang_id) {
           $query->where('slug', $subcategory_slug)->where('lang_id', $lang_id);
       })->orWhereHas('translations', function ($query) use ($subcategory_slug) {
           $query->where('slug', $subcategory_slug);
       })->with(['translations' => fn($q) => $q->where('lang_id', $lang_id)])->first();

       $metaTitle = null;
       $metaDescription = null;


       if ($category) {
           $catTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection
               ? $category->translations->first()
               : ($category->translations ?? $category->translation);

           if (!$catTrans) {
               $catTrans = $category->categoryTranslations()->where('lang_id', $lang_id)->first()
                   ?? $category->categoryTranslations()->where('lang_id', 1)->first()
                   ?? $category->categoryTranslations()->first();
           }

           if ($catTrans) {
               $metaTitle = !empty($catTrans->meta_title)
                   ? $catTrans->meta_title
                   : ($catTrans->name ?? 'Category Page');
               $metaDescription = !empty($catTrans->meta_description)
                   ? $catTrans->meta_description
                   : strip_tags($catTrans->description ?? '');

               // Check Feature Meta Tags (Cascade or Custom Override)
               if (!empty($feature_slug)) {
                   $matchedFeat = \App\Models\Feature::findBySlug($feature_slug, $category?->id, $lang_id);

                   if ($matchedFeat) {
                       $resolvedFeatMeta = \App\Services\MetaTagService::resolveFeatureMeta($matchedFeat, $category, $lang_id);
                       $metaTitle = $resolvedFeatMeta['title'];
                       $metaDescription = $resolvedFeatMeta['description'];
                   }
               }

               if (function_exists('format_meta_text')) {
                   $metaTitle = format_meta_text($metaTitle, 'Category Page');
                   $metaDescription = format_meta_text($metaDescription);
               }
           }
       }

       return view('User.category.category_detail', [
           'slug' => $subcategory_slug,
           'feature_slug' => $feature_slug,
           'page' => 1,
           'metaTitle' => $metaTitle,
           'metaDescription' => $metaDescription,
       ]);
     }

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
                    Log::error("Missing language code for lang ID: {$targetLangId}");
                    continue;
                }

                // Translate using helper
                $translatedNameRaw = website_translator($sourceName, $langCode);
                $translatedDescRaw = website_translator($sourceDescription ?? '', $langCode);

                Log::info("Translating category {$categoryId} to {$langCode}", [
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
                $baseSlug = Str::slug($translatedName);
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
            Log::error('Translation Error: ' . $e->getMessage(), [
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
        $matchedLang = Language::where('lang_code', $locale)->first();
        $lang_id = $matchedLang ? $matchedLang->id : getCurrentLanguageID();

        $category = Category::with(['imageMedia', 'topRatedImageMedia', 'faqOverlayMedia'])->whereHas('categoryTranslations', function ($query) use ($slug, $lang_id) {
            $query->where('slug', $slug)->where('lang_id', $lang_id);
        })->first();

        if (!$category) {
            $category = Category::whereHas('categoryTranslations', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })->first();
        }

        if (!$category) {
            abort(404, 'Category not found');
        }

        $categoryTranslation = CategoryTranslation::where('category_id', $category->id)
            ->where('lang_id', $lang_id)
            ->first() ?? CategoryTranslation::where('category_id', $category->id)->first();

        $parentCategory = $category->parent;
        $parentTrans = $parentCategory 
            ? (CategoryTranslation::where('category_id', $parentCategory->id)->where('lang_id', $lang_id)->first() ?? CategoryTranslation::where('category_id', $parentCategory->id)->first()) 
            : null;
        $catName = $categoryTranslation->name ?? $category->name ?? 'Category';

        $faqs = [];
        if (!empty($categoryTranslation->faqs)) {
            $decoded = is_array($categoryTranslation->faqs) ? $categoryTranslation->faqs : json_decode($categoryTranslation->faqs, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $faqs = $decoded;
            }
        }

        // Do not fallback to dummy FAQs; leave empty if not uploaded
        // if (empty($faqs)) { ... }

        $catIds = [$category->id];
        if ($category->parent_id === null) {
            $childIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
            $catIds = array_merge($catIds, $childIds);
        } else {
            $catIds[] = $category->parent_id;
        }

        $topBusinesses = \App\Models\Business::where(function ($q) use ($catIds) {
            $q->whereIn('category_id', $catIds)
              ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                  $subQ->whereIn('categories.id', $catIds);
              });
        })
        ->where('status', 1)
        ->whereHas('translations', function ($q) use ($lang_id) {
            $q->where('lang_id', $lang_id);
        })
        ->withCount(['reviews as active_reviews_count' => fn($q) => $q->whereIn('status', ['published', 'active'])])
        ->withAvg(['reviews as average_rating' => fn($q) => $q->whereIn('status', ['published', 'active'])], 'rating')
        ->with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'category.translations' => fn($q) => $q->where('lang_id', $lang_id),
        ])
        ->orderByDesc('is_affiliate')
        ->orderByDesc('average_rating')
        ->orderByDesc('active_reviews_count')
        ->take(3)
        ->get();

        $recentReviews = \App\Models\Review::whereHas('business', function ($q) use ($catIds) {
            $q->whereIn('category_id', $catIds)
              ->orWhereHas('subCategories', function ($subQ) use ($catIds) {
                  $subQ->whereIn('categories.id', $catIds);
              });
        })
        ->whereIn('status', ['published', 'active'])
        ->with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $lang_id),
            'business.translations' => fn($q) => $q->where('lang_id', $lang_id)
        ])
        ->orderByDesc('created_at')
        ->take(2)
        ->get();

        if ($recentReviews->isEmpty()) {
            $recentReviews = Review::whereIn('status', ['published', 'active'])
                ->with([
                    'user',
                    'translations' => fn($q) => $q->where('language_id', $lang_id),
                    'business.translations' => fn($q) => $q->where('lang_id', $lang_id)
                ])
                ->orderByDesc('created_at')
                ->take(2)
                ->get();
        }

        $languageObj = \App\Models\Language::where('lang_code', $locale)->first();
        $faqSlug = !empty($languageObj->faq_slug) ? $languageObj->faq_slug : 'faqs';
        $discSlug = !empty($languageObj->discussions_slug) ? $languageObj->discussions_slug : 'discussions';

        $recentDiscussions = \App\Models\BusinessDiscussion::where(function ($q) use ($category, $catIds) {
            $q->where('category_id', $category->id)
              ->orWhereIn('category_id', $catIds)
              ->orWhereHas('business', function ($bQ) use ($catIds) {
                  $bQ->whereIn('category_id', $catIds);
              });
        })
        ->where('status', 'active')
        ->with(['user'])
        ->orderByDesc('created_at')
        ->take(2)
        ->get();

        if ($recentDiscussions->isEmpty()) {
            $recentDiscussions = \App\Models\BusinessDiscussion::where('status', 'active')
                ->with(['user'])
                ->orderByDesc('created_at')
                ->take(2)
                ->get();
        }

        $exploreSubcategories = ['title' => '', 'items' => collect()];
        if ($category->parent_id === null) {
            $subcats = Category::where('parent_id', $category->id)
                ->where('status', 1)
                ->whereHas('translations', function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
                })
                ->with([
                    'translations' => function ($q) use ($lang_id) {
                        $q->where('lang_id', $lang_id);
                    },
                    'topRatedImageMedia'
                ])
                ->withCount(['businesses' => function ($q) {
                    $q->where('status', 1);
                }])
                ->get();
            $exploreSubcategories = [
                'title' => 'Explore ' . $catName . ' categories',
                'items' => $subcats
            ];
        } else {
            $parent = $category->parent;
            $parentTransObj = $parent ? (CategoryTranslation::where('category_id', $parent->id)->where('lang_id', $lang_id)->first() ?? CategoryTranslation::where('category_id', $parent->id)->first()) : null;
            $parentName = $parentTransObj->name ?? $parent->name ?? '';
            $subcats = Category::where('parent_id', $category->parent_id)
                ->where('id', '!=', $category->id)
                ->where('status', 1)
                ->whereHas('translations', function ($q) use ($lang_id) {
                    $q->where('lang_id', $lang_id)->whereNotNull('name')->where('name', '!=', '');
                })
                ->with([
                    'translations' => function ($q) use ($lang_id) {
                        $q->where('lang_id', $lang_id);
                    },
                    'topRatedImageMedia'
                ])
                ->withCount(['businesses' => function ($q) {
                    $q->where('status', 1);
                }])
                ->get();
            $exploreSubcategories = [
                'title' => 'Explore other ' . (!empty($parentName) ? $parentName : $catName) . ' categories',
                'items' => $subcats
            ];
        }

        $resolvedFaqMeta = \App\Services\MetaTagService::resolveCategoryFaqMeta($category, $lang_id);
        $faqMetaTitle = $resolvedFaqMeta['title'];
        $faqMetaDesc = $resolvedFaqMeta['description'];

        return view('User.category.category_faqs', compact(
            'category', 'categoryTranslation', 'parentCategory', 'parentTrans', 
            'catName', 'faqs', 'recentReviews', 'topBusinesses', 'lang_id',
            'exploreSubcategories', 'faqSlug', 'discSlug', 'recentDiscussions', 'locale',
            'faqMetaTitle', 'faqMetaDesc'
        ));
    }

}
