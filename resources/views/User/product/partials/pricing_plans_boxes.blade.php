@php
    $offerProducts = $business->offerProducts()
        ->with([
            'plans.prices',
            'plans.featuredHighlights.feature',
            'plans.planFeatures.feature',
            'plans.specifications',
            'directPrices',
            'directFeatures.feature'
        ])
        ->where('status', 'public')
        ->orderBy('sort_order', 'asc')
        ->get();
@endphp

@if($offerProducts->isNotEmpty())
<style>
.localio-plans-container {
    background: #ffffff;
    border: 1px solid #eaecf0;
    border-radius: 16px;
    padding: 24px;
    margin-top: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
}
.plans-category-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 22px;
}
.plans-tab-pill {
    padding: 7px 18px;
    font-size: 13.5px;
    font-weight: 500;
    border-radius: 9999px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
    line-height: 1.4;
}
.plans-tab-pill:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
}
.plans-tab-pill.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    font-weight: 600;
}
.plans-boxes-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
@media (max-width: 991px) {
    .plans-boxes-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 640px) {
    .plans-boxes-grid {
        grid-template-columns: 1fr;
    }
}
.plan-card-item {
    background: #ffffff;
    border: 1px solid #eaecf0;
    border-radius: 12px;
    padding: 22px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.plan-card-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
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
    margin-bottom: 14px;
}
.plan-divider {
    border-top: 1px solid #f1f5f9;
    margin: 0;
}
.plan-price-block {
    padding: 14px 0;
}
.plan-price-amount {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}
.plan-price-period {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
    margin-top: 3px;
}
.plan-features-block {
    padding: 14px 0;
    flex-grow: 1;
}
.plan-features-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 12px;
}
.plan-feature-row {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 9px;
    font-size: 13px;
    color: #334155;
    line-height: 1.45;
}
.plan-feature-icon {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    margin-top: 2.5px;
    color: #10b981;
}
.plan-renewal-block {
    padding: 12px 0 14px 0;
}
.plan-renewal-label {
    font-size: 12px;
    color: #64748b;
}
.plan-renewal-val {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    margin-top: 2px;
}
.plan-view-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    font-weight: 600;
    font-size: 13.5px;
    color: #0f172a;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 6px;
    transition: color 0.15s ease, background-color 0.15s ease;
    width: 100%;
}
.plan-view-link:hover {
    color: #2563eb;
    background-color: #f8fafc;
}
.compare-all-plans-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 6px;
}
.compare-all-plans-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    font-size: 13.5px;
    color: #475569;
    text-decoration: none;
    transition: color 0.15s ease;
    padding: 4px 8px;
}
.compare-all-plans-link:hover {
    color: #0f172a;
}
</style>

