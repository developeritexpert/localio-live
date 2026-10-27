<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bcp47Language;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\Category;
use App\Models\Feature;
use App\Models\ContentTemplate;
use App\Models\EntitySectionTranslation;
use App\Services\ContentTemplateService;
use App\Models\Country;
use App\Models\Language;
use App\Models\Review;
use App\Models\ReviewTranslation;
use App\Models\StaticContentKey;
use App\Models\StaticContentTranslation;
use App\Models\TranslationBatch;
use App\Models\WebSetting;
use App\Services\ReviewModerationService;
use App\Services\ReviewTranslationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TranslationsController extends Controller
{
    /**
     * Common supported BCP47 languages for the matrix tables formatted for views.
     */
    protected function getMatrixLanguages(): array
    {
        return Bcp47Language::where('status', 1)->orderBy('code')->get()->map(function ($l) {
            return [
                'code' => $l->code,
                'name' => $l->name,
            ];
        })->toArray();
    }

    /**
     * Map of lowercase BCP47 and lang_code strings to system language IDs.
     */
    protected function getLangCodeToIdMap(): array
    {
        $siteLanguages = Language::where('status', 1)->with('bcp47Language')->get();
        $map = [];
        foreach ($siteLanguages as $sLang) {
            $code = $sLang->bcp47Language?->code ?? $sLang->lang_code;
            if ($code) {
                $map[strtolower($code)] = $sLang->id;
            }
            if (!empty($sLang->lang_code)) {
                $map[strtolower($sLang->lang_code)] = $sLang->id;
            }
        }
        return $map;
    }

    // =========================================================================
    // 1. BUSINESSES TRANSLATION MATRIX
    // =========================================================================
    public function businesses(Request $request)
    {
        $languages = $this->getMatrixLanguages();
        $langCodeToIdMap = $this->getLangCodeToIdMap();

        $query = Business::with(['category', 'countries', 'translations']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('translations', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('country_id')) {
            $countryId = $request->country_id;
            $query->whereHas('countries', function ($q) use ($countryId) {
                $q->where('countries.id', $countryId);
            });
        }

        $businesses = $query->orderBy('id', 'desc')->paginate(20);
        $categories = Category::onlyParents()->orderBy('id')->with('translation')->get();
        $countries = Country::orderBy('name')->get();

        // Build matrix table data for view
        $matrix = [];
        foreach ($businesses as $biz) {
            $locales = [];
            $transLangIds = $biz->translations->pluck('lang_id')->toArray();

            foreach ($languages as $lang) {
                $codeKey = strtolower($lang['code']);
                $matchedId = $langCodeToIdMap[$codeKey] ?? null;

                if ($matchedId && in_array($matchedId, $transLangIds)) {
                    $tr = $biz->translations->firstWhere('lang_id', $matchedId);
                    $hasAbout = !empty($tr->about);
                    $hasDesc = !empty($tr->description);
                    $status = ($hasAbout && $hasDesc) ? 'full' : 'partial';
                } else {
                    $status = 'missing';
                }
                $locales[$lang['code']] = ['status' => $status];
            }

            $matrix[] = [
                'business' => $biz,
                'locales'  => $locales,
            ];
        }

        return view('Admin.translations.businesses', compact('businesses', 'matrix', 'languages', 'categories', 'countries'));
    }

    // =========================================================================
    // 2. REVIEWS TRANSLATION MANAGER
    // =========================================================================
    public function reviews(Request $request)
    {
        $statusFilter = $request->input('status', 'all');
        $query = ReviewTranslation::with(['review.business.translations', 'batch'])
            ->whereNotNull('target_locale');

        if ($statusFilter !== 'all') {
            $query->where('status', strtoupper($statusFilter));
        }

        if ($request->filled('source_locale')) {
            $query->where('source_locale', $request->source_locale);
        }

        if ($request->filled('target_locale')) {
            $query->where('target_locale', $request->target_locale);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('translated_title', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('translated_text', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reviews_id', $search)
                  ->orWhere('review_id', $search);
            });
        }

        $translations = $query->orderBy('id', 'desc')->paginate(25);

        // Counts for status tabs
        $counts = [
            'all'          => ReviewTranslation::whereNotNull('target_locale')->count(),
            'waiting'      => ReviewTranslation::whereNotNull('target_locale')->where('status', ReviewTranslation::STATUS_WAITING)->count(),
            'batched'      => ReviewTranslation::whereNotNull('target_locale')->where('status', ReviewTranslation::STATUS_BATCHED)->count(),
            'completed'    => ReviewTranslation::whereNotNull('target_locale')->where('status', ReviewTranslation::STATUS_COMPLETED)->count(),
            'outdated'     => ReviewTranslation::whereNotNull('target_locale')->where('status', ReviewTranslation::STATUS_OUTDATED)->count(),
            'failed'       => ReviewTranslation::whereNotNull('target_locale')->where('status', ReviewTranslation::STATUS_FAILED)->count(),
            'needs_review' => ReviewTranslation::whereNotNull('target_locale')->where('status', ReviewTranslation::STATUS_NEEDS_REVIEW)->count(),
        ];

        $locales = ReviewTranslationService::getSupportedTargetLocales();

        return view('Admin.translations.reviews', compact('translations', 'counts', 'statusFilter', 'locales'));
    }

    /**
     * Dispatch all WAITING review translations into a new Batch.
     */
    public function dispatchReviewBatch(Request $request)
    {
        $batch = ReviewTranslationService::createBatchFromWaiting(5000);

        if (!$batch) {
            return redirect()->back()->with('error', 'No WAITING review translations found to dispatch.');
        }

        $apiKey = config('openai.api_key') ?: optional(WebSetting::where('key', 'openai_api_key')->first())->value;
        if ($apiKey && $request->boolean('submit_api', false)) {
            $this->submitBatchToOpenAI($batch, $apiKey);
            return redirect()->route('translations.batch-jobs')
                ->with('success', "Batch #{$batch->id} submitted to OpenAI API ({$batch->request_count} requests).");
        }

        return redirect()->route('translations.batch-jobs')
            ->with('success', "Batch #{$batch->id} created with {$batch->request_count} translation requests. Ready for JSON download or API submission.");
    }

    /**
     * Retry an individual failed translation.
     */
    public function retryReviewTranslation(Request $request, $id = null)
    {
        $transId = $id ?: $request->input('translation_id');
        $trans = ReviewTranslation::findOrFail($transId);
        $trans->update([
            'status'      => ReviewTranslation::STATUS_WAITING,
            'retry_count' => 0,
        ]);

        return redirect()->back()->with('success', "Translation #{$transId} reset to WAITING. It will be included in the next batch.");
    }

    // =========================================================================
    // 3. WEBSITE STRINGS TRANSLATION MATRIX
    // =========================================================================
    public function website(Request $request)
    {
        $languages = $this->getMatrixLanguages();
        $langCodeToIdMap = $this->getLangCodeToIdMap();

        $query = StaticContentKey::with('translations');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('default_value', 'like', "%{$search}%");
            });
        }

        if ($request->filled('field_type')) {
            $query->where('field_type', $request->field_type);
        }

        $keys = $query->orderBy('key')->paginate(30);
        $fieldTypes = StaticContentKey::select('field_type')->distinct()->whereNotNull('field_type')->pluck('field_type');

        $matrix = [];
        foreach ($keys as $k) {
            $locales = [];
            $transLangIds = $k->translations->pluck('lang_id')->toArray();

            foreach ($languages as $lang) {
                $codeKey = strtolower($lang['code']);
                $matchedId = $langCodeToIdMap[$codeKey] ?? null;

                if ($matchedId && in_array($matchedId, $transLangIds)) {
                    $tr = $k->translations->firstWhere('lang_id', $matchedId);
                    $hasVal = !empty($tr->value);
                    $status = $hasVal ? 'full' : 'missing';
                    $val = $tr->value ?? '';
                } else {
                    $status = 'missing';
                    $val = '';
                }

                $locales[$lang['code']] = [
                    'status' => $status,
                    'val'    => $val,
                ];
            }

            $matrix[] = [
                'key'           => $k->key,
                'name'          => $k->name ?? $k->key,
                'default_value' => $k->default_value ?? $k->key,
                'field_type'    => $k->field_type ?? 'text',
                'locales'       => $locales,
            ];
        }

        return view('Admin.translations.website', compact('keys', 'matrix', 'languages', 'fieldTypes'));
    }

    /**
     * Export Website missing strings for manual ChatGPT translation.
     */
    public function exportWebsiteJson(Request $request)
    {
        $targetLocale = $request->input('target_locale', 'de-DE');
        $keys = StaticContentKey::with('translations')->get();

        $exportData = [];
        foreach ($keys as $k) {
            $enTrans = $k->translations->firstWhere('lang_id', 1);
            $exportData[] = [
                'key'            => $k->key,
                'english_value'  => $enTrans?->value ?? $k->default_value ?? $k->key,
                'translated_val' => '',
            ];
        }

        $payload = [
            'type'          => 'website_strings',
            'target_locale' => $targetLocale,
            'count'         => count($exportData),
            'instructions'  => "Translate each english_value into {$targetLocale} and populate translated_val.",
            'strings'       => $exportData,
        ];

        $filename = "localio_website_strings_{$targetLocale}_" . date('Y-m-d_His') . ".json";
        return response()->json($payload, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Import Website translated JSON.
     */
    public function importWebsiteJson(Request $request)
    {
        $request->validate([
            'json_file' => 'nullable|file|mimes:json,txt',
            'json_text' => 'nullable|string',
        ]);

        $rawJson = $request->input('json_text');
        if ($request->hasFile('json_file')) {
            $rawJson = file_get_contents($request->file('json_file')->getRealPath());
        }

        if (empty($rawJson)) {
            return redirect()->back()->with('error', 'Please provide a JSON file or paste JSON text.');
        }

        $data = json_decode($rawJson, true);
        if (!is_array($data)) {
            return redirect()->back()->with('error', 'Invalid JSON structure provided.');
        }

        $targetLocale = $data['target_locale'] ?? 'en-US';
        $strings = $data['strings'] ?? $data;

        $lang = Language::where('lang_code', strtolower($targetLocale))->first()
            ?? Language::whereHas('bcp47Language', fn($q) => $q->where('code', $targetLocale))->first();

        $langId = $lang ? $lang->id : 1;
        $updated = 0;

        foreach ($strings as $item) {
            $key = $item['key'] ?? null;
            $val = $item['translated_val'] ?? ($item['value'] ?? null);

            if ($key && !empty($val)) {
                $keyModel = StaticContentKey::where('key', $key)->first();
                if ($keyModel) {
                    StaticContentTranslation::updateOrCreate(
                        [
                            'static_content_key_id' => $keyModel->id,
                            'lang_id'               => $langId,
                        ],
                        [
                            'value' => $val,
                        ]
                    );
                    $updated++;
                }
            }
        }

        return redirect()->back()->with('success', "Successfully imported {$updated} translated website strings for locale {$targetLocale}.");
    }

    // =========================================================================
    // 4. TEMPLATES TRANSLATION MATRIX
    // =========================================================================
    public function templates(Request $request)
    {
        $languages = $this->getMatrixLanguages();
        $langCodeToIdMap = $this->getLangCodeToIdMap();

        $activeTab = $request->query('tab', 'categories');
        if (!in_array($activeTab, ['categories', 'businesses', 'features', 'global_templates'], true)) {
            $activeTab = 'categories';
        }

        $search = trim((string)$request->query('search'));
        $sectionGroup = $request->query('section_group');
        $selectedCategoryId = $request->query('category_id');

        // Auto-seed/sync reference rows if current tab table is empty
        $checkCount = EntitySectionTranslation::where('lang_id', 1)
            ->where('entity_type', $activeTab === 'global_templates' ? 'template' : ($activeTab === 'categories' ? 'category' : ($activeTab === 'businesses' ? 'business' : 'feature')))
            ->count();
        if ($checkCount === 0) {
            \Artisan::call('translations:sync-page-sections', [
                '--type' => ($activeTab === 'global_templates' ? 'template' : $activeTab),
                '--limit' => 25
            ]);
        }

        // Query EntitySectionTranslation for lang_id = 1 (source of truth in English)
        $query = EntitySectionTranslation::where('lang_id', 1);

        if ($activeTab === 'categories') {
            $query->where('entity_type', 'category');
            if (!empty($selectedCategoryId)) {
                $query->where('entity_id', $selectedCategoryId);
            }
        } elseif ($activeTab === 'businesses') {
            $query->where('entity_type', 'business');
        } elseif ($activeTab === 'features') {
            $query->where('entity_type', 'feature');
        } elseif ($activeTab === 'global_templates') {
            $query->where('entity_type', 'template');
        }

        if (!empty($sectionGroup)) {
            $query->where('section_group', $sectionGroup);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('field_key', 'like', "%{$search}%")
                  ->orWhere('source_headline', 'like', "%{$search}%")
                  ->orWhere('source_subtext', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('id', 'asc')->paginate(25)->withQueryString();

        // Preload target language translations for items on current page
        $lookupKeys = [];
        foreach ($items as $item) {
            $lookupKeys[] = [
                'entity_type' => $item->entity_type,
                'entity_id'   => $item->entity_id,
                'field_key'   => $item->field_key,
            ];
        }

        $targetRows = collect();
        if (!empty($lookupKeys)) {
            $targetQuery = EntitySectionTranslation::where('lang_id', '>', 1)
                ->where(function ($q) use ($lookupKeys) {
                    foreach ($lookupKeys as $lk) {
                        $q->orWhere(function ($sub) use ($lk) {
                            $sub->where('entity_type', $lk['entity_type'])
                                ->where('entity_id', $lk['entity_id'])
                                ->where('field_key', $lk['field_key']);
                        });
                    }
                });
            $targetRows = $targetQuery->get();
        }

        // Preload entity names for display
        $categoryIds = $items->where('entity_type', 'category')->pluck('entity_id')->unique()->filter()->values();
        $categoriesMap = Category::whereIn('id', $categoryIds)->with('translations')->get()->keyBy('id');

        $businessIds = $items->where('entity_type', 'business')->pluck('entity_id')->unique()->filter()->values();
        $businessesMap = Business::whereIn('id', $businessIds)->with('translations')->get()->keyBy('id');

        $featureIds = $items->where('entity_type', 'feature')->pluck('entity_id')->unique()->filter()->values();
        $featuresMap = Feature::whereIn('id', $featureIds)->with('translations')->get()->keyBy('id');

        $matrix = [];
        foreach ($items as $item) {
            $entityTitle = 'Global Template';
            $pageUrl = '#';
            $pageType = 'Template';

            if ($item->entity_type === 'category') {
                $cat = $categoriesMap->get($item->entity_id);
                $catName = $cat?->translations?->first()?->name ?? $cat?->name ?? ('Category #' . $item->entity_id);
                $catSlug = $cat?->translations?->first()?->slug ?? $cat?->slug ?? $cat?->id;
                if ($cat && $cat->parent_id) {
                    $entityTitle = "Subcategory: {$catName}";
                    $pageType = 'Subcategory Page';
                } else {
                    $entityTitle = "Category: {$catName}";
                    $pageType = 'Category Page';
                }
                if ($catSlug) {
                    if (str_contains($item->field_key, 'faq')) {
                        $pageUrl = url("/en-us/{$catSlug}/faqs");
                    } elseif (str_contains($item->field_key, 'discussion')) {
                        $pageUrl = url("/en-us/{$catSlug}/discussions");
                    } else {
                        $pageUrl = url("/en-us/{$catSlug}");
                    }
                }
            } elseif ($item->entity_type === 'business') {
                $biz = $businessesMap->get($item->entity_id);
                $bizName = $biz?->translations?->first()?->name ?? $biz?->title ?? $biz?->name ?? ('Business #' . $item->entity_id);
                $bizSlug = $biz?->translations?->first()?->slug ?? $biz?->slug ?? $biz?->id;
                $entityTitle = "Business: {$bizName}";
                $pageType = 'Business Page';
                if ($bizSlug) {
                    if (str_contains($item->field_key, 'alternatives')) {
                        $pageUrl = url("/en-us/{$bizSlug}/alternatives");
                    } elseif (str_contains($item->field_key, 'faq')) {
                        $pageUrl = url("/en-us/{$bizSlug}/faqs");
                    } elseif (str_contains($item->field_key, 'pricing')) {
                        $pageUrl = url("/en-us/{$bizSlug}/pricing");
                    } elseif (str_contains($item->field_key, 'reviews') || str_contains($item->field_key, 'user_reviews')) {
                        $pageUrl = url("/en-us/{$bizSlug}/reviews");
                    } elseif (str_contains($item->field_key, 'discussion')) {
                        $pageUrl = url("/en-us/{$bizSlug}/discussions");
                    } else {
                        $pageUrl = url("/en-us/{$bizSlug}");
                    }
                }
            } elseif ($item->entity_type === 'feature') {
                $feat = $featuresMap->get($item->entity_id);
                $featName = $feat?->translations?->first()?->name ?? $feat?->name ?? ('Feature #' . $item->entity_id);
                $entityTitle = "Feature: {$featName}";
                $pageType = 'Feature Page';
                $featCat = $feat?->category;
                $featCatSlug = $featCat?->translations?->first()?->slug ?? $featCat?->slug;
                $featSlug = $feat?->translations?->first()?->slug ?? Str::slug($featName);
                if ($featCat && $featCat->parent_id && $featSlug) {
                    $parentCat = Category::find($featCat->parent_id);
                    $parentSlug = $parentCat?->translations?->first()?->slug ?? $parentCat?->slug;
                    if ($parentSlug) {
                        $pageUrl = url("/en-us/{$parentSlug}/{$featCatSlug}/{$featSlug}");
                    } else {
                        $pageUrl = url("/en-us/{$featCatSlug}/{$featSlug}");
                    }
                } elseif ($featCatSlug && $featSlug) {
                    $pageUrl = url("/en-us/{$featCatSlug}/{$featSlug}");
                }
            }

            $locales = [];
            foreach ($languages as $lang) {
                $codeKey = strtolower($lang['code']);
                $matchedId = $langCodeToIdMap[$codeKey] ?? null;

                if ($codeKey === 'en-us' || $matchedId === 1) {
                    $locales[$lang['code']] = [
                        'status' => 'full',
                        'label'  => 'Original Source (en-US)',
                        'row_id' => $item->id,
                        'lang_id'=> 1,
                    ];
                    continue;
                }

                $matchingTrans = $targetRows->first(function ($r) use ($item, $matchedId) {
                    return $r->entity_type === $item->entity_type
                        && (int)$r->entity_id === (int)$item->entity_id
                        && $r->field_key === $item->field_key
                        && (int)$r->lang_id === (int)$matchedId;
                });

                if ($matchingTrans) {
                    if ($matchingTrans->status === EntitySectionTranslation::STATUS_OUTDATED) {
                        $status = 'outdated';
                    } elseif (!empty($matchingTrans->translated_headline) || !empty($matchingTrans->translated_subtext)) {
                        $status = 'full';
                    } else {
                        $status = 'missing';
                    }
                    $rowId = $matchingTrans->id;
                } else {
                    $status = 'missing';
                    $rowId = null;
                }

                $locales[$lang['code']] = [
                    'status' => $status,
                    'row_id' => $rowId,
                    'lang_id'=> $matchedId,
                ];
            }

            $matrix[] = [
                'id'              => $item->id,
                'entity_type'     => $item->entity_type,
                'entity_id'       => $item->entity_id,
                'entity_title'    => $entityTitle,
                'page_type'       => $pageType,
                'page_url'        => $pageUrl,
                'field_key'       => $item->field_key,
                'section_group'   => $item->section_group,
                'source_headline' => $item->source_headline,
                'source_subtext'  => $item->source_subtext,
                'source_hash'     => $item->source_hash,
                'locales'         => $locales,
            ];
        }

        $allCategories = Category::onlyParents()->with('translations')->get();
        $sectionGroups = EntitySectionTranslation::select('section_group')->distinct()->whereNotNull('section_group')->pluck('section_group');

        return view('Admin.translations.templates', compact(
            'items',
            'matrix',
            'languages',
            'activeTab',
            'search',
            'sectionGroup',
            'selectedCategoryId',
            'allCategories',
            'sectionGroups'
        ));
    }

    /**
     * AJAX endpoint to fetch translation details for modal editor.
     */
    public function getSectionTranslation(Request $request)
    {
        $entityType = $request->query('entity_type');
        $entityId   = (int) $request->query('entity_id', 0);
        $fieldKey   = $request->query('field_key');
        $langId     = (int) $request->query('lang_id', 1);

        $enRow = EntitySectionTranslation::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('field_key', $fieldKey)
            ->where('lang_id', 1)
            ->first();

        $transRow = null;
        if ($langId > 1) {
            $transRow = EntitySectionTranslation::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->where('field_key', $fieldKey)
                ->where('lang_id', $langId)
                ->first();
        }

        return response()->json([
            'success'             => true,
            'entity_type'         => $entityType,
            'entity_id'           => $entityId,
            'field_key'           => $fieldKey,
            'lang_id'             => $langId,
            'source_headline'     => $enRow?->source_headline ?? '',
            'source_subtext'      => $enRow?->source_subtext ?? '',
            'source_hash'         => $enRow?->source_hash ?? '',
            'translated_headline' => $transRow?->translated_headline ?? '',
            'translated_subtext'  => $transRow?->translated_subtext ?? '',
            'status'              => $transRow?->status ?? 'missing',
        ]);
    }

    /**
     * AJAX endpoint to save an edited page section translation.
     */
    public function saveSectionTranslation(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|string',
            'entity_id'   => 'required|integer',
            'field_key'   => 'required|string',
            'lang_id'     => 'required|integer',
        ]);

        $entityType = $request->input('entity_type');
        $entityId   = (int) $request->input('entity_id', 0);
        $fieldKey   = $request->input('field_key');
        $langId     = (int) $request->input('lang_id');

        $enRow = EntitySectionTranslation::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('field_key', $fieldKey)
            ->where('lang_id', 1)
            ->first();

        $currentHash = $enRow?->source_hash ?? EntitySectionTranslation::computeHash(
            $request->input('source_headline'),
            $request->input('source_subtext')
        );

        $row = EntitySectionTranslation::updateOrCreate(
            [
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'field_key'   => $fieldKey,
                'lang_id'     => $langId,
            ],
            [
                'section_group'       => $enRow?->section_group ?? 'general',
                'source_headline'     => $enRow?->source_headline ?? $request->input('source_headline'),
                'source_subtext'      => $enRow?->source_subtext ?? $request->input('source_subtext'),
                'source_hash'         => $currentHash,
                'translated_headline' => $request->input('translated_headline'),
                'translated_subtext'  => $request->input('translated_subtext'),
                'status'              => EntitySectionTranslation::STATUS_COMPLETED,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Translation saved successfully!',
            'row'     => $row,
        ]);
    }

    /**
     * Export Missing / Outdated Page Section Strings for ChatGPT / Batch translation.
     */
    public function exportTemplatesJson(Request $request)
    {
        $targetLocale = $request->input('target_locale', 'de-DE');
        $tab = $request->input('tab', 'categories');
        $langCodeToIdMap = $this->getLangCodeToIdMap();
        $targetLangId = $langCodeToIdMap[strtolower($targetLocale)] ?? null;

        if (!$targetLangId) {
            return redirect()->back()->with('error', "Locale {$targetLocale} not supported.");
        }

        $enItems = EntitySectionTranslation::where('lang_id', 1)->get();
        $exportData = [];

        foreach ($enItems as $en) {
            $trans = EntitySectionTranslation::where('entity_type', $en->entity_type)
                ->where('entity_id', $en->entity_id)
                ->where('field_key', $en->field_key)
                ->where('lang_id', $targetLangId)
                ->first();

            $isMissingOrOutdated = (!$trans || $trans->status === EntitySectionTranslation::STATUS_OUTDATED || empty($trans->translated_headline));
            if ($isMissingOrOutdated) {
                $exportData[] = [
                    'custom_id'           => "sec_{$en->entity_type}_{$en->entity_id}_{$en->field_key}",
                    'entity_type'         => $en->entity_type,
                    'entity_id'           => $en->entity_id,
                    'field_key'           => $en->field_key,
                    'english_headline'    => $en->source_headline ?? '',
                    'english_subtext'     => $en->source_subtext ?? '',
                    'translated_headline' => '',
                    'translated_subtext'  => '',
                ];
            }
        }

        $payload = [
            'type'          => 'page_section_templates',
            'target_locale' => $targetLocale,
            'count'         => count($exportData),
            'instructions'  => "Translate each english_headline and english_subtext into natural {$targetLocale} with correct grammar and populate translated_headline and translated_subtext.",
            'strings'       => $exportData,
        ];

        $filename = "localio_page_sections_{$targetLocale}_" . date('Y-m-d_His') . ".json";
        return response()->json($payload, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Import Translated Page Section JSON.
     */
    public function importTemplatesJson(Request $request)
    {
        $request->validate([
            'json_file' => 'nullable|file|mimes:json,txt',
            'json_text' => 'nullable|string',
        ]);

        $rawJson = $request->input('json_text');
        if ($request->hasFile('json_file')) {
            $rawJson = file_get_contents($request->file('json_file')->getRealPath());
        }

        if (empty($rawJson)) {
            return redirect()->back()->with('error', 'Please provide a JSON file or paste JSON text.');
        }

        $data = json_decode($rawJson, true);
        if (!is_array($data)) {
            return redirect()->back()->with('error', 'Invalid JSON structure provided.');
        }

        $targetLocale = $data['target_locale'] ?? 'de-DE';
        $strings = $data['strings'] ?? $data;

        $langCodeToIdMap = $this->getLangCodeToIdMap();
        $targetLangId = $langCodeToIdMap[strtolower($targetLocale)] ?? 1;

        $updated = 0;
        foreach ($strings as $item) {
            $entityType = $item['entity_type'] ?? null;
            $entityId   = isset($item['entity_id']) ? (int)$item['entity_id'] : 0;
            $fieldKey   = $item['field_key'] ?? null;
            $transHead  = $item['translated_headline'] ?? ($item['headline'] ?? null);
            $transSub   = $item['translated_subtext'] ?? ($item['subtext'] ?? null);

            if ($entityType && $fieldKey && (!empty($transHead) || !empty($transSub))) {
                $enRow = EntitySectionTranslation::where('entity_type', $entityType)
                    ->where('entity_id', $entityId)
                    ->where('field_key', $fieldKey)
                    ->where('lang_id', 1)
                    ->first();

                $currentHash = $enRow?->source_hash ?? EntitySectionTranslation::computeHash($item['english_headline'] ?? '', $item['english_subtext'] ?? '');

                EntitySectionTranslation::updateOrCreate(
                    [
                        'entity_type' => $entityType,
                        'entity_id'   => $entityId,
                        'field_key'   => $fieldKey,
                        'lang_id'     => $targetLangId,
                    ],
                    [
                        'section_group'       => $enRow?->section_group ?? 'general',
                        'source_headline'     => $enRow?->source_headline ?? ($item['english_headline'] ?? ''),
                        'source_subtext'      => $enRow?->source_subtext ?? ($item['english_subtext'] ?? ''),
                        'source_hash'         => $currentHash,
                        'translated_headline' => $transHead,
                        'translated_subtext'  => $transSub,
                        'status'              => EntitySectionTranslation::STATUS_COMPLETED,
                    ]
                );
                $updated++;
            }
        }

        return redirect()->back()->with('success', "Successfully imported {$updated} translated page sections for locale {$targetLocale}.");
    }

    // =========================================================================
    // 5. BATCH JOBS (OPENAI BATCH & MANUAL JSON WORKFLOW)
    // =========================================================================
    public function batchJobs(Request $request)
    {
        $query = TranslationBatch::withCount('translations');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $batches = $query->orderBy('id', 'desc')->paginate(20);

        return view('Admin.translations.batch-jobs', compact('batches'));
    }

    /**
     * Download Batch JSON for ChatGPT execution.
     */
    public function downloadBatchJson(TranslationBatch $batch)
    {
        $payload = ReviewTranslationService::buildCleanBatchPayload($batch);
        $filename = "localio_translation_batch_{$batch->id}_{$batch->type}_" . date('Y-m-d_His') . ".json";

        return response()->json($payload, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Upload completed batch result JSON (manual JSON workflow).
     */
    public function uploadBatchResult(Request $request, ?TranslationBatch $batch = null)
    {
        $request->validate([
            'result_file' => 'nullable|file|mimes:json,txt',
            'json_file'   => 'nullable|file|mimes:json,txt',
            'result_text' => 'nullable|string',
            'json_text'   => 'nullable|string',
            'batch_id'    => 'nullable|exists:translation_batches,id',
        ]);

        $rawJson = $request->input('result_text') ?: $request->input('json_text');
        $uploadedFile = $request->file('result_file') ?: $request->file('json_file');
        if ($uploadedFile) {
            $rawJson = file_get_contents($uploadedFile->getRealPath());
        }

        if (empty($rawJson)) {
            return redirect()->back()->with('error', 'Please provide a result JSON file or paste JSON text.');
        }

        $data = json_decode($rawJson, true);
        if (!is_array($data)) {
            return redirect()->back()->with('error', 'Invalid JSON payload provided.');
        }

        $batchId = $batch?->id ?: ($request->input('batch_id') ?: ($data['batch_id'] ?? null));
        $targetBatch = $batchId ? TranslationBatch::find($batchId) : null;

        if (!$targetBatch) {
            $targetBatch = TranslationBatch::whereIn('status', ['waiting', 'submitted', 'in_progress'])
                ->orderByDesc('id')
                ->first();
        }

        if (!$targetBatch) {
            return redirect()->back()->with('error', 'Could not locate the associated translation batch for this result.');
        }

        $items = $data['translations'] ?? ($data['results'] ?? ($data['items'] ?? $data));
        if (!is_array($items)) {
            return redirect()->back()->with('error', 'Could not find translation items array in JSON payload.');
        }

        $res = ReviewTranslationService::processBatchResultItems($targetBatch, $items);

        return redirect()->route('translations.batch-jobs')
            ->with('success', "Batch #{$targetBatch->id} result processed: {$res['successful']} succeeded, {$res['failed']} failed.");
    }

    /**
     * Poll status now for active batches.
     */
    public function pollStatusNow()
    {
        $apiKey = config('openai.api_key') ?: optional(WebSetting::where('key', 'openai_api_key')->first())->value;
        if (!$apiKey) {
            return redirect()->back()->with('warning', 'OPENAI_API_KEY is not configured. Live polling skipped.');
        }

        $activeBatches = TranslationBatch::whereIn('status', ['submitted', 'in_progress'])
            ->whereNotNull('openai_batch_id')
            ->get();

        if ($activeBatches->isEmpty()) {
            return redirect()->back()->with('info', 'No active translation batch jobs to poll.');
        }

        $polled = 0;
        foreach ($activeBatches as $batch) {
            try {
                $resp = Http::withToken($apiKey)->timeout(15)->get("https://api.openai.com/v1/batches/{$batch->openai_batch_id}");
                if ($resp->successful()) {
                    $polled++;
                    $status = $resp->json('status');
                    $outputFileId = $resp->json('output_file_id');

                    if ($status === 'completed' && $outputFileId) {
                        $fileResp = Http::withToken($apiKey)->get("https://api.openai.com/v1/files/{$outputFileId}/content");
                        if ($fileResp->successful()) {
                            $lines = explode("\n", trim($fileResp->body()));
                            $results = [];
                            foreach ($lines as $line) {
                                $item = json_decode($line, true);
                                if (!$item) continue;
                                $customId = $item['custom_id'] ?? '';
                                $contentStr = $item['response']['body']['choices'][0]['message']['content'] ?? '';
                                $parsed = json_decode($contentStr, true);
                                $results[] = [
                                    'custom_id'        => $customId,
                                    'translated_title' => $parsed['translated_title'] ?? null,
                                    'translated_text'  => $parsed['translated_text'] ?? null,
                                ];
                            }
                            ReviewTranslationService::processBatchResultItems($batch, $results);
                        }
                    } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
                        $batch->update(['status' => 'failed', 'failed_at' => now()]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Poll error for batch #{$batch->id}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Polled {$polled} active batch job(s).");
    }

    /**
     * Submit batch to OpenAI API directly.
     */
    protected function submitBatchToOpenAI(TranslationBatch $batch, string $apiKey): bool
    {
        try {
            $payload = ReviewTranslationService::buildCleanBatchPayload($batch);
            $jsonlLines = [];

            foreach ($payload['requests'] as $req) {
                $messages = [
                    ['role' => 'system', 'content' => "You are a professional localizer. Translate the provided review title and text accurately from {$req['source_locale']} to {$req['target_locale']}. Respond ONLY in valid JSON: {\"translated_title\": \"...\", \"translated_text\": \"...\"}"],
                    ['role' => 'user', 'content' => "Review Title: {$req['review_title']}\n\nReview Text: {$req['review_text']}"],
                ];

                $jsonlLines[] = json_encode([
                    'custom_id' => $req['custom_id'],
                    'method'    => 'POST',
                    'url'       => '/v1/chat/completions',
                    'body'      => [
                        'model'       => $batch->model ?: 'gpt-5.6-luna',
                        'messages'    => $messages,
                        'temperature' => 0.3,
                    ]
                ]);
            }

            $jsonlContent = implode("\n", $jsonlLines);
            $tempFile = tempnam(sys_get_temp_dir(), 'batch_upload_') . '.jsonl';
            file_put_contents($tempFile, $jsonlContent);

            $fileResp = Http::withToken($apiKey)
                ->timeout(60)
                ->attach('file', fopen($tempFile, 'r'), 'batch_input.jsonl')
                ->post('https://api.openai.com/v1/files', ['purpose' => 'batch']);

            @unlink($tempFile);

            if (!$fileResp->successful()) {
                return false;
            }

            $fileId = $fileResp->json('id');
            $batchResp = Http::withToken($apiKey)->post('https://api.openai.com/v1/batches', [
                'input_file_id'     => $fileId,
                'endpoint'          => '/v1/chat/completions',
                'completion_window' => '24h',
                'metadata'          => [
                    'batch_id' => (string) $batch->id,
                    'type'     => 'review_translation',
                ]
            ]);

            if ($batchResp->successful()) {
                $batch->update([
                    'openai_batch_id' => $batchResp->json('id'),
                    'status'          => 'submitted',
                    'submitted_at'    => now(),
                ]);
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            Log::error("Failed to submit batch #{$batch->id} to OpenAI: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a batch job.
     */
    public function deleteBatchJob(TranslationBatch $batch)
    {
        ReviewTranslation::where('batch_id', $batch->id)
            ->where('status', ReviewTranslation::STATUS_BATCHED)
            ->update([
                'batch_id'         => null,
                'openai_custom_id' => null,
                'status'           => ReviewTranslation::STATUS_WAITING,
            ]);

        $batch->delete();

        return redirect()->back()->with('success', "Batch #{$batch->id} deleted successfully. Batched translations reset to WAITING.");
    }
}