@extends('user_layout.master')

@php
    $lang_id = getCurrentLanguageID();
    $bTranslation = $business->translations->firstWhere('language_id', $lang_id) ?? $business->translations->first();
    $bName = $bTranslation->name ?? 'Business';
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatName = $parentCatTrans->name ?? '';
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $subHeadline = 'Ask questions, share experiences, exchange tips, and discuss ' . $bName . ' with other users.';

    $totalDiscs = $totalDiscussionsCount ?? $business->discussions()->count();
    $discIds = $business->discussions()->pluck('id');
    $totalComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->count();
    $activeTodayDiscs = \App\Models\BusinessDiscussion::where('business_id', $business->id)->where('updated_at', '>=', now()->startOfDay())->count();
    $activeTodayComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->where('created_at', '>=', now()->startOfDay())->count();
    $totalActiveToday = $activeTodayDiscs + $activeTodayComments;
@endphp

@php
    $discMeta = \App\Services\MetaTagService::resolveBusinessDiscussionsMeta($business, $lang_id);
@endphp
@section('meta_title', format_meta_text($discMeta['title']))
@section('meta_description', format_meta_text($discMeta['description']))
<style>
    
    .top-rated-heading-sec {
        margin-top: 95px;
        padding-top: 30px;
        padding-bottom: 20px;
        background-color: #f7f9fb;
        border-bottom: 1px solid #e2e8f0;
    }
</style>

@section('content')

<section class="top-rated-heading-sec">
    <div class="container">

        <!-- Breadcrumb & Social Share -->
        <div class="bread_row row align-items-center mb-2">
            <div class="col-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0"
                        style="background: transparent; padding: 0; display: flex; align-items: center;">

                        @if($parentCatName)
                            <li class="breadcrumb-item">
                                @if($parentCatSlug)
                                    <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentCatSlug]) }}"
                                       style="color: inherit; text-decoration: none; font-size: 13px;">
                                        {{ $parentCatName }}
                                    </a>
                                @else
                                    <span style="color: #64748b; font-size: 13px;">
                                        {{ $parentCatName }}
                                    </span>
                                @endif
                            </li>
                        @endif

                        @if($catName)
                            <li class="breadcrumb-item">
                                @if($catSlug)
                                    <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}"
                                       style="color: inherit; text-decoration: none; font-size: 13px;">
                                        {{ $catName }}
                                    </a>
                                @else
                                    <span style="color: #64748b; font-size: 13px;">
                                        {{ $catName }}
                                    </span>
                                @endif
                            </li>
                        @endif

                        @if($business)
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.product_detail', [
                                    'locale' => app()->getLocale(),
                                    'id' => $bTranslation->slug ?? $business->id
                                ]) }}"
                                   style="color: inherit; text-decoration: none; font-size: 13px;">
                                    {{ $bName }}
                                </a>
                            </li>
                        @endif

                        <li class="breadcrumb-item active"
                            aria-current="page"
                            style="color: #002347 !important; text-decoration: none; font-weight: 500; margin-left: 2px;">
                            Discussions
                        </li>

                    </ol>
                </nav>
            </div>

            <div class="col-4 d-flex justify-content-end">
                <x-social-icon :business="$business" />
            </div>
        </div>


        <!-- Main Heading (with Business Logo and Visit Website Button) -->
        <div class="top-rated-heading-block">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-8 col-12 text-start">
                    <div class="top_head d-flex align-items-center gap-3">
                        <!-- Business Logo -->
                        <div class="top-product-logo">
                            <x-business-logo :business="$business" :name="$bName" />
                        </div>
                        <div>
                            <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                                <h1 style="margin: 0;">
                                    {{ $bName }} Discussions
                                </h1>
                            </div>

                            <p class="text-meta d-none">
                                Last updated {{ now()->subDay()->format('F j, Y') }}
                            </p>

                            <p style="margin-bottom: 0; font-size: 16px; color: #475569; margin-top: 4px;">
                                {!! $subHeadline !!}
                            </p>
                        </div>
                    </div>
                </div>

                @if($business->getTrackedUrl())
                <div class="col-md-4 col-12 text-md-end text-start mt-md-0 mt-3">
                    <a href="{{ $business->getTrackedUrl() }}" target="_blank" rel="sponsored" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border-radius: 8px;" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                        {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>


