<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOfferPlan extends Model
{
    use HasFactory;

    protected $table = "business_offer_plans";

    protected $guarded = [];

    protected $casts = [
        "names_by_locale" => "array",
        "is_featured" => "boolean",
    ];

    public function product()
    {
        return $this->belongsTo(BusinessOfferProduct::class, "business_product_id");
    }

    public function prices()
    {
        return $this->hasMany(BusinessOfferPrice::class, "business_plan_id");
    }

    public function planFeatures()
    {
        return $this->hasMany(BusinessOfferPlanFeature::class, "business_plan_id")->orderBy("sort_order", "asc");
    }

    public function featuredHighlights()
    {
        return $this->hasMany(BusinessOfferPlanFeature::class, "business_plan_id")
            ->where("is_featured_highlight", true)
            ->orderBy("sort_order", "asc");
    }

    public function specifications()
    {
        return $this->hasMany(BusinessOfferSpecification::class, "business_plan_id");
    }

    public function getLocalizedNameAttribute(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        if (!empty($this->names_by_locale) && is_array($this->names_by_locale)) {
            if (isset($this->names_by_locale[$locale])) {
                return $this->names_by_locale[$locale];
            }
            $short = strtolower(explode("-", $locale)[0]);
            if (isset($this->names_by_locale[$short])) {
                return $this->names_by_locale[$short];
            }
        }
        return $this->name ?? "";
    }

    public function getPrimaryPriceAttribute()
    {
        return $this->prices()->where("is_active", true)->first();
    }
}

