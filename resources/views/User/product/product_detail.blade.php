@extends('user_layout.master')
@section('body_class', 'product-page-body')

@section('meta_title', isset($business->translations->first()->name) && isset($business->translations->first()->name) ?
    $business->translations->first()->name : 'Products')
@section('content')
    @livewire('add-review')
    <style>
               #section1 .asn_dv {
                padding-bottom: 0;
               }
            .Tab-outerlnk.container-fluid {
                padding: 0;
            }
            .asn_dv .Tab-outerlnk #table-of-content ul li a:hover {
                background: #06498b0d;
            }
            .asan-slider.asan-slider-btm.slider-nav {
    padding: 0;
}

.asan-slider.asan-slider-btm.slider-nav .slick-list {
    width: 100%;
}

.asan-slider.asan-slider-btm.slider-nav .slick-track {
    display: flex;
}

.asan-slider.asan-slider-btm.slider-nav .slick-track .slick-slide.slick-active {
    margin: 10px 5px 0 !important;
}
.new-visit-anc .cta.cta_orange {
    font-size: 13px;
    padding: 12px 22px;
    width: 185px;
    justify-content: center;
}
.new-review-side .rate_box {
    font-size: 13px !important;
}
          @media (max-width: 1599px) {
                .asn_dv .Tab-outerlnk #table-of-content{
                    padding-inline: 15px !important;
                }
            }
            @media (max-width: 575px) {
             .frst_rw {
                    gap: 20px 0;
                }
                .new-visit-anc .cta.cta_orange {
                font-size: 12px;
                width: fit-content;
            }
            }
    </style>
    <div data-business-id="{{ $business->id }}">
        <section class="product_sec">
            <div class="inner_banner_sec">
                <div class="container-fluid" style="display: flex; justify-content: space-between;">
                    <div class="inner_banr_content">
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);"
                                        onclick="changeCategory('{{ $business->category->translation()->first()->slug }}')"
                                        style="color: inherit; transition: none;" onmouseover="this.style.color='#f26522'"
                                        onmouseout="this.style.color=''">
                                        {{ optional($business->category->translation)->name }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $business->translations->first()->name }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="inside_sec_text">
                        <x-social-icon />
                    </div>
                </div>
            </div>

        </section>

        <!-- Products Section -->
        <section class="asn_main_sec asn_main_sec_2" id="section1">
            <div class="asn_dv">
                <div class="container-fluid">
                    <div class="asn_dv_contnt ">
                        <div class="asn-img">
                            <img src="{{ asset($business->icon_id) }}" alt="{{ $business->translations->first()->name }}">
                        </div>
                        <div class="div_prent_ever">



                            <div class="row frst_rw align-items-center">
                                <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="ans_lft">
                                        <div class="asn-rating">
                                            <div class="an_lkd">
                                                <h1 style="color: #000;" class="mb-0 p-1">
                                                    {{ $business->translations->first()->name }} Review {{ $business->created_at->format('Y') }} </h1>

                                                {{-- <h6 style="color: #000;" class="mb-0 p-1">Review
                                                    {{ $business->created_at->format('Y') }}</h6> --}}

                                                <livewire:wishlist :product-id="$business->id"
                                                    :wire:key="'wishlist-'.$business->id" />

                                            </div>
                                            <p class="product-pricing">
                                                Pricing, Features, Alternatives & User Reviews
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="ans_ryt">
                                        <div class="site_vsit">
                                            <ul class="list-unstyled">
                                            </ul>
                                            <div class="top-pro-btn tp_visit new-visit-anc">
                                                <a href="{{ $business->getTrackedUrl() }}"
                                                    data-track="{{ json_encode([
                                                        'type' => 'click',
                                                        // 'product_id' => $product->id,
                                                        'business_id' => $business->id,
                                                        'action' => 'visit_website',
                                                        'label' => 'Visit Website',
                                                    ]) }}"
                                                    class="cta cta_orange d-flex align-items-center" target="_blank"
                                                    tabindex="0">
                                                    Visit
                                                    Website
                                                    <div class="right-arw">
                                                        <img src="{{ asset('front/img/right-arrw.svg') }}" alt="">
                                                    </div>
                                                </a>
                                            </div>

                                            {{-- Replace class add here --}}
                                            @php
                                            $ratingCount = $business->reviews->where('status', 'active')->count();
                                        @endphp
                                        <div class="right_bottom col-lg-6 hd_str new-review-side "
                                            @auth

                                            onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"

                                            @else
                                            onclick="window.location.href = '/login'" @endauth>
                                            {{-- Change Review  --}}
                                            <div class="inn_ul">
                                                <div class="rating-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= floor($averageRating))
                                                            <i class="fas fa-star text-warning"></i>
                                                        @elseif ($i - 0.5 <= $averageRating)
                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-warning"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="rate_box">
                                                {{ number_format($averageRating, 1) }} |
                                                @if ($ratingCount === 0)
                                                    0 Ratings
                                                @elseif ($ratingCount === 1)
                                                    1 Rating
                                                @else
                                                    {{ $ratingCount }} Reviews
                                                @endif
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="frst_re_2 row">
                                <div class="tp-btm d-flex col-lg-6">
                                    {{-- add under this class an_lkd --}}
                                    {{-- <p class="size20 m-0">
                                        Pricing, Features, Alternatives & User Reviews
                                    </p> --}}
                                </div>

                                {{-- replace this under the visit button top-pro-btn tp_visit  --}}

                                {{-- @php
                                    $ratingCount = $business->reviews->where('status', 'active')->count();
                                @endphp
                                <div class="right_bottom col-lg-6 hd_str"
                                    @auth

                                    onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"

                                    @else
                                    onclick="window.location.href = '/login'" @endauth>



                                    {{-- Change Review  --}}
                                    {{-- <div class="inn_ul">
                                        <div class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= floor($averageRating))
                                                    <i class="fas fa-star text-warning"></i>
                                                @elseif ($i - 0.5 <= $averageRating)
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="rate_box">
                                        {{ number_format($averageRating, 1) }} |
                                        @if ($ratingCount === 0)
                                            0 Ratings
                                        @elseif ($ratingCount === 1)
                                            1 Rating
                                        @else
                                            {{ $ratingCount }} Ratings
                                        @endif
                                    </div>
                                </div> --}}

                            </div>
                        </div>
                    </div>
                </div>
                 {{-- Added the change Stacture content table code --}}
                 <div class="Tab-outerlnk container-fluid">
                    <div class="inner_table2">

                        @php

                            $name = $business->translations->first()->name ?? 'This Business';

                            // Fixed dynamic top content
                            $staticBottomSections = [
                                // ['id' => 'section9', 'label' => "Software like"],
                                ['id' => 'section15', 'label' => 'FAQ'],
                                ['id' => 'section14', 'label' => "Reviews"],
                                ['id' => 'section16', 'label' => 'Inbox'],
                            ];

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
                                ['id' => 'section3', 'label' => "Pricing"],
                                // ['id' => 'section4', 'label' => "Pros & cons"],
                                ['id' => 'section5', 'label' => 'Alternatives'],
                                ['id' => 'features' , 'label' => 'Features'],
                                // ['id' => 'softweretopic', 'label' => 'Software Topic'],
                                ['id' => 'business-integration', 'label' => 'Integration']

                            ];

                            $tableOfContents = array_merge(
                                $staticTopSections,
                                //$dynamicTopics,
                                $staticBottomSections,
                            );
                        @endphp

                        <div class="inner_table2">
                            <div class="table_st">
                                <div id="table-of-content" class="feture_box p-3 shadow rounded bg-white bar-option"
                                    style="top: 90px; max-height: max-content; overflow-y: auto;">
                                    <ul class="list-unstyled toc-links small">
                                        @foreach ($tableOfContents as $i => $item)
                                            @php
                                                $isLastDynamic =
                                                    str_starts_with($item['id'], 'section') &&
                                                    (int) filter_var($item['id'], FILTER_SANITIZE_NUMBER_INT) ===
                                                        100 + count($dynamicTopics) - 1;
                                            @endphp

                                            <li class="py-1 {{ $isLastDynamic ? 'mb-0' : '' }}">
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
                </div>
                {{-- End this table content Stacture code --}}

            </div>
           
        </section>


              <section class="revie_img_sec">
                   <div class="container">
                       <div class="image_revie_inr">
                            <div class="is-asana-wrp imges_left_sde" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="row sld_rw">
                                        <div class="col-lg-12">
                                            <div class="is-asana-lft">
                                                <h2>What is {{ $business->translations->first()->name }}</h2>
                                                <div class="is_text">
                                                    {!! $business->translations->first()->description !!}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="col-lg-12">
                                    <div class="is-asana-rgt">
                                        <div class="is-asan-slider">
                                            <div class="asan-slider slider-for">
                                                <div class="asan-slider-inr"><img
                                                        src="{{ asset('front/img/video-img.png') }}" alt="">
                                                    </div>
                                                    <div class="asan-slider-inr"><img
                                                            src="{{ asset('front/img/video-img3.png') }}" alt="">
                                                    </div>
                                                    <div class="asan-slider-inr"><img
                                                            src="{{ asset('front/img/video-img.png') }}" alt="">
                                                    </div>
                                                    <div class="asan-slider-inr"><img
                                                            src="{{ asset('front/img/video-img3.png') }}" alt="">
                                                    </div>
                                                    <div class="asan-slider-inr"><img
                                                            src="{{ asset('front/img/video-img.png') }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="asan-slider asan-slider-btm slider-nav">
                                                    <div><img src="{{ asset('front/img/small-video-img.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div><img src="{{ asset('front/img/sm-video-img3.png') }}"
                                                            alt=""></div>
                                                    <div><img src="{{ asset('front/img/small-video-img.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div><img src="{{ asset('front/img/sm-video-img3.png') }}"
                                                            alt=""></div>
                                                    <div><img src="{{ asset('front/img/small-video-img.png') }}"
                                                            alt="">
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    </div> --}}


                                        {{-- Business Image slider --}}
                                        @php
                                            // Safely decode the business_images
                                            $images = is_array($business->business_images)
                                                ? $business->business_images
                                                : json_decode($business->business_images ?? '[]', true);
                                        @endphp

                                        @if (!empty($images))
                                            <div class="col-lg-12">
                                                <div class="is-asana-rgt">
                                                    <div class="row is-asan-slider">
                                                        <!-- Main Slider -->
                                                        <div class="col-md-12 asan-slider slider-for">
                                                            @foreach ($images as $index => $image)
                                                                <div class="asan-slider-inr">
                                                                    <img src="{{ asset($image) }}"
                                                                        alt="Business Image {{ $index + 1 }}"
                                                                        style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px;">
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Thumbnail Slider -->
                                                        <div class="col-md-12 asan-slider asan-slider-btm slider-nav"
                                                            style="margin-top: 15px;">
                                                            @foreach ($images as $index => $image)
                                                                <div style="padding: 0 5px;">
                                                                    <img src="{{ asset($image) }}"
                                                                        alt="Thumbnail {{ $index + 1 }}"
                                                                        style="width: 150px; height: 100px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 2px solid transparent;"
                                                                        onmouseover="this.style.borderColor='#007bff'"
                                                                        onmouseout="this.style.borderColor='transparent'">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- End Business Images  --}}

                                    </div>
                                    <div class="lcl_text">
                                    <p class="sml_text">{{ static_text('localio_commissions_message') }}
                                        <a class="big-bld" type="button" onclick="openModal()">Learn more</a>
                                    </p>
                                </div>
                            </div>
                            <div class="thre_revi_rgt">
                                
                                @php
                                    $productBadgeLabel = static_text('product_badge_label') ?? 'Key Features';
                                    // dd($productBadgeLabel);
                                @endphp

                                {{-- changes here --}}
                                {{-- <div class="main_feture" style="--product-badge-label: '{{ addslashes($productBadgeLabel) }}';"> --}}
                                <div class="main_feture">
                                    <div class=" fetru_row d-flex justify-content-between" data-aos="fade-up" data-aos-duration="1000">
                                        @if (count($business->features) > 0)
                                            {{-- <div class="main_feature_lg">
                                                <div class="feture_box lft_check_box size18 position-relative">
                                                <span class="badge-label">{{ $productBadgeLabel }}</span>
                                                    <ul class="list-unstyled">
                                                        @foreach ($business->features as $feature)
                                                            <li class="d-flex align-items-center size18">
                                                                <div class="grn_chk">
                                                                    <img src="{{ asset('front/img/tick-img.png') }}">
                                                                </div>
                                                                <p class="m-0">{{ $feature->translations->first()->name ?? '' }}</p>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div> --}}
                                        @else
                                            <div class="main_feature_lg">
                                                <div class="feture_box lft_check_box size18">
                                                    <ul class="list-unstyled">
                                                        <li class="d-flex align-items-center size18">
                                                            <div class="grn_chk">
                                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                                            </div>
                                                            <p class="m-0">Free domain & SSL certificate</p>
                                                        </li>
                                                        <li class="d-flex align-items-center size18">
                                                            <div class="grn_chk">
                                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                                            </div>
                                                            <p class="m-0">Customizable automatic updates</p>
                                                        </li>
                                                        <li class="d-flex align-items-center size18">
                                                            <div class="grn_chk">
                                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                                            </div>
                                                            <p class="m-0">Scalable performance management</p>
                                                        </li>
                                                        <li class="d-flex align-items-center size18">
                                                            <div class="grn_chk">
                                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                                            </div>
                                                            <p class="m-0">DDoS & malware protection</p>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Replace This Feature Section side of the Free trial box --}}
                                        {{-- <div class="main_feature_lg">
                                            <div class="feture_box">
                                                <h2 class="size22 big-bld">Localio Review Breakdown</h2>
                                                <ul class="p-0 m-0">
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Ease of Use</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $easeOfUseAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $easeOfUseAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Customer Service</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $customerServiceAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $customerServiceAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Features</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $exclusiveFeatureAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Value for Money</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $valueForMoneyAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $valueForMoneyAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div> --}}

                                    

                                        {{-- Add Here Feature Localio Review Breakdown --}}
                                        <div class="main_feature_lg">
                                            <div class="feture_box">
                                                <h2 class="size22 big-bld">Localio Review Breakdown</h2>
                                                <ul class="p-0 m-0">
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Ease of Use</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $easeOfUseAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $easeOfUseAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Customer Service</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $customerServiceAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $customerServiceAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Features</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $exclusiveFeatureAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Value for Money</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $valueForMoneyAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $valueForMoneyAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        {{-- End Feature Localio Review Breakdown --}}
                                        <div class="innr_price_trail">

                                            <div class="main_feature_sm">
                                                <div class="feture_box str_prc_box">
                                                    <div class=r"src_box text-center">
                                                        <!-- Blue Dollarrr Circrle -->
                                                        <div class="price-icon">
                                                            <img src="{{ asset('ffront/img/ddmoney.svg') }}">
                                                        </div>
                                                        <h2 class="big_text mt-2">Starting Price</h2>
                                                        @if ($startingPrice)
                                                            <h3 class="blue-text">{{ $currency }}{{ $startingPrice }}<span
                                                                    class="big_text">/ {{ $timeUnit }}</span></h3>
                                                        @else
                                                            <h3 class="blue-text">{{ $currency }}9<span class="big_text">/
                                                                    {{ $timeUnit }}</span></h3>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="main_feature_sm">
                                                <div class="fre_trail feture_box size22">
                                                    <div class="grn_check_big">
                                                        <img src="{{ asset('front/img/new-grn-chk.svg') }}">
                                                    </div>
                                                    <h6 class="blue-text big-bld">Free Trial
                                                        Available
                                                    </h6>
                                                    <div class="accor-btn">
                                                        <a class="cta cta_white blue_t_org_btn"
                                                            data-track="{{ json_encode([
                                                                'type' => 'click',
                                                                // 'product_id' => $product->id,
                                                                'business_id' => $business->id,
                                                                'action' => 'claim_now',
                                                                'label' => 'Claim Now',
                                                            ]) }}"
                                                            type="button">Claim Now</a>
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

        
{{-- -------------------------------------------------------------- --}}

        <!-- Products Section -->

        <!-- Products Section -->

        <!-- section whatis -->
        <div class="con_table pb-0 all_sec_wrp" id="section2">
            <div class="container">

                <div class="row pt-0 ">
                    <div class="col-lg-12">
                        <div class="inner_table_1">
                            <section class="is-asana light">

                                
                            </section>

                            {{-- Added The New Section preview reviews --}}
                            <section class="reviews-section">
                                <div class="container py-5">
                                    <div class="swiper myReviewSlider">
                                        <div class="swiper-wrapper">
                                            @php
                                                $reviewsCount = $topReviews->count();
                                                $chunks = $topReviews->take(7)->chunk(2); // 2 reviews per slide
                                            @endphp

                                            @forelse($chunks as $chunk)
                                                <div class="swiper-slide">
                                                    <div class="d-flex">
                                                        @foreach($chunk as $review)
                                                            <div class="p-5 bg-white slide-bx rounded shadow-sm flex-fill me-3">
                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="me-3">
                                                                            @if ($review->user && $review->user->profile_image)
                                                                                <img src="{{ asset($review->user->profile_image) }}" alt="User" class="rounded-circle" width="50" height="50">
                                                                            @else
                                                                                <img src="{{ asset($default_image) }}" alt="Default" class="rounded-circle" width="50" height="50">
                                                                            @endif
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-1">{{ $review->user->first_name ?? 'Anonymous' }}</h6>
                                                                            <div class="rating">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if ($i <= floor($review->rating))
                                                                                        <i class="fas fa-star text-warning"></i>
                                                                                    @elseif ($i - 0.5 <= $review->rating)
                                                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                                                    @else
                                                                                        <i class="far fa-star text-warning"></i>
                                                                                    @endif
                                                                                @endfor
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <small>{{ $review->created_at->diffForHumans() }}</small>
                                                                </div>
                                                                <p class="fw-bold mb-2">{{ $review->translations->first()->title ?? 'Review' }}</p>
                                                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($review->translations->first()->description ?? 'No Description'), 120) }}</p>
                                                            </div>
                                                        @endforeach

                                                        @if($chunk->count() == 1)
                                                            <!-- Fill empty space if only 1 review in chunk -->
                                                            <div class="p-4 bg-white rounded shadow-sm flex-fill" style="display: none;"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="swiper-slide text-center">
                                                    <p>No reviews yet.</p>
                                                </div>
                                            @endforelse
                                        </div>

                                        <!-- Navigation -->
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-button-next"></div>
                                    </div>

                                    {{-- <div class="text-center mt-4">
                                        <a href="#allReviewsSection" class="btn btn-dark">View All Reviews</a>
                                    </div> --}}

                                    <div class="text-center mt-4 btm-btn">
                                        <a href="javascript:void(0);" id="scrollToReviews" class="cta cta_orange">View All Reviews</a>
                                    </div>

                                </div>
                            </section>
                            {{-- End preview reviews section  --}}

                            <!-- Product Pricing Section -->
                            <section class="product_pricing_sec p_50 pb-0" id="section3">

                                <div class="section_title text-center mb-4" data-aos="fade-up" data-aos-duration="1000">
                                    <h2> {{ $business->translations->first()->name }} Pricing Plans </h2>
                                </div>
                                <div class="pricing_plans_row d-flex align-items-center">
                                    <div class="row table_st_1">
                                        @if ($business->products->count() > 0)
                                            @foreach ($business->products as $product)
                                                @php
                                                    $lang_id = getCurrentLanguageID();
                                                    $productTranslation = $product->translations;
                                                    $price = $product->prices->first();

                                                    // Determine which price to display
                                                    $displayPrice = null;
                                                    $originalPrice = null;
                                                    $timeUnit = 'month';
                                                    $isDiscounted = false;

                                                    if ($price) {
                                                        $today = \Carbon\Carbon::now()->startOfDay();
                                                        $timeUnit = $price->time_unit;

                                                        // Check if discount is available and not expired
                                                        if (
                                                            $price->discount_price &&
                                                            (!$price->discount_expiration_date ||
                                                                \Carbon\Carbon::parse(
                                                                    $price->discount_expiration_date,
                                                                )->endOfDay() >= $today)
                                                        ) {
                                                            $displayPrice = $price->discount_price;
                                                            $originalPrice = $price->price;
                                                            $timeUnit =
                                                                $price->discount_time_units ?? $price->time_unit;
                                                            $isDiscounted = true;
                                                        }
                                                        // Check if renewal price is available
                                                        elseif ($price->renewal_price) {
                                                            $displayPrice = $price->renewal_price;
                                                            $timeUnit = $price->renewal_time_units ?? $price->time_unit;
                                                        }
                                                        // Use regular price as fallback
                                                        else {
                                                            $displayPrice = $price->price;
                                                        }

                                                        // Format time unit for display
                                                        $timeUnitDisplay = $timeUnit;
                                                        if ($timeUnit == 'one_time') {
                                                            $timeUnitDisplay = 'One-Time';
                                                        } elseif ($timeUnit == 'quarter') {
                                                            $timeUnitDisplay = '3 months';
                                                        }
                                                    }
                                                @endphp

                                                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up"
                                                    data-aos-duration="1000">
                                                    <div class="pricing_card">
                                                        <div class="pricing_header">
                                                            <h6>{{ $productTranslation ? $productTranslation->name : 'Plan' }}
                                                            </h6>
                                                        </div>
                                                        <div class="pricing_amount">
                                                            @if ($displayPrice !== null)
                                                                <h3 class="blue-text">
                                                                    {{ $price->currency }}{{ number_format($displayPrice, 2) }}
                                                                    @if ($timeUnitDisplay)
                                                                        <span class="big_text">/
                                                                            {{ $timeUnitDisplay }}</span>
                                                                    @endif
                                                                </h3>

                                                                @if ($isDiscounted && $originalPrice)
                                                                    <p class="original_price">
                                                                        <s>{{ $price->currency }}
                                                                            {{ number_format($originalPrice, 2) }}</s>
                                                                        <span class="discount_badge">Sale</span>
                                                                    </p>
                                                                @endif

                                                                @if ($price->additional_info)
                                                                    <p class="additional_info">
                                                                        {{ $price->additional_info }}
                                                                    </p>
                                                                @endif
                                                            @else
                                                                <h3 class="blue-text">Contact for Pricing</h3>
                                                            @endif
                                                        </div>
                                                        @if ($isDiscounted && $price->discount_expiration_date)
                                                            <div class="discount_timer">
                                                                <p>Offer ends:
                                                                    {{ \Carbon\Carbon::parse($price->discount_expiration_date)->format('M d, Y') }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                        <div class="pricing_action d-flex justify-content-center">
                                                            <a href="{{ $product->product_link ?? ($business->affiliate_link ?? '#') }}"
                                                                data-track="{{ json_encode([
                                                                    'type' => 'click',
                                                                    'product_id' => $product->id,
                                                                    'business_id' => $business->id,
                                                                    'action' => 'try_for_free',
                                                                    'label' => 'Try for free',
                                                                ]) }}"
                                                                target="_blank" class="cta cta_orange"
                                                                style="  border: none;">
                                                                Try for free
                                                                <span class="right-arw">
                                                                    <img src="{{ asset('front/img/right-arrw.svg') }}"
                                                                        alt="">
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12 text-center">
                                                <p>No pricing plans available for this product.</p>
                                            </div>
                                        @endif
                                    </div>

                                </div>

                                <div class="lcl_text mt-4">
                                    <p class="sml_text">* Prices may vary. Visit the official website for the most current
                                        pricing.</p>
                                </div>

                            </section>

                            {{-- pros and Cons --}}
                            @php
                                $prosList = collect();
                                $consList = collect();

                                foreach ($business->reviews as $review) {
                                    $translation = $review->translations->firstWhere('language_id', $lang_id);
                                    if ($translation) {
                                        // Assuming pros and cons are comma-separated strings or arrays
                                        $prosList = $prosList->merge(
                                            array_filter(array_map('trim', explode(',', $translation->pros))),
                                        );
                                        $consList = $consList->merge(
                                            array_filter(array_map('trim', explode(',', $translation->cons))),
                                        );
                                    }
                                }

                                $uniquePros = $prosList->unique()->take(5); // Limit for layout
                                $uniqueCons = $consList->unique()->take(5);
                            @endphp

                            <section class="pros-cons p_50 light" id="section4">
                                <div>
                                    <div style="margin-bottom: 30px;">
                                        <h2>{{ $business->translations->first()->name ?? 'Business' }} pros and cons</h2>

                                        <p style="color:; line-height: 1.6; margin-bottom: 20px;">
                                            To find out what users like and dislike about
                                            {{ $business->translations->first()->name ?? 'this business' }}, we analyzed
                                            the most common positive and negative aspects mentioned in user reviews.
                                        </p>
                                    </div>

                                    {{-- Two Column Layout --}}
                                    <div class="tikcrsotr">
                                        {{-- Pros --}}
                                        <div class="pros_ot">
                                            <h3>Things I like</h3>
                                            @foreach ($uniquePros as $pro)
                                                <div class="pr_pros">
                                                    <div class="innr_pr">
                                                        <div class="greenfonticon">
                                                            <i class="fa-solid fa-check"></i>
                                                        </div>
                                                        <div>
                                                            <h5>{{ ucfirst($pro) }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Cons --}}
                                        <div class="cons_ot">
                                            <h4>Things that could be improved</h4>

                                            @foreach ($uniqueCons as $con)
                                                <div class="pr_cons">
                                                    <div class="innr_pr">
                                                        <div class="redboxicon">
                                                            <i class="fa-solid fa-minus"></i>
                                                        </div>
                                                        <div>
                                                            <h5>{{ ucfirst($con) }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- section popular-altrnative -->
                            

                            {{-- Feature title and description section --}}
                            {{-- <section class="business-features light" id="features">
                                <div class="container" data-aos="fade-up" data-aos-duration="1000">
                                    <h2 class="text-feature">Features</h2>

                                    @if($business->features && $business->features->isNotEmpty())
                                     <div class="feature-section">
                                        <div class="row">

                                            @foreach($business->features as $feature)
                                                @php
                                                    $translation = $feature->translations->first();

                                                    // Feature-specific reviews
                                                    $featureReviews = $feature->reviews ?? collect();
                                                    $avgRating = $featureReviews->count() ? $featureReviews->avg('rating') : 0;

                                                    // Fallback to overall business rating
                                                    $averageRating = $avgRating ?: ($business->reviews->where('status', 'active')->count() ? $business->reviews->where('status', 'active')->avg('rating') : 0);
                                                    $ratingCount = $business->reviews->where('status', 'active')->count();
                                                @endphp

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="feature-card">
                                                        <!-- Feature title with rating -->
                                                        <div class="feature-texts">
                                                            <h6 class="feature-title-hd">{{ $translation->name ?? $feature->name ?? 'Unnamed Feature' }}</h6>
                                                            <p class="feature-content">{{ number_format($averageRating, 1) }} ({{ $ratingCount }})</p>
                                                        </div>

                                                        <!-- Feature description -->
                                                        @if(!empty($translation->description))
                                                            <p class="text-muted mb-3">{{ $translation->description }}</p>
                                                        @else
                                                            <p class="text-muted mb-3">No description available.</p>
                                                        @endif

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                     </div>
                                    @else
                                        <p class="text-center text-muted">No features have been added yet for this business.</p>
                                    @endif
                                </div>
                            </section> --}}

                            <section class="business-features light" id="features">
                                <div class="container" data-aos="fade-up" data-aos-duration="1000">
                                    <h2 class="text-feature">Features</h2>

                                    @if($business->features && $business->features->isNotEmpty())
                                        <div class="feature-section">
                                            <div class="row">
                                                @foreach($business->features as $feature)
                                                    @php
                                                        $translation = $feature->translations->first();
                                                        $featureReviews = $feature->reviews ?? collect();
                                                        $avgRating = $featureReviews->count() ? $featureReviews->avg('rating') : 0;
                                                        $ratingCount = $featureReviews->count();
                                                    @endphp

                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="feature-card border rounded p-3 mb-3 shadow-sm">
                                                            <div class="feature-texts">
                                                                <h6 class="feature-title-hd mb-1">
                                                                    {{ $translation->name ?? $feature->name ?? 'Unnamed Feature' }}
                                                                </h6>

                                                                <!-- Clickable Rating Text In key Feature (opens popup) -->
                                                                <p class="feature-content mb-2 open-review-popup"
                                                                   data-feature="{{ $feature->id }}">
                                                                    {{ number_format($avgRating, 1) }} ({{ $ratingCount }})
                                                                </p>
                                                            </div>

                                                            <!-- Description -->
                                                            @if(!empty($translation->description))
                                                                <p class="text-muted mb-0">{{ $translation->description }}</p>
                                                            @else
                                                                <p class="text-muted mb-0">No description available.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-center text-muted">No features have been added yet for this business.</p>
                                    @endif
                                </div>
                            </section>

                            <!-- Key Feature Popup Modal -->
                            <div class="modal fade" id="reviewPopup" tabindex="-1" aria-labelledby="reviewPopupLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="reviewPopupLabel">Submit Your Review</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body text-center">
                                            <input type="hidden" id="popupFeatureId">
                                            <input type="hidden" id="popupRating">

                                            <!-- Stars -->
                                            <div class="mb-3">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="popup-star fs-4 mx-1" data-value="{{ $i }}" style="cursor:pointer; color:#ccc;">&#9733;</span>
                                                @endfor
                                            </div>

                                            <!-- Comment -->
                                            <textarea id="popupComment" class="form-control mb-3" rows="3" placeholder="Write a review (optional)"></textarea>

                                            <!-- Submit Button -->
                                            <button id="submitPopupReview" class="btn btn-primary w-100">Submit Review</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End Feature section --}}


                            {{-- Top Reviews --}}
                            {{-- Comments This Section  --}}
                            {{-- <section class="review_sec p_50 " id="section6">
                                <div class="container">
                                    <div class="review_content" data-aos="fade-up" data-aos-duration="1000">
                                        <h2>Top Reviews</h2>
                                        @if ($topReviews->isNotEmpty())
                                            @foreach ($topReviews as $review)
                                                <div class="review_detl">
                                                    <div class="reviw_hd">
                                                        <div class="ans_lft">
                                                            <div class="asn-img">
                                                                @if ($review->user && $review->user->profile_image)
                                                                    <img src="{{ asset($review->user->profile_image) }}"
                                                                        class="img-fluid profile-circle"
                                                                        style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                        alt="User Image">
                                                                @else
                                                                    <img src="{{ asset($default_image) }}"
                                                                        class="img-fluid profile-circle"
                                                                        style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                        alt="Default Image">
                                                                @endif
                                                            </div>
                                                            <div class="asn-rating">
                                                                <h6>
                                                                    @if ($review->user && $review->user->user_type === 'admin')
                                                                        {{ $review->public_name ?? 'Public' }}
                                                                    @else
                                                                        {{ $review->user->first_name ?? 'Anonymous' }}
                                                                    @endif
                                                                </h6>

                                                                <div class="rating light"> --}}
                                                                    {{-- Change Review --}}
                                                                    {{-- <div class="inn_ul">
                                                                        <div class="rating-stars">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($averageRating))
                                                                                    <i
                                                                                        class="fas fa-star text-warning"></i>
                                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                                    <i
                                                                                        class="fas fa-star-half-alt text-warning"></i>
                                                                                @else
                                                                                    <i
                                                                                        class="far fa-star text-warning"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                    <div class="rate_box">
                                                                        {{ number_format($averageRating, 1) }} | --}}
                                                                        {{-- {{ $ratingCount }} ratings --}}
                                                                        {{-- @if ($ratingCount === 0)
                                                                        0 Ratings
                                                                        @elseif ($ratingCount === 1)
                                                                            1 Rating
                                                                        @else
                                                                            {{ $ratingCount }} Ratings
                                                                        @endif
                                                                    </div> --}}


                                                                     {{-- Average + Ratings count --}}
                                                                     {{-- <div class="rate_box">
                                                                        {{ number_format($averageRating, 1) }} |
                                                                        @if ($ratingCount === 0)
                                                                            0 Ratings
                                                                        @elseif ($ratingCount === 1)
                                                                            1 Rating
                                                                        @else
                                                                            {{ $ratingCount }} Ratings
                                                                        @endif
                                                                    </div> --}}

                                                                    {{-- <span
                                                                    class="rating-score size18">{{ number_format($review->rating, 1) }}</span>
                                                                <div class="rating_str">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= floor($review->rating))
                                                                        <i class="fas fa-star text-warning"></i>
                                                                        @elseif ($i - 0.5 <= $review->rating)
                                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                                            @else
                                                                            <i class="far fa-star text-warning"></i>
                                                                            @endif
                                                                            @endfor
                                                                </div>  --}}


                                                                {{-- </div>
                                                            </div>
                                                        </div>
                                                        <p class="m-0">{{ $review->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    <div class="review_text size18">
                                                        <p class="size22 big-bld">
                                                            {{ $review->translations->first()->title ?? 'Review' }}
                                                        </p>
                                                        <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                        </p> --}}

                                                        {{-- Show The Pros and Cons --}}
                                                        {{-- <div class="pros-cons-box row mt-4">
                                                    <div class="col-md-6">
                                                        <div class="pros-box p-3 border border-success rounded bg-light">
                                                            <h6 class="text-success mb-2"><i class="fas fa-thumbs-up me-1"></i> Pros</h6>
                                                            <p class="mb-0">{{ $review->translations->first()->pros }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-3 mt-md-0">
                                                            <div class="cons-box p-3 border border-danger rounded bg-light">
                                                                <h6 class="text-danger mb-2"><i class="fas fa-thumbs-down me-1"></i> Cons</h6>
                                                                <p class="mb-0">{{ $review->translations->first()->cons }}</p>
                                                            </div>
                                                        </div>
                                                        </div> --}}

                                                    {{-- </div>
                                                </div>
                                            @endforeach

                                            <div class="btm-bttn light">
                                                <a class="cta cta_white"
                                                    href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}">View
                                                    All Reviews</a>
                                            </div>
                                        @else
                                            <p>No reviews found for this business.</p>
                                        @endif
                                    </div>
                                </div>

                            </section> --}}

                            <section class="choice-business" id="softweretopic">
                            @php
                                $langId = getCurrentLanguageID();
                                $businessName =
                                    $business->translations->firstWhere('lang_id', $langId)?->name ?? 'This Business';
                            @endphp

                            @foreach ($business->category->topics as $topic)
                                @php
                                    $topicTranslation = $topic->translations->firstWhere('lang_id', $langId);
                                    $title = str_replace(
                                        '{business_name}',
                                        $businessName,
                                        $topicTranslation?->title ?? '',
                                    );
                                    $descriptionEntry = $business->topicDescriptions->firstWhere(
                                        'topic_id',
                                        $topic->id,
                                    );
                                    $paragraphs = explode("\n", $descriptionEntry?->description ?? '');
                                @endphp

                                @if ($title && $descriptionEntry)
                                    {{-- <section class="worth-it p_50 light" id="section{{ 100 + $loop->index }}">
                                        <h3>{{ $title }}</h3>

                                        @foreach ($paragraphs as $para)
                                            <p style="color: line-height: 1.6; margin-bottom: 15px;">
                                                {{-- {{ trim($para) }} --}}
                                                {{-- {!! trim($para) !!}
                                            </p>
                                        @endforeach
                                    </section> --}}

                                        <div class="worth-it light" id="section{{ 100 + $loop->index }}">
                                        <h2>{{ $title }}</h2>

                                        @foreach ($paragraphs as $para)
                                            {{-- <p> --}}
                                                {!! trim($para) !!}
                                            {{-- </p> --}}
                                        @endforeach
                                    </div>

                                @endif
                            @endforeach
                        </section>

                      {{-- Business Integration Section --}}
                                @php
                                // Decode items safely
                                $items = [];
                                if (!empty($business->integration?->items)) {
                                    $items = is_array($business->integration->items) ? $business->integration->items : json_decode($business->integration->items, true);
                                }

                            @endphp

                            @if($items && count($items) > 0)
                            <section class="business-integration-section" id="business-integration">
                                <div class="container">
                                    {{-- Section Title --}}
                                    <h2 class="mb-3">{{ $business->integration->title }}</h2>
                                    <p class="mb-4">{{ $business->integration->description }}</p>
                                    <p class="mb-2 text-secondary">{{ $business->integration->subheading ?? 'Popular Integrations' }}</p>

                                    {{-- Integration Items --}}
                                    <div class="integration-items">
                                        @foreach($items as $item)
                                        <div class="integration-inboxs">
                                            <a href="{{ $item['link'] ?? '#' }}" target="_blank" class="integration-item">

                                                @php
                                                    $icon_path = $item['icon'] ?? null;
                                                    if($icon_path && file_exists(public_path($icon_path))) {
                                                        $icon_url = asset($icon_path);
                                                    } else {
                                                        $icon_url = asset('images/placeholder.png');
                                                    }
                                                @endphp

                                                <img src="{{ $icon_url }}" alt="{{ $item['name'] ?? 'No Icon' }}">
                                                <span>{{ $item['name'] ?? '' }}</span>
                                            </a>
                                        </div>

                                        @endforeach
                                    </div>
                                </div>
                            </section>
                            @else
                            <p class="text-center text-muted">No integration items available.</p>
                            @endif

                            <!-- section software-like -->
                            <section class="software-like p_50 product_integra_sec " id="section9">
                                <div class="sftwre-like-innr">
                                    <div class="sftwre-asana-hd text-center" data-aos="fade-up" data-aos-duration="1000">
                                        {{-- <h2>Software like {{ $business->translations->first()->name }}</h2> --}}
                                        <h2>{{ $business->translations->first()->name }} Alternatives & Competitors</h2>
                                        <p>Based on other buyer's searches, these are the products that could be a good fit
                                            for you.
                                        </p>
                                    </div>
                                    <div class="sft_ware_test"
                                        style="display: flex; justify-content:center; align-items: center;">

                                        <div class="sftware-alternative d-flex" data-aos="fade-up"
                                            data-aos-duration="1000">
                                            <div class="sftware-alternative-pck" data-aos="fade-up"
                                                data-aos-duration="1000">
                                                <div class="ans_lft p_top_btm_sftwre pt-0">
                                                    <div class="asn-img">
                                                        <img
                                                            src="{{ asset($business->icon_id ?? 'front/img/sftare-img1.svg') }}">
                                                    </div>
                                                    <div class="asn-rating">
                                                        <h6 class="m-0">{{ $business->translations->first()->name }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <div class="overall-rate-sftwre p_top_btm_sftwre">
                                                    <h6 class="fw_700">Overall Rating:</h6>
                                                    @php
                                                        $ratingCount = $business->reviews
                                                            ->where('status', 'active')
                                                            ->count();

                                                    @endphp

                                                    <div class="tp-btm d-flex flex-col-mob">
                                                        <div class="inn_ul">
                                                            <div class="rating-stars">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <= floor($averageRating))
                                                                        <i class="fas fa-star text-warning"></i>
                                                                    @elseif ($i - 0.5 <= $averageRating)
                                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                                    @else
                                                                        <i class="far fa-star text-warning"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                        </div>
                                                        <div class="rate_box">
                                                            {{ number_format($averageRating, 1) }} |
                                                            @if ($ratingCount === 0)
                                                                0 Ratings
                                                            @elseif ($ratingCount === 1)
                                                                1 Rating
                                                            @else
                                                                {{ $ratingCount }} Reviews
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="over-rate-progress p_top_btm_sftwre">
                                                    <div class="ovr-progrs-div d-flex">
                                                        <p class="m-0">Ease of Use</p>
                                                        <div class="prgs_br d-flex align-items-center">
                                                            <progress class="progress-bar"
                                                                value="{{ $easeOfUseAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $easeOfUseAvg }}/5</output>
                                                        </div>
                                                    </div>

                                                    <div class="ovr-progrs-div d-flex">
                                                        <p class="m-0">Customer Service</p>
                                                        <div class="prgs_br d-flex align-items-center">
                                                            <progress class="progress-bar"
                                                                value="{{ $customerServiceAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $customerServiceAvg }}/5</output>
                                                        </div>
                                                    </div>

                                                    <div class="ovr-progrs-div d-flex">
                                                        <p class="m-0">Features</p>
                                                        <div class="prgs_br d-flex align-items-center">
                                                            <progress class="progress-bar"
                                                                value="{{ $exclusiveFeatureAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                        </div>
                                                    </div>

                                                    <div class="ovr-progrs-div d-flex">
                                                        <p class="m-0">Value for Money</p>
                                                        <div class="prgs_br d-flex align-items-center">
                                                            <progress class="progress-bar"
                                                                value="{{ $valueForMoneyAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $valueForMoneyAvg }}/5</output>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="start-from p_top_btm_sftwre">
                                                    <h6>Starting From:</h6>
                                                    <p class="m-0">
                                                        <span>{{ $currency }}{{ $startingPrice }}</span>/{{ $timeUnit }}
                                                    </p>
                                                </div>
                                                <div class="pricing-model">
                                                    <h6>Pricing Model:</h6>
                                                    <span>{{ $additional_info }}</span>
                                                </div>
                                            </div>

                                            @foreach ($alternativeBusiness as $altbusiness)
                                                <div class="sftware-alternative-pck" data-aos="fade-up"
                                                    data-aos-duration="1000">
                                                    @php
                                                        $price = getBusinessesWithStartingPrice($altbusiness);
                                                        // dd($price);
                                                        if (!empty($price) && isset($price[0]['starting_price'])) {
                                                            $businessprice = $price[0]['starting_price'];
                                                            $startingPrice = $businessprice['amount'];
                                                            $currency = $businessprice['currency'] ?? '$';
                                                            $timeUnit = ucfirst($businessprice['time_unit'] ?? 'month');
                                                            $additional_info =
                                                                $businessprice['additional_info'] ?? 'NA';
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

                                                    @endphp

                                                    <div class="ans_lft p_top_btm_sftwre pt-0">
                                                        <div class="asn-img">
                                                            <img src="{{ asset($altbusiness->icon_id ?? 'front/img/top-rate-img2.svg') }}"
                                                                alt="">
                                                        </div>
                                                        <div class="asn-rating">
                                                            @if ($altbusiness->translations->isNotEmpty())
                                                                <h6 class="m-0">
                                                                    {{ $altbusiness->translations->first()->name }}
                                                                </h6>
                                                            @else
                                                                <h6 class="m-0">Name not available</h6>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="overall-rate-sftwre p_top_btm_sftwre">
                                                        <h6 class="fw_700">Overall Rating:</h6>
                                                        <div class="tp-btm d-flex flex-col-mob">
                                                            <div class="inn_ul">
                                                                <div class="rating-stars">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= floor($altbusiness->reviews->avg('rating')))
                                                                            <i class="fas fa-star text-warning"></i>
                                                                        @elseif ($i - 0.5 <= $altbusiness->reviews->avg('rating'))
                                                                            <i
                                                                                class="fas fa-star-half-alt text-warning"></i>
                                                                        @else
                                                                            <i class="far fa-star text-warning"></i>
                                                                        @endif
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <div class="rate_box">
                                                                @php
                                                                    $count = $altbusiness->reviews->where('status', 'active')->count();
                                                                @endphp
                                                                {{ number_format($altbusiness->reviews->avg('rating'), 1) }} |
                                                                {{-- {{ $altbusiness->reviews->where('status', 'active')->count() }} --}}
                                                                @if ($count === 0)
                                                                    0 Ratings
                                                                @elseif ($count === 1)
                                                                    1 Rating
                                                                @else
                                                                    {{ $count }} Reviews
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="over-rate-progress p_top_btm_sftwre">
                                                        <div class="ovr-progrs-div d-flex">
                                                            <p class="m-0">Ease of Use</p>
                                                            <div class="prgs_br d-flex align-items-center">
                                                                <progress class="progress-bar"
                                                                    value="{{ ($altEaseOfUseAvg ?? 0) * 20 }}"
                                                                    max="100">
                                                                    {{ ($altEaseOfUseAvg ?? 0) * 20 }}%
                                                                </progress>
                                                                <output>{{ $altEaseOfUseAvg ?? 0 }}/5</output>
                                                            </div>
                                                        </div>
                                                        <div class="ovr-progrs-div d-flex">
                                                            <p class="m-0">Customer Service</p>
                                                            <div class="prgs_br d-flex align-items-center">
                                                                <progress class="progress-bar"
                                                                    value="{{ ($altCustomerServiceAvg ?? 0) * 20 }}"
                                                                    max="100">
                                                                    {{ ($altCustomerServiceAvg ?? 0) * 20 }}%
                                                                </progress>
                                                                <output>{{ $altCustomerServiceAvg ?? 0 }}/5</output>
                                                            </div>
                                                        </div>
                                                        <div class="ovr-progrs-div d-flex">
                                                            <p class="m-0">Features</p>
                                                            <div class="prgs_br d-flex align-items-center">
                                                                <progress class="progress-bar"
                                                                    value="{{ ($altExclusiveFeatureAvg ?? 0) * 20 }}"
                                                                    max="100">
                                                                    {{ ($altExclusiveFeatureAvg ?? 0) * 20 }}%
                                                                </progress>
                                                                <output>{{ $altExclusiveFeatureAvg ?? 0 }}/5</output>
                                                            </div>
                                                        </div>
                                                        <div class="ovr-progrs-div d-flex">
                                                            <p class="m-0">Value for Money</p>
                                                            <div class="prgs_br d-flex align-items-center">
                                                                <progress class="progress-bar"
                                                                    value="{{ ($altValueForMoneyAvg ?? 0) * 20 }}"
                                                                    max="100">
                                                                    {{ ($altValueForMoneyAvg ?? 0) * 20 }}%
                                                                </progress>
                                                                <output>{{ $altValueForMoneyAvg ?? 0 }}/5</output>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="start-from p_top_btm_sftwre">
                                                        <h6>Starting From:</h6>
                                                        <p class="m-0">
                                                            <span>{{ $currency }}{{ $startingPrice }}</span>/{{ $timeUnit }}
                                                        </p>
                                                    </div>
                                                    <div class="pricing-model  p_top_btm_sftwre pt-0">
                                                        <h6>Pricing Model:</h6>
                                                        <span>{{ $additional_info }}</span>
                                                    </div>
                                                    <div class="sftwre-alt-btn">
                                                        <a href="{{ $altbusiness->websites->first()->website_url ?? ($altbusiness->affiliate_link ?? ($altbusiness->permanent_url ?? '#')) }}"
                                                            class="cta cta_orange d-flex align-items-center justify-content-center">
                                                            Visit Website
                                                            <svg width="17" height="12" viewBox="0 0 17 12"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M1.2647 5.84154C7.08595 5.84154 7.42894 5.84154 13.2205 5.84154C13.2304 5.81429 13.2403 5.78704 13.2403 5.75979C13.1017 5.6962 12.9631 5.63262 12.8245 5.55995C10.9435 4.62434 9.91386 3.14372 9.52775 1.25434C9.50795 1.18167 9.59705 1.09084 9.63665 1C9.72575 1.0545 9.86436 1.09992 9.89406 1.18167C10.0525 1.6086 10.1317 2.06278 10.3198 2.47154C11.1712 4.34275 12.6859 5.45095 14.8738 5.78704C15.0124 5.80521 15.1114 5.97779 15.25 6.0868C14.4778 6.32297 13.7947 6.45922 13.1908 6.72265C11.4286 7.49475 10.4089 8.82095 9.99306 10.5559C9.94356 10.7739 9.97326 11.11 9.51785 10.9647C9.87426 8.82095 10.6861 7.79451 13.3096 6.21397C13.0324 6.21397 12.8443 6.21397 12.6562 6.21397C7.20123 6.21397 7.18494 6.21397 1.7201 6.20488C1.5122 6.20488 1.1756 6.32297 1.2647 5.84154Z"
                                                                    fill="white" stroke="white" stroke-width="0.8" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>



                                    </div>
                                </div>

                                {{-- <div class="sft_btm">
                          <a class="cta"
                            onclick="changeCategory('{{ $business->category->translation()->first()->slug }}')">View
                            All
                            Alternatives</a>
                            </div> --}}

                            </section>



                            {{-- busines bar --}}
                            <section class="about_asn_2 light pt-0 p_50">

                                <div class="about_asn_content">
                                    <div class="hd_content asan-text-para">
                                        {!! $product->translations->overview ?? "" !!}
                                    </div>

                                   {{-- review section  --}}
                                    {{-- <div class="asn_dv asv_orng asv_blue" data-aos="fade-up" data-aos-duration="1000">
                                        <div class="asn_dv_contnt">
                                            <div class="row frst_rw">

                                                {{-- Rating logic --}}
                                                {{-- @php
                                                    $ratingCount = $business->reviews
                                                        ->where('status', 'active')
                                                        ->count();
                                                    $averageRating =
                                                        $business->reviews->count() > 0
                                                            ? $business->reviews->avg('rating')
                                                            : 0;
                                                @endphp --}}
                                                {{-- LEFT COLUMN --}}
                                                {{-- <div class="col-md-6">
                                                    <div class="ans_lft">
                                                        <div class="asn-img">
                                                            <img
                                                                src="{{ asset($business->icon_id ?? 'front/img/tdot-rd.svg') }}">
                                                        </div> --}}

                                                        {{-- Business name + review info + inline tags --}}
                                                        {{-- <div class="asn-rating"> --}}
                                                            {{-- Top Row: Name + Heart + Review Year --}}
                                                            {{-- <div class="d-flex justify-content-between align-items-center">
                                                                <h6 class="light mb-0">
                                                                    {{ $business->translations->first()->name }}
                                                                </h6>

                                                                <div class="d-flex align-items-center gap-2"
                                                                    style="color: #FFF">
                                                                    <livewire:wishlist :product-id="$business->id"
                                                                        :wire:key="'wishlist-'.$business->id" /> --}}
                                                                    {{-- <span style="font-size: 14px; color: #ffffff;">Review
                                                            {{ date('Y') }}</span> --}}
                                                                {{-- </div>
                                                            </div> --}}

                                                            {{-- Bottom Row: Inline Info --}}
                                                            {{-- <div class="str_fl">
                                                                <p class="text-white mb-0"> --}}
                                                                    {{-- Star ratings below button --}}
                                                                {{-- <div class="tp-btm d-flex flex-col-mob mt-2">
                                                                    <div class="inn_ul">
                                                                        <div class="rating-stars">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($averageRating))
                                                                                    <i
                                                                                        class="fas fa-star text-warning"></i>
                                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                                    <i
                                                                                        class="fas fa-star-half-alt text-warning"></i>
                                                                                @else
                                                                                    <i
                                                                                        class="far fa-star text-warning"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                    <div class="rate_box" style="color:white;">
                                                                        {{ number_format($averageRating, 1) }} |
                                                                        {{-- {{ $ratingCount }} --}}
                                                                        {{-- @if ($ratingCount === 0)
                                                                            0 Ratings
                                                                        @elseif ($ratingCount === 1)
                                                                            1 Rating
                                                                        @else
                                                                            {{ $ratingCount }} Ratings
                                                                        @endif
                                                                    </div>
                                                                </div> --}}
                                                                {{-- End star rating --}}
                                                                {{-- </p>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div> --}}

                                                {{-- RIGHT COLUMN --}}
                                                {{-- <div class="col-md-6">
                                                    <div class="ans_ryt">
                                                        <div class="site_vsit">
                                                            <div class="accor-btn acr_wht">
                                                                <a href="{{ $link }}"
                                                                    class="cta cta_orange d-flex align-items-center"
                                                                    tabindex="0">
                                                                    Visit Website
                                                                    <div class="right-arw">
                                                                        <img src="{{ asset('front/img/right-arrw.svg') }}"
                                                                            alt="">
                                                                    </div>
                                                                </a>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                {{-- END RIGHT COLUMN --}}
                                            {{-- </div>

                                        </div>
                                    </div> --}}
                                    {{-- End Review  --}}
                                </div>
                                {{-- <div class="asan-text-para" data-aos="fade-up" data-aos-duration="1000">
                                <h6 class="fw_700 h6_26"> Usability and Experience</h6>
                                <p>{{ $business->translations->first()->description }} </p>
                                </div>
                                @if (!empty($business->pricingOptions) && $business->pricingOptions->first()?->translations->isNotEmpty())
                                @php
                                $pricingOption = $business->pricingOptions->first();
                                $translation =
                                $pricingOption->translations->firstWhere('lang_id', getCurrentLanguageID()) ??
                                $pricingOption->translations->first();
                                @endphp

                                <div class="asn_dv asv_orng trial-avil" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="asn_dv_contnt">
                                        <div class="row frst_rw">
                                            <div class="col-md-6">
                                                <div class="ans_lft">
                                                    <div class="asn-img">
                                                        <img src="{{ asset('front/img/tick-white.svg') }}">
                                                    </div>
                                                    <div class="asn-rating">
                                                        <p class="m-0">{{ $translation->name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="ans_ryt">
                                                    <div class="site_vsit">
                                                        <div class="accor-btn acr_wht">
                                                            <a href="{{ $link }}"
                                                                class="cta d-flex align-items-center" tabindex="0">Claim
                                                                Now</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif --}}
                            </section>

                            {{-- faq --}}
                            <section class="faq-section  faq-section_1 p_50 pt-2 light" id="section15">
                                <div class="container">
                                    <div class="faq-inner">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="d-flex flex-column w-auto">
                                                    {{-- <h2>Frequently Asked Questions (FAQs)</h2>
                                                    <p>
                                                        Find quick answers to the most common questions about using Localio
                                                        to discover, filter, and connect with the best local businesses and
                                                        products.
                                                    </p> --}}

                                                    @php
                                                    use App\Models\StaticContentKey;

                                                    $faq_title = StaticContentKey::where('key', 'faq_title')->first();
                                                    $faq_description = StaticContentKey::where('key', 'faq_description')->first();
                                                    //dd($faq_title, $faq_description);
                                                @endphp

                                                    <h2>{{ $faq_title?->default_value ?? '' }}</h2>
                                                    <p>{{ $faq_description?->default_value ?? '' }}</p>


                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="faq-accor">
                                                    <div class="accordion" id="accordionExample">
                                                        @forelse ($business->faqs as $index => $faq)
                                                            @php $translation = $faq->translations->first(); @endphp
                                                            @if ($translation)
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header"
                                                                        id="heading{{ $index }}">
                                                                        <button
                                                                            class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}"
                                                                            type="button" data-bs-toggle="collapse"
                                                                            data-bs-target="#collapse{{ $index }}"
                                                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                                            aria-controls="collapse{{ $index }}">
                                                                            <span>{{ $translation->question }}</span>
                                                                        </button>
                                                                    </h2>
                                                                    <div id="collapse{{ $index }}"
                                                                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                                                        aria-labelledby="heading{{ $index }}"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body">
                                                                            {{ $translation->answer }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @empty
                                                            <p>No FAQs available for this business.</p>
                                                        @endforelse

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            {{-- <div class="crm-review-innr crm-review-innr_2 " data-aos="fade-up" data-aos-duration="1000">
                    <div class="row pt-0 p_50">
                        <div class="col-xl-5">
                            <div class="sales-crm-pack crm-pack-lft">
                                <div class="inn_sl_hed">
                                    <div class="sli_img choice_img">
                                        <img class="slider_img"
                                            src="{{ asset($business->icon_id ?? 'front/img/big-asana.png') }}"
                                        alt="">
                                        </div>
                                        <div class="sl_h">
                                            <div class="inn_h d-flex align-items-center">
                                                <h6 class="head">{{ $business->translations->first()->name }}
                                                </h6>
                                                <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                                            </div>
                                            <div class="tp-btm d-flex flex-col-mob pt-2">
                                                <div class="inn_ul">
                                                    <div class="rating-stars">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= floor($averageRating))
                                                            <i class="fas fa-star text-warning"></i>
                                                            @elseif ($i - 0.5 <= $averageRating)
                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                                @else
                                                                <i class="far fa-star text-warning"></i>
                                                                @endif
                                                                @endfor
                                                    </div>
                                                </div>
                                                <div class="rate_box">
                                                    {{ number_format($averageRating, 1) }} | {{ $ratingCount }}
                                                    ratings
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        </div>
                                        </div>


                                        <div class="col-xl-4 ">
                                            <div class="sales-crm-pack">
                                                <div class="feture_box">
                                                    <h6 class="size22 big-bld">Localio Review Breakdown</h6>
                                                    <ul class="p-0 m-0">
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Ease of Use</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $easeOfUseAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $easeOfUseAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Customer Service</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $customerServiceAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $customerServiceAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Features</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $exclusiveFeatureAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Value for Money</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $valueForMoneyAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $valueForMoneyAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xl-3 ">
                                            <div class="sales-crm-pack">
                                                <div class="fre_trail feture_box size22">
                                                    <div class="grn_check_big">
                                                        <img src="{{ asset('front/img/new-grn-chk.png') }}">
                                                    </div>
                                                    <h6 class="blue-text big-bld">Free Trial <br>
                                                        Available
                                                    </h6>
                                                    <div class="accor-btn p-0">
                                                        <a class="cta cta_white">Claim Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        </div> --}}


                            <!-- scetion crm sec -->
                            <section class="crm_sec revie_left_rgt_sec " id="section14">

                                <div class="crm_content" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="crm_hd">
                                        <div class="crm_lft">
                                            <h2>Localio {{ $business->translations->first()->name }} Reviews</h2>
                                        </div>
                                        <div class="crm-ryt">
                                            <div class="ryt-rvw-btn" style="cursor: pointer;">
                                                <a class="cta cta_orange"
                                                            @auth
                                       onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                @else
                                onclick="window.location.href = '/login'" @endauth>Write
                                                            Review</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Added Localio Review Breakdown --}}
                                {{-- <div class="crm-review-innr crm-review-innr_2 " data-aos="fade-up"
                                    data-aos-duration="1000">
                                    <div class="row pt-0 p_50">
                                        <div class="col-xl-5">
                                            <div class="sales-crm-pack crm-pack-lft"> --}}

                                                {{-- same --}}

                                                {{-- <div class="inn_sl_hed">
                                                    <div class="sli_img choice_img">
                                                        <img class="slider_img"
                                                            src="{{ asset($business->icon_id ?? 'front/img/big-asana.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="sl_h">
                                                        <div class="inn_h d-flex align-items-center">
                                                            <h6 class="head">
                                                                {{ $business->translations->first()->name }}
                                                            </h6>
                                                            <livewire:wishlist :product-id="$business->id"
                                                                :wire:key="'wishlist-'.$business->id" />
                                                        </div>
                                                        <div class="tp-btm d-flex flex-col-mob pt-2">
                                                            <div class="inn_ul">
                                                                <div class="rating-stars">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= floor($averageRating))
                                                                            <i class="fas fa-star text-warning"></i>
                                                                        @elseif ($i - 0.5 <= $averageRating)
                                                                            <i
                                                                                class="fas fa-star-half-alt text-warning"></i>
                                                                        @else
                                                                            <i class="far fa-star text-warning"></i>
                                                                        @endif
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <div class="rate_box">
                                                                {{ number_format($averageRating, 1) }} | --}}

                                                                {{-- {{ $ratingCount }} --}}

                                                                {{-- @if ($ratingCount === 0)
                                                                    0 Ratings
                                                                @elseif ($ratingCount === 1)
                                                                    1 Rating
                                                                @else
                                                                    {{ $ratingCount }} Ratings
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-xl-4 ">
                                            <div class="sales-crm-pack">
                                                <div class="feture_box">
                                                    <h6 class="size22 big-bld">Localio Review Breakdown</h6>
                                                    <ul class="p-0 m-0">
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Ease of Use</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $easeOfUseAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $easeOfUseAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Customer Service</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $customerServiceAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $customerServiceAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Features</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $exclusiveFeatureAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Value for Money</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $valueForMoneyAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $valueForMoneyAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xl-3 ">
                                            <div class="sales-crm-pack">
                                                <div class="fre_trail feture_box size22">
                                                    <div class="grn_check_big">
                                                        <img src="{{ asset('front/img/new-grn-chk.png') }}">
                                                    </div>
                                                    <h6 class="blue-text big-bld">Free Trial <br>
                                                        Available
                                                    </h6>
                                                    <div class="accor-btn p-0">
                                                        <a class="cta cta_white">Claim Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                {{-- End Localio Review Breakdown --}}
                                <div class="crm_review_box review_sec" id="all-reviews">
                                    <nav class="d-flex">
                                        {{-- <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-home" type="button" role="tab"
                                                aria-controls="nav-home" aria-selected="true">All Reviews</button> --}}

                                            {{-- @auth
                                                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                                    data-bs-target="#nav-profile" type="button" role="tab"
                                                    aria-controls="nav-profile" aria-selected="false">Localio
                                                    Reviews
                                                </button>
                                            @endauth --}}

                                                {{-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile" aria-selected="false">Localio
                                                Reviews
                                            </button>

                                            <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-contact" type="button" role="tab"
                                                aria-controls="nav-contact" aria-selected="false">Trustpilot
                                                Reviews</button>
                                        </div> --}}
                                        <div class="selct_box">
                                            <form method="GET" id="sort-form">
                                                <label for="rating-select">Sort by:</label>
                                                <select class="form-select" id="rating-select" name="sort" onchange="this.form.submit()">
                                                    <option value="best"
                                                        {{ request('sort') == 'best' ? 'selected' : '' }}>Best
                                                        Rating
                                                    </option>
                                                    <option value="high-to-low"
                                                        {{ request('sort') == 'high-to-low' ? 'selected' : '' }}>High
                                                        to Low</option>
                                                    <option value="low-to-high"
                                                        {{ request('sort') == 'low-to-high' ? 'selected' : '' }}>Low
                                                        to High</option>
                                                </select>
                                            </form>
                                        </div>
                                    </nav>
                                <div class="row review-row-prod-inr">
                                      <div class="col-lg-4">
                                            <div class="review-col">

                                                {{-- <div class="review-star-box">
                                                    <ul class="progress-list">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            @php
                                                                $count = $ratingsCount[$i] ?? 0;
                                                                $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                                            @endphp
                                                            <li class="progress-list-item">
                                                                <div class="star-div">
                                                                    <ol class="star-div-ol">
                                                                        @for ($j = 1; $j <= 5; $j++)
                                                                            @if ($j <= $i)
                                                                                <li><img src="{{ asset('front/img/star-lft.svg') }}" alt="Star"></li>
                                                                            @else
                                                                                <li><img src="{{ asset('front/img/empty-star-img.svg') }}" alt="Star"></li>
                                                                            @endif
                                                                        @endfor
                                                                    </ol>
                                                                </div>
                                                                <div class="progress-box">
                                                                    <div class="progress-fill" style="width: {{ $percent }}%;"></div>
                                                                </div>
                                                                <div class="profres-value">
                                                                    <p>{{ $percent }}%</p>
                                                                </div>
                                                            </li>
                                                        @endfor
                                                    </ul>
                                                </div> --}}

                                                <div class="review-star-box">
                                                    <ul class="progress-list">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            @php
                                                                $count = $ratingsCount[$i] ?? 0;
                                                                $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                                            @endphp
                                                            <li class="progress-list-item">
                                                                <div class="star-div">
                                                                    <ol class="star-div-ol">
                                                                        @for ($j = 1; $j <= 5; $j++)
                                                                            <li>
                                                                                @if ($j <= $i)
                                                                                    <i class="fas fa-star text-warning"></i>
                                                                                @else
                                                                                    <i class="far fa-star text-warning"></i>
                                                                                @endif
                                                                            </li>
                                                                        @endfor
                                                                    </ol>
                                                                </div>
                                                                <div class="progress-box">
                                                                    <div class="progress-fill" style="width: {{ $percent }}%;"></div>
                                                                </div>
                                                                <div class="profres-value">
                                                                    <p>{{ $percent }}%</p>
                                                                </div>
                                                            </li>
                                                        @endfor
                                                    </ul>
                                                </div>

                                             </div>
                                      </div>
                                      <div class="col-lg-8">
                                        <div class="tab-content" id="nav-tabContent">
                                            <div class="tab-pane fade active show" id="nav-home" role="tabpanel"
                                                aria-labelledby="nav-home-tab">
                                                @foreach ($allReviews as $review)
                                                    <div class="review_detl populr-alternative" data-aos="fade-up"
                                                        data-aos-duration="1000">
                                                        <div class="reviw_hd">
                                                            <div class="ans_lft">
                                                                <div class="asn-img">
                                                                    @if ($review->user && $review->user->profile_image)
                                                                        <img src="{{ asset($review->user->profile_image) }}"
                                                                            class="img-fluid profile-circle"
                                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                            alt="User Image">
                                                                    @else
                                                                        <img src="{{ asset($default_image) }}"
                                                                            class="img-fluid profile-circle"
                                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                            alt="Default Image">
                                                                    @endif
                                                                </div>
                                                                <div class="asn-rating">
                                                                    <h6>
                                                                        @if ($review->user && $review->user->user_type === 'admin')
                                                                            {{ $review->public_name ?? 'Public' }}
                                                                        @else
                                                                            {{ $review->user->first_name ?? 'Anonymous' }}
                                                                        @endif
                                                                    </h6>

                                                                    <div class="rating light">
                                                                        {{-- Change Review --}}
                                                                        <div class="inn_ul">
                                                                            <div class="rating-stars">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if ($i <= floor($averageRating))
                                                                                        <i
                                                                                            class="fas fa-star text-warning"></i>
                                                                                    @elseif ($i - 0.5 <= $averageRating)
                                                                                        <i
                                                                                            class="fas fa-star-half-alt text-warning"></i>
                                                                                    @else
                                                                                        <i
                                                                                            class="far fa-star text-warning"></i>
                                                                                    @endif
                                                                                @endfor
                                                                            </div>
                                                                        </div>
                                                                        <div class="rate_box">
                                                                            {{ number_format($averageRating, 1) }} |
                                                                            {{-- {{ $ratingCount }} ratings --}}
                                                                            @if ($ratingCount === 0)
                                                                                0 Ratings
                                                                            @elseif ($ratingCount === 1)
                                                                                1 Rating
                                                                            @else
                                                                                {{ $ratingCount }} Ratings
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    {{-- <span
                                                                    class="rating-text size18">{{ number_format($review->rating, 1) }}</span>
                                                                <div class="rating_str">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= floor($review->rating))
                                                                        <i class="fas fa-star text-warning"></i>
                                                                        @elseif ($i - 0.5 <= $review->rating)
                                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                                            @else
                                                                            <i class="far fa-star text-warning"></i>
                                                                            @endif
                                                                            @endfor
                                                                </div>  --}}

                                                                </div>
                                                            </div>
                                                            <p>{{ $review->created_at->diffForHumans() }}</p>
                                                        </div>
                                                        <div class="review_text size18">
                                                            <p class="size22 big-bld">
                                                                {{ $review->translations->first()->title ?? '' }}
                                                            </p>
                                                            <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="btm-bttn light">
                                                    <a class="cta cta_white"
                                                        href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}">View
                                                        More Reviews</a>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                                aria-labelledby="nav-profile-tab">
                                                @foreach ($ourReviews as $review)
                                                    @if ($review->user)
                                                        <div class="review_detl" data-aos="fade-up" data-aos-duration="1000">
                                                            <div class="reviw_hd">
                                                                <div class="ans_lft">
                                                                    <div class="asn-img">
                                                                        @if ($review->user && $review->user->profile_image)
                                                                            <img src="{{ asset($review->user->profile_image) }}"
                                                                                class="img-fluid profile-circle"
                                                                                style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                                alt="User Image">
                                                                        @else
                                                                            <img src="{{ asset($default_image) }}"
                                                                                class="img-fluid profile-circle"
                                                                                style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                                alt="Default Image">
                                                                        @endif
                                                                    </div>
                                                                    <div class="asn-rating">
                                                                        <h6>
                                                                            @if ($review->user && $review->user->user_type === 'admin')
                                                                                {{ $review->public_name ?? 'Public' }}
                                                                            @else
                                                                                {{ $review->user->first_name ?? 'Anonymous' }}
                                                                            @endif
                                                                        </h6>
                                                                        <div class="rating light">
                                                                            <span
                                                                                class="rating-score size18">{{ number_format($review->rating, 1) }}</span>
                                                                            <div class="rating_str">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if ($i <= floor($review->rating))
                                                                                        <i
                                                                                            class="fas fa-star text-warning"></i>
                                                                                    @elseif ($i - 0.5 <= $review->rating)
                                                                                        <i
                                                                                            class="fas fa-star-half-alt text-warning"></i>
                                                                                    @else
                                                                                        <i
                                                                                            class="far fa-star text-warning"></i>
                                                                                    @endif
                                                                                @endfor
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <p>{{ $review->created_at->diffForHumans() }}</p>
                                                            </div>
                                                            <div class="review_text size18">
                                                                <p class="size22 big-bld">
                                                                    {{ $review->translations->first()->title ?? '' }}
                                                                </p>
                                                                <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                <div class="btm-bttn light">
                                                    <a class="cta cta_white"
                                                        href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}">View
                                                        All Reviews</a>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="nav-contact" role="tabpanel"
                                                aria-labelledby="nav-contact-tab">
                                                @foreach ($trustpilotReviews as $review)
                                                    <div class="review_detl" data-aos="fade-up" data-aos-duration="1000">
                                                        <div class="reviw_hd">
                                                            <div class="ans_lft">
                                                                <div class="asn-img">
                                                                    @if ($review->user && $review->user->profile_image)
                                                                        <img src="{{ asset($review->user->profile_image) }}"
                                                                            class="img-fluid profile-circle"
                                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                            alt="User Image">
                                                                    @else
                                                                        <img src="{{ asset($default_image) }}"
                                                                            class="img-fluid profile-circle"
                                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                            alt="Default Image">
                                                                    @endif
                                                                </div>
                                                                <div class="asn-rating">
                                                                    <h6>
                                                                        @if ($review->user && $review->user->user_type === 'admin')
                                                                            {{ $review->public_name ?? 'Public' }}
                                                                        @else
                                                                            {{ $review->user->first_name ?? 'Anonymous' }}
                                                                        @endif
                                                                    </h6>

                                                                    {{-- <div class="rating light">
                                                                        <span
                                                                            class="rating-text size18">{{ number_format($review->rating, 1) }} 2313221</span>
                                                                        <div class="rating_str">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($review->rating))
                                                                                    <i class="fas fa-star text-warning"></i>
                                                                                @elseif ($i - 0.5 <= $review->rating)
                                                                                    <i
                                                                                        class="fas fa-star-half-alt text-warning"></i>
                                                                                @else
                                                                                    <i class="far fa-star text-warning"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div> --}}

                                                                    <div class="rating light">
                                                                        <div class="inn_ul">
                                                                            <div class="rating-stars">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if ($i <= floor($review->rating))
                                                                                        <i class="fas fa-star text-warning"></i>
                                                                                    @elseif ($i - 0.5 <= $review->rating)
                                                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                                                    @else
                                                                                        <i class="far fa-star text-warning"></i>
                                                                                    @endif
                                                                                @endfor
                                                                            </div>
                                                                        </div>
                                                                        <div class="rate_box">
                                                                            {{ number_format($review->rating, 1) }} |
                                                                            {{-- {{ $ratingCount }} ratings --}}
                                                                            @if ($ratingCount === 0)
                                                                            0 Reviews
                                                                            @elseif ($ratingCount === 1)
                                                                                1 Review
                                                                            @else
                                                                                {{ $ratingCount }} Reviews
                                                                            @endif
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <p>{{ $review->created_at->diffForHumans() }}</p>
                                                        </div>
                                                        <div class="review_text size18">
                                                            <p class="size22 big-bld">
                                                                {{ $review->translations->first()->title ?? '' }}
                                                            </p>
                                                            <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="btm-bttn light">
                                                    <a class="cta cta_white"
                                                        href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}">View
                                                        All Reviews</a>
                                                </div>
                                            </div>
                                        </div>
                                     </div>
                                </div>     

                                </div>
                            </section>
                        </div>
                    </div>

                    {{-- Comments this table and added this header change layout --}}
                    {{-- <div class="col-lg-3">
                        <div class="inner_table2">

                            @php

                                $name = $business->translations->first()->name ?? 'This Business';

                                // Fixed dynamic top content
                                $staticBottomSections = [
                                    ['id' => 'section9', 'label' => "Software like $name"],
                                    ['id' => 'section15', 'label' => 'FAQ'],
                                    ['id' => 'section14', 'label' => "Localio $name Reviews"],
                                    ['id' => 'section16', 'label' => 'Get the Best Picks in Your Inbox'],
                                ];

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
                                    ['id' => 'section1', 'label' => 'User Reviews Breakdown'],
                                    ['id' => 'section2', 'label' => "What is $name"],
                                    ['id' => 'section3', 'label' => "$name Pricing Plans"],
                                    ['id' => 'section4', 'label' => "$name Pros and cons"],
                                    ['id' => 'section5', 'label' => 'Compare With A Popular Alternative'],
                                    ['id' => 'section6', 'label' => 'Top Reviews'],
                                ];

                                $tableOfContents = array_merge(
                                    $staticTopSections,
                                    $dynamicTopics,
                                    $staticBottomSections,
                                );
                            @endphp

                            <div class="inner_table2">
                                <div class="table_st">
                                    <div id="table-of-content" class="feture_box p-3 shadow rounded bg-white"
                                        style="top: 90px; max-height: max-content; overflow-y: auto;">
                                        <h6 class="blue-text big-bld mb-3">Table of Content</h6>
                                        <ul class="list-unstyled toc-links small">
                                            @foreach ($tableOfContents as $i => $item)
                                                @php
                                                    $isLastDynamic =
                                                        str_starts_with($item['id'], 'section') &&
                                                        (int) filter_var($item['id'], FILTER_SANITIZE_NUMBER_INT) ===
                                                            100 + count($dynamicTopics) - 1;
                                                @endphp

                                                <li class="py-1 {{ $isLastDynamic ? 'mb-0' : '' }}">
                                                    <a href="#{{ $item['id'] }}" class="text-blue-600 d-block">
                                                        {{ $item['label'] }}
                                                    </a>
                                                </li>

                                                {{-- Add divider spacing (not <br>) after last dynamic topic --}}
                                                 {{-- @if ($isLastDynamic)
                                                    <li class="my-1"></li>
                                                @endif
                                            @endforeach


                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div> --}}
                    {{-- End this table code --}}

                </div>


            </div>
            <section class="subs_sec light p_50" id="section16">
                <x-news-letter-subscription />
            </section>
        </div>
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

        jQuery(window).scroll(function() {
            var scroll = jQuery(window).scrollTop();
            if (scroll >= 200) {
                jQuery(".asn_main_sec > .asn_dv").addClass("fixed-div");
            } else {
                jQuery(".asn_main_sec > .asn_dv").removeClass("fixed-div");
            }
        });
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

    <!-- jQuery (required for Bootstrap JS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        window.addEventListener('review-submitted', () => {
            window.location.reload();
        });
    </script>


    {{-- Content table script --}}
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

            // Active section highlight on scroll
            window.addEventListener('scroll', function() {
                const scrollPosition = window.scrollY + 120;
                let currentSectionId = null;

                sections.forEach(section => {
                    if (section && section.offsetTop <= scrollPosition) {
                        currentSectionId = section.id;
                    }
                });

                links.forEach(link => {
                    link.classList.remove('active');
                    const href = link.getAttribute('href').substring(1);
                    if (href === currentSectionId) {
                        link.classList.add('active');
                    }
                });
            });
        });
    </script>

    {{-- Business image slider script --}}

    {{-- <script>
    $(document).ready(function () {
        // Initialize main slider (big image)
        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            adaptiveHeight: true
        });

        // Initialize thumbnail slider (small images)
        $('.slider-nav').slick({
            slidesToShow: 4, // Adjust as needed
            slidesToScroll: 1,
            vertical: true,
            arrows: false,
            infinite: false,
            centerMode: false,
            focusOnSelect: false,
            swipe: false,
            draggable: false
            //Do NOT set `asNavFor`
        });

        //  Fix: Use `data-slick-index` safely
        $('.slider-nav').on('mouseenter', '.slick-slide', function () {
            let $slide = $(this);
            let index = $slide.attr('data-slick-index');

            if (index !== undefined) {
                $('.slider-for').slick('slickGoTo', parseInt(index));

                // Optional: highlight active thumbnail
                $('.slider-nav img').css('border-color', 'transparent');
                $slide.find('img').css('border-color', '#007bff');
            }
        });

        // Set initial border
        setTimeout(function () {
            $('.slider-nav .slick-current img').css('border-color', '#007bff');
        }, 100);
    });
        </script> --}}

        <script>
            $(document).ready(function () {
                let slidersReady = false;

                // Initialize main slider (big image)
                $('.slider-for').on('init', function () {
                    slidersReady = true;

                    // Set initial border for active thumbnail
                    $('.slider-nav .slick-current img').css('border-color', '#007bff');
                }).slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    fade: true,
                    adaptiveHeight: true
                });

                // Initialize thumbnail slider (small images)
                $('.slider-nav').slick({
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

                // Hover to change main image (only after sliders are ready)
                $('.slider-nav').on('mouseenter', '.slick-slide', function () {
                    if (!slidersReady) return; // Ensure sliders are fully initialized

                    let $thumb = $(this);
                    let index = $thumb.data('slick-index');

                    if (typeof index !== 'undefined') {
                        // Change main image to the clicked thumbnail's index
                        $('.slider-for').slick('slickGoTo', parseInt(index, 10), true);

                        // Optional: Set active border color for the current thumbnail
                        $('.slider-nav img').css('border-color', 'transparent');
                        $thumb.find('img').css('border-color', '#007bff');
                    }
                });

                // Fallback: Reset to active thumbnail on mouse leave
                $('.slider-nav').on('mouseleave', function () {
                    let currentIndex = $('.slider-for').slick('slickCurrentSlide');
                    let $currentThumb = $('.slider-nav [data-slick-index="' + currentIndex + '"] img');
                    $('.slider-nav img').css('border-color', 'transparent');
                    $currentThumb.css('border-color', '#007bff');
                });

                // Preload all images in the slider to avoid delay when switching images
                function preloadSliderImages() {
                    $('.slider-for img').each(function () {
                        const img = new Image();
                        img.src = $(this).attr('src');
                    });
                }

                $(window).on('load', function () {
                    preloadSliderImages(); // Preload images on window load
                });
            });
        </script>

         {{-- End Business image slider script --}}

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

            observer.observe(document.body, { childList: true, subtree: true });
        });

        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', () => {
                setupAllStarRatings();
            });
        });
    </script>

    {{-- Preview Section Script --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
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
                0: { slidesPerView: 1, slidesPerGroup: 1 },
                768: { slidesPerView: slidesPerView, slidesPerGroup: slidesPerGroup }
            }
        });

        // Add 'active' class to the first slide initially
        slides.forEach(slide => slide.classList.remove('active'));
        slides[reviewSwiper.activeIndex].classList.add('active');

        // Update 'active' class on slide change
        reviewSwiper.on('slideChange', function () {
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

{{-- Added This Script View All Reviews Scrolling --}}
<script>
    $(document).ready(function () {
        // Smooth scroll on "View All Reviews"
        $("#scrollToReviews").on("click", function (e) {
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


{{-- Key Feature Review Script --}}
<script>
    $(function () {
        let selectedRating = 0;
        let selectedFeatureId = null;
        let businessId = "{{ $business->id ?? '' }}";

        // Setup CSRF token
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Open popup when clicking rating text
        $('.open-review-popup').on('click', function () {
            selectedFeatureId = $(this).data('feature');
            $('#popupFeatureId').val(selectedFeatureId);
            $('#reviewPopup').modal('show');
        });

        // Star selection
        $('.popup-star').on('mouseover', function () {
            const value = $(this).data('value');
            $('.popup-star').each(function () {
                $(this).css('color', $(this).data('value') <= value ? '#fbc02d' : '#ccc');
            });
        }).on('mouseout', function () {
            $('.popup-star').each(function () {
                $(this).css('color', $(this).data('value') <= selectedRating ? '#fbc02d' : '#ccc');
            });
        }).on('click', function () {
            selectedRating = $(this).data('value');
            $('#popupRating').val(selectedRating);
            $('.popup-star').each(function () {
                $(this).css('color', $(this).data('value') <= selectedRating ? '#fbc02d' : '#ccc');
            });
        });

        // Submit review via AJAX
        $('#submitPopupReview').on('click', function (e) {
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
                success: function (response) {
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
                error: function (xhr) {
                    if (xhr.status === 401) {
                        toastr.error('You must be logged in to submit a review.');
                    } else if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        toastr.error(errors);
                    } else {
                        toastr.error(' Error submitting review. Please try again.');
                    }
                },
                complete: function() {
                    $('#submitPopupReview').prop('disabled', false);
                }
            });
        });
    });
    </script>

@endsection
