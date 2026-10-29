<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FeatureTransalte;
use App\Models\Business;
use Illuminate\Support\Facades\Cache;

class Feature extends Model
{
    use HasFactory;
    protected $table = 'features';

    protected $guarded = [];

    /**
     * Request-level in-memory cache to prevent duplicate database lookups
     * when findBySlug is called repeatedly across middleware, controllers, and Livewire.
     */
    private static array $resolvedSlugs = [];

    public function translations()
    {
        return  $this->hasMany(FeatureTransalte::class, 'feature_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_feature');
    }
    public function reviews()
    {
        return $this->hasMany(FeatureBusinessReview::class, 'feature_id');
    }

    public static function findBySlug(string $slug, ?int $categoryId = null, ?int $langId = null): ?self
    {
        if (empty($slug)) {
            return null;
        }

        $langId = $langId ?: 1;
        $memKey = "{$slug}|" . ($categoryId ?? 'null') . "|{$langId}";

        // 1. In-memory per-request cache (0ms, zero external dependencies)
        if (array_key_exists($memKey, self::$resolvedSlugs)) {
            return self::$resolvedSlugs[$memKey];
        }

        // 2. Safe persistent cache (wrapped in try/catch: works seamlessly whether Redis/file cache exists or not)
        $cacheKey = "feat_slug:v1:{$langId}:" . ($categoryId ?? 0) . ":" . md5(strtolower($slug));
        try {
            $cachedId = Cache::get($cacheKey);
            if ($cachedId !== null) {
                if ($cachedId === 0) {
                    return self::$resolvedSlugs[$memKey] = null;
                }
                $cachedFeature = self::with(['translations' => fn($q) => $q->whereIn('lang_id', [$langId, 1])])
                    ->where('id', $cachedId)
                    ->first();
                if ($cachedFeature) {
                    return self::$resolvedSlugs[$memKey] = $cachedFeature;
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore cache driver errors/missing Redis
        }

        $targetSlug = \Illuminate\Support\Str::slug($slug);
        $cleanSlug = str_replace(['-', '.', '_', ' '], '', strtolower($slug));
        $nameWithSpaces = str_replace(['-', '_'], ' ', $slug);

        $query = self::query()->with(['translations' => function ($q) use ($langId) {
            $q->whereIn('lang_id', [$langId, 1]);
        }]);

        // Candidate category IDs (if category provided)
        $candidateCatIds = [];
        if ($categoryId) {
            $candidateCatIds[] = (int) $categoryId;
            $cat = Category::find($categoryId);
            if ($cat?->parent_id) {
                $candidateCatIds[] = (int) $cat->parent_id;
            }
        }

        // 1. Try querying features for this category hierarchy first (uses composite idx_features_cat_status)
        if (!empty($candidateCatIds)) {
            $catFeats = (clone $query)->whereIn('category_id', $candidateCatIds)->where('status', 'active')->get();
            $found = $catFeats->first(function ($f) use ($targetSlug, $cleanSlug) {
                return self::matchesSlug($f, $targetSlug, $cleanSlug);
            });
            if ($found) {
                self::safeCachePut($cacheKey, $found->id, 86400);
                return self::$resolvedSlugs[$memKey] = $found;
            }
        }

        // 2. Query with indexed SQL matching across all features (uses idx_ft_lang_name)
        $candidate = (clone $query)->where('status', 'active')->whereHas('translations', function ($q) use ($nameWithSpaces, $slug, $langId) {
            $q->whereIn('lang_id', [$langId, 1])
              ->where(function ($sub) use ($nameWithSpaces, $slug) {
                  $sub->where('name', $nameWithSpaces)
                      ->orWhere('name', $slug)
                      ->orWhere('name', 'LIKE', $nameWithSpaces . '%');
              });
        })->first();

        if ($candidate) {
            self::safeCachePut($cacheKey, $candidate->id, 86400);
            return self::$resolvedSlugs[$memKey] = $candidate;
        }

        // 3. Fallback matching without full-table memory scan
        $fallback = (clone $query)->where('status', 'active')->whereHas('translations', function ($q) use ($nameWithSpaces, $langId) {
            $q->whereIn('lang_id', [$langId, 1])
              ->where('name', 'LIKE', '%' . $nameWithSpaces . '%');
        })->first();

        if ($fallback) {
            self::safeCachePut($cacheKey, $fallback->id, 86400);
            return self::$resolvedSlugs[$memKey] = $fallback;
        }

        // Cache negative lookup (0) for 10 minutes to prevent repeated DB misses by bots/crawlers
        self::safeCachePut($cacheKey, 0, 600);
        return self::$resolvedSlugs[$memKey] = null;
    }

    private static function safeCachePut(string $key, int $val, int $ttl): void
    {
        try {
            Cache::put($key, $val, $ttl);
        } catch (\Throwable $e) {
            // Silently ignore cache write failures if Redis/cache not configured
        }
    }

    public static function matchesSlug(self $f, string $targetSlug, string $cleanSlug): bool
    {
        foreach ($f->translations as $t) {
            if (!empty($t->slug) && \Illuminate\Support\Str::slug($t->slug) === $targetSlug) {
                return true;
            }
            if (!empty($t->name)) {
                if (\Illuminate\Support\Str::slug($t->name) === $targetSlug) {
                    return true;
                }
                $cleanName = str_replace(['-', '.', '_', ' '], '', strtolower($t->name));
                if ($cleanName === $cleanSlug) {
                    return true;
                }
            }
        }
        if (!empty($f->name)) {
            if (\Illuminate\Support\Str::slug($f->name) === $targetSlug) {
                return true;
            }
            $cleanName = str_replace(['-', '.', '_', ' '], '', strtolower($f->name));
            if ($cleanName === $cleanSlug) {
                return true;
            }
        }
        return false;
    }
}