@props(['business' => null, 'category' => null, 'bName' => null, 'bTranslation' => null, 'isTopRated' => false, 'headline' => null, 'subtext' => null])

@php
    if ($isTopRated) {
        $bName = $bName ?? 'Top Rated';
        $allDiscsQuery = \App\Models\BusinessDiscussion::where('is_top_rated', 1)->where('status', 'active')->with(['user.country']);
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
        $allDiscsQuery = $category->discussions()->with(['user.country']);
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
        $allDiscsQuery = $business->discussions()->with(['user.country']);
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
    $finalHeadline = $headline ?? $discHeadline ?? ($bName . ' discussions (' . number_format($totalDiscs) . ')');
    $finalSubtext = $subtext ?? $discSubtext ?? null;
@endphp

<div class="mb-4 text-start">
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 8px; color: #002347; line-height: 1.3;">
       {!! \App\Services\ContentTemplateService::cleanHeading($finalHeadline) !!}
    </h2>
    @if(!empty($finalSubtext))
        <p style="color: #475569; margin: 0; font-size: 15px;">
            {!! $finalSubtext !!}
        </p>
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
        <input type="text" class="form-control discussion-prompt-input" placeholder="Ask a question or share something about {{ $bName }}..." readonly @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="openLoginModal()" @endif style="background: #ffffff; cursor: pointer; border-color: #cbd5e1; border-radius: 30px; font-size: 13.5px; padding-left: 16px; height: 42px;">
        <button type="button" class="blue-btn btn btn-primary px-3 text-nowrap d-inline-flex align-items-center gap-1" @if(auth()->check()) data-bs-toggle="modal" data-bs-target="#newDiscussionModal" @else onclick="openLoginModal()" @endif style="border-radius: 30px; font-size: 13px; font-weight: 600; line-height: 1.3; height: 40px; padding: 0 18px; background: #002347; border-color: #002347;">
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
                <option value="popular">Top / Popular</option>
                <option value="recent">Most Recent</option>
                <option value="commented">Most Active</option>
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
            <span><strong>{{ number_format($totalActiveToday) }}</strong> active today</span>
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
                <div class="p-3 mb-3 preview-discussion-item"
                     data-title="{{ strtolower($pDisc->title) }}"
                     data-content="{{ strtolower(strip_tags($pDisc->content)) }}"
                     data-author="{{ strtolower($pDisc->author_display_name) }}"
                     data-score="{{ (int)($pDisc->upvotes_count - $pDisc->downvotes_count) }}"
                     data-created="{{ $pDisc->created_at ? $pDisc->created_at->timestamp : 0 }}"
                     data-comments="{{ (int)$pDisc->comments_count }}"
                     style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; transition: opacity 0.2s ease;">
                    <div class="d-flex align-items-start">
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
                            <!-- Review-matching author header -->
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
                                        {{-- Country removed per request --}}
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

                            <!-- Discussion Title matching review headline style -->
                            <h3 class="discussion-title" style="font-size: 18px; font-weight: 700; color: #1e3050; margin: 12px 0 8px; line-height: 1.35;">
                                <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="discussion-title-link" style="color: inherit; text-decoration: none;">
                                    {{ $pDisc->title }}
                                </a>
                            </h3>

                            <p class="text-secondary m-0" style="font-size: 13.5px; line-height: 1.55; color: #475569; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ Str::limit(strip_tags($pDisc->content), 180) }}
                            </p>

                            <!-- Comments Count with discussion svg icon (no underline) -->
                            <div class="mt-2 d-flex align-items-center gap-3" style="font-size: 12.5px; color: #64748b; font-weight: 600;">
                                <a href="{{ $seeAllUrl }}#discussion-{{ $pDisc->id }}" class="text-decoration-none d-inline-flex align-items-center text-muted discussion-comment-count-link" style="text-decoration: none !important;">
                                    <svg viewBox="0 0 39 36" width="15" height="15" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="vertical-align: -2px; margin-right: 5px;">
                                        <path d="M5.01672 2.63798H33.965C35.2962 2.63798 36.3352 3.66561 36.3352 4.99009V24.0121C36.3352 25.3366 35.2962 26.3665 33.965 26.3665H7.55691C7.24307 26.3665 6.94339 26.5024 6.74002 26.7415L2.64931 31.5245V4.98953C2.64874 3.66561 3.68771 2.63798 5.01672 2.63798ZM5.01672 0.500567C2.53714 0.500567 0.5 2.52071 0.5 4.99066V34.4289C0.5 35.4237 1.74347 35.8786 2.39212 35.1218L8.05034 28.5028H33.9644C36.4474 28.5028 38.4805 26.4837 38.4805 24.0127V4.99009C38.4805 2.52071 36.4474 0.5 33.9644 0.5H5.01672V0.500567Z"/>
                                        <path d="M7.43411 10.6329C6.83305 10.625 6.34473 11.1042 6.34473 11.6996C6.34473 12.2973 6.83362 12.7771 7.43411 12.7686H31.5478C32.1455 12.7777 32.6372 12.2973 32.6372 11.6996C32.6372 11.1042 32.1455 10.6255 31.5478 10.6329H7.43411Z"/>
                                        <path d="M7.43411 17.5002C6.83305 17.49 6.34473 17.9704 6.34473 18.568C6.34473 19.1634 6.83362 19.6455 7.43411 19.637H18.2288C18.8265 19.6461 19.3176 19.1634 19.3176 18.568C19.3176 17.9704 18.8265 17.4905 18.2288 17.5002H7.43411Z"/>
                                    </svg>
                                    <span>{{ $pDisc->comments_count }} {{ $pDisc->comments_count == 1 ? 'comment' : 'comments' }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <button type="submit" class="btn btn-primary px-4 fw-semibold" style="border-radius: 8px; font-size: 13.5px; background: #002347; border-color: #002347;">
                        Post Discussion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
