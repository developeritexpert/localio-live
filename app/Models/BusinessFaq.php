<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessFaq extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_id',
        'position',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function translations()
    {
        return $this->hasMany(BusinessFaqTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(BusinessFaqTranslation::class)->where('lang_id', getCurrentLanguageID());
    }

    // Get translation for specific language
    public function getTranslation($langId = null)
    {
        $langId = $langId ?? getCurrentLanguageID();
        return $this->translations()->where('lang_id', $langId)->first();
    }

    // Scope for active FAQs
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope for ordered FAQs
    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }
    
}
