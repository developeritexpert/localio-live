@extends('user_layout.master')


@section('content')

<!-- Banner Section -->

<section class="banner_sec help-cntr-bnr inr-bnr dark" style="background-color: #003F7D;">
    <div class="bubble-wrp">
        <img src="{{ asset($work->banner_image_right) }}" alt="">
    </div>
    <div class="banner_content">
        <div class="container">
            <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                <div class="banner_text_col">
                    <div class="banner_content_inner bnr_inr_contnt">
                        <h1>{{ $work->banner_title}}</h1>
                        <p>{{ $work->banner_description }}</p>

                    </div>
                </div>
                <div class="banner_image_col">
                    <div class="banner_image">
                        <img src="{{ asset($work->banner_image_left) }}" class="banner_top_image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Steps (from PageTile translations) -->
<section class="works">
    <div class="container">
        <h2>{{ $work->main_heading ?? 'How It Works' }}</h2>
        <div class="row">
            @foreach ($pageTileTranslationRightTool as $tile)
                @foreach ($tile->translations as $item)
                    <div class="col-lg-4">
                        <div class="inner_part_boxx">
                            <div class="part_1_boxs">
                                @if ($item->image)
                                    <img src="{{ asset($item->image) }}" alt="icon" class="img-fluid" style="max-height: 80px;">
                                @endif
                            </div>
                            <div class="part_2_boxs">
                                <h2>{{ $item->title ?? '' }}</h2>
                                <p>{{ $item->description ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</section>

<!-- Section 1 -->
<section class="few_clicks p_50">
    <div class="container">
        <h2>{{ $work->section_1_title ?? 'Find the Best Software in Just a Few Clicks' }}</h2>
        <p>{{ $work->section_1_description ?? 'We help you discover, compare, and choose the right software—based on real reviews and global insights' }}</p>
    </div>


    <div class="accor-bdy-btm">
        <div class="container">
            <div class="row accor-bdy-row">
                @php
                    $orders = ['order-lg-2', 'order-lg-1', 'order-lg-3']; // Center, Left, Right
                @endphp

                @forelse ($topBusinesses as $index => $business)
                    @php
                        $businessTranslation = $business->translations->first();
                        $review = $business->reviews->first();
                        $reviewTranslation = $review && $review->translations ? $review->translations->first() : null;

                        $avgRating = number_format($business->reviews_avg_rating ?? 0, 1);
                        $ratingsCount = $business->reviews->count() ?? 0;
                        $isBestValue = $index === 0;
                        $orderClass = $orders[$index] ?? 'order-lg-3'; // Fallback to right
                    @endphp

                    <div class="col-lg-4 {{ $orderClass }}">
                        <div class="review_card light top-rate-card {{ $isBestValue ? 'center-card-pack' : '' }}">
                            <div class="inner_box_silder top-rate-innr top-rate-innr_2">
                                @if ($isBestValue)
                                    <div class="best-value">
                                        <p><img src="{{ asset('front/img/star.png') }}" alt="">BEST VALUE</p>
                                    </div>
                                @endif

                                <div class="inn_sl_hed mst_hdn {{ $isBestValue ? 'mt-4 image-above' : '' }}">
                                    <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $businessTranslation->slug]) }}">
                                        <div class="sli_img">
                                            <img class="slider_img"
                                                src="{{ $business->icon_id ? asset($business->icon_id) : asset('front/img/slider' . ($index + 1) . '_img.svg') }}"
                                                alt="">
                                        </div>
                                    </a>
                                    <div class="sl_h">
                                        <div class="inn_h">
                                            <div class="sl_main">
                                                <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $businessTranslation->slug]) }}">
                                                    <h6 class="head">{{ $businessTranslation->name ?? 'Business' }}</h6>
                                                    <div wire:key="wishlist-container-{{ $business->id }}">
                                                        @livewire('wishlist', ['productId' => $business->id], key('wishlist-' . $business->id))
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="tp-btm d-flex">
                                            <div class="inn_ul">
                                                <div class="tab_star_li">
                                                    {{-- @php $rating = $avgRating > 0 ? round($avgRating) : 0; @endphp --}}

                                                    {{-- @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fa-star {{ $i <= $rating ? 'fas text-warning' : 'far text-muted' }}"></i>
                                                    @endfor --}}

                                                    <div class="rating-stars">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= floor($avgRating))
                                                                <i class="fas fa-star text-warning"></i>
                                                            @elseif ($i - 0.5 <= $avgRating)
                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                            @else
                                                                <i class="far fa-star text-warning"></i>
                                                            @endif
                                                        @endfor
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="rate_box">
                                                {{ $avgRating }} | {{ $ratingsCount }} ratings
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="slider_content_sec">
                                    <div class="content_para text-truncate-3-lines">
                                        {{ $businessTranslation->description ?? 'Description not available.' }}
                                    </div>
                                    <div class="view-more-btn">
                                        <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $businessTranslation->slug]) }}">
                                            Read More
                                        </a>
                                    </div>
                                </div>

                                <div class="top-pro-box">
                                    <div class="top-pro-btn">
                                        <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $businessTranslation->slug]) }}"
                                            class="cta cta_orange d-flex align-items-center">
                                            {{ $homeContents['visit_website'] ?? 'Visit Website' }}
                                            <div class="right-arw">
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>No top-rated businesses available right now.</p>
                    </div>
                @endforelse

            </div>
        </div>

    </div>


</section>

<!-- Section 2 -->
<section class="ready_to">
    <div class="container">
        <h2>{{ $work->section_2_title ?? 'Ready to Get Started?' }}</h2>
        <p>{{ $work->section_2_description ?? '' }}</p>
        @if (!empty($work->section_2_button))
        <div class="btn-holdr">
            <a href="#" class="cta cta_orange mt-5">{{ $work->section_2_button }}</a>
        </div>
        @endif
    </div>
</section>

<!-- Section 3 -->
<section class="why_trust">
    <div class="container">
        <div class="inner_trust">
            <div class="row">
                <div class="col-lg-6">
                    <div class="part_1_trust">
                        @if ($work->section_3_image)
                            <img src="{{ asset($work->section_3_image) }}" alt="Why Trust Us" class="img-fluid" />
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="inner_2tru">
                        <h2>{{ $work->section_3_title ?? 'Why Trust Us' }}</h2>
                        <p>{{ $work->section_3_description ?? '' }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<section>
    @livewire('latest-reviews')
</section>
@endsection
