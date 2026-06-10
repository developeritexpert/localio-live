<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Session;
use Illuminate\Support\Facades\Route;
use App\Models\Language;
class AddLocaleAutomatically
{
    // public function handle(Request $request, Closure $next)
    // {
    //     $validLocales = ['en-us'];
    //     $firstSegment = $request->segment(1);
    //     if (!in_array($firstSegment, $validLocales)) {
    //         $defaultLocale = 'en-us';
    //         return redirect()->to("/$defaultLocale" . $request->getRequestUri());
    //     }
    //     App::setLocale($firstSegment);
    //     return $next($request);

    // }
    public function handle(Request $request, Closure $next)
    {
        // Fetch valid language codes from the database
        $validLocales = Language::pluck('lang_code')->toArray();
    
        // Get the language from the session (if set)
        $sessionLangCode = session('lang_code');
    
        // If session has a valid language code, use it
        if ($sessionLangCode && in_array($sessionLangCode, $validLocales)) {
            App::setLocale($sessionLangCode);
    
            // Check if the URL already starts with the lang code
            $firstSegment = $request->segment(1);
            if ($firstSegment !== $sessionLangCode) {
                // Redirect with language prefix
                $pathWithoutLang = ltrim($request->getRequestUri(), '/');
                return redirect()->to("/" . $sessionLangCode . '/' . $pathWithoutLang);
            }
    
            return $next($request);
        }
    
        // Otherwise, set and store the default language
        $defaultLanguage = Language::where('lang_code', 'en-us')->first();
        $langCode = $defaultLanguage->lang_code ?? 'en-us';
       
        App::setLocale($langCode);
        session([
            'lang_code' => $langCode,
            'lang_name' => ucfirst($defaultLanguage->name ?? 'English'),
        ]);
        
        // Check if the URL already starts with lang code before redirecting
        $firstSegment = $request->segment(1);
        if ($firstSegment !== $langCode) {
            $pathWithoutLang = ltrim($request->getRequestUri(), '/');
            return redirect()->to("/" . $langCode . '/' . $pathWithoutLang);
        }
    
        return $next($request);
    }
    
    
}
