@extends('user_layout.master')

@php
    $lang_id = getCurrentLanguageID();
    $metaResolved = \App\Services\MetaTagService::resolveBusinessReviewsMeta($business, $lang_id);
    $reviewsMetaTitle = $metaResolved['title'];
    $reviewsMetaDesc = $metaResolved['description'];
@endphp

@section('meta_title', format_meta_text($reviewsMetaTitle))
@if(!empty($reviewsMetaDesc))
@section('meta_description', format_meta_text($reviewsMetaDesc))
@endif

@section('content')

@php
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatName = $parentCatTrans->name ?? '';
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $bTranslation = $business->translations->firstWhere('language_id', $lang_id) ?? $business->translations->first();
    $bName = $bTranslation->name ?? 'Business';
    $reviewsWord = static_text('reviews_word') !== 'reviews_word' ? static_text('reviews_word') : 'reviews';
    $subHeadline = static_text('business_reviews_subheadline') !== 'business_reviews_subheadline' 
        ? static_text('business_reviews_subheadline') 
        : 'Real reviews, user discussions & alternatives ';

    $reviewsTitle1 = !empty($bTranslation->reviews_title) ? $bTranslation->reviews_title : "What is {$bName}";
    $reviewsDesc1 = $bTranslation->reviews_description ?? '';
    $reviewsTitle2 = !empty($bTranslation->reviews_title_2) ? $bTranslation->reviews_title_2 : "What is {$bName}";
    $reviewsDesc2 = $bTranslation->reviews_description_2 ?? '';

    // Pros & Cons extraction logic from business reviews
    $pros = collect();
    $cons = collect();
    $activeReviews = $business->reviews->where('status', 'active');
    $ratingCount = $business->approved_reviews_count;
    $hasUserRatings = $business->should_show_detailed_ratings;
    $hasUserReviews = $ratingCount > 0;
    $averageRating = $business->display_rating;

    foreach ($activeReviews as $rev) {
        $rTrans = $rev->translations->where('language_id', $lang_id)->first() ?? $rev->translations->first();
        if ($rTrans) {
            if (!empty($rTrans->pros)) {
                $pItems = array_filter(array_map('trim', explode(',', strip_tags($rTrans->pros))));
                foreach ($pItems as $pi) { $pros->push($pi); }
            }
            if (!empty($rTrans->cons)) {
                $cItems = array_filter(array_map('trim', explode(',', strip_tags($rTrans->cons))));
                foreach ($cItems as $ci) { $cons->push($ci); }
            }
        }
    }
    $pros = $pros->unique()->take(3);
    $cons = $cons->unique()->take(3);

    // Fallbacks if no pros/cons exist
    if ($pros->isEmpty()) {
        $pros = collect(['Wide range of IT services', 'Strong focus on innovation', 'Professional project management']);
    }
    if ($cons->isEmpty()) {
        $cons = collect(['Enterprise services can be expensive', 'Initial consultation may take time', 'Custom projects require detailed requirements']);
    }

    $totalReviewsCount = $totalReviews ?? ($ratingCount ?: 0);
@endphp

