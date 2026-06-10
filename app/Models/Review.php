<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'lang_id',
        'user_id',

        'ease_of_use_rating',
        'value_for_money_rating',
        'customer_service_rating',
        'exclusive_service_rating',
        'rating',
        'status',  // keep this if you use it elsewhere, otherwise optional
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function translations()
    {
        return $this->hasMany(ReviewTranslation::class, 'reviews_id');
    }

    public function translation($lang_id)
    {
        return $this->hasOne(ReviewTranslation::class,'reviews_id', 'id')->where('language_id',$lang_id);
    }


    public function original()
    {
        return $this->hasOne(ReviewTranslation::class, 'reviews_id', 'id')
        ->join('reviews', 'review_translations.reviews_id', '=', 'reviews.id')
        ->whereColumn('review_translations.language_id', 'reviews.lang_id')
        ->select('review_translations.*');
    }
}
