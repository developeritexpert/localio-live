<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessFaqCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'position',
        'status'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function faqs()
    {
        return $this->hasMany(BusinessFaq::class, 'business_faq_category_id')->ordered();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }
}
