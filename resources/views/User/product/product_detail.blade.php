@extends('user_layout.master')

@section('body_class', 'product-page-body')

@php
$matchedLang = \App\Models\Language::where('lang_code', request()->segment(1))->first();
$currentLangId = isset($lang_id) && $lang_id ? $lang_id : ($matchedLang ? $matchedLang->id : getCurrentLanguageID());
$resolvedBizMeta = \App\Services\MetaTagService::resolveBusinessMeta($business, $currentLangId);
$pageMetaTitle = !empty($metaTitle) ? $metaTitle : $resolvedBizMeta['title'];
$pageMetaDescription = !empty($metaDescription) ? $metaDescription : $resolvedBizMeta['description'];
@endphp

@section('meta_title', format_meta_text($pageMetaTitle))
@if(!empty($pageMetaDescription))
@section('meta_description', format_meta_text($pageMetaDescription))
@endif

@section('content')
@php
    $images = [];
    $matchedCategory = null;
    $screenshotCategories = is_array($business->screenshot_categories) 
        ? $business->screenshot_categories 
        : json_decode($business->screenshot_categories ?? '[]', true);
        
    if (!empty($screenshotCategories)) {
        foreach ($screenshotCategories as $category) {
            if (($category['target_regions_mode'] ?? '') === 'specific' && !empty($category['target_regions'])) {
                if (in_array($lang_id, $category['target_regions'])) {
                    $matchedCategory = $category;
                    break;
                }
            }
        }
        if (!$matchedCategory) {
            foreach ($screenshotCategories as $category) {
                if (!empty($category['is_default'])) {
                    $matchedCategory = $category;
                    break;
                }
            }
        }
        if (!$matchedCategory) {
            foreach ($screenshotCategories as $category) {
                if (($category['target_regions_mode'] ?? 'all') === 'all') {
                    $matchedCategory = $category;
                    break;
                }
            }
        }
        if ($matchedCategory && !empty($matchedCategory['images'])) {
            $images = $matchedCategory['images'];
        } elseif ($matchedCategory && !empty($matchedCategory['urls'])) {
            foreach ($matchedCategory['urls'] as $url) {
                if (!empty($url)) {
                    $images[] = 'https://image.thum.io/get/width/600/crop/800/noanimate/' . $url;
                }
            }
        }
    }
    
    if (empty($images)) {
        $images = is_array($business->business_images)
            ? $business->business_images
            : json_decode($business->business_images ?? '[]', true);

        if (empty($images) && !empty($business->screenshot_urls)) {
            $urls = is_array($business->screenshot_urls)
                ? $business->screenshot_urls
                : json_decode($business->screenshot_urls ?? '[]', true);
            $urls = array_values(array_filter((array)$urls));
            if (!empty($urls)) {
                $images = $urls;
            }
        }
    }
@endphp

<script>
    console.log("Gallery script block rendered");

    let modalImages = {
        !!json_encode($images) !!
    };
    let currentModalIndex = 0;

    function updateModalImage(index) {
        console.log("updateModalImage called with index:", index);
        currentModalIndex = parseInt(index, 10);
        if (currentModalIndex >= 0 && currentModalIndex < modalImages.length) {
            let rawImg = modalImages[currentModalIndex];
            let imgUrl = (rawImg.startsWith('http://') || rawImg.startsWith('https://')) ?
                rawImg :
                '{{ asset("") }}' + rawImg.replace(/^\/+/, '');
            console.log("Updating modal active image src to:", imgUrl);
            let activeImg = document.getElementById('modalActiveImg');
            if (activeImg) {
                activeImg.src = imgUrl;
            } else {
                console.error("modalActiveImg element not found");
            }

            const thumbs = document.querySelectorAll('.modal-thumb-item');
            thumbs.forEach(thumb => {
                thumb.classList.remove('active-thumb');
                thumb.style.borderColor = 'transparent';
                thumb.style.opacity = '0.6';
            });

            const activeThumb = document.querySelector(`.modal-thumb-item[data-index="${currentModalIndex}"]`);
            if (activeThumb) {
                activeThumb.classList.add('active-thumb');
                activeThumb.style.borderColor = '#007bff';
                activeThumb.style.opacity = '1';
            } else {
                console.warn("Active thumbnail element not found for index:", currentModalIndex);
            }
        } else {
            console.error("Invalid currentModalIndex:", currentModalIndex, "Images count:", modalImages.length);
        }
    }

    window.openGallery = function(index) {
        console.log("Global openGallery called with index:", index);
        try {
            updateModalImage(index);
            const modalEl = document.getElementById('imageGalleryModal');
            if (modalEl) {
                console.log("Found modalEl, initializing Bootstrap Modal instance");
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    console.log("Bootstrap modal.show() invoked successfully");
                } else {
                    console.error("bootstrap or bootstrap.Modal is undefined! Make sure bootstrap bundle JS is loaded.");
                    // Fallback in case bootstrap object is missing:
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                    document.body.classList.add('modal-open');
                    let backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                    console.log("Applied fallback modal display style rules");
                }
            } else {
                console.error("imageGalleryModal element not found");
            }
        } catch (err) {
            console.error("Error in openGallery function:", err);
        }
    };

    window.addEventListener('load', function() {
        console.log("Gallery window load event fired. Images parsed:", modalImages);

        const thumbContainer = document.getElementById('modalThumbContainer');
        if (thumbContainer) {
            thumbContainer.addEventListener('click', function(e) {
                const thumbItem = e.target.closest('.modal-thumb-item');
                if (thumbItem) {
                    const index = thumbItem.getAttribute('data-index');
                    console.log("Modal thumbnail clicked. Index:", index);
                    updateModalImage(index);
                }
            });
        }

        const prevBtn = document.getElementById('modalPrevBtn');
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                let nextIndex = (currentModalIndex - 1 + modalImages.length) % modalImages.length;
                console.log("Modal prev button clicked. Next index:", nextIndex);
                updateModalImage(nextIndex);
            });
        }

        const nextBtn = document.getElementById('modalNextBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                let nextIndex = (currentModalIndex + 1) % modalImages.length;
                console.log("Modal next button clicked. Next index:", nextIndex);
                updateModalImage(nextIndex);
            });
        }

        document.addEventListener('keydown', function(e) {
            const modalEl = document.getElementById('imageGalleryModal');
            if (modalEl && modalEl.classList.contains('show')) {
                if (e.key === 'ArrowLeft') {
                    let nextIndex = (currentModalIndex - 1 + modalImages.length) % modalImages.length;
                    console.log("Left arrow pressed. Next index:", nextIndex);
                    updateModalImage(nextIndex);
                } else if (e.key === 'ArrowRight') {
                    let nextIndex = (currentModalIndex + 1) % modalImages.length;
                    console.log("Right arrow pressed. Next index:", nextIndex);
                    updateModalImage(nextIndex);
                }
            }
        });
    });
