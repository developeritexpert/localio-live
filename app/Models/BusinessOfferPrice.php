<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOfferPrice extends Model
{
    use HasFactory;

    protected $table = "business_offer_prices";

    protected $guarded = [];

    protected $casts = [
        "regular_price" => "float",
        "renewal_price" => "float",
        "intro_price" => "float",
        "is_active" => "boolean",
    ];

    public function plan()
    {
        return $this->belongsTo(BusinessOfferPlan::class, "business_plan_id");
    }

    public function product()
    {
        return $this->belongsTo(BusinessOfferProduct::class, "business_product_id");
    }

    public function getFormattedPriceAttribute(): string
    {
        $symbol = match ($this->currency) {
            "USD" => "$",
            "EUR" => "€",
            "GBP" => "£",
            default => $this->currency . " ",
        };

        if ($this->price_model === "free") {
            return "Free";
        }
        if ($this->price_model === "quote") {
            return "Contact for quote";
        }

        $formatted = number_format($this->regular_price, 2);
        return $symbol . $formatted;
    }

    public function getBillingPeriodLabelAttribute(): string
    {
        return match ($this->billing_period) {
            "1m" => "month",
            "3m" => "3 months",
            "6m" => "6 months",
            "12m" => "year",
            "24m" => "2 years",
            "36m" => "3 years",
            "1w" => "week",
            "1d" => "day",
            "once" => "one-time",
            "none" => "free",
            "custom" => ($this->custom_months ? $this->custom_months . " months" : "custom"),
            default => $this->billing_period,
        };
    }

    public function getFormattedRenewalPriceAttribute(): ?string
    {
        if ($this->renewal_price === null) {
            return null;
        }
        $symbol = match ($this->currency) {
            "USD" => "$",
            "EUR" => "€",
            "GBP" => "£",
            default => $this->currency . " ",
        };
        return $symbol . number_format($this->renewal_price, 2) . " / " . $this->billing_period_label;
    }
}

