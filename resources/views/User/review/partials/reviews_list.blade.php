<div class="review-cntnt-btm">
    @if ($reviews->isNotEmpty())
        @foreach ($reviews as $review)
            <div class="review_detl populr-alternative review-card" id="review-{{ $review->id }}" data-review-id="{{ $review->id }}" data-aos="fade-up" data-aos-duration="1000" style="background-color:#f7f9fb; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px; position: relative;">
                
                <!-- Top right actions (Date & Copy link) -->
                <div class="review-actions-top-right" style="position: absolute; top: 24px; right: 24px; display: flex; align-items: center; gap: 16px;">
                    <span style="font-size: 12px; color: #777; font-weight: 500;">{{ $review->created_at->diffForHumans() }}</span>
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
                                    {{ $review->user ? (method_exists($review->user, 'displayName') ? $review->user->displayName() : ($review->user->first_name ?? 'Anonymous')) : 'Anonymous' }}
                                @endif
                            </h6>
                            @if($review->user && $review->user->job_title)
                                <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight:500;">{{ $review->user->job_title }}</p>
                            @endif
                            @if($review->user && $review->user->company_size)
                                <p style="font-size: 13px; color: #777; margin: 0; line-height: 1.2; font-weight:500;">{{ function_exists('static_text') ? (static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size) : $review->user->company_size }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="review_text size18" style="padding-right: 0;">
                    @php
                        $translation = $review->translations->firstWhere('language_id', getCurrentLanguageID());
                        $title = $translation?->title ?? $review->translations->first()?->title ?? '';
                        $description = $translation?->description ?? $review->description ?? 'No Review Available';
                    @endphp

                    @if(!empty($title))
                        <h5 class="size22 big-bld" style="font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 12px;">
                            {{ $title }}
                        </h5>
                    @endif
                    
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
                    </div>

                    <div style="color: #444; line-height: 1.6; font-size: 14px; margin-bottom: 0;">
                        <p class="review-text">
                            {!! nl2br(e(strip_tags($description))) !!}
                        </p>
                    </div>
                </div>

                <div class="translaton-txt mt-2">
                    <a href="javascript:void(0);"
                        class="btn-toggle-translation"
                        data-mode="translation"
                        data-review-id="{{ $review->id }}"
                        data-type="original"
                        data-language-id="{{ getCurrentLanguageID() }}">
                        View Original
                    </a>
                </div>

                <!-- Bottom right actions (Report flag & Copy link) -->
                <div class="review-actions-bottom-right" style="display:flex; justify-content: end; gap:10px;">
                    <a href="mailto:support@example.com?subject=Report Review ID: {{ $review->id }}" title="Report this review" style="color: #a0aec0; transition: color 0.2s;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                        <i class="fas fa-flag"></i>
                    </a>
                    <a href="javascript:void(0)" onclick="copyToClipboard('{{ url()->current() }}#review-{{ $review->id }}')" title="Copy link to review" style="color: #a0aec0; transition: color 0.2s; font-size: 15px;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                        <i class="fas fa-link"></i>
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <div class="p-4 text-center text-muted">
            <p>No reviews found matching the selected criteria.</p>
        </div>
    @endif
</div>

<div class="pagination-wrap mt-4">
    {{ $reviews->links('pagination::bootstrap-4') }}
</div>
