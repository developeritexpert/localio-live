<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        @if($allReviews->isEmpty())
            <div class="p-4 text-center text-muted">
                <p>No reviews found matching the selected criteria.</p>
            </div>
        @else
            @foreach ($allReviews as $review)
                <div class="review_detl populr-alternative" data-aos="fade-up" data-aos-duration="1000">
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
                                <p style="font-size: 14px; color: #777; margin: 4px 0 0 0;">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="rating light" style="display: flex; align-items: center; gap: 8px;">
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
                            <div class="rate_box" style="margin: 0; font-size: 15px; font-weight: 600; color: #555;">
                                {{ number_format($review->rating, 1) }}
                            </div>
                        </div>
                    </div>
                    <div class="review_text size18">
                        <p class="size22 big-bld">
                            {{ $review->translations->first()->title ?? '' }}
                        </p>
                        <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}</p>
                    </div>
                </div>
            @endforeach
        @endif
        <div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
            <a href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}"
                style="font-size: 16px; font-weight: 600; color: #06498b; text-decoration: none;">View more reviews</a>
        </div>
    </div>
    
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
        @if($ourReviews->isEmpty())
            <div class="p-4 text-center text-muted">
                <p>No reviews found matching the selected criteria.</p>
            </div>
        @else
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
                                    <p style="font-size: 14px; color: #777; margin: 4px 0 0 0;">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="rating light" style="display: flex; align-items: center; gap: 8px;">
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
                                </div>
                                <span class="rating-score size18" style="margin: 0; font-size: 15px; font-weight: 600; color: #555;">{{ number_format($review->rating, 1) }}</span>
                            </div>
                        </div>
                        <div class="review_text size18">
                            <p class="size22 big-bld">
                                {{ $review->translations->first()->title ?? '' }}
                            </p>
                            <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
        <div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
            <a href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}"
                style="font-size: 16px; font-weight: 600; color: #06498b; text-decoration: none;">View all reviews</a>
        </div>
    </div>
    
    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
        @if($trustpilotReviews->isEmpty())
            <div class="p-4 text-center text-muted">
                <p>No reviews found matching the selected criteria.</p>
            </div>
        @else
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
                                <p style="font-size: 14px; color: #777; margin: 4px 0 0 0;">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="rating light" style="display: flex; align-items: center; gap: 8px;">
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
                            <div class="rate_box" style="margin: 0; font-size: 15px; font-weight: 600; color: #555;">
                                {{ number_format($review->rating, 1) }}
                            </div>
                        </div>
                    </div>
                    <div class="review_text size18">
                        <p class="size22 big-bld">
                            {{ $review->translations->first()->title ?? '' }}
                        </p>
                        <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}</p>
                    </div>
                </div>
            @endforeach
        @endif
        <div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
            <a href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}"
                style="font-size: 16px; font-weight: 600; color: #06498b; text-decoration: none;">View all reviews</a>
        </div>
    </div>
</div>
