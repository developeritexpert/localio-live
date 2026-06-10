<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StaticContentKey;
use App\Models\StaticContentTranslation;
use Illuminate\Support\Facades\DB;

class StaticContentTranslations extends Command
{
    protected $signature = 'app:static-content-translations';
    protected $description = 'Translate static content to other languages';

    public function handle()
    {
        //Get all static content keys
        $staticContentKeys = StaticContentKey::all();

        if ($staticContentKeys->isEmpty()) {
            $this->warn("No static content keys found.");
            return;
        }

        //Define target languages (locale => lang_id)
        $languages = [
            'fr-fr' => 2,
            // Add more like: 'es-es' => 3, 'de-de' => 4
        ];

        //Prepare translations array
        $translations = [];

        foreach ($staticContentKeys as $contentKey) {
            foreach ($languages as $locale => $langId) {
                // Check if this key is already translated to the given language
                $exists = StaticContentTranslation::where('static_content_key_id', $contentKey->id)
                    ->where('lang_id', $langId)
                    ->exists();

                if ($exists) {
                    $this->line("Key '{$contentKey->key}' already translated to {$locale}. Skipping.");
                    continue;
                }

                // Translate the value
                $translatedValue = website_translator($contentKey->default_value, $locale);

                // Add to array
                $translations[] = [
                    'static_content_key_id' => $contentKey->id,
                    'lang_id'               => $langId,
                    'value'                 => $translatedValue ?: $contentKey->default_value,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ];

                $this->info("Prepared translation for key '{$contentKey->key}' - {$locale}");
            }
        }

        // dump($translations);

        //Insert translations to DB
        if (!empty($translations)) {
            DB::table('static_content_translations')->upsert(
                $translations,
                ['static_content_key_id', 'lang_id'], // unique key
                ['value', 'updated_at'] // update on duplicate
            );

            $this->info("Static content translations inserted/updated successfully.");
        } else {
            $this->warn("No new static content translations were needed.");
        }
    }
}
