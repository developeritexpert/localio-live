@extends('user_layout.master')
@push('styles')
    <style>
        /* FAQ Search Box Styling */
        .faq-search-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 35px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .faq-search-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .faq-search-input-group .search-icon {
            position: absolute;
            left: 18px;
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }
        .faq-search-input-group input {
            width: 100%;
            padding: 13px 20px 13px 48px;
            border: 1.5px solid #cbd5e1;
            border-radius: 30px;
            font-size: 15px;
            color: #1e293b;
            outline: none;
            transition: all 0.2s ease;
        }
        .faq-search-input-group input:focus {
            border-color: #06498b;
            box-shadow: 0 0 0 3px rgba(6, 73, 139, 0.12);
        }

        /* In-Page Category Navigation */
        .faq-category-nav {
            display: flex;
            align-items: center;
            gap: 25px;
            flex-wrap: wrap;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0;
            margin-bottom: 30px;
        }
        .faq-cat-tab {
            padding: 12px 0;
            font-size: 15px;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: -1px;
        }
        .faq-cat-tab:hover {
            color: #1e3050;
            background: transparent;
        }
        .faq-cat-tab.active {
            background: transparent;
            color: #1e3050;
            font-weight: 600;
            border-bottom: 2px solid #1e3050;
        }

        /* FAQ Feedback Row (Was this helpful?) */
        .faq-feedback-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
            padding-top: 14px;
            font-size: 13.5px;
            color: #64748b;
        }
        .faq-feedback-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn-vote-faq {
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0;
            font-size: 13px;
            font-weight: 600;
            outline: none;
            transition: all 0.15s ease;
        }
        .btn-vote-faq i {
            font-size: 15px;
            color: #64748b;
            font-weight: 400;
            transition: all 0.15s ease;
        }
        .btn-vote-faq:hover, .btn-vote-faq:hover i {
            color: #1e3050;
        }
        .btn-vote-faq.voted-yes, .btn-vote-faq.voted-yes i,
        .btn-vote-faq.voted-no, .btn-vote-faq.voted-no i {
            color: #1e3050 !important;
        }
        .btn-vote-faq.voted-yes i.fa-thumbs-up,
        .btn-vote-faq.voted-no i.fa-thumbs-down {
            font-weight: 900 !important;
        }

        /* Ask Community Banner */
        .ask-community-banner {
            background: linear-gradient(135deg, #002347 0%, #174889 100%);
            border-radius: 16px;
            padding: 36px 32px;
            color: #ffffff;
            margin-top: 45px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 6px 20px rgba(0, 35, 71, 0.12);
        }
        .ask-community-btn {
            background: #ff5722;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .ask-community-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }
        .top-rated-heading-sec {
            margin-top: 115px;
            padding-top: 30px;
            background-color: #f7f9fb;
            border-bottom: 1px solid #e2e8f0;
            /* margin-bottom: 25px; */
        }
    </style>
@endpush

@section('meta_title', format_meta_text('Top-Rated FAQs & Questions Answered | Localio'))
@section('meta_description', format_meta_text('Find answers to frequently asked questions about top-rated products, businesses, user reviews, and how rankings work on Localio.'))

@section('content')

<!-- Upper Header Section -->
<section class="top-rated-heading-sec help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/' . app()->getLocale()) }}" style="color: #64748b; text-decoration: none;">All</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" style="color: #64748b; text-decoration: none;">Top rated</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        FAQs
                    </li>
                </ol>
            </nav>
            <div class="col-4 d-flex justify-content-end">
                <x-social-icon />
            </div>
        </div>

        <!-- Top-Rated Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 1.2; color: #1e3050;">
                                {{ $topProductContents['faq_page_main_headline'] ?? 'Top rated on Localio FAQs' }}
                            </h1>
                        </div>
                        <p style="font-size: 16px; color: #444; margin-top: 4px; margin-bottom: 0; font-weight:400;">
                            {!! $topProductContents['faq_page_main_subheadline'] ?? 'Frequently asked questions and answers about top-rated rankings and products on Localio.' !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content & Right Sidebar Section -->
<section class="revie_img_sec py-5" style="background-color: #f8fafc; border-top: 1px solid #eef2f6;">
    <div class="container">
        <div class="row g-4">

            <!-- Left Side: FAQs Content, Search & Accordion -->
            <div class="col-lg-8 col-12">
                
                <!-- Section 1: Intro Title & Description -->
                <div class="faq-top-intro mb-4">
                    <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                        {{ $topProductContents['faq_page_about_headline'] ?? 'About Top-Rated Rankings' }}
                    </h2>
                    <div class="is_text" style="font-size: 15px; color: #475569; line-height: 1.7;">
                        <p>
                            {!! $topProductContents['faq_page_about_subheadline'] ?? 'Learn more about how Localio calculates community ratings, moderates verified reviews, and compiles top-rated leaderboards across software and local business categories.' !!}
                        </p>
                    </div>
                </div>

                <!-- Section 2: Search Option -->
                <div class="faq-search-wrapper">
                    <h3 style="font-size: 20px; font-weight: 600 !important; color: #002347; margin-bottom: 14px;">
                        {{ $topProductContents['faq_page_search_heading'] ?? 'Search Top-Rated FAQs' }}
                    </h3>
                    <div class="faq-search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="faqSearchInput" placeholder="Search questions..." autocomplete="off">
                    </div>
                </div>

                <!-- Main FAQ Section: Headline & In-Page Navigation -->
                <div class="main-faq-container">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin: 0;">
                            {{ $topProductContents['faq_page_list_headline'] ?? 'Frequently asked questions' }}
                        </h2>
                    </div>

                    <!-- In-Page Category Navigation -->
                    @if(isset($faqCategories) && count($faqCategories) > 1)
                    <div class="faq-category-nav" id="faqCategoryNav">
                        <button type="button" class="faq-cat-tab active" data-category-id="all">
                            All
                        </button>
                        @foreach($faqCategories as $catName)
                            <button type="button" class="faq-cat-tab" data-category-id="{{ \Illuminate\Support\Str::slug($catName) }}">
                                {{ $catName }}
                            </button>
                        @endforeach
                    </div>
                    @endif

                    <!-- FAQs Accordion List - Grouped by Category -->
                    <div id="faqAccordionContainer">
                        @php 
                            $totalFaqCount = 0; 
                            $itemGlobalIndex = 0;
                        @endphp

                        @if(isset($groupedFaqs) && count($groupedFaqs) > 0)
                            @foreach($groupedFaqs as $catName => $categoryFaqs)
                                @php $catSlug = \Illuminate\Support\Str::slug($catName); @endphp
                                <div class="faq-category-group mb-4" data-category-group="{{ $catSlug }}">
                                    @if(count($faqCategories) > 1)
                                    <h3 class="category-group-headline mb-3" style=" color: #1e3050;">
                                        {{ $catName }}
                                    </h3>
                                    @endif

                                    <div class="accordion" id="topRatedFaqAccordion_{{ $catSlug }}">
                                        @foreach($categoryFaqs as $faq)
                                            @php
                                                $totalFaqCount++;
                                                $itemGlobalIndex++;
                                                $question = $faq['question'] ?? '';
                                                $answer = $faq['answer'] ?? '';
                                            @endphp
                                            @if(!empty($question))
                                            <div class="accordion-item mb-3" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                                                <h2 class="accordion-header" id="faqHeading{{ $itemGlobalIndex }}">
                                                    <button class="accordion-button {{ $itemGlobalIndex > 1 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $itemGlobalIndex }}" aria-expanded="{{ $itemGlobalIndex === 1 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $itemGlobalIndex }}" style="font-weight: 600; font-size: 16px; color: #002347; background-color: #ffffff;">
                                                        {{ $question }}
                                                    </button>
                                                </h2>
                                                <div id="faqCollapse{{ $itemGlobalIndex }}" class="accordion-collapse collapse {{ $itemGlobalIndex === 1 ? 'show' : '' }}" aria-labelledby="faqHeading{{ $itemGlobalIndex }}" data-bs-parent="#topRatedFaqAccordion_{{ $catSlug }}">
                                                    <div class="accordion-body rich-text-content" style="font-size: 14.5px; color: #555; line-height: 1.7; background-color: #ffffff;">
                                                        <div class="faq-answer-text">
                                                            {!! $answer !!}
                                                        </div>

                                                        <!-- FAQ Feedback Row -->
                                                        <div class="faq-feedback-row">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span>Was this answer helpful?</span>
                                                                <div class="faq-feedback-actions">
                                                                    <button type="button" class="btn-vote-faq" onclick="voteFaqItem(this, true)" title="Helpful">
                                                                        
                                                                            <i class="far fa-thumbs-up"></i>
                                                                        Yes
                                                                    </button>
                                                                    <button type="button" class="btn-vote-faq" onclick="voteFaqItem(this, false)" title="Not helpful">
                                                                        
                                                                            <i class="far fa-thumbs-down"></i>
                                                                        No
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-4 text-center text-muted bg-light rounded border">
                                <p class="m-0">No FAQs available at this time.</p>
                            </div>
                        @endif

                        <div id="noFaqSearchResults" class="p-4 text-center text-muted bg-light rounded border" style="display: none;">
                            <p class="m-0">No questions found matching your search. Try different keywords or ask the community below.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Ask Community Bottom Banner -->
                <div class="ask-community-banner">
                    <div>
                        <h3 style="font-size: 22px; font-weight: 700; margin-bottom: 6px;">
                            {{ $topProductContents['faq_page_banner_headline'] ?? 'Still have a question about top-rated rankings?' }}
                        </h3>
                        <p style="font-size: 15px; color: #e2e8f0; margin-bottom: 0;">
                            {{ $topProductContents['faq_page_banner_subheadline'] ?? 'Ask the Localio community' }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" class="ask-community-btn blue-btn">
                           {{ $topProductContents['faq_page_banner_btn_text'] ?? 'Ask a question' }} <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
            <!-- End Left Side -->

            <!-- Right Side: Sidebar -->
            <div class="col-lg-4 col-12">
                <div class="d-flex flex-column gap-4">

                    <!-- 1. Recent Reviews Box -->
                    <div class="boxshadow_border bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <h5 class="m-0 card-h-title" style="font-size: 16px; font-weight: 700; color: #002347;">Recent reviews</h5>
                            <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" class="view-review-link">
                                View all
                            </a>
                        </div>

                        @if(isset($recentReviews) && $recentReviews->count() > 0)
                            @foreach($recentReviews as $rev)
                                @php
                                    $revTrans = $rev->translations->first();
                                    $u = $rev->user;
                                    if ($u && $u->user_type === 'admin') {
                                        $displayName = $rev->public_name ?? 'Public';
                                    } elseif ($u) {
                                        $displayName = $u->displayName();
                                    } else {
                                        $displayName = 'Anonymous';
                                    }
                                    $bName = $rev->business?->translations?->first()?->name ?? $rev->business?->name ?? 'Business';
                                @endphp
                                <div class="sidebar-review-card {{ !$loop->last ? 'pb-3 mb-3 border-bottom' : '' }}">
                                    <div class="review-header d-flex justify-content-between align-items-start">
                                        <div class="review-user d-flex align-items-center gap-2">
                                            @if($u && $u->profile_image && $u->profile_image !== 'front/img/default.png')
                                                <img src="{{ asset($u->profile_image) }}" class="rounded-circle" width="38" height="38" style="object-fit:cover;">
                                            @else
                                                <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <span style="color: white; font-weight: bold; font-size: 16px;">{{ strtoupper(substr($displayName, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">
        @if(isset($rev) && $rev->user_id)
            <a href="{{ route('user.profile', ['id' => $rev->user_id]) }}" style="color: inherit; text-decoration: none;">
                {{ $displayName }}
            </a>
        @else
            {{ $displayName }}
        @endif
        </h6>
        
                                                @php
                                                    $badge = isset($review) && $review->user ? $review->user->getContributorBadge() : (isset($u) ? $u->getContributorBadge() : null);
                                                @endphp
                                                @if($badge)
                                                    <div class="user-contributor-badge" style="font-size: 13px; color: #64748b; margin-top: 2px;">
                                                        {!! $badge->icon !!} {{ $badge->name }}
                                                    </div>
                                                @endif

                                                @if($u && $u->job_title)
                                                    <div style="font-size: 11px; color: #777; margin-top: 1px;">{{ $u->job_title }}</div>
                                                @elseif(!empty($bName))
                                                    <div style="font-size: 11px; color: #777; margin-top: 1px;">on {{ $bName }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $rev->created_at ? $rev->created_at->diffForHumans() : '' }}</small>
                                    </div>
                                    <h6 style="margin-top: 8px; margin-bottom: 3px; font-size: 14px; font-weight: 600; color: #1e3050;">
                                        {{ $revTrans->title ?? 'Review' }}
                                    </h6>
                                    <div class="rating-stars mb-1" style="font-size: 11px; color: #ff5722;">
                                        @for($s=1; $s<=5; $s++)
                                            @if($s<=floor($rev->rating))
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($s-0.5<=$rev->rating)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    @if($revTrans && !empty($revTrans->description))
                                        <p style="font-size: 13px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">{{ \Illuminate\Support\Str::limit(strip_tags($revTrans->description), 85) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="sidebar-review-card mb-3 pb-3 border-bottom">
                                <div class="review-header d-flex justify-content-between align-items-start">
                                    <div class="review-user d-flex align-items-center gap-2">
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="color: white; font-weight: bold; font-size: 16px;">B</span>
                                        </div>
                                        <div>
                                            <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">Beck K.</h6>
                                            <div style="font-size: 11px; color: #777;">Fugiat aute anim eni</div>
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">1 week ago</small>
                                </div>
                                <h6 style="margin-top: 8px; margin-bottom: 3px; font-size: 14px; font-weight: 600; color: #1e3050;">
                                    Rem sint adipisicin
                                </h6>
                                <div class="rating-stars mb-1" style="font-size: 11px; color: #ff5722;">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <p style="font-size: 13px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">Numquam officia rem</p>
                            </div>
                        @endif
                    </div>

                    <!-- 2. Recent Discussions Box -->
                    <div class="boxshadow_border bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <h5 class="m-0 card-h-title" style="font-size: 16px; font-weight: 700; color: #002347;">Recent discussions</h5>
                            <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" class="view-review-link">
                                View all
                            </a>
                        </div>

                        <div class="sidebar-review-card mb-3 pb-3 border-bottom">
                            <div class="review-header d-flex justify-content-between align-items-start">
                                <div class="review-user d-flex align-items-center gap-2">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-weight: bold; font-size: 16px;">M</span>
                                    </div>
                                    <div>
                                        <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">Marc L.</h6>
                                        <div style="font-size: 11px; color: #777;">Product Manager • Small Business</div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">2 hours ago</small>
                            </div>
                            <h6 style="margin-top: 8px; font-size: 13.5px; font-weight: 600; color: #1e3050;">
                                Is there a free tier for API access or is it trial only?
                            </h6>
                            <p style="font-size: 12.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                We are looking to integrate this into our workflow and want to test latency...
                            </p>
                        </div>

                        <div class="sidebar-review-card">
                            <div class="review-header d-flex justify-content-between align-items-start">
                                <div class="review-user d-flex align-items-center gap-2">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-weight: bold; font-size: 16px;">S</span>
                                    </div>
                                    <div>
                                        <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">Sarah J.</h6>
                                        <div style="font-size: 11px; color: #777;">CTO • Mid-Market</div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">1 day ago</small>
                            </div>
                            <h6 style="margin-top: 8px; font-size: 13.5px; font-weight: 600; color: #1e3050;">
                                How does the performance compare to alternatives in large datasets?
                            </h6>
                            <p style="font-size: 12.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                We noticed some latency spikes during queries with more than 10k items...
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            <!-- End Right Side -->

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // 1. Real-Time Search Filter
    document.getElementById('faqSearchInput')?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('#faqAccordionContainer .accordion-item');
        const groups = document.querySelectorAll('.faq-category-group');
        let totalVisible = 0;

        items.forEach(item => {
            const question = item.querySelector('.accordion-button')?.textContent.toLowerCase() || '';
            const answer = item.querySelector('.faq-answer-text')?.textContent.toLowerCase() || '';
            const isMatch = question.includes(query) || answer.includes(query);

            if (isMatch) {
                item.style.display = 'block';
                totalVisible++;
            } else {
                item.style.display = 'none';
            }
        });

        // Hide empty category groups
        groups.forEach(group => {
            const visibleInGroup = group.querySelectorAll('.accordion-item[style="display: block;"]').length;
            if (query !== '' && visibleInGroup === 0) {
                group.style.display = 'none';
            } else {
                group.style.display = 'block';
            }
        });

        const noResults = document.getElementById('noFaqSearchResults');
        if (noResults) {
            noResults.style.display = (totalVisible === 0 && query !== '') ? 'block' : 'none';
        }
    });

    // 2. In-Page Category Tab Filter
    document.querySelectorAll('.faq-cat-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.faq-cat-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const categoryId = this.dataset.categoryId;
            const groups = document.querySelectorAll('.faq-category-group');

            // Reset search input
            const searchInput = document.getElementById('faqSearchInput');
            if (searchInput) searchInput.value = '';

            groups.forEach(group => {
                const groupCat = group.dataset.categoryGroup;
                if (categoryId === 'all' || groupCat === categoryId) {
                    group.style.display = 'block';
                    group.querySelectorAll('.accordion-item').forEach(c => c.style.display = 'block');
                } else {
                    group.style.display = 'none';
                }
            });

            const noResults = document.getElementById('noFaqSearchResults');
            if (noResults) noResults.style.display = 'none';
        });
    });

    // 3. Helpful / Not Helpful Feedback Handler
    function voteFaqItem(btn, isHelpful) {
        const parentActions = btn.closest('.faq-feedback-actions');
        if (!parentActions) return;

        parentActions.querySelectorAll('.btn-vote-faq').forEach(b => {
            b.classList.remove('voted-yes', 'voted-no');
            const icon = b.querySelector('i');
            if (icon) {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });

        if (isHelpful) {
            btn.classList.add('voted-yes');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            }
        } else {
            btn.classList.add('voted-no');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            }
        }
    }
</script>
@endpush