<!-- Main Discussion Body Container -->
<div class="container my-4" data-business-id="{{ $business->id }}">
    <div class="row g-4">
        <!-- Left Column: Discussion Feed -->
        <div class="col-lg-8">

            <!-- Inline Post Prompt Card -->
            <div class="boxshadow_border bg-white p-3 mb-3 discussion-composer-card" style="border-radius: 14px; border: 1px solid #eef2f6;">
                <div class="d-flex align-items-center gap-2">
                    @if(auth()->check() && auth()->user()->profile_image && auth()->user()->profile_image !== 'front/img/default.png')
                        <img src="{{ asset(auth()->user()->profile_image) }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover; flex-shrink: 0;" alt="{{ auth()->user()->displayName() }}">
                    @else
                        <div class="user-avatar-initial" style="width: 36px; height: 36px; border-radius: 50%; background: #003f7d; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                            {{ auth()->check() ? strtoupper(substr(auth()->user()->displayName(), 0, 1)) : '?' }}
                        </div>
                    @endif
                    <input type="text" class="form-control discussion-prompt-input" placeholder="Ask a question or share something about  {{ $bName }}..." readonly @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="openLoginModal()" @endif style="background: #ffffff; cursor: pointer; border-color: #cbd5e1; border-radius: 30px; font-size: 13.5px; padding-left: 16px; height: 42px;">
                    <button type="button" class="blue-btn btn btn-primary px-3 text-nowrap d-none d-sm-inline-flex align-items-center gap-1" @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="openLoginModal()" @endif style="border-radius: 20px; font-size: 13px; font-weight: 600; line-height: 1.3;">
                        <i class="fas fa-pen" style="font-size: 12px;"></i> Post
                    </button>
                </div>
            </div>          

            <!-- Discussions List Container -->
            <div id="discussionsFeedContainer" style="transition: opacity 0.2s ease;">
            @if($discussions->isEmpty())
                <div class="boxshadow_border bg-white text-center p-4 pb-5" style="border-radius: 16px; border: 1px dashed #cbd5e1;">
                <div class="mb-4">
                     <!-- <h2 style="font-size: 20px; font-weight: 700; color: #002347; margin-bottom: 6px; line-height: 1.3;">
                    Join the conversation
                    </h2>
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 8px;">
                    Connect with other {{ $bName }} users to ask questions, share experiences,and exchange useful tips.
                       </p> -->
                     <div class="d-flex align-items-center gap-2 pb-2" style="font-size: 13.5px; color: #475569; font-weight: 500; border-bottom: 1px solid #f1f5f9;">
                    <span><strong>{{ number_format($totalDiscs) }}</strong> {{ $totalDiscs == 1 ? 'discussion' : 'discussions' }}</span>
                    <span>&bull;</span>
                    <span><strong>{{ number_format($totalComments) }}</strong> {{ $totalComments == 1 ? 'comment' : 'comments' }}</span>
                    <span>&bull;</span>
                    <span><strong>{{ number_format($totalActiveToday) }}</strong> active today</span>
                    </div>
               </div>

                    <div style="width: 56px; height: 56px; margin: 0 auto 10px; border-radius: 50%; background: #f0f7ff; color: #214787; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <img src="{{ asset('front/img/discussions.svg') }}" alt="Discussions" style="width: 28px; height: 28px;">
                    </div>
                    <h3 style="font-weight: 700; color: #002347; font-size: 18px;" class="mb-2">No discussions about {{ $bName }}.</h3>
                    <p class="text-muted mb-3 d-none" style="font-size: 14px;">Be the first to start a conversation or ask a question about {{ $bName }}!</p>
                    <button type="button" class="blue-btn btn btn-primary px-4 py-2 fw-semibold" @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="openLoginModal()" @endif style="border-radius: 8px; font-size: 13.5px;">
                        <i class="fas fa-plus me-1"></i> Start a discussion 
                    </button>
                </div>
            @else
                @foreach($discussions as $disc)
                    <div class="boxshadow_border bg-white p-3 p-md-4 mb-3 discussion-item-card" id="discussion-{{ $disc->id }}" data-discussion-id="{{ $disc->id }}" style="border-radius: 16px; border: 1px solid #eef2f6;">
                        <div class="d-flex align-items-start">
                            <!-- Desktop Vote Sidebar -->
                            <div class="vote-sidebar d-none d-sm-flex flex-column align-items-center me-3 pe-3" style="border-right: 1px solid #f1f5f9; min-width: 44px;">
                                <button type="button" class="vote-btn vote-trigger" data-type="discussion" data-id="{{ $disc->id }}" data-vote="up" aria-label="Upvote" title="Upvote">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <span class="vote-score vote-score-{{ $disc->id }}" style="font-weight: 700; font-size: 13px; color: #1e293b; margin: 4px 0;">{{ $disc->upvotes_count - $disc->downvotes_count }}</span>
                                <button type="button" class="vote-btn vote-trigger" data-type="discussion" data-id="{{ $disc->id }}" data-vote="down" aria-label="Downvote" title="Downvote">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>

                            <!-- Discussion Content Body -->
                            <div class="flex-grow-1 min-w-0">
                                <!-- Author Header & Options -->
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="asn-img" style="flex-shrink: 0;">
                                            @if ($disc->user)
                                                <a href="{{ $disc->user->profileUrl() }}" style="display: block; text-decoration: none;">
                                            @endif

                                            @if ($disc->user && $disc->user->profile_image && $disc->user->profile_image !== 'front/img/default.png')
                                                <img src="{{ asset($disc->user->profile_image) }}"
                                                    class="img-fluid profile-circle"
                                                    style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                                    alt="{{ $disc->author_display_name }}">
                                            @elseif ($disc->author_avatar)
                                                <img src="{{ asset($disc->author_avatar) }}"
                                                    class="img-fluid profile-circle"
                                                    style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                                    alt="{{ $disc->author_display_name }}">
                                            @else
                                                <div class="profile-circle"
                                                    style="width: 48px; height: 48px; border-radius: 50%; background-color: #003f7d; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <span style="color: white; font-weight: bold; font-size: 20px;">
                                                        {{ $disc->author_initial ?: 'A' }}
                                                    </span>
                                                </div>
                                            @endif

                                            @if ($disc->user)
                                                </a>
                                            @endif
                                        </div>

                                        <div class="asn-rating" style="display: flex; flex-direction: column;">
                                            <p style="font-size: 15px; font-weight: 600; margin: 0; color: #1e3050; line-height: 1.2;">
                                                @if ($disc->user)
                                                    <a href="{{ $disc->user->profileUrl() }}" style="color: inherit; text-decoration: none;">
                                                        {{ $disc->author_display_name }}
                                                    </a>
                                                @else
                                                    {{ $disc->author_display_name }}
                                                @endif
                                            </p>
                                            {{-- Country removed per request --}}
                                            @if($disc->user && $disc->user->job_title)
                                                <p class="asn-rating-p" style="color: #5b6b7a; margin: 0; line-height: 1.2; font-weight: 500; font-size: 12px;">
                                                    {{ $disc->user->job_title }}
                                                </p>
                                            @elseif($disc->author_title)
                                                <p class="asn-rating-p" style="color: #5b6b7a; margin: 0; line-height: 1.2; font-weight: 500; font-size: 12px;">
                                                    {{ $disc->author_title }}
                                                </p>
                                            @endif
                                            @php
                                                $cSizeMap = [
                                                    '2' => 'Small Business (1-50 emp.)',
                                                    '3' => 'Mid-Market (51-1000 emp.)',
                                                    '4' => 'Enterprise (>1000 emp.)',
                                                ];
                                                $compSize = $disc->user ? $disc->user->company_size : null;
                                                $displayCompSize = $compSize ? ($cSizeMap[$compSize] ?? (function_exists('static_text') ? (static_text('company_size_' . $compSize) ?: $compSize) : $compSize)) : null;
                                            @endphp
                                            @if($displayCompSize)
                                                <p class="asn-rating-p" style="color: #5b6b7a; margin: 0; line-height: 1.2; font-weight: 500; font-size: 12px;">
                                                    {{ $displayCompSize }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2" style="flex-shrink: 0;">
                                        <span class="text-muted" style="font-size: 12px;">{{ $disc->created_at->diffForHumans() }}</span>

                                        @if($disc->is_pinned)
                                            <span class="badge bg-warning text-dark me-1" style="font-size: 11px;"><i class="fas fa-thumbtack"></i> Pinned</span>
                                        @endif

                                        @if(auth()->check() && (auth()->id() == $disc->user_id || auth()->user()->user_type === 'admin'))
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-link text-muted p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Options" style="font-size: 13px;">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px; border-radius: 10px; border-color: #e2e8f0;">
                                                    <li>
                                                        <button class="dropdown-item text-danger d-flex align-items-center gap-2" onclick="deleteDiscussion({{ $disc->id }})">
                                                            <i class="far fa-trash-alt"></i> Delete discussion
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Discussion Title -->
                                <h2 class="discussion-title h5 mb-2" style="font-size: 18px; font-weight: 700; color: #1e3050; line-height: 1.35; margin: 12px 0 8px;">
                                    {{ $disc->title }}
                                </h2>

                                <!-- Discussion Body -->
                                <div class="text-secondary mb-3 discussion-body-text" style="font-size: 14px; line-height: 1.6; color: #334155; word-break: break-word;">
                                    {!! nl2br(e($disc->content)) !!}
                                </div>

                                <!-- Action Toolbar -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2" style="border-top: 1px solid #f8fafc;">
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Mobile Vote Pill -->
                                        <div class="comment-vote-pill d-inline-flex d-sm-none align-items-center">
                                            <button type="button" class="comment-vote-btn vote-trigger" data-type="discussion" data-id="{{ $disc->id }}" data-vote="up" aria-label="Upvote">
                                                <i class="fas fa-arrow-up"></i>
                                            </button>
                                            <span class="comment-vote-count vote-score-{{ $disc->id }}">{{ $disc->upvotes_count - $disc->downvotes_count }}</span>
                                            <button type="button" class="comment-vote-btn vote-trigger" data-type="discussion" data-id="{{ $disc->id }}" data-vote="down" aria-label="Downvote">
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                        </div>

                                        <!-- Comments Toggle Button & Count -->
                                        <div class="d-flex align-items-center gap-1">
                                            <button class="post-action-btn toggle-comments-btn" data-target="#comments-{{ $disc->id }}" title="Toggle comments" aria-label="Toggle comments">
                                                <svg viewBox="0 0 39 36" width="16" height="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.01672 2.63798H33.965C35.2962 2.63798 36.3352 3.66561 36.3352 4.99009V24.0121C36.3352 25.3366 35.2962 26.3665 33.965 26.3665H7.55691C7.24307 26.3665 6.94339 26.5024 6.74002 26.7415L2.64931 31.5245V4.98953C2.64874 3.66561 3.68771 2.63798 5.01672 2.63798ZM5.01672 0.500567C2.53714 0.500567 0.5 2.52071 0.5 4.99066V34.4289C0.5 35.4237 1.74347 35.8786 2.39212 35.1218L8.05034 28.5028H33.9644C36.4474 28.5028 38.4805 26.4837 38.4805 24.0127V4.99009C38.4805 2.52071 36.4474 0.5 33.9644 0.5H5.01672V0.500567Z"/>
                                                    <path d="M7.43411 10.6329C6.83305 10.625 6.34473 11.1042 6.34473 11.6996C6.34473 12.2973 6.83362 12.7771 7.43411 12.7686H31.5478C32.1455 12.7777 32.6372 12.2973 32.6372 11.6996C32.6372 11.1042 32.1455 10.6255 31.5478 10.6329H7.43411Z"/>
                                                    <path d="M7.43411 17.5002C6.83305 17.49 6.34473 17.9704 6.34473 18.568C6.34473 19.1634 6.83362 19.6455 7.43411 19.637H18.2288C18.8265 19.6461 19.3176 19.1634 19.3176 18.568C19.3176 17.9704 18.8265 17.4905 18.2288 17.5002H7.43411Z"/>
                                                </svg>
                                            </button>
                                            <span class="text-muted fw-semibold" style="font-size: 13px;">{{ $disc->comments_count }}</span>
                                        </div>

                                        <!-- Share Button (Circular SVG) -->
                                        <button type="button" class="post-action-btn share-trigger-btn" onclick="openShareModal('{{ url()->current() }}#discussion-{{ $disc->id }}', '{{ addslashes($disc->title) }}', event)" title="Share discussion" aria-label="Share discussion">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="18" cy="5" r="3"></circle>
                                                <circle cx="6" cy="12" r="3"></circle>
                                                <circle cx="18" cy="19" r="3"></circle>
                                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                            </svg>
                                        </button>

                                        <!-- Admin Pin Button -->
                                        @if(auth()->check() && (auth()->user()->user_type === 'admin' || (auth()->user()->business_id && auth()->user()->business_id == $business->id)))
                                            <button type="button" class="post-action-btn pin-trigger {{ $disc->is_pinned ? 'text-primary fw-bold' : '' }}" data-type="discussion" data-id="{{ $disc->id }}" title="{{ $disc->is_pinned ? 'Unpin Discussion' : 'Pin Discussion' }}">
                                                <i class="fas fa-thumbtack"></i> <span class="pin-text">{{ $disc->is_pinned ? 'Pinned' : 'Pin' }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Collapsible Comments Container -->
                                <div class="comments-thread-container collapse mt-3" id="comments-{{ $disc->id }}">
                                    @if(auth()->check())
                                        <form action="{{ route('business.discussion.comment') }}" method="POST" class="mb-3 comment-submit-form">
                                            @csrf
                                            <input type="hidden" name="business_discussion_id" value="{{ $disc->id }}">
                                            <div class="mb-2">
                                                <textarea name="content" class="form-control" rows="2" placeholder="What are your thoughts?" required style="border-color: #cbd5e1; font-size: 13.5px; border-radius: 10px;"></textarea>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-sm btn-primary px-3 py-1 fw-semibold" style="border-radius: 6px; font-size: 12.5px;">
                                                    Post Comment
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="p-3 mb-3 text-center bg-light rounded-3" style="border: 1px dashed #cbd5e1;">
                                            <span class="text-muted" style="font-size: 13.5px;">Want to join this discussion? </span>
                                            <a href="javascript:void(0)" onclick="openLoginModal()" class="fw-semibold text-primary" style="font-size: 13.5px; text-decoration: underline;">Log in or create an account</a>
                                        </div>
                                    @endif

                                    <!-- Top-Level Comments List -->
                                    <div class="top-level-comments-list" id="top-level-comments-{{ $disc->id }}">
                                        @foreach($disc->comments as $comment)
                                            @include('User.product.partials.comment_item', ['comment' => $comment, 'disc' => $disc, 'level' => 0])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $discussions->withQueryString()->links() }}
                </div>
            @endif
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-sidebar-nav-wrapper">
                <!-- About Community Card -->
                <!-- <div class="boxshadow_border bg-white p-4 mb-4" style="border-radius: 16px; border: 1px solid #eef2f6;">
                    <h4 style="font-weight: 700; color: #002347; font-size: 16px;" class="mb-2">About {{ $bName }} Community</h4>
                    <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.6;">
                        This space is dedicated for verified users and community members to discuss {{ $bName }} features, troubleshooting, pricing choices, and best practices.
                    </p>
                    <button type="button" class="btn btn-outline-primary w-100 fw-semibold" @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="openLoginModal()" @endif style="border-radius: 8px; font-size: 13.5px;">
                        <i class="fas fa-plus me-1"></i> Start New Discussion
                    </button>
                </div> -->

                <!-- Join the Conversation White Box -->
                <div class="boxshadow_border bg-white p-4 mb-4"
                     style="
                        border-radius: 16px;
                        border: 1px solid #eef2f6;
                        text-align: left;
                     ">
                    <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                        <p style="
                            margin: 0;
                            font-weight: 700;
                            color:#002347;
                            font-size: 17px;
                        ">
                            Join the conversation 
                        </p>

                        <img src="{{ asset('front/img/top_rated_img.png') }}"
                             alt="Community Discussions"
                             style="
                                height: 32px;
                                width: auto;
                                object-fit: contain;
                             ">
                    </div>

                    <p style="margin-bottom: 14px; font-size: 13.5px; color: #64748b; line-height: 1.55;">
                        Connect with other {{ $bName }} users to ask questions, share experiences, and exchange useful tips. 
                    </p>

                    <a class="learn_mre_btn"
                       style="font-size: 13.5px; color: #002347; font-weight: 700; text-decoration: none; cursor:pointer; display: inline-flex; align-items: center; gap: 6px;"
                       @if(auth()->check())
                           data-bs-toggle="modal"
                           data-bs-target="#newDiscussionModal"
                       @else
                           onclick="openLoginModal()"
                       @endif>
                        <i class="fas fa-plus" style="font-size: 12px;"></i>
                        Start a discussion 
                    </a>
                </div>

                <!-- Search & Sort Filter Card (Live Lag-free Filter) -->
                <div class="boxshadow_border bg-white p-3 mb-3" style="border-radius: 14px; border:    1px solid #eef2f6;">
                    <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center mb-0" id="discussionFilterForm" onsubmit="return false;">
                    <div class="col-sm-8 col-12">
                        <div class="input-group position-relative">
                            <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; color: #94a3b8; border-radius: 8px 0 0 8px; height: 40px;">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="q" id="discussionLiveSearchInput" value="{{ request('q') }}" class="form-control border-start-0 ps-0" placeholder="Search discussions..." aria-label="Search discussions" autocomplete="off" style="border-color: #cbd5e1; font-size: 13.5px; border-radius: 0 8px 8px 0; height: 40px; box-shadow: none;">
                            <button type="button" id="discussionLiveClearBtn" class="btn position-absolute text-muted {{ request('q') ? '' : 'd-none' }}" title="Clear search" style="right: 8px; top: 50%; transform: translateY(-50%); z-index: 10; padding: 4px 8px; font-size: 13px; background: transparent; border: none;">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <select name="sort" id="discussionLiveSortSelect" class="form-select" aria-label="Sort discussions" style="border-color: #cbd5e1; font-size: 13.5px; border-radius: 8px; height: 40px;">
                            <option value="popular" {{ request('sort') == 'popular' || !request('sort') ? 'selected' : '' }}>Top / Popular</option>
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Most Recent</option>
                            <option value="commented" {{ request('sort') == 'commented' ? 'selected' : '' }}>Most Active</option>
                        </select>
                    </div>
                    </form>
                </div> 

                <!-- More About Business Component -->
                <x-more-about-business :business="$business" />
            </div>
        </div>
    </div>
