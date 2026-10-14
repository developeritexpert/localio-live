<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FeatureTransalte;
use App\Models\Business;

class Feature extends Model
{
    use HasFactory;
    protected $table = 'features';

    protected $guarded = [];

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

        $targetSlug = \Illuminate\Support\Str::slug($slug);
        $cleanSlug = str_replace(['-', '.', '_', ' '], '', strtolower($slug));

        $query = self::query()->with(['translations' => function ($q) use ($langId) {
            if ($langId) {
                $q->where('lang_id', $langId)->orWhereRaw('1=1');
            }
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

        // 1. If category candidate IDs exist, try querying features for this category hierarchy first
        if (!empty($candidateCatIds)) {
            $catFeats = (clone $query)->whereIn('category_id', $candidateCatIds)->get();
            $found = $catFeats->first(function ($f) use ($targetSlug, $cleanSlug) {
                return self::matchesSlug($f, $targetSlug, $cleanSlug);
            });
            if ($found) {
                return $found;
            }
        }

        // 2. Query with SQL matching conditions across all features
        $candidate = (clone $query)->whereHas('translations', function ($q) use ($slug, $cleanSlug) {
            $q->where('name', 'LIKE', str_replace('-', ' ', $slug))
                ->orWhere('name', 'LIKE', $slug)
                ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(LOWER(name), '-', ''), '.', ''), '_', ''), ' ', '') = ?", [$cleanSlug]);
        })->first();

        if ($candidate) {
            return $candidate;
        }

        // 3. Fallback: scan features and compare using Str::slug
        $allFeats = (clone $query)->get();
        return $allFeats->first(function ($f) use ($targetSlug, $cleanSlug) {
            return self::matchesSlug($f, $targetSlug, $cleanSlug);
        });
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
