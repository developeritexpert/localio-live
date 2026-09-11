@php
    $level = $level ?? 0;
    $hasReplies = $comment->replies && $comment->replies->count() > 0;
    $replyCount = $comment->replies ? $comment->replies->count() : 0;
    $score = ($comment->upvotes_count ?? 0) - ($comment->downvotes_count ?? 0);

    $isGloballyPinned = !empty($comment->is_pinned);
    $isUserPinned = in_array($comment->id, session()->get('user_pinned_comments', []));
    $isPinnedForUser = $isGloballyPinned || $isUserPinned;
@endphp

<div class="reddit-comment-item {{ $level > 0 ? 'nested-comment-item' : '' }}" id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}">
    <div class="comment-thread-left">
        <div class="comment-avatar">
            @if($comment->author_avatar)
                <img src="{{ asset($comment->author_avatar) }}" alt="{{ $comment->author_display_name }}" class="avatar-img">
            @else
                <div class="avatar-initial avatar-color-{{ ($comment->id % 6) + 1 }}">
                    {{ $comment->author_initial }}
                </div>
            @endif
        </div>
        
        <!-- Root Connecting Thread Line -->
        <div class="comment-thread-line-container" title="Click to collapse thread" onclick="toggleCommentThread({{ $comment->id }})">
            <div class="comment-thread-line"></div>
        </div>
    </div>

    <!-- Right Comment Content Body -->
    <div class="comment-content-body">
        <!-- Collapsed Comment Summary Row (Visible only when is-collapsed) -->
        <div class="comment-collapsed-summary align-items-center gap-2" onclick="toggleCommentThread({{ $comment->id }})" title="Expand comment thread">
            <i class="fas fa-plus-circle text-muted" style="font-size: 14px;"></i>
            <span class="comment-author-name" style="font-size: 13px; color: #475569;">{{ $comment->author_display_name }}</span>
            <span class="comment-dot-sep">•</span>
            <span class="comment-timestamp" style="font-size: 12px; color: #64748b;">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>
            @if($hasReplies)
                <span class="badge bg-light text-secondary border ms-1" style="font-size: 11px;">+{{ $replyCount }} {{ $replyCount == 1 ? 'reply' : 'replies' }}</span>
            @endif
        </div>

        <!-- Comment Header (Author, Badge, Date) -->
        <div class="comment-header d-flex align-items-center flex-wrap gap-2 mb-1">
            <span class="comment-author-name">{{ $comment->author_display_name }}</span>

            @if($isGloballyPinned)
                <span class="badge bg-warning text-dark me-1 comment-pinned-badge" style="font-size: 10px;"><i class="fas fa-thumbtack"></i> Pinned</span>
            @elseif($isUserPinned)
                <span class="badge bg-info text-white me-1 user-pinned-badge" style="font-size: 10px;"><i class="fas fa-thumbtack"></i> Pinned by you</span>
            @endif

            <!-- @if($comment->author_title)
                <span class="comment-author-badge">{{ $comment->author_title }}</span>
            @endif -->

            <span class="comment-dot-sep">•</span>
            <span class="comment-timestamp">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>
        </div>

        <!-- Comment Text Content -->
        <div class="comment-text-content mb-2" id="comment-text-{{ $comment->id }}">
            {!! nl2br(e($comment->content)) !!}
        </div>

        <!-- Comment Action Toolbar (Upvote/Downvote, Reply, Share, Pin) -->
        <div class="comment-action-bar d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="comment-collapse-toggle-btn" onclick="toggleCommentThread({{ $comment->id }})" title="Collapse comment thread">
                <i class="fas fa-minus-circle"></i>
            </button>

            <div class="comment-vote-pill d-inline-flex align-items-center">
                <button type="button" class="comment-vote-btn vote-trigger" data-type="comment" data-id="{{ $comment->id }}" data-vote="up" title="Upvote">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <span class="comment-vote-count vote-score-comment-{{ $comment->id }}">{{ $score }}</span>
                <button type="button" class="comment-vote-btn vote-trigger" data-type="comment" data-id="{{ $comment->id }}" data-vote="down" title="Downvote">
                    <i class="fas fa-arrow-down"></i>
                </button>
            </div>

            <div class="d-flex align-items-center gap-1">
                <button type="button" class="post-action-btn toggle-reply-btn" onclick="toggleReplyForm({{ $comment->id }})" title="Reply">
                    <svg rpl="" aria-hidden="true" class="icon-comment rpl-rtl-icon" fill="currentColor" height="16" icon-name="comment" viewBox="0 0 20 20" width="16" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 1a9 9 0 00-9 9c0 1.947.79 3.58 1.935 4.957L.231 17.661A.784.784 0 00.785 19H10a9 9 0 009-9 9 9 0 00-9-9Zm0 16.2H6.162c-.994.004-1.907.053-3.045.144l-.076-.188a36.981 36.981 0 002.328-2.087l-1.05-1.263C3.297 12.576 2.8 11.331 2.8 10c0-3.97 3.23-7.2 7.2-7.2s7.2 3.23 7.2-7.2 7.2Z"></path>
                    </svg>
                </button>
                <span class="toggle-reply-text" role="button" onclick="toggleReplyForm({{ $comment->id }})">Reply</span>
            </div>

            <button type="button" class="post-action-btn share-trigger-btn" onclick="openShareModal('{{ url()->current() }}#comment-{{ $comment->id }}', 'Comment on discussion', event)" title="Share">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
            </button>

            <button type="button" class="post-action-btn pin-trigger {{ $isPinnedForUser ? 'text-primary fw-bold' : '' }}" data-type="comment" data-id="{{ $comment->id }}" title="{{ $isPinnedForUser ? 'Unpin Comment' : 'Pin Comment' }}">
                <i class="fas fa-thumbtack"></i> <span class="pin-text">{{ $isPinnedForUser ? 'Pinned' : 'Pin' }}</span>
            </button>
        </div>

        <!-- Nested Reply Form Box (Hidden by default) -->
        <div class="reply-form-box mt-3 d-none" id="reply-form-{{ $comment->id }}">
            <form action="{{ route('business.discussion.comment') }}" method="POST" class="comment-submit-form">
                @csrf
                <input type="hidden" name="business_discussion_id" value="{{ $comment->business_discussion_id }}">
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <div class="mb-2">
                    <textarea name="content" class="form-control" rows="2" placeholder="What are your thoughts?" required style="font-size: 13.5px; border-color: #cbd5e1;"></textarea>
                </div>
                @guest
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <input type="text" name="author_name" class="form-control" placeholder="Your Name (Optional)" style="font-size: 12.5px;">
                        </div>
                    </div>
                @endguest
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary" style="background: #06498b; border-color: #06498b; font-size: 12px; font-weight: 600;">
                        Post Reply
                    </button>
                    <button type="button" class="btn btn-sm btn-light" onclick="toggleReplyForm({{ $comment->id }})" style="font-size: 12px;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Recursive Replies Container -->
        <div class="comment-replies-container mt-2" id="replies-container-{{ $comment->id }}">
            @if($hasReplies)
                @foreach($comment->replies as $reply)
                    @include('User.product.partials.comment_item', ['comment' => $reply, 'disc' => $disc ?? null, 'level' => $level + 1])
                @endforeach
            @endif
        </div>
    </div>
</div>
