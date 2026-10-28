<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryFeatureContent;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryFeatureContentController extends Controller
{
    public function index(Request $request)
    {
        $lang_id = $request->query('lang_id', getCurrentLanguageID());
        $selectedCategoryId = $request->query('category_id');
        $selectedFeatureId = $request->query('feature_id');
        $searchTerm = $request->query('search');

        $query = CategoryFeatureContent::with([
            'category.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'feature.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'language'
        ]);

        if (!empty($lang_id)) {
            $query->where('lang_id', $lang_id);
        }

        if (!empty($selectedCategoryId)) {
            $query->where('category_id', $selectedCategoryId);
        }

        if (!empty($selectedFeatureId)) {
            $query->where('feature_id', $selectedFeatureId);
        }

        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('text_sections', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category.translations', fn($cq) => $cq->where('name', 'LIKE', "%{$searchTerm}%"))
                  ->orWhereHas('feature.translations', fn($fq) => $fq->where('name', 'LIKE', "%{$searchTerm}%"));
            });
        }

        $contents = $query->latest('updated_at')->paginate(20)->appends($request->query());

        // Categories for filter
        $categories = Category::with(['translation' => fn($q) => $q->where('lang_id', $lang_id)])->get()->map(function($cat) {
            $cat->display_name = ($cat->parent_id ? '— ' : '') . ($cat->translation->name ?? $cat->name ?? ('Category #' . $cat->id));
            return $cat;
        });

        // Features for filter (optimized for 20k scale: scoped by category or limited)
        $featuresQuery = Feature::with(['translations' => fn($q) => $q->where('lang_id', $lang_id)]);
        if (!empty($selectedCategoryId)) {
            $featuresQuery->where('category_id', $selectedCategoryId);
        } else {
            $featuresQuery->limit(300);
        }
        $features = $featuresQuery->get()->map(function($f) {
            $f->display_name = $f->translations->first()?->name ?? ('Feature #' . $f->id);
            return $f;
        })->sortBy('display_name')->values();

        $languages = Language::where('status', 1)->get();

        return view('Admin.category-feature-content.index', compact(
            'contents',
            'categories',
            'features',
            'languages',
            'lang_id',
            'selectedCategoryId',
            'selectedFeatureId',
            'searchTerm'
        ));
    }

    public function create(Request $request)
    {
        $selectedCategoryId = $request->query('category_id');
        $selectedFeatureId  = $request->query('feature_id');
        $lang_id            = $request->query('lang_id') ?: getCurrentLanguageID();

        // If a record already exists for this exact combination, redirect straight to edit
        if ($selectedCategoryId && $selectedFeatureId) {
            $existing = CategoryFeatureContent::where('category_id', $selectedCategoryId)
                ->where('feature_id', $selectedFeatureId)
                ->where('lang_id', $lang_id)
                ->first();
            if ($existing) {
                return redirect()->route('admin.category-feature-content.edit', $existing->id)
                    ->with('info', 'Content already exists for this Category and Feature. Opened in edit mode.');
            }
        }

        $categories = Category::with(['translation' => fn($q) => $q->where('lang_id', $lang_id)])->get()->map(function($cat) {
            $cat->display_name = ($cat->parent_id ? '— ' : '') . ($cat->translation->name ?? $cat->name ?? ('Category #' . $cat->id));
            return $cat;
        });

        $featuresQuery = Feature::with(['translations' => fn($q) => $q->where('lang_id', $lang_id)]);
        if (!empty($selectedCategoryId)) {
            $featuresQuery->where('category_id', $selectedCategoryId);
        } else {
            $featuresQuery->limit(300);
        }
        $features = $featuresQuery->get()->map(function($f) {
            $f->display_name = $f->translations->first()?->name ?? ('Feature #' . $f->id);
            return $f;
        })->sortBy('display_name')->values();

        $languages = Language::where('status', 1)->get();

        return view('Admin.category-feature-content.create', compact('categories', 'features', 'languages', 'lang_id', 'selectedCategoryId', 'selectedFeatureId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'feature_id' => 'required|exists:features,id',
            'lang_id' => 'required|exists:languages,id',
            'page_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'top_rated_listing_headline' => 'nullable|string|max:255',
            'top_rated_listing_subtext' => 'nullable|string',
            'compare_alternatives_headline' => 'nullable|string|max:255',
            'compare_alternatives_subtext' => 'nullable|string',
            'community_says_headline' => 'nullable|string|max:255',
            'community_says_subtext' => 'nullable|string',
            'explore_subcategories_headline' => 'nullable|string|max:255',
            'explore_subcategories_subtext' => 'nullable|string',
            'popular_comparisons_headline' => 'nullable|string|max:255',
            'popular_comparisons_subtext' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Process Dynamic Text Sections
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

        CategoryFeatureContent::updateOrCreate(
            [
                'category_id' => $request->category_id,
                'feature_id' => $request->feature_id,
                'lang_id' => $request->lang_id,
            ],
            [
                'page_title' => $request->page_title,
                'description' => $request->description,
                'top_rated_listing_headline' => $request->top_rated_listing_headline,
                'top_rated_listing_subtext' => $request->top_rated_listing_subtext,
                'compare_alternatives_headline' => $request->compare_alternatives_headline,
                'compare_alternatives_subtext' => $request->compare_alternatives_subtext,
                'community_says_headline' => $request->community_says_headline,
                'community_says_subtext' => $request->community_says_subtext,
                'explore_subcategories_headline' => $request->explore_subcategories_headline,
                'explore_subcategories_subtext' => $request->explore_subcategories_subtext,
                'popular_comparisons_headline' => $request->popular_comparisons_headline,
                'popular_comparisons_subtext' => $request->popular_comparisons_subtext,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'text_sections' => !empty($cleanTextSections) ? json_encode($cleanTextSections) : null,
            ]
        );

        return redirect()->route('admin.category-feature-content.index')
            ->with('success', 'Category-Feature content saved successfully.');
    }

    public function edit($id)
    {
        $content = CategoryFeatureContent::findOrFail($id);
        $lang_id = $content->lang_id;

        $categories = Category::with(['translation' => fn($q) => $q->where('lang_id', $lang_id)])->get()->map(function($cat) {
            $cat->display_name = ($cat->parent_id ? '— ' : '') . ($cat->translation->name ?? $cat->name ?? ('Category #' . $cat->id));
            return $cat;
        });

        $featuresQuery = Feature::with(['translations' => fn($q) => $q->where('lang_id', $lang_id)]);
        if (!empty($content->category_id)) {
            $featuresQuery->where('category_id', $content->category_id);
        } else {
            $featuresQuery->limit(300);
        }
        $features = $featuresQuery->get()->map(function($f) {
            $f->display_name = $f->translations->first()?->name ?? ('Feature #' . $f->id);
            return $f;
        })->sortBy('display_name')->values();

        $languages = Language::where('status', 1)->get();

        $text_sections = [];
        if (!empty($content->text_sections)) {
            $text_sections = is_array($content->text_sections) ? $content->text_sections : json_decode($content->text_sections, true);
        }

        return view('Admin.category-feature-content.edit', compact('content', 'categories', 'features', 'languages', 'text_sections'));
    }

    public function update(Request $request, $id)
    {
        $content = CategoryFeatureContent::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'feature_id' => 'required|exists:features,id',
            'lang_id' => 'required|exists:languages,id',
            'page_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'top_rated_listing_headline' => 'nullable|string|max:255',
            'top_rated_listing_subtext' => 'nullable|string',
            'compare_alternatives_headline' => 'nullable|string|max:255',
            'compare_alternatives_subtext' => 'nullable|string',
            'community_says_headline' => 'nullable|string|max:255',
            'community_says_subtext' => 'nullable|string',
            'explore_subcategories_headline' => 'nullable|string|max:255',
            'explore_subcategories_subtext' => 'nullable|string',
            'popular_comparisons_headline' => 'nullable|string|max:255',
            'popular_comparisons_subtext' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Process Dynamic Text Sections
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

        $content->update([
            'category_id' => $request->category_id,
            'feature_id' => $request->feature_id,
            'lang_id' => $request->lang_id,
            'page_title' => $request->page_title,
            'description' => $request->description,
            'top_rated_listing_headline' => $request->top_rated_listing_headline,
            'top_rated_listing_subtext' => $request->top_rated_listing_subtext,
            'compare_alternatives_headline' => $request->compare_alternatives_headline,
            'compare_alternatives_subtext' => $request->compare_alternatives_subtext,
            'community_says_headline' => $request->community_says_headline,
            'community_says_subtext' => $request->community_says_subtext,
            'explore_subcategories_headline' => $request->explore_subcategories_headline,
            'explore_subcategories_subtext' => $request->explore_subcategories_subtext,
            'popular_comparisons_headline' => $request->popular_comparisons_headline,
            'popular_comparisons_subtext' => $request->popular_comparisons_subtext,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'text_sections' => !empty($cleanTextSections) ? json_encode($cleanTextSections) : null,
        ]);

        return redirect()->route('admin.category-feature-content.index')
            ->with('success', 'Category-Feature content updated successfully.');
    }

    public function destroy($id)
    {
        $content = CategoryFeatureContent::findOrFail($id);
        $content->delete();

        return redirect()->back()->with('success', 'Category-Feature content deleted successfully.');
    }

    public function jsonImportView()
    {
        $languages = Language::where('status', 1)->get();
        return view('Admin.category-feature-content.json_import', compact('languages'));
    }

    public function jsonImportProcess(Request $request)
    {
        $request->validate([
            'json_data' => 'required|string',
            'default_lang_id' => 'nullable|exists:languages,id'
        ]);

        $rawJson = $request->json_data;

        if (empty($rawJson) || trim($rawJson) === '') {
            return redirect()->back()->with('error', 'Please paste JSON content in the text area.');
        }

        $items = json_decode($rawJson, true);
        if (!is_array($items)) {
            return redirect()->back()->with('error', 'Invalid JSON syntax. Please check the JSON format.');
        }

        // Wrap single object in array if user passed single object
        if (isset($items['category_id']) || isset($items['category_name']) || isset($items['category_slug'])) {
            $items = [$items];
        }

        $defaultLangId = $request->default_lang_id ?? getCurrentLanguageID();
        $importedCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($items as $index => $item) {
                // 1. Resolve Category
                $categoryId = $item['category_id'] ?? null;
                if (!$categoryId) {
                    $catSearch = $item['category_slug'] ?? $item['category_name'] ?? null;
                    if ($catSearch) {
                        $matchedCatTrans = \App\Models\CategoryTranslation::where('slug', $catSearch)
                            ->orWhere('name', 'LIKE', $catSearch)
                            ->first();
                        $categoryId = $matchedCatTrans?->category_id;
                    }
                }

                if (!$categoryId) {
                    $skippedCount++;
                    $errors[] = "Row #" . ($index + 1) . ": Category not found (" . ($item['category_slug'] ?? $item['category_name'] ?? 'empty') . ")";
                    continue;
                }

                // 2. Resolve Feature
                $featureId = $item['feature_id'] ?? null;
                if (!$featureId) {
                    $featSearch = $item['feature_name'] ?? $item['feature_slug'] ?? null;
                    if ($featSearch) {
                        $matchedFeatTrans = DB::table('feature_translations')
                            ->where('name', 'LIKE', str_replace('-', ' ', $featSearch))
                            ->orWhere('name', 'LIKE', $featSearch)
                            ->first();
                        $featureId = $matchedFeatTrans?->feature_id;
                    }
                }

                if (!$featureId) {
                    $skippedCount++;
                    $errors[] = "Row #" . ($index + 1) . ": Feature not found (" . ($item['feature_name'] ?? $item['feature_slug'] ?? 'empty') . ")";
                    continue;
                }

                // 3. Resolve Language
                $langId = $item['lang_id'] ?? null;
                if (!$langId && !empty($item['lang_code'])) {
                    $langCode = strtolower(trim($item['lang_code']));
                    $langObj = Language::where('lang_code', 'LIKE', "{$langCode}%")->first();
                    $langId = $langObj?->id;
                }
                if (!$langId) {
                    $langId = $defaultLangId;
                }

                // 4. Resolve Description & Text Sections
                $description = $item['description'] ?? null;
                $textSections = $item['text_sections'] ?? null;
                if (is_string($textSections)) {
                    $decodedSections = json_decode($textSections, true);
                    if (is_array($decodedSections)) {
                        $textSections = $decodedSections;
                    }
                }

                CategoryFeatureContent::updateOrCreate(
                    [
                        'category_id' => $categoryId,
                        'feature_id' => $featureId,
                        'lang_id' => $langId,
                    ],
                    [
                        'page_title' => $item['page_title'] ?? null,
                        'description' => $description,
                        'top_rated_listing_headline' => $item['top_rated_listing_headline'] ?? null,
                        'top_rated_listing_subtext' => $item['top_rated_listing_subtext'] ?? null,
                        'compare_alternatives_headline' => $item['compare_alternatives_headline'] ?? null,
                        'compare_alternatives_subtext' => $item['compare_alternatives_subtext'] ?? null,
                        'community_says_headline' => $item['community_says_headline'] ?? null,
                        'community_says_subtext' => $item['community_says_subtext'] ?? null,
                        'explore_subcategories_headline' => $item['explore_subcategories_headline'] ?? null,
                        'explore_subcategories_subtext' => $item['explore_subcategories_subtext'] ?? null,
                        'popular_comparisons_headline' => $item['popular_comparisons_headline'] ?? null,
                        'popular_comparisons_subtext' => $item['popular_comparisons_subtext'] ?? null,
                        'meta_title' => $item['meta_title'] ?? null,
                        'meta_description' => $item['meta_description'] ?? null,
                        'text_sections' => !empty($textSections) ? json_encode($textSections) : null,
                    ]
                );

                $importedCount++;
            }

            DB::commit();

            $msg = "Successfully processed {$importedCount} category-feature content records.";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} skipped due to missing category/feature mappings)";
            }

            return redirect()->route('admin.category-feature-content.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error during JSON import: ' . $e->getMessage());
        }
    }

    public function exportJson(Request $request)
    {
        $query = CategoryFeatureContent::with([
            'category.translations',
            'feature.translations',
            'language'
        ]);

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('lang_id') && !empty($request->lang_id)) {
            $query->where('lang_id', $request->lang_id);
        }

        $records = $query->get()->map(function($item) {
            $catTrans = $item->category?->translation ?? $item->category?->translations;
            $featTrans = $item->feature?->translations->firstWhere('lang_id', $item->lang_id) ?? $item->feature?->translations->first();

            return [
                'category_id' => $item->category_id,
                'category_name' => $catTrans?->name ?? '',
                'category_slug' => $catTrans?->slug ?? '',
                'feature_id' => $item->feature_id,
                'feature_name' => $featTrans?->name ?? '',
                'lang_id' => $item->lang_id,
                'lang_code' => $item->language?->lang_code ?? 'en-us',
                'page_title' => $item->page_title,
                'description' => $item->description,
                'top_rated_listing_headline' => $item->top_rated_listing_headline,
                'top_rated_listing_subtext' => $item->top_rated_listing_subtext,
                'compare_alternatives_headline' => $item->compare_alternatives_headline,
                'compare_alternatives_subtext' => $item->compare_alternatives_subtext,
                'community_says_headline' => $item->community_says_headline,
                'community_says_subtext' => $item->community_says_subtext,
                'explore_subcategories_headline' => $item->explore_subcategories_headline,
                'explore_subcategories_subtext' => $item->explore_subcategories_subtext,
                'popular_comparisons_headline' => $item->popular_comparisons_headline,
                'popular_comparisons_subtext' => $item->popular_comparisons_subtext,
                'meta_title' => $item->meta_title,
                'meta_description' => $item->meta_description,
                'text_sections' => !empty($item->text_sections) ? (is_array($item->text_sections) ? $item->text_sections : json_decode($item->text_sections, true)) : [],
            ];
        });

        $fileName = 'category_feature_contents_' . date('Y-m-d_H-i-s') . '.json';
        return response()->json($records, 200, [
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Content-Type' => 'application/json'
        ]);
    }
}
