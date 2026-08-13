<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultRatingCriteria extends Model
{
    protected $table = 'default_rating_criteria';
    protected $fillable = ['key', 'name', 'default_description', 'sort_order'];
}
