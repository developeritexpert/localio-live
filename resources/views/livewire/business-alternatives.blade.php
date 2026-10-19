<div>
    @include('User.partials.business_detail_styles')
@php
    $lang_id = getCurrentLanguageID();
    $bCategoryIds = array_filter(array_unique(array_merge(
        [$business->category_id],
        $business->category && $business->category->parent_id === null ? \App\Models\Category::where('parent_id', $business->category_id)->pluck('id')->toArray() : [],
        $business->subCategories ? $business->subCategories->pluck('id')->toArray() : []
    )));

    // 1. Fetch 4-6 popular alternatives
    $popularAltBusinesses = \App\Models\Business::where('id', '!=', $business->id)
        ->where('status', 1)
        ->whereIn('category_id', $bCategoryIds)
        ->with([
            'translations' => fn($q) => $q->where('lang_id', $lang_id),
            'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
            'category.ratingCriteria',
            'products.prices'
        ])
        ->get()
        ->sortByDesc(function($b) {
            return $b->display_rating ?? 0;
        })
        ->take(3);

    if ($popularAltBusinesses->count() < 3) {
        $morePop = \App\Models\Business::where('id', '!=', $business->id)
            ->where('status', 1)
            ->whereNotNull('category_id')
            ->whereNotIn('id', $popularAltBusinesses->pluck('id'))
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
                'category.ratingCriteria',
                'products.prices'
            ])
            ->get()
            ->sortByDesc(function ($b) {
                return $b->display_rating ?? 0;
            })
            ->take(3 - $popularAltBusinesses->count());

        $popularAltBusinesses = $popularAltBusinesses->merge($morePop);
    }

    // 2. Fetch 10 affiliated businesses for tables
    if (isset($topAffiliatedBusinesses) && $topAffiliatedBusinesses->count() > 0) {
        $affiliatedTableBusinesses = $topAffiliatedBusinesses;
    } else {
        $affiliatedTableBusinesses = \App\Models\Business::where('is_affiliate', 1)
            ->where('id', '!=', $business->id)
            ->where('status', 1)
            ->whereIn('category_id', $bCategoryIds)
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $lang_id),
                'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
                'category.ratingCriteria'
            ])
            ->get()
            ->sortByDesc(function($b) {
                return $b->display_rating ?? 0;
            })
            ->take(10);

        if ($affiliatedTableBusinesses->count() < 10) {
            $moreAff = \App\Models\Business::where('is_affiliate', 1)
                ->where('id', '!=', $business->id)
                ->whereNotIn('id', $affiliatedTableBusinesses->pluck('id'))
                ->where('status', 1)
                ->with([
                    'translations' => fn($q) => $q->where('lang_id', $lang_id),
                    'reviews' => fn($q) => $q->whereIn('status', ['published', 'active']),
                    'category.ratingCriteria'
                ])
                ->get()
                ->sortByDesc(function($b) {
                    return $b->display_rating ?? 0;
                })
                ->take(10 - $affiliatedTableBusinesses->count());

            $affiliatedTableBusinesses = $affiliatedTableBusinesses->merge($moreAff);
        }
        // ONLY affiliated businesses allowed
    }

    $affiliatedTableBusinesses = $affiliatedTableBusinesses->where('is_affiliate', 1);

    // In these tables show ONLY affiliated businesses
    $tableRowsList = collect($business && $business->is_affiliate ? [$business] : [])
        ->merge($affiliatedTableBusinesses)
        ->filter(fn($b) => (int)($b->is_affiliate ?? 0) === 1)
        ->unique('id');
@endphp


    <section class="top-automotive-sec top_rate_pg light common_detail_sec" style="{{ !empty($hasUpperHeader) ? 'margin-top: 20px !important; padding-top: 0 !important;' : '' }}">
    <div class="top-auto-btm">
        
            <div class="container">
                <div class="top-auto-choice">
                    <div class="top-rated-heading-block" style="padding-bottom: 16px; margin-bottom: 24px;">
                        <div class="row align-items-start">
                            <div class="text-start col-lg-8">


                                <!-- 1. Most popular alternatives (Matched design with product_detail.blade.php) -->
                                @if($popularAltBusinesses->count() > 0)
                                    <section class="software-like p_50 product_integra_sec my-4" id="sectionAlternatives">
                                        <div class="container">
                                            <div class="sftwre-like-innr">
                                                @php
                                                    if (!isset($bTrans) || !$bTrans) {
                                                        $bTrans = $business ? ($business->translations->firstWhere('lang_id', $lang_id) ?? ($business->translations->where('language_id', $lang_id)->first() ?? $business->translations->first())) : null;
                                                    }
                                                    $resolvedPopAlt = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                        $business,
                                                        'popular_alternatives',
                                                        $lang_id
                                                    );
                                                    $altPopHeadline = !empty($resolvedPopAlt['headline'])
                                                        ? $resolvedPopAlt['headline']
                                                        : (!empty($bTrans->popular_alternatives_headline)
                                                            ? $bTrans->popular_alternatives_headline
                                                            : ('Most popular ' . $businessName . ' alternatives'));

                                                    $altPopDesc = !empty($resolvedPopAlt['subtext'])
                                                        ? $resolvedPopAlt['subtext']
                                                        : (!empty($bTrans->popular_alternatives_subtext)
                                                            ? $bTrans->popular_alternatives_subtext
                                                            : "Based on other buyer's searches, these are the products that could be a good fit for you.");
                                                @endphp
                                                <div class="sftwre-asana-hd" data-aos="fade-up" data-aos-duration="1000">
                                                    <h2>{{ $altPopHeadline }}</h2>
                                                    @if(!empty($altPopDesc))
                                                        <div class="ck-content" style="color: #64748b; font-size: 15px;">
                                                            {!! $altPopDesc !!}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="sft_ware_test" style="">
                                                    <div class="sftware-alternative d-flex flex-wrap justify-content-center gap-4" data-aos="fade-up" data-aos-duration="1000">
                                                        @foreach ($popularAltBusinesses->take(3) as $altbusiness)
                                                            @php
                                                                $altTrans = $altbusiness->translations->firstWhere('lang_id', $lang_id) ?? $altbusiness->translations->first();
                                                                $altBizName = $altTrans->name ?? $altbusiness->name ?? 'Business';
                                                                $altBizSlug = $altTrans->slug ?? $altbusiness->slug ?? 'business-' . $altbusiness->id;

                                                                $altStartingPrice = null;
                                                                $altCurrency = '$';
                                                                $altAdditionalInfo = null;
                                                                $altPrice = getBusinessesWithStartingPrice($altbusiness);
                                                                if (!empty($altPrice) && isset($altPrice[0]['starting_price'])) {
                                                                    $altBusinessPrice = $altPrice[0]['starting_price'];
                                                                    $altStartingPrice = $altBusinessPrice['amount'] ?? null;
                                                                    $altCurrency = $altBusinessPrice['currency'] ?? '$';
                                                                    $altAdditionalInfo = $altBusinessPrice['additional_info'] ?? null;
                                                                }

                                                            $altReviews = \App\Models\Review::where('business_id', $altbusiness->id)
    ->whereIn('status', ['published', 'active'])
    ->get();

$altCriteria = $altbusiness->category
    ? $altbusiness->category->getEffectiveRatingCriteria()
    : collect();

