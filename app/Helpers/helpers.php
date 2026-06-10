<?php

use Illuminate\Support\Facades\Cookie;
use App\Models\{Business, Category, Language, WebSetting,  CategoryTranslation, CountryLanguage, Filter, FilterOption, FilterTranslation, FilterOptionTranslation};
use App\Services\TranslationService;
use App\Models\Country;
use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use App\Models\StaticContentKey;
use App\Services\AssetManager;

function getCurrentLocale($type="lang_code")
{
    if($type == "lang_code"){
        if (Cookie::get('lang_code')) {
            $langcode = Cookie::get('lang_code');
        } elseif (Session::get('lang_code')) {
            $langcode = Session::get('lang_code');
        } else {
            $langcode = "en-us";
        }
        return $langcode;
    }else if($type == "lang_id"){
        if (Cookie::get('lang_id')) {
            $lang_id = Cookie::get('lang_id');
        } elseif (Session::get('lang_id')) {
            $lang_id = Session::get('lang_id');
        } else {
            $lang_id = 1;
        }
        return $lang_id;
    }
}

function getCurrentCountry(){
    $lang_id= getCurrentLanguageID();
    $country_language = CountryLanguage::where('language_id', $lang_id)->first();
    $country_id= Country::where('id',$country_language->country_id)->value('id');
    return $country_id;
}


function getLanguageRole()
{
    $locale = Cookie::get('lang_code', config('app.locale'));
    $siteLanguage = Language::where('lang_code', $locale)->first();
    if ($siteLanguage && $siteLanguage->primary !== 1) {
        return $siteLanguage->lang_code;
    } else {
        return 'global';
    }
}
function getCurrentLanguageID()
{
    if (Cookie::get('lang_code')) {
        $langcode = Cookie::get('lang_code');
    } elseif (Session::get('lang_code')) {
        $langcode = Session::get('lang_code');
    } else {
        $langcode = "en-us";
    }

    $siteLanguage = Language::where('lang_code', $langcode)->first();

    if ($siteLanguage) {
        // Check if base_language_id is set and not null
        if ($siteLanguage->base_language_id) {
            return $siteLanguage->base_language_id;
        }
        return $siteLanguage->id;
    }

    // Default fallback ID
    return 1;
}
function getCurrentSiteLanguage()
{
    $locale = Cookie::get('lang_code', config('app.locale'));
    return Language::where('lang_code', $locale)->first();
}

function formatInr($amount)
{
    // Ensure the amount is a number
    $amount = (float) $amount;

    // Remove the decimals (if any)
    $amount = floor($amount);

    // Convert the amount to a string for manipulation
    $amount = (string) $amount;

    // Get the length of the amount
    $length = strlen($amount);

    // If the length is more than 3, start formatting from the 4th digit
    if ($length > 3) {
        // Split the last 3 digits
        $lastThree = substr($amount, -3);

        // Get the remaining part
        $remaining = substr($amount, 0, $length - 3);

        // Format the remaining part by adding commas every 2 digits
        $remaining = preg_replace('/(?<=\d)(?=(\d{2})+(?!\d))/', ',', $remaining);

        // Combine the two parts
        $formattedAmount = $remaining . ',' . $lastThree;
    } else {
        $formattedAmount = $amount;
    }

    return $formattedAmount;
}


function website_translator($logoName,$lang_code)
{
    if ($logoName == "") {
        return "";   }
    try {
        $translationService = app(TranslationService::class);
        $translatedLogoName = $translationService->translate($logoName, $lang_code);
       // dd($translatedLogoName);
        return $translatedLogoName;
    } catch (\Exception $e) {
       saveLog('Language Translation Eroor', 'helpers', $e->getMessage());
    }
}


// function website_translator($text, $lang_code)
// {
//     if (empty($text)) {
//         return "";
//     }

//     try {
//         $translationService = app(\App\Services\TranslationService::class);
//         $translated = $translationService->translate($text, $lang_code);

//         \Log::info("Text: {$text} | Lang: {$lang_code} | Translated: {$translated}");

//         return $translated;
//     } catch (\Exception $e) {
//         saveLog('Language Translation Error', 'helpers', $e->getMessage());
//         return "";
//     }
// }




