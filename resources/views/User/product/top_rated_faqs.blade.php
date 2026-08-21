@extends('user_layout.master')
@push('styles')
    <style>
        /* FAQ Search Box Styling */
        .faq-search-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 30px;
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
    </style>
@endpush

@section('meta_title', format_meta_text('Top-Rated FAQs & Questions Answered | Localio'))
@section('meta_description', format_meta_text('Find answers to frequently asked questions about top-rated products, businesses, user reviews, and how rankings work on Localio.'))

@section('content')

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="margin-top: 100px; background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/' . app()->getLocale()) }}" style="color: #64748b; text-decoration: none;">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" style="color: #64748b; text-decoration: none;">Top-Rated</a>
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
                                Top rated on Localio FAQs
                            </h1>
                        </div>
                        <p style="font-size: 16px; color: #444; margin-top: 4px; margin-bottom: 0; font-weight:400;">
                            Frequently asked questions and answers about top-rated rankings and products on Localio.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content & Right Sidebar Section -->
<section class="revie_img_sec py-5" style="background-color: #ffffff;">
    <div class="container">
        <div class="row g-4">

            <!-- Left Side: FAQs Content, Search & Accordion -->
            <div class="col-lg-8 col-12">
                
                <!-- Section 1: Intro Title & Description -->
                <div class="faq-top-intro mb-4">
                    <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                        About Top-Rated Rankings
                    </h2>
                    <div class="is_text" style="font-size: 15px; color: #475569; line-height: 1.7;">
                        <p>
                            Learn more about how Localio calculates community ratings, moderates verified reviews, and compiles top-rated leaderboards across software and local business categories.
                        </p>
                    </div>
                </div>

                <!-- Section 2: Search Option -->
                <div class="faq-search-wrapper">
                    <h3 style="font-size: 20px; font-weight: 700; color: #002347; margin-bottom: 14px;">
                        Search Top-Rated FAQs
                    </h3>
                    <div class="faq-search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="faqSearchInput" placeholder="Search questions..." autocomplete="off">
                    </div>
                </div>

                <!-- Main FAQ Section: Headline & Accordion List -->
                <div class="main-faq-container">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin: 0;">
                            Frequently asked questions
                        </h2>
                    </div>

                    <!-- FAQs Accordion List - Exact Top-Rated Product Page Structure -->
                    <div id="faqAccordionContainer">
                        @if(isset($faqs) && count($faqs) > 0)
                            <div class="accordion" id="topRatedFaqAccordion">
                                @foreach($faqs as $fIndex => $faq)
                                    @php
                                        $question = $faq['question'] ?? '';
                                        $answer = $faq['answer'] ?? '';
                                    @endphp
                                    @if(!empty($question))
                                    <div class="accordion-item mb-3" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden;">
                                        <h2 class="accordion-header" id="faqHeading{{ $fIndex }}">
                                            <button class="accordion-button {{ $fIndex > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $fIndex }}" aria-expanded="{{ $fIndex === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $fIndex }}" style="font-weight: 600; font-size: 16px; color: #002347; background-color: #fdfdfd;">
                                                {{ $question }}
                                            </button>
                                        </h2>
                                        <div id="faqCollapse{{ $fIndex }}" class="accordion-collapse collapse {{ $fIndex === 0 ? 'show' : '' }}" aria-labelledby="faqHeading{{ $fIndex }}" data-bs-parent="#topRatedFaqAccordion">
                                            <div class="accordion-body rich-text-content" style="font-size: 14.5px; color: #555; line-height: 1.7; background-color: #fdfdfd;">
                                                {!! $answer !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
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
                            Still have a question about top-rated rankings?
                        </h3>
                        <p style="font-size: 15px; color: #e2e8f0; margin-bottom: 0;">
                            Ask the Localio community
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" class="ask-community-btn">
                            Explore top-rated <i class="fa-solid fa-arrow-right"></i>
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
                            <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" class="view-review-link" style="color: #06498b; font-size: 13px; font-weight: 600; text-decoration: none;">
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
                                                <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">{{ $displayName }}</h6>
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
                            <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" class="view-review-link" style="color: #06498b; font-size: 13px; font-weight: 600; text-decoration: none;">
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
    // Real-Time Search Filter
    document.getElementById('faqSearchInput')?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('#topRatedFaqAccordion .accordion-item');
        let totalVisible = 0;

        items.forEach(item => {
            const question = item.querySelector('.accordion-button')?.textContent.toLowerCase() || '';
            const answer = item.querySelector('.accordion-body')?.textContent.toLowerCase() || '';
            const isMatch = question.includes(query) || answer.includes(query);

            if (isMatch) {
                item.style.display = 'block';
                totalVisible++;
            } else {
                item.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noFaqSearchResults');
        if (noResults) {
            noResults.style.display = (totalVisible === 0 && query !== '') ? 'block' : 'none';
        }
    });
</script>
@endpush
