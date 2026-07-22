<section class="latest_review_part_footer populr-alternative" style="background-color: #f9fafb !important; padding: 60px 0 !important;">
    <style>
        /* Responsive adjustments */
        @media (max-width: 575px) {
            .latest_review_part_footer {
                padding: 40px 0 !important;
            }
            .latest_review_part_footer .container {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .latest_review_part_footer .reviews_slider {
                margin: 0 !important;
            }
            .latest_review_part_footer .review_card {
                height: auto !important;
                min-height: 240px !important;
                padding: 16px !important;
            }
        }
    </style>
    <div class="section_hed" data-aos="fade-up" data-aos-duration="1000" style="margin-bottom: 30px;">
        <div class="container">
            <div class="slider_h" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="head_box" style="margin: 0;">
                    <!-- <h2 style="color: #0d1b2a !important; font-size: 28px; font-weight: 700; margin: 0;">{{ $title ?? static_text('latest_reviews') ?? 'Recent reviews' }}</h2> -->
                    <h2 style="color: #0d1b2a !important; font-size: 28px; font-weight: 700; margin: 0; text-transform:none;">Latest reviews</h2>
                </div>
                <div class="reviews-slider-nav" style="display: flex; gap: 8px;">
                    <button class="reviews-prev" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #cbd5e0; background: #ffffff; color: #4a5568; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#4a5568'; this.style.background='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.background='#ffffff';"><i class="fas fa-chevron-left"></i></button>
                    <button class="reviews-next" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #06498b; background: #ffffff; color: #06498b; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#06498b'; this.style.color='#ffffff';" onmouseout="this.style.background='#ffffff'; this.style.color='#06498b';"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="reviews_block p-0" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
            <div class="row">
                <div class="latest-reviews-slider reviews_slider" style="margin: 0 -10px;">
                    @foreach ($latestReviews->chunk(2) as $chunk)
                        <div class="review-slide-column" style="padding: 10px; box-sizing: border-box;">
                            @foreach ($chunk as $review)
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

                                @if ($business && $businessTranslation)
                                    <div class="review_card light" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); height: auto; min-height: 260px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; transition: transform 0.2s, box-shadow 0.2s; margin-bottom: 20px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.05)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.02)';">
                                        <!-- Top part: user icon, name, rating -->
                                        <div class="review-card-top" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                            <div class="user-avatar" style="width: 44px; height: 44px; border-radius: 50%; overflow: hidden; background: #003f7d; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                @if ($user && $user->profile_image && $user->profile_image !== 'front/img/default.png')
                                                    <img src="{{ asset($user->profile_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <span style="font-weight: 600; color: #ffffff; font-size: 18px;">{{ strtoupper(substr($userName, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="user-info" style="display: flex; flex-direction: column; gap: 2px;">
                                                <div class="user-name" style="font-size: 14px; font-weight: 600; color: #1a202c; line-height: 1.2;">{{ $userName }}</div>
                                                @if ($user && $user->job_title)
                                                    <div class="user-job-title" style="font-size: 12px; color: #718096; line-height: 1.2;">{{ $user->job_title }}</div>
                                                @endif
                                                @if ($user && $user->company_size)
                                                    <div class="user-company-size" style="font-size: 12px; color: #718096; line-height: 1.2;">{{ $user->company_size }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Middle part: Title & Description -->
                                        <div class="review-card-middle" style="flex-grow: 1; margin-bottom: 12px; overflow: hidden;">
                                            <h5 style="color: #0d1b2a; font-size: 14px; font-weight: 600; margin: 0 0 6px 0; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;">{{ $reviewTitle }}</h5>
                                            <div class="rating-stars" style="display: flex; gap: 2px; margin-bottom: 8px;">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= floor($review->rating))
                                                        <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                                    @else
                                                        <i class="far fa-star text-warning" style="font-size: 12px;"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <p style="font-size: 13.5px; color: #4a5568; line-height: 1.4; margin: 0; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                                {{ $reviewDescription ? strip_tags($reviewDescription) : 'No review content available.' }}
                                            </p>
                                        </div>

                                        <!-- Bottom part: Reviewed Business (icon & name) -->
                                        <div class="review-card-bottom" style="border-top: 1px solid #edf2f7; padding-top: 12px; display: flex; align-items: center; gap: 8px; margin-top: auto;">
                                            <div class="business-avatar" style="width: 28px; height: 28px; border-radius: 50%; overflow: hidden; background: #f7fafc; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                                <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;">
                                            </div>
                                            <div class="business-info" style="display: flex; flex-direction: column; overflow: hidden; min-width: 0; margin: 0; padding: 0; gap: 0;">
                                                <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $businessTranslation->slug]) }}" style="text-decoration: none; display: block; margin: 0; padding: 0;">
                                                    <span style="font-size: 13px; font-weight: 600; color: #2d3748; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; line-height: 1.2; margin: 0; padding: 0;">{{ $businessName }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
