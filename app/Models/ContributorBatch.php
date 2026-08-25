<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributorBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_name',
        'min_reviews',
        'max_reviews',
        'icon',
        'status',
    ];

    protected $casts = [
        'min_reviews' => 'integer',
        'max_reviews' => 'integer',
    ];
}