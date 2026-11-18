<?php

namespace App\Services;

use App\Models\Bcp47Language;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\EntitySectionTranslation;
use App\Models\Language;
use App\Models\TranslationBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessTranslationService
{
    /**
     * Core translatable fields on business_translations.
     */
    const TRANSLATABLE_FIELDS = [
        'short_description'        => 'Short Description',
        'description'              => 'Overview / What is',
        'long_preview_description' => 'Long Description',
        'reviews_description'      => 'Reviews Summary',
        'meta_title'               => 'Meta Title',
        'meta_description'         => 'Meta Description',
    ];

    /**
     * Map of locale codes (lower case) to system Language models.
     */
    protected static ?array $localeToLanguageCache = null;

    public static function getLocaleToLanguageMap(): array
    {
        if (self::$localeToLanguageCache !== null) {
            return self::$localeToLanguageCache;
        }

        $siteLanguages = Language::where('status', 1)->with('bcp47Language')->get();
        $map = [];
        foreach ($siteLanguages as $sLang) {
            $code = $sLang->bcp47Language?->code ?? $sLang->lang_code;
            if ($code) {
                $map[strtolower(trim($code))] = $sLang;
            }
            if (!empty($sLang->lang_code)) {
                $map[strtolower(trim($sLang->lang_code))] = $sLang;
            }
        }

        self::$localeToLanguageCache = $map;
        return $map;
    }

    /**
     * Resolve Language ID from a BCP47 or locale code string.
     */
    public static function resolveLangId(string $localeCode): ?int
    {
        $map = self::getLocaleToLanguageMap();
        $norm = strtolower(trim($localeCode));
        if (isset($map[$norm])) {
            return (int)$map[$norm]->id;
        }
        $parts = explode('-', $norm);
        if (isset($parts[0]) && isset($map[$parts[0]])) {
            return (int)$map[$parts[0]]->id;
        }
        return null;
    }

    /**
     * Resolve BCP47 code string from a Language ID.
     */
    public static function resolveLocaleCode(int $langId): string
    {
        $lang = Language::with('bcp47Language')->find($langId);
        return $lang?->bcp47Language?->code ?? ($lang?->lang_code ?? 'en-US');
    }

    /**
     * Calculate hash for field content.
     */
    public static function computeFieldHash(?string $content): string
    {
        return hash('sha256', trim((string)$content));
    }

    /**
     * Sync the English (lang_id = 1) source fields for a business into entity_section_translations.
     * When any English field content changes, existing target translations whose source_hash
     * differs from the new hash are marked OUTDATED.
     *
     * @param Business|int $business
     * @return void
     */
    public static function syncBusinessEnglishSource($business): void
    {
        $bizId = is_numeric($business) ? (int)$business : $business->id;
        $enTrans = BusinessTranslation::where('business_id', $bizId)->where('lang_id', 1)->first();
        if (!$enTrans) {
            return;
        }

        foreach (self::TRANSLATABLE_FIELDS as $fieldKey => $fieldLabel) {
            $currentEnText = (string)($enTrans->{$fieldKey} ?? '');
            if (trim($currentEnText) === '') {
                continue;
            }

            $newHash = self::computeFieldHash($currentEnText);

            // Fetch or create English reference row (lang_id = 1)
            $enRow = EntitySectionTranslation::firstOrNew([
                'entity_type' => 'business',
                'entity_id'   => $bizId,
                'field_key'   => $fieldKey,
                'lang_id'     => 1,
            ]);

            $hashChanged = ($enRow->exists && $enRow->source_hash !== $newHash);

            $enRow->section_group       = 'business_content';
            $enRow->source_headline     = $enTrans->name ?: "Business #{$bizId}";
            $enRow->source_subtext      = $currentEnText;
            $enRow->source_hash         = $newHash;
            $enRow->translated_subtext  = $currentEnText;
            $enRow->status              = EntitySectionTranslation::STATUS_COMPLETED;
            $enRow->save();

            // If English text changed, flag all target translations for this field as OUTDATED
            if ($hashChanged) {
                EntitySectionTranslation::where('entity_type', 'business')
                    ->where('entity_id', $bizId)
                    ->where('field_key', $fieldKey)
                    ->where('lang_id', '>', 1)
                    ->where(function ($q) use ($newHash) {
                        $q->where('source_hash', '!=', $newHash)
                          ->orWhereNull('source_hash');
                    })
                    ->update([
                        'source_subtext' => $currentEnText,
                        'source_hash'    => $newHash,
                        'status'         => EntitySectionTranslation::STATUS_OUTDATED,
                    ]);
            }
        }
    }

    /**
     * Synchronize and get detailed localization matrix for a list of businesses.
     * Returns:
     * [
     *    'status' => 'full'|'outdated'|'partial'|'missing',
     *    'outdated_fields' => [...],
     *    'missing_fields'  => [...],
     *    'completed_fields'=> [...]
     * ]
     */
    public static function getMatrixRowForBusiness(Business $biz, array $languages, array $langCodeToIdMap): array
    {
        $bizId = $biz->id;
        $enTrans = $biz->translations->firstWhere('lang_id', 1);

        // Fetch all tracked section translations for this business
        $sections = EntitySectionTranslation::where('entity_type', 'business')
            ->where('entity_id', $bizId)
            ->get();

        $enSections = $sections->where('lang_id', 1)->keyBy('field_key');

        $locales = [];
        $activeFields = array_keys(self::TRANSLATABLE_FIELDS);

        foreach ($languages as $lang) {
            $code = $lang['code'];
            $codeKey = strtolower($code);
            $matchedLangId = $langCodeToIdMap[$codeKey] ?? null;

            if ($codeKey === 'en-us' || $matchedLangId === 1) {
                $locales[$code] = [
                    'status'           => 'full',
                    'label'            => 'Source (en-US)',
                    'outdated_count'   => 0,
                    'missing_count'    => 0,
                    'completed_count'  => count($activeFields),
                    'outdated_fields'  => [],
                    'missing_fields'   => [],
                ];
                continue;
            }

            if (!$matchedLangId) {
                $locales[$code] = [
                    'status'          => 'missing',
                    'label'           => 'Language not configured',
                    'outdated_count'  => 0,
                    'missing_count'   => count($activeFields),
                    'completed_count' => 0,
                    'outdated_fields' => [],
                    'missing_fields'  => $activeFields,
                ];
                continue;
            }

            // Target language translation record from business_translations
            $targetTrans = $biz->translations->firstWhere('lang_id', $matchedLangId);
            $targetSections = $sections->where('lang_id', $matchedLangId)->keyBy('field_key');

            $outdatedFields = [];
            $missingFields = [];
            $completedFields = [];

            foreach ($activeFields as $fieldKey) {
                $enVal = (string)($enTrans?->{$fieldKey} ?? '');
                // If English has no content for this field, it doesn't need translation
                if (trim($enVal) === '') {
                    continue;
                }

                $enHash = self::computeFieldHash($enVal);
                $sec = $targetSections->get($fieldKey);
                $targetVal = (string)($targetTrans?->{$fieldKey} ?? '');

                if ($sec) {
                    if ($sec->status === EntitySectionTranslation::STATUS_OUTDATED || ($sec->source_hash && $sec->source_hash !== $enHash)) {
                        $outdatedFields[] = $fieldKey;
                    } elseif (!empty($sec->translated_subtext) || !empty($targetVal)) {
                        $completedFields[] = $fieldKey;
                    } else {
                        $missingFields[] = $fieldKey;
                    }
                } else {
                    if (!empty($targetVal)) {
                        // Translation exists in business_translations but not yet tracked in entity_sections
                        $completedFields[] = $fieldKey;
                    } else {
                        $missingFields[] = $fieldKey;
                    }
                }
            }

            $totalActive = count($completedFields) + count($outdatedFields) + count($missingFields);

            if ($totalActive === 0) {
                $status = 'missing';
            } elseif (count($outdatedFields) > 0) {
                // English content changed!
                $status = 'outdated';
            } elseif (count($completedFields) === $totalActive) {
                $status = 'full';
            } elseif (count($completedFields) > 0) {
                $status = 'partial';
            } else {
                $status = 'missing';
            }

            $locales[$code] = [
                'status'           => $status,
                'outdated_count'   => count($outdatedFields),
                'missing_count'    => count($missingFields),
                'completed_count'  => count($completedFields),
                'outdated_fields'  => $outdatedFields,
                'missing_fields'   => $missingFields,
                'completed_fields' => $completedFields,
            ];
        }

        return $locales;
    }

    /**
     * Create business translation batch jobs for selected businesses and target locales.
     * ONLY missing or outdated fields are included.
     *
     * @param array $businessIds
     * @param array $targetLocales
     * @param array $fields
     * @param string $provider
     * @param int $batchSize
     * @return array ['batches' => [...], 'request_count' => int, 'businesses_count' => int]
     */
    public static function createBusinessTranslationBatch(
        array $businessIds,
        array $targetLocales,
        array $fields,
        string $provider = 'manual',
        int $batchSize = 10
    ): array {
        $businessIds = array_values(array_unique(array_filter(array_map('intval', $businessIds))));
        if (empty($businessIds)) {
            return ['batches' => [], 'request_count' => 0, 'businesses_count' => 0];
        }

        $allTranslatable = self::TRANSLATABLE_FIELDS;
        $fieldsToProcess = !empty($fields) ? array_intersect(array_keys($allTranslatable), $fields) : array_keys($allTranslatable);
        if (empty($fieldsToProcess)) {
            $fieldsToProcess = array_keys($allTranslatable);
        }

        $businesses = Business::with(['translations'])->whereIn('id', $businessIds)->get();
        $langCodeToIdMap = [];
        foreach ($targetLocales as $loc) {
            $id = self::resolveLangId($loc);
            if ($id && $id > 1) {
                $langCodeToIdMap[$loc] = $id;
            }
        }

        // 1. Gather all translation units that are MISSING or OUTDATED
        $pendingUnits = [];

        foreach ($businesses as $biz) {
            // First ensure English reference hashes are synced
            self::syncBusinessEnglishSource($biz);

            $enTrans = $biz->translations->firstWhere('lang_id', 1);
            if (!$enTrans) {
                continue;
            }

            $bizName = $enTrans->name ?: ($biz->title ?: "Business #{$biz->id}");

            foreach ($langCodeToIdMap as $targetLocale => $targetLangId) {
                $targetTrans = $biz->translations->firstWhere('lang_id', $targetLangId);

                foreach ($fieldsToProcess as $fieldKey) {
                    $enText = (string)($enTrans->{$fieldKey} ?? '');
                    if (trim($enText) === '') {
                        continue;
                    }

                    $enHash = self::computeFieldHash($enText);

                    // Check existing section translation row
                    $sec = EntitySectionTranslation::firstOrNew([
                        'entity_type' => 'business',
                        'entity_id'   => $biz->id,
                        'field_key'   => $fieldKey,
                        'lang_id'     => $targetLangId,
                    ]);

                    $targetVal = (string)($targetTrans?->{$fieldKey} ?? '');
                    $isOutdated = ($sec->exists && $sec->status === EntitySectionTranslation::STATUS_OUTDATED) || ($sec->source_hash && $sec->source_hash !== $enHash);
                    $isMissing = empty($sec->translated_subtext) && empty($targetVal);

                    // Only process if OUTDATED (English changed) or MISSING!
                    if ($isOutdated || $isMissing) {
                        $pendingUnits[] = [
                            'business_id'     => $biz->id,
                            'business_name'   => $bizName,
                            'field_key'       => $fieldKey,
                            'field_label'     => $allTranslatable[$fieldKey] ?? $fieldKey,
                            'target_locale'   => $targetLocale,
                            'target_lang_id'  => $targetLangId,
                            'english_text'    => $enText,
                            'english_hash'    => $enHash,
                            'section_row'     => $sec,
                        ];
                    }
                }
            }
        }

        if (empty($pendingUnits)) {
            return ['batches' => [], 'request_count' => 0, 'businesses_count' => 0];
        }

        // 2. Chunk units into batches by batch_size (number of businesses per batch)
        $batchSize = max(1, min(50, $batchSize));
        $unitsByBusiness = [];
        foreach ($pendingUnits as $u) {
            $unitsByBusiness[$u['business_id']][] = $u;
        }

        $bizIdChunks = array_chunk(array_keys($unitsByBusiness), $batchSize);
        $createdBatches = [];

        foreach ($bizIdChunks as $chunkBizIds) {
            $chunkUnits = [];
            foreach ($chunkBizIds as $bId) {
                foreach ($unitsByBusiness[$bId] as $u) {
                    $chunkUnits[] = $u;
                }
            }

            if (empty($chunkUnits)) {
                continue;
            }

            $batch = TranslationBatch::create([
                'type'             => 'business_translation',
                'model'            => $provider === 'anthropic' ? 'claude-3-5-sonnet' : 'gpt-5.6-luna',
                'prompt_id'        => 'Localio Business Localization',
                'prompt_version'   => '1.0',
                'request_count'    => count($chunkUnits),
                'status'           => TranslationBatch::STATUS_WAITING,
                'successful_count' => 0,
                'failed_count'     => 0,
            ]);

            // Link section rows to batch and mark as batched
            foreach ($chunkUnits as $u) {
                /** @var EntitySectionTranslation $sec */
                $sec = $u['section_row'];
                $sec->section_group       = 'business_content';
                $sec->source_headline     = $u['business_name'];
                $sec->source_subtext      = $u['english_text'];
                $sec->source_hash         = $u['english_hash'];
                $sec->batch_id            = $batch->id;
                $sec->status              = EntitySectionTranslation::STATUS_BATCHED;
                $sec->save();
            }

            $createdBatches[] = $batch;
        }

        return [
            'batches'          => $createdBatches,
            'request_count'    => count($pendingUnits),
            'businesses_count' => count($unitsByBusiness),
        ];
    }

    /**
     * Build clean JSON payload for a business translation batch to download for ChatGPT / Claude.
     */
    public static function buildBusinessBatchPayload(TranslationBatch $batch): array
    {
        $sections = EntitySectionTranslation::where('batch_id', $batch->id)
            ->where('entity_type', 'business')
            ->get();

        $items = [];
        foreach ($sections as $sec) {
            $locale = self::resolveLocaleCode($sec->lang_id);
            $customId = "biz_{$sec->entity_id}_{$sec->field_key}_{$locale}";

            $items[] = [
                'custom_id'       => $customId,
                'business_id'     => (int)$sec->entity_id,
                'business_name'   => $sec->source_headline ?: "Business #{$sec->entity_id}",
                'field_key'       => $sec->field_key,
                'field_label'     => self::TRANSLATABLE_FIELDS[$sec->field_key] ?? $sec->field_key,
                'target_locale'   => $locale,
                'english_text'    => $sec->source_subtext ?: '',
                'translated_text' => '',
            ];
        }

        return [
            'batch_id'     => $batch->id,
            'type'         => 'business_translation',
            'model'        => $batch->model ?: 'gpt-5.6-luna',
            'total_items'  => count($items),
            'instructions' => "Translate each english_text into natural, fluent {$batch->target_locale} or the item's target_locale. Maintain professional tone, Markdown or HTML formatting where present, and marketing clarity. Populate translated_text and return the full JSON.",
            'items'        => $items,
        ];
    }

    /**
     * Process result items uploaded via manual JSON or returned from AI batch.
     * Updates both business_translations and entity_section_translations.
     */
    public static function processBusinessBatchResult(TranslationBatch $batch, array $items): array
    {
        $successful = 0;
        $failed = 0;

        foreach ($items as $item) {
            $customId = $item['custom_id'] ?? null;
            $bizId = isset($item['business_id']) ? (int)$item['business_id'] : null;
            $fieldKey = $item['field_key'] ?? null;
            $targetLocale = $item['target_locale'] ?? null;
            $translatedText = $item['translated_text'] ?? ($item['translation'] ?? ($item['text'] ?? null));

            // Parse custom_id if needed: biz_{id}_{fieldKey}_{locale}
            if (!$bizId && $customId && preg_match('/^biz_(\d+)_([a-zA-Z0-9_]+)_([a-zA-Z0-9\-]+)$/', $customId, $m)) {
                $bizId = (int)$m[1];
                $fieldKey = $m[2];
                $targetLocale = $m[3];
            }

            if (!$bizId || !$fieldKey) {
                continue;
            }

            $targetLangId = $targetLocale ? self::resolveLangId($targetLocale) : null;
            if (!$targetLangId) {
                $failed++;
                continue;
            }

            if (empty(trim((string)$translatedText))) {
                $failed++;
                continue;
            }

            try {
                // 1. Update business_translations for the target language
                $bt = BusinessTranslation::firstOrNew([
                    'business_id' => $bizId,
                    'lang_id'     => $targetLangId,
                ]);

                if (in_array($fieldKey, array_keys(self::TRANSLATABLE_FIELDS))) {
                    $bt->{$fieldKey} = trim($translatedText);
                }

                // If business name is missing on target translation, copy from English
                if (empty($bt->name)) {
                    $enTrans = BusinessTranslation::where('business_id', $bizId)->where('lang_id', 1)->first();
                    $bt->name = $enTrans?->name ?? "Business #{$bizId}";
                }
                $bt->save();

                // 2. Fetch current English row to get latest source_hash
                $enSec = EntitySectionTranslation::where('entity_type', 'business')
                    ->where('entity_id', $bizId)
                    ->where('field_key', $fieldKey)
                    ->where('lang_id', 1)
                    ->first();

                $enHash = $enSec?->source_hash ?? self::computeFieldHash($enSec?->source_subtext ?? '');

                // 3. Update entity_section_translations row for tracking and change detection
                EntitySectionTranslation::updateOrCreate(
                    [
                        'entity_type' => 'business',
                        'entity_id'   => $bizId,
                        'field_key'   => $fieldKey,
                        'lang_id'     => $targetLangId,
                    ],
                    [
                        'section_group'      => 'business_content',
                        'source_headline'    => $enSec?->source_headline ?: "Business #{$bizId}",
                        'source_subtext'     => $enSec?->source_subtext ?: '',
                        'source_hash'        => $enHash,
                        'translated_subtext' => trim($translatedText),
                        'status'             => EntitySectionTranslation::STATUS_COMPLETED,
                        'batch_id'           => $batch->id,
                    ]
                );

                $successful++;
            } catch (\Throwable $e) {
                Log::error("Failed to apply translation item for business {$bizId}, field {$fieldKey}: " . $e->getMessage());
                $failed++;
            }
        }

        $batch->update([
            'successful_count' => ($batch->successful_count ?? 0) + $successful,
            'failed_count'     => ($batch->failed_count ?? 0) + $failed,
            'status'           => TranslationBatch::STATUS_COMPLETED,
            'completed_at'     => now(),
        ]);

        return [
            'successful' => $successful,
            'failed'     => $failed,
            'processed'  => $successful + $failed,
        ];
    }
}
