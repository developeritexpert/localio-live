@php
    $level = $level ?? 0;
    $hasReplies = $comment->replies && $comment->replies->count() > 0;
    $replyCount = $comment->replies ? $comment->replies->count() : 0;
    $score = ($comment->upvotes_count ?? 0) - ($comment->downvotes_count ?? 0);
@endphp

<div class="reddit-comment-item {{ $level > 0 ? 'nested-comment-item' : '' }}" id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}">
    <!-- Left Thread Column (Avatar & Connecting Root Line) -->
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

    <!-- Main Content & Body Column -->
    <div class="comment-content-col">
        <!-- Comment Header Row -->
        <div class="comment-header d-flex align-items-center flex-wrap gap-2 mb-1">
            <!-- Collapse / Expand Toggle Button -->
            <button type="button" class="btn-thread-toggle" title="Toggle thread collapse" onclick="toggleCommentThread({{ $comment->id }})">
                <i class="far fa-minus-square collapse-icon-minus"></i>
                <i class="far fa-plus-square collapse-icon-plus d-none"></i>
            </button>

            <span class="comment-author-name">{{ $comment->author_display_name }}</span>

            <!-- @if($comment->author_title)
                <span class="comment-author-badge">{{ $comment->author_title }}</span>
            @endif -->

            <span class="comment-timestamp">• {{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>

            <!-- Collapsed summary preview label -->
            <span class="collapsed-summary-pill d-none ms-2" onclick="toggleCommentThread({{ $comment->id }})">
                <i class="fas fa-plus me-1"></i> {{ $replyCount > 0 ? $replyCount . ' ' . Str::plural('reply', $replyCount) . ' collapsed' : 'comment collapsed' }}
            </span>
        </div>

        <!-- Collapsible Content Wrapper -->
        <div class="comment-body-wrapper" id="comment-body-wrapper-{{ $comment->id }}">
            <div class="comment-text-content">
                {!! nl2br(e($comment->content)) !!}
            </div>

            <!-- Reddit Action Bar (Vote, Reply, Award, Share, More) -->
            <div class="comment-action-bar d-flex align-items-center flex-wrap gap-3 mt-2">
                <!-- Upvote/Downvote Group -->
                <div class="comment-vote-group d-inline-flex align-items-center">
                    <button type="button" class="comment-vote-btn vote-trigger" data-type="comment" data-id="{{ $comment->id }}" data-vote="up" title="Upvote">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <span class="comment-vote-score vote-score-comment-{{ $comment->id }}">{{ $score }}</span>
                    <button type="button" class="comment-vote-btn vote-trigger" data-type="comment" data-id="{{ $comment->id }}" data-vote="down" title="Downvote">
                        <i class="fas fa-arrow-down"></i>
                    </button>
                </div>

                <!-- Reply Button -->
                <button type="button" class="comment-action-btn" onclick="toggleReplyForm({{ $comment->id }})">
                    <i class="far fa-comment-alt"></i> Reply
                </button>

                <!-- Award Button -->
                <!-- <button type="button" class="comment-action-btn d-none d-sm-inline-flex">
                    <i class="fas fa-award"></i> Award
                </button> -->

                <!-- Share Button -->
                <button type="button" class="comment-action-btn" onclick="copyCommentLink({{ $comment->id }})">
                    <i class="far fa-share-square"></i> Share
                </button>

                <!-- More Options Button -->
                <!-- <button type="button" class="comment-action-btn px-1" title="More actions">
                    <i class="fas fa-ellipsis-h"></i>
                </button> -->
            </div>

            <!-- Nested Reply Form -->
            <div class="reply-box-container d-none mt-3" id="reply-form-{{ $comment->id }}">
                <form action="{{ route('business.discussion.comment') }}" method="POST" class="comment-submit-form">
                    @csrf
                    <input type="hidden" name="business_discussion_id" value="{{ $disc->id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <div class="mb-2">
                        <textarea name="content" class="form-control" rows="2" placeholder="Write a reply..." required style="font-size: 13px; border-radius: 8px; border-color: #cbd5e1;"></textarea>
                    </div>
                    @guest
                        <div class="mb-2">
                            <input type="text" name="author_name" class="form-control" placeholder="Your Name (Optional)" style="font-size: 13px; border-radius: 6px; border-color: #cbd5e1;">
                        </div>
                    @endguest
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-sm btn-primary" style="background: #06498b; border-color: #06498b; font-weight: 600; border-radius: 6px; font-size: 12px;">
                            Submit Reply
                        </button>
                        <button type="button" class="btn btn-sm btn-light" onclick="toggleReplyForm({{ $comment->id }})" style="font-size: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Render Sub-Replies (Recursive) -->
            <div class="comment-replies-container mt-2" id="replies-container-{{ $comment->id }}">
                @if($hasReplies)
                    @foreach($comment->replies as $reply)
                        @include('User.product.partials.comment_item', ['comment' => $reply, 'disc' => $disc, 'level' => $level + 1])
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
