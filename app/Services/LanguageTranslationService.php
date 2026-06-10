<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Language;

class LanguageTranslationService
{
    /**
     * The API key for translation service
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Default source language ID
     *
     * @var int
     */
    protected $defaultLangId = 1;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiKey = config('services.translation.api_key');
    }

    /**
     * Create translations for a specific model if they don't exist
     *
     * @param string $modelClass The fully qualified model class name
     * @param int $targetLangId The target language ID
     * @param array $translatableFields Fields to translate
     * @return array Result of the operation
     */
    public function createTranslationsIfNotExist(string $modelClass, int $targetLangId, array $translatableFields = []): array
    {
        $result = [
            'success' => true,
            'message' => 'No translation needed',
            'created' => 0,
            'errors' => []
        ];

        try {
            // Check if model exists
            if (!class_exists($modelClass)) {
                throw new \Exception("Model class {$modelClass} not found");
            }

            // Get source language code and target language code
            $sourceLang = Language::where('id', $this->defaultLangId)->first();
            $targetLang = Language::where('id', $targetLangId)->first();

            if (!$sourceLang || !$targetLang) {
                throw new \Exception("Source or target language not found");
            }

            $sourceCode = $sourceLang->lang_code;
            $targetCode = $targetLang->lang_code;

            // Check if data exists for target language
            $targetData = $modelClass::where('lang_id', $targetLangId)->first();

            if (!$targetData) {
                // Get source data
                $sourceData = $modelClass::where('lang_id', $this->defaultLangId)->first();

                if (!$sourceData) {
                    throw new \Exception("No source data found for language ID {$this->defaultLangId}");
                }

                // Create new record for target language
                $newData = $sourceData->replicate();
                $newData->lang_id = $targetLangId;

                // If translatable fields are provided, translate them
                if (!empty($translatableFields)) {
                    foreach ($translatableFields as $field) {
                        if (property_exists($sourceData, $field) && !empty($sourceData->$field)) {
                            $newData->$field = $this->translateText(
                                $sourceData->$field,
                                $sourceCode,
                                $targetCode
                            );
                        }
                    }
                }

                $newData->save();
                $result['created'] = 1;
                $result['message'] = "Translation created for {$modelClass}";
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Translation error: " . $e->getMessage(), [
                'model' => $modelClass,
                'target_lang_id' => $targetLangId
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'created' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Create translations for multiple models
     *
     * @param array $models Array of model configurations
     * @param int $targetLangId Target language ID
     * @return array Results of the operation
     */
    public function createMultipleTranslations(array $models, int $targetLangId): array
    {
        $result = [
            'success' => true,
            'total_created' => 0,
            'details' => [],
            'errors' => []
        ];

        foreach ($models as $model) {
            $modelClass = $model['model'];
            $translatableFields = $model['fields'] ?? [];

            $modelResult = $this->createTranslationsIfNotExist($modelClass, $targetLangId, $translatableFields);
            $result['details'][$modelClass] = $modelResult;
            $result['total_created'] += $modelResult['created'];

            if (!$modelResult['success']) {
                $result['success'] = false;
                $result['errors'][] = $modelResult['message'];
            }
        }

        return $result;
    }

    /**
     * Create translations for related models with relationships
     *
     * @param string $parentModel Parent model class
     * @param array $relations Array of relation configurations
     * @param int $targetLangId Target language ID
     * @return array Results of the operation
     */
    public function createRelatedTranslations(string $parentModel, array $relations, int $targetLangId): array
    {
        $result = [
            'success' => true,
            'total_created' => 0,
            'details' => [],
            'errors' => []
        ];

        try {
            // First create the parent model translation if needed
            $parentResult = $this->createTranslationsIfNotExist(
                $parentModel,
                $targetLangId,
                $relations['parent_fields'] ?? []
            );

            $result['details']['parent'] = $parentResult;
            $result['total_created'] += $parentResult['created'];

            // Now handle related models
            if (!empty($relations['related'])) {
                foreach ($relations['related'] as $relation) {
                    $relationName = $relation['relation'];
                    $modelClass = $relation['model'];
                    $translatableFields = $relation['fields'] ?? [];
                    $foreignKey = $relation['foreign_key'] ?? 'parent_id';

                    // Get parent model instances
                    $sourceParent = $parentModel::where('lang_id', $this->defaultLangId)->first();
                    $targetParent = $parentModel::where('lang_id', $targetLangId)->first();

                    if (!$sourceParent || !$targetParent) {
                        throw new \Exception("Source or target parent model not found");
                    }

                    // Get related items from source parent
                    $sourceItems = $sourceParent->$relationName;

                    foreach ($sourceItems as $sourceItem) {
                        // Check if this item exists for target language
                        $exists = DB::table((new $modelClass)->getTable())
                            ->where('lang_id', $targetLangId)
                            ->where($foreignKey, $targetParent->id)
                            ->where('reference_id', $sourceItem->id) // Assuming you have a reference_id column
                            ->exists();

                        if (!$exists) {
                            // Create new related item
                            $newItem = $sourceItem->replicate();
                            $newItem->lang_id = $targetLangId;
                            $newItem->$foreignKey = $targetParent->id;
                            $newItem->reference_id = $sourceItem->id;

                            // Translate fields
                            $sourceLang = Language::where('id', $this->defaultLangId)->first();
                            $targetLang = Language::where('id', $targetLangId)->first();

                            if ($sourceLang && $targetLang) {
                                $sourceCode = $sourceLang->lang_code;
                                $targetCode = $targetLang->lang_code;

                                foreach ($translatableFields as $field) {
                                    if (!empty($sourceItem->$field)) {
                                        $newItem->$field = $this->translateText(
                                            $sourceItem->$field,
                                            $sourceCode,
                                            $targetCode
                                        );
                                    }
                                }
                            }

                            $newItem->save();
                            $result['total_created']++;
                        }
                    }
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Related translation error: " . $e->getMessage(), [
                'parent_model' => $parentModel,
                'target_lang_id' => $targetLangId
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'total_created' => $result['total_created'],
                'details' => $result['details'],
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Translate text using external API
     *
     * @param string $text Text to translate
     * @param string $sourceLanguage Source language code
     * @param string $targetLanguage Target language code
     * @return string Translated text
     */
    protected function translateText(string $text, string $sourceLanguage, string $targetLanguage): string
    {
        // Skip translation if languages are the same
        if ($sourceLanguage === $targetLanguage) {
            return $text;
        }

        try {
            // Use Google Cloud Translation API
            // You can replace this with any translation service you prefer
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://translation.googleapis.com/v3/projects/' . config('services.translation.project_id') . ':translateText', [
                'sourceLanguageCode' => $sourceLanguage,
                'targetLanguageCode' => $targetLanguage,
                'contents' => [$text],
                'mimeType' => 'text/plain',
            ]);

            if ($response->successful()) {
                $translatedText = $response->json()['translations'][0]['translatedText'] ?? $text;
                return $translatedText;
            }

            // If API call fails, log error and return original text
            Log::error('Translation API error: ' . $response->body());
            return $text;
        } catch (\Exception $e) {
            Log::error('Translation exception: ' . $e->getMessage());
            return $text;
        }
    }

    /**
     * Set default source language ID
     *
     * @param int $langId
     * @return $this
     */
    public function setDefaultLanguageId(int $langId): self
    {
        $this->defaultLangId = $langId;
        return $this;
    }
}
