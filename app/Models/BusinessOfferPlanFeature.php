<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOfferPlanFeature extends Model
{
    use HasFactory;

    protected $table = "business_offer_plan_features";

    protected $guarded = [];

    protected $casts = [
        "is_featured_highlight" => "boolean",
    ];

    public function plan()
    {
        return $this->belongsTo(BusinessOfferPlan::class, "business_plan_id");
    }

    public function product()
    {
        return $this->belongsTo(BusinessOfferProduct::class, "business_product_id");
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, "feature_id");
    }

    public function getFeatureNameAttribute(): string
    {
        $langId = getCurrentLanguageID();
        $trans = FeatureTransalte::where("feature_id", $this->feature_id)
            ->where("lang_id", $langId)
            ->first();

        if (!$trans) {
            $trans = FeatureTransalte::where("feature_id", $this->feature_id)->first();
        }

        return $trans->name ?? ("Feature #" . $this->feature_id);
    }
}

