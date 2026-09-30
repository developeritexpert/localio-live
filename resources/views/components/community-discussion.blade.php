@props(['business' => null, 'category' => null, 'bName' => null, 'bTranslation' => null, 'isTopRated' => false, 'headline' => null, 'subtext' => null, 'promptPlaceholder' => null])

@php
    if ($isTopRated) {
        $bName = $bName ?? 'Top Rated';
        $allDiscsQuery = \App\Models\BusinessDiscussion::where('is_top_rated', 1)->where('status', 'active')->orderByDesc('is_pinned')->orderByDesc('upvotes_count')->latest()->with(['user.country', 'comments' => fn($q) => $q->whereNull('parent_id')->orderByDesc('is_pinned')->orderBy('created_at', 'asc')->with(['user', 'replies.user', 'replies.replies.user'])]);
        $discList = (clone $allDiscsQuery)->take(4)->get();
        $totalDiscs = (clone $allDiscsQuery)->count();
        $discIds = (clone $allDiscsQuery)->pluck('id');
        $totalComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->count();
        $activeTodayDiscs = \App\Models\BusinessDiscussion::where('is_top_rated', 1)->where('status', 'active')->where('updated_at', '>=', now()->startOfDay())->count();
        $activeTodayComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->where('created_at', '>=', now()->startOfDay())->count();
        $totalActiveToday = $activeTodayDiscs + $activeTodayComments;
        $seeAllUrl = route('top-rated.discussions', ['locale' => app()->getLocale()]);
    } elseif ($category) {
        $catTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection
            ? $category->translations->first()
            : ($category->translations ?? $category->translation);
        $bName = $bName ?? ($catTrans->name ?? $category->name ?? 'Category');
        $allDiscsQuery = $category->discussions()->reorder()->where('status', 'active')->orderByDesc('is_pinned')->orderByDesc('upvotes_count')->latest()->with(['user.country', 'comments' => fn($q) => $q->whereNull('parent_id')->orderByDesc('is_pinned')->orderBy('created_at', 'asc')->with(['user', 'replies.user', 'replies.replies.user'])]);
        $discList = (clone $allDiscsQuery)->take(4)->get();
        $totalDiscs = (clone $allDiscsQuery)->count();
        $discIds = (clone $allDiscsQuery)->pluck('id');
        $totalComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->count();
        $activeTodayDiscs = \App\Models\BusinessDiscussion::where('category_id', $category->id)->where('updated_at', '>=', now()->startOfDay())->count();
        $activeTodayComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->where('created_at', '>=', now()->startOfDay())->count();
        $totalActiveToday = $activeTodayDiscs + $activeTodayComments;
        $seeAllUrl = url('/' . app()->getLocale() . '/' . ($catTrans->slug ?? $category->slug ?? '') . '/discussions');
    } else {
        $bTranslation = $bTranslation ?? ($business->translations->firstWhere('language_id', getCurrentLanguageID()) ?? $business->translations->first());
        $bName = $bName ?? ($bTranslation->title ?? $business->business_name ?? '');
        $bName = $bTranslation->name ?? 'Business';
        $allDiscsQuery = $business->discussions()->reorder()->where('status', 'active')->orderByDesc('is_pinned')->orderByDesc('upvotes_count')->latest()->with(['user.country', 'comments' => fn($q) => $q->whereNull('parent_id')->orderByDesc('is_pinned')->orderBy('created_at', 'asc')->with(['user', 'replies.user', 'replies.replies.user'])]);
        $discList = (clone $allDiscsQuery)->take(4)->get();
        $totalDiscs = (clone $allDiscsQuery)->count();
        $discIds = (clone $allDiscsQuery)->pluck('id');
        $totalComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->count();
        $activeTodayDiscs = \App\Models\BusinessDiscussion::where('business_id', $business->id)->where('updated_at', '>=', now()->startOfDay())->count();
        $activeTodayComments = \App\Models\BusinessDiscussionComment::whereIn('business_discussion_id', $discIds)->where('created_at', '>=', now()->startOfDay())->count();
        $totalActiveToday = $activeTodayDiscs + $activeTodayComments;
        $seeAllUrl = route('business.discussions', ['locale' => session('lang_code', 'en-us'), 'business_slug' => $bTranslation->slug ?? $business->id]);
    }
    $discHeadline = null;
    $discSubtext = null;
    if ($category) {
        $resolvedCatDisc = \App\Services\ContentTemplateService::resolveCategorySection(
            $category, 
            'category_page_discussions', 
            getCurrentLanguageID(),
            ['[count]' => number_format($totalDiscs)]
        );
        if (!empty($resolvedCatDisc['headline'])) {
            $discHeadline = $resolvedCatDisc['headline'];
            if (!str_contains($discHeadline, '(') && !str_contains($discHeadline, (string)$totalDiscs)) {
                $discHeadline .= ' (' . number_format($totalDiscs) . ')';
            }
        }
        if (!empty($resolvedCatDisc['subtext'])) {
            $discSubtext = $resolvedCatDisc['subtext'];
        } else {
            $discSubtext = 'Explore questions, experiences, and opinions shared by Localio users.';
        }
    }
    $finalHeadline = $headline ?? $discHeadline ?? (' Discussions (' . number_format($totalDiscs) . ')');
    $finalSubtext = $subtext ?? $discSubtext ?? null;
    $finalPromptPlaceholder = $promptPlaceholder ?? ('Ask a question or share something about ' . $bName . '...');
