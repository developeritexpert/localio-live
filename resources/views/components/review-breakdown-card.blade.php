@props(['business'])

@php
    $curLangId = getCurrentLanguageID();
    $bTrans = $business->translations->firstWhere('lang_id', $curLangId) ?? $business->translations->first();
    $bName = $bTrans->name ?? $business->name ?? '';
    
    $bizTotalReviews = $business->reviews()->where('status', 'active')->count();
    $bizAvgRating = $business->average_rating ?? null;
    
    $criteriaRatings = [];
    foreach($business->category->ratingCriteria ?? [] as $criterion) {
        $avg = $business->reviews()
            ->join('review_ratings', 'reviews.id', '=', 'review_ratings.review_id')
            ->where('review_ratings.criteria_id', $criterion->id)
            ->avg('review_ratings.rating');
        if ($avg !== null) {
            $criteriaRatings[$criterion->name] = round($avg, 1);
        }
    }
    
    $recommendPercent = $business->reviews()->count() > 0 
        ? round(($business->reviews()->where('recommend', 1)->count() / $business->reviews()->count()) * 100) 
        : 0;
@endphp

<!-- Review Breakdown Card -->
<div class="boxshadow_border p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    <div class="review-header-box top_review_bx" style="display: flex;  flex-wrap; justify-content:space-between; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
        <div class="d-flex align-items-center gap-2">
            <div class="top-product-logo">
                <x-business-logo :business="$business" :name="$bName" />
            </div>
            <div>
                <p style="font-size: 16px !important; font-weight: 600 !important; margin: 0 0 4px 0; line-height: 1 !important;">{{ $bName }}</p>
                <div class="rating-group" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span style="">{{ formatRating($bizAvgRating) }}</span>
                    <div class="rating-stars" style="display: flex; gap: 0px;">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($bizAvgRating))
                                <i class="fas fa-star text-warning" style="font-size: 13px;"></i>
                            @elseif ($i - 0.5 <= $bizAvgRating)
                                <i class="fas fa-star-half-alt text-warning" style="font-size: 13px;"></i>
                            @else
                                <i class="far fa-star text-warning" style="font-size: 13px;"></i>
                            @endif
                        @endfor
                    </div>
                    <span style="   ">({{ number_format($bizTotalReviews) }})</span>
                </div>
            </div>
        </div>

        <a href="{{ $business->affiliate_link ?? $business->permanent_url }}" class="cta btn-orng justify-content-center" target="_blank" style="display: flex !important;  width:fit-content;  height:fit-content; align-items: center;    padding: 8px 11px; white-space: nowrap;">
            {{ static_text('visit_website') ?: 'Visit website' }}
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
        </a>
    </div>

    @if(count($criteriaRatings) > 0)
        <p style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 15px;">Detailed user ratings </p>
        <div class="mb-3">
            @foreach ($criteriaRatings as $cName => $cRating)
                <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                    <p class="m-0" style="font-size: 12px; font-weight: 500; color: #444;">{{ $cName }}</p>
                    <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                        <progress class="progress-bar w-100" value="{{ $cRating * 20 }}" max="100" style="height: 8px;"></progress>
                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 35px; text-align: right;">{{ formatRating($cRating) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center pt-3 mt-3" style="border-top: 1px solid #f0f0f0;">
        <span style="font-weight: 600; color: #002347; font-size: 14px;">Recommended by users</span>
        <strong style="color: #002347; font-size: 14px; font-weight: 600;">{{ $recommendPercent }}%</strong>
    </div>

    <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: 600; color: #1e3050; font-size: 14px;">Do you recommend {{ $bName }}?</span>
        <div style="display: flex; gap: 8px;">
            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor='#007bff';">
                <i class="fas fa-thumbs-up" style="font-size: 16px;"></i>
            </a>
            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 28px; height: 28px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#033b74';" onmouseout="this.style.backgroundColor='#007bff';">
                <i class="fas fa-thumbs-down" style="font-size: 16px;"></i>
            </a>
        </div>
    </div>
</div>
