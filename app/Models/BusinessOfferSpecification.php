<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOfferSpecification extends Model
{
    use HasFactory;

    protected $table = "business_offer_specifications";

    protected $guarded = [];

    public function plan()
    {
        return $this->belongsTo(BusinessOfferPlan::class, "business_plan_id");
    }
}