</script>

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
                    Livewire.dispatch('openReviewModal', {
                        businessId: {
                            {
                                $pendingBusId
                            }
                        },
                        recommend: {
                            {
                                json_encode($pendingRec)
                            }
                        }
                    });
                } else if (typeof Livewire.emit === 'function') {
                    Livewire.emit('openReviewModal', {
                        {
                            $pendingBusId
                        }
                    }, {
                        {
                            json_encode($pendingRec)
                        }
                    });
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
@include('User.partials.business_detail_styles')
<div data-business-id="{{ $business->id }}">
    <section class="product_sec">
        <div class="inner_banner_sec">
            <div class="container-fluid" style="display: flex; justify-content: space-between;">
                <div class="inner_banr_content">
                    <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            @if ($business->category && $business->category->parent)
                            @php
                            $parentTranslation = $business->category->parent->translation()->first();
                            @endphp
                            @if ($parentTranslation)
                            <li class="breadcrumb-item">
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentTranslation->slug]) }}"
                                    style="color: inherit; transition: none;" onmouseover="this.style.color='#f26522'"
                                    onmouseout="this.style.color=''">
                                    {{ $parentTranslation->name }}
                                </a>
                            </li>
                            @endif
                            @endif
                            @if ($business->category)
                            @php
                            $categoryTranslation = $business->category->translation()->first();
                            @endphp
                            @if ($categoryTranslation)
                            <li class="breadcrumb-item">
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $categoryTranslation->slug]) }}"
                                    style="color: inherit; transition: none;" onmouseover="this.style.color='#f26522'"
                                    onmouseout="this.style.color=''">
                                    {{ $categoryTranslation->name }}
                                </a>
                            </li>
                            @endif
                            @endif
                            <li class="breadcrumb-item active" aria-current="page">
                                <span>{{  $business->translations->first()->name ?? '' }}</span>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="inside_sec_text">
                    <x-social-icon :business="$business" />
                </div>
            </div>
        </div>

    </section>

    <section class="asn_main_sec asn_main_sec_2" id="section1">
        <div class="asn_dv">
            @php
            $activeReviews = $business->reviews->where('status', 'active');
            $ratingCount = $activeReviews->count();
            $hasUserRatings = $ratingCount >= 10;
            $hasUserReviews = $ratingCount > 0;
            if ($hasUserRatings) {
                $averageRating = round($activeReviews->avg('rating'), 1);
            } elseif ($business->admin_rating !== null && (float)$business->admin_rating > 0) {
                $averageRating = (float)$business->admin_rating;
            } else {
                $averageRating = null;
            }
            @endphp
            <div class="container-fluid">
                <div class="asn_dv_contnt ">
                    <div class="top-product-logo">
                        <x-business-logo :business="$business" />
                    </div>
                    <div class="div_prent_ever">

                        <div class="row frst_rw align-items-center">
                            <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                                <div class="ans_lft">
                                    <div class="asn-rating">
                                        @php
                                            $bizH1Fallback = ($business->translations->first()->name ?? $business->name ?? '') . ' review ' . date('Y');
                                            $resolvedBizH1 = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                $business,
                                                'profile_h1',
                                                getCurrentLanguageID()
                                            );
                                            $finalBizH1 = !empty($resolvedBizH1['headline']) ? $resolvedBizH1['headline'] : $bizH1Fallback;

                                            $bizSubtextFallback = 'Real reviews, user discussions & alternatives';
                                            $resolvedBizSubtext = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                $business,
                                                'profile_subtext',
                                                getCurrentLanguageID()
                                            );
                                            $finalBizSubtext = !empty($resolvedBizSubtext['headline']) ? $resolvedBizSubtext['headline'] : $bizSubtextFallback;
                                        @endphp
                                        <div class="an_lkd">
                                            <h1 style="color: #000;" class="mb-0 p-1">
                                                {!! \App\Services\ContentTemplateService::cleanHeading($finalBizH1) !!}
                                            </h1>
                                        </div>
                                        @if(!empty($finalBizSubtext))
                                            <p class="text-muted size16 hide-on-sticky" style="color: #666; font-size: 18px; margin-bottom: 0;">
                                                {!! $finalBizSubtext !!}
                                            </p>
                                        @endif

                                        @if ($averageRating !== null)
                                        <div class="main-view-rating-hide">
                                            <span style="font-size: 14px; font-weight: 500; color: #555;">
                                                {{ formatRating($averageRating) }}
                                            </span>
                                            <div style="display: flex; gap: 2px;">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <=floor($averageRating))
                                                    <i class="fas fa-star text-warning" style="font-size: 14px;"></i>
                                                    @elseif ($i - 0.5 <= $averageRating)
                                                        <i class="fas fa-star-half-alt text-warning" style="font-size: 14px;"></i>
                                                        @else
                                                        <i class="far fa-star text-warning" style="font-size: 14px;"></i>
                                                        @endif
                                                        @endfor
                                            </div>
                                            @if ($hasUserRatings)
                                            <span style="font-size: 14px; font-weight: 500; color: #555;">
                                                ({{ $ratingCount }})
                                            </span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                                <div class="ans_ryt">
                                    <div class="site_vsit">
                                        <ul class="list-unstyled">
                                        </ul>
                                        @if($business->is_affiliate)
                                        <div class="top-pro-btn tp_visit new-visit-anc top-vist">
                                            <a href="{{ $business->getTrackedUrl() }}"
                                                data-track="{{ json_encode([
                                                        'type' => 'click',
                                                        // 'product_id' => $product->id,
                                                        'business_id' => $business->id,
                                                        'action' => 'visit_website',
                                                        'label' => 'Visit Website',
                                                    ]) }}"
                                                class="btn-orng cta cta_orange d-flex align-items-center" target="_blank" rel="sponsored"
                                                tabindex="0">
                                                {{ static_text('visit_website') ?: 'Visit website' }}
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;">
                                                    <path d="M15 3h6v6"></path>
                                                    <path d="M10 14 21 3"></path>
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                </svg>
                                            </a>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="frst_re_2 row">
                            <div class="tp-btm d-flex col-lg-6">

                            </div>

                            @php
                            $ratingCount = $business->reviews->where('status', 'active')->count();
                            @endphp

                        </div>
                    </div>
                </div>

                                        @if ($averageRating !== null)
                                        <!-- Mobile Review Summary & Sliding Breakdown Card (Mobile only, below business name) -->
                                        <div class="mobile-review-summary-card d-md-none bg-white p-3 p-sm-4 rounded-3 border w-100 mt-3" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 14px rgba(0, 35, 71, 0.05); box-sizing: border-box;">
                                            <div class="d-flex justify-content-between align-items-start pb-3" style="border-bottom: 1px solid #f0f0f0;">
                                                <div class="d-flex flex-column align-items-start">
                                                    <span class="overall-rating-number" style="font-size: 38px; font-weight: 700; color: #002347; line-height: 1;">
                                                        {{ formatRating($averageRating) }}
                                                    </span>
                                                    @if($ratingWord = get_rating_label($averageRating))
                                                    <span class="rating-word-label mt-1" style="font-size: 14px; font-weight: 600; color: #002347;">
                                                        {{ $ratingWord }}
                                                    </span>
                                                    @endif
                                                    <div class="d-flex align-items-center gap-2 my-1 @if($hasUserRatings) mobile-rating-trigger @endif" @if($hasUserRatings) onclick="toggleMobileReviewBreakdown()" style="cursor: pointer; user-select: none;" title="Toggle rating breakdown" @endif>
                                                        <div class="d-flex align-items-center gap-1">
                                                            @for ($j = 1; $j <= 5; $j++)
                                                                @if ($j <= floor($averageRating))
                                                                <i class="fas fa-star text-warning" style="font-size: 16px;"></i>
                                                                @elseif ($j - 0.5 <= $averageRating)
                                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 16px;"></i>
                                                                @else
                                                                <i class="far fa-star text-warning" style="font-size: 16px;"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        @if($hasUserRatings)
                                                        <span class="mobile-rating-arrow-wrap d-inline-flex align-items-center justify-content-center" style="width: 22px; height: 22px; border-radius: 50%; background-color: #f1f5f9; transition: background-color 0.2s ease;">
                                                            <i class="fas fa-chevron-down mobile-rating-chevron" style="font-size: 11px; color: #64748b; transition: transform 0.3s ease;"></i>
                                                        </span>
                                                        @endif
                                                    </div>
                                                    @if($hasUserRatings)
                                                    <span class="com-rating" style="font-size: 13px; color: #64748b;">
                                                        {{ number_format($ratingCount) }} {{ $ratingCount == 1 ? 'review' : 'reviews' }}
                                                    </span>
                                                    @endif
                                                    <div class="top-pro-btn tp_visit new-visit-anc review-visit">
                                                        <a href="{{ isset($business) && method_exists($business, 'getTrackedUrl') ? $business->getTrackedUrl() : 'javascript:void(0)' }}" data-track="{{ json_encode(['type' => 'click', 'business_id' => $business->id ?? null, 'action' => 'visit_website', 'label' => 'Visit Website']) }}" class="btn-orng cta cta_orange d-flex align-items-center" target="_blank" rel="sponsored" tabindex="0">
                                                            {{ static_text('visit_website') ?: 'Visit website' }}
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;">
                                                                <path d="M15 3h6v6"></path>
                                                                <path d="M10 14 21 3"></path>
                                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>

                                                @if(($ratingCount ?? 0) >= 10)
                                                <a href="#section14" class="view-review-link" style="color: #003f7d; font-weight: 600; font-size: 13px; text-decoration: none; padding-top: 4px;" onclick="document.getElementById('section14')?.scrollIntoView({behavior: 'smooth'})">
                                                    View all reviews
                                                </a>
                                                @endif
                                            </div>

                                            <!-- Collapsible Breakdown Section (Hidden by default, slides out when clicking stars or arrow) -->
                                            @if($hasUserRatings)
                                            <div id="mobileReviewBreakdownContent" class="mobile-breakdown-collapsible" data-expanded="false" style="max-height: 0; overflow: hidden; opacity: 0; transition: max-height 0.35s ease-in-out, opacity 0.25s ease;">
                                                @if(isset($criteria) && count($criteria) > 0)
                                                <div class="pt-3">
                                                    <p class="breakdown-title" style="font-size: 14px; font-weight: 700; color: #002347; margin-bottom: 12px;">Review breakdown</p>

                                                    <div class="mb-2">
                                                        @foreach ($criteria as $criterion)
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span style="font-size: 13px; font-weight: 500; color: #002347; min-width: 105px; flex-shrink: 0;">{{ $criterion->name }}</span>
                                                            <div class="d-flex align-items-center flex-grow-1 mx-2" style="max-width: 55%;">
                                                                <div class="progress rounded-pill w-100" style="height: 7px; background-color: #e2e8f0;">
                                                                    <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ ($criterion->average_rating / 5) * 100 }}%; background-color: #007bff;" aria-valuenow="{{ $criterion->average_rating }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                </div>
                                                            </div>
                                                            <span style="font-size: 13px; font-weight: 600; color: #002347; min-width: 28px; text-align: right; flex-shrink: 0;">{{ formatRating($criterion->average_rating) }}</span>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif

                                                @if(isset($recommendPercent) && $recommendPercent > 0)
                                                <div class="pt-3 mt-2 border-top d-flex justify-content-between align-items-center" style="border-top-color: #f1f5f9 !important;">
                                                    <span style="font-weight: 600; color: #002347; font-size: 14px;">{{ static_text('recommended_by_users') ?: 'Recommended by users' }}</span>
                                                    <strong style="color: #002347; font-size: 14px; font-weight: 600;">{{ $recommendPercent }}%</strong>
                                                </div>
                                                @endif

                                                <div class="do-you-recommend " style="display: flex; justify-content: space-between; align-items: center;">
                                                    <span style="font-weight: 600; color: #002347; font-size: 14px;">{{ static_text('do_you_recommend_question') ?: 'Do you recommend' }} {{ $business->translations->first()->name ?? 'this business' }}?</span>
                                                    <div style="display: flex; gap: 8px;">
                                                        @auth
                                                        <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor='#007bff';">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor='#007bff';">
                                                            <i class="fas fa-thumbs-down"></i>
                                                        </a>
                                                        @else
                                                        <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor='#007bff';">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor='#007bff';">
                                                            <i class="fas fa-thumbs-down"></i>
                                                        </a>
                                                        @endauth
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
            </div>

            <div class="Tab-outerlnk container-fluid d-none">
                <div class="inner_table2">

                    @php

                    $name = $business->translations->first()->name ?? 'This Business';

                    // Fixed dynamic top content
                    $staticBottomSections = [];

                    if (isset($alternativeBusiness) && $alternativeBusiness->count() > 0) {
                    $staticBottomSections[] = ['id' => 'section9', 'label' => 'Alternatives'];
                    }

                    if (isset($business->faqs) && $business->faqs->count() > 0) {
                    $staticBottomSections[] = ['id' => 'section15', 'label' => 'FAQs'];
                    }

                    if (isset($peerComparisons) && count($peerComparisons) > 0) {
                    $staticBottomSections[] = ['id' => 'section-compare', 'label' => 'Compare'];
                    }

                    $staticBottomSections[] = ['id' => 'section14', 'label' => "Reviews"];

                    // Dynamic middle sections from topics
                    // Start dynamic topic sections at a high number to avoid conflict
                    $dynamicTopics = collect($business->category->topics ?? [])
                    ->map(function ($topic, $index) use ($name) {
                    $translatedHeading =
                    $topic->translations->first()?->title ?? 'Topic ' . ($index + 1);
                    $label = str_replace('{business_name}', $name, $translatedHeading);

                    return [
                    'id' => 'section' . (100 + $index), // Avoid conflicts with static IDs
                    'label' => $label,
                    ];
                    })
                    ->toArray();

                    // dd($dynamicTopics);

                    // Fixed bottom static sections

                    $staticTopSections = [
                    ['id' => 'section1', 'label' => 'Description'],
                    // ['id' => 'section2', 'label' => "$name"],
                    // ['id' => 'section3', 'label' => "Pricing"],
                    // ['id' => 'section4', 'label' => "Pros & cons"],
                    // ['id' => 'features' , 'label' => 'Features'],
                    // ['id' => 'section15' , 'label' => 'FAQs'],
                    // ['id' => 'softweretopic', 'label' => 'Software Topic'],
                    // ['id' => 'business-integration', 'label' => 'Integration']

                    ];

                    $tableOfContents = array_merge(
                    $staticTopSections,
                    //$dynamicTopics,
                    $staticBottomSections,
                    );
                    @endphp

                    <div class="inner_table2">
                        <div class="table_st">
                            <div id="table-of-content" class="feture_box p-3 shadow  bg-white bar-option"
                                style="top: 90px; max-height: max-content; overflow-y: auto;">
                                <ul class="list-unstyled toc-links small">
                                    @foreach ($tableOfContents as $i => $item)
                                    @php
                                    $isLastDynamic =
                                    str_starts_with($item['id'], 'section') &&
                                    (int) filter_var($item['id'], FILTER_SANITIZE_NUMBER_INT) ===
                                    100 + count($dynamicTopics) - 1;
                                    @endphp

                                    <li class=" {{ $isLastDynamic ? 'mb-0' : '' }}">
                                        <a href="#{{ $item['id'] }}" class="text-blue-600 d-block">
                                            {{ $item['label'] }}
                                        </a>
                                    </li>

                                    @if ($isLastDynamic)
                                    <li class="my-1"></li>
                                    @endif
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- after this -->
            </div>

        </div>

    </section>

    <section class="revie_img_sec blue-host_rvw">
        <div class="container">
            <div class="image_revie_inr mt-3">
                <div class="is-asana-wrp imges_left_sde" data-aos="fade-up" data-aos-duration="1000">
                    <div class="row sld_rw">
                        <div class="col-lg-12">
                            <div class="is-asana-lft">
                                @php
                                    $bizWhatIsFallback = 'What is ' . ($business->translations->first()->name ?? $business->name ?? '');
                                    $resolvedBizWhatIs = \App\Services\ContentTemplateService::resolveBusinessSection(
                                        $business,
                                        'profile_what_is',
                                        getCurrentLanguageID()
                                    );
                                    $finalBizWhatIs = !empty($resolvedBizWhatIs['headline']) ? $resolvedBizWhatIs['headline'] : $bizWhatIsFallback;
                                @endphp
                                <h2>{!! \App\Services\ContentTemplateService::cleanHeading($finalBizWhatIs) !!}</h2>
                                <div class="is_text">
                                    {!! $business->translations->first()->description !!}
                                </div>
                            </div>
                        </div>

                        @if(isset($aggregatedPros) && isset($aggregatedCons) && count($aggregatedPros) > 0 && count($aggregatedCons) > 0)
                        <div class="col-lg-12 mt-2 mb-4">
                            @php
                                $bizProsConsFallbackH2 = ($business->translations->first()->name ?? $business->name ?? 'Business') . ' pros and cons';
                                $resolvedBizProsCons = \App\Services\ContentTemplateService::resolveBusinessSection(
                                    $business,
                                    'profile_pros_cons',
                                    getCurrentLanguageID()
                                );
                                $finalBizProsConsH2 = !empty($resolvedBizProsCons['headline']) ? $resolvedBizProsCons['headline'] : $bizProsConsFallbackH2;
                                $finalBizProsConsUpper = !empty($resolvedBizProsCons['subtext']) ? $resolvedBizProsCons['subtext'] : (!empty($business->pro_cons_intro) ? $business->pro_cons_intro : null);
                            @endphp
                            <div class="pros-cons-header mb-3">
                                <h2 style="font-weight: 600; color: #1e3050; font-size: 24px; margin-bottom: {{ !empty($finalBizProsConsUpper) ? '8px' : '16px' }};">
                                    {!! \App\Services\ContentTemplateService::cleanHeading($finalBizProsConsH2) !!}
                                </h2>
                                @if(!empty($finalBizProsConsUpper))
                                <div class="pros-cons-intro" style="font-size: 15px; margin-bottom: 20px; line-height: 1.6; color: #475569;">
                                    {!! $finalBizProsConsUpper !!}
                                </div>
                                @endif
                            </div>

                            <div class="row g-4 d-none d-md-flex">
                                <div class="col-md-6">
                                    <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                        <div class="card-body">
                                            <p class="card-titles mb-3" style="font-weight: 500 !important; font-size:16px !important;">Pros</p>
                                            <ul class="list-unstyled mb-0">
                                                @foreach($aggregatedPros as $pro)
                                                <li class="d-flex align-items-start mb-2">
                                                    <span class="me-2" style="font-size: 18px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                    <span style="color: #202124;">{{ $pro->text }}
                                                        @if($hasUserRatings && isset($pro->review_count))<small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $pro->review_count }} {{ $pro->review_count == 1 ? 'review' : 'reviews' }}</small>@endif
                                                    </span>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                        <div class="card-body">
                                            <p class="card-titles mb-3" style="font-weight: 500 !important; font-size:16px !important;">Cons</p>
                                            <ul class="list-unstyled mb-0">
                                                @foreach($aggregatedCons as $con)
                                                <li class="d-flex align-items-start mb-2">
                                                    <span class="me-2" style="font-size: 18px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                    <span style="color: #202124;">{{ $con->text }}
                                                        @if($hasUserRatings && isset($con->review_count))<small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $con->review_count }} {{ $con->review_count == 1 ? 'review' : 'reviews' }}</small>@endif
                                                    </span>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Pros & Cons Tabs -->
                            <div class="mobile-pros-cons d-md-none mt-3">
                                <ul class="nav nav-tabs nav-justified" id="prosConsTab" role="tablist" style="border-bottom: 1px solid #e2e8f0; margin-bottom: 20px; flex-wrap: nowrap;">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="pros-tab" data-bs-toggle="tab" data-bs-target="#pros-content" type="button" role="tab" aria-controls="pros-content" aria-selected="true" style="font-weight: 600; color: #1e3050; border: none; background: transparent; padding-bottom: 12px; position: relative;">
                                            <span style="color: rgb(33, 172, 33);"><i class="fas fa-check"></i></span> Pros
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="cons-tab" data-bs-toggle="tab" data-bs-target="#cons-content" type="button" role="tab" aria-controls="cons-content" aria-selected="false" style="font-weight: 600; color: #64748b; border: none; background: transparent; padding-bottom: 12px; position: relative;">
                                            <span style="color: rgb(247, 40, 60);"><i class="fas fa-times"></i></span> Cons
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content" id="prosConsTabContent">
                                    <!-- Pros Tab Content -->
                                    <div class="tab-pane fade show active" id="pros-content" role="tabpanel" aria-labelledby="pros-tab">
                                        <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                            <div class="card-body">
                                                <p class="card-titles mb-3" style="font-weight: 500 !important; font-size:16px !important;">Pros</p>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($aggregatedPros as $pro)
                                                    <li class="d-flex align-items-start mb-2">
                                                        <span class="me-2" style="font-size: 18px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                        <span style="color: #202124;">{{ $pro->text }}
                                                            @if($hasUserRatings && isset($pro->review_count))<small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $pro->review_count }} {{ $pro->review_count == 1 ? 'review' : 'reviews' }}</small>@endif
                                                        </span>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cons Tab Content -->
                                    <div class="tab-pane fade" id="cons-content" role="tabpanel" aria-labelledby="cons-tab">
                                        <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                            <div class="card-body">
                                                <p class="card-titles mb-3" style="font-weight: 500 !important; font-size:16px !important;">Cons</p>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($aggregatedCons as $con)
                                                    <li class="d-flex align-items-start mb-2">
                                                        <span class="me-2" style="font-size: 18px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                        <span style="color: #202124;">{{ $con->text }}
                                                            @if($hasUserRatings && isset($con->review_count))<small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $con->review_count }} {{ $con->review_count == 1 ? 'review' : 'reviews' }}</small>@endif
                                                        </span>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @push('styles')
<style>
                                #prosConsTab .nav-link.active {
                                    color: #1e3050 !important;
                                }

                                #prosConsTab .nav-link.active::after {
                                    content: '';
                                    position: absolute;
                                    bottom: -1px;
                                    left: 0;
                                    width: 100%;
                                    height: 2px;
                                    background-color: #1e3050;
                                }
                            </style>
