@extends('user_layout.master')

@php
    $lang_id = getCurrentLanguageID();
    $langCode = app()->getLocale();

    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? $business->category->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatName = $parentCatTrans->name ?? $business->category->parent->name ?? '';
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $businessSlug = $business->translations->firstWhere('language_id', $lang_id)?->slug 
        ?? $business->translations->first()?->slug 
        ?? $business->id;

    // Retrieve all public offer products of the business
    $offerProducts = $business->offerProducts()
        ->where('status', 'public')
        ->with([
            'plans' => function($q) {
                $q->where('status', 'public')->orderBy('sort_order', 'asc');
            },
            'plans.prices' => function($q) {
                $q->where('is_active', true);
            },
            'plans.featuredHighlights.feature.translations',
            'plans.planFeatures.feature.translations',
            'plans.specifications',
        ])
        ->orderBy('sort_order', 'asc')
        ->get();

    // Determine initial active product (first product that has plans, or first product)
    $activeProduct = $offerProducts->firstWhere(fn($p) => $p->plans->isNotEmpty()) ?? $offerProducts->first();
@endphp

@section('meta_title', format_meta_text($metaTitle))
@if(!empty($metaDescription))
@section('meta_description', format_meta_text($metaDescription))
@endif

@section('content')

@include('User.partials.business_detail_styles')

<style>
.pricing-hero-sec {
    background-color: #f7f9fb;
    color: #1e3050;
    border-bottom: 1px solid #e2e8f0;
    padding: 30px 0 35px;
}
.pricing-container-card {
    background: #ffffff;
    border: 1px solid #eaecf0;
    border-radius: 16px;
    padding: 28px 24px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
}
@media (min-width: 768px) {
    .pricing-container-card {
        padding: 32px 30px;
    }
}
.pricing-product-tab {
    border-radius: 9999px;
    padding: 7px 20px;
    font-size: 13.5px;
    font-weight: 500;
    transition: all 0.15s ease;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    line-height: 1.4;
}
.pricing-product-tab:hover:not(.active) {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
}
.pricing-product-tab.active {
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
    font-weight: 600;
}
.pricing-cycle-wrapper {
    background: #f1f5f9;
    padding: 4px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
}
.pricing-cycle-btn {
    border: none;
    background: transparent;
    border-radius: 9999px;
    padding: 5px 18px;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
    line-height: 1.35;
}
.pricing-cycle-btn.active {
    background: #ffffff;
    color: #0f172a;
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
.plan-col-card {
    background: #ffffff;
    border: 1px solid #eaecf0;
    border-radius: 12px;
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.plan-col-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
}
.plan-name-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
    letter-spacing: -0.01em;
}
.plan-short-desc {
    font-size: 13px;
    color: #64748b;
    line-height: 1.45;
    min-height: 38px;
    margin-bottom: 16px;
}
.plan-price-amount {
    font-size: 26px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}