@include('User.partials.business_detail_styles')

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec business_reviews_sec all-com" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4" style="background-color: #f7f9fb;">
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
                    <li class="breadcrumb-item">
                        <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $business->translations->first()->slug ?? '']) }}" style="color: #64748b; text-decoration: none;">{{ $bName }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #002347; font-weight: 500;">
                        Reviews
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon :business="$business" />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-6">
                <div class="top_head d-flex align-items-center gap-2">
                    <!-- Business Icon: Square with rounded edges -->
                    <div class="top-product-logo" >
                        <x-business-logo :business="$business" :name="$bName" />
                    </div>
                    @php
                        $resolvedReviewsH1 = \App\Services\ContentTemplateService::resolveBusinessSection(
                            $business,
                            'reviews_h1',
                            getCurrentLanguageID()
                        );
                        $finalReviewsH1 = !empty($resolvedReviewsH1['headline']) ? $resolvedReviewsH1['headline'] : ($bName . ' ' . $reviewsWord . ' reviews');

                        $resolvedReviewsSub = \App\Services\ContentTemplateService::resolveBusinessSection(
                            $business,
                            'reviews_subtext',
                            getCurrentLanguageID()
                        );
                        $finalReviewsSub = !empty($resolvedReviewsSub['headline']) ? $resolvedReviewsSub['headline'] : $subHeadline;
                    @endphp
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="margin: 0;">
                                {!! \App\Services\ContentTemplateService::cleanHeading($finalReviewsH1) !!}
                            </h1>
                        </div>
                        @if(!empty($finalReviewsSub))
                        <div class="text-meta" style="margin-top: 4px;">
                            {!! $finalReviewsSub !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @if(!empty($business->is_affiliate))
            <div class="col-md-4 col-6 text-md-end text-end is_affiliate_btn">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" rel="sponsored" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition:unset;" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Main Two-Column Section with Left-Aligned Content and Right Sticky Navigation -->
<section class="revie_img_sec py-4" style="background-color: #f8fafc; border-top: 1px solid #eef2f6;">
    <div class="container">
        <div class="image_revie_inr mt-3">
            
            <!-- Left Side: All Content (Description, Pros & Cons, FAQs, Reviews) -->
            <div class="is-asana-wrp imges_left_sde" data-aos="fade-up" data-aos-duration="1000">
                <div class="row sld_rw">
                    
                    <!-- 1. Description / Overview Section -->
                    <div class="col-lg-12">
                        <div class="is-asana-lft" id="section1">
                            <h2 style="margin-bottom: 12px;">{{ $reviewsTitle1 }}</h2>
                            <div class="">
                                @if(!empty($reviewsDesc1))
                                    {!! $reviewsDesc1 !!}
                                @else
                                    <p style="margin-bottom: 0;">{{ $bName }} is a trusted technology service provider dedicated to helping businesses streamline operations, improve productivity, and strengthen their online presence. From custom software development and website design to cloud solutions and IT consulting, the company delivers tailored services that match the unique goals of each client.</p>
                                @endif
                            </div>

                            @if(!empty($reviewsDesc2))
                                <h2 class="mt-4" style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">{{ $reviewsTitle2 }}</h2>
                                <div class="is_text" style="font-size: 15px; color: #475569; line-height: 1.7;">
                                    {!! $reviewsDesc2 !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Community Pros & Cons Section -->
                    @php
                        $reviewsProConsIntro = $bTranslation->reviews_pro_cons_intro ?? '';
                        $reviewsProConsSummary = $bTranslation->reviews_pro_cons_summary ?? '';
                    @endphp
                    @if((isset($aggregatedPros) && count($aggregatedPros) > 0 && isset($aggregatedCons) && count($aggregatedCons) > 0) || (isset($pros) && $pros->count() > 0 && isset($cons) && $cons->count() > 0))
                    <div class="col-lg-12 mt-4 mb-4" id="section-pros-cons">
                        <div class="pros-cons-header mb-3">
                            <h2 style="margin-bottom: 8px;">
                                {{ static_text('community_sentiment_heading') ?: 'What the community says' }}
                            </h2>
                            @if(!empty($reviewsProConsIntro))
                                <p style="font-size: 15px; margin-bottom: 20px; line-height: 1.6; color: #475569;">
                                    {!! $reviewsProConsIntro !!}
                                </p>
                            @endif
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                    <div class="card-body p-3 p-md-4">
                                        <p class="card-titles mb-3" style="font-weight: 500 !important; font-size: 16px !important;">{{ static_text('pros_label') ?: 'Pros' }}</p>
                                        <ul class="list-unstyled mb-0">
                                            @if(isset($aggregatedPros) && count($aggregatedPros) > 0)
                                                @foreach($aggregatedPros as $pro)
                                                <li class="d-flex align-items-start mb-2">
                                                    <span class="me-2" style="font-size: 18px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                    <span style="color: #202124;">{{ $pro->text }}
                                                        @if($hasUserRatings && isset($pro->review_count))
                                                            <small class="badge font-weight-normal ms-1" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 11.5px;">from {{ $pro->review_count }} {{ $pro->review_count == 1 ? 'review' : 'reviews' }}</small>
                                                        @endif
                                                    </span>
                                                </li>
                                                @endforeach
                                            @else
                                                @foreach($pros as $p)
                                                <li class="d-flex align-items-start mb-2">
                                                    <span class="me-2" style="font-size: 18px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                    <span style="color: #202124; font-size: 14px;">{{ $p }}</span>
                                                </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                    <div class="card-body p-3 p-md-4">
                                        <p class="card-titles mb-3" style="font-weight: 500 !important; font-size: 16px !important;">{{ static_text('cons_label') ?: 'Cons' }}</p>
                                        <ul class="list-unstyled mb-0">
                                            @if(isset($aggregatedCons) && count($aggregatedCons) > 0)
                                                @foreach($aggregatedCons as $con)
                                                <li class="d-flex align-items-start mb-2">
                                                    <span class="me-2" style="font-size: 18px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                    <span style="color: #202124;">{{ $con->text }}
                                                        @if($hasUserRatings && isset($con->review_count))
                                                            <small class="badge font-weight-normal ms-1" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 11.5px;">from {{ $con->review_count }} {{ $con->review_count == 1 ? 'review' : 'reviews' }}</small>
                                                        @endif
                                                    </span>
                                                </li>
                                                @endforeach
                                            @else
                                                @foreach($cons as $c)
                                                <li class="d-flex align-items-start mb-2">
                                                    <span class="me-2" style="font-size: 18px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                    <span style="color: #202124; font-size: 14px;">{{ $c }}</span>
                                                </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!empty($reviewsProConsSummary))
                        <div class="mt-4" style="font-size: 14.5px; color: #475569; line-height: 1.7;">
                            {!! $reviewsProConsSummary !!}
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- 3. FAQs Section (Exact structure from product_detail.blade.php) -->
                    @if(isset($business->faqs) && $business->faqs->count() > 0)
                    @php
                        $faq_title = \App\Models\StaticContentKey::where('key', 'faq_title')->first();
                        $faq_description = \App\Models\StaticContentKey::where('key', 'faq_description')->first();
                    @endphp
                    <div class="col-lg-12" id="section15">
                        <section class="faq-section product_inr_faq py-5 light" style="background: transparent;">
                            <div class="container px-0">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-start mb-4">
                                            <h2 style=" margin-bottom: 8px;">
                                                {{ $faq_title?->default_value ?: 'Frequently asked questions' }}
                                            </h2>
                                            <p style=" margin-bottom: 0;">
                                                {{ $faq_description?->default_value ?: ('Everything you need to know about ' . $bName . ' and its services.') }}
                                            </p>
                                        </div>

                                        <div class="accordion" id="businessFaqAccordion">
                                            @foreach ($business->faqs->take(10) as $index => $faq)
                                                @php $translation = $faq->translations->first(); @endphp
                                                @if ($translation)
                                                    <div class="accordion-item mb-3" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden;">
                                                        <h3 class="accordion-header" id="faqHeading{{ $index }}">
                                                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#faqCollapse{{ $index }}"
                                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                                aria-controls="faqCollapse{{ $index }}"
                                                                style="font-weight: 600; font-size: 16px; color: #002347; background-color: #fff;">
                                                                <span>{{ $translation->question }}</span>
                                                            </button>
                                                        </h3>
                                                        <div id="faqCollapse{{ $index }}"
                                                            class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                                            aria-labelledby="faqHeading{{ $index }}"
                                                            data-bs-parent="#businessFaqAccordion">
                                                            <div class="accordion-body rich-text-content" style="background-color: #fff;">
                                                                {!! $translation->answer !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        @php
                                            $currentLangObj = \App\Models\Language::where('lang_code', app()->getLocale())->first();
                                            $faqSlugVal = !empty($currentLangObj->faq_slug) ? $currentLangObj->faq_slug : 'faqs';
                                            $bizSlugVal = $business->translations->first()->slug ?? $business->slug ?? '';
                                        @endphp
                                        <div class="text-center mt-4">
                                            <a href="{{ route('business.all_faqs', ['locale' => app()->getLocale(), 'business_slug' => $bizSlugVal, 'faq_slug' => $faqSlugVal]) }}"
                                               class="btn-g-link">
                                                View all FAQs 
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    @endif

                                        <!-- 4. User Reviews Section (Exact structure from product_detail.blade.php) -->
                    <div class="col-lg-12 mt-4" id="section14">
                        <section class="crm_sec revie_left_rgt_sec pt-0" style="overflow: visible !important; background-color: transparent;">
                            <div class="container px-0">
                                 
                                <div class="crm_review_box review_sec" id="all-reviews">
                                    @if(!$hasUserRatings)
                                    <style>
                                        .review_detl.populr-alternative:first-child {
                                            margin-top: 24px;
                                        }
                                    </style>
                                    @endif

                                
<div class="review-prompt-banner" id="reviewPromptBanner"
    style="background-color: #f7fafc; padding: 20px 24px; margin-bottom: 40px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #e2e8f0; flex-wrap: wrap; gap: 20px;">

    <div style="display: flex; align-items: center; gap: 16px;">
        <div class="top-product-logo">
            <x-business-logo :business="$business" />
        </div>

        <div>
            <p style="margin: 0 0 4px 0; font-size: 18px !important; font-weight: 700 !important;">
                Would you recommend {{ $business->translations->first()->name ?? 'this product' }} to others?
            </p>

            <p style="margin: 0; font-size: 13.5px; color: #4a5568;">
                Share your experience with the community
            </p>
        </div>
    </div>

    <div style="display: flex; gap: 12px; align-items: center;">
        @auth
            <button
                onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true });"
                style="padding: 12px 12px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 18px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';"
                onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                <i class="fas fa-thumbs-up"></i>
            </button>

            <button
                onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false });"
                style="padding: 12px 12px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 18px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';"
                onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                <i class="fas fa-thumbs-down"></i>
            </button>
        @else
            <button
                onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true });"
                style="padding: 12px 12px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 18px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';"
                onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                <i class="fas fa-thumbs-up"></i>
            </button>

            <button
                onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false });"
                style="padding: 12px 12px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 18px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';"
                onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                <i class="fas fa-thumbs-down"></i>
            </button>
        @endauth
    </div>
