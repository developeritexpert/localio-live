@extends('user_layout.master')
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/product-detail-components.css') }}">
    @include('User.partials.business_detail_styles')
    <style>
        .community-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            /* background: #f0f7ff; */
            border: 1px solid #cce3ff;
            color: #004b93;
            padding: 8px 56px;
            border-radius: 5px;
            font-size: 13px;
            /* font-weight: 600; */
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .comments-thread-container {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 10px;
            padding: 16px;
        }

        .reddit-comment-item {
            display: flex;
            gap: 10px;
            position: relative;
            margin-top: 12px;
            margin-bottom: 12px;
        }

        .reddit-comment-item.nested-comment-item {
            margin-top: 10px;
            margin-bottom: 6px;
        }

        .comment-thread-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 28px;
            flex-shrink: 0;
            position: relative;
        }

        .comment-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .comment-avatar .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .comment-avatar .avatar-initial {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            color: #ffffff;
            border-radius: 50%;
        }

        .avatar-color-1 { background: #06498b; }
        .avatar-color-2 { background: #0d9488; }
        .avatar-color-3 { background: #7c3aed; }
        .avatar-color-4 { background: #d97706; }
        .avatar-color-5 { background: #e11d48; }
        .avatar-color-6 { background: #2563eb; }

        .comment-thread-line-container {
            flex-grow: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            cursor: pointer;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .comment-thread-line {
            width: 2px;
            height: 100%;
            background-color: #cbd5e1;
            transition: background-color 0.2s ease, width 0.2s ease;
            border-radius: 2px;
        }

        .comment-thread-line-container:hover .comment-thread-line,
        .reddit-comment-item:hover > .comment-thread-left > .comment-thread-line-container .comment-thread-line {
            background-color: #06498b;
            width: 3px;
        }

        .comment-content-col {
            flex-grow: 1;
            min-width: 0;
        }

        .btn-thread-toggle {
            background: transparent;
            border: none;
            padding: 0;
            color: #94a3b8;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .btn-thread-toggle:hover {
            color: #06498b;
        }

        .comment-author-name {
            font-weight: 700;
            font-size: 13.5px;
            color: #0f172a;
        }

        .comment-author-badge {
            background: #e0f2fe;
            color: #0369a1;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .comment-timestamp {
            color: #64748b;
            font-size: 12px;
        }

        .collapsed-summary-pill {
            background: #f1f5f9;
            color: #475569;
            font-size: 11.5px;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .collapsed-summary-pill:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .comment-text-content {
            font-size: 14px;
            line-height: 1.55;
            color: #1e293b;
            margin-top: 3px;
            margin-bottom: 6px;
            word-break: break-word;
        }

        .comment-action-bar {
            font-size: 12px;
        }

        .comment-vote-group {
            background: #f1f5f9;
            border-radius: 20px;
            padding: 2px 8px;
            gap: 4px;
        }

        .comment-vote-btn {
            background: transparent;
            border: none;
            color: #64748b;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .comment-vote-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .comment-vote-score {
            font-weight: 700;
            font-size: 12px;
            color: #1e293b;
            min-width: 14px;
            text-align: center;
        }

        .comment-action-btn {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 12.5px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .comment-action-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .reddit-comment-item.is-collapsed > .comment-content-col > .comment-body-wrapper {
            display: none !important;
        }

        .reddit-comment-item.is-collapsed > .comment-thread-left > .comment-thread-line-container {
            display: none !important;
        }

        .reddit-comment-item.is-collapsed > .comment-content-col .collapsed-summary-pill {
            display: inline-block !important;
        }

        .reddit-comment-item.is-collapsed > .comment-content-col .btn-thread-toggle .collapse-icon-minus {
            display: none !important;
        }

        .reddit-comment-item.is-collapsed > .comment-content-col .btn-thread-toggle .collapse-icon-plus {
            display: inline-block !important;
        }

        .comment-replies-container {
            position: relative;
        }

        .reddit-post-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .reddit-post-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .vote-sidebar {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-right: 18px;
            border-right: 1px solid #f1f5f9;
            min-width: 54px;
        }

        .vote-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .vote-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .vote-btn.active-up {
            background: #ff4500;
            border-color: #ff4500;
            color: #ffffff !important;
        }
        .vote-btn.active-down {
            background: #7193ff;
            border-color: #7193ff;
            color: #ffffff !important;
        }

        .vote-score {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin: 6px 0;
        }

        .post-author-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #06498b;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            flex-shrink: 0;
        }

        .post-cat-badge {
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .post-action-btn {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s ease;
        }
        .post-action-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .comments-thread-container {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
            background: #fafafa;
            border-radius: 8px;
            padding: 16px;
        }

        .comment-item {
            position: relative;
            padding-left: 14px;
            border-left: 2px solid #e2e8f0;
            margin-bottom: 14px;
        }
        .comment-item:last-child {
            margin-bottom: 0;
        }

        .reply-box {
            margin-top: 10px;
            padding-top: 10px;
        }

        .discussions-filter-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 22px;
            margin-bottom: 24px;
        }
    </style>
@endpush

@php
    $lang_id = getCurrentLanguageID();
    $bTranslation = $business->translations->firstWhere('lang_id', $lang_id) ?? $business->translations->first();
    $bName = $bTranslation->name ?? $business->name ?? 'Business';
    $bCategory = $business->category?->translation?->name ?? 'Software';
@endphp

@section('meta_title', format_meta_text($bName . ' Community Discussions & Q&A'))
@section('meta_description', format_meta_text('Join the ' . $bName . ' community discussion. Ask questions, share insights, read user threads and engage with active users.'))

@section('content')
<div data-business-id="{{ $business->id }}">
    <section class="product_sec">
        <div class="inner_banner_sec">
            <div class="container-fluid" style="display: flex; justify-content: space-between;">
                <div class="inner_banr_content">
                    <div class="banner_heading d-flex align-items-center gap-3" style="margin-top: 23px;">
                        @if($business->has_logo)
                            <img src="{{ asset($business->icon_id) }}" alt="{{ $bName }}" style="width: 56px; height: 56px; object-fit: contain; border-radius: 8px; background: #fff; padding: 4px; border: 1px solid #e2e8f0;">
                        @else
                            <div style="width: 56px; height: 56px; border-radius: 8px; background: #06498b; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700;">
                                {{ $business->initial }}
                            </div>
                        @endif
                        <div>
                            <h2 class="m-0" style="font-size: 28px; font-weight: 600; color: #002347; margin-top:15px;">{{ $bName }} Community</h2>
                            <p class="m-0 text-muted" style="font-size: 15px;">Ask questions, exchange tips, and discuss features with active {{ $bName }} users.</p>
                        </div>
                    </div>

                    <!-- <div class="community-header-badge">
                        <span>•</span>  
                        <span>{{ number_format($displayDiscussionsCount) }} {{ $displayDiscussionsCount == 1 ? 'discussion' : 'discussions' }}</span>
                        <span>•</span>
                        <span>{{ number_format($displayCommentsCount) }} {{ $displayCommentsCount == 1 ? 'comment' : 'comments' }}</span>
                        <span>•</span>
                        <span>{{ number_format($displayActiveToday) }} active today</span>
                    </div> -->
                </div>

                <!-- @if($business->getTrackedUrl())
                    <div class="banner_right_btn d-none d-lg-block align-self-center">
                        <a href="{{ $business->getTrackedUrl() }}" target="_blank" rel="nofollow noopener" class="btn btn-primary px-4 py-2" style="background: #06498b; border-color: #06498b; font-weight: 600; border-radius: 8px;">
                            Visit {{ $bName }} <i class="fas fa-external-link-alt ms-1" style="font-size: 13px;"></i>
                        </a>
                    </div>
                @endif -->
            </div>
        </div>
    </section>

    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-lg-8">

                <div class="card mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="post-author-avatar">
                                {{ auth()->check() ? strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) : '?' }}
                            </div>
                            @if(auth()->check())
                                <input type="text" class="form-control" placeholder="Start a discussion or ask a question about {{ $bName }}..." style="border-radius: 20px; background: #f8fafc; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#newDiscussionModal" readonly>
                                <button class="btn btn-primary text-nowrap px-3 py-2" style="background: #06498b; border-color: #06498b; border-radius: 8px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#newDiscussionModal">
                                    <i class="fas fa-pen me-1"></i> Post
                                </button>
                            @else
                                <input type="text" class="form-control" placeholder="Start a discussion or ask a question about {{ $bName }}..." style="border-radius: 20px; background: #f8fafc; cursor: pointer;" onclick="triggerAuthLoginModal()" readonly>
                                <button class="btn btn-primary text-nowrap px-3 py-2" style="background: #06498b; border-color: #06498b; border-radius: 8px; font-weight: 600;" onclick="triggerAuthLoginModal()">
                                    <i class="fas fa-pen me-1"></i> Post
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="discussions-filter-card">
                    <form method="GET" action="{{ url()->current() }}" class="row align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1;"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Search discussions..." style="border-color: #cbd5e1;">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <select name="sort" class="form-select" onchange="this.form.submit()" style="border-color: #cbd5e1;">
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Top / Popular</option>
                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Most Recent</option>
                                <option value="commented" {{ request('sort') == 'commented' ? 'selected' : '' }}>Most Commented</option>
                            </select>
                        </div>
                    </form>
                </div>

                @if($discussions->isEmpty())
                    <div class="card text-center py-5" style="border-radius: 12px; border: 1px dashed #cbd5e1;">
                        <div class="card-body">
                            <i class="far fa-comments text-muted mb-3" style="font-size: 44px;"></i>
                            <h4 style="font-weight: 600; color: #1e293b;">No discussions found</h4>
                            <p class="text-muted">Be the first to start a conversation about {{ $bName }}!</p>
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newDiscussionModal" style="background: #06498b; border-color: #06498b;">
                                Start a Discussion
                            </button>
                        </div>
                    </div>
                @else
                    @foreach($discussions as $disc)
                        <div class="reddit-post-card" id="discussion-{{ $disc->id }}">
                            <div class="d-flex align-items-start">
                                <div class="vote-sidebar">
                                    <button class="vote-btn vote-trigger" data-type="discussion" data-id="{{ $disc->id }}" data-vote="up" title="Upvote">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <span class="vote-score vote-score-{{ $disc->id }}">{{ $disc->upvotes_count - $disc->downvotes_count }}</span>
                                    <button class="vote-btn vote-trigger" data-type="discussion" data-id="{{ $disc->id }}" data-vote="down" title="Downvote">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>

                                <div class="flex-grow-1 ps-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="post-author-avatar">
                                                {{ $disc->author_initial }}
                                            </div>
                                            <div>
                                                <span class="fw-bold" style="color: #0f172a; font-size: 14px;">{{ $disc->author_display_name }}</span>
                                                @if($disc->author_title)
                                                    <!-- <span class="text-muted" style="font-size: 12px;">• {{ $disc->author_title }}</span> -->
                                                @endif
                                                <div class="text-muted" style="font-size: 11px;">{{ $disc->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                         $isDiscGloballyPinned = !empty($disc->is_pinned);
                                         $isDiscUserPinned = in_array($disc->id, session()->get('user_pinned_discussions', []));
                                         $isDiscPinnedForUser = $isDiscGloballyPinned || $isDiscUserPinned;
                                     @endphp

                                     <h3 class="h5 font-weight-bold mb-2" style="color: #0f172a; font-size: 18px; font-weight: 700;">
                                         @if($isDiscGloballyPinned)
                                             <span class="badge bg-warning text-dark me-1 disc-pinned-badge" style="font-size: 11px;"><i class="fas fa-thumbtack"></i> Pinned by admin</span>
                                         @elseif($isDiscUserPinned)
                                             <span class="badge bg-info text-white me-1 user-disc-pinned-badge" style="font-size: 11px;"><i class="fas fa-thumbtack"></i> Pinned by you</span>
                                         @endif
                                         {{ $disc->title }}
                                     </h3>

                                     <p class="text-secondary mb-3" style="font-size: 14.5px; line-height: 1.6; color: #334155;">
                                         {!! nl2br(e($disc->content)) !!}
                                     </p>

                                     <div class="d-flex align-items-center gap-3">
                                         <button class="post-action-btn toggle-comments-btn" data-target="#comments-{{ $disc->id }}">
                                             <i class="far fa-comment-alt"></i> {{ $disc->comments_count }} Comments
                                         </button>
                                          <button type="button" class="post-action-btn share-trigger-btn" onclick="openShareModal('{{ url()->current() }}#discussion-{{ $disc->id }}', '{{ addslashes($disc->title) }}', event)">
                                              <i class="far fa-share-square"></i> Share
                                          </button>
                                         <button type="button" class="post-action-btn pin-trigger {{ $isDiscPinnedForUser ? 'text-primary fw-bold' : '' }}" data-type="discussion" data-id="{{ $disc->id }}" title="{{ $isDiscPinnedForUser ? 'Unpin Discussion' : 'Pin Discussion' }}">
                                              <i class="fas fa-thumbtack"></i> <span class="pin-text">{{ $isDiscPinnedForUser ? 'Pinned' : 'Pin' }}</span>
                                          </button>
                                     </div>

                                    <div class="comments-thread-container collapse mt-3" id="comments-{{ $disc->id }}">
                                        <form action="{{ route('business.discussion.comment') }}" method="POST" class="mb-4 comment-submit-form">
                                            @csrf
                                            <input type="hidden" name="business_discussion_id" value="{{ $disc->id }}">
                                            <div class="mb-2">
                                                <textarea name="content" class="form-control" rows="2" placeholder="What are your thoughts?" required style="border-color: #cbd5e1; font-size: 14px;"></textarea>
                                            </div>
                                            @guest
                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-6">
                                                        <input type="text" name="author_name" class="form-control" placeholder="Your Name (Optional)" style="font-size: 13px;">
                                                    </div>
                                                </div>
                                            @endguest
                                            <button type="submit" class="btn btn-sm btn-primary" style="background: #06498b; border-color: #06498b; font-weight: 600;">
                                                Post Comment
                                            </button>
                                        </form>

                                        <div class="top-level-comments-list" id="top-level-comments-{{ $disc->id }}">
                                            @foreach($disc->comments as $comment)
                                                @include('User.product.partials.comment_item', ['comment' => $comment, 'disc' => $disc])
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-center mt-4">
                        {{ $discussions->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="thre_revi_rgt">
                    <div class="main_feture mb-4">
                        <div class="feture_box review-breakdown-card p-3">
                            <h4 style="font-weight: 700; color: #002347; font-size: 18px;" class="mb-3">About {{ $bName }} Community</h4>
                            <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                                This space is dedicated for verified users and team members to discuss {{ $bName }} features, troubleshooting, pricing choices, and best practices.
                            </p>

                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-primary" @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="triggerAuthLoginModal()" @endif style="background: #06498b; border-color: #06498b; font-weight: 600;">
                                    <i class="fas fa-plus me-1"></i> Start New Discussion
                                </button>
                            </div>
                        </div>
                    </div>
                    <x-more-about-business :business="$business" />
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newDiscussionModal" tabindex="-1" aria-labelledby="newDiscussionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header" style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                <h5 class="modal-title font-weight-bold" id="newDiscussionModalLabel" style="font-weight: 700; color: #002347;">
                    Start a Discussion on {{ $bName }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.discussion.store') }}" method="POST" onsubmit="if(!IS_USER_LOGGED_IN) { event.preventDefault(); var m = bootstrap.Modal.getInstance(document.getElementById('newDiscussionModal')); if(m) m.hide(); triggerAuthLoginModal(); return false; }">
                @csrf
                <input type="hidden" name="business_id" value="{{ $business->id }}">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label font-weight-semibold" style="font-weight: 600; color: #1e293b;">Title / Question</label>
                        <input type="text" name="title" class="form-control" placeholder="What would you like to discuss or ask?" required style="border-color: #cbd5e1;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-semibold" style="font-weight: 600; color: #1e293b;">Details / Explanation</label>
                        <textarea name="content" class="form-control" rows="5" placeholder="Provide background context, specific questions or code snippets..." required style="border-color: #cbd5e1;"></textarea>
                    </div>

                    @guest
                        <div class="mb-3">
                            <label class="form-label font-weight-semibold" style="font-weight: 600; color: #1e293b;">Your Name (Optional)</label>
                            <input type="text" name="author_name" class="form-control" placeholder="e.g. Alex Miller" style="border-color: #cbd5e1;">
                        </div>
                    @endguest
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="background: #06498b; border-color: #06498b; font-weight: 600;">
                        Publish Discussion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const IS_USER_LOGGED_IN = {{ auth()->check() ? 'true' : 'false' }};

function triggerAuthLoginModal() {
    if (typeof openLoginModal === 'function') {
        openLoginModal();
        return;
    }
    const modal = document.getElementById('login-modal') || document.getElementById('loginModal');
    if (modal) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            let bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
            bsModal.show();
        } else {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        }
    } else {
        window.location.href = "{{ route('login', ['locale' => app()->getLocale()]) }}";
    }
}

function toggleCommentThread(commentId) {
    const item = document.getElementById('comment-' + commentId);
    if (item) {
        item.classList.toggle('is-collapsed');
    }
}

function toggleReplyForm(commentId) {
    if (!IS_USER_LOGGED_IN) {
        triggerAuthLoginModal();
        return;
    }
    const formContainer = document.getElementById('reply-form-' + commentId);
    if (formContainer) {
        formContainer.classList.toggle('d-none');
    }
}

function copyCommentLink(commentId) {
    const url = window.location.href.split('#')[0] + '#comment-' + commentId;
    navigator.clipboard.writeText(url).then(() => {
        alert('Comment link copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy link:', err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-comments-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const target = document.querySelector(targetId);
            if(target) {
                target.classList.toggle('show');
            }
        });
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.vote-trigger');
        if (!btn) return;

        e.preventDefault();
        if (!IS_USER_LOGGED_IN) {
            triggerAuthLoginModal();
            return;
        }

        const type = btn.getAttribute('data-type');
        const id = btn.getAttribute('data-id');
        const vote = btn.getAttribute('data-vote');

        fetch('{{ route("business.discussion.vote") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type, id, vote })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                if(type === 'discussion') {
                    const scoreEl = document.querySelector(`.vote-score-${id}`);
                    if(scoreEl) scoreEl.textContent = data.score;
                } else if(type === 'comment') {
                    const commentScore = document.querySelector(`.vote-score-comment-${id}`);
                    if(commentScore) {
                        commentScore.textContent = data.score !== undefined ? data.score : (data.upvotes - (data.downvotes || 0));
                    }
                }
            }
        })
        .catch(err => console.error('Vote error:', err));
    });

    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.comment-submit-form');
        if (!form) return;

        e.preventDefault();
        if (!IS_USER_LOGGED_IN) {
            triggerAuthLoginModal();
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.html) {
                const parentId = data.parent_id;
                const discussionId = data.discussion_id;

                if (parentId) {
                    const repliesContainer = document.getElementById('replies-container-' + parentId);
                    if (repliesContainer) {
                        repliesContainer.insertAdjacentHTML('beforeend', data.html);
                    }
                    const replyFormBox = document.getElementById('reply-form-' + parentId);
                    if (replyFormBox) {
                        replyFormBox.classList.add('d-none');
                    }
                } else {
                    const topLevelContainer = document.getElementById('top-level-comments-' + discussionId);
                    if (topLevelContainer) {
                        topLevelContainer.insertAdjacentHTML('beforeend', data.html);
                    }
                }

                const commentsCountBtn = document.querySelector(`[data-target="#comments-${discussionId}"]`);
                if (commentsCountBtn) {
                    commentsCountBtn.innerHTML = `<i class="far fa-comment-alt"></i> ${data.comments_count} Comments`;
                }

                form.reset();
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error('Comment submit error:', err);
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalText || 'Post Comment';
            }
        });
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.pin-trigger');
        if (!btn) return;

        e.preventDefault();
        if (!IS_USER_LOGGED_IN) {
            triggerAuthLoginModal();
            return;
        }
        const type = btn.getAttribute('data-type');
        const id = btn.getAttribute('data-id');

        fetch('{{ route("business.discussion.pin") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type, id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const pinText = btn.querySelector('.pin-text');
                if (data.is_pinned) {
                    btn.classList.add('text-primary', 'fw-bold');
                    if (pinText) pinText.textContent = 'Pinned';
                    btn.setAttribute('title', type === 'discussion' ? 'Unpin Discussion' : 'Unpin Comment');
                } else {
                    btn.classList.remove('text-primary', 'fw-bold');
                    if (pinText) pinText.textContent = 'Pin';
                    btn.setAttribute('title', type === 'discussion' ? 'Pin Discussion' : 'Pin Comment');
                }

                if (type === 'discussion') {
                    const postCard = document.getElementById('discussion-' + id);
                    if (postCard) {
                        const titleEl = postCard.querySelector('h3.h5');
                        let globalBadge = postCard.querySelector('.disc-pinned-badge');
                        let userBadge = postCard.querySelector('.user-disc-pinned-badge');
                        if (data.is_pinned) {
                            if (data.is_global) {
                                if (!globalBadge && titleEl) {
                                    if (userBadge) userBadge.remove();
                                    titleEl.insertAdjacentHTML('afterbegin', `<span class="badge bg-warning text-dark me-1 disc-pinned-badge" style="font-size: 11px;"><i class="fas fa-thumbtack"></i> Pinned</span>`);
                                }
                            } else {
                                if (!userBadge && !globalBadge && titleEl) {
                                    titleEl.insertAdjacentHTML('afterbegin', `<span class="badge bg-info text-white me-1 user-disc-pinned-badge" style="font-size: 11px;"><i class="fas fa-thumbtack"></i> Pinned by you</span>`);
                                }
                            }
                        } else {
                            if (data.is_global) {
                                if (globalBadge) globalBadge.remove();
                            } else {
                                if (userBadge) userBadge.remove();
                            }
                        }
                        reorderPinnedElement(postCard, data.is_pinned, 'discussion');
                    }
                } else if (type === 'comment') {
                    const commentEl = document.getElementById('comment-' + id);
                    if (commentEl) {
                        const headerEl = commentEl.querySelector('.comment-header');
                        let globalBadge = commentEl.querySelector('.comment-pinned-badge');
                        let userBadge = commentEl.querySelector('.user-pinned-badge');
                        const authorName = headerEl ? headerEl.querySelector('.comment-author-name') : null;

                        if (data.is_pinned) {
                            if (data.is_global) {
                                if (!globalBadge && authorName) {
                                    if (userBadge) userBadge.remove();
                                    authorName.insertAdjacentHTML('afterend', ` <span class="badge bg-warning text-dark me-1 comment-pinned-badge" style="font-size: 10px;"><i class="fas fa-thumbtack"></i> Pinned</span>`);
                                }
                            } else {
                                if (!userBadge && !globalBadge && authorName) {
                                    authorName.insertAdjacentHTML('afterend', ` <span class="badge bg-info text-white me-1 user-pinned-badge" style="font-size: 10px;"><i class="fas fa-thumbtack"></i> Pinned by you</span>`);
                                }
                            }
                        } else {
                            if (data.is_global) {
                                if (globalBadge) globalBadge.remove();
                            } else {
                                if (userBadge) userBadge.remove();
                            }
                        }
                        reorderPinnedElement(commentEl, data.is_pinned, 'comment');
                    }
                }
            }
        })
        .catch(err => console.error('Pin error:', err));
    });
});

