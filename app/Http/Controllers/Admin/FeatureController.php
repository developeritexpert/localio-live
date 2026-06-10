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
    public function index()
    {
        $lang_id = getCurrentLanguageID();

        $features = Feature::whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->with([
            'category.translations' => function ($q) use ($lang_id) {
                $q->where('lang_id', $lang_id);
            },
            'translations' => function ($query) use ($lang_id) {
                $query->where('lang_id', $lang_id);
            }
        ])->get();

        return view('Admin.features.index', compact('features'));
    }

    public function create()
    {
        $lang_id = getCurrentLanguageID();
        $categories = Category::whereHas('translations', function ($q) use ($lang_id) {
            $q->where('lang_id', $lang_id);
        })->with(['translations' => function ($q) use ($lang_id) {
            $q->where('lang_id', $lang_id);
        }])->get();
        return view('Admin.features.add', compact('categories'));
    }
    public function store(Request $request)
    {
        $lang_id = getCurrentLanguageID();
        $request->merge([
            'status' => $request->status == 1 ? 'active' : 'inactive',
        ]);
        $request->validate([
            'category_ids' => 'required',
            'status' => 'required|in:active,inactive',
            'name' => 'required|string|unique:feature_translations,name,NULL,id,lang_id,' . $lang_id,
        ]);
        DB::beginTransaction();

        try {
            // Create feature
            $feature = Feature::create([
                'category_id' => $request->category_ids,
                'status' => $request->status,
            ]);

            // Create translations
            $feature->translations()->create([
                'lang_id' => $lang_id,
                'name' => $request->name,
            ]);
            DB::commit();

            return redirect()->route('features')->with('success', 'Feature created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('features.create')->with('error', 'Error creating feature: ' . $e->getMessage());
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

        $categories = Category::whereHas('translations', function ($q) use ($lang_id) {
            $q->where('lang_id', $lang_id);
        })->with(['translations' => function ($q) use ($lang_id) {
            $q->where('lang_id', $lang_id);
        }])->get();

        return view('Admin.features.edit', compact('feature', 'categories', 'lang_id'));
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $lang_id = getCurrentLanguageID();

        $request->merge([
            'status' => $request->status == 1 ? 'active' : 'inactive',
        ]);

        $request->validate([
            'category_ids' => 'required',
            'status' => 'required|in:active,inactive',
            'name' => [
                'required',
                'string',
                Rule::unique('feature_translations', 'name')
                    ->where('lang_id', $lang_id)
                    ->ignore($id, 'feature_id'),
            ],

        ]);

        DB::beginTransaction();

        try {
            $feature = Feature::findOrFail($id);

            // Update feature fields
            $feature->update([
                'category_id' => $request->category_ids,
                'status' => $request->status,
            ]);

            // Update or create translation for the current language
            $feature->translations()->updateOrCreate(
                ['lang_id' => $lang_id],
                ['name' => $request->name,
                  'description' => $request->description, //added description
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