</div>





                                    <div class="row review-row-prod-inr mx-0">

                                        <div class="col-lg-12">
                                            <div class="review-sidebar-sticky">

                                                @php
                                                    $totalReviewsCount = $ratingCount;
                                                    $resolvedBizReviews = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                        $business,
                                                        'business_page_reviews',
                                                        getCurrentLanguageID(),
                                                        ['[count]' => number_format($totalReviewsCount)]
                                                    );
                                                    $finalBizReviewsHeadline = !empty($resolvedBizReviews['headline'])
                                                        ? $resolvedBizReviews['headline']
                                                        : (($business->translations->first()->name ?? $business->name ?? '') . ' user reviews' . ($hasUserRatings ? ' (' . number_format($totalReviewsCount) . ')' : ''));
                                                    if (!$hasUserRatings) {
                                                        $finalBizReviewsHeadline = preg_replace('/\s*\(\d+\)/', '', $finalBizReviewsHeadline);
                                                        $finalBizReviewsHeadline = preg_replace('/\s*\[count\]/i', '', $finalBizReviewsHeadline);
                                                        $finalBizReviewsHeadline = trim($finalBizReviewsHeadline);
                                                    }
                                                    $finalBizReviewsSubtext = !empty($resolvedBizReviews['subtext']) ? $resolvedBizReviews['subtext'] : null;
                                                @endphp

                                                <h2 style="font-size: 18px; font-weight: 700; margin-bottom: {{ !empty($finalBizReviewsSubtext) ? '6px' : '16px' }}; color: #1e3050; line-height: 1.3; text-align: left;">
                                                    {!! \App\Services\ContentTemplateService::cleanHeading($finalBizReviewsHeadline) !!}
                                                </h2>
                                                @if(!empty($finalBizReviewsSubtext))
                                                    <div class="reviews-section-subtext mb-3" style="color: #475569; font-size: 14.5px;">
                                                        {!! $finalBizReviewsSubtext !!}
                                                    </div>
                                                @endif
                                                <div class="review-filter-sec" style="display: block;">
                                                    <div class="user-reviews-summary-card p-4 p-md-4 bg-white rounded-3 border w-100" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
                                                        <div class="row align-items-start">
                                                            <!-- Left Column: Big Rating & Stars -->
                                                            <div class="col-lg-5 col-md-5 col-12 mt-0">
                                                                <div class="d-flex flex-column align-items-start">
                                                                    @if ($averageRating !== null && (float)$averageRating > 0)
                                                                        <span class="overall-rating-number" style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1;">{{ formatRating($averageRating) }}</span>
                                                                        @if($ratingWord = get_rating_label($averageRating))
                                                                            <span class="rating-word-label" style="font-size: 15px; font-weight: 700; color: #1e3050;">
                                                                                {{ $ratingWord }}
                                                                            </span>
                                                                        @endif
                                                                        <div class="d-flex align-items-center gap-2 mb-1 @if($hasUserRatings) section14-rating-trigger @endif" @if($hasUserRatings) onclick="toggleSection14ReviewBreakdown()" style="user-select: none;" @endif>
                                                                            <div class="d-flex align-items-center gap-1">
                                                                                @for ($j = 1; $j <= 5; $j++)
                                                                                    @if ($j <= floor((float)$averageRating))
                                                                                        <i class="fas fa-star text-warning" style="font-size: 18px;"></i>
                                                                                    @elseif ($j - 0.5 <= (float)$averageRating)
                                                                                        <i class="fas fa-star-half-alt text-warning" style="font-size: 18px;"></i>
                                                                                    @else
                                                                                        <i class="far fa-star text-warning" style="font-size: 18px;"></i>
                                                                                    @endif
                                                                                @endfor
                                                                            </div>
                                                                            @if($hasUserRatings)
                                                                            <span class="section14-rating-arrow-wrap d-inline-flex align-items-center justify-content-center d-md-none" style="width: 22px; height: 22px; border-radius: 50%; background-color: #f1f5f9; transition: background-color 0.2s ease;">
                                                                                <i class="fas fa-chevron-down section14-rating-chevron" style="font-size: 11px; color: #64748b; transition: transform 0.3s ease;"></i>
                                                                            </span>
                                                                            @endif
                                                                        </div>
                                                                    @else
                                                                        <span class="overall-rating-number" style="font-size: 20px; font-weight: 600; color: #64748b; line-height: 1.3; margin-bottom: 8px;">{{ static_text('not_rated_yet') ?: 'Not rated yet' }}</span>
                                                                    @endif
                                                                    @if($hasUserRatings)
                                                                    <span class="com-rating">{{ number_format($ratingCount) }} {{ $ratingCount == 1 ? 'review' : 'reviews' }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Right Column: Review Breakdown & Recommendation -->
                                                            @if($hasUserRatings)
                                                            <div class="col-lg-7 col-md-7 col-12 mt-0 section14-breakdown-collapsible" id="section14ReviewBreakdownContent" data-expanded="false">
                                                                <div class="d-md-none border-top w-100 my-3" style="border-top: 1px solid #f0f0f0 !important;"></div>
                                                                @if(isset($criteria) && count($criteria) > 0)
                                                                    <p class="breakdown-title" style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 14px;">{{ static_text('detailed_user_ratings_heading') ?: 'Detailed user ratings' }}</p>

                                                                    <div class="mb-2">
                                                                        @foreach ($criteria as $criterion)
                                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                                            <span style="font-size: 13px; font-weight: 500; color: #002347; min-width: 130px;">{{ $criterion->name }}</span>
                                                                            <div class="d-flex align-items-center flex-grow-1 mx-3" style="max-width: 65%;">
                                                                                <div class="progress rounded-pill w-100" style="height: 7px; background-color: #e2e8f0;">
                                                                                    <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ ($criterion->average_rating / 5) * 100 }}%;" aria-valuenow="{{ $criterion->average_rating }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                                </div>
                                                                            </div>
                                                                            <span style="font-size: 13px; font-weight: 600; color: #002347; min-width: 28px; text-align: right;">{{ formatRating($criterion->average_rating) }}</span>
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif

                                                                @if(isset($recommendPercent) && $recommendPercent > 0)
                                                                    <div class="pt-3 mt-3 border-top d-flex justify-content-between align-items-center" style="border-top-color: #f1f5f9 !important;">
                                                                        <span style="font-weight: 600; color: #002347; font-size: 14px;">{{ static_text('recommended_by_users') ?: 'Recommended by users' }}</span>
                                                                        <strong style="color: #002347; font-size: 14px; font-weight: 600;">{{ $recommendPercent }}%</strong>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">

                                            @if($hasUserRatings)
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; margin-top: 25px; flex-wrap: wrap; gap: 12px;">
                                                
                                                <!-- Filter Button with Dropdown & Clear filter on Left -->
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="dropdown" id="reviewFilterDropdownWrap">
                                                        <button class="btn btn-outline-secondary d-flex align-items-center" 
                                                                type="button" 
                                                                id="reviewFilterDropdownBtn" 
                                                                data-bs-toggle="dropdown" 
                                                                data-bs-auto-close="outside"
                                                                aria-expanded="false" 
                                                                style="border-color: #ced4da; color: #002347; font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 6px; background: #fff; gap: 4px;">
                                                            <img src="{{ asset('front/img/filter-icon.svg') }}" alt="Filter" style="width: 15px !important; height: 15px !important;">
                                                            <span>{{ static_text('filter_by_rating_label') ?: 'Filter by rating' }}</span>
                                                            <span class="badge bg-primary rounded-pill d-none" id="filter-count-badge" style="font-size: 11px;">0</span>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px; flex-shrink: 0;">
                                                                <polyline points="6 9 12 15 18 9" />
                                                            </svg>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-start p-3 shadow-sm" 
                                                             aria-labelledby="reviewFilterDropdownBtn" 
                                                             style="min-width: 280px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                                            <div class="mb-3 pb-2 border-bottom">
                                                                <span style="font-size: 14px; font-weight: 600; color: #002655;">{{ static_text('filter_by_rating_label') ?: 'Filter by rating' }}</span>
                                                            </div>

                                                            <div class="review-star-box">
                                                                <ul class="progress-list" style="list-style: none; padding: 0; margin: 0;">
                                                                    @for ($i = 5; $i >= 1; $i--)
                                                                        @php
                                                                            $count = $ratingsCount[$i] ?? ($ratingCounts[$i] ?? 0);
                                                                            $percent = $totalReviewsCount > 0 ? round(($count / $totalReviewsCount) * 100) : 0;
                                                                        @endphp
                                                                        <li class="progress-list-item" style="display: flex; align-items: center; margin-bottom: 10px; gap: 8px;">
                                                                            <input type="checkbox" class="rating-filter-checkbox" value="{{ $i }}" id="star-check-{{ $i }}" style="cursor: pointer; width: 16px; height: 16px; margin: 0;">
                                                                            <label for="star-check-{{ $i }}" style="display: flex; align-items: center; width: 100%; cursor: pointer; margin: 0;">
                                                                                <span style="display: inline-flex; align-items: center; width: 45px; font-size: 13px; color: #5b6b7a; flex-shrink: 0;">
                                                                                    <i class="far fa-star text-warning" style="margin-right: 4px;"></i> {{ $i }}
                                                                                </span>
                                                                                <div class="progress-box" style="flex-grow: 1; height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden; margin-left: 4px; margin-right: 10px;">
                                                                                    <div class="progress-fill" style="width: {{ $percent }}%; height: 100%; "></div>
                                                                                </div>
                                                                                <span style="font-size: 13px; color: #5b6b7a; min-width: 35px; text-align: right; flex-shrink: 0; white-space: nowrap;">({{ $count }})</span>
                                                                            </label>
                                                                        </li>
                                                                    @endfor
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Clear filter link next to button -->
                                                    <span class="clear-filters-btn ms-2" id="clear-filters" style="display: none; font-size: 13px; font-weight: 600;">
                                                        <i class="fas fa-times me-1"></i>Clear filter
                                                    </span>
                                                </div>

                                                @php
                                                    $reviewSortOptions = [
                                                        'recent' => static_text('sort_most_recent') ?: 'Most recent',
                                                        'high-to-low' => static_text('sort_highest_rated') ?: 'Highest rated',
                                                        'low-to-high' => static_text('sort_lowest_rated') ?: 'Lowest rated',
                                                    ];
                                                    $currentSort = request('sort', 'recent');
                                                    if (!array_key_exists($currentSort, $reviewSortOptions)) {
                                                        $currentSort = ($currentSort === 'best') ? 'high-to-low' : 'recent';
                                                    }
                                                    $currentSortLabel = $reviewSortOptions[$currentSort] ?? 'Most recent';
                                                @endphp

                                                <!-- Sort Dropdown on Right (Top Rated Style) -->
                                                <div class="dropdown" id="reviewSortDropdownWrap">
                                                    <button type="button" 
                                                        class="sorting d-inline-flex align-items-center gap-2" 
                                                        id="reviewSortDropdownBtn"
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false"
                                                        style="background-color: #fdfdfd; color: #0f172a;  padding: 7px 16px; border: 1px solid #cbd5e1; outline: none; cursor: pointer; font-size: 13px; font-weight: 400;">
                                                        <span>{{ static_text('sort_label') ?: 'Sort:' }} <span id="current-sort-label" style="font-weight: 600;">{{ $currentSortLabel }}</span></span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="sort-chevron-icon">
                                                            <polyline points="6 9 12 15 18 9" />
                                                        </svg>
                                                    </button>

                                                    <div class="dropdown-menu dropdown-menu-end shadow-sm" 
                                                         aria-labelledby="reviewSortDropdownBtn"
                                                         style="min-width: 200px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.12); padding: 6px 0; margin-top: 6px; overflow: hidden;">
                                                        @foreach($reviewSortOptions as $val => $label)
                                                        <button type="button"
                                                            data-sort-value="{{ $val }}"
                                                            data-sort-label="{{ $label }}"
                                                            class="review-sort-option dropdown-item d-flex align-items-center justify-content-between px-3 py-2 {{ $currentSort === $val ? 'is-selected' : '' }}">
                                                            <span>{{ $label }}</span>
                                                            <span class="sort-check-icon {{ $currentSort === $val ? '' : 'd-none' }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                    viewBox="0 0 24 24" fill="none" stroke="#1e3050" stroke-width="2.5"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    style="flex-shrink: 0; margin-left: 8px;">
                                                                    <polyline points="20 6 9 17 4 12" />
                                                                </svg>
                                                            </span>
                                                        </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            <div id="reviews-list-container">
                                                @include('User.review.partials.reviews_list')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                </div>
            </div>

            <!-- Right Side: Sticky Sidebar Navigation & Cards (Exact structure from product_detail.blade.php) -->
            <div class="thre_revi_rgt">
                <div class="main_feture">
                    <div class="fetru_row">

                        <!-- 1. Review Breakdown Card -->
                        @if ($averageRating !== null && (float)$averageRating > 0)
                        <div class="main_feature_lg">
                            <div class="feture_box review-breakdown-card">
                                <div class="review-header-box top_review_bx" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom:15px;">
                                    <div class="overall-rating-box" style="display: flex; flex-direction: column; align-items: flex-start;">
                                        <span class="overall-rating-number" style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1;">
                                            {{ formatRating($averageRating) }}
                                        </span>
                                        @if($ratingWord = get_rating_label($averageRating))
                                            <span class="rating-word-label">
                                                {{ $ratingWord }}
                                            </span>
                                        @endif

                                        <div style="margin-bottom: 3px; display: flex; gap: 4px;">
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

                                        @if ($hasUserRatings)
                                            <span class="com-rating">{{ number_format($totalReviewsCount) }} {{ $totalReviewsCount == 1 ? 'review' : 'reviews' }}</span>
                                        @endif
                                    </div>

                                    @if ($hasUserRatings)
                                        <a href="#section14" class="view-review-link">
                                            Go to reviews 
                                        </a>
                                    @endif
                                </div>

                                @if ($hasUserRatings && isset($criteria) && count($criteria) > 0)
                                    <p class="breakdown-title" style="margin-bottom: 15px;">
                                       Detailed user ratings 
                                    </p>

                                    <div class="review-progress-list mb-3">
                                        @foreach ($criteria as $criterion)
                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                <p class="m-0" style="font-size: 12px; font-weight: 500">{{ $criterion->name }}</p>
                                                <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                                    <progress class="progress-bar w-100" value="{{ $criterion->average_rating * 20 }}" max="100" style="height: 8px;"></progress>
                                                    <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 35px; text-align: right;">{{ formatRating($criterion->average_rating) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if(isset($recommendPercent) && $recommendPercent > 0)
                                        <div class="recommendation-rate mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 600; color: #002347; font-size: 14px;">Recommended by users</span>
                                            <strong style="color: #002347; font-size: 14px; font-weight: 600;">{{ $recommendPercent }}%</strong>
                                        </div>
                                    @endif
                                @endif

                                <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-weight: 600; color: #002347; font-size: 14px;">Do you recommend {{ $bName }}?</span>
                                    <div style="display: flex; gap: 8px;">
                                        @auth
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff  ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff    ';">
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff     ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff   ';">
                                                <i class="fas fa-thumbs-down"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff  ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff    ';">
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff     ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff   ';">
                                                <i class="fas fa-thumbs-down"></i>
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- 2. Highlighted Reviews Card -->
                        @php
                            $topReviews = $business->reviews ? $business->reviews->where('status', 'active') : collect();
                            if ($topReviews->isEmpty() && $business->reviews) {
                                $topReviews = $business->reviews;
                            }
                        @endphp
                        @if($topReviews && $topReviews->count() > 0)
                        <div class="main_feature_lg">
                            <div class="feture_box review-breakdown-box">
                                <div class="review-header-box top_review_bx" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                                    <h2 class="size22 big-bld m-0">Highlighted reviews</h2>
                                    <a href="#section14" class="view-review-link">
                                        Go to reviews 
                                    </a>
                                </div>

                                @foreach($topReviews->take(2) as $review)
                                    <div class="sidebar-review-card {{ !$loop->last ? 'pb-4 mb-4' : '' }}" style="{{ !$loop->last ? 'border-bottom: 1px solid #f0f0f0;' : '' }}">
                                        <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                            <div class="review-user" style="display: flex; align-items: center; gap: 12px;">
                                                <a href="{{ optional($review->user)->profileUrl() ?? route('user.profile', ['id' => $review->user_id]) }}" style="text-decoration: none;">
@if($review->user && $review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                                                    <img src="{{ asset($review->user->profile_image) }}"
                                                        class="rounded-circle"
                                                        style="width: 42px; height: 42px; object-fit: cover;"
                                                        alt="User Image">
                                                @else
                                                    <div style="width: 42px; height: 42px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                        <span style="color: white; font-weight: bold; font-size: 17px;">
                                                            @if ($review->user && $review->user->user_type === 'admin')
                                                                {{ strtoupper(substr($review->public_name ?? 'P', 0, 1)) }}
                                                            @else
                                                                {{ strtoupper(substr($review->user->first_name ?? ($review->user->name ?? 'A'), 0, 1)) }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
</a>

                                                <div>
                                                    <p style="margin: 0; font-weight: 600;">
                                                        @if ($review->user && $review->user->user_type === 'admin')
                                                            {{ $review->public_name ?? 'Public' }}
                                                        @else
                                                            
        @if($review->user)
            <a href="{{ optional($review->user)->profileUrl() ?? route('user.profile', ['id' => $review->user_id]) }}" style="color: inherit; text-decoration: none;">
                {{ $review->user->displayName() }}
            </a>
        @else
            Anonymous
        @endif
        
                                                        @endif
                                                    </p>
                                                    @if($review->user && $review->user->job_title)
                                                        <div style="font-size: 12px; color: #5b6b7a; line-height:1;">{{ $review->user->job_title }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div style="text-align: right; flex-shrink: 0;">
                                                @if($review->created_at)
                                                    <span class="text-muted" style="font-size: 12px; white-space: nowrap; font-weight: 400;">{{ $review->created_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($review->translations && $review->translations->first() && $review->translations->first()->title)
                                            <p class="sidebar-review-card-p">
                                                {{ $review->translations->first()->title }}
                                            </p>
                                        @endif

                                        @if($hasUserRatings)
                                        <div class="d-flex align-items-center gap-1 mb-2" style="margin-top: 4px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($review->rating))
                                                    <i class="fas fa-star text-warning" style="font-size: 13px;"></i>
                                                @elseif($i - 0.5 <= $review->rating)
                                                    <i class="fas fa-star-half-alt text-warning" style="font-size: 13px;"></i>
                                                @else
                                                    <i class="far fa-star text-warning" style="font-size: 13px;"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        @endif

                                        @if($review->translations && $review->translations->first() && $review->translations->first()->description)
                                            <p>
                                                {{ \Illuminate\Support\Str::limit(strip_tags($review->translations->first()->description), 110) }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- 3. More about Business Sub-pages Card -->
                        <div class="main_feature_lg">
                            <x-more-about-business :business="$business" />
                        </div>

                        <!-- 4. Sticky Sidebar Navigation Card — LAST item, sticks as you scroll -->
                        <div class="main_feature_lg sticky-sidebar-nav-wrapper" id="stickySidebarNavWrapper">
                            <div class="feture_box review-breakdown-box sticky-sidebar-nav-card" id="stickySidebarNav" style="max-height: 25rem;">
                                <div class="sticky-nav-header text-center">
                                    <div class="sticky-nav-logo mb-2 d-flex justify-content-center align-items-center" style="gap: 10px;">
                                        <div class="business-initial-logo" style="width: 44px; height: 44px; margin-bottom:8px">
                                            <x-business-logo :business="$business" />
                                        </div>
                                        <div class="sticky-wrap-mf">
                                            <p class="sticky-nav-title mb-1" style="font-size: 17px; font-weight: 700; color: #002347;">
                                                {{ $business->translations->first()->name ?? '' }}
                                            </p>
                                            @if ($averageRating !== null)
                                                <div class="rating-group sticky-nav-rating mb-3">
                                                    <span>{{ formatRating($averageRating) }}</span>
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
                                                    @if ($hasUserRatings)
                                                        <span>({{ $ratingCount }})</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($business->is_affiliate)
                                        <a href="{{ $business->getTrackedUrl() }}"
                                            data-track="{{ json_encode([
                                                'type' => 'click',
                                                'business_id' => $business->id,
                                                'action' => 'visit_website',
                                                'label' => 'Visit Website',
                                            ]) }}"
                                            target="_blank" rel="sponsored" class="btn-orng cta cta_orange w-100 mb-3"
                                            style="font-size: 14px; font-weight: 600; padding: 9px 18px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                            {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt ms-2" style="font-size: 11px;"></i>
                                        </a>
                                    @endif
                                </div>

                                <div class="sticky-nav-links">
                                    <a href="#section1" class="sticky-nav-item active" data-target="section1">
                                        <span>Description</span>
                                    </a>
                                    @if((isset($aggregatedPros) && count($aggregatedPros) > 0) || (isset($pros) && $pros->count() > 0))
                                        <a href="#section-pros-cons" class="sticky-nav-item" data-target="section-pros-cons">
                                            <span>Pros & Cons</span>
                                        </a>
                                    @endif
                                    @if (isset($business->faqs) && $business->faqs->count() > 0)
                                        <a href="#section15" class="sticky-nav-item" data-target="section15">
                                            <span>FAQs</span>
                                        </a>
                                    @endif
                                    <a href="#section14" class="sticky-nav-item" data-target="section14">
                                        <span>Reviews</span>
                                    </a>
                                </div>

                                <div class="sticky-nav-footer pt-3 mt-2 border-top text-center">
                                    @auth
                                        <button type="button"
                                            onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                            class="btn-write-review-link"
                                            style="background: transparent; border: none; color: #002347; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                            <span style="text-decoration:none"><i class="fas fa-pencil-alt"></i></span> Write a review
                                        </button>
                                    @else
                                        <a href="javascript:void(0)"
                                            onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                            class="btn-write-review-link"
                                            style="background: transparent; border: none; color: #003f7d; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                            <span style="text-decoration:none"><i class="fas fa-pencil-alt"></i></span> Write a review
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

@livewire('add-review')

@if(auth()->check() && session()->has('pending_review_business_id'))
    @php
        $pendingBusId = session('pending_review_business_id');
        $pendingRec = session('pending_review_recommend');
        session()->forget(['pending_review_business_id', 'pending_review_recommend']);
    @endphp
    <script>
        (function() {
            let attempts = 0;
            function triggerPendingReviewModal() {
                attempts++;
                if (window.Livewire) {
                    if (typeof Livewire.dispatch === 'function') {
                        Livewire.dispatch('openReviewModal', { businessId: {{ $pendingBusId }}, recommend: {{ json_encode($pendingRec) }} });
                    } else if (typeof Livewire.emit === 'function') {
                        Livewire.emit('openReviewModal', {{ $pendingBusId }}, {{ json_encode($pendingRec) }});
                    }
                }
                if (attempts < 5) {
                    setTimeout(triggerPendingReviewModal, 500);
                }
            }
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(triggerPendingReviewModal, 300);
            } else {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(triggerPendingReviewModal, 300);
                });
            }
        })();
    </script>
