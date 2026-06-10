<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorReviewFeedback extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'message','review_id', 'is_inappropriate'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function review(){
        return $this->belongsTo(Review::class,'review_id');
    }
}
