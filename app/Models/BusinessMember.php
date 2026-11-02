<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessMember extends Model
{
    use HasFactory;

    protected $table = 'business_members';

    protected $fillable = [
        'business_id',
        'user_id',
        'role',
        'job_title',
        'work_email',
        'permissions',
        'invited_by',
        'status',
        'invitation_token',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public const ROLE_OWNER = 'owner';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MEMBER = 'member';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INVITED = 'invited';
    public const STATUS_SUSPENDED = 'suspended';

    public const DEFAULT_PERMISSIONS = [
        'can_reply_reviews'       => true,
        'can_report_reviews'      => true,
        'can_invite_reviews'      => true,
        'can_manage_team'         => false,
        'can_suggest_corrections' => true,
    ];

    public const OWNER_PERMISSIONS = [
        'can_reply_reviews'       => true,
        'can_report_reviews'      => true,
        'can_invite_reviews'      => true,
        'can_manage_team'         => true,
        'can_suggest_corrections' => true,
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_OWNER, self::ROLE_ADMIN], true);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        $perms = $this->permissions ?? [];
        return !empty($perms[$permission]);
    }
}

