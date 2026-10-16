<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOfferProduct extends Model
{
    use HasFactory;

    protected $table = "business_offer_products";

    protected $guarded = [];

    protected $casts = [
        "category_ids" => "array",
        "subcategory_ids" => "array",
        "names_by_locale" => "array",
        "has_plans" => "boolean",
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, "business_id");
    }

    public function plans()
    {
        return $this->hasMany(BusinessOfferPlan::class, "business_product_id")->orderBy("sort_order", "asc");
    }

    public function directPrices()
    {
        return $this->hasMany(BusinessOfferPrice::class, "business_product_id");
    }

    public function directFeatures()
    {
        return $this->hasMany(BusinessOfferPlanFeature::class, "business_product_id")->orderBy("sort_order", "asc");
    }

    public function getLocalizedNameAttribute(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        if (!empty($this->names_by_locale) && is_array($this->names_by_locale)) {
            // direct match
            if (isset($this->names_by_locale[$locale])) {
                return $this->names_by_locale[$locale];
            }
            // fallback without region: e.g. "fr" if "fr-be"
            $short = strtolower(explode("-", $locale)[0]);
            if (isset($this->names_by_locale[$short])) {
                return $this->names_by_locale[$short];
            }
        }
        return $this->name ?? "";
    }
}

