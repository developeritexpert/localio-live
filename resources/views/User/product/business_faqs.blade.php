@extends('user_layout.master')
@push('styles')
    <link rel="stylesheet" href="{{ asset('front/css/product-detail-components.css') }}">
@endpush
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

    $faqTitle1 = $bTranslation->faqs_title ?? ('Frequently Asked Questions about ' . $bName);
    $faqDesc1  = $bTranslation->faqs_description ?? '';
    $faqTitle2 = $bTranslation->faqs_title_2 ?? '';
    $faqDesc2  = $bTranslation->faqs_description_2 ?? '';
@endphp

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="margin-top: 100px; background-color: #f7f9fb; color: #1e3050;  border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" >
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
                            <h1 style="font-size: 28px; font-weight: 700;  margin: 0; line-height: 1;">
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

<!-- Content & Right Sidebar Section (matching product_detail.blade.php structure) -->
<section class="revie_img_sec">
    <div class="container">
        <div class="image_revie_inr">

            <!-- Left Side: FAQs Content & Accordion -->
            <div class="is-asana-wrp imges_left_sde" data-aos="fade-up" data-aos-duration="1000">
                <div class="row sld_rw">
                    <div class="col-lg-12">
                        <div class="is-asana-lft">

                            <!-- Section 1: Title 1 & Description 1 -->
                            @if($faqTitle1)
                                <h2>{{ $faqTitle1 }}</h2>
                                @if($faqDesc1)
                                    <div class="is_text mb-5">
                                        {!! $faqDesc1 !!}
                                    </div>
                                @endif
                            @endif

                            <!-- Section 2: Title 2 & Description 2 -->
                            @if($faqTitle2 || $faqDesc2)
                                @if($faqTitle2)
                                    <h2>{{ $faqTitle2 }}</h2>
                                @endif
                                @if($faqDesc2)
                                    <div class="is_text ">
                                        {!! $faqDesc2 !!}
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>

                    <!-- FAQs Accordion (exact structure from product_detail) -->
                    <div class="col-lg-12">
                        <div class="faq-accor" style="width:100%;">
                            <div class="accordion" id="businessFaqAccordion">
                                @forelse ($business->faqs as $index => $faq)
                                    @php $translation = $faq->translations->first(); @endphp
                                    @if ($translation)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFaq{{ $index }}">
                                                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapseFaq{{ $index }}"
                                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                        aria-controls="collapseFaq{{ $index }}"
                                                        style="font-weight: 600;color: #002347;font-size: 16px;padding: 16px 20px;"
                                                        >
                                                    <span>{{ $translation->question }}</span>
                                                </button>
                                            </h2>
                                            <div id="collapseFaq{{ $index }}"
                                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                                 aria-labelledby="headingFaq{{ $index }}"
                                                 data-bs-parent="#businessFaqAccordion">
                                                <div class="accordion-body"
                                                style="font-size: 15px; color: #444; line-height: 1.6; padding: 20px;"
                                                >
                                                    {{ $translation->answer }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <p>No FAQs available for {{ $bName }} at this time.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- End Left Side -->

            <!-- Right Side: Sidebar (exact thre_revi_rgt structure from product_detail) -->
            <div class="thre_revi_rgt">
                <div class="main_feture">
                    <div class="fetru_row d-flex justify-content-between" data-aos="fade-up" data-aos-duration="1000">

                        <!-- 1. USPs -->
                        @if(!empty($business->is_affiliate) && $business->usps && $business->usps->count() > 0)
                            <div class="main_feature_lg">
                                <div class="feture_box lft_check_box size15">
                                    <ul class="list-unstyled">
                                        @foreach($business->usps as $usp)
                                            @php $uText = $usp->text ?? $usp->usp_text ?? ''; @endphp
                                            @if(!empty($uText))
                                                <li class="d-flex flex-row align-items-center size15">
                                                    <div class="grn_chk">
                                                        <img src="{{ asset('front/img/green-tick.svg') }}">
                                                    </div>
                                                    <p class="m-0">{{ $uText }}</p>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- 2. Review Breakdown -->
                        <div class="main_feature_lg">
                            <div class="feture_box review-breakdown-card">

                                {{-- Header & Overall Rating --}}
                                <div class="review-header-box top_review_bx" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom:15px;">
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
                                        <span class="f-12" style="color: #666;">{{ number_format($totalReviews) }} {{ $totalReviews == 1 ? 'review' : 'reviews' }}</span>
                                    </div>
                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section14" class="view-review-link" style="color: #06498b; font-weight: 600; font-size: 14px; text-decoration: none; padding-top: 5px;">
                                        View all reviews
                                    </a>
                                </div>

                                <h2 class="breakdown-title" style="margin-bottom: 15px;">
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
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                                <i class="fas fa-thumbs-down"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                                <i class="fas fa-thumbs-down"></i>
                                            </a>
                                        @endauth
                                    </div>
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
                            <div class="innr_price_trail">
                                @if($hasStartingPrice)
                                <div class="main_feature_sm">
                                    <div class="feture_box str_prc_box">
                                        <h6 class="starting-price-title">Starting price</h6>
                                        <h2 class="starting-price-value">{{ $currency }}{{ $startingPrice }}</h2>
                                        <p class="starting-price-text">Flat Rate, Per {{ ucfirst($timeUnit) }}</p>
                                        <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section6" class="starting-price-link">
                                            View pricing
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @if($hasFreeTrial)
                                <div class="main_feature_sm">
                                    <div class="fre_trail feture_box size22">
                                        <div class="grn_check_big">
                                            <img src="{{ asset('front/img/new-grn-chk.svg') }}">
                                        </div>
                                        <h6 class="blue-text big-bld">Free trial available</h6>
                                        <div class="accor-btn">
                                            <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="cta cta_white blue_t_org_btn" style="text-transform:none !important;">Claim now</a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endif

                        <!-- 4. Highlighted Reviews -->
                        @if(isset($topReviews) && $topReviews->count() > 0)
                            <div class="main_feature_lg">
                                <div class="feture_box review-breakdown-box">

                                    <div class="review-header-box pb-3" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 15px;">
                                        <h2 class="size22 big-bld m-0">Highlighted reviews</h2>
                                        <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#section14" class="view-review-link">
                                            View all reviews
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
                                        <div class="sidebar-review-card" style="margin-bottom: 20px;">
                                            <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                                <div class="review-user" style="display: flex; align-items: center; gap: 12px;">
                                                    @if($u && $u->profile_image && $u->profile_image !== 'front/img/default.png')
                                                        <img src="{{ asset($u->profile_image) }}" class="rounded-circle" width="45" height="45">
                                                    @else
                                                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                            <span style="color: white; font-weight: bold; font-size: 20px;">{{ strtoupper(substr($u->first_name ?? 'A', 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 style="margin: 0; font-size: 14px; font-weight: 600; color: #1e3050;">{{ $displayName }}</h6>
                                                        @if($u && $u->job_title)
                                                            <div style="font-size: 12px; color: #777; margin-top: 2px; line-height: 1.2;">{{ $u->job_title }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div style="text-align: right; flex-shrink: 0;">
                                                    <small class="text-muted" style="font-size: 11px; white-space: nowrap;">{{ $rev->created_at ? $rev->created_at->diffForHumans() : '' }}</small>
                                                </div>
                                            </div>
                                            <h5 style="margin-top: 10px; margin-bottom: 4px; font-size: 15px; font-weight: 600; color: #1e3050;">
                                                {{ $revTrans->title ?? 'Review' }}
                                            </h5>
                                            <div class="rating-stars-wrapper" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                                <div class="rating-stars">
                                                    @for($s=1; $s<=5; $s++)
                                                        @if($s<=floor($rev->rating))
                                                            <i class="fas fa-star text-warning" style="font-size: 12px !important;"></i>
                                                        @elseif($s-0.5<=$rev->rating)
                                                            <i class="fas fa-star-half-alt text-warning" style="font-size: 12px !important;"></i>
                                                        @else
                                                            <i class="far fa-star text-warning" style="font-size: 12px !important;"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            @if($revTrans && !empty($revTrans->description))
                                                <p style="font-size: 13.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">{{ \Illuminate\Support\Str::limit(strip_tags($revTrans->description), 90) }}</p>
                                            @endif
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @endif

                        <!-- 5. Recent Discussions -->
                        <div class="main_feature_lg">
                            <div class="feture_box review-breakdown-box">
                                <div class="review-header-box pb-3" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                                    <h2 class="size22 big-bld m-0">Recent discussions</h2>
                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bTranslation->slug ?? '']) }}#sectionDiscussions" class="view-review-link">
                                        View all discussions
                                    </a>
                                </div>

                                <div class="sidebar-review-card" style="margin-bottom: 20px;">
                                    <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                        <div class="review-user" style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <span style="color: white; font-weight: bold; font-size: 20px;">M</span>
                                            </div>
                                            <div>
                                                <h6 style="margin: 0; font-size: 14px; font-weight: 600; color: #1e3050;">Marc L.</h6>
                                                <div style="font-size: 12px; color: #777; margin-top: 2px;">Product Manager • Small Business (1-50 emp.)</div>
                                            </div>
                                        </div>
                                        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;">
                                            <small class="text-muted" style="font-size: 11px; white-space: nowrap;">2 hours ago</small>
                                        </div>
                                    </div>
                                    <h5 style="cursor: pointer;" onclick="document.getElementById('sectionDiscussions')?.scrollIntoView({behavior: 'smooth'})">
                                        Is there a free tier for API access or is it trial only?
                                    </h5>
                                    <p style="font-size: 13.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                        We are looking to integrate this into our workflow and want to test the latency over a few weeks...
                                    </p>
                                </div>

                                <div class="sidebar-review-card" style="margin-bottom: 0;">
                                    <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                        <div class="review-user" style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <span style="color: white; font-weight: bold; font-size: 20px;">S</span>
                                            </div>
                                            <div>
                                                <h6 style="margin: 0; font-size: 14px; font-weight: 600; color: #1e3050;">Sarah J.</h6>
                                                <div style="font-size: 12px; color: #777; margin-top: 2px;">CTO • Mid-Market (51-1000 emp.)</div>
                                            </div>
                                        </div>
                                        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;">
                                            <small class="text-muted" style="font-size: 11px; white-space: nowrap;">1 day ago</small>
                                        </div>
                                    </div>
                                    <h5 style="cursor: pointer;" onclick="document.getElementById('sectionDiscussions')?.scrollIntoView({behavior: 'smooth'})">
                                        How does the performance compare to alternatives in large datasets?
                                    </h5>
                                    <p style="font-size: 13.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                        We noticed some latency spikes during queries with more than 10k items. Anyone else facing this?
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 6. More about Business Card -->
                        <div class="main_feature_lg">
                            <x-more-about-business :business="$business" />
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Right Side -->

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
