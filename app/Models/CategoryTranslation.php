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
        'category_cap',
        'category_tc',
        'category_lc',
        'entity1_cap',
        'entity1_lc',
        'entities1_cap',
        'entities1_lc',
        'entity1_short_cap',
        'entity1_short_lc',
        'entities1_short_cap',
        'entities1_short_lc',
        'entity2_cap',
        'entity2_lc',
        'entities2_cap',
        'entities2_lc',
        'entity2_short_cap',
        'entity2_short_lc',
        'entities2_short_cap',
        'entities2_short_lc',
        'comparison_name',
        'glance_option_3_label',
        'glance_option_4_label',
        'see_all_text',
        'page_title',
        'title',
        'homepage_link_text',
        'description',
        'top_rated_description',
        'all_categories_description',
        'meta_title',
        'meta_description',
        'slug',
        'worth_it_content',
        'best_for_content',
        'integrations_content',
        'security_compliance_content',
        'comparison_slug',
        'text_sections',
        'faqs',
        'top_rated_listing_headline',
        'top_rated_listing_subtext',
        'compare_alternatives_headline',
        'compare_alternatives_subtext',
        'community_says_headline',
        'community_says_subtext',
        'explore_subcategories_headline',
        'explore_subcategories_subtext',
        'popular_comparisons_headline',
        'popular_comparisons_subtext',
        'compare_tool_headline',
        'compare_tool_subtext',
        'category_faqs_headline',
        'category_faqs_subtext',
        'faq_h1_title',
        'faq_subtext',
        'faq_about_headline',
        'faq_about_description',
        'faq_explore_headline',
        'faq_explore_subtext',
        'faq_meta_title',
        'faq_meta_description',
        'discussions_meta_title',
        'discussions_meta_description'
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
