<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhoWeAre extends Model
{
    use HasFactory;
    protected $table = 'who_we_ares';

    // Allow mass assignment for these fields
    protected $fillable = [
        'lang_id',
        'main_heading',
        'sub_heading',
        'bg_top_img',
        'top_left_section_img',
        'top_right_section_img',
        'mp_heading',
        'mp_sub_heading',
        'top_card_title',
        'top_card_image',
        'top_card_desc',
        'specialists_heading',
        'ss_heading',
        'ss_sub_desc',
        'protfolio_btn',
        'status',
        'meta_title',
        'meta_description',
        'permanent_url'
    ];

}
