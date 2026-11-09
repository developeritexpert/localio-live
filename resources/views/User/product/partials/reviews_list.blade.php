@php
    $hasUserRatings = $hasUserRatings ?? (isset($business) && $business->reviews->whereIn('status', ['published', 'active'])->count() >= 10);
@endphp




<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        @if($allReviews->isEmpty())
            <div class="p-4 text-center text-muted">
                <p>No reviews found matching the selected criteria.</p>
            </div>
        @else
            @foreach ($allReviews as $review)
                <div class="review_detl populr-alternative" id="review-{{ $review->id }}" data-aos="fade-up" data-aos-duration="1000" style="padding: 24px;margin-bottom: 24px; position: relative;">
                    
                    <!-- Top right actions (Date & Copy link) -->
                    <div class="review-actions-top-right" style="position: absolute; top: 24px; right: 24px; display: flex; align-items: center; gap: 16px;">
                        <span class="text-muted" style="font-size: 12px;">{{ $review->created_at->diffForHumans() }}</span>
                        <!-- <a href="javascript:void(0)" onclick="copyToClipboard('{{ url()->current() }}#review-{{ $review->id }}')" title="Copy link to review" style="color: #a0aec0; transition: color 0.2s; font-size: 15px;" onmouseover="this.style.color='#06498b';" onmouseout="this.style.color='#a0aec0';">
                            <i class="fas fa-link"></i>
                        </a> -->
                    </div>

                    <div class="reviw_hd" style="margin-bottom: 20px; border-bottom: none; padding-bottom: 0;">
                        <div class="ans_lft" style="display: flex; gap: 12px; align-items: flex-start;">
                            
