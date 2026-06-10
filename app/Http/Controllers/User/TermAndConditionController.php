<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\Language;
use App\Models\TermsTranslation;
use App\Models\PolicyTranslation;
use Illuminate\Support\Facades\Session;
use App\Models\Rule;
class TermAndConditionController extends Controller
{
    //
    public function privacyPolicy()
    {
        $lang = Session::get('current_lang');

        $locale = getCurrentLocale(); // Get the current locale (language)
        $lang_code = Language::where('lang_code', $locale)->first();

        if (!$lang_code) {
            return redirect()
                ->back()
                ->withErrors('Language not found.');
        }
        $privacy_policy = PolicyTranslation::where('lang_id', $lang_code->id)->get();
        $terms = TermsTranslation::where('lang_id', $lang_code->id)->get();


        // if ($privacy_policy->isEmpty()) {
        //     // Handle the case where no data is found
        //     dd('No records found for the given language');
        // } else {
        //     dd($privacy_policy);   // This will display the result if data exists
        // }


        // $siteLanguage = SiteLanguages::where('handle', $lang)->first();


        // $policies = Policy::with(['translations' => function ($query) use ($siteLanguage){
        //                     $query->where('language_id',$siteLanguage->id);
        //                     }])->where('title','Privacy Policy')->get();

        // $privacyPolicy = Policy::where('title','Privacy Policy')->first();
        // dd($privacyPolicy);

        // $rules = Rule::with(['translations' => function ($query) use ($siteLanguage){
        //                 $query->where('language_id', $siteLanguage->id);
        //                 }])->where('policy_id' ,$privacyPolicy->id)->get();

        return view('User.terms_condition.privacy_policy',compact('privacy_policy','terms'));
    }
    public function termsCondtion()
    {
        $lang = Session::get('current_lang');
        // dd($lang);
        // $siteLanguage = SiteLanguages::where('handle', $lang)->first();

        // $policies = Policy::with(['translations' => function ($query) use (){
        //                     $query->where('language_id',1);
        //                     }])->where('title','Terms and Conditions')->get();

        // $terms= Policy::where('title','Terms and Conditions')->first();

        // $rules = Rule::with(['translations' => function ($query) use ($siteLanguage){
        //                 $query->where('language_id', $siteLanguage->id);
        //                 }])->where('policy_id' ,$terms->id)->get();
        $locale = getCurrentLocale(); // Get the current locale (language)
        $lang_code = Language::where('lang_code', $locale)->first();

        if (!$lang_code) {
            return redirect()
                ->back()
                ->withErrors('Language not found.');
        }
        $terms = TermsTranslation::where('lang_id', $lang_code->id)->get();
        $privacy_policy = PolicyTranslation::where('lang_id', $lang_code->id)->get();

        return view('User.terms_condition.terms_condition',compact('terms','privacy_policy'));
    }
}
