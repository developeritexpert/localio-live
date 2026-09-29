@extends('user_layout.master')
@push('styles')
<style>
    
</style>
@endpush

@php
    $lang_id = getCurrentLanguageID();
    $bTransTmp = $business->translations->firstWhere('lang_id', $lang_id) ?? $business->translations->first();
    $bNameTmp = $bTransTmp->name ?? 'Business';
    $bSlug = $bTransTmp->slug ?? $business->slug;
    $metaResolved = \App\Services\MetaTagService::resolveBusinessCompareMeta($business, $lang_id);
    $compMetaTitle = $metaResolved['title'];
    $compMetaDesc = $metaResolved['description'];
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

    $vsKey = static_text('vs_keyword');
    if (empty($vsKey) || $vsKey === 'vs_keyword') {
        $vsKey = 'vs';
    }
    $vsKeySlug = Str::slug($vsKey);
    $currentSubcat = request()->get('subcategory');
@endphp

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec all-com" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="asn_dv d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="background-color: #f7f9fb;">
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
                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $bName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" style="color: #002347; font-weight: 500;">
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
            <div class="col-md-8 col-8">
                <div class="top_head">
                    @php
                        $resolvedCompH1 = \App\Services\ContentTemplateService::resolveBusinessSection(
                            $business,
                            'compare_h1',
                            getCurrentLanguageID()
                        );
                        $finalCompH1 = !empty($resolvedCompH1['headline']) ? $resolvedCompH1['headline'] : ('Compare ' . $bName);

                        $resolvedCompSub = \App\Services\ContentTemplateService::resolveBusinessSection(
                            $business,
                            'compare_subtext',
                            getCurrentLanguageID()
                        );
                        $finalCompSub = !empty($resolvedCompSub['headline']) ? $resolvedCompSub['headline'] : $subHeadline;
                    @endphp
                    <div class="an_lkd d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h1 style=" margin: 0; ">
                            {!! \App\Services\ContentTemplateService::cleanHeading($finalCompH1) !!}
                        </h1>
                    </div>
                    <p class="text-meta">
                        Last updated {{ now()->subDay()->format('F j, Y') }}
                    </p>
                    @if(!empty($finalCompSub))
                    <div style="margin: 0; color: #1e3050;">
                        {!! $finalCompSub !!}
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-md-4 col-4 mt-4 mt-md-0">
                <div class="comparison-header-icons d-flex justify-content-md-end">
                    <div class="comp-header-icon-box" style=" border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);" title="{{ $bName }}">
                        <x-business-logo :business="$business" :name="$bName" size="xl" style="width: 100%; height: 100%; object-fit: contain;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Content Section: Description 1 Left, Interactive Compare Box + Sidebars Right -->