<div class="col-lg-12 localio-plans-container" id="section-plans-pricing">
    
    {{-- 1. Product Category Tabs (shown when multiple products exist) --}}
    @if($offerProducts->count() > 1)
    <div class="plans-category-tabs">
        @foreach($offerProducts as $idx => $prod)
            <button type="button" 
                    class="plans-tab-pill {{ $idx === 0 ? 'active' : '' }}" 
                    onclick="switchProductPlansTab({{ $prod->id }}, this)">
                {{ $prod->localized_name ?: $prod->name }}
            </button>
        @endforeach
    </div>
    @endif

    {{-- 2. Product Panes --}}
    @php
        $bizSlug = $business->translations->first()?->slug ?: ($business->id ?? '');
        $compareAllUrl = url(app()->getLocale() . '/' . $bizSlug . '/pricing');
        $viewPlanUrl = $business->is_affiliate ? $business->getTrackedUrl() : ($business->permanent_url ?: 'javascript:void(0)');
        $localeShort = strtolower(explode('-', app()->getLocale())[0]);
        $localeFull = strtolower(app()->getLocale());
    @endphp

    @foreach($offerProducts as $idx => $prod)
    <div class="product-plans-pane {{ $idx === 0 ? '' : 'd-none' }}" id="plans-pane-{{ $prod->id }}">
        
        @if($prod->plans->isNotEmpty())
            {{-- Plan Boxes Grid --}}
            <div class="plans-boxes-grid">
                @foreach($prod->plans as $plan)
                @php
                    // Determine localized price matching current region, default/all, or first active
                    $primaryPrice = $plan->prices->first(function($pr) use ($localeFull, $localeShort) {
                        if (!$pr->is_active) return false;
                        $mkt = strtolower($pr->country_region ?? '');
                        return $mkt === $localeFull || $mkt === $localeShort;
                    });
                    if (!$primaryPrice) {
                        $primaryPrice = $plan->prices->first(function($pr) {
                            if (!$pr->is_active) return false;
                            $mkt = strtolower($pr->country_region ?? '');
                            return $mkt === 'all' || $mkt === 'default' || $mkt === '';
                        });
                    }
                    if (!$primaryPrice) {
                        $primaryPrice = $plan->prices->where('is_active', true)->first();
                    }

                    // Format Price & Billing Period
                    $priceAmountDisplay = '—';
                    $pricePeriodDisplay = 'Price / billing period';
                    if ($primaryPrice) {
                        if ($primaryPrice->price_model === 'free' || ((float)$primaryPrice->regular_price === 0.0 && $primaryPrice->regular_price !== null)) {
                            $priceAmountDisplay = 'Free';
                            $pricePeriodDisplay = $primaryPrice->billing_period_label ? ('/ ' . $primaryPrice->billing_period_label) : '';
                        } elseif ($primaryPrice->price_model === 'quote') {
                            $priceAmountDisplay = 'Contact for quote';
                            $pricePeriodDisplay = '';
                        } else {
                            $priceAmountDisplay = $primaryPrice->formatted_price;
                            $pricePeriodDisplay = '/ ' . ($primaryPrice->billing_period_label ?: 'month');
                        }
                    }

                    // Format Renewal Price
                    $renewalPriceDisplay = $primaryPrice?->formatted_renewal_price ?: '—';

                    // Collect 4-6 Key Features
                    $highlightFeatures = $plan->featuredHighlights;
                    $highlightIds = $highlightFeatures->pluck('feature_id')->toArray();
                    $remainingSlots = max(0, 6 - $highlightFeatures->count());
                    $extraFeatures = $plan->planFeatures->whereNotIn('feature_id', $highlightIds)->take($remainingSlots);
                    $displayFeatures = $highlightFeatures->concat($extraFeatures)->take(6);
                @endphp

                <div class="plan-card-item">
                    {{-- Plan Name & Short Description --}}
                    <div>
                        <h3 class="plan-name-title">{{ $plan->localized_name ?: $plan->name }}</h3>
                        <p class="plan-short-desc">{{ $plan->short_description ?: '—' }}</p>
                    </div>

                    <div class="plan-divider"></div>

                    {{-- Price / Billing Period --}}
                    <div class="plan-price-block">
                        <div class="plan-price-amount">{{ $priceAmountDisplay }}</div>
                        <div class="plan-price-period">{{ $pricePeriodDisplay }}</div>
                    </div>

                    <div class="plan-divider"></div>

                    {{-- Key Features (4-6 verified items with checkmarks) --}}
                    <div class="plan-features-block">
                        <div class="plan-features-title">Key features</div>
                        @if($displayFeatures->isNotEmpty())
                            @foreach($displayFeatures as $feat)
                            <div class="plan-feature-row">
                                <svg class="plan-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ $feat->feature_name }}</span>
                            </div>
                            @endforeach
                        @else
                            <div class="text-muted small">Standard business features included</div>
                        @endif
                    </div>

                    <div class="plan-divider"></div>

                    {{-- Renewal Price --}}
                    <div class="plan-renewal-block">
                        <div class="plan-renewal-label">Renewal price</div>
                        <div class="plan-renewal-val">{{ $renewalPriceDisplay }}</div>
                    </div>

                    {{-- View Plan Button --}}
                    <div>
                        <a href="{{ $viewPlanUrl }}" target="_blank" @if($business->is_affiliate) rel="sponsored noopener" @else rel="noopener noreferrer" @endif class="plan-view-link">
                            <span>View plan</span>
                            <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Compare All Plans Footer --}}
            <div class="compare-all-plans-footer">
                <a href="{{ $compareAllUrl }}" class="compare-all-plans-link">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    <span>Compare all plans →</span>
                </a>
            </div>

        @elseif($prod->directPrices->isNotEmpty() || $prod->directFeatures->isNotEmpty())
            {{-- Standalone Product (Non-Plan) --}}
            @php
                $dPrice = $prod->directPrices->first();
                $dFeatures = $prod->directFeatures->take(6);
            @endphp
            <div class="plans-boxes-grid" style="grid-template-columns: minmax(280px, 420px);">
                <div class="plan-card-item">
                    <div>
                        <h3 class="plan-name-title">{{ $prod->localized_name ?: $prod->name }}</h3>
                        <p class="plan-short-desc">{{ $prod->description ?: 'Standalone product offering.' }}</p>
                    </div>

                    <div class="plan-divider"></div>

                    <div class="plan-price-block">
                        <div class="plan-price-amount">
                            {{ $dPrice ? $dPrice->formatted_price : '—' }}
                        </div>
                        <div class="plan-price-period">
                            {{ $dPrice ? ('/ ' . ($dPrice->billing_period_label ?: 'one-time')) : 'Price / billing period' }}
                        </div>
                    </div>

                    <div class="plan-divider"></div>

                    <div class="plan-features-block">
                        <div class="plan-features-title">Key features</div>
                        @if($dFeatures->isNotEmpty())
                            @foreach($dFeatures as $feat)
                            <div class="plan-feature-row">
                                <svg class="plan-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ $feat->feature_name }}</span>
                            </div>
                            @endforeach
                        @else
                            <div class="text-muted small">Standard product features included</div>
                        @endif
                    </div>

                    <div class="plan-divider"></div>

                    <div class="plan-renewal-block">
                        <div class="plan-renewal-label">Renewal price</div>
                        <div class="plan-renewal-val">—</div>
                    </div>

                    <div>
                        <a href="{{ $viewPlanUrl }}" target="_blank" @if($business->is_affiliate) rel="sponsored noopener" @else rel="noopener noreferrer" @endif class="plan-view-link">
                            <span>Get product</span>
                            <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="compare-all-plans-footer">
                <a href="{{ $compareAllUrl }}" class="compare-all-plans-link">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    <span>Compare all plans →</span>
                </a>
            </div>

        @else
            {{-- Category with plans coming soon --}}
            <div class="p-4 text-center" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px;">
                <p class="mb-1 font-weight-bold" style="font-size: 15px; color: #1e293b;">
                    {{ $prod->localized_name ?: $prod->name }} plans
                </p>
                <p class="small text-muted mb-3">
                    Plans and pricing for this category will be available soon.
                </p>
                <a href="{{ $viewPlanUrl }}" target="_blank" @if($business->is_affiliate) rel="sponsored noopener" @else rel="noopener noreferrer" @endif class="btn btn-sm btn-outline-dark" style="border-radius: 6px; font-weight: 500;">
                    Visit official website ↗
                </a>
            </div>
        @endif

    </div>
    @endforeach
</div>

<script>
function switchProductPlansTab(productId, tabBtn) {
    document.querySelectorAll('.plans-tab-pill').forEach(btn => btn.classList.remove('active'));
    tabBtn.classList.add('active');

    document.querySelectorAll('.product-plans-pane').forEach(pane => pane.classList.add('d-none'));
    const targetPane = document.getElementById('plans-pane-' + productId);
    if (targetPane) {
        targetPane.classList.remove('d-none');
    }
}
</script>
@endif
