<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'report_reason',
        'report_type',
        'reporter_type',
        'report_details',
        'status',
        'remoderation_triggered',
        'remoderation_result',
    ];

    protected $casts = [
        'remoderation_triggered' => 'boolean',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_REVIEWED  = 'reviewed';
    const STATUS_DISMISSED = 'dismissed';
    const STATUS_ACTIONED  = 'actioned';

    const TYPE_FAKE                 = 'fake';
    const TYPE_CONFLICT_OF_INTEREST = 'conflict_of_interest';
    const TYPE_PERSONAL_INFO        = 'personal_info';
    const TYPE_FALSE_ALLEGATION     = 'false_allegation';
    const TYPE_HATE                 = 'hate';
    const TYPE_SPAM                 = 'spam';
    const TYPE_WRONG_BUSINESS       = 'wrong_business';
    const TYPE_COPYRIGHT            = 'copyright';
    const TYPE_OTHER                = 'other';

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
