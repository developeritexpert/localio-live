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

    $activeReviews = $business->reviews->whereIn('status', ['published', 'active']);
    $ratingCount = $activeReviews->count();
    $averageRating = $ratingCount >= 10 ? round($activeReviews->avg('rating'), 1) : ($business->admin_rating ?? 4.5);

    // Filter offer products with plans
    $productsList = $business->offerProducts()
        ->where('status', 'public')
        ->whereHas('plans')
        ->with([
            'plans' => function($q) {
                $q->where('status', 'public')->orderBy('sort_order', 'asc');
            },
            'plans.prices' => function($q) {
                $q->where('is_active', true);
            },
            'plans.planFeatures.feature.translations'
        ])
        ->orderBy('sort_order', 'asc')
        ->get();

    $firstProduct = $productsList->first();
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
.pricing-product-pill {
    border-radius: 9999px;
    padding: 8px 22px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
}
.pricing-product-pill.active {
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
    font-weight: 600;
}
.pricing-product-pill:hover:not(.active) {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.cycle-toggle-wrapper {
    background: #f1f5f9;
    padding: 3px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
}
.cycle-btn {
    border: none;
    background: transparent;
    border-radius: 9999px;
    padding: 5px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}
.cycle-btn.active {
    background: #ffffff;
    color: #0f172a;
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
.plan-col-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 26px 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.plan-col-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}
.plan-col-card.is-featured {
    border-color: #0f172a;
    position: relative;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
}
.plan-featured-badge {
    position: absolute;
    top: -12px;
    right: 20px;
    background: #0f172a;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 3px 12px;
    border-radius: 9999px;
}
.plan-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    font-size: 15px;
    color: #0f172a;
    transition: color 0.15s ease, transform 0.15s ease;
}
.plan-cta-btn:hover {
    color: #2563eb;
    transform: translateX(2px);
}
.compare-matrix-table th {
    padding: 14px 16px;
    font-size: 14.5px;
    font-weight: 600;
    color: #0f172a;
    border-bottom: 2px solid #e2e8f0;
}
.compare-matrix-table td {
    padding: 14px 16px;
    font-size: 14px;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
}
.compare-matrix-table tr:hover td {
    background-color: #f8fafc;
}
.pricing-faq-item {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 12px;
    background: #ffffff;
    overflow: hidden;
    transition: border-color 0.15s ease;
}
.pricing-faq-item:hover {
    border-color: #cbd5e1;
}
.pricing-faq-question {
    padding: 16px 20px;
    font-weight: 600;
    font-size: 15px;
    color: #0f172a;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    user-select: none;
}
.pricing-faq-answer {
    padding: 0 20px 16px;
    font-size: 14px;
    color: #475569;
    line-height: 1.6;
    display: none;
}
.pricing-faq-item.active .pricing-faq-answer {
    display: block;
}
.pricing-faq-item.active .faq-chevron {
    transform: rotate(180deg);
}
.faq-chevron {
    transition: transform 0.2s ease;
    color: #94a3b8;
    font-size: 13px;
}
</style>

