<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeatureTransalte;
use Illuminate\Support\Facades\DB;

class FeatureTranslations extends Command
{
    protected $signature = 'app:feature-translations';
    protected $description = 'Import or update feature translations';

    public function handle()
    {
        $features = FeatureTransalte::where('lang_id', 1)->get();
        // dump($features);

        $languages = [
            //'en-in' => 5,
            //'tr-tr' => 10,
            // 'de-de' => 4,  // example: German
            'fr-fr' => 2,
        ];

        $translations = [];

        foreach ($features as $feature) {
            foreach ($languages as $locale => $langId) {

                // Translate name with fallback
                $name = website_translator($feature->name, $locale);
                if (empty($name)) {
                    $name = $feature->name ?: '';  // fallback placeholder if needed
                }

                // Translate description with fallback
                // $description = website_translator($feature->description, $locale);
                // if (empty($description)) {
                //     $description = $feature->description ?: '';
                // }

                $translations[] = [
                    'feature_id' => $feature->id,
                    'lang_id' => $langId,
                    'name' => $name,
                    'description' => $feature->description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $this->info("Prepared translation for Feature ID {$feature->id} - Locale: {$locale}");
            }
        }

        if (!empty($translations)) {
            // Bulk upsert to avoid multiple queries and ensure update if exists
            DB::table('feature_translations')->upsert(
                $translations,
                ['feature_id', 'lang_id'],       // Unique keys to identify existing rows
                ['name', 'description', 'updated_at']  // Fields to update if row exists
            );
        }

        $this->info('Feature translations imported successfully.');
    }
}
