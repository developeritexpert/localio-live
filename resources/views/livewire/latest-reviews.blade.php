<section class="latest_review_part_footer populr-alternative">
    <div class="section_hed" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
            <div class="slider_h">
                <div class="head_box">
                    <h2>{{ $title ?? static_text('latest_reviews') ?? 'Latest Reviews' }}</h2>
                </div>
                <div class="text-right">
                    {{-- @if (!request()->routeIs('help-center' ,'category' ,'expert-guide' ,'vendor-how-it-work' ,'expert-guide-category','expert-guide-article'))
                    <a class="cta cta_white" href="{{ route('top-rated-product') }}">Explore Products</a>
                @endif --}}

                </div>
            </div>
        </div>
    </div>
{{--<div class="reviews_block" data-aos="fade-up" data-aos-duration="1000"> --}}
        <div class="reviews_block light p_50" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
            <div class="row">
                <div class="latest-reviews-slider reviews_slider">
                    @foreach ($latestReviews as $review)
                        @php
                            $translation = $review->translations->first();
                            $business = $review->business;
                            $businessTranslation = $business?->translations?->firstWhere('lang_id', $lang_id);
                            $businessName = $businessTranslation?->name ?? 'Default Business Name';
                            $businessRating = $review->rating ?? 0;
                            $reviewTitle = $translation?->title ?? 'No Title';
                            $reviewDescription = $translation?->description ?? null;
                            $user = $review->user ?? null;
                            $userImage = $user?->profile_image ?? $default_image;
                            $userName = $user?->first_name ?? 'Anonymous';
                            $userType = $user?->user_type ?? 'Anonymous';
                        @endphp

                        @if ($business  && $businessTranslation)
                            <div class="review_card light">
                                {{-- <div class="inner_box_silder populr-alternative"> --}}
                                    <div class="inner_box_silder">
                                    <div class="inn_sl_hed">
                                        <div class="sli_img">
                                            <a  href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $businessTranslation->slug]) }}">
                                            <img src="{{ asset($business->icon_id ?? 'no-image.png') }}"
                                                 class="header_img"
                                                 alt="{{ $businessName }}">
                                                 <a>
                                        </div>
                                        <div class="sl_h">
                                            <div class="inn_h">
                                                <a  href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $businessTranslation->slug]) }}">
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
                                                        <span>
                                                            {{-- {{ $review->rating }} --}}
                                                        </span>
                                                        <span class="rating">
                                                            {{-- @for ($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : 'empty' }}" data-rating="{{ $i }}"></i>
                                                            @endfor --}}

                                                            {{-- <div class="inn_ul"> --}}
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
                                                            {{-- </div> --}}


                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="rate_box">
                                                    {{ number_format($businessRating, 1) }} |
                                                    {{ $business->reviews->count() }} ratings
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="slider_content_sec">
                                        <div class="first_box">{{ $reviewTitle }}</div>
                                        <div class="content_para">
                                            {{ $reviewDescription ? \Illuminate\Support\Str::limit(strip_tags($reviewDescription), 150) : 'No review content available.' }}
                                        </div>
                                    </div>

                                    <div class="joh_box">
                                        <div class="joh_img">
                                            <img src="{{ asset($userImage) }}"
                                                 class="img-fluid profile-circle"
                                                 {{-- style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;" --}}
                                                 alt="User Image">
                                        </div>
                                        <div class="joh_sec">
                                            <div class="joh_head">{{ $userName }}</div>
                                            {{-- <div class="joh_pos">{{ $userType }}</div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
