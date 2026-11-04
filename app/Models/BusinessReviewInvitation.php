<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReviewInvitation extends Model
{
    use HasFactory;

    protected $table = 'business_review_invitations';

    protected $fillable = [
        'business_id',
        'user_id',
        'recipient_email',
        'invitation_token',
        'status',
    ];

    public const STATUS_SENT = 'sent';
    public const STATUS_OPENED = 'opened';
    public const STATUS_REVIEWED = 'reviewed';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

