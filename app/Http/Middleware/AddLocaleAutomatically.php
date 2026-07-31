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
    
        // 1. If first URL segment is a valid language code, update session and locale to match URL
        $firstSegment = strtolower($request->segment(1));
        if ($firstSegment && in_array($firstSegment, $validLocales)) {
            $langObj = Language::where('lang_code', $firstSegment)->first();
            if ($langObj) {
                App::setLocale($firstSegment);
                session([
                    'lang_code' => $firstSegment,
                    'lang_id'   => $langObj->id,
                    'lang_name' => ucfirst($langObj->name),
                ]);
                Cookie::queue('lang_code', $firstSegment, 60 * 24 * 30);
                Cookie::queue('lang_id', $langObj->id, 60 * 24 * 30);
            }
            return $next($request);
        }

        // 2. If session has a valid language code, use it to prefix request
        $sessionLangCode = session('lang_code');
        if ($sessionLangCode && in_array($sessionLangCode, $validLocales)) {
            App::setLocale($sessionLangCode);
            $pathWithoutLang = ltrim($request->getRequestUri(), '/');
            return redirect()->to("/" . $sessionLangCode . ($pathWithoutLang ? '/' . $pathWithoutLang : ''));
        }

        // 3. Fallback default language (en-us)
        $defaultLanguage = Language::where('lang_code', 'en-us')->first();
        $langCode = $defaultLanguage->lang_code ?? 'en-us';
        $langId = $defaultLanguage->id ?? 1;

        App::setLocale($langCode);
        session([
            'lang_code' => $langCode,
            'lang_id'   => $langId,
            'lang_name' => ucfirst($defaultLanguage->name ?? 'English'),
        ]);

        $pathWithoutLang = ltrim($request->getRequestUri(), '/');
        return redirect()->to("/" . $langCode . ($pathWithoutLang ? '/' . $pathWithoutLang : ''));
    
        return $next($request);
    }
    
    
}
