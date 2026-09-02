<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiBatchJob;
use App\Models\AiContentQueue;
use App\Models\AiPrompt;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\Category;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentGenerationController extends Controller
{
    // The 5 content fields we can generate
    const CONTENT_FIELDS = ['content', 'faqs', 'features', 'pros_cons', 'metatags'];

    /*
    |==========================================================================
    | INPUT FORMAT sent to AI (as JSON variables):
    |
    |   {
    |     "business_name": "Bluehost",
    |     "category_name": "Web Hosting",
    |     "subcategory_name": "Shared Hosting"
    |   }
    |
    | EXPECTED OUTPUT FORMAT from AI (strict JSON):
    |
    |  content:
    |   { "content": "Full HTML or plain text overview of the business..." }
    |
    |  faqs:
    |   { "faqs": [{"question": "...", "answer": "..."}, ...] }
    |
    |  features:
    |   { "features": [{"title": "...", "description": "..."}, ...] }
    |
    |  pros_cons:
    |   { "pros": ["...", "..."], "cons": ["...", "..."] }
    |
    |  metatags:
    |   { "meta_title": "...", "meta_description": "..." }
    |==========================================================================
    */

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

        // Load AI queue statuses for all businesses on this page
        $businessIds = $businesses->pluck('id')->toArray();
        $queueItems  = AiContentQueue::where('resource_type', 'business')
            ->whereIn('resource_id', $businessIds)
            ->get()
            ->groupBy('resource_id');

        // Keep only the latest entry per field per business
        $fieldStatusMap = [];
        foreach ($queueItems as $businessId => $items) {
            foreach ($items as $item) {
                if (!isset($fieldStatusMap[$businessId][$item->content_field]) ||
                    $item->updated_at > $fieldStatusMap[$businessId][$item->content_field]->updated_at) {
                    $fieldStatusMap[$businessId][$item->content_field] = $item;
                }
            }
        }

        // Load configured prompts per field for the prompt ID status indicator
        $configuredPrompts = [];
        foreach (self::CONTENT_FIELDS as $field) {
            $p = AiPrompt::forContentField($field);
            $configuredPrompts[$field] = $p;
        }

        $categories = Category::whereNull('parent_id')->orderBy('id')->get();
        $fields     = self::CONTENT_FIELDS;

        return view('Admin.content-generation.businesses', compact(
            'businesses', 'fieldStatusMap', 'categories', 'fields', 'configuredPrompts'
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

        // Get API credentials
        $apiKey = $provider === 'openai'
            ? optional(WebSetting::where('key', 'openai_api_key')->first())->value
            : optional(WebSetting::where('key', 'anthropic_api_key')->first())->value;

        $model = $provider === 'openai'
            ? (optional(WebSetting::where('key', 'openai_default_model')->first())->value ?: 'gpt-4o')
            : (optional(WebSetting::where('key', 'anthropic_default_model')->first())->value ?: 'claude-opus-4-5');

        if (empty($apiKey)) {
            return back()->with('error', 'No API key configured for ' . ucfirst($provider) . '. Set it in AI Configuration.');
        }

        // Build batch requests
        $requests  = [];
        $queueData = [];

        foreach ($businessIds as $businessId) {
            $business = Business::with(['translations' => fn($q) => $q->where('lang_id', 1), 'category'])->find($businessId);
            if (!$business) continue;

            $businessName = optional($business->translations->first())->name ?? 'Business #' . $businessId;
            $categoryName = optional($business->category)->name ?? '';

            foreach ($fields as $field) {
                // Load the configured prompt for this field
                $prompt = AiPrompt::forContentField($field);

                $customId = "biz_{$businessId}_{$field}";

                // Input variables (JSON) sent to the AI
                $inputVariables = [
                    'business_name'    => $businessName,
                    'category_name'    => $categoryName,
                    'subcategory_name' => '',
                ];

                if ($provider === 'openai') {
                    if ($prompt && $prompt->hasOpenAIPromptId()) {
                        // === USE SAVED PROMPT ID (cheaper — no prompt text sent) ===
                        // Uses OpenAI Responses API with prompt reference
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
                        // === FALLBACK: send full prompt text (no saved prompt ID configured) ===
                        $promptText = $prompt
                            ? $prompt->getProcessedPrompt($inputVariables)
                            : $this->defaultPromptText($field, $businessName, $categoryName);

                        $requests[] = [
                            'custom_id' => $customId,
                            'method'    => 'POST',
                            'url'       => '/v1/chat/completions',
                            'body'      => [
                                'model'       => $model,
                                'messages'    => [
                                    ['role' => 'system', 'content' => 'You are a professional content writer for a business directory. Always respond with valid JSON only, no markdown, no explanation.'],
                                    ['role' => 'user', 'content' => $promptText],
                                ],
                                'max_tokens'      => 2000,
                                'temperature'     => 0.7,
                                'response_format' => ['type' => 'json_object'],
                            ],
                        ];
                    }
                } else {
                    // Anthropic
                    if ($prompt && $prompt->hasAnthropicPromptId()) {
                        // Anthropic prompt-by-ID (via beta prompt tools if available)
                        $requests[] = [
                            'custom_id' => $customId,
                            'params'    => [
                                'model'      => $model,
                                'max_tokens' => 2000,
                                'betas'      => ['prompt-caching-2024-07-31'],
                                'system'     => 'You are a professional content writer. Always respond with valid JSON only.',
                                'messages'   => [
                                    ['role' => 'user', 'content' => 'prompt_id:' . $prompt->anthropic_prompt_id . ' variables:' . json_encode($inputVariables)],
                                ],
                            ],
                        ];
                    } else {
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

            $batchJob = AiBatchJob::create([
                'provider'     => $provider,
                'batch_id'     => $batchId,
                'status'       => 'processing',
                'submitted_at' => now(),
                'total_items'  => count($requests),
            ]);

            foreach ($queueData as $item) {
                AiContentQueue::create(array_merge($item, ['batch_job_id' => $batchJob->id]));
            }

            return back()->with('success', "Batch submitted! ID: <code>{$batchId}</code>. Results will be processed automatically every 30 minutes. <a href='" . route('content-gen.batch-jobs') . "'>View batch jobs</a>");

        } catch (\Exception $e) {
            Log::error('Batch submission failed: ' . $e->getMessage());
            return back()->with('error', 'Batch submission failed: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // RETRY FAILED ITEMS
    // -------------------------------------------------------------------------
    public function retryFailed(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'field'       => 'required|string|in:content,faqs,features,pros_cons,metatags',
            'provider'    => 'required|in:openai,anthropic',
        ]);

        // Delete existing failed items so we start fresh
        AiContentQueue::where('resource_type', 'business')
            ->where('resource_id', $request->business_id)
            ->where('content_field', $request->field)
            ->where('status', 'failed')
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
        $jobs = AiBatchJob::withCount('queueItems')->orderBy('id', 'desc')->paginate(20);
        return view('Admin.content-generation.batch-jobs', compact('jobs'));
    }

    // -------------------------------------------------------------------------
    // VIEW SINGLE RESULT
    // -------------------------------------------------------------------------
    public function viewResult(AiContentQueue $item)
    {
        return view('Admin.content-generation.result', compact('item'));
    }

    // -------------------------------------------------------------------------
    // POLL BATCH RESULTS (called by scheduler every 30 min)
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
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

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

        // Upload JSONL file
        $fileResponse = Http::withToken($apiKey)
            ->attach('file', $jsonl, 'batch_input.jsonl', ['Content-Type' => 'application/json'])
            ->post('https://api.openai.com/v1/files', ['purpose' => 'batch']);

        if (!$fileResponse->successful()) {
            throw new \Exception('OpenAI file upload failed: ' . $fileResponse->body());
        }

        $fileId = $fileResponse->json('id');

        // Create batch
        $batchResponse = Http::withToken($apiKey)->post('https://api.openai.com/v1/batches', [
            'input_file_id'     => $fileId,
            'endpoint'          => '/v1/responses',  // Responses API supports prompt ID
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
            'x-api-key' => $apiKey, 'anthropic-version' => '2023-06-01',
            'anthropic-beta' => 'message-batches-2024-09-24',
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
                    'x-api-key' => $apiKey, 'anthropic-version' => '2023-06-01',
                    'anthropic-beta' => 'message-batches-2024-09-24',
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

            // Support both chat/completions and responses API output formats
            $content = $result['response']['body']['choices'][0]['message']['content']
                ?? $result['response']['body']['output'][0]['content'][0]['text']
                ?? null;
            $error = $result['error']['message'] ?? null;

            $queueItem = $job->queueItems()->where('openai_request_id', $customId)->first();
            if (!$queueItem) continue;

            if ($content) {
                // Try to parse JSON output — store raw if not valid JSON
                $parsed = json_decode($content, true);
                $queueItem->update(['status' => 'completed', 'result' => $content]);
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
                $queueItem->update(['status' => 'completed', 'result' => $content]);
            } else {
                $queueItem->update(['status' => 'failed', 'error_message' => $error]);
            }
        }
    }
}
