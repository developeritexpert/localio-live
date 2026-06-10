<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    protected $fillable = [
        'name',
        'key',
        'value',
        'status',
        'type',
        'model_ref',
        'params',
        'field_type',
    ];

    use HasFactory;
}
