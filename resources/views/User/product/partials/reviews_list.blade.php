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
                        <span style="font-size: 12px; color: #777; font-weight: 500;">{{ $review->created_at->diffForHumans() }}</span>
                        <!-- <a href="javascript:void(0)" onclick="copyToClipboard('{{ url()->current() }}#review-{{ $review->id }}')" title="Copy link to review" style="color: #a0aec0; transition: color 0.2s; font-size: 15px;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                            <i class="fas fa-link"></i>
                        </a> -->
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
                                    <p class="asn-rating-p" style="font-size: 13px; color: #777; margin: 0; line-height: 1.2;  font-weight:500;">{{ $review->user->job_title }}</p>
                                @endif
                                @if($review->user && $review->user->company_size)
                                    <p class="asn-rating-p" style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight:500;">{{ static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size }}</p>
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
                    <div class="review-actions-bottom-right" style="display:flex; justify-content: end; gap:10px;">
                        <a href="mailto:support@example.com?subject=Report Review ID: {{ $review->id }}" title="Report this review" style="color: #a0aec0; transition: color 0.2s;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                            <i class="fas fa-flag"></i>
                        </a>
                        <!-- <a href="javascript:void(0)" onclick="copyToClipboard('{{ url()->current() }}#review-{{ $review->id }}')" title="Copy link to review" style="color: #a0aec0; transition: color 0.2s; font-size: 15px;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                            <i class="fas fa-link"></i>
                        </a> -->
                        <a href="javascript:void(0)" aria-label="Copy Link" class="copy_link_icon share-btn" style="color: #a0aec0;" data-platform="copy" onclick="copyToClipboard('{{ url()->current() }}#review-{{ $review->id }}')" title="Copy link to review" >
                            <span class="svg svg-share">
                                <svg style="display:block;border-radius:999px;" focusable="false" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-4 -4 40 40">
                                    <path fill="currentColor"
                                        d="M24.412 21.177c0-.36-.126-.665-.377-.917l-2.804-2.804a1.235 1.235 0 0 0-.913-.378c-.377 0-.7.144-.97.43.026.028.11.11.255.25.144.14.24.236.29.29s.117.14.2.256c.087.117.146.232.177.344.03.112.046.236.046.37 0 .36-.126.666-.377.918a1.25 1.25 0 0 1-.918.377 1.4 1.4 0 0 1-.373-.047 1.062 1.062 0 0 1-.345-.175 2.268 2.268 0 0 1-.256-.2 6.815 6.815 0 0 1-.29-.29c-.14-.142-.223-.23-.25-.254-.297.28-.445.607-.445.984 0 .36.126.664.377.916l2.778 2.79c.243.243.548.364.917.364.36 0 .665-.118.917-.35l1.982-1.97c.252-.25.378-.55.378-.9zm-9.477-9.504c0-.36-.126-.665-.377-.917l-2.777-2.79a1.235 1.235 0 0 0-.913-.378c-.35 0-.656.12-.917.364L7.967 9.92c-.254.252-.38.553-.38.903 0 .36.126.665.38.917l2.802 2.804c.242.243.547.364.916.364.377 0 .7-.14.97-.418-.026-.027-.11-.11-.255-.25s-.24-.235-.29-.29a2.675 2.675 0 0 1-.2-.255 1.052 1.052 0 0 1-.176-.344 1.396 1.396 0 0 1-.047-.37c0-.36.126-.662.377-.914.252-.252.557-.377.917-.377.136 0 .26.015.37.046.114.03.23.09.346.175.117.085.202.153.256.2.054.05.15.148.29.29.14.146.222.23.25.258.294-.278.442-.606.442-.983zM27 21.177c0 1.078-.382 1.99-1.146 2.736l-1.982 1.968c-.745.75-1.658 1.12-2.736 1.12-1.087 0-2.004-.38-2.75-1.143l-2.777-2.79c-.75-.747-1.12-1.66-1.12-2.737 0-1.106.392-2.046 1.183-2.818l-1.186-1.185c-.774.79-1.708 1.186-2.805 1.186-1.078 0-1.995-.376-2.75-1.13l-2.803-2.81C5.377 12.82 5 11.903 5 10.826c0-1.08.382-1.993 1.146-2.738L8.128 6.12C8.873 5.372 9.785 5 10.864 5c1.087 0 2.004.382 2.75 1.146l2.777 2.79c.75.747 1.12 1.66 1.12 2.737 0 1.105-.392 2.045-1.183 2.817l1.186 1.186c.774-.79 1.708-1.186 2.805-1.186 1.078 0 1.995.377 2.75 1.132l2.804 2.804c.754.755 1.13 1.672 1.13 2.75z">
                                    </path>
                                </svg>
                            </span>
                        </a>

                    </div>
                </div>
            @endforeach
        @endif
        @php
            $currentLangCode = getCurrentLocale();
            $langObj = \App\Models\Language::where('lang_code', $currentLangCode)->first();
            $rSlug = !empty($langObj->reviews_slug) ? $langObj->reviews_slug : 'reviews';
            $bSlug = $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug ?? $business->translations->first()->slug ?? '';
        @endphp
        <div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
            <a href="{{ route('ReviewShow', ['locale' => $currentLangCode, 'slug' => $bSlug, 'reviews_slug' => $rSlug]) }}"
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
                                        <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight:500;">{{ $review->user->job_title }}</p>
                                    @endif
                                    @if($review->user && $review->user->company_size)
                                        <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight:500;">{{ static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size }}</p>
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
                            <a href="mailto:support@example.com?subject=Report Review ID: {{ $review->id }}" title="Report this review" style="color: #a0aec0; transition: color 0.2s;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                                <i class="fas fa-flag"></i>
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
        <div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
            <a href="{{ route('ReviewShow', ['locale' => $currentLangCode, 'slug' => $bSlug, 'reviews_slug' => $rSlug]) }}"
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
            <a href="{{ route('ReviewShow', ['locale' => $currentLangCode, 'slug' => $bSlug, 'reviews_slug' => $rSlug]) }}"
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
