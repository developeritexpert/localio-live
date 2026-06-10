<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use App\Models\UserInterest;
use Illuminate\Support\Facades\Log;

class SyncUserInterests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $redis = Redis::connection();
            $pattern = "user_interest:{$this->userId}:*:*";
            $keys = $redis->keys($pattern);
            
            Log::info('Starting SyncUserInterests', [
                'user_id' => $this->userId,
                'total_redis_keys' => count($keys)
            ]);
            
            if (empty($keys)) {
                Log::info('No Redis keys found for user', ['user_id' => $this->userId]);
                return;
            }
            
            $processedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;
            
            // Process in batches to avoid memory issues
            $keyBatches = array_chunk($keys, 50);
            
            foreach ($keyBatches as $keyBatch) {
                foreach ($keyBatch as $key) {
                    try {
                        $result = $this->processRedisKey($redis, $key);
                        
                        if ($result === 'processed') {
                            $processedCount++;
                        } elseif ($result === 'skipped') {
                            $skippedCount++;
                        }
                        
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::error('Error processing individual Redis key', [
                            'user_id' => $this->userId,
                            'key' => $key,
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                }
            }
            
            Log::info('SyncUserInterests completed', [
                'user_id' => $this->userId,
                'total_keys' => count($keys),
                'processed' => $processedCount,
                'skipped' => $skippedCount,
                'errors' => $errorCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('SyncUserInterests job failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function processRedisKey($redis, $key)
    {
        $score = $redis->get($key);
        
        // Validate score
        if ($score === null || $score === false || $score === '' || !is_numeric($score)) {
            Log::debug('Skipping invalid Redis score', [
                'key' => $key,
                'score' => $score,
                'type' => gettype($score)
            ]);
            return 'skipped';
        }
        
        // Parse key
        $parts = explode(':', $key);
        if (count($parts) < 4) {
            Log::warning('Invalid Redis key format', ['key' => $key]);
            return 'skipped';
        }
        
        $interestType = $parts[2];
        $interestId = $parts[3];
        $numericScore = floatval($score);
        
        // Skip very low scores
        if ($numericScore <= 0.1) {
            Log::debug('Skipping low score', [
                'key' => $key,
                'score' => $numericScore
            ]);
            return 'skipped';
        }

        // Validate interest type and ID
        if (!in_array($interestType, ['product', 'category', 'business'])) {
            Log::warning('Invalid interest type', [
                'key' => $key,
                'type' => $interestType
            ]);
            return 'skipped';
        }

        if (!is_numeric($interestId) || $interestId <= 0) {
            Log::warning('Invalid interest ID', [
                'key' => $key,
                'interest_id' => $interestId
            ]);
            return 'skipped';
        }

        // Update or create user interest
        $userInterest = UserInterest::updateOrCreate(
            [
                'user_id' => $this->userId,
                'interest_type' => $interestType,
                'interest_id' => intval($interestId)
            ],
            [
                'score' => $numericScore,
                'last_updated' => now()
            ]
        );
        
        Log::debug('Processed user interest', [
            'key' => $key,
            'user_interest_id' => $userInterest->id,
            'score' => $numericScore,
            'was_created' => $userInterest->wasRecentlyCreated
        ]);
        
        return 'processed';
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Log::error('SyncUserInterests job failed permanently', [
            'user_id' => $this->userId,
            'exception' => $exception->getMessage()
        ]);
    }
}