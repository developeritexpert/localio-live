@extends('user_layout.master')
@push('styles')
<style>
    .auto-choice-rgt .btn-pages {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-top: 50px;
    }
    .auto-choice-rgt .btn-pages .pagination-btn {
        width: 44px;
        height: 44px;
        border-radius: 50% !important;
        border: 1.5px solid #174889;
        background-color: #ffffff;
        color: #174889;
        font-size: 15px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .auto-choice-rgt .btn-pages .pagination-btn.active {
        background-color: #174889 !important;
        color: #ffffff !important;
        border-color: #174889 !important;
    }
    .auto-choice-rgt .btn-pages .pagination-btn:hover {
        background-color: #174889 !important;
        color: #ffffff !important;
        border-color: #174889 !important;
    }
    .auto-choice-rgt .btn-pages .pagination-btn:hover i {
        color: #ffffff !important;
    }
    .auto-choice-rgt .btn-pages .pagination-arrow {
        background-color: #ffffff;
        border: 1.5px solid #174889;
        border-radius: 50% !important;
    }
    .auto-choice-rgt .btn-pages .pagination-arrow i {
        color: #174889;
        font-size: 13px;
    }
    .auto-choice-rgt .btn-pages .pagination-dots {
        font-size: 16px;
        color: #64748b;
        padding: 0 4px;
    }
</style>
@endpush


@php
    $lang_id = getCurrentLanguageID();
    $bTransTmp = $business->translations->firstWhere('lang_id', $lang_id) ?? $business->translations->first();
    $bNameTmp = $bTransTmp->name ?? 'Business';
    $compMetaTitle = !empty($bTransTmp->comparison_meta_title) ? $bTransTmp->comparison_meta_title : "Compare {$bNameTmp} with Top Competitors";
    $compMetaDesc = !empty($bTransTmp->comparison_meta_description) ? $bTransTmp->comparison_meta_description : ($bTransTmp->comparison_description ?? '');
@endphp

@section('meta_title', format_meta_text($compMetaTitle))
@if(!empty($compMetaDesc))
@section('meta_description', format_meta_text($compMetaDesc))
@endif

@section('content')
@php
    $lang_id = getCurrentLanguageID();
    $translation = $business->translations->first();
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? 'providers';
    $parentCatName = $parentCatTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $compSlug = $catTrans->comparison_slug ?? 'compare';
    $bName = $translation->name ?? 'Business';
    $subHeadline = static_text('business_comparisons_subheadline') !== 'business_comparisons_subheadline' 
        ? static_text('business_comparisons_subheadline') 
        : 'See how ' . $bName . ' compares to other ' . $catName . ' providers.';

    $compTitle1 = $translation->comparison_title ?? ('Compare ' . $bName);
    $compDesc1  = $translation->comparison_description ?? '';
    $compTitle2 = $translation->comparison_title_2 ?? '';
    $compDesc2  = $translation->comparison_description_2 ?? '';

    $vsKey = static_text('vs_keyword');
    if (empty($vsKey) || $vsKey === 'vs_keyword') {
        $vsKey = 'vs';
    }
    $vsKeySlug = Str::slug($vsKey);
@endphp

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="asn_dv d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="background-color: #f7f9fb;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb " style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
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
                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $business->translations->first()->slug ?? '']) }}" style="color: #64748b; text-decoration: none;">{{ $bName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        Comparisons
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
                <div class="top_head">
                    <div class="an_lkd d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin: 0; line-height: 1.2;">
                            Compare {{ $bName }}
                        </h1>
                    </div>
                    <p class="text-muted" style="font-size: 13px; margin-bottom: 14px; color: #888;">
                        Last updated on {{ now()->format('F j, Y') }}
                    </p>
                    <p style="font-size: 16px; color: #444; margin: 0; font-weight: 400;">
                        {{ $subHeadline }}
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-end text-start mt-md-0 mt-3">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="d-none btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Visit website <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Top Content Section: Both Titles & Descriptions on Left, Both Widgets on Right -->
<section class="py-5 common_detail_sec" style="background-color: #ffffff;">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side: Title 1 & Description 1 AND Title 2 & Description 2 -->
            <div class="col-lg-8 col-12">
                <!-- Section 1 -->
                @if($compTitle1)
                    <div class="mb-5">
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                            {{ $compTitle1 }}
                        </h2>
                        @if($compDesc1)
                            <div class="content_box" style="font-size: 15px; color: #4a5568; line-height: 1.6;">
                                {!! $compDesc1 !!}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 2 -->
                @if($compTitle2 || $compDesc2)
                    <div class="mb-4">
                        @if($compTitle2)
                            <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                                {{ $compTitle2 }}
                            </h2>
                        @endif
                        @if($compDesc2)
                            <div class="content_box" style="font-size: 15px; color: #4a5568; line-height: 1.6;">
                                {!! $compDesc2 !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Right Side: Rating Breakdown Widget & Popular Comparisons Widget -->
            <div class="col-lg-4 col-12 d-flex flex-column gap-4">
                <!-- Widget 1: Rating Breakdown (Identical to business details page) -->
                @php
                    $bSlug = $business->translations->first()->slug ?? ($business->slug ?? 'business');
                    $rWordRaw = static_text('reviews_word');
                    $rSlug = (!empty($rWordRaw) && $rWordRaw !== 'reviews_word') ? $rWordRaw : 'reviews';
                    $reviewsPageUrl = route('ReviewShow', ['locale' => app()->getLocale(), 'slug' => $bSlug, 'reviews_slug' => $rSlug]);
                @endphp
                <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div class="review-header-box top_review_bx" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                        <div class="d-flex align-items-center gap-2">
                            @if(!empty($business->icon_id))
                                <div style="width: 48px; height: 48px; flex-shrink: 0; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <img src="{{ asset($business->icon_id) }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @endif
                            <div>
                                <h3 style="font-size: 16px !important; font-weight: 700 !important; margin: 0 0 4px 0;">{{ $bName }}</h3>
                                <div class="rating-group" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                                    <span>{{ number_format($averageRating, 1) }}</span>
                                    <div class="rating-stars" style="display: flex; gap: 2px;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= floor($averageRating))
                                                <i class="fas fa-star text-warning" style="font-size: 13px;"></i>
                                            @elseif ($i - 0.5 <= $averageRating)
                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 13px;"></i>
                                            @else
                                                <i class="far fa-star text-warning" style="font-size: 13px;"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span>({{ number_format($totalReviews) }})</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ $business->getTrackedUrl() }}" class="cta btn-orng justify-content-center" target="_blank" style="display: flex !important; width: fit-content; height: fit-content; align-items: center; border-radius: 30px; padding: 11px 25px;">
                            Visit website
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                        </a>
                    </div>

                    @if(isset($criteria) && count($criteria) > 0)
                        <h6 style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 15px;">Review breakdown</h6>
                        <div class="mb-3">
                            @foreach ($criteria as $criterion)
                                <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                    <p class="m-0" style="font-size: 12px; font-weight: 500; color: #444;">{{ $criterion->name }}</p>
                                    <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                        <progress class="progress-bar w-100" value="{{ $criterion->average_rating * 20 }}" max="100" style="height: 8px;"></progress>
                                        <span style="font-size: 12px; font-weight: 600; color: #333;  min-width: 35px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center pt-3 mt-3" style="border-top: 1px solid #f0f0f0;">
                        <span style="font-weight: 600; color: #002347; font-size: 14px;">Recommended by users</span>
                        <strong style="font-weight: 600; color: #002347; font-size: 14px;">{{ $recommendPercent }}%</strong>
                    </div>

                    <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: #1e3050; font-size: 14px;">Do you recommend {{ $bName }}?</span>
                        <div style="display: flex; gap: 8px;">
                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: rgb(23, 72, 137); color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#f9633b';" onmouseout="this.style.backgroundColor='#06498b';">
                                <i class="fas fa-thumbs-up" style="font-size: 12px;"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 28px; height: 28px; border-radius: 50%; background-color: #06498b; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#f9633b';" onmouseout="this.style.backgroundColor='#06498b';">
                                <i class="fas fa-thumbs-down" style="font-size: 12px;"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Widget 2: Popular Comparisons -->
                @if(count($peerComparisons) > 0)
                    <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid #f0f0f0;">
                            <h6 style="font-size: 14px; font-weight: 600; color: #002347; margin: 0;">Popular comparisons</h6>
                            <a href="#grid-comparisons-section" style="color: #002347; font-weight: 600; font-size: 13px; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                View all comparisons
                            </a>
                        </div>

                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 14px;">
                            @foreach($peerComparisons->take(5) as $popPeer)
                                @php
                                    $popPeerName = $popPeer->translations->first()->name ?? 'Business';
                                    $popUrl = route('product-comparison.seo', [
                                        'locale' => app()->getLocale(),
                                        'comparison_slug' => $compSlug,
                                        'comparison_businesses' => Str::slug($bName) . '-' . $vsKeySlug . '-' . Str::slug($popPeerName)
                                    ]);
                                @endphp
                                <li>
                                    <a href="{{ $popUrl }}" style="color: #2b6cb0; text-decoration: none; font-weight: 600;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        {{ $bName }} VS {{ $popPeerName }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Comparisons Grid Section -->
<section id="grid-comparisons-section" class="compare-section all_comparisons_sec py-5" style="background-color: #f7f9fb !important; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div class="hd_text mb-4" data-aos="fade-up" data-aos-duration="1000">
            <h2 style="font-size: 26px; font-weight: 700; color: #1e3050; margin-bottom: 6px;">
                Compare {{ $bName }}
            </h2>
            <p style="font-size: 15px; color: #64748b; margin: 0;">
                See how {{ $bName }} compares to other {{ $catName }} providers.
            </p>
        </div>

        <div class="row g-4" data-aos="fade-up" data-aos-duration="1000">
            @forelse($peerComparisons as $peer)
                @php
                    $peerName = $peer->translations->first()->name ?? 'Business';
                    $peerRating = round($peer->reviews->avg('rating'), 1);
                    $seoUrl = route('product-comparison.seo', [
                        'locale' => app()->getLocale(),
                        'comparison_slug' => $compSlug,
                        'comparison_businesses' => Str::slug($bName) . '-' . $vsKeySlug . '-' . Str::slug($peerName)
                    ]);
                @endphp
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="comparison-box p-3 rounded-3 border h-100 d-flex flex-column justify-content-between" style="background-color: #f8fafc !important; border-radius: 12px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.03);" >
                        <div class="cmpr_bx d-flex align-items-center justify-content-between mb-3">
                            <!-- Business A -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$business" :name="$bName" />
                                </div>
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $bName }}</div>
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ number_format($businessRating, 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- VS Keyword -->
                            <!-- <div class="px-2 vs_circle text-muted flex-shrink-0" style="font-size: 16px; font-family: sans-serif;">
                                {{ strtoupper($vsKey) }}
                            </div> -->
                            <div class="px-2 vs_circle text-muted flex-shrink-0" style="font-size: 16px;font-family: sans-serif;">vs</div>
                            <!-- Business B (Peer) -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1; justify-content: flex-end;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$peer" :name="$peerName" />
                                </div>
                                <div style="min-width: 0; text-align: left;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $peerName }}</div>
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ number_format($peerRating, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compare Button -->
                        <div class="cmpre_btn w-100 mt-auto">
                            <a href="{{ $seoUrl }}" class="cta cta_btn text-decoration-none w-100" style="padding: 8px 20px !important; border-radius: 50px !important; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: center; width: 100%;" >
                                Compare
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">No comparisons available for this business yet.</div>
            @endforelse
        </div>

        @php
            $currentPage = $peerComparisons->currentPage();
            $lastPage = $peerComparisons->lastPage() ?? 1;
            $maxVisible = 7;

            $startPage = max(1, $currentPage - floor($maxVisible / 2));
            $endPage = min($lastPage, $startPage + $maxVisible - 1);

            if ($endPage - $startPage + 1 < $maxVisible) {
                $startPage = max(1, $endPage - $maxVisible + 1);
            }
            $showLeftDots = $startPage > 2;
            $showRightDots = $endPage < $lastPage - 1;
        @endphp

        @if ($lastPage > 1)
            <div class="auto-choice-rgt d-flex justify-content-center mt-5">
                <div class="btn-pages">
                    {{-- Previous Button (only if there's a previous page) --}}
                    @if ($currentPage > 1)
                        <a href="{{ $peerComparisons->url($currentPage - 1) }}" class="pagination-btn pagination-arrow">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- First Page --}}
                    @if ($startPage > 1)
                        <a href="{{ $peerComparisons->url(1) }}" class="pagination-btn {{ $currentPage == 1 ? 'active' : '' }}">1</a>
                        @if ($showLeftDots)
                            <span class="pagination-dots">...</span>
                        @endif
                    @endif

                    {{-- Page Numbers --}}
                    @for ($page = $startPage; $page <= $endPage; $page++)
                        <a href="{{ $peerComparisons->url($page) }}" class="pagination-btn {{ $currentPage == $page ? 'active' : '' }}">
                            {{ $page }}
                        </a>
                    @endfor

                    {{-- Last Page --}}
                    @if ($endPage < $lastPage)
                        @if ($showRightDots)
                            <span class="pagination-dots">...</span>
                        @endif
                        <a href="{{ $peerComparisons->url($lastPage) }}" class="pagination-btn {{ $currentPage == $lastPage ? 'active' : '' }}">
                            {{ $lastPage }}
                        </a>
                    @endif

                    {{-- Next Button (only if there's a next page) --}}
                    @if ($currentPage < $lastPage)
                        <a href="{{ $peerComparisons->url($currentPage + 1) }}" class="pagination-btn pagination-arrow next">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
