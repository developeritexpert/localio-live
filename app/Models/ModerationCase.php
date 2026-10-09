<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_number',
        'title',
        'type',
        'business_id',
        'status',
        'summary',
        'signals',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'signals'     => 'array',
        'resolved_at' => 'datetime',
    ];

    const STATUS_OPEN          = 'open';
    const STATUS_INVESTIGATING = 'investigating';
    const STATUS_RESOLVED      = 'resolved';
    const STATUS_DISMISSED     = 'dismissed';

    const TYPE_COORDINATED_MANIPULATION = 'coordinated_manipulation';
    const TYPE_IP_CLUSTER               = 'ip_cluster';
    const TYPE_DEVICE_CLUSTER           = 'device_cluster';
    const TYPE_TIMING_BURST             = 'timing_burst';
    const TYPE_LANGUAGE_SIMILARITY      = 'language_similarity';

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviews()
    {
        return $this->belongsToMany(Review::class, 'moderation_case_reviews', 'case_id', 'review_id')->withTimestamps();
    }
}
