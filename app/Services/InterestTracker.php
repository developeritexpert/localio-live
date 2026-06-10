<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\UserActivity;
use App\Models\UserInterest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PHPUnit\TextUI\Configuration\Php;
use Psy\Readline\Hoa\Console;

class InterestTracker
{
    private $redis;
    
    public function __construct()
    {
        $this->redis = Redis::connection();
    }

    /**
     * Main tracking method with proper duplicate prevention
     */
    public function trackActivity($userId, $sessionId, $activityData)
    {
        // dd($userId, $sessionId, $activityData);
        // Skip tracking for irrelevant activities
        if (!$this->shouldTrackActivity($activityData)) {
            Log::info('Skipping irrelevant activity', ['activity' => $activityData]);
            return;
        }

        // Prevent duplicate activities
        if ($this->isDuplicateActivity($userId, $sessionId, $activityData)) {
            Log::info('Skipping duplicate activity', [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'activity_type' => $activityData['type']
            ]);
            return;
        }

        $key = "user_activity:{$userId}:{$sessionId}";
    
        $activity = [
            'user_id' => $userId,
            'session_id' => $sessionId,
            'activity_type' => $activityData['type'],
            'product_id' => $activityData['product_id'] ?? null,
            'category_id' => $activityData['category_id'] ?? null,
            'business_id' => $activityData['business_id'] ?? null,
            'duration' => $activityData['duration'] ?? 0,
            'timestamp' => now()->timestamp,
            'metadata' => $activityData['metadata'] ?? []
        ];
    
        try {
            // Store in Redis for temporary tracking
            $this->redis->lpush($key, json_encode($activity));
            $this->redis->expire($key, 86400);
    
            // Store in database
            UserActivity::create($activity);
    
            // Update interest scores
            $this->updateInterestScores($userId, $activity);
    
            Log::info('Activity tracked successfully', [
                'user_id' => $userId,
                'activity_type' => $activity['activity_type'],
                'category_id' => $activity['category_id'],
                'product_id' => $activity['product_id']
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error in trackActivity: ' . $e->getMessage(), [
                'activity' => $activity,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function shouldTrackActivity($activityData)
    {
        // Must have at least one relevant ID
        if (!isset($activityData['product_id']) && 
            !isset($activityData['category_id']) && 
            !isset($activityData['business_id'])) {
            return false;
        }

        // At least one ID must be non-null
        if (empty($activityData['product_id']) && 
            empty($activityData['category_id']) && 
            empty($activityData['business_id'])) {
            return false;
        }

        // Don't track very short engagement times
        if ($activityData['type'] === 'engagement' && 
            isset($activityData['duration']) && 
            $activityData['duration'] < 10) {
            return false;
        }

        return true;
    }

    private function isDuplicateActivity($userId, $sessionId, $activityData)
    {
        // Create unique activity signature
        $signature = md5(serialize([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'type' => $activityData['type'],
            'product_id' => $activityData['product_id'] ?? null,
            'category_id' => $activityData['category_id'] ?? null,
            'business_id' => $activityData['business_id'] ?? null,
        ]));

        $duplicateKey = "activity_duplicate:{$signature}";
        
        // Check if this exact activity happened recently
        if ($this->redis->exists($duplicateKey)) {
            return true; // This is a duplicate
        }
        
        // Mark this activity to prevent duplicates for next 5 minutes
        $this->redis->setex($duplicateKey, 300, 1);
        return false;
    }

    private function updateInterestScores($userId, $activity)
    {
        if (!$userId) return;
    
        $scores = $this->calculateScores($activity);
        
        foreach ($scores as $type => $items) {
            foreach ($items as $itemId => $score) {
                if (!is_numeric($score) || $score <= 0) {
                    continue;
                }
            
                $key = "user_interest:{$userId}:{$type}:{$itemId}";
                
                // Get current score
                $currentScore = $this->redis->get($key) ?: 0;
                $newScore = floatval($currentScore) + floatval($score);
                
                // Set new score
                $this->redis->set($key, $newScore);
                $this->redis->expire($key, 2592000); // 30 days
                
                Log::info('Updated interest score', [
                    'user_id' => $userId,
                    'type' => $type,
                    'item_id' => $itemId,
                    'old_score' => $currentScore,
                    'added_score' => $score,
                    'new_score' => $newScore
                ]);
            }
        }
    
        // Schedule sync job with delay to batch updates
        \App\Jobs\SyncUserInterests::dispatch($userId)->delay(now()->addMinutes(2));
    }
    
    private function calculateScores($activity)
    {
        $scores = [];
        
        $weights = [
            'view' => 1,
            'click' => 2,
            'engagement' => 3,
            'wishlist' => 5,
            'purchase' => 10,
            'share' => 3
        ];

        $baseScore = $weights[$activity['activity_type']] ?? 1;

        // Engagement bonus
        if ($activity['activity_type'] === 'engagement' && $activity['duration'] > 0) {
            $engagementBonus = min($activity['duration'] / 60, 2); // Max 2x bonus
            $baseScore *= (1 + $engagementBonus);
            
            // High engagement ratio bonus
            if (isset($activity['metadata']['engagement_ratio']) && 
                $activity['metadata']['engagement_ratio'] > 0.5) {
                $baseScore *= 1.2;
            }
        }

        // Assign scores to different interest types
        if (!empty($activity['product_id'])) {
            $scores['product'][$activity['product_id']] = $baseScore;
        }
        if (!empty($activity['category_id'])) {
            $scores['category'][$activity['category_id']] = $baseScore * 0.7;
        }
        if (!empty($activity['business_id'])) {
            $scores['business'][$activity['business_id']] = $baseScore * 0.8;
        }

        return $scores;
    }

    public function getUserInterests($userId, $type = null, $limit = 20)
    {
        if (!$userId) return [];

        $pattern = $type ? "user_interest:{$userId}:{$type}:*" : "user_interest:{$userId}:*:*";
        $keys = $this->redis->keys($pattern);

        $interests = [];
        foreach ($keys as $key) {
            $score = $this->redis->get($key);
            if ($score > 0.5) { // Only meaningful scores
                $parts = explode(':', $key);
                if (count($parts) >= 4) {
                    $interests[] = [
                        'type' => $parts[2],
                        'id' => $parts[3],
                        'score' => (float) $score
                    ];
                }
            }
        }

        usort($interests, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_slice($interests, 0, $limit);
    }

    /**
     * Clear all Redis data for a user (useful for testing/reset)
     */
    public function clearUserData($userId)
    {
        $patterns = [
            "user_interest:{$userId}:*:*",
            "user_activity:{$userId}:*",
            "activity_duplicate:*"
        ];

        foreach ($patterns as $pattern) {
            $keys = $this->redis->keys($pattern);
            if (!empty($keys)) {
                $this->redis->del($keys);
            }
        }
        return ['Cleared Redis data for user', ['user_id' => $userId]];
    }

    public function deleteSpecificDeal($userId, $dealId, $interestType)
    {
        // Construct the exact Redis key (not pattern)
        $key = "user_interest:{$userId}:{$interestType}:{$dealId}";
        // dd($key);
        // Delete Redis key
        if ($this->redis->exists($key)) {
            $this->redis->del($key);
        }
    
        $deleted = UserInterest::where('user_id', $userId)
            ->where('interest_id', $dealId)
            ->where('interest_type', $interestType)
            ->delete();
    
        return $deleted;
    }
    

    // Your existing deal methods remain the same...
    public function getPersonalizedDeals($interests, $userId, $limit = 20)
    {
        // Return empty if no interests provided - don't fall back to other deals
        if (empty($interests)) {
            return collect([]);
        }
    
        $productIds = [];
        $categoryIds = [];
        $businessIds = [];
    
        foreach ($interests as $interest) {
            switch ($interest['type']) {
                case 'product':
                    $productIds[] = $interest['id'];
                    break;
                case 'category':
                    $categoryIds[] = $interest['id'];
                    break;
                case 'business':
                    $businessIds[] = $interest['id'];
                    break;
            }
        }
    
        // Only proceed if we have at least one type of interest
        if (empty($productIds) && empty($categoryIds) && empty($businessIds)) {
            return collect([]);
        }
    
        $dealsQuery = \App\Models\ProductPrice::with(['product.businesses.reviews', 'product.categories'])
            ->whereNotNull('discount_price')
            ->where('discount_expiration_date', '>', now())
            ->where(function ($query) use ($productIds, $categoryIds, $businessIds) {
                $hasConditions = false;
                
                // Only add conditions if we have IDs for that type
                if (!empty($productIds)) {
                    $query->whereIn('product_id', $productIds);
                    $hasConditions = true;
                }
        
                if (!empty($categoryIds)) {
                    if ($hasConditions) {
                        $query->orWhereHas('product.categories', function ($q) use ($categoryIds) {
                            $q->whereIn('categories.id', $categoryIds);
                        });
                    } else {
                        $query->whereHas('product.categories', function ($q) use ($categoryIds) {
                            $q->whereIn('categories.id', $categoryIds);
                        });
                        $hasConditions = true;
                    }
                }
        
                if (!empty($businessIds)) {
                    if ($hasConditions) {
                        $query->orWhereHas('product.businesses', function ($q) use ($businessIds) {
                            $q->whereIn('businesses.id', $businessIds);
                        });
                    } else {
                        $query->whereHas('product.businesses', function ($q) use ($businessIds) {
                            $q->whereIn('businesses.id', $businessIds);
                        });
                    }
                }
            })
            ->orderByRaw('RAND()')
            ->limit($limit);
    
        $deals = $dealsQuery->get();
    
        // NO fallback deals - only return what matches user interests
        
        return $deals->map(function ($deal) {
            $product = $deal->product;
        
            return [
                'product_price_id' => $deal->id,
                'original_price' => $deal->price,
                'discounted_price' => $deal->discount_price,
                'currency' => $deal->currency,
                'discount_expiration_date' => $deal->discount_expiration_date,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->product_image,
                    'categories' => $product->categories->pluck('name')->toArray(),
                ],
                'businesses' => $product->businesses->map(function ($b) {
                    $websiteUrl = $b->websites->first()?->website_url;
                    
                    $reviews = $b->reviews;
                    $totalReviews = $reviews->count();
                    $averageRating = $totalReviews ? round($reviews->avg('rating'), 1) : null;
    
                    return [
                        'id' => $b->id,
                        'icon_id' => $b->icon_id,
                        'name' => $b->translations->first()?->name,
                        'description' =>$b->translations->first()?->description,
                        'logo' => $b->icon_id,
                        'link' => $websiteUrl 
                                    ?? $b->affiliate_link 
                                    ?? $b->permanent_url,
                        'reviews' => [
                            'count' => $totalReviews,
                            'average' => $averageRating,
                        ],
                    ];
                })->toArray(),
                'is_personalized' => true
            ];
        })->values();
    }
    

    // public function getFallbackDeals($limit)
    // {
    //     return \App\Models\ProductPrice::with(['product.business', 'product.category'])
    //         ->whereNotNull('discount_price')
    //         ->where('discount_expiration_date', '>', now())
    //         ->orderBy('discount_expiration_date')
    //         ->limit($limit)
    //         ->get();
    // }
}