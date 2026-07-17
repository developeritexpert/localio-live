@extends('user_layout.master')
@section('content')
    <section class="inner_banner_sec cmpari_inner">
        <div class="container">
            <div class="inner_banr_content">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        {{-- <li class="breadcrumb-item"><a href="#">Automotive</a></li> --}}
                        <li class="breadcrumb-item active" aria-current="page">
                            @if (count($businesses) >= 2)
                                {{ $businesses[0]->translations->first()->name ?? '' }} vs {{ $businesses[1]->translations->first()->name ?? '' }}
                            @else
                                Businesses Comparison
                            @endif
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <!-- section product comparison -->
    <section class="product_comp_sec p_120 light">
        <div class="container">
            <div class="asn_dv xeo_dv" data-aos="fade-up" data-aos-duration="1000">
                <div class="xeo_lft">
                    <h2>
                        @if (count($businesses) >= 2)
                            {{ $businesses[0]->translations->first()->name ?? '' }} vs {{ $businesses[1]->translations->first()->name ?? '' }}:
                            Which is a better fit?
                        @else
                            Product Comparison: Which is a better fit?
                        @endif
                    </h2>
                </div>
                <div class="ans_ryt">
                    <div class="site_vsit">
                        <x-social-icon/>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center pro-row-gp versus-row" data-aos="fade-up" data-aos-duration="1000">
                <div class="col-lg-9">
                    <div class="asn_tprw">
                        <div class="pdc_box ">
                            @foreach ($businesses as $index => $business)
                                @if ($index > 0)
                                    <div class="versus-box">
                                        <p class="d-flex m-0">vs</p>
                                    </div>
                                @endif
                                <div class="pdc_choice">
                                    <div class="auto-choice-hd">
                                        <div class="inn_sl_hed">
                                            <div class="sli_img choice_img">
                                                <img class="slider_img" src="{{ asset($business->icon_id) }}"
                                                    alt="{{ $business->translations->first()->name ?? '' }}">
                                            </div>
                                            <div class="sl_h">
                                                <div class="inn_h d-flex align-items-center">
                                                    <h6 class="head">{{ $business->translations->first()->name ?? '' }}</h6>
                                                    <div>
                                                        <livewire:wishlist :product-id="$business->id"
                                                            :wire:key="'wishlist-'.$business->id" />
                                                    </div>
                                                </div>
                                                <div class="tp-btm d-flex">
                                                    <div class="inn_ul d-flex align-items-center gap-1 list-unstyled m-0">
                                                        @php
                                                            $rating = round($business->reviews->avg('rating'), 1);
                                                            $ratingCount = $business->reviews->count();
                                                        @endphp

                                                        <li>{{ $ratingCount > 0 ? number_format($rating, 1) : '0.0' }}</li>
                                                        <li class="d-flex">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($rating >= $i)
                                                                    <i class="fas fa-star text-warning me-1"></i>
                                                                @elseif ($rating >= $i - 0.5)
                                                                    <i class="fas fa-star-half-alt text-warning me-1"></i>
                                                                @else
                                                                    <i class="far fa-star text-warning me-1"></i>
                                                                @endif
                                                            @endfor
                                                        </li>
                                                        <li><i class="fa-solid fa-angle-down"></i></li>
                                                    </div>
                                                    <div class="rate_box ms-3">
                                                        {{ $ratingCount }} Reviews{{ $ratingCount !== 1 ? 's' : '' }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="auto-choice-btn fit-btn">
                                            <a href="{{ $business->permanent_url ?? $business->affiliate_link ?? 'javascript:void(0)' }}"
                                                class="cta cta_orange d-flex align-items-center justify-content-center">
                                                Visit website
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                            </a>
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

            <div class="row localio-brkdwn">
                <div class="col-lg-9">
                    <!-- localio breakdown -->
                    <div class="main_feture compari-feature" data-aos="fade-up" data-aos-duration="1000">
                        @foreach ($businesses as $index => $business)
                            <div class="feture_box black-hding">
                                <h6 class="size22 big-bld">{{ $business->translations->first()->name ?? 'Business' }} Review Breakdown
                                </h6>
                                <ul>
                                    <li class="d-flex justify-content-between">
                                        <span class="lyt-text">Ease of Use</span>
                                        <div class="prgs_br">
                                            @php
                                                $easeOfUse = $business->reviews->avg('ease_of_use_rating') ?? 0;
                                                $easeOfUsePercent = $easeOfUse * 20;
                                            @endphp
                                            <progress class="progress-bar" id="progress-bar-1-{{ $index }}"
                                                value="{{ $easeOfUsePercent }}"
                                                max="100">{{ $easeOfUsePercent }}%</progress>
                                            <output
                                                id="progress-output-1-{{ $index }}">{{ number_format($easeOfUse, 1) }}/5</output>
                                        </div>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="lyt-text">Customer Service</span>
                                        <div class="prgs_br">
                                            @php
                                                $customerService = $business->reviews->avg('customer_service_rating') ?? 0;
                                                $customerServicePercent = $customerService * 20;
                                            @endphp
                                            <progress class="progress-bar" id="progress-bar-2-{{ $index }}"
                                                value="{{ $customerServicePercent }}"
                                                max="100">{{ $customerServicePercent }}%</progress>
                                            <output
                                                id="progress-output-2-{{ $index }}">{{ number_format($customerService, 1) }}/5</output>
                                        </div>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="lyt-text">Exclusive Service</span>
                                        <div class="prgs_br">
                                            @php
                                                $exclusiveService = $business->reviews->avg('exclusive_service_rating') ?? 0;
                                                $exclusiveServicePercent = $exclusiveService * 20;
                                            @endphp
                                            <progress class="progress-bar" id="progress-bar-3-{{ $index }}"
                                                value="{{ $exclusiveServicePercent }}"
                                                max="100">{{ $exclusiveServicePercent }}%</progress>
                                            <output
                                                id="progress-output-3-{{ $index }}">{{ number_format($exclusiveService, 1) }}/5</output>
                                        </div>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="lyt-text">Value for Money</span>
                                        <div class="prgs_br">
                                            @php
                                                $valueForMoney = $business->reviews->avg('value_for_money_rating') ?? 0;
                                                $valueForMoneyPercent = $valueForMoney * 20;
                                            @endphp
                                            <progress class="progress-bar" id="progress-bar-4-{{ $index }}"
                                                value="{{ $valueForMoneyPercent }}"
                                                max="100">{{ $valueForMoneyPercent }}%</progress>
                                            <output
                                                id="progress-output-4-{{ $index }}">{{ number_format($valueForMoney, 1) }}/5</output>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        @endforeach
                    </div>

                    <!-- key features div -->
                    <div class="cpa_rw cpa_bg light size18 pro-row-gp d-flex" data-aos="fade-up" data-aos-duration="1000">
                        @foreach ($businesses as $business)
                            <div class="cpa_bg_div {{ !$loop->first ? 'p_left' : '' }}">
                                <div class="tp_box cpa_tp_box">
                                    <h6 class="big-bld">Key Features</h6>
                                    <ul class="list-unstyled">
                                        @if($business->features && $business->features->count() > 0)
                                            @foreach($business->features as $feature)
                                                <li class="d-flex align-items-center">
                                                    <span><img src="{{ asset('front/img/pros-tick.svg') }}" alt=""></span>
                                                    <span>{{ $feature->translations->first()->name ?? 'Feature Name' }}</span>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="d-flex align-items-center">
                                                <span><img src="{{ asset('front/img/pros-tick.svg') }}" alt=""></span>
                                                <span>No features available</span>
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
                                    <h6>What is {{ $business->translations->first()->name ?? 'Product' }}?</h6>
                                    <div class="xro_text">
                                        <p>{!! $business->translations->first()->description ?? 'No description available.' !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- price start from -->
                    <div class="prc_dv" data-aos="fade-up" data-aos-duration="1000">
                        <div class="hd_text">
                            <h2>Price Starts From</h2>
                        </div>
                        <div class="prc_bx">
                            @foreach ($businesses as $business)
                                <div class="prc_contnt">
                                    <div class="sli_img">
                                        <img class="slider_img" src="{{ asset($business->icon_id) }}"
                                            alt="{{ $business->translations->first()->name ?? '' }}">
                                    </div>
                                    <div class="inn_h d-flex align-items-center">
                                        <h6 class="head">{{ $business->translations->first()->name ?? '' }}</h6>
                                        <div>
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
                                        <a href="{{ $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}" class="cta cta_orange fw_500">
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
                                            <h6 class="head">{{ $business->translations->first()->name }}</h6>

                                            <div>
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
                                                {{ $ratingCount }} Reviews{{ $ratingCount !== 1 ? 's' : '' }}
                                            </div>
                                        </div>

                                        <div class="sftwre-alt-btn mt-2">
                                            <a href="{{ $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}"
                                                class="cta cta_orange d-flex align-items-center justify-content-center fw_500">
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
                            <button class="nav-link cta active" id="nav-home-tab" data-bs-toggle="tab"
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
                                                        <div class="compari_pck_innr">
                                                            <div class="compari_card_top">
                                                                <div class="compari-img-txt d-flex">
                                                                    <div class="compari-crd-img">
                                                                        <img src="{{ asset('front/img/john-plus.png') }}" alt="">
                                                                    </div>
                                                                    <div class="compari-txt">
                                                                        <h6>{{ $review->user->first_name ?? 'Anonymous User' }}</h6>
                                                                        <p class="m-0">{{ $review->user->position ?? 'Customer' }}</p>
                                                                        <div class="rating">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($review->rating >= $i)
                                                                                    <i class="fas fa-star text-warning"></i>
                                                                                @elseif ($review->rating >= $i - 0.5)
                                                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                                                @else
                                                                                    <i class="far fa-star text-warning"></i>
                                                                                @endif
                                                                            @endfor
                                                                            <span class="ms-2">{{ number_format($review->rating, 1) }}/5</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <p class="compari_p">"{{ $review->comment ?? 'Great product with excellent features and customer service.' }}"</p>
                                                                <a href="javascript:void(0)" class="btn-see-full">See full review</a>
                                                                @if($loop->last && $business->reviews->count() > 3)
                                                                    <div class="compari_tabi">
                                                                        <span>{{ $loop->iteration }}/{{ $business->reviews->count() }}</span>
                                                                        <a href="javascript:void(0)" class="">Next <i class="fa-solid fa-chevron-right"></i></a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="compari_pck_innr">
                                                        <div class="compari_card_top">
                                                            <div class="compari-img-txt d-flex">
                                                                <div class="compari-crd-img">
                                                                    <img src="{{ asset('front/img/john-plus.png') }}" alt="">
                                                                </div>
                                                                <div class="compari-txt">
                                                                    <h6>No Reviews Yet</h6>
                                                                    <p class="m-0">Be the first to review</p>
                                                                </div>
                                                            </div>
                                                            <p class="compari_p">"This business hasn't received any reviews yet. Be the first to share your experience!"</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="view-review">
                                                <a href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}" class="cta cta_white">View All Reviews</a>
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
                                                    $ourReviews = $business->reviews->take(2);
                                                @endphp
                                                @if($ourReviews->count() > 0)
                                                    @foreach($ourReviews as $review)
                                                        <div class="compari_pck_innr">
                                                            <div class="compari_card_top">
                                                                <div class="compari-img-txt d-flex">
                                                                    <div class="compari-crd-img">
                                                                        <img src="{{ asset('front/img/john-plus.png') }}" alt="">
                                                                    </div>
                                                                    <div class="compari-txt">
                                                                        <h6>{{ $review->user->name ?? 'Anonymous User' }}</h6>
                                                                        <p class="m-0">{{ $review->user->position ?? 'Customer' }}</p>
                                                                    </div>
                                                                </div>
                                                                <p class="compari_p">"{{ $review->comment ?? 'Great experience with this business.' }}"</p>
                                                                <a href="javascript:void(0)" class="btn-see-full">See full review</a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="compari_pck_innr">
                                                        <div class="compari_card_top">
                                                            <p class="compari_p">No internal reviews available for {{ $business->translations->first()->name ?? 'this business' }}.</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="view-review">
                                                <a href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}" class="cta cta_white">View Reviews</a>
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
                                                        <div class="compari_pck_innr">
                                                            <div class="compari_card_top">
                                                                <div class="compari-img-txt d-flex">
                                                                    <div class="compari-crd-img">
                                                                        <img src="{{ asset('front/img/john-plus.png') }}" alt="">
                                                                    </div>
                                                                    <div class="compari-txt">
                                                                        <h6>{{ $review->user->name ?? 'Trustpilot User' }}</h6>
                                                                        <p class="m-0">Trustpilot Review</p>
                                                                    </div>
                                                                </div>
                                                                <p class="compari_p">"{{ $review->comment ?? 'Verified Trustpilot review.' }}"</p>
                                                                <a href="javascript:void(0)" class="btn-see-full">See full review</a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="compari_pck_innr">
                                                        <div class="compari_card_top">
                                                            <p class="compari_p">No Trustpilot reviews available for {{ $business->translations->first()->name ?? 'this business' }}.</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="view-review">
                                                <a href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}" class="cta cta_white">View Reviews</a>
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

    <section class="subs_sec light p_120 ">
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
