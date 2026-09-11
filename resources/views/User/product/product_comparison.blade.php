@extends('user_layout.master')
@php
    $bizNames = isset($businesses) && is_iterable($businesses) ? collect($businesses)->map(function($b) {
        $trans = $b->translations ?? collect();
        $t = is_iterable($trans) ? collect($trans)->firstWhere('lang_id', getCurrentLanguageID()) : null;
        if (!$t && is_iterable($trans)) {
            $t = collect($trans)->first();
        }
        return $t->name ?? ($b->name ?? '');
    })->filter()->implode(' vs ') : '';
    $compPageTitle = !empty($bizNames) ? "{$bizNames} Comparison | Localio" : 'Product Comparison | Localio';
    $compPageDesc = !empty($bizNames) ? "Compare {$bizNames} side-by-side on pricing, features, user reviews, pros, and cons on Localio." : 'Compare products side-by-side on Localio.';
@endphp
@section('meta_title', format_meta_text($compPageTitle))
@section('meta_description', format_meta_text($compPageDesc))
@section('content')
@php
    $firstBiz = isset($businesses[0]) ? $businesses[0] : null;
    $parentCat = $firstBiz && $firstBiz->category && $firstBiz->category->parent ? $firstBiz->category->parent : null;
    $cat = $firstBiz && $firstBiz->category ? $firstBiz->category : null;

    $parentCatName = $parentCat?->translation?->name ?? $parentCat?->translations?->first()?->name ?? null;
    $parentCatSlug = $parentCat?->translation?->slug ?? $parentCat?->translations?->first()?->slug ?? null;

    $catName = $cat?->translation?->name ?? $cat?->translations?->first()?->name ?? null;
    $catSlug = $cat?->translation?->slug ?? $cat?->translations?->first()?->slug ?? null;

    $hasAffiliated = $businesses->contains(fn($b) => !empty($b->is_affiliate));
@endphp
<section class=" help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 comparsn_bnr_sec" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4" style="background-color: #f7f9fb;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom: 0;">
                    @if($parentCatName)
                        <li class="breadcrumb-item">
                            @if($parentCatSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentCatSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $parentCatName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $parentCatName }}</span>
                            @endif
                        </li>
                    @endif
                    @if($catName)
                        <li class="breadcrumb-item">
                            @if($catSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $catName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $catName }}</span>
                            @endif
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" style="color: #002347; font-weight: 500;">
                        @if (count($businesses) >= 2)
                            {{ $businesses[0]->translations->first()?->name ?? '' }} vs {{ $businesses[1]->translations->first()?->name ?? '' }}
                        @elseif (count($businesses) == 1)
                            {{ $businesses[0]->translations->first()?->name ?? '' }}
                        @else
                            Businesses Comparison
                        @endif
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="comparison-header-left">
                <div class="top_head">
                    <h1 style="font-size: 28px !important; color: #002347; margin: 0;">
                        @if (count($businesses) >= 2)
                            {{ $businesses->map(fn($b) => $b->translations->first()?->name ?? $b->name ?? '')->filter()->implode(' vs ') }}: Comparison
                        @elseif (count($businesses) == 1)
                            {{ $businesses[0]->translations->first()?->name ?? '' }}: Comparison
                        @else
                            Businesses Comparison
                        @endif
                    </h1>
                    <p class="text-meta">Last updated on {{ now()->format('F j, Y') }}</p>
                    <p style="margin-bottom: 0; ">
                        @if(!empty($catName))
                            Learn more from our team about {{ $catName }} pricing, features, and user ratings in our side-by-side comparison guide.
                        @else
                            Learn more from our team about top software and services pricing, features, and user ratings in our side-by-side comparison guide.
                        @endif
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-6 mt-4 mt-lg-0">
                <div class="comparison-header-icons">
                    @foreach($businesses as $b)
                        <div class="comp-header-icon-box" title="{{ $b->translations->first()?->name ?? '' }}">
                            <x-business-logo :business="$b" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- section product comparison -->
