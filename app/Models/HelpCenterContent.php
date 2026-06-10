<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCenterContent extends Model
{
    use HasFactory;
    protected $fillable = [
        'lang_id',
        'banner_headline',
        'banner_description',
        'main_heading',
        'left_section_title',
        'left_section_description',
        'faq_section_title',
        'faq_section_description',
        'meta_title',
        'meta_description',
        'knowledge_base_title',
        'knowledge_base_description',
        'help_center_title',
        'help_center_description',
    ];
    
    public function categories(){
       return $this->hasMany(HelpCenterCategory::class, 'help_center_content_id');
    }
}
