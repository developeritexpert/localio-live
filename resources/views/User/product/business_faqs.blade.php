@extends('user_layout.master')
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/product-detail-components.css') }}">
    @include('User.partials.business_detail_styles')
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
            border-radius: 8px;
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
            /* opacity: 0.92; */
            transform: translateY(-1px);
        }
            /* Sticky Sidebar & Table of contents */
        .sticky-sidebar-nav-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 14px rgba(0, 35, 71, 0.04);
            padding: 20px;
            border: 1px solid #eef2f6;
            width: 100%;
            box-sizing: border-box;
        }
        .sticky-nav-links {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin: 8px 0 0 0;
        }
        .sticky-nav-item {
            display: flex;
            align-items: center;
            padding: 9px 14px;
            border-radius: 8px;
            color:#5b6b7a;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            position: relative;
        }
        .sticky-nav-item:hover {
            background-color: #f4f7fb;
            color: #003f7d;
            font-weight: 600;
            transform: translateX(3px);
        }
        .sticky-nav-item.active {
            background-color: #eaf1f9;
            color: #002347;
            font-weight: 700;
        }
        .sticky-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 3.5px;
            background-color: #003f7d;
            border-radius: 0 3px 3px 0;
        }
        .revie_img_sec .image_revie_inr {
            display: flex !important;
            align-items: flex-start !important;
            gap: 40px !important;
        }
        .revie_img_sec .imges_left_sde {
            width: 62% !important;
            flex: 1 1 62% !important;
            min-width: 0 !important;
        }
        .revie_img_sec .thre_revi_rgt {
            position: relative !important;
            display: flex !important;
            flex-direction: column !important;
            width: 38% !important;
            flex: 0 0 38% !important;
            max-width: 38% !important;
            height: auto !important;
            gap: 20px !important;
        }
        .sticky-sidebar-nav-wrapper {
            position: -webkit-sticky !important;
            position: sticky !important;
            top: 90px !important;
            z-index: 30 !important;
            width: 100% !important;
            margin-top: 0 !important;
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
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec business_faq_sec" style=" background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
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
                    <li class="breadcrumb-item active" aria-current="page" style="color: #002347; font-weight: 500;">
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
                    <div class="top-product-logo" >
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
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Content & Right Sidebar Section -->
<section class="revie_img_sec py-5" style="background-color: #f8fafc; border-top: 1px solid #eef2f6;">
    <div class="container">
        <div class="row g-4">

            <!-- Left Side: FAQs Content, Search & Accordion -->
            <div class="col-lg-8 col-12">
                
                <!-- Section 1: Title 1 & Description 1 -->
                @if(!empty($faqTitle1) || !empty($faqDesc1))
                <div class="faq-top-intro mb-4" id="section-about-faq">
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
                <div class="faq-search-wrapper" id="section-search-faq">
                    <h3 style="font-size: 20px; font-weight: 700; color: #002347; margin-bottom: 14px;">
                        Search {{ $bName }} FAQs
                    </h3>
                    <div class="faq-search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="faqSearchInput" placeholder="Search questions..." autocomplete="off">
                    </div>
                </div>

                <!-- Main FAQ Section: Headline & In-Page Navigation -->
                <div class="main-faq-container" id="section-faq-list">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin: 0;">
                            Frequently asked questions about {{ $bName }}
                        </h2>
                    </div>

                    <!-- In-Page Category Navigation -->
                    @if(isset($faqCategories) && $faqCategories->count() > 0)
                    <div class="faq-category-nav" id="faqCategoryNav">
                        <button type="button" class="faq-cat-tab active" data-category-id="all">
                            All
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

                    <!-- FAQs Accordion List - Matching Top Rated Style -->
                    <div id="faqAccordionContainer">
                        @php $totalFaqCount = 0; @endphp

                        <div class="accordion" id="businessFaqAccordion">
                            {{-- Grouped by Categories --}}
                            @if(isset($faqCategories) && $faqCategories->count() > 0)
                                @foreach($faqCategories as $cat)
                                    @if($cat->faqs && $cat->faqs->count() > 0)
                                    <div class="faq-category-group mb-4" data-category-group="{{ $cat->id }}">
                                        <h3 class="category-group-headline mb-3" style="">
                                            {{ $cat->name }}
                                        </h3>

                                        @foreach($cat->faqs as $faq)
                                            @php
                                                $totalFaqCount++;
                                                $trans = $faq->translations->first();
                                                $userVoted = $userVotes[$faq->id] ?? null;
                                            @endphp
                                            @if($trans)
                                            <div class="accordion-item mb-3" data-faq-id="{{ $faq->id }}" data-category="{{ $cat->id }}" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden;">
                                                <h2 class="accordion-header" id="faqHeading{{ $faq->id }}">
                                                    <button class="accordion-button {{ $totalFaqCount > 1 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $faq->id }}" aria-expanded="{{ $totalFaqCount === 1 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $faq->id }}" style="font-weight: 600; font-size: 16px; color: #002347; background-color: #fdfdfd;">
                                                        {{ $trans->question }}
                                                    </button>
                                                </h2>
                                                <div id="faqCollapse{{ $faq->id }}" class="accordion-collapse collapse {{ $totalFaqCount === 1 ? 'show' : '' }}" aria-labelledby="faqHeading{{ $faq->id }}" data-bs-parent="#businessFaqAccordion">
                                                    <div class="accordion-body rich-text-content" style="font-size: 14.5px; line-height: 1.7; background-color: #fdfdfd;">
                                                        <div class="faq-answer-text mb-3">
                                                            {!! nl2br(e(strip_tags($trans->answer))) !!}
                                                        </div>

                                                        <!-- Was this helpful? Row -->
                                                        <div class="faq-feedback-row">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span>Was this helpful?</span>
                                                                <div class="faq-feedback-actions d-flex align-items-center" style="gap: 6px;">
                                                                    <button type="button" class="btn-vote-faq {{ $userVoted === true ? 'voted-yes' : '' }}" onclick="voteFaq({{ $faq->id }}, true, this)" title="Helpful">
                                                                        <i class="far fa-thumbs-up"></i>
                                                                        <span class="helpful-count" style="font-size: 12px; font-weight: 600; color: #64748b;">{{ $faq->helpful_count ?? 0 }}</span>
                                                                    </button>
                                                                    <button type="button" class="btn-vote-faq {{ $userVoted === false ? 'voted-no' : '' }}" onclick="voteFaq({{ $faq->id }}, false, this)" title="Not helpful">
                                                                        <i class="far fa-thumbs-down"></i>
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
                                    <h3 class="category-group-headline mb-3" style=" ">
                                        General
                                    </h3>
                                    @endif

                                    @foreach($uncategorizedFaqs as $faq)
                                        @php
                                            $totalFaqCount++;
                                            $trans = $faq->translations->first();
                                            $userVoted = $userVotes[$faq->id] ?? null;
                                        @endphp
                                        @if($trans)
                                        <div class="accordion-item mb-3" data-faq-id="{{ $faq->id }}" data-category="general" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden;">
                                            <h2 class="accordion-header" id="faqHeading{{ $faq->id }}">
                                                <button class="accordion-button {{ $totalFaqCount > 1 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $faq->id }}" aria-expanded="{{ $totalFaqCount === 1 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $faq->id }}" style="font-weight: 600; font-size: 16px; color: #002347; background-color: #fdfdfd;">
                                                    {{ $trans->question }}
                                                </button>
                                            </h2>
                                            <div id="faqCollapse{{ $faq->id }}" class="accordion-collapse collapse {{ $totalFaqCount === 1 ? 'show' : '' }}" aria-labelledby="faqHeading{{ $faq->id }}" data-bs-parent="#businessFaqAccordion">
                                                <div class="accordion-body rich-text-content" style="font-size: 14.5px;  line-height: 1.7; background-color: #fdfdfd;">
                                                    <div class="faq-answer-text mb-3">
                                                        {!! nl2br(e(strip_tags($trans->answer))) !!}
                                                    </div>

                                                    <!-- Was this helpful? Row -->
                                                    <div class="faq-feedback-row">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span>Was this helpful?</span>
                                                            <div class="faq-feedback-actions d-flex align-items-center" style="gap: 6px;">
                                                                <button type="button" class="btn-vote-faq {{ $userVoted === true ? 'voted-yes' : '' }}" onclick="voteFaq({{ $faq->id }}, true, this)" title="Helpful">
                                                                    <i class="far fa-thumbs-up"></i>
                                                                    <span class="helpful-count" style="font-size: 12px; font-weight: 600; color: #64748b;">{{ $faq->helpful_count ?? 0 }}</span>
                                                                </button>
                                                                <button type="button" class="btn-vote-faq {{ $userVoted === false ? 'voted-no' : '' }}" onclick="voteFaq({{ $faq->id }}, false, this)" title="Not helpful">
                                                                    <i class="far fa-thumbs-down"></i>
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
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

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
                <div class="ask-community-banner" id="section-ask-community">
                    <div>
                        <h3 style="font-size: 22px; font-weight: 700; margin-bottom: 6px;">
                            Still have a question about {{ $bName }}?
                        </h3>
                        <p style="font-size: 15px; margin-bottom: 0;">
                            Ask the Localio community
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#sectionDiscussions" class="blue-btn ask-community-btn">
                            Ask a question <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
            <!-- End Left Side -->

            <!-- Right Side: Sidebar -->
            <div class="col-lg-4 col-12">
                <div class="faq-sidebar-sticky d-flex flex-column gap-4">

                    <!-- Sticky Sidebar Navigation Card (Exact structure from product_detail.blade.php) -->
                    <div class="d-none main_feature_lg sticky-sidebar-nav-wrapper">
                        <div class="feture_box review-breakdown-box sticky-sidebar-nav-card" id="stickySidebarNav">
                            <div class="sticky-nav-header text-center">
                                <div class="sticky-nav-logo mb-2 d-flex justify-content-center align-items-center" style="gap: 10px;">
                                    <div class="business-initial-logo" style="width: 44px; height: 44px; margin-bottom:8px">
                                        <x-business-logo :business="$business" />
                                    </div>

                                    <div class="sticky-wrap-mf">
                                        <h3 class="sticky-nav-title mb-1" style="font-size: 17px; font-weight: 700; color: #002347;">
                                            {{ $business->translations->first()->name ?? '' }}
                                        </h3>
                                        @if (isset($averageRating) && $averageRating !== null)
                                            <div class="rating-group sticky-nav-rating mb-3">
                                                <span>{{ number_format($averageRating, 1) }}</span>
                                                <div class="rating-star">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= floor($averageRating))
                                                            <i class="fas fa-star"></i>
                                                        @elseif ($i - 0.5 <= $averageRating)
                                                            <i class="fas fa-star-half-alt"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                @if (isset($totalReviews) && $totalReviews > 0)
                                                    <span>({{ $totalReviews }})</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($business->is_affiliate)
                                    <a href="{{ $business->getTrackedUrl() }}"
                                        target="_blank" class="btn-orng cta cta_orange w-100 mb-3"
                                        style="font-size: 14px; font-weight: 600; padding: 9px 18px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                        {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt ms-2" style="font-size: 11px;"></i>
                                    </a>
                                @endif
                            </div>

                            <div class="sticky-nav-links">
                                @if(!empty($faqTitle1) || !empty($faqDesc1))
                                    <a href="#section-about-faq" class="sticky-nav-item active" data-target="section-about-faq">
                                        <span>About {{ $bName }}</span>
                                    </a>
                                @endif
                                <a href="#section-search-faq" class="sticky-nav-item" data-target="section-search-faq">
                                    <span>Search FAQs</span>
                                </a>
                                <a href="#section-faq-list" class="sticky-nav-item" data-target="section-faq-list">
                                    <span>Frequently asked questions</span>
                                </a>
                                <a href="#section-ask-community" class="sticky-nav-item" data-target="section-ask-community">
                                    <span>Ask the community</span>
                                </a>
                            </div>

                            <div class="sticky-nav-footer pt-3 mt-2 border-top text-center">
                                @auth
                                    <button type="button"
                                        onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                        class="btn-write-review-link"
                                        style="background: transparent; border: none; color: #002347; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                        <i class="fas fa-pencil-alt"></i> Write a review
                                    </button>
                                @else
                                    <a href="javascript:void(0)"
                                        onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                        class="btn-write-review-link"
                                        style="background: transparent; border: none; color: #003f7d; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                        <i class="fas fa-pencil-alt"></i> Write a review
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <!-- 1. USPs -->
                    @if(!empty($business->is_affiliate) && $business->usps && $business->usps->count() > 0)
                        <div class="feture_box lft_check_box size15 bg-white p-4 boxshadow_border" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                            <ul class="list-unstyled mb-0">
                                @foreach($business->usps as $usp)
                                    @php $uText = $usp->text ?? $usp->usp_text ?? ''; @endphp
                                    @if(!empty($uText))
                                        <li class="d-flex flex-row align-items-center size15 mb-3">
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
                                @if($ratingWord = get_rating_label($averageRating))
                                    <span class="rating-word-label" >
                                        {{ $ratingWord }}
                                    </span>
                                @endif
                                <div class="" style="margin-bottom: 3px; display: flex;">
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
                                <span class="com-rating"> {{ number_format($totalReviews) }} {{ $totalReviews == 1 ? 'review' : 'reviews' }}</span>
                            </div>
                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section14" class="view-review-link" style="color: #06498b; font-weight: 600; font-size: 12px; text-decoration: none; padding-top: 5px;">
                                View all reviews
                            </a>
                        </div>

                        <h2 class="breakdown-title" style="margin-bottom: 15px; font-size: 16px; font-weight: 600; color: #002347;">
                            User rating
                        </h2>

                        {{-- Breakdown --}}
                        <div class="review-progress-list">
                            @if(isset($criteria) && count($criteria) > 0)
                                @foreach ($criteria as $criterion)
                                    <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                        <p class="m-0" style="font-size: 12px; font-weight: 500;">{{ $criterion->name }}</p>
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
                            <span style="font-weight: 600; color: #002347; font-size: 14px;">Do you recommend {{ $bName }}?</span>
                            <div style="display: flex; gap: 8px;">
                                @auth
                                    <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff ';">
                                        <i class="far fa-thumbs-up"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff ';">
                                        <i class="far fa-thumbs-down"></i>
                                    </a>
                                @else
                                    <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff ';">
                                        <i class="far fa-thumbs-up"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff ';">
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
                            <div class="feture_box str_prc_box p-4 bg-white boxshadow_border" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                                <h6 class="starting-price-title">Starting price</h6>
                                <h2 class="starting-price-value">{{ $currency }}{{ $startingPrice }}</h2>
                                <p class="starting-price-text">Flat Rate, Per {{ ucfirst($timeUnit) }}</p>
                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section6" class="starting-price-link" >
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
                                    <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="blue-btn cta" style="text-transform:none !important; padding: 6px 20px;">Claim now</a>
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
                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section14" class="view-review-link">
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
                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#sectionDiscussions" class="view-review-link">
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
@include('User.partials.business_detail_styles')
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
        /* Sticky Sidebar & Table of contents */
        .sticky-sidebar-nav-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 14px rgba(0, 35, 71, 0.04);
            padding: 20px;
            border: 1px solid #eef2f6;
            width: 100%;
            box-sizing: border-box;
        }
        .sticky-nav-links {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin: 8px 0 0 0;
        }
        .sticky-nav-item {
            display: flex;
            align-items: center;
            padding: 9px 14px;
            border-radius: 8px;
            color: #5b6b7a;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            position: relative;
        }
        .sticky-nav-item:hover {
            background-color: #f4f7fb;
            color: #003f7d;
            font-weight: 600;
            transform: translateX(3px);
        }
        .sticky-nav-item.active {
            background-color: #eaf1f9;
            color: #002347;
            font-weight: 700;
        }
        .sticky-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 3.5px;
            background-color: #003f7d;
            border-radius: 0 3px 3px 0;
        }
        .revie_img_sec .image_revie_inr {
            display: flex !important;
            align-items: flex-start !important;
            gap: 40px !important;
        }
        .revie_img_sec .imges_left_sde {
            width: 62% !important;
            flex: 1 1 62% !important;
            min-width: 0 !important;
        }
        .revie_img_sec .thre_revi_rgt {
            position: relative !important;
            display: flex !important;
            flex-direction: column !important;
            width: 38% !important;
            flex: 0 0 38% !important;
            max-width: 38% !important;
            height: auto !important;
            gap: 20px !important;
        }
        .sticky-sidebar-nav-wrapper {
            position: -webkit-sticky !important;
            position: sticky !important;
            top: 90px !important;
            z-index: 30 !important;
            width: 100% !important;
            margin-top: 0 !important;
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
    // 1. Real-Time Search Filter
    document.getElementById('faqSearchInput')?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('#businessFaqAccordion .accordion-item');
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

            document.getElementById('noFaqSearchResults').style.display = 'none';
        });
    });

    // 3. Helpful / Not Helpful Vote Handler
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

            const card = button.closest('.accordion-item');
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

    // 4. Report Issue Modal Handler
    function openReportModal(faqId) {
        @if(!auth()->check())
            if(typeof openLoginModal === 'function') {
                openLoginModal();
            } else {
                window.location.href = "{{ route('login') }}";
            }
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
    // Sticky Sidebar Navigation Active Link & Smooth Scroll
    document.addEventListener('DOMContentLoaded', function() {
        const stickyNavLinks = document.querySelectorAll('.sticky-nav-links .sticky-nav-item');
        if (stickyNavLinks.length > 0) {
            stickyNavLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('data-target') || (this.getAttribute('href') ? this.getAttribute('href').replace('#', '') : '');
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        e.preventDefault();
                        const headerOffset = 90;
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        stickyNavLinks.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                    }
                });
            });

            // Update active link on scroll
            const navSectionTargets = [
                { id: 'section-about-faq', target: 'section-about-faq' },
                { id: 'section-search-faq', target: 'section-search-faq' },
                { id: 'section-faq-list', target: 'section-faq-list' },
                { id: 'section-ask-community', target: 'section-ask-community' }
            ];

            window.addEventListener('scroll', function() {
                const scrollPos = window.scrollY + 130;
                let activeId = '';

                for (const item of navSectionTargets) {
                    const el = document.getElementById(item.id);
                    if (el) {
                        const top = el.offsetTop;
                        const height = el.offsetHeight;
                        if (scrollPos >= top && scrollPos < top + height) {
                            activeId = item.target;
                        }
                    }
                }

                if (activeId) {
                    stickyNavLinks.forEach(link => {
                        if (link.getAttribute('data-target') === activeId || link.getAttribute('href') === '#' + activeId) {
                            link.classList.add('active');
                        } else {
                            link.classList.remove('active');
                        }
                    });
                }
            });
        }
    });
</script>
@endpush