<!-- Upper Header Section (Styled after user_review2.blade.php) -->
<section class="pricing-hero-sec help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 business_reviews_sec all-com">
    <div class="container">
        <!-- Breadcrumbs & Share Row -->
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
                        <h1 style="margin: 0; font-weight: 700; font-size: 28px; color: #002347;">
                            {!! \App\Services\ContentTemplateService::cleanHeading($pricingH1) !!}
                        </h1>
                        @if(!empty($pricingSubtext))
                        <div class="text-meta" style="margin-top: 6px; font-size: 15px; color: #475569;">
                            {!! $pricingSubtext !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(!empty($business->is_affiliate))
            <div class="col-md-4 col-12 text-md-end text-end is_affiliate_btn mt-3 mt-md-0">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" rel="sponsored" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border-radius: 8px;" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    {{ static_text('visit_website') ?: 'Visit website' }} <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Main Page Content Section -->
<section class="py-5" style="background-color: #f8fafc; border-top: 1px solid #eef2f6;">
    <div class="container">

        @if($productsList->count() > 0)

            @foreach($productsList as $pIndex => $product)
            @php
                $isFirstProduct = $pIndex === 0;
                $plans = $product->plans;
                $totalPlans = $plans->count();

                // Mapped features for this product
                $pFeatures = [];
                foreach ($plans as $pl) {
                    foreach ($pl->planFeatures as $pf) {
                        $fId = $pf->feature_id;
                        if (!isset($pFeatures[$fId])) {
                            $featModel = $pf->feature;
                            $ft = $featModel ? ($featModel->translations->firstWhere('lang_id', $lang_id) ?? $featModel->translations->first()) : null;
                            $fName = $ft?->name ?? $featModel?->name ?? "Feature #{$fId}";
                            $fSlug = Str::slug($fName);
                            $pFeatures[$fId] = [
                                'id' => $fId,
                                'name' => $fName,
                                'url' => url("/{$langCode}/" . ($parentCatSlug ?: 'hosting') . "/{$catSlug}/{$fSlug}"),
                                'is_highlight' => false,
                                'plan_matrix' => [],
                            ];
                        }
                        if ($pf->is_featured_highlight) {
                            $pFeatures[$fId]['is_highlight'] = true;
                        }
                        $pFeatures[$fId]['plan_matrix'][$pl->id] = true;
                    }
                }
            @endphp

            <div class="product-pricing-section" id="product-section-{{ $product->id }}" style="{{ $isFirstProduct ? '' : 'display: none;' }}">
                
                <!-- SECTION 1: Plans Box (Image 3) -->
                <div class="bg-white rounded-4 p-4 p-md-5 shadow-sm border mb-5" style="border-color: #e2e8f0 !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                        <h2 style="font-weight: 700; font-size: 24px; color: #002347; margin: 0;">
                            Plans & pricing
                        </h2>
                    </div>

                    <!-- Product Tabs (Image 3: Shared hosting, WordPress hosting, VPS hosting) -->
                    @if($productsList->count() > 1)
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                        @foreach($productsList as $prodPill)
                            @php $isPillActive = $prodPill->id === $product->id; @endphp
                            <button type="button" 
                                    class="pricing-product-pill {{ $isPillActive ? 'active' : '' }}" 
                                    onclick="switchProductSection({{ $prodPill->id }})">
                                {{ $prodPill->name }}
                            </button>
                        @endforeach
                    </div>
                    @endif

                    <!-- Location & Monthly/Yearly Toggle Row -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-4 border-bottom" style="border-color: #f1f5f9 !important;">
                        <!-- Left: Country / Region -->
                        <div>
                            <div style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 2px;">
                                Country / region
                            </div>
                            <div class="d-flex align-items-center gap-2" style="font-weight: 600; font-size: 14.5px; color: #0f172a;">
                                <span>🌐</span>
                                <span>United States · USD</span>
                            </div>
                        </div>

                        <!-- Right: Monthly / Yearly Cycle Switch -->
                        <div class="cycle-toggle-wrapper" data-product-id="{{ $product->id }}">
                            <button type="button" class="cycle-btn" data-cycle="monthly" onclick="setBillingCycle({{ $product->id }}, 'monthly')">
                                Monthly
                            </button>
                            <button type="button" class="cycle-btn active" data-cycle="yearly" onclick="setBillingCycle({{ $product->id }}, 'yearly')">
                                Yearly
                            </button>
                        </div>
                    </div>

                    <!-- Plan Cards 3-Column Grid -->
                    <div class="row g-4 align-items-stretch">
                        @foreach($plans as $plan)
                            @php
                                $priceMonthly = $plan->prices->firstWhere('billing_period', '1m');
                                $priceYearly = $plan->prices->firstWhere('billing_period', '12m') ?? $plan->prices->first();
                                
                                $valMonthly = $priceMonthly ? number_format($priceMonthly->regular_price, 2) : '—';
                                $valYearly = $priceYearly ? number_format($priceYearly->regular_price, 2) : ($valMonthly !== '—' ? $valMonthly : '—');
                                
                                $renMonthly = ($priceMonthly && $priceMonthly->renewal_price) ? number_format($priceMonthly->renewal_price, 2) : '—';
                                $renYearly = ($priceYearly && $priceYearly->renewal_price) ? number_format($priceYearly->renewal_price, 2) : ($renMonthly !== '—' ? $renMonthly : '—');

                                $planHighlights = $plan->planFeatures->where('is_featured_highlight', true);
                                $planCheckoutUrl = $business->getTrackedUrl();
                            @endphp

                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="plan-col-card {{ $plan->is_featured ? 'is-featured' : '' }}">
                                    @if($plan->is_featured)
                                        <div class="plan-featured-badge">Popular</div>
                                    @endif

                                    <!-- Top Plan Details -->
                                    <div>
                                        <h3 style="font-weight: 700; font-size: 20px; color: #0f172a; margin-bottom: 6px;">
                                            {{ $plan->name }}
                                        </h3>
                                        <p style="font-size: 13.5px; color: #64748b; line-height: 1.45; min-height: 40px; margin-bottom: 18px;">
                                            {{ $plan->short_description ?: 'Reliable hosting with key performance capabilities.' }}
                                        </p>

                                        <!-- Price -->
                                        <div class="mb-1">
                                            <div class="price-val-container" data-plan-id="{{ $plan->id }}">
                                                <span class="price-val" style="font-size: 32px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px;" 
                                                      data-monthly="{{ $valMonthly }}" 
                                                      data-yearly="{{ $valYearly }}">
                                                    {{ $valYearly !== '—' ? '$' . $valYearly : '—' }}
                                                </span>
                                                <span class="price-cycle-unit" style="font-size: 14px; color: #64748b; font-weight: 500;">
                                                    {{ $valYearly !== '—' ? '/ mo' : '' }}
                                                </span>
                                            </div>
                                            <div class="cycle-subtext" style="font-size: 12.5px; color: #64748b; margin-top: 3px;" data-plan-id="{{ $plan->id }}">
                                                Yearly price
                                            </div>
                                        </div>

                                        <hr style="margin: 20px 0; border-color: #f1f5f9;">

                                        <!-- Key Features Highlight -->
                                        <div style="font-weight: 600; font-size: 13.5px; color: #0f172a; margin-bottom: 14px;">
                                            Key features
                                        </div>

                                        <div class="plan-key-features-list mb-3">
                                            @forelse($planHighlights as $hl)
                                                @php
                                                    $hFeat = $hl->feature;
                                                    $ht = $hFeat ? ($hFeat->translations->firstWhere('lang_id', $lang_id) ?? $hFeat->translations->first()) : null;
                                                    $hlName = $ht?->name ?? $hFeat?->name ?? 'Feature #' . $hl->feature_id;
                                                @endphp
                                                <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 13.5px; color: #334155;">
                                                    <span style="color: #16a34a; font-weight: 700; font-size: 15px; line-height: 1;">✓</span>
                                                    <span>{{ $hlName }}</span>
                                                </div>
                                            @empty
                                                <!-- Fallback to first 4 features if none marked highlight -->
                                                @foreach($plan->planFeatures->take(4) as $pf)
                                                    @php
                                                        $hFeat = $pf->feature;
                                                        $ht = $hFeat ? ($hFeat->translations->firstWhere('lang_id', $lang_id) ?? $hFeat->translations->first()) : null;
                                                        $hlName = $ht?->name ?? $hFeat?->name ?? 'Feature #' . $pf->feature_id;
                                                    @endphp
                                                    <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 13.5px; color: #334155;">
                                                        <span style="color: #16a34a; font-weight: 700; font-size: 15px; line-height: 1;">✓</span>
                                                        <span>{{ $hlName }}</span>
                                                    </div>
                                                @endforeach
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- Bottom Renewal & CTA -->
                                    <div>
                                        <hr style="margin: 18px 0; border-color: #f1f5f9;">

                                        <div class="mb-3">
                                            <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">
                                                Renewal price
                                            </div>
                                            <div class="renewal-val" style="font-weight: 600; font-size: 14.5px; color: #0f172a;" 
                                                 data-plan-id="{{ $plan->id }}"
                                                 data-monthly="{{ $renMonthly }}"
                                                 data-yearly="{{ $renYearly }}">
                                                {{ $renYearly !== '—' ? '$' . $renYearly . ' / mo' : '—' }}
                                            </div>
                                        </div>

                                        <div class="text-end pt-1">
                                            <a href="{{ $planCheckoutUrl }}" target="_blank" rel="nofollow sponsored" class="plan-cta-btn text-decoration-none">
                                                <span>View plan</span>
                                                <i class="fas fa-arrow-up-right-from-square" style="font-size: 12px;"></i>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SECTION 2: Plan Feature Comparison Table (Image 4) -->
                @if(count($pFeatures) > 0)
                <div class="bg-white rounded-4 p-4 p-md-5 shadow-sm border mb-5" style="border-color: #e2e8f0 !important;">
                    <h3 style="font-weight: 700; font-size: 20px; color: #002347; margin-bottom: 20px;">
                        Compare {{ $product->name }} features by plan
                    </h3>

                    <div class="table-responsive">
                        <table class="table compare-matrix-table align-middle" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 45%;">Feature</th>
                                    @foreach($plans as $plan)
                                        <th scope="col" class="text-center">{{ $plan->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pFeatures as $feat)
                                    <tr>
                                        <td>
                                            <a href="{{ $feat['url'] }}" class="text-decoration-none text-dark hover-primary" style="transition: color 0.15s;">
                                                {{ $feat['name'] }}
                                            </a>
                                        </td>
                                        @foreach($plans as $plan)
                                            @php $isInc = !empty($feat['plan_matrix'][$plan->id]); @endphp
                                            <td class="text-center">
                                                @if($isInc)
                                                    <span style="color: #16a34a; font-weight: 700; font-size: 17px;">✓</span>
                                                @else
                                                    <span style="color: #94a3b8; font-size: 17px;">—</span>
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
            <!-- Fallback if no offer products exist yet -->
            <div class="bg-white rounded-4 p-5 text-center shadow-sm border mb-5" style="border-color: #e2e8f0;">
                <h3 style="font-size: 20px; font-weight: 600; color: #0f172a; margin-bottom: 8px;">Plans & pricing coming soon</h3>
                <p style="color: #64748b; font-size: 14px; max-width: 500px; margin: 0 auto 20px;">
                    We are currently cataloging all verified plans, features, and regional prices for {{ $bName }}. Check back soon or visit their website directly.
                </p>
                @if(!empty($business->is_affiliate))
                    <a href="{{ $business->getTrackedUrl() }}" target="_blank" rel="sponsored" class="btn btn-primary" style="border-radius: 8px; font-weight: 600; padding: 10px 24px;">
                        Visit {{ $bName }} <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                @endif
            </div>
        @endif

        <!-- SECTION 3: Bottom Pricing FAQs Accordion -->
        @if($pricingFaqs && $pricingFaqs->count() > 0)
        <div class="faq-pricing">
            <div class="mb-4">
                <h2 style="font-weight: 700; font-size: 24px; color: #002347; margin-bottom: 8px;">
                    Frequently asked questions
                </h2>
                <p style="font-size: 14.5px; color: #64748b; margin-bottom: 0;">
                    Common questions regarding {{ $bName }} plans, pricing models, renewals, and features.
                </p>
            </div>

            @php
                $currentLangObj = \App\Models\Language::where('lang_code', app()->getLocale())->first();
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
                    <div class="accordion-item mb-3" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden;">
                        <h3 class="accordion-header" id="faqPricingHeading{{ $faq->id }}">
                            <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }}"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#faqPricingCollapse{{ $faq->id }}"
                                aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                aria-controls="faqPricingCollapse{{ $faq->id }}"
                                style="font-weight: 600; font-size: 16px; color: #002347; background-color: #fff;">
                                <span>{{ $qText }}</span>
                            </button>
                        </h3>
                        <div id="faqPricingCollapse{{ $faq->id }}"
                            class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}" role="region"
                            aria-labelledby="faqPricingHeading{{ $faq->id }}"
                            data-bs-parent="#businessPricingFaqAccordion">
                            <div class="accordion-body rich-text-content" style="background-color: #fff; font-size: 14.5px; color: #475569; line-height: 1.65;">
                                <div class="faq-answer-text mb-3">
                                    {!! nl2br(e(strip_tags($aText))) !!}
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top" style="border-color: #f1f5f9 !important;">
                                    <div class="d-flex align-items-center gap-2" style="font-size: 13px; color: #64748b;">
                                        <span>Was this helpful?</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="voteFaqHelpful({{ $faq->id }}, 1, this)" style="border-radius: 6px; padding: 3px 10px; font-size: 12.5px; border-color: #cbd5e1; background: #fff; color: #475569;">
                                                <i class="far fa-thumbs-up me-1"></i> Yes
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="voteFaqHelpful({{ $faq->id }}, 0, this)" style="border-radius: 6px; padding: 3px 10px; font-size: 12.5px; border-color: #cbd5e1; background: #fff; color: #475569;">
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

            <div class="text-center mt-4">
                <a href="{{ route('business.all_faqs', ['locale' => $langCode, 'business_slug' => $businessSlug, 'faq_slug' => $faqSlugVal]) }}" class="btn-g-link">
                    View all {{ $bName }} FAQs
                </a>
            </div>
        </div>
        @endif

    </div>
