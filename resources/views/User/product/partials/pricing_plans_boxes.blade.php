@php
    $offerProducts = $business->offerProducts()
        ->with([
            'plans.prices',
            'plans.featuredHighlights',
            'plans.planFeatures',
            'plans.specifications',
            'directPrices',
            'directFeatures'
        ])
        ->where('status', 'public')
        ->get();
@endphp

@if($offerProducts->isNotEmpty())
<style>
.localio-pricing-wrapper {
    margin-top: 2rem;
    margin-bottom: 2.5rem;
}
.product-tabs-nav {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.product-tab-pill {
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 99px;
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
}
.product-tab-pill:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}
.product-tab-pill.active {
    background: #111827;
    color: #ffffff;
    border-color: #111827;
}
.pricing-card-box {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.pricing-card-box:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
}
.plan-header-title {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 4px;
}
.plan-short-desc {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.4;
    min-height: 38px;
    margin-bottom: 12px;
}
.plan-price-wrap {
    margin: 14px 0;
}
.plan-price-main {
    font-size: 26px;
    font-weight: 800;
    color: #111827;
    line-height: 1.2;
}
.plan-price-period {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}
.plan-features-heading {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #4b5563;
    margin-bottom: 12px;
}
.plan-feature-item {
    font-size: 13.5px;
    color: #374151;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
}
.plan-feature-check {
    color: #10b981;
    font-weight: 800;
    font-size: 14px;
    flex-shrink: 0;
    line-height: 1.4;
}
.plan-renewal-box {
    border-top: 1px solid #f3f4f6;
    padding-top: 12px;
    margin-top: auto;
}
.plan-renewal-label {
    font-size: 12px;
    color: #6b7280;
}
.plan-renewal-val {
    font-size: 13px;
    font-weight: 600;
    color: #1f2937;
}
.btn-view-plan {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #111827;
    text-decoration: none;
    margin-top: 14px;
    transition: background 0.15s ease, border-color 0.15s ease;
}
.btn-view-plan:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #111827;
}
.btn-compare-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #4b5563;
    font-weight: 600;
    font-size: 13.5px;
    text-decoration: none;
    cursor: pointer;
    background: none;
    border: none;
    padding: 6px 12px;
    margin-top: 20px;
    transition: color 0.15s ease;
}
.btn-compare-all:hover {
    color: #111827;
}
</style>

