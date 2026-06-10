<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate all related tables
        DB::table('country_languages')->truncate();
        DB::table('languages')->truncate();
        DB::table('countries')->truncate();
        DB::table('currencies')->truncate();

        $baseLanguageMap = [
            'en-US' => 'en-US',
            'en-GB' => 'en-GB',
            'en-AU' => 'en-GB',
            'en-IN' => 'en-GB',
            'en-CA' => 'en-US',
            'en-NZ' => 'en-GB',
            'en-ZA' => 'en-GB',
            'en-SG' => 'en-GB',
            'en-PH' => 'en-US',
            'en-MY' => 'en-GB',
            'en-AE' => 'en-GB',
            'fr-FR' => 'fr-FR',
            'fr-CA' => 'fr-FR',
            'fr-CH' => 'fr-FR',
            'de-DE' => 'de-DE',
            'de-AT' => 'de-DE',
            'de-CH' => 'de-DE',
            'pt-PT' => 'pt-PT',
            'pt-BR' => 'pt-PT',
            'es-ES' => 'es-ES',
            'es-MX' => 'es-ES',
            'es-AR' => 'es-ES',
            'es-CL' => 'es-ES',
            'es-CO' => 'es-ES',
            'es-PE' => 'es-ES',
            'es-VE' => 'es-ES',
            'tr-TR' => 'tr-TR',
            'pl-PL' => 'pl-PL',
            'it-IT' => 'it-IT',
        ];

        $countries = [
            ['country_code' => 'US', 'name' => 'United States'],
            ['country_code' => 'FR', 'name' => 'France'],
            ['country_code' => 'ES', 'name' => 'Spain'],
            ['country_code' => 'DE', 'name' => 'Germany'],
            ['country_code' => 'IN', 'name' => 'India'],
            ['country_code' => 'GB', 'name' => 'United Kingdom'],
            ['country_code' => 'PT', 'name' => 'Portugal'],
            ['country_code' => 'BR', 'name' => 'Brazil'],
            ['country_code' => 'AU', 'name' => 'Australia'],
            ['country_code' => 'TR', 'name' => 'Turkey'],
            ['country_code' => 'PL', 'name' => 'Poland'],
            ['country_code' => 'CA', 'name' => 'Canada'],
            ['country_code' => 'MX', 'name' => 'Mexico'],
            ['country_code' => 'AR', 'name' => 'Argentina'],
            ['country_code' => 'CL', 'name' => 'Chile'],
            ['country_code' => 'CO', 'name' => 'Colombia'],
            ['country_code' => 'PE', 'name' => 'Peru'],
            ['country_code' => 'VE', 'name' => 'Venezuela'],
            ['country_code' => 'AT', 'name' => 'Austria'],
            ['country_code' => 'CH', 'name' => 'Switzerland'],
            ['country_code' => 'IE', 'name' => 'Ireland'],
            ['country_code' => 'NZ', 'name' => 'New Zealand'],
            ['country_code' => 'SG', 'name' => 'Singapore'],
            ['country_code' => 'PH', 'name' => 'Philippines'],
            ['country_code' => 'MY', 'name' => 'Malaysia'],
            ['country_code' => 'ZA', 'name' => 'South Africa'],
            ['country_code' => 'AE', 'name' => 'United Arab Emirates'],
            ['country_code' => 'PK', 'name' => 'Pakistan'],
            ['country_code' => 'HK', 'name' => 'Hong Kong'],
            ['country_code' => 'IL', 'name' => 'Israel'],
            ['country_code' => 'IT', 'name' => 'Italy'],
        ];

        // Define base languages with their primary countries
        $baseLanguages = [
            'en-us' => ['name' => 'United States - English', 'primary_country' => 'US'],
            'fr-fr' => ['name' => 'France - Français', 'primary_country' => 'FR'],
            'es-es' => ['name' => 'España - Español', 'primary_country' => 'ES'],
            'de-de' => ['name' => 'Deutschland - Deutsch', 'primary_country' => 'DE'],
            'en-in' => ['name' => 'India - English', 'primary_country' => 'IN'],
            'en-uk' => ['name' => 'United Kingdom - English', 'primary_country' => 'GB'],
            'pt-pt' => ['name' => 'Portuguese - Portugal', 'primary_country' => 'PT'],
            'pt-br' => ['name' => 'Brasil - Português', 'primary_country' => 'BR'],
            'en-au' => ['name' => 'Australia - English', 'primary_country' => 'AU'],
            'tr-tr' => ['name' => 'Türkiye - Türkçe', 'primary_country' => 'TR'],
            'pl-pl' => ['name' => 'Polska - Polski', 'primary_country' => 'PL'],
            'en-ca' => ['name' => 'Canada - English', 'primary_country' => 'CA'],
            'fr-ca' => ['name' => 'Canada - Français', 'primary_country' => 'CA'],
            'es-mx' => ['name' => 'México - Español', 'primary_country' => 'MX'],
            'es-ar' => ['name' => 'Argentina - Español', 'primary_country' => 'AR'],
            'es-cl' => ['name' => 'Chile - Español', 'primary_country' => 'CL'],
            'es-co' => ['name' => 'Colombia - Español', 'primary_country' => 'CO'],
            'es-pe' => ['name' => 'Perú - Español', 'primary_country' => 'PE'],
            'es-ve' => ['name' => 'Venezuela - Español', 'primary_country' => 'VE'],
            'de-at' => ['name' => 'Österreich - Deutsch', 'primary_country' => 'AT'],
            'de-ch' => ['name' => 'Schweiz - Deutsch', 'primary_country' => 'CH'],
            'fr-ch' => ['name' => 'Suisse - Français', 'primary_country' => 'CH'],
            'it-ch' => ['name' => 'Svizzera - Italiano', 'primary_country' => 'CH'],
            'en-ie' => ['name' => 'Ireland - English', 'primary_country' => 'IE'],
            'en-nz' => ['name' => 'New Zealand - English', 'primary_country' => 'NZ'],
            'en-sg' => ['name' => 'Singapore - English', 'primary_country' => 'SG'],
            'en-ph' => ['name' => 'Philippines - English', 'primary_country' => 'PH'],
            'en-my' => ['name' => 'Malaysia - English', 'primary_country' => 'MY'],
            'en-za' => ['name' => 'South Africa - English', 'primary_country' => 'ZA'],
            'en-ae' => ['name' => 'United Arab Emirates - English', 'primary_country' => 'AE'],
            'en-pk' => ['name' => 'Pakistan - English', 'primary_country' => 'PK'],
            'en-hk' => ['name' => 'Hong Kong - English', 'primary_country' => 'HK'],
            'en-il' => ['name' => 'Israel - English', 'primary_country' => 'IL'],
            'es-us' => ['name' => 'Estados Unidos - Español', 'primary_country' => 'US'],
        ];

        // Define country-language mappings using the language codes
        $countryLanguageMappings = [
            // North America
            'US' => ['en-us', 'es-us'],
            'CA' => ['en-ca', 'fr-ca'],
            'MX' => ['es-mx'],

            // South America
            'AR' => ['es-ar'],
            'BR' => ['pt-br'],
            'CL' => ['es-cl'],
            'CO' => ['es-co'],
            'PE' => ['es-pe'],
            'VE' => ['es-ve'],

            // Europe
            'GB' => ['en-uk'],
            'FR' => ['fr-fr'],
            'DE' => ['de-de'],
            'IT' => ['it-ch'],
            'ES' => ['es-es'],
            'PT' => ['pt-pt'],
            'AT' => ['de-at'],
            'CH' => ['de-ch', 'fr-ch', 'it-ch'],
            'PL' => ['pl-pl'],
            'IE' => ['en-ie'],

            // Asia
            'IN' => ['en-in'],
            'SG' => ['en-sg'],
            'PH' => ['en-ph'],
            'MY' => ['en-my'],
            'AE' => ['en-ae'],
            'PK' => ['en-pk'],
            'HK' => ['en-hk'],
            'IL' => ['en-il'],
            'TR' => ['tr-tr'],

            // Oceania
            'AU' => ['en-au'],
            'NZ' => ['en-nz'],

            // Africa
            'ZA' => ['en-za'],
        ];

        // Create a mapping from country codes to IDs for later use
        $countryIds = [];

        // Insert countries and store their IDs
        foreach ($countries as $country) {
            $countryId = DB::table('countries')->insertGetId([
                'country_code' => $country['country_code'],
                'name' => $country['name'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Store country ID for later reference
            $countryIds[$country['country_code']] = $countryId;
        }

        // Insert languages with their primary country IDs
        $languageIds = [];
        foreach ($baseLanguages as $langCode => $langDetails) {
            $primaryCountryCode = $langDetails['primary_country'];
            $countryId = $countryIds[$primaryCountryCode] ?? null;

            $languageId = DB::table('languages')->insertGetId([
                'name' => $langDetails['name'],
                'lang_code' => $langCode,
                'country_id' => $countryId,
                'status' => 1,
                'is_active_translation' => 0,
                'is_valid_language_code' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $languageIds[$langCode] = $languageId;
        }
        foreach ($baseLanguageMap as $langCode => $baseLangCode) {
            if (isset($languageIds[strtolower($langCode)]) && isset($languageIds[strtolower($baseLangCode)])) {
                DB::table('languages')
                    ->where('id', $languageIds[strtolower($langCode)])
                    ->update(['base_language_id' => $languageIds[strtolower($baseLangCode)]]);
            }
        }

        // Create the country_languages relationships
        foreach ($countryLanguageMappings as $countryCode => $languages) {
            if (isset($countryIds[$countryCode])) {
                $countryId = $countryIds[$countryCode];

                foreach ($languages as $langCode) {
                    if (isset($languageIds[$langCode])) {
                        DB::table('country_languages')->insert([
                            'country_id' => $countryId,
                            'language_id' => $languageIds[$langCode],
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
        // Define basic currency info per country
        $currencyMap = [
            'US' => ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'locale' => 'en_US'],
            'FR' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'locale' => 'fr_FR'],
            'ES' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'locale' => 'es_ES'],
            'DE' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'locale' => 'de_DE'],
            'IN' => ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'locale' => 'en_IN'],
            'GB' => ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'locale' => 'en_GB'],
            'PT' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'locale' => 'pt_PT'],
            'BR' => ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'locale' => 'pt_BR'],
            'AU' => ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'locale' => 'en_AU'],
            'TR' => ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺', 'locale' => 'tr_TR'],
            'PL' => ['code' => 'PLN', 'name' => 'Polish Zloty', 'symbol' => 'zł', 'locale' => 'pl_PL'],
            'CA' => ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'locale' => 'en_CA'],
            'MX' => ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$', 'locale' => 'es_MX'],
            'AR' => ['code' => 'ARS', 'name' => 'Argentine Peso', 'symbol' => '$', 'locale' => 'es_AR'],
            'CL' => ['code' => 'CLP', 'name' => 'Chilean Peso', 'symbol' => '$', 'locale' => 'es_CL'],
            'CO' => ['code' => 'COP', 'name' => 'Colombian Peso', 'symbol' => '$', 'locale' => 'es_CO'],
            'PE' => ['code' => 'PEN', 'name' => 'Peruvian Sol', 'symbol' => 'S/', 'locale' => 'es_PE'],
            'VE' => ['code' => 'VES', 'name' => 'Venezuelan Bolívar', 'symbol' => 'Bs.', 'locale' => 'es_VE'],
            'AT' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'locale' => 'de_AT'],
            'CH' => ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'locale' => 'de_CH'],
            'IE' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'locale' => 'en_IE'],
            'NZ' => ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'locale' => 'en_NZ'],
            'SG' => ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'locale' => 'en_SG'],
            'PH' => ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => '₱', 'locale' => 'en_PH'],
            'MY' => ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'locale' => 'en_MY'],
            'ZA' => ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'locale' => 'en_ZA'],
            'AE' => ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'locale' => 'en_AE'],
            'PK' => ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => '₨', 'locale' => 'en_PK'],
            'HK' => ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'locale' => 'en_HK'],
            'IL' => ['code' => 'ILS', 'name' => 'Israeli Shekel', 'symbol' => '₪', 'locale' => 'en_IL'],
            'IT' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'locale' => 'it_IT'],
        ];

        // Track inserted currency codes to avoid duplicates
        $insertedCurrencies = [];

        foreach ($languageIds as $langCode => $languageId) {
            $countryCode = $baseLanguages[$langCode]['primary_country'];
            if (isset($currencyMap[$countryCode])) {
                $currency = $currencyMap[$countryCode];

                // Avoid duplicate insert
                if (!in_array($currency['code'], $insertedCurrencies)) {
                    DB::table('currencies')->insert([
                        'code' => $currency['code'],
                        'name' => $currency['name'],
                        'symbol' => $currency['symbol'],
                      'locale' => strtolower($currency['locale']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $insertedCurrencies[] = $currency['code'];
                }
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
