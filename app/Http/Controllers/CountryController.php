<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountryController extends Controller
{
    public function index(Request $request){
        $countries = Country::where('status',1)->get();
        if($request->ajax()){
            return response()->json($countries);
        }
        return view('Admin.setting.country.index',compact('countries'));
    }

    public function update($id)
    {
        $countryid = Country::findOrFail($id);
        return view('Admin.setting.country.update',compact('countryid'));
    }

    public function updateProcc(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'name' => 'required|unique:countries,name,' . $id,
        ]);
        try {
            $country = Country::findOrFail($id);
            $oldName = $country->name;
            $country->name = $request->name;
            $country->show_disclaimer = $request->has('show_disclaimer') ? 1 : 0;
            $country->save();

            // Sync with corresponding Language if exists
            $language = Language::where('country_id', $country->id)->first() ?? Language::where('name', $oldName)->first();
            if ($language) {
                $language->name = $request->name;
                $language->save();
            }

            return redirect()->route('country.index')->with('success', 'Country/region updated successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Country/region not found.');
        }
    }

    public function add(){
        return view('Admin.setting.country.add');
    }

    public function addProcc(Request $request){
        $request->validate([
            'name' => 'required|unique:countries,name',
        ]);

        $country = new Country;
        $country->name = $request->name ?? '';
        $country->show_disclaimer = $request->has('show_disclaimer') ? 1 : 0;
        $country->status = 1;
        $country->save();

        // Also create/sync corresponding Language entry so it appears in footer and throughout site
        $slug = Str::slug($request->name);
        $parts = explode('-', $slug);
        $langCode = count($parts) >= 2 ? strtolower(substr(end($parts), 0, 2) . '-' . substr($parts[0], 0, 2)) : 'c-' . $country->id;
        if (Language::where('lang_code', $langCode)->exists()) {
            $langCode = 'c-' . $country->id;
        }

        Language::create([
            'name' => $request->name,
            'lang_code' => $langCode,
            'country_id' => $country->id,
            'status' => 1,
            'faq_slug' => 'faqs',
            'alternatives_slug' => 'alternatives',
            'reviews_slug' => 'reviews',
            'comparisons_slug' => 'comparisons',
        ]);

        return redirect()->route('country.index')->with('success', 'Country/region added successfully.');
    }

    public function delete($id){
        $country = Country::find($id);
        if ($country) {
            Language::where('country_id', $id)->delete();
            $country->delete();
        }
        return redirect()->back()->with('success', 'Country/region deleted successfully.');
    }
}
