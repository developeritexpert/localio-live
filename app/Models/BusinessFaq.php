<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessFaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'business_faq_category_id',
        'position',
        'helpful_count',
        'not_helpful_count',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function category()
    {
        return $this->belongsTo(BusinessFaqCategory::class, 'business_faq_category_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(BusinessFaqFeedback::class, 'business_faq_id');
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

    // Scope for ordered by helpful score
    public function scopeOrderedByHelpful($query)
    {
        return $query->orderByRaw('(CAST(helpful_count AS SIGNED) - CAST(not_helpful_count AS SIGNED)) DESC')->orderBy('position', 'asc');
    }
}
