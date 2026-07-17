<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryRatingCriteria extends Model
{
    protected $table = 'category_rating_criteria';
    protected $fillable = ['category_id', 'name'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