.plan-price-subtext {
    font-size: 13px;
    color: #64748b;
    margin-top: 4px;
}
.plan-link-btn {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none;
    transition: color 0.15s ease;
}
.plan-link-btn:hover {
    color: #2563eb;
}
.pricing-compare-table th {
    padding: 14px 16px;
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    border-bottom: 1px solid #e2e8f0;
}
.pricing-compare-table td {
    padding: 13px 16px;
    font-size: 13.5px;
    font-weight: 500;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.pricing-compare-table tr:hover td {
    background-color: #f8fafc;
}
.accordion-button:not(.collapsed) {
    background-color: #f8fafc !important;
    color: #002347 !important;
}
</style>

<!-- Upper Header Section (Styled cleanly after user_review2.blade.php) -->
<section class="pricing-hero-sec help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 business_reviews_sec all-com">
    <div class="container">
        <!-- Breadcrumbs & Social Share Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom: 0;">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/' . $langCode) }}" style="color: #64748b; text-decoration: none;">Home</a>
                    </li>
                    @if($parentCatName)
                        <li class="breadcrumb-item">
                            @if($parentCatSlug)
                                <a href="{{ route('category.detail', ['locale' => $langCode, 'slug' => $parentCatSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $parentCatName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $parentCatName }}</span>
                            @endif
                        </li>
                    @endif
                    @if($catName)
                        <li class="breadcrumb-item">
                            @if($catSlug)
                                <a href="{{ route('category.detail', ['locale' => $langCode, 'slug' => $catSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $catName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $catName }}</span>
                            @endif
                        </li>
                    @endif
                    <li class="breadcrumb-item">
                        <a href="{{ route('user.product_detail', ['locale' => $langCode, 'id' => $businessSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $bName }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #002347; font-weight: 500;">
                        Plans & pricing
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon :business="$business" />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <div class="top-product-logo">
                        <x-business-logo :business="$business" :name="$bName" />
                    </div>
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="margin: 0; font-size: 26px; font-weight: 700; color: #002347;">
                                {!! \App\Services\ContentTemplateService::cleanHeading($pricingH1) !!}
                            </h1>
                        </div>
                        @if(!empty($pricingSubtext))
                        <div class="text-meta" style="margin-top: 5px; font-size: 14.5px; color: #475569;">
                            {!! $pricingSubtext !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(!empty($business->is_affiliate) || !empty($business->website_url))
            <div class="col-md-4 col-12 text-md-end text-end is_affiliate_btn mt-3 mt-md-0">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" rel="sponsored" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border-radius: 8px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Main Page Content Section -->
<section class="py-5" style="background-color: #f8fafc; border-top: 1px solid #eef2f6; min-height: 600px;">
    <div class="container">

        @if($offerProducts->isNotEmpty())

            @foreach($offerProducts as $pIdx => $prod)
                @php
                    $isActiveProduct = $activeProduct && $activeProduct->id === $prod->id;
                    $plans = $prod->plans;
                    $totalPlansCount = $plans->count();

                    // Map all features across plans of this product
                    $productFeatures = [];
                    foreach ($plans as $pl) {
                        foreach ($pl->planFeatures as $pf) {
                            $fId = $pf->feature_id;
                            if (!isset($productFeatures[$fId])) {
                                $feat = $pf->feature;
                                $ft = $feat ? ($feat->translations->firstWhere('lang_id', $lang_id) ?? $feat->translations->first()) : null;
                                $fName = $ft?->name ?? $feat?->name ?? "Feature #{$fId}";
                                $fSlug = Str::slug($fName);
                                $productFeatures[$fId] = [
                                    'id' => $fId,
                                    'name' => $fName,
                                    'url' => url("/{$langCode}/" . ($parentCatSlug ?: 'hosting') . "/{$catSlug}/{$fSlug}"),
                                    'is_highlight' => false,
                                    'plans' => [],
                                    'plan_matrix' => [],
                                ];
                            }
                            if ($pf->is_featured_highlight) {
                                $productFeatures[$fId]['is_highlight'] = true;
                            }
                            if (!in_array($pl->id, $productFeatures[$fId]['plans'])) {
                                $productFeatures[$fId]['plans'][] = $pl->id;
                            }
                            $productFeatures[$fId]['plan_matrix'][$pl->id] = true;
                        }
                    }

                    // Format helpers for regular and renewal prices
                    $getPlanPrices = function($plan) {
                        $p1m = $plan->prices->firstWhere('billing_period', '1m');
                        $p12m = $plan->prices->firstWhere('billing_period', '12m') ?? $plan->prices->first();

                        $formatAmount = function($priceObj, $isRenewal = false) {
                            if (!$priceObj) return '—';
                            if (!$isRenewal && $priceObj->price_model === 'free') return 'Free';
                            $val = $isRenewal ? $priceObj->renewal_price : $priceObj->regular_price;
                            if ($val === null || $val === '') return '—';
                            $symbol = match ($priceObj->currency) {
                                'USD' => '$',
                                'EUR' => '€',
                                'GBP' => '£',
                                default => ($priceObj->currency ? $priceObj->currency . ' ' : '$'),
                            };
                            return $symbol . number_format((float)$val, 2);
                        };

                        $mReg = $formatAmount($p1m);
                        $yReg = $formatAmount($p12m);
                        if ($yReg === '—' && $mReg !== '—') $yReg = $mReg;
                        if ($mReg === '—' && $yReg !== '—') $mReg = $yReg;

                        $mRen = $formatAmount($p1m, true);
                        $yRen = $formatAmount($p12m, true);
                        if ($yRen === '—' && $mRen !== '—') $yRen = $mRen;
                        if ($mRen === '—' && $yRen !== '—') $mRen = $yRen;

                        return [
                            'monthly_reg' => $mReg,
                            'yearly_reg' => $yReg,
                            'monthly_ren' => ($mRen !== '—' ? $mRen . ' / mo' : '—'),
                            'yearly_ren' => ($yRen !== '—' ? $yRen . ' / mo' : '—'),
                        ];
                    };
                @endphp

                <div class="product-pricing-pane" id="productPricingPane{{ $prod->id }}" style="{{ $isActiveProduct ? '' : 'display: none;' }}">
                    
                    <!-- BOX 1: Plans Box (Image 1) -->
                    <div class="pricing-container-card mb-4">
                        
                        <!-- Header -->
                        <h2 style="font-weight: 700; font-size: 24px; color: #0f172a; margin-bottom: 20px; letter-spacing: -0.01em;">
                            Plans &amp; pricing
                        </h2>

                        <!-- Product Tabs (Image 1: Shared hosting, WordPress hosting, VPS hosting) -->
                        @if($offerProducts->count() > 1)
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-4" id="pricingProductTabs">
                            @foreach($offerProducts as $pillProd)
                                @php $isTabActive = $pillProd->id === $prod->id; @endphp
                                <button type="button" 
                                        class="btn btn-sm pricing-product-tab {{ $isTabActive ? 'active' : '' }}" 
                                        onclick="switchProductPricing({{ $pillProd->id }})">
                                    {{ $pillProd->localized_name ?: $pillProd->name }}
                                </button>
                            @endforeach
                        </div>
                        @endif

                        <!-- Subheader Row: Country/Region on Left, Monthly/Yearly on Right (Image 1) -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-4 border-bottom" style="border-color: #f1f5f9 !important;">
                            <!-- Left: Country / Region -->
                            <div>
                                <div style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 2px;">
                                    Country / region
                                </div>
                                <div class="d-flex align-items-center gap-2" style="font-weight: 700; font-size: 14.5px; color: #0f172a;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748b;">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                    <span>United States · USD</span>
                                </div>
                            </div>

                            <!-- Right: Monthly / Yearly Cycle Switch (Image 1) -->
                            <div class="pricing-cycle-wrapper" data-product-id="{{ $prod->id }}">
                                <button type="button" class="pricing-cycle-btn cycle-monthly" 
                                        data-cycle="monthly" 
                                        onclick="setPricingCycle({{ $prod->id }}, 'monthly')">
                                    Monthly
                                </button>
                                <button type="button" class="pricing-cycle-btn cycle-yearly active" 
                                        data-cycle="yearly" 
                                        onclick="setPricingCycle({{ $prod->id }}, 'yearly')">
                                    Yearly
                                </button>
                            </div>
                        </div>

                        <!-- Plan Cards Grid (Image 1) -->
                        @if($plans->isNotEmpty())
                            <div class="row g-3 align-items-stretch">
                                @foreach($plans as $plan)
                                    @php
                                        $prices = $getPlanPrices($plan);

                                        // Retrieve 4-6 key features (highlights first, fallback to first features)
                                        $highlights = $plan->planFeatures->where('is_featured_highlight', true);
                                        if ($highlights->count() < 4) {
                                            $remaining = $plan->planFeatures->where('is_featured_highlight', false);
                                            $keyPlanFeatures = $highlights->concat($remaining)->take(6);
                                        } else {
                                            $keyPlanFeatures = $highlights->take(6);
                                        }

                                        $planCheckoutUrl = $business->getTrackedUrl();
                                    @endphp

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="plan-col-card">
                                            
                                            <!-- Top details -->
                                            <div>
                                                <!-- Plan Title -->
                                                <h3 class="plan-name-title">
                                                    {{ $plan->localized_name ?: $plan->name }}
                                                </h3>

                                                <!-- Short Description -->
                                                <div class="plan-short-desc">
                                                    {{ $plan->localized_short_description ?: ($plan->short_description ?? '') }}
                                                </div>

                                                <!-- Price Block (Image 1) -->
                                                <div class="plan-price-block mb-3">
                                                    <div class="plan-price-amount">
                                                        <span class="plan-price-val" 
                                                              data-monthly="{{ $prices['monthly_reg'] }}" 
                                                              data-yearly="{{ $prices['yearly_reg'] }}">
                                                            {{ $prices['yearly_reg'] }}
                                                        </span>
                                                    </div>
                                                    <div class="plan-price-subtext">
                                                        Yearly price
                                                    </div>
                                                </div>

                                                <hr style="margin: 16px 0; border: 0; border-top: 1px solid #f1f5f9;">

                                                <!-- Key Features (Image 1) -->
                                                <div style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 12px;">
                                                    Key features
                                                </div>
                                                <div class="plan-key-features-list mb-3">
                                                    @foreach($keyPlanFeatures as $kpf)
                                                        @php
                                                            $kf = $kpf->feature;
                                                            $kt = $kf ? ($kf->translations->firstWhere('lang_id', $lang_id) ?? $kf->translations->first()) : null;
                                                            $kfName = $kt?->name ?? $kf?->name ?? 'Feature #' . $kpf->feature_id;
                                                        @endphp
                                                        <div class="d-flex align-items-start gap-2 mb-2" style="font-size: 13.5px; color: #334155; line-height: 1.45;">
                                                            <span style="color: #10b981; font-weight: 700; font-size: 14px; margin-top: 1px; flex-shrink: 0;">✓</span>
                                                            <span>{{ $kfName }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Bottom Renewal & Link (Image 1) -->
                                            <div>
                                                <hr style="margin: 16px 0; border: 0; border-top: 1px solid #f1f5f9;">
                                                <div class="d-flex align-items-end justify-content-between gap-2">
                                                    <div>
                                                        <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">
                                                            Renewal price
                                                        </div>
                                                        <div class="plan-renewal-val" style="font-weight: 600; font-size: 14.5px; color: #0f172a;"
                                                             data-monthly="{{ $prices['monthly_ren'] }}"
                                                             data-yearly="{{ $prices['yearly_ren'] }}">
                                                            {{ $prices['yearly_ren'] }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <a href="{{ $planCheckoutUrl }}" target="_blank" rel="nofollow sponsored" class="plan-link-btn">
                                                            <span>View plan</span>
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M7 17L17 7M17 7H7M17 7V17"></path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-5 text-center text-muted" style="font-size: 14.5px;">
                                Plans and pricing details for {{ $prod->localized_name ?: $prod->name }} will be available soon.
                            </div>
                        @endif

                    </div>

                    <!-- BOX 2: Plan Feature Comparison Table (Image 2) -->
                    @if(count($productFeatures) > 0 && $plans->isNotEmpty())
                    <div class="pricing-container-card mb-5">
                        <div class="table-responsive">
                            <table class="table pricing-compare-table mb-0" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <th style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e2e8f0; text-align: left; width: 45%;">
                                            Feature
                                        </th>
                                        @foreach($plans as $pl)
                                            <th class="text-center" style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e2e8f0; width: {{ round(55 / max(1, $totalPlansCount)) }}%;">
                                                {{ $pl->localized_name ?: $pl->name }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productFeatures as $feat)
                                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s ease;">
                                            <td style="padding: 13px 16px; font-size: 13.5px; font-weight: 500; color: #1e293b; vertical-align: middle;">
                                                <a href="{{ $feat['url'] }}" class="text-decoration-none" style="color: inherit;">
                                                    {{ $feat['name'] }}
                                                </a>
                                            </td>
                                            @foreach($plans as $pl)
                                                @php $hasFeat = !empty($feat['plan_matrix'][$pl->id]); @endphp
                                                <td class="text-center" style="padding: 13px 16px; vertical-align: middle;">
                                                    @if($hasFeat)
                                                        <span style="color: #10b981; font-weight: 700; font-size: 16px;">✓</span>
                                                    @else
                                                        <span style="color: #94a3b8; font-size: 16px;">—</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>
            @endforeach

        @else
            <!-- Fallback if business has no offer products -->
            <div class="pricing-container-card text-center py-5 mb-5">
                <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">
                    Plans &amp; pricing information
                </h3>
                <p class="text-muted" style="font-size: 14.5px; max-width: 600px; margin: 0 auto;">
                    Detailed plan tiers and pricing matrices for {{ $bName }} are being updated. Check back soon or visit their official website below.
                </p>
                @if(!empty($business->website_url))
                <div class="mt-4">
                    <a href="{{ $business->getTrackedUrl() }}" target="_blank" rel="sponsored" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                        Visit {{ $bName }} website
                    </a>
                </div>
                @endif
            </div>
        @endif

        <!-- SECTION 3: Frequently Asked Questions on bottom (Image 2 bottom & prompt) -->
        @if($pricingFaqs && $pricingFaqs->count() > 0)
        <div class="pricing-faq-section mt-5 pt-2">
            <div class="mb-4">
                <h2 style="font-weight: 700; font-size: 24px; color: #002347; margin-bottom: 8px;">
                    Frequently asked questions
                </h2>
                <p style="font-size: 14.5px; color: #64748b; margin-bottom: 0;">
                    Common questions regarding {{ $bName }} plans, pricing models, renewals, and features.
                </p>
            </div>

            @php
                $currentLangObj = \App\Models\Language::where('lang_code', $langCode)->first();
                $faqSlugVal = !empty($currentLangObj->faq_slug) ? $currentLangObj->faq_slug : 'faqs';
            @endphp
            <div class="accordion" id="businessPricingFaqAccordion">
                @foreach($pricingFaqs as $faqIdx => $faq)
                    @php
                        $trans = $faq->translations->firstWhere('lang_id', $lang_id) ?? $faq->translations->first();
                        $qText = $trans?->question ?? 'Question';
                        $aText = $trans?->answer ?? 'Answer';
                        $isFirst = $faqIdx === 0;
                    @endphp
                    <div class="accordion-item mb-3" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                        <h3 class="accordion-header" id="faqPricingHeading{{ $faq->id }}">
                            <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }}"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#faqPricingCollapse{{ $faq->id }}"
                                aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                aria-controls="faqPricingCollapse{{ $faq->id }}"
                                style="font-weight: 600; font-size: 15.5px; color: #002347; background-color: #ffffff; padding: 16px 20px;">
                                <span>{{ $qText }}</span>
                            </button>
                        </h3>
                        <div id="faqPricingCollapse{{ $faq->id }}"
                            class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}" role="region"
                            aria-labelledby="faqPricingHeading{{ $faq->id }}"
                            data-bs-parent="#businessPricingFaqAccordion">
                            <div class="accordion-body rich-text-content" style="background-color: #ffffff; font-size: 14.5px; color: #475569; line-height: 1.65; padding: 0 20px 20px;">
                                <div class="faq-answer-text mb-3">
                                    {!! nl2br(e(strip_tags($aText))) !!}
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top" style="border-color: #f1f5f9 !important;">
                                    <div class="d-flex align-items-center gap-2" style="font-size: 13px; color: #64748b;">
                                        <span>Was this helpful?</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-sm" onclick="votePricingFaqHelpful({{ $faq->id }}, 1, this)" style="border-radius: 6px; padding: 4px 12px; font-size: 12.5px; border: 1px solid #cbd5e1; background: #fff; color: #475569;">
                                                <i class="far fa-thumbs-up me-1"></i> Yes
                                            </button>
                                            <button type="button" class="btn btn-sm" onclick="votePricingFaqHelpful({{ $faq->id }}, 0, this)" style="border-radius: 6px; padding: 4px 12px; font-size: 12.5px; border: 1px solid #cbd5e1; background: #fff; color: #475569;">
                                                <i class="far fa-thumbs-down me-1"></i> No
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4 pt-2">
                <a href="{{ route('business.all_faqs', ['locale' => $langCode, 'business_slug' => $businessSlug, 'faq_slug' => $faqSlugVal]) }}" 
                   class="btn" 
                   style="border-radius: 9999px; border: 1px solid #cbd5e1; font-size: 13.5px; font-weight: 500; color: #0f172a; padding: 8px 22px; background: #ffffff;">
                    View all {{ $bName }} FAQs &rarr;
                </a>
            </div>
        </div>
        @endif

    </div>
</section>

<script>
// Switch between offer products tabs (e.g. Shared hosting, WordPress hosting, VPS hosting)
function switchProductPricing(productId) {
    document.querySelectorAll('.product-pricing-pane').forEach(pane => {
        pane.style.display = 'none';
    });
    const targetPane = document.getElementById('productPricingPane' + productId);
    if (targetPane) {
        targetPane.style.display = 'block';
    }

    // Update active tab buttons in all tab bars
    document.querySelectorAll('#pricingProductTabs .pricing-product-tab').forEach(tab => {
        tab.classList.remove('active');
        tab.style.background = '#ffffff';
        tab.style.color = '#475569';
        tab.style.borderColor = '#e2e8f0';
        tab.style.fontWeight = '500';
    });
    document.querySelectorAll('#pricingProductTabs .pricing-product-tab').forEach(tab => {
        if (tab.getAttribute('onclick') && tab.getAttribute('onclick').includes(productId)) {
            tab.classList.add('active');
            tab.style.background = '#0f172a';
            tab.style.color = '#ffffff';
            tab.style.borderColor = '#0f172a';
            tab.style.fontWeight = '600';
        }
    });
}

// Monthly / Yearly billing cycle toggle
function setPricingCycle(productId, cycle) {
    const pane = document.getElementById('productPricingPane' + productId);
    if (!pane) return;

    // Toggle button active states
    const wrapper = pane.querySelector('.pricing-cycle-wrapper');
    if (wrapper) {
        wrapper.querySelectorAll('.pricing-cycle-btn').forEach(btn => {
            if (btn.getAttribute('data-cycle') === cycle) {
                btn.classList.add('active');
                btn.style.background = '#ffffff';
                btn.style.color = '#0f172a';
                btn.style.fontWeight = '600';
                btn.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.08)';
            } else {
                btn.classList.remove('active');
                btn.style.background = 'transparent';
                btn.style.color = '#64748b';
                btn.style.fontWeight = '500';
                btn.style.boxShadow = 'none';
            }
        });
    }

    // Update plan price values
    pane.querySelectorAll('.plan-price-val').forEach(el => {
        const val = el.getAttribute('data-' + cycle);
        if (val) {
            el.textContent = val;
        }
    });

    // Update price subtext (Yearly price vs Monthly price)
    pane.querySelectorAll('.plan-price-subtext').forEach(el => {
        el.textContent = (cycle === 'yearly') ? 'Yearly price' : 'Monthly price';
    });

    // Update renewal prices
    pane.querySelectorAll('.plan-renewal-val').forEach(el => {
        const renVal = el.getAttribute('data-' + cycle);
        if (renVal) {
            el.textContent = renVal;
        }
    });
}

// Helpful vote AJAX
function votePricingFaqHelpful(faqId, isHelpful, btnEl) {
    fetch("{{ route('business.faq.vote') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            business_faq_id: faqId,
            is_helpful: isHelpful
        })
    }).then(res => res.json()).then(data => {
        const container = btnEl.closest('.d-flex.align-items-center.gap-2');
        if (container) {
            container.innerHTML = '<span class="text-success" style="font-size: 13px;"><i class="fas fa-check me-1"></i> Thank you for your feedback!</span>';
        }
    }).catch(err => {
        console.error(err);
    });
}
</script>

@endsection
