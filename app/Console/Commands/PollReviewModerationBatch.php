<?php

namespace App\Console\Commands;

use App\Models\AiBatchJob;
use App\Models\Review;
use App\Services\ReviewModerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PollReviewModerationBatch extends Command
{
    protected $signature   = 'moderation:poll-batch';
    protected $description = 'Poll OpenAI batch results for Layer 3 review moderation and apply outcomes.';

    public function handle(): int
    {
        $apiKey    = config('openai.api_key');
        $model     = config('openai.review_ai_model', 'gpt-5.6-luna');
        $promptId  = config('openai.review_prompt_id', '');
        $promptVer = config('openai.review_prompt_version', '1');

        if (!$apiKey) {
            $this->warn('OPENAI_API_KEY not set. Skipping batch poll.');
            return self::SUCCESS;
        }

        // Find all in-progress review moderation batch jobs
        $pendingJobs = AiBatchJob::where('prompt_type', 'review_moderation')
            ->where('status', 'processing')
            ->get();

        if ($pendingJobs->isEmpty()) {
            $this->info('No in-progress review moderation batch jobs found.');
            return self::SUCCESS;
        }

        foreach ($pendingJobs as $job) {
            $this->info("Polling batch: {$job->batch_id}");

            try {
                $response = Http::withToken($apiKey)
                    ->timeout(30)
                    ->get("https://api.openai.com/v1/batches/{$job->batch_id}");

                if (!$response->successful()) {
                    $this->error("Failed to poll batch {$job->batch_id}: " . $response->body());
                    continue;
                }

                $data   = $response->json();
                $status = $data['status'] ?? 'unknown';

                if ($status === 'completed') {
                    $this->info("Batch {$job->batch_id} completed. Processing results...");
                    $this->processCompletedBatch($job, $data, $apiKey, $model, $promptId, $promptVer);

                    $job->update([
                        'status'          => 'completed',
                        'completed_at'    => now(),
                        'completed_items' => $data['request_counts']['completed'] ?? 0,
                        'failed_items'    => $data['request_counts']['failed'] ?? 0,
                    ]);
                } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
                    $this->warn("Batch {$job->batch_id} has status: {$status}. Marking failed.");
                    $job->update(['status' => 'failed', 'error_message' => "Batch status: {$status}"]);

                    // Reviews stuck in AI_STATUS_PROCESSING should go to human review
                    Review::where('localio_ai_batch_id', $job->batch_id)
                        ->where('localio_ai_status', Review::AI_STATUS_PROCESSING)
                        ->update([
                            'localio_ai_status' => Review::AI_STATUS_DONE,
                            'status'            => Review::STATUS_PENDING_REVIEW,
                            'escalation_source' => Review::ESCALATION_MANUAL,
                            'localio_ai_reason' => "Batch {$status}. Requires human review.",
                        ]);
                } else {
                    $this->info("Batch {$job->batch_id} still {$status}. Will check again next poll.");
                }
            } catch (\Throwable $e) {
                $this->error("Exception polling batch {$job->batch_id}: " . $e->getMessage());
                Log::error("moderation:poll-batch exception: " . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }

    protected function processCompletedBatch(
        AiBatchJob $job,
        array $batchData,
        string $apiKey,
        string $model,
        string $promptId,
        string $promptVer
    ): void {
        $outputFileId = $batchData['output_file_id'] ?? null;

        if (!$outputFileId) {
            $this->warn("Batch {$job->batch_id} has no output_file_id.");
            return;
        }

        // Download the JSONL results file
        try {
            $fileResponse = Http::withToken($apiKey)
                ->timeout(60)
                ->get("https://api.openai.com/v1/files/{$outputFileId}/content");

            if (!$fileResponse->successful()) {
                $this->error("Failed to download output file {$outputFileId}: " . $fileResponse->body());
                return;
            }

            $lines = explode("\n", trim($fileResponse->body()));
        } catch (\Throwable $e) {
            Log::error("moderation:poll-batch failed to download results: " . $e->getMessage());
            return;
        }

        $processed = 0;
        $failed    = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $item = json_decode($line, true);
            if (!$item) continue;

            $customId = $item['custom_id'] ?? '';
            $reviewId = (int) str_replace('review-', '', $customId);

            if (!$reviewId) {
                $this->warn("Could not parse review ID from custom_id: {$customId}");
                $failed++;
                continue;
            }

            $review = Review::find($reviewId);
            if (!$review) {
                $this->warn("Review #{$reviewId} not found in database.");
                $failed++;
                continue;
            }

            // Extract GPT response
            // Responses API output format
            $responseBody = $item['response']['body'] ?? null;

            if (!$responseBody) {
                $this->warn("No response body for review #{$reviewId}. Sending to human review.");
                $review->update([
                    'localio_ai_status'    => Review::AI_STATUS_DONE,
                    'status'               => Review::STATUS_PENDING_REVIEW,
                    'escalation_source'    => Review::ESCALATION_LOCALIO_AI,
                    'localio_ai_reason'    => 'Empty response from AI batch.',
                ]);
                $failed++;
                continue;
            }

            // Parse structured JSON output from GPT
            $outputText = null;
            if (isset($responseBody['output'])) {
                foreach ($responseBody['output'] as $output) {
                    if (isset($output['content'])) {
                        foreach ($output['content'] as $contentItem) {
                            if (($contentItem['type'] ?? '') === 'output_text') {
                                $outputText = $contentItem['text'] ?? null;
                                break 2;
                            }
                        }
                    }
                }
            }

            if (!$outputText) {
                $this->warn("No output text for review #{$reviewId}.");
                $review->update([
                    'localio_ai_status'  => Review::AI_STATUS_DONE,
                    'status'             => Review::STATUS_PENDING_REVIEW,
                    'escalation_source'  => Review::ESCALATION_LOCALIO_AI,
                    'localio_ai_reason'  => 'Could not parse AI output.',
                ]);
                $failed++;
                continue;
            }

            // GPT returns structured JSON
            $gptResult = json_decode($outputText, true);

            if (!$gptResult || !isset($gptResult['decision'])) {
                $this->warn("Invalid JSON from GPT for review #{$reviewId}: {$outputText}");
                $review->update([
                    'localio_ai_status'  => Review::AI_STATUS_DONE,
                    'status'             => Review::STATUS_PENDING_REVIEW,
                    'escalation_source'  => Review::ESCALATION_LOCALIO_AI,
                    'localio_ai_reason'  => 'Invalid structured output from AI.',
                ]);
                $failed++;
                continue;
            }

            // Apply the result
            ReviewModerationService::applyLayer3(
                $review,
                $gptResult,
                $job->batch_id,
                $model,
                $promptId,
                $promptVer
            );

            $this->line("  ✓ Review #{$reviewId}: decision={$gptResult['decision']}, status={$review->fresh()->status}");
            $processed++;
        }

        $this->info("Batch {$job->batch_id} processed: {$processed} OK, {$failed} failed.");
        Log::info("moderation:poll-batch: Batch {$job->batch_id} done. {$processed} processed, {$failed} failed.");
    }
}