<section class="lts-wht-bg product_comp_sec p_120 light new-white-bg" style="padding-top: 30px;">
    <div class="container">
        <!-- Your comparison top bar (Capterra style in Localio design) -->
        <div class="your-comparison-wrapper position-relative">
            <div class="your-comp-pills-wrap">
                <span class="your-comp-title">Your comparison</span>
                @foreach($businesses as $biz)
                    @php
                        $bTitle = $biz->translations->first()?->name ?? $biz->name ?? 'Business';
                    @endphp
                    @if(count($businesses) > 1)
                    <a href="javascript:void(0);" class="your-comp-pill remove-from-comparison" 
                        data-url="{{ route('remove-from-comparison', ['locale' => app()->getLocale(), 'productId' => $biz->id]) }}"
                        data-product-id="{{ $biz->id }}"
                        title="Remove {{ $bTitle }}">
                        <div class="your-comp-pill-logo">
                            <x-business-logo :business="$biz" />
                        </div>
                        <span>{{ $bTitle }}</span>
                        <span class="your-comp-pill-remove">
                            <i class="fas fa-times"></i>
                        </span>
                    </a>
                    @else
                    <div class="your-comp-pill" title="{{ $bTitle }}">
                        <div class="your-comp-pill-logo">
                            <x-business-logo :business="$biz" />
                        </div>
                        <span>{{ $bTitle }}</span>
                    </div>
                    @endif
                @endforeach
                <span class="your-comp-count d-none ">{{ count($businesses) }} of 4 added </span>
            </div>

            @if(count($businesses) < 4)
            <div class="position-relative">
                <button type="button" class="add-products-btn" id="toggleAddProductBtn">
                    Add more <i class="fas fa-plus ms-1"></i>
                </button>
                <div class="add-comp-dropdown" id="addCompDropdown">
                    <div class="add-comp-search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" class="add-comp-search-input" id="addCompSearchInput" placeholder="Search product to add..." autocomplete="off">
                    </div>
                    <div class="add-comp-items-list" id="addCompItemsList">
                        @if(isset($availableCategoryBusinesses) && count($availableCategoryBusinesses) > 0)
                            @php $unselectedCount = 0; @endphp
                            @foreach($availableCategoryBusinesses as $avail)
                                @if(!$businesses->pluck('id')->contains($avail['id']))
                                    @php $unselectedCount++; @endphp
                                    <div class="add-comp-item-row" data-id="{{ $avail['id'] }}" data-name="{{ strtolower($avail['name']) }}">
                                        <div class="item-left">
                                            @if(!empty($avail['icon']))
                                                <img src="{{ asset($avail['icon']) }}" class="item-logo" alt="{{ $avail['name'] }}">
                                            @else
                                                <div class="item-logo" style="background:#002347;color:#fff;font-size:13px;font-weight:bold;">
                                                    {{ strtoupper(substr($avail['name'], 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="item-name" title="{{ $avail['name'] }}">{{ $avail['name'] }}</span>
                                        </div>
                                        <span class="item-add-icon">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                            @if($unselectedCount === 0)
                                <div class="p-3 text-muted small text-center">All products in this category are already added</div>
                            @endif
                        @else
                            <div class="p-3 text-muted small text-center">No other products found in this category</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        

            <div class="row justify-content-center pro-row-gp versus-row" data-aos="fade-up" data-aos-duration="1000">
                <div class="col-12">
                    <div class="asn_tprw">
                        <div class="pdc_box pdc_box_count_{{ count($businesses) }} bg-white rounded-4 border shadow-sm p-4 p-md-5" style="border-radius: 20px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;">
                            @foreach ($businesses as $index => $business)
                                @if ($index > 0)
                                    <div class="versus-box">
                                        <p class="d-flex m-0">vs</p>
                                    </div>
                                @endif
                                <div class="pdc_choice text-center d-flex flex-column align-items-center">
                                    <div class="auto-choice-hd w-100 d-flex flex-column align-items-center" style="border: none; padding: 0; margin-bottom: 0;">
                                        <div class="inn_sl_hed w-100">
                                            <div class=" top-product-logo">
                                                <x-business-logo :business="$business" />
                                            </div>
                                            <div class="sl_h">
                                                <div class="inn_h" style="margin-bottom:0;">
                                                    <h6 class="head">{{ $business->translations->first()?->name ?? '' }}</h6>
                                                </div>
                                                <div class="rating-group">
                                                    @php
                                                        $rating = round($business->reviews->avg('rating'), 1);
                                                        $ratingCount = $business->reviews->count();
                                                    @endphp

                                                    <span style="">{{ $ratingCount > 0 ? number_format($rating, 1) : '0.0' }}</span>
                                                    <div class="d-flex align-items-center">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($rating >= $i)
                                                                <i class="r-star fas fa-star text-warning" style=""></i>
                                                            @elseif ($rating >= $i - 0.5)
                                                                <i class="r-star fas fa-star-half-alt text-warning" style=""></i>
                                                            @else
                                                                <i class="r-star far fa-star text-warning" style=";"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span style="">({{ $ratingCount }})</span>
                                                </div>
                                                

                                            </div>
                                            <div class="auto-choice-btn fit-btn d-flex align-items-center gap-2" style="margin-top: 0; flex-wrap: wrap;">
                                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $business->translations->first()?->slug ?? $business->slug]) }}"
                                                    class="cta cta_outline d-flex align-items-center justify-content-center"
                                                    style="white-space: nowrap;">
                                                    {{ static_text('profile_and_reviews') ?: 'Profile & reviews' }}
                                                </a>
                                                @if(!empty($business->is_affiliate))
                                                <a href="{{ $business->getTrackedUrl() ?? $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}"
                                                    target="_blank"
                                                    class="btn-orng  cta cta_orange  d-flex align-items-center justify-content-center"
                                                    style="white-space: nowrap;">
                                                    {{ static_text('visit_website') ?: 'Visit website' }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                </a>
                                                @endif
                                            </div>
                                        </div>


                                        <!-- Thin horizontal line -->
                                        <hr class="w-100 my-3" style="border-top: 1px solid #e2e8f0; opacity: 1; margin-left: 0; margin-right: 0;">

                                        <!-- Review Breakdown inside card -->
                                        @php
                                            $activeReviews = $business->reviews->where('status', 'active');
                                            $activeReviewIds = $activeReviews->pluck('id')->toArray();
                                            $criteria = $business->category ? $business->category->getEffectiveRatingCriteria() : collect();

                                            $activeReviewsCount = $activeReviews->count();
                                            if ($activeReviewsCount > 0) {
                                                $recommendCount = $activeReviews->where('recommend', 1)->count();
                                                $recPercent = round(($recommendCount / $activeReviewsCount) * 100);
                                            } else {
                                                $recPercent = $ratingCount > 0 && $rating > 0 ? round(($rating / 5) * 100) : 100;
                                            }
                                        @endphp
                                        <div class="review-breakdown-card w-100 text-start d-flex flex-column" style="flex: 1 1 auto;">
                                            <p style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 14px;">Detailed user ratings </p>
                                            <ul class="list-unstyled mb-0 d-flex flex-column review-criteria-list" style="gap: 10px; flex: 1 1 auto;">
                                                @forelse($criteria as $criterion)
                                                    @php
                                                        $matchingCriteriaIds = \App\Models\CategoryRatingCriteria::where('id', $criterion->id)
                                                            ->orWhere(function ($q) use ($criterion) {
                                                                if ($criterion->default_key) {
                                                                    $q->where('default_key', $criterion->default_key);
                                                                }
                                                                $q->orWhereRaw('LOWER(name) = ?', [strtolower($criterion->name)]);
                                                            })
                                                            ->pluck('id')
                                                            ->toArray();

                                                        $ratingRecords = \App\Models\ReviewRating::whereIn('review_id', $activeReviewIds)
                                                            ->whereIn('criteria_id', $matchingCriteriaIds)
                                                            ->get();

                                                        $totalScore = 0;
                                                        $count = 0;
                                                        if ($ratingRecords->isNotEmpty()) {
                                                            $totalScore = $ratingRecords->sum('rating');
                                                            $count = $ratingRecords->count();
                                                        } else {
                                                            foreach ($activeReviews as $review) {
                                                                $legacyVal = null;
                                                                $cName = strtolower($criterion->name);
                                                                if ($cName === 'ease of use') {
                                                                    $legacyVal = $review->ease_of_use_rating;
                                                                } elseif ($cName === 'customer service') {
                                                                    $legacyVal = $review->customer_service_rating;
                                                                } elseif ($cName === 'features') {
                                                                    $legacyVal = $review->exclusive_service_rating;
                                                                } elseif ($cName === 'value for money') {
                                                                    $legacyVal = $review->value_for_money_rating;
                                                                }
                                                                if (!is_null($legacyVal) && $legacyVal > 0) {
                                                                    $totalScore += $legacyVal;
                                                                    $count++;
                                                                }
                                                            }
                                                        }
                                                        $crAvg = $count > 0 ? round($totalScore / $count, 1) : 0;
                                                        $crPercent = min(100, max(0, $crAvg * 20));
                                                    @endphp
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <span style="font-size: 12px; font-weight: 500; color: #002347; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;" title="{{ $criterion->name }}">{{ $criterion->name }}</span>
                                                        <div class="d-flex align-items-center gap-2" style="flex: 1; max-width: 140px; margin-left: 10px; justify-content: flex-end;">
                                                            <div class="progress w-100" style="height: 8px; background-color: #f1f5f9; border-radius: 10px;">
                                                                <div class="progress-bar" style="height: 8px; width: {{ $crPercent }}%; background-color: #007bff; border-radius: 10px;"></div>
                                                            </div>
                                                            <span style="font-size: 12px; font-weight: 600; color: #002347; min-width: 28px; text-align: right;">{{ number_format($crAvg, 1) }}</span>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="d-flex justify-content-between align-items-center text-muted small">
                                                        <span>No criteria available</span>
                                                    </li>
                                                @endforelse
                                            </ul>

                                            <div class="d-flex justify-content-between align-items-center  recommended-users-row">
                                                <span style="font-size: 14px; font-weight: 600; color: #002347;">Recommended by users</span>
                                                <span style="font-size: 14px; font-weight: 600; color: #002347;">{{ $recPercent }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <a href="javascript:void(0);" class="remove-from-comparison"
                                        data-url="{{ route('remove-from-comparison', ['locale' => app()->getLocale(), 'productId' => $business->id]) }}">
                                        <span class="ct_icn"><i class="fa-solid fa-xmark"></i></span>
                                    </a> -->
                                </div>
                            @endforeach
                            @if (count($businesses) < 2)
                                <div class="versus-box" style="visibility: hidden;">
                                    <p class="d-flex m-0">vs</p>
                                </div>
                                <div class="pdc_choice text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 100%;">
                                    <a href="javascript:void(0);" id="addFromCompareCardBtn" class="pdc_ryt" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; text-decoration: none; min-height: 320px; border: 2px dashed #cbd5e1; border-radius: 16px; background: #f8fafc; transition: all 0.2s ease; cursor: pointer;">
                                        <div class="ad_lnk" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
                                            <img src="{{ asset('front/img/pls-add.svg') }}" style="width: 44px; height: 44px;">
                                            <span style="font-size: 15px; font-weight: 600; color: #06498b;"> Add to compare </span>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
                    <!-- USPs or Company Description Card for each business -->
                    <div class="row pro-row-gp mt-4" data-aos="fade-up" data-aos-duration="1000">
                        @foreach ($businesses as $business)
                            @php
                                $bName = $business->translations->first()?->name ?? 'Product';
                                $bDesc = $business->translations->first()?->description ?? null;
                                $hasUsps = !empty($business->is_affiliate) && (($business->usps && $business->usps->count() > 0) || ($business->features && $business->features->count() > 0));
                            @endphp
                            <div class="col-lg-{{ count($businesses) == 4 ? 3 : (count($businesses) == 3 ? 4 : 6) }} col-md-6 mb-3">
                                <div class="bg-white p-4" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); height: 100%; display: flex; flex-direction: column;">
                                    @if($hasUsps)
                                        <ul class="list-unstyled mb-0 d-flex flex-column" style="gap: 14px;">
                                            @if($business->usps && $business->usps->count() > 0)
                                                @foreach($business->usps as $usp)
                                                    @php $uText = $usp->text ?? $usp->usp_text ?? ''; @endphp
                                                    @if(!empty($uText))
                                                    <li class="d-flex align-items-center" style="gap: 12px;">
                                                        <span class="d-inline-flex align-items-center justify-content-center" style="flex-shrink: 0; width: 22px;">
                                                            <img src="{{ asset('front/img/pros-tick.svg') }}" alt="Tick" style="width: 20px; height: 15px; object-fit: contain;">
                                                        </span>
                                                        <span style="font-size: 15px; font-weight: 500; color: #002347; line-height: 1.3;">{{ $uText }}</span>
                                                    </li>
                                                    @endif
                                                @endforeach
                                            @elseif($business->features && $business->features->count() > 0)
                                                @foreach($business->features as $feature)
                                                    <li class="d-flex align-items-center" style="gap: 12px;">
                                                        <span class="d-inline-flex align-items-center justify-content-center" style="flex-shrink: 0; width: 22px;">
                                                            <img src="{{ asset('front/img/pros-tick.svg') }}" alt="Tick" style="width: 20px; height: 15px; object-fit: contain;">
                                                        </span>
                                                        <span style="font-size: 15px; font-weight: 500; color: #000; line-height: 1.3;">{{ $feature->translations->first()?->name ?? 'Feature Name' }}</span>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    @else
                                        <div>
                                            <h6 style="font-size: 16px; font-weight: 700;  margin-bottom: 12px;">About {{ $bName }}</h6>
                                            <div style="font-size: 14px;">
                                                @if(!empty($bDesc))
                                                    {!! $bDesc !!}
                                                @else
                                                    <p class="text-muted mb-0">No description available.</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pros and Cons Section for Each Business in Comparison Grid -->
                    @php
                        $businessesWithProCons = collect();
                        foreach ($businesses as $b) {
                            $proCons = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                ->where('reviews.business_id', $b->id)
                                ->where('reviews.status', 'active')
                                ->select('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as review_count'))
                                ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text')
                                ->orderByDesc('review_count')
                                ->get();

                            $pros = $proCons->where('type', 'pro')->values();
                            $cons = $proCons->where('type', 'con')->values();

                            if ($pros->count() > 0 || $cons->count() > 0) {
                                $businessesWithProCons->put($b->id, [
                                    'business' => $b,
                                    'pros' => $pros,
                                    'cons' => $cons,
                                ]);
                            }
                        }
                    @endphp

                    @if($businessesWithProCons->isNotEmpty())
                    <div class="pros_cons_comp_sec my-4" data-aos="fade-up" data-aos-duration="1000">
                        <div class="row g-4">
                            @foreach ($businesses as $index => $business)
                                @php
                                    $bProCons = $businessesWithProCons->get($business->id);
                                    $bName = $business->translations->first()?->name ?? 'Business';
                                    $bPros = $bProCons ? $bProCons['pros'] : collect();
                                    $bCons = $bProCons ? $bProCons['cons'] : collect();
                                @endphp
                                <div class="col-lg-{{ count($businesses) == 4 ? 3 : (count($businesses) == 3 ? 4 : 6) }} col-md-6 col-12">
                                    <div class="bg-white p-4 h-100" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); display: flex; flex-direction: column;">
                                        
                                        {{-- Header: Logo + Business Name --}}
                                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                            <div class="top-product-medium-logo" style="width: 32px; height: 32px;">
                                                <x-business-logo :business="$business" :name="$bName" />
                                            </div>
                                            <h5 style="font-size: 15.5px; font-weight: 700; color: #1e3050; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $bName }} pros and cons">
                                                {{ $bName }} pros and cons
                                            </h5>
                                        </div>

                                        @if($bPros->isNotEmpty() || $bCons->isNotEmpty())
                                            {{-- Pros Section --}}
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2" style="font-weight: 700; font-size: 14px; color: #1e3050;">
                                                    <span style="color: rgb(33, 172, 33);" class="me-2">
                                                        <!-- <i class="fas fa-plus-circle"></i> -->
                                                    </span>
                                                    Pros
                                                </div>
                                                @if($bPros->isNotEmpty())
                                                    <ul class="list-unstyled mb-0 d-flex flex-column" style="gap: 10px;">
                                                        @foreach($bPros->take(6) as $pro)
                                                            <li class="d-flex align-items-start" style="gap: 8px;">
                                                                <span style="color: rgb(33, 172, 33); flex-shrink: 0; font-size: 15px; margin-top: 2px; line-height: 1;">
                                                                    <i class="fas fa-plus-circle"></i>
                                                                </span>
                                                                <span style="color: #202124; font-size: 13.5px; line-height: 1.4;">
                                                                    {{ $pro->text }}
                                                                    <small class="badge font-weight-normal ms-1" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 11px; border: 1px solid #edf2f7;">
                                                                        from {{ $pro->review_count }} {{ $pro->review_count == 1 ? 'review' : 'reviews' }}
                                                                    </small>
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-muted small mb-0 py-1">No pros added yet.</p>
                                                @endif
                                            </div>

                                            <hr class="my-3" style="border-top: 1px solid #e2e8f0; opacity: 0.8;">

                                            {{-- Cons Section --}}
                                            <div class="mb-1">
                                                <div class="d-flex align-items-center mb-2" style="font-weight: 700; font-size: 14px; color: #1e3050;">
                                                    <span style="color: rgb(247, 40, 60);" class="me-2">
                                                        <!-- <i class="fas fa-minus-circle"></i> -->
                                                    </span>
                                                    Cons
                                                </div>
                                                @if($bCons->isNotEmpty())
                                                    <ul class="list-unstyled mb-0 d-flex flex-column" style="gap: 10px;">
                                                        @foreach($bCons->take(6) as $con)
                                                            <li class="d-flex align-items-start" style="gap: 8px;">
                                                                <span style="color: rgb(247, 40, 60); flex-shrink: 0; font-size: 15px; margin-top: 2px; line-height: 1;">
                                                                    <i class="fas fa-minus-circle"></i>
                                                                </span>
                                                                <span style="color: #202124; font-size: 13.5px; line-height: 1.4;">
                                                                    {{ $con->text }}
                                                                    <small class="badge font-weight-normal ms-1" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 11px; border: 1px solid #edf2f7;">
                                                                        from {{ $con->review_count }} {{ $con->review_count == 1 ? 'review' : 'reviews' }}
                                                                    </small>
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-muted small mb-0 py-1">No cons added yet.</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center text-muted small flex-grow-1" style="min-height: 120px;">
                                                No user-submitted pros or cons yet.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- price start from / offers (only for affiliated businesses) -->
                    @if($hasAffiliated)
                    <div class="prc_dv_new my-4" data-aos="fade-up" data-aos-duration="1000">
                        <div class="row g-4">
                            @foreach ($businesses as $index => $business)
                                @php
                                    $startingPriceBusiness = getBusinessesWithStartingPrice($business);
                                    $startingPrice = $startingPriceBusiness[0]['starting_price'] ?? null;
                                    $bName = $business->translations->first()?->name ?? '';
                                    $currency = $startingPrice['currency'] ?? '$';
                                    $amountStr = isset($startingPrice['amount']) ? number_format($startingPrice['amount'], 2) : null;
                                    $timeUnit = ucfirst($startingPrice['time_unit'] ?? 'One_time');
                                @endphp
                                <div class="col-lg-{{ count($businesses) == 4 ? 3 : (count($businesses) == 3 ? 4 : 6) }} col-md-6 col-12">
                                    @if(!empty($business->is_affiliate))
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="top-product-medium-logo">
                                        <x-business-logo :business="$business" :name="$bName" />
                                        </div>
                                        <h5 style="font-size: 16px; font-weight: 700; color: #1e3050; margin: 0;">{{ $bName }}</h5>
                                    </div>
                                    <div class="row g-3">
                                        <!-- Starting price card -->
                                        <div class="col-6">
                                            <div class="p-3 bg-white border h-100 d-flex flex-column justify-content-between text-center" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                                <div>
                                                    <p class="starting-price-title">Starting price</p>
                                                    @if($startingPrice)
                                                        <p class="starting-price-value">{{ $currency }}{{ $amountStr ?? 'Free' }}</p>
                                                        <p class="starting-price-text">Flat Rate, Per {{ $timeUnit }}</p>
                                                    @else
                                                        <p class="starting-price-value">Contact</p>
                                                        <p class="starting-price-text">Custom subscription</p>
                                                    @endif
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ $business->getTrackedUrl() ?? $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}" target="_blank" style="font-size: 13px; font-weight: 600; color: #06498b; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">View pricing</a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Free trial card -->
                                        <div class="col-6">
                                            <div class="p-3 bg-white border h-100 d-flex flex-column justify-content-between text-center" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="grn_check_big">
                                                        <img src="{{ asset('front/img/new-grn-chk.svg') }}" alt="Check">
                                                    </div>
                                                    <h6 class="blue-text big-bld mt-2">Free Trial<br>Available</h6>
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ $business->getTrackedUrl() ?? $business->affiliate_link ?? $business->permanent_url ?? 'javascript:void(0)' }}" target="_blank" class="blue-btn blue_t_org_btn btn text-white w-100 fw-semibold" style=" border-radius: 50px; font-size: 13px; padding: 8px 16px; text-decoration: none;">Claim Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section (Matches user reference: Business Cards with conditional Visit Website button, Filter by rating, Sort, and Reviews List) -->
    <div class="crm_sec light pb-0 rev-tabi" id="comparison-reviews-section">
        <div class="container">
            <div class="crm_content" data-aos="fade-up" data-aos-duration="1000">
                <div class="crm_hd mb-4">
                    <div class="crm_lft">
                        <h2 style="margin: 0;">Reviews</h2>
                    </div>
                </div>

                {{-- 1. TOP BUSINESS SUMMARY CARDS (Logo, Name, Stars, Count, and Visit Website button ONLY if affiliated) --}}
                <div class="row g-4 mb-4">
                    @foreach($businesses as $business)
                        @php
                            $activeReviews = $business->reviews->where('status', 'active');
                            $bAvgRating = $activeReviews->count() > 0
                                ? round($activeReviews->avg('rating'), 1)
                                : (float)($business->admin_rating ?? 0);
                            $bTotalReviews = $activeReviews->count();
                            $bName = $business->translations->first()?->name ?? 'Business';
                            $curLang = getCurrentLocale();
                            $lObj = \App\Models\Language::where('lang_code', $curLang)->first();
                            $revSlug = !empty($lObj->reviews_slug) ? $lObj->reviews_slug : 'reviews';
                            $bizSlug = $business->translations->where('lang_id', getCurrentLanguageID())->first()?->slug ?? $business->translations->first()?->slug ?? $business->slug;
                        @endphp
                        <div class="col-lg-{{ count($businesses) == 4 ? 3 : (count($businesses) == 3 ? 4 : 6) }} col-md-6 col-12">
                            <div class="p-3 bg-white d-flex align-items-center justify-content-between" style="border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); min-height: 80px;">
                                <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                    <div class="top-product-medium-logo" style="width: 44px; height: 44px; flex-shrink: 0;">
                                        <x-business-logo :business="$business" :name="$bName" />
                                    </div>
                                    <div style="min-width: 0;">
                                        <h5 style="font-size: 15.5px; font-weight: 700; margin: 0 0 2px 0; color: #1e3050; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $bName }}">
                                            {{ $bName }}
                                        </h5>
                                        <div class="rating-group">
                                            <span >{{ number_format($bAvgRating, 1) }}</span>
                                            <div class="d-inline-flex align-items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= floor($bAvgRating))
                                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                                    @elseif ($i - 0.5 <= $bAvgRating)
                                                        <i class="fas fa-star-half-alt text-warning" style="font-size: 10px;"></i>
                                                    @else
                                                        <i class="far fa-star text-warning" style="font-size: 10px;"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span >({{ number_format($bTotalReviews) }})</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Show Visit website button ONLY if affiliated --}}
                                @if(!empty($business->is_affiliate))
                                    <a href="{{ $business->getTrackedUrl() }}" class="cta btn-orng justify-content-center ms-2 flex-shrink-0" target="_blank" style="display: inline-flex !important; align-items: center;  padding: 8px 18px; font-size: 12px;  white-space: nowrap; text-decoration: none;">
                                        {{ static_text('visit_website') ?: 'Visit website' }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 5px; flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- 2. FILTER BY RATING & SORT TOOLBAR --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 mb-4" style="border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <!-- Filter Button with Dropdown -->
                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary d-flex align-items-center gap-2" 
                                    type="button" 
                                    id="compReviewFilterBtn" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="outside"
                                    aria-expanded="false" 
                                    style="border-color: #ced4da; color: #1e3050; font-size: 13.5px; font-weight: 600; padding: 7px 16px; border-radius: 8px; background: #fff;">
                                <img src="{{ asset('front/img/filter-icon.svg') }}" alt="Filter" style="width: 14px; height: 14px;">
                                <span>Filter by rating</span>
                                <span class="badge bg-primary rounded-pill d-none" id="comp-filter-count-badge" style="font-size: 11px;">0</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px;">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-start p-3 shadow-sm" 
                                 aria-labelledby="compReviewFilterBtn" 
                                 style="min-width: 250px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                <div class="mb-2 pb-2 border-bottom">
                                    <span style="font-size: 13.5px; font-weight: 700; color: #002655;">Filter by star rating</span>
                                </div>
                                <ul class="list-unstyled mb-0 d-flex flex-column" style="gap: 8px;">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <li>
                                            <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-size: 13px; color: #334155;">
                                                <input type="checkbox" class="comp-rating-filter-checkbox" value="{{ $i }}" style="cursor: pointer; width: 16px; height: 16px; accent-color: #06498b;">
                                                <span>{{ $i }} {{ $i == 1 ? 'Star' : 'Stars' }}</span>
                                                <div class="d-inline-flex" style="gap: 1px;">
                                                    @for ($s = 1; $s <= $i; $s++)
                                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                                    @endfor
                                                </div>
                                            </label>
                                        </li>
                                    @endfor
                                </ul>
                            </div>
                        </div>

                        <span class="comp-clear-filters-btn ms-2 text-primary" id="comp-clear-filters" style="display: none; font-size: 13px; font-weight: 600; cursor: pointer;">
                            <i class="fas fa-times me-1"></i>Clear filter
                        </span>
                    </div>

                    <!-- Sort Dropdown on Right -->
                    <div class="dropdown">
                        <button type="button" 
                            class="sorting d-inline-flex align-items-center gap-2" 
                            id="compReviewSortBtn"
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            style="background-color: #fff; color: #0f172a; border-radius: 20px; padding: 7px 18px; border: 1px solid #cbd5e1; outline: none; cursor: pointer; font-size: 13px;">
                            <span>Sort: <span id="comp-current-sort-label" style="font-weight: 600;">Most recent</span></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </button>

                        <div class="dropdown-menu dropdown-menu-end shadow-sm" 
                             aria-labelledby="compReviewSortBtn"
                             style="min-width: 180px; border-radius: 12px; border: 1px solid #e2e8f0; padding: 6px 0;">
                            <button type="button" data-sort-val="recent" data-sort-txt="Most recent" class="comp-sort-opt dropdown-item px-3 py-2 fw-semibold" style="font-size: 13px;">Most recent</button>
                            <button type="button" data-sort-val="high" data-sort-txt="Highest rated" class="comp-sort-opt dropdown-item px-3 py-2" style="font-size: 13px;">Highest rated</button>
                            <button type="button" data-sort-val="low" data-sort-txt="Lowest rated" class="comp-sort-opt dropdown-item px-3 py-2" style="font-size: 13px;">Lowest rated</button>
                        </div>
                    </div>
                </div>

                {{-- 3. REVIEWS GRID BY BUSINESS (Matching layout from image) --}}
                <div class="row g-4 mb-4" id="comp-reviews-columns-container">
                    @foreach($businesses as $business)
                        @php
                            $activeReviews = $business->reviews->where('status', 'active');
                            $bName = $business->translations->first()?->name ?? 'Business';
                            $curLang = getCurrentLocale();
                            $lObj = \App\Models\Language::where('lang_code', $curLang)->first();
                            $revSlug = !empty($lObj->reviews_slug) ? $lObj->reviews_slug : 'reviews';
                            $bizSlug = $business->translations->where('lang_id', getCurrentLanguageID())->first()?->slug ?? $business->translations->first()?->slug ?? $business->slug;
                            $bizCriteria = $business->category ? $business->category->getEffectiveRatingCriteria() : collect();
                            if ($bizCriteria->isEmpty()) {
                                $bizCriteria = \App\Models\DefaultRatingCriteria::orderBy('sort_order')->get();
                            }
                        @endphp
                        <div class="col-lg-{{ count($businesses) == 4 ? 3 : (count($businesses) == 3 ? 4 : 6) }} col-md-6 col-12 comp-biz-reviews-col" data-biz-id="{{ $business->id }}">
                            <div class="d-flex flex-column" style="gap: 16px;">
                                @forelse($activeReviews->take(6) as $rev)
                                    @php
                                        $revTrans = $rev->translations->first();
                                        $revTitle = $revTrans->title ?? '';
                                        $revDesc = strip_tags($revTrans->description ?? '');
                                        $revUser = $rev->user;
                                        $initial = strtoupper(substr($revUser->first_name ?? ($rev->public_name ?? 'A'), 0, 1));
                                    @endphp
                                                                        <div class="comp-single-review-card review_detl" 
                                         id="review-{{ $rev->id }}"
                                         data-rating="{{ round($rev->rating) }}" 
                                         data-date="{{ $rev->created_at->timestamp }}"
                                         style="background-color: #fff; border-radius: 12px; padding: 22px; border: 1px solid #e2e8f0; position: relative; display: flex; flex-direction: column; margin-bottom: 0;">
                                        
                                        <!-- Top right actions (Date) -->
                                        <div class="review-actions-top-right" style="position: absolute; top: 22px; right: 22px; display: flex; align-items: center; gap: 12px;">
                                            <span class="text-muted" style="font-size: 12px;">{{ $rev->created_at->diffForHumans() }}</span>
                                        </div>

                                        <!-- Reviewer Header -->
                                        <div class="reviw_hd" style="margin-bottom: 16px; border-bottom: none; padding-bottom: 0;">
                                            <div class="ans_lft" style="display: flex; gap: 12px; align-items: flex-start;">
                                                <div class="asn-img" style="flex-shrink: 0;">
                                                    @if ($revUser && $revUser->profile_image && $revUser->profile_image !== 'front/img/default.png')
                                                        <img src="{{ asset($revUser->profile_image) }}"
                                                            class="img-fluid profile-circle"
                                                            style="width: 44px; height: 44px; object-fit: cover; border-radius: 50%;"
                                                            alt="User Image">
                                                    @else
                                                        <div class="profile-circle" style="width: 44px; height: 44px; border-radius: 50%; background-color: #003f7d; display: flex; align-items: center; justify-content: center;">
                                                            <span style="color: white; font-weight: bold; font-size: 18px;">
                                                                {{ $initial }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="asn-rating" style="display: flex; flex-direction: column; padding-right: 75px;">
                                                    <h6 style="font-size: 14.5px; font-weight: 600; margin: 0; color: #1e3050;">
                                                        @if ($revUser)
                                                            <a href="{{ route('user.profile', ['id' => $revUser->id]) }}" style="color: inherit; ">
                                                                {{ $revUser->displayName() }}
                                                            </a>
                                                        @else
                                                            {{ $rev->public_name ?? 'Anonymous' }}
                                                        @endif
                                                    </h6>
                                                    @if($revUser && $revUser->job_title)
                                                        <p class="asn-rating-p mb-0" style="font-size: 12px; color: #777; margin: 0; line-height: 1.2; font-weight: 500;">{{ $revUser->job_title }}</p>
                                                    @endif
                                                    @if($revUser && $revUser->company_size)
                                                        <p class="asn-rating-p mb-0" style="font-size: 12px; color: #777; margin: 0; line-height: 1.2; font-weight: 500;">{{ static_text('company_size_' . $revUser->company_size) ?: $revUser->company_size }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Review Body (Headline -> Stars -> Description) -->
                                        <div class="review_text flex-grow-1" style="padding-right: 0;">
                                            @if(!empty($revTitle))
                                                <h5 class="size22 big-bld" style="font-size: 16px; font-weight: 700; color: #1e3050; margin-bottom: 8px;">
                                                    {{ $revTitle }}
                                                </h5>
                                            @endif

                                            <!-- Stars below headline + Arrow + Detailed User Ratings Breakdown Popup -->
                                            <div class="rating light mb-2" style="display: flex; align-items: center; gap: 6px;">
                                                <div class="user-rating-breakdown-wrapper position-relative d-inline-block">
                                                    <div class="user-rating-trigger d-inline-flex align-items-center" style="cursor: pointer; gap: 4px;" title="Detailed user ratings">
                                                        <div class="rating-stars" style="display: flex; gap: 2px;">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= floor($rev->rating))
                                                                    <i class="fas fa-star text-warning" style="font-size: 13px;"></i>
                                                                @elseif ($i - 0.5 <= $rev->rating)
                                                                    <i class="fas fa-star-half-alt text-warning" style="font-size: 13px;"></i>
                                                                @else
                                                                    <i class="far fa-star text-warning" style="font-size: 13px;"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <span class="user-rating-dropdown-btn">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                <polyline points="6 9 12 15 18 9" />
                                                            </svg>
                                                        </span>
                                                    </div>

                                                    @if(isset($bizCriteria) && count($bizCriteria) > 0)
                                                    <div class="dropdown-menu shadow-sm user-rating-breakdown-menu" style="display: none;">
                                                        <div class="user-rating-header">Detailed user ratings</div>
                                                        <div class="user-rating-body">
                                                            @foreach ($bizCriteria as $criterion)
                                                            @php
                                                                $scoreVal = null;
                                                                if ($rev->ratings && $rev->ratings->isNotEmpty()) {
                                                                    $match = $rev->ratings->firstWhere('criteria_id', $criterion->id);
                                                                    if (!$match && $criterion->default_key) {
                                                                        $match = $rev->ratings->first(fn($r) => optional($r->criteria)->default_key === $criterion->default_key);
                                                                    }
                                                                    if (!$match) {
                                                                        $match = $rev->ratings->first(fn($r) => strtolower(optional($r->criteria)->name) === strtolower($criterion->name));
                                                                    }
                                                                    if ($match && !is_null($match->rating)) {
                                                                        $scoreVal = (float) $match->rating;
                                                                    }
                                                                }
                                                                if (is_null($scoreVal)) {
                                                                    $cName = strtolower($criterion->name);
                                                                    $cKey = $criterion->default_key;
                                                                    if ($cKey === 'ease_of_use' || $cName === 'ease of use') {
                                                                        $scoreVal = $rev->ease_of_use_rating;
                                                                    } elseif ($cKey === 'features' || $cName === 'features') {
                                                                        $scoreVal = $rev->exclusive_service_rating;
                                                                    } elseif ($cKey === 'value_for_money' || $cName === 'value for money') {
                                                                        $scoreVal = $rev->value_for_money_rating;
                                                                    } elseif ($cKey === 'customer_service' || $cName === 'customer service' || $cName === 'support') {
                                                                        $scoreVal = $rev->customer_service_rating;
                                                                    }
                                                                }
                                                                if (is_null($scoreVal) || $scoreVal <= 0) {
                                                                    $scoreVal = ((float)$rev->rating > 0) ? (float)$rev->rating : 4.0;
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
                                                    @endif
                                                </div>
                                            </div>

                                            @if(!empty($revDesc))
                                                <div style="color: #444; line-height: 1.6; font-size: 13.5px; margin-bottom: 12px;">
                                                    {!! nl2br(e(\Illuminate\Support\Str::limit($revDesc, 160))) !!}
                                                </div>
                                            @endif

                                            <!-- Review Pros and Cons -->
                                            @php
                                                $selectedPros = $rev->selectedProCons ? $rev->selectedProCons->where('type', 'pro') : collect();
                                                $selectedCons = $rev->selectedProCons ? $rev->selectedProCons->where('type', 'con') : collect();
                                            @endphp

                                            @if($selectedPros->isNotEmpty())
                                                <div class="comp-rev-pros mb-2" style="margin-top: 10px;">
                                                    <div style="font-size: 13px; font-weight: 600; color: #1e3050; margin-bottom: 5px;">
                                                        Pros
                                                    </div>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                                        @foreach($selectedPros as $pro)
                                                            <span style="display: inline-flex; align-items: center; padding: 4px 10px; background: #f0fdf4; color: #166534; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                                {{ $pro->text }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if($selectedCons->isNotEmpty())
                                                <div class="comp-rev-cons mb-2" style="margin-top: 8px;">
                                                    <div style="font-size: 13px; font-weight: 600; color: #1e3050; margin-bottom: 5px;">
                                                        Cons
                                                    </div>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                                        @foreach($selectedCons as $con)
                                                            <span style="display: inline-flex; align-items: center; padding: 4px 10px; background: #fef2f2; color: #991b1b; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                                {{ $con->text }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Bottom actions (Report flag & Share button) identical to detail page -->
                                        <div class="review-actions-bottom-right mt-auto pt-2" style="display: flex; justify-content: flex-end; align-items: center; gap: 8px;">
                                            <button type="button" class="action-icon-btn" onclick="openReviewReportModal({{ $rev->id }})" title="Report this review" >
                                                <i class="fas fa-flag"></i>
                                            </button>
                                            <button type="button" class="action-icon-btn" onclick="window.openShareModal('{{ url()->current() }}#review-{{ $rev->id }}', 'Review on {{ config('app.name') }}', event)" title="Share this review" aria-label="Share this review">
                                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="18" cy="5" r="3"></circle>
                                                    <circle cx="6" cy="12" r="3"></circle>
                                                    <circle cx="18" cy="19" r="3"></circle>
                                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 bg-white text-center text-muted" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                                        No reviews available for this business yet.
                                    </div>
                                @endforelse

                                {{-- Link to see all reviews for this business --}}
                                <div class="text-center pt-2">
                                    <a href="{{ route('ReviewShow', ['locale' => $curLang, 'slug' => $bizSlug, 'reviews_slug' => $revSlug]) }}" class="btn-g-link">
                                        View all reviews 
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for client-side filter and sorting of comparison reviews --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterCheckboxes = document.querySelectorAll('.comp-rating-filter-checkbox');
            const clearFilterBtn = document.getElementById('comp-clear-filters');
            const filterCountBadge = document.getElementById('comp-filter-count-badge');
            const sortOptions = document.querySelectorAll('.comp-sort-opt');
            const sortLabel = document.getElementById('comp-current-sort-label');
            let currentSort = 'recent';

            function filterAndSortReviews() {
                const selectedRatings = Array.from(filterCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => parseInt(cb.value));

                // Update clear filter button and badge
                if (selectedRatings.length > 0) {
                    clearFilterBtn.style.display = 'inline-block';
                    filterCountBadge.textContent = selectedRatings.length;
                    filterCountBadge.classList.remove('d-none');
                } else {
                    clearFilterBtn.style.display = 'none';
                    filterCountBadge.classList.add('d-none');
                }

                // Process each business column
                document.querySelectorAll('.comp-biz-reviews-col').forEach(col => {
                    const cardsContainer = col.querySelector('.d-flex.flex-column');
                    const cards = Array.from(cardsContainer.querySelectorAll('.comp-single-review-card'));

                    // Filter visibility
                    cards.forEach(card => {
                        const cardRating = parseInt(card.getAttribute('data-rating'));
                        if (selectedRatings.length === 0 || selectedRatings.includes(cardRating)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Sort visible cards
                    cards.sort((a, b) => {
                        const dateA = parseInt(a.getAttribute('data-date')) || 0;
                        const dateB = parseInt(b.getAttribute('data-date')) || 0;
                        const ratA = parseInt(a.getAttribute('data-rating')) || 0;
                        const ratB = parseInt(b.getAttribute('data-rating')) || 0;

                        if (currentSort === 'recent') {
                            return dateB - dateA;
                        } else if (currentSort === 'high') {
                            return ratB - ratA;
                        } else if (currentSort === 'low') {
                            return ratA - ratB;
                        }
                        return 0;
                    });

                    // Re-append sorted cards before the view all link
                    const viewAllDiv = cardsContainer.querySelector('.text-center.pt-2');
                    cards.forEach(card => {
                        if (viewAllDiv) {
                            cardsContainer.insertBefore(card, viewAllDiv);
                        } else {
                            cardsContainer.appendChild(card);
                        }
                    });
                });
            }

            filterCheckboxes.forEach(cb => {
                cb.addEventListener('change', filterAndSortReviews);
            });

            if (clearFilterBtn) {
                clearFilterBtn.addEventListener('click', function () {
                    filterCheckboxes.forEach(cb => cb.checked = false);
                    filterAndSortReviews();
                });
            }

            sortOptions.forEach(opt => {
                opt.addEventListener('click', function () {
                    currentSort = this.getAttribute('data-sort-val');
                    sortLabel.textContent = this.getAttribute('data-sort-txt');
                    sortOptions.forEach(o => o.classList.remove('fw-semibold'));
                    this.classList.add('fw-semibold');
                    filterAndSortReviews();
                });
            });
        });
    </script>


    <!-- Alternatives Section (Native Localio Design) -->
    @php
        $comparedIds = $businesses->pluck('id')->toArray();
        $currentLocale = app()->getLocale();
        $langObj = \App\Models\Language::where('lang_code', $currentLocale)->first();
        $expectedAlternativesSlug = !empty($langObj->alternatives_slug) ? $langObj->alternatives_slug : 'alternatives';
    @endphp

    <div class="alternatives_comp_sec crm_sec light py-5 rev-tabi grey_dark_bg" id="comparison-alternatives-section">
        <div class="container">
            <div class="crm_content" data-aos="fade-up" data-aos-duration="1000">
                
                {{-- Section Title --}}
                <div class="crm_hd mb-4">
                    <div class="crm_lft">
                        <h2 style="margin: 0;">Alternatives</h2>
                    </div>
                </div>

                {{-- Columns matching the exact comparison grid --}}
                <div class="row g-4 mb-4">
                    @foreach($businesses as $business)
                        @php
                            $bName = $business->translations->first()?->name ?? 'Business';
                            $bizSlug = $business->translations->where('lang_id', getCurrentLanguageID())->first()?->slug ?? $business->translations->first()?->slug ?? $business->slug;
                            
                            // Query alternatives from same category or fallback to other active businesses
                            $categoryIds = array_filter([$business->category_id]);
                            $subCategoryIds = $business->subCategories->pluck('id')->toArray();
                            $allRelatedCatIds = array_unique(array_merge($categoryIds, $subCategoryIds));

                            $altsQuery = \App\Models\Business::whereNotIn('id', $comparedIds);
                            if (!empty($allRelatedCatIds)) {
                                $altsQuery->where(function($q) use ($allRelatedCatIds) {
                                    $q->whereIn('category_id', $allRelatedCatIds)
                                      ->orWhereHas('subCategories', function($sq) use ($allRelatedCatIds) {
                                          $sq->whereIn('categories.id', $allRelatedCatIds);
                                      });
                                });
                            }
                            $alts = $altsQuery->take(4)->get();

                            if ($alts->isEmpty()) {
                                $alts = \App\Models\Business::whereNotIn('id', $comparedIds)->take(4)->get();
                            }
                        @endphp

                        <div class="col-lg-{{ count($businesses) == 4 ? 3 : (count($businesses) == 3 ? 4 : 6) }} col-md-6 col-12">
                            <div class="comp-alt-box p-4 bg-white d-flex flex-column h-100" style="border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                
                                {{-- Business Header --}}
                                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                    <div class="top-product-medium-logo" style="width: 28px; height: 28px; flex-shrink: 0;">
                                        <x-business-logo :business="$business" :name="$bName" />
                                    </div>
                                    <h5 style="font-size: 15px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $bName }} alternatives">
                                        {{ $bName }} alternatives
                                    </h5>
                                </div>

                                {{-- Alternatives List --}}
                                <div class="d-flex flex-column flex-grow-1" style="gap: 12px;">
                                    @forelse($alts as $alt)
                                        @php
                                            $altName = $alt->translations->first()?->name ?? 'Product';
                                            $altProdId = $alt->products->first()?->id ?? $alt->id;
                                            $altReviews = $alt->reviews->where('status', 'active');
                                            $altRating = $altReviews->count() > 0 ? round($altReviews->avg('rating'), 1) : 0;
                                        @endphp
                                        <div class="comp-alt-item d-flex align-items-center justify-content-between p-2 rounded" style="background-color: #f8fafc; border: 1px solid #edf2f7;   ">
                                            <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                                <div class="top-product-medium-logo" style="width: 34px; height: 34px; flex-shrink: 0;">
                                                    <x-business-logo :business="$alt" :name="$altName" />
                                                </div>
                                                <div style="min-width: 0;">
                                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $alt->translations->first()?->slug ?? $alt->slug]) }}" style="font-size: 13.5px; font-weight: 600; color: #002347; text-decoration: none; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $altName }}">
                                                        {{ $altName }}
                                                    </a>
                                                    @if($altRating > 0)
                                                        <div class="d-flex align-items-center gap-1" style="font-size: 11px; color: #64748b;">
                                                            <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                                            <span>{{ number_format($altRating, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <button type="button" 
                                                    class="alt-add-to-comp-btn flex-shrink-0 ms-2" 
                                                    data-id="{{ $altProdId }}" 
                                                    title="Add {{ $altName }} to comparison">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    @empty
                                        <p class="text-muted small mb-0 py-3 text-center">No other alternatives available.</p>
                                    @endforelse
                                </div>

                                {{-- "See all alternatives" link --}}
                                <div class="text-center pt-3 mt-3 border-top">
                                    <a href="{{ route('business.alternatives', ['locale' => $currentLocale, 'business_slug' => $bizSlug, 'alternatives_slug' => $expectedAlternativesSlug]) }}" 
                                       class="btn-g-link" >
                                        See all alternatives 
                                    </a>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle + click on alternatives to add to comparison
            document.querySelectorAll('.alt-add-to-comp-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-id');
                    if (!productId) return;

                    fetch("{{ route('add-to-comparison', app()->getLocale()) }}/" + productId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            currentIds: @json($businesses->pluck('id')->toArray())
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        if (err.json) {
                            err.json().then(errorData => {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(errorData.error || 'Failed to add business to comparison');
                                } else {
                                    alert(errorData.error || 'Failed to add business to comparison');
                                }
                            });
                        } else {
                            console.error('Error adding to comparison:', err);
                        }
                    });
                });
            });
        });
    </script>


        <section class="subs_sec light pt_120 ">
        <x-news-letter-subscription/>
    </section>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown toggle for "Add products"
            const toggleBtn = document.getElementById('toggleAddProductBtn');
            const dropdown = document.getElementById('addCompDropdown');
            const searchInput = document.getElementById('addCompSearchInput');
            const itemsList = document.getElementById('addCompItemsList');
            const addFromCardBtn = document.getElementById('addFromCompareCardBtn');

            function openAddCompDropdown() {
                if (!dropdown) return;
                dropdown.style.display = 'block';
                const wrapper = document.querySelector('.your-comparison-wrapper');
                if (wrapper) {
                    wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 250);
                }
            }

            if (toggleBtn && dropdown) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isVisible = dropdown.style.display === 'block';
                    dropdown.style.display = isVisible ? 'none' : 'block';
                    if (!isVisible && searchInput) {
                        setTimeout(() => searchInput.focus(), 100);
                    }
                });

                if (addFromCardBtn) {
                    addFromCardBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openAddCompDropdown();
                    });
                }

                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && e.target !== toggleBtn && (!addFromCardBtn || !addFromCardBtn.contains(e.target))) {
                        dropdown.style.display = 'none';
                    }
                });
            }

            // Real-time filter inside add products dropdown
            if (searchInput && itemsList) {
                searchInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    const rows = itemsList.querySelectorAll('.add-comp-item-row');
                    rows.forEach(row => {
                        const name = row.getAttribute('data-name') || '';
                        row.style.display = name.includes(q) ? 'flex' : 'none';
                    });
                });
            }

            // Click to add product to comparison
            document.querySelectorAll('.add-comp-item-row').forEach(row => {
                row.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    if (!productId) return;

                    fetch("{{ route('add-to-comparison', app()->getLocale()) }}/" + productId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            currentIds: @json($businesses->pluck('id')->toArray())
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(async error => {
                        let message = 'Could not add product to comparison.';
                        try {
                            const errJson = await error.json();
                            message = errJson.message || message;
                        } catch (_) {}
                        alert(message);
                    });
                });
            });

            // Remove from comparison
            document.querySelectorAll('.remove-from-comparison').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const url = this.getAttribute('data-url');
                    if (!url) return;

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                productId: this.getAttribute('data-product-id') || null,
                                currentIds: @json($businesses->pluck('id')->toArray()),
                                comparison_slug: '{{ $comparison_slug ?? "" }}'
                            })
                        })
                        .then(response => {
                            if (!response.ok) throw response;
                            return response.json();
                        })
                        .then(data => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.href =
                                    "{{ route('product-comparison', app()->getLocale()) }}";
                            }
                        })
                        .catch(async error => {
                            let message = 'Something went wrong.';
                            try {
                                const errJson = await error.json();
                                message = errJson.message || message;
                            } catch (_) {}
                            alert(message);
                        });
                });
            });
        });
    </script>


