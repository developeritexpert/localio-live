<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Schema;
use App\Models\Language;

use Illuminate\Console\Command;

class TranslateAllContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translate-all-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

     protected array $config=[
        [
            'type' => 'row',
            'model' => \App\Models\Product::class,
            'translation_model' => \App\Models\ProductTranslation::class,
            'foreign_key' => 'product_id',
        ],[
            'type' => 'key_value',
            'translation_model' => \App\Models\HeaderContent::class,
            'field_column' => 'meta_key',
            'value_column' => 'meta_value',
        ]

     ];


    protected int $defaultLangId = 1; // en
    protected array $targetLangIds = [3];


    public function handle()
    {
        //
        foreach($this->config as $item){
            if($item['type']=='row'){
                $this->translateRowBasedModel($item);
            }
            // if($item['type']=='key_value'){
            //     $this->translateKeyValueModel($item);
            // }
        }
    }


    protected function translateRowBasedModel(array $item)
    {
        $model = $item['model'];
        $translationModel = $item['translation_model'];
        $foreignKey = $item['foreign_key'];

        $records = $model::with(['translationsData' => function ($q) {
            $q->whereIn('lang_id', [$this->defaultLangId, ...$this->targetLangIds]);
        }])->get();

        $fields = $this->getTranslatableFields($translationModel, $foreignKey);

        foreach ($records as $record) {
            $original = $record->translationsData->firstWhere('lang_id', $this->defaultLangId);
            // $thi->info($original);s
            if (!$original) {
                $this->info("Skipping record ID {$record->id} – no default language found.");
                continue;
            }

            foreach ($this->targetLangIds as $langId) {
                $existing = $record->translationsData->firstWhere('lang_id', $langId);
                if ($existing) {
                    $this->info("Record ID {$record->id} already has translation for lang_id {$langId}.");
                    continue;
                }

                $language = \App\Models\Language::find($langId);
                $baseLangCode = null;

                if ($language && $language->base_language_id) {
                    $baseLang = \App\Models\Language::find($language->base_language_id);
                    $baseLangCode = $baseLang?->lang_code;
                }

                $lang_code = $baseLangCode ? $baseLangCode : $language?->lang_code;

                $translated = [
                    $foreignKey => $record->id,
                    'lang_id' => $langId
                ];

                foreach ($fields as $field) {
                    // $this->info("  {$field}: " . ($original->$field ?? NULL));
                    if($original->$field){
                        $translated[$field] = website_translator($original->$field, $lang_code);
                        $this->info(website_translator($original->$field, $lang_code));
                    }

                }




                

                // $this->info("Translating for model: " . class_basename($model) . " ID: {$record->id}, Lang: {$lang_code}");
                // foreach ($translated as $key => $value) {
                //     $this->info("  {$key}: {$value}");
                // }


                // $translationModel::create($translated);
                // $this->info("Translated record ID {$record->id} to lang_id {$langId}.");
            }
        }
    }



    private function translateKeyValueModel(array $item){
        $model = $item['translation_model'];
        $fieldCol = $item['field_column'];
        $valueCol = $item['value_column'];

        $originalEntries = $model::where('lang_id', $this->defaultLangId)->get();

        foreach ($originalEntries as $entry) {
            foreach ($this->targetLangIds as $langId) {
                $exists = $model::where('lang_id', $langId)
                    ->where($fieldCol, $entry->$fieldCol)
                    ->exists();

                if ($exists) continue;


                $language = \App\Models\Language::find($langId);

                if ($language && $language->base_language_id) {
                    $lang_code = \App\Models\Language::find($language->base_language_id)?->lang_code;
                } else {
                    $lang_code = $language?->lang_code;
                }


                $translatedValue = website_translator($entry->$valueCol,$lang_code);


                $model::create([
                    'lang_id' => $langId,
                    $fieldCol => $entry->$fieldCol,
                    $valueCol => $translatedValue,
                ]);

                $this->info("Translated [{$entry->$fieldCol}] to lang_id {$langId}");


            }
        }

    }


    private function getTranslatableFields($modelClass, $foreignKey)
    {
        $model = new $modelClass;
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);

        return collect($columns)->reject(fn($col) =>
            in_array($col, ['id', $foreignKey, 'lang_id', 'created_at', 'updated_at'])
        )->values()->toArray();
    }


}