@endpush

                            @if(!empty($business->pro_cons_summary))
                            <div class="mt-4">
                                <div>{!! $business->pro_cons_summary !!}</div>
                            </div>
                            @endif
                        </div>
                        @endif

                        @php $offering = $business->offerings->first(); @endphp
                        @if($offering && (!empty($offering->headline) || !empty($offering->top_text)))
                        <div class="col-lg-12 mt-4 mb-2">
                            <div class="offering-section">
                                @php
                                    $bizOffersFallback = 'What ' . ($business->translations->first()->name ?? $business->name ?? '') . ' offers';
                                    $resolvedBizOffers = \App\Services\ContentTemplateService::resolveBusinessSection(
                                        $business,
                                        'profile_what_offers',
                                        getCurrentLanguageID()
                                    );
                                    $finalBizOffers = !empty($resolvedBizOffers['headline']) 
                                        ? $resolvedBizOffers['headline'] 
                                        : (!empty($offering->headline) ? $offering->headline : $bizOffersFallback);
                                @endphp
                                <h2 class="mb-2" style="font-weight: 600; font-size:24px;">{!! \App\Services\ContentTemplateService::cleanHeading($finalBizOffers) !!}</h2>

                                @if(!empty($offering->top_text))
                                <div class="mb-2">
                                    {!! $offering->top_text !!}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @php
                        // Safely decode the business_images
                        $images = [];
                        $matchedCategory = null;
                        $screenshotCategories = is_array($business->screenshot_categories) 
                            ? $business->screenshot_categories 
                            : json_decode($business->screenshot_categories ?? '[]', true);
                            
                        if (!empty($screenshotCategories)) {
                            foreach ($screenshotCategories as $category) {
                                if (($category['target_regions_mode'] ?? '') === 'specific' && !empty($category['target_regions'])) {
                                    if (in_array($lang_id, $category['target_regions'])) {
                                        $matchedCategory = $category;
                                        break;
                                    }
                                }
                            }
                            if (!$matchedCategory) {
                                foreach ($screenshotCategories as $category) {
                                    if (!empty($category['is_default'])) {
                                        $matchedCategory = $category;
                                        break;
                                    }
                                }
                            }
                            if (!$matchedCategory) {
                                foreach ($screenshotCategories as $category) {
                                    if (($category['target_regions_mode'] ?? 'all') === 'all') {
                                        $matchedCategory = $category;
                                        break;
                                    }
                                }
                            }
                            if ($matchedCategory && !empty($matchedCategory['images'])) {
                                $images = $matchedCategory['images'];
                            } elseif ($matchedCategory && !empty($matchedCategory['urls'])) {
                                foreach ($matchedCategory['urls'] as $url) {
                                    if (!empty($url)) {
                                        $images[] = 'https://image.thum.io/get/width/600/crop/800/noanimate/' . $url;
                                    }
                                }
                            }
                        }

                        if (empty($images)) {
                            $images = is_array($business->business_images)
                                ? $business->business_images
                                : json_decode($business->business_images ?? '[]', true);

                            if (empty($images) && !empty($business->screenshot_urls)) {
                                $urls = is_array($business->screenshot_urls)
                                    ? $business->screenshot_urls
                                    : json_decode($business->screenshot_urls ?? '[]', true);
                                $urls = array_values(array_filter((array)$urls));
                                if (!empty($urls)) {
                                    $images = $urls;
                                }
                            }
                        }
                        @endphp

                        @if (!empty($images))
                        <div class="col-lg-12 mb-4">
                            <div class="is-asana-rgt">
                                <div class="row is-asan-slider">

                                    <div class="col-md-12 asan-slider slider-for business-main-preview mb-3">
                                        @foreach ($images as $index => $image)
                                        @php
                                        $imgSrc = Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image);
                                        @endphp
                                        <div class="asan-slider-inr">
                                            <a href="{{ $business->is_affiliate ? $business->getTrackedUrl() : ($business->permanent_url ?: 'javascript:void(0)') }}"
                                                data-track="{{ json_encode([
                                                                            'type' => 'click',
                                                                            'business_id' => $business->id,
                                                                            'action' => 'visit_website',
                                                                            'label' => 'Visit Website',
                                                                        ]) }}"
                                                target="_blank" @if($business->is_affiliate) rel="sponsored" @else rel="noopener noreferrer" @endif class="d-block border rounded-3 overflow-hidden" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); outline: none;">
                                                <img src="{{ $imgSrc }}"
                                                    alt="Business Image {{ $index + 1 }}" class="img-fluid w-100 preview-main-display"
                                                    style="width: 100%; max-height: 650px; min-height: 250px; object-fit: cover; border-radius: 8px;">
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="col-md-12 asan-slider asan-slider-btm slider-nav">
                                        @foreach ($images as $index => $image)
                                        @php
                                        $imgSrc = Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image);
                                        @endphp
                                        <div style="padding: 0 5px; cursor: pointer;">
                                            <img src="{{ $imgSrc }}"
                                                alt="Thumbnail {{ $index + 1 }}"
                                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; cursor: pointer; border: none; outline: none;">
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Wait for slick to initialize
                                setTimeout(() => {
                                    var $slider = $('.asan-slider-btm');

                                    // Ensure only first thumbnail starts active
                                    $slider.find('.slick-slide').removeClass('active hover_main');
                                    $slider.find('.slick-slide[data-slick-index="0"]').addClass('active hover_main');

                                    // On hover, add class and remove from others
                                    $slider.on('mouseenter', '.slick-slide', function() {
                                        $slider.find('.slick-slide').removeClass('active hover_main');
                                        $(this).addClass('active hover_main');
                                        let index = $(this).data('slick-index');
                                        if (typeof index !== 'undefined' && $('.slider-for').hasClass('slick-initialized')) {
                                            $('.slider-for').slick('slickGoTo', parseInt(index, 10), true);
                                        }
                                    });

                                    $slider.on('click', '.slick-slide', function() {
                                        $slider.find('.slick-slide').removeClass('active hover_main');
                                        $(this).addClass('active hover_main');
                                        let index = $(this).data('slick-index');
                                        if (typeof index !== 'undefined' && $('.slider-for').hasClass('slick-initialized')) {
                                            $('.slider-for').slick('slickGoTo', parseInt(index, 10), true);
                                        }
                                    });
                                }, 500);
                            });
                        </script>

                        @php
                        $afterImageDesc = $business->translations->first()->after_image_description ?? ($offering->bottom_text ?? null);
                        @endphp
                        @if (!empty($afterImageDesc))
                        <div class="col-lg-12 mt-2 after-image-desc">
                            <div class="is_text">
                                {!! $afterImageDesc !!}
                            </div>
                        </div>
                        @endif

                    </div>

                    @php
                    $businessName = optional($business->translations->first())->name ?? 'Business';
                    $langCode = app()->getLocale() ?: 'en';

                    // Primary / secondary subcategory info
                    $primarySubcat = $business->subCategories->first() ?? $business->category;
                    $parentCat = $business->category?->parent ?? $business->category;
                    $categorySlug = $parentCat?->translations?->slug ?? ($business->category?->translations?->slug ?? 'category');
                    $subcategorySlug = $primarySubcat?->translations?->slug ?? 'all';

                    // Helper closure to calculate rating average for a key/name
                    $getRatingForCriterion = function($criterionName, $criterionKey = null) use ($criteria) {
                    foreach ($criteria as $cr) {
                    if (($criterionKey && $cr->default_key === $criterionKey) || strtolower($cr->name) === strtolower($criterionName)) {
                    return $cr->average_rating;
                    }
                    }
                    return 0.0;
                    };
                    @endphp

                    <div class="col-lg-12 mt-3">
                        @php
                        $effReviewCount = $ratingCount ?? ($business->reviews->where('status', 'active')->count() ?: 0);
                        @endphp

                        @php
                        $featRating = $getRatingForCriterion('Features', 'features');
                        $featRatingVal = $featRating > 0 ? $featRating : ($averageRating > 0 ? $averageRating : 4.0);
                        $featPercent = min(100, max(0, ($featRatingVal / 5) * 100));
                        @endphp
                        @if($hasUserRatings || $business->features->count() > 0)
                        <div class="rating-criteria-section mb-5 p-4 bg-white rounded shadow-sm border">
                            <div class="mb-3">
                                <h2 class="m-0" style="font-weight: 600; font-size: 24px; color: #002347;">Features</h2>
                            </div>

                            @if(!empty($ratingTexts['features']['intro_text']))
                            <div class="mb-3">{!! $ratingTexts['features']['intro_text'] !!}</div>
                            @endif

                            @if($hasUserRatings)
                            <div class="community-rating-box p-3 mb-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="community-rating mb-2" style="">User rating </div>
                                <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                    <span class="rating_bar_span">Features</span>
                                    <div class="progress" style="height: 8px; width: 140px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $featPercent }}%; border-radius: 10px;" aria-valuenow="{{ $featRatingVal }}" aria-valuemin="0" aria-valuemax="5"></div>
                                    </div>
                                    <span class="rating_bar_span">{{ formatRating($featRatingVal) }}</span>
                                </div>
                                <div class="community-base-rating">Based on {{ $effReviewCount }} {{ $effReviewCount == 1 ? 'rating' : 'ratings' }}</div>
                            </div>
                            @endif

                            @if(!empty($ratingTexts['features']['end_text']))
                            <div class="mb-3">{!! $ratingTexts['features']['end_text'] !!}</div>
                            @endif

                            @if($business->features->count() > 0)
                            <div class="features-chips-container mt-3">
                                <div class="fw-bold mb-2" style="font-size: 14px; color: #1e3050;">Key features</div>
                                <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                    @foreach($business->features as $feat)
                                    @php
                                    $featName = optional($feat->translations->first())->name ?? $feat->name;
                                    $featSlug = Str::slug($featName);
                                    $featUrl = url("/{$langCode}/{$categorySlug}/{$subcategorySlug}/{$featSlug}");
                                    @endphp
                                    <a href="{{ $featUrl }}" class="badge rounded-pill bg-light text-dark border px-3 py-2 text-decoration-none" style="font-weight: 500; font-size: 13px; ">
                                        {{ $featName }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($hasUserRatings)
                        @php
                        $easeRating = $getRatingForCriterion('Ease of use', 'ease_of_use');
                        $easeRatingVal = $easeRating > 0 ? $easeRating : ($averageRating > 0 ? $averageRating : 4.2);
                        $easePercent = min(100, max(0, ($easeRatingVal / 5) * 100));
                        @endphp
                        <div class="rating-criteria-section mb-5 p-4 bg-white rounded shadow-sm border">
                            <div class="mb-3">
                                <h2 class="m-0" style="font-weight: 600; font-size: 24px; color: #002347;">Ease of use</h2>
                            </div>

                            @if(!empty($ratingTexts['ease_of_use']['intro_text']))
                            <div class="mb-3">{!! $ratingTexts['ease_of_use']['intro_text'] !!}</div>
                            @endif

                            @if($hasUserRatings)
                            <div class="community-rating-box p-3 mb-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="community-rating mb-2">User rating </div>
                                <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                    <span class="rating_bar_span">Ease of use</span>
                                    <div class="progress" style="height: 8px; width: 140px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $easePercent }}%; border-radius: 10px;" aria-valuenow="{{ $easeRatingVal }}" aria-valuemin="0" aria-valuemax="5"></div>
                                    </div>
                                    <span class="rating_bar_span">{{ formatRating($easeRatingVal) }}</span>
                                </div>
                                <div class="community-base-rating">Based on {{ $effReviewCount }} {{ $effReviewCount == 1 ? 'rating' : 'ratings' }}</div>
                            </div>
                            @endif

                            @if(!empty($ratingTexts['ease_of_use']['end_text']))
                            <div class="mb-3">{!! $ratingTexts['ease_of_use']['end_text'] !!}</div>
                            @endif
                        </div>

                        @foreach($criteria as $cr)
                        @if(!in_array($cr->default_key, ['features', 'ease_of_use', 'value_for_money']) && !in_array(strtolower($cr->name), ['features', 'ease of use', 'value for money']))
                        @php
                        $crKey = (string)$cr->id;
                        $crRating = $cr->average_rating;
                        $crRatingVal = $crRating > 0 ? $crRating : 4.0;
                        $crPercent = min(100, max(0, ($crRatingVal / 5) * 100));
                        $crDisplayName = ucfirst($cr->name);
                        @endphp
                        <div class="rating-criteria-section mb-5 p-4 bg-white rounded shadow-sm border">
                            <div class="mb-3">
                                <h2 class="m-0" style="font-weight: 600; font-size: 24px; color: #002347;">{{ $crDisplayName }}</h2>
                            </div>

                            @if(!empty($ratingTexts[$crKey]['intro_text']))
                            <div class="mb-3">{!! $ratingTexts[$crKey]['intro_text'] !!}</div>
                            @endif

                            @if($hasUserRatings)
                            <div class="community-rating-box p-3 mb-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0; ">
                                <div class="community-rating mb-2">User rating </div>
                                <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                    <span class="rating_bar_span">{{ $crDisplayName }}</span>
                                    <div class="progress" style="height: 8px; width: 140px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $crPercent }}%; border-radius: 10px;" aria-valuenow="{{ $crRatingVal }}" aria-valuemin="0" aria-valuemax="5"></div>
                                    </div>
                                    <span class="rating_bar_span">{{ formatRating($crRatingVal) }}</span>
                                </div>
                                <div class="community-base-rating">Based on {{ $effReviewCount }} {{ $effReviewCount == 1 ? 'rating' : 'ratings' }}</div>
                            </div>
                            @endif

                            @if(!empty($ratingTexts[$crKey]['end_text']))
                            <div class="mb-3">{!! $ratingTexts[$crKey]['end_text'] !!}</div>
                            @endif
                        </div>
                        @endif
                        @endforeach

                        @php
                        $vfmRating = $getRatingForCriterion('Value for money', 'value_for_money');
                        $vfmRatingVal = $vfmRating > 0 ? $vfmRating : ($averageRating > 0 ? $averageRating : 4.1);
                        $vfmPercent = min(100, max(0, ($vfmRatingVal / 5) * 100));
                        @endphp
                        <div class="rating-criteria-section mb-5 p-4 bg-white rounded shadow-sm border">
                            <div class="mb-3">
                                <h2 class="m-0" style="font-weight: 600; font-size: 24px; color: #002347;">Value for money</h2>
                            </div>

                            @if(!empty($ratingTexts['value_for_money']['intro_text']))
                            <div class="mb-3">{!! $ratingTexts['value_for_money']['intro_text'] !!}</div>
                            @endif

                            @if($hasUserRatings)
                            <div class="community-rating-box p-3 mb-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="community-rating mb-2">User rating </div>
                                <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                    <span class="rating_bar_span">Value for money</span>
                                    <div class="progress" style="height: 8px; width: 140px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $vfmPercent }}%; border-radius: 10px;" aria-valuenow="{{ $vfmRatingVal }}" aria-valuemin="0" aria-valuemax="5"></div>
                                    </div>
                                    <span class="rating_bar_span">{{ formatRating($vfmRatingVal) }}</span>
                                </div>
                                <div class="community-base-rating">Based on {{ $effReviewCount }} {{ $effReviewCount == 1 ? 'rating' : 'ratings' }}</div>
                            </div>
                            @endif

                            @if(!empty($ratingTexts['value_for_money']['end_text']))
                            <div class="mb-3">{!! $ratingTexts['value_for_money']['end_text'] !!}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                    <!-- all new part here -->
                    <div class="con_table pb-0 all_sec_wrp" id="section2">
                        <div class="">

                            <div class=" ">
                                <div class="">
                                    <div class="inner_table_1">
                                        <section class="is-asana light">

                                        </section>

                                        <section class="reviews-section">

                                        </section>

                                        @php
                                        $currentLangId = $lang_id ?? getCurrentLanguageID();
                                        $prosList = collect();
                                        $consList = collect();

                                        foreach ($business->reviews as $review) {
                                        $translation = $review->translations->firstWhere('language_id', $currentLangId);
                                        if ($translation) {
                                        // Assuming pros and cons are comma-separated strings or arrays
                                        $prosList = $prosList->merge(
                                        array_filter(array_map('trim', explode(',', $translation->pros ?? ''))),
                                        );
                                        $consList = $consList->merge(
                                        array_filter(array_map('trim', explode(',', $translation->cons ?? ''))),
                                        );
                                        }
                                        }

                                        $uniquePros = $prosList->unique()->take(5); // Limit for layout
                                        $uniqueCons = $consList->unique()->take(5);
                                        @endphp

                                        <div class="modal fade" id="reviewPopup" tabindex="-1" role="dialog" aria-labelledby="reviewPopupLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="reviewPopupLabel">{{ static_text('submit_your_review_heading') ?: 'Submit Your Review' }}</h3>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body text-center">
                                                        <input type="hidden" id="popupFeatureId">
                                                        <input type="hidden" id="popupRating">

                                                        <div class="mb-3">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <span class="popup-star fs-4 mx-1" data-value="{{ $i }}" style="cursor:pointer; color:#ccc;">&#9733;</span>
                                                                @endfor
                                                        </div>

                                                        <textarea id="popupComment" class="form-control mb-3" rows="3" placeholder="{{ static_text('write_a_review_placeholder') ?: 'Write a review (optional)' }}"></textarea>

                                                        <button id="submitPopupReview" class="btn btn-primary w-100">{{ static_text('submit_review_button') ?: 'Submit Review' }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <section class="choice-business d-none" id="softweretopic">

                                        </section>

                                        @php
                                        // Decode items safely
                                        $items = [];
                                        if (!empty($business->integration?->items)) {
                                        $items = is_array($business->integration->items) ? $business->integration->items : json_decode($business->integration->items, true);
                                        }

                                        @endphp

                                        @if(isset($alternativeBusiness) && $alternativeBusiness->count() > 0)
                                        <section class="software-like p_50 product_integra_sec " id="section9">
                                            <div class="container">
                                                <div class="sftwre-like-innr">
                                                    <div class="sftwre-asana-hd text-center" data-aos="fade-up" data-aos-duration="1000">
                                                        @php
                                                            $bName = $business->translations->first()->name ?? $business->name ?? 'Business';
                                                            $bizAltFallbackH2 = $bName . ' Alternatives & Competitors';
                                                            $bizAltFallbackSub = "Based on other buyer's searches, these are the products that could be a good fit for you.";
                                                            $resolvedBizAlternatives = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                                $business,
                                                                'profile_alternatives',
                                                                getCurrentLanguageID()
                                                            );
                                                            $finalBizAltH2 = !empty($resolvedBizAlternatives['headline']) ? $resolvedBizAlternatives['headline'] : $bizAltFallbackH2;
                                                            $finalBizAltSubtext = !empty($resolvedBizAlternatives['subtext']) ? $resolvedBizAlternatives['subtext'] : $bizAltFallbackSub;
                                                        @endphp
                                                        <h2>{!! \App\Services\ContentTemplateService::cleanHeading($finalBizAltH2) !!}</h2>
                                                        @if(!empty($finalBizAltSubtext))
                                                            @if(str_starts_with(trim($finalBizAltSubtext), '<'))
                                                                {!! $finalBizAltSubtext !!}
                                                            @else
                                                                <p>{!! $finalBizAltSubtext !!}</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="sft_ware_test"
                                                        style="display: flex; justify-content:center; align-items: center;">

                                                        <div class="sftware-alternative d-flex" data-aos="fade-up"
                                                            data-aos-duration="1000">
                                                            @foreach ($alternativeBusiness as $altbusiness)
                                                            @if ($altbusiness->id == $business->id)
                                                            @continue
                                                            @endif
                                                            <div class="sftware-alternative-pck" data-aos="fade-up"
                                                                data-aos-duration="1000"
                                                                onclick="if(!event.target.closest('a')) { window.location.href = '{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $altbusiness->translations->first()->slug]) }}'; }"
                                                                style="cursor: pointer; padding: 25px 20px;">
                                                                @php
                                                                $altStartingPrice = 'N/A';
                                                                $altCurrency = '$';
                                                                $altAdditionalInfo = 'NA';
                                                                $altPrice = getBusinessesWithStartingPrice($altbusiness);
                                                                if (!empty($altPrice) && isset($altPrice[0]['starting_price'])) {
                                                                $altBusinessPrice = $altPrice[0]['starting_price'];
                                                                $altStartingPrice = $altBusinessPrice['amount'] ?? 'N/A';
                                                                $altCurrency = $altBusinessPrice['currency'] ?? '$';
                                                                $altTimeUnit = ucfirst($altBusinessPrice['time_unit'] ?? 'month');
                                                                $altAdditionalInfo =
                                                                $altBusinessPrice['additional_info'] ?? 'NA';
                                                                }

                                                                // Get reviews for the current altbusiness
                                                                $altReviews = \App\Models\Review::where(
                                                                'business_id',
                                                                $altbusiness->id,
                                                                )->get();

                                                                $altEaseOfUseAvg = round(
                                                                $altReviews->avg('ease_of_use_rating'),
                                                                1,
                                                                );
                                                                $altValueForMoneyAvg = round(
                                                                $altReviews->avg('value_for_money_rating'),
                                                                1,
                                                                );
                                                                $altCustomerServiceAvg = round(
                                                                $altReviews->avg('customer_service_rating'),
                                                                1,
                                                                );
                                                                $altExclusiveFeatureAvg = round(
                                                                $altReviews->avg('exclusive_service_rating'),
                                                                1,
                                                                );

                                                                $altRatingAvg = $altbusiness->reviews->avg('rating');
                                                                $count = $altbusiness->reviews->where('status', 'active')->count();
                                                                @endphp

                                                                <div class="ans_lft p_top_btm_sftwre pt-0 pb-3" style="border-bottom: 1px solid #eee;">
                                                                    <div class="top-product-logo">
                                                                        <x-business-logo :business="$altbusiness" />
                                                                    </div>
                                                                    <div class="asn-rating">
                                                                        @if ($altbusiness->translations->isNotEmpty())
                                                                        <div class="m-0 fw_700" style="color: #002347">
                                                                            {{ $altbusiness->translations->first()->name }}
                                                                        </div>
                                                                        @else
                                                                        <div class="m-0 fw_700" style="color: #002347">Name not available</div>
                                                                        @endif
                                                                        <div class="rating-group">
                                                                            <span class="rate_box_num fw-medium" style="">{{ formatRating($altRatingAvg) }}</span>
                                                                            <div class="rating-stars" style="">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if ($i <=floor($altRatingAvg))
                                                                                    <i class="fas fa-star text-warning"></i>
                                                                                    @elseif ($i - 0.5 <= $altRatingAvg)
                                                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                                                        @else
                                                                                        <i class="far fa-star text-warning"></i>
                                                                                        @endif
                                                                                        @endfor
                                                                            </div>
                                                                            <span class="rate_box_text" style="">
                                                                                ({{ $count }})
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="over-rate-progress p_top_btm_sftwre pt-3 pb-3" style="border-bottom: 1px solid #eee;">
                                                                    <div class="fw_700 mb-3" style="color: #002347; font-size:14px; font-weight: 700;">User rating</div>
                                                                    <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                        <p class="m-0" style="font-size: 12px; color: #555;">Ease of Use</p>
                                                                        <div class="prgs_br d-flex align-items-center">
                                                                            <progress class="progress-bar"
                                                                                value="{{ ($altEaseOfUseAvg ?? 0) * 20 }}"
                                                                                max="100">
                                                                            </progress>
                                                                            <span style="font-size: 12px; font-weight: 600; color: #333;  min-width: 32px; text-align: right;">{{ $altEaseOfUseAvg ?? 0 }} </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                        <p class="m-0" style="font-size: 12px; color: #555;">Customer Service</p>
                                                                        <div class="prgs_br d-flex align-items-center">
                                                                            <progress class="progress-bar"
                                                                                value="{{ ($altCustomerServiceAvg ?? 0) * 20 }}"
                                                                                max="100">
                                                                            </progress>
                                                                            <span style="font-size: 12px; font-weight: 600; color: #333;  min-width: 32px; text-align: right;">{{ $altCustomerServiceAvg ?? 0 }} </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                        <p class="m-0" style="font-size: 12px; color: #555;">Features</p>
                                                                        <div class="prgs_br d-flex align-items-center">
                                                                            <progress class="progress-bar"
                                                                                value="{{ ($altExclusiveFeatureAvg ?? 0) * 20 }}"
                                                                                max="100">
                                                                            </progress>
                                                                            <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ $altExclusiveFeatureAvg ?? 0 }} </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                        <p class="m-0" style="font-size: 12px; color: #555;">Value for Money</p>
                                                                        <div class="prgs_br d-flex align-items-center">
                                                                            <progress class="progress-bar"
                                                                                value="{{ ($altValueForMoneyAvg ?? 0) * 20 }}"
                                                                                max="100">
                                                                            </progress>
                                                                            <span style="font-size: 12px; font-weight: 600; color: #333;  min-width: 32px; text-align: right;">{{ $altValueForMoneyAvg ?? 0 }} </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @php
                                                                    $hasAltPrice = !empty($altbusiness->is_affiliate) && !empty($altStartingPrice) && !in_array(strtoupper(trim((string)$altStartingPrice)), ['N/A', 'NA', 'NULL', '0', '0.00', '']);
                                                                @endphp
                                                                @if ($hasAltPrice)
                                                                    <div class="start-from p_top_btm_sftwre pt-3 pb-3">
                                                                        <div style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 14px;">Starting price</div>
                                                                        <h3 class="m-0 mt-1" style="font-weight: 700; color: #333; font-size: 24px; line-height:1!important; ">
                                                                            <span>{{ $altCurrency }}{{ $altStartingPrice }}</span>
                                                                        </h3>
                                                                        @if(!empty($altAdditionalInfo) && !in_array(strtoupper(trim((string)$altAdditionalInfo)), ['NA', 'N/A', 'NULL']))
                                                                            <small class="text-muted" style="font-size: 12px;">{{ $altAdditionalInfo }}</small>
                                                                        @elseif(!empty($altBusinessPrice['time_unit']) && !in_array(strtolower($altBusinessPrice['time_unit']), ['one_time', 'na', 'n/a', 'null']))
                                                                            <small class="text-muted" style="font-size: 12px;">per {{ strtolower($altBusinessPrice['time_unit']) }}</small>
                                                                        @endif
                                                                    </div>
                                                                @endif

                                                                <div class="sftwre-alt-btn pt-2">
                                                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $altbusiness->translations->first()->slug]) }}"
                                                                        class="cta btn_blue w-100 d-flex align-items-center justify-content-center"
                                                                        style=" padding: 10px 20px; font-weight: 500; text-decoration: none; font-size: 14px;  ">
                                                                        {{ static_text('profile_and_reviews') ?: 'Profile & reviews' }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="text-center mt-4">
                                                    @php
                                                    $languageObj = \App\Models\Language::where('lang_code', app()->getLocale())->first();
                                                    $expectedAlternativesSlug = !empty($languageObj->alternatives_slug) ? $languageObj->alternatives_slug : 'alternatives';
                                                    @endphp
                                                    <a href="{{ route('business.alternatives', ['locale' => app()->getLocale(), 'business_slug' => $business->translations->first()->slug ?? $business->slug ?? '', 'alternatives_slug' => $expectedAlternativesSlug]) }}"
                                                        class="view-more-link"
                                                        style="font-size: 14px; font-weight: 600; color: #002347; text-decoration: none;">
                                                        View more alternatives
                                                    </a>
                                                </div>
                                            </div>
                                        </section>
                                        @endif

                                        @if(isset($business->faqs) && $business->faqs->count() > 0)
                                        @php
                                        $faq_title = \App\Models\StaticContentKey::where('key', 'faq_title')->first();
                                        $faq_description = \App\Models\StaticContentKey::where('key', 'faq_description')->first();
                                        $bName = $business->translations->first()->name ?? $business->name ?? 'this provider';
                                        $bizFaqFallbackH2 = $faq_title?->default_value ?: 'Frequently asked questions';
                                        $bizFaqFallbackSub = $faq_description?->default_value ?: ('Everything you need to know about ' . $bName . ' and its services.');
                                        $resolvedBizFaqs = \App\Services\ContentTemplateService::resolveBusinessSection(
                                            $business,
                                            'profile_faqs',
                                            getCurrentLanguageID()
                                        );
                                        $finalBizFaqH2 = !empty($resolvedBizFaqs['headline']) ? $resolvedBizFaqs['headline'] : $bizFaqFallbackH2;
                                        $finalBizFaqSubtext = !empty($resolvedBizFaqs['subtext']) ? $resolvedBizFaqs['subtext'] : $bizFaqFallbackSub;
                                        @endphp
                                        <!-- Business FAQs Section -->
                                        <section class="faq-section product_inr_faq py-5 light" id="section15">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="">
                                                        <div class="text-start mb-4">
                                                            <h2 style="font-size: 26px; font-weight: 700; color: #002347; margin-bottom: 8px;">
                                                                {!! \App\Services\ContentTemplateService::cleanHeading($finalBizFaqH2) !!}
                                                            </h2>
                                                            @if(!empty($finalBizFaqSubtext))
                                                                @if(str_starts_with(trim($finalBizFaqSubtext), '<'))
                                                                    {!! $finalBizFaqSubtext !!}
                                                                @else
                                                                    <p style="font-size: 15px; margin-bottom: 0;">
                                                                        {!! $finalBizFaqSubtext !!}
                                                                    </p>
                                                                @endif
                                                            @endif
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
                                                                    class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" role="region"
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
                                        @endif

                                        @if(isset($peerComparisons) && count($peerComparisons) > 0)
                                        @php
                                        $bName = $business->translations->first()->name ?? 'Business';
                                        $catTrans = $business->category->translation ?? null;
                                        $catName = $catTrans->name ?? 'providers';
                                        $compSlug = $catTrans->comparison_slug ?? 'compare';
                                        @endphp
                                        <section class="compare-section p_50 light" id="section-compare" style="background-color: #f7f9fb !important;">
                                            <div class="container">
                                                @php
                                                    $bizCompareFallbackH2 = 'Compare ' . $bName;
                                                    $bizCompareFallbackSub = 'See how ' . $bName . ' compares to other ' . $catName . ' providers.';
                                                    $resolvedBizCompare = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                        $business,
                                                        'profile_compare',
                                                        getCurrentLanguageID()
                                                    );
                                                    $finalBizCompareH2 = !empty($resolvedBizCompare['headline']) ? $resolvedBizCompare['headline'] : $bizCompareFallbackH2;
                                                    $finalBizCompareSubtext = !empty($resolvedBizCompare['subtext']) ? $resolvedBizCompare['subtext'] : $bizCompareFallbackSub;
                                                @endphp
                                                <div class="hd_text mb-4" data-aos="fade-up" data-aos-duration="1000">
                                                    <h2 style="font-size: 26px; font-weight: 700; color: #1e3050; margin-bottom: 8px;">
                                                        {!! \App\Services\ContentTemplateService::cleanHeading($finalBizCompareH2) !!}
                                                    </h2>
                                                    @if(!empty($finalBizCompareSubtext))
                                                    <div style="font-size: 15px; margin: 0; color: #475569;">
                                                        {!! $finalBizCompareSubtext !!}
                                                    </div>
                                                    @endif
                                                </div>

                                                <div class="row g-3" data-aos="fade-up" data-aos-duration="1000">
                                                    @forelse($peerComparisons as $peer)
                                                    @php
                                                    $peerName = $peer->translations->first()->name ?? 'Business';
                                                    $peerRating = $peer->display_rating;
                                                    $vsKey = static_text('vs_keyword');
                                                    if (empty($vsKey) || $vsKey === 'vs_keyword') {
                                                    $vsKey = 'vs';
                                                    }
                                                    $vsKey = Str::slug($vsKey);
                                                    $seoUrl = route('product-comparison.seo', [
                                                    'locale' => app()->getLocale(),
                                                    'comparison_slug' => $compSlug,
                                                    'comparison_businesses' => Str::slug($bName) . '-' . $vsKey . '-' . Str::slug($peerName)
                                                    ]);
                                                    @endphp
                                                   
