<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetListed extends Model
{
    use HasFactory;

    // Table name (if different from default plural form)
    protected $table = 'get_listed';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'lang_id',

        'banner_heading',
        'banner_sub_heading',
        'banner_button',


        'banner_image_left',
        'banner_image_right',

        'section_1_image',
        'section_1_title',
        'section_1_description',

        'section_2_title',
        'section_2_description',
        'section_2_image',

        'section_3_title',
        'section_3_description',
        'section_3_image',

        'section_3_title',
        'section_3_description',
        'section_3_button',

        'claim_section',
        
        'meta_title',
        'meta_description'
    ];


    protected $casts = [
        'claim_section' => 'array',
    ];

    
    
    // Relationship with Language Model (Assuming you have a Language model)
    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }


    public function getLeftImageUrlAttribute()
    {
        return $this->left_image ? asset('get_listed/' . $this->left_image) : null;
    }

    public function getRightImageUrlAttribute()
    {
        return $this->right_image ? asset('get_listed/' . $this->right_image) : null;
    }

    public function getBottomImageUrlAttribute()
    {
        return $this->bottom_image ? asset('get_listed/' . $this->bottom_image) : null;
    }

    public function getSecondLeftImageUrlAttribute()
    {
        return $this->second_left_image ? asset('get_listed/' . $this->second_left_image) : null;
    }

    public function getSecondRightImageUrlAttribute()
    {
        return $this->second_right_image ? asset('get_listed/' . $this->second_right_image) : null;
    }
}
