@php
    $level = $level ?? 0;
    $hasReplies = $comment->replies && $comment->replies->count() > 0;
    $replyCount = $comment->replies ? $comment->replies->count() : 0;
    $score = ($comment->upvotes_count ?? 0) - ($comment->downvotes_count ?? 0);
    $isAuthorOrAdmin = auth()->check() && (auth()->id() == $comment->user_id || auth()->user()->user_type === 'admin');
    $isAdminOrOwner = auth()->check() && (auth()->user()->user_type === 'admin' || (auth()->user()->business_id && isset($disc) && auth()->user()->business_id == $disc->business_id));
@endphp

<div class="reddit-comment-item {{ $level > 0 ? 'nested-comment-item' : '' }}" id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}">
    <div class="comment-thread-left">
        <div class="comment-avatar">
            @if($comment->author_avatar)
                <img src="{{ asset($comment->author_avatar) }}" alt="{{ $comment->author_display_name }}" class="avatar-img">
            @else
                <div class="avatar-initial">
                    {{ $comment->author_initial }}
                </div>
            @endif
        </div>
        
        <!-- Root Connecting Thread Line -->
        <div class="comment-thread-line-container" title="Click to collapse thread" onclick="toggleCommentThread({{ $comment->id }})" role="button" tabindex="0" aria-label="Collapse comment thread">
            <div class="comment-thread-line"></div>
        </div>
    </div>

    <!-- Right Comment Content Body -->
    <div class="comment-content-body">
        <!-- Collapsed Comment Summary Row (Visible only when is-collapsed) -->
        <div class="comment-collapsed-summary align-items-center gap-2" onclick="toggleCommentThread({{ $comment->id }})" title="Expand comment thread" role="button" tabindex="0">
            <i class="fas fa-plus-circle text-muted" style="font-size: 13px;"></i>
            <span class="comment-author-name">{{ $comment->author_display_name }}</span>
            <span class="comment-dot-sep">•</span>
            <span class="comment-timestamp">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>
            @if($hasReplies)
                <span class="badge bg-light text-secondary border ms-1" style="font-size: 11px;">+{{ $replyCount }} {{ $replyCount == 1 ? 'reply' : 'replies' }}</span>
            @endif
        </div>

        <!-- Comment Header (Author, Badge, Date, Options) -->
        <div class="comment-header d-flex align-items-center justify-content-between mb-1">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="comment-author-name">{{ $comment->author_display_name }}</span>

                @if(!empty($comment->is_pinned))
                    <span class="badge bg-warning text-dark me-1 comment-pinned-badge" style="font-size: 10px;"><i class="fas fa-thumbtack"></i> Pinned</span>
                @endif

                <span class="comment-dot-sep">•</span>
                <span class="comment-timestamp">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>
            </div>

            @if($isAuthorOrAdmin)
                <div class="dropdown">
                    <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Options" style="font-size: 12px;">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 12px; border-radius: 8px; border-color: #e2e8f0;">
                        <li>
                            <button class="dropdown-item text-danger d-flex align-items-center gap-2" onclick="deleteComment({{ $comment->id }})">
                                <i class="far fa-trash-alt"></i> Delete comment
                            </button>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <!-- Comment Text Content -->
        <div class="comment-text-content mb-2" id="comment-text-{{ $comment->id }}">
            {!! nl2br(e($comment->content)) !!}
        </div>

        <!-- Comment Action Toolbar (Upvote/Downvote, Reply, Share, Pin) -->
        <div class="comment-action-bar d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="comment-collapse-toggle-btn" onclick="toggleCommentThread({{ $comment->id }})" title="Collapse comment thread" aria-label="Collapse comment thread">
                <i class="fas fa-minus-circle"></i>
            </button>

            <div class="comment-vote-pill d-inline-flex align-items-center">
                <button type="button" class="comment-vote-btn vote-trigger" data-type="comment" data-id="{{ $comment->id }}" data-vote="up" title="Upvote" aria-label="Upvote comment">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <span class="comment-vote-count vote-score-comment-{{ $comment->id }}">{{ $score }}</span>
                <button type="button" class="comment-vote-btn vote-trigger" data-type="comment" data-id="{{ $comment->id }}" data-vote="down" title="Downvote" aria-label="Downvote comment">
                    <i class="fas fa-arrow-down"></i>
                </button>
            </div>

            <div class="d-flex align-items-center gap-1">
                <button type="button" class="post-action-btn toggle-reply-btn" @if(auth()->check()) onclick="toggleReplyForm({{ $comment->id }})" @else onclick="openLoginModal()" @endif title="Reply" aria-label="Reply to comment">
                    <svg viewBox="0 0 39 36" width="16" height="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.01672 2.63798H33.965C35.2962 2.63798 36.3352 3.66561 36.3352 4.99009V24.0121C36.3352 25.3366 35.2962 26.3665 33.965 26.3665H7.55691C7.24307 26.3665 6.94339 26.5024 6.74002 26.7415L2.64931 31.5245V4.98953C2.64874 3.66561 3.68771 2.63798 5.01672 2.63798ZM5.01672 0.500567C2.53714 0.500567 0.5 2.52071 0.5 4.99066V34.4289C0.5 35.4237 1.74347 35.8786 2.39212 35.1218L8.05034 28.5028H33.9644C36.4474 28.5028 38.4805 26.4837 38.4805 24.0127V4.99009C38.4805 2.52071 36.4474 0.5 33.9644 0.5H5.01672V0.500567Z"/>
                        <path d="M7.43411 10.6329C6.83305 10.625 6.34473 11.1042 6.34473 11.6996C6.34473 12.2973 6.83362 12.7771 7.43411 12.7686H31.5478C32.1455 12.7777 32.6372 12.2973 32.6372 11.6996C32.6372 11.1042 32.1455 10.6255 31.5478 10.6329H7.43411Z"/>
                        <path d="M7.43411 17.5002C6.83305 17.49 6.34473 17.9704 6.34473 18.568C6.34473 19.1634 6.83362 19.6455 7.43411 19.637H18.2288C18.8265 19.6461 19.3176 19.1634 19.3176 18.568C19.3176 17.9704 18.8265 17.4905 18.2288 17.5002H7.43411Z"/>
                    </svg>
                </button>
                <span class="toggle-reply-text" role="button" @if(auth()->check()) onclick="toggleReplyForm({{ $comment->id }})" @else onclick="openLoginModal()" @endif>Reply</span>
            </div>

            <button type="button" class="post-action-btn share-trigger-btn" onclick="openShareModal('{{ url()->current() }}#comment-{{ $comment->id }}', 'Comment on discussion', event)" title="Share comment" aria-label="Share comment">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
            </button>

            @if($isAdminOrOwner)
                <button type="button" class="post-action-btn pin-trigger {{ !empty($comment->is_pinned) ? 'text-primary fw-bold' : '' }}" data-type="comment" data-id="{{ $comment->id }}" title="{{ !empty($comment->is_pinned) ? 'Unpin Comment' : 'Pin Comment' }}">
                    <i class="fas fa-thumbtack"></i> <span class="pin-text">{{ !empty($comment->is_pinned) ? 'Pinned' : 'Pin' }}</span>
                </button>
            @endif
        </div>

        <!-- Nested Reply Form Box (Hidden by default) -->
        <div class="reply-form-box mt-3 d-none" id="reply-form-{{ $comment->id }}">
            @if(auth()->check())
                <form action="{{ route('business.discussion.comment') }}" method="POST" class="comment-submit-form">
                    @csrf
                    <input type="hidden" name="business_discussion_id" value="{{ $comment->business_discussion_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <div class="mb-2">
                        <textarea name="content" class="form-control" rows="2" placeholder="Write your reply..." required style="font-size: 13.5px; border-color: #cbd5e1; border-radius: 8px;"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary px-3 py-1 fw-semibold" style="font-size: 12px;">
                            Post Reply
                        </button>
                        <button type="button" class="btn btn-sm btn-light px-2 py-1" onclick="toggleReplyForm({{ $comment->id }})" style="font-size: 12px;">
                            Cancel
                        </button>
                    </div>
                </form>
            @else
                <div class="p-3 text-center bg-light rounded-3" style="border: 1px dashed #cbd5e1;">
                    <span class="text-muted" style="font-size: 13px;">Want to reply? </span>
                    <a href="javascript:void(0)" onclick="openLoginModal()" class="fw-semibold text-primary" style="font-size: 13px; text-decoration: underline;">Log in to post a reply</a>
                </div>
            @endif
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
