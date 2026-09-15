<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feature;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeatureController extends Controller
{
    public function index(Request $request)
    {
        $lang_id = getCurrentLanguageID();
        $selectedCategoryId = $request->query('category_id');

        $featuresQuery = Feature::whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->with([
            'category.translations' => function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id);
            },
            'translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            }
        ]);

        if (!empty($selectedCategoryId)) {
            $featuresQuery->where('category_id', $selectedCategoryId);
        }

        $features = $featuresQuery->get();

        // Retrieve top-level categories only
        $categories = Category::where(function ($query) {
            $query->whereNull('parent_id')->orWhere('parent_id', 0);
        })->get()->map(function ($cat) use ($lang_id) {
            $catName = DB::table('category_translations')
                ->where('category_id', $cat->id)
                ->where('lang_id', $lang_id)
                ->value('name');
            if (empty($catName)) {
                $catName = DB::table('category_translations')
                    ->where('category_id', $cat->id)
                    ->value('name') ?? ('Category #' . $cat->id);
            }
            $cat->translated_name = $catName;
            return $cat;
        })->sortBy('translated_name', SORT_NATURAL|SORT_FLAG_CASE)->values();

        return view('Admin.features.index', compact('features', 'categories', 'selectedCategoryId'));
    }

    public function create()
    {
        $lang_id = getCurrentLanguageID();
        // Retrieve top-level categories only
        $categories = Category::where(function ($query) {
            $query->whereNull('parent_id')->orWhere('parent_id', 0);
        })->get()->map(function ($cat) use ($lang_id) {
            $catName = DB::table('category_translations')
                ->where('category_id', $cat->id)
                ->where('lang_id', $lang_id)
                ->value('name');
            if (empty($catName)) {
                $catName = DB::table('category_translations')
                    ->where('category_id', $cat->id)
                    ->value('name') ?? ('Category #' . $cat->id);
            }
            $cat->translated_name = $catName;
            return $cat;
        });

        return view('Admin.features.add', compact('categories'));
    }

    public function store(Request $request)
    {
        $lang_id = getCurrentLanguageID();
        $request->merge([
            'status' => $request->status == 1 ? 'active' : 'inactive',
        ]);

        $name = trim($request->category_cap ?: ($request->name ?: ''));
        $request->merge(['name' => $name]);

        $request->validate([
            'category_ids' => 'required',
            'status'       => 'required|in:active,inactive',
            'name'         => 'required|string|unique:feature_translations,name,NULL,id,lang_id,' . $lang_id,
            'category_cap' => 'nullable|string',
            'category_tc'  => 'nullable|string',
            'category_lc'  => 'nullable|string',
        ]);
        DB::beginTransaction();

        try {
            // Create feature
            $feature = Feature::create([
                'category_id' => $request->category_ids,
                'status'      => $request->status,
            ]);

            $categoryCap = $name;
            $categoryTc  = trim($request->category_tc ?? '') ?: mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
            $categoryLc  = trim($request->category_lc ?? '') ?: mb_strtolower($name);

            // Create translations
            $feature->translations()->create([
                'lang_id'      => $lang_id,
                'name'         => $name,
                'category_cap' => $categoryCap,
                'category_tc'  => $categoryTc,
                'category_lc'  => $categoryLc,
                'feature_cap'  => $categoryCap,
                'feature_tc'   => $categoryTc,
                'feature_lc'   => $categoryLc,
            ]);
            DB::commit();

            return redirect()->route('features')->with('success', 'Feature created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('features.create')->with('error', 'Error creating feature: ' . $e->getMessage());
        }
    }

    public function jsonUpload(Request $request)
    {
        $lang_id = getCurrentLanguageID();

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'json_data'   => 'required|string',
        ]);

        $category = Category::findOrFail($request->category_id);
        if (!empty($category->parent_id) && $category->parent_id != 0) {
            return redirect()->back()->with('error', 'Features can only be assigned to top-level categories, not subcategories.');
        }

        $items = json_decode($request->json_data, true);
        if (!is_array($items)) {
            return redirect()->back()->with('error', 'Invalid JSON format provided.');
        }

        DB::beginTransaction();
        try {
            $importedCount = 0;
            foreach ($items as $item) {
                if (empty($item['name']) && empty($item['feature_name']) && empty($item['category_cap']) && empty($item['feature_cap'])) {
                    continue;
                }
                $name = trim($item['category_cap'] ?? ($item['feature_cap'] ?? ($item['name'] ?? ($item['feature_name'] ?? ''))));
                if (empty($name)) {
                    continue;
                }

                $categoryCap = trim($item['category_cap'] ?? ($item['feature_cap'] ?? $name));
                $categoryTc  = trim($item['category_tc'] ?? ($item['feature_tc'] ?? ''));
                if (empty($categoryTc)) {
                    $categoryTc = mb_convert_case($categoryCap, MB_CASE_TITLE, 'UTF-8');
                }
                $categoryLc  = trim($item['category_lc'] ?? ($item['feature_lc'] ?? ''));
                if (empty($categoryLc)) {
                    $categoryLc = mb_strtolower($categoryCap);
                }

                // Check existing feature translation for language
                $existingTranslation = DB::table('feature_translations')
                    ->where('lang_id', $lang_id)
                    ->where(function ($q) use ($name, $categoryCap) {
                        $q->where('name', $name)
                          ->orWhere('category_cap', $categoryCap)
                          ->orWhere('name', $categoryCap);
                    })
                    ->first();

                if ($existingTranslation) {
                    $feature = Feature::find($existingTranslation->feature_id);
                    if ($feature) {
                        $feature->update(['category_id' => $request->category_id]);
                        DB::table('feature_translations')->where('id', $existingTranslation->id)->update([
                            'name'         => $categoryCap,
                            'category_cap' => $categoryCap,
                            'category_tc'  => $categoryTc,
                            'category_lc'  => $categoryLc,
                            'feature_cap'  => $categoryCap,
                            'feature_tc'   => $categoryTc,
                            'feature_lc'   => $categoryLc,
                        ]);
                    }
                } else {
                    $feature = Feature::create([
                        'category_id' => $request->category_id,
                        'status'      => 'active',
                    ]);

                    $feature->translations()->create([
                        'lang_id'      => $lang_id,
                        'name'         => $categoryCap,
                        'category_cap' => $categoryCap,
                        'category_tc'  => $categoryTc,
                        'category_lc'  => $categoryLc,
                        'feature_cap'  => $categoryCap,
                        'feature_tc'   => $categoryTc,
                        'feature_lc'   => $categoryLc,
                    ]);
                }
                $importedCount++;
            }

            DB::commit();
            return redirect()->route('features')->with('success', "Successfully processed {$importedCount} features via JSON.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error uploading JSON: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $lang_id = getCurrentLanguageID();

        $feature = Feature::with([
            'category',
            'translations' => function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id);
            }
        ])->findOrFail($id);

        // Retrieve top-level categories only
        $categories = Category::where(function ($query) {
            $query->whereNull('parent_id')->orWhere('parent_id', 0);
        })->get()->map(function ($cat) use ($lang_id) {
            $catName = DB::table('category_translations')
                ->where('category_id', $cat->id)
                ->where('lang_id', $lang_id)
                ->value('name');
            if (empty($catName)) {
                $catName = DB::table('category_translations')
                    ->where('category_id', $cat->id)
                    ->value('name') ?? ('Category #' . $cat->id);
            }
            $cat->translated_name = $catName;
            return $cat;
        });

        return view('Admin.features.edit', compact('feature', 'categories', 'lang_id'));
    }

    public function update(Request $request, $id)
    {
        $lang_id = getCurrentLanguageID();

        $request->merge([
            'status' => $request->status == 1 ? 'active' : 'inactive',
        ]);

        $name = trim($request->category_cap ?: ($request->name ?: ''));
        $request->merge(['name' => $name]);

        $request->validate([
            'category_ids' => 'required',
            'status'       => 'required|in:active,inactive',
            'name'         => [
                'required',
                'string',
                Rule::unique('feature_translations', 'name')
                    ->where('lang_id', $lang_id)
                    ->ignore($id, 'feature_id'),
            ],
            'category_cap' => 'nullable|string',
            'category_tc'  => 'nullable|string',
            'category_lc'  => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $feature = Feature::findOrFail($id);

            // Update feature fields
            $feature->update([
                'category_id' => $request->category_ids,
                'status'      => $request->status,
            ]);

            $categoryCap = $name;
            $categoryTc  = trim($request->category_tc ?? '') ?: mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
            $categoryLc  = trim($request->category_lc ?? '') ?: mb_strtolower($name);

            // Update or create translation for current language
            $feature->translations()->updateOrCreate(
                ['lang_id' => $lang_id],
                [
                    'name'         => $name,
                    'category_cap' => $categoryCap,
                    'category_tc'  => $categoryTc,
                    'category_lc'  => $categoryLc,
                    'feature_cap'  => $categoryCap,
                    'feature_tc'   => $categoryTc,
                    'feature_lc'   => $categoryLc,
                ]
            );
            DB::commit();
            return redirect()->route('features')->with('success', 'Feature updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating feature: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $feature = Feature::find($id);
        if (!$feature) {
            return redirect()->route('features')->with('error', 'Feature not found.');
        }
        $feature->translations()->delete();
        $feature->delete();
        return redirect()->route('features')->with('success', 'Feature deleted successfully.');
    }
}