<section class="py-5 common_detail_sec" style="background-color: #ffffff;">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side: Title 1 & Description 1 AND Interactive Compare Tool -->
            <div class="col-lg-8 col-12">
                <!-- Section 1: Intro Text -->
                @if($compTitle1)
                    <div class="mb-4">
                        <h2 style="margin-bottom: 12px;">
                            {{ $compTitle1 }}
                        </h2>
                        @if($compDesc1)
                            <div class="content_box" style="font-size: 15px; color: #4a5568; line-height: 1.6;">
                                {!! $compDesc1 !!}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 2: Interactive Compare Tool Box (Replacing 2nd text) -->
                <div class="p-4 bg-white rounded-3 border mb-4" style="border-radius: 16px !important; border: 1.5px solid #e2e8f0 !important; box-shadow: 0 4px 14px rgba(0,0,0,0.03); overflow: visible !important;">
                    <h3 style="margin-bottom: 6px;">
                        Compare {{ $bName }} with any provider
                    </h3>
                    @php
                        $compareSubtext = static_text('compare_with_any_provider_desc');
                        if (empty($compareSubtext) || $compareSubtext === 'compare_with_any_provider_desc') {
                            $compareSubtext = 'Select another provider from the search box below to generate an instant side-by-side comparison.';
                        }
                    @endphp
                    <p>
                        {{ $compareSubtext }}
                    </p>

                    <div class="d-flex align-items-center gap-3 flex-wrap flex-md-nowrap p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; overflow: visible !important;">
                        <!-- Business A (Fixed) -->
                        <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-pill-8 border" style="min-width: 170px; border-color: #cbd5e1 !important; flex-shrink: 0; height: 48px;">
                            <div class="top-product-small-logo" >
                                <x-business-logo :business="$business" :name="$bName" />
                            </div>
                            <span style="font-size: 14px; font-weight: 600; color: #1e3050; white-space: nowrap;">{{ $bName }}</span>
                        </div>

                        <!-- VS Label -->
                        <span class="fw-bold text-muted px-1" style="font-size: 15px; font-family: sans-serif; flex-shrink: 0;">vs</span>

                        <!-- Business B Search Input Container (Shown when nothing selected) -->
                        <div id="compareSearchInputWrapper" class="position-relative flex-grow-1" style="min-width: 220px; overflow: visible !important;">
                            <div style="position: relative; display: flex; align-items: center;">
                                <i class="fas fa-search" style="position: absolute; left: 16px; color: #94a3b8; font-size: 14px; pointer-events: none;"></i>
                                <input type="text" id="compareSearchInput" class="form-control rounded-pill-8" placeholder="{{ static_text('search_product_to_compare_placeholder') ?: 'Search business to compare...' }}" style="font-size: 14px; border: 1.5px solid #cbd5e1; outline: none; padding-left: 42px; padding-right: 16px; height: 48px;" autocomplete="off">
                            </div>
                            <!-- Custom Auto-complete Container -->
                            <div id="compareSearchDropdown" class="custom-search-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; width: 100%; max-height: 260px; overflow-y: auto; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); z-index: 99999; margin-top: 6px;"></div>
                        </div>

                        <!-- Business B Selected Pill (Shown when business selected, same style as left with cross) -->
                        <div id="selectedBusinessPill" class="align-items-center justify-content-between gap-2 px-3 py-2 bg-white rounded-pill-8 border flex-grow-1" style="display: none !important; border-color: #cbd5e1 !important; min-width: 170px; height: 48px;">

                            <div class="d-flex align-items-center gap-2" style="min-width: 0;">

                                <div class="top-product-small-logo" id="selectedBusinessLogo" >
                                </div>

                                <span id="selectedBusinessName" style="font-size: 14px; font-weight: 600; color: #1e3050; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                            </div>

                            <button type="button" id="btnRemoveSelectedBusiness" class="btn p-0 border-0 d-flex align-items-center justify-content-center" style="width: 0px; height: 0px; border-radius: 5px; background: #f1f5f9; color: #64748b; font-size: 12px; cursor: pointer; flex-shrink: 0;" title="Remove selection" onmouseover="this.style.background='#e2e8f0'; this.style.color='#002347';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b';">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Compare Action Button -->
                        <button type="button" id="btnRunCompare" class="blue-btn btn px-4 fw-semibold" style="background-color: #174889; border-color: #174889; color: #ffffff; font-size: 14px; white-space: nowrap; height: 48px; opacity: 0.6; flex-shrink: 0;" disabled>
                            Compare
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Rating Breakdown, Popular Comparisons & More About Business -->
            <div class="col-lg-4 col-12 d-flex flex-column gap-4">
                <!-- Widget 1: Rating Breakdown -->
                @php
                    $rWordRaw = static_text('reviews_word');
                    $rSlug = (!empty($rWordRaw) && $rWordRaw !== 'reviews_word') ? $rWordRaw : 'reviews';
                    $reviewsPageUrl = route('ReviewShow', ['locale' => app()->getLocale(), 'slug' => $bSlug, 'reviews_slug' => $rSlug]);
                @endphp
                <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div class="review-header-box top_review_bx" style="display: flex; justify-content: space-between; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="top-product-medium-logo">
                                <x-business-logo :business="$business" :name="$bName" />
                            </div>
                            <div>
                                <p style="font-size: 16px !important; font-weight: 700 !important; margin: 0 0 4px 0; line-height: 1 !important;">{{ $bName }}</p>
                                @if($business->should_show_rating)
                                <div class="rating-group" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                                    <span>{{ formatRating($averageRating) }}</span>
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
                                    @if($business->should_show_review_count)
                                    <span>({{ number_format($totalReviews) }})</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        <a href="{{ $business->getTrackedUrl() }}" class="cta btn-orng justify-content-center" target="_blank" rel="sponsored" style="display: flex !important; width: fit-content; height: fit-content; align-items: center; padding: 8px 11px; white-space: nowrap;">
                            {{ static_text('visit_website') ?: 'Visit website' }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                        </a>
                    </div>

                    @if($business->should_show_detailed_ratings && isset($criteria) && count($criteria) > 0)
                        <p style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 15px;">{{ static_text('detailed_user_ratings_heading') ?: 'Detailed user ratings' }}</p>
                        <div class="mb-3">
                            @foreach ($criteria as $criterion)
                                <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                    <p class="m-0" style="font-size: 12px; font-weight: 500; color: #5b6b7a ;">{{ $criterion->name }}</p>
                                    <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                        <progress class="progress-bar w-100" value="{{ $criterion->average_rating * 20 }}" max="100" style="height: 8px;"></progress>
                                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 35px; text-align: right;">{{ formatRating($criterion->average_rating) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($business->should_show_detailed_ratings)
                    <div class="d-flex justify-content-between align-items-center pt-3 mt-3" style="border-top: 1px solid #f0f0f0;">
                        <span style="font-weight: 600; color: #002347; font-size: 14px;">{{ static_text('recommended_by_users') ?: 'Recommended by users' }}</span>
                        <strong style="font-weight: 600; color: #002347; font-size: 14px;">{{ $recommendPercent }}%</strong>
                    </div>
                    @endif

                    <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color:#002347; font-size: 14px;">{{ static_text('do_you_recommend_question') ?: 'Do you recommend' }} {{ $bName }}?</span>
                        <div style="display: flex; gap: 8px;">
                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor='#007bff';">
                                <i class="fas fa-thumbs-up" style="font-size: 16px;"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 28px; height: 28px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor='#007bff';">
                                <i class="fas fa-thumbs-down" style="font-size: 16px;"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Widget 2: Popular Comparisons Sidebar -->
                @if(isset($popularComparisons) && count($popularComparisons) > 0)
                    <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid #f0f0f0;">
                            <h6 style="font-size: 14px; font-weight: 600; color: #002347; margin: 0;">{{ static_text('popular_comparisons_heading') ?: 'Popular comparisons' }}</h6>
                            <a href="#grid-all-comparisons-section" style="color: #002347; font-weight: 600; font-size: 13px; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                View all comparisons
                            </a>
                        </div>

                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 14px;">
                            @foreach($popularComparisons->take(5) as $popPeer)
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

                <!-- Widget 3: More About Business Card -->
                <x-more-about-business :business="$business" />
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Popular {{ $bName }} comparisons (Top 6 Most Clicked/Popular) -->
@if(isset($popularComparisons) && count($popularComparisons) > 0)
<section class="compare-section py-5" style="background-color: #f7f9fb !important; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div class="hd_text mb-4">
            <h2 style="margin-bottom: 6px;">
                Popular {{ $bName }} comparisons
            </h2>
            <p style="margin: 0;">
                See how {{ $bName }} compares to top competitors.
            </p>
        </div>

        <div class="row g-4">
            @foreach($popularComparisons->take(6) as $popItem)
                @php
                    $popItemName = $popItem->translations->first()->name ?? 'Business';
                    $popItemRating = $popItem->display_rating ?? 0;
                    $seoUrlPop = route('product-comparison.seo', [
                        'locale' => app()->getLocale(),
                        'comparison_slug' => $compSlug,
                        'comparison_businesses' => Str::slug($bName) . '-' . $vsKeySlug . '-' . Str::slug($popItemName)
                    ]);
                @endphp
                <div class="col-lg-4 col-md-6 col-12">
                    <a href="{{ $seoUrlPop }}" class="comparison-box p-3 h-100 d-flex flex-column justify-content-center" style="background-color: #ffffff !important; border-radius: 12px !important; border: 1px solid #e2e8f0 ; color: inherit; text-decoration: none;">
                        <div class="cmpr_bx d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$business" :name="$bName" />
                                </div>
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $bName }}</div>
                                    @if($business->should_show_rating)
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ formatRating($business->display_rating) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="px-2 vs_circle text-muted flex-shrink-0" style="font-size: 16px; font-family: sans-serif;">{{ static_text('vs_keyword') ?: 'vs' }}</div>
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1; justify-content: flex-end;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$popItem" :name="$popItemName" />
                                </div>
                                <div style="min-width: 0; text-align: left;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $popItemName }}</div>
                                    @if($popItem->should_show_rating)
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ formatRating($popItem->display_rating) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Section 3: Recently viewed comparisons (3-6 items) -->
@if(isset($recentlyViewedComparisons) && count($recentlyViewedComparisons) > 0)
<section class="compare-section py-5" style="background-color: #ffffff !important; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div class="hd_text mb-4">
            <h2 style="margin-bottom: 6px;">
                Recently viewed comparisons
            </h2>
            <p>
                Comparisons of {{ $bName }} recently viewed by users.
            </p>
        </div>

        <div class="row g-4">
            @foreach($recentlyViewedComparisons->take(6) as $recentItem)
                @php
                    $recItemName = $recentItem->translations->first()->name ?? 'Business';
                    $recItemRating = $recentItem->display_rating ?? 0;
                    $seoUrlRec = route('product-comparison.seo', [
                        'locale' => app()->getLocale(),
                        'comparison_slug' => $compSlug,
                        'comparison_businesses' => Str::slug($bName) . '-' . $vsKeySlug . '-' . Str::slug($recItemName)
                    ]);
                @endphp
                <div class="col-lg-4 col-md-6 col-12">
                    <a href="{{ $seoUrlRec }}" class="comparison-box p-3 h-100 d-flex flex-column justify-content-center" style="border-radius: 12px !important; border: 1px solid #e2e8f0; color: inherit; text-decoration: none; ">
                        <div class="cmpr_bx d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$business" :name="$bName" />
                                </div>
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $bName }}</div>
                                    @if($business->should_show_rating)
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ formatRating($business->display_rating) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="px-2 vs_circle text-muted flex-shrink-0" style="font-size: 16px; font-family: sans-serif;">{{ static_text('vs_keyword') ?: 'vs' }}</div>
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1; justify-content: flex-end;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$recentItem" :name="$recItemName" />
                                </div>
                                <div style="min-width: 0; text-align: left;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $recItemName }}</div>
                                    @if($recentItem->should_show_rating)
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ formatRating($recentItem->display_rating) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Explore Subcategories Section (Matching Category Page) -->
@if(isset($exploreSubcategories) && isset($exploreSubcategories['items']) && count($exploreSubcategories['items']) > 0)
<section class="explore-subcategories-sec py-5" style="background: #f7f9fb; border-top: 1px solid #e2e8f0;border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div class="text-start mb-4">
            <h2 style="margin-bottom: 6px;">
                {{ $exploreSubcategories['title'] }}
            </h2>
            <p>
                Browse specialized categories to find the exact tools and solutions for your requirements.
            </p>
        </div>

        <div class="row g-3">
            @foreach($exploreSubcategories['items'] as $subcatItem)
            @php
                $subTrans = null;
                if (isset($subcatItem->translations)) {
                    if (is_object($subcatItem->translations) && !($subcatItem->translations instanceof \Illuminate\Database\Eloquent\Collection)) {
                        $subTrans = $subcatItem->translations;
                    } elseif ($subcatItem->translations instanceof \Illuminate\Database\Eloquent\Collection && $subcatItem->translations->isNotEmpty()) {
                        $subTrans = $subcatItem->translations->first();
                    }
                }
                $subName = $subTrans->name ?? $subcatItem->name ?? 'Subcategory';
                $subSlug = $subTrans->slug ?? $subcatItem->slug ?? (string)$subcatItem->id;
                $listingsCount = $subcatItem->listings_count ?? ($subcatItem->businesses_count ?? $subcatItem->businesses()->where('status', 1)->count());
            @endphp
            <div class="col-lg-4 col-md-6 col-12">
                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $subSlug]) }}" class="text-decoration-none d-block h-100">
                    <div class="comparison-box p-3 border rounded-3 bg-white h-100 d-flex flex-column align-items-center justify-content-between">
                        
                        <div class="mb-3 w-100 d-flex align-items-center justify-content-center">
                            @if(isset($subcatItem->topRatedImageMedia))
                                <img src="{{ asset($subcatItem->topRatedImageMedia->dir_path . '/' . $subcatItem->topRatedImageMedia->file_name) }}" alt="{{ $subName }}" style=" object-fit: contain;">
                            @else
                                <!-- Placeholder if no SVG is uploaded -->
                                <div style="width: 100%; height: 100px; background-color: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;"></div>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center w-100 mt-auto pt-2" style="border-top: 1px solid #f1f5f9;">
                            <p class="m-0 fw-bold text-start" style="font-size: 14px; color: #1e3050;">{{ $subName }}</p>
                            <span class="badge rounded-pill fw-normal" style="color: #64748b; background-color: #f8fafc; border: 1px solid #e2e8f0; font-size: 11px; padding: 5px 10px;">
                                {{ $listingsCount }} {{ $listingsCount == 1 ? 'Listing' : 'Listings' }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchableBusinesses = @json($allSearchableBusinesses ?? []);
        const input = document.getElementById('compareSearchInput');
        const inputWrapper = document.getElementById('compareSearchInputWrapper');
        const selectedPill = document.getElementById('selectedBusinessPill');
        const selectedLogo = document.getElementById('selectedBusinessLogo');
        const selectedName = document.getElementById('selectedBusinessName');
        const btnRemove = document.getElementById('btnRemoveSelectedBusiness');
        const dropdown = document.getElementById('compareSearchDropdown');
        const btn = document.getElementById('btnRunCompare');
        const compSlug = @json($compSlug);
        const vsKeySlug = @json($vsKeySlug);
        const bNameSlug = @json(Str::slug($bName));
        const currentLocale = @json(app()->getLocale());

        let selectedTargetSlug = null;

        if (!input || !dropdown || !btn) return;

        function renderResults(query) {
            const trimmed = query.toLowerCase().trim();

            let matches = searchableBusinesses;
            if (trimmed.length > 0) {
                matches = searchableBusinesses.filter(b => b.name.toLowerCase().includes(trimmed));
            }

            if (matches.length === 0) {
                dropdown.innerHTML = '<div class="p-3 text-muted small text-center">No businesses found matching "' + query + '"</div>';
                dropdown.style.display = 'block';
                return;
            }

            let html = '';
            matches.slice(0, 10).forEach(b => {
                const iconHtml = b.icon ? `<img src="${b.icon}" width="24" height="24" style="border-radius:5px; object-fit:cover; flex-shrink:0;">` : `<div style="width:24px;height:24px;border-radius:5px;background:#002347;color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">${b.name.charAt(0).toUpperCase()}</div>`;
                html += `
                    <div class="search-preview-item" data-slug="${b.slug}" data-name="${b.name}" data-icon="${b.icon || ''}">
                        ${iconHtml}
                        <span>${b.name}</span>
                    </div>
                `;
            });

            dropdown.innerHTML = html;
            dropdown.style.display = 'block';

            dropdown.querySelectorAll('.search-preview-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const name = this.dataset.name;
                    const slug = this.dataset.slug;
                    const icon = this.dataset.icon;

                    selectedTargetSlug = slug;
                    input.value = name;
                    dropdown.style.display = 'none';

                    // Update selected pill
                    if (selectedName) selectedName.textContent = name;
                    if (selectedLogo) {
                        if (icon && icon.trim() !== '') {
                            selectedLogo.innerHTML = `<img src="${icon}" alt="${name}" style="width: 100%; height: 100%; object-fit: cover;">`;
                        } else {
                            const initial = name.charAt(0).toUpperCase();
                            selectedLogo.innerHTML = `<div class="business-initial-logo" >${initial}</div>`;
                        }
                    }

                    // Show selected pill, hide input wrapper
                    if (inputWrapper) inputWrapper.style.display = 'none';
                    if (selectedPill) selectedPill.style.setProperty('display', 'flex', 'important');

                    btn.disabled = false;
                    btn.style.opacity = '1';
                });
            });
        }

        // Remove selected business action
        if (btnRemove) {
            btnRemove.addEventListener('click', function(e) {
                e.stopPropagation();
                selectedTargetSlug = null;
                input.value = '';
                if (selectedPill) selectedPill.style.setProperty('display', 'none', 'important');
                if (inputWrapper) inputWrapper.style.display = 'block';
                input.focus();
                btn.disabled = true;
                btn.style.opacity = '0.6';
            });
        }

        input.addEventListener('focus', function() {
            renderResults(this.value);
        });

        input.addEventListener('input', function() {
            renderResults(this.value);
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        btn.addEventListener('click', function() {
            if (!selectedTargetSlug) return;
            const targetUrl = `/${currentLocale}/${compSlug}/${bNameSlug}-${vsKeySlug}-${selectedTargetSlug}`;
            window.location.href = targetUrl;
        });
    });
</script>
@endpush
