<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiBatchJob;
use App\Models\AiContentQueue;
use App\Models\AiPrompt;
use App\Models\Business;
use App\Models\BusinessFaq;
use App\Models\BusinessFaqCategory;
use App\Models\BusinessFaqTranslation;
use App\Models\BusinessTranslation;
use App\Models\BusinessUsp;
use App\Models\Category;
use App\Models\Feature;
use App\Models\FeatureTransalte;
use App\Models\CategoryFeatureContent;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentGenerationController extends Controller
{
    const CONTENT_FIELDS = [
        'short_description',
        'long_description',
        'usps',
        'what_is',
        'reviews_description',
        'pros_cons',
        'features',
        'services',
        'faqs',
        'categories',
        'active_countries',
        'rating',
    ];

    const FEATURE_CONTENT_FIELDS = [
        'header',
    ];

    public static array $featureFieldLabels = [
        'header' => 'Header Text',
    ];


    // -------------------------------------------------------------------------
    // FEATURES LIST
    // -------------------------------------------------------------------------
    public function features(Request $request)
    {
        $query = Feature::with([
            'category.translation' => fn($q) => $q->where('lang_id', 1),
            'translations'         => fn($q) => $q->where('lang_id', 1),
        ]);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $statusVal = ($request->status === 'active' || $request->status === '1') ? 1 : 0;
            $query->where('status', $statusVal);
        }
        if ($request->filled('search')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('lang_id', 1)->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $features   = $query->orderBy('id', 'desc')->paginate(30)->withQueryString();
        $featureIds = $features->pluck('id')->toArray();

        $queueItems = AiContentQueue::where('resource_type', 'category_feature')
            ->whereIn('resource_id', $featureIds)
            ->get()
            ->groupBy('resource_id');

        $fieldStatusMap = [];
        foreach ($queueItems as $featureId => $items) {
            foreach ($items as $item) {
                if (!isset($fieldStatusMap[$featureId][$item->content_field]) ||
                    $item->updated_at > $fieldStatusMap[$featureId][$item->content_field]->updated_at) {
                    $fieldStatusMap[$featureId][$item->content_field] = $item;
                }
            }
        }

        $existingContents = CategoryFeatureContent::whereIn('feature_id', $featureIds)
            ->get()
            ->keyBy('feature_id');

        $lastPollAt = Cache::get('ai_batch_last_poll');
        $categories = Category::with(['translation' => fn($q) => $q->where('lang_id', 1)])
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get()
            ->map(function($cat) {
                $catName = $cat->translation?->name ?? ($cat->name ?? ('Category #' . $cat->id));
                $cat->display_name = ($cat->parent_id ? '— ' : '') . $catName;
                return $cat;
            });
        $fields     = self::FEATURE_CONTENT_FIELDS;

        $batchSize = (int) (WebSetting::where('key', 'ai_batch_size')->value('value') ?: 5);

        return view('Admin.content-generation.features', compact(
            'features', 'fieldStatusMap', 'existingContents', 'categories', 'fields', 'lastPollAt', 'batchSize'
        ));
    }

    // -------------------------------------------------------------------------
    // SUBMIT FEATURES BATCH
    // -------------------------------------------------------------------------
    public function submitFeaturesBatch(Request $request)
    {
        $request->validate([
            'feature_ids'   => 'required|array|min:1',
            'feature_ids.*' => 'integer|exists:features,id',
            'fields'        => 'required|array|min:1',
            'fields.*'      => 'string|in:header,top_rated,top_10,pros_cons,explore_comparisons,metatags',
            'provider'      => 'required|in:manual,openai,anthropic',
            'batch_size'    => 'nullable|integer|min:1|max:50',
        ]);

        $provider   = $request->provider;
        $featureIds = array_values(array_unique($request->feature_ids));
        $fields     = $request->fields;

        if ($request->filled('batch_size')) {
            WebSetting::updateOrCreate(['key' => 'ai_batch_size'], ['value' => (int)$request->batch_size]);
        }
        $batchSize = (int) ($request->batch_size ?: (WebSetting::where('key', 'ai_batch_size')->value('value') ?: 5));

        // ---------------------------------------------------------------------
        // MANUAL BATCH WORKFLOW FOR FEATURES (Download/Copy JSON for ChatGPT & Upload Results)
        // ---------------------------------------------------------------------
        if ($provider === 'manual') {
            $chunks = array_chunk($featureIds, $batchSize);
            $createdJobs = [];

            $allFields = self::FEATURE_CONTENT_FIELDS;
            $isAll = empty(array_diff($allFields, $fields));
            $fieldsValue = $isAll ? 'ALL' : array_values($fields);

            foreach ($chunks as $chunk) {
                $featuresData = [];
                $featureModels = Feature::with([
                    'translations'         => fn($q) => $q->where('lang_id', 1),
                    'category.translation' => fn($q) => $q->where('lang_id', 1),
                ])->whereIn('id', $chunk)->get();

                foreach ($chunk as $featId) {
                    $feat = $featureModels->firstWhere('id', $featId);
                    if (!$feat) continue;
                    $fName   = optional($feat->translations->first())->name ?? 'Feature #' . $featId;
                    $catName = optional($feat->category)->translation?->name ?? (optional($feat->category)->name ?? 'General');

                    $featuresData[] = [
                        'feature_id'       => $featId,
                        'feature_name'     => $fName,
                        'category_id'      => $feat->category_id,
                        'category_name'    => $catName,
                        'fields_requested' => $fieldsValue,
                    ];
                }

                $batchJobCode = 'BATCH-FEAT-MANUAL-' . strtoupper(Str::random(8));
                $inputPayload = [
                    'batch_id'       => $batchJobCode,
                    'batch_type'     => 'features',
                    'total_features' => count($featuresData),
                    'features'       => $featuresData,
                ];

                $batchJob = AiBatchJob::create([
                    'provider'      => 'manual',
                    'batch_id'      => $batchJobCode,
                    'prompt_type'   => 'feature:' . implode(',', $fields),
                    'status'        => 'pending',
                    'submitted_at'  => now(),
                    'total_items'   => count($featuresData) * count($fields),
                    'input_payload' => json_encode($inputPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ]);

                foreach ($featuresData as $fItem) {
                    foreach ($fields as $field) {
                        AiContentQueue::create([
                            'batch_job_id'      => $batchJob->id,
                            'resource_type'     => 'category_feature',
                            'resource_id'       => $fItem['feature_id'],
                            'content_field'     => $field,
                            'openai_request_id' => "feat_{$fItem['feature_id']}_{$field}",
                            'status'            => 'pending',
                        ]);
                    }
                }
                $createdJobs[] = $batchJob;
            }

            $countJobs = count($createdJobs);
            return redirect()->route('content-gen.batch-jobs')->with('success', "Created {$countJobs} manual batch job(s) ({$batchSize} features per batch). You can now copy the JSON and upload the results!");
        }

        $apiKey = $provider === 'openai'
            ? optional(WebSetting::where('key', 'openai_api_key')->first())->value
            : optional(WebSetting::where('key', 'anthropic_api_key')->first())->value;

        $model = $provider === 'openai'
            ? (optional(WebSetting::where('key', 'openai_default_model')->first())->value ?: 'gpt-4o')
            : (optional(WebSetting::where('key', 'anthropic_default_model')->first())->value ?: 'claude-opus-4-5');

        if (empty($apiKey)) {
            return back()->with('error', 'No API key configured for ' . ucfirst($provider) . '. Set it in AI Configuration.');
        }

        $requests  = [];
        $queueData = [];

        foreach ($featureIds as $featureId) {
            $feature = Feature::with([
                'translations'         => fn($q) => $q->where('lang_id', 1),
                'category.translation' => fn($q) => $q->where('lang_id', 1),
            ])->find($featureId);
            if (!$feature) continue;

            $featureName  = optional($feature->translations->first())->name ?? 'Feature #' . $featureId;
            $categoryName = optional($feature->category)->translation?->name ?? (optional($feature->category)->name ?? 'General');

            foreach ($fields as $field) {
                $prompt   = AiPrompt::forContentField("feat_{$field}");
                $customId = "feat_{$featureId}_{$field}";

                $inputVariables = [
                    'feature_name'  => $featureName,
                    'category_name' => $categoryName,
                ];

                if ($provider === 'openai') {
                    if ($prompt && $prompt->hasOpenAIPromptId()) {
                        $requests[] = [
                            'custom_id' => $customId,
                            'method'    => 'POST',
                            'url'       => '/v1/responses',
                            'body'      => [
                                'model'  => $model,
                                'prompt' => [
                                    'id'        => $prompt->openai_prompt_id,
                                    'variables' => $inputVariables,
                                ],
                            ],
                        ];
                    } else {
                        $promptText = $prompt
                            ? $prompt->getProcessedPrompt($inputVariables)
                            : $this->defaultFeaturePromptText($field, $featureName, $categoryName);

                        $requests[] = [
                            'custom_id' => $customId,
                            'method'    => 'POST',
                            'url'       => '/v1/chat/completions',
                            'body'      => [
                                'model'           => $model,
                                'messages'        => [
                                    ['role' => 'system', 'content' => 'You are a professional content writer for a business and software directory. Always respond with valid JSON only, no markdown, no explanation.'],
                                    ['role' => 'user',   'content' => $promptText],
                                ],
                                'response_format' => ['type' => 'json_object'],
                            ],
                        ];
                    }
                } else {
                    $promptText = $prompt
                        ? $prompt->getProcessedPrompt($inputVariables)
                        : $this->defaultFeaturePromptText($field, $featureName, $categoryName);

                    $requests[] = [
                        'custom_id' => $customId,
                        'params'    => [
                            'model'      => $model,
                            'max_tokens' => 2000,
                            'system'     => 'You are a professional content writer for a business and software directory. Always respond with valid JSON only, no markdown.',
                            'messages'   => [
                                ['role' => 'user', 'content' => $promptText],
                            ],
                        ],
                    ];
                }

                $queueData[] = [
                    'resource_type'     => 'category_feature',
                    'resource_id'       => $featureId,
                    'content_field'     => $field,
                    'openai_request_id' => $customId,
                    'status'            => 'processing',
                    'prompt_id'         => $prompt?->id,
                ];
            }
        }

        if (empty($requests)) {
            return back()->with('error', 'No valid requests could be built for selected features.');
        }

        try {
            $batchId = $provider === 'openai'
                ? $this->submitOpenAIBatch($apiKey, $requests)
                : $this->submitAnthropicBatch($apiKey, $requests);

            $batchJob = AiBatchJob::create([
                'provider'     => $provider,
                'batch_id'     => $batchId,
                'prompt_type'  => 'feature:' . implode(',', $fields),
                'status'       => 'processing',
                'submitted_at' => now(),
                'total_items'  => count($requests),
            ]);

            foreach ($queueData as $item) {
                $item['batch_job_id'] = $batchJob->id;
                $item['created_at']   = now();
                $item['updated_at']   = now();
                AiContentQueue::create($item);
            }

            return back()->with('success', "Batch submitted for {$batchJob->total_items} feature field(s). Provider: {$provider}. Batch ID: {$batchId}");
        } catch (\Exception $e) {
            Log::error("Feature batch dispatch failed: " . $e->getMessage());
            return back()->with('error', 'Batch submission failed: ' . $e->getMessage());
        }
    }

    public function retryFeatureField(Request $request)
    {
        $request->validate([
            'feature_id' => 'required|integer|exists:features,id',
            'field'      => 'required|string|in:header,top_rated,top_10,pros_cons,explore_comparisons,metatags',
            'provider'   => 'required|in:manual,openai,anthropic',
        ]);

        return $this->submitFeaturesBatch(new Request([
            'feature_ids' => [$request->feature_id],
            'fields'      => [$request->field],
            'provider'    => $request->provider,
        ]));
    }

    public function applyAllFeatureCompleted(Request $request)
    {
        $request->validate(['feature_id' => 'required|integer|exists:features,id']);

        $items = AiContentQueue::where('resource_type', 'category_feature')
            ->where('resource_id', $request->feature_id)
            ->where('status', 'completed')
            ->whereNotNull('result')
            ->get();

        $applied = 0;
        $errors  = [];

        foreach ($items as $item) {
            $data = json_decode($item->result, true);
            if (!is_array($data)) {
                $errors[] = "Field {$item->content_field}: invalid JSON";
                continue;
            }
            try {
                $this->applyResultToRecord($item, $data);
                $applied++;
            } catch (\Exception $e) {
                $errors[] = "Field {$item->content_field}: " . $e->getMessage();
            }
        }

        $total = $items->count();
        $msg   = "Applied {$applied} of {$total} fields to the category feature content.";
        if (!empty($errors)) {
            return back()->with('warning', $msg . ' Errors: ' . implode('; ', $errors));
        }
        return back()->with('success', $msg);
    }

    // -------------------------------------------------------------------------
    // BUSINESSES LIST
    // -------------------------------------------------------------------------
    /**
     * Import businesses via JSON payload (Business name, Business URL).
     */
    public function importJson(Request $request)
    {
        $rawJson = null;
        if ($request->hasFile('json_file')) {
            $rawJson = file_get_contents($request->file('json_file')->getRealPath());
        } elseif ($request->filled('json_data')) {
            $rawJson = trim($request->json_data);
        }

        if (empty($rawJson)) {
            return back()->with('error', 'Please provide JSON data by pasting it or by uploading a .json file.');
        }

        // Clean any markdown code blocks if user pasted ```json ... ```
        $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $rawJson);
        $cleanJson = preg_replace('/\s*```$/', '', $cleanJson);
        $cleanJson = trim($cleanJson);

        $decoded = json_decode($cleanJson, true);
        if (!is_array($decoded)) {
            // Attempt auto-fix for missing commas between objects (e.g. "}\n{") and trailing commas
            $fixedJson = preg_replace('/\}\s*\{/', '},{', $cleanJson);
            $fixedJson = preg_replace('/,\s*([\]\}])/', '$1', $fixedJson);
            $decoded   = json_decode($fixedJson, true);
        }

        if (!is_array($decoded)) {
            // Attempt line-by-line JSONL parse
            $lines = preg_split('/\r\n|\r|\n/', $cleanJson);
            $jsonlItems = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $line = rtrim($line, ',');
                $lineDecoded = json_decode($line, true);
                if (is_array($lineDecoded)) {
                    $jsonlItems[] = $lineDecoded;
                }
            }
            if (!empty($jsonlItems)) {
                $decoded = $jsonlItems;
            } else {
                return back()->with('error', 'Invalid JSON syntax: ' . json_last_error_msg());
            }
        }

        // If wrapped in {"businesses": [...]}, {"data": [...]}, {"items": [...]}, or {"results": [...]}
        if (isset($decoded['businesses']) && is_array($decoded['businesses'])) {
            $decoded = $decoded['businesses'];
        } elseif (isset($decoded['data']) && is_array($decoded['data'])) {
            $decoded = $decoded['data'];
        } elseif (isset($decoded['items']) && is_array($decoded['items'])) {
            $decoded = $decoded['items'];
        } elseif (isset($decoded['results']) && is_array($decoded['results'])) {
            $decoded = $decoded['results'];
        } elseif (!empty($decoded) && !isset($decoded[0])) {
            $decoded = [$decoded];
        }

        $updateExisting = $request->boolean('update_existing', true);
        $createdCount   = 0;
        $updatedCount   = 0;
        $skippedCount   = 0;
        $errors         = [];

        DB::beginTransaction();
        try {
            foreach ($decoded as $index => $item) {
                if (!is_array($item)) {
                    $skippedCount++;
                    continue;
                }

                // Flexible name and URL resolution
                $name = trim($item['name'] ?? $item['business_name'] ?? $item['Business name'] ?? $item['businessName'] ?? '');
                $url  = trim($item['url'] ?? $item['business_url'] ?? $item['Business URL'] ?? $item['businessUrl'] ?? $item['website'] ?? $item['website_url'] ?? '');

                if (empty($name)) {
                    $skippedCount++;
                    $errors[] = "Item #" . ($index + 1) . " skipped: missing Business Name.";
                    continue;
                }

                if (!empty($url) && !preg_match('/^https?:\/\//i', $url)) {
                    $url = 'https://' . $url;
                }

                // Check for existing business by normalized URL or by Name
                $existingBiz = null;
                if (!empty($url)) {
                    $normalizedUrl = rtrim(preg_replace('/^https?:\/\/(www\.)?/i', '', $url), '/');
                    if (!empty($normalizedUrl)) {
                        $existingBiz = Business::whereRaw("TRIM(TRAILING '/' FROM REPLACE(REPLACE(permanent_url, 'https://', ''), 'http://', '')) LIKE ?", ['%' . $normalizedUrl . '%'])
                            ->first();
                    }
                }

                if (!$existingBiz && !empty($name)) {
                    $bizId = BusinessTranslation::whereRaw('LOWER(name) = ?', [strtolower($name)])
                        ->where('lang_id', 1)
                        ->value('business_id');
                    if ($bizId) {
                        $existingBiz = Business::find($bizId);
                    }
                }

                $business = null;

                if ($existingBiz) {
                    if (!$updateExisting) {
                        $skippedCount++;
                        continue;
                    }
                    $business = $existingBiz;
                    if (!empty($url) && empty($business->permanent_url)) {
                        $business->permanent_url = $url;
                        $business->save();
                    }
                    $updatedCount++;
                } else {
                    $baseSlug = Str::slug($name);
                    if (empty($baseSlug)) {
                        $baseSlug = 'business-' . time() . '-' . rand(100, 999);
                    }
                    $slug = $baseSlug;
                    $counter = 1;
                    while (BusinessTranslation::where('slug', $slug)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }

                    $affLinkInitial = trim($item['affiliate_link'] ?? ($item['affiliate_url'] ?? ''));
                    $isAffInitial = (!empty($affLinkInitial) || !empty($item['is_affiliate'])) ? 1 : 0;

                    $business = Business::create([
                        'permanent_url'        => $url ?: null,
                        'affiliate_link'       => $affLinkInitial ?: null,
                        'status'               => 1,
                        'created_by'           => auth()->id() ?? 1,
                        'is_affiliate'         => $isAffInitial,
                        'active_all_countries' => 1,
                        'category_id'          => null,
                    ]);

                    BusinessTranslation::create([
                        'business_id' => $business->id,
                        'lang_id'     => 1,
                        'name'        => $name,
                        'slug'        => $slug,
                        'status'      => 1,
                    ]);

                    $business->languages()->syncWithoutDetaching([1]);
                    $createdCount++;
                }

                // Ingest all other fields (category, descriptions, rating, usps, pros_cons, faqs, services)
                $this->applyBusinessFields($business, $item);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to import businesses via JSON: " . $e->getMessage());
            return back()->with('error', 'Error during JSON import: ' . $e->getMessage());
        }

        $msgParts = [];
        if ($createdCount > 0) $msgParts[] = "{$createdCount} new business(es) created";
        if ($updatedCount > 0) $msgParts[] = "{$updatedCount} existing business(es) updated";
        if ($skippedCount > 0) $msgParts[] = "{$skippedCount} skipped";

        $finalMsg = !empty($msgParts) ? "Import complete: " . implode(', ', $msgParts) . "." : "No businesses were imported.";

        return redirect()->route('content-gen.businesses')->with('success', $finalMsg);
    }

    /**
     * Helper to ingest and apply all structured fields for a business.
     */
    public function applyBusinessFields(Business $business, array $item): int
    {
        $businessId = $business->id;
        $appliedCount = 0;

        // 1. Categories
        $catData = $item['categories'] ?? null;
        if (!$catData && (isset($item['category_id']) || isset($item['category']) || isset($item['subcategory_ids']) || isset($item['subcategories']) || isset($item['primary_category_id']) || isset($item['primary_subcategory_id']))) {
            $catData = [
                'primary_category_id'    => $item['primary_category_id'] ?? null,
                'primary_subcategory_id' => $item['primary_subcategory_id'] ?? null,
                'category_id'            => $item['category_id'] ?? null,
                'category_name'          => $item['category_name'] ?? ($item['category'] ?? null),
                'category_ids'           => $item['category_ids'] ?? [],
                'subcategory_ids'        => $item['subcategory_ids'] ?? ($item['subcategories'] ?? []),
            ];
        }
        if ($catData) {
            self::applyCategoriesToBusiness($business, $catData);
            $appliedCount++;
        }

        // 2. Short description
        $shortDesc = $item['short_description'] ?? null;
        if (!empty($shortDesc)) {
            BusinessTranslation::where('business_id', $businessId)->where('lang_id', 1)->update(['short_description' => $shortDesc]);
            $appliedCount++;
        }

        // 3. Long description
        $longDesc = $item['long_description'] ?? ($item['long_preview_description'] ?? null);
        if (!empty($longDesc)) {
            BusinessTranslation::where('business_id', $businessId)->where('lang_id', 1)->update(['long_preview_description' => $longDesc]);
            $appliedCount++;
        }

        // 4. What is / Description
        $whatIs = $item['what_is'] ?? ($item['description'] ?? null);
        if (!empty($whatIs)) {
            BusinessTranslation::where('business_id', $businessId)->where('lang_id', 1)->update(['description' => $whatIs]);
            $appliedCount++;
        }

        // 5. Reviews description
        $revDesc = $item['reviews_description'] ?? ($item['short_business_description_reviews'] ?? null);
        if (!empty($revDesc)) {
            BusinessTranslation::where('business_id', $businessId)->where('lang_id', 1)->update(['reviews_description' => $revDesc]);
            $appliedCount++;
        }

        // 6. Rating
        $rating = $item['rating'] ?? ($item['admin_rating'] ?? null);
        if (is_numeric($rating)) {
            $rVal = max(1.0, min(5.0, round((float) $rating, 2)));
            $business->admin_rating = $rVal;
            $business->save();
            $appliedCount++;
        }

        // 7. USPs
        $usps = $item['usps'] ?? [];
        if (!empty($usps) && is_array($usps)) {
            BusinessUsp::where('business_id', $businessId)->delete();
            foreach ($usps as $idx => $u) {
                $text = is_array($u) ? ($u['text'] ?? ($u['title'] ?? '')) : (string) $u;
                if (empty(trim($text))) continue;
                BusinessUsp::create([
                    'business_id' => $businessId,
                    'text'        => trim($text),
                    'sort_order'  => $idx + 1,
                ]);
            }
            $appliedCount++;
        }

        // 7b. Features
        $featData = $item['feature_ids'] ?? ($item['features'] ?? null);
        if (!empty($featData)) {
            $featCount = self::applyFeaturesToBusiness($business, $featData);
            if ($featCount > 0) $appliedCount++;
        }

        // 8. Pros & Cons
        $proConData = $item['pros_cons'] ?? null;
        if (!$proConData && (isset($item['pro_ids']) || isset($item['con_ids']))) {
            $proConData = [
                'pro_ids' => $item['pro_ids'] ?? [],
                'con_ids' => $item['con_ids'] ?? [],
            ];
        }
        if (isset($proConData) && is_array($proConData)) {
            $proConCount = self::applyProsConsToBusiness($business, $proConData);
            if ($proConCount > 0) $appliedCount++;
        }

        // 9. FAQs
        $faqs = $item['faqs'] ?? [];
        if (!empty($faqs) && is_array($faqs)) {
            BusinessFaq::where('business_id', $businessId)->delete();
            foreach ($faqs as $fIndex => $faq) {
                $q = $faq['question'] ?? ($faq['q'] ?? null);
                $a = $faq['answer'] ?? ($faq['a'] ?? null);
                if (!$q || !$a) continue;

                $catName = trim($faq['category'] ?? ($faq['category_name'] ?? ($faq['cat'] ?? '')));
                $faqCatId = null;
                if (!empty($catName)) {
                    $catObj = BusinessFaqCategory::where('business_id', $businessId)
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($catName)])
                        ->first();
                    if (!$catObj) {
                        $nextPos = (BusinessFaqCategory::where('business_id', $businessId)->max('position') ?? 0) + 1;
                        $catObj = BusinessFaqCategory::create([
                            'business_id' => $businessId,
                            'name'        => $catName,
                            'position'    => $nextPos,
                            'status'      => 1,
                        ]);
                    }
                    $faqCatId = $catObj->id;
                }

                $bizFaq = BusinessFaq::create([
                    'business_id'              => $businessId,
                    'business_faq_category_id' => $faqCatId,
                    'position'                 => $fIndex + 1,
                    'status'                   => true,
                ]);
                BusinessFaqTranslation::create([
                    'business_faq_id' => $bizFaq->id,
                    'lang_id'         => 1,
                    'question'        => $q,
                    'answer'          => $a,
                ]);
            }
            $appliedCount++;
        }

        // 10. Services / Offering (headline is optional as templates provide it)
        if (isset($item['services']) && is_array($item['services'])) {
            $headline = $item['services']['headline'] ?? null;
            $topText = $item['services']['top_text'] ?? null;
            $bottomText = $item['services']['bottom_text'] ?? null;
            $offering = \App\Models\BusinessOffering::firstOrNew(['business_id' => $businessId]);
            if ($headline !== null && trim($headline) !== '') $offering->headline = $headline;
            if ($topText !== null) $offering->top_text = $topText;
            if ($bottomText !== null) $offering->bottom_text = $bottomText;
            $offering->save();
            if ($bottomText !== null) {
                BusinessTranslation::where('business_id', $businessId)->where('lang_id', 1)->update(['after_image_description' => $bottomText]);
            }
            $appliedCount++;
        }

        // 11. Active countries (support country_ids as primary, fallback to country_codes)
        if (isset($item['active_countries']) && is_array($item['active_countries'])) {
            $activeAll = !empty($item['active_countries']['active_all_countries']);
            $business->active_all_countries = $activeAll ? 1 : 0;
            $business->save();
            if (!$activeAll) {
                $countryIds = [];
                if (!empty($item['active_countries']['country_ids']) && is_array($item['active_countries']['country_ids'])) {
                    $countryIds = array_values(array_filter(array_map('intval', $item['active_countries']['country_ids'])));
                } elseif (!empty($item['active_countries']['country_codes']) && is_array($item['active_countries']['country_codes'])) {
                    $countryIds = \App\Models\Country::whereIn('country_code', $item['active_countries']['country_codes'])->pluck('id')->toArray();
                }
                if (!empty($countryIds)) {
                    $business->countries()->sync($countryIds);
                }
            }
            $appliedCount++;
        }

        // 12. Affiliate & Website URLs
        $affLink = trim($item['affiliate_link'] ?? ($item['affiliate_url'] ?? ($item['affiliate'] ?? '')));
        $isAffVal = isset($item['is_affiliate']) ? (bool)$item['is_affiliate'] : null;

        if (!empty($affLink)) {
            $business->affiliate_link = $affLink;
            $business->is_affiliate = 1;
            $business->save();
            $appliedCount++;
        } elseif ($isAffVal !== null) {
            $business->is_affiliate = $isAffVal ? 1 : 0;
            $business->save();
            $appliedCount++;
        }

        $websitesList = $item['websites'] ?? ($item['affiliate_urls'] ?? ($item['country_websites'] ?? null));
        if (!empty($websitesList) && is_array($websitesList)) {
            $hasAnyAffiliate = false;
            foreach ($websitesList as $wEntry) {
                if (!is_array($wEntry)) continue;
                $cId = $wEntry['country_id'] ?? null;
                if (!$cId && !empty($wEntry['country_code'])) {
                    $cId = \App\Models\Country::where('country_code', strtoupper($wEntry['country_code']))->value('id');
                }
                $wUrl = trim($wEntry['url'] ?? ($wEntry['website_url'] ?? ($wEntry['affiliate_url'] ?? '')));
                $wIsAff = isset($wEntry['is_affiliate']) ? (bool)$wEntry['is_affiliate'] : true;
                if ($cId && !empty($wUrl)) {
                    \App\Models\BusinessWebsite::updateOrCreate(
                        ['business_id' => $businessId, 'country_id' => $cId],
                        ['website_url' => $wUrl, 'is_affiliate' => $wIsAff ? 1 : 0, 'status' => 1]
                    );
                    if ($wIsAff) $hasAnyAffiliate = true;
                }
            }
            if ($hasAnyAffiliate) {
                $business->is_affiliate = 1;
                $business->save();
            }
            $appliedCount++;
        }

        return $appliedCount;
    }

    public function businesses(Request $request)
    {
        $query = Business::with([
            'category',
            'translations' => function ($q) {
                $q->where('lang_id', 1)->select('business_id', 'name', 'lang_id');
            },
        ]);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('lang_id', 1)->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $businesses = $query->orderBy('id', 'desc')->paginate(30)->withQueryString();

        $businessIds = $businesses->pluck('id')->toArray();
        $queueItems  = AiContentQueue::where('resource_type', 'business')
            ->whereIn('resource_id', $businessIds)
            ->get()
            ->groupBy('resource_id');

        $fieldStatusMap = [];
        foreach ($queueItems as $businessId => $items) {
            foreach ($items as $item) {
                if (!isset($fieldStatusMap[$businessId][$item->content_field]) ||
                    $item->updated_at > $fieldStatusMap[$businessId][$item->content_field]->updated_at) {
                    $fieldStatusMap[$businessId][$item->content_field] = $item;
                }
            }
        }

        $configuredPrompts = [];
        foreach (self::CONTENT_FIELDS as $field) {
            $configuredPrompts[$field] = AiPrompt::forContentField($field);
        }

        $lastPollAt = Cache::get('ai_batch_last_poll');
        $categories = Category::whereNull('parent_id')->orderBy('id')->get();
        $fields     = self::CONTENT_FIELDS;

        $batchSize = (int)(WebSetting::where('key', 'ai_batch_size')->value('value') ?: 5);

        return view('Admin.content-generation.businesses', compact(
            'businesses', 'fieldStatusMap', 'categories', 'fields', 'configuredPrompts', 'lastPollAt', 'batchSize'
        ));
    }

    // -------------------------------------------------------------------------
    // SUBMIT BATCH
    // -------------------------------------------------------------------------
    public function submitBatch(Request $request)
    {
        $validFields = array_merge(self::CONTENT_FIELDS, ['content', 'features', 'metatags']);
        $request->validate([
            'business_ids'   => 'required|array|min:1',
            'business_ids.*' => 'integer|exists:businesses,id',
            'fields'         => 'required|array|min:1',
            'fields.*'       => 'string|in:' . implode(',', $validFields),
            'provider'       => 'required|in:manual,openai,anthropic',
            'batch_size'     => 'nullable|integer|min:1|max:50',
        ]);

        $provider    = $request->provider;
        $businessIds = array_values(array_unique($request->business_ids));
        $fields      = $request->fields;

        if ($request->filled('batch_size')) {
            WebSetting::updateOrCreate(['key' => 'ai_batch_size'], ['value' => (int)$request->batch_size]);
        }
        $batchSize = (int) ($request->batch_size ?: (WebSetting::where('key', 'ai_batch_size')->value('value') ?: 5));

        // ---------------------------------------------------------------------
        // MANUAL BATCH WORKFLOW (Download JSON for ChatGPT & Upload Results)
        // ---------------------------------------------------------------------
        if ($provider === 'manual') {
            $chunks = array_chunk($businessIds, $batchSize);
            $createdJobs = [];

            $allFields = self::CONTENT_FIELDS;
            $isAll = empty(array_diff($allFields, $fields));
            $fieldsValue = $isAll ? 'ALL' : array_values($fields);

            foreach ($chunks as $chunk) {
                $businessesData = [];
                $businessModels = Business::with([
                    'translations' => fn($q) => $q->where('lang_id', 1),
                    'websites',
                ])->whereIn('id', $chunk)->get();

                foreach ($chunk as $bizId) {
                    $b = $businessModels->firstWhere('id', $bizId);
                    if (!$b) continue;
                    $bName = optional($b->translations->first())->name ?? $b->name ?? 'Business #' . $bizId;
                    // Official business URL only — never affiliate link!
                    $bUrl  = $b->permanent_url ?: (optional($b->websites->first())->website_url ?: '');
                    $businessesData[] = [
                        'business_id'   => $bizId,
                        'business_name' => $bName,
                        'business_url'  => $bUrl,
                    ];
                }

                $batchJobCode = 'BATCH-MANUAL-' . strtoupper(Str::random(8));
                $inputPayload = [
                    'batch_id'         => $batchJobCode,
                    'total_businesses' => count($businessesData),
                    'businesses'       => $businessesData,
                ];

                $batchJob = AiBatchJob::create([
                    'provider'      => 'manual',
                    'batch_id'      => $batchJobCode,
                    'prompt_type'   => implode(',', $fields),
                    'status'        => 'pending',
                    'submitted_at'  => now(),
                    'total_items'   => count($businessesData) * count($fields),
                    'input_payload' => json_encode($inputPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                ]);

                foreach ($businessesData as $bItem) {
                    foreach ($fields as $field) {
                        AiContentQueue::create([
                            'batch_job_id'      => $batchJob->id,
                            'resource_type'     => 'business',
                            'resource_id'       => $bItem['business_id'],
                            'content_field'     => $field,
                            'openai_request_id' => "biz_{$bItem['business_id']}_{$field}",
                            'status'            => 'pending',
                        ]);
                    }
                }
                $createdJobs[] = $batchJob;
            }

            $countJobs = count($createdJobs);
            return redirect()->route('content-gen.batch-jobs')->with('success', "Created {$countJobs} manual batch job(s) ({$batchSize} businesses per batch). You can now download the JSONs and upload the results!");
        }

        $apiKey = $provider === 'openai'
            ? optional(WebSetting::where('key', 'openai_api_key')->first())->value
            : optional(WebSetting::where('key', 'anthropic_api_key')->first())->value;

        $model = $provider === 'openai'
            ? (optional(WebSetting::where('key', 'openai_default_model')->first())->value ?: 'gpt-4o')
            : (optional(WebSetting::where('key', 'anthropic_default_model')->first())->value ?: 'claude-opus-4-5');

        if (empty($apiKey)) {
            return back()->with('error', 'No API key configured for ' . ucfirst($provider) . '. Set it in AI Configuration.');
        }

        $requests  = [];
        $queueData = [];

        foreach ($businessIds as $businessId) {
            // GAP 11: load subCategories for variable injection
            $business = Business::with([
                'translations' => fn($q) => $q->where('lang_id', 1),
                'category',
                'subCategories',
            ])->find($businessId);
            if (!$business) continue;

            $businessName    = optional($business->translations->first())->name ?? 'Business #' . $businessId;
            $businessUrl     = $business->permanent_url ?: ($business->affiliate_link ?: '');
            $categoryName    = optional(optional($business->category)->translations->firstWhere('lang_id', 1))->name ?? optional($business->category)->name ?? '';
            $subcategoryName = $business->subCategories->pluck('name')->filter()->implode(', ');

            // Contextual pros/cons list for pros_cons field (includes IDs for strict selection)
            $availableProsStr = "- [ID: 1] High reliability\n- [ID: 2] Good value for money\n- [ID: 3] Affordable pricing\n- [ID: 4] Easy to use interface\n- [ID: 5] 24/7 expert customer support\n- [ID: 6] Fast performance\n- [ID: 7] Comprehensive features";
            $availableConsStr = "- [ID: 8] Steep learning curve\n- [ID: 9] Limited advanced features\n- [ID: 10] Hidden additional fees\n- [ID: 11] Unclear pricing\n- [ID: 12] Occasional support delays";
            $allBusinessCatIds = array_values(array_unique(array_filter(array_merge(
                [$business->category_id],
                $business->subCategories ? $business->subCategories->pluck('id')->toArray() : []
            ))));
            if (!empty($allBusinessCatIds)) {
                $catProCons = \App\Models\CategoryProCon::whereIn('category_id', $allBusinessCatIds)->where('status', 1)->get();
                if ($catProCons->isEmpty()) {
                    $catProCons = \App\Models\CategoryProCon::where('status', 1)->take(30)->get();
                }
                $prosList = $catProCons->where('type', 'pro')->map(fn($p) => "[ID: {$p->id}] {$p->text}")->filter()->values()->toArray();
                $consList = $catProCons->where('type', 'con')->map(fn($c) => "[ID: {$c->id}] {$c->text}")->filter()->values()->toArray();
                if (!empty($prosList)) $availableProsStr = "- " . implode("\n- ", $prosList);
                if (!empty($consList)) $availableConsStr = "- " . implode("\n- ", $consList);
            }

            // Category list for categories field
            $allCategoriesList = Category::whereNull('parent_id')
                ->where('status', 1)
                ->with(['children' => fn($q) => $q->where('status', 1)])
                ->take(20)
                ->get()
                ->map(function($cat) {
                    $subs = $cat->children->map(fn($c) => "[ID: {$c->id}] {$c->name}")->implode(', ');
                    return "[ID: {$cat->id}] {$cat->name} (Subcategories: " . ($subs ?: 'None') . ")";
                })
                ->implode("\n");

            $extra = [
                'available_pros' => $availableProsStr,
                'available_cons' => $availableConsStr,
                'category_list'  => $allCategoriesList,
            ];

            foreach ($fields as $field) {
                $prompt   = AiPrompt::forContentField($field);
                $customId = "biz_{$businessId}_{$field}";

                $inputVariables = [
                    'business_name'    => $businessName,
                    'business_url'     => $businessUrl,
                    'category_name'    => $categoryName,
                    'subcategory_name' => $subcategoryName,
                    'available_pros'   => $availableProsStr,
                    'available_cons'   => $availableConsStr,
                    'category_list'    => $allCategoriesList,
                ];

                if ($provider === 'openai') {
                    if ($prompt && $prompt->hasOpenAIPromptId()) {
                        $requests[] = [
                            'custom_id' => $customId,
                            'method'    => 'POST',
                            'url'       => '/v1/responses',
                            'body'      => [
                                'model'  => $model,
                                'prompt' => [
                                    'id'        => $prompt->openai_prompt_id,
                                    'variables' => $inputVariables,
                                ],
                            ],
                        ];
                    } else {
                        $promptText = $prompt
                            ? $prompt->getProcessedPrompt($inputVariables)
                            : $this->defaultPromptText($field, $businessName, $categoryName, $businessUrl, $extra);

                        $requests[] = [
                            'custom_id' => $customId,
                            'method'    => 'POST',
                            'url'       => '/v1/chat/completions',
                            'body'      => [
                                'model'           => $model,
                                'messages'        => [
                                    ['role' => 'system', 'content' => 'You are a professional content writer for a business directory. Always respond with valid JSON only, no markdown, no explanation.'],
                                    ['role' => 'user',   'content' => $promptText],
                                ],
                                'max_tokens'      => 2000,
                                'temperature'     => 0.7,
                                'response_format' => ['type' => 'json_object'],
                            ],
                        ];
                    }
                } else {
                    // GAP 3: Anthropic has no saved prompt ID API — always expand full prompt text
                    $promptText = $prompt
                        ? $prompt->getProcessedPrompt($inputVariables)
                        : $this->defaultPromptText($field, $businessName, $categoryName);

                    $requests[] = [
                        'custom_id' => $customId,
                        'params'    => [
                            'model'      => $model,
                            'max_tokens' => 2000,
                            'system'     => 'You are a professional content writer for a business directory. Always respond with valid JSON only, no markdown.',
                            'messages'   => [
                                ['role' => 'user', 'content' => $promptText],
                            ],
                        ],
                    ];
                }

                $queueData[] = [
                    'resource_type'     => 'business',
                    'resource_id'       => $businessId,
                    'content_field'     => $field,
                    'openai_request_id' => $customId,
                    'status'            => 'processing',
                    'prompt_id'         => $prompt?->id,
                ];
            }
        }

        if (empty($requests)) {
            return back()->with('error', 'No valid requests could be built. Check prompt configuration.');
        }

        try {
            $batchId = $provider === 'openai'
                ? $this->submitOpenAIBatch($apiKey, $requests)
                : $this->submitAnthropicBatch($apiKey, $requests);

            // GAP 4: Populate prompt_type with comma-joined field list
            $batchJob = AiBatchJob::create([
                'provider'     => $provider,
                'batch_id'     => $batchId,
                'prompt_type'  => implode(',', $fields),
                'status'       => 'processing',
                'submitted_at' => now(),
                'total_items'  => count($requests),
            ]);

            foreach ($queueData as $item) {
                AiContentQueue::create(array_merge($item, ['batch_job_id' => $batchJob->id]));
            }

            $batchUrl = route('content-gen.batch-jobs');
            return back()->with('success', "Batch submitted! ID: <code>{$batchId}</code>. Results will be processed automatically every 30 minutes. <a href='{$batchUrl}'>View batch jobs</a>");

        } catch (\Exception $e) {
            Log::error('Batch submission failed: ' . $e->getMessage());
            return back()->with('error', 'Batch submission failed: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // RETRY / REGENERATE (GAP 8: works for both failed AND completed items)
    // -------------------------------------------------------------------------
    public function retryFailed(Request $request)
    {
        $validFields = array_merge(self::CONTENT_FIELDS, ['content', 'features', 'metatags']);
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'field'       => 'required|string|in:' . implode(',', $validFields),
            'provider'    => 'required|in:manual,openai,anthropic',
        ]);

        // GAP 8: Allow re-generation of completed items too
        AiContentQueue::where('resource_type', 'business')
            ->where('resource_id', $request->business_id)
            ->where('content_field', $request->field)
            ->whereIn('status', ['failed', 'completed'])
            ->delete();

        return $this->submitBatch(new Request([
            'business_ids' => [$request->business_id],
            'fields'       => [$request->field],
            'provider'     => $request->provider,
        ]));
    }

    // -------------------------------------------------------------------------
    // BATCH JOBS LIST
    // -------------------------------------------------------------------------
    public function batchJobs()
    {
        $jobs                      = AiBatchJob::withCount('queueItems')->orderBy('id', 'desc')->paginate(20);
        $lastPollAt                = Cache::get('ai_batch_last_poll');
        $batchSize                 = (int)(WebSetting::where('key', 'ai_batch_size')->value('value') ?: 5);
        $batchResultExample        = self::getBatchResultExampleJson();
        $featureBatchResultExample = self::getFeatureBatchResultExampleJson();

        return view('Admin.content-generation.batch-jobs', compact('jobs', 'lastPollAt', 'batchSize', 'batchResultExample', 'featureBatchResultExample'));
    }

    // -------------------------------------------------------------------------
    // GAP 5: BATCH JOB ITEMS DRILL-DOWN
    // -------------------------------------------------------------------------
    public function batchJobItems(AiBatchJob $job)
    {
        $items = $job->queueItems()
            ->with(['prompt'])
            ->orderBy('resource_id')
            ->orderBy('content_field')
            ->paginate(50);

        $isFeatureJob = $job->queueItems()->where('resource_type', 'category_feature')->exists()
            || str_starts_with($job->prompt_type ?? '', 'feature:');

        $businessNames = [];
        if ($isFeatureJob) {
            $featureIds    = $items->pluck('resource_id')->unique()->toArray();
            $businessNames = FeatureTransalte::whereIn('feature_id', $featureIds)
                ->where('lang_id', 1)
                ->pluck('name', 'feature_id');
        } else {
            $businessIds   = $items->pluck('resource_id')->unique()->toArray();
            $businessNames = BusinessTranslation::whereIn('business_id', $businessIds)
                ->where('lang_id', 1)
                ->pluck('name', 'business_id');
        }

        return view('Admin.content-generation.batch-job-items', compact('job', 'items', 'businessNames', 'isFeatureJob'));
    }

    public function deleteBatchJob(AiBatchJob $job)
    {
        $batchId = $job->batch_id ?: ('#' . $job->id);
        $job->queueItems()->delete();
        $job->delete();

        return back()->with('success', "Batch job {$batchId} and all associated queue items have been deleted successfully.");
    }

    // -------------------------------------------------------------------------
    // VIEW SINGLE RESULT
    // -------------------------------------------------------------------------
    public function viewResult(AiContentQueue $item)
    {
        $isFeature = ($item->resource_type === 'category_feature');
        if ($isFeature) {
            $businessName = FeatureTransalte::where('feature_id', $item->resource_id)
                ->where('lang_id', 1)
                ->value('name') ?? 'Feature #' . $item->resource_id;
        } else {
            $businessName = BusinessTranslation::where('business_id', $item->resource_id)
                ->where('lang_id', 1)
                ->value('name') ?? 'Business #' . $item->resource_id;
        }

        return view('Admin.content-generation.result', compact('item', 'businessName', 'isFeature'));
    }

    // -------------------------------------------------------------------------
    // GAP 1: APPLY SINGLE RESULT TO BUSINESS RECORD (manual)
    // -------------------------------------------------------------------------
    public function applyResult(AiContentQueue $item)
    {
        if ($item->status !== 'completed' || empty($item->result)) {
            return back()->with('error', 'Cannot apply: item is not completed or has no result.');
        }

        $data = json_decode($item->result, true);
        if (!is_array($data)) {
            return back()->with('error', 'Result is not valid JSON — cannot apply automatically.');
        }

        try {
            $this->applyResultToRecord($item, $data);
            return back()->with('success', 'Content applied to business record successfully.');
        } catch (\Exception $e) {
            Log::error("applyResult failed for queue item {$item->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to apply content: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // GAP 1 / GAP 6: BULK APPLY ALL COMPLETED RESULTS FOR A BUSINESS
    // -------------------------------------------------------------------------
    public function applyAllCompleted(Request $request)
    {
        $request->validate(['business_id' => 'required|integer|exists:businesses,id']);

        $items = AiContentQueue::where('resource_type', 'business')
            ->where('resource_id', $request->business_id)
            ->where('status', 'completed')
            ->whereNotNull('result')
            ->get();

        $applied = 0;
        $errors  = [];

        foreach ($items as $item) {
            $data = json_decode($item->result, true);
            if (!is_array($data)) {
                $errors[] = "Field {$item->content_field}: invalid JSON";
                continue;
            }
            try {
                $this->applyResultToRecord($item, $data);
                $applied++;
            } catch (\Exception $e) {
                $errors[] = "Field {$item->content_field}: " . $e->getMessage();
            }
        }

        $total = $items->count();
        $msg   = "Applied {$applied} of {$total} fields to the business record.";
        if (!empty($errors)) {
            return back()->with('warning', $msg . ' Errors: ' . implode('; ', $errors));
        }
        return back()->with('success', $msg);
    }

    // -------------------------------------------------------------------------
    // POLL BATCH RESULTS (scheduler every 30 min + GAP 10: manual Poll Now)
    // -------------------------------------------------------------------------
    public function pollBatches()
    {
        $processingJobs = AiBatchJob::where('status', 'processing')->get();
        foreach ($processingJobs as $job) {
            try {
                if ($job->provider === 'openai') {
                    $this->pollOpenAIBatch($job);
                } else {
                    $this->pollAnthropicBatch($job);
                }
            } catch (\Exception $e) {
                Log::error("Error polling batch {$job->id}: " . $e->getMessage());
            }
        }
        // GAP 10: Record last successful poll time
        Cache::put('ai_batch_last_poll', now()->toDateTimeString(), now()->addDays(7));
    }

    // GAP 10: HTTP-accessible manual Poll Now button
    public function pollNow()
    {
        $this->pollBatches();
        return back()->with('success', 'Batch poll triggered. Status refreshed.');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * GAP 1: Write the parsed JSON result into the correct Localio record columns.
     */
    private function applyResultToRecord(AiContentQueue $item, array $data): void
    {
        if ($item->resource_type === 'category_feature') {
            $featureId = $item->resource_id;
            $feature   = Feature::find($featureId);
            if (!$feature) return;

            $categoryId = $feature->category_id;
            if (!$categoryId) return;

            $content = CategoryFeatureContent::firstOrNew([
                'category_id' => $categoryId,
                'feature_id'  => $featureId,
                'lang_id'     => 1,
            ]);

            $field = $item->content_field;
            switch ($field) {
                case 'header':
                    if (isset($data['page_title']))  $content->page_title = $data['page_title'];
                    if (isset($data['description'])) $content->description = $data['description'];
                    break;
                case 'top_rated':
                    if (isset($data['top_rated_listing_headline'])) $content->top_rated_listing_headline = $data['top_rated_listing_headline'];
                    if (isset($data['top_rated_listing_subtext']))  $content->top_rated_listing_subtext = $data['top_rated_listing_subtext'];
                    break;
                case 'top_10':
                    if (isset($data['compare_alternatives_headline'])) $content->compare_alternatives_headline = $data['compare_alternatives_headline'];
                    if (isset($data['compare_alternatives_subtext']))  $content->compare_alternatives_subtext = $data['compare_alternatives_subtext'];
                    break;
                case 'pros_cons':
                    if (isset($data['community_says_headline'])) $content->community_says_headline = $data['community_says_headline'];
                    if (isset($data['community_says_subtext']))  $content->community_says_subtext = $data['community_says_subtext'];
                    break;
                case 'explore_comparisons':
                    if (isset($data['explore_subcategories_headline'])) $content->explore_subcategories_headline = $data['explore_subcategories_headline'];
                    if (isset($data['explore_subcategories_subtext']))  $content->explore_subcategories_subtext = $data['explore_subcategories_subtext'];
                    if (isset($data['popular_comparisons_headline']))   $content->popular_comparisons_headline = $data['popular_comparisons_headline'];
                    if (isset($data['popular_comparisons_subtext']))    $content->popular_comparisons_subtext = $data['popular_comparisons_subtext'];
                    break;
                case 'metatags':
                    if (isset($data['meta_title']))       $content->meta_title = $data['meta_title'];
                    if (isset($data['meta_description'])) $content->meta_description = $data['meta_description'];
                    break;
            }
            $content->save();
            return;
        }

        $businessId = $item->resource_id;
        $field      = $item->content_field;

        // Ensure business has default language attached to be visible in listings
        $bizRecord = Business::find($businessId);
        if ($bizRecord) {
            $bizRecord->languages()->syncWithoutDetaching([1]);
        }

        switch ($field) {
            case 'short_description':
                $shortDesc = $data['short_description'] ?? null;
                if ($shortDesc !== null) {
                    BusinessTranslation::where('business_id', $businessId)
                        ->where('lang_id', 1)
                        ->update(['short_description' => $shortDesc]);
                }
                break;

            case 'long_description':
                $longDesc = $data['long_preview_description'] ?? ($data['long_description'] ?? null);
                if ($longDesc !== null) {
                    BusinessTranslation::where('business_id', $businessId)
                        ->where('lang_id', 1)
                        ->update(['long_preview_description' => $longDesc]);
                }
                break;

            case 'usps':
                $usps = $data['usps'] ?? ($data['features'] ?? []);
                if (!empty($usps) && is_array($usps)) {
                    BusinessUsp::where('business_id', $businessId)->delete();
                    foreach ($usps as $index => $itemVal) {
                        $text = is_array($itemVal) ? ($itemVal['title'] ?? ($itemVal['text'] ?? '')) : (string)$itemVal;
                        if (empty(trim($text))) continue;
                        BusinessUsp::create([
                            'business_id' => $businessId,
                            'text'        => trim($text),
                            'sort_order'  => $index + 1,
                        ]);
                    }
                }
                break;

            case 'what_is':
                $whatIs = $data['description'] ?? ($data['what_is'] ?? ($data['content'] ?? null));
                if ($whatIs !== null) {
                    BusinessTranslation::where('business_id', $businessId)
                        ->where('lang_id', 1)
                        ->update(['description' => $whatIs]);
                }
                break;

            case 'reviews_description':
                $revDesc = $data['reviews_description'] ?? ($data['description'] ?? ($data['content'] ?? null));
                if ($revDesc !== null) {
                    BusinessTranslation::where('business_id', $businessId)
                        ->where('lang_id', 1)
                        ->update(['reviews_description' => $revDesc]);
                }
                break;

            case 'pros_cons':
                if ($bizRecord) {
                    self::applyProsConsToBusiness($bizRecord, $data);
                }
                break;

            case 'features':
                if ($bizRecord) {
                    self::applyFeaturesToBusiness($bizRecord, $data['feature_ids'] ?? ($data['features'] ?? $data));
                }
                break;

            case 'services':
                $headline   = $data['headline'] ?? null;
                $topText    = $data['top_text'] ?? null;
                $bottomText = $data['bottom_text'] ?? null;

                $offering = \App\Models\BusinessOffering::firstOrNew(['business_id' => $businessId]);
                if ($headline !== null)   $offering->headline   = $headline;
                if ($topText !== null)    $offering->top_text    = $topText;
                if ($bottomText !== null) $offering->bottom_text = $bottomText;
                $offering->save();

                if ($bottomText !== null) {
                    BusinessTranslation::where('business_id', $businessId)
                        ->where('lang_id', 1)
                        ->update(['after_image_description' => $bottomText]);
                }
                break;

            case 'faqs':
                $faqs = $data['faqs'] ?? [];
                if (!empty($faqs) && is_array($faqs)) {
                    BusinessFaq::where('business_id', $businessId)->delete();
                    foreach ($faqs as $index => $faq) {
                        $question = $faq['question'] ?? null;
                        $answer   = $faq['answer']   ?? null;
                        if (!$question || !$answer) continue;

                        $catName = trim($faq['category'] ?? ($faq['category_name'] ?? ($faq['cat'] ?? '')));
                        $faqCatId = null;
                        if (!empty($catName)) {
                            $catObj = BusinessFaqCategory::where('business_id', $businessId)
                                ->whereRaw('LOWER(name) = ?', [mb_strtolower($catName)])
                                ->first();
                            if (!$catObj) {
                                $nextPos = (BusinessFaqCategory::where('business_id', $businessId)->max('position') ?? 0) + 1;
                                $catObj = BusinessFaqCategory::create([
                                    'business_id' => $businessId,
                                    'name'        => $catName,
                                    'position'    => $nextPos,
                                    'status'      => 1,
                                ]);
                            }
                            $faqCatId = $catObj->id;
                        }

                        $bizFaq = BusinessFaq::create([
                            'business_id'              => $businessId,
                            'business_faq_category_id' => $faqCatId,
                            'position'                 => $index + 1,
                            'status'                   => true,
                        ]);

                        BusinessFaqTranslation::create([
                            'business_faq_id' => $bizFaq->id,
                            'lang_id'         => 1,
                            'question'        => $question,
                            'answer'          => $answer,
                        ]);
                    }
                }
                break;

            case 'categories':
                if ($bizRecord) {
                    self::applyCategoriesToBusiness($bizRecord, $data);
                }
                break;

            case 'active_countries':
                $activeAll = !empty($data['active_all_countries']);
                Business::where('id', $businessId)->update(['active_all_countries' => $activeAll ? 1 : 0]);

                if (!$activeAll) {
                    $countryIds = [];
                    if (!empty($data['country_ids']) && is_array($data['country_ids'])) {
                        $countryIds = array_values(array_filter(array_map('intval', $data['country_ids'])));
                    } elseif (!empty($data['country_codes']) && is_array($data['country_codes'])) {
                        $countryIds = \App\Models\Country::whereIn('country_code', $data['country_codes'])->pluck('id')->toArray();
                    }
                    $biz = Business::find($businessId);
                    if ($biz && !empty($countryIds)) {
                        $biz->countries()->sync($countryIds);
                    }
                }
                break;

            case 'rating':
                $ratingVal = $data['rating'] ?? ($data['value'] ?? null);
                if (is_numeric($ratingVal)) {
                    $ratingVal = max(1.0, min(5.0, round((float) $ratingVal, 2)));
                    Business::where('id', $businessId)->update(['admin_rating' => $ratingVal]);
                }
                break;

            // Legacy fallbacks
            case 'content':
                $content = $data['content'] ?? null;
                if ($content !== null) {
                    BusinessTranslation::where('business_id', $businessId)
                        ->where('lang_id', 1)
                        ->update(['description' => $content]);
                }
                break;

            case 'features':
                $features = $data['features'] ?? [];
                if (!empty($features) && is_array($features)) {
                    BusinessUsp::where('business_id', $businessId)->delete();
                    foreach ($features as $index => $feature) {
                        $title = $feature['title']       ?? null;
                        $desc  = $feature['description'] ?? null;
                        if (!$title) continue;
                        $text = $desc ? "{$title}: {$desc}" : $title;
                        BusinessUsp::create([
                            'business_id' => $businessId,
                            'text'        => $text,
                            'sort_order'  => $index + 1,
                        ]);
                    }
                }
                break;

            case 'metatags':
                $updates = [];
                if (isset($data['meta_title']))       $updates['meta_title']       = $data['meta_title'];
                if (isset($data['meta_description'])) $updates['meta_description'] = $data['meta_description'];
                if (!empty($updates)) {
                    Business::where('id', $businessId)->update($updates);
                }
                break;
        }
    }

    /**
     * GAP 9: Validate AI JSON output matches expected schema for the given content field.
     * Returns true on success, or an error string on failure.
     */
    private function validateOutputSchema(string $field, string $rawContent): bool|string
    {
        $data = json_decode($rawContent, true);

        if (!is_array($data)) {
            return 'Response is not valid JSON.';
        }

        switch ($field) {
            case 'short_description':
                if (!isset($data['short_description']) || !is_string($data['short_description'])) {
                    return 'Expected {"short_description": "..."}';
                }
                break;
            case 'long_description':
                if (!isset($data['long_preview_description']) && !isset($data['long_description'])) {
                    return 'Expected {"long_preview_description": "..."}';
                }
                break;
            case 'usps':
                $usps = $data['usps'] ?? ($data['features'] ?? null);
                if (!is_array($usps)) {
                    return 'Expected {"usps": ["...", "..."]}';
                }
                break;
            case 'what_is':
                if (!isset($data['description']) && !isset($data['what_is']) && !isset($data['content'])) {
                    return 'Expected {"description": "..."}';
                }
                break;
            case 'reviews_description':
                if (!isset($data['reviews_description']) && !isset($data['description']) && !isset($data['content'])) {
                    return 'Expected {"reviews_description": "..."}';
                }
                break;
            case 'pros_cons':
                $hasPros = (isset($data['pros']) && is_array($data['pros'])) || (isset($data['pro_ids']) && is_array($data['pro_ids']));
                $hasCons = (isset($data['cons']) && is_array($data['cons'])) || (isset($data['con_ids']) && is_array($data['con_ids']));
                if (!$hasPros || !$hasCons) {
                    return 'Expected {"pro_ids": [...], "con_ids": [...]} or {"pros": [...], "cons": [...]}';
                }
                break;
            case 'services':
                if (!isset($data['top_text']) && !isset($data['bottom_text'])) {
                    return 'Expected {"top_text": "...", "bottom_text": "..."}';
                }
                break;
            case 'faqs':
                if (!isset($data['faqs']) || !is_array($data['faqs'])) {
                    return 'Expected {"faqs": [{"category": "...", "question": "...", "answer": "..."}]}';
                }
                break;
            case 'categories':
                if (!isset($data['category_id']) && !isset($data['subcategory_ids'])) {
                    return 'Expected {"category_id":..., "subcategory_ids": [...]}';
                }
                break;
            case 'active_countries':
                if (!isset($data['active_all_countries']) && !isset($data['country_codes'])) {
                    return 'Expected {"active_all_countries": true|false, "country_codes": [...]}';
                }
                break;
            case 'rating':
                $rCheck = $data['rating'] ?? ($data['value'] ?? null);
                if ($rCheck === null || !is_numeric($rCheck)) {
                    return 'Expected {"rating": 4.5} (numeric rating between 1.0 and 5.0)';
                }
                break;
            case 'content':
                if (!isset($data['content'])) return 'Expected {"content": "..."}';
                break;
            case 'features':
                if (!isset($data['features'])) return 'Expected {"features": [...]}';
                break;
            case 'metatags':
                if (!isset($data['meta_title']) && !isset($data['meta_description'])) return 'Expected meta tags';
                break;
        }

        return true;
    }


    private function defaultFeaturePromptText(string $field, string $featureName, string $categoryName): string
    {
        return match ($field) {
            'header' => "Write an engaging page title (H1) and introductory header text for the feature '{$featureName}' in category '{$categoryName}'. Return JSON: {\"page_title\": \"Top {$categoryName} with {$featureName}\", \"description\": \"Detailed overview...\"}",
            'top_rated' => "Write a Compare Top-Rated section Headline (H2) and explanatory subtext for '{$featureName}' in '{$categoryName}'. Return JSON: {\"top_rated_listing_headline\": \"Compare Top-Rated {$categoryName} with {$featureName}\", \"top_rated_listing_subtext\": \"Discover highest rated options...\"}",
            'top_10' => "Write a Top 10 alternatives Headline (H2) and subtext for '{$featureName}' in '{$categoryName}'. Return JSON: {\"compare_alternatives_headline\": \"Top 10 {$categoryName} with {$featureName}\", \"compare_alternatives_subtext\": \"Explore alternatives...\"}",
            'pros_cons' => "Write a Pros and Cons Headline (H2) and subtext evaluating '{$featureName}' in '{$categoryName}'. Return JSON: {\"community_says_headline\": \"Pros and Cons of {$featureName} in {$categoryName}\", \"community_says_subtext\": \"What users love and find challenging...\"}",
            'explore_comparisons' => "Write section headlines and subtexts for exploring categories and popular comparisons for '{$featureName}' in '{$categoryName}'. Return JSON: {\"explore_subcategories_headline\": \"Explore Categories with {$featureName}\", \"explore_subcategories_subtext\": \"...\", \"popular_comparisons_headline\": \"Popular Comparisons\", \"popular_comparisons_subtext\": \"...\"}",
            'metatags' => "Write an SEO meta title and meta description for '{$featureName}' in '{$categoryName}'. Return JSON: {\"meta_title\": \"Best {$categoryName} with {$featureName} | Localio\", \"meta_description\": \"Compare top options...\"}",
            default => "Generate content for '{$featureName}' in category '{$categoryName}'. Return JSON.",
        };
    }

    private function defaultPromptText(string $field, string $businessName, string $categoryName, string $businessUrl = '', array $extra = []): string
    {
        $urlInfo = !empty($businessUrl) ? " Website/URL: {$businessUrl}." : "";
        $catInfo = !empty($categoryName) ? " Category: {$categoryName}." : "";

        return match ($field) {
            'short_description' => "Write a concise, engaging summary for {$businessName}.{$catInfo}{$urlInfo} Length: 1-2 punchy sentences (under 160 characters) suitable for a directory listing card preview. Return JSON: {\"short_description\": \"...\"}",

            'long_description' => "Write an extended, in-depth preview description for {$businessName}.{$catInfo}{$urlInfo} This will be displayed in the extended listing box on category pages. Highlight the company's core platform, key features, reliability, target users, and competitive advantages in 2-3 well-written HTML paragraphs. Return JSON: {\"long_preview_description\": \"<p>...</p><p>...</p>\"}",

            'usps' => "Generate 4 to 6 Unique Selling Propositions (USPs) for {$businessName}.{$catInfo}{$urlInfo} Each USP must be concise (3-8 words) highlighting a standout benefit, guarantee, or key feature (e.g., 'Free domain & SSL certificate', '24/7 expert customer support', '99.9% uptime guarantee'). Return JSON: {\"usps\": [\"USP 1\", \"USP 2\", \"USP 3\", \"USP 4\"]}",

            'what_is' => "Write an authoritative, detailed overview for {$businessName}.{$catInfo}{$urlInfo} for the 'What is {$businessName}' section on their profile page. Provide 2-3 structured HTML paragraphs explaining what the business is, what infrastructure or services it provides, who its ideal customers are, and why users choose it. Return JSON: {\"description\": \"<p>...</p><p>...</p>\"}",

            'reviews_description' => "Write a concise overview for {$businessName}.{$catInfo}{$urlInfo} for the 'What is {$businessName}' section on their reviews page. Provide 1-2 engaging HTML paragraphs explaining what the business is and why users choose it. Return JSON: {\"reviews_description\": \"<p>...</p>\"}", 

            'pros_cons' => "Evaluate {$businessName}.{$catInfo}{$urlInfo} Select the 3 to 5 most accurate Pro IDs and 2 to 4 Con IDs strictly from the available category options below:

Available Pros:
" . ($extra['available_pros'] ?? "- [ID: 1] High reliability
- [ID: 2] Good value for money
- [ID: 3] Affordable pricing
- [ID: 4] Easy to use
- [ID: 5] 24/7 customer support") . "

Available Cons:
" . ($extra['available_cons'] ?? "- [ID: 6] Steep learning curve
- [ID: 7] Limited advanced features
- [ID: 8] Hidden additional fees
- [ID: 9] Unclear pricing") . "

Return JSON strictly using the selected IDs: {\"pro_ids\": [1, 2, 3], \"con_ids\": [6, 7]}",

            'services' => "Write the Services section for {$businessName}.{$catInfo}{$urlInfo} which accompanies product screenshots. Provide: 1. Top text: 1-2 paragraphs introducing the core platform and user experience before the screenshot tour. 2. Bottom text: Extended services and features after the screenshots, using [headline]Section Title[/headline] before each subsection (our system automatically renders them as <h3> headlines with proper spacing) followed by descriptive text with <br> for paragraph spacing. Return JSON: {\"top_text\": \"Core platform overview...\", \"bottom_text\": \"[headline]Cloud Platform & Infrastructure[/headline]<br>High performance cloud servers and scaling...<br><br>[headline]Enterprise Security & Backups[/headline]<br>Details on automated backups, firewall and protection...\"}",

            'faqs' => "Write 5 high-value FAQs for {$businessName}.{$catInfo}{$urlInfo} addressing common customer questions regarding plans, pricing, key features, setup, and support. Group each FAQ logically under a category (e.g. \"Plans & Pricing\", \"Setup & Migration\", \"Features\", \"Support\"). Return JSON: {\"faqs\": [{\"category\": \"...\", \"question\": \"...\", \"answer\": \"...\"}]}",

            'categories' => "Analyze {$businessName}.{$urlInfo} Select the most suitable primary category ID and up to 3 subcategory IDs from the following list:
" . ($extra['category_list'] ?? "No category list provided") . "

Return JSON: {\"category_id\": 1, \"category_name\": \"...\", \"subcategory_ids\": [2, 3]}",

            'active_countries' => 'Analyze ' . $businessName . '.' . $urlInfo . ' Determine whether this business operates globally (available in all countries) or is restricted to specific countries. If available globally in all countries, return JSON: {"active_all_countries": true}. If restricted to specific countries, return JSON: {"active_all_countries": false, "country_ids": [1, 2, 3]} using localio country IDs.',

            'rating' => "Evaluate the overall market reputation, service quality, and reliability for {$businessName}.{$catInfo}{$urlInfo} Provide an initial overall editorial rating score between 1.0 and 5.0 (with one or two decimal places, e.g. 4.6 or 4.8) to be displayed on their directory profile until 10 approved user reviews are reached. Return JSON: {\"rating\": 4.6}",

            'content'   => "Write a comprehensive overview for {$businessName} ({$categoryName}). Return JSON: {\"content\": \"...\"}",
            'features'  => "List 6 key features of {$businessName} ({$categoryName}). Return JSON: {\"features\": [{\"title\": \"...\", \"description\": \"...\"}]}",
            'metatags'  => "Write SEO meta tags for {$businessName} ({$categoryName}). Return JSON: {\"meta_title\": \"...\", \"meta_description\": \"...\"}",
            default     => "Generate content for {$businessName}. Return JSON.",
        };
    }

    private function submitOpenAIBatch(string $apiKey, array $requests): string
    {
        $jsonl = '';
        foreach ($requests as $req) {
            $jsonl .= json_encode($req) . "\n";
        }

        $fileResponse = Http::withToken($apiKey)
            ->attach('file', $jsonl, 'batch_input.jsonl', ['Content-Type' => 'application/json'])
            ->post('https://api.openai.com/v1/files', ['purpose' => 'batch']);

        if (!$fileResponse->successful()) {
            throw new \Exception('OpenAI file upload failed: ' . $fileResponse->body());
        }

        $fileId = $fileResponse->json('id');

        $batchResponse = Http::withToken($apiKey)->post('https://api.openai.com/v1/batches', [
            'input_file_id'     => $fileId,
            'endpoint'          => '/v1/responses',
            'completion_window' => '24h',
        ]);

        if (!$batchResponse->successful()) {
            throw new \Exception('OpenAI batch creation failed: ' . $batchResponse->body());
        }

        return $batchResponse->json('id');
    }

    private function submitAnthropicBatch(string $apiKey, array $requests): string
    {
        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'anthropic-beta'    => 'message-batches-2024-09-24',
        ])->post('https://api.anthropic.com/v1/messages/batches', ['requests' => $requests]);

        if (!$response->successful()) {
            throw new \Exception('Anthropic batch creation failed: ' . $response->body());
        }

        return $response->json('id');
    }

    private function pollOpenAIBatch(AiBatchJob $job): void
    {
        $apiKey = optional(WebSetting::where('key', 'openai_api_key')->first())->value;
        if (empty($apiKey)) return;

        $response = Http::withToken($apiKey)->get("https://api.openai.com/v1/batches/{$job->batch_id}");
        if (!$response->successful()) return;

        $data   = $response->json();
        $status = $data['status'] ?? '';

        $job->update([
            'completed_items' => $data['request_counts']['completed'] ?? 0,
            'failed_items'    => $data['request_counts']['failed'] ?? 0,
        ]);

        if ($status === 'completed') {
            $outputFileId = $data['output_file_id'] ?? null;
            if ($outputFileId) {
                $fileResponse = Http::withToken($apiKey)->get("https://api.openai.com/v1/files/{$outputFileId}/content");
                if ($fileResponse->successful()) {
                    $this->processOpenAIResults($job, $fileResponse->body());
                }
            }
            $job->update(['status' => 'completed', 'completed_at' => now()]);

        } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
            $job->update(['status' => 'failed', 'error_message' => "Batch ended with status: {$status}"]);
            $job->queueItems()->where('status', 'processing')->update(['status' => 'failed', 'error_message' => "Batch {$status}"]);
        }
    }

    private function pollAnthropicBatch(AiBatchJob $job): void
    {
        $apiKey = optional(WebSetting::where('key', 'anthropic_api_key')->first())->value;
        if (empty($apiKey)) return;

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'anthropic-beta'    => 'message-batches-2024-09-24',
        ])->get("https://api.anthropic.com/v1/messages/batches/{$job->batch_id}");

        if (!$response->successful()) return;

        $data   = $response->json();
        $status = $data['processing_status'] ?? '';
        $counts = $data['request_counts'] ?? [];

        $job->update([
            'completed_items' => $counts['succeeded'] ?? 0,
            'failed_items'    => ($counts['errored'] ?? 0) + ($counts['expired'] ?? 0),
        ]);

        if ($status === 'ended') {
            $resultsUrl = $data['results_url'] ?? null;
            if ($resultsUrl) {
                $resultsResponse = Http::withHeaders([
                    'x-api-key'         => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'anthropic-beta'    => 'message-batches-2024-09-24',
                ])->get($resultsUrl);
                if ($resultsResponse->successful()) {
                    $this->processAnthropicResults($job, $resultsResponse->body());
                }
            }
            $job->update(['status' => 'completed', 'completed_at' => now()]);
        }
    }

    private function processOpenAIResults(AiBatchJob $job, string $jsonlBody): void
    {
        foreach (explode("\n", trim($jsonlBody)) as $line) {
            if (empty(trim($line))) continue;
            $result = json_decode($line, true);
            if (!$result) continue;

            $customId = $result['custom_id'] ?? '';
            $content  = $result['response']['body']['choices'][0]['message']['content']
                ?? $result['response']['body']['output'][0]['content'][0]['text']
                ?? null;
            $error = $result['error']['message'] ?? null;

            $queueItem = $job->queueItems()->where('openai_request_id', $customId)->first();
            if (!$queueItem) continue;

            if ($content) {
                // GAP 9: Schema validation before marking complete
                $schemaCheck = $this->validateOutputSchema($queueItem->content_field, $content);
                if ($schemaCheck !== true) {
                    $queueItem->update(['status' => 'failed', 'error_message' => 'Schema validation failed: ' . $schemaCheck]);
                    continue;
                }

                $queueItem->update(['status' => 'completed', 'result' => $content]);

                // GAP 1: Auto-apply result to the business record
                try {
                    $this->applyResultToRecord($queueItem, json_decode($content, true));
                } catch (\Exception $e) {
                    Log::warning("Auto-apply failed for queue item {$queueItem->id}: " . $e->getMessage());
                }
            } else {
                $queueItem->update(['status' => 'failed', 'error_message' => $error ?? 'Unknown error']);
            }
        }
    }

    private function processAnthropicResults(AiBatchJob $job, string $jsonlBody): void
    {
        foreach (explode("\n", trim($jsonlBody)) as $line) {
            if (empty(trim($line))) continue;
            $result = json_decode($line, true);
            if (!$result) continue;

            $customId = $result['custom_id'] ?? '';
            $type     = $result['result']['type'] ?? '';
            $content  = null;
            $error    = null;

            if ($type === 'succeeded') {
                $content = $result['result']['message']['content'][0]['text'] ?? null;
            } else {
                $error = $result['result']['error']['message'] ?? 'Unknown error';
            }

            $queueItem = $job->queueItems()->where('openai_request_id', $customId)->first();
            if (!$queueItem) continue;

            if ($content) {
                // GAP 9: Schema validation
                $schemaCheck = $this->validateOutputSchema($queueItem->content_field, $content);
                if ($schemaCheck !== true) {
                    $queueItem->update(['status' => 'failed', 'error_message' => 'Schema validation failed: ' . $schemaCheck]);
                    continue;
                }

                $queueItem->update(['status' => 'completed', 'result' => $content]);

                // GAP 1: Auto-apply result to the business record
                try {
                    $this->applyResultToRecord($queueItem, json_decode($content, true));
                } catch (\Exception $e) {
                    Log::warning("Auto-apply failed for queue item {$queueItem->id}: " . $e->getMessage());
                }
            } else {
                $queueItem->update(['status' => 'failed', 'error_message' => $error]);
            }
        }
    }

    // -------------------------------------------------------------------------
    // LOGO UPLOAD (SVG / Image direct upload from table)
    // -------------------------------------------------------------------------
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'logo'        => 'required|file|mimes:svg,png,jpg,jpeg,webp|max:5120',
        ]);

        $business = Business::findOrFail($request->business_id);
        $file     = $request->file('logo');
        $ext      = strtolower($file->getClientOriginalExtension() ?: 'svg');
        $filename = time() . '_' . Str::random(10) . '.' . $ext;
        $dest     = public_path('business/icon');

        if (!\File::exists($dest)) {
            \File::makeDirectory($dest, 0755, true);
        }

        $file->move($dest, $filename);
        $relativePath = 'business/icon/' . $filename;

        // Delete old custom icon if exists
        $oldIcon = $business->getRawOriginal('icon_id');
        if ($oldIcon && \File::exists(public_path($oldIcon)) && !in_array($oldIcon, [
            'front/img/default_business_logo.svg', 'front/img/logo.svg', 'front/img/default.png', 'images/default.png'
        ])) {
            @unlink(public_path($oldIcon));
        }

        $business->icon_id = $relativePath;
        $business->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Logo uploaded successfully!',
            'logo_url' => asset($relativePath),
        ]);
    }

    // -------------------------------------------------------------------------
    // BATCH SIZE CONFIGURATION SETTING
    // -------------------------------------------------------------------------
    public function updateBatchSetting(Request $request)
    {
        $request->validate([
            'batch_size' => 'required|integer|min:1|max:50',
        ]);

        WebSetting::updateOrCreate(['key' => 'ai_batch_size'], ['value' => (int)$request->batch_size]);

        return back()->with('success', 'Batch size updated to ' . $request->batch_size . ' businesses per batch.');
    }

    // -------------------------------------------------------------------------
    // CLEAN BATCH JSON PAYLOAD BUILDER
    // -------------------------------------------------------------------------
    public function buildCleanBatchPayload(AiBatchJob $job): array
    {
        $hasFeatures = $job->queueItems()->where('resource_type', 'category_feature')->exists()
            || str_starts_with($job->prompt_type ?: '', 'feature:');

        if ($hasFeatures) {
            $featureIds = $job->queueItems()->where('resource_type', 'category_feature')->pluck('resource_id')->unique()->toArray();
            $features   = Feature::with([
                'translations'         => fn($q) => $q->where('lang_id', 1),
                'category.translation' => fn($q) => $q->where('lang_id', 1),
            ])->whereIn('id', $featureIds)->get();

            $promptType = str_replace('feature:', '', $job->prompt_type ?: '');
            $requestedFields = array_values(array_filter(explode(',', $promptType)));
            if (empty($requestedFields)) {
                $requestedFields = self::FEATURE_CONTENT_FIELDS;
            }
            $isAll = empty(array_diff(self::FEATURE_CONTENT_FIELDS, $requestedFields));
            $fieldsValue = $isAll ? 'ALL' : $requestedFields;

            $fList = [];
            foreach ($features as $f) {
                $fName   = optional($f->translations->first())->name ?? 'Feature #' . $f->id;
                $catName = optional($f->category)->translation?->name ?? (optional($f->category)->name ?? 'General');

                $fList[] = [
                    'feature_id'       => $f->id,
                    'feature_name'     => $fName,
                    'category_id'      => $f->category_id,
                    'category_name'    => $catName,
                    'fields_requested' => $fieldsValue,
                ];
            }

            return [
                'batch_id'       => $job->batch_id ?: ('BATCH-' . $job->id),
                'batch_type'     => 'features',
                'total_features' => count($fList),
                'features'       => $fList,
            ];
        }

        $businessIds = $job->queueItems()->where('resource_type', 'business')->pluck('resource_id')->unique()->toArray();
        $businesses  = Business::with([
            'translations' => fn($q) => $q->where('lang_id', 1),
            'websites',
        ])->whereIn('id', $businessIds)->get();

        $bList = [];
        foreach ($businesses as $b) {
            $bUrl = $b->permanent_url ?: (optional($b->websites->first())->website_url ?: '');
            $bList[] = [
                'business_id'   => $b->id,
                'business_name' => optional($b->translations->first())->name ?? $b->name ?? 'Business #' . $b->id,
                'business_url'  => $bUrl,
            ];
        }

        return [
            'batch_id'         => $job->batch_id ?: ('BATCH-' . $job->id),
            'total_businesses' => count($bList),
            'businesses'       => $bList,
        ];
    }

    public function getBatchJson(AiBatchJob $job)
    {
        $payload = $this->buildCleanBatchPayload($job);
        return response()->json($payload, 200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function downloadBatchJson(AiBatchJob $job)
    {
        $payload = $this->buildCleanBatchPayload($job);
        $content = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $type = isset($payload['batch_type']) ? $payload['batch_type'] : 'businesses';
        $filename = 'batch_' . $type . '_' . $job->id . '_' . ($job->batch_id ?: 'job') . '.json';

        return response($content, 200, [
            'Content-Type'        => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadCategories()
    {
        $categories = Category::whereNull('parent_id')
            ->with([
                'translation' => fn($q) => $q->where('lang_id', 1),
                'children.translation' => fn($q) => $q->where('lang_id', 1),
            ])
            ->orderBy('id')
            ->get()
            ->map(function($parent) {
                return [
                    'category_id'   => $parent->id,
                    'category_name' => optional($parent->translation)->name ?? ('Category #' . $parent->id),
                    'subcategories' => $parent->children->map(fn($child) => [
                        'subcategory_id'   => $child->id,
                        'subcategory_name' => optional($child->translation)->name ?? ('Subcategory #' . $child->id),
                    ])->values(),
                ];
            });

        $json = json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type'        => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="localio_categories_and_ids.json"',
        ]);
    }

    public function downloadCountries()
    {
        $countries = \App\Models\Country::where('status', 1)
            ->orderBy('name')
            ->get()
            ->map(fn($c) => [
                'country_id'   => $c->id,
                'country_name' => $c->name,
            ])
            ->values();

        $json = json_encode($countries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type'        => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="localio_country_ids.json"',
        ]);
    }

    public function downloadProsCons()
    {
        $categories = Category::with([
            'translation' => fn($q) => $q->where('lang_id', 1),
            'proCons' => fn($q) => $q->where('status', 1)->orderBy('type')->orderBy('id'),
        ])
        ->whereHas('proCons', fn($q) => $q->where('status', 1))
        ->orderBy('id')
        ->get()
        ->map(function ($cat) {
            $catName = optional($cat->translation)->name ?? ($cat->name ?? ('Category #' . $cat->id));
            $pros = $cat->proCons->where('type', 'pro')->values()->map(fn($item) => [
                'id'          => $item->id,
                'text'        => $item->text,
                'description' => $item->description ?? '',
                'aliases'     => $item->aliases ?: [],
            ]);
            $cons = $cat->proCons->where('type', 'con')->values()->map(fn($item) => [
                'id'          => $item->id,
                'text'        => $item->text,
                'description' => $item->description ?? '',
                'aliases'     => $item->aliases ?: [],
            ]);

            return [
                'category_id'   => $cat->id,
                'category_name' => $catName,
                'total_pros'    => $pros->count(),
                'total_cons'    => $cons->count(),
                'pros'          => $pros,
                'cons'          => $cons,
            ];
        })
        ->values();

        $json = json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type'        => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="localio_pros_and_cons_and_ids.json"',
        ]);
    }

    public function downloadFeatures()
    {
        $categories = Category::with([
            'translation' => fn($q) => $q->where('lang_id', 1),
            'features' => fn($q) => $q->where('status', 'active')
                ->with(['translations' => fn($t) => $t->where('lang_id', 1)])
                ->orderBy('id'),
        ])
        ->whereHas('features', fn($q) => $q->where('status', 'active'))
        ->orderBy('id')
        ->get()
        ->map(function ($cat) {
            $catName = optional($cat->translation)->name ?? ($cat->name ?? ('Category #' . $cat->id));
            $features = $cat->features->map(function ($f) {
                $fName = optional($f->translations->first())->name ?? ('Feature #' . $f->id);
                return [
                    'feature_id'   => $f->id,
                    'feature_name' => $fName,
                ];
            })->values();

            return [
                'category_id'    => $cat->id,
                'category_name'  => $catName,
                'total_features' => $features->count(),
                'features'       => $features,
            ];
        })
        ->values();

        $json = json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type'        => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="localio_features_and_ids.json"',
        ]);
    }

    // -------------------------------------------------------------------------
    // UPLOAD BATCH RESULT JSON (From ChatGPT)
    // -------------------------------------------------------------------------
    public function uploadBatchResult(Request $request, ?AiBatchJob $job = null)
    {
        $request->validate([
            'result_file' => 'nullable|file|max:10240',
            'result_text' => 'nullable|string',
        ]);

        $rawJson = null;
        if ($request->hasFile('result_file')) {
            $rawJson = file_get_contents($request->file('result_file')->getRealPath());
        } elseif ($request->filled('result_text')) {
            $rawJson = trim($request->result_text);
        }

        if (empty($rawJson)) {
            return back()->with('error', 'Please upload a JSON file or paste the JSON text.');
        }

        // Clean any markdown code blocks if user pasted ```json ... ```
        $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $rawJson);
        $cleanJson = preg_replace('/\s*```$/', '', $cleanJson);
        $cleanJson = trim($cleanJson);

        $parsed = json_decode($cleanJson, true);
        if (!is_array($parsed)) {
            return back()->with('error', 'Invalid JSON syntax: ' . json_last_error_msg());
        }

        // Auto-detect batch job if not provided in URL
        if (!$job || !$job->exists) {
            $batchId = $parsed['batch_id'] ?? ($parsed['id'] ?? ($parsed['batch_job_id'] ?? null));

            if (!$batchId && isset($parsed['businesses']) && is_array($parsed['businesses'])) {
                foreach ($parsed['businesses'] as $b) {
                    if (is_array($b) && isset($b['batch_id'])) {
                        $batchId = $b['batch_id'];
                        break;
                    }
                }
            }
            if (!$batchId && isset($parsed['features']) && is_array($parsed['features'])) {
                foreach ($parsed['features'] as $f) {
                    if (is_array($f) && isset($f['batch_id'])) {
                        $batchId = $f['batch_id'];
                        break;
                    }
                }
            }

            if ($batchId) {
                $job = AiBatchJob::where('batch_id', $batchId)->first();
                if (!$job && is_numeric($batchId)) {
                    $job = AiBatchJob::find($batchId);
                }
                if (!$job && preg_match('/BATCH-(\d+)/i', (string)$batchId, $m)) {
                    $job = AiBatchJob::find($m[1]);
                }
            }

            // Fallback: match by business IDs in queue items
            if (!$job) {
                $bList = $parsed['businesses'] ?? (array_is_list($parsed) ? $parsed : []);
                $candidateBizIds = [];
                foreach ($bList as $b) {
                    if (!is_array($b)) continue;
                    $bId = $b['business_id'] ?? ($b['id'] ?? null);
                    if ($bId) $candidateBizIds[] = $bId;
                }
                if (!empty($candidateBizIds)) {
                    $matchedJobId = AiContentQueue::where('resource_type', 'business')
                        ->whereIn('resource_id', $candidateBizIds)
                        ->whereNotNull('batch_job_id')
                        ->latest('id')
                        ->value('batch_job_id');
                    if ($matchedJobId) {
                        $job = AiBatchJob::find($matchedJobId);
                    }
                }
            }

            if (!$job) {
                return back()->with('error', 'Could not detect which batch job this result belongs to. Please make sure the JSON includes "batch_id": "BATCH-..." matching one of your batch jobs.');
            }
        }

        $isFeatureJob = ($job->queueItems()->where('resource_type', 'category_feature')->exists())
            || str_starts_with($job->prompt_type ?: '', 'feature:')
            || isset($parsed['features']);

        if ($isFeatureJob) {
            $featureList = [];
            if (isset($parsed['features']) && is_array($parsed['features'])) {
                $featureList = $parsed['features'];
            } elseif (isset($parsed['results']) && is_array($parsed['results'])) {
                $featureList = $parsed['results'];
            } elseif (array_is_list($parsed)) {
                $featureList = $parsed;
            } else {
                $featureList = [$parsed];
            }

            $appliedFeaturesCount = 0;
            $appliedFieldsCount   = 0;

            foreach ($featureList as $fData) {
                if (!is_array($fData)) continue;

                $rawFeatId = $fData['feature_id'] ?? ($fData['id'] ?? null);
                $featName  = trim($fData['feature_name'] ?? ($fData['name'] ?? ''));

                $feat = null;
                if ($rawFeatId) {
                    $feat = Feature::find($rawFeatId);
                }
                if (!$feat && !empty($featName)) {
                    $jobFeatIds = $job->queueItems()->where('resource_type', 'category_feature')->pluck('resource_id')->unique();
                    $feat = Feature::whereIn('id', $jobFeatIds)
                        ->whereHas('translations', function ($q) use ($featName) {
                            $q->where('lang_id', 1)->where('name', 'like', $featName);
                        })->first();
                }

                if (!$feat) {
                    Log::warning("Batch result feature not found or not matched: ID '{$rawFeatId}', name '{$featName}'");
                    continue;
                }

                $featId = $feat->id;
                $appliedFeaturesCount++;

                $jobFields = $job->queueItems()
                    ->where('resource_type', 'category_feature')
                    ->where('resource_id', $featId)
                    ->pluck('content_field')
                    ->toArray();

                if (empty($jobFields)) {
                    $jobFields = self::FEATURE_CONTENT_FIELDS;
                }

                foreach ($jobFields as $field) {
                    $normalizedData = [];

                    if ($field === 'header') {
                        if (isset($fData['header']) && is_array($fData['header'])) {
                            $normalizedData = [
                                'page_title'  => $fData['header']['page_title'] ?? ($fData['header']['title'] ?? ''),
                                'description' => $fData['header']['description'] ?? ($fData['header']['text'] ?? ''),
                            ];
                        } else {
                            $normalizedData = [
                                'page_title'  => $fData['page_title'] ?? ($fData['title'] ?? ''),
                                'description' => $fData['description'] ?? ($fData['header_text'] ?? ''),
                            ];
                        }
                    } elseif ($field === 'top_rated') {
                        $src = (isset($fData['top_rated']) && is_array($fData['top_rated'])) ? $fData['top_rated'] : $fData;
                        $normalizedData = [
                            'top_rated_listing_headline' => $src['top_rated_listing_headline'] ?? ($src['headline'] ?? ''),
                            'top_rated_listing_subtext'  => $src['top_rated_listing_subtext'] ?? ($src['subtext'] ?? ''),
                        ];
                    } elseif ($field === 'top_10') {
                        $src = (isset($fData['top_10']) && is_array($fData['top_10'])) ? $fData['top_10'] : $fData;
                        $normalizedData = [
                            'compare_alternatives_headline' => $src['compare_alternatives_headline'] ?? ($src['headline'] ?? ''),
                            'compare_alternatives_subtext'  => $src['compare_alternatives_subtext'] ?? ($src['subtext'] ?? ''),
                        ];
                    } elseif ($field === 'pros_cons') {
                        $src = (isset($fData['pros_cons']) && is_array($fData['pros_cons'])) ? $fData['pros_cons'] : $fData;
                        $normalizedData = [
                            'community_says_headline' => $src['community_says_headline'] ?? ($src['headline'] ?? ''),
                            'community_says_subtext'  => $src['community_says_subtext'] ?? ($src['subtext'] ?? ''),
                        ];
                    } elseif ($field === 'explore_comparisons') {
                        $src = (isset($fData['explore_comparisons']) && is_array($fData['explore_comparisons'])) ? $fData['explore_comparisons'] : $fData;
                        $normalizedData = [
                            'explore_subcategories_headline' => $src['explore_subcategories_headline'] ?? '',
                            'explore_subcategories_subtext'  => $src['explore_subcategories_subtext'] ?? '',
                            'popular_comparisons_headline'   => $src['popular_comparisons_headline'] ?? '',
                            'popular_comparisons_subtext'    => $src['popular_comparisons_subtext'] ?? '',
                        ];
                    } elseif ($field === 'metatags') {
                        $src = (isset($fData['metatags']) && is_array($fData['metatags'])) ? $fData['metatags'] : $fData;
                        $normalizedData = [
                            'meta_title'       => $src['meta_title'] ?? '',
                            'meta_description' => $src['meta_description'] ?? '',
                        ];
                    }

                    $queueItem = AiContentQueue::firstOrNew([
                        'batch_job_id'  => $job->id,
                        'resource_type' => 'category_feature',
                        'resource_id'   => $featId,
                        'content_field' => $field,
                    ]);

                    $queueItem->openai_request_id = "feat_{$featId}_{$field}";
                    $queueItem->status            = 'completed';
                    $queueItem->result            = json_encode($normalizedData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $queueItem->save();

                    try {
                        $this->applyResultToRecord($queueItem, $normalizedData);
                        $appliedFieldsCount++;
                    } catch (\Exception $e) {
                        Log::error("Failed to auto-apply field {$field} for feature {$featId}: " . $e->getMessage());
                    }
                }
            }

            $job->update([
                'status'          => 'completed',
                'completed_items' => $appliedFieldsCount,
                'completed_at'    => now(),
                'raw_response'    => $cleanJson,
            ]);

            return back()->with('success', "Batch result processed successfully! Applied content for {$appliedFeaturesCount} feature(s) across {$appliedFieldsCount} field(s).");
        }

        // If wrapped in {"businesses": [...]} or {"results": [...]}
        $businessList = [];
        if (isset($parsed['businesses']) && is_array($parsed['businesses'])) {
            $businessList = $parsed['businesses'];
        } elseif (isset($parsed['results']) && is_array($parsed['results'])) {
            $businessList = $parsed['results'];
        } elseif (array_is_list($parsed)) {
            $businessList = $parsed;
        } else {
            $businessList = [$parsed];
        }

        $appliedBusinessesCount = 0;
        $appliedFieldsCount     = 0;
        $newlyCreatedCount      = 0;

        foreach ($businessList as $bData) {
            if (!is_array($bData)) continue;

            $rawBizId = $bData['business_id'] ?? ($bData['id'] ?? null);
            $bizName  = trim($bData['business_name'] ?? ($bData['name'] ?? ''));
            $bizUrl   = trim($bData['business_url'] ?? ($bData['url'] ?? ($bData['permanent_url'] ?? '')));

            if (!empty($bizUrl) && !preg_match('/^https?:\/\//i', $bizUrl)) {
                $bizUrl = 'https://' . $bizUrl;
            }

            $biz = null;

            // 1. If rawBizId is provided, check if it matches a business in this batch job
            if ($rawBizId && $job) {
                $isJobBiz = $job->queueItems()->where('resource_type', 'business')->where('resource_id', $rawBizId)->exists();
                if ($isJobBiz) {
                    $biz = Business::find($rawBizId);
                }
            }

            // 2. Try matching by business name against businesses in this batch job
            if (!$biz && !empty($bizName) && $job) {
                $jobBizIds = $job->queueItems()->where('resource_type', 'business')->pluck('resource_id')->unique();
                $biz = Business::whereIn('id', $jobBizIds)
                    ->whereHas('translations', function($q) use ($bizName) {
                        $q->where('name', 'like', "%{$bizName}%");
                    })->first();
            }

            // 2b. Try matching by URL against businesses in this batch job
            if (!$biz && !empty($bizUrl) && $job) {
                $jobBizIds = $job->queueItems()->where('resource_type', 'business')->pluck('resource_id')->unique();
                $normalizedUrl = rtrim(preg_replace('/^https?:\/\/(www\.)?/i', '', $bizUrl), '/');
                if (!empty($normalizedUrl)) {
                    $biz = Business::whereIn('id', $jobBizIds)
                        ->whereRaw("TRIM(TRAILING '/' FROM REPLACE(REPLACE(permanent_url, 'https://', ''), 'http://', '')) LIKE ?", ['%' . $normalizedUrl . '%'])
                        ->first();
                }
            }

            // 3. Fallback: If this batch job only has 1 business in its queue items
            if (!$biz && $job) {
                $jobBizIds = $job->queueItems()->where('resource_type', 'business')->pluck('resource_id')->unique()->values();
                if ($jobBizIds->count() === 1) {
                    $biz = Business::find($jobBizIds->first());
                }
            }

            // 4. Try matching globally by rawBizId if it exists in the businesses table
            if (!$biz && $rawBizId) {
                $biz = Business::find($rawBizId);
            }

            // 5. Try matching globally by business name in translations (exact, case-insensitive, or cleaned)
            if (!$biz && !empty($bizName)) {
                $cleanName = trim(preg_replace('/\s*\([^)]*\)/', '', $bizName)); // remove trailing (Web Hosting)
                $matchedBizId = BusinessTranslation::whereRaw('LOWER(name) = ?', [strtolower($bizName)])->value('business_id')
                    ?? BusinessTranslation::whereRaw('LOWER(name) = ?', [strtolower($cleanName)])->value('business_id')
                    ?? BusinessTranslation::where('name', 'like', "%{$cleanName}%")->value('business_id');
                if ($matchedBizId) {
                    $biz = Business::find($matchedBizId);
                }
            }

            // 5b. Try matching globally by permanent URL
            if (!$biz && !empty($bizUrl)) {
                $normalizedUrl = rtrim(preg_replace('/^https?:\/\/(www\.)?/i', '', $bizUrl), '/');
                if (!empty($normalizedUrl)) {
                    $biz = Business::whereRaw("TRIM(TRAILING '/' FROM REPLACE(REPLACE(permanent_url, 'https://', ''), 'http://', '')) LIKE ?", ['%' . $normalizedUrl . '%'])
                        ->first();
                }
            }

            // 6. If business still does not exist, auto-create it on the fly!
            if (!$biz && !empty($bizName)) {
                $cleanName = trim(preg_replace('/\s*\([^)]*\)/', '', $bizName));
                $bizUrl = trim($bData['business_url'] ?? ($bData['url'] ?? ''));
                if (!empty($bizUrl) && !preg_match('/^https?:\/\//i', $bizUrl)) {
                    $bizUrl = 'https://' . $bizUrl;
                }

                $baseSlug = Str::slug($cleanName);
                if (empty($baseSlug)) {
                    $baseSlug = 'business-' . time() . '-' . rand(100, 999);
                }
                $slug = $baseSlug;
                $counter = 1;
                while (BusinessTranslation::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $affLinkInitial = trim($bData['affiliate_link'] ?? ($bData['affiliate_url'] ?? ''));
                $isAffInitial = (!empty($affLinkInitial) || !empty($bData['is_affiliate'])) ? 1 : 0;
                $biz = Business::create([
                    'permanent_url'        => $bizUrl ?: null,
                    'affiliate_link'       => $affLinkInitial ?: null,
                    'status'               => 1,
                    'created_by'           => auth()->id() ?? 1,
                    'is_affiliate'         => $isAffInitial,
                    'active_all_countries' => 1,
                    'category_id'          => null,
                ]);

                BusinessTranslation::create([
                    'business_id' => $biz->id,
                    'lang_id'     => 1,
                    'name'        => $cleanName,
                    'slug'        => $slug,
                    'status'      => 1,
                ]);

                $biz->languages()->syncWithoutDetaching([1]);
                $newlyCreatedCount = ($newlyCreatedCount ?? 0) + 1;
            }

            if (!$biz) continue;
            $bizId = $biz->id;
            $appliedBusinessesCount++;

            // Order fields so 'categories' is processed first to set category_id before pros_cons & features!
            $orderedFields = self::CONTENT_FIELDS;
            if (($catIdx = array_search('categories', $orderedFields)) !== false) {
                unset($orderedFields[$catIdx]);
                array_unshift($orderedFields, 'categories');
            }

            foreach ($orderedFields as $field) {
                $fieldVal = null;
                $hasField = false;

                if ($field === 'categories') {
                    if (isset($bData['categories'])) {
                        $fieldVal = $bData['categories'];
                        $hasField = true;
                    } elseif (isset($bData['category']) || isset($bData['category_id'])) {
                        $fieldVal = [
                            'category_id'     => $bData['category_id'] ?? null,
                            'category_name'   => $bData['category'] ?? null,
                            'subcategory_ids' => $bData['subcategory_ids'] ?? ($bData['subcategories'] ?? []),
                        ];
                        $hasField = true;
                    }
                } elseif ($field === 'pros_cons') {
                    if (isset($bData['pros_cons'])) {
                        $fieldVal = $bData['pros_cons'];
                        $hasField = true;
                    } elseif (isset($bData['pro_ids']) || isset($bData['con_ids']) || isset($bData['pros']) || isset($bData['cons'])) {
                        $fieldVal = [
                            'pro_ids' => $bData['pro_ids'] ?? ($bData['pros'] ?? []),
                            'con_ids' => $bData['con_ids'] ?? ($bData['cons'] ?? []),
                        ];
                        $hasField = true;
                    }
                } elseif ($field === 'reviews_description') {
                    if (isset($bData['reviews_description'])) {
                        $fieldVal = $bData['reviews_description'];
                        $hasField = true;
                    } elseif (isset($bData['review_description'])) {
                        $fieldVal = $bData['review_description'];
                        $hasField = true;
                    } elseif (isset($bData['short_business_description_reviews'])) {
                        $fieldVal = $bData['short_business_description_reviews'];
                        $hasField = true;
                    }
                } elseif ($field === 'rating') {
                    if (isset($bData['rating'])) {
                        $fieldVal = $bData['rating'];
                        $hasField = true;
                    } elseif (isset($bData['admin_rating'])) {
                        $fieldVal = $bData['admin_rating'];
                        $hasField = true;
                    }
                } elseif ($field === 'features') {
                    if (isset($bData['features'])) {
                        $fieldVal = $bData['features'];
                        $hasField = true;
                    } elseif (isset($bData['feature_ids'])) {
                        $fieldVal = $bData['feature_ids'];
                        $hasField = true;
                    }
                } elseif (isset($bData[$field])) {
                    $fieldVal = $bData[$field];
                    $hasField = true;
                }

                if (!$hasField) continue;

                $normalizedData = is_array($fieldVal) ? $fieldVal : [$field => $fieldVal];

                if ($field === 'categories') {
                    if (is_string($fieldVal)) {
                        $normalizedData = ['category_name' => $fieldVal];
                    } elseif (is_numeric($fieldVal)) {
                        $normalizedData = ['category_id' => (int)$fieldVal];
                    } elseif (is_array($fieldVal)) {
                        $normalizedData = $fieldVal;
                    }
                } elseif ($field === 'short_description' && is_string($fieldVal)) {
                    $normalizedData = ['short_description' => $fieldVal];
                } elseif ($field === 'long_description' && is_string($fieldVal)) {
                    $normalizedData = ['long_preview_description' => $fieldVal, 'long_description' => $fieldVal];
                } elseif ($field === 'what_is' && is_string($fieldVal)) {
                    $normalizedData = ['description' => $fieldVal, 'what_is' => $fieldVal];
                } elseif (($field === 'reviews_description' || $field === 'short_business_description_reviews') && (is_string($fieldVal) || is_array($fieldVal))) {
                    $val = is_array($fieldVal) ? ($fieldVal['reviews_description'] ?? ($fieldVal['description'] ?? '')) : $fieldVal;
                    $normalizedData = ['reviews_description' => $val];
                } elseif ($field === 'usps' && is_array($fieldVal)) {
                    $normalizedData = ['usps' => $fieldVal];
                } elseif ($field === 'faqs' && is_array($fieldVal)) {
                    $normalizedData = ['faqs' => $fieldVal];
                } elseif (($field === 'features' || $field === 'feature_ids') && is_array($fieldVal)) {
                    $normalizedData = ['features' => $fieldVal];
                } elseif ($field === 'active_countries' && is_array($fieldVal)) {
                    $activeAll = !empty($fieldVal['active_all_countries']);
                    if ($activeAll) {
                        $normalizedData = ['active_all_countries' => true];
                    } else {
                        $countryIds = $fieldVal['country_ids'] ?? [];
                        $countryCodes = $fieldVal['country_codes'] ?? ($fieldVal['countries'] ?? []);
                        $normalizedData = [
                            'active_all_countries' => false,
                            'country_ids'          => is_array($countryIds) ? array_values(array_filter(array_map('intval', $countryIds))) : [],
                            'country_codes'        => is_array($countryCodes) ? array_values($countryCodes) : [],
                        ];
                    }
                } elseif ($field === 'rating') {
                    $rVal = is_array($fieldVal) ? ($fieldVal['rating'] ?? $fieldVal['value'] ?? null) : $fieldVal;
                    $rFloat = is_numeric($rVal) ? round((float)$rVal, 2) : 4.5;
                    $rFloat = max(1.0, min(5.0, $rFloat));
                    $normalizedData = ['rating' => $rFloat];
                }

                // Find or create AiContentQueue record
                $queueItem = AiContentQueue::firstOrNew([
                    'batch_job_id'  => $job->id,
                    'resource_type' => 'business',
                    'resource_id'   => $bizId,
                    'content_field' => $field,
                ]);

                $queueItem->openai_request_id = "biz_{$bizId}_{$field}";
                $queueItem->status            = 'completed';
                $queueItem->result            = json_encode($normalizedData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $queueItem->save();

                // Apply directly to database record
                try {
                    $this->applyResultToRecord($queueItem, $normalizedData);
                    $appliedFieldsCount++;
                    if ($field === 'categories') {
                        $biz->refresh();
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to auto-apply field {$field} for business {$bizId}: " . $e->getMessage());
                }
            }

            // Ingest basic business information ("At a glance" fields)
            $glanceUpdates = [];
            if (isset($bData['main_offering'])) {
                $glanceUpdates['main_offering'] = is_string($bData['main_offering']) ? trim($bData['main_offering']) : null;
            }
            if (isset($bData['suitable_for'])) {
                $glanceUpdates['suitable_for'] = is_string($bData['suitable_for']) ? trim($bData['suitable_for']) : null;
            }
            $opt3Val = $bData['glance_option_3'] ?? ($bData['option_3'] ?? ($bData['hosting_options'] ?? null));
            if ($opt3Val !== null) {
                $glanceUpdates['glance_option_3'] = is_string($opt3Val) ? trim($opt3Val) : null;
            }
            $opt4Val = $bData['glance_option_4'] ?? ($bData['option_4'] ?? ($bData['website_tools'] ?? null));
            if ($opt4Val !== null) {
                $glanceUpdates['glance_option_4'] = is_string($opt4Val) ? trim($opt4Val) : null;
            }
            if (!empty($glanceUpdates)) {
                BusinessTranslation::where('business_id', $bizId)
                    ->where('lang_id', 1)
                    ->update($glanceUpdates);
            }

            // Also ingest affiliate link / websites from batch result if provided
            $affLink = trim($bData['affiliate_link'] ?? ($bData['affiliate_url'] ?? ($bData['affiliate'] ?? '')));
            if (!empty($affLink)) {
                $biz->affiliate_link = $affLink;
                $biz->is_affiliate = 1;
                $biz->save();
            } elseif (isset($bData['is_affiliate'])) {
                $biz->is_affiliate = $bData['is_affiliate'] ? 1 : 0;
                $biz->save();
            }
            if (!empty($bData['websites']) && is_array($bData['websites'])) {
                $hasAff = false;
                foreach ($bData['websites'] as $wEntry) {
                    if (!is_array($wEntry)) continue;
                    $cId = $wEntry['country_id'] ?? null;
                    if (!$cId && !empty($wEntry['country_code'])) {
                        $cId = \App\Models\Country::where('country_code', strtoupper($wEntry['country_code']))->value('id');
                    }
                    $wUrl = trim($wEntry['url'] ?? ($wEntry['website_url'] ?? ''));
                    $wAff = isset($wEntry['is_affiliate']) ? (bool)$wEntry['is_affiliate'] : true;
                    if ($cId && !empty($wUrl)) {
                        \App\Models\BusinessWebsite::updateOrCreate(
                            ['business_id' => $biz->id, 'country_id' => $cId],
                            ['website_url' => $wUrl, 'is_affiliate' => $wAff ? 1 : 0, 'status' => 1]
                        );
                        if ($wAff) $hasAff = true;
                    }
                }
                if ($hasAff) {
                    $biz->is_affiliate = 1;
                    $biz->save();
                }
            }
        }

        $job->update([
            'status'          => 'completed',
            'completed_items' => $appliedFieldsCount,
            'completed_at'    => now(),
            'raw_response'    => $cleanJson,
        ]);

        $msg = "Batch result processed successfully! Applied content for {$appliedBusinessesCount} business(es)";
        if ($newlyCreatedCount > 0) {
            $msg .= " (including {$newlyCreatedCount} newly created in directory)";
        }
        $msg .= " across {$appliedFieldsCount} field(s).";
        return back()->with('success', $msg);
    }

    // -------------------------------------------------------------------------
    // EXAMPLE OUTPUT JSON FOR CHATGPT
    // -------------------------------------------------------------------------
    public static function getBatchResultExampleJson(): string
    {
        $example = [
            'batch_id'   => 'BATCH-MANUAL-SAMPLE1234',
            'businesses' => [
                [
                    "business_id"         => 101,
                    "business_name"       => "HostGator",
                    "short_description"   => "Leading web hosting provider offering shared, VPS, and dedicated servers with unmetered bandwidth.",
                    "long_description"    => "HostGator is a global provider of web hosting and related services. Founded in a Florida dorm room, it has grown into a world-leading provider of Shared, Reseller, VPS, and Dedicated web hosting.",
                    "usps"                => [
                        "99.9% Uptime Guarantee",
                        "Free SSL Certificate Included",
                        "One-Click WordPress Installs",
                        "24/7/365 Live Support"
                    ],
                    "what_is"             => "A comprehensive web hosting platform catering to personal blogs, small businesses, and scalable enterprise web apps.",
                    "main_offering"       => "Web hosting and domain services",
                    "suitable_for"        => "Website owners, bloggers, small businesses, and online stores",
                    "glance_option_3"     => "Shared hosting, WordPress hosting, VPS, and dedicated servers",
                    "glance_option_4"     => "WordPress installation, website management, and ecommerce tools",
                    "reviews_description" => "A reliable, beginner-friendly web hosting solution with flexible pricing tiers and 24/7 customer support.",
                    "pros_cons"           => [
                        "pro_ids" => [20, 22, 24],
                        "con_ids" => [21, 23]
                    ],
                    "features"            => [1007, 1008, 1012],
                    "services"            => [
                        "top_text"    => "Whether launching your first site or scaling enterprise operations, HostGator delivers flexible cloud servers and management.",
                        "bottom_text" => "Includes automated SSL certificates, email accounts, control panel administration, and daily website backups."
                    ],
                    "faqs"                => [
                        [
                            "category" => "Domains & Plans",
                            "question" => "Does HostGator provide a free domain name?",
                            "answer"   => "Yes, HostGator includes a free domain name for the first year with select annual hosting plans."
                        ],
                        [
                            "category" => "Migration & Setup",
                            "question" => "Can I easily migrate an existing site to HostGator?",
                            "answer"   => "Yes, HostGator offers free cPanel website migration assistance for qualifying new accounts."
                        ]
                    ],
                    "categories"          => [
                        "primary_category_id"    => 11,
                        "primary_subcategory_id" => null,
                        "category_ids"           => [11, 30],
                        "subcategory_ids"        => [19, 20, 23, 32, 33, 50]
                    ],
                    "active_countries"    => [
                        "active_all_countries" => true
                    ],
                    "rating"              => 4.7
                ],
                [
                    "business_id"         => 102,
                    "business_name"       => "CloudFlare & Enterprise Hosting (Primary Subcategory Example)",
                    "short_description"   => "High-performance edge cloud platform providing CDN, DNS, DDoS protection, and cloud server hosting.",
                    "long_description"    => "An integrated global cloud platform that delivers security, performance, and reliability across edge networks and cloud infrastructure.",
                    "usps"                => [
                        "Global Anycast Network across 300+ cities",
                        "Enterprise DDoS protection",
                        "Integrated Serverless Workers",
                        "Sub-millisecond DNS resolution"
                    ],
                    "what_is"             => "A unified cloud infrastructure and edge networking platform.",
                    "reviews_description" => "Industry-standard CDN and edge compute provider with robust enterprise security capabilities.",
                    "pros_cons"           => [
                        "pro_ids" => [25, 26],
                        "con_ids" => [27]
                    ],
                    "services"            => [
                        "top_text"    => "Secure regional infrastructure optimized for low latency and high availability.",
                        "bottom_text" => "Automated edge routing and enterprise threat intelligence."
                    ],
                    "faqs"                => [
                        [
                            "category" => "Coverage & Availability",
                            "question" => "Which countries do you operate in?",
                            "answer"   => "Services are distributed globally across more than 100 countries."
                        ]
                    ],
                    "categories"          => [
                        "primary_category_id"    => null,
                        "primary_subcategory_id" => 23,
                        "category_ids"           => [],
                        "subcategory_ids"        => [23, 20]
                    ],
                    "active_countries"    => [
                        "active_all_countries" => false,
                        "country_ids"          => [1, 2, 3, 4, 5]
                    ],
                    "rating"              => 4.8
                ]
            ]
        ];

        return json_encode($example, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }


    public static function getFeatureBatchResultExampleJson(): string
    {
        $example = [
            [
                "feature_id"   => 1007,
                "feature_name" => "Server resource isolation",
                "header"       => [
                    "page_title"  => "Top Web Hosting with Server Resource Isolation",
                    "description" => "Discover leading web hosting providers featuring dedicated server resource isolation for enhanced stability, security, and consistent high performance."
                ]
            ],
            [
                "feature_id"   => 1006,
                "feature_name" => "Control panel",
                "header"       => [
                    "page_title"  => "Best Web Hosting with Intuitive Control Panel",
                    "description" => "Compare top-tier hosting plans providing comprehensive control panel management, effortless database configuration, and 1-click script deployments."
                ]
            ]
        ];

        return json_encode($example, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }


    /**
     * Apply Pros and Cons to a Business strictly from existing CategoryProCon records.
     */
    public static function applyCategoriesToBusiness(Business $business, $catData): void
    {
        $primaryCatId = null;
        $secondaryCatIds = [];
        $subCatIds = [];

        if (is_string($catData)) {
            $catData = ['category_name' => $catData];
        } elseif (is_numeric($catData)) {
            $catData = ['category_id' => (int)$catData];
        }

        if (is_array($catData)) {
            // Check primary category vs subcategory as defined by user:
            // "primary_category_id": 11, "primary_subcategory_id": null
            // OR "primary_category_id": null, "primary_subcategory_id": 23
            $rawPrimaryCat = $catData['primary_category_id'] ?? null;
            $rawPrimarySub = $catData['primary_subcategory_id'] ?? null;

            if (!empty($rawPrimaryCat) && Category::where('id', $rawPrimaryCat)->exists()) {
                $primaryCatId = (int)$rawPrimaryCat;
            } elseif (!empty($rawPrimarySub) && Category::where('id', $rawPrimarySub)->exists()) {
                $primaryCatId = (int)$rawPrimarySub;
            }

            // category_ids: [11, 30]
            $cIds = $catData['category_ids'] ?? [];
            if (!empty($cIds)) {
                if (!is_array($cIds)) $cIds = [$cIds];
                foreach ($cIds as $cid) {
                    if (is_numeric($cid) && Category::where('id', $cid)->exists()) {
                        if ($primaryCatId === null) {
                            $primaryCatId = (int)$cid;
                        } else {
                            $secondaryCatIds[] = (int)$cid;
                        }
                    }
                }
            }

            // subcategory_ids: [19, 20, 23, ...]
            $subs = $catData['subcategory_ids'] ?? ($catData['subcategories'] ?? ($catData['subcategory_id'] ?? []));
            if (!is_array($subs)) $subs = [$subs];
            foreach ($subs as $sub) {
                if (is_numeric($sub) && Category::where('id', $sub)->exists()) {
                    if ($primaryCatId === null && !empty($rawPrimarySub)) {
                        $primaryCatId = (int)$sub;
                    } else {
                        $subCatIds[] = (int)$sub;
                    }
                } elseif (is_string($sub) && trim($sub) !== '') {
                    $matchedSub = \App\Models\CategoryTranslation::whereRaw('LOWER(name) = ?', [strtolower(trim($sub))])
                        ->value('category_id');
                    if ($matchedSub) $subCatIds[] = $matchedSub;
                }
            }

            // Fallback for previous formats: category_id or category_name
            if ($primaryCatId === null) {
                $rawCid = $catData['category_id'] ?? null;
                $catName = $catData['category_name'] ?? ($catData['category'] ?? null);

                if ($rawCid && Category::where('id', $rawCid)->exists()) {
                    $primaryCatId = (int)$rawCid;
                } elseif ($catName) {
                    $clean = trim($catName);
                    $matched = \App\Models\CategoryTranslation::whereRaw('LOWER(name) = ?', [strtolower($clean)])
                        ->orWhere('name', 'like', '%' . $clean . '%')
                        ->value('category_id');
                    if ($matched) {
                        $primaryCatId = (int)$matched;
                    }
                }
            }

            // Format: Array of category objects: [ {"category_id": 11, "subcategory_ids": [19, 20]}, ... ]
            if (array_is_list($catData)) {
                foreach ($catData as $entry) {
                    if (!is_array($entry)) {
                        if (is_numeric($entry) && Category::where('id', $entry)->exists()) {
                            if ($primaryCatId === null) $primaryCatId = (int)$entry;
                            else $secondaryCatIds[] = (int)$entry;
                        }
                        continue;
                    }
                    $cId = $entry['category_id'] ?? ($entry['id'] ?? null);
                    if ($cId && Category::where('id', $cId)->exists()) {
                        if ($primaryCatId === null) $primaryCatId = (int)$cId;
                        else $secondaryCatIds[] = (int)$cId;
                    }
                    $eSubs = $entry['subcategory_ids'] ?? ($entry['subcategories'] ?? []);
                    if (!is_array($eSubs)) $eSubs = [$eSubs];
                    foreach ($eSubs as $s) {
                        if (is_numeric($s) && Category::where('id', $s)->exists()) {
                            $subCatIds[] = (int)$s;
                        }
                    }
                }
            }
        }

        if ($primaryCatId) {
            $business->update(['category_id' => $primaryCatId]);
        }

        $allPivotIds = array_values(array_unique(array_filter(array_merge($secondaryCatIds, $subCatIds))));
        $business->subCategories()->sync($allPivotIds);
    }

    public static function applyProsConsToBusiness(Business $business, array $data): int
    {
        $businessId = $business->id;

        // Gather all category IDs the business belongs to
        $allCategoryIds = array_values(array_unique(array_filter(array_merge(
            [$business->category_id],
            $business->subCategories ? $business->subCategories->pluck('id')->toArray() : []
        ))));

        $rawItems = [];
        if (isset($data['pros_cons']) && is_array($data['pros_cons'])) {
            $rawItems = $data['pros_cons'];
        } elseif (isset($data[0]) && is_array($data[0])) {
            $rawItems = $data;
        }

        $rawPros = [];
        $rawCons = [];

        if (!empty($rawItems)) {
            foreach ($rawItems as $item) {
                if (!is_array($item)) continue;
                $t = strtolower($item['type'] ?? 'pro');
                if ($t === 'con') {
                    $rawCons[] = $item;
                } else {
                    $rawPros[] = $item;
                }
            }
        } else {
            $rawPros = $data['pro_ids'] ?? ($data['pros'] ?? []);
            $rawCons = $data['con_ids'] ?? ($data['cons'] ?? []);
        }

        $normalizeList = function($items, $defaultType) use ($allCategoryIds) {
            if (!is_array($items)) {
                $items = array_filter(array_map('trim', explode("\n", (string)$items)));
            }

            $results = [];
            foreach ($items as $item) {
                $id = null;
                $text = null;
                $description = null;
                $weight = 5;
                $aliases = [];

                if (is_array($item)) {
                    $id = $item['id'] ?? ($item['category_pro_con_id'] ?? null);
                    $text = $item['text'] ?? ($item['name'] ?? null);
                    $description = $item['description'] ?? ($item['desc'] ?? null);
                    if (isset($item['weight']) && is_numeric($item['weight'])) {
                        $weight = max(1, min(10, (int)$item['weight']));
                    }
                    if (isset($item['aliases']) && is_array($item['aliases'])) {
                        $aliases = $item['aliases'];
                    }
                } elseif (is_numeric($item)) {
                    $id = (int)$item;
                } elseif (is_string($item)) {
                    $text = trim($item);
                }

                $found = null;
                if ($id) {
                    $query = \App\Models\CategoryProCon::where('id', (int)$id)->where('type', $defaultType)->where('status', 1);
                    if (!empty($allCategoryIds)) {
                        $found = (clone $query)->whereIn('category_id', $allCategoryIds)->first();
                    }
                    if (!$found) {
                        $found = $query->first();
                    }
                }

                if (!$found && !empty($text)) {
                    $cleanText = trim($text);
                    $query = \App\Models\CategoryProCon::where('type', $defaultType)->where('status', 1);
                    if (!empty($allCategoryIds)) {
                        $found = (clone $query)->whereIn('category_id', $allCategoryIds)
                            ->where(function($q) use ($cleanText, $aliases) {
                                $q->whereRaw('LOWER(text) = ?', [strtolower($cleanText)])
                                  ->orWhereJsonContains('aliases', $cleanText);
                                foreach ($aliases as $al) {
                                    $q->orWhereRaw('LOWER(text) = ?', [strtolower($al)])
                                      ->orWhereJsonContains('aliases', $al);
                                }
                            })->first();
                    }
                    if (!$found) {
                        $found = $query->where(function($q) use ($cleanText, $aliases) {
                            $q->whereRaw('LOWER(text) = ?', [strtolower($cleanText)])
                              ->orWhereJsonContains('aliases', $cleanText);
                            foreach ($aliases as $al) {
                                $q->orWhereRaw('LOWER(text) = ?', [strtolower($al)])
                                  ->orWhereJsonContains('aliases', $al);
                            }
                        })->first();
                    }
                }

                $finalCatId = $found ? $found->id : ($id ?: null);
                $finalText = $text ?: ($found ? $found->text : '');
                $finalDesc = !empty($description) ? $description : ($found ? $found->description : null);

                if (!empty($finalText)) {
                    $results[] = [
                        'category_pro_con_id' => $finalCatId,
                        'type'                => $defaultType,
                        'text'                => $finalText,
                        'description'         => $finalDesc ?: null,
                        'weight'              => $weight,
                    ];
                }
            }

            // Order by importance: highest weight first (10 to 1)
            usort($results, fn($a, $b) => ($b['weight'] ?? 5) <=> ($a['weight'] ?? 5));
            return $results;
        };

        $matchedPros = $normalizeList($rawPros, 'pro');
        $matchedCons = $normalizeList($rawCons, 'con');

        \App\Models\BusinessProCon::where('business_id', $businessId)->delete();

        foreach ($matchedPros as $pro) {
            \App\Models\BusinessProCon::create([
                'business_id'         => $businessId,
                'category_pro_con_id' => $pro['category_pro_con_id'],
                'type'                => 'pro',
                'text'                => $pro['text'],
                'description'         => $pro['description'],
                'weight'              => $pro['weight'],
            ]);
        }

        foreach ($matchedCons as $con) {
            \App\Models\BusinessProCon::create([
                'business_id'         => $businessId,
                'category_pro_con_id' => $con['category_pro_con_id'],
                'type'                => 'con',
                'text'                => $con['text'],
                'description'         => $con['description'],
                'weight'              => $con['weight'],
            ]);
        }

        $business->pro_cons_intro = collect($matchedPros)->pluck('text')->implode("\n");
        $business->pro_cons_summary = collect($matchedCons)->pluck('text')->implode("\n");
        $business->save();

        return count($matchedPros) + count($matchedCons);
    }

    /**
     * Apply Features to a Business strictly from existing Feature records.
     */
    public static function applyFeaturesToBusiness(Business $business, $featuresInput): int
    {
        if (empty($featuresInput)) return 0;

        if (!is_array($featuresInput)) {
            $featuresInput = array_filter(array_map('trim', explode("\n", (string)$featuresInput)));
        }

        $categoryId = $business->category_id;
        $validFeatureIds = [];

        foreach ($featuresInput as $item) {
            $val = is_array($item) ? ($item['feature_id'] ?? ($item['id'] ?? ($item['name'] ?? null))) : $item;
            if (!$val) continue;

            if (is_numeric($val)) {
                $featId = (int)$val;
                $featQuery = Feature::where('id', $featId)->where('status', 'active');
                if ($categoryId && (clone $featQuery)->where('category_id', $categoryId)->exists()) {
                    $validFeatureIds[] = $featId;
                } elseif ($featQuery->exists()) {
                    $validFeatureIds[] = $featId;
                }
            } elseif (is_string($val) && trim($val) !== '') {
                $cleanName = trim($val);
                $featQuery = Feature::where('status', 'active')
                    ->whereHas('translations', function($t) use ($cleanName) {
                        $t->where('lang_id', 1)->where('name', 'like', $cleanName);
                    });
                if ($categoryId) {
                    $matched = (clone $featQuery)->where('category_id', $categoryId)->value('id');
                } else {
                    $matched = null;
                }
                if (!$matched) {
                    $matched = $featQuery->value('id');
                }
                if ($matched) {
                    $validFeatureIds[] = $matched;
                }
            }
        }

        $validFeatureIds = array_values(array_unique($validFeatureIds));
        if (!empty($validFeatureIds)) {
            $business->features()->sync($validFeatureIds);
            return count($validFeatureIds);
        }

        return 0;
    }
}