@endphp

<div class="mb-4 text-start">
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 8px; color: #002347; line-height: 1.3;">
       {!! \App\Services\ContentTemplateService::cleanHeading($finalHeadline) !!}
    </h2>
    @if(!empty($finalSubtext))
        <div class="community-discussion-subtext" style="color: #475569; margin: 0; font-size: 15px;">
            {!! $finalSubtext !!}
        </div>
    @endif
</div>

<!-- Inline Post Prompt Card -->
<div class="boxshadow_border bg-white p-3 mb-3 discussion-composer-card" style="border-radius: 14px; border: 1px solid #eef2f6; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
    <div class="d-flex align-items-center gap-2">
        @if(auth()->check() && auth()->user()->profile_image && auth()->user()->profile_image !== 'front/img/default.png')
            <img src="{{ asset(auth()->user()->profile_image) }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover; flex-shrink: 0;" alt="{{ auth()->user()->displayName() }}">
        @else
            <div class="user-avatar-initial" style="width: 36px; height: 36px; border-radius: 50%; background: #003f7d; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->displayName(), 0, 1)) : '?' }}
            </div>
        @endif
        <input type="text" class="form-control discussion-prompt-input" placeholder="{{ $finalPromptPlaceholder }}" readonly @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="openLoginModal()" @endif style="background: #ffffff; cursor: pointer; border-color: #cbd5e1; border-radius: 8px; font-size: 13px; padding-left: 16px; height: 38.45px;">
        <button type="button" class="blue-btn btn btn-primary px-3 text-nowrap d-inline-flex align-items-center gap-1" @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="openLoginModal()" @endif style="border-radius: 30px; font-size: 13px; font-weight: 600;  height: 40px; padding: 0 18px; background: #002347; border-color: #002347;">
            <i class="fas fa-pen" style="font-size: 12px;"></i> Post
        </button>
    </div>
</div>

