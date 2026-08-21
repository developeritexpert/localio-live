<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'lang_id',
        'status',
        'is_important',
        'name',
        'comparison_name',
        'page_title',
        'title',
        'homepage_link_text',
        'description',
        'meta_title',
        'meta_description',
        'slug',
        'worth_it_content',
        'best_for_content',
        'integrations_content',
        'security_compliance_content',
        'comparison_slug',
        'text_sections',
        'faqs'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'lang_id');
    }
}