</div>

<!-- Start New Discussion Modal -->
<div class="modal fade" id="newDiscussionModal" tabindex="-1" aria-labelledby="newDiscussionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid #eef2f6; overflow: hidden;">
            <div class="modal-header px-4 py-3" style="border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
                <h5 class="modal-title fw-bold" id="newDiscussionModalLabel" style="color: #002347; font-size: 17px;">
                    <img src="{{ asset('front/img/discussions.svg') }}" alt="Discussions" style="width: 22px; height: 22px; margin-right: 8px; vertical-align: -3px;"> Start a Discussion about {{ $bName }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.discussion.store') }}" method="POST">
                @csrf
                <input type="hidden" name="business_id" value="{{ $business->id }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #002347; font-size: 13.5px;">Title / Question <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="What would you like to ask or share?" required style="border-color: #cbd5e1; border-radius: 8px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #002347; font-size: 13.5px;">Details & Context <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="5" placeholder="Provide background context, specific questions, or personal experience..." required style="border-color: #cbd5e1; border-radius: 8px; font-size: 14px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3" style="border-top: 1px solid #f1f5f9;">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 13.5px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold" style="border-radius: 8px; font-size: 13.5px;">
                        Post Discussion
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

function toggleCommentThread(commentId) {
    const item = document.getElementById('comment-' + commentId);
    if (item) {
        item.classList.toggle('is-collapsed');
    }
}

function toggleReplyForm(commentId) {
    if (!IS_USER_LOGGED_IN) {
        if (typeof openLoginModal === 'function') {
            openLoginModal();
        }
        return;
    }
    const formContainer = document.getElementById('reply-form-' + commentId);
    if (formContainer) {
        formContainer.classList.toggle('d-none');
        const textarea = formContainer.querySelector('textarea');
        if (textarea && !formContainer.classList.contains('d-none')) {
            textarea.focus();
        }
    }
}

function deleteDiscussion(discussionId) {
    if (!confirm('Are you sure you want to delete this discussion?')) return;
    fetch('/business-discussion/' + discussionId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById('discussion-' + discussionId);
            if (card) {
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => card.remove(), 300);
            }
        } else {
            alert(data.message || 'Failed to delete discussion.');
        }
    })
    .catch(err => console.error('Delete error:', err));
}