<!-- Search & Sort Filter Card (Live Lag-free Filter) -->
<div class="boxshadow_border bg-white p-3 mb-3 d-none" style="border-radius: 14px; border: 1px solid #eef2f6; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
    <form class="row g-2 align-items-center" id="previewDiscussionFilterForm" onsubmit="return false;">
        <div class="col-sm-8 col-12">
            <div class="input-group position-relative">
                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; color: #94a3b8; border-radius: 8px 0 0 8px; height: 40px;">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="previewDiscussionSearchInput" class="form-control border-start-0 ps-0" placeholder="Search discussions..." aria-label="Search discussions" autocomplete="off" style="border-color: #cbd5e1; font-size: 13.5px; border-radius: 0 8px 8px 0; height: 40px; box-shadow: none;">
                <button type="button" id="previewDiscussionClearBtn" class="btn position-absolute text-muted d-none" title="Clear search" style="right: 8px; top: 50%; transform: translateY(-50%); z-index: 10; padding: 4px 8px; font-size: 13px; background: transparent; border: none;">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
        </div>
        <div class="col-sm-4 col-12">
            <select id="previewDiscussionSortSelect" class="form-select" aria-label="Sort discussions" style="border-color: #cbd5e1; font-size: 13.5px; border-radius: 8px; height: 40px;">
                <option value="popular">{{ static_text('sort_top_popular') ?: 'Top / Popular' }}<option>
                <option value="recent">{{ static_text('sort_most_recent_disc') ?: 'Most Recent' }}<option>
                <option value="commented">{{ static_text('sort_most_active_disc') ?: 'Most Active' }}<option>
            </select>
        </div>
    </form>
</div>

