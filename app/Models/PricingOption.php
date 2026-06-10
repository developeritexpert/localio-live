<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingOption extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function translations()
    {
        return $this->hasMany(PricingOptionTranslation::class);
    }
    public function translation($lang_id = null)
    {
        $lang_id = $lang_id ?? getCurrentLanguageID();
        return $this->translations()->where('lang_id', $lang_id)->first();
    }




    
    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_pricing_option');
    }
}
