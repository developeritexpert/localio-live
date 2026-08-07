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
@endphp
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0; margin-top: 120px; padding-top: 50px; padding-bottom: 30px;">
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
<section class="product_comp_sec p_120 light new-white-bg" style="padding-top: 30px;">
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
                                            <div class="sli_img choice_img mx-auto" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 8px;">
                                                <img class="slider_img" src="{{ asset($business->icon_id) }}"
                                                    alt="{{ $business->translations->first()?->name ?? '' }}" style="width: 100%; height: 100%; object-fit: contain;">
                                            </div>
                                            <div class="sl_h text-center w-100">
                                                <div class="inn_h d-flex align-items-center justify-content-center">
                                                    <h6 class="head" style="font-size: 18px; font-weight: 700; color: #1e3050; margin: 0;">{{ $business->translations->first()?->name ?? '' }}</h6>
                                                </div>
                                                <div class="tp-btm d-flex align-items-center justify-content-center mt-1" style="gap: 6px;">
                                                    @php
                                                        $rating = round($business->reviews->avg('rating'), 1);
                                                        $ratingCount = $business->reviews->count();
                                                    @endphp

                                                    <span style="font-size: 13.5px; font-weight: 600; color: #64748b;">{{ $ratingCount > 0 ? number_format($rating, 1) : '0.0' }}</span>
                                                    <div class="d-flex align-items-center">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($rating >= $i)
                                                                <i class="fas fa-star text-warning" style="font-size: 13px; margin-right: 2px;"></i>
                                                            @elseif ($rating >= $i - 0.5)
                                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 13px; margin-right: 2px;"></i>
                                                            @else
                                                                <i class="far fa-star text-warning" style="font-size: 13px; margin-right: 2px;"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span style="font-size: 13.5px; color: #64748b; font-weight: 500;">({{ $ratingCount }})</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="auto-choice-btn fit-btn w-100 mt-3" style="max-width: 220px;">
                                            <a href="{{ $business->permanent_url ?? $business->affiliate_link ?? 'javascript:void(0)' }}"
                                                class="cta cta_orange d-flex align-items-center justify-content-center"
                                                style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 14px; padding: 10px 20px; border-radius: 30px; text-decoration: none; width: 100%; border:none;"
                                                onmouseover="this.style.backgroundColor='#e64a19';"
                                                onmouseout="this.style.backgroundColor='#ff5722';">
                                                Visit website
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                            </a>
                                        </div>

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
                                                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ number_format($valueForMoney, 1) }}/5</span>
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
                                                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ number_format($easeOfUse, 1) }}/5</span>
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
                                                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ number_format($featuresRating, 1) }}/5</span>
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
                        </div>
                    </div>
                </div>
                @if (count($businesses) < 2)
                <div class="col-lg-3">
                    <a href="{{route('top-rated-product')}}" class="pdc_ryt">
                        <div class="ad_lnk">
                            <img src="{{ asset('front/img/pls-add.png') }}">
                            Add Business
                        </div>
                    </a>
                </div>
                @endif
            </div>



                    <!-- Features div (styled like business details page) -->
                    <div class="row pro-row-gp mt-4" data-aos="fade-up" data-aos-duration="1000">
                        @foreach ($businesses as $business)
                            <div class="col-lg-6 col-md-6 mb-3">
                                <div class="bg-white p-4" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); height: 100%;">
                                    <ul class="list-unstyled mb-0 d-flex flex-column" style="gap: 14px;">
                                        @if($business->features && $business->features->count() > 0)
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
                                                <span style="font-size: 16px;  font-weight:500; color: #000;">No features available</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>

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

                    <!-- price start from -->
                    <div class="prc_dv" data-aos="fade-up" data-aos-duration="1000" style="max-width: 100% !important;">
                        <div class="hd_text">
                            <h2>Price Starts From</h2>
                        </div>
                        <div class="prc_bx">
                            @foreach ($businesses as $business)
                                <div class="prc_contnt">
                                    <div class="sli_img">
                                        <img class="slider_img" src="{{ asset($business->icon_id) }} "style="max-width:55px;"
                                            alt="{{ $business->translations->first()?->name ?? '' }}">
                                    </div>
                                    <div class="inn_h d-flex align-items-center">
                                        <h6 class="head">{{ $business->translations->first()?->name ?? '' }}</h6>
                                        <div class="d-none">
                                            <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                                        </div>
                                    </div>
                                    @php
                                    $startingPriceBusiness = getBusinessesWithStartingPrice($business);
                                    $startingPrice = $startingPriceBusiness[0]['starting_price'] ?? null;
                                    @endphp

                                    @if($startingPrice)
                                        <p class="m-0">
                                            <span>
                                                {{ $startingPrice['currency'] ?? '' }}{{ isset($startingPrice['amount']) ? number_format($startingPrice['amount'], 2) : 'Free' }}
                                            </span>
                                            @if(isset($startingPrice['amount']) && $startingPrice['amount'] > 0)
                                                /user
                                            @endif
                                        </p>
                                        <p>{{ ucfirst($startingPrice['time_unit'] ?? 'monthly') }} subscription</p>
                                    @else
                                        <p class="m-0"><span>Contact for Pricing</span></p>
                                        <p>Custom subscription</p>
                                    @endif

                                    <div class="auto-choice-btn">
                                        <a href="{{ $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}" class="btn-orng cta cta_orange fw_500">
                                            @if($startingPrice && isset($startingPrice['amount']) && $startingPrice['amount'] == 0)
                                                Try for Free
                                            @else
                                                Get Started
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
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
                        @endphp

                        <div class="col-md-6">
                            <div class="sales-crm-pack crm-pack-lft compari_crm_pck">
                                <div class="inn_sl_hed">
                                    <div class="sli_img choice_img">
                                        <img src="{{ asset($business->icon_id) }}" alt="No Image available">
                                    </div>
                                    <div class="sl_h">
                                        <div class="inn_h d-flex align-items-center">
                                            <h6 class="head">{{ $business->translations->first()?->name ?? '' }}</h6>

                                            <div class="d-none">
                                                <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                                            </div>
                                        </div>

                                        {{-- Your original rating HTML with dynamic values --}}
                                        <div class="tp-btm d-flex">
                                            <div class="inn_ul d-flex align-items-center">
                                                <li>{{ $rating }}</li>
                                                <li>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($rating >= $i)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @elseif ($rating >= $i - 0.5)
                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-warning"></i>
                                                        @endif
                                                    @endfor
                                                </li>
                                            </div>
                                            <div class="rate_box">
                                                ({{ $ratingCount }})
                                            </div>
                                        </div>

                                        <div class="sftwre-alt-sftwre-alt-btn mt-2">
                                            <a href="{{ $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}"
                                                class="btn-orng cta cta_orange d-flex align-items-center justify-content-center fw_500">
                                                Visit website
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- review tabination -->
                <div class="crm_review_box review_sec compari_review" data-aos="fade-up" data-aos-duration="1000">
                    <nav class="d-flex">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link  active" id="nav-home-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                aria-selected="true">All Reviews</button>
                            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                                aria-selected="false" tabindex="-1">Our
                                Reviews</button>
                            <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact"
                                aria-selected="false" tabindex="-1">Trustpilot Reviews</button>
                        </div>
                        <div class="selct_box">
                            <label for="rating-select">Sort by:</label>
                            <select id="rating-select" name="rating">
                                <option value="best">Best Rating</option>
                                <option value="high-to-low">High to Low</option>
                                <option value="low-to-high">Low to High</option>
                                <option value="most-reviewed">Most Reviewed</option>
                            </select>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent" data-aos="fade-up" data-aos-duration="1000">
                        <div class="tab-pane fade active show" id="nav-home" role="tabpanel"
                            aria-labelledby="nav-home-tab">
                            <!-- all reviews -->
                            <div class="comparison-reviews">
                                <div class="row">
                                    @foreach($businesses as $business)
                                        <div class="col-lg-6 col-md-6">
                                            <div class="compari-pack">
                                                @if($business->reviews && $business->reviews->count() > 0)
                                                    @foreach($business->reviews->take(3) as $review)
                                                        <div class="review_detl populr-alternative" id="review-{{ $review->id }}" style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px; position: relative;">
                                                            
                                                            @if($review->created_at)
                                                                <div class="review-actions-top-right" style="position: absolute; top: 24px; right: 24px;">
                                                                    <span style="font-size: 13px; color: #777; font-weight: 500;">{{ $review->created_at->diffForHumans() }}</span>
                                                                </div>
                                                            @endif

                                                            <div class="reviw_hd" style="margin-bottom: 16px; border-bottom: none; padding-bottom: 0;">
                                                                <div class="ans_lft" style="display: flex; gap: 12px; align-items: flex-start;">
                                                                    <div class="asn-img" style="flex-shrink: 0;">
                                                                        @if ($review->user && $review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                                                                            <img src="{{ asset($review->user->profile_image) }}"
                                                                                class="img-fluid profile-circle"
                                                                                style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                                                                alt="User Image">
                                                                        @else
                                                                            <div class="profile-circle" style="width: 48px; height: 48px; border-radius: 50%; background-color: #003f7d; display: flex; align-items: center; justify-content: center;">
                                                                                <span style="color: white; font-weight: bold; font-size: 20px;">
                                                                                    @if ($review->user && $review->user->user_type === 'admin')
                                                                                        {{ strtoupper(substr($review->public_name ?? 'P', 0, 1)) }}
                                                                                    @else
                                                                                        {{ strtoupper(substr($review->user->first_name ?? ($review->user->name ?? 'A'), 0, 1)) }}
                                                                                    @endif
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="asn-rating" style="display: flex; flex-direction: column; gap: 2px;">
                                                                        <h6 style="font-size: 15px; font-weight: 600; margin: 0; color: #1e3050;">
                                                                            @if ($review->user && $review->user->user_type === 'admin')
                                                                                {{ $review->public_name ?? 'Public' }}
                                                                            @else
                                                                                {{ $review->user ? $review->user->displayName() : ($review->user->name ?? 'Anonymous User') }}
                                                                            @endif
                                                                        </h6>
                                                                        @if($review->user && $review->user->job_title)
                                                                            <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight: 500;">{{ $review->user->job_title }}</p>
                                                                        @endif
                                                                        @if($review->user && $review->user->company_size)
                                                                            <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight: 500;">{{ static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="review_text size18" style="padding-right: 0;">
                                                                @php
                                                                    $revTitle = $review->translations?->first()?->title;
                                                                @endphp
                                                                @if($revTitle)
                                                                    <h5 class="size22 big-bld" style="font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 12px;">
                                                                        {{ $revTitle }}
                                                                    </h5>
                                                                @endif
                                                                
                                                                <div class="rating light" style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                                                    <div class="inn_ul">
                                                                        <div class="rating-stars" style="display: flex; gap: 2px;">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($review->rating))
                                                                                    <i class="fas fa-star text-warning" style="font-size: 14px;"></i>
                                                                                @elseif ($i - 0.5 <= $review->rating)
                                                                                    <i class="fas fa-star-half-alt text-warning" style="font-size: 14px;"></i>
                                                                                @else
                                                                                    <i class="far fa-star text-warning" style="font-size: 14px;"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div style="color: #444; line-height: 1.6; font-size: 14px; margin-bottom: 0;">
                                                                    {!! nl2br(e(strip_tags($review->translations?->first()?->description ?? $review->comment ?? 'No Description Available'))) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="review_detl" style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                                                        <p style="color: #666; margin: 0; font-size: 14px;">This business hasn't received any reviews yet. Be the first to share your experience!</p>
                                                    </div>
                                                @endif
                                            </div>
                                             <div class="view-review">
                                                 @php
                                                     $curLang = getCurrentLocale();
                                                     $lObj = \App\Models\Language::where('lang_code', $curLang)->first();
                                                     $revSlug = !empty($lObj->reviews_slug) ? $lObj->reviews_slug : 'reviews';
                                                     $bizSlug = $business->translations->where('lang_id', getCurrentLanguageID())->first()?->slug ?? $business->translations->first()?->slug ?? '';
                                                 @endphp
                                                 <a href="{{ route('ReviewShow', ['locale' => $curLang, 'slug' => $bizSlug, 'reviews_slug' => $revSlug]) }}" class="cta cta_white">View All Reviews</a>
                                             </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <!-- our reviews -->
                            <div class="comparison-reviews_tab">
                                <div class="row">
                                    @foreach($businesses as $business)
                                        <div class="col-lg-6 col-md-6">
                                            <div class="compari-pack_tab">
                                                @php
                                                    $internalReviews = $business->reviews->take(2);
                                                @endphp
                                                @if($internalReviews->count() > 0)
                                                    @foreach($internalReviews as $review)
                                                        <div class="review_detl populr-alternative" id="review-{{ $review->id }}" style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px; position: relative;">
                                                            
                                                            @if($review->created_at)
                                                                <div class="review-actions-top-right" style="position: absolute; top: 24px; right: 24px;">
                                                                    <span style="font-size: 13px; color: #777; font-weight: 500;">{{ $review->created_at->diffForHumans() }}</span>
                                                                </div>
                                                            @endif

                                                            <div class="reviw_hd" style="margin-bottom: 16px; border-bottom: none; padding-bottom: 0;">
                                                                <div class="ans_lft" style="display: flex; gap: 12px; align-items: flex-start;">
                                                                    <div class="asn-img" style="flex-shrink: 0;">
                                                                        @if ($review->user && $review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                                                                            <img src="{{ asset($review->user->profile_image) }}"
                                                                                class="img-fluid profile-circle"
                                                                                style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                                                                alt="User Image">
                                                                        @else
                                                                            <div class="profile-circle" style="width: 48px; height: 48px; border-radius: 50%; background-color: #003f7d; display: flex; align-items: center; justify-content: center;">
                                                                                <span style="color: white; font-weight: bold; font-size: 20px;">
                                                                                    @if ($review->user && $review->user->user_type === 'admin')
                                                                                        {{ strtoupper(substr($review->public_name ?? 'P', 0, 1)) }}
                                                                                    @else
                                                                                        {{ strtoupper(substr($review->user->first_name ?? ($review->user->name ?? 'A'), 0, 1)) }}
                                                                                    @endif
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="asn-rating" style="display: flex; flex-direction: column; gap: 2px;">
                                                                        <h6 style="font-size: 15px; font-weight: 600; margin: 0; color: #1e3050;">
                                                                            @if ($review->user && $review->user->user_type === 'admin')
                                                                                {{ $review->public_name ?? 'Public' }}
                                                                            @else
                                                                                {{ $review->user ? $review->user->displayName() : ($review->user->name ?? 'User') }}
                                                                            @endif
                                                                        </h6>
                                                                        @if($review->user && $review->user->job_title)
                                                                            <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight: 500;">{{ $review->user->job_title }}</p>
                                                                        @else
                                                                            <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight: 500;">Verified User</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="review_text size18" style="padding-right: 0;">
                                                                @php
                                                                    $revTitle = $review->translations?->first()?->title;
                                                                @endphp
                                                                @if($revTitle)
                                                                    <h5 class="size22 big-bld" style="font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 12px;">
                                                                        {{ $revTitle }}
                                                                    </h5>
                                                                @endif

                                                                <div class="rating light" style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                                                    <div class="inn_ul">
                                                                        <div class="rating-stars" style="display: flex; gap: 2px;">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($review->rating))
                                                                                    <i class="fas fa-star text-warning" style="font-size: 14px;"></i>
                                                                                @elseif ($i - 0.5 <= $review->rating)
                                                                                    <i class="fas fa-star-half-alt text-warning" style="font-size: 14px;"></i>
                                                                                @else
                                                                                    <i class="far fa-star text-warning" style="font-size: 14px;"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div style="color: #444; line-height: 1.6; font-size: 14px; margin-bottom: 0;">
                                                                    {!! nl2br(e(strip_tags($review->translations?->first()?->description ?? $review->comment ?? 'Great experience with this business.'))) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="review_detl" style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                                                        <p style="color: #666; margin: 0; font-size: 14px;">No internal reviews available for {{ $business->translations->first()?->name ?? 'this business' }}.</p>
                                                    </div>
                                                @endif
                                            </div>
                                             <div class="view-review">
                                                 @php
                                                     $curLang = getCurrentLocale();
                                                     $lObj = \App\Models\Language::where('lang_code', $curLang)->first();
                                                     $revSlug = !empty($lObj->reviews_slug) ? $lObj->reviews_slug : 'reviews';
                                                     $bizSlug = $business->translations->where('lang_id', getCurrentLanguageID())->first()?->slug ?? $business->translations->first()?->slug ?? '';
                                                 @endphp
                                                 <a href="{{ route('ReviewShow', ['locale' => $curLang, 'slug' => $bizSlug, 'reviews_slug' => $revSlug]) }}" class="cta cta_white">View Reviews</a>
                                             </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                            <!-- trustpilot reviews -->
                            <div class="comparison-reviews_tab">
                                <div class="row">
                                    @foreach($businesses as $business)
                                        <div class="col-lg-6 col-md-6">
                                            <div class="compari-pack_tab">
                                                @php
                                                    $trustpilotReviews = $business->reviews->where('source', 'trustpilot')->take(2);
                                                @endphp
                                                @if($trustpilotReviews->count() > 0)
                                                    @foreach($trustpilotReviews as $review)
                                                        <div class="review_detl populr-alternative" id="review-{{ $review->id }}" style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px; position: relative;">
                                                            
                                                            @if($review->created_at)
                                                                <div class="review-actions-top-right" style="position: absolute; top: 24px; right: 24px;">
                                                                    <span style="font-size: 13px; color: #777; font-weight: 500;">{{ $review->created_at->diffForHumans() }}</span>
                                                                </div>
                                                            @endif

                                                            <div class="reviw_hd" style="margin-bottom: 16px; border-bottom: none; padding-bottom: 0;">
                                                                <div class="ans_lft" style="display: flex; gap: 12px; align-items: flex-start;">
                                                                    <div class="asn-img" style="flex-shrink: 0;">
                                                                        @if ($review->user && $review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                                                                            <img src="{{ asset($review->user->profile_image) }}"
                                                                                class="img-fluid profile-circle"
                                                                                style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                                                                alt="User Image">
                                                                        @else
                                                                            <div class="profile-circle" style="width: 48px; height: 48px; border-radius: 50%; background-color: #003f7d; display: flex; align-items: center; justify-content: center;">
                                                                                <span style="color: white; font-weight: bold; font-size: 20px;">
                                                                                    {{ strtoupper(substr($review->user->name ?? 'T', 0, 1)) }}
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="asn-rating" style="display: flex; flex-direction: column; gap: 2px;">
                                                                        <h6 style="font-size: 15px; font-weight: 600; margin: 0; color: #1e3050;">
                                                                            {{ $review->user->name ?? 'Trustpilot User' }}
                                                                        </h6>
                                                                        <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight: 500;">Trustpilot Review</p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="review_text size18" style="padding-right: 0;">
                                                                @php
                                                                    $revTitle = $review->translations?->first()?->title;
                                                                @endphp
                                                                @if($revTitle)
                                                                    <h5 class="size22 big-bld" style="font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 12px;">
                                                                        {{ $revTitle }}
                                                                    </h5>
                                                                @endif

                                                                <div class="rating light" style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                                                    <div class="inn_ul">
                                                                        <div class="rating-stars" style="display: flex; gap: 2px;">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($review->rating))
                                                                                    <i class="fas fa-star text-warning" style="font-size: 14px;"></i>
                                                                                @elseif ($i - 0.5 <= $review->rating)
                                                                                    <i class="fas fa-star-half-alt text-warning" style="font-size: 14px;"></i>
                                                                                @else
                                                                                    <i class="far fa-star text-warning" style="font-size: 14px;"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div style="color: #444; line-height: 1.6; font-size: 14px; margin-bottom: 0;">
                                                                    {!! nl2br(e(strip_tags($review->translations?->first()?->description ?? $review->comment ?? 'Verified Trustpilot review.'))) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="review_detl" style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                                                        <p style="color: #666; margin: 0; font-size: 14px;">No Trustpilot reviews available for {{ $business->translations->first()?->name ?? 'this business' }}.</p>
                                                    </div>
                                                @endif
                                            </div>
                                             <div class="view-review">
                                                 @php
                                                     $curLang = getCurrentLocale();
                                                     $lObj = \App\Models\Language::where('lang_code', $curLang)->first();
                                                     $revSlug = !empty($lObj->reviews_slug) ? $lObj->reviews_slug : 'reviews';
                                                     $bizSlug = $business->translations->where('lang_id', getCurrentLanguageID())->first()?->slug ?? $business->translations->first()?->slug ?? '';
                                                 @endphp
                                                 <a href="{{ route('ReviewShow', ['locale' => $curLang, 'slug' => $bizSlug, 'reviews_slug' => $revSlug]) }}" class="cta cta_white">View Reviews</a>
                                             </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </section>

    <section class="subs_sec light pt_120 ">
        {{-- <div class="container">
            <div class="subs_content" data-aos="fade-up" data-aos-duration="1000">
                <h2>Send this comparison chart to my inbox</h2>
                <div class="mail_field">
                    <div class="email_box">
                        <input type="email" id="email" name="email" placeholder="Email Address*">
                    </div>
                    <div class="accor-btn sbs_bttn">
                        <a href="" class="cta cta_white">Get The Comparison</a>
                    </div>
                </div>
                <p>By proceeding, you agree to our <span class="big-bld">Terms Of Use</span> and <span
                        class="big-bld">Privacy Policy</span></p>
            </div>
        </div> --}}
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
