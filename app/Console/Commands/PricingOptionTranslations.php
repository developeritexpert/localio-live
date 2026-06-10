<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PricingOption;
use App\Models\PricingOptionTranslation;
use Illuminate\Support\Facades\DB;

class PricingOptionTranslations extends Command
{
    protected $signature = 'app:pricing-option-translations';
    protected $description = 'Translate pricing options into other languages';

    public function handle()
    {
        // Get original pricing options in default language (lang_id = 1)
        $pricingOptions = PricingOptionTranslation::where('lang_id', 1)->get();
        // dump($pricingOptions);

        $languages = [
            'fr-fr' => 2, // French
        ];

        $translations = [];

        foreach ($pricingOptions as $option) {
            foreach ($languages as $locale => $langId) {
                // Translate name with fallback
                $translatedName = website_translator($option->name, $locale);
                $name = !empty($translatedName) ? $translatedName : ($option->name ?: '');

                $translations[] = [
                    'pricing_option_id' => $option->pricing_option_id,
                    'lang_id' => $langId,
                    'name' => $name,
                ];

                $this->info("Prepared translation for PricingOption ID {$option->pricing_option_id} - Locale: {$locale}");
            }
        }

        if (!empty($translations)) {
            DB::table('pricing_option_translations')->upsert(
                $translations,
                ['pricing_option_id', 'lang_id'], // Unique keys
                ['name']                          // Fields to update
            );
        }

        $this->info('Pricing option translations imported successfully.');
    }
}
