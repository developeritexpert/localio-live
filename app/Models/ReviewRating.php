<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewRating extends Model
{
    protected $table = 'review_ratings';
    protected $fillable = ['review_id', 'criteria_id', 'rating'];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function criteria()
    {
        return $this->belongsTo(CategoryRatingCriteria::class, 'criteria_id');
    }
}
