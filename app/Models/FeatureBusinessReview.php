<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureBusinessReview extends Model
{
    use HasFactory;

    protected $table = 'feature_business_reviews';

    protected $fillable = [
        'business_id',
        'feature_id',
        'user_id',
        'rating',
        'comment',
    ];

    // Relationships (optional)
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