function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment?')) return;
    fetch('/business-discussion/comment/' + commentId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const commentEl = document.getElementById('comment-' + commentId);
            if (commentEl) {
                commentEl.style.transition = 'opacity 0.3s ease';
                commentEl.style.opacity = '0';
                setTimeout(() => commentEl.remove(), 300);
            }
        } else {
            alert(data.message || 'Failed to delete comment.');
        }
    })
    .catch(err => console.error('Delete comment error:', err));
}

document.addEventListener('DOMContentLoaded', function() {
    // Toggle Comments Button
    document.querySelectorAll('.toggle-comments-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            if (!targetId) return;
            const target = document.querySelector(targetId);
            if (target) {
                target.classList.toggle('show');
            }
        });
    });

    // Upvote / Downvote Vote Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.vote-trigger');
        if (!btn) return;
        e.preventDefault();

        const type = btn.getAttribute('data-type');
        const id = btn.getAttribute('data-id');
        const vote = btn.getAttribute('data-vote');

        fetch('{{ route("business.discussion.vote") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: type, id: id, vote: vote })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update score labels everywhere for this item
                document.querySelectorAll(`.vote-score-${id}`).forEach(el => el.textContent = data.score);
                document.querySelectorAll(`.vote-score-comment-${id}`).forEach(el => el.textContent = data.score);

                const parent = btn.closest('.vote-sidebar') || btn.closest('.comment-vote-pill');
                if (parent) {
                    const upBtn = parent.querySelector('[data-vote="up"]');
                    const downBtn = parent.querySelector('[data-vote="down"]');
                    if (upBtn) upBtn.classList.remove('active-up');
                    if (downBtn) downBtn.classList.remove('active-down');

                    if (data.voted === 'up' && upBtn) upBtn.classList.add('active-up');
                    if (data.voted === 'down' && downBtn) downBtn.classList.add('active-down');
                }
            }
        })
        .catch(err => console.error('Vote error:', err));
    });

    // AJAX Comment & Reply Submission
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.comment-submit-form');
        if (!form) return;
        e.preventDefault();

        if (!IS_USER_LOGGED_IN) {
            if (typeof openLoginModal === 'function') {
                openLoginModal();
            }
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
        }

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }

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
                        form.reset();
                    }
                } else {
                    const topLevelContainer = document.getElementById('top-level-comments-' + discussionId);
                    if (topLevelContainer) {
                        topLevelContainer.insertAdjacentHTML('beforeend', data.html);
                        form.reset();
                    }
                }

                // Update comments counter
                const countBadge = document.querySelector(`[data-target="#comments-${discussionId}"] + span`);
                if (countBadge && data.comments_count !== undefined) {
                    countBadge.textContent = data.comments_count;
                }
            } else if (data.require_login) {
                if (typeof openLoginModal === 'function') {
                    openLoginModal();
                }
            } else {
                alert(data.message || 'Failed to submit comment.');
            }
        })
        .catch(err => {
            console.error('Comment error:', err);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });

    // Admin Pin Toggle Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.pin-trigger');
        if (!btn) return;
        e.preventDefault();

        const type = btn.getAttribute('data-type');
        const id = btn.getAttribute('data-id');

        fetch('{{ route("business.discussion.pin") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: type, id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.classList.toggle('text-primary', data.is_pinned);
                btn.classList.toggle('fw-bold', data.is_pinned);
                const pinText = btn.querySelector('.pin-text');
                if (pinText) pinText.textContent = data.is_pinned ? 'Pinned' : 'Pin';

                if (type === 'discussion') {
                    const postCard = document.getElementById('discussion-' + id);
                    if (postCard) {
                        let badge = postCard.querySelector('.disc-pinned-badge');
                        if (data.is_pinned) {
                            if (!badge) {
                                const titleHeader = postCard.querySelector('h2');
                                if (titleHeader) {
                                    titleHeader.insertAdjacentHTML('afterbegin', '<span class="badge bg-warning text-dark me-1 disc-pinned-badge" style="font-size: 11px;"><i class="fas fa-thumbtack"></i> Pinned</span> ');
                                }
                            }
                        } else if (badge) {
                            badge.remove();
                        }
                    }
                }
            }
        })
        .catch(err => console.error('Pin error:', err));
    });

    // Smooth scroll and highlight if arriving with anchor
    if (window.location.hash) {
        const targetEl = document.querySelector(window.location.hash);
        if (targetEl) {
            setTimeout(() => {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                targetEl.style.boxShadow = '0 0 0 3px rgba(33, 71, 135, 0.25)';
                targetEl.style.transition = 'box-shadow 0.6s ease';
                setTimeout(() => {
                    targetEl.style.boxShadow = '';
                }, 2500);
            }, 300);
        }
    }
});

