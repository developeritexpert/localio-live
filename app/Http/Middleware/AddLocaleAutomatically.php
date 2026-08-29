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
        // 1. Fetch all active valid language codes from database, with a config fallback
        try {
            $validLanguages = Language::where('status', 1)->get();
            $validLocales = $validLanguages->pluck('lang_code')->map(fn($c) => strtolower(trim($c)))->toArray();
        } catch (\Throwable $e) {
            $validLanguages = collect();
            $validLocales = collect(config('app.available_locales', [config('app.locale', 'en-us')]))
                ->map(fn($locale) => strtolower(trim((string) $locale)))
                ->filter()
                ->values()
                ->toArray();
        }

        if (empty($validLocales)) {
            $validLocales = [strtolower((string) config('app.locale', 'en-us'))];
        }

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
            App::setLocale($firstSegment);
            session(['lang_code' => $firstSegment]);

            if ($langObj) {
                session([
                    'lang_id'   => $langObj->id,
                    'lang_name' => ucfirst($langObj->name),
                ]);
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
                App::setLocale($cookieLocale);
                session(['lang_code' => $cookieLocale]);

                if ($langObj) {
                    session([
                        'lang_id'   => $langObj->id,
                        'lang_name' => ucfirst($langObj->name),
                    ]);
                }
                return redirect()->to('/' . $cookieLocale);
            }

            // No cookie: Detect browser locale from Accept-Language header
            $acceptLanguageHeader = $request->header('Accept-Language');

            if (!empty($acceptLanguageHeader)) {
                $tags = $this->parseAcceptLanguage($acceptLanguageHeader);
                foreach ($tags as $item) {
                    $tag = $item['tag'];
                    $hasCountry = (strpos($tag, '-') !== false || strpos($tag, '_') !== false);
                    $normalized = strtolower(str_replace('_', '-', $tag));

                    if ($hasCountry) {
                        // Exact match (language + country)
                        if (in_array($normalized, $validLocales)) {
                            $langObj = $validLanguages->first(fn($l) => strtolower(trim($l->lang_code)) === $normalized);
                            App::setLocale($normalized);
                            session(['lang_code' => $normalized]);

                            if ($langObj) {
                                session([
                                    'lang_id'   => $langObj->id,
                                    'lang_name' => ucfirst($langObj->name),
                                ]);
                            }
                            Cookie::queue('user_locale', $normalized, 60 * 24 * 30);
                            Cookie::queue('lang_code', $normalized, 60 * 24 * 30);
                            return redirect()->to('/' . $normalized);
                        }
                    } else {
                        // Language-only tag (e.g. "en", "de", "es")
                        // Find first supported locale starting with this language code (e.g. "en-us", "de-de")
                        $primaryLang = $normalized;
                        $fallbackLocale = null;
                        foreach ($validLocales as $loc) {
                            if (str_starts_with($loc, $primaryLang . '-')) {
                                $fallbackLocale = $loc;
                                break;
                            }
                        }

                        if ($fallbackLocale) {
                            $langObj = $validLanguages->first(fn($l) => strtolower(trim($l->lang_code)) === $fallbackLocale);
                            App::setLocale($fallbackLocale);
                            session(['lang_code' => $fallbackLocale]);

                            if ($langObj) {
                                session([
                                    'lang_id'   => $langObj->id,
                                    'lang_name' => ucfirst($langObj->name),
                                ]);
                            }
                            // Flash show_country_modal flag to show the modal on the redirected page
                            session()->flash('show_country_modal', true);
                            return redirect()->to('/' . $fallbackLocale);
                        }
                    }
                }
            }

            // Fallback to default locale if no detection worked
            $langObj = $validLanguages->first(fn($l) => strtolower(trim($l->lang_code)) === $defaultLocale);
            App::setLocale($defaultLocale);
            session(['lang_code' => $defaultLocale]);

            if ($langObj) {
                session([
                    'lang_id'   => $langObj->id,
                    'lang_name' => ucfirst($langObj->name),
                ]);
            }
            return redirect()->to('/' . $defaultLocale);
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
