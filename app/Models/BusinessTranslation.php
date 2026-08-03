<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTranslation extends Model
{
    use HasFactory;
    protected $table = 'business_translations';
    protected $fillable = [
        'name',
        'lang_id',
        'description_title',
        'description',
        'short_description',
        'after_image_description',
        'business_id',
        'headquarters',
        'support_options',
        'status',
        'slug',
        'primary_keywords',
        'secondary_keywords',
        'long_tail_keywords',
        'high_intent_keywords',
        'alternatives_title',
        'alternatives_description',
        'alternatives_title_2',
        'alternatives_description_2',
        'reviews_title',
        'reviews_description',
        'reviews_title_2',
        'reviews_description_2',
        'faqs_title',
        'faqs_description',
        'faqs_title_2',
        'faqs_description_2',
        'comparison_title',
        'comparison_description',
        'comparison_title_2',
        'comparison_description_2',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
