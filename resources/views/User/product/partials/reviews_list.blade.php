<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        @if($allReviews->isEmpty())
            <div class="p-4 text-center text-muted">
                <p>No reviews found matching the selected criteria.</p>
            </div>
        @else
            @foreach ($allReviews as $review)
                <div class="review_detl populr-alternative" id="review-{{ $review->id }}" data-aos="fade-up" data-aos-duration="1000" style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px; position: relative;">
                    
                    <!-- Top right actions (Date & Copy link) -->
                    <div class="review-actions-top-right" style="position: absolute; top: 24px; right: 24px; display: flex; align-items: center; gap: 16px;">
                        <span style="font-size: 13px; color: #777; font-weight: 500;">{{ $review->created_at->diffForHumans() }}</span>
                        <a href="javascript:void(0)" onclick="copyToClipboard('{{ url()->current() }}#review-{{ $review->id }}')" title="Copy link to review" style="color: #a0aec0; transition: color 0.2s; font-size: 15px;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                            <i class="fas fa-link"></i>
                        </a>
                    </div>

                    <div class="reviw_hd" style="margin-bottom: 20px; border-bottom: none; padding-bottom: 0;">
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
                                                {{ strtoupper(substr($review->user->first_name ?? 'A', 0, 1)) }}
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
                                        {{ $review->user ? $review->user->displayName() : 'Anonymous' }}
                                    @endif
                                @if($review->user && $review->user->job_title)
                                    <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2;">{{ $review->user->job_title }}</p>
                                @endif
                                @if($review->user && $review->user->company_size)
                                    <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2;">{{ static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="review_text size18" style="padding-right: 0;">
                        <h5 class="size22 big-bld" style="font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 12px;">
                            {{ $review->translations->first()->title ?? '' }}
                        </h5>
                        
                        <!-- Ratings below headline -->
                        <div class="rating light" style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
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
                            <!-- <div class="rate_box" style="margin: 0; font-size: 14px; font-weight: 600; color: #555;">
                                {{ number_format($review->rating, 1) }}
                            </div> -->
                        </div>

                        <div style="color: #444; line-height: 1.6; font-size: 14px; margin-bottom: 0;">
                            {!! nl2br(e(strip_tags($review->translations->first()->description ?? 'No Description Available'))) !!}
                        </div>
                    </div>

                    <!-- Bottom right actions (Report flag) -->
                    <div class="review-actions-bottom-right" style="display:flex; justify-content: end;">
                        <a href="mailto:support@example.com?subject=Report Review ID: {{ $review->id }}" title="Report this review" style="color: #a0aec0; transition: color 0.2s;" onmouseover="this.style.color='#e53e3e';" onmouseout="this.style.color='#a0aec0';">
                            <i class="fas fa-flag"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
        <div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
            <a href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}"
                style="font-size: 14px; font-weight: 600; color: #002347; text-decoration: none;">View more reviews</a>
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
                    <div class="review_detl" id="review-{{ $review->id }}" data-aos="fade-up" data-aos-duration="1000" style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px; position: relative;">
                        
                        <!-- Top right actions (Date & Copy link) -->
                        <div class="review-actions-top-right" style="position: absolute; top: 24px; right: 24px; display: flex; align-items: center; gap: 16px;">
                            <span style="font-size: 13px; color: #777; font-weight: 500;">{{ $review->created_at->diffForHumans() }}</span>
                            <a href="javascript:void(0)" onclick="copyToClipboard('{{ url()->current() }}#review-{{ $review->id }}')" title="Copy link to review" style="color: #a0aec0; transition: color 0.2s; font-size: 15px;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                                <i class="fas fa-link"></i>
                            </a>
                        </div>

                        <div class="reviw_hd" style="margin-bottom: 20px; border-bottom: none; padding-bottom: 0;">
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
                                                    {{ strtoupper(substr($review->user->first_name ?? 'A', 0, 1)) }}
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
                                            {{ $review->user ? $review->user->displayName() : 'Anonymous' }}
                                        @endif
                                    @if($review->user && $review->user->job_title)
                                        <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2;">{{ $review->user->job_title }}</p>
                                    @endif
                                    @if($review->user && $review->user->company_size)
                                        <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2;">{{ static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="review_text size18" style="padding-right: 0;">
                            <h5 class="size22 big-bld" style="font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 12px;">
                                {{ $review->translations->first()->title ?? '' }}
                            </h5>
                            
                            <!-- Ratings below headline -->
                            <div class="rating light" style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
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
                                <div class="rate_box" style="margin: 0; font-size: 14px; font-weight: 600; color: #555;">
                                    {{ number_format($review->rating, 1) }}
                                </div>
                            </div>

                            <div style="color: #444; line-height: 1.6; font-size: 14px; margin-bottom: 0;">
                                {!! nl2br(e(strip_tags($review->translations->first()->description ?? 'No Description Available'))) !!}
                            </div>
                        </div>

                        <!-- Bottom right actions (Report flag) -->
                        <div class="review-actions-bottom-right" style="position: absolute; bottom: 24px; right: 24px;">
                            <a href="mailto:support@example.com?subject=Report Review ID: {{ $review->id }}" title="Report this review" style="color: #a0aec0; transition: color 0.2s;" onmouseover="this.style.color='#e53e3e';" onmouseout="this.style.color='#a0aec0';">
                                <i class="fas fa-flag"></i>
                            </a>
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
                                @if ($review->user && $review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                                    <img src="{{ asset($review->user->profile_image) }}"
                                        class="img-fluid profile-circle"
                                        style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                        alt="User Image">
                                @else
                                    <div class="profile-circle" style="width: 48px; height: 48px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: white; font-weight: bold; font-size: 20px;">
                                            @if ($review->user && $review->user->user_type === 'admin')
                                                {{ strtoupper(substr($review->public_name ?? 'P', 0, 1)) }}
                                            @else
                                                {{ strtoupper(substr($review->user->first_name ?? 'A', 0, 1)) }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="asn-rating">
                                <h6 style="font-size: 15px; font-weight: 600; margin-bottom: 2px;">
                                    @if ($review->user && $review->user->user_type === 'admin')
                                        {{ $review->public_name ?? 'Public' }}
                                    @else
                                        {{ $review->user ? $review->user->displayName() : 'Anonymous' }}
                                    @endif
                                @if($review->user && $review->user->job_title)
                                    <p style="font-size: 13px; color: #777; margin: 2px 0 0 0;">{{ $review->user->job_title }}</p>
                                @endif
                                @if($review->user && $review->user->company_size)
                                    <p style="font-size: 13px; color: #777; margin: 2px 0 0 0;">{{ static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size }}</p>
                                @endif
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
                        <p class="size22 big-bld" style="margin-bottom: 4px;">
                            {{ $review->translations->first()->title ?? '' }}
                        </p>
                        <p style="font-size: 13px; color: #777; margin: 0 0 10px 0;">{{ $review->created_at->diffForHumans() }}</p>
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

<script>
    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Link to review copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        } else {
            // fallback
            let textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                alert('Link to review copied to clipboard!');
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
            }
            document.body.removeChild(textArea);
        }
    }
</script>