function saveLog($fileName = null, $message = null, $name = null, $payload = [], $isShowAdmin = false, $sentMail = false, $status = 'active')
{
    return Log::create([
        'file_name' => $fileName,
        'message' => $message,
        'name' => $name,
        'payload' => json_encode($payload),
        'is_show_admin' => $isShowAdmin,
        'sent_mail' => $sentMail,
        'status' => $status,
    ]);
}


function getLanguages($codes = false)
{
    $cacheKey = 'languages';
    // $languages = json_decode(Redis::get($cacheKey), true);

    if (isset($languages) && $languages) {
        return $languages;
    }

    $languages = Language::all()->mapWithKeys(function ($lang) {
        return [$lang->id => ['lang_code' => $lang->code, 'name' => $lang->name, 'id'=>$lang->id]];
    })->toArray();

    // Redis::set($cacheKey, json_encode($languages));

    return $languages;
}


function getWebSetting($key, $value = false)
{
    $cacheKey = 'web_settings';
    $webSettings = Cache::get($cacheKey);
    if (!$webSettings) {
        $webSettings = WebSetting::all()->keyBy('key');
        Cache::put($cacheKey, $webSettings, now()->addDays(7));
    }

    if ($value) {
        if (isset($webSettings[$key])) {
            return $webSettings[$key]->value;
        } else {
            return null;
        }
    }

    return $webSettings;
}

function changeUserLanguage($lang_id=1){


    if(isset($languages[$lang_id])){

    }
}
function storePrefrences($data)
{
    $sessionTimeInDays = getWebSetting('USER_SESSION_LOGOUT_TIME', true);
    if (!$sessionTimeInDays) {
        $sessionTimeInDays = 30;
    }
    $minutes = $sessionTimeInDays * 24 * 60;
    Cookie::queue('userDetails', json_encode($data), $minutes);
    Session::put('userDetails', $data);
}


function detectLocation($request, $ip = null)         //($ip = null,Request $request)
{
    if (!$ip) {
        $ip = $request->ip();
    }

    $cacheKey = 'userDetails';
    $userDetails = Session::get('userDetails');

    if ($userDetails) {
        return $userDetails;
    }

    $userDetails = [
        'lang_code' => 'en-us',
        'lang_name' => 'United States - English',
        'lang_id' => 33,
    ];

    storePrefrences($userDetails);
    return $userDetails;
}


