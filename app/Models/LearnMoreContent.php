<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnMoreContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'section_type',
        'title',
        'content',
        'sort_order',
        'lang_id'
    ];
}