<div class="col-lg-6 col-md-6 col-12">
    <a href="{{ $seoUrl }}" class="comparison-box-link text-decoration-none d-block h-100">
        <div class="comparison-box p-3 rounded-3 border h-100 d-flex flex-column justify-content-between"
            style="background-color: #fff !important; border-radius: 12px !important; border: 1px solid #e2e8f0 !important;">

            <div class="d-flex align-items-center justify-content-between gap-2">

                <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                    <div class="top-product-medium-logo">
                        <x-business-logo :business="$business" :name="$bName" />
                    </div>

                    <div style="min-width: 0;">
                        <div class="fw-semibold text-truncate"
                            style="font-size: 13px; color: #002347 !important;">
                            {{ $bName }}
                        </div>

                        @if($business->should_show_rating)
                        <div class="d-flex align-items-center gap-1"
                            style="font-size: 12px; color: #5b6b7a;">
                            <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                            <span>{{ formatRating($business->display_rating) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="px-2 vs_circle text-muted flex-shrink-0"
                    style="font-size: 16px; font-family: sans-serif;">
                    vs
                </div>

                <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                    <div class="top-product-medium-logo">
                        <x-business-logo :business="$peer" :name="$peerName" />
                    </div>

                    <div style="min-width: 0;">
                        <div class="fw-semibold text-truncate"
                            style="font-size: 13px; color: #1e293b !important;">
                            {{ $peerName }}
                        </div>

                        @if($peer->should_show_rating)
                        <div class="d-flex align-items-center gap-1"
                            style="font-size: 12px; color: #5b6b7a;">
                            <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                            <span>{{ formatRating($peer->display_rating) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </a>
</div>


                                                    @empty
                                                    <div class="col-12 text-muted p-0">{{ static_text('no_comparisons_available') ?: 'No comparisons available for this business yet.' }}</div>
                                                    @endforelse
                                                </div>

                                                @php
                                                $cCompSlug = !empty($languageObj->comparisons_slug) ? $languageObj->comparisons_slug : 'comparisons';
                                                $bSlugStr = $business->translations->first()->slug ?? $business->slug;
                                                @endphp
                                                <div class="mt-4" data-aos="fade-up" data-aos-duration="1000">
                                                    <a href="{{ url(app()->getLocale() . '/' . $bSlugStr . '/' . $cCompSlug) }}" class="view-more-link" style="font-size: 14px; font-weight: 600; color: #002347; text-decoration: none;">
                                                        View more comparisons
                                                    </a>
                                                </div>
                                            </div>
                                        </section>
                                        @endif

                                        <section class="about_asn_2 light pt-0 p_50 d-none">

                                            <div class="about_asn_content">
                                                <div class="hd_content asan-text-para">
                                                    {!! $product->translations->overview ?? "" !!}
                                                </div>

                                            </div>

                                        </section>

                                        <section class="crm_sec revie_left_rgt_sec" id="section14" style="overflow: visible !important; background-color:#fdfdfd;">
                                            <div class="container">
                                                @push('styles')
<style>
                                                    .review-sidebar-sticky {
                                                        position: sticky !important;
                                                        position: -webkit-sticky !important;
                                                        top: 100px !important;
                                                        height: fit-content !important;
                                                        z-index: 10;
                                                    }

                                                    .review-star-box {
                                                        border: none !important;
                                                        background: none !important;
                                                        box-shadow: none !important;
                                                        padding: 0 !important;
                                                    }

                                                    .rating-filter-header h4 {
                                                        font-size: 16px;
                                                        font-weight: 600;
                                                        color: #777;
                                                        margin-bottom: 5px;
                                                    }

                                                    .overall-stars {
                                                        display: flex;
                                                        align-items: center;
                                                        gap: 8px;
                                                        margin-bottom: 5px;
                                                    }

                                                    .overall-stars i {
                                                        font-size: 18px;
                                                    }

                                                    .filter-by-title-row {
                                                        display: flex;
                                                        justify-content: space-between;
                                                        align-items: center;
                                                        margin-top: 20px;
                                                        margin-bottom: 15px;
                                                        font-weight: 600;
                                                        font-size: 15px;
                                                        color: #333;
                                                        border-bottom: 1px solid #eee;
                                                        padding-bottom: 8px;
                                                    }

                                                    .review_sec .review_detl {
                                                        border: 1px solid rgba(0, 0, 0, 0.08) !important;
                                                        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important;
                                                        border-radius: 16px !important;

                                                    }

                                                    .review_sec .review_detl:hover {
                                                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07) !important;
                                                        border-color: rgba(0, 0, 0, 0.12) !important;
                                                    }

                                                    @if(!$hasUserRatings)
                                                    .review_detl.populr-alternative:first-child {
                                                        margin-top: 24px;
                                                    }
                                                    @endif
                                                </style>
@endpush
                                                <div class="crm_review_box review_sec" id="all-reviews">

                                                    <div class="review-prompt-banner" id="reviewPromptBanner" style="background-color: #f7fafc;  padding: 20px 24px; margin-bottom: 40px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #e2e8f0; flex-wrap: wrap; gap: 20px;">
                                                        <div style="display: flex; align-items: center; gap: 16px;">
                                                            <div class="top-product-logo" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                                                <x-business-logo :business="$business" />
                                                            </div>
                                                            <div>
                                                                <p style="font-size: 18px !important; margin: 0 0 4px 0; font-weight: 700 !important; ">Would you recommend {{ $business->translations->first()->name ?? 'this product' }} to others?</p>
                                                                <p style="margin: 0; font-size: 13.5px; ">{{ static_text('share_experience_subheading') ?: 'Share your experience with the community' }}</p>
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

                                                    <div class="row review-row-prod-inr">

                                                        <div class="col-lg-12">
                                                            <div class="review-sidebar-sticky">

                                                                @php
                                                                    if ($hasUserRatings) {
                                                                        $countStr = number_format($ratingCount);
                                                                        $bizReviewsFallback = ($business->translations->first()->name ?? $business->name ?? '') . ' user reviews (' . $countStr . ')';
                                                                        $resolvedBizReviews = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                                            $business,
                                                                            'profile_reviews',
                                                                            getCurrentLanguageID(),
                                                                            ['[count]' => $countStr]
                                                                        );
                                                                        $finalBizReviewsHeadline = !empty($resolvedBizReviews['headline']) ? $resolvedBizReviews['headline'] : $bizReviewsFallback;
                                                                        if (!str_contains($finalBizReviewsHeadline, '(') && !str_contains($finalBizReviewsHeadline, $countStr)) {
                                                                            $finalBizReviewsHeadline .= ' (' . $countStr . ')';
                                                                        }
                                                                    } else {
                                                                        $bizReviewsFallback = ($business->translations->first()->name ?? $business->name ?? '') . ' user reviews';
                                                                        $resolvedBizReviews = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                                            $business,
                                                                            'profile_reviews',
                                                                            getCurrentLanguageID(),
                                                                            ['([count])' => '', '([count] reviews)' => '', '([count] review)' => '', '[count]' => '']
                                                                        );
                                                                        $finalBizReviewsHeadline = !empty($resolvedBizReviews['headline']) ? $resolvedBizReviews['headline'] : $bizReviewsFallback;
                                                                        $finalBizReviewsHeadline = preg_replace('/\s*\(\s*\d*\s*\)/', '', $finalBizReviewsHeadline);
                                                                        $finalBizReviewsHeadline = preg_replace('/\s*\[count\]/i', '', $finalBizReviewsHeadline);
                                                                        $finalBizReviewsHeadline = trim($finalBizReviewsHeadline);
                                                                    }
                                                                    $finalBizReviewsSubtext = !empty($resolvedBizReviews['subtext']) ? $resolvedBizReviews['subtext'] : null;
                                                                @endphp

                                                                <h2 style="font-size: 18px; font-weight: 700; margin-bottom: {{ !empty($finalBizReviewsSubtext) ? '6px' : '16px' }}; color: #1e3050; line-height: 1.3;">
                                                                    {!! \App\Services\ContentTemplateService::cleanHeading($finalBizReviewsHeadline) !!}
                                                                </h2>
                                                                @if(!empty($finalBizReviewsSubtext))
                                                                    <div class="reviews-section-subtext mb-3" style="color: #475569; font-size: 14.5px;">
                                                                        {!! $finalBizReviewsSubtext !!}
                                                                    </div>
                                                                @endif
                                                                <div class="review-filter-sec" style="display: block;">
                                                                    <div class="user-reviews-summary-card p-4 p-md-4 bg-white rounded-3 border w-100" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
                                                                        <div class="row align-items-start g-4">
                                                                            <!-- Left Column: Big Rating & Stars -->
                                                                            <div class="col-lg-5 col-md-5 col-12 mt-0">
                                                                                <div class="d-flex flex-column align-items-start">
                                                                                    @if ($averageRating !== null && (float)$averageRating > 0)
                                                                                        <span class="overall-rating-number" style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1; ">{{ formatRating($averageRating) }}</span>
                                                                                        @if($ratingWord = get_rating_label($averageRating))
                                                                                        <span class="rating-word-label " style="">
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
                                                                                <p class="breakdown-title" style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 14px;">Review breakdown </p>

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

                                                        @php
                                                        $reviewSortOptions = [
                                                            'recent' => (static_text('sort_most_recent') ?: 'Most recent'),
                                                            'high-to-low' => (static_text('sort_highest_rated') ?: 'Highest rated'),
                                                            'low-to-high' => (static_text('sort_lowest_rated') ?: 'Lowest rated'),
                                                        ];
                                                        $currentSort = request('sort', 'recent');
                                                        if (!array_key_exists($currentSort, $reviewSortOptions)) {
                                                            $currentSort = ($currentSort === 'best') ? 'high-to-low' : 'recent';
                                                        }
                                                        $currentSortLabel = $reviewSortOptions[$currentSort] ?? 'Most recent';
                                                        @endphp
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
                                                                            style="border-color: #ced4da; color: #002347; font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 6px; background: #fdfdfd; gap: 4px;">
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
                                                                                    $count = $ratingCounts[$i] ?? 0;
                                                                                    $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                                                                    @endphp
                                                                                    <li class="progress-list-item" style="display: flex; align-items: center; margin-bottom: 10px; gap: 8px;">
                                                                                        <input type="checkbox" class="rating-filter-checkbox" value="{{ $i }}" id="star-check-{{ $i }}" style="cursor: pointer; width: 16px; height: 16px; margin: 0;">
                                                                                        <label for="star-check-{{ $i }}" style="display: flex; align-items: center; width: 100%; cursor: pointer; margin: 0;">
                                                                                            <span style="display: inline-flex; align-items: center; width: 45px; font-size: 13px; color: #5b6b7a; flex-shrink: 0;">
                                                                                                <i class="far fa-star text-warning" style="margin-right: 4px;"></i> {{ $i }}
                                                                                            </span>
                                                                                            <div class="progress-box" style="flex-grow: 1; height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden; margin-left: 4px; margin-right: 10px;">
                                                                                                <div class="progress-fill" style="width: {{ $percent }}%; height: 100%;"></div>
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

                                                                <!-- Sort Dropdown on Right (Top Rated Style) -->
                                                                <div class="dropdown" id="reviewSortDropdownWrap">
                                                                    <button type="button"
                                                                        class="sorting d-inline-flex align-items-center gap-2"
                                                                        id="reviewSortDropdownBtn"
                                                                        data-bs-toggle="dropdown"
                                                                        aria-expanded="false"
                                                                        style="background-color: #fdfdfd; color: #002347; padding: 7px 16px; border: 1px solid #cbd5e1; outline: none; cursor: pointer; font-size: 13px; font-weight: 400;">
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
                                                                @include('User.product.partials.reviews_list')
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const checkboxes = document.querySelectorAll('.rating-filter-checkbox');
                                                        const clearBtns = document.querySelectorAll('.clear-filters-btn');
                                                        const filterBadge = document.getElementById('filter-count-badge');
                                                        const filterBtn = document.getElementById('reviewFilterDropdownBtn');
                                                        const container = document.getElementById('reviews-list-container');
                                                        const sortOptions = document.querySelectorAll('.review-sort-option');
                                                        const sortLabelSpan = document.getElementById('current-sort-label');

                                                        let currentSortValue = '{{ $currentSort ?? "recent" }}';

                                                        sortOptions.forEach(opt => {
                                                            opt.addEventListener('click', function(e) {
                                                                e.preventDefault();
                                                                const val = this.getAttribute('data-sort-value');
                                                                const label = this.getAttribute('data-sort-label');
                                                                currentSortValue = val;
                                                                if (sortLabelSpan) {
                                                                    sortLabelSpan.textContent = label;
                                                                }
                                                                sortOptions.forEach(o => {
                                                                    const isMatch = (o.getAttribute('data-sort-value') === val);
                                                                    if (isMatch) {
                                                                        o.classList.add('is-selected');
                                                                    } else {
                                                                        o.classList.remove('is-selected');
                                                                    }
                                                                    const icon = o.querySelector('.sort-check-icon');
                                                                    if (icon) {
                                                                        if (isMatch) icon.classList.remove('d-none');
                                                                        else icon.classList.add('d-none');
                                                                    }
                                                                });
                                                                fetchReviews();
                                                            });
                                                        });

                                                        checkboxes.forEach(cb => {
                                                            cb.addEventListener('change', function() {
                                                                updateClearButtonVisibility();
                                                                fetchReviews();
                                                            });
                                                        });

                                                        clearBtns.forEach(btn => {
                                                            btn.addEventListener('click', function() {
                                                                checkboxes.forEach(cb => cb.checked = false);
                                                                updateClearButtonVisibility();
                                                                fetchReviews();
                                                            });
                                                        });

                                                        function updateClearButtonVisibility() {
                                                            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                                                            clearBtns.forEach(btn => {
                                                                btn.style.display = checkedCount > 0 ? 'inline' : 'none';
                                                            });
                                                            if (filterBadge) {
                                                                if (checkedCount > 0) {
                                                                    filterBadge.textContent = checkedCount;
                                                                    filterBadge.classList.remove('d-none');
                                                                } else {
                                                                    filterBadge.classList.add('d-none');
                                                                }
                                                            }
                                                            if (filterBtn) {
                                                                if (checkedCount > 0) {
                                                                    filterBtn.style.borderColor = '#06498b';
                                                                    filterBtn.style.color = '#06498b';
                                                                } else {
                                                                    filterBtn.style.borderColor = '#ced4da';
                                                                    filterBtn.style.color = '#1e3050';
                                                                }
                                                            }
                                                        }

                                                        // Check pre-selected stars from URL query params
                                                        const urlParams = new URLSearchParams(window.location.search);
                                                        const starsParam = urlParams.get('stars');
                                                        if (starsParam) {
                                                            const starsArr = starsParam.split(',');
                                                            checkboxes.forEach(cb => {
                                                                if (starsArr.includes(cb.value)) {
                                                                    cb.checked = true;
                                                                }
                                                            });
                                                            updateClearButtonVisibility();
                                                        }

                                                        function fetchReviews() {
                                                            const selectedStars = Array.from(checkboxes)
                                                                .filter(cb => cb.checked)
                                                                .map(cb => cb.value);

                                                            const sortValue = currentSortValue || 'recent';

                                                            if (container) {
                                                                container.style.opacity = '0.5';
                                                            }

                                                            const url = new URL(window.location.href);
                                                            url.searchParams.set('sort', sortValue);
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
                                                                    if (typeof AOS !== 'undefined') {
                                                                        AOS.refresh();
                                                                    }
                                                                })
                                                                .catch(err => {
                                                                    console.error('Error fetching reviews:', err);
                                                                    if (container) {
                                                                        container.style.opacity = '1';
                                                                    }
                                                                });
                                                        }
                                                    });
                                                </script>

                                            </div>
                                        </section>


                                        <!-- this is end section -->
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div id="sectionDiscussions" class="mt-5 pt-4">
                            @php
                                $totalDiscsCount = $business->discussions()->count();
                                $resolvedBizDiscussions = \App\Services\ContentTemplateService::resolveBusinessSection(
                                    $business,
                                    'profile_discussions',
                                    getCurrentLanguageID(),
                                    ['[count]' => number_format($totalDiscsCount)]
                                );
                                $finalBizDiscHeadline = null;
                                $finalBizDiscSubtext = null;
                                if (!empty($resolvedBizDiscussions['headline'])) {
                                    $finalBizDiscHeadline = $resolvedBizDiscussions['headline'];
                                    if (!str_contains($finalBizDiscHeadline, '(') && !str_contains($finalBizDiscHeadline, (string)$totalDiscsCount)) {
                                        $finalBizDiscHeadline .= ' (' . number_format($totalDiscsCount) . ')';
                                    }
                                }
                                if (!empty($resolvedBizDiscussions['subtext'])) {
                                    $finalBizDiscSubtext = $resolvedBizDiscussions['subtext'];
                                }
                            @endphp
                            <x-community-discussion :business="$business" :headline="$finalBizDiscHeadline" :subtext="$finalBizDiscSubtext" />
                        </div>
                        <div class="mt-5">
                            <section class="subs_sec light p_50 d-none" id="section16">
                                <x-news-letter-subscription />
                            </section>
                        </div>
                    </div>
                    <!-- all new part section end here  -->
                </div>

                <div class="thre_revi_rgt">

                    @php
                    $productBadgeLabel = static_text('product_badge_label') ?? 'Key Features';
                    // dd($productBadgeLabel);
                    @endphp

                    <div class="main_feture">
                        <div class="fetru_row d-flex justify-content-between">

                            @if ($business->is_affiliate && $business->usps->count() > 0)

                            <div class="main_feature_lg">
                                <div class="feture_box lft_check_box size15">
                                    <ul class="list-unstyled">
                                        @foreach ($business->usps->take(5) as $usp)
                                        <li class="d-flex flex-row align-items-center size15">
                                            <div class="grn_chk">
                                                <img src="{{ asset('front/img/green-tick.svg') }}" alt="Verified">
                                            </div>
                                            <p class="m-0">{{ $usp->text }}</p>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @elseif ($business->is_affiliate)

                            <div class="main_feature_lg">
                                <div class="feture_box lft_check_box size15">
                                    <ul class="list-unstyled">
                                        <li class="d-flex flex-row size15">
                                            <div class="grn_chk">
                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                            </div>
                                            <p class="m-0">Free domain & SSL certificate</p>
                                        </li>
                                        <li class="d-flex flex-row size15">
                                            <div class="grn_chk">
                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                            </div>
                                            <p class="m-0">Customizable automatic updates</p>
                                        </li>
                                        <li class="d-flex flex-row size15">
                                            <div class="grn_chk">
                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                            </div>
                                            <p class="m-0">Scalable performance management</p>
                                        </li>
                                        <li class="d-flex flex-row size15">
                                            <div class="grn_chk">
                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                            </div>
                                            <p class="m-0">DDoS & malware protection</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @endif

                            <div class="main_feature_lg not-on-mobile d-none d-md-block">
                                <div class="feture_box review-breakdown-card">

                                    @if ($averageRating !== null)
                                    <div class="review-header-box top_review_bx" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom:15px; border-bottom: 1px solid #f0f0f0;">
                                        <div class="overall-rating-box" style="display: flex; flex-direction: column; align-items: flex-start;">
                                            <span class="overall-rating-number" style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1;">
                                                {{ formatRating($averageRating) }}
                                            </span>
                                            @if($ratingWord = get_rating_label($averageRating))
                                            <span class="rating-word-label">
                                                {{ $ratingWord }}
                                            </span>
                                            @endif

                                            <div class="d-flex align-items-center gap-2 mb-1 bottom-rating-trigger" onclick="toggleBottomReviewBreakdown()" style="user-select: none;">
                                                <div class="d-flex align-items-center gap-1">
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
                                                <span class="bottom-rating-arrow-wrap d-inline-flex align-items-center justify-content-center d-md-none" style="width: 22px; height: 22px; border-radius: 50%; background-color: #f1f5f9; transition: background-color 0.2s ease;">
                                                    <i class="fas fa-chevron-down bottom-rating-chevron" style="font-size: 11px; color: #64748b; transition: transform 0.3s ease;"></i>
                                                </span>
                                            </div>

                                            @if ($hasUserRatings)
                                            <span class="com-rating">{{ number_format($totalReviews) }} {{ $totalReviews == 1 ? 'review' : 'reviews' }}</span>
                                            @endif
                                        </div>

                                        @if ($hasUserRatings)
                                        <a href="#section14" class="view-review-link" style="">
                                           {{ static_text('go_to_reviews_link') ?: 'Go to reviews' }}
                                        </a>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Collapsible Breakdown Section (Mobile: hidden by default, toggles; Desktop: always shown) -->
                                    <div id="bottomReviewBreakdownContent" class="bottom-breakdown-collapsible" data-expanded="false">
                                        @if ($hasUserRatings)
                                        <p class="breakdown-title" style="margin-bottom: 15px;">
                                            {{ static_text('review_breakdown_heading') ?: 'Review breakdown' }}
                                        </p>

                                        <div class="review-progress-list ">
                                            @foreach ($criteria as $criterion)
                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                <p class="m-0" style="font-size: 12px; font-weight: 500;">{{ $criterion->name }}</p>
                                                <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                                    <progress class="progress-bar w-100"
                                                        value="{{ $criterion->average_rating * 20 }}"
                                                        max="100" style="height: 8px;"></progress>
                                                    <span style="font-size: 12px; font-weight: 600;  min-width: 35px; text-align: right;">{{ formatRating($criterion->average_rating) }}</span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        <div class="recommendation-rate mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 600; color: #002347; font-size: 14px;">{{ static_text('recommended_by_users') ?: 'Recommended by users' }}</span>
                                            <strong style="color: #002347; font-size: 14px; font-weight:600;">{{ $recommendPercent }}%</strong>
                                        </div>
                                        @endif

                                        <div class="do-you-recommend" style=" display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 600; color: #002347; font-size: 14px;">{{ static_text('do_you_recommend_question') ?: 'Do you recommend' }} {{ $business->translations->first()->name ?? 'this business' }}?</span>
                                            <div style="display: flex; gap: 8px;">
                                                @auth
                                                <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; " onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff ';">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </a>
                                                <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; " onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff ';">
                                                    <i class="fas fa-thumbs-down"></i>
                                                </a>
                                                @else
                                                <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; " onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff ';">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </a>
                                                <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color:  #007bff ; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; " onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor=' #007bff ';">
                                                    <i class="fas fa-thumbs-down"></i>
                                                </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

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
@if($business->is_affiliate && ($hasStartingPrice || $hasFreeTrial))
    <div class="innr_price_trail">

        @if($hasStartingPrice)
            <div class="main_feature_sm">
                <div class="feture_box str_prc_box">

                    <p class="starting-price-title">
                        {{ static_text('starting_price_label') ?: 'Starting price' }}
                    </p>

                    <p class="starting-price-value">
                        {{ $currency }}{{ $startingPrice }}
                    </p>

    @if (
    !empty($additional_info) &&
    !in_array(
        strtoupper(trim((string) $additional_info)),
        ['NA', 'N/A', 'NULL']
    )
)
    <p class="starting-price-text">
        {{ $additional_info }}
    </p>