let searchDebounceTimer = null;
const liveSearchInput = document.getElementById('discussionLiveSearchInput');
const liveSortSelect = document.getElementById('discussionLiveSortSelect');
const liveClearBtn = document.getElementById('discussionLiveClearBtn');
const feedContainer = document.getElementById('discussionsFeedContainer');

function performDiscussionsLiveSearch() {
    const q = (liveSearchInput ? liveSearchInput.value : '').trim();
    const sortVal = liveSortSelect ? liveSortSelect.value : 'popular';

    if (liveClearBtn) {
        liveClearBtn.classList.toggle('d-none', !q);
    }

    if (feedContainer) {
        feedContainer.style.opacity = '0.4';
        feedContainer.style.pointerEvents = 'none';
    }

    const currentUrl = new URL(window.location.href);
    if (q) {
        currentUrl.searchParams.set('q', q);
    } else {
        currentUrl.searchParams.delete('q');
    }
    currentUrl.searchParams.set('sort', sortVal);
    currentUrl.searchParams.delete('page');

    fetch(currentUrl.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newFeed = doc.getElementById('discussionsFeedContainer');
        if (newFeed && feedContainer) {
            feedContainer.innerHTML = newFeed.innerHTML;
        }
        window.history.replaceState(null, '', currentUrl.toString());
    })
    .catch(err => console.error('Discussions live search error:', err))
    .finally(() => {
        if (feedContainer) {
            feedContainer.style.opacity = '1';
            feedContainer.style.pointerEvents = 'auto';
        }
    });
}

liveSearchInput?.addEventListener('input', function() {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(performDiscussionsLiveSearch, 220);
});

liveSearchInput?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(searchDebounceTimer);
        performDiscussionsLiveSearch();
    }
});

liveSortSelect?.addEventListener('change', function() {
    clearTimeout(searchDebounceTimer);
    performDiscussionsLiveSearch();
});

liveClearBtn?.addEventListener('click', function() {
    if (liveSearchInput) {
        liveSearchInput.value = '';
        liveSearchInput.focus();
    }
    clearTimeout(searchDebounceTimer);
    performDiscussionsLiveSearch();
});

</script>
@endpush
