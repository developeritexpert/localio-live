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

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name (via translation)
        if ($request->filled('search')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('lang_id', 1)->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $businesses = $query->orderBy('id', 'desc')->paginate(30)->withQueryString();

        // Load AI queue statuses for all businesses on this page
        $businessIds = $businesses->pluck('id')->toArray();
        $queueItems = AiContentQueue::where('resource_type', 'business')
            ->whereIn('resource_id', $businessIds)
            ->get()
            ->groupBy('resource_id');

        // Get latest batch job per business-field combination
        $fieldStatusMap = [];
        foreach ($queueItems as $businessId => $items) {
            foreach ($items as $item) {
                // Keep the latest entry per field
                if (!isset($fieldStatusMap[$businessId][$item->content_field]) ||
                    $item->updated_at > $fieldStatusMap[$businessId][$item->content_field]->updated_at) {
                    $fieldStatusMap[$businessId][$item->content_field] = $item;
                }
            }
        }

        $categories = Category::whereNull('parent_id')->orderBy('id')->get();
        $fields     = self::CONTENT_FIELDS;

        return view('Admin.content-generation.businesses', compact(
            'businesses', 'fieldStatusMap', 'categories', 'fields'
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
            return back()->with('error', 'No API key configured for ' . ucfirst($provider) . '. Please set it in AI Configuration.');
        }

        // Build batch requests
        $requests = [];
        $queueData = [];

        foreach ($businessIds as $businessId) {
            $business = Business::with(['translations' => fn($q) => $q->where('lang_id', 1)])->find($businessId);
            if (!$business) continue;

            $businessName = optional($business->translations->first())->name ?? 'Business #' . $businessId;
            $categoryName = optional($business->category)->name ?? '';

            foreach ($fields as $field) {
                // Get the prompt for this field type
                $prompt = $this->buildPromptForField($field, $businessName, $categoryName);
                if (!$prompt) continue;

                $customId = "biz_{$businessId}_{$field}";

                if ($provider === 'openai') {
                    $requests[] = [
                        'custom_id' => $customId,
                        'method'    => 'POST',
                        'url'       => '/v1/chat/completions',
                        'body'      => [
                            'model'       => $model,
                            'messages'    => [
                                ['role' => 'system', 'content' => 'You are a professional content writer for a business directory website. Write factual, engaging content.'],
                                ['role' => 'user', 'content' => $prompt],
                            ],
                            'max_tokens'  => 2000,
                            'temperature' => 0.7,
                        ],
                    ];
                } else {
                    // Anthropic
                    $requests[] = [
                        'custom_id' => $customId,
                        'params'    => [
                            'model'      => $model,
                            'max_tokens' => 2000,
                            'messages'   => [
                                ['role' => 'user', 'content' => $prompt],
                            ],
                        ],
                    ];
                }

                $queueData[] = [
                    'resource_type'    => 'business',
                    'resource_id'      => $businessId,
                    'content_field'    => $field,
                    'openai_request_id' => $customId,
                    'status'           => 'pending',
                ];
            }
        }

        if (empty($requests)) {
            return back()->with('error', 'No valid prompt templates found for the selected fields.');
        }

        // Submit to provider
        try {
            $batchId = $provider === 'openai'
                ? $this->submitOpenAIBatch($apiKey, $requests)
                : $this->submitAnthropicBatch($apiKey, $requests);

            // Save batch job
            $batchJob = AiBatchJob::create([
                'provider'     => $provider,
                'batch_id'     => $batchId,
                'status'       => 'processing',
                'submitted_at' => now(),
                'total_items'  => count($requests),
            ]);

            // Save queue items
            foreach ($queueData as $item) {
                AiContentQueue::create(array_merge($item, [
                    'batch_job_id' => $batchJob->id,
                    'status'       => 'processing',
                ]));
            }

            return back()->with('success', "Batch submitted successfully! Batch ID: {$batchId}. Results will be processed automatically when ready (usually within a few hours).");

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

        // Mark any existing failed items as retrying
        AiContentQueue::where('resource_type', 'business')
            ->where('resource_id', $request->business_id)
            ->where('content_field', $request->field)
            ->where('status', 'failed')
            ->delete();

        // Re-submit single item as a new batch
        return $this->submitBatch(new Request([
            'business_ids' => [$request->business_id],
            'fields'       => [$request->field],
            'provider'     => $request->provider,
        ]));
    }

    // -------------------------------------------------------------------------
    // BATCH JOBS STATUS PAGE
    // -------------------------------------------------------------------------
    public function batchJobs()
    {
        $jobs = AiBatchJob::with('queueItems')->orderBy('id', 'desc')->paginate(20);
        return view('Admin.content-generation.batch-jobs', compact('jobs'));
    }

    // -------------------------------------------------------------------------
    // POLL AND PROCESS BATCH RESULTS (called by scheduler)
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

    private function buildPromptForField(string $field, string $businessName, string $categoryName): ?string
    {
        // Look for a matching prompt in the ai_prompts table by type
        $prompt = AiPrompt::where('type', $field)->where('is_active', true)->first();

        if ($prompt) {
            return str_replace(
                ['{BusinessName}', '{business_name}', '{CategoryName}', '{category_name}'],
                [$businessName, $businessName, $categoryName, $categoryName],
                $prompt->original_prompt
            );
        }

        // Fallback prompts if none defined in admin
        return match ($field) {
            'content'   => "Write a comprehensive overview for {$businessName}, a {$categoryName} service. Include what it offers, who it's for, and its main strengths. Write 3-4 paragraphs in a professional, neutral tone.",
            'faqs'      => "Write 5 frequently asked questions (with answers) for {$businessName}, a {$categoryName} service. Format as Q: [question] A: [answer].",
            'features'  => "List the 6 most important features of {$businessName} ({$categoryName}). For each feature give a short title and 1-2 sentence description.",
            'pros_cons' => "List 5 pros and 4 cons for {$businessName} ({$categoryName}). Be honest and balanced.",
            'metatags'  => "Write an SEO meta title (max 60 chars) and meta description (max 155 chars) for {$businessName} ({$categoryName}). Format: TITLE: [title] DESCRIPTION: [description]",
            default     => null,
        };
    }

    private function submitOpenAIBatch(string $apiKey, array $requests): string
    {
        // Step 1: Create JSONL content
        $jsonl = '';
        foreach ($requests as $req) {
            $jsonl .= json_encode($req) . "\n";
        }

        // Step 2: Upload file
        $response = Http::withToken($apiKey)
            ->attach('file', $jsonl, 'batch_input.jsonl', ['Content-Type' => 'application/json'])
            ->post('https://api.openai.com/v1/files', [
                'purpose' => 'batch',
            ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI file upload failed: ' . $response->body());
        }

        $fileId = $response->json('id');

        // Step 3: Create batch
        $batchResponse = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/batches', [
                'input_file_id'    => $fileId,
                'endpoint'         => '/v1/chat/completions',
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
        ])->post('https://api.anthropic.com/v1/messages/batches', [
            'requests' => $requests,
        ]);

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

        if ($status === 'completed') {
            // Download output file
            $outputFileId = $data['output_file_id'] ?? null;
            if ($outputFileId) {
                $fileResponse = Http::withToken($apiKey)->get("https://api.openai.com/v1/files/{$outputFileId}/content");
                if ($fileResponse->successful()) {
                    $this->processOpenAIResults($job, $fileResponse->body());
                }
            }
            $job->update(['status' => 'completed', 'completed_at' => now(), 'raw_response' => json_encode($data)]);

        } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
            $job->update(['status' => 'failed', 'error_message' => "Batch ended with status: {$status}"]);
            $job->queueItems()->where('status', 'processing')->update(['status' => 'failed', 'error_message' => "Batch {$status}"]);
        }

        $job->update([
            'completed_items' => $data['request_counts']['completed'] ?? 0,
            'failed_items'    => $data['request_counts']['failed'] ?? 0,
        ]);
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

        if ($status === 'ended') {
            // Download results
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
            $job->update(['status' => 'completed', 'completed_at' => now(), 'raw_response' => json_encode($data)]);
        }

        $counts = $data['request_counts'] ?? [];
        $job->update([
            'completed_items' => ($counts['succeeded'] ?? 0),
            'failed_items'    => ($counts['errored'] ?? 0) + ($counts['expired'] ?? 0),
        ]);
    }

    private function processOpenAIResults(AiBatchJob $job, string $jsonlBody): void
    {
        $lines = explode("\n", trim($jsonlBody));
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $result = json_decode($line, true);
            if (!$result) continue;

            $customId = $result['custom_id'] ?? '';
            $content  = $result['response']['body']['choices'][0]['message']['content'] ?? null;
            $error    = $result['error']['message'] ?? null;

            $queueItem = $job->queueItems()->where('openai_request_id', $customId)->first();
            if (!$queueItem) continue;

            if ($content) {
                $queueItem->update(['status' => 'completed', 'result' => $content]);
            } else {
                $queueItem->update(['status' => 'failed', 'error_message' => $error ?? 'Unknown error']);
            }
        }
    }

    private function processAnthropicResults(AiBatchJob $job, string $jsonlBody): void
    {
        $lines = explode("\n", trim($jsonlBody));
        foreach ($lines as $line) {
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

    // -------------------------------------------------------------------------
    // VIEW SINGLE RESULT
    // -------------------------------------------------------------------------
    public function viewResult(\App\Models\AiContentQueue $item)
    {
        return view('Admin.content-generation.result', compact('item'));
    }

}