<!-- Report Issue Modal -->
<div class="modal fade" id="reportReviewModal" tabindex="-1" aria-labelledby="reportReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 40px rgba(0,0,0,0.14);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 20px 24px 16px;">
                <div>
                    <h5 class="modal-title fw-bold m-0" id="reportReviewModalLabel" style="color: #002347; font-size: 18px;">Report a review</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size: 13px;">Select the reason that best describes the issue</p>
                </div>
                <button type="button" class="model-close" data-bs-dismiss="modal" aria-label="Close">✕</button>
            </div>
            <div class="modal-body" style="padding: 20px 24px;">
                <input type="hidden" id="reportReviewModalId">
                <input type="hidden" id="reportReviewReason">

                <!-- Radio option cards -->
                <div class="d-flex flex-column gap-2" id="reportReviewReasonOptions">
                    @foreach([
                        ['value' => 'Fake or fraudulent',   'icon' => 'fa-ban',              'desc' => 'The review appears to be fake or paid for'],
                        ['value' => 'Inappropriate content', 'icon' => 'fa-exclamation-triangle', 'desc' => 'Contains offensive language or hate speech'],
                        ['value' => 'Spam or advertising',  'icon' => 'fa-bullhorn',         'desc' => 'Promotional or irrelevant content'],
                        ['value' => 'Conflict of interest', 'icon' => 'fa-user-tie',         'desc' => 'Written by someone affiliated with the business'],
                        ['value' => 'Other issue',          'icon' => 'fa-comment-dots',     'desc' => 'Another problem not listed above']
                    ] as $opt)
                        <div class="report-review-option-card d-flex align-items-start gap-3 p-3" 
                             onclick="selectReviewReportReason('{{ $opt['value'] }}', this)"
                             style="border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; ">
                            
                            <div class="report-check flex-shrink-0 mt-1" style="width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; ">
                                <i class="fas fa-check" style="font-size: 10px; color: transparent;"></i>
                            </div>
                            
                            <div>
                                <h6 class="m-0" style="font-size: 14.5px; font-weight: 600; color: #1e293b; margin-bottom: 2px !important;">
                                    <i class="fas {{ $opt['icon'] }} me-1 text-muted" style="font-size: 13px;"></i> 
                                    {{ $opt['value'] }}
                                </h6>
                                <p class="text-muted m-0" style="font-size: 13px; line-height: 1.4;">{{ $opt['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Textarea (hidden initially) -->
                <div id="reportReviewDetailsWrapper" class="mt-4" style="display: none;">
                    <label class="form-label fw-bold" style="font-size: 13.5px; color: #334155;">Additional details (optional)</label>
                    <textarea id="reportReviewDetails" class="form-control" rows="3" placeholder="Please provide any additional context..." style="font-size: 14px; border-radius: 12px; padding: 12px; resize: none;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 16px 24px;">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 14px;">Cancel</button>
                <button type="button" class="btn rounded-pill px-4" id="submitReviewReportBtn" disabled style="background: #002347; border-color: #002347; color: white; font-size: 14px; opacity: 0.5; transition: opacity 0.2s;" onclick="submitReviewReport()">Submit report</button>
            </div>
        </div>
    </div>
</div>
<style>
    .report-review-option-card:hover {
        border-color: #94a3b8 !important;
        background: #f8fafc;
    }
    .report-review-option-card.selected {
        border-color: #002347 !important;
        background: #f0f4ff;
    }
    .report-review-option-card.selected .report-check {
        background: #002347;
        border-color: #002347;
    }
    .report-review-option-card.selected .report-check i {
        color: #ffffff !important;
    }
</style>
<script>
function openReviewReportModal(reviewId) {
    @if(!auth()->check())
        if(typeof openLoginModal === 'function') {
            openLoginModal();
        } else {
            window.location.href = "{{ route('login') }}";
        }
        return;
    @endif

    document.getElementById('reportReviewModalId').value = reviewId;
    document.getElementById('reportReviewReason').value = '';
    document.getElementById('reportReviewDetails').value = '';
    document.getElementById('reportReviewDetailsWrapper').style.display = 'none';
    document.getElementById('submitReviewReportBtn').disabled = true;
    document.getElementById('submitReviewReportBtn').style.opacity = '0.5';

    document.querySelectorAll('.report-review-option-card').forEach(c => c.classList.remove('selected'));

    const modalEl = document.getElementById('reportReviewModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function selectReviewReportReason(reason, card) {
    document.getElementById('reportReviewReason').value = reason;
    document.querySelectorAll('.report-review-option-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('reportReviewDetailsWrapper').style.display = 'block';
    const btn = document.getElementById('submitReviewReportBtn');
    btn.disabled = false;
    btn.style.opacity = '1';
}

function submitReviewReport() {
    const reviewId = document.getElementById('reportReviewModalId').value;
    const reason = document.getElementById('reportReviewReason').value;
    const details = document.getElementById('reportReviewDetails').value;
    const submitBtn = document.getElementById('submitReviewReportBtn');

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    fetch('/review/report', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            review_id: reviewId,
            report_reason: reason,
            report_details: details
        })
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit report';

        if (data.success) {
            const modalEl = document.getElementById('reportReviewModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            alert(data.message || 'Report submitted successfully!');
        } else if (data.require_login) {
            alert(data.message || 'Please sign in.');
        } else {
            alert(data.message || 'An error occurred. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit report';
    });
}
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Handle click to toggle detailed user rating breakdown on stars/arrow
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.user-rating-trigger');
        if (trigger) {
            e.preventDefault();
            e.stopPropagation();
            const wrapper = trigger.closest('.user-rating-breakdown-wrapper');
            if (wrapper) {
                const isShown = wrapper.classList.contains('show');
                document.querySelectorAll('.user-rating-breakdown-wrapper.show').forEach(function (el) {
                    el.classList.remove('show');
                });
                if (!isShown) {
                    wrapper.classList.add('show');
                    adjustRatingMenuPosition(wrapper);
                }
            }
        } else if (!e.target.closest('.user-rating-breakdown-menu')) {
            document.querySelectorAll('.user-rating-breakdown-wrapper.show').forEach(function (el) {
                el.classList.remove('show');
            });
        }
    });

    // Handle mouseover to adjust menu position so it doesn't overflow the viewport
    document.addEventListener('mouseover', function (e) {
        const wrapper = e.target.closest('.user-rating-breakdown-wrapper');
        if (wrapper) {
            adjustRatingMenuPosition(wrapper);
        }
    });

    function adjustRatingMenuPosition(wrapper) {
        const menu = wrapper.querySelector('.user-rating-breakdown-menu');
        if (!menu) return;
        menu.style.left = '0';
        menu.style.right = 'auto';
        const rect = menu.getBoundingClientRect();
        if (rect.right > window.innerWidth - 15) {
            menu.style.left = 'auto';
            menu.style.right = '0';
        }
    }
});
</script>

@endsection
