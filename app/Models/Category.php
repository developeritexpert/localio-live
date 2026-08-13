<?php

namespace App\Models;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [ 'parent_id', 'image','category_icon', 'status','total_products','total_reviews', 'show_on_homepage', 'homepage_order', 'homepage_product_limit'];

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
            // First check if there is an existing criteria record with matching default_key or name
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

            if (!$criterion && $this->parent_id) {
                // Try inheriting default criterion description from parent
                $parentCriterion = CategoryRatingCriteria::where('category_id', $this->parent_id)
                    ->where(function ($q) use ($def) {
                        $q->where('default_key', $def->key)
                          ->orWhereRaw('LOWER(name) = ?', [strtolower($def->name)]);
                    })
                    ->first();

                if ($parentCriterion) {
                    $criterion = CategoryRatingCriteria::firstOrCreate(
                        [
                            'category_id' => $this->id,
                            'default_key' => $def->key,
                        ],
                        [
                            'name' => $def->name,
                            'description' => $parentCriterion->description,
                            'is_default' => true,
                        ]
                    );
                }
            }

            if (!$criterion) {
                // Create default criterion row for this category
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
    


}
