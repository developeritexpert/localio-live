<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReviewReply extends Model
{
    use HasFactory;

    protected $table = 'business_review_replies';

    protected $fillable = [
        'review_id',
        'business_id',
        'user_id',
        'reply_text',
        'status',
    ];

    public const STATUS_PUBLISHED = 'published';
    public const STATUS_HIDDEN = 'hidden';

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

