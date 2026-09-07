<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDiscussionComment extends Model
{
    use HasFactory;

    protected $table = 'business_discussion_comments';

    protected $fillable = [
        'business_discussion_id',
        'user_id',
        'parent_id',
        'author_name',
        'author_title',
        'author_avatar',
        'content',
        'upvotes_count',
        'downvotes_count',
        'status',
    ];

    protected $casts = [
        'upvotes_count' => 'integer',
        'downvotes_count' => 'integer',
    ];

    public function discussion()
    {
        return $this->belongsTo(BusinessDiscussion::class, 'business_discussion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(BusinessDiscussionComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(BusinessDiscussionComment::class, 'parent_id')
            ->where('status', 'active')
            ->orderBy('created_at', 'asc');
    }

    public function votes()
    {
        return $this->morphMany(BusinessDiscussionVote::class, 'voteable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getAuthorDisplayNameAttribute()
    {
        if ($this->user) {
            return $this->user->displayName();
        }
        return $this->author_name ?: 'Community Member';
    }

    public function getAuthorInitialAttribute()
    {
        $name = $this->author_display_name;
        return strtoupper(substr($name, 0, 1));
    }
}