foreach ($altCriteria as $criterion) {
    $totalScore = 0;
    $ratingCount = 0;

    foreach ($altReviews as $review) {
        $ratingRecord = \App\Models\ReviewRating::where('review_id', $review->id)
            ->where('criteria_id', $criterion->id)
            ->first();

        if ($ratingRecord && $ratingRecord->rating !== null) {
            $totalScore += (float) $ratingRecord->rating;
            $ratingCount++;
            continue;
        }

        $legacyVal = null;
        $criterionName = strtolower(trim($criterion->name));

        if ($criterionName === 'ease of use') {
            $legacyVal = $review->ease_of_use_rating;
        } elseif ($criterionName === 'customer service') {
            $legacyVal = $review->customer_service_rating;
        } elseif ($criterionName === 'features') {
            $legacyVal = $review->exclusive_service_rating;
        } elseif ($criterionName === 'value for money') {
            $legacyVal = $review->value_for_money_rating;
        }

        if ($legacyVal !== null && $legacyVal !== '') {
            $totalScore += (float) $legacyVal;
            $ratingCount++;
        }
    }

    $criterion->average_rating = $ratingCount > 0
        ? round($totalScore / $ratingCount, 1)
        : null;
}


                                                                $altRatingAvg = $altbusiness->reviews->avg('rating');
                                                                $count = $altbusiness->approved_reviews_count;
                                                            @endphp
                                                            <div class="sftware-alternative-pck" data-aos="fade-up" data-aos-duration="1000"
                                                                onclick="if(!event.target.closest('a')) { window.location.href = '{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $altBizSlug]) }}'; }"
                                                                style="cursor: pointer; padding: 25px 20px;">

                                                                <div class="ans_lft p_top_btm_sftwre pt-0 pb-3" style="border-bottom: 1px solid #eee;">
                                                                    <div class="top-product-logo">
                                                                        <x-business-logo :business="$altbusiness" :name="$altBizName" />
                                                                    </div>
                                                                    <div class="asn-rating">
                                                                        <h6 class="m-0 fw_700" style="color: #002347">
                                                                            {{ $altBizName }}
                                                                        </h6>
                                                                        @if($altbusiness->should_show_rating)
                                                                        <div class="rating-group">
                                                                            <span class="rate_box_num fw-medium">{{ number_format($altbusiness->display_rating, 1) }}</span>
                                                                            <div class="rating-stars">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if ($i <= floor($altbusiness->display_rating))
                                                                                        <i class="fas fa-star text-warning"></i>
                                                                                    @elseif ($i - 0.5 <= $altbusiness->display_rating)
                                                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                                                    @else
                                                                                        <i class="far fa-star text-warning"></i>
                                                                                    @endif
                                                                                @endfor
                                                                            </div>
                                                                            @if($altbusiness->should_show_review_count)
                                                                            <span class="rate_box_text">
                                                                                ({{ $altbusiness->approved_reviews_count }})
                                                                            </span>
                                                                            @endif
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                @if($altbusiness->should_show_detailed_ratings)
                                                                <div class="over-rate-progress p_top_btm_sftwre pt-3 pb-3" style="border-bottom: 1px solid #eee;">
                                                                    <p class="fw_700 mb-3" style="color: #002347; font-size:12px;">
                                                                        Detailed user ratings 
                                                                    </p>

                                                                    @foreach ($altCriteria as $criterion)
                                                                        <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">

                                                                            <p class="m-0" style="font-size: 12px; color: #002347;">
                                                                                {{ $criterion->name }}
                                                                            </p>

                                                                            <div class="prgs_br d-flex align-items-center">

                                                                                @if ($criterion->average_rating !== null)
                                                                                    <progress
                                                                                        class="progress-bar"
                                                                                        value="{{ $criterion->average_rating * 20 }}"
                                                                                        max="100">
                                                                                    </progress>

                                                                                    <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">
                                                                                        {{ number_format($criterion->average_rating, 1) }}
                                                                                    </span>
                                                                                @else
                                                                                    <span style="font-size: 12px; font-weight: 600; color: #999; min-width: 32px; text-align: right;">
                                                                                        N/A
                                                                                    </span>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                @endif

                                                                @php
                                                                    $hasAltPrice = !empty($altbusiness->is_affiliate) && !empty($altStartingPrice) && !in_array(strtoupper(trim((string)$altStartingPrice)), ['N/A', 'NA', 'NULL', '0', '0.00', '']);
                                                                @endphp
                                                                @if ($hasAltPrice)
                                                                    <div class="start-from p_top_btm_sftwre pt-3 pb-3">
                                                                        <h6 style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 14px;">Starting price</h6>
                                                                        <h3 class="m-0 mt-1" style="font-weight: 700; color: #333; font-size: 24px; line-height:1!important;">
                                                                            <span>{{ $altCurrency }}{{ $altStartingPrice }}</span>
                                                                        </h3>
                                                                        @if(!empty($altAdditionalInfo) && !in_array(strtoupper(trim((string)$altAdditionalInfo)), ['NA', 'N/A', 'NULL']))
                                                                            <small class="text-muted" style="font-size: 12px;">{{ $altAdditionalInfo }}</small>
                                                                        @elseif(!empty($altBusinessPrice['time_unit']) && !in_array(strtolower($altBusinessPrice['time_unit']), ['one_time', 'na', 'n/a', 'null']))
                                                                            <small class="text-muted" style="font-size: 12px;">per {{ strtolower($altBusinessPrice['time_unit']) }}</small>
                                                                        @endif
                                                                    </div>
                                                                @endif

                                                                <div class="sftwre-alt-btn pt-2">
                                                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $altBizSlug]) }}"
                                                                        class="cta btn_blue w-100 d-flex align-items-center justify-content-center"
                                                                        style="padding: 10px 20px; font-weight: 500; text-decoration: none; font-size: 14px;">
                                                                        {{ static_text('profile_and_reviews') ?: 'Profile & reviews' }}
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

                                
                            </div>
                            <div class="col-lg-4 mt-4 mt-md-0 text-start">
                                <div class="mb-4">
                                    <x-review-breakdown-card :business="$business" />
                                </div>
                                <!-- Widget 3: More About Business Card -->
                                <x-more-about-business :business="$business" current="alternatives" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(isset($tableRowsList) && $tableRowsList->count() > 0)
    @php
        $hasUnder10Reviews = false;
        if (isset($tableRowsList) && $tableRowsList->count() > 0) {
            $hasUnder10Reviews = $tableRowsList->contains(function ($rBiz) {
                $revCount = $rBiz->relationLoaded('reviews')
                    ? $rBiz->reviews->whereIn('status', ['published', 'active'])->count()
                    : $rBiz->reviews()->whereIn('status', ['published', 'active'])->count();
                return $revCount < 10;
            });
        }
    @endphp

    <!-- Dynamic Section 2: Top 10 (Affiliated Table 1) -->
    <section class="comparison-table-sec py-4">
        <div class="container">
            <div class="comparison-table-section my-4">
                            @php
                                if (!isset($bTrans) || !$bTrans) {
                                    $bTrans = $business ? ($business->translations->firstWhere('lang_id', $lang_id) ?? ($business->translations->where('language_id', $lang_id)->first() ?? $business->translations->first())) : null;
                                }
                                $altFieldKey = $hasUnder10Reviews ? 'compare_alternatives_under_10' : 'compare_alternatives';
                                $resolvedCompAlt = \App\Services\ContentTemplateService::resolveBusinessSection(
                                    $business,
                                    $altFieldKey,
                                    $lang_id
                                );
                                if ($hasUnder10Reviews && empty($resolvedCompAlt['headline']) && empty($resolvedCompAlt['subtext'])) {
                                    $resolvedCompAlt = \App\Services\ContentTemplateService::resolveBusinessSection(
                                        $business,
                                        'compare_alternatives',
                                        $lang_id
                                    );
                                }
                                $compAlternativesHeadline = !empty($resolvedCompAlt['headline'])
                                    ? $resolvedCompAlt['headline']
                                    : (!empty($bTrans->compare_alternatives_headline)
                                        ? $bTrans->compare_alternatives_headline
                                        : ('Top 10 ' . $businessName . ' alternatives'));

                                $compAlternativesSubtext = !empty($resolvedCompAlt['subtext'])
                                    ? $resolvedCompAlt['subtext']
                                    : ($bTrans->compare_alternatives_subtext ?? '');
                            @endphp
                            <h2 style="margin-bottom: {{ !empty($compAlternativesSubtext) ? '6px' : '16px' }};">
                                {{ $compAlternativesHeadline }}
                            </h2>
                            @if(!empty($compAlternativesSubtext))
                                <style>
                                    .compare-alternatives-subtext p { margin-bottom: 0 !important; }
                                    .compare-alternatives-subtext p:not(:last-child) { margin-bottom: 10px !important; }
                                </style>
                                <div class="compare-alternatives-subtext ck-content mb-3" style="font-size: 15px; color: #64748b;">
                                    {!! $compAlternativesSubtext !!}
                                </div>
                            @endif

                            <div class="table-responsive rounded-3 border bg-white" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                <table class="table align-middle mb-0" style="font-size: 14px; color: #1e3050;">
                                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                        <tr>
                                            <th scope="col" style="padding: 14px 18px; font-weight: 500; color: #002347; width: 240px; min-width: 240px; max-width: 240px;"></th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347; width: 140px; min-width: 140px; max-width: 140px;">{{ static_text('overall_rating_label') ?: 'Overall rating' }}</th>
                                            @php
                                                $targetCat = $business->category;
                                                $allCriteria = collect();

                                                if ($targetCat) {
                                                    // If subcategory with parent, rating criteria are defined on the parent (main category)
                                                    if ($targetCat->parent_id && $targetCat->parent && $targetCat->parent->ratingCriteria->isNotEmpty()) {
                                                        $allCriteria = $targetCat->parent->ratingCriteria;
                                                        // If subcategory also defined custom criteria, append them
                                                        if ($targetCat->ratingCriteria->isNotEmpty()) {
                                                            $subCustom = $targetCat->ratingCriteria->where('is_default', 0)->whereNotIn('name', $allCriteria->pluck('name'));
                                                            if ($subCustom->isNotEmpty()) {
                                                                $allCriteria = $allCriteria->concat($subCustom);
                                                            }
                                                        }
                                                    } elseif ($targetCat->ratingCriteria->isNotEmpty()) {
                                                        $allCriteria = $targetCat->ratingCriteria;
                                                    }
                                                }

                                                // Fallback 1: Check table businesses' categories
                                                if ($allCriteria->isEmpty()) {
                                                    foreach ($tableRowsList as $tblBiz) {
                                                        $bCat = $tblBiz->category;
                                                        if ($bCat) {
                                                            if ($bCat->parent_id && $bCat->parent && $bCat->parent->ratingCriteria->isNotEmpty()) {
                                                                $allCriteria = $bCat->parent->ratingCriteria;
                                                                break;
                                                            } elseif ($bCat->ratingCriteria->isNotEmpty()) {
                                                                $allCriteria = $bCat->ratingCriteria;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }

                                                // Fallback 2: getEffectiveRatingCriteria()
                                                if ($allCriteria->isEmpty() && $targetCat) {
                                                    $allCriteria = $targetCat->getEffectiveRatingCriteria();
                                                }

                                                // Fallback 3: DefaultRatingCriteria
                                                if ($allCriteria->isEmpty()) {
                                                    $allCriteria = \App\Models\DefaultRatingCriteria::orderBy('sort_order')->get();
                                                }

                                                $allStartingPrices = collect(getBusinessesWithStartingPrice($tableRowsList))->keyBy('business.id');
                                            @endphp
                                            @foreach($allCriteria as $criterion)
                                                <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 500; color: #002347;">{{ $criterion->name }}</th>
                                            @endforeach
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 500; color: #002347;">{{ static_text('recommend_label') ?: 'Recommend' }}</th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 500; color: #002347; width: 140px; min-width: 140px; max-width: 140px;">Starting price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tableRowsList as $rowBiz)
                                            @if(empty($rowBiz->is_affiliate))
                                                @continue
                                            @endif
                                            @php
                                                $rTrans = $rowBiz->translations->firstWhere('lang_id', $lang_id) ?? $rowBiz->translations->first();
                                                $rName = $rTrans->name ?? $rowBiz->name ?? 'Business';
                                                $rSlug = $rTrans->slug ?? $rowBiz->slug ?? 'business-' . $rowBiz->id;
                                                $affiliateUrl = $rowBiz->getTrackedUrl() ?? $rowBiz->affiliate_link ?? $rowBiz->permanent_url ?? route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $rSlug]);
                                                $rRevs = ($rowBiz->relationLoaded('reviews') && $rowBiz->reviews->whereIn('status', ['published', 'active'])->isNotEmpty()) ? $rowBiz->reviews->whereIn('status', ['published', 'active']) : $rowBiz->reviews()->whereIn('status', ['published', 'active'])->get();
                                                $rCount = $rRevs->count();
                                                $recommendPercent = ($rowBiz->should_show_detailed_ratings && $rCount > 0)
                                                    ? round(($rRevs->where('recommend', 1)->count() / $rCount) * 100)
                                                    : null;

                                                $bizStartingPrice = $allStartingPrices->get($rowBiz->id)['starting_price'] ?? null;
                                                if (!$bizStartingPrice && $rowBiz->products) {
                                                    $firstPrice = $rowBiz->products->flatMap->prices->first();
                                                    if ($firstPrice) {
                                                        $bizStartingPrice = [
                                                            'amount' => $firstPrice->price,
                                                            'currency' => $firstPrice->currency ?? '$',
                                                            'time_unit' => $firstPrice->time_unit ?? 'month'
                                                        ];
                                                    }
                                                }
                                                $bizCurrency = $bizStartingPrice['currency'] ?? '$';
                                                $bizAmount = isset($bizStartingPrice['amount']) ? number_format($bizStartingPrice['amount'], 2) : null;
                                                $bizTimeUnit = strtolower($bizStartingPrice['time_unit'] ?? '');
                                            @endphp
                                            <tr style="border-bottom: 1px solid #f1f5f9; {{ $rowBiz->id === $business->id ? 'background-color: #f8fafc;' : '' }}">
                                                <td style="padding: 14px 18px; font-weight: 500; color: #002347;">
                                                    <a href="{{ $affiliateUrl }}" target="_blank" rel="sponsored noopener" class="table-business-link d-flex align-items-center gap-2 text-decoration-none" style="color: inherit;">
                                                        <div class="top-product-small-logo">
                                                            <x-business-logo :business="$rowBiz" :name="$rName" />
                                                        </div>
                                                        <div class="d-flex align-items-center flex-nowrap">
                                                            <span class="table-business-name" style="color: #002347; font-weight: 500;">{{ $rName }}</span>
                                                            @if($rowBiz->id === $business->id)
                                                                <span class="badge bg-primary ms-1" style="font-size: 10px; font-weight: 600;">{{ static_text('current_listing_badge') ?: 'Current' }}</span>
                                                            @endif
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#002347" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                        </div>
                                                    </a>
                                                </td>
                                                <td class="text-center fw-bold" style="padding: 14px; color: #002347; white-space: nowrap;">
                                                    @if($rowBiz->should_show_rating)
                                                    <span class="d-inline-flex align-items-center gap-1">
                                                        <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                                        <span>{{ formatRating($rowBiz->display_rating) }}</span>
                                                    </span>
                                                    @else
                                                        <span style="color: #5b6b7a;">-</span>
                                                    @endif
                                                </td>
                                                @foreach($allCriteria as $criterion)
                                                    @php
                                                        $avg = null;
                                                        if ($rowBiz->should_show_detailed_ratings) {
                                                            $avg = $rRevs->isNotEmpty()
                                                                ? \App\Models\ReviewRating::whereIn('review_id', $rRevs->pluck('id'))
                                                                    ->where('criteria_id', $criterion->id)
                                                                    ->avg('rating')
                                                                : null;
                                                            if ($avg === null && $rowBiz->category) {
                                                                $bizCrit = $rowBiz->category->ratingCriteria->first(fn($c) => strtolower(trim($c->name)) === strtolower(trim($criterion->name)))
                                                                    ?? ($rowBiz->category->parent ? $rowBiz->category->parent->ratingCriteria->first(fn($c) => strtolower(trim($c->name)) === strtolower(trim($criterion->name))) : null)
                                                                    ?? (method_exists($rowBiz->category, 'getEffectiveRatingCriteria') ? $rowBiz->category->getEffectiveRatingCriteria()->first(fn($c) => strtolower(trim($c->name)) === strtolower(trim($criterion->name))) : null);
                                                                if ($bizCrit) {
                                                                    $avg = \App\Models\ReviewRating::whereIn('review_id', $rRevs->pluck('id'))
                                                                        ->where('criteria_id', $bizCrit->id)
                                                                        ->avg('rating');
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <td class="text-center" style="padding: 14px; color: #5b6b7a; font-weight: 500;">
                                                        {{ $avg !== null ? formatRating($avg) : '-' }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center fw-semibold" style="padding: 14px; color: #002347;">
                                                    @if($recommendPercent !== null)
                                                        {{ $recommendPercent }}%
                                                    @else
                                                        <span style="color: #5b6b7a;">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center" style="padding: 14px; color: #002347; font-weight: 500; white-space: nowrap;">
                                                    @if($bizStartingPrice && $bizAmount !== null && !empty($rowBiz->is_affiliate))
                                                        <span style="font-weight: 600;">{{ $bizCurrency }}{{ $bizAmount }}</span>
                                                        @if(!empty($bizStartingPrice['additional_info']) && !in_array(strtoupper(trim((string)$bizStartingPrice['additional_info'])), ['NA', 'N/A', 'NULL']))
                                                            <span style="font-size: 12px; color: #5b6b7a;"> {{ $bizStartingPrice['additional_info'] }}</span>
                                                        @elseif(!empty($bizTimeUnit) && !in_array($bizTimeUnit, ['one_time', 'na', 'n/a', 'null']))
                                                            <span style="font-size: 12px; color: #5b6b7a;">/{{ $bizTimeUnit === 'month' ? 'mo' : ($bizTimeUnit === 'year' ? 'yr' : $bizTimeUnit) }}</span>
                                                        @endif
                                                    @else
                                                        <span style="color: #5b6b7a;">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
        </div>
    </div>
    </section>

    <!-- Dynamic Section 3: Pros & cons (Pros/Cons Table 2) -->
    <section class="community-feedback-sec border_bt py-4">
        <div class="container">
            <div class="community-feedback-table-section my-4">
                                <div class="mb-3">
                                    @php
                                        if (!isset($bTrans) || !$bTrans) {
                                            $bTrans = $business ? ($business->translations->firstWhere('lang_id', $lang_id) ?? ($business->translations->where('language_id', $lang_id)->first() ?? $business->translations->first())) : null;
                                        }
                                        $commFieldKey = $hasUnder10Reviews ? 'community_says_under_10' : 'community_says';
                                        $resolvedCommunity = \App\Services\ContentTemplateService::resolveBusinessSection(
                                            $business,
                                            $commFieldKey,
                                            $lang_id
                                        );
                                        if ($hasUnder10Reviews && empty($resolvedCommunity['headline']) && empty($resolvedCommunity['subtext'])) {
                                            $resolvedCommunity = \App\Services\ContentTemplateService::resolveBusinessSection(
                                                $business,
                                                'community_says',
                                                $lang_id
                                            );
                                        }
                                        $commFeedbackHeadline = !empty($resolvedCommunity['headline'])
                                            ? $resolvedCommunity['headline']
                                            : (!empty($bTrans->community_says_headline)
                                                ? $bTrans->community_says_headline
                                                : (static_text('what_localio_community_says_headline') ?: 'Pros & cons'));

                                        $commFeedbackSubtext = !empty($resolvedCommunity['subtext'])
                                            ? $resolvedCommunity['subtext']
                                            : (!empty($bTrans->community_says_subtext)
                                                ? $bTrans->community_says_subtext
                                                : (static_text('most_mentioned_pros_cons_subtext') ?: 'Most mentioned pros and cons shared by real users.'));
                                    @endphp
                                    <h2 style="margin-bottom: 4px;">
                                        {{ $commFeedbackHeadline }}
                                    </h2>
                                    @if(!empty($commFeedbackSubtext))
                                        <style>
                                            .community-says-subtext p { margin-bottom: 0 !important; }
                                            .community-says-subtext p:not(:last-child) { margin-bottom: 10px !important; }
                                        </style>
                                        <div class="community-says-subtext ck-content" style="font-size: 15px; color: #64748b; margin: 0;">
                                            {!! $commFeedbackSubtext !!}
                                        </div>
                                    @endif
                                </div>

                                <div class="table-responsive rounded-3 border bg-white" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                    <table class="table align-middle mb-0" style="font-size: 14px; color: #1e3050;">
                                        <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                            <tr>
                                                <th scope="col" style="padding: 14px 18px; font-weight: 500; color: #002347; width: 240px; min-width: 240px; max-width: 240px;"></th>
                                                <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347; width: 140px; min-width: 140px; max-width: 140px;">{{ static_text('overall_rating_label') ?: 'Overall rating' }}</th>
                                                <th scope="col" style="padding: 14px 18px; font-weight: 500; color: #002347; width: 38%;">{{ static_text('most_mentioned_pro_label') ?: 'Most mentioned pro' }}</th>
                                                <th scope="col" style="padding: 14px 18px; font-weight: 500; color: #002347; width: 38%;">{{ static_text('most_mentioned_con_label') ?: 'Most mentioned con' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tableRowsList as $rowBiz)
                                                @if(empty($rowBiz->is_affiliate))
                                                    @continue
                                                @endif
                                                @php
                                                    $rTrans = $rowBiz->translations->firstWhere('lang_id', $lang_id) ?? $rowBiz->translations->first();
                                                    $rName = $rTrans->name ?? $rowBiz->name ?? 'Business';
                                                    $rSlug = $rTrans->slug ?? $rowBiz->slug ?? 'business-' . $rowBiz->id;
                                                    $affiliateUrl = $rowBiz->getTrackedUrl() ?? $rowBiz->affiliate_link ?? $rowBiz->permanent_url ?? route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $rSlug]);
                                                    $rRevs = ($rowBiz->relationLoaded('reviews') && $rowBiz->reviews->whereIn('status', ['published', 'active'])->isNotEmpty()) ? $rowBiz->reviews->whereIn('status', ['published', 'active']) : $rowBiz->reviews()->whereIn('status', ['published', 'active'])->get();
                                                    $rCount = $rRevs->count();
                                                    $rRating = $rCount > 0 ? round($rRevs->avg('rating'), 1) : (float)($rowBiz->admin_rating ?? 0);

                                                    if ($rCount >= 10) {
                                                        $proCons = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                                            ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                                            ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                                            ->where('reviews.business_id', $rowBiz->id)
                                                            ->whereIn('reviews.status', ['published', 'active'])
                                                            ->select('category_pro_cons.type', 'category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as cnt'))
                                                            ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text')
                                                            ->orderByDesc('cnt')
                                                            ->get();

                                                        $topProObj = $proCons->firstWhere('type', 'pro');
                                                        $topConObj = $proCons->firstWhere('type', 'con');

                                                        $proStr = $topProObj ? $topProObj->text . ' (' . $topProObj->cnt . ')' : '-';
                                                        $conStr = $topConObj ? $topConObj->text . ' (' . $topConObj->cnt . ')' : '-';
                                                    } else {
                                                        // When < 10 reviews: Check business_pro_cons first, or fallback to any review pro/con
                                                        $manPro = $rowBiz->proCons ? $rowBiz->proCons->where('type', 'pro')->sortByDesc(fn($p) => $p->weight ?? 5)->first() : null;
                                                        $manCon = $rowBiz->proCons ? $rowBiz->proCons->where('type', 'con')->sortByDesc(fn($c) => $c->weight ?? 5)->first() : null;

                                                        if ($manPro) {
                                                            $proStr = $manPro->text;
                                                        } else {
                                                            $revPro = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                                                ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                                                ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                                                ->where('reviews.business_id', $rowBiz->id)
                                                                ->whereIn('reviews.status', ['published', 'active'])
                                                                ->where('category_pro_cons.type', 'pro')
                                                                ->select('category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as cnt'))
                                                                ->groupBy('category_pro_cons.id', 'category_pro_cons.text')
                                                                ->orderByDesc('cnt')
                                                                ->first();
                                                            $proStr = $revPro ? $revPro->text . ' (' . $revPro->cnt . ')' : '-';
                                                        }

                                                        if ($manCon) {
                                                            $conStr = $manCon->text;
                                                        } else {
                                                            $revCon = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                                                ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                                                ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                                                ->where('reviews.business_id', $rowBiz->id)
                                                                ->whereIn('reviews.status', ['published', 'active'])
                                                                ->where('category_pro_cons.type', 'con')
                                                                ->select('category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as cnt'))
                                                                ->groupBy('category_pro_cons.id', 'category_pro_cons.text')
                                                                ->orderByDesc('cnt')
                                                                ->first();
                                                            $conStr = $revCon ? $revCon->text . ' (' . $revCon->cnt . ')' : '-';
                                                        }
                                                    }
                                                @endphp
                                                <tr style="border-bottom: 1px solid #f1f5f9; {{ $rowBiz->id === $business->id ? 'background-color: #f8fafc;' : '' }}">
                                                    <td style="padding: 14px 18px; font-weight: 500; color: #002347;">
                                                        <a href="{{ $affiliateUrl }}" target="_blank" rel="sponsored noopener" class="table-business-link d-flex align-items-center gap-2 text-decoration-none" style="color: inherit;">
                                                            <div class="top-product-small-logo">
                                                                <x-business-logo :business="$rowBiz" :name="$rName" />
                                                            </div>
                                                            <div class="d-flex align-items-center flex-nowrap">
                                                                <span class="table-business-name" style="color: #002347; font-weight: 500;">{{ $rName }}</span>
                                                                @if($rowBiz->id === $business->id)
                                                                    <span class="badge bg-primary ms-1" style="font-size: 10px; font-weight: 600;">{{ static_text('current_listing_badge') ?: 'Current' }}</span>
                                                                @endif
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#002347" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                            </div>
                                                        </a>
                                                    </td>
                                                    <td class="text-center fw-bold" style="padding: 14px; color: #1e3050; white-space: nowrap;">
                                                        @if($rowBiz->should_show_rating)
                                                        <span class="d-inline-flex align-items-center gap-1">
                                                            <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                                            <span>{{ formatRating($rowBiz->display_rating) }}</span>
                                                        </span>
                                                        @else
                                                            <span style="color: #5b6b7a;">-</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 14px 18px; color: #334155; font-weight: 500;">
                                                        @if($proStr !== '-')
                                                            <div class="d-flex align-items-start">
                                                                <span class="me-2" style="font-size: 16px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                                <span style="color: #202124; font-size: 14px;">{{ $proStr }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 14px 18px; color: #334155; font-weight: 500;">
                                                        @if($conStr !== '-')
                                                            <div class="d-flex align-items-start">
                                                                <span class="me-2" style="font-size: 16px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                                <span style="color: #202124; font-size: 14px;">{{ $conStr }}</span>
                                                            </div>
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
        </div>
    </section>
    @endif

    <!-- All Alternatives Listing Section -->
    <section class="top-automotive-sec top_rate_pg light py-4" id="allAlternativesSection">
        <div class="container">
            <div class="top-auto-choice">
                <div class="top-rated-heading-block" style="padding-bottom: 16px; margin-bottom: 24px;">
                    @php
                        $resolvedAllAlt = \App\Services\ContentTemplateService::resolveBusinessSection(
                            $business,
                            'all_alternatives',
                            $lang_id
                        );
                        $allAlternativesHeadline = !empty($resolvedAllAlt['headline'])
                            ? $resolvedAllAlt['headline']
                            : (!empty($bTrans->all_alternatives_headline)
                                ? $bTrans->all_alternatives_headline
                                : ('All ' . $businessName . ' alternatives'));

                        $allAlternativesSubtext = !empty($resolvedAllAlt['subtext'])
                            ? $resolvedAllAlt['subtext']
                            : ($bTrans->all_alternatives_subtext ?? '');
                    @endphp
                    <h2 class="mt-3" style="margin-bottom: {{ !empty($allAlternativesSubtext) ? '6px' : '16px' }};">
                        {{ $allAlternativesHeadline }}
                    </h2>
                    @if(!empty($allAlternativesSubtext))
                        <style>
                            .all-alternatives-subtext p { margin-bottom: 0 !important; }
                            .all-alternatives-subtext p:not(:last-child) { margin-bottom: 10px !important; }
                        </style>
                        <div class="all-alternatives-subtext ck-content mb-3" style="font-size: 15px; color: #64748b;">
                            {!! $allAlternativesSubtext !!}
                        </div>
                    @endif
                </div>

                <div class="auto-choice-row d-flex">
                            <!-- Mobile Filter Backdrop Overlay -->
                            <div class="mobile-filter-backdrop d-lg-none" onclick="closeMobileFilterDrawer()"></div>

                            <div class="auto-choice-lft">
                                <!-- Mobile Filter Drawer Header -->
                                <div class="mobile-filter-header d-flex d-lg-none justify-content-between align-items-center">
                                    <h5 class="m-0 fw-bold" style="color: #002347; font-size: 18px;">{{ static_text('filters') ?: 'Filters' }}</h5>
                                    <button type="button" class="mobile-filter-close-btn" onclick="closeMobileFilterDrawer()" aria-label="Close filters">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                                <div class="container">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                    </div>
                                    <div class="col-md-12">
                                        <!-- Rating Filter Section - Styled like the image -->
                                        <div class="filter-section">
                                            <p class="fw-semibold text-dark mb-2">
                                                {{ static_text('user_rating') }}</p>

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
                                            <p class="fw-semibold text-dark mb-3">{{ static_text('price_range') }}</p>

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
                                                    <p>
                                                        {{ $filterName }}
                                                    </p>

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

                    <!-- Mobile Filter Drawer Sticky Footer -->
                    <div class="mobile-filter-footer d-lg-none">
                        <button type="button" class="btn-show-results" onclick="closeMobileFilterDrawer()">
                            @if(isset($products) && $products->total() > 0)
                                Show {{ $products->total() }}+ results
                            @else
                                Show results
                            @endif
                        </button>
                    </div>
                    
                </div>
                @if ($products->count())
                                <div class="auto-choice-rgt ">
                                    <!-- Product Count and Sort -->
                                    @if ($products->count() > 0)
                                        @php
                                            $currentPage = $products->currentPage();
                                            $perPage = $products->perPage();
                                            $total = $products->total();
                                            $from = ($currentPage - 1) * $perPage + 1;
                                            $to = min($currentPage * $perPage, $total);
                                        @endphp
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- Mobile Filter Button (matching Image 2) -->
                                            <button type="button" class="mobile-filter-btn d-inline-flex d-lg-none" onclick="openMobileFilterDrawer()" aria-label="Open filters">
                                                <svg width="22" height="16" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;">
                                                    <path d="M2 5h10m4 0h6M12 2.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM2 13h4m4 0h12M6 10.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5z" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                @php
                                                    $activeFilterCount = count($selectedRatings ?? []) + count($selectedCategories ?? []) + count($selectedFeatures ?? []) + (!empty($priceRange) ? 1 : 0);
                                                @endphp
                                                @if($activeFilterCount > 0)
                                                    <span class="filter-badge">{{ $activeFilterCount }}</span>
                                                @endif
                                            </button>

                                            @if ($products->count() > 0)
                                                <p class="m-0 showing-p d-none d-sm-block">
                                                    Showing {{$from}}-{{$to}} of {{$total}}
                                                </p>
                                            @elseif($products->count() > 0)
                                                <p class="m-0 showing-p d-none d-sm-block">Showing {{ $products->count() }} results</p>
                                            @endif
                                        </div>
                                        <div wire:ignore class="d-none">
                                            <x-social-icon/>
                                        </div>
                                    </div>
                                    @if ($products->count() > 0)
                                        <p class="m-0 showing-p d-block d-sm-none mb-3" style="font-size: 13.5px; color: #64748b;">
                                            Showing {{$from}}-{{$to}} of {{$total}}
                                        </p>
                                    @endif
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
                                                                <span style="background-color: #f8fafc; color: #06498b; border: 1px solid #06498b; padding: 4px 12px; border-radius: 20px; ">
                                                                    <i class="far fa-star text-warning" style="margin-right: 4px; color: #06498b !important;"></i> {{ static_text('category_badge_label') ?: 'Top rated' }}
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
                                                                            @if($item->should_show_rating)
                                                                            <div class="tp-btm d-flex align-items-center" style="gap: 6px;">
                                                                                <span class="rate_box_num" style="font-size: 14px; font-weight: 500; color: #333;">{{ number_format($item->display_rating, 1) }}</span>
                                                                                <div class="inn_ul d-inline-flex m-0">
                                                                                    <div class="rating-stars ">
                                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                                            @if ($i <= floor($item->display_rating))
                                                                                                <i class="fas fa-star text-warning"></i>
                                                                                            @elseif ($i - 0.5 <= $item->display_rating)
                                                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                                                            @else
                                                                                                <i class="far fa-star text-warning"></i>
                                                                                            @endif
                                                                                        @endfor
                                                                                    </div>
                                                                                </div>
                                                                                @if($item->should_show_review_count)
                                                                                <span class="rate_box_count text-muted" style="font-size: 14px;">({{ $item->approved_reviews_count }})</span>
                                                                                @endif
                                                                            </div>
                                                                            @endif
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
                                                                @if (!empty($item->is_affiliate))
                                                                <div class="slider_content_sec my-3" style="width: 100% !important; max-width: 100% !important;">
                                                                    <div class="main_feature_lg">
                                                                        <div class="feture_box lft_check_box size18" style="border: none; padding: 0; background: transparent; min-height: auto; width: 100% !important; max-width: 100% !important;">
                                                                            <div class="usp-grid-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                                                                @if ($item->usps->count() > 0)
                                                                                    @foreach ($item->usps->take(4) as $usp)
                                                                                        <div class="d-flex align-items-center size18">
                                                                                            <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                                <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                            </div>
                                                                                            <p class="m-0" style="font-size: 14px !important; font-weight:500 !important; color: #002347; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $usp->text }}</p>
                                                                                        </div>
                                                                                    @endforeach
                                                                                @else
                                                                                    <div class="d-flex align-items-center size18">
                                                                                        <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                            <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                        </div>
                                                                                        <p class="m-0" style="font-size: 14px !important; font-weight:500 !important; color: #002347; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Free domain & SSL certificate</p>
                                                                                    </div>
                                                                                    <div class="d-flex align-items-center size18">
                                                                                        <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                            <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                        </div>
                                                                                        <p class="m-0" style="font-size: 14px !important; font-weight:500 !important;  color: #002347; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Customizable automatic updates</p>
                                                                                    </div>
                                                                                    <div class="d-flex align-items-center size18">
                                                                                        <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                            <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                        </div>
                                                                                        <p class="m-0" style="font-size: 14px !important; font-weight:500 !important;  color: #002347; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Scalable performance management</p>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif

                                                                <!-- Compare Checkbox -->
                                                            </div>

                                                            <!-- Right Column -->
                                                            <div  class="rgt_rgt_bx" style="  flex: 0 0 250px; min-width: 250px; display: flex; flex-direction: column; justify-content: space-between; align-items: stretch; margin-top: 10px;">
                                                                <!-- Buttons -->
                                                                <div class="auto-choice-btn d-flex flex-column gap-2" style="width: 100%; margin: 0;">
                                                                    @if(!empty($item->is_affiliate))
                                                                    <a href="{{ $item->getEffectiveWebsiteUrl() ?: ($item->affiliate_link ?: $item->permanent_url) }}"
                                                                        class="cta cta_orange justify-content-center"
                                                                        target="_blank" rel="sponsored" style="display: flex !important; width: 100%; align-items: center;">
                                                                        {{ static_text('visit_website') ?: 'Visit website' }}
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                                    </a>
                                                                    @endif
                                                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}"
                                                                        class="cta cta_outline justify-content-center" style="display: flex !important; width: 100%; align-items: center; border: 1px solid #06498b; color: #06498b;">
                                                                        {{ static_text('profile_and_reviews') ?: 'Profile & reviews' }}
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
                                                            @if ($startingPrice && !empty($item->is_affiliate))
                                                                    <div class="text-center mt-4 w-100"
                                                                        style="padding: 15px 25px; border-radius: 8px;">

                                                                        <h6
                                                                            style="font-size: 13px; color: #002347; font-weight: 600; margin-bottom: 4px;">
                                                                            Starting price
                                                                        </h6>

                                                                        <h3
                                                                            style="font-weight: 700 !important; color: #002347; font-size: 26px !important; margin-bottom: 2px;">
                                                                            {{ $startingPrice->currency }}{{ number_format($displayPrice, 2) }}
                                                                        </h3>

                                                                        @if (
                                                                            !empty($startingPrice->additional_info) &&
                                                                            !in_array(
                                                                                strtoupper(trim((string) $startingPrice->additional_info)),
                                                                                ['NA', 'N/A', 'NULL']
                                                                            )
                                                                        )
                                                                            <p style="font-size: 11px; margin-bottom: 0;">
                                                                                {{ $startingPrice->additional_info }}
                                                                            </p>
                                                                        @elseif(!empty($startingPrice->time_unit) && !in_array(strtolower($startingPrice->time_unit), ['one_time', 'na', 'n/a', 'null']))
                                                                            <p style="font-size: 11px; margin-bottom: 0;">
                                                                                per {{ strtolower($startingPrice->time_unit) }}
                                                                            </p>
                                                                        @endif

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
                                    @if($products->count() > 0)
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <p class="m-0" style="font-size: 13px; color:#5b6b7a;">Showing {{ $products->count() }} results </p>
                                        </div>
                                        <x-social-icon/>
                                    </div>
                                    @endif

                                    <div class="alert alert-info py-4 px-3" style="background-color:#f7f9fb; border: 1px solid #e2e8f0; border-radius: 8px;">
                                        @if ($this->hasActiveFilters())
                                            <h4 class="mb-2 text-center fw-bold" style="color: #1e3050; font-size: 20px;">{{ static_text('no_matches_found') ?: 'No matches found' }}</h4>
                                            <p class="m-0 text-center text-secondary" style="font-size: 15px;">Try adjusting or removing some filters to see more options.</p>
                                        @else
                                            <h4 class="m-0 text-center fw-bold" style="color: #1e3050; font-size: 20px;">{{ static_text('no_matches_found') ?: 'No matches found' }}</h4>
                                        @endif
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
                                                {{ static_text('no_prod_mach_fil') ?: 'No products found matching your selected filters.' }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div> --}}
                            @endif
                </div>
            </div>
        </div>
        <livewire:compare-bar />
    </section>

    <!-- Top 10 Businesses with Details (Exact design matching category page) -->
    @php
        $detailedBusinesses = (isset($topAffiliatedBusinesses) && count($topAffiliatedBusinesses) > 0 
            ? $topAffiliatedBusinesses 
            : ($affiliatedTableBusinesses ?? collect()))
            ->filter(fn($b) => (int)($b->is_affiliate ?? 0) === 1)
            ->values();
    @endphp
    @if(isset($detailedBusinesses) && count($detailedBusinesses) > 0)
    <section class="revie_img_sec border_bt bg_new_grey">
        <div class="container">
            <div class="mb-4">
                @foreach($detailedBusinesses as $index => $biz)
                @if(empty($biz->is_affiliate))
                    @continue
                @endif
                @php
                    $curLangId = getCurrentLanguageID();
                    $bizTrans = $biz->translations->firstWhere('lang_id', $curLangId) ?? ($biz->translations->firstWhere('language_id', $curLangId) ?? $biz->translations->first());
                    $bizName = $bizTrans->name ?? $biz->name ?? '';
                    $bizSlug = $bizTrans->slug ?? $biz->slug ?? (string)$biz->id;
                    $longDescription = $bizTrans->long_preview_description ?? '';
                    $hasBizTrackedUrl = !empty($biz->getTrackedUrl());
                    $affiliateUrl = $hasBizTrackedUrl ? $biz->getTrackedUrl() : route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bizSlug]);
                    
                    // Fetch criteria ratings
                    $bizCatForCrit = ($biz->category && $biz->category->parent_id && $biz->category->parent && $biz->category->parent->ratingCriteria->isNotEmpty())
                        ? $biz->category->parent
                        : $biz->category;
                    $criteriaRatings = [];
                    foreach($bizCatForCrit->ratingCriteria ?? [] as $criterion) {
                        $avg = $biz->reviews()->join('review_ratings', 'reviews.id', '=', 'review_ratings.review_id')->where('review_ratings.criteria_id', $criterion->id)->avg('review_ratings.rating');
                        if ($avg !== null) {
                            $criteriaRatings[$criterion->name] = round($avg, 1);
                        }
                    }
                    $recommendPercent = $biz->reviews()->count() > 0 ? round(($biz->reviews()->where('recommend', 1)->count() / $biz->reviews()->count()) * 100) : 0;
                @endphp
                
                <div class="image_revie_inr m-30 {{ !$loop->last ? 'border-bottom border-gray-200' : '' }}">
                    <!-- Left Column: Description, Image, Pros/Cons -->
                    <div class="is-asana-wrp imges_left_sde" data-aos="fade-up" data-aos-duration="1000">
                        <div class="row sld_rw">
                            <h2 style="font-weight: 700 !important; margin-bottom: 24px;">
                                #{{ $index + 1 }} {{ $bizName }}
                            </h2>
                        
                            @if(!empty($longDescription))
                            <div class="rich-text-content mb-4" style="font-size: 16px; color: var(--color-dark-blue-text, #002347); line-height: 1.6;">
                                {!! $longDescription !!}
                            </div>
                            @endif
                            
                            @php
                                $sliderImages = is_array($biz->business_images) ? $biz->business_images : json_decode($biz->business_images ?? '[]', true);
                                if (empty($sliderImages)) {
                                    $sliderImages = is_array($biz->screenshot_urls) ? $biz->screenshot_urls : json_decode($biz->screenshot_urls ?? '[]', true);
                                }
                                $sliderImages = array_values(array_filter((array) $sliderImages));
                            @endphp
                            @if(!empty($sliderImages) && count($sliderImages) > 0)
                            <div class="business-preview-images mb-5">
                                @php
                                    $firstImg = \Illuminate\Support\Str::startsWith($sliderImages[0], ['http://', 'https://']) ? $sliderImages[0] : asset($sliderImages[0]);
                                @endphp
                                <div class="business-main-preview mb-3">
                                    <a href="{{ $affiliateUrl }}" target="_blank" rel="sponsored" class="d-block border rounded-3 overflow-hidden" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                        <img src="{{ $firstImg }}" alt="{{ $bizName }}" id="main-preview-img-{{ $biz->id }}" class="img-fluid w-100 preview-main-display">
                                    </a>
                                </div>
                                @if(count($sliderImages) > 1)
                                <div class="business-preview-thumbnails d-flex flex-wrap align-items-center justify-content-start">
                                    @foreach($sliderImages as $thumbIndex => $thumbImg)
                                        @php
                                            $tSrc = \Illuminate\Support\Str::startsWith($thumbImg, ['http://', 'https://']) ? $thumbImg : asset($thumbImg);
                                        @endphp
                                        <div class="preview-thumb-item {{ $thumbIndex === 0 ? 'active' : '' }}"
                                             onmouseenter="switchCategoryPreview('{{ $biz->id }}', '{{ $tSrc }}', this)"
                                             onclick="switchCategoryPreview('{{ $biz->id }}', '{{ $tSrc }}', this)">
                                            <img src="{{ $tSrc }}" alt="{{ $bizName }} preview {{ $thumbIndex + 1 }}" class="preview-thumb-img">
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Pros and Cons Section -->
                            @php
                                $bizReviews = $biz->reviews ? $biz->reviews->whereIn('status', ['published', 'active']) : collect();
                                $bizRevCount = $bizReviews->count();
                                $topPros = collect();
                                $topCons = collect();

                                if ($bizRevCount >= 10) {
                                    $aggregatedProCons = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                    ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                    ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                    ->where('reviews.business_id', $biz->id)
                                    ->whereIn('reviews.status', ['published', 'active'])
                                    ->select('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as reviews_count'))
                                    ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text')
                                    ->orderByDesc('reviews_count')
                                    ->get();

                                $topPros = $aggregatedProCons->where('type', 'pro')->values()->take(5);
                                $topCons = $aggregatedProCons->where('type', 'con')->values()->take(5);

                                if ($topPros->isEmpty() && $topCons->isEmpty() && $biz->proCons && $biz->proCons->isNotEmpty()) {
                                    $topPros = $biz->proCons->where('type', 'pro')->map(function($item) {
                                        return (object)[
                                            'id'            => $item->id,
                                            'type'          => 'pro',
                                            'text'          => $item->text,
                                            'reviews_count' => null,
                                        ];
                                    })->values()->take(5);

                                    $topCons = $biz->proCons->where('type', 'con')->map(function($item) {
                                        return (object)[
                                            'id'            => $item->id,
                                            'type'          => 'con',
                                            'text'          => $item->text,
                                            'reviews_count' => null,
                                        ];
                                    })->values()->take(5);
                                }
                            } else {
                                // When < 10 reviews: ONLY show manually added pros and cons (from business_pro_cons) ordered by weight DESC
                                if ($biz->proCons && $biz->proCons->isNotEmpty()) {
                                    $biz->loadMissing(['proCons.categoryProCon']);
                                    $topPros = $biz->proCons->where('type', 'pro')->sortByDesc(fn($item) => $item->weight ?? 5)->map(function($item) {
                                        return (object)[
                                            'id'            => $item->id,
                                            'type'          => 'pro',
                                            'text'          => $item->text,
                                            'description'   => !empty($item->description) ? $item->description : optional($item->categoryProCon)->description,
                                            'weight'        => $item->weight ?? 5,
                                            'reviews_count' => null,
                                        ];
                                    })->values()->take(5);

                                    $topCons = $biz->proCons->where('type', 'con')->sortByDesc(fn($item) => $item->weight ?? 5)->map(function($item) {
                                        return (object)[
                                            'id'            => $item->id,
                                            'type'          => 'con',
                                            'text'          => $item->text,
                                            'description'   => !empty($item->description) ? $item->description : optional($item->categoryProCon)->description,
                                            'weight'        => $item->weight ?? 5,
                                            'reviews_count' => null,
                                        ];
                                    })->values()->take(5);
                                }
                            }
                            @endphp
                            @if($topPros->isNotEmpty() && $topCons->isNotEmpty())
                            @php
                                $bizUnder10 = ($bizRevCount < 10);
                                $bizProsConsKey = $bizUnder10 ? 'profile_pros_cons_under_10' : 'profile_pros_cons';
                                $resolvedBizCardProsCons = \App\Services\ContentTemplateService::resolveBusinessSection(
                                    $biz,
                                    $bizProsConsKey,
                                    $curLangId
                                );
                                if ($bizUnder10 && empty($resolvedBizCardProsCons['headline']) && empty($resolvedBizCardProsCons['subtext'])) {
                                    $resolvedBizCardProsCons = \App\Services\ContentTemplateService::resolveBusinessSection(
                                        $biz,
                                        'community_says_under_10',
                                        $curLangId
                                    );
                                }
                                $bizCardProsConsHeadline = !empty($resolvedBizCardProsCons['headline'])
                                    ? $resolvedBizCardProsCons['headline']
                                    : ($bizName . ' pros and cons');
                                $bizCardProsConsSubtext = !empty($resolvedBizCardProsCons['subtext'])
                                    ? $resolvedBizCardProsCons['subtext']
                                    : null;
                            @endphp
                            <div class="col-lg-12 mt-2 mb-4 pros-cons-wrapper mt-4">
                                <div class="pros-cons-header mb-3">
                                    <h3 style="margin-bottom: {{ !empty($bizCardProsConsSubtext) ? '6px' : '8px' }}; font-weight: 600; color: #1e3050; font-size: 20px;">
                                        {!! \App\Services\ContentTemplateService::cleanHeading($bizCardProsConsHeadline) !!}
                                    </h3>
                                    @if(!empty($bizCardProsConsSubtext))
                                        <div class="pros-cons-subtext ck-content" style="font-size: 14.5px; color: #64748b; margin-bottom: 16px; line-height: 1.6;">
                                            {!! $bizCardProsConsSubtext !!}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="row g-4 d-flex">
                                    <div class="col-md-6">
                                        <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                            <div class="card-body">
                                                <p class="card-titles mb-3" style="font-weight: 500 !important; font-size:16px !important;">Pros</p>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($topPros as $pro)
                                                    <li class="d-flex align-items-start mb-2">
                                                        <span class="me-2" style="font-size: 18px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                        <span style="color: #202124;">{{ $pro->text }}
                                                            @if(!empty($pro->reviews_count))
                                                            <small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $pro->reviews_count }} {{ $pro->reviews_count == 1 ? 'review' : 'reviews' }}</small>
                                                            @endif
                                                        </span>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                            <div class="card-body">
                                                <p class="card-titles mb-3" style="font-weight: 500 !important; font-size:16px !important;">Cons</p>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($topCons as $con)
                                                    <li class="d-flex align-items-start mb-2">
                                                        <span class="me-2" style="font-size: 18px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                        <span style="color: #202124;">{{ $con->text }}
                                                            @if(!empty($con->reviews_count))
                                                            <small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $con->reviews_count }} {{ $con->reviews_count == 1 ? 'review' : 'reviews' }}</small>
                                                            @endif
                                                        </span>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right Column: Stats and Sidebar (Matching Product Detail Design) -->
                    <div class="thre_revi_rgt">
                        <div class="main_feture">
                            <div class="fetru_row d-flex justify-content-between">

                                <!-- Review Breakdown Card -->
                                <x-review-breakdown-card :business="$biz" />

                                <!-- Features / USPs Checklist -->
                                @if($biz->usps->count() > 0)
                                <div class="main_feature_lg">
                                    <div class="feture_box lft_check_box size15">
                                        <ul class="list-unstyled">
                                            @foreach ($biz->usps->take(5) as $usp)
                                                <li class="d-flex flex-row align-items-center size15">
                                                    <div class="grn_chk">
                                                        <img src="{{ asset('front/img/green-tick.svg') }}">
                                                    </div>
                                                    <p class="m-0">{{ $usp->text }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif

                                <!-- More about box -->
                                <x-more-about-business :business="$biz" current="alternatives" />
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Discussions Section (From business details page) -->
    <section class="business-discussions-sec py-5" id="sectionDiscussions">
        <div class="container">
        @php
            $totalDiscsCount = $business->discussions()->count();
            $resolvedBizDiscussions = \App\Services\ContentTemplateService::resolveBusinessSection(
                $business,
                'profile_discussions',
                getCurrentLanguageID(),
                ['[count]' => number_format($totalDiscsCount)]
            );
            $finalBizDiscHeadline = null;
            $finalBizDiscSubtext = null;
            if (!empty($resolvedBizDiscussions['headline'])) {
                $finalBizDiscHeadline = $resolvedBizDiscussions['headline'];
                if (!str_contains($finalBizDiscHeadline, '(') && !str_contains($finalBizDiscHeadline, (string)$totalDiscsCount)) {
                    $finalBizDiscHeadline .= ' (' . number_format($totalDiscsCount) . ')';
                }
            }
            if (!empty($resolvedBizDiscussions['subtext'])) {
                $finalBizDiscSubtext = $resolvedBizDiscussions['subtext'];
            }
        @endphp
        <x-community-discussion :business="$business" :headline="$finalBizDiscHeadline" :subtext="$finalBizDiscSubtext" />
        </div>
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


    <script>
        function switchCategoryPreview(bizId, imgSrc, thumbElement) {
            var mainImg = document.getElementById('main-preview-img-' + bizId);
            if (mainImg && mainImg.src !== imgSrc) {
                mainImg.src = imgSrc;
            }
            if (thumbElement && thumbElement.parentElement) {
                var siblings = thumbElement.parentElement.querySelectorAll('.preview-thumb-item');
                siblings.forEach(function(el) {
                    el.classList.remove('active');
                });
                thumbElement.classList.add('active');
            }
        }
    </script>
</div>
