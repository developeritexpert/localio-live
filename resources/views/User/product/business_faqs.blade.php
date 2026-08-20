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

        /* In-Page Category Navigation */
        .faq-category-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 30px;
        }
        .faq-cat-tab {
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 14.5px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            background: transparent;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .faq-cat-tab:hover {
            color: #002347;
            background: #f1f5f9;
        }
        .faq-cat-tab.active {
            background: #002347;
            color: #ffffff;
            border-color: #002347;
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
        /* YouTube-style vote buttons */
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

        /* Flag icon button (same as reviews) */
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

@php
    $lang_id = getCurrentLanguageID();
    $bTransTmp = $business->translations->firstWhere('lang_id', $lang_id) ?? $business->translations->first();
    $bNameTmp = $bTransTmp->name ?? 'Business';
    $faqsMetaTitle = !empty($bTransTmp->faqs_meta_title) ? $bTransTmp->faqs_meta_title : "{$bNameTmp} FAQs & Questions Answered";
    $faqsMetaDesc = !empty($bTransTmp->faqs_meta_description) ? $bTransTmp->faqs_meta_description : ($bTransTmp->faqs_description ?? '');
@endphp

@section('meta_title', format_meta_text($faqsMetaTitle))
@if(!empty($faqsMetaDesc))
@section('meta_description', format_meta_text($faqsMetaDesc))
@endif

@section('content')

@php
    $lang_id = getCurrentLanguageID();
    $bTranslation = $business->translations->first();
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatName = $parentCatTrans->name ?? '';
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $bName = $bTranslation->name ?? 'Business';
    $stFaqSub = static_text('business_faqs_subheadline');
    if (!empty($stFaqSub) && $stFaqSub !== 'business_faqs_subheadline') {
        $subHeadline = $stFaqSub . ' ' . $bName . '.';
    } else {
        $subHeadline = 'Frequently asked questions and answers about ' . $bName . '.';
    }

    $faqTitle1 = $bTranslation->faqs_title ?? ('About ' . $bName);
    $faqDesc1  = $bTranslation->faqs_description ?? '';
@endphp

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="margin-top: 100px; background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
                    @if($parentCatName)
                        <li class="breadcrumb-item">
                            @if($parentCatSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentCatSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $parentCatName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $parentCatName }}</span>
                            @endif
                        </li>
                    @endif
                    @if($catName)
                        <li class="breadcrumb-item">
                            @if($catSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $catName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $catName }}</span>
                            @endif
                        </li>
                    @endif
                    @if($business)
                        <li class="breadcrumb-item">
                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}" style="color: #64748b; text-decoration: none;">{{ $bName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        FAQs
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon :business="$business" />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-2">
                    <!-- Business Icon -->
                    <div class="asn-img" style="width: 55px; height: 55px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                        <x-business-logo :business="$business" :name="$bName" />
                    </div>
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 1;">
                                {{ $bName }} FAQs
                            </h1>
                            <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                        </div>
                        <p style="font-size: 16px; color: #444; margin: 0; font-weight:400;">
                            {{ $subHeadline }}
                        </p>
                    </div>
                </div>
            </div>
            @if(!empty($business->is_affiliate))
            <div class="col-md-4 col-12 text-md-end text-start mt-md-0 mt-3">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Visit website <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Content & Right Sidebar Section -->