function getUserPrefrences()
{
    $lang_code = getCurrentLocale();
    $language_id = Language::where('lang_code', $lang_code)->value('id');
    $lang_data = [
        'lang_id' => $language_id,
    ];
    Session::put('lang_data', $lang_data);
    return $lang_data;
}
function getBusinessesWithStartingPrice($businesses)
{

    $unitConversionToMonthly = [
        'day' => 30,
        'week' => 4.3,
        'month' => 1,
        'quarter' => 1 / 3,
        'year' => 1 / 12,
        'one_time' => 0, // or set to a high number to ignore
    ];

    $countryId = getCurrentCountry(); // Assuming this helper returns the current country ID

    if (is_array($businesses)) {
        $businesses = Business::whereIn('id', $businesses)
            ->with(['products' => function ($query) use ($countryId) {
                $query->with('prices')
                    ->where(function ($q) use ($countryId) {
                        $q->where('active_all_countries', 1)
                          ->orWhere(function ($subQuery) use ($countryId) {
                              $subQuery->where('active_all_countries', 0)
                                       ->whereHas('countries', function ($countryQuery) use ($countryId) {
                                           $countryQuery->where('country_id', $countryId);
                                       });
                          });
                    });
            }])
            ->get();
    } elseif ($businesses instanceof \Illuminate\Database\Eloquent\Builder) {
        $businesses = $businesses->with(['products' => function ($query) use ($countryId) {
            $query->with('prices')
                ->where(function ($q) use ($countryId) {
                    $q->where('active_all_countries', 1)
                      ->orWhere(function ($subQuery) use ($countryId) {
                          $subQuery->where('active_all_countries', 0)
                                   ->whereHas('countries', function ($countryQuery) use ($countryId) {
                                       $countryQuery->where('country_id', $countryId);
                                   });
                      });
                });
        }])->get();
    } elseif ($businesses instanceof Business) {
        $businesses = collect([
            $businesses->load(['products' => function ($query) use ($countryId) {
                $query->with('prices')
                    ->where(function ($q) use ($countryId) {
                        $q->where('active_all_countries', 1)
                          ->orWhere(function ($subQuery) use ($countryId) {
                              $subQuery->where('active_all_countries', 0)
                                       ->whereHas('countries', function ($countryQuery) use ($countryId) {
                                           $countryQuery->where('country_id', $countryId);
                                       });
                          });
                    });
            }])
        ]);
    } elseif ($businesses instanceof \Illuminate\Support\Collection) {
        $businesses->loadMissing(['products' => function ($query) use ($countryId) {
            $query->with('prices')
                ->where(function ($q) use ($countryId) {
                    $q->where('active_all_countries', 1)
                      ->orWhere(function ($subQuery) use ($countryId) {
                          $subQuery->where('active_all_countries', 0)
                                   ->whereHas('countries', function ($countryQuery) use ($countryId) {
                                       $countryQuery->where('country_id', $countryId);
                                   });
                      });
                });
        }]);
    } else {
        return [];
    }
    $now = now();
    $result = [];

    foreach ($businesses as $business) {
        $lowest = null;

        foreach ($business->products as $product) {
            foreach ($product->prices as $price) {
                $effectivePrice = null;
                $type = 'base';
                $currency = $price->currency;
                $timeUnit = $price->time_unit;
                $additional_info=$price->additional_info;
                if ($price->discount_price && (!$price->discount_expiration_date || $price->discount_expiration_date >= $now)) {
                    $effectivePrice = $price->discount_price;
                    $timeUnit = $price->discount_time_units ?? $price->time_unit;
                    $type = 'discount';
                } elseif ($price->renewal_price) {
                    $effectivePrice = $price->renewal_price;
                    $timeUnit = $price->renewal_time_units ?? $price->time_unit;
                    $type = 'renewal';
                } else {
                    $effectivePrice = $price->price;
                    $timeUnit = $price->time_unit;
                }

                // Normalize to monthly price
                $monthlyEquivalent = $effectivePrice * $unitConversionToMonthly[$timeUnit];

                if (is_null($lowest) || $monthlyEquivalent < $lowest['normalized_price']) {
                    $lowest = [
                        'amount' => $effectivePrice,
                        'currency' => $currency,
                        'time_unit' => $timeUnit,
                        'price_type' => $type,
                        'product_id' => $product->id,
                        'normalized_price' => $monthlyEquivalent,
                        'additional_info' =>$additional_info
                    ];
                }
            }
        }

        $result[] = [
            'business' => $business,
            'starting_price' => $lowest,
        ];
    }

    return $result;
}
function static_text($key)
{
    $langId = session('lang_id', 1);
    $defaultLangId = 1;

    $cacheKey = "static_texts_all";

    $all = cache()->remember($cacheKey, now()->addHours(6), function () {
        // Eager load translations relationship
        return StaticContentKey::with('translations')->get()->keyBy('key');
    });

    if (!isset($all[$key])) {
        return '';  // key not found at all
    }

    $content = $all[$key];

    // If requested lang is default, return default_value directly
    if ($langId == $defaultLangId) {
        return $content->default_value ?? '';
    }

    // Try to find translation for requested lang
    $translation = $content->translations->firstWhere('lang_id', $langId);

    // Return translation if found, else fallback to default_value
    return $translation->value ?? $content->default_value ?? '';
}


function web_setting($key = null, $value = false, $type = null ,$modelRef = null)
{
    $response = null;

    if ($type) {
        $query = WebSetting::where('type', $type);

        if ($modelRef) {
            $query->where('model_ref', $modelRef); // ✅ Filter by model_ref
        }

        $response = $query->pluck('value', 'key')->toArray();
        return $response;
    }


    if ($key != null && $value) {
        $setting = WebSetting::where('key', $key)->first();
        if ($setting) {
            $response = $setting->value;
        }
    } elseif ($key != null) {
        $response = WebSetting::where('key', $key)->first();
    } else {
        $response = WebSetting::pluck('value', 'key')->toArray();
    }
    return $response;
}

function dimage() {
    $setting = \App\Models\WebSetting::where('key', 'user_default_image')->first();
    return $setting && $setting->value ? asset($setting->value) : asset('default/user.png');
}

function dashboardDefaultImage() {
    $setting = WebSetting::where('key', 'user_dashboard_default_page_image')->first();
    return $setting && $setting->value ? asset($setting->value) : asset('default/dashboard-page.svg');
}

function media_fetch($path, $default = null)
{
    $manager = new AssetManager(); // You can pass basePath/publicUrl if needed
    return $manager->get($path, $default);
}