<div class="card p-4" style="border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
    <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 mb-3" style="border-bottom: 1px solid #f1f5f9;">
        <div class="d-flex align-items-center gap-2" style="font-size: 13.5px; color: #475569; font-weight: 500;">
            <span><strong>{{ number_format($totalDiscs) }}</strong> {{ $totalDiscs == 1 ? 'discussion' : 'discussions' }}</span>
            <span>&bull;</span>
            <span><strong>{{ number_format($totalComments) }}</strong> {{ $totalComments == 1 ? 'comment' : 'comments' }}</span>
            <span>&bull;</span>
            <span><strong>{{ number_format($totalActiveToday) }}</strong> {{ static_text('active_today_label') ?: 'active today' }}</span>
        </div>

        <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0 d-none">
            <a href="{{ $seeAllUrl }}" class="view-discussion-link">
                View all discussions
            </a>
        </div>
    </div>

    @if($discList->isEmpty())
        <div class="text-center py-4 text-muted">
            <p class="m-0">No discussions about {{ $bName }}.</p>
        </div>
    @else
        <div class="discussions-preview-list" id="previewDiscussionsList">
            @foreach($discList as $pDisc)
                @if($loop->first)
                    {{-- First Discussion Thread: Partially Expanded with Active Conversation --}}
                    <div class="discussion-preview-card discussion-first-expanded{{ !$loop->last ? ' pb-4 mb-4' : '' }}" @if(!$loop->last) style="border-bottom: 1px solid #f1f5f9;" @endif>
                        <div class="d-flex align-items-start main_cls_diss">
                            <div class="preview-vote-sidebar">
                                <button type="button" class="vote-btn vote-trigger" data-type="discussion" data-id="{{ $pDisc->id }}" data-vote="up" title="Upvote">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <span class="vote-score vote-score-{{ $pDisc->id }}" style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 4px 0;">{{ $pDisc->upvotes_count - $pDisc->downvotes_count }}</span>
                                <button type="button" class="vote-btn vote-trigger" data-type="discussion" data-id="{{ $pDisc->id }}" data-vote="down" title="Downvote">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <!-- Author header -->
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="asn-img" style="flex-shrink: 0;">
                                            @if ($pDisc->user)
                                                <a href="{{ $pDisc->user->profileUrl() }}" style="display: block; text-decoration: none;">
                                            @endif
                                            
                                            @if ($pDisc->user && $pDisc->user->profile_image && $pDisc->user->profile_image !== 'front/img/default.png')
                                                <img src="{{ asset($pDisc->user->profile_image) }}"
                                                    class="img-fluid profile-circle"
                                                    style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                                    alt="{{ $pDisc->author_display_name }}">
                                            @elseif ($pDisc->author_avatar)
                                                <img src="{{ asset($pDisc->author_avatar) }}"
                                                    class="img-fluid profile-circle"
                                                    style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                                    alt="{{ $pDisc->author_display_name }}">
                                            @else
                                                <div class="profile-circle"
                                                    style="width: 48px; height: 48px; border-radius: 50%; background-color: #003f7d; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <span style="color: white; font-weight: bold; font-size: 20px;">
                                                        {{ $pDisc->author_initial ?: 'A' }}
                                                    </span>
                                                </div>
                                            @endif

                                            @if ($pDisc->user)
                                                </a>
                                            @endif
                                        </div>

                                        <div class="asn-rating" style="display: flex; flex-direction: column;">
                                            <p style="font-size: 15px; font-weight: 600; margin: 0; color: #1e3050; line-height: 1.2;">
                                                @if ($pDisc->user)
                                                    <a href="{{ $pDisc->user->profileUrl() }}" style="color: inherit; text-decoration: none;">
                                                        {{ $pDisc->author_display_name }}
                                                    </a>
                                                @else
                                                    {{ $pDisc->author_display_name }}
                                                @endif
                                            </p>
                                            @if($pDisc->user && $pDisc->user->job_title)
                                                <p class="asn-rating-p" style="color: #5b6b7a; margin: 0; line-height: 1.2; font-weight: 500; font-size: 12px;">
                                                    {{ $pDisc->user->job_title }}
                                                </p>
                                            @elseif($pDisc->author_title)
                                                <p class="asn-rating-p" style="color: #5b6b7a; margin: 0; line-height: 1.2; font-weight: 500; font-size: 12px;">
                                                    {{ $pDisc->author_title }}
                                                </p>
                                            @endif
                                            @php
                                                $cSizeMap = [
                                                    '2' => 'Small Business (1-50 emp.)',
                                                    '3' => 'Mid-Market (51-1000 emp.)',
                                                    '4' => 'Enterprise (>1000 emp.)',
                                                ];
                                                $compSize = $pDisc->user ? $pDisc->user->company_size : null;
                                                $displayCompSize = $compSize ? ($cSizeMap[$compSize] ?? (function_exists('static_text') ? (static_text('company_size_' . $compSize) ?: $compSize) : $compSize)) : null;
                                            @endphp
                                            @if($displayCompSize)
                                                <p class="asn-rating-p" style="color: #5b6b7a; margin: 0; line-height: 1.2; font-weight: 500; font-size: 12px;">
                                                    {{ $displayCompSize }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-end" style="flex-shrink: 0;">
                                        <span class="text-muted" style="font-size: 12px;">{{ $pDisc->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <!-- Discussion Title -->
                                <h3 class="discussion-title" style="font-size: 18px; font-weight: 700; color: #1e3050; margin: 12px 0 8px; line-height: 1.35;">
                                    <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="discussion-title-link" style="color: inherit; text-decoration: none;">
                                        {{ $pDisc->title }}
                                    </a>
                                </h3>

                                <!-- Discussion Body Text -->
                                <div class="text-secondary mb-3 discussion-body-text" style="font-size: 14px; line-height: 1.6; color: #334155; word-break: break-word;">
                                    {!! nl2br(e($pDisc->content)) !!}
                                </div>

                                <!-- Active Discussion Conversation Preview -->
                                @if($pDisc->comments && $pDisc->comments->isNotEmpty())
                                    <div class="preview-active-discussion mt-3 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #edf2f7;">
                                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between mb-2 pb-2" style="border-bottom: 1px solid #eef2f6;">
                                            <span style="font-size: 13px; font-weight: 700; color: #1e3050;">
                                                <i class="far fa-comments text-primary me-1"></i> Community Discussion ({{ $pDisc->comments_count }} {{ $pDisc->comments_count == 1 ? 'reply' : 'replies' }})
                                            </span>
                                            <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="text-primary text-decoration-none fw-semibold" style="font-size: 12.5px;">
                                                Join discussion <i class="fas fa-arrow-right ms-1" style="font-size: 11px;"></i>
                                            </a>
                                        </div>

                                        <div class="top-level-comments-list">
                                            @foreach($pDisc->comments->take(1) as $comment)
                                                @include('User.product.partials.comment_item', ['comment' => $comment, 'disc' => $pDisc, 'level' => 0])
                                            @endforeach
                                        </div>

                                        @if($pDisc->comments_count > 1)
                                            <div class="mt-2 pt-2 text-center" style="border-top: 1px dashed #e2e8f0;">
                                                <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="text-muted text-decoration-none fw-semibold" style="font-size: 12.5px;">
                                                    + View all {{ $pDisc->comments_count }} replies on Discussions page &rarr;
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-2 d-flex align-items-center gap-3" style="font-size: 12.5px; color: #64748b; font-weight: 600;">
                                        <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 20px; font-size: 12.5px; padding: 4px 14px; text-decoration: none;">
                                            <i class="fas fa-reply me-1"></i> Be the first to join this discussion
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Subsequent Discussions: Title-Only Compact Card --}}
                    <div class="bg-white p-3 mb-2 discussion-preview-card discussion-title-only-card" style="border-radius: 12px; border: 1px solid #eef2f6; transition: border-color 0.2s ease, box-shadow 0.2s ease;">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                                <div class="asn-img" style="flex-shrink: 0;">
                                    @if ($pDisc->user && $pDisc->user->profile_image && $pDisc->user->profile_image !== 'front/img/default.png')
                                        <img src="{{ asset($pDisc->user->profile_image) }}"
                                            class="img-fluid rounded-circle"
                                            style="width: 28px; height: 28px; object-fit: cover;"
                                            alt="{{ $pDisc->author_display_name }}">
                                    @elseif ($pDisc->author_avatar)
                                        <img src="{{ asset($pDisc->author_avatar) }}"
                                            class="img-fluid rounded-circle"
                                            style="width: 28px; height: 28px; object-fit: cover;"
                                            alt="{{ $pDisc->author_display_name }}">
                                    @else
                                        <div class="rounded-circle"
                                            style="width: 28px; height: 28px; background-color: #003f7d; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="color: white; font-weight: bold; font-size: 12px;">
                                                {{ $pDisc->author_initial ?: 'A' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-grow-1">
                                    <h4 class="m-0 text-truncate" style="font-size: 15px; font-weight: 600; line-height: 1.4;">
                                        <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="discussion-title-link" style="color: #1e3050; text-decoration: none;">
                                            @if($pDisc->is_pinned)
                                                <span class="badge bg-warning text-dark me-1" style="font-size: 10px; font-weight: 600;"><i class="fas fa-thumbtack"></i> {{ static_text('pinned_discussion_badge') ?: 'Pinned' }}</span>
                                            @endif
                                            {{ $pDisc->title }}
                                        </a>
                                    </h4>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2" style="flex-shrink: 0;">
                                <span class="badge bg-light text-secondary border d-none d-sm-inline-flex align-items-center gap-1" style="font-size: 12px; font-weight: 600; padding: 4px 8px;">
                                    <i class="fas fa-arrow-up text-muted" style="font-size: 10px;"></i>
                                    <span class="vote-score-{{ $pDisc->id }}">{{ $pDisc->upvotes_count - $pDisc->downvotes_count }}</span>
                                </span>

                                <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="badge bg-light text-secondary border text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 12px; font-weight: 600; padding: 4px 8px;">
                                    <i class="far fa-comment text-muted" style="font-size: 11px;"></i>
                                    <span>{{ $pDisc->comments_count }}</span>
                                </a>

                                <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="text-muted ms-1" title="View discussion" style="font-size: 12px;">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- No Matching Search Results State -->
        <div id="previewDiscussionsNoMatches" class="text-center py-4 text-muted d-none" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 14px; margin-top: 10px;">
            <p class="m-0" style="font-size: 14px; color: #475569;">No discussions match "<strong id="previewDiscussionsQueryText" style="color: #002347;"></strong>".</p>
            <button type="button" class="btn btn-sm btn-link text-decoration-none mt-2" id="previewDiscussionsResetBtn" style="color: #003f7d; font-weight: 600; font-size: 13px;">
                <i class="fas fa-undo me-1"></i> Clear search
            </button>
        </div>

        <script>
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.vote-trigger');
                if (!btn) return;
                e.preventDefault();

                const type = btn.dataset.type;
                const id = btn.dataset.id;
                const vote = btn.dataset.vote;

                fetch("{{ route('business.discussion.vote') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ type, id, vote })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const scoreEl = document.querySelector(`.vote-score-${id}`);
                        if (scoreEl) scoreEl.textContent = data.score;

                        const parent = btn.closest('.preview-vote-sidebar') || btn.closest('.vote-sidebar') || btn.closest('.comment-vote-pill');
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
        </script>
    
        <script>
            (function() {
                const searchInput = document.getElementById('previewDiscussionSearchInput');
                const clearBtn = document.getElementById('previewDiscussionClearBtn');
                const sortSelect = document.getElementById('previewDiscussionSortSelect');
                const listContainer = document.getElementById('previewDiscussionsList');
                const noMatchesEl = document.getElementById('previewDiscussionsNoMatches');
                const queryTextEl = document.getElementById('previewDiscussionsQueryText');
                const resetBtn = document.getElementById('previewDiscussionsResetBtn');

                if (!listContainer) return;

                let debounceTimer = null;

                function applyFilterAndSort() {
                    const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
                    const sortVal = sortSelect ? sortSelect.value : 'popular';

                    if (clearBtn) {
                        clearBtn.classList.toggle('d-none', !query);
                    }

                    const items = Array.from(listContainer.querySelectorAll('.preview-discussion-item'));
                    let visibleCount = 0;

                    items.forEach(item => {
                        const title = item.dataset.title || '';
                        const content = item.dataset.content || '';
                        const author = item.dataset.author || '';

                        const isMatch = !query || title.includes(query) || content.includes(query) || author.includes(query);
                        if (isMatch) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Sort items in DOM
                    items.sort((a, b) => {
                        if (sortVal === 'recent') {
                            return (parseInt(b.dataset.created) || 0) - (parseInt(a.dataset.created) || 0);
                        } else if (sortVal === 'commented') {
                            return (parseInt(b.dataset.comments) || 0) - (parseInt(a.dataset.comments) || 0);
                        } else {
                            return (parseInt(b.dataset.score) || 0) - (parseInt(a.dataset.score) || 0);
                        }
                    });

                    items.forEach(item => listContainer.appendChild(item));

                    if (noMatchesEl) {
                        if (visibleCount === 0 && query) {
                            noMatchesEl.classList.remove('d-none');
                            if (queryTextEl) queryTextEl.textContent = searchInput.value.trim();
                        } else {
                            noMatchesEl.classList.add('d-none');
                        }
                    }
                }

                searchInput?.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(applyFilterAndSort, 120);
                });

                searchInput?.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(debounceTimer);
                        applyFilterAndSort();
                    }
                });

                clearBtn?.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                    }
                    clearTimeout(debounceTimer);
                    applyFilterAndSort();
                });

                resetBtn?.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                    }
                    clearTimeout(debounceTimer);
                    applyFilterAndSort();
                });

                sortSelect?.addEventListener('change', function() {
                    applyFilterAndSort();
                });
            })();
        </script>
    @endif
</div>

<!-- Footer Link -->
<div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
    <a href="{{ $seeAllUrl }}" class="see-all-discussions-link">
        View all discussions 
    </a>
</div>

<!-- New Discussion Modal -->
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
                @if($isTopRated)
                    <input type="hidden" name="is_top_rated" value="1">
                @elseif($category)
                    <input type="hidden" name="category_id" value="{{ $category->id }}">
                @elseif($business)
                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                @endif
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #002347; font-size: 13.5px;">{{ static_text('discussion_title_field_label') ?: 'Title / Question' }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="{{ static_text('discussion_title_placeholder') ?: 'What would you like to ask or share?' }}" required style="border-color: #cbd5e1; border-radius: 8px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #002347; font-size: 13.5px;">{{ static_text('discussion_details_field_label') ?: 'Details & Context' }} <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="5" placeholder="{{ static_text('discussion_details_placeholder') ?: 'Provide background context, specific questions, or personal experience...' }}" required style="border-color: #cbd5e1; border-radius: 8px; font-size: 14px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3" style="border-top: 1px solid #f1f5f9;">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 13.5px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold" style="border-radius: 8px; font-size: 13.5px; background: #002347; border-color: #002347;">
                        Post Discussion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