<section class="revie_img_sec py-5" style="background-color: #ffffff;">
    <div class="container">
        <div class="row g-4">

            <!-- Left Side: FAQs Content, Search & Accordion -->
            <div class="col-lg-8 col-12">
                
                <!-- Section 1: Title 1 & Description 1 -->
                @if(!empty($faqTitle1) || !empty($faqDesc1))
                <div class="faq-top-intro mb-4">
                    @if($faqTitle1)
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">{{ $faqTitle1 }}</h2>
                    @endif
                    @if($faqDesc1)
                        <div class="is_text" style="font-size: 15px; color: #475569; line-height: 1.7;">
                            {!! $faqDesc1 !!}
                        </div>
                    @endif
                </div>
                @endif

                <!-- Section 2: Search Option (Replacing 2nd text section) -->
                <div class="faq-search-wrapper">
                    <h3 style="font-size: 20px; font-weight: 700; color: #002347; margin-bottom: 14px;">
                        Search {{ $bName }} FAQs
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
                            Frequently asked questions about {{ $bName }}
                        </h2>
                    </div>

                    <!-- In-Page Category Navigation -->
                    @if(isset($faqCategories) && $faqCategories->count() > 0)
                    <div class="faq-category-nav" id="faqCategoryNav">
                        <button type="button" class="faq-cat-tab active" data-category-id="all">
                            All Questions
                        </button>
                        @foreach($faqCategories as $cat)
                            <button type="button" class="faq-cat-tab" data-category-id="{{ $cat->id }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                        @if(isset($uncategorizedFaqs) && $uncategorizedFaqs->count() > 0)
                            <button type="button" class="faq-cat-tab" data-category-id="general">
                                General
                            </button>
                        @endif
                    </div>
                    @endif

                    <!-- FAQs Accordion List -->
                    <div id="faqAccordionContainer">
                        @php $totalFaqCount = 0; @endphp

                        {{-- Grouped by Categories --}}
                        @if(isset($faqCategories) && $faqCategories->count() > 0)
                            @foreach($faqCategories as $cat)
                                @if($cat->faqs && $cat->faqs->count() > 0)
                                <div class="faq-category-group mb-4" data-category-group="{{ $cat->id }}">
                                    <h4 class="category-group-headline mb-3" style="font-size: 18px; font-weight: 700; color: #1e3050; border-left: 3px solid #002347; padding-left: 10px;">
                                        {{ $cat->name }}
                                    </h4>

                                    @foreach($cat->faqs as $faq)
                                        @php
                                            $totalFaqCount++;
                                            $trans = $faq->translations->first();
                                            $userVoted = $userVotes[$faq->id] ?? null;
                                        @endphp
                                        @if($trans)
                                        <div class="business-faq-card" data-faq-id="{{ $faq->id }}" data-category="{{ $cat->id }}">
                                            <div class="business-faq-header" onclick="toggleFaqCard(this)">
                                                <h4>{{ $trans->question }}</h4>
                                                <div class="faq-toggle-circle">+</div>
                                            </div>
                                            <div class="business-faq-body">
                                                <div class="faq-answer-text">
                                                    {!! nl2br(e(strip_tags($trans->answer))) !!}
                                                </div>

                                                <!-- Was this helpful? Row -->
                                                <div class="faq-feedback-row">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span>Was this helpful?</span>
                                                        <div class="faq-feedback-actions d-flex align-items-center" style="gap: 6px;">
                                                            <button type="button" class="btn-vote-faq {{ $userVoted === true ? 'voted-yes' : '' }}" onclick="voteFaq({{ $faq->id }}, true, this)" title="Helpful">
                                                                <span class="vote-icon-circle"><i class="far fa-thumbs-up"></i></span>
                                                                <span class="helpful-count" style="font-size: 12px; font-weight: 600; color: #64748b;">{{ $faq->helpful_count ?? 0 }}</span>
                                                            </button>
                                                            <button type="button" class="btn-vote-faq {{ $userVoted === false ? 'voted-no' : '' }}" onclick="voteFaq({{ $faq->id }}, false, this)" title="Not helpful">
                                                                <span class="vote-icon-circle"><i class="far fa-thumbs-down"></i></span>
                                                                <span class="not-helpful-count" style="font-size: 12px; font-weight: 600; color: #64748b;">{{ $faq->not_helpful_count ?? 0 }}</span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Report Flag -->
                                                    <button type="button" class="faq-flag-btn" onclick="openReportModal({{ $faq->id }})" title="Report an issue">
                                                        <i class="fas fa-flag" style="font-size: 13px;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                @endif
                            @endforeach
                        @endif

                        {{-- Uncategorized / General FAQs --}}
                        @if(isset($uncategorizedFaqs) && $uncategorizedFaqs->count() > 0)
                            <div class="faq-category-group mb-4" data-category-group="general">
                                @if(isset($faqCategories) && $faqCategories->count() > 0)
                                <h4 class="category-group-headline mb-3" style="font-size: 18px; font-weight: 700; color: #1e3050; border-left: 3px solid #002347; padding-left: 10px;">
                                    General
                                </h4>
                                @endif

                                @foreach($uncategorizedFaqs as $faq)
                                    @php
                                        $totalFaqCount++;
                                        $trans = $faq->translations->first();
                                        $userVoted = $userVotes[$faq->id] ?? null;
                                    @endphp
                                    @if($trans)
                                    <div class="business-faq-card" data-faq-id="{{ $faq->id }}" data-category="general">
                                        <div class="business-faq-header" onclick="toggleFaqCard(this)">
                                            <h4>{{ $trans->question }}</h4>
                                            <div class="faq-toggle-circle">+</div>
                                        </div>
                                        <div class="business-faq-body">
                                            <div class="faq-answer-text">
                                                {!! nl2br(e(strip_tags($trans->answer))) !!}
                                            </div>

                                            <!-- Was this helpful? Row -->
                                            <div class="faq-feedback-row">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span>Was this helpful?</span>
                                                    <div class="faq-feedback-actions d-flex align-items-center" style="gap: 6px;">
                                                        <button type="button" class="btn-vote-faq {{ $userVoted === true ? 'voted-yes' : '' }}" onclick="voteFaq({{ $faq->id }}, true, this)" title="Helpful">
                                                            <span class="vote-icon-circle"><i class="far fa-thumbs-up"></i></span>
                                                            <span class="helpful-count" style="font-size: 12px; font-weight: 600; color: #64748b;">{{ $faq->helpful_count ?? 0 }}</span>
                                                        </button>
                                                        <button type="button" class="btn-vote-faq {{ $userVoted === false ? 'voted-no' : '' }}" onclick="voteFaq({{ $faq->id }}, false, this)" title="Not helpful">
                                                            <span class="vote-icon-circle"><i class="far fa-thumbs-down"></i></span>
                                                            <span class="not-helpful-count" style="font-size: 12px; font-weight: 600; color: #64748b;">{{ $faq->not_helpful_count ?? 0 }}</span>
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Report Flag -->
                                                <button type="button" class="faq-flag-btn" onclick="openReportModal({{ $faq->id }})" title="Report an issue">
                                                    <i class="fas fa-flag" style="font-size: 13px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if($totalFaqCount === 0)
                            <div class="p-4 text-center text-muted bg-light rounded border">
                                <p class="m-0">No FAQs available for {{ $bName }} at this time.</p>
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
                            Still have a question about {{ $bName }}?
                        </h3>
                        <p style="font-size: 15px; color: #e2e8f0; margin-bottom: 0;">
                            Ask the Localio community
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#sectionDiscussions" class="ask-community-btn">
                            Ask a question <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
            <!-- End Left Side -->

            <!-- Right Side: Sidebar -->
            <div class="col-lg-4 col-12">
                <div class="d-flex flex-column gap-4">

                    <!-- 1. USPs -->
                    @if(!empty($business->is_affiliate) && $business->usps && $business->usps->count() > 0)
                        <div class="feture_box lft_check_box size15 bg-white p-4 boxshadow_border" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                            <ul class="list-unstyled mb-0">
                                @foreach($business->usps as $usp)
                                    @php $uText = $usp->text ?? $usp->usp_text ?? ''; @endphp
                                    @if(!empty($uText))
                                        <li class="d-flex flex-row align-items-center size15 mb-2">
                                            <div class="grn_chk me-2">
                                                <img src="{{ asset('front/img/green-tick.svg') }}" width="18">
                                            </div>
                                            <p class="m-0" style="color: #334155;">{{ $uText }}</p>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- 2. Review Breakdown -->
                    <div class="boxshadow_border feture_box review-breakdown-card bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        {{-- Header & Overall Rating --}}
                        <div class="review-header-box top_review_bx" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom:15px; border-bottom: 1px solid #f0f0f0;">
                            <div class="overall-rating-box" style="display: flex; flex-direction: column; align-items: flex-start;">
                                <span class="overall-rating-number" style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1;">
                                    {{ number_format($averageRating, 1) }}
                                </span>
                                <div class="rating-stars" style="margin-top: 10px; margin-bottom: 6px; display: flex; gap: 4px;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($averageRating))
                                            <i class="fas fa-star text-warning" style="font-size: 18px;"></i>
                                        @elseif ($i - 0.5 <= $averageRating)
                                            <i class="fas fa-star-half-alt text-warning" style="font-size: 18px;"></i>
                                        @else
                                            <i class="far fa-star text-warning" style="font-size: 18px;"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="f-12" style="color: #666;">Community rating · {{ number_format($totalReviews) }} {{ $totalReviews == 1 ? 'review' : 'reviews' }}</span>
                            </div>
                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section14" class="view-review-link" style="color: #06498b; font-weight: 600; font-size: 14px; text-decoration: none; padding-top: 5px;">
                                View all reviews
                            </a>
                        </div>

                        <h2 class="breakdown-title" style="margin-bottom: 15px; font-size: 16px; font-weight: 700; color: #002347;">
                            Review breakdown
                        </h2>

                        {{-- Breakdown --}}
                        <div class="review-progress-list">
                            @if(isset($criteria) && count($criteria) > 0)
                                @foreach ($criteria as $criterion)
                                    <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                        <p class="m-0" style="font-size: 12px; font-weight: 500; color: #444;">{{ $criterion->name }}</p>
                                        <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                            <progress class="progress-bar w-100" value="{{ $criterion->average_rating * 20 }}" max="100" style="height: 8px;"></progress>
                                            <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 35px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="recommendation-rate mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 600; color: #002347; font-size: 14px;">Recommended by users</span>
                            <strong style="color: #002347; font-size: 14px; font-weight:600;">{{ $recommendPercent }}%</strong>
                        </div>

                        <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 600; color: #1e3050; font-size: 14px;">Do you recommend {{ $bName }}?</span>
                            <div style="display: flex; gap: 8px;">
                                @auth
                                    <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                        <i class="far fa-thumbs-up"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                        <i class="far fa-thumbs-down"></i>
                                    </a>
                                @else
                                    <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                        <i class="far fa-thumbs-up"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                        <i class="far fa-thumbs-down"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <!-- 3. Starting Price & Free Trial -->
                    @php
                        $hasFreeTrial = false;
                        if ($business->relationLoaded('products')) {
                            $hasFreeTrial = $business->products->flatMap->pricingOptions->contains('slug', 'free-trial');
                        }
                        if (!$hasFreeTrial && $business->pricingOptions) {
                            $hasFreeTrial = $business->pricingOptions->contains('slug', 'free-trial');
                        }
                        $hasStartingPrice = !empty($startingPrice) && is_numeric($startingPrice) && $startingPrice > 0;
                    @endphp

                    @if(!empty($business->is_affiliate) && ($hasStartingPrice || $hasFreeTrial))
                        <div class="innr_price_trail d-flex flex-column gap-3">
                            @if($hasStartingPrice)
                            <div class="feture_box str_prc_box p-4 bg-white" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                                <h6 class="starting-price-title" style="font-size: 13px; color: #64748b; font-weight: 600;">Starting price</h6>
                                <h2 class="starting-price-value" style="font-size: 32px; font-weight: 700; color: #002347;">{{ $currency }}{{ $startingPrice }}</h2>
                                <p class="starting-price-text" style="font-size: 13px; color: #64748b;">Flat Rate, Per {{ ucfirst($timeUnit) }}</p>
                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section6" class="starting-price-link" style="color: #06498b; font-weight: 600; font-size: 13px;">
                                    View pricing
                                </a>
                            </div>
                            @endif
                            @if($hasFreeTrial)
                            <div class="fre_trail feture_box size22 p-4 bg-white" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                                <div class="grn_check_big mb-2">
                                    <img src="{{ asset('front/img/new-grn-chk.svg') }}" width="24">
                                </div>
                                <h6 class="blue-text big-bld" style="font-size: 16px; font-weight: 700; color: #002347;">Free trial available</h6>
                                <div class="accor-btn mt-2">
                                    <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="cta cta_white blue_t_org_btn" style="text-transform:none !important; padding: 6px 20px; font-size: 13px; border-radius: 20px;">Claim now</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif

                    <!-- 4. Highlighted Reviews -->
                    @if(isset($topReviews) && $topReviews->count() > 0)
                        <div class="boxshadow_border bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                            <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                <h5 class="m-0 card-h-title" style="font-size: 16px; font-weight: 700; color: #002347;">Highlighted reviews</h5>
                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section14" class="view-review-link" style="color: #06498b; font-size: 13px; font-weight: 600; text-decoration: none;">
                                    View all
                                </a>
                            </div>

                            @foreach($topReviews->take(2) as $rev)
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
                                @endphp
                                <div class="sidebar-review-card {{ !$loop->last ? 'pb-3 mb-3 border-bottom' : '' }}">
                                    <div class="review-header d-flex justify-content-between align-items-start">
                                        <div class="review-user d-flex align-items-center gap-2">
                                            @if($u && $u->profile_image && $u->profile_image !== 'front/img/default.png')
                                                <img src="{{ asset($u->profile_image) }}" class="rounded-circle" width="38" height="38" style="object-fit:cover;">
                                            @else
                                                <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <span style="color: white; font-weight: bold; font-size: 16px;">{{ strtoupper(substr($u->first_name ?? 'A', 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">{{ $displayName }}</h6>
                                                @if($u && $u->job_title)
                                                    <div style="font-size: 11px; color: #777; margin-top: 1px;">{{ $u->job_title }}</div>
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
                        </div>
                    @endif

                    <!-- 5. Recent Discussions -->
                    <div class="boxshadow_border bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <h5 class="m-0 card-h-title" style="font-size: 16px; font-weight: 700; color: #002347;">Recent discussions</h5>
                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#sectionDiscussions" class="view-review-link" style="color: #06498b; font-size: 13px; font-weight: 600; text-decoration: none;">
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
                            <h6 style="cursor: pointer; margin-top: 8px; font-size: 13.5px; font-weight: 600; color: #1e3050;" onclick="document.getElementById('sectionDiscussions')?.scrollIntoView({behavior: 'smooth'})">
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
                            <h6 style="cursor: pointer; margin-top: 8px; font-size: 13.5px; font-weight: 600; color: #1e3050;" onclick="document.getElementById('sectionDiscussions')?.scrollIntoView({behavior: 'smooth'})">
                                How does the performance compare to alternatives in large datasets?
                            </h6>
                            <p style="font-size: 12.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                We noticed some latency spikes during queries with more than 10k items...
                            </p>
                        </div>
                    </div>

                    <!-- 6. More about Business Card -->
                    <x-more-about-business :business="$business" />

                </div>
            </div>
            <!-- End Right Side -->

        </div>
    </div>
</section>

<!-- Report Issue Modal -->
<div class="modal fade" id="reportIssueModal" tabindex="-1" aria-labelledby="reportIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 40px rgba(0,0,0,0.14);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 20px 24px 16px;">
                <div>
                    <h5 class="modal-title fw-bold m-0" id="reportIssueModalLabel" style="color: #002347; font-size: 18px;">Report an issue</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size: 13px;">Select the reason that best describes the issue</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 20px 24px;">
                <input type="hidden" id="reportModalFaqId">
                <input type="hidden" id="reportModalReason">

                <!-- Radio option cards -->
                <div class="d-flex flex-column gap-2" id="reportReasonOptions">
                    @foreach([
                        ['value' => 'Outdated information',   'icon' => 'fa-clock',           'desc' => 'The information is no longer accurate'],
                        ['value' => 'Incorrect information',  'icon' => 'fa-times-circle',     'desc' => 'The answer contains factual errors'],
                        ['value' => 'Unclear answer',         'icon' => 'fa-question-circle',  'desc' => 'The answer is confusing or incomplete'],
                        ['value' => 'Spam or advertising',    'icon' => 'fa-ban',              'desc' => 'Promotional or irrelevant content'],
                        ['value' => 'Other',                  'icon' => 'fa-ellipsis-h',       'desc' => 'Something else is wrong'],
                    ] as $opt)
                    <label class="report-option-card d-flex align-items-center gap-3 p-3 rounded-3 cursor-pointer" style="border: 1.5px solid #e2e8f0; cursor: pointer; transition: all 0.15s ease;" onclick="selectReportReason('{{ $opt['value'] }}', this)">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas {{ $opt['icon'] }}" style="color: #64748b; font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 14px; font-weight: 600; color: #1e3050;">{{ $opt['value'] }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ $opt['desc'] }}</div>
                        </div>
                        <div class="report-check" style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid #cbd5e1; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.15s ease;">
                            <i class="fas fa-check" style="font-size: 10px; color: transparent; transition: color 0.15s ease;"></i>
                        </div>
                    </label>
                    @endforeach
                </div>

                <!-- Optional details textarea (hidden until option selected) -->
                <div id="reportDetailsWrapper" style="display: none; margin-top: 16px;">
                    <label for="reportModalDetails" style="font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Additional details <span style="font-weight: 400; color: #94a3b8;">(optional)</span></label>
                    <textarea class="form-control" id="reportModalDetails" rows="3" placeholder="Describe the issue..." style="border-radius: 10px; font-size: 14px; border-color: #e2e8f0; resize: none;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 16px 24px;">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 14px;">Cancel</button>
                <button type="button" class="btn rounded-pill px-4" id="submitReportBtn" disabled style="background: #002347; border-color: #002347; color: white; font-size: 14px; opacity: 0.5; transition: opacity 0.2s;" onclick="submitFaqReport()">Submit report</button>
            </div>
        </div>
    </div>
</div>
<style>
    .report-option-card.selected {
        border-color: #002347 !important;
        background: #f0f4ff;
    }
    .report-option-card.selected .report-check {
        background: #002347;
        border-color: #002347;
    }
    .report-option-card.selected .report-check i {
        color: #ffffff !important;
    }
    .report-option-card.selected > div:first-child + div + div + div,
    .report-option-card.selected .report-check {
        background: #002347;
        border-color: #002347;
    }
</style>

@livewire('add-review')

@if(auth()->check() && session()->has('pending_review_business_id'))
    @php
        $pendingBusId = session('pending_review_business_id');
        $pendingRec = session('pending_review_recommend');
        session()->forget(['pending_review_business_id', 'pending_review_recommend']);
    @endphp
    <script>
        function triggerPendingReviewModal() {
            if (window.Livewire) {
                if (typeof Livewire.dispatch === 'function') {
                    Livewire.dispatch('openReviewModal', { businessId: {{ $pendingBusId }}, recommend: {{ json_encode($pendingRec) }} });
                } else if (typeof Livewire.emit === 'function') {
                    Livewire.emit('openReviewModal', {{ $pendingBusId }}, {{ json_encode($pendingRec) }});
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(triggerPendingReviewModal, 300);
        });
        document.addEventListener('livewire:load', function() {
            setTimeout(triggerPendingReviewModal, 300);
        });
    </script>
@endif
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
        const groups = document.querySelectorAll('.faq-category-group');
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

        // Hide empty category groups
        groups.forEach(group => {
            const visibleInGroup = group.querySelectorAll('.business-faq-card[style="display: block;"]').length;
            const allInGroup = group.querySelectorAll('.business-faq-card').length;
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

    // 3. In-Page Category Tab Filter
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
                    group.querySelectorAll('.business-faq-card').forEach(c => c.style.display = 'block');
                } else {
                    group.style.display = 'none';
                }
            });

            document.getElementById('noFaqSearchResults').style.display = 'none';
        });
    });

    // 4. Helpful / Not Helpful Vote Handler
    function voteFaq(faqId, isHelpful, button) {
        @if(!auth()->check())
            alert('Please sign in to vote on FAQs.');
            return;
        @endif

        fetch('/business-faq/vote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                faq_id: faqId,
                is_helpful: isHelpful
            })
        })
        .then(response => {
            if (response.status === 401) {
                alert('Please sign in to vote.');
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data || !data.success) return;

            const card = button.closest('.business-faq-card');
            const yesBtn = card.querySelector('.btn-vote-faq:first-child');
            const noBtn = card.querySelector('.btn-vote-faq:last-child');

            yesBtn.querySelector('.helpful-count').textContent = data.helpful_count;
            noBtn.querySelector('.not-helpful-count').textContent = data.not_helpful_count;

            if (data.user_vote === 'yes') {
                yesBtn.classList.add('voted-yes');
                noBtn.classList.remove('voted-no');
            } else if (data.user_vote === 'no') {
                noBtn.classList.add('voted-no');
                yesBtn.classList.remove('voted-yes');
            }
        })
        .catch(err => console.error('Vote Error:', err));
    }

    // 5. Report Issue Modal Handler
    function openReportModal(faqId) {
        @if(!auth()->check())
            alert('Please sign in to report an issue.');
            return;
        @endif

        // Reset state
        document.getElementById('reportModalFaqId').value = faqId;
        document.getElementById('reportModalReason').value = '';
        document.getElementById('reportModalDetails').value = '';
        document.getElementById('reportDetailsWrapper').style.display = 'none';
        document.getElementById('submitReportBtn').disabled = true;
        document.getElementById('submitReportBtn').style.opacity = '0.5';

        // Deselect all option cards
        document.querySelectorAll('.report-option-card').forEach(c => c.classList.remove('selected'));

        const modalEl = document.getElementById('reportIssueModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    function selectReportReason(reason, card) {
        document.getElementById('reportModalReason').value = reason;

        // Highlight selected card
        document.querySelectorAll('.report-option-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');

        // Show details textarea
        document.getElementById('reportDetailsWrapper').style.display = 'block';

        // Enable submit
        const btn = document.getElementById('submitReportBtn');
        btn.disabled = false;
        btn.style.opacity = '1';
    }

    function submitFaqReport() {
        const faqId = document.getElementById('reportModalFaqId').value;
        const reason = document.getElementById('reportModalReason').value;
        const details = document.getElementById('reportModalDetails').value;
        const submitBtn = document.getElementById('submitReportBtn');

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        fetch('/business-faq/report', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                faq_id: faqId,
                report_reason: reason,
                report_details: details
            })
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit report';

            if (data.success) {
                const modalEl = document.getElementById('reportIssueModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                alert('Thank you! Your report has been submitted.');
            } else {
                alert(data.message || 'Error submitting report.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit report';
            console.error('Report Error:', err);
        });
    }
</script>
@endpush