@elseif(!empty($timeUnit) && !in_array(strtolower($timeUnit), ['one_time', 'na', 'n/a', 'null']))
    <p class="starting-price-text">
        {{ static_text('pricing_per_prefix') ?: 'per' }} {{ strtolower($timeUnit) }}
    </p>
@endif

                    <a href="{{ $business->getTrackedUrl() }}"
                        data-track="{{ json_encode([
                            'type' => 'click',
                            'business_id' => $business->id,
                            'action' => 'view_pricing',
                            'label' => 'View pricing',
                        ]) }}"
                        target="_blank"
                        rel="sponsored"
                        class="starting-price-link">
                        {{ static_text('view_pricing_link') ?: 'View pricing' }}
                    </a>

                </div>
            </div>
        @endif

    </div>


                                @if($hasFreeTrial)
                                <div class="main_feature_sm">
                                    <div class="fre_trail feture_box size22">
                                        <div class="grn_check_big">
                                            <img src="{{ asset('front/img/new-grn-chk.svg') }}">
                                        </div>
                                        <h6 class="blue-text big-bld">{{ static_text('free_trial_available_heading') ?: 'Free trial available' }}</h6>
                                        <div class="accor-btn">
                                            <a class="cta cta_white blue-btn"
                                                data-track="{{ json_encode([
                                                                'type' => 'click',
                                                                'business_id' => $business->id,
                                                                'action' => 'claim_now',
                                                                'label' => 'Claim Now',
                                                            ]) }}"
                                                type="button" style="text-transform:none !important;">{{ static_text('claim_now_button') ?: 'Claim now' }}</a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            <!-- </div> -->
                            @endif
                            @php
                            $pros = collect();
                            $cons = collect();

                            $reviews = $business->reviews
                            ->where('status', 'active')
                            ->shuffle(); // Random reviews

                            foreach ($reviews as $review) {

                            $translation = $review->translations
                            ->where('language_id', getCurrentLanguageID())
                            ->first() ?? $review->translations->first();

                            if (!$translation) {
                            continue;
                            }

                            // ---------- Pros ----------
                            if (!empty($translation->pros)) {

                            $text = trim(strip_tags($translation->pros));

                            if (str_word_count($text) <= 6) {
                                $pros->push($text);
                                }
                                }

                                // ---------- Cons ----------
                                if (!empty($translation->cons)) {

                                $text = trim(strip_tags($translation->cons));

                                if (str_word_count($text) <= 6) {
                                    $cons->push($text);
                                    }
                                    }

                                    if ($pros->count() >= 2 && $cons->count() >= 2) {
                                    break;
                                    }
                                    }

                                    $pros = $pros->unique()->take(2);
                                    $cons = $cons->unique()->take(2);

                                    // Default values
                                    $defaultPros = ['Service staff is good', 'Easy to use and navigate'];
                                    foreach ($defaultPros as $defaultPro) {
                                    if ($pros->count() >= 2) {
                                    break;
                                    }
                                    if (!$pros->contains($defaultPro)) {
                                    $pros->push($defaultPro);
                                    }
                                    }

                                    $defaultCons = ['Service not as well as expected', 'Pricing could be more flexible'];
                                    foreach ($defaultCons as $defaultCon) {
                                    if ($cons->count() >= 2) {
                                    break;
                                    }
                                    if (!$cons->contains($defaultCon)) {
                                    $cons->push($defaultCon);
                                    }
                                    }
                                    @endphp
                                    <div class="main_feature_lg">
                                        <div class="feture_box review-breakdown-box not-on-mobile">

                                            <div class="review-header-box pb-3" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 15px;">
                                                <h2 class="size22 big-bld m-0 underline-hover">{{ static_text('highlighted_reviews_heading') ?: 'Highlighted reviews' }} </h2>
                                                <a href="#section14" class="view-review-link">
                                                    {{ static_text('go_to_reviews_link') ?: 'Go to reviews' }}
                                                </a>
                                            </div>

                                            @foreach($topReviews->take(2) as $review)
                                            <div class="sidebar-review-card" style="margin-bottom: 20px;">

                                                <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">

                                                    <div class="review-user" style="display: flex; align-items: center; gap: 12px;">

                                                        @if($review->user && $review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                                                        <img src="{{ asset($review->user->profile_image) }}"
                                                            alt="{{ $review->user->displayName() }}"
                                                            class="rounded-circle"
                                                            width="45"
                                                            height="45">
                                                        @else
                                                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                            <span style="color: white; font-weight: bold; font-size: 20px;">{{ strtoupper(substr($review->user->first_name ?? 'A', 0, 1)) }}</span>
                                                        </div>
                                                        @endif

                                                        <div>
                                                            <p style="line-height:1">
                                                                @if($review->user)
                                                                <a href="{{ $review->user->profileUrl() }}" style="color: inherit;">
                                                                    {{ $review->user->displayName() }}
                                                                </a>
                                                                @else
                                                                Anonymous
                                                                @endif
                                                            </p>
                                                            @php
                                                            $badge = isset($review) && $review->user ? $review->user->getContributorBadge() : (isset($u) ? $u->getContributorBadge() : null);
                                                            @endphp
                                                            @if($badge)
                                                            <div class="user-contributor-badge" style="font-size: 13px; color: #64748b; margin-top: 2px;">
                                                                {!! $badge->icon !!} {{ $badge->name }}
                                                            </div>
                                                            @endif

                                                            @if($review->user && $review->user->job_title)
                                                            <div style="font-size: 12px; color: #777; margin-top: 2px; line-height: 1.2;">{{ $review->user->job_title }}</div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div style="text-align: right; flex-shrink: 0;">
                                                        <small class="text-muted" style="font-size: 11px; white-space: nowrap;">{{ $review->created_at->diffForHumans() }}</small>
                                                    </div>

                                                </div>

                                                <p class="sidebar-review-card-p">
                                                    {{ $review->translations->first()->title ?? 'Review' }}
                                                </p>

                                                @if($hasUserRatings)
                                                <div class="rating-stars-wrapper" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                                    <div class="rating-stars">
                                                        @for($i=1;$i<=5;$i++)
                                                            @if($i<=floor($review->rating))
                                                            <i class="fas fa-star text-warning" style="font-size: 12px !important;"></i>
                                                            @elseif($i-0.5<=$review->rating)
                                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 12px !important;"></i>
                                                                @else
                                                                <i class="far fa-star text-warning" style="font-size: 12px !important;"></i>
                                                                @endif
                                                                @endfor
                                                    </div>
                                                </div>
                                                @endif

                                                <p>
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($review->translations->first()->description ?? ''),90) }}
                                                </p>

                                            </div>
                                            @endforeach

                                        </div>
                                    </div>
                                    @php
                                        $sidebarDiscussions = $business->discussions()->with(['user'])->latest()->take(2)->get();
                                    @endphp
                                    @if($sidebarDiscussions->isNotEmpty())
                                    <div class="main_feature_lg">
                                        <div class="feture_box review-breakdown-box not-on-mobile">
                                            <div class="review-header-box pb-3" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                                                <h2 class="size22 big-bld m-0 underline-hover">{{ static_text('recent_discussions_heading') ?: 'Recent discussions' }}</h2>
                                                <a href="#sectionDiscussions" class="view-review-link">
                                                     {{ static_text('go_to_discussions_link') ?: 'Go to discussions' }}
                                                </a>
                                            </div>

                                            @foreach($sidebarDiscussions as $pDisc)
                                            <div class="sidebar-review-card" style="margin-bottom: {{ $loop->last ? '0' : '20px' }};">
                                                <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                                    <div class="review-user" style="display: flex; align-items: center; gap: 12px;">
                                                        @if($pDisc->user && $pDisc->user->profile_image && $pDisc->user->profile_image !== 'front/img/default.png')
                                                        <img src="{{ asset($pDisc->user->profile_image) }}"
                                                            alt="{{ $pDisc->author_display_name }}"
                                                            class="rounded-circle"
                                                            width="45"
                                                            height="45"
                                                            style="object-fit: cover;">
                                                        @elseif($pDisc->author_avatar)
                                                        <img src="{{ asset($pDisc->author_avatar) }}"
                                                            alt="{{ $pDisc->author_display_name }}"
                                                            class="rounded-circle"
                                                            width="45"
                                                            height="45"
                                                            style="object-fit: cover;">
                                                        @else
                                                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                            <span style="color: white; font-weight: bold; font-size: 20px;">{{ $pDisc->author_initial ?: 'A' }}</span>
                                                        </div>
                                                        @endif
                                                        <div>
                                                            <p style="line-height:1">
                                                                @if($pDisc->user)
                                                                <a href="{{ $pDisc->user->profileUrl() }}" style="color: inherit; text-decoration: none;">
                                                                    {{ $pDisc->author_display_name }}
                                                                </a>
                                                                @else
                                                                {{ $pDisc->author_display_name }}
                                                                @endif
                                                            </p>
                                                            <div style="font-size: 12px; color: #5b6b7a; line-height:1;">
                                                                @php
                                                                    $metaParts = [];
                                                                    if ($pDisc->user && $pDisc->user->job_title) {
                                                                        $metaParts[] = $pDisc->user->job_title;
                                                                    } elseif ($pDisc->author_title) {
                                                                        $metaParts[] = $pDisc->author_title;
                                                                    }
                                                                    if ($pDisc->user && $pDisc->user->company_size) {
                                                                        $cSizeMap = [
                                                                            '2' => 'Small Business (1-50 emp.)',
                                                                            '3' => 'Mid-Market (51-1000 emp.)',
                                                                            '4' => 'Enterprise (>1000 emp.)',
                                                                        ];
                                                                        $cSize = $pDisc->user->company_size;
                                                                        $metaParts[] = $cSizeMap[$cSize] ?? (function_exists('static_text') ? (static_text('company_size_' . $cSize) ?: $cSize) : $cSize);
                                                                    }
                                                                @endphp
                                                                {{ !empty($metaParts) ? implode(' • ', $metaParts) : ($pDisc->author_title ?: '') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;">
                                                        <small class="text-muted" style="font-size: 11px; white-space: nowrap;">{{ $pDisc->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>

                                                <p class="sidebar-review-card-p" style="cursor: pointer;" onclick="document.getElementById('sectionDiscussions')?.scrollIntoView({behavior: 'smooth'})">
                                                    {{ $pDisc->title }}
                                                </p>
                                                <p>
                                                    {{ Str::limit(strip_tags($pDisc->content), 120) }}
                                                </p>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif


                                    <!-- here we start sticky part -->
                                    <div class="main_feature_lg sticky-sidebar-nav-wrapper d-none d-lg-block">
                                        <div class="feture_box review-breakdown-box sticky-sidebar-nav-card" id="stickySidebarNav">
                                            <div class="sticky-nav-header text-center">
                                                <div class="sticky-nav-logo mb-2 d-flex justify-content-center align-items-center" style="gap: 10px;">
                                                    <div class="top-product-logo">
                                                        <x-business-logo :business="$business" />
                                                    </div>

                                                    <div class="sticky-wrap-mf">
                                                        <p class="sticky-nav-title mb-1"
                                                            style="font-size: 17px; font-weight: 700; color: #002347;">
                                                            {{ $business->translations->first()->name ?? '' }}
                                                        </p>
                                                        @if ($averageRating !== null)
                                                        <div
                                                            class="rating-group sticky-nav-rating">
                                                            <span
                                                                style="">{{ formatRating($averageRating) }}</span>
                                                            <div class="rating-star" style="">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <=floor($averageRating))
                                                                    <i class="fas fa-star"></i>
                                                                    @elseif ($i - 0.5 <= $averageRating)
                                                                        <i class="fas fa-star-half-alt"></i>
                                                                        @else
                                                                        <i class="far fa-star"></i>
                                                                        @endif
                                                                        @endfor
                                                            </div>
                                                            @if ($hasUserRatings)
                                                            <span class=""
                                                                style="">({{ $ratingCount }})</span>
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
                                                    {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt ms-2"
                                                        style="font-size: 11px;"></i>
                                                </a>
                                                @endif
                                            </div>


                                            <div class="sticky-nav-links">
                                                <a href="#top" class="sticky-nav-item active" data-target="top">
                                                    <span>Description</span>
                                                </a>
                                                @if (isset($alternativeBusiness) && $alternativeBusiness->count() > 0)
                                                <a href="#section9" class="sticky-nav-item" data-target="section9">
                                                    <span>Alternatives</span>
                                                </a>
                                                @endif
                                                @if (isset($business->faqs) && $business->faqs->count() > 0)
                                                <a href="#section15" class="sticky-nav-item" data-target="section15">
                                                    <span>FAQs</span>
                                                </a>
                                                @endif
                                                @if (isset($peerComparisons) && count($peerComparisons) > 0)
                                                <a href="#section-compare" class="sticky-nav-item" data-target="section-compare">
                                                    <span>Compare</span>
                                                </a>
                                                @endif
                                                <a href="#section14" class="sticky-nav-item" data-target="section14">
                                                    <span>Reviews</span>
                                                </a>
                                                <a href="#sectionDiscussions" class="sticky-nav-item" data-target="sectionDiscussions">
                                                    <span>Discussions</span>
                                                </a>
                                            </div>


                                            <div class="sticky-nav-footer pt-3 mt-2 border-top text-center">
                                                @auth
                                                <button type="button"
                                                    onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                                    class="btn-write-review-link"
                                                    style="background: transparent; border: none; color: #002347; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                                    <span style="text-decoration: none;"><i class="fas fa-pencil-alt "></i> </span>Write a review
                                                </button>
                                                @else
                                                <a href="javascript:void(0)"
                                                    onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                                    class="btn-write-review-link"
                                                    style="background: transparent; border: none; color: #003f7d; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                                    <span style="text-decoration: none;"><i class="fas fa-pencil-alt "></i> </span>Write a review
                                                </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                    <!-- here we end sticky part -->
                                    <!-- MOBILE STICKY BOTTOM NAV -->
                                    <div class="mobile-sticky-bottom-nav d-lg-none d-flex align-items-center p-2" style="gap: 10px;">
                                        <!-- Left side: Dropdown for current section -->
                                        <div class="msbn-left dropup" style="flex: 1; min-width: 0;">
                                            <button class="btn btn-light dropdown-toggle msbn-dropdown-toggle d-flex align-items-center justify-content-between" type="button" id="msbnCurrentSection" data-bs-toggle="dropdown" aria-expanded="false" style="width: 100%; text-align: left; background: #fdfdfd; color: #1e3050; font-weight: 600; font-size: 14px; border-radius: 8px;">
                                                <span id="msbn-active-text" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Description</span>
                                            </button>
                                            <ul class="dropdown-menu msbn-dropdown-menu w-100" aria-labelledby="msbnCurrentSection" style="border-radius: 8px; box-shadow: 0 -4px 12px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; margin-bottom: 10px; min-width: 100%;">
                                                <li><a class="dropdown-item msbn-item active" href="#top" data-target="section1">{{ static_text('tab_description') ?: 'Description' }}</a></li>
                                                @if (isset($alternativeBusiness) && $alternativeBusiness->count() > 0)
                                                <li><a class="dropdown-item msbn-item" href="#section9" data-target="section9">{{ static_text('tab_alternatives') ?: 'Alternatives' }}</a></li>
                                                @endif
                                                @if (isset($business->faqs) && $business->faqs->count() > 0)
                                                <li><a class="dropdown-item msbn-item" href="#section15" data-target="section15">{{ static_text('tab_faqs') ?: 'FAQs' }}</a></li>
                                                @endif
                                                @if (isset($peerComparisons) && count($peerComparisons) > 0)
                                                <li><a class="dropdown-item msbn-item" href="#section-compare" data-target="section-compare">{{ static_text('tab_compare') ?: 'Compare' }}</a></li>
                                                @endif
                                                <li><a class="dropdown-item msbn-item" href="#section14" data-target="section14">{{ static_text('tab_reviews') ?: 'Reviews' }}</a></li>
                                                <li><a class="dropdown-item msbn-item" href="#sectionDiscussions" data-target="sectionDiscussions">{{ static_text('tab_discussions') ?: 'Community' }}</a></li>
                                            </ul>
                                        </div>

                                        <!-- Right side: Visit CTA -->
                                        <div class="msbn-right" style="flex: 1; min-width: 0;">
                                            @if ($business->is_affiliate)
                                            <a href="{{ $business->getTrackedUrl() }}"
                                                data-track="{{ json_encode([
                    'type' => 'click',
                    'business_id' => $business->id,
                    'action' => 'visit_website',
                    'label' => 'Visit Website',
                ]) }}"
                                                target="_blank" rel="sponsored" class="btn btn-orng cta cta_orange w-100 shadow-sm" style="border-radius: 8px; font-weight: 600; font-size: 14px; padding: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none;     color: #fdfdfd;">
                                                {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt ms-2" style="font-size: 12px;"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </div>

                                    @push('styles')
<style>
                                        .mobile-sticky-bottom-nav {
                                            position: fixed;
                                            bottom: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 7svh;
                                            background-color: #ffffff;
                                            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
                                            z-index: 91;
                                            padding-bottom: calc(10px + env(safe-area-inset-bottom));
                                            /* iOS safe area */
                                            padding-top: 10px;
                                        }

                                        .msbn-dropdown-toggle::after {
                                            margin-left: auto;
                                        }

                                        .msbn-item.active {
                                            background-color: #f1f5f9;
                                            color: #0f172a;
                                            font-weight: 600;
                                        }

                                        /* Add padding to the body so the bottom content isn't covered by the sticky bar */
                                        @media (max-width: 991px) {
                                            body.product-page-body {
                                                padding-bottom: 75px !important;
                                            }
                                        }
                                    </style>
@endpush

                        </div>
                    </div>

                </div>
            </div>
            </div>
    </section>


</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ===== FILTER FUNCTIONALITY =====
        // 1. Sort reviews
        const sortSelect = document.getElementById('reviewSort');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                applyFilters();
            });
        }

        // 2. Filter by star rating
        const starFilterButtons = document.querySelectorAll('.filter-stars .btn');
        if (starFilterButtons) {
            starFilterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    starFilterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    applyFilters();
                });
            });
        }

        // Function to apply all filters
        function applyFilters() {
            const sortBy = sortSelect ? sortSelect.value : 'newest';

            // Get active star filter
            const activeStarFilter = document.querySelector('.filter-stars .btn.active');
            let starFilter = 'all';
            if (activeStarFilter && !activeStarFilter.textContent.includes('All')) {
                starFilter = activeStarFilter.textContent.trim().split(' ')[0];
            }

            // Build the query string
            const params = new URLSearchParams(window.location.search);
            params.set('sort', sortBy);
            params.set('stars', starFilter);

            // Redirect with filter parameters
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }

        // ===== REVIEW FORM ENHANCEMENTS =====
        // Star rating interaction in the form
        const ratingInputs = document.querySelectorAll('.rating-select input[type="radio"]');
        const ratingLabels = document.querySelectorAll('.rating-select label');
        const ratingText = document.querySelector('.rating-text');

        const ratingDescriptions = {
            5: 'Excellent! Highly recommend',
            4: 'Very Good',
            3: 'Good, meets expectations',
            2: 'Fair, some issues',
            1: 'Poor, not recommended'
        };

        // Set initial state of stars based on any selected input
        function updateStars() {
            let selectedRating = null;

            ratingInputs.forEach(input => {
                if (input.checked) {
                    selectedRating = parseInt(input.value);
                }
            });

            ratingLabels.forEach(label => {
                const starValue = parseInt(label.getAttribute('for').replace('star', ''));
                const starIcon = label.querySelector('.star-icon');

                if (selectedRating !== null && starValue <= selectedRating) {
                    starIcon.classList.remove('far');
                    starIcon.classList.add('fas');
                    starIcon.classList.add('text-warning');
                } else {
                    starIcon.classList.remove('fas');
                    starIcon.classList.remove('text-warning');
                    starIcon.classList.add('far');
                }
            });

            if (selectedRating !== null && ratingText) {
                ratingText.textContent = ratingDescriptions[selectedRating];
            }
        }

        // Initialize stars
        updateStars();

        // Handle star selection
        ratingLabels.forEach(label => {
            label.addEventListener('mouseenter', function() {
                const starValue = parseInt(this.getAttribute('for').replace('star', ''));

                ratingLabels.forEach(label => {
                    const labelValue = parseInt(label.getAttribute('for').replace(
                        'star', ''));
                    const starIcon = label.querySelector('.star-icon');

                    if (labelValue <= starValue) {
                        starIcon.classList.remove('far');
                        starIcon.classList.add('fas');
                        starIcon.classList.add('text-warning');
                    } else {
                        starIcon.classList.remove('fas');
                        starIcon.classList.remove('text-warning');
                        starIcon.classList.add('far');
                    }
                });

                if (ratingText) {
                    ratingText.textContent = ratingDescriptions[starValue];
                }
            });

            label.addEventListener('mouseleave', function() {
                updateStars();
            });
        });

        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                updateStars();
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initializeRatings();
        setupStarHoverEffects();
        animateStars();
    });

    function initializeRatings() {
        const ratingContainers = document.querySelectorAll('.tab_star_li');

        ratingContainers.forEach(container => {
            const ratingValue = parseFloat(container.dataset.rating) || 0;
            container.innerHTML = '';

            for (let i = 1; i <= 5; i++) {
                const starWrapper = document.createElement('span');
                starWrapper.classList.add('rating-on');
                starWrapper.setAttribute('data-rating', i);
                starWrapper.setAttribute('role', 'img');
                starWrapper.setAttribute('aria-label', `Rated ${i} out of 5`);

                const filledStar = document.createElement('span');
                filledStar.textContent = '★';
                filledStar.style.position = 'absolute';
                filledStar.style.left = '0';
                filledStar.style.top = '0';
                filledStar.style.overflow = 'hidden';
                filledStar.style.width = '0%';
                filledStar.style.color = '#FFC107';
                filledStar.style.zIndex = '1';

                const baseStar = document.createElement('span');
                baseStar.textContent = '★';
                baseStar.style.color = '#e0e0e0';
                baseStar.style.position = 'relative';
                baseStar.style.zIndex = '0';

                // Append both spans
                starWrapper.style.position = 'relative';
                starWrapper.style.display = 'inline-block';
                starWrapper.style.width = '20px';
                starWrapper.style.height = '20px';

                if (i <= ratingValue) {
                    filledStar.style.width = '100%';
                } else if (i - 0.5 <= ratingValue) {
                    filledStar.style.width = '50%';
                }

                starWrapper.appendChild(filledStar);
                starWrapper.appendChild(baseStar);
                container.appendChild(starWrapper);
            }
        });
    }

    function setupStarHoverEffects() {
        document.querySelectorAll('.tab_star_li').forEach(container => {
            const stars = container.querySelectorAll('.rating-on');

            stars.forEach(star => {
                star.addEventListener('mouseenter', function() {
                    const hoverRating = parseInt(this.getAttribute('data-rating'));

                    stars.forEach((s, index) => {
                        const filled = s.querySelector('span:first-child');
                        if (index < hoverRating) {
                            filled.style.width = '100%';
                        } else {
                            filled.style.width = '0%';
                        }
                    });
                });
            });

            container.addEventListener('mouseleave', function() {
                initializeRatings(); // Reset to original
            });
        });
    }

    function animateStars() {
        setTimeout(() => {
            document.querySelectorAll('.tab_star_li .rating-on').forEach((star, index) => {
                star.style.opacity = '0';
                setTimeout(() => {
                    star.style.opacity = '1';
                    star.style.transition = 'opacity 0.3s ease';
                }, index * 100);
            });
        }, 300);
    }

    // jQuery(window).scroll(function() {
    //     var scroll = jQuery(window).scrollTop();
    //     if (scroll >= 200) {
    //         jQuery(".asn_main_sec > .asn_dv").addClass("fixed-div");
    //     } else {
    //         jQuery(".asn_main_sec > .asn_dv").removeClass("fixed-div");
    //     }
    // });
