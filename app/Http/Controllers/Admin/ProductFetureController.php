<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\FeatureTransalte;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductFeature;
use App\Models\ProductFeatureTranslate;
use App\Models\ProductTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductFetureController extends Controller
{
    public function index()
    {
        $custmor = request('tab');
        $lang_id = getCurrentLanguageID();
        $siteLanguage = Language::where('id', '1')->first();
        $features = Feature::with('feature_translation')
        ->where('type', $custmor)
        ->get()
        ->map(function ($feature) {
            return [
                'feature_name' => optional($feature->feature_translation)->name, // Avoid null errors
                'feature_id' => $feature->id,
                'status' => $feature->status
            ];
        })->toArray();
        // dd($features);
        return view('Admin.product-feature.index', compact('features'));
    }

    public function add()
    {
        return view('Admin.product-feature.add');
    }
    public function add_process(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:feature_translation',
        ]);

        $featureid = Feature::create([
            'lang_id' => 1,
            'type' => $request->tab,
            'status' => 'active'
        ])->id;

        $product_translate_feture =  DB::table('feature_translation')->insert([
            'name' => $request->name,
            'feature_id' => $featureid,
            'status' => 'active'
        ]);

        if ($product_translate_feture) {
            return redirect()->back()->with('success', 'successfull created product Feature');
        } else {
            return redirect()->back()->with('error', 'Some thing Went Wrong !');
        }
    }

    public function update($id)
    {
        $productFeature = Feature::with('feature_translation')->where('id', $id)->first();
        $productFeatureTranslate = optional($productFeature->feature_translation)->toArray() ?? [];
         return view('Admin.product-feature.update', compact('productFeatureTranslate'));
    }

    public function update_process(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        $name = FeatureTransalte::where('id', $request->feture_translate_id)->update([
            'name' => $request->name
        ]);
        if ($name) {
            return redirect()->route('productfeature.index', ['tab' => request('tab')])->with('success', 'Successfull updated');
        } else {
            return redirect()->back()->with('error', 'Some thing went wrong');
        }
    }

    public function remove($id)
    {
        $productFeature = Feature::with('feature_translation')->where('id', $id)->first();
        if ($productFeature) {
            FeatureTransalte::where('feature_id', $id)->delete();
            ProductFeature::where('feature_id', $id)->delete();
            $productFeature->delete();
            return redirect()->route('productfeature.index', ['tab' => request('tab')])->with('success', 'Successfull updated');
        }
        return redirect()->route('productfeature.index', ['tab' => request('tab')])->with('error', 'No remove data');
    }
}
