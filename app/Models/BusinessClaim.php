<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessClaim extends Model
{
    use HasFactory;

    protected $table = 'business_claims';

    protected $fillable = [
        'business_id',
        'user_id',
        'connection_type',
        'agency_authorized',
        'job_title',
        'verification_method',
        'work_email',
        'verification_code',
        'verification_token',
        'code_expires_at',
        'evidence_text',
        'evidence_document',
        'evidence_channel',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'agency_authorized' => 'boolean',
        'code_expires_at'   => 'datetime',
        'reviewed_at'       => 'datetime',
    ];

    public const STATUS_PENDING_CODE = 'pending_code';
    public const STATUS_PENDING_MANUAL = 'pending_manual_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

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

