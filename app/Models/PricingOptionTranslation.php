<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingOptionTranslation extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $guarded = [];
    public function pricingOption()
    {
        return $this->belongsTo(PricingOption::class);
    }
}