<div class="asn-img" style="flex-shrink: 0;">
    @if ($review->user)
        <a href="{{ optional($review->user)->profileUrl() ?? route('user.profile', ['id' => $review->user_id]) }}"
            style="display: block; text-decoration: none;">

            @if ($review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                <img src="{{ asset($review->user->profile_image) }}"
                    class="img-fluid profile-circle"
                    style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                    alt="User Image">
            @else
                <div class="profile-circle"
                    style="width: 48px; height: 48px; border-radius: 50%; background-color: #003f7d; display: flex; align-items: center; justify-content: center;">

                    <span style="color: white; font-weight: bold; font-size: 20px;">
                        @if ($review->user->user_type === 'admin')
                            {{ strtoupper(substr($review->public_name ?? 'P', 0, 1)) }}
                        @else
                            {{ strtoupper(substr($review->user->first_name ?? 'A', 0, 1)) }}
                        @endif
                    </span>

                </div>
            @endif

        </a>
    @endif
</div>


                            <div class="asn-rating" style="display: flex; flex-direction: column; ">
                                <div style="font-size: 15px; font-weight: 600; margin: 0; color: #1e3050;">
                                    @if ($review->user && $review->user->user_type === 'admin')
                                        {{ $review->public_name ?? 'Public' }}
                                    @else
                                        
        @if($review->user)
            <a href="{{ optional($review->user)->profileUrl() ?? route('user.profile', ['id' => $review->user_id]) }}" style="color: inherit;">
                {{ $review->user->displayName() }}
            </a>
        @else
            Anonymous
        @endif
        
                                    @endif
                                </div>
                                @if($review->user && $review->user->job_title)
                                    <p class="asn-rating-p" style="color: #5b6b7a ; margin: 0; line-height: 1;  font-weight:500;  text-decoration: none;">{{ $review->user->job_title }}</p>
                                @endif
                                @if($review->user && $review->user->company_size)
                                    <p class="asn-rating-p" style="color: #5b6b7a ; margin: 0; line-height: 1; font-weight:500;">{{ static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="review_text size18" style="padding-right: 0;">
                        <h3 class="review-title" id="review-title-{{ $review->id }}" style="font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 12px;">
                            {{ $review->display_title ?? $review->translations->first()->title ?? '' }}
                        </h3>
                    @push('styles')
<style>
    .rating-stars {
        cursor: pointer !important;
    }

    .user-rating-dropdown-btn {
        cursor: pointer;
    }
    .rating {
    cursor: pointer;
}
</style>
@endpush  
                        @if(!empty($hasUserRatings))
                    <!-- Ratings below headline -->
                    <div class="rating light" style="display: flex; align-items: center; gap: 6px; ">
                        <div class="inn_ul">
                            <div class="rating-stars" style="display: flex; gap: 2px;">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($review->rating))
                                        <i class="fas fa-star text-warning" style="font-size: 14px;"></i>
                                    @elseif ($i - 0.5 <= $review->rating)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        @if(isset($criteria) && count($criteria) > 0)
                        <div class="dropdown d-inline-block">
                            <button class=" user-rating-dropdown-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" aria-label="User rating">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </button>
                            <div class="dropdown-menu shadow-sm user-rating-breakdown-menu">
                                <div class="user-rating-header">Detailed user rating </div>
                                <div class="user-rating-body">
                                    @foreach ($criteria as $criterion)
                                    @php
                                        $scoreVal = null;
                                        if ($review->ratings && $review->ratings->isNotEmpty()) {
                                            $match = $review->ratings->firstWhere('criteria_id', $criterion->id);
                                            if (!$match && $criterion->default_key) {
                                                $match = $review->ratings->first(fn($r) => optional($r->criteria)->default_key === $criterion->default_key);
                                            }
                                            if (!$match) {
                                                $match = $review->ratings->first(fn($r) => strtolower((string) optional($r->criteria)->name) === strtolower((string) $criterion->name));
                                            }
                                            if ($match && !is_null($match->rating)) {
                                                $scoreVal = (float) $match->rating;
                                            }
                                        }
                                        if (is_null($scoreVal)) {
                                            $cName = strtolower((string) $criterion->name);
                                            $cKey = $criterion->default_key;
                                            if ($cKey === 'ease_of_use' || $cName === 'ease of use') {
                                                $scoreVal = $review->ease_of_use_rating;
                                            } elseif ($cKey === 'features' || $cName === 'features') {
                                                $scoreVal = $review->exclusive_service_rating;
                                            } elseif ($cKey === 'value_for_money' || $cName === 'value for money') {
                                                $scoreVal = $review->value_for_money_rating;
                                            } elseif ($cKey === 'customer_service' || $cName === 'customer service' || $cName === 'support') {
                                                $scoreVal = $review->customer_service_rating;
                                            }
                                        }
                                        if (is_null($scoreVal) || $scoreVal <= 0) {
                                            $scoreVal = ((float)$review->rating > 0) ? (float)$review->rating : 4.0;
                                        }
                                         $percent = min(100, max(0, ((float)$scoreVal / 5) * 100));
                                    @endphp
                                    <div class="user-rating-row">
                                        <span class="user-rating-label">{{ $criterion->name }}</span>
                                        <div class="user-rating-progress-box">
                                            <div class="user-rating-progress-fill" style="width: {{ $percent }}%;"></div>
                                        </div>
                                        <span class="user-rating-score">{{ number_format($scoreVal, 1) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                        <div style="color: #444; line-height: 1.6; font-size: 15px; margin-bottom: 0;">
                            <span class="review-display-text" id="review-text-{{ $review->id }}">{!! nl2br(e(strip_tags($review->display_text ?? $review->translations->first()->description ?? 'No Description Available'))) !!}</span>
                        </div>
                        @if(!empty($review->can_toggle_original) && !empty($review->is_translated))
                            <div class="mt-1">
                                <a href="javascript:void(0);" 
                                   class="text-primary view-original-toggle" 
                                   data-review-id="{{ $review->id }}"
                                   data-state="translated"
                                   data-translated-title="{{ e($review->display_title ?? '') }}"
                                   data-translated-text="{{ e(strip_tags($review->display_text ?? '')) }}"
                                   data-original-title="{{ e($review->original_title ?? '') }}"
                                   data-original-text="{{ e(strip_tags($review->original_text ?? '')) }}"
                                   style="font-size: 13px; color: #64748b; font-weight: 500; text-decoration: underline; cursor: pointer;">
                                    {{ static_text('view_original_text') ?: 'View Original' }}
                                </a>
                            </div>
                        @endif
@php
    $selectedPros = $review->selectedProCons->where('type', 'pro');
    $selectedCons = $review->selectedProCons->where('type', 'con');
@endphp

@if($selectedPros->isNotEmpty())
    <div style="margin-top: 18px;">
        <div style="font-size: 15px; font-weight: 500; color: #1e3050; margin-bottom: 8px;">
            Pros
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach($selectedPros as $pro)
                <span style="display: inline-flex; align-items: center; padding: 6px 12px; background: #f0fdf4; color: #166534; border-radius: 6px; font-size: 13px; font-weight: 500;">
                    {{ $pro->text }}
                </span>
            @endforeach
        </div>
    </div>
@endif

@if($selectedCons->isNotEmpty())
    <div style="margin-top: 14px;">
        <div style="font-size: 15px; font-weight: 500; color: #1e3050; margin-bottom: 8px;">
            Cons
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach($selectedCons as $con)
                <span style="display: inline-flex; align-items: center; padding: 6px 12px; background: #fef2f2; color: #991b1b; border-radius: 6px; font-size: 13px; font-weight: 500;">
                    {{ $con->text }}
                </span>
            @endforeach
        </div>
    </div>
@endif

                    </div>

                    <!-- Bottom right actions (Report flag & Share) -->
                    <div class="review-actions-bottom-right" style="display:flex; justify-content: end; align-items: center; gap: 6px;">
                        <button type="button" class="action-icon-btn" onclick="openReviewReportModal({{ $review->id }})" title="Report this review" style="background: transparent; border: none; padding: 0;">
                            <i class="fas fa-flag" style="font-size: 13px; color: #94a3b8;"></i>
                        </button>
                        <button type="button" class="action-icon-btn" style="background: transparent; border: none; padding: 0; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; color: #94a3b8; cursor: pointer; outline: none; box-shadow: none; appearance: none; -webkit-appearance: none;" onclick="window.openShareModal('{{ url()->current() }}#review-{{ $review->id }}', 'Review on {{ config('app.name') }}', event)" title="Share this review" aria-label="Share this review">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                        </button>
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
        @php
            $tabAllCount = isset($business) ? ($business->approved_reviews_count ?? $business->reviews->whereIn('status', ['published', 'active'])->count()) : $allReviews->count();
        @endphp
        @if($tabAllCount >= 10)
        <div class="btm-bttn light" >
            <a href="{{ route('ReviewShow', ['locale' => $currentLangCode, 'slug' => $bSlug, 'reviews_slug' => $rSlug]) }}"
                > View all reviews</a>
        </div>
        @endif
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
                                <div class="asn-rating" style="display: flex; flex-direction: column; ">
                                    <h6 style="font-size: 15px; font-weight: 600; margin: 0; color: #1e3050;">
                                        @if ($review->user && $review->user->user_type === 'admin')
                                            {{ $review->public_name ?? 'Public' }}
                                        @else
                                            
        @if($review->user)
            <a href="{{ optional($review->user)->profileUrl() ?? route('user.profile', ['id' => $review->user_id]) }}" style="color: inherit;">
                {{ $review->user->displayName() }}
            </a>
        @else
            Anonymous
        @endif
        
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
                            <h3 class="review-title" id="review-title-layout2-{{ $review->id }}" style="font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 12px;">
                                {{ $review->display_title ?? $review->translations->first()->title ?? '' }}
                            </h3>
                            
                            <!-- Ratings below headline -->
                        @if(!empty($hasUserRatings))
                        <div class="rating light" style="display: flex; align-items: center; gap: 6px; margin-bottom: 16px;">
                            <div class="inn_ul">
                                <div class="rating-stars" style="display: flex; gap: 2px;">
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

                            @if(isset($criteria) && count($criteria) > 0)
                            <div class="dropdown d-inline-block">
                                <button class=" user-rating-dropdown-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" aria-label="User rating">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu shadow-sm user-rating-breakdown-menu">
                                    <div class="user-rating-header">Detailed user rating </div>
                                    <div class="user-rating-body">
                                        @foreach ($criteria as $criterion)
                                        @php
                                            $scoreVal = null;
                                            if ($review->ratings && $review->ratings->isNotEmpty()) {
                                                $match = $review->ratings->firstWhere('criteria_id', $criterion->id);
                                                if (!$match && $criterion->default_key) {
                                                    $match = $review->ratings->first(fn($r) => optional($r->criteria)->default_key === $criterion->default_key);
                                                }
                                               if (!$match) {
                                                    $match = $review->ratings->first(fn($r) => strtolower((string) optional($r->criteria)->name) === strtolower((string) $criterion->name));
                                                }
                                                if ($match && !is_null($match->rating)) {
                                                    $scoreVal = (float) $match->rating;
                                                }
                                            }
                                            if (is_null($scoreVal)) {
                                                $cName = strtolower((string) $criterion->name);
                                                $cKey = $criterion->default_key;
                                                if ($cKey === 'ease_of_use' || $cName === 'ease of use') {
                                                    $scoreVal = $review->ease_of_use_rating;
                                                } elseif ($cKey === 'features' || $cName === 'features') {
                                                    $scoreVal = $review->exclusive_service_rating;
                                                } elseif ($cKey === 'value_for_money' || $cName === 'value for money') {
                                                    $scoreVal = $review->value_for_money_rating;
                                                } elseif ($cKey === 'customer_service' || $cName === 'customer service' || $cName === 'support') {
                                                    $scoreVal = $review->customer_service_rating;
                                                }
                                            }
                                            if (is_null($scoreVal) || $scoreVal <= 0) {
                                                $scoreVal = ((float)$review->rating > 0) ? (float)$review->rating : 4.0;
                                            }
                                         $percent = min(100, max(0, ((float)$scoreVal / 5) * 100));
                                        @endphp
                                        <div class="user-rating-row">
                                            <span class="user-rating-label">{{ $criterion->name }}</span>
                                            <div class="user-rating-progress-box">
                                            <div class="user-rating-progress-fill" style="width: {{ $percent }}%;"></div>
                                            </div>
                                            <span class="user-rating-score">{{ number_format($scoreVal, 1) }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="rate_box" style="margin: 0; font-size: 14px; font-weight: 600; color: #555;">
                                {{ number_format($review->rating, 1) }}
                            </div>
                        </div>
                            <div style="color: #444; line-height: 1.6; font-size: 15px; margin-bottom: 0;">
                        @endif
                                <span class="review-display-text" id="review-text-layout2-{{ $review->id }}">{!! nl2br(e(strip_tags($review->display_text ?? $review->translations->first()->description ?? 'No Description Available'))) !!}</span>
                            </div>
                            @if(!empty($review->can_toggle_original) && !empty($review->is_translated))
                                <div class="mt-1">
                                    <a href="javascript:void(0);" 
                                       class="text-primary view-original-toggle" 
                                       data-review-id="{{ $review->id }}"
                                       data-state="translated"
                                       data-translated-title="{{ e($review->display_title ?? '') }}"
                                       data-translated-text="{{ e(strip_tags($review->display_text ?? '')) }}"
                                       data-original-title="{{ e($review->original_title ?? '') }}"
                                       data-original-text="{{ e(strip_tags($review->original_text ?? '')) }}"
                                       style="font-size: 13px; color: #64748b; font-weight: 500; text-decoration: underline; cursor: pointer;">
                                        {{ static_text('view_original_text') ?: 'View Original' }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Bottom right actions (Report flag & Share) -->
                        <div class="review-actions-bottom-right" style="position: absolute; bottom: 20px; right: 20px; display: flex; align-items: center; gap: 6px;">
                            <button type="button" class="action-icon-btn" onclick="openReviewReportModal({{ $review->id }})" title="Report this review" style="background: transparent; border: none; padding: 0;">
                                <i class="fas fa-flag" style="font-size: 13px; color: #94a3b8;"></i>
                            </button>
                            <button type="button" class="action-icon-btn" style="background: transparent; border: none; padding: 0; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; color: #94a3b8; cursor: pointer; outline: none; box-shadow: none; appearance: none; -webkit-appearance: none;" onclick="window.openShareModal('{{ url()->current() }}#review-{{ $review->id }}', 'Review on {{ config('app.name') }}', event)" title="Share this review" aria-label="Share this review">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="18" cy="5" r="3"></circle>
                                    <circle cx="6" cy="12" r="3"></circle>
                                    <circle cx="18" cy="19" r="3"></circle>
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
        @if($ourReviews->count() >= 10)
        <div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
            <a href="{{ route('ReviewShow', ['locale' => $currentLangCode, 'slug' => $bSlug, 'reviews_slug' => $rSlug]) }}"
                style="font-size: 16px; font-weight: 600; color: #06498b; text-decoration: none;">View all reviews</a>
        </div>
        @endif
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
                                <div style="font-size: 15px; font-weight: 600; margin-bottom: 2px;">
                                    @if ($review->user && $review->user->user_type === 'admin')
                                        {{ $review->public_name ?? 'Public' }}
                                    @else
                                        
        @if($review->user)
            <a href="{{ optional($review->user)->profileUrl() ?? route('user.profile', ['id' => $review->user_id]) }}" style="color: inherit;">
                {{ $review->user->displayName() }}
            </a>
        @else
            Anonymous
        @endif
        
                                    @endif
                                </div>
                                @if($review->user && $review->user->job_title)
                                    <p style="font-size: 13px; color: #777; margin: 2px 0 0 0;">{{ $review->user->job_title }}</p>
                                @endif
                                @if($review->user && $review->user->company_size)
                                    <p style="font-size: 13px; color: #777; margin: 2px 0 0 0;">{{ static_text('company_size_' . $review->user->company_size) ?: $review->user->company_size }}</p>
                                @endif
                            </div>
                        </div>
                        @if(!empty($hasUserRatings))
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
                        @endif
                    </div>
                    <div class="review_text size18">
                        <p class="review-title" id="review-title-mobile-{{ $review->id }}" style="margin-bottom: 4px;">
                            {{ $review->display_title ?? $review->translations->first()->title ?? '' }}
                        </p>
                        <p style="font-size: 13px; color: #777; margin: 0 0 10px 0;">{{ $review->created_at->diffForHumans() }}</p>
                        <p class="review-display-text" id="review-text-mobile-{{ $review->id }}">{{ strip_tags($review->display_text ?? $review->translations->first()->description ?? 'No Description Available') }}</p>
                        @if(!empty($review->can_toggle_original) && !empty($review->is_translated))
                            <div class="mt-1">
                                <a href="javascript:void(0);" 
                                   class="text-primary view-original-toggle" 
                                   data-review-id="{{ $review->id }}"
                                   data-state="translated"
                                   data-translated-title="{{ e($review->display_title ?? '') }}"
                                   data-translated-text="{{ e(strip_tags($review->display_text ?? '')) }}"
                                   data-original-title="{{ e($review->original_title ?? '') }}"
                                   data-original-text="{{ e(strip_tags($review->original_text ?? '')) }}"
                                   style="font-size: 13px; color: #64748b; font-weight: 500; text-decoration: underline; cursor: pointer;">
                                    {{ static_text('view_original_text') ?: 'View Original' }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
        @if($trustpilotReviews->count() >= 10)
        <div class="btm-bttn light" style="display: flex; justify-content: center; margin-top: 25px;">
            <a href="{{ route('ReviewShow', ['locale' => $currentLangCode, 'slug' => $bSlug, 'reviews_slug' => $rSlug]) }}"
                style="font-size: 16px; font-weight: 600; color: #06498b; text-decoration: none;">View all reviews</a>
        </div>
        @endif
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


document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.rating.light').forEach(function (ratingWrapper) {

        const stars = ratingWrapper.querySelector('.rating-stars');
        const dropdown = ratingWrapper.querySelector('.dropdown');
        const dropdownButton = ratingWrapper.querySelector('.user-rating-dropdown-btn');
        const dropdownMenu = ratingWrapper.querySelector('.user-rating-breakdown-menu');

        if (!stars || !dropdown || !dropdownButton || !dropdownMenu) {
            return;
        }

        let closeTimer;

        function showRating() {
            clearTimeout(closeTimer);

            dropdown.classList.add('show');
            dropdownMenu.classList.add('show');
            dropdownButton.setAttribute('aria-expanded', 'true');
        }

        function hideRating() {
            closeTimer = setTimeout(function () {
                dropdown.classList.remove('show');
                dropdownMenu.classList.remove('show');
                dropdownButton.setAttribute('aria-expanded', 'false');
            }, 100);
        }

        // Stars hover
        stars.addEventListener('mouseenter', showRating);
        stars.addEventListener('mouseleave', hideRating);

        // Arrow hover
        dropdownButton.addEventListener('mouseenter', showRating);
        dropdownButton.addEventListener('mouseleave', hideRating);

        // Rating box hover
        dropdownMenu.addEventListener('mouseenter', showRating);
        dropdownMenu.addEventListener('mouseleave', hideRating);
    });

});


    

// View Original Toggle Handler
$(document).on('click', '.view-original-toggle', function(e) {
    e.preventDefault();
    var $btn = $(this);
    var reviewId = $btn.data('review-id');
    var state = $btn.data('state');
    var $card = $btn.closest('.review_detl');
    var $textEl = $card.find('.review-display-text');
    if (!$textEl.length) $textEl = $('#review-text-' + reviewId);
    var $titleEl = $card.find('.review-title');
    if (!$titleEl.length) $titleEl = $('#review-title-' + reviewId);

    if (state === 'translated') {
        var origText = $btn.data('original-text');
        var origTitle = $btn.data('original-title');
        if (origText) $textEl.html(String(origText).replace(/\n/g, '<br>'));
        if (origTitle && $titleEl.length) $titleEl.text(origTitle);
        $btn.text('View Translation');
        $btn.data('state', 'original');
    } else {
        var transText = $btn.data('translated-text');
        var transTitle = $btn.data('translated-title');
        if (transText) $textEl.html(String(transText).replace(/\n/g, '<br>'));
        if (transTitle && $titleEl.length) $titleEl.text(transTitle);
        $btn.text('View Original');
        $btn.data('state', 'translated');
    }
});

</script>