</section>

<script>
// Switch between offer products (e.g. Shared hosting, WordPress hosting, VPS hosting)
function switchProductSection(productId) {
    document.querySelectorAll('.product-pricing-section').forEach(sec => {
        sec.style.display = 'none';
    });
    const target = document.getElementById('product-section-' + productId);
    if (target) {
        target.style.display = 'block';
    }

    // Update active pill in all product pill groups
    document.querySelectorAll('.pricing-product-pill').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelectorAll('.pricing-product-pill').forEach(btn => {
        if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(productId)) {
            btn.classList.add('active');
        }
    });
}

// Monthly / Yearly billing cycle toggle
function setBillingCycle(productId, cycle) {
    const sec = document.getElementById('product-section-' + productId);
    if (!sec) return;

    // Toggle button active states
    const toggleBtns = sec.querySelectorAll('.cycle-btn');
    toggleBtns.forEach(b => {
        if (b.getAttribute('data-cycle') === cycle) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });

    // Update prices in plan cards
    sec.querySelectorAll('.price-val').forEach(el => {
        const val = el.getAttribute('data-' + cycle);
        if (val && val !== '—') {
            el.textContent = '$' + val;
            const unit = el.nextElementSibling;
            if (unit) unit.textContent = ' / mo';
        } else {
            el.textContent = '—';
            const unit = el.nextElementSibling;
            if (unit) unit.textContent = '';
        }
    });

    // Update cycle subtext
    sec.querySelectorAll('.cycle-subtext').forEach(el => {
        el.textContent = (cycle === 'yearly') ? 'Yearly price' : 'Monthly price';
    });

    // Update renewal prices
    sec.querySelectorAll('.renewal-val').forEach(el => {
        const rVal = el.getAttribute('data-' + cycle);
        if (rVal && rVal !== '—') {
            el.textContent = '$' + rVal + ' / mo';
        } else {
            el.textContent = '—';
        }
    });
}

// Accordion toggle for FAQs
function togglePricingFaq(questionEl) {
    const item = questionEl.closest('.pricing-faq-item');
    if (!item) return;
    const isActive = item.classList.contains('active');
    
    // Optional: close other items
    document.querySelectorAll('.pricing-faq-item').forEach(it => {
        it.classList.remove('active');
    });

    if (!isActive) {
        item.classList.add('active');
    }
}

// Vote on FAQ helpfulness
function voteFaqHelpful(faqId, isHelpful, btnEl) {
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
        const container = btnEl.closest('div');
        if (container) {
            container.innerHTML = '<span class="text-success"><i class="fas fa-check me-1"></i> Thank you for your feedback!</span>';
        }
    }).catch(err => {
        console.error(err);
    });
}
</script>

@endsection
