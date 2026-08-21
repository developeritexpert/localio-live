<div>
    <style>
        .view-review-link:hover {
            text-decoration: underline !important;
        }
        .top-rated-heading-block h1 {
            font-size: 28px !important;
            font-weight: 700;
            padding: 0 !important;
            margin-bottom: 4px !important;
            color: #002347 !important;
            
        }
        .top-rated-heading-block{
            border-bottom: 2px solid #e8eef6;
            margin-bottom: 15px;
        }
        /* section.top-automotive-sec.top_rate_pg.light {
           margin-top: 120px !important;
        } */
        /* View details button – match height of Visit website */
        .auto-choice-btn .cta_outline {
            padding: 8px 16px !important;
            height: auto !important;
            /* min-height: 44px !important; */
            box-sizing: border-box !important;
            line-height: 1.5 !important;
        }
        .automotive-card .blue-chkbox {
            bottom: 0 !important;
            transition: all 0.3s ease;
            right:unset;
            left:-30px
        }
        .usp-grid-container {
            display: grid !important;
            grid-template-columns: auto auto !important;
            justify-content: start !important;
            gap: 8px 45px !important;
            width: 100% !important;
        }
        @media (max-width: 768px) {
            /* .automotive-card {
                padding-bottom: 20px !important;
            } */
            /* .automotive-card .blue-chkbox {
                position: relative !important;
                bottom: auto !important;
                right: auto !important;
                border-radius: 8px !important;
                display: block !important;
                width: 100% !important;
                text-align: center !important;
                margin-top: 15px !important;
                padding: 12px 15px !important;
            } */
            .automotive-card .blue-chkbox label {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin: 0 !important;
            }
            .usp-grid-container {
                grid-template-columns: 1fr !important;
                gap: 8px 0px !important;
            }
            .key-feature-price {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            .starting-price-box, .free-trial-box {
                width: 100% !important;
                min-width: 100% !important;
                margin-bottom: 10px !important;
            }
        #myID.search-box {
            visibility: visible !important;
            display: block !important;
        }
        /* .top-automotive-sec.top_rate_pg {
            padding-top: 160px !important;
        } */
        .top-rated-heading-block {
            /* margin-left: 27%; */
            margin-bottom: 24px;
            padding-bottom: 16px;
            /* border-bottom: 2px solid #e8eef6; */
        }
        .top-rated-heading-block h1 {
            /* font-size: 34px;
            font-weight: 800;
            color: #002347;
            margin: 0 0 5px 0;
            letter-spacing: -0.5px;
            line-height: 1.2; */
            font-size: 28px !important;
            font-weight: 700;
            padding: 0 !important;
            margin-bottom: 4px !important;
            color: #002347 !important;
        }
        .top-rated-heading-block p {
            font-size: 15px;
            color: #7a8ea8;
            margin: 0;
            font-weight: 400;
        }
    }
        @media (max-width: 768px) {
            /* .top-automotive-sec.top_rate_pg {
                padding-top: 110px !important;
            } */
            .top-rated-heading-block h1 {
                font-size: 24px;
            }
            .top-auto-choice {
    padding-top: 0;
}
section.top-automotive-sec.top_rate_pg.light {
    margin-top: 55px !important;
}
        }
    </style>

    <section class="top-automotive-sec top_rate_pg light common_detail_sec" style="{{ !empty($hasUpperHeader) ? 'margin-top: 20px !important; padding-top: 0 !important;' : '' }}">
    <div class="top-auto-btm">
        
            <div class="container">
                <div class="top-auto-choice">
                    <div class="top-rated-heading-block" style="padding-bottom: 16px; margin-bottom: 24px;">
                        <div class="row align-items-start">
                            @if(empty($hasUpperHeader))
                                <div class="text-start col-lg-8">
                                    @php
                                        $bTrans = $business ? ($business->translations->where('language_id', getCurrentLanguageID())->first() ?? $business->translations->first()) : null;
                                        $altTitle = !empty($bTrans->alternatives_title) ? $bTrans->alternatives_title : ($businessName . ' alternatives');
                                        $altDesc = !empty($bTrans->alternatives_description) ? $bTrans->alternatives_description : ('Compare the best alternatives to ' . $businessName . '. Find similar products based on pricing, features, user ratings, and reviews.');
                                    @endphp
                                    <h1 style="color: #002347; font-size: 24px !important; font-weight: 600 !important; margin-bottom: 4px;">
                                        {{ $altTitle }}
                                    </h1>
                
                                    <div style="font-size: 14.5px; color: #475569; line-height: 1.6;">
                                        {!! $altDesc !!}
                                    </div>
                                </div>
                            @endif
                            @php
                                $activeReviews = ($business->reviews ?? collect())->where('status', 'active');
                                $bReviewCount = $activeReviews->count();
                                $bAvgRating = $bReviewCount > 0 ? round($activeReviews->avg('rating'), 1) : 0;

                                // Dynamic Rating Criteria Breakdown (Identical to ProductDetails & Reviews Page)
                                $bCriteria = $business->category ? $business->category->ratingCriteria : collect();
                                foreach ($bCriteria as $criterion) {
                                    $totalScore = 0;
                                    $count = 0;
                                    foreach ($activeReviews as $review) {
                                        $ratingRecord = \App\Models\ReviewRating::where('review_id', $review->id)
                                            ->where('criteria_id', $criterion->id)
                                            ->first();
                                        if ($ratingRecord) {
                                            $totalScore += $ratingRecord->rating;
                                            $count++;
                                        } else {
                                            $legacyVal = null;
                                            if ($criterion->name === 'Ease of Use') {
                                                $legacyVal = $review->ease_of_use_rating;
                                            } elseif ($criterion->name === 'Customer Service') {
                                                $legacyVal = $review->customer_service_rating;
                                            } elseif ($criterion->name === 'Features') {
                                                $legacyVal = $review->exclusive_service_rating;
                                            } elseif ($criterion->name === 'Value for Money') {
                                                $legacyVal = $review->value_for_money_rating;
                                            }
                                            if (!is_null($legacyVal)) {
                                                $totalScore += $legacyVal;
                                                $count++;
                                            }
                                        }
                                    }
                                    $criterion->average_rating = $count > 0 ? round($totalScore / $count, 1) : 0;
                                }

                                // Recommendation percentage (Identical to ProductDetails & Reviews Page)
                                if ($bReviewCount > 0) {
                                    $recommendCount = $activeReviews->where('recommend', 1)->count();
                                    $bRecommendPercent = round(($recommendCount / $bReviewCount) * 100);
                                } else {
                                    $bRecommendPercent = 100;
                                }

                                // Reviews URL
                                $langObj = \App\Models\Language::where('lang_code', app()->getLocale())->first();
                                $rSlug = !empty($langObj->reviews_slug) ? $langObj->reviews_slug : 'reviews';
                                $bName = $business->translations->first()?->name ?? $business->name ?? 'Business';
                                $bSlug = $business->translations->first()?->slug ?? $business->slug ?? 'business-' . $business->id;
                                $reviewsUrl = route('ReviewShow', ['locale' => app()->getLocale(), 'slug' => $bSlug, 'reviews_slug' => $rSlug]);
                            @endphp
                            <div class="col-lg-4 mt-4 mt-md-0 text-start">
                                <div class="p-4 bg-white rounded-3 border mb-4" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                    <div class="review-header-box top_review_bx" style="display: flex;  flex-wrap; justify-content:space-between; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 48px; height: 48px; flex-shrink: 0; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0;">
                                                <x-business-logo :business="$business" :name="$bName" />
                                            </div>
                                            <div>
                                                <h3 style="font-size: 16px !important; font-weight: 600 !important; margin: 0 0 4px 0;">{{ $bName }}</h3>
                                                <div class="rating-group" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                                                    <span style="">{{ number_format($bAvgRating, 1) }}</span>
                                                    <div class="rating-stars" style="display: flex; gap: 0px;">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= floor($bAvgRating))
                                                                <i class="fas fa-star text-warning" style="font-size: 13px;"></i>
                                                            @elseif ($i - 0.5 <= $bAvgRating)
                                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 13px;"></i>
                                                            @else
                                                                <i class="far fa-star text-warning" style="font-size: 13px;"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span style="   ">({{ number_format($bReviewCount) }})</span>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ $business->affiliate_link ?? $business->permanent_url }}" class="cta btn-orng justify-content-center" target="_blank" style="display: flex !important;  width:fit-content;  height:fit-content; align-items: center; border-radius: 30px; padding:11px 25px;">
                                            Visit website
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                        </a>
                                    </div>

                                    @if(count($bCriteria) > 0)
                                        <h6 style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 15px;">Review breakdown</h6>
                                        <div class="mb-3">
                                            @foreach ($bCriteria as $criterion)
                                                <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                    <p class="m-0" style="font-size: 12px; font-weight: 500; color: #444;">{{ $criterion->name }}</p>
                                                    <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                                        <progress class="progress-bar w-100" value="{{ $criterion->average_rating * 20 }}" max="100" style="height: 8px;"></progress>
                                                        <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 35px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center pt-3 mt-3" style="border-top: 1px solid #f0f0f0;">
                                        <span style="font-weight: 600; color: #002347; font-size: 14px;">Recommended by users</span>
                                        <strong style="color: #002347; font-size: 14px; font-weight: 600;">{{ $bRecommendPercent }}%</strong>
                                    </div>

                                    <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-weight: 600; color: #1e3050; font-size: 14px;">Do you recommend {{ $businessName }}?</span>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#f9633b';" onmouseout="this.style.backgroundColor='#174889';">
                                                <i class="fas fa-thumbs-up" style="font-size: 12px;"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 28px; height: 28px; border-radius: 50%; background-color: #06498b; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#f9633b';" onmouseout="this.style.backgroundColor='#06498b';">
                                                <i class="fas fa-thumbs-down" style="font-size: 12px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Widget 3: More About Business Card -->
                                <x-more-about-business :business="$business" />
                            </div>


                        </div>
                        
                        <!-- Dynamic Section 1: Most popular alternatives (Cards) -->
                        <!-- Dynamic Section 2: Compare alternatives (Affiliated Table 1) -->
                        <!-- Dynamic Section 3: What the Localio community says (Pros/Cons Table 2) -->
                        @php
                            $lang_id = getCurrentLanguageID();
                            $bCategoryIds = array_filter(array_unique(array_merge([$business->category_id], $business->subCategories ? $business->subCategories->pluck('id')->toArray() : [])));

                            // 1. Fetch 4-6 popular alternatives
                            $popularAltBusinesses = \App\Models\Business::where('id', '!=', $business->id)
                                ->where('status', 1)
                                ->whereIn('category_id', $bCategoryIds)
                                ->with([
                                    'translations' => fn($q) => $q->where('lang_id', $lang_id),
                                    'reviews' => fn($q) => $q->where('status', 'active'),
                                    'category.ratingCriteria',
                                    'products.prices'
                                ])
                                ->get()
                                ->sortByDesc(function($b) {
                                    $r = $b->reviews->where('status', 'active');
                                    return $r->count() > 0 ? $r->avg('rating') : ($b->admin_rating ?? 0);
                                })
                                ->take(3);

                            if ($popularAltBusinesses->count() < 3) {
                                $morePop = \App\Models\Business::where('id', '!=', $business->id)
                                    ->where('status', 1)
                                    ->whereNotIn('id', $popularAltBusinesses->pluck('id'))
                                    ->with([
                                        'translations' => fn($q) => $q->where('lang_id', $lang_id),
                                        'reviews' => fn($q) => $q->where('status', 'active'),
                                        'category.ratingCriteria',
                                        'products.prices'
                                    ])
                                    ->get()
                                    ->take(3 - $popularAltBusinesses->count());
                                $popularAltBusinesses = $popularAltBusinesses->merge($morePop);
                            }

                            // 2. Fetch 10 affiliated businesses for tables
                            $affiliatedTableBusinesses = \App\Models\Business::where('is_affiliate', 1)
                                ->where('id', '!=', $business->id)
                                ->where('status', 1)
                                ->whereIn('category_id', $bCategoryIds)
                                ->with([
                                    'translations' => fn($q) => $q->where('lang_id', $lang_id),
                                    'reviews' => fn($q) => $q->where('status', 'active'),
                                    'category.ratingCriteria'
                                ])
                                ->get()
                                ->sortByDesc(function($b) {
                                    $r = $b->reviews->where('status', 'active');
                                    return $r->count() > 0 ? $r->avg('rating') : ($b->admin_rating ?? 0);
                                })
                                ->take(10);

                            if ($affiliatedTableBusinesses->count() < 10) {
                                $moreAff = \App\Models\Business::where('is_affiliate', 1)
                                    ->where('id', '!=', $business->id)
                                    ->whereNotIn('id', $affiliatedTableBusinesses->pluck('id'))
                                    ->where('status', 1)
                                    ->with([
                                        'translations' => fn($q) => $q->where('lang_id', $lang_id),
                                        'reviews' => fn($q) => $q->where('status', 'active'),
                                        'category.ratingCriteria'
                                    ])
                                    ->get()
                                    ->sortByDesc(function($b) {
                                        $r = $b->reviews->where('status', 'active');
                                        return $r->count() > 0 ? $r->avg('rating') : ($b->admin_rating ?? 0);
                                    })
                                    ->take(10 - $affiliatedTableBusinesses->count());

                                $affiliatedTableBusinesses = $affiliatedTableBusinesses->merge($moreAff);
                            }

                            $tableRowsList = collect([$business])->merge($affiliatedTableBusinesses);
                        @endphp

                        <!-- 1. Most popular alternatives (Matched design with product_detail.blade.php) -->
                        @if($popularAltBusinesses->count() > 0)
                            <section class="software-like p_50 product_integra_sec my-4" id="sectionAlternatives">
                                <div class="container">
                                    <div class="sftwre-like-innr">
                                        @php
                                            $altPopHeadlineRaw = static_text('alternatives_page_most_popular_headline');
                                            if (empty($altPopHeadlineRaw) || $altPopHeadlineRaw === 'alternatives_page_most_popular_headline') {
                                                $altPopHeadlineRaw = 'Most popular [business] alternatives';
                                            }
                                            $altPopHeadline = str_replace(
                                                ['[business]', ':business', 'XXXXX', 'XXXX'],
                                                $businessName,
                                                $altPopHeadlineRaw
                                            );
                                            $altPopDesc = static_text('alternatives_page_most_popular_desc');
                                            if (empty($altPopDesc) || $altPopDesc === 'alternatives_page_most_popular_desc') {
                                                $altPopDesc = "Based on other buyer's searches, these are the products that could be a good fit for you.";
                                            }
                                        @endphp
                                        <div class="sftwre-asana-hd text-center" data-aos="fade-up" data-aos-duration="1000">
                                            <h2>{{ $altPopHeadline }}</h2>
                                            <p>{{ $altPopDesc }}</p>
                                        </div>
                                        <div class="sft_ware_test" style="display: flex; justify-content:center; align-items: center;">
                                            <div class="sftware-alternative d-flex flex-wrap justify-content-center gap-4" data-aos="fade-up" data-aos-duration="1000">
                                                @foreach ($popularAltBusinesses->take(3) as $altbusiness)
                                                    @php
                                                        $altTrans = $altbusiness->translations->firstWhere('lang_id', $lang_id) ?? $altbusiness->translations->first();
                                                        $altBizName = $altTrans->name ?? $altbusiness->name ?? 'Business';
                                                        $altBizSlug = $altTrans->slug ?? $altbusiness->slug ?? 'business-' . $altbusiness->id;

                                                        $altStartingPrice = 'N/A';
                                                        $altCurrency = '$';
                                                        $altAdditionalInfo = 'NA';
                                                        $altPrice = getBusinessesWithStartingPrice($altbusiness);
                                                        if (!empty($altPrice) && isset($altPrice[0]['starting_price'])) {
                                                            $altBusinessPrice = $altPrice[0]['starting_price'];
                                                            $altStartingPrice = $altBusinessPrice['amount'] ?? 'N/A';
                                                            $altCurrency = $altBusinessPrice['currency'] ?? '$';
                                                            $altAdditionalInfo = $altBusinessPrice['additional_info'] ?? 'NA';
                                                        }

                                                        $altReviews = \App\Models\Review::where('business_id', $altbusiness->id)->get();
                                                        $altEaseOfUseAvg = round($altReviews->avg('ease_of_use_rating'), 1);
                                                        $altValueForMoneyAvg = round($altReviews->avg('value_for_money_rating'), 1);
                                                        $altCustomerServiceAvg = round($altReviews->avg('customer_service_rating'), 1);
                                                        $altExclusiveFeatureAvg = round($altReviews->avg('exclusive_service_rating'), 1);

                                                        $altRatingAvg = $altbusiness->reviews->avg('rating');
                                                        $count = $altbusiness->reviews->where('status', 'active')->count();
                                                    @endphp
                                                    <div class="sftware-alternative-pck" data-aos="fade-up" data-aos-duration="1000"
                                                        onclick="if(!event.target.closest('a')) { window.location.href = '{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $altBizSlug]) }}'; }"
                                                        style="cursor: pointer; padding: 25px 20px;">

                                                        <div class="ans_lft p_top_btm_sftwre pt-0 pb-3" style="border-bottom: 1px solid #eee;">
                                                            <div class="top-product-logo">
                                                                <x-business-logo :business="$altbusiness" :name="$altBizName" />
                                                            </div>
                                                            <div class="asn-rating">
                                                                <h6 class="m-0 fw_700">
                                                                    {{ $altBizName }}
                                                                </h6>
                                                                <div class="rating-group">
                                                                    <span class="rate_box_num fw-medium">{{ number_format($altRatingAvg, 1) }}</span>
                                                                    <div class="rating-stars">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            @if ($i <= floor($altRatingAvg))
                                                                                <i class="fas fa-star text-warning"></i>
                                                                            @elseif ($i - 0.5 <= $altRatingAvg)
                                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                                            @else
                                                                                <i class="far fa-star text-warning"></i>
                                                                            @endif
                                                                        @endfor
                                                                    </div>
                                                                    <span class="rate_box_text">
                                                                        ({{ $count }})
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="over-rate-progress p_top_btm_sftwre pt-3 pb-3" style="border-bottom: 1px solid #eee;">
                                                            <h6 class="fw_700 mb-3" style="color: #002347; font-size:12px;">Review breakdown</h6>
                                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                <p class="m-0" style="font-size: 12px; color: #555;">Ease of Use</p>
                                                                <div class="prgs_br d-flex align-items-center">
                                                                    <progress class="progress-bar" value="{{ ($altEaseOfUseAvg ?? 0) * 20 }}" max="100"></progress>
                                                                    <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ $altEaseOfUseAvg ?? 0 }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                <p class="m-0" style="font-size: 12px; color: #555;">Customer Service</p>
                                                                <div class="prgs_br d-flex align-items-center">
                                                                    <progress class="progress-bar" value="{{ ($altCustomerServiceAvg ?? 0) * 20 }}" max="100"></progress>
                                                                    <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ $altCustomerServiceAvg ?? 0 }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                <p class="m-0" style="font-size: 12px; color: #555;">Features</p>
                                                                <div class="prgs_br d-flex align-items-center">
                                                                    <progress class="progress-bar" value="{{ ($altExclusiveFeatureAvg ?? 0) * 20 }}" max="100"></progress>
                                                                    <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ $altExclusiveFeatureAvg ?? 0 }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                <p class="m-0" style="font-size: 12px; color: #555;">Value for Money</p>
                                                                <div class="prgs_br d-flex align-items-center">
                                                                    <progress class="progress-bar" value="{{ ($altValueForMoneyAvg ?? 0) * 20 }}" max="100"></progress>
                                                                    <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ $altValueForMoneyAvg ?? 0 }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="start-from p_top_btm_sftwre pt-3 pb-3">
                                                            <h6 style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 14px;">Starting price</h6>
                                                            <h3 class="m-0 mt-1" style="font-weight: 700; color: #333; font-size: 24px; line-height:1!important;">
                                                                <span>{{ $altCurrency }}{{ $altStartingPrice }}</span>
                                                            </h3>
                                                            <small class="text-muted" style="font-size: 12px;">{{ $altAdditionalInfo }}</small>
                                                        </div>

                                                        <div class="sftwre-alt-btn pt-2">
                                                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $altBizSlug]) }}"
                                                                class="cta btn_blue w-100 d-flex align-items-center justify-content-center"
                                                                style="border-radius: 25px; padding: 10px 20px; font-weight: 500; text-decoration: none; font-size: 14px;">
                                                                View details
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endif

                        <!-- 2. Compare alternatives (Affiliated Table 1) -->
                        <div class="comparison-table-section my-5 pt-3">
                            <h2 style="font-size: 22px; font-weight: 700; color: #002347; margin-bottom: 16px;">
                                Compare {{ $businessName }} alternatives
                            </h2>

                            <div class="table-responsive rounded-3 border bg-white" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                <table class="table align-middle mb-0" style="font-size: 14px; color: #1e3050;">
                                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                        <tr>
                                            <th scope="col" style="padding: 14px 18px; font-weight: 700; color: #002347; min-width: 180px;">Business</th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347;">Overall rating</th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347;">Features</th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347;">Ease of use</th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347;">Value for money</th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347;">Performance & reliability</th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347;">Customer support</th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347;">Recommend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tableRowsList as $rowBiz)
                                            @php
                                                $rTrans = $rowBiz->translations->firstWhere('lang_id', $lang_id) ?? $rowBiz->translations->first();
                                                $rName = $rTrans->name ?? $rowBiz->name ?? 'Business';
                                                $rSlug = $rTrans->slug ?? $rowBiz->slug ?? 'business-' . $rowBiz->id;
                                                $rRevs = $rowBiz->reviews->where('status', 'active');
                                                $rCount = $rRevs->count();
                                                $rRating = $rCount > 0 ? round($rRevs->avg('rating'), 1) : (float)($rowBiz->admin_rating ?? 0);
                                                $rRec = $rCount > 0 ? round(($rRevs->where('recommend', 1)->count() / $rCount) * 100) . '%' : '-';

                                                $rCriteria = $rowBiz->category ? $rowBiz->category->getEffectiveRatingCriteria() : collect();
                                                $critScores = [];
                                                foreach(['Features', 'Ease of use', 'Value for money', 'Performance & reliability', 'Customer support'] as $cKey) {
                                                    $foundCrit = $rCriteria->first(fn($c) => strtolower($c->name) === strtolower($cKey) || (strtolower($cKey) === 'customer support' && strtolower($c->name) === 'customer service') || (strtolower($cKey) === 'performance & reliability' && strtolower($c->name) === 'service management'));
                                                    if ($foundCrit) {
                                                        $cScore = 0; $cCnt = 0;
                                                        foreach($rRevs as $rev) {
                                                            $rrRecord = \App\Models\ReviewRating::where('review_id', $rev->id)->where('criteria_id', $foundCrit->id)->first();
                                                            if ($rrRecord) { $cScore += $rrRecord->rating; $cCnt++; }
                                                        }
                                                        $critScores[$cKey] = $cCnt > 0 ? round($cScore / $cCnt, 1) : number_format($rRating, 1);
                                                    } else {
                                                        $critScores[$cKey] = number_format($rRating, 1);
                                                    }
                                                }
                                            @endphp
                                            <tr style="border-bottom: 1px solid #f1f5f9; {{ $loop->first ? 'background-color: #f8fafc;' : '' }}">
                                                <td style="padding: 14px 18px; font-weight: 700; color: #002347;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div style="width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0;">
                                                            <x-business-logo :business="$rowBiz" :name="$rName" />
                                                        </div>
                                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $rSlug]) }}" style="color: #002347; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                                {{ $rName }}
                                                            </a>
                                                            @if($loop->first)
                                                                <span class="badge bg-primary ms-1" style="font-size: 10px; font-weight: 600;">Current</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center fw-bold" style="padding: 14px; color: #1e3050; white-space: nowrap;">
                                                    <span class="d-inline-flex align-items-center gap-1">
                                                        <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                                        <span>{{ number_format($rRating, 1) }}</span>
                                                    </span>
                                                </td>
                                                <td class="text-center" style="padding: 14px; color: #475569;">{{ $critScores['Features'] }}</td>
                                                <td class="text-center" style="padding: 14px; color: #475569;">{{ $critScores['Ease of use'] }}</td>
                                                <td class="text-center" style="padding: 14px; color: #475569;">{{ $critScores['Value for money'] }}</td>
                                                <td class="text-center" style="padding: 14px; color: #475569;">{{ $critScores['Performance & reliability'] }}</td>
                                                <td class="text-center" style="padding: 14px; color: #475569;">{{ $critScores['Customer support'] }}</td>
                                                <td class="text-center fw-semibold" style="padding: 14px; color: #002347;">{{ $rRec }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 3. What the Localio community says (Pros/Cons Table 2) -->
                        <div class="community-feedback-table-section my-5 pt-3">
                            <div class="mb-3">
                                <h2 style="font-size: 22px; font-weight: 700; color: #002347; margin-bottom: 4px;">
                                    What the Localio community says
                                </h2>
                                <p style="font-size: 14px; color: #64748b; margin: 0;">
                                    Most mentioned pros and cons shared by real users.
                                </p>
                            </div>

                            <div class="table-responsive rounded-3 border bg-white" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                <table class="table align-middle mb-0" style="font-size: 14px; color: #1e3050;">
                                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                        <tr>
                                            <th scope="col" style="padding: 14px 18px; font-weight: 700; color: #002347; min-width: 180px;">Business</th>
                                            <th scope="col" style="padding: 14px 18px; font-weight: 700; color: #002347; width: 42%;">Most mentioned pro</th>
                                            <th scope="col" style="padding: 14px 18px; font-weight: 700; color: #002347; width: 42%;">Most mentioned con</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tableRowsList as $rowBiz)
                                            @php
                                                $rTrans = $rowBiz->translations->firstWhere('lang_id', $lang_id) ?? $rowBiz->translations->first();
                                                $rName = $rTrans->name ?? $rowBiz->name ?? 'Business';
                                                $rSlug = $rTrans->slug ?? $rowBiz->slug ?? 'business-' . $rowBiz->id;

                                                $proCons = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                                    ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                                    ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                                    ->where('reviews.business_id', $rowBiz->id)
                                                    ->where('reviews.status', 'active')
                                                    ->select('category_pro_cons.type', 'category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as cnt'))
                                                    ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text')
                                                    ->orderByDesc('cnt')
                                                    ->get();

                                                $topProObj = $proCons->firstWhere('type', 'pro');
                                                $topConObj = $proCons->firstWhere('type', 'con');

                                                $proStr = $topProObj ? $topProObj->text . ' (' . $topProObj->cnt . ')' : '-';
                                                $conStr = $topConObj ? $topConObj->text . ' (' . $topConObj->cnt . ')' : '-';
                                            @endphp
                                            <tr style="border-bottom: 1px solid #f1f5f9; {{ $loop->first ? 'background-color: #f8fafc;' : '' }}">
                                                <td style="padding: 14px 18px; font-weight: 700; color: #002347;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div style="width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0;">
                                                            <x-business-logo :business="$rowBiz" :name="$rName" />
                                                        </div>
                                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $rSlug]) }}" style="color: #002347; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                                {{ $rName }}
                                                            </a>
                                                            @if($loop->first)
                                                                <span class="badge bg-primary ms-1" style="font-size: 10px; font-weight: 600;">Current</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="padding: 14px 18px; color: #334155; font-weight: 500;">
                                                    @if($proStr !== '-')
                                                        <span style="color: #166534; font-weight: 600;">{{ $proStr }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 14px 18px; color: #334155; font-weight: 500;">
                                                    @if($conStr !== '-')
                                                        <span style="color: #991b1b; font-weight: 600;">{{ $conStr }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

<h2 class="mt-3">All {{ $businessName }} alternatives </h2>
                    </div>
                    <div class="auto-choice-row d-flex ">
                        <div class="auto-choice-lft">
                            <div class="container">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                </div>
                                <div class="col-md-12">
                                    <!-- Rating Filter Section - Styled like the image -->
                                    <div class="filter-section">
                                        <h3 class="fw-semibold text-dark mb-2">
                                            {{ static_text('user_rating') }}</h3>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input"
                                                wire:model.live="selectedRatings" value="4"
                                                id="rating-4">
                                            <label class="form-check-label d-flex align-items-center gap-1" for="rating-4" style="cursor: pointer;">
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="far fa-star text-warning"></i>
                                                <span class="ms-1" style="font-weight: 500; font-size: 14px; color: #334155;">4+</span>
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                wire:model.live="selectedRatings" value="3"
                                                id="rating-3">
                                            <label class="form-check-label d-flex align-items-center gap-1" for="rating-3" style="cursor: pointer;">
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="far fa-star text-warning"></i>
                                                <i class="far fa-star text-warning"></i>
                                                <span class="ms-1" style="font-weight: 500; font-size: 14px; color: #334155;">3+</span>
                                            </label>
                                        </div>
                                    </div>

                                    <link rel="stylesheet"
                                        href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" />

                                    <div class="filter-section mt-3 mb-3 pb-3 border-bottom pric_rnge">
                                        <h3 class="fw-semibold text-dark mb-3">{{ static_text('price_range') }}</h3>

                                        <div class="price-slider-container">
                                            <div
                                                class="price-inputs d-flex gap-2 align-items-center mt-3">
                                                <div class="price-input">
                                                    <span class="currency">$</span>
                                                    <input type="number" id="minPriceInput2" wire:model.live="minPrice"
                                                        min="0" max="5000"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <span class="price-separator">to</span>
                                                <div class="price-input">
                                                    <span class="currency">$</span>
                                                    <input type="number" id="maxPriceInput2" wire:model.live="maxPrice"
                                                        min="0" max="5000"
                                                        class="form-control form-control-sm">
                                                </div>
                                            </div>

                                            <div id="priceRangeSlider2" data-max-price="{{ $maxPriceValue ?? $maxPrice ?? 10000 }}"
                                                style="margin-top: 20px;" wire:ignore></div>
                                        </div>
                                    </div>
                                    <div class="accordion d-none" id="filterAccordion" style="border: none; width: 100%;">
                                        @foreach ($filters as $filter)
                                            @php
                                                $currentLangId = $lang_id ?? getCurrentLanguageID();
                                                $filterName =
                                                    $filter->translations->where('language_id', $currentLangId)->first()
                                                        ->name ?? $filter->name;
                                                $filterType = $filter->filterType
                                                    ? $filter->filterType->slug
                                                    : 'checkbox';
                                            @endphp

                                            <div class="filter-section">
                                                <h3>
                                                    {{ $filterName }}
                                                </h3>

                                                <div class="accordion-body" style="padding: 0;">
                                                    @if ($filterType === 'checkbox')
                                                        @foreach ($filter->options as $option)
                                                            @php
                                                                $optionName =
                                                                    $option->translations
                                                                        ->where('language_id', $lang_id)
                                                                        ->first()->name ?? $option->name;
                                                            @endphp
                                                            <div class="form-check" style="margin-bottom: 5px;">
                                                                <input type="checkbox" class="form-check-input"
                                                                    wire:model.live="selectedOptions"
                                                                    value="{{ $option->id }}"
                                                                    id="option-{{ $option->id }}"
                                                                    style="margin-right: 8px; cursor: pointer;">
                                                                <label class="form-check-label"
                                                                    for="option-{{ $option->id }}"
                                                                    style="font-size: 13px; color: #555; cursor: pointer;">
                                                                    {{ $optionName }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @elseif($filterType === 'radio')
                                                        @foreach ($filter->options as $option)
                                                            @php
                                                                $optionName =
                                                                    $option->translations
                                                                        ->where('language_id', $lang_id)
                                                                        ->first()->name ?? $option->name;
                                                            @endphp
                                                            <div class="form-check" style="margin-bottom: 5px;">
                                                                <input type="radio" class="form-check-input"
                                                                    name="filter_{{ $filter->id }}"
                                                                    wire:key="radio-{{ $filter->id }}-{{ $option->id }}"
                                                                    wire:click="toggleFilterOption({{ $option->id }})"
                                                                    {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}
                                                                    value="{{ $option->id }}"
                                                                    id="option-{{ $option->id }}"
                                                                    style="margin-right: 8px; cursor: pointer;">
                                                                <label class="form-check-label"
                                                                    for="option-{{ $option->id }}"
                                                                    style="font-size: 13px; color: #555; cursor: pointer;">
                                                                    {{ $optionName }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @elseif($filterType === 'dropdown')
                                                        @php
                                                            $selected = $filter->options->firstWhere(
                                                                'id',
                                                                $selectedOptions[0] ?? null,
                                                            );
                                                            $selectedOptionName = $selected
                                                                ? $selected->translations
                                                                        ->where('language_id', $lang_id)
                                                                        ->first()->name ?? $selected->name
                                                                : __('Select');
                                                        @endphp
                                                        <div class="">
                                                            <select
                                                                class="form-select w-full p-2 border border-gray-300 rounded-md text-sm"
                                                                wire:model.live="selectedOptions.{{ $filter->id }}">
                                                                <option value="">{{ __('Select') }}</option>
                                                                @foreach ($filter->options as $option)
                                                                    @php
                                                                        $optionName =
                                                                            $option->translations
                                                                                ->where('language_id', $lang_id)
                                                                                ->first()->name ?? $option->name;
                                                                    @endphp
                                                                    <option value="{{ $option->id }}">
                                                                        {{ $optionName }}</option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                    @elseif($filterType === 'toggle')
                                                        @foreach ($filter->options as $option)
                                                            @php
                                                                $optionTranslation = $option->translations
                                                                    ->where('language_id', $lang_id)
                                                                    ->first();
                                                                $optionName = $optionTranslation->name ?? $option->name;
                                                                $onLabel =
                                                                    $optionTranslation->on_label ??
                                                                    ($option->on_label ?? 'On');
                                                                $offLabel =
                                                                    $optionTranslation->off_label ??
                                                                    ($option->off_label ?? 'Off');
                                                                $isChecked = in_array($option->id, $selectedOptions);
                                                            @endphp
                                                            <div class="toggle-switch mb-2">
                                                                <label
                                                                    class="toggle-label flex items-center cursor-pointer">
                                                                    <div class="relative">
                                                                        <input type="checkbox"
                                                                            wire:model.live="selectedOptions"
                                                                            value="{{ $option->id }}"
                                                                            class="sr-only peer"
                                                                            {{ $isChecked ? 'checked' : '' }}>

                                                                        <div
                                                                            class="w-12 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 transition-colors">
                                                                        </div>
                                                                        <div
                                                                            class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6">
                                                                        </div>
                                                                    </div>
                                                                    <div class="ml-3">
                                                                        <span
                                                                            class="font-medium">{{ $optionName }}</span><br>
                                                                        <span class="text-xs text-gray-500">
                                                                            {{ $isChecked ? $onLabel : $offLabel }}
                                                                        </span>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @elseif($filterType === 'slider')
                                                        <div x-data="{
                                                            min: {{ $filter->options->min('min_value') ?? 0 }},
                                                            max: {{ $filter->options->max('max_value') ?? 100 }},
                                                            currentMin: {{ $minPrice }},
                                                            currentMax: {{ $maxPrice }},
                                                            unit: '{{ $filter->options->first() ? $filter->options->first()->translations->where('language_id', $lang_id)->first()->unit ?? ($filter->options->first()->unit ?? '') : '' }}',

                                                            init() {
                                                                this.$nextTick(() => {
                                                                    this.setupSlider();
                                                                });
                                                            },

                                                            setupSlider() {
                                                                const slider = this.$refs.slider;
                                                                if (typeof noUiSlider !== 'undefined' && slider) {
                                                                    if (slider.noUiSlider) {
                                                                        slider.noUiSlider.destroy();
                                                                    }

                                                                    noUiSlider.create(slider, {
                                                                        start: [this.currentMin, this.currentMax],
                                                                        connect: true,
                                                                        range: {
                                                                            'min': this.min,
                                                                            'max': this.max
                                                                        }
                                                                    });

                                                                    slider.noUiSlider.on('update', (values) => {
                                                                        this.currentMin = Math.round(values[0]);
                                                                        this.currentMax = Math.round(values[1]);
                                                                    });

                                                                    slider.noUiSlider.on('end', () => {
                                                                        $wire.setPriceRange(this.currentMin, this.currentMax);
                                                                    });
                                                                }
                                                            }
                                                        }" class="range-slider py-4">
                                                            <div class="values-display flex justify-between mb-2">
                                                                <span x-text="currentMin + ' ' + unit"></span>
                                                                <span x-text="currentMax + ' ' + unit"></span>
                                                            </div>

                                                            <div x-ref="slider" class="slider-element"></div>
                                                        </div>
                                                    @elseif($filterType === 'color')
                                                        <div class="color-options flex flex-wrap gap-2">
                                                            @foreach ($filter->options as $option)
                                                                @php
                                                                    $isSelected = in_array(
                                                                        $option->id,
                                                                        $selectedOptions,
                                                                    );
                                                                    $optionTranslation = $option->translations
                                                                        ->where('language_id', $lang_id)
                                                                        ->first();
                                                                    $colorName =
                                                                        $optionTranslation->name ?? $option->name;
                                                                    // Get color value from option or fallback to a default
                                                                    $colorValue =
                                                                        $optionTranslation->color_value ?? '#cccccc';
                                                                @endphp

                                                                <button
                                                                    wire:click="toggleFilterOption({{ $option->id }})"
                                                                    class="color-option w-6 h-6 rounded-full border {{ $isSelected ? 'border-black' : 'border-gray-300' }}"
                                                                    style="background-color: {{ $colorValue }}; position: relative;"
                                                                    title="{{ $colorName }}">
                                                                    @if ($isSelected)
                                                                        <span
                                                                            class="absolute inset-0 flex items-center justify-center text-white">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="12" height="12"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round">
                                                                                <polyline points="20 6 9 17 4 12">
                                                                                </polyline>
                                                                            </svg>
                                                                        </span>
                                                                    @endif
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                       
                </div>
                
             </div>
              @if ($products->count())
                            <div class="auto-choice-rgt ">
                                <!-- Product Count and Sort -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        @if ($products->count() > 0)
                                            @php
                                                $currentPage = $products->currentPage();
                                                $perPage = $products->perPage();
                                                $total = $products->total();
                                                $from = ($currentPage - 1) * $perPage + 1;
                                                $to = min($currentPage * $perPage, $total);
                                            @endphp
                                            <p class="m-0">
                                                Showing {{$from}}-{{$to}} of {{$total}}
                                            </p>
                                        @else
                                            <p class="m-0">Showing {{ $products->count() }} results</p>
                                        @endif
                                    </div>
                                    <div wire:ignore class="d-none">
                                        <x-social-icon/>
                                    </div>
                                </div>
                                @if (!empty($products))
                                    @foreach ($products as $index => $item)
                                        <div class="automotive-card auto-bg" data-aos="fade-up"
                                            data-aos-duration="1000" wire:key="product-{{ $item->id }}">
                                            <div class="auto-choice-card" style="position: relative; ">
                                                @php
                                                     $isRecommended = $item->is_affiliate && $index === 0;
                                                 @endphp
                                                 <div class="card-compare-m">
                                                     @if($isRecommended)
                                                         <div style="margin-bottom: 25px;">
                                                             <span style="background-color: #f8fafc; color: #06498b; border: 1px solid #06498b; padding: 4px 12px; border-radius: 20px; ;">
                                                                 <i class="far fa-star text-warning" style="margin-right: 4px; color: #06498b !important;"></i> Top choice
                                                             </span>
                                                         </div>
                                                     @endif

                                                    <div  style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: stretch; gap: 20px; width: 100%;">
                                                        <!-- Left Column -->
                                                        <div style="flex: 1 1 0%; min-width: 320px; display: flex; flex-direction: column; justify-content: flex-start;">
                                                            <!-- Logo & Title -->
                                                            <div class="auto-choice-hd" style="border: none; padding: 0; margin-bottom: 0;">
                                                                <div class="inn_sl_hed" style="width: 100%;">
                                                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}">
                                                                        <div class="sli_img choice_img top-product-logo">
                                                                            <x-business-logo :business="$item" />
                                                                        </div>
                                                                    </a>
                                                                    <div class="sl_h">
                                                                        <div class="inn_h">
                                                                            <div class="sl_main">
                                                                                <h6 class="head">{{ $item->translations->first()?->name ?? '' }}</h6>
                                                                                <div class="d-none" wire:key="wishlist-container-{{ $item->id }}">
                                                                                    @livewire('wishlist', ['productId' => $item->id], key('wishlist-' . $item->id))
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tp-btm d-flex align-items-center" style="gap: 6px;">
                                                                            <span class="rate_box_num" style="font-size: 14px; font-weight: 500; color: #333;">{{ number_format($item->reviews->avg('rating'), 1) }}</span>
                                                                            <div class="inn_ul d-inline-flex m-0">
                                                                                <div class="rating-stars ">
                                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                                        @if ($i <= floor($item->reviews->avg('rating')))
                                                                                            <i class="fas fa-star text-warning"></i>
                                                                                        @elseif ($i - 0.5 <= $item->reviews->avg('rating'))
                                                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                                                        @else
                                                                                            <i class="far fa-star text-warning"></i>
                                                                                        @endif
                                                                                    @endfor
                                                                                </div>
                                                                            </div>
                                                                            <span class="rate_box_count text-muted" style="font-size: 14px;">({{ $item->reviews->count() }})</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Short Description -->
                                                            @if(!empty($item->translations->first()?->short_description))
                                                                <div class="mb-3 mt-1 text-start" style="font-size: 14px; color: #444; line-height: 1.5; width: 100%;">
                                                                    {{ $item->translations->first()?->short_description }}
                                                                </div>
                                                            @endif

                                                            <!-- Features -->
                                                            <div class="slider_content_sec my-3" style="width: 100% !important; max-width: 100% !important;">
                                                                <div class="main_feature_lg" style="width: 100% !important; max-width: 100% !important;">
                                                                    <div class="feture_box lft_check_box size18" style="border: none; padding: 0; background: transparent; min-height: auto; width: 100% !important; max-width: 100% !important;">
                                                                        <div class="usp-grid-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                                                            @if ($item->usps->count() > 0)
                                                                                @foreach ($item->usps->take(4) as $usp)
                                                                                    <div class="d-flex align-items-center size18">
                                                                                        <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                            <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                        </div>
                                                                                        <p class="m-0" style="font-size: 14px !important; font-weight:500 !important; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $usp->text }}</p>
                                                                                    </div>
                                                                                @endforeach
                                                                            @else
                                                                                <div class="d-flex align-items-center size18">
                                                                                    <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                        <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                    </div>
                                                                                    <p class="m-0" style="font-size: 14px !important; font-weight:500 !important; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Free domain & SSL certificate</p>
                                                                                </div>
                                                                                <div class="d-flex align-items-center size18">
                                                                                    <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                        <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                    </div>
                                                                                    <p class="m-0" style="font-size: 14px !important; font-weight:500 !important;  color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Customizable automatic updates</p>
                                                                                </div>
                                                                                <div class="d-flex align-items-center size18">
                                                                                    <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                        <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                    </div>
                                                                                    <p class="m-0" style="font-size: 14px !important; font-weight:500 !important;  color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Scalable performance management</p>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Compare Checkbox -->
                                                        </div>

                                                        <!-- Right Column -->
                                                        <div  class="rgt_rgt_bx" style="  flex: 0 0 250px; min-width: 250px; display: flex; flex-direction: column; justify-content: space-between; align-items: stretch; margin-top: 10px;">
                                                            <!-- Buttons -->
                                                            <div class="auto-choice-btn d-flex flex-column gap-2" style="width: 100%; margin: 0;">
                                                                <a href="{{ $item->affiliate_link ?? $item->permanent_url }}"
                                                                    class="cta cta_orange justify-content-center"
                                                                    target="_blank" style="display: flex !important; width: 100%; align-items: center; border-radius: 30px;">
                                                                    Visit website
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                                </a>
                                                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}"
                                                                    class="cta cta_outline justify-content-center" style="display: flex !important; width: 100%; align-items: center; border: 1px solid #06498b; color: #06498b; border-radius: 30px;">
                                                                    View details
                                                                </a>
                                                            </div>
                                                            
                                                            @php
                                                                $allPrices = $item->products
                                                                    ->flatMap(function ($product) {
                                                                        return $product->prices;
                                                                    })
                                                                    ->sortBy(function ($price) {
                                                                        $now = Illuminate\Support\Carbon::now();
                                                                        if ($price->discount_price && $price->discount_expiration_date && $now->lte(Illuminate\Support\Carbon::parse($price->discount_expiration_date))) {
                                                                            return $price->discount_price;
                                                                        } elseif ($price->renewal_price) {
                                                                            return $price->renewal_price;
                                                                        } else {
                                                                            return $price->price;
                                                                        }
                                                                    });

                                                                $startingPrice = $allPrices->first();
                                                                $displayPrice = null;

                                                                if ($startingPrice) {
                                                                    $now = Illuminate\Support\Carbon::now();
                                                                    if ($startingPrice->discount_price && $startingPrice->discount_expiration_date && $now->lte(Illuminate\Support\Carbon::parse($startingPrice->discount_expiration_date))) {
                                                                        $displayPrice = $startingPrice->discount_price;
                                                                    } elseif ($startingPrice->renewal_price) {
                                                                        $displayPrice = $startingPrice->renewal_price;
                                                                    } else {
                                                                        $displayPrice = $startingPrice->price;
                                                                    }
                                                                }
                                                            @endphp

                                                            <!-- Price -->
                                                            @if ($startingPrice)
                                                                <div class="text-center mt-4 w-100" style="  padding: 15px 25px; border-radius: 8px;">
                                                                    <h6 style="font-size: 13px; color: #002347; font-weight: 600; margin-bottom: 4px;">Starting price</h6>
                                                                    <h3 style="font-weight: 700 !important; color: #002347; font-size: 26px !important; margin-bottom: 2px;">
                                                                        {{ $startingPrice->currency }}{{ number_format($displayPrice, 2) }}
                                                                    </h3>
                                                                    <p style="font-size: 11px; color: #444444; margin-bottom: 0;">Flat Rate, Per One_time</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="width: 100%;">
                                                    <livewire:compare-products :item="$item" :key="'compare-' . $item->id" />
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                                @endif
                                <!-- Pagination Links -->
                                @php
                                $currentPage = $products->currentPage();
                                $lastPage = $products->lastPage() ?? 1;
                                $maxVisible = 7;

                                $startPage = max(1, $currentPage - floor($maxVisible / 2));
                                $endPage = min($lastPage, $startPage + $maxVisible - 1);

                                if ($endPage - $startPage + 1 < $maxVisible) {
                                    $startPage = max(1, $endPage - $maxVisible + 1);
                                }

                                $showLeftDots = $startPage > 2;
                                $showRightDots = $endPage < $lastPage - 1;
                            @endphp

                            @if ($lastPage > 1)
                                <div class="btn-pages">
                                    {{-- Previous Button (only if there's a previous page) --}}
                                    @if ($currentPage > 1)
                                        <a
                                            href="{{ $this->getCleanUrl($currentPage - 1) }}"
                                            wire:click.prevent="previousPage"
                                            class="pagination-btn pagination-arrow">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    {{-- First Page --}}
                                    @if ($startPage > 1)
                                        <a
                                            href="{{ $this->getCleanUrl(1) }}"
                                            wire:click.prevent="gotoPage(1)"
                                            class="pagination-btn {{ $currentPage == 1 ? 'active' : '' }}"
                                        >1</a>

                                        @if ($showLeftDots)
                                            <span class="pagination-dots">...</span>
                                        @endif
                                    @endif

                                    {{-- Page Numbers --}}
                                    @for ($page = $startPage; $page <= $endPage; $page++)
                                        <a
                                            href="{{ $this->getCleanUrl($page) }}"
                                            wire:click.prevent="gotoPage({{ $page }})"
                                            class="pagination-btn {{ $currentPage == $page ? 'active' : '' }}"
                                        >{{ $page }}</a>
                                    @endfor

                                    {{-- Last Page --}}
                                    @if ($endPage < $lastPage)
                                        @if ($showRightDots)
                                            <span class="pagination-dots">...</span>
                                        @endif

                                        <a
                                            href="{{ $this->getCleanUrl($lastPage) }}"
                                            wire:click.prevent="gotoPage({{ $lastPage }})"
                                            class="pagination-btn {{ $currentPage == $lastPage ? 'active' : '' }}"
                                        >{{ $lastPage }}</a>
                                    @endif

                                    {{-- Next Button (only if there's a next page) --}}
                                    @if ($currentPage < $lastPage)
                                        <a
                                            href="{{ $this->getCleanUrl($currentPage + 1) }}"
                                            wire:click.prevent="nextPage"
                                            class="pagination-btn pagination-arrow next">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif

                        </div>
                        @else

                            <div class="auto-choice-rgt">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <p class="m-0">Showing {{ $products->count() }} results </p>
                                    </div>
                                    <x-social-icon/>
                                </div>

                                <div class="alert alert-info">
                                    <p class="mb-2 text-center fs-4 text-secondary">Sorry, we don't have any Products that match your filters. Try adjusting them to see more options.</p>
                                </div>
                            </div>
                            {{-- <div class="auto-choice-rgt" style="position: relative; min-height: 300px;">
                                <div style="position: absolute; top: 0; left: 0; z-index: 1050; margin: 1rem;">
                                    <div class="alert alert-info text-start shadow" style="max-width: 320px;">
                                        @if ($this->hasActiveFilters())
                                        <p class="mb-3">{{ static_text('no_prod_mach_fil') }}</p>
                                        <button wire:click="clearFilters" class="btn btn-primary btn-sm">
                                            <i class="fa fa-refresh me-1"></i> {{ static_text('reset_filter') }}
                                        </button>
                                        @else
                                        <p class="mb-0">
                                            Sorry ,No Products available at the moment.
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div> --}}
                        @endif
        </div>
            <livewire:compare-bar />
    </section>
    <section class="subs_sec light top_rated_org_sec ">
        {{-- <div class="container">
            <div class="subs_content">
                <h2 data-aos="fade-up" data-aos-duration="1000">{{ static_text('top_rated_mail_section_titile') }}
                </h2>
                <p data-aos="fade-up" data-aos-duration="1000">{{ static_text('top_rated_mail_section_desc') }}
                </p>
                <div class="mail_field" data-aos="fade-up" data-aos-duration="1000">
                    <div class="email_box">
                        <input type="email" id="email" name="email" placeholder="Email Address*">
                    </div>
                    <div class="accor-btn sbs_bttn">
                        <a href="javascript:void(0)" class="cta cta_white">{{ static_text('subscribe') }}</a>
                    </div>
                </div>
                <div class="checkbox_field" data-aos="fade-up" data-aos-duration="1000"
                    style="margin-top: 10px; display: flex; justify-content: center;">
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" id="agree_terms" name="agree_terms" required>
                        <span>{{ static_text('mail_below_text') }}</span>
                    </label>
                </div>
            </div>
        </div> --}}
        <x-news-letter-subscription/>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
    <script>
        function initPriceSlider() {
            setTimeout(() => {
                const slider = document.getElementById('priceRangeSlider2');
                const minInput = document.getElementById('minPriceInput2');
                const maxInput = document.getElementById('maxPriceInput2');
                // Get the dynamic maximum price from the data attribute
                const maxPriceValue = slider.dataset.maxPrice ? parseInt(slider.dataset.maxPrice) : 10000;

                if (!slider || !minInput || !maxInput || typeof noUiSlider === 'undefined') {
                    console.warn("Slider init failed.");
                    return;
                }

                if (slider.noUiSlider) {
                    slider.noUiSlider.destroy();
                }

                noUiSlider.create(slider, {
                    start: [parseInt(minInput.value) || 0, parseInt(maxInput.value) || maxPriceValue],
                    connect: true,
                    range: {
                        min: 0,
                        max: maxPriceValue
                    },
                    step: Math.max(1, Math.floor(maxPriceValue / 100)) // Dynamic step based on max price
                });

                slider.noUiSlider.on('update', function(values) {
                    const min = Math.round(values[0]);
                    const max = Math.round(values[1]);
                    minInput.value = min;
                    maxInput.value = max;
                });

                slider.noUiSlider.on('change', function() {
                    minInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    maxInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                });

                minInput.addEventListener('change', function() {
                    slider.noUiSlider.set([this.value, null]);
                });

                maxInput.addEventListener('change', function() {
                    slider.noUiSlider.set([null, this.value]);
                });
            }, 100); // slight delay to ensure DOM is updated
        }

        document.addEventListener('DOMContentLoaded', initPriceSlider);
        document.addEventListener('livewire:load', initPriceSlider);

        // Set up a listener for Livewire events that might update max price
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('set-price-range', (data) => {
                const slider = document.getElementById('priceRangeSlider2');
                if (slider && data.maxPrice) {
                    slider.dataset.maxPrice = data.maxPrice;
                    // Re-initialize slider with the new max value
                    initPriceSlider();
                }
            });
        });
        // Livewire.hook('message.processed', (message, component) => {
        //     initPriceSlider(); // Re-initialize after DOM is updated by Livewire
        // });
    </script>
    <script>
        window.addEventListener('scroll-to-middle', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        window.addEventListener('scroll-to-top', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        // Update browser URL when pagination changes
        window.addEventListener('update-pagination-url', function(event) {
            window.history.pushState(null, '', event.detail.url);
        });
    </script>


</div>
