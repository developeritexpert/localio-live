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

        $name = trim($request->feature_cap ?: ($request->category_cap ?: ($request->name ?: '')));
        $request->merge(['name' => $name]);

        $request->validate([
            'category_ids' => 'required',
            'status'       => 'required|in:active,inactive',
            'name'         => 'required|string|unique:feature_translations,name,NULL,id,lang_id,' . $lang_id,
            'feature_cap'     => 'nullable|string',
            'features_tc'     => 'nullable|string',
            'feature_tc'      => 'nullable|string',
            'feature_lc'      => 'nullable|string',
            'feature_with_lc' => 'nullable|string',
        ]);
        DB::beginTransaction();

        try {
            // Create feature
            $feature = Feature::create([
                'category_id' => $request->category_ids,
                'status'      => $request->status,
            ]);

            $featureCap    = $name;
            $featureTc     = trim($request->features_tc ?? ($request->feature_tc ?? ($request->category_tc ?? ''))) ?: mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
            $featureLc     = trim($request->feature_lc ?? ($request->category_lc ?? '')) ?: mb_strtolower($name);
            $featureWithLc = trim($request->feature_with_lc ?? '') ?: ('with ' . $featureLc);

            // Create translations - populate both feature_* and category_* fields
            $feature->translations()->create([
                'lang_id'         => $lang_id,
                'name'            => $name,
                'feature_cap'     => $featureCap,
                'feature_tc'      => $featureTc,
                'features_tc'     => $featureTc,
                'feature_lc'      => $featureLc,
                'feature_with_lc' => $featureWithLc,
                'category_cap'    => $featureCap,
                'category_tc'     => $featureTc,
                'category_lc'     => $featureLc,
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
            'json_data'   => 'nullable|string',
            'json_file'   => 'nullable|file|max:10240',
        ]);

        $category = Category::findOrFail($request->category_id);
        if (!empty($category->parent_id) && $category->parent_id != 0) {
            return redirect()->back()->with('error', 'Features can only be assigned to top-level categories, not subcategories.');
        }

        $rawJson = null;
        if ($request->hasFile('json_file') && $request->file('json_file')->isValid()) {
            $rawJson = file_get_contents($request->file('json_file')->getRealPath());
        } elseif ($request->filled('json_data')) {
            $rawJson = $request->json_data;
        }

        if (empty($rawJson) || !is_string($rawJson) || trim($rawJson) === '') {
            return redirect()->back()->with('error', 'Please provide JSON data either by pasting it or by uploading a .json file.');
        }

        // Clean UTF-8 BOM if present
        $rawJson = preg_replace('/^\xEF\xBB\xBF/', '', trim($rawJson));

        $items = json_decode($rawJson, true);
        if (!is_array($items)) {
            return redirect()->back()->with('error', 'Invalid JSON format provided. Please ensure the syntax is valid JSON.');
        }

        // Handle possible wrappers, e.g. {"features": [...]} or {"data": [...]}
        if (isset($items['features']) && is_array($items['features'])) {
            $items = $items['features'];
        } elseif (isset($items['data']) && is_array($items['data'])) {
            $items = $items['data'];
        }

        DB::beginTransaction();
        try {
            $importedCount = 0;
            foreach ($items as $rawItem) {
                if (!is_array($rawItem)) {
                    continue;
                }

                // Normalize keys: strip brackets [], quotes, whitespace and lowercase
                $item = [];
                foreach ($rawItem as $k => $v) {
                    $cleanKey = strtolower(trim((string)$k, " \t\n\r\0\x0B[]'\""));
                    $item[$cleanKey] = is_string($v) ? trim($v) : $v;
                }

                // Extract all variations ([feature_cap], [features_tc], [feature_lc])
                $featureCap    = $item['feature_cap'] ?? ($item['category_cap'] ?? ($item['name'] ?? ($item['feature_name'] ?? '')));
                $featureTc     = $item['features_tc'] ?? ($item['feature_tc'] ?? ($item['category_tc'] ?? ''));
                $featureLc     = $item['feature_lc'] ?? ($item['category_lc'] ?? '');
                $featureWithLc = $item['feature_with_lc'] ?? '';
                $groupKey      = !empty($item['group_key']) ? trim((string)$item['group_key']) : (!empty($item['category']) ? trim((string)$item['category']) : null);
                $keyPriority   = isset($item['key_priority']) ? intval($item['key_priority']) : (isset($item['priority']) ? intval($item['priority']) : 0);
                $keyPriority   = max(0, min(10, $keyPriority));

                // Fallbacks if any field is empty
                if (empty($featureCap) && !empty($featureTc)) {
                    $featureCap = $featureTc;
                }
                if (empty($featureCap) && !empty($featureLc)) {
                    $featureCap = ucfirst($featureLc);
                }
                if (empty($featureCap)) {
                    continue;
                }

                if (empty($featureTc)) {
                    $featureTc = mb_convert_case($featureCap, MB_CASE_TITLE, 'UTF-8');
                }
                if (empty($featureLc)) {
                    $featureLc = mb_strtolower($featureCap);
                }
                if (empty($featureWithLc)) {
                    $featureWithLc = 'with ' . $featureLc;
                }

                // Base name: explicit name if given, else featureCap
                $name = !empty($item['name']) ? $item['name'] : $featureCap;
                $description = $item['description'] ?? null;

                // Check existing feature translation for language
                $existingTranslation = DB::table('feature_translations')
                    ->where('lang_id', $lang_id)
                    ->where(function ($q) use ($name, $featureCap, $featureTc) {
                        $q->where('name', $name)
                          ->orWhere('feature_cap', $featureCap)
                          ->orWhere('category_cap', $featureCap)
                          ->orWhere('name', $featureCap)
                          ->orWhere('features_tc', $featureTc)
                          ->orWhere('feature_tc', $featureTc);
                    })
                    ->first();

                if ($existingTranslation) {
                    $feature = Feature::find($existingTranslation->feature_id);
                    if ($feature) {
                        $featUpdate = ['category_id' => $request->category_id];
                        if ($groupKey !== null) {
                            $featUpdate['group_key'] = $groupKey;
                        }
                        if (isset($item['key_priority']) || isset($item['priority'])) {
                            $featUpdate['key_priority'] = $keyPriority;
                        }
                        $feature->update($featUpdate);

                        $updateData = [
                            'name'            => $name,
                            'feature_cap'     => $featureCap,
                            'feature_tc'      => $featureTc,
                            'features_tc'     => $featureTc,
                            'feature_lc'      => $featureLc,
                            'feature_with_lc' => $featureWithLc,
                            'category_cap'    => $featureCap,
                            'category_tc'     => $featureTc,
                            'category_lc'     => $featureLc,
                            'updated_at'      => now(),
                        ];
                        if ($groupKey !== null) {
                            $updateData['group_key'] = $groupKey;
                        }
                        if (isset($item['key_priority']) || isset($item['priority'])) {
                            $updateData['key_priority'] = $keyPriority;
                        }
                        if (!empty($description)) {
                            $updateData['description'] = $description;
                        }
                        DB::table('feature_translations')->where('id', $existingTranslation->id)->update($updateData);
                    }
                } else {
                    $feature = Feature::create([
                        'category_id'  => $request->category_id,
                        'status'       => 'active',
                        'group_key'    => $groupKey,
                        'key_priority' => $keyPriority,
                    ]);

                    $insertData = [
                        'lang_id'         => $lang_id,
                        'name'            => $name,
                        'feature_cap'     => $featureCap,
                        'feature_tc'      => $featureTc,
                        'features_tc'     => $featureTc,
                        'feature_lc'      => $featureLc,
                        'feature_with_lc' => $featureWithLc,
                        'category_cap'    => $featureCap,
                        'category_tc'     => $featureTc,
                        'category_lc'     => $featureLc,
                        'group_key'       => $groupKey,
                        'key_priority'    => $keyPriority,
                    ];
                    if (!empty($description)) {
                        $insertData['description'] = $description;
                    }

                    $feature->translations()->create($insertData);
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

        $name = trim($request->feature_cap ?: ($request->category_cap ?: ($request->name ?: '')));
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
            'feature_cap'     => 'nullable|string',
            'features_tc'     => 'nullable|string',
            'feature_tc'      => 'nullable|string',
            'feature_lc'      => 'nullable|string',
            'feature_with_lc' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $feature = Feature::findOrFail($id);

            // Update feature fields
            $feature->update([
                'category_id' => $request->category_ids,
                'status'      => $request->status,
            ]);

            $featureCap    = $name;
            $featureTc     = trim($request->features_tc ?? ($request->feature_tc ?? ($request->category_tc ?? ''))) ?: mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
            $featureLc     = trim($request->feature_lc ?? ($request->category_lc ?? '')) ?: mb_strtolower($name);
            $featureWithLc = trim($request->feature_with_lc ?? '') ?: ('with ' . $featureLc);

            // Update or create translation for current language
            $feature->translations()->updateOrCreate(
                ['lang_id' => $lang_id],
                [
                    'name'            => $name,
                    'feature_cap'     => $featureCap,
                    'feature_tc'      => $featureTc,
                    'features_tc'     => $featureTc,
                    'feature_lc'      => $featureLc,
                    'feature_with_lc' => $featureWithLc,
                    'category_cap'    => $featureCap,
                    'category_tc'     => $featureTc,
                    'category_lc'     => $featureLc,
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

        DB::beginTransaction();
        try {
            DB::table('business_feature')->where('feature_id', $id)->delete();
            DB::table('product_features')->where('feature_id', $id)->delete();
            DB::table('category_features')->where('feature_id', $id)->delete();
            DB::table('feature_business_reviews')->where('feature_id', $id)->delete();
            DB::table('category_feature_contents')->where('feature_id', $id)->delete();
            $feature->translations()->delete();
            $feature->delete();

            DB::commit();
            return redirect()->route('features')->with('success', 'Feature deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('features')->with('error', 'Error deleting feature: ' . $e->getMessage());
        }
    }

    public function deleteAll(Request $request)
    {
        $categoryId = $request->input('category_id') ?? $request->query('category_id');

        DB::beginTransaction();
        try {
            if (!empty($categoryId)) {
                $featureIds = Feature::where('category_id', $categoryId)->pluck('id')->toArray();
                $count = count($featureIds);

                if ($count === 0) {
                    return redirect()->route('features', ['category_id' => $categoryId])
                        ->with('error', 'No features found to delete in the selected category.');
                }

                // Clean up related records for these features
                DB::table('business_feature')->whereIn('feature_id', $featureIds)->delete();
                DB::table('product_features')->whereIn('feature_id', $featureIds)->delete();
                DB::table('category_features')->whereIn('feature_id', $featureIds)->delete();
                DB::table('feature_business_reviews')->whereIn('feature_id', $featureIds)->delete();
                DB::table('category_feature_contents')->whereIn('feature_id', $featureIds)->delete();
                DB::table('feature_translations')->whereIn('feature_id', $featureIds)->delete();

                Feature::whereIn('id', $featureIds)->delete();

                DB::commit();
                return redirect()->route('features')->with('success', "Successfully deleted all {$count} features for the selected category.");
            } else {
                $count = Feature::count();

                // Global delete: clean up all feature tables including any legacy orphaned records
                DB::table('business_feature')->delete();
                DB::table('product_features')->delete();
                DB::table('category_features')->delete();
                DB::table('feature_business_reviews')->delete();
                DB::table('category_feature_contents')->delete();
                DB::table('feature_translations')->delete();
                Feature::query()->delete();

                DB::commit();
                return redirect()->route('features')->with('success', "Successfully deleted all {$count} features.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('features', !empty($categoryId) ? ['category_id' => $categoryId] : [])
                ->with('error', 'Error deleting features: ' . $e->getMessage());
        }
    }
}