@endif

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        // Translation Toggle
        $(document).on('click', '.btn-toggle-translation', function () {
            const button = $(this);
            const reviewId = button.data('review-id');
            const languageId = button.data('language-id');
            const type = button.data('type');
            const card = button.closest('.review-card');
            const reviewTextContainer = card.find('.review-text');

            const currentMode = button.data('mode') || 'original';

            if (currentMode === 'translation') {
                const originalText = button.data('original-text');
                reviewTextContainer.text(originalText);
                button.text('View Translation');
                button.data('mode', 'original');
                return;
            }

            if (!button.data('original-text')) {
                button.data('original-text', reviewTextContainer.text());
            }

            const cachedTranslation = button.data('cached-translation');
            if (cachedTranslation) {
                reviewTextContainer.text(cachedTranslation);
                button.text('View Original');
                button.data('mode', 'translation');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/{{ app()->getLocale() }}/review/translation`,
                type: 'POST',
                data: {
                    review_id: reviewId,
                    language_id: languageId,
                    type: type
                },
                success: function (response) {
                    if (response.translated) {
                        reviewTextContainer.text(response.translated);
                        button.text('View Original');
                        button.data('cached-translation', response.translated);
                        button.data('mode', 'translation');
                    } else {
                        alert("{{ static_text('error_translation_unavailable') ?: 'Translation not available.' }}");
                        button.text('View Translation');
                        button.data('mode', 'original');
                    }
                },
                error: function () {
                    alert('Error fetching translation.');
                    button.text('View Translation');
                    button.data('mode', 'original');
                }
            });
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.rating-filter-checkbox');
    const clearBtn = document.getElementById('clear-filters');
    const filterBadge = document.getElementById('filter-count-badge');
    const container = document.getElementById('reviews-list-container');
    let currentSortValue = '{{ request("sort", "recent") }}';

    // Sticky Sidebar Navigation Active Link & Smooth Scroll (Exact from product_detail.blade.php)
    const stickyNavLinks = document.querySelectorAll('.sticky-nav-links .sticky-nav-item');
    if (stickyNavLinks.length > 0) {
        stickyNavLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('data-target') || (this.getAttribute('href') ? this.getAttribute('href').replace('#', '') : '');
                
                if (targetId === 'section1') {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    stickyNavLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    return;
                }

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
            { id: 'section1', target: 'section1' },
            { id: 'section-pros-cons', target: 'section-pros-cons' },
            { id: 'section15', target: 'section15' },
            { id: 'section14', target: 'section14' }
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

    // JS Sticky Sidebar: makes nav card stick after scrolling past its natural position
    (function() {
        var wrapper = document.getElementById('stickySidebarNavWrapper');
        var card    = document.getElementById('stickySidebarNav');
        if (!wrapper || !card) return;

        var NAVBAR_HEIGHT = 90;
        var originalTop   = null;
        var originalLeft  = null;
        var originalWidth = null;
        var isFixed       = false;

        function computeOriginals() {
            // Always reset to natural flow before measuring
            if (isFixed) {
                card.style.cssText = '';
                wrapper.style.height = '';
                isFixed = false;
            }
            var rect      = wrapper.getBoundingClientRect();
            originalTop   = rect.top + window.scrollY;
            originalLeft  = rect.left;                    // left offset from viewport — scrollbar-safe
            originalWidth = rect.width;                   // use rect.width not offsetWidth
        }

        function onScroll() {
            if (originalTop === null) computeOriginals();
            var scrollY       = window.scrollY;
            var shouldBeFixed = (scrollY + NAVBAR_HEIGHT) > originalTop;

            if (shouldBeFixed && !isFixed) {
                isFixed = true;
                wrapper.style.height = card.offsetHeight + 'px';  // hold layout space
                card.style.cssText   =
                    'position:fixed !important;' +
                    'top:' + NAVBAR_HEIGHT + 'px !important;' +
                    'left:' + originalLeft + 'px !important;' +
                    'width:' + originalWidth + 'px !important;' +
                    'z-index:50 !important;' +
                    'max-height:440px !important;' +
                    'box-sizing:border-box !important;';
            } else if (!shouldBeFixed && isFixed) {
                isFixed = false;
                card.style.cssText   = '';
                wrapper.style.height = '';
            }
        }

        window.addEventListener('resize', function() { originalTop = null; onScroll(); });
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('load', function() { computeOriginals(); onScroll(); });
    })();

    // Sort Dropdown option selection
    document.querySelectorAll('.review-sort-option').forEach(optionBtn => {
        optionBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const sortVal = this.dataset.sortValue;
            const sortLbl = this.dataset.sortLabel;

            currentSortValue = sortVal;
            const labelEl = document.getElementById('current-sort-label');
            if (labelEl) labelEl.textContent = sortLbl;

            // Update checkmarks
            document.querySelectorAll('.review-sort-option').forEach(btn => {
                btn.classList.remove('is-selected');
                const check = btn.querySelector('.sort-check-icon');
                if (check) check.classList.add('d-none');
            });
            this.classList.add('is-selected');
            const check = this.querySelector('.sort-check-icon');
            if (check) check.classList.remove('d-none');

            // Close dropdown
            const dropdownBtn = document.getElementById('reviewSortDropdownBtn');
            if (dropdownBtn && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                const instance = bootstrap.Dropdown.getInstance(dropdownBtn) || new bootstrap.Dropdown(dropdownBtn);
                instance.hide();
            }

            fetchReviews();
        });
    });

    // Rating Checkbox changes
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateFilterUi();
            fetchReviews();
        });
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            updateFilterUi();
            fetchReviews();
        });
    }

    // AJAX Pagination clicks
    $(document).on('click', '#reviews-list-container .btn-pages a.pagination-btn', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            fetchReviews(url);
        }
    });

    function updateFilterUi() {
        const checkedList = Array.from(checkboxes).filter(cb => cb.checked);
        const count = checkedList.length;

        if (clearBtn) {
            clearBtn.style.display = count > 0 ? 'inline' : 'none';
        }
        if (filterBadge) {
            if (count > 0) {
                filterBadge.textContent = count;
                filterBadge.classList.remove('d-none');
            } else {
                filterBadge.classList.add('d-none');
            }
        }
    }

    function fetchReviews(customUrl) {
        const selectedStars = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (container) container.style.opacity = '0.5';

        const url = new URL(customUrl || window.location.href);
        url.searchParams.set('sort', currentSortValue);
        if (selectedStars.length > 0) {
            url.searchParams.set('stars', selectedStars.join(','));
        } else {
            url.searchParams.delete('stars');
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            if (container) {
                container.innerHTML = html;
                container.style.opacity = '1';
            }
            window.history.pushState({}, '', url.toString());
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
        })
        .catch(err => {
            console.error('Error fetching reviews:', err);
            if (container) container.style.opacity = '1';
        });
    }

    updateFilterUi();
});

function toggleSection14ReviewBreakdown() {
    if (window.innerWidth >= 768) return;

    const content = document.getElementById('section14ReviewBreakdownContent');
    const chevron = document.querySelector('.section14-rating-chevron');
    if (!content) return;

    const isExpanded = content.getAttribute('data-expanded') === 'true';

    if (isExpanded) {
        content.style.maxHeight = '0px';
        content.style.opacity = '0';
        content.setAttribute('data-expanded', 'false');
        if (chevron) {
            chevron.style.transform = 'rotate(0deg)';
            chevron.style.color = '#64748b';
        }
    } else {
        content.style.maxHeight = (content.scrollHeight + 50) + 'px';
        content.style.opacity = '1';
        content.setAttribute('data-expanded', 'true');
        if (chevron) {
            chevron.style.transform = 'rotate(180deg)';
            chevron.style.color = '#002347';
        }
    }
}

function openReviewReportModal(reviewId) {
    @if(!auth()->check())
        if(typeof openLoginModal === 'function') {
            openLoginModal();
        } else {
            window.location.href = "{{ route('login') }}";
        }
        return;
    @endif

    // Reset state
    document.getElementById('reportReviewModalId').value = reviewId;
    document.getElementById('reportReviewReason').value = '';
    document.getElementById('reportReviewDetails').value = '';
    document.getElementById('reportReviewDetailsWrapper').style.display = 'none';
    document.getElementById('submitReviewReportBtn').disabled = true;
    document.getElementById('submitReviewReportBtn').style.opacity = '0.5';

    // Deselect all option cards
    document.querySelectorAll('.report-review-option-card').forEach(c => c.classList.remove('selected'));

    const modalEl = document.getElementById('reportReviewModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function selectReviewReportReason(reason, card) {
    document.getElementById('reportReviewReason').value = reason;

    // Highlight selected card
    document.querySelectorAll('.report-review-option-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');

    // Show details textarea
    document.getElementById('reportReviewDetailsWrapper').style.display = 'block';

    // Enable submit
    const btn = document.getElementById('submitReviewReportBtn');
    btn.disabled = false;
    btn.style.opacity = '1';
}

function submitReviewReport() {
    const reviewId = document.getElementById('reportReviewModalId').value;
    const reason = document.getElementById('reportReviewReason').value;
    const details = document.getElementById('reportReviewDetails').value;
    const submitBtn = document.getElementById('submitReviewReportBtn');

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    fetch('/review/report', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            review_id: reviewId,
            report_reason: reason,
            report_details: details
        })
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit report';

        if (data.success) {
            const modalEl = document.getElementById('reportReviewModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            alert(data.message || 'Report submitted successfully!');
        } else if (data.require_login) {
            alert(data.message || 'Please sign in.');
        } else {
            alert(data.message || 'An error occurred. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit report';
        alert("{{ static_text('error_connection_issue') ?: 'An error occurred. Please check your connection.' }}");
    });
}
</script>
@endpush

<!-- Report Issue Modal -->
<div class="modal fade" id="reportReviewModal" tabindex="-1" aria-labelledby="reportReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 40px rgba(0,0,0,0.14);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 20px 24px 16px;">
                <div>
                    <h5 class="modal-title fw-bold m-0" id="reportReviewModalLabel" style="color: #002347; font-size: 18px;">Report a review</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size: 13px;">Select the reason that best describes the issue</p>
                </div>
                <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">✕</button>
            </div>
            <div class="modal-body" style="padding: 20px 24px;">
                <input type="hidden" id="reportReviewModalId">
                <input type="hidden" id="reportReviewReason">

                <!-- Radio option cards -->
                <div class="d-flex flex-column gap-2" id="reportReviewReasonOptions">
                    @foreach([
                        ['value' => 'Fake or fraudulent',   'icon' => 'fa-ban',              'desc' => 'The review appears to be fake or paid for'],
                        ['value' => 'Inappropriate content', 'icon' => 'fa-exclamation-triangle', 'desc' => 'Contains offensive language or hate speech'],
                        ['value' => 'Spam or advertising',  'icon' => 'fa-bullhorn',         'desc' => 'Promotional or irrelevant content'],
                        ['value' => 'Conflict of interest', 'icon' => 'fa-user-tie',         'desc' => 'Written by someone affiliated with the business'],
                        ['value' => 'Other issue',          'icon' => 'fa-comment-dots',     'desc' => 'Another problem not listed above']
                    ] as $opt)
                        <div class="report-review-option-card d-flex align-items-start gap-3 p-3" 
                             onclick="selectReviewReportReason('{{ $opt['value'] }}', this)"
                             style="border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; ">
                            
                            <div class="report-check flex-shrink-0 mt-1" style="width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; ">
                                <i class="fas fa-check" style="font-size: 10px; color: transparent;"></i>
                            </div>
                            
                            <div>
                                <h6 class="m-0" style="font-size: 14.5px; font-weight: 600; color: #1e293b; margin-bottom: 2px !important;">
                                    <i class="fas {{ $opt['icon'] }} me-1 text-muted" style="font-size: 13px;"></i> 
                                    {{ $opt['value'] }}
                                </h6>
                                <p class="text-muted m-0" style="font-size: 13px; line-height: 1.4;">{{ $opt['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Textarea (hidden initially) -->
                <div id="reportReviewDetailsWrapper" class="mt-4" style="display: none;">
                    <label class="form-label fw-bold" style="font-size: 13.5px; color: #334155;">Additional details (optional)</label>
                    <textarea id="reportReviewDetails" class="form-control" rows="3" placeholder="Please provide any additional context..." style="font-size: 14px; border-radius: 12px; padding: 12px; resize: none;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 16px 24px;">
                <button type="button" class="btn btn-light rounded-pill-8 px-4" data-bs-dismiss="modal" style="font-size: 14px;">Cancel</button>
                <button type="button" class="blue-btn btn rounded-pill-8 px-4" id="submitReviewReportBtn" disabled style=" color: white; font-size: 14px; " onclick="submitReviewReport()">Submit report</button>
            </div>
        </div>
    </div>
</div>
<style>
    .report-review-option-card:hover {
        border-color: #94a3b8 !important;
        background: #f8fafc;
    }
    .report-review-option-card.selected {
        border-color: #002347 !important;
        background: #f0f4ff;
    }
    .report-review-option-card.selected .report-check {
        background: #002347;
        border-color: #002347;
    }
    .report-review-option-card.selected .report-check i {
        color: #ffffff !important;
    }
</style>
