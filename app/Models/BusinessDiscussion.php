<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDiscussion extends Model
{
    use HasFactory;

    protected $table = 'business_discussions';

    protected $fillable = [
        'business_id',
        'user_id',
        'author_name',
        'author_title',
        'author_avatar',
        'category',
        'title',
        'content',
        'upvotes_count',
        'downvotes_count',
        'comments_count',
        'views_count',
        'is_pinned',
        'status',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'upvotes_count' => 'integer',
        'downvotes_count' => 'integer',
        'comments_count' => 'integer',
        'views_count' => 'integer',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        $userPinnedComments = session()->get('user_pinned_comments', []);
        $userPinnedComments = is_array($userPinnedComments) ? array_filter(array_map('intval', $userPinnedComments)) : [];

        $relation = $this->hasMany(BusinessDiscussionComment::class, 'business_discussion_id')
            ->where('status', 'active')
            ->whereNull('parent_id');

        if (!empty($userPinnedComments)) {
            $idsStr = implode(',', $userPinnedComments);
            $relation->orderByRaw("CASE WHEN is_pinned = 1 OR id IN ({$idsStr}) THEN 1 ELSE 0 END DESC");
        } else {
            $relation->orderByDesc('is_pinned');
        }

        return $relation->orderBy('created_at', 'asc');
    }

    public function allComments()
    {
        return $this->hasMany(BusinessDiscussionComment::class, 'business_discussion_id')
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
        return $this->author_name ?: '';
    }

    public function getAuthorInitialAttribute()
    {
        $name = $this->author_display_name;
        return strtoupper(substr($name, 0, 1));
    }
}
