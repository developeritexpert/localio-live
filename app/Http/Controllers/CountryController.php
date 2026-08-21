<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::where('status', 1)->with('language')->get();
        if ($request->ajax()) {
            return response()->json($countries);
        }
        return view('Admin.setting.country.index', compact('countries'));
    }

    public function update($id)
    {
        $countryid = Country::with('language')->findOrFail($id);
        return view('Admin.setting.country.update', compact('countryid'));
    }

    public function updateProcc(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'name'      => 'required|unique:countries,name,' . $id,
            'lang_code' => 'required|string|max:20|regex:/^[a-z0-9-]+$/|unique:languages,lang_code,' . (Language::where('country_id', $id)->value('id') ?? 0),
        ]);

        try {
            $country  = Country::findOrFail($id);
            $oldName  = $country->name;
            $country->name             = $request->name;
            $country->show_disclaimer  = $request->has('show_disclaimer') ? 1 : 0;
            $country->save();

            // Sync lang_code on the linked Language record
            $language = Language::where('country_id', $country->id)->first()
                     ?? Language::where('name', $oldName)->first();

            if ($language) {
                $language->name      = $request->name;
                $language->lang_code = strtolower($request->lang_code);
                $language->save();
            } else {
                // Create language record if missing
                Language::create([
                    'name'               => $request->name,
                    'lang_code'          => strtolower($request->lang_code),
                    'country_id'         => $country->id,
                    'status'             => 1,
                    'faq_slug'           => 'faqs',
                    'alternatives_slug'  => 'alternatives',
                    'reviews_slug'       => 'reviews',
                    'comparisons_slug'   => 'comparisons',
                ]);
            }

            return redirect()->route('country.index')->with('success', 'Country/region updated successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Country/region not found.');
        }
    }

    public function add()
    {
        return view('Admin.setting.country.add');
    }

    public function addProcc(Request $request)
    {
        $request->validate([
            'name'      => 'required|unique:countries,name',
            'lang_code' => 'required|string|max:20|regex:/^[a-z0-9-]+$/|unique:languages,lang_code',
        ]);

        $country = new Country;
        $country->name             = $request->name;
        $country->show_disclaimer  = $request->has('show_disclaimer') ? 1 : 0;
        $country->status           = 1;
        $country->save();

        Language::create([
            'name'               => $request->name,
            'lang_code'          => strtolower($request->lang_code),
            'country_id'         => $country->id,
            'status'             => 1,
            'faq_slug'           => 'faqs',
            'alternatives_slug'  => 'alternatives',
            'reviews_slug'       => 'reviews',
            'comparisons_slug'   => 'comparisons',
        ]);

        return redirect()->route('country.index')->with('success', 'Country/region added successfully.');
    }

    public function delete($id)
    {
        $country = Country::find($id);
        if ($country) {
            Language::where('country_id', $id)->delete();
            $country->delete();
        }
        return redirect()->back()->with('success', 'Country/region deleted successfully.');
    }
}