function reorderPinnedElement(element, isPinned, type) {
    const container = element.parentElement;
    if (!container) return;

    const globalBadgeSelector = type === 'discussion' ? '.disc-pinned-badge' : '.comment-pinned-badge';
    const userBadgeSelector = type === 'discussion' ? '.user-disc-pinned-badge' : '.user-pinned-badge';

    const siblings = Array.from(container.children);
    const otherPinnedItems = siblings.filter(child => 
        child !== element && 
        (child.querySelector(globalBadgeSelector) || child.querySelector(userBadgeSelector))
    );

    if (isPinned) {
        if (otherPinnedItems.length > 0) {
            const lastPinned = otherPinnedItems[otherPinnedItems.length - 1];
            lastPinned.after(element);
        } else {
            const formEl = container.querySelector('.comment-submit-form');
            if (formEl) {
                formEl.after(element);
            } else {
                container.prepend(element);
            }
        }
    } else {
        const remainingPinnedItems = siblings.filter(child => 
            child !== element && 
            (child.querySelector(globalBadgeSelector) || child.querySelector(userBadgeSelector))
        );

        if (remainingPinnedItems.length > 0) {
            const lastPinned = remainingPinnedItems[remainingPinnedItems.length - 1];
            lastPinned.after(element);
        }
    }
}
</script>
@endpush
