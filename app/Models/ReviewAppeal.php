<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewAppeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'appeal_reason',
        'status',
        'moderator_id',
        'moderator_decision',
        'moderator_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    const STATUS_OPEN         = 'open';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_RESOLVED     = 'resolved';

    const DECISION_RESTORED             = 'restored';
    const DECISION_CONFIRMED_REJECTION  = 'confirmed_rejection';
    const DECISION_INFO_REQUESTED       = 'info_requested';

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }
}
