@extends('user_layout.master')
{{-- {{ dd($translated_data) }} --}}
@section('meta_title', isset($homeContents['meta_home_title']) ? $homeContents['meta_home_title'] : 'Default Title')
@section('meta_description', isset($homeContents['meta_home_description']) ? $homeContents['meta_home_description'] :
    'Default Description')
@section('content')
    <section class="banner_sec dark" style="background-color: #003F7D;">
        <div class="bubble-wrp">
            <?php
            $headerBackgroundImage = $homeContantImages->get('header_background_img');
            $headerImage = $homeContantImages->get('header_img');
            $trustedBrandImages = $homeContantImages->get('trusted_brands_img');
            $aiLeftImage = $homeContantImages->get('ai_section_left_img');
            $aiRightImage = $homeContantImages->get('ai_section_right_img');
            $findToolRightImage = $homeContantImages->get('find_tool_right_img');
            $findToolLeftImage = $homeContantImages->get('find_tool_left_img');
            $verifiedImage = $homeContantImages->get('user_reviews_img');
            $featureImage = $homeContantImages->get('price_compare_img');
            $indepentImage = $homeContantImages->get('independent_img');
            $reviewRightImage = $homeContantImages->get('review_section_right_img');
            $reviewLeftImage = $homeContantImages->get('review_section_left_img');
            $aiImage = $homeContantImages->get('ai_send_img');

            ?>
            @if (isset($headerBackgroundImage))
                <img src="{{ asset($headerBackgroundImage->meta_value) }}" alt="{{ $headerBackgroundImage->meta_key }}">
            @else
                <img src="{{ asset('front/img/bnnr-bg.png') }}" alt="">
            @endif
        </div>
        <div class="banner_content">
            <div class="container">
                <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                    <div class="banner_text_col">
                        <div class="banner_content_inner">
                            <h1>{{ $homeContents['header_title'] ?? null }}</h1>
                            <p>{{ $homeContents['header_description'] ?? null }}</p>
                            @livewire('search-bar', ['placeholder' => $homeContents['placeholder_text'] ?? 'Search...'])
                        </div>
                    </div>
                    <div class="banner_image_col">
                        <div class="banner_image">
                            @if (isset($headerImage))
                                <img src="{{ asset($headerImage->meta_value) }}" alt="{{ $headerImage->meta_key }}">
                            @else
                                <img src="{{ asset('front/img/banner_image.png') }}" alt="">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end  -->
    <!-- section trusted brands marquee -->
    <section class="trusted-brands">
        <div class="container">
            <div class="trst-brnd-wrp" style="display: none;">
                {{-- <div class="brnd-wrp-lft">
                    <p class="m-0 brnd-p-txt">
                        {{ $homeContents['trusted_brands_text'] ?? null }}
                    </p>
                </div> --}}

                <div class="trust-brnd-marque">
                    @if (isset($trustedBrapPndImages))
                        @php
                            $imageIds = json_decode($trustedBrandImages->meta_value);
                        @endphp
                        @if (is_array($imageIds))
                            @foreach ($imageIds as $imageId)
                                @php
                                    $media = \App\Models\HomeContentMedia::find($imageId);
                                @endphp
                                <div class="marq-innr">
                                    <img src="{{ asset($media->file_path) }}" alt="{{ $media->meta_key }}">
                                </div>
                            @endforeach
                        @else
                            <img src="{{ asset($trustedBrandImages->meta_value) }}"
                                alt="{{ $trustedBrandImages->meta_key }}" style="width: 100px; height: auto;">
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- section most-popular -->
    <section class="most-populr-sec light p_120">
        <div class="container">
            <div class="most-popular-wrp" data-aos="fade-up" data-aos-duration="1000">

                @if (isset($categories) && $categories->isNotEmpty())

                    <div class="most_hed">
                        <h2 class="text-center">{{ $homeContents['most_popular'] ?? 'Most Popular' }}</h2>
                    </div>
                    <div class="popular-accordion-wrp">
                        <div class="accordion" id="accordionExample">
                            @foreach ($categories as $key => $category)
                                @if ($category->translations && $category->businesses->isNotEmpty())
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $key }}">
                                            <button class="accordion-button {{ $key != 0 ? 'collapsed' : '' }}"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $key }}"
                                                aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $key }}">
                                                <div class="accor-img" style="background-color: #fff">
                                                    {{-- <img src="{{ asset('front/img/accordion-img1.png') }}" alt=""> --}}
                                                      <img src="{{ $category->media ? asset($category->media->dir_path . '/' . $category->media->file_name) : asset('images/no-image.png') }}">
                                                </div> {{ $category->translations->name }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $key }}"
                                            class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}"
                                            aria-labelledby="heading{{ $key }}"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="accordion-bdy-wrp">
                                                    <div class="accor-bdy-hd">
                                                        <div class="row align-items-center">
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="accor-lft-img">
                                                                    {{-- <img src="{{ asset('front/img/accor-bdy-img.png') }}"
                                                                        alt=""> --}}
                                                                        <img src="{{ asset(($category->imageMedia?->dir_path ?? 'images') . '/' . ($category->imageMedia?->file_name ?? 'default-category.png')) }}" alt="Category Icon">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="accor-txt-contnt">
                                                                    {!! $category->translations->description !!}
                                                                    <div class="accor-btn">
                                                                        <a  onclick="changeCategory('{{ $category->translations->slug ?? '' }}')"
                                                                            class="cta cta_white">{{ $homeContents['campare_business'] ?? 'Compare Businesses' }}</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accor-bdy-btm">
                                                        <div class="row accor-bdy-row">
                                                            @if ($category->businesses->isNotEmpty())
                                                                @php
                                                                    $businesses = $category->businesses
                                                                        ->sortByDesc('reviews_avg_rating')
                                                                        ->take(3);
                                                                @endphp
                                                                @if ($category->businesses->isNotEmpty())
                                                                    @php
                                                                        $businesses = $category->businesses
                                                                            ->sortByDesc('reviews_avg_rating')
                                                                            ->take(3);
                                                                    @endphp

                                                                    @foreach ($businesses as $index => $business)
                                                                        @php
                                                                            $businessTranslation = $business->translations->first();
                                                                            $review = $business->reviews->first();
                                                                            $reviewTranslation =
                                                                                $review && $review->translations
                                                                                    ? $review->translations->first()
                                                                                    : null;

                                                                            $avgRating = number_format(
                                                                                $business->reviews_avg_rating ?? 0,
                                                                                1,
                                                                            );
                                                                            $ratingsCount =
                                                                                $business->reviews->count() ?? 0;

                                                                            $isBestValue = $index === 0; // Highest rated business
                                                                        @endphp

                                                                        <div
                                                                            class="col-lg-4 {{ $isBestValue ? 'order-lg-2' : ($index === 1 ? 'order-lg-1' : 'order-lg-3') }}">
                                                                            <div
                                                                                class="review_card light top-rate-card {{ $isBestValue ? 'center-card-pack' : '' }}">
                                                                                <div class="inner_box_silder top-rate-innr top-rate-innr_2 ">

                                                                                    @if ($isBestValue)
                                                                                        <div class="best-value">
                                                                                            <p><img src="{{ asset('front/img/star.png') }}"
                                                                                                    alt="">BEST
                                                                                                VALUE</p>
                                                                                        </div>
                                                                                    @endif

                                                                                    <div
                                                                                        class="inn_sl_hed mst_hdn {{ $isBestValue ? 'mt-4' : '' }}">
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
                                                                                                    <h6 class="head">
                                                                                                        {{ $businessTranslation->name ?? 'Business' }}
                                                                                                    </h6>
                                                                                                    <div
                                                                                                        wire:key="wishlist-container-{{ $business->id }}">
                                                                                                        @livewire('wishlist', ['productId' => $business->id], key('wishlist-' . $business->id))
                                                                                                    </div>
                                                                                                </a>
                                                                                                </div>

                                                                                            </div>
                                                                                            <div class="tp-btm d-flex">
                                                                                                <div class="inn_ul">
                                                                                                    <div
                                                                                                        class="tab_star_li">
                                                                                                        @php
                                                                                                            $rating =
                                                                                                                $avgRating >
                                                                                                                0
                                                                                                                    ? round(
                                                                                                                        $avgRating,
                                                                                                                    )
                                                                                                                    : 0;
                                                                                                        @endphp

                                                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                                                            <i
                                                                                                                class="fa-star {{ $i <= $rating ? 'fas text-warning' : 'far text-warning' }}"></i>
                                                                                                        @endfor
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="rate_box">
                                                                                                    {{ $avgRating }} |
                                                                                                    {{ $ratingsCount }}
                                                                                                    Reviews
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="slider_content_sec">
                                                                                        <div
                                                                                            class="content_para text-truncate-3-lines">
                                                                                            {{ $businessTranslation->description ?? 'Description not available.' }}
                                                                                        </div>
                                                                                        <div class="view-more-btn">
                                                                                            @if ($businessTranslation)
                                                                                                <a
                                                                                                    href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $businessTranslation->slug]) }}">
                                                                                                    Read More
                                                                                                </a>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="top-pro-box">
                                                                                        <div class="top-pro-btn">
                                                                                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $businessTranslation->slug]) }}"
                                                                                                class="cta cta_orange d-flex align-items-center">
                                                                                                {{ $homeContents['visit_website'] ?? 'Visit Website' }}
                                                                                                <div class="right-arw"><i
                                                                                                        class="fa-solid fa-arrow-right"></i>
                                                                                                </div>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- scetion exclusive deals -->
    {{-- <section class="xclusve-deal light p_120 ">
        <div class="section_hed xclu-m">
            <div class="container">
                <div class="slider_h">
                    <div class="head_box">
                        <h2 class="text-center"> {{ $homeContents['exclusive_deals'] ?? null }}</h2>
                    </div>

                    <div class="review_box text-right revw-width">
                        @if ($exclusive_products->count())
                        <a class="cta cta_white"> {{ $homeContents['all_exclusive'] ?? null }}</a>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        @if ($exclusive_products->count())
            <div class="container">
                <div class="xclusve-wrp" data-aos="fade-up" data-aos-duration="1000">
                    <div class="xclusve-slider">
                        @foreach ($exclusive_products as $product)
                            <div class="xclusve-pack">
                                <a
                                href="{{ route('product.details', ['locale' => getCurrentLocale(), 'slug' => $product->businesses->first()->translations()->first()->slug ?? '#']) }}">
                                <div class="save">
                                    <div class="save-txt">
                                        <p class="size22">Save {{ $product->discount_percentage }}%</p>
                                        <div class="save-img">
                                            <img src="{{ asset('front/img/save-img.png') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                                   <div class="xclusve-img">
                                        <img src="{{ $product->product_icon ?? asset('front/img/default-product.png') }}"
                                            alt="">
                                    </div>
                                  <div class="xclusve-txt">
                                    <h3>
                                            {{ $product->businesses->first()?->translations->first()?->name ?? 'Business Title' }}
                                            - {{ $product->translations->name ?? '' }}
                                        </h3>
                                    @php
                                        $price = $product->prices->first();
                                    @endphp
                                    <p class="grey-txt">
                                        @if ($price)
                                            <span
                                                class="line-through">{{ $price->currency }}{{ number_format($price->price, 2) }}</span>
                                            <span
                                                class="orange-txt">{{ $price->currency }}{{ number_format($price->discount_price, 2) }}</span>
                                        @endif
                                    </p>
                                    <div class="xclu-txt-btn">
                                        <a href="{{ $product->translations->product_link }}" class="cta cta_white">
                                            {{ $homeContents['get_this_deal'] ?? 'Get This Deal' }}
                                        </a>
                                    </div>
                                </div>
                            </a>
                         </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="container text-center py-5">
                <h4 class="text-muted">
                    {{ $homeContents['no_exclusive_deals'] ?? 'No exclusive deals available at the moment.' }}</h4>
            </div>
        @endif
    </section> --}}
    <!--  -------------------------------------------slider section start ------------------------------------->
    <section class="outer_slider dark">
        {{-- <div class="section_hed" data-aos="fade-up" data-aos-duration="1000">
            <div class="container">
                <div class="slider_h">
                    <div class="head_box">
                        <h2> {{ $homeContents['latest_reviews'] ?? 'Latest Reviews' }}</h2>
                    </div>
                    <div class="review_box text-right">

                    </div>
                </div>
            </div>
        </div>
        <div class="reviews_block" data-aos="fade-up" data-aos-duration="1000">
            <div class="container">
                <div class="row">
                    <div class="latest-reviews-slider reviews_slider ">
                        @if ($reviews)
                            @foreach ($reviews as $group)
                                @foreach ($group as $review)
                                    @php
                                        $translation = optional($review->translations)->first();
                                        $business = $review->business;
                                        $user = $review->user;
                                        $businessRating = optional(optional($business)->reviews)->avg('rating');
                                        $businessName =
                                            optional(optional($business)->translations->first())->name ??
                                            'Default Business Name';
                                        $businessImage =
                                            $business && $business->image_id
                                                ? asset($business->image_id)
                                                : asset('front/img/default-business.png');
                                    @endphp
                                    <div class="review_card light">
                                        <div class="inner_box_silder ">
                                            <div class="inn_sl_hed">
                                                <div class="sli_img">
                                                <a   href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $business->translations()->first()->slug]) }}">
                                                    <img src="{{ asset($business->icon_id) }}" class="header_img"
                                                        alt="{{ $business->translations->first()->name ?? 'Business' }}">
                                                    </a>
                                                </div>

                                                <div class="sl_h">
                                                    <div class="inn_h">
                                                        <a   href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $business->translations()->first()->slug]) }}">
                                                        <div class="sl_main">
                                                            <h6 class="head">{{ $businessName }}</h6>
                                                            <div class="wishlist">
                                                                <livewire:wishlist :product-id="$business->id"
                                                                    :wire:key="'wishlist-'.$business->id" />
                                                            </div>
                                                        </div>
                                                    </a>
                                                    </div>
                                                    <div class="tp-btm d-flex">
                                                        <div class="inn_ul">
                                                            <div class="tab_star_li">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <span
                                                                        class="rating-{{ $i <= $review->rating ? 'on' : 'off' }} rate-{{ $i }}"
                                                                        data-rating="{{ $i }}"></span>
                                                                @endfor

                                                            </div>
                                                            <div><i class="fa-solid fa-angle-down"></i>
                                                            </div>

                                                        </div>
                                                        <div class="rate_box"> {{ number_format($businessRating, 1) }} |
                                                            {{ $business->reviews->count() }} ratings</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="slider_content_sec">
                                                <div class="first_box"> {{ $translation->title ?? 'Review Title' }} </div>
                                                <div class="content_para">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($translation->description), 150) }}

                                                </div>
                                            </div>
                                            <div class="joh_box">
                                                <div class="joh_img">
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
                                                <div class="joh_sec">
                                                    <div class="joh_head">
                                                        @if ($review->user && $review->user->user_type === 'admin')
                                                            {{ $review->public_name ?? 'Public' }}
                                                        @else
                                                            {{ $review->user->first_name ?? 'Anonymous' }}
                                                        @endif
                                                    </div>
                                                    <div class="joh_pos"> Position Here </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div> --}}
        @livewire('latest-reviews')
    </section>
    <!--  -------------------------------------------slider section end ------------------------------------->
    <!----------------------------------------- read section start --------------------------------------- -->
    {{-- <section class="read_sec_outer light p_120">
        <div class="section_hed" data-aos="fade-up" data-aos-duration="1000">
            <div class="container">
                <div class="slider_h">
                    <div class="head_box">
                        <h2>{{ $homeContents['read_article'] ?? 'Read Articles' }}</h2>
                    </div>
                    <div class="review_box text-right">
                        <a class="cta cta_white" href="{{ route('expert-guide', ['locale' => app()->getLocale()]) }}">
                            {{ $homeContents['view_all_article'] ?? null }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($articles))
            <div class="read_content_sec light" data-aos="fade-up" data-aos-duration="1000">
                <div class="container">
                    <div class="row">
                        @foreach ($articles as $article)
                            @php
                                $translation = $article->translationForCurrentLang;
                                $catTranslation = $article->category ? $article->category->translations->first() : null;
                            @endphp

                            @if ($translation && $translation->slug && $catTranslation && $catTranslation->slug)
                                <div class="col-lg-4 col-md-4">
                                    <a href="{{ route('expert-guide-article', [
                                        'locale' => session('lang_code', 'en-us'),
                                        'cat_slug' => $catTranslation->slug,
                                        'art_slug' => $translation->slug,
                                    ]) }}"
                                        class="in_cont_box" style="width:100%">
                                        <div class="read_img" style="width:100%">
                                            <div class="blog_thumb">
                                                <img class="r_img" src="{{ asset($article->image) }}" alt="">
                                            </div>
                                        </div>
                                        <div class="read_content_in">
                                            <div class="read_cont_h">
                                                <h3 class="read_text">
                                                    {{ $translation->preview_title ?? ($translation->title ?? 'No Title') }}
                                                </h3>
                                            </div>
                                            <div class="read_para">
                                                <p>{!! $translation->preview_description ?? ($translation->description ?? 'no Preview description available') !!}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section> --}}

    <!-- section right-tool -->
    <section class="right_tool_sec dark p_80">
        <div class="container">
            <div class="right-tool-wrp text-center" data-aos="fade-up" data-aos-duration="1000">
                <div class="otr_rgtool">
                    <h2>{{ $homeContents['find_tool'] ?? null }}<h2>
                </div>
                <div class="right-tool-pack">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="tool-card">
                                <div class="tool-card-img">
                                    @if (isset($verifiedImage))
                                        <img src="{{ asset($verifiedImage->meta_value) }}" alt="">
                                    @endif

                                    <!-- <img src="{{ asset('front/img/right-tool-img1.png') }}" alt=""> -->
                                </div>
                                <div class="tool-crd-bdy">
                                    <h3 class="tool_hed">{{ $homeContents['verify_user_review'] ?? null }}</h3>
                                    <p class="size18">{{ $homeContents['verify_review_description'] ?? null }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="tool-card">
                                <div class="tool-card-img">

                                    @if (isset($featureImage))
                                        <img src="{{ asset($featureImage->meta_value) }}" alt="">
                                    @else
                                        <img src="{{ asset('front/img/right-tool-img2.png') }}" alt="">
                                    @endif
                                    <!-- <img src="{{ asset('front/img/right-tool-img2.png') }}" alt=""> -->
                                </div>
                                <div class="tool-crd-bdy">
                                    <h3 class="tool_hed">{{ $homeContents['feature_price'] ?? null }}</h3>
                                    <p class="size18">
                                        {{ $homeContents['feature_price_description'] ?? null }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="tool-card">
                                <div class="tool-card-img">
                                    @if (isset($indepentImage))
                                        <img src="{{ asset($indepentImage->meta_value) }}" alt="">
                                    @endif
                                    <!-- <img src="{{ asset('front/img/right-tool-img3.png') }}" alt=""> -->
                                </div>
                                <div class="tool-crd-bdy">
                                    <h3 class="tool_hed"> {{ $homeContents['independent'] ?? null }}</h3>
                                    <p class="size18">{{ $homeContents['independent_description'] ?? null }} </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right-tool-btn text-center">
                    <a href="{{ route('category', ['locale' => app()->getLocale()]) }}" class="cta">
                        {{ $homeContents['get_button_lable'] ?? null }}
                    </a>
                </div>
            </div>
        </div>

        <div class="back-image1">
            @if (isset($findToolLeftImage))
                <img src="{{ asset($findToolLeftImage->meta_value) }}" class="image-pattern1"
                    alt="{{ $findToolLeftImage->meta_key }}">
            @endif
            <!-- <img src="{{ asset('front/img/right-tool-vector1.png') }}" class="image-pattern1" alt=""> -->
        </div>
        <div class="back-image2">
            @if (isset($findToolRightImage))
                <img src="{{ asset($findToolRightImage->meta_value) }}" class="image-pattern2"
                    alt="{{ $findToolRightImage->meta_key }}">
            @endif
            <!-- <img src="{{ asset('front/img/right-tool-vector2.png') }}" class="image-pattern2" alt=""> -->
        </div>
    </section>
@endsection

<script>
    // document.addEventListener("DOMContentLoaded", function() {
    //     let element = document.querySelector(".most-populr-sec");
    //     if (element) {
    //         element.scrollIntoView({
    //             behavior: 'smooth',
    //             block: 'start'
    //         });
    //         element.scrollIntoView({
    //             behavior: 'smooth',
    //             block: 'start'
    //         });
    //     }
    // });
</script>

<script>
    $(window).on('load', function() {
        $('body').addClass('HomeIndxPgCls');
    });
</script>
