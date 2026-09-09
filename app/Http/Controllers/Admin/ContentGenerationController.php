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

class ContentGenerationController extends Controller
{
    const CONTENT_FIELDS = ['content', 'faqs', 'features', 'pros_cons', 'metatags'];

    const FEATURE_CONTENT_FIELDS = [
        'header',
        'top_rated',
        'top_10',
        'pros_cons',
        'explore_comparisons',
        'metatags',
    ];

    public static array $featureFieldLabels = [
        'header'              => 'Header Text',
        'top_rated'           => 'Compare Top-Rated',
        'top_10'              => 'Top 10',
        'pros_cons'           => 'Pros & Cons',
        'explore_comparisons' => 'Explore & Comparisons',
        'metatags'            => 'Meta Tags',
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
            $query->where('status', $request->status);
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

        return view('Admin.content-generation.businesses', compact(
            'businesses', 'fieldStatusMap', 'categories', 'fields', 'configuredPrompts', 'lastPollAt'
        ));
    }

    // -------------------------------------------------------------------------
    // SUBMIT BATCH
    // -------------------------------------------------------------------------
    public function submitBatch(Request $request)
    {
        $request->validate([
            'business_ids'   => 'required|array|min:1',
            'business_ids.*' => 'integer|exists:businesses,id',
            'fields'         => 'required|array|min:1',
            'fields.*'       => 'string|in:content,faqs,features,pros_cons,metatags',
            'provider'       => 'required|in:openai,anthropic',
        ]);

        $provider    = $request->provider;
        $businessIds = $request->business_ids;
        $fields      = $request->fields;

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
            $categoryName    = optional($business->category)->name ?? '';
            $subcategoryName = $business->subCategories->pluck('name')->filter()->implode(', ');

            foreach ($fields as $field) {
                $prompt   = AiPrompt::forContentField($field);
                $customId = "biz_{$businessId}_{$field}";

                $inputVariables = [
                    'business_name'    => $businessName,
                    'category_name'    => $categoryName,
                    'subcategory_name' => $subcategoryName,
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
                            : $this->defaultPromptText($field, $businessName, $categoryName);

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
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'field'       => 'required|string|in:content,faqs,features,pros_cons,metatags',
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
        $jobs       = AiBatchJob::withCount('queueItems')->orderBy('id', 'desc')->paginate(20);
        $lastPollAt = Cache::get('ai_batch_last_poll');
        return view('Admin.content-generation.batch-jobs', compact('jobs', 'lastPollAt'));
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
            case 'content':
                $content = $data['content'] ?? null;
                if ($content !== null) {
                    BusinessTranslation::where('business_id', $businessId)
                        ->where('lang_id', 1)
                        ->update(['description' => $content]);
                }
                break;

            case 'faqs':
                $faqs = $data['faqs'] ?? [];
                if (!empty($faqs) && is_array($faqs)) {
                    // Remove all existing FAQs for this business before inserting AI-generated ones
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

            case 'features':
                // Store as USPs (business_usps): title + optional description combined
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

            case 'pros_cons':
                $pros = $data['pros'] ?? [];
                $cons = $data['cons'] ?? [];

                $prosText = is_array($pros) ? implode("\n", $pros) : (string) $pros;
                $consText = is_array($cons) ? implode("\n", $cons) : (string) $cons;

                Business::where('id', $businessId)->update([
                    'pro_cons_intro'   => $prosText,
                    'pro_cons_summary' => $consText,
                ]);
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
            case 'content':
                if (!isset($data['content']) || !is_string($data['content'])) {
                    return 'Expected {"content": "..."}, got: ' . substr($rawContent, 0, 100);
                }
                break;
            case 'faqs':
                if (!isset($data['faqs']) || !is_array($data['faqs'])) {
                    return 'Expected {"faqs": [{"question":..., "answer":...}]}, got: ' . substr($rawContent, 0, 100);
                }
                foreach ($data['faqs'] as $i => $faq) {
                    if (!isset($faq['question']) || !isset($faq['answer'])) {
                        return "FAQ item #{$i} missing 'question' or 'answer' key.";
                    }
                }
                break;
            case 'features':
                if (!isset($data['features']) || !is_array($data['features'])) {
                    return 'Expected {"features": [{"title":..., "description":...}]}, got: ' . substr($rawContent, 0, 100);
                }
                foreach ($data['features'] as $i => $feat) {
                    if (!isset($feat['title'])) {
                        return "Feature item #{$i} missing 'title' key.";
                    }
                }
                break;
            case 'pros_cons':
                if (!isset($data['pros']) || !isset($data['cons'])) {
                    return 'Expected {"pros": [...], "cons": [...]}, got: ' . substr($rawContent, 0, 100);
                }
                break;
            case 'metatags':
                if (!isset($data['meta_title']) && !isset($data['meta_description'])) {
                    return 'Expected {"meta_title":..., "meta_description":...}, got: ' . substr($rawContent, 0, 100);
                }
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

    private function defaultPromptText(string $field, string $businessName, string $categoryName): string
    {
        return match ($field) {
            'content'   => "Write a comprehensive overview for {$businessName} ({$categoryName}). Return JSON: {\"content\": \"...\"}",
            'faqs'      => "Write 5 FAQs for {$businessName} ({$categoryName}). Return JSON: {\"faqs\": [{\"question\": \"...\", \"answer\": \"...\"}]}",
            'features'  => "List 6 key features of {$businessName} ({$categoryName}). Return JSON: {\"features\": [{\"title\": \"...\", \"description\": \"...\"}]}",
            'pros_cons' => "Write pros and cons for {$businessName} ({$categoryName}). Return JSON: {\"pros\": [\"...\"], \"cons\": [\"...\"]}",
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
}
