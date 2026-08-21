<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use App\Models\Language;

class AddLocaleAutomatically
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Fetch all active valid language codes from database
        $validLanguages = Language::where('status', 1)->get();
        $validLocales = $validLanguages->pluck('lang_code')->map(fn($c) => strtolower(trim($c)))->toArray();
        $defaultLocale = in_array('en-us', $validLocales) ? 'en-us' : ($validLocales[0] ?? 'en-us');

        $firstSegment = strtolower(trim((string)$request->segment(1)));
        $isIndexPage = empty($firstSegment) || $request->path() === '/' || $request->getRequestUri() === '/';

        // -------------------------------------------------------------
        // CASE 1: Request already contains a valid locale in the URL
        // Example: /en-us/top-rated-products, /de-de/categories, /es-mx
        // Rule: NEVER redirect pages with a locale in the URL.
        // -------------------------------------------------------------
        if (!empty($firstSegment) && in_array($firstSegment, $validLocales)) {
            $langObj = $validLanguages->first(fn($l) => strtolower(trim($l->lang_code)) === $firstSegment);
            if ($langObj) {
                App::setLocale($firstSegment);
                session([
                    'lang_code' => $firstSegment,
                    'lang_id'   => $langObj->id,
                    'lang_name' => ucfirst($langObj->name),
                ]);
                // Keep the cookie synced with their current active browsing locale
                Cookie::queue('user_locale', $firstSegment, 60 * 24 * 30);
                Cookie::queue('lang_code', $firstSegment, 60 * 24 * 30);
                Cookie::queue('lang_id', $langObj->id, 60 * 24 * 30);
            }
            return $next($request);
        }

        // -------------------------------------------------------------
        // CASE 2: User accessed the root index page (/)
        // Rule: Locale detection & country-check applies ONLY on index page (/)
        // -------------------------------------------------------------
        if ($isIndexPage) {
            // Check for existing saved cookie preference
            $cookieLocale = strtolower(trim((string)($request->cookie('user_locale') ?? $request->cookie('lang_code'))));
            if (!empty($cookieLocale) && in_array($cookieLocale, $validLocales)) {
                $langObj = $validLanguages->first(fn($l) => strtolower(trim($l->lang_code)) === $cookieLocale);
                if ($langObj) {
                    App::setLocale($cookieLocale);
                    session([
                        'lang_code' => $cookieLocale,
                        'lang_id'   => $langObj->id,
                        'lang_name' => ucfirst($langObj->name),
                    ]);
                }
                return redirect()->to('/' . $cookieLocale);
            }

            // No cookie: Detect browser locale from Accept-Language header
            $acceptLanguageHeader = $request->header('Accept-Language');
            $detectedMatch = $this->detectMatchingLocaleWithCountry($acceptLanguageHeader, $validLocales);

            if ($detectedMatch) {
                // Detected a specific Country + Language match (e.g. en-US -> en-us, de-DE -> de-de, es-MX -> es-mx)
                $langObj = $validLanguages->first(fn($l) => strtolower(trim($l->lang_code)) === $detectedMatch);
                if ($langObj) {
                    App::setLocale($detectedMatch);
                    session([
                        'lang_code' => $detectedMatch,
                        'lang_id'   => $langObj->id,
                        'lang_name' => ucfirst($langObj->name),
                    ]);
                }
                Cookie::queue('user_locale', $detectedMatch, 60 * 24 * 30);
                Cookie::queue('lang_code', $detectedMatch, 60 * 24 * 30);
                return redirect()->to('/' . $detectedMatch);
            }

            // Only a generic language tag is available (e.g. 'en', 'es', 'de', 'fr') or no exact country match
            // Show Country Selection Modal so user can choose their exact region
            $targetLocale = $this->findBestLanguageFallback($acceptLanguageHeader, $validLocales, $defaultLocale);
            $langObj = $validLanguages->first(fn($l) => strtolower(trim($l->lang_code)) === $targetLocale);
            if ($langObj) {
                App::setLocale($targetLocale);
                session([
                    'lang_code' => $targetLocale,
                    'lang_id'   => $langObj->id,
                    'lang_name' => ucfirst($langObj->name),
                    'show_country_modal' => true, // Triggers Country Selection Modal on landing
                ]);
            }
            Cookie::queue('user_locale', $targetLocale, 60 * 24 * 30);
            Cookie::queue('lang_code', $targetLocale, 60 * 24 * 30);
            return redirect()->to('/' . $targetLocale);
        }

        // -------------------------------------------------------------
        // CASE 3: Non-index page without locale prefix (e.g. /some-legacy-path)
        // -------------------------------------------------------------
        $targetLocale = session('lang_code') ?? ($request->cookie('user_locale') ?? $defaultLocale);
        if (!in_array($targetLocale, $validLocales)) {
            $targetLocale = $defaultLocale;
        }

        $pathWithoutLang = ltrim($request->getRequestUri(), '/');
        return redirect()->to('/' . $targetLocale . ($pathWithoutLang ? '/' . $pathWithoutLang : ''));
    }

    /**
     * Detect exact Country + Language match from Accept-Language header.
     * Only returns a match if the tag includes a region/country subtag (e.g. en-US, es-MX, de-DE).
     */
    protected function detectMatchingLocaleWithCountry(?string $header, array $validLocales): ?string
    {
        if (empty($header)) {
            return null;
        }

        $tags = $this->parseAcceptLanguage($header);
        foreach ($tags as $item) {
            $tag = $item['tag'];
            // Check if tag has a country component (e.g. en-US, pt-BR, de_DE)
            if (strpos($tag, '-') !== false || strpos($tag, '_') !== false) {
                $normalized = strtolower(str_replace('_', '-', $tag));
                if (in_array($normalized, $validLocales)) {
                    return $normalized;
                }
            }
        }

        return null;
    }

    /**
     * Finds the best language fallback if only language is supplied (e.g. 'de' -> 'de-de', 'es' -> 'es-es', 'en' -> 'en-us').
     */
    protected function findBestLanguageFallback(?string $header, array $validLocales, string $defaultLocale): string
    {
        if (empty($header)) {
            return $defaultLocale;
        }

        $tags = $this->parseAcceptLanguage($header);
        foreach ($tags as $item) {
            $primaryLang = strtolower(explode('-', str_replace('_', '-', $item['tag']))[0]);
            // Find first supported locale starting with this language code
            foreach ($validLocales as $loc) {
                if (str_starts_with($loc, $primaryLang . '-')) {
                    return $loc;
                }
            }
        }

        return $defaultLocale;
    }

    /**
     * Parse Accept-Language header into ordered array by quality score.
     */
    protected function parseAcceptLanguage(string $header): array
    {
        $tags = [];
        foreach (explode(',', $header) as $part) {
            $sub = explode(';', trim($part));
            $tag = trim($sub[0]);
            $q = 1.0;
            if (isset($sub[1]) && preg_match('/q=([0-9.]+)/', $sub[1], $m)) {
                $q = (float) $m[1];
            }
            if (!empty($tag)) {
                $tags[] = ['tag' => $tag, 'q' => $q];
            }
        }
        usort($tags, fn($a, $b) => $b['q'] <=> $a['q']);
        return $tags;
    }
}
