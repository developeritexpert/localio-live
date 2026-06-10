<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterContent extends Model
{
    use HasFactory;
    protected $fillable = [
        'meta_key',
        'meta_value',
        'lang_code',
        'type',
    ];
}
