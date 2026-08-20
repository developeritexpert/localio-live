@extends('user_layout.master')
@section('content')
@php
    $firstBiz = isset($businesses[0]) ? $businesses[0] : null;
    $parentCat = $firstBiz && $firstBiz->category && $firstBiz->category->parent ? $firstBiz->category->parent : null;
    $cat = $firstBiz && $firstBiz->category ? $firstBiz->category : null;

    $parentCatName = $parentCat?->translation?->name ?? $parentCat?->translations?->first()?->name ?? null;
    $parentCatSlug = $parentCat?->translation?->slug ?? $parentCat?->translations?->first()?->slug ?? null;

    $catName = $cat?->translation?->name ?? $cat?->translations?->first()?->name ?? null;
    $catSlug = $cat?->translation?->slug ?? $cat?->translations?->first()?->slug ?? null;

    $hasAffiliated = $businesses->contains(fn($b) => !empty($b->is_affiliate));
@endphp
<section class=" help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 comparsn_bnr_sec" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="background-color: #f7f9fb;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom: 0;">
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
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        @if (count($businesses) >= 2)
                            {{ $businesses[0]->translations->first()?->name ?? '' }} vs {{ $businesses[1]->translations->first()?->name ?? '' }}
                        @elseif (count($businesses) == 1)
                            {{ $businesses[0]->translations->first()?->name ?? '' }}
                        @else
                            Businesses Comparison
                        @endif
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-12 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <div>
                        <div class="an_lkd   align-items-center gap-2 " style="display:unset;">
                            <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin: 0; line-height: 1.2;">
                                @if (count($businesses) >= 2)
                                    {{ $businesses[0]->translations->first()?->name ?? '' }} vs {{ $businesses[1]->translations->first()?->name ?? '' }}: Comparison
                                @elseif (count($businesses) == 1)
                                    {{ $businesses[0]->translations->first()?->name ?? '' }}: Comparison
                                @else
                                    Businesses Comparison
                                @endif
                            </h1>
                            <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">Last updated on {{ now()->format('F j, Y') }}</p>
                            <p style="font-size: 15px; color: #444; margin-bottom: 0;">
                                Learn more from our team about Website Builder Software pricing features and benefits in our Website Builder Buyers Guide
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- section product comparison -->
<section class="lts-wht-bg product_comp_sec p_120 light new-white-bg" style="padding-top: 30px;">
    <div class="container">

            <div class="row justify-content-center pro-row-gp versus-row" data-aos="fade-up" data-aos-duration="1000">
                <div class="col-12">
                    <div class="asn_tprw">
                        <div class="pdc_box bg-white rounded-4 border shadow-sm p-4 p-md-5" style="border-radius: 20px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;">
                            @foreach ($businesses as $index => $business)
                                @if ($index > 0)
                                    <div class="versus-box">
                                        <p class="d-flex m-0">vs</p>
                                    </div>
                                @endif
                                <div class="pdc_choice text-center d-flex flex-column align-items-center">
                                    <div class="auto-choice-hd w-100 d-flex flex-column align-items-center" style="border: none; padding: 0; margin-bottom: 0;">
                                        <div class="inn_sl_hed flex-column align-items-center text-center w-100" style="gap: 8px;">
                                            <div class=" top-product-logo mx-auto" style="margin-bottom: 8px;">
                                                <x-business-logo :business="$business" />
                                            </div>
                                            <div class="sl_h text-center w-100">
                                                <div class="inn_h d-flex align-items-center justify-content-center">
                                                    <h6 class="head" style="font-size: 18px; font-weight: 700; color: #1e3050; margin: 0;">{{ $business->translations->first()?->name ?? '' }}</h6>
                                                </div>
                                                <div class="tp-btm d-flex align-items-center justify-content-center mt-1 rating-group" style="gap: 6px;">
                                                    @php
                                                        $rating = round($business->reviews->avg('rating'), 1);
                                                        $ratingCount = $business->reviews->count();
                                                    @endphp

                                                    <span style="">{{ $ratingCount > 0 ? number_format($rating, 1) : '0.0' }}</span>
                                                    <div class="d-flex align-items-center">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($rating >= $i)
                                                                <i class="r-star fas fa-star text-warning" style=""></i>
                                                            @elseif ($rating >= $i - 0.5)
                                                                <i class="r-star fas fa-star-half-alt text-warning" style=""></i>
                                                            @else
                                                                <i class="r-star far fa-star text-warning" style=";"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span style="">({{ $ratingCount }})</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if(!empty($business->is_affiliate))
                                        <div class="auto-choice-btn fit-btn w-100 mt-3" style="max-width: 220px;">
                                            <a href="{{ $business->getTrackedUrl() ?? $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}"
                                                target="_blank"
                                                class="cta cta_orange d-flex align-items-center justify-content-center"
                                                style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 14px; padding: 10px 20px; border-radius: 30px; text-decoration: none; width: 100%; border:none;"
                                                onmouseover="this.style.backgroundColor='#e64a19';"
                                                onmouseout="this.style.backgroundColor='#ff5722';">
                                                Visit website
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Thin horizontal line -->
                                        <hr class="w-100 my-3" style="border-top: 1px solid #e2e8f0; opacity: 1; margin-left: 0; margin-right: 0;">

                                        <!-- Review Breakdown inside card -->
                                        <div class="review-breakdown-card w-100 text-start">
                                            <h6 style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 14px;">Review breakdown</h6>
                                            <ul class="list-unstyled mb-0 d-flex flex-column" style="gap: 10px;">
                                                <li class="d-flex justify-content-between align-items-center">
                                                    <span style="font-size: 12px; font-weight: 500; color: #444;">Value for money</span>
                                                    <div class="d-flex align-items-center gap-2" style="flex: 1; max-width: 140px; margin-left: 10px;">
                                                        @php
                                                            $valueForMoney = $business->reviews->avg('value_for_money_rating') ?? 0;
                                                            $valueForMoneyPercent = $valueForMoney * 20;
                                                        @endphp
                                                        <div class="progress w-100" style="height: 8px; background-color: #f1f5f9; border-radius: 10px;">
                                                            <div class="progress-bar" style="height: 8px; width: {{ $valueForMoneyPercent }}%; background-color: #22c55e; border-radius: 10px;"></div>
                                                        </div>
                                                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ number_format($valueForMoney, 1) }}</span>
                                                    </div>
                                                </li>
                                                <li class="d-flex justify-content-between align-items-center">
                                                    <span style="font-size: 12px; font-weight: 500; color: #444;">Ease of use</span>
                                                    <div class="d-flex align-items-center gap-2" style="flex: 1; max-width: 140px; margin-left: 10px;">
                                                        @php
                                                            $easeOfUse = $business->reviews->avg('ease_of_use_rating') ?? 0;
                                                            $easeOfUsePercent = $easeOfUse * 20;
                                                        @endphp
                                                        <div class="progress w-100" style="height: 8px; background-color: #f1f5f9; border-radius: 10px;">
                                                            <div class="progress-bar" style="height: 8px; width: {{ $easeOfUsePercent }}%; background-color: #22c55e; border-radius: 10px;"></div>
                                                        </div>
                                                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ number_format($easeOfUse, 1) }}</span>
                                                    </div>
                                                </li>
                                                <li class="d-flex justify-content-between align-items-center">
                                                    <span style="font-size: 12px; font-weight: 500; color: #444;">Features</span>
                                                    <div class="d-flex align-items-center gap-2" style="flex: 1; max-width: 140px; margin-left: 10px;">
                                                        @php
                                                            $featuresRating = $business->reviews->avg('exclusive_service_rating') ?? 0;
                                                            $featuresPercent = $featuresRating * 20;
                                                        @endphp
                                                        <div class="progress w-100" style="height: 8px; background-color: #f1f5f9; border-radius: 10px;">
                                                            <div class="progress-bar" style="height: 8px; width: {{ $featuresPercent }}%; background-color: #22c55e; border-radius: 10px;"></div>
                                                        </div>
                                                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ number_format($featuresRating, 1) }}</span>
                                                    </div>
                                                </li>
                                            </ul>

                                            @php
                                                $recPercent = 100;
                                                if ($ratingCount > 0 && $rating > 0) {
                                                    $recPercent = round(($rating / 5) * 100);
                                                }
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top: 1px solid #f1f5f9;">
                                                <span style="font-size: 14px; font-weight: 600; color: #002347;">Recommended by users</span>
                                                <span style="font-size: 14px; font-weight: 600; color: #002347;">{{ $recPercent }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="remove-from-comparison"
                                        data-url="{{ route('remove-from-comparison', ['locale' => app()->getLocale(), 'productId' => $business->id]) }}">
                                        <span class="ct_icn"><i class="fa-solid fa-xmark"></i></span>
                                    </a>
                                </div>
                            @endforeach
                            @if (count($businesses) < 2)
                                <div class="versus-box" style="visibility: hidden;">
                                    <p class="d-flex m-0">vs</p>
                                </div>
                                <div class="pdc_choice text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 100%;">
                                    <a href="{{route('top-rated-product')}}" class="pdc_ryt" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; text-decoration: none; min-height: 320px;">
                                        <div class="ad_lnk" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
                                            <img src="{{ asset('front/img/pls-add.svg') }}" style="width: 48px; height: 48px;">
                                            <span style="font-size: 15px; font-weight: 600; color: #06498b;"> Add to compare </span>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

                    <!-- USPs / Features div (only for affiliated businesses) -->
                    @php
                        $hasAnyAffiliatedUsps = $businesses->contains(function($b) {
                            return !empty($b->is_affiliate) && (($b->usps && $b->usps->count() > 0) || ($b->features && $b->features->count() > 0));
                        });
                    @endphp
                    @if($hasAnyAffiliatedUsps)
                    <div class="row pro-row-gp mt-4" data-aos="fade-up" data-aos-duration="1000">
                        @foreach ($businesses as $business)
                            <div class="col-lg-6 col-md-6 mb-3">
                                @if(!empty($business->is_affiliate))
                                <div class="bg-white p-4" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); height: 100%;">
                                    <ul class="list-unstyled mb-0 d-flex flex-column" style="gap: 14px;">
                                        @if($business->usps && $business->usps->count() > 0)
                                            @foreach($business->usps as $usp)
                                                @php $uText = $usp->text ?? $usp->usp_text ?? ''; @endphp
                                                @if(!empty($uText))
                                                <li class="d-flex align-items-center" style="gap: 12px;">
                                                    <span class="d-inline-flex align-items-center justify-content-center" style="flex-shrink: 0; width: 22px;">
                                                        <img src="{{ asset('front/img/pros-tick.svg') }}" alt="Tick" style="width: 20px; height: 15px; object-fit: contain;">
                                                    </span>
                                                    <span style="font-size: 16px; font-weight: 500; color: #000; line-height: 1.3;">{{ $uText }}</span>
                                                </li>
                                                @endif
                                            @endforeach
                                        @elseif($business->features && $business->features->count() > 0)
                                             @foreach($business->features as $feature)
                                                <li class="d-flex align-items-center" style="gap: 12px;">
                                                    <span class="d-inline-flex align-items-center justify-content-center" style="flex-shrink: 0; width: 22px;">
                                                        <img src="{{ asset('front/img/pros-tick.svg') }}" alt="Tick" style="width: 20px; height: 15px; object-fit: contain;">
                                                    </span>
                                                    <span style="font-size: 16px; font-weight: 500; color: #000; line-height: 1.3;">{{ $feature->translations->first()?->name ?? 'Feature Name' }}</span>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="d-flex align-items-center" style="gap: 12px;">
                                                <span class="d-inline-flex align-items-center justify-content-center" style="flex-shrink: 0; width: 22px;">
                                                    <img src="{{ asset('front/img/pros-tick.svg') }}" alt="Tick" style="width: 20px; height: 15px; object-fit: contain;">
                                                </span>
                                                <span style="font-size: 16px; font-weight:500; color: #000;">No features available</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- what is product -->
                    <div class="row xrro_dv light pro-row-gp" data-aos="fade-up" data-aos-duration="1000">
                        @foreach ($businesses as $index => $business)
                            <div class="col-lg-6 {{ $index == 0 ? 'xrro_bordr' : '' }}">
                                <div class="xro_box {{ $index == 1 ? 'p_left' : '' }}">
                                    <h6>What is {{ $business->translations->first()?->name ?? 'Product' }}?</h6>
                                    <div class="xro_text">
                                        <p>{!! $business->translations->first()?->description ?? 'No description available.' !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- price start from / offers (only for affiliated businesses) -->
                    @if($hasAffiliated)
                    <div class="prc_dv_new my-4" data-aos="fade-up" data-aos-duration="1000">
                        <div class="row g-4">
                            @foreach ($businesses as $index => $business)
                                @php
                                    $startingPriceBusiness = getBusinessesWithStartingPrice($business);
                                    $startingPrice = $startingPriceBusiness[0]['starting_price'] ?? null;
                                    $bName = $business->translations->first()?->name ?? '';
                                    $currency = $startingPrice['currency'] ?? '$';
                                    $amountStr = isset($startingPrice['amount']) ? number_format($startingPrice['amount'], 2) : null;
                                    $timeUnit = ucfirst($startingPrice['time_unit'] ?? 'One_time');
                                @endphp
                                <div class="col-lg-6 col-12">
                                    @if(!empty($business->is_affiliate))
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="top-product-medium-logo">
                                        <x-business-logo :business="$business" :name="$bName" />
                                        </div>
                                        <h5 style="font-size: 16px; font-weight: 700; color: #1e3050; margin: 0;">{{ $bName }}</h5>
                                    </div>
                                    <div class="row g-3">
                                        <!-- Starting price card -->
                                        <div class="col-6">
                                            <div class="p-3 bg-white border h-100 d-flex flex-column justify-content-between text-center" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                                <div>
                                                    <h6 class="starting-price-title">Starting price</h6>
                                                    @if($startingPrice)
                                                        <h2 class="starting-price-value">{{ $currency }}{{ $amountStr ?? 'Free' }}</h2>
                                                        <p class="starting-price-text">Flat Rate, Per {{ $timeUnit }}</p>
                                                    @else
                                                        <h2 class="starting-price-value">Contact</h2>
                                                        <p class="starting-price-text">Custom subscription</p>
                                                    @endif
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ $business->getTrackedUrl() ?? $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}" target="_blank" style="font-size: 13px; font-weight: 600; color: #06498b; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">View pricing</a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Free trial card -->
                                        <div class="col-6">
                                            <div class="p-3 bg-white border h-100 d-flex flex-column justify-content-between text-center" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="grn_check_big">
                                                        <img src="{{ asset('front/img/new-grn-chk.svg') }}" alt="Check">
                                                    </div>
                                                    <h6 class="blue-text big-bld mt-2">Free Trial<br>Available</h6>
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ $business->getTrackedUrl() ?? $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}" target="_blank" class="blue-btn blue_t_org_btn btn text-white w-100 fw-semibold" style=" border-radius: 50px; font-size: 13px; padding: 8px 16px; text-decoration: none;">Claim Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- review tabination -->
    <div class="crm_sec light pb-0 rev-tabi">
        <div class="container">
            <div class="crm_content" data-aos="fade-up" data-aos-duration="1000">
                <div class="crm_hd">
                    <div class="crm_lft">
                        <h2>Reviews</h2>
                    </div>
                </div>
                <div class="row" data-aos="fade-up" data-aos-duration="1000">
                    @foreach($businesses as $business)
                        @php
                            $product = $business->products->first();
                            $rating = round($business->reviews->avg('rating'), 1);
                            $ratingCount = $business->reviews->count();
                            $curLang = getCurrentLocale();
                            $lObj = \App\Models\Language::where('lang_code', $curLang)->first();
                            $revSlug = !empty($lObj->reviews_slug) ? $lObj->reviews_slug : 'reviews';
                            $bizSlug = $business->translations->where('lang_id', getCurrentLanguageID())->first()?->slug ?? $business->translations->first()?->slug ?? $business->slug;
                        @endphp

                        <div class="col-md-6 mb-4 ">
                            @php
                                $activeReviews = $business->reviews;
                                $bAvgRating = $activeReviews->count() > 0
                                    ? round($activeReviews->avg('rating'), 1)
                                    : (float)($business->admin_rating ?? 0);
                                $bTotalReviews = $activeReviews->count();
                                $bHasReviews = $bTotalReviews > 0;
                                $bName = $business->translations->first()?->name ?? '';
                            @endphp

                            <div class="p-4 bg-white rounded-3 border h-100 d-flex flex-column" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">

                                {{-- Header: logo + name + stars + visit --}}
                                <div class="review-header-box top_review_bx" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 48px; height: 48px; flex-shrink: 0; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <x-business-logo :business="$business" :name="$bName" />
                                        </div>
                                        <div>
                                            <h3 style="font-size: 16px !important; font-weight: 700 !important; margin: 0 0 4px 0; color: #1e3050;">{{ $bName }}</h3>
                                            <div style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                                                <span style="font-weight: 600; color: #334155;">{{ number_format($bAvgRating, 1) }}</span>
                                                <div style="display: flex; gap: 2px;">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= floor($bAvgRating))
                                                            <i class="fas fa-star text-warning" style="font-size: 13px;"></i>
                                                        @elseif ($i - 0.5 <= $bAvgRating)
                                                            <i class="fas fa-star-half-alt text-warning" style="font-size: 13px;"></i>
                                                        @else
                                                            <i class="far fa-star text-warning" style="font-size: 13px;"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span style="color: #64748b;">({{ number_format($bTotalReviews) }})</span>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ $business->getTrackedUrl() }}" class="cta btn-orng justify-content-center" target="_blank" style="display: flex !important; width: fit-content; height: fit-content; align-items: center; border-radius: 30px; padding: 9px 20px; white-space: nowrap;">
                                        Visit website
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                    </a>
                                </div>

                                {{-- Actual Review Cards --}}
                                @if($bHasReviews)
                                    @php $recentReviews = $activeReviews->sortByDesc('created_at')->take(2); @endphp
                                    @if($recentReviews->count() > 0)
                                        <div class="mt-3 pt-3 " style="">
                                            <h6 style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 14px;">Recent reviews</h6>
                                            @foreach($recentReviews as $rev)
                                                @php
                                                    $revTrans = $rev->translations->first();
                                                    $revTitle = $revTrans->title ?? 'Review';
                                                    $revDesc = \Illuminate\Support\Str::limit(strip_tags($revTrans->description ?? ''), 120);
                                                    $revUser = $rev->user;
                                                @endphp
                                                <div class="sidebar-review-card" style="margin-bottom: 16px; padding-bottom: 16px; {{ !$loop->last ? 'border-bottom: 1px solid #f0f0f0;' : '' }}">
                                                    {{-- Reviewer header --}}
                                                    <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%; margin-bottom: 8px;">
                                                        <div class="review-user" style="display: flex; align-items: center; gap: 10px;">
                                                            @if($revUser && $revUser->profile_image && $revUser->profile_image !== 'front/img/default.png')
                                                                <img src="{{ asset($revUser->profile_image) }}" class="rounded-circle" width="36" height="36" style="object-fit:cover;">
                                                            @else
                                                                <div style="width: 36px; height: 36px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                                    <span style="color: white; font-weight: bold; font-size: 15px;">{{ strtoupper(substr($revUser->first_name ?? 'A', 0, 1)) }}</span>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div style="font-size: 13px; font-weight: 600; color: #1e3050;">{{ $revUser ? $revUser->displayName() : 'Anonymous' }}</div>
                                                                @if($revUser && $revUser->job_title)
                                                                    <div style="font-size: 11px; color: #777;">{{ $revUser->job_title }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <small class="text-muted" style="font-size: 11px; white-space: nowrap;">{{ $rev->created_at->diffForHumans() }}</small>
                                                    </div>

                                                    {{-- Stars + Title --}}
                                                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                                        @for($i=1;$i<=5;$i++)
                                                            @if($i<=floor($rev->rating))
                                                                <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                                            @elseif($i-0.5<=$rev->rating)
                                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 11px;"></i>
                                                            @else
                                                                <i class="far fa-star text-warning" style="font-size: 11px;"></i>
                                                            @endif
                                                        @endfor
                                                        <span style="font-size: 12px; font-weight: 600; color: #334155;">{{ number_format($rev->rating, 1) }}</span>
                                                    </div>

                                                    @if($revTitle && $revTitle !== 'Review')
                                                        <div style="font-size: 13.5px; font-weight: 600; color: #1e3050; margin-bottom: 3px;">{{ $revTitle }}</div>
                                                    @endif

                                                    @if($revDesc)
                                                        <p style="font-size: 13px; line-height: 1.5; color: #4a5568; margin: 0;">{{ $revDesc }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                {{-- View all reviews link --}}
                                <div class="mt-3 pt-3 text-center" style="border-top: 1px solid #f0f0f0;">
                                    <a href="{{ route('ReviewShow', ['locale' => $curLang, 'slug' => $bizSlug, 'reviews_slug' => $revSlug]) }}" class="btn-g-link">
                                        View all reviews
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <section class="subs_sec light pt_120 ">
        <x-news-letter-subscription/>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.remove-from-comparison').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const url = this.getAttribute('data-url');

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => {
                            if (!response.ok) throw response;
                            return response.json();
                        })
                        .then(data => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                // Reload current page
                                window.location.href =
                                    "{{ route('product-comparison', app()->getLocale()) }}";
                            }
                        })
                        .catch(async error => {
                            let message = 'Something went wrong.';
                            try {
                                const errJson = await error.json();
                                message = errJson.message || message;
                            } catch (_) {}
                            alert(message);
                        });
                });
            });
        });
    </script>
@endsection
