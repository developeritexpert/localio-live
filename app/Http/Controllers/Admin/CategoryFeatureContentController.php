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

        // Features for filter
        $features = Feature::with(['translations' => fn($q) => $q->where('lang_id', $lang_id)])->get()->map(function($f) {
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

    public function create()
    {
        $lang_id = getCurrentLanguageID();
        $categories = Category::with(['translation' => fn($q) => $q->where('lang_id', $lang_id)])->get()->map(function($cat) {
            $cat->display_name = ($cat->parent_id ? '— ' : '') . ($cat->translation->name ?? $cat->name ?? ('Category #' . $cat->id));
            return $cat;
        });

        $features = Feature::with(['translations' => fn($q) => $q->where('lang_id', $lang_id)])->get()->map(function($f) {
            $f->display_name = $f->translations->first()?->name ?? ('Feature #' . $f->id);
            return $f;
        })->sortBy('display_name')->values();

        $languages = Language::where('status', 1)->get();

        return view('Admin.category-feature-content.create', compact('categories', 'features', 'languages', 'lang_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'feature_id' => 'required|exists:features,id',
            'lang_id' => 'required|exists:languages,id',
            'description' => 'nullable|string',
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
                'description' => $request->description,
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

        $features = Feature::with(['translations' => fn($q) => $q->where('lang_id', $lang_id)])->get()->map(function($f) {
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
            'description' => 'nullable|string',
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
            'description' => $request->description,
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
            'json_file' => 'nullable|file|mimes:json,txt',
            'json_data' => 'nullable|string',
            'default_lang_id' => 'nullable|exists:languages,id'
        ]);

        $rawJson = $request->json_data;
        if ($request->hasFile('json_file')) {
            $rawJson = file_get_contents($request->file('json_file')->getRealPath());
        }

        if (empty($rawJson) || trim($rawJson) === '') {
            return redirect()->back()->with('error', 'Please provide JSON data or upload a JSON file.');
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
                        'description' => $description,
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
            $catTrans = $item->category?->translations->firstWhere('lang_id', $item->lang_id) ?? $item->category?->translations->first();
            $featTrans = $item->feature?->translations->firstWhere('lang_id', $item->lang_id) ?? $item->feature?->translations->first();

            return [
                'category_id' => $item->category_id,
                'category_name' => $catTrans?->name ?? '',
                'category_slug' => $catTrans?->slug ?? '',
                'feature_id' => $item->feature_id,
                'feature_name' => $featTrans?->name ?? '',
                'lang_id' => $item->lang_id,
                'lang_code' => $item->language?->lang_code ?? 'en-us',
                'description' => $item->description,
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
