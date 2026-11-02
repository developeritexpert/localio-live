<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCorrectionRequest extends Model
{
    use HasFactory;

    protected $table = 'business_correction_requests';

    protected $fillable = [
        'business_id',
        'user_id',
        'issue_type',
        'current_info',
        'suggested_correction',
        'supporting_link',
        'status',
        'admin_notes',
        'reviewed_by',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPLIED = 'applied';
    public const STATUS_DECLINED = 'declined';

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

