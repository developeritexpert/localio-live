<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\{Country, Currency, Language, SiteLanguages};
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class SiteLanguagesController extends Controller
{
    public function index()
    {
        $siteLanguages = Language::all();

        return view('Admin.setting.siteLanguages.index', compact('siteLanguages'));
    }
    public function add()
    {
        $countries = Country::all();
        $languagesforBase= Language::where('status',1)->get();
        return view('Admin.setting.siteLanguages.add', compact('countries','languagesforBase'));
    }
    public function addProcc(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:languages,name|string|max:255',
            'lang_code' => 'required|alpha_dash|unique:languages,lang_code|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'base_language_id' => 'nullable|exists:languages,id',
            'status' => 'nullable|in:0,1',
            'is_active_translation' => 'nullable|boolean',
            'is_valid_language_code' => 'nullable|boolean',
        ]);
        $language = new Language();
        $language->name = $request->name;
        $language->lang_code = $request->lang_code;
        $language->country_id = $request->country_id;
        $language->base_language_id = $request->base_language_id;
        $language->status = $request->status ?? 1;
        // $language->is_active_translation = $request->is_active_translation ?? 0;
        $language->save();

        return redirect()->route('site-languages')->with('success', 'Site Language added successfully.');
    }

    public function update($id)
    {
        $siteLanguage = Language::findOrFail($id);
        $countries = Country::all();
        $languagesforBase= Language::where('status',1)->get();
        return view('Admin.setting.siteLanguages.update', compact('siteLanguage', 'countries','languagesforBase'));
    }
    public function updateProcc(Request $request)
    {
        $id = $request->id ?? $request->route('id');
    
        $request->validate([
            'name' => 'required|string|max:255',
            'lang_code' => 'required|string|unique:languages,lang_code,' . $id . ',id',
            'country_id' => 'required|exists:countries,id',
            'base_language_id' => 'nullable|exists:languages,id',
            'status' => 'nullable|in:0,1',
            'is_active_translation' => 'nullable|boolean',
        ]);
    
        // 🔍 Check if lang_code is valid using the helper
        $translated = website_translator('test', $request->lang_code);
        dd($translated);
    
        if (empty($translated)) {
            return back()->withErrors(['lang_code' => 'Invalid language code.'])->withInput();
        }
    
        $language = Language::findOrFail($id);
        $language->name = $request->name;
        $language->lang_code = $request->lang_code;
        $language->country_id = $request->country_id;
        $language->base_language_id = $request->base_language_id;
        $language->status = $request->status ?? $language->status;
        $language->save();
    
        return redirect()->route('site-languages')->with('success', 'Site Language updated successfully.');
    }
    

    public function remove($id)
    {
        $siteLanguage = Language::findOrFail($id);
        
        $siteLanguage->delete();

        return redirect()->route('site-languages')->with('success', 'Site Language deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $siteLanguage = Language::findOrFail($id);

        $siteLanguage->status = $siteLanguage->status == 1 ? 0 : 1;
        $siteLanguage->save();

        $message = $siteLanguage->status ? 'Site Language enabled successfully.' : 'Site Language disabled successfully.';

        return redirect()->route('site-languages')->with('success', $message);
    }



    public function setActiveSiteLanguage($lang_id)
    {
        $language = Language::find($lang_id);
        if (!$language) {
            $language = Language::find(1);
            $lang_code = $language->lang_code;
        }
        $lang_code = $language->lang_code;
        $lang_name = $language->name;
        session(['lang_code' => $lang_code, 'lang_id' => $lang_id, 'lang_name' => $lang_name]);
        Cookie::queue('lang_code', $lang_code, 60 * 24 * 30);
        Cookie::queue('lang_id', $lang_id, 60 * 24 * 30);
        App::setLocale($lang_code);
        return redirect('/' . $lang_code);
    }
    public function setActiveAdminLanguage($lang_id)
    {
        //  dd($lang_id);
        $language = Language::find($lang_id);
        // dd($languages);
        if (!$language) {
            $language = Language::find(1);
            $lang_code = $language->lang_code;
        }
        $lang_code = $language->lang_code;
        $lang_name = $language->name;

        session(['lang_code' => $lang_code, 'lang_id' => $lang_id, 'lang_name' => $lang_name]);
        Cookie::queue('lang_code', $lang_code, 60 * 24 * 30);
        Cookie::queue('lang_id', $lang_id, 60 * 24 * 30);

        return redirect()->back()->with('success', 'Language Changed');
    }


    public function allLanguage(){
        $languages = Language::with(['baseLanguage', 'currency'])->get();
        $currencies=Currency::all();
        return view('Admin.language-region.all_language',compact('languages'));
    }
}
