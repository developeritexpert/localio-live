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
        'description_title',
        'lang_id',
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
        'pro_cons_headline',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