</script>
<script>
    const form = document.getElementById('some-form');
    console.log(form); // should not be null
    if (form) {
        form.submit();
    } else {
        console.warn('Form not found');
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    window.addEventListener('review-submitted', () => {
        window.location.reload();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const links = document.querySelectorAll('#table-of-content a');
        const sections = Array.from(links).map(link => {
            const id = link.getAttribute('href').substring(1);
            return document.getElementById(id);
        });

        // Smooth scroll on click
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                links.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                if (target) {
                    const offset = 100;
                    const targetPosition = target.getBoundingClientRect().top + window.scrollY -
                        offset;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        function updateActiveSection() {
            const scrollPosition = window.scrollY + 120;
            let currentSectionId = null;

            sections.forEach(section => {
                if (section && section.offsetTop <= scrollPosition) {
                    currentSectionId = section.id;
                }
            });

            // Default to the first section if we are at the top or above it
            if (!currentSectionId && sections.length > 0 && sections[0]) {
                currentSectionId = sections[0].id;
            }

            links.forEach(link => {
                link.classList.remove('active');
                const href = link.getAttribute('href').substring(1);
                if (href === currentSectionId) {
                    link.classList.add('active');
                }
            });
        }

        // Active section highlight on scroll
        window.addEventListener('scroll', updateActiveSection);

        // Initialize on page load
        updateActiveSection();
    });
</script>

<script>
    $(document).ready(function() {
        let slidersReady = false;

        // Initialize main slider (big image)
        $('.slider-for').on('init', function() {
            slidersReady = true;
        }).slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            adaptiveHeight: true,
            accessibility: false,
            focusOnChange: false,
            dots: true
        });

        // Initialize thumbnail slider (small images)
        $('.slider-nav').on('init', function() {
            setTimeout(() => {
                $('.slider-nav .slick-slide').removeClass('active hover_main');
                $('.slider-nav .slick-slide[data-slick-index="0"]').addClass('active hover_main');
            }, 50);
        }).slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            vertical: true,
            arrows: false,
            infinite: false,
            centerMode: false,
            focusOnSelect: false,
            swipe: false,
            draggable: false
        });

        // Keep thumbnails in sync when main slider changes
        $('.slider-for').on('afterChange', function(event, slick, currentSlide) {
            $('.slider-nav .slick-slide').removeClass('active hover_main');
            $('.slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]').addClass('active hover_main');
        });

        // Hover to change main image (only after sliders are ready)
        $('.slider-nav').on('mouseenter', '.slick-slide', function() {
            if (!slidersReady) return;

            let $thumb = $(this);
            let index = $thumb.data('slick-index');

            if (typeof index !== 'undefined') {
                $('.slider-for').slick('slickGoTo', parseInt(index, 10), true);
                $('.slider-nav .slick-slide').removeClass('active hover_main');
                $thumb.addClass('active hover_main');
            }
        });

        // Click to change main image
        $('.slider-nav').on('click', '.slick-slide', function() {
            let $thumb = $(this);
            let index = $thumb.data('slick-index');
            if (typeof index !== 'undefined') {
                $('.slider-for').slick('slickGoTo', parseInt(index, 10), true);
                $('.slider-nav .slick-slide').removeClass('active hover_main');
                $thumb.addClass('active hover_main');
            }
        });

        // Fallback: Reset to active thumbnail on mouse leave
        $('.slider-nav').on('mouseleave', function() {
            let currentIndex = $('.slider-for').slick('slickCurrentSlide');
            $('.slider-nav .slick-slide').removeClass('active hover_main');
            $('.slider-nav .slick-slide[data-slick-index="' + currentIndex + '"]').addClass('active hover_main');
        });

        // Click handlers to open the gallery modal using CustomEvent delegation
        $('.slider-for').on('click', '.asan-slider-inr', function() {
            let index = $(this).attr('data-gallery-index');
            if (typeof index !== 'undefined') {
                window.dispatchEvent(new CustomEvent('open-gallery-modal', {
                    detail: {
                        index: parseInt(index, 10)
                    }
                }));
            }
        });

        $('.slider-nav').on('click', '[data-gallery-index]', function() {
            let index = $(this).attr('data-gallery-index');
            if (typeof index !== 'undefined') {
                window.dispatchEvent(new CustomEvent('open-gallery-modal', {
                    detail: {
                        index: parseInt(index, 10)
                    }
                }));
            }
        });

        // Preload all images in the slider to avoid delay when switching images
        function preloadSliderImages() {
            $('.slider-for img').each(function() {
                const img = new Image();
                img.src = $(this).attr('src');
            });
        }

        $(window).on('load', function() {
            preloadSliderImages(); // Preload images on window load
        });
    });
