<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiBatchJob;
use App\Models\AiContentQueue;
use App\Models\AiPrompt;
use App\Models\Business;
use App\Models\BusinessFaq;
use App\Models\BusinessFaqTranslation;
use App\Models\BusinessTranslation;
use App\Models\BusinessUsp;
use App\Models\Category;
use App\Models\Feature;
use App\Models\CategoryFeatureContent;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        'pros_cons',
        'services',
        'faqs',
        'categories',
        'active_countries',
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

        return view('Admin.content-generation.features', compact(
            'features', 'fieldStatusMap', 'existingContents', 'categories', 'fields', 'lastPollAt'
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
            'provider'      => 'required|in:openai,anthropic',
        ]);

        $provider   = $request->provider;
        $featureIds = $request->feature_ids;
        $fields     = $request->fields;

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
            'provider'   => 'required|in:openai,anthropic',
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
        $jsonInput = $request->input('json_data');
        if (empty($jsonInput)) {
            return back()->with('error', 'Please provide JSON data with businesses.');
        }

        $decoded = json_decode($jsonInput, true);
        if (!is_array($decoded)) {
            return back()->with('error', 'Invalid JSON format. Please ensure you provide a valid JSON array of objects.');
        }

        // If a single object was passed instead of array, wrap it
        if (!empty($decoded) && !isset($decoded[0])) {
            $decoded = [$decoded];
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($decoded as $index => $item) {
            if (!is_array($item)) {
                $skipped++;
                continue;
            }

            // Flexible key resolution
            $name = trim($item['name'] ?? $item['business_name'] ?? $item['Business name'] ?? $item['businessName'] ?? '');
            $url  = trim($item['url'] ?? $item['business_url'] ?? $item['Business URL'] ?? $item['businessUrl'] ?? $item['website'] ?? $item['website_url'] ?? '');

            if (empty($name)) {
                $skipped++;
                $errors[] = "Item #" . ($index + 1) . " skipped: missing Business Name.";
                continue;
            }

            // Clean & format URL
            if (!empty($url) && !preg_match('/^https?:\/\//i', $url)) {
                $url = 'https://' . $url;
            }

            // Generate unique slug
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

            try {
                $business = Business::create([
                    'permanent_url'        => $url ?: null,
                    'status'               => 1,
                    'created_by'           => auth()->id() ?? 1,
                    'is_affiliate'         => 0,
                    'active_all_countries' => 1,
                    'category_id'          => null,
                ]);

                BusinessTranslation::create([
                    'business_id' => $business->id,
                    'lang_id'     => 1, // Default primary language (English)
                    'name'        => $name,
                    'slug'        => $slug,
                    'status'      => 1,
                ]);

                $imported++;
            } catch (\Exception $e) {
                Log::error("Failed to import business {$name}: " . $e->getMessage());
                $skipped++;
                $errors[] = "Error creating {$name}: " . $e->getMessage();
            }
        }

        $msg = "Successfully imported {$imported} " . ($imported === 1 ? 'business' : 'businesses') . "!";
        if ($skipped > 0) {
            $msg .= " ({$skipped} skipped/failed)";
        }

        return redirect()->route('content-gen.businesses')->with('success', $msg);
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

            foreach ($chunks as $chunk) {
                $businessesData = [];
                $businessModels = Business::with([
                    'translations' => fn($q) => $q->where('lang_id', 1),
                ])->whereIn('id', $chunk)->get();

                foreach ($chunk as $bizId) {
                    $b = $businessModels->firstWhere('id', $bizId);
                    if (!$b) continue;
                    $bName = optional($b->translations->first())->name ?? $b->name ?? 'Business #' . $bizId;
                    $bUrl  = $b->permanent_url ?: ($b->affiliate_link ?: '');
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
                    'instructions'     => 'For each business below, generate high quality directory content in valid JSON matching the schema.',
                    'fields_requested' => $fields,
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

            // Contextual pros/cons list for pros_cons field
            $availableProsStr = "- High reliability\n- Good value for money\n- Affordable pricing\n- Easy to use interface\n- 24/7 expert customer support\n- Fast performance\n- Comprehensive features";
            $availableConsStr = "- Steep learning curve\n- Limited advanced features\n- Hidden additional fees\n- Unclear pricing\n- Occasional support delays";
            if ($business->category_id) {
                $catProCons = \App\Models\CategoryProCon::where('category_id', $business->category_id)->get();
                if ($catProCons->isEmpty()) {
                    $catProCons = \App\Models\CategoryProCon::take(30)->get();
                }
                $prosList = $catProCons->where('type', 'pro')->pluck('text')->filter()->values()->toArray();
                $consList = $catProCons->where('type', 'con')->pluck('text')->filter()->values()->toArray();
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
            'provider'    => 'required|in:openai,anthropic',
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
        $jobs               = AiBatchJob::withCount('queueItems')->orderBy('id', 'desc')->paginate(20);
        $lastPollAt         = Cache::get('ai_batch_last_poll');
        $batchSize          = (int)(WebSetting::where('key', 'ai_batch_size')->value('value') ?: 5);
        $batchResultExample = self::getBatchResultExampleJson();

        return view('Admin.content-generation.batch-jobs', compact('jobs', 'lastPollAt', 'batchSize', 'batchResultExample'));
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

        $businessIds   = $items->pluck('resource_id')->unique()->toArray();
        $businessNames = BusinessTranslation::whereIn('business_id', $businessIds)
            ->where('lang_id', 1)
            ->pluck('name', 'business_id');

        return view('Admin.content-generation.batch-job-items', compact('job', 'items', 'businessNames'));
    }

    // -------------------------------------------------------------------------
    // VIEW SINGLE RESULT
    // -------------------------------------------------------------------------
    public function viewResult(AiContentQueue $item)
    {
        $businessName = BusinessTranslation::where('business_id', $item->resource_id)
            ->where('lang_id', 1)
            ->value('name') ?? 'Business #' . $item->resource_id;

        return view('Admin.content-generation.result', compact('item', 'businessName'));
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

            case 'pros_cons':
                $pros = $data['pros'] ?? [];
                $cons = $data['cons'] ?? [];

                // 1. Delete and populate business_pro_cons
                \App\Models\BusinessProCon::where('business_id', $businessId)->delete();
                if (is_array($pros)) {
                    foreach ($pros as $p) {
                        if (!empty(trim($p))) {
                            \App\Models\BusinessProCon::create([
                                'business_id' => $businessId,
                                'type'        => 'pro',
                                'text'        => trim($p),
                            ]);
                        }
                    }
                }
                if (is_array($cons)) {
                    foreach ($cons as $c) {
                        if (!empty(trim($c))) {
                            \App\Models\BusinessProCon::create([
                                'business_id' => $businessId,
                                'type'        => 'con',
                                'text'        => trim($c),
                            ]);
                        }
                    }
                }

                // 2. Also populate pro_cons_intro / pro_cons_summary on Business
                $prosText = is_array($pros) ? implode("\n", $pros) : (string) $pros;
                $consText = is_array($cons) ? implode("\n", $cons) : (string) $cons;
                Business::where('id', $businessId)->update([
                    'pro_cons_intro'   => $prosText,
                    'pro_cons_summary' => $consText,
                ]);
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

                        $bizFaq = BusinessFaq::create([
                            'business_id'              => $businessId,
                            'business_faq_category_id' => null,
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
                $catId = $data['category_id'] ?? null;
                $catName = $data['category_name'] ?? ($data['category'] ?? null);

                // If catId was provided but does not exist in DB, reset it to null
                if ($catId && !Category::where('id', $catId)->exists()) {
                    $catId = null;
                }

                // If catId is null, look up by category name in category_translations
                if (!$catId && $catName) {
                    $cleanCatName = trim($catName);
                    $matchedCatId = \App\Models\CategoryTranslation::whereRaw('LOWER(name) = ?', [strtolower($cleanCatName)])
                        ->orWhere('name', 'like', '%' . $cleanCatName . '%')
                        ->value('category_id');
                    if ($matchedCatId) {
                        $catId = $matchedCatId;
                    }
                }

                // If still not matched, check if business name indicates a primary category
                if (!$catId) {
                    $bizName = BusinessTranslation::where('business_id', $businessId)->value('name') ?? '';
                    if (stripos($bizName, 'host') !== false) {
                        $catId = \App\Models\CategoryTranslation::where('name', 'like', '%hosting%')
                            ->whereHas('category', fn($q) => $q->whereNull('parent_id'))
                            ->value('category_id');
                    }
                }

                if ($catId && Category::where('id', $catId)->exists()) {
                    Business::where('id', $businessId)->update(['category_id' => $catId]);
                }

                // Subcategories: can be array of IDs or array of names
                $subCatIds = $data['subcategory_ids'] ?? ($data['subcategories'] ?? []);
                if (is_array($subCatIds) && !empty($subCatIds)) {
                    $validSubs = [];
                    foreach ($subCatIds as $sub) {
                        if (is_numeric($sub) && Category::where('id', $sub)->exists()) {
                            $validSubs[] = (int) $sub;
                        } elseif (is_string($sub)) {
                            $matchedSub = \App\Models\CategoryTranslation::whereRaw('LOWER(name) = ?', [strtolower(trim($sub))])
                                ->value('category_id');
                            if ($matchedSub) {
                                $validSubs[] = $matchedSub;
                            }
                        }
                    }
                    $biz = Business::find($businessId);
                    if ($biz && !empty($validSubs)) {
                        $biz->subCategories()->sync($validSubs);
                    }
                }
                break;

            case 'active_countries':
                $activeAll = !empty($data['active_all_countries']);
                Business::where('id', $businessId)->update(['active_all_countries' => $activeAll ? 1 : 0]);

                if (!$activeAll && !empty($data['country_codes']) && is_array($data['country_codes'])) {
                    $countryIds = \App\Models\Country::whereIn('country_code', $data['country_codes'])->pluck('id')->toArray();
                    $biz = Business::find($businessId);
                    if ($biz) {
                        $biz->countries()->sync($countryIds);
                    }
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
                if (!isset($data['usps']) && !isset($data['features'])) {
                    return 'Expected {"usps": ["...", "..."]}';
                }
                break;
            case 'what_is':
                if (!isset($data['description']) && !isset($data['what_is']) && !isset($data['content'])) {
                    return 'Expected {"description": "..."}';
                }
                break;
            case 'pros_cons':
                if (!isset($data['pros']) || !isset($data['cons'])) {
                    return 'Expected {"pros": [...], "cons": [...]}';
                }
                break;
            case 'services':
                if (!isset($data['top_text']) && !isset($data['headline']) && !isset($data['bottom_text'])) {
                    return 'Expected {"headline": "...", "top_text": "...", "bottom_text": "..."}';
                }
                break;
            case 'faqs':
                if (!isset($data['faqs']) || !is_array($data['faqs'])) {
                    return 'Expected {"faqs": [{"question":..., "answer":...}]}';
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

            'pros_cons' => "Evaluate {$businessName}.{$catInfo}{$urlInfo} Select the 3 to 5 most accurate Pros and 2 to 4 Cons strictly from the available category options below:

Available Pros:
" . ($extra['available_pros'] ?? "- High reliability
- Good value for money
- Affordable pricing
- Easy to use
- 24/7 customer support") . "

Available Cons:
" . ($extra['available_cons'] ?? "- Steep learning curve
- Limited advanced features
- Hidden additional fees
- Unclear pricing") . "

Return JSON: {\"pros\": [\"Pro 1\", \"Pro 2\", \"Pro 3\"], \"cons\": [\"Con 1\", \"Con 2\"]}",

            'services' => "Write the Services section for {$businessName}.{$catInfo}{$urlInfo} which accompanies product screenshots. Provide: 1. A catchy section headline. 2. Top text: 1-2 paragraphs introducing the core platform and user experience before the screenshot tour. 3. Bottom text: 1-2 paragraphs elaborating on extended services, add-ons, or integrations after the screenshots. Return JSON: {\"headline\": \"...\", \"top_text\": \"<p>...</p>\", \"bottom_text\": \"<p>...</p>\"}",

            'faqs' => "Write 5 high-value FAQs for {$businessName}.{$catInfo}{$urlInfo} addressing common customer questions regarding plans, pricing, key features, setup, and support. Return JSON: {\"faqs\": [{\"question\": \"...\", \"answer\": \"...\"}]}",

            'categories' => "Analyze {$businessName}.{$urlInfo} Select the most suitable primary category ID and up to 3 subcategory IDs from the following list:
" . ($extra['category_list'] ?? "No category list provided") . "

Return JSON: {\"category_id\": 1, \"category_name\": \"...\", \"subcategory_ids\": [2, 3]}",

            'active_countries' => "Analyze {$businessName}.{$urlInfo} Determine whether this business operates globally (available in all countries) or is restricted to specific countries. Return JSON: {\"active_all_countries\": true, \"country_codes\": [\"US\", \"GB\", \"CA\", \"AU\", \"DE\"]}",

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
    // DOWNLOAD BATCH JSON (For ChatGPT Prompt)
    // -------------------------------------------------------------------------
    public function downloadBatchJson(AiBatchJob $job)
    {
        if (!empty($job->input_payload)) {
            $content = $job->input_payload;
        } else {
            $businessIds = $job->queueItems()->where('resource_type', 'business')->pluck('resource_id')->unique()->toArray();
            $businesses  = Business::with(['translations' => fn($q) => $q->where('lang_id', 1)])->whereIn('id', $businessIds)->get();
            $bList = [];
            foreach ($businesses as $b) {
                $bList[] = [
                    'business_id'   => $b->id,
                    'business_name' => optional($b->translations->first())->name ?? $b->name ?? 'Business #' . $b->id,
                    'business_url'  => $b->permanent_url ?: ($b->affiliate_link ?: ''),
                ];
            }
            $content = json_encode([
                'batch_id'         => $job->batch_id ?: ('BATCH-' . $job->id),
                'total_businesses' => count($bList),
                'instructions'     => 'For each business below, generate high quality directory content in valid JSON matching the schema.',
                'fields_requested' => explode(',', $job->prompt_type ?: implode(',', self::CONTENT_FIELDS)),
                'businesses'       => $bList,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $filename = 'batch_' . $job->id . '_' . ($job->batch_id ?: 'job') . '.json';

        return response($content, 200, [
            'Content-Type'        => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // -------------------------------------------------------------------------
    // UPLOAD BATCH RESULT JSON (From ChatGPT)
    // -------------------------------------------------------------------------
    public function uploadBatchResult(Request $request, AiBatchJob $job)
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

        foreach ($businessList as $bData) {
            if (!is_array($bData)) continue;

            $rawBizId = $bData['business_id'] ?? ($bData['id'] ?? null);
            $bizName  = trim($bData['business_name'] ?? ($bData['name'] ?? ''));

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

            // 5. Try matching globally by business name in translations
            if (!$biz && !empty($bizName)) {
                $matchedBizId = BusinessTranslation::where('name', $bizName)->value('business_id')
                    ?? BusinessTranslation::where('name', 'like', "%{$bizName}%")->value('business_id');
                if ($matchedBizId) {
                    $biz = Business::find($matchedBizId);
                }
            }

            if (!$biz) continue;
            $bizId = $biz->id;
            $appliedBusinessesCount++;

            // Process each of the 9 fields
            foreach (self::CONTENT_FIELDS as $field) {
                if (!isset($bData[$field])) continue;

                $fieldVal = $bData[$field];
                $normalizedData = is_array($fieldVal) ? $fieldVal : [$field => $fieldVal];

                if ($field === 'short_description' && is_string($fieldVal)) {
                    $normalizedData = ['short_description' => $fieldVal];
                } elseif ($field === 'long_description' && is_string($fieldVal)) {
                    $normalizedData = ['long_preview_description' => $fieldVal, 'long_description' => $fieldVal];
                } elseif ($field === 'what_is' && is_string($fieldVal)) {
                    $normalizedData = ['description' => $fieldVal, 'what_is' => $fieldVal];
                } elseif ($field === 'usps' && is_array($fieldVal)) {
                    $normalizedData = ['usps' => $fieldVal];
                } elseif ($field === 'faqs' && is_array($fieldVal)) {
                    $normalizedData = ['faqs' => $fieldVal];
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
                } catch (\Exception $e) {
                    Log::error("Failed to auto-apply field {$field} for business {$bizId}: " . $e->getMessage());
                }
            }
        }

        $job->update([
            'status'          => 'completed',
            'completed_items' => $appliedFieldsCount,
            'completed_at'    => now(),
            'raw_response'    => $cleanJson,
        ]);

        return back()->with('success', "Batch result processed successfully! Applied content for {$appliedBusinessesCount} business(es) across {$appliedFieldsCount} field(s).");
    }

    // -------------------------------------------------------------------------
    // EXAMPLE OUTPUT JSON FOR CHATGPT
    // -------------------------------------------------------------------------
    public static function getBatchResultExampleJson(): string
    {
        $example = [
            [
                "business_id"       => 101,
                "business_name"     => "HostGator",
                "short_description" => "HostGator is a leading provider of secure, scalable, and affordable web hosting solutions.",
                "long_description"  => "HostGator offers robust shared, VPS, and dedicated web hosting built for high performance and reliability.",
                "usps"              => [
                    "99.9% uptime guarantee",
                    "Free domain registration for the first year",
                    "24/7/365 phone, chat, and email support",
                    "One-click WordPress and CMS installs"
                ],
                "what_is"           => "HostGator is an established global web hosting provider and domain registrar serving businesses and web creators worldwide.",
                "pros_cons"         => [
                    "pros" => [
                        "Affordable introductory pricing",
                        "Unmetered bandwidth on all shared plans",
                        "Responsive 24/7 technical customer support",
                        "Generous 45-day money-back guarantee"
                    ],
                    "cons" => [
                        "Higher renewal pricing upon term expiration",
                        "Aggressive upsells during the checkout process",
                        "Advanced speed optimization limited to higher tiers"
                    ]
                ],
                "services"          => [
                    "headline"    => "Full-Service Web Hosting & Domain Management",
                    "top_text"    => "Whether launching your first site or scaling enterprise operations, HostGator delivers flexible cloud servers and management.",
                    "bottom_text" => "Includes automated SSL certificates, email accounts, control panel administration, and daily website backups."
                ],
                "faqs"              => [
                    [
                        "question" => "Does HostGator provide a free domain name?",
                        "answer"   => "Yes, HostGator includes a free domain name for the first year with select annual hosting plans."
                    ],
                    [
                        "question" => "Can I easily migrate an existing site to HostGator?",
                        "answer"   => "Yes, HostGator offers free cPanel website migration assistance for qualifying new accounts."
                    ]
                ],
                "categories"        => [
                    "category_id"     => 4,
                    "subcategory_ids" => [12, 15]
                ],
                "active_countries"  => [
                    "active_all_countries" => true,
                    "country_codes"        => ["US", "GB", "CA", "AU", "DE"]
                ]
            ]
        ];

        return json_encode($example, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

}
