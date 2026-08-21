@extends('user_layout.master')
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/product-detail-components.css') }}">
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

        /* FAQ Accordion Styling Matching Design */
        .business-faq-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 16px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .business-faq-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .business-faq-header {
            padding: 18px 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
            background: #ffffff;
        }
        .business-faq-header h4 {
            font-size: 16px;
            font-weight: 700;
            color: #002347;
            margin: 0;
            padding-right: 15px;
            line-height: 1.4;
        }
        .faq-toggle-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #002347;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.2s ease;
        }
        .business-faq-card.open .faq-toggle-circle {
            background: #002347;
        }
        .business-faq-body {
            display: none;
            padding: 0 24px 20px 24px;
            font-size: 14.5px;
            color: #475569;
            line-height: 1.7;
            border-top: 1px solid #f1f5f9;
            background: #ffffff;
        }
        .business-faq-card.open .business-faq-body {
            display: block;
            padding-top: 16px;
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
            border-top: 1px solid #f1f5f9;
            font-size: 13.5px;
            color: #64748b;
        }
        .faq-feedback-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-vote-faq {
            background: transparent;
            border: none;
            color: #334155;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding: 0;
            font-size: 13px;
            font-weight: 600;
            outline: none;
        }
        .btn-vote-faq .vote-icon-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s ease;
        }
        .btn-vote-faq:hover .vote-icon-circle {
            background: #e2e8f0;
        }
        .btn-vote-faq i {
            font-size: 15px;
            color: #475569;
            font-weight: 400; /* Outline by default */
            transition: all 0.15s ease;
        }
        /* Fill icon solid on hover */
        .btn-vote-faq:hover i {
            color: #002347;
            font-weight: 900 !important;
        }
        /* Fill icon solid after voting */
        .btn-vote-faq.voted-yes i.fa-thumbs-up,
        .btn-vote-faq.voted-no i.fa-thumbs-down {
            color: #002347 !important;
            font-weight: 900 !important;
        }
        .faq-flag-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            padding: 0;
            transition: all 0.2s ease;
        }
        .faq-flag-btn:hover {
            background: #a0aec03b;
            color: #003f7d;
        }

        .dropdown-report-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .dropdown-report-btn:hover {
            color: #e11d48;
            background: #fff1f2;
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
        </div>

        <!-- Top-Rated Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <div class="asn-img" style="width: 55px; height: 55px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                        <i class="fas fa-question" style="font-size: 24px; color: #002347;"></i>
                    </div>
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 1.2; color: #1e3050;">
                                Top-Rated FAQs
                            </h1>
                        </div>
                        <p style="font-size: 16px; color: #444; margin-top: 4px; margin-bottom: 0; font-weight:400;">
                            Frequently asked questions and answers about top-rated rankings and products on Localio.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-end text-start mt-md-0 mt-3">
                <a href="{{ url('/' . app()->getLocale() . '/top-rated') }}" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Explore top-rated <i class="fas fa-arrow-right" style="font-size: 13px;"></i>
                </a>
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

                    <!-- FAQs Accordion List -->
                    <div id="faqAccordionContainer">
                        @if(isset($faqs) && count($faqs) > 0)
                            @foreach($faqs as $index => $faq)
                                @php
                                    $question = $faq['question'] ?? '';
                                    $answer = $faq['answer'] ?? '';
                                @endphp
                                @if(!empty($question))
                                <div class="business-faq-card" data-faq-id="{{ $index }}">
                                    <div class="business-faq-header" onclick="toggleFaqCard(this)">
                                        <h4>{{ $question }}</h4>
                                        <div class="faq-toggle-circle">+</div>
                                    </div>
                                    <div class="business-faq-body">
                                        <div class="faq-answer-text">
                                            {!! nl2br(e(strip_tags($answer))) !!}
                                        </div>

                                        <!-- Was this helpful? Row -->
                                        <div class="faq-feedback-row">
                                            <div class="d-flex align-items-center gap-2">
                                                <span>Was this helpful?</span>
                                                <div class="faq-feedback-actions">
                                                    <button type="button" class="btn-vote-faq" onclick="voteTopFaq({{ $index }}, true, this)">
                                                        👍 Yes
                                                    </button>
                                                    <button type="button" class="btn-vote-faq" onclick="voteTopFaq({{ $index }}, false, this)">
                                                        👎 No
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Report Flag Dropdown -->
                                            <div class="dropdown">
                                                <button type="button" class="dropdown-report-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-flag"></i> Report an issue
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13.5px; border-radius: 8px;">
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="openReportModal({{ $index }}, 'Outdated information')">Outdated information</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="openReportModal({{ $index }}, 'Incorrect information')">Incorrect information</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="openReportModal({{ $index }}, 'Unclear answer')">Unclear answer</a></li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="openReportModal({{ $index }}, 'Other')">Other</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
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
                        <h3 style="font-size: 22px; font-weight: 700; margin-bottom: 6px; color: #fdfdfd !important;">
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

                    <!-- 1. Recent Reviews Box (Instead of Highlighted reviews) -->
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

<!-- Report Issue Modal -->
<div class="modal fade" id="reportIssueModal" tabindex="-1" aria-labelledby="reportIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="reportIssueModalLabel" style="color: #002347; font-size: 20px;">Tell us more</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted small mb-3" id="reportModalReasonDisplay"></p>
                <input type="hidden" id="reportModalFaqId">
                <input type="hidden" id="reportModalReason">

                <div class="form-group mb-3">
                    <label for="reportModalDetails" class="form-label small fw-bold text-dark">Details (optional)</label>
                    <textarea class="form-control" id="reportModalDetails" rows="4" placeholder="Add more details..." style="border-radius: 10px; font-size: 14px;"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="submitReportBtn" style="background: #002347; border-color: #002347;" onclick="submitFaqReport()">Submit report</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. Toggle Accordion Card with +/- icon
    function toggleFaqCard(element) {
        const card = element.closest('.business-faq-card');
        const circle = card.querySelector('.faq-toggle-circle');
        const isOpen = card.classList.contains('open');

        if (isOpen) {
            card.classList.remove('open');
            circle.textContent = '+';
        } else {
            card.classList.add('open');
            circle.textContent = '−';
        }
    }

    // 2. Real-Time Search Filter
    document.getElementById('faqSearchInput')?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.business-faq-card');
        let totalVisible = 0;

        cards.forEach(card => {
            const question = card.querySelector('.business-faq-header h4')?.textContent.toLowerCase() || '';
            const answer = card.querySelector('.faq-answer-text')?.textContent.toLowerCase() || '';
            const isMatch = question.includes(query) || answer.includes(query);

            if (isMatch) {
                card.style.display = 'block';
                totalVisible++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noFaqSearchResults');
        if (noResults) {
            noResults.style.display = (totalVisible === 0 && query !== '') ? 'block' : 'none';
        }
    });

    // 3. Vote Handler
    function voteTopFaq(faqIndex, isHelpful, button) {
        const row = button.closest('.faq-feedback-actions');
        const yesBtn = row.querySelector('.btn-vote-faq:first-child');
        const noBtn = row.querySelector('.btn-vote-faq:last-child');

        if (isHelpful) {
            yesBtn.classList.add('voted-yes');
            noBtn.classList.remove('voted-no');
        } else {
            noBtn.classList.add('voted-no');
            yesBtn.classList.remove('voted-yes');
        }
    }

    // 4. Report Issue Modal Handler
    function openReportModal(faqId, reason) {
        document.getElementById('reportModalFaqId').value = faqId;
        document.getElementById('reportModalReason').value = reason;
        document.getElementById('reportModalReasonDisplay').textContent = 'Reason: ' + reason;
        document.getElementById('reportModalDetails').value = '';

        const modalEl = document.getElementById('reportIssueModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    function submitFaqReport() {
        const submitBtn = document.getElementById('submitReportBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit report';

            const modalEl = document.getElementById('reportIssueModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            alert('Thank you! Your feedback has been noted.');
        }, 500);
    }
</script>
@endpush