</script>

<script>
    function applyStarHoverEffects(container) {
        const stars = container.querySelectorAll('.star-item');

        stars.forEach(star => {
            star.addEventListener('mouseenter', () => {
                const hoverValue = parseInt(star.dataset.value);

                stars.forEach(s => {
                    const value = parseInt(s.dataset.value);
                    s.classList.toggle('js-hovered', value <= hoverValue);
                });
            });

            star.addEventListener('mouseleave', () => {
                stars.forEach(s => s.classList.remove('js-hovered'));
            });
        });
    }

    function setupAllStarRatings() {
        const containers = document.querySelectorAll('.star-rating');

        containers.forEach(container => {
            if (!container.dataset.hoverSetup) {
                applyStarHoverEffects(container);
                container.dataset.hoverSetup = 'true';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        setupAllStarRatings();

        const observer = new MutationObserver(() => {
            setupAllStarRatings();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

    document.addEventListener('livewire:load', () => {
        Livewire.hook('message.processed', () => {
            setupAllStarRatings();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const swiperContainer = document.querySelector(".myReviewSlider");
        const wrapper = swiperContainer.querySelector(".swiper-wrapper");
        let slides = wrapper.querySelectorAll(".swiper-slide");
        let slideCount = slides.length;

        const slidesPerView = 1;
        const slidesPerGroup = 1;
        const minLoopSlides = 2;

        // Duplicate slides if less than minimum
        if (slideCount < minLoopSlides) {
            const clonesNeeded = minLoopSlides - slideCount;
            for (let i = 0; i < clonesNeeded; i++) {
                const clone = slides[i % slideCount].cloneNode(true);
                wrapper.appendChild(clone);
            }
            slides = wrapper.querySelectorAll(".swiper-slide");
            slideCount = slides.length;
        }

        const reviewSwiper = new Swiper(".myReviewSlider", {
            slidesPerView: slidesPerView,
            slidesPerGroup: slidesPerGroup,
            spaceBetween: 20,
            loop: slideCount > 1,
            autoplay: false,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                0: {
                    slidesPerView: 1,
                    slidesPerGroup: 1
                },
                768: {
                    slidesPerView: slidesPerView,
                    slidesPerGroup: slidesPerGroup
                }
            }
        });

        // Add 'active' class to the first slide initially
        slides.forEach(slide => slide.classList.remove('active'));
        slides[reviewSwiper.activeIndex].classList.add('active');

        // Update 'active' class on slide change
        reviewSwiper.on('slideChange', function() {
            slides.forEach(slide => slide.classList.remove('active'));
            slides[reviewSwiper.activeIndex].classList.add('active');
        });

        // Show/hide navigation buttons
        const navButtons = document.querySelectorAll('.swiper-button-next, .swiper-button-prev');
        navButtons.forEach(el => {
            if (slideCount > 1) {
                el.style.display = 'block';
                el.classList.remove('carousel-disabled');
            } else {
                el.style.display = 'none';
                el.classList.add('carousel-disabled');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Smooth scroll on "View All Reviews"
        $("#scrollToReviews").on("click", function(e) {
            e.preventDefault();

            // target the full reviews section
            let reviewsSection = $("#nav-tabContent");

            if (reviewsSection.length) {
                $("html, body").animate({
                    scrollTop: reviewsSection.offset().top
                }, 600); // 600ms = smooth duration
            }
        });
    });
</script>

<script>
    $(function() {
                let selectedRating = 0;
                let selectedFeatureId = null;
                let businessId = "{{ $business->id ?? '' }}";

                // Setup CSRF token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Open popup when clicking rating text
                $('.open-review-popup').on('click', function() {
                    selectedFeatureId = $(this).data('feature');
                    $('#popupFeatureId').val(selectedFeatureId);
                    $('#reviewPopup').modal('show');
                });

                // Star selection
                $('.popup-star').on('mouseover', function() {
                    const value = $(this).data('value');
                    $('.popup-star').each(function() {
                        $(this).css('color', $(this).data('value') <= value ? '#fbc02d' : '#ccc');
                    });
                }).on('mouseout', function() {
                    $('.popup-star').each(function() {
                        $(this).css('color', $(this).data('value') <= selectedRating ? '#fbc02d' : '#ccc');
                    });
                }).on('click', function() {
                    selectedRating = $(this).data('value');
                    $('#popupRating').val(selectedRating);
                    $('.popup-star').each(function() {
                        $(this).css('color', $(this).data('value') <= selectedRating ? '#fbc02d' : '#ccc');
                    });
                });

                // Submit review via AJAX
                $('#submitPopupReview').on('click', function(e) {
                    e.preventDefault();

                    const featureId = $('#popupFeatureId').val();
                    const rating = $('#popupRating').val();
                    const comment = $('#popupComment').val();

                    if (!rating) {
                        toastr.warning('Please select a rating before submitting.');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('business.feature.review.store', app()->getLocale()) }}",
                        type: "POST",
                        dataType: "json",
                        data: {
                            business_id: businessId,
                            feature_id: featureId,
                            rating: rating,
                            comment: comment
                        },
                        beforeSend: function() {
                            $('#submitPopupReview').prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#reviewPopup').modal('hide');
                                $('#popupComment').val('');
                                $('.popup-star').css('color', '#ccc');
                                selectedRating = 0;
                            } else {
                                toastr.error(response.message || 'Something went wrong.');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 401) {
                                toastr.error("{{ static_text('error_review_login_required') ?: 'You must be logged in to submit a review.' }}");
                            } else if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                                toastr.error(errors);
                            } else {
                                toastr.error("{{ static_text('error_review_submit_failed') ?: 'Error submitting review. Please try again.' }}");
                            }
                        },
                        complete: function() {
                            $('#submitPopupReview').prop('disabled', false);
                        }
                    });
                });
</script>

<div class="modal fade" id="imageGalleryModal" tabindex="-1" aria-hidden="true" style="z-index: 999999;">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                style="position: absolute; right: 24px; top: 24px; z-index: 10000; font-size: 20px; font-weight: bold; background: none; border: none; color: #555;">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="gallery-header">
                <div class="gallery-header-left">
                    <div style="width: 56px; height: 56px; border-radius: 8px; overflow: hidden; background: #f9f9f9; display: flex; align-items: center; justify-content: center; border: 1px solid #eaeaea; flex-shrink: 0;">
                        <x-business-logo :business="$business" />
                    </div>
                    <div>
                        <h3 style="margin: 0 0 4px 0; font-size: 22px; font-weight: 700; color: #002347;">
                            {{ $business->translations->first()->name }}
                        </h3>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #555; white-space: nowrap; flex-wrap: nowrap;">
                            @if ($averageRating !== null && (float)$averageRating > 0)
                            <div style="color: #ff9d28; display: flex; gap: 2px; flex-shrink: 0; white-space: nowrap;">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor((float)$averageRating))
                                    <i class="fas fa-star"></i>
                                    @elseif ($i - 0.5 <= (float)$averageRating)
                                        <i class="fas fa-star-half-alt"></i>
                                        @else
                                        <i class="far fa-star"></i>
                                        @endif
                                        @endfor
                            </div>
                            <span style="font-weight: 600; color: #333;">{{ formatRating($averageRating) }}</span>
                            @endif
                            @if ($hasUserRatings)
                            <span style="color: #888;">({{ $ratingCount }} {{ $ratingCount == 1 ? 'review' : 'reviews' }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($business->is_affiliate)
                <div class="gallery-header-cta">
                    <a href="{{ $business->affiliate_link ?? $business->permanent_url }}"
                        target="_blank"
                        rel="sponsored"
                        class="cta cta_orange"
                        style="padding: 12px 24px; font-weight: 600; border-radius: 30px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        {{ static_text('visit_website') ?: 'Visit website' }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;">
                            <path d="M15 3h6v6"></path>
                            <path d="M10 14 21 3"></path>
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        </svg>
                    </a>
                </div>
                @endif
            </div>

            <div class="gallery-body">

                <button type="button" id="modalPrevBtn"
                    style="position: absolute; left: 24px; top: 50%; transform: translateY(-50%); background: #ffffff; border: 1px solid #eaeaea; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-size: 18px; color: #333; z-index: 1000; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#007bff'; this.style.color='#ffffff';"
                    onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#333';">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="gallery-image-wrap">
                    <img id="modalActiveImg" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'/%3E"
                        alt="Active View"
                        style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; display: block; width: auto; height: auto;">
                </div>

                <button type="button" id="modalNextBtn"
                    style="position: absolute; right: 24px; top: 50%; transform: translateY(-50%); background: #ffffff; border: 1px solid #eaeaea; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-size: 18px; color: #333; z-index: 1000; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#007bff'; this.style.color='#ffffff';"
                    onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#333';">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <div id="modalThumbContainer" class="gallery-thumbnails">
                @foreach ($images as $index => $image)
                <div class="modal-thumb-item" data-index="{{ $index }}"
                    style="width: 90px; height: 60px; border-radius: 6px; overflow: hidden; cursor: pointer; border: 3px solid transparent; opacity: 0.6; transition: all 0.2s; flex-shrink: 0; box-sizing: border-box;"
                    onmouseover="this.style.opacity='1'"
                    onmouseout="if(!$(this).hasClass('active-thumb')) this.style.opacity='0.6'">
                    <img src="{{ asset($image) }}"
                        alt="Thumbnail {{ $index + 1 }}"
                        style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('write_review')) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({
                path: cleanUrl
            }, '', cleanUrl);

            setTimeout(() => {
                Livewire.dispatch('openReviewModal', {
                    businessId: {{ $business->id }}
                });
            }, 500);
        }
    });

    // Unified Sticky Sidebar Navigation: Active on Scroll & Smooth Click
    document.addEventListener('DOMContentLoaded', function() {
        const desktopLinks = document.querySelectorAll('.sticky-nav-links .sticky-nav-item');
        const mobileLinks = document.querySelectorAll('.msbn-item');
        const mobileActiveText = document.getElementById('msbn-active-text');

        const navSectionTargets = [
            { id: 'section1', target: 'section1' },
            { id: 'section9', target: 'section9' },
            { id: 'section15', target: 'section15' },
            { id: 'section-compare', target: 'section-compare' },
            { id: 'section14', target: 'section14' },
            { id: 'sectionDiscussions', target: 'sectionDiscussions' }
        ];

        function isTargetMatch(link, targetKey) {
            const dataTarget = link.getAttribute('data-target');
            const href = link.getAttribute('href');
            const hrefClean = href ? href.replace('#', '') : '';

            if (targetKey === 'section1') {
                return dataTarget === 'section1' || dataTarget === 'top' || href === '#top' || href === '#section1' || hrefClean === 'top' || hrefClean === 'section1';
            }

            return dataTarget === targetKey || href === '#' + targetKey || hrefClean === targetKey;
        }

        function setActiveNav(targetKey) {
            if (!targetKey) return;

            desktopLinks.forEach(link => {
                if (isTargetMatch(link, targetKey)) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            mobileLinks.forEach(link => {
                if (isTargetMatch(link, targetKey)) {
                    link.classList.add('active');
                    if (mobileActiveText) {
                        mobileActiveText.textContent = link.textContent.trim();
                    }
                } else {
                    link.classList.remove('active');
                }
            });
        }

        let isManualScrolling = false;
        let manualScrollTimer = null;

        function updateActiveOnScroll() {
            if (isManualScrolling) return;

            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const docHeight = document.documentElement.scrollHeight;

            const visibleSections = [];
            navSectionTargets.forEach(item => {
                const el = document.getElementById(item.id);
                if (el && el.offsetParent !== null) {
                    visibleSections.push({
                        id: item.id,
                        target: item.target,
                        top: el.getBoundingClientRect().top + scrollY
                    });
                }
            });

            if (visibleSections.length === 0) return;

            // 1. Near the very top of page
            if (scrollY < 200) {
                setActiveNav(visibleSections[0].target);
                return;
            }

            // 2. Near the bottom of page
            if (windowHeight + scrollY >= docHeight - 80) {
                setActiveNav(visibleSections[visibleSections.length - 1].target);
                return;
            }

            // 3. Section based on viewport offset (130px header offset)
            const currentPos = scrollY + 130;
            let activeTarget = visibleSections[0].target;

            for (let i = 0; i < visibleSections.length; i++) {
                if (currentPos >= visibleSections[i].top) {
                    activeTarget = visibleSections[i].target;
                } else {
                    break;
                }
            }

            setActiveNav(activeTarget);
        }

        // Attach click events for both desktop and mobile nav items
        function handleNavClick(e) {
            const dataTarget = this.getAttribute('data-target');
            const href = this.getAttribute('href');
            const targetAttr = dataTarget || (href ? href.replace('#', '') : '');

            if (!targetAttr) return;

            let targetEl = null;
            let targetKey = targetAttr;

            if (targetAttr === 'top' || targetAttr === 'section1') {
                targetEl = document.getElementById('section1') || document.body;
                targetKey = 'section1';
            } else {
                targetEl = document.getElementById(targetAttr);
            }

            if (targetEl) {
                e.preventDefault();
                setActiveNav(targetKey);

                isManualScrolling = true;
                clearTimeout(manualScrollTimer);
                manualScrollTimer = setTimeout(() => {
                    isManualScrolling = false;
                }, 800);

                const headerOffset = 90;
                let offsetPosition = 0;

                if (targetAttr === 'top' && !document.getElementById('section1')) {
                    offsetPosition = 0;
                } else {
                    offsetPosition = targetEl.getBoundingClientRect().top + (window.pageYOffset || document.documentElement.scrollTop) - headerOffset;
                }

                window.scrollTo({
                    top: Math.max(0, offsetPosition),
                    behavior: 'smooth'
                });

                if (window.history && window.history.pushState) {
                    window.history.pushState(null, null, '#' + (targetAttr === 'section1' ? 'top' : targetAttr));
                }
            }
        }

        desktopLinks.forEach(link => link.addEventListener('click', handleNavClick));
        mobileLinks.forEach(link => link.addEventListener('click', handleNavClick));

        // Listen for scroll events with passive performance
        window.addEventListener('scroll', updateActiveOnScroll, { passive: true });

        // Initial check on page load or anchor hash
        setTimeout(() => {
            const hash = window.location.hash;
            if (hash) {
                let cleanHash = hash.replace('#', '');
                if (cleanHash === 'top') cleanHash = 'section1';
                setActiveNav(cleanHash);
            } else {
                updateActiveOnScroll();
            }
        }, 120);
    });
</script>

@push('styles')
<style>
@media (max-width: 767.98px) {
    .section14-breakdown-collapsible,
    .bottom-breakdown-collapsible {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.35s ease-in-out, opacity 0.25s ease;
    }
    .section14-rating-trigger,
    .bottom-rating-trigger {
        cursor: pointer;
    }
}
@media (min-width: 768px) {
    .section14-breakdown-collapsible,
    .bottom-breakdown-collapsible {
        max-height: none !important;
        overflow: visible !important;
        opacity: 1 !important;
        display: block !important;
    }
}
</style>
@endpush

<script>
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
</script>

@push('styles')
<style>
@media (max-width: 767.98px) {
    .bottom-breakdown-collapsible {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.35s ease-in-out, opacity 0.25s ease;
    }
    .bottom-rating-trigger {
        cursor: pointer;
    }
}
@media (min-width: 768px) {
    .bottom-breakdown-collapsible {
        max-height: none !important;
        overflow: visible !important;
        opacity: 1 !important;
        display: block !important;
    }
}
</style>
@endpush

<script>
    function toggleBottomReviewBreakdown() {
        if (window.innerWidth >= 768) return;

        const content = document.getElementById('bottomReviewBreakdownContent');
        const chevron = document.querySelector('.bottom-rating-chevron');
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

    function toggleMobileReviewBreakdown() {
        const content = document.getElementById('mobileReviewBreakdownContent');
        const chevron = document.querySelector('.mobile-rating-chevron');
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
</script>

<!-- Report Issue Modal -->
<div class="modal fade" id="reportReviewModal" tabindex="-1" role="dialog" aria-labelledby="reportReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 40px rgba(0,0,0,0.14);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 20px 24px 16px;">
                <div>
                    <h3 class="modal-title fw-bold m-0" id="reportReviewModalLabel" style="color: #002347; font-size: 18px;">{{ static_text('report_a_review_heading') ?: 'Report a review' }}</h3>
                    <p class="text-muted mb-0 mt-1" style="font-size: 13px;">{{ static_text('select_issue_reason_subtext') ?: 'Select the reason that best describes the issue' }}</p>
                </div>
                <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">✕</button>
            </div>
            <div class="modal-body" style="padding: 20px 24px;">
                <input type="hidden" id="reportReviewModalId">
                <input type="hidden" id="reportReviewReason">

                <!-- Radio option cards -->
                <div class="d-flex flex-column gap-2" id="reportReviewReasonOptions">
                    @foreach([
                    ['value' => 'Fake or fraudulent', 'icon' => 'fa-ban', 'desc' => 'The review appears to be fake or paid for'],
                    ['value' => 'Inappropriate content', 'icon' => 'fa-exclamation-triangle', 'desc' => 'Contains offensive language or hate speech'],
                    ['value' => 'Spam or advertising', 'icon' => 'fa-bullhorn', 'desc' => 'Promotional or irrelevant content'],
                    ['value' => 'Conflict of interest', 'icon' => 'fa-user-tie', 'desc' => 'Written by someone affiliated with the business'],
                    ['value' => 'Other issue', 'icon' => 'fa-comment-dots', 'desc' => 'Another problem not listed above']
                    ] as $opt)
                    <div class="report-review-option-card d-flex align-items-start gap-3 p-3"
                        onclick="selectReviewReportReason('{{ $opt['value'] }}', this)"
                        style="border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer;">

                        <div class="report-check flex-shrink-0 mt-1" style="width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                            <i class="fas fa-check" style="font-size: 10px; color: transparent;"></i>
                        </div>

                        <div>
                            <h4 class="m-0" style="font-size: 14.5px; font-weight: 600; color: #1e293b; margin-bottom: 2px !important;">
                                <i class="fas {{ $opt['icon'] }} me-1 text-muted" style="font-size: 13px;"></i>
                                {{ $opt['value'] }}
                            </h4>
                            <p class="text-muted m-0" style="font-size: 13px; line-height: 1.4;">{{ $opt['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Textarea (hidden initially) -->
                <div id="reportReviewDetailsWrapper" class="mt-4" style="display: none;">
                    <label class="form-label fw-bold" style="font-size: 13.5px; color: #334155;">{{ static_text('additional_details_optional') ?: 'Additional details (optional)' }}</label>
                    <textarea id="reportReviewDetails" class="form-control" rows="3" placeholder="{{ static_text('additional_details_placeholder') ?: 'Please provide any additional context...' }}" style="font-size: 14px; border-radius: 12px; padding: 12px; resize: none;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 16px 24px;">
                <button type="button" class="btn btn-light rounded-pill-8 px-4" data-bs-dismiss="modal" style="font-size: 14px;">Cancel</button>
                <button type="button" class="blue-btn btn rounded-pill-8 px-4" id="submitReviewReportBtn" disabled style="background: #002347; border-color: #002347; color: white; font-size: 14px; opacity: 0.5; transition: opacity 0.2s;" onclick="submitReviewReport()">Submit report</button>
            </div>
        </div>
    </div>
</div>
@push('styles')
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
@endpush
<script>
    function openReviewReportModal(reviewId) {
        @if(!auth()->check())
        if (typeof openLoginModal === 'function') {
            openLoginModal();
        } else {
            window.location.href = "{{ route('login') }}";
        }
        return;
        @endif

        document.getElementById('reportReviewModalId').value = reviewId;
        document.getElementById('reportReviewReason').value = '';
        document.getElementById('reportReviewDetails').value = '';
        document.getElementById('reportReviewDetailsWrapper').style.display = 'none';
        document.getElementById('submitReviewReportBtn').disabled = true;
        document.getElementById('submitReviewReportBtn').style.opacity = '0.5';

        document.querySelectorAll('.report-review-option-card').forEach(c => c.classList.remove('selected'));

        const modalEl = document.getElementById('reportReviewModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    function selectReviewReportReason(reason, card) {
        document.getElementById('reportReviewReason').value = reason;
        document.querySelectorAll('.report-review-option-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        document.getElementById('reportReviewDetailsWrapper').style.display = 'block';
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
            });
    }
</script>

@endsection

@push('styles')
<style>
.comparison-box-link {
    color: inherit;
    text-decoration: none;
    cursor: pointer;
}

.comparison-box-link:hover {
    color: inherit;
    text-decoration: none;
}

</style>
@endpush
