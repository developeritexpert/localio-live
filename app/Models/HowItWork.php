<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HowItWork extends Model
{
    use HasFactory;


    protected $table = 'how_it_works';

    protected $fillable = [
        'lang_id',
        'banner_title',
        'banner_description',
        'banner_image_left',
        'banner_image_right',

        'main_heading',
 

        'section_1_title',
        'section_1_description',

        'section_2_title',
        'section_2_description',
        'section_2_button',


        'section_3_title',
        'section_3_description',
        'section_3_image',
    ];


}
