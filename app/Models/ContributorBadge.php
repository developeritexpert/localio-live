<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributorBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_reviews',
        'max_reviews',
        'icon',
    ];
}
