<?php

namespace App\Models;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [ 'parent_id', 'target_category_id', 'image','category_icon', 'top_rated_image', 'faq_image', 'status','total_products','total_reviews', 'show_on_homepage', 'homepage_order', 'homepage_product_limit', 'page_title_addition', 'top_rated_listing_headline_addition', 'category_overlay_image', 'category_overlay_position', 'faq_overlay_image', 'faq_overlay_position', 'comparison_overlay_image', 'comparison_overlay_position'];

    protected $lang_code, $lang_id;

    public function __construct()
    {
        $this->lang_id = session()->get('lang_id');
        $this->lang_code = session()->get('lang_code');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function targetCategory()
    {
        return $this->belongsTo(Category::class, 'target_category_id');
    }

    public function aliasSubCategories()
    {
        return $this->hasMany(Category::class, 'target_category_id');
    }

    public function isAlias(): bool
    {
        return !empty($this->target_category_id);
    }

    public function getEffectiveCategory(): Category
    {
        return ($this->target_category_id && $this->targetCategory) ? $this->targetCategory : $this;
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function scopeOnlyParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOnlySubcategories($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function ratingCriteria()
    {
        return $this->hasMany(CategoryRatingCriteria::class, 'category_id');
    }

    public function getEffectiveRatingCriteria()
    {
        $defaultMaster = \App\Models\DefaultRatingCriteria::orderBy('sort_order')->get();
        $effectiveCriteria = collect();

        // 1. Resolve 3 Default Criteria for this category
        foreach ($defaultMaster as $def) {
            if ($this->parent_id) {
                // Subcategory: Inherit default criterion description directly from parent category
                $parentCriterion = CategoryRatingCriteria::where('category_id', $this->parent_id)
                    ->where(function ($q) use ($def) {
                        $q->where('default_key', $def->key)
                          ->orWhereRaw('LOWER(name) = ?', [strtolower($def->name)]);
                    })
                    ->first();

                $description = ($parentCriterion && !empty($parentCriterion->description))
                    ? $parentCriterion->description
                    : $def->default_description;

                $criterion = CategoryRatingCriteria::where('category_id', $this->id)
                    ->where(function ($q) use ($def) {
                        $q->where('default_key', $def->key)
                          ->orWhereRaw('LOWER(name) = ?', [strtolower($def->name)]);
                    })
                    ->first();

                if ($criterion && !$criterion->default_key) {
                    $criterion->update([
                        'is_default' => true,
                        'default_key' => $def->key,
                        'name' => $def->name,
                    ]);
                }

                if (!$criterion) {
                    $criterion = CategoryRatingCriteria::firstOrCreate(
                        [
                            'category_id' => $this->id,
                            'default_key' => $def->key,
                        ],
                        [
                            'name' => $def->name,
                            'description' => $description,
                            'is_default' => true,
                        ]
                    );
                }

                if (empty($criterion->description)) {
                    $criterion->description = $description;
                }
            } else {
                // Main Category: Fetch or initialize local default criterion
                $criterion = CategoryRatingCriteria::where('category_id', $this->id)
                    ->where(function ($q) use ($def) {
                        $q->where('default_key', $def->key)
                          ->orWhereRaw('LOWER(name) = ?', [strtolower($def->name)]);
                    })
                    ->first();

                if ($criterion && !$criterion->default_key) {
                    $criterion->update([
                        'is_default' => true,
                        'default_key' => $def->key,
                        'name' => $def->name,
                    ]);
                }

                if (!$criterion) {
                    $criterion = CategoryRatingCriteria::firstOrCreate(
                        [
                            'category_id' => $this->id,
                            'default_key' => $def->key,
                        ],
                        [
                            'name' => $def->name,
                            'description' => $def->default_description,
                            'is_default' => true,
                        ]
                    );
                }
            }

            $effectiveCriteria->push($criterion);
        }

        // 2. Resolve Main Category Custom Criteria (if this is a subcategory)
        if ($this->parent_id) {
            $parentCustomCriteria = CategoryRatingCriteria::where('category_id', $this->parent_id)
                ->where('is_default', false)
                ->get();
            foreach ($parentCustomCriteria as $pCrit) {
                $pCrit->is_inherited = true;
                $effectiveCriteria->push($pCrit);
            }
        }

        // 3. Resolve Current Category Custom Criteria
        $ownCustomCriteria = CategoryRatingCriteria::where('category_id', $this->id)
            ->where('is_default', false)
            ->get();
        foreach ($ownCustomCriteria as $ownCrit) {
            $ownCrit->is_inherited = false;
            $effectiveCriteria->push($ownCrit);
        }

        return $effectiveCriteria;
    }

    public function proCons()
    {
        return $this->hasMany(CategoryProCon::class, 'category_id');
    }

    public function media()
{
    return $this->hasOne(Media::class, 'id', 'category_icon');
}

    public function topRatedImageMedia()
    {
        return $this->hasOne(Media::class, 'id', 'top_rated_image');
    }

    public function faqImageMedia()
    {
        return $this->hasOne(Media::class, 'id', 'faq_image');
    }

    public function categoryOverlayMedia()
    {
        return $this->hasOne(Media::class, 'id', 'category_overlay_image');
    }

    public function faqOverlayMedia()
    {
        return $this->hasOne(Media::class, 'id', 'faq_overlay_image');
    }

    public function comparisonOverlayMedia()
    {
        return $this->hasOne(Media::class, 'id', 'comparison_overlay_image');
    }

public function iconMedia()
{
    return $this->hasOne(Media::class, 'id', 'category_icon');
}

public function imageMedia()
{
    return $this->hasOne(Media::class, 'id', 'image');
}


    public function categoryTranslations()
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public function translation()
    {
        return $this->hasOne(CategoryTranslation::class, 'category_id');
    }
    public function businesses()
    {
        return $this->hasMany(Business::class, 'category_id');
    }
    public function businessesWithReviews()
    {
        return $this->hasMany(Business::class)
                    ->whereHas('reviews');
    }
    public function translations()
    {
        $category=  $this->hasOne(CategoryTranslation::class, 'category_id', 'id');

        if(!$category){
            $category=  $this->hasOne(CategoryTranslation::class, 'category_id', 'id');
        }
        return $category;
    }

    public function Gettranslations($lang_id)
    {
        $category=  $this->hasOne(CategoryTranslation::class, 'category_id', 'id')
        ->where('lang_id',$this->lang_id );
        if(!$category){
            $category=  $this->hasOne(CategoryTranslation::class, 'category_id', 'id')
         ->where('lang_id', 1);

        }
        return $category;
    }
    public function exclusiveDeals()
    {
        return $this->hasMany(ExclusiveDeal::class, 'applies_to_id')
            ->where('applies_to_type', 'category');
    }

    public function getNameAttribute($value)
    {
        $translation = $this->translation()->first();
        return $translation ? $translation->name : $value;
    }

    public function filters()
    {
        return $this->hasMany(Filter::class);
    }

    public function pricingOptions()
    {
        return $this->belongsToMany(PricingOption::class, 'category_pricing_option');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_products', 'category_id', 'product_id');
    }

    public function topics()
    {
        return $this->hasMany(BusinessCategoryTopic::class, 'category_id');
    }
    public function top_businesses()
{
    return $this->hasMany(Business::class, 'category_id')
        ->where('status', 1)
        ->limit(3);
}

    public function discussions()
    {
        return $this->hasMany(BusinessDiscussion::class, 'category_id')
            ->where('status', 'active')
            ->orderBy('is_pinned', 'desc')
            ->latest();
    }

    public function getListingsCount($lang_id = null, $country_id = null)
    {
        $catId = (int)$this->id;
        $parentId = !empty($this->parent_id) ? (int)$this->parent_id : null;

        $q = Business::where('status', 1);

        if ($parentId === null) {
            $childIds = self::where('parent_id', 'target_category_id', $catId)->pluck('id')->toArray();
            $allIds = array_merge([$catId], $childIds);
            $q->where(function ($sub) use ($allIds) {
                $sub->whereIn('category_id', $allIds)
                    ->orWhereHas('subCategories', function ($sq) use ($allIds) {
                        $sq->whereIn('categories.id', $allIds);
                    });
            });
        } else {
            $q->where(function ($sub) use ($catId, $parentId) {
                $sub->where('category_id', $catId)
                    ->orWhere('category_id', $parentId)
                    ->orWhereHas('subCategories', function ($sq) use ($catId) {
                        $sq->where('categories.id', $catId);
                    });
            });
        }

        $lang_id = $lang_id ?? (function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : null);
        if ($lang_id) {
            $q->where(function ($query) use ($lang_id) {
                $query->whereHas('languages', function ($lq) use ($lang_id) {
                    $lq->where('language_id', $lang_id);
                })->orWhereHas('translations', function ($tq) use ($lang_id) {
                    $tq->where('lang_id', $lang_id);
                });
            });
        }

        $country_id = $country_id ?? (function_exists('getCurrentCountry') ? getCurrentCountry() : null);
        if ($country_id) {
            $q->where(function ($query) use ($country_id) {
                $query->where('active_all_countries', 1)
                    ->orWhereHas('countries', function ($cq) use ($country_id) {
                        $cq->where('country_id', $country_id);
                    });
            });
        }

        return $q->count();
    }

    public function getListingsCountAttribute()
    {
        if (array_key_exists('listings_count', $this->attributes)) {
            return $this->attributes['listings_count'];
        }
        return $this->getListingsCount();
    }
    public function getGlanceLabels(?int $langId = null): array
    {
        $langId = $langId ?? (function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : 1);

        $trans = \App\Models\CategoryTranslation::where('category_id', $this->id)
            ->where('lang_id', $langId)
            ->first();
        if (!$trans && $langId !== 1) {
            $trans = \App\Models\CategoryTranslation::where('category_id', $this->id)
                ->where('lang_id', 1)
                ->first();
        }
        if (!$trans) {
            $trans = \App\Models\CategoryTranslation::where('category_id', $this->id)->first();
        }

        $option3 = (!empty($trans->glance_option_3_label) && trim($trans->glance_option_3_label) !== '')
            ? trim($trans->glance_option_3_label)
            : null;
        $option4 = (!empty($trans->glance_option_4_label) && trim($trans->glance_option_4_label) !== '')
            ? trim($trans->glance_option_4_label)
            : null;

        // If subcategory and either option is missing, inherit from parent category
        if (($option3 === null || $option4 === null) && $this->parent_id) {
            $parent = $this->relationLoaded('parent') ? $this->parent : \App\Models\Category::find($this->parent_id);
            if ($parent) {
                $parentTrans = \App\Models\CategoryTranslation::where('category_id', $parent->id)
                    ->where('lang_id', $langId)
                    ->first();
                if (!$parentTrans && $langId !== 1) {
                    $parentTrans = \App\Models\CategoryTranslation::where('category_id', $parent->id)
                        ->where('lang_id', 1)
                        ->first();
                }
                if (!$parentTrans) {
                    $parentTrans = \App\Models\CategoryTranslation::where('category_id', $parent->id)->first();
                }

                if ($parentTrans) {
                    if ($option3 === null && !empty($parentTrans->glance_option_3_label) && trim($parentTrans->glance_option_3_label) !== '') {
                        $option3 = trim($parentTrans->glance_option_3_label);
                    }
                    if ($option4 === null && !empty($parentTrans->glance_option_4_label) && trim($parentTrans->glance_option_4_label) !== '') {
                        $option4 = trim($parentTrans->glance_option_4_label);
                    }
                }
            }
        }

        return [
            'option_3' => $option3,
            'option_4' => $option4,
        ];
    }
}