<div class="col-lg-12 localio-pricing-wrapper" id="section-pricing">
    {{-- 1. Product Tabs (if more than 1 product) --}}
    @if($offerProducts->count() > 1)
    <div class="product-tabs-nav">
        @foreach($offerProducts as $idx => $prod)
            <button type="button" class="product-tab-pill {{ $idx === 0 ? 'active' : '' }}" onclick="switchProductTab({{ $prod->id }}, this)">
                {{ $prod->localized_name }}
            </button>
        @endforeach
    </div>
    @endif

    {{-- 2. Product Panes --}}
    @foreach($offerProducts as $idx => $prod)
    <div class="product-pricing-pane {{ $idx === 0 ? '' : 'd-none' }}" id="product-pane-{{ $prod->id }}">
        
        @if($prod->plans->isNotEmpty())
        {{-- Plan Cards Grid (3-column responsive) --}}
        <div class="row g-4">
            @foreach($prod->plans as $plan)
            @php
                $primaryPrice = $plan->primaryPrice;
                $highlights = $plan->featuredHighlights->isNotEmpty() ? $plan->featuredHighlights : $plan->planFeatures->take(5);
                $targetUrl = $business->affiliate_link ?: ($business->permanent_url ?: '#');
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card-box">
                    
                    {{-- Plan Name & Short Description --}}
                    <div>
                        <h3 class="plan-header-title">{{ $plan->localized_name }}</h3>
                        <p class="plan-short-desc">{{ $plan->short_description ?: '—' }}</p>
                    </div>

                    <div style="height: 1px; background: #f3f4f6; margin-bottom: 12px;"></div>

                    {{-- Price & Billing Period --}}
                    <div class="plan-price-wrap">
                        @if($primaryPrice)
                            @if($primaryPrice->price_model === 'free')
                                <div class="plan-price-main">Free</div>
                            @elseif($primaryPrice->price_model === 'quote')
                                <div class="plan-price-main" style="font-size: 20px;">Contact for quote</div>
                            @else
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="plan-price-main">{{ $primaryPrice->formatted_price }}</span>
                                    <span class="plan-price-period">/ {{ $primaryPrice->billing_period_label }}</span>
                                </div>
                            @endif
                        @else
                            <div class="plan-price-main text-muted" style="font-size: 20px;">—</div>
                            <div class="plan-price-period">Price / billing period</div>
                        @endif
                    </div>

                    {{-- Key Features List (4-6 verified items with checkmarks) --}}
                    <div class="mb-3">
                        <div class="plan-features-heading">Key features</div>
                        @if($highlights->isNotEmpty())
                            @foreach($highlights as $feat)
                            <div class="plan-feature-item">
                                <span class="plan-feature-check">?</span>
                                <span>{{ $feat->feature_name }}</span>
                            </div>
                            @endforeach
                        @else
                            <div class="text-muted small">Standard business features included</div>
                        @endif
                    </div>

                    {{-- Renewal Price --}}
                    <div class="plan-renewal-box">
                        <div class="plan-renewal-label">Renewal price</div>
                        <div class="plan-renewal-val">
                            {{ $primaryPrice?->formatted_renewal_price ?: '—' }}
                        </div>
                    </div>

                    {{-- CTA Button --}}
                    <a href="{{ $targetUrl }}" target="_blank" rel="nofollow noopener" class="btn-view-plan">
                        <span>View plan</span>
                        <span>?</span>
                    </a>

                </div>
            </div>
            @endforeach
        </div>

        {{-- Compare All Plans Button --}}
        <div class="text-center">
            <button type="button" class="btn-compare-all" data-bs-toggle="modal" data-bs-target="#compareModal-{{ $prod->id }}">
                <span>?</span>
                <span>Compare all plans ?</span>
            </button>
        </div>

        {{-- Compare Modal --}}
        <div class="modal fade" id="compareModal-{{ $prod->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">Compare all {{ $prod->localized_name }} plans</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" style="min-width: 650px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%;">Feature / Specification</th>
                                        @foreach($prod->plans as $p)
                                            <th class="text-center">
                                                <div class="font-weight-bold">{{ $p->localized_name }}</div>
                                                <div class="small text-muted">{{ $p->primaryPrice?->formatted_price ?: '' }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Specifications Rows --}}
                                    @php
                                        $allSpecCodes = $prod->plans->flatMap(fn($p) => $p->specifications->pluck('code'))->unique();
                                    @endphp
                                    @if($allSpecCodes->isNotEmpty())
                                    <tr class="table-secondary">
                                        <td colspan="{{ $prod->plans->count() + 1 }}" class="font-weight-bold small text-uppercase py-2">
                                            Technical Specifications
                                        </td>
                                    </tr>
                                    @foreach($allSpecCodes as $code)
                                    <tr>
                                        <td class="font-weight-bold text-capitalize">{{ str_replace('_', ' ', $code) }}</td>
                                        @foreach($prod->plans as $p)
                                            @php $sObj = $p->specifications->firstWhere('code', $code); @endphp
                                            <td class="text-center">
                                                {{ $sObj ? ($sObj->value . ' ' . ($sObj->unit ?: '')) : '—' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                    @endif

                                    {{-- Features Rows --}}
                                    @php
                                        $allFeatureIds = $prod->plans->flatMap(fn($p) => $p->planFeatures->pluck('feature_id'))->unique();
                                    @endphp
                                    @if($allFeatureIds->isNotEmpty())
                                    <tr class="table-secondary">
                                        <td colspan="{{ $prod->plans->count() + 1 }}" class="font-weight-bold small text-uppercase py-2">
                                            Features & Services
                                        </td>
                                    </tr>
                                    @foreach($allFeatureIds as $fId)
                                    @php
                                        $sampleFeat = $prod->plans->flatMap->planFeatures->firstWhere('feature_id', $fId);
                                    @endphp
                                    <tr>
                                        <td>{{ $sampleFeat?->feature_name ?: ('Feature #' . $fId) }}</td>
                                        @foreach($prod->plans as $p)
                                            @php $hasF = $p->planFeatures->contains('feature_id', $fId); @endphp
                                            <td class="text-center">
                                                @if($hasF)
                                                    <span class="text-success font-weight-bold">?</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @else
        {{-- Standalone Product without plans (Single card or service direct price) --}}
        <div class="row g-4">
            <div class="col-lg-6 col-md-8">
                <div class="pricing-card-box">
                    <h3 class="plan-header-title">{{ $prod->localized_name }}</h3>
                    <p class="plan-short-desc">{{ $prod->description ?: 'Standalone product offering.' }}</p>

                    @php $dPrice = $prod->directPrices->first(); @endphp
                    @if($dPrice)
                    <div class="plan-price-wrap">
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="plan-price-main">{{ $dPrice->formatted_price }}</span>
                            <span class="plan-price-period">/ {{ $dPrice->billing_period_label }}</span>
                        </div>
                    </div>
                    @endif

                    @if($prod->directFeatures->isNotEmpty())
                    <div class="mb-3">
                        <div class="plan-features-heading">Included features</div>
                        @foreach($prod->directFeatures as $feat)
                        <div class="plan-feature-item">
                            <span class="plan-feature-check">?</span>
                            <span>{{ $feat->feature_name }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <a href="{{ $business->affiliate_link ?: ($business->permanent_url ?: '#') }}" target="_blank" rel="nofollow noopener" class="btn-view-plan">
                        <span>Get product</span>
                        <span>?</span>
                    </a>
                </div>
            </div>
        </div>
        @endif

    </div>
    @endforeach
</div>

<script>
function switchProductTab(productId, tabBtn) {
    document.querySelectorAll('.product-tab-pill').forEach(btn => btn.classList.remove('active'));
    tabBtn.classList.add('active');

    document.querySelectorAll('.product-pricing-pane').forEach(pane => pane.classList.add('d-none'));
    const targetPane = document.getElementById('product-pane-' + productId);
    if (targetPane) {
        targetPane.classList.remove('d-none');
    }
}
</script>
@endif
