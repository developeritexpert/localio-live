@extends('admin_layout.master')

@section('content')
@php
    $bizTrans = $business->translations->first();
    $bizName = $bizTrans->name ?? ('Business #' . $business->id);
@endphp

<style>
.stat-box {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.stat-box-label {
    font-size: 12px;
    color: #64748b;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.stat-box-num {
    font-size: 32px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
    margin-top: 4px;
}
.table-catalog {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.table-catalog th {
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    font-weight: 700;
    padding: 14px 20px;
    border-bottom: 1px solid #e2e8f0;
}
.table-catalog td {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.term-pill {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #dbeafe;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
    margin: 2px 2px;
}
.curr-pill {
    background: #f8fafc;
    color: #334155;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
    margin: 2px;
}
.drawer-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.5);
    z-index: 1040;
    display: none;
}
.drawer-backdrop.show { display: block; }
.side-drawer {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: min(850px, 100vw);
    background: #fff;
    z-index: 1050;
    box-shadow: -10px 0 30px rgba(0,0,0,0.15);
    transform: translateX(100%);
    transition: transform 0.25s ease-in-out;
    display: flex;
    flex-direction: column;
}
.side-drawer.show { transform: translateX(0); }
.drawer-header {
    padding: 20px 28px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.drawer-body {
    padding: 24px 28px;
    overflow-y: auto;
    flex: 1;
}
.drawer-footer {
    padding: 16px 28px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
.price-card {
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 10px;
    padding: 16px 18px;
    margin-bottom: 14px;
    position: relative;
}
.period-preview {
    background: #f1f5f9;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    margin-top: 10px;
}
</style>

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between align-items-center">
                        <div class="nk-block-head-content">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-arrow mb-1">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.products-prices.index') }}">Products & Prices</a></li>
                                    <li class="breadcrumb-item active">{{ $bizName }}</li>
                                </ol>
                            </nav>
                            <h3 class="nk-block-title page-title">Products & prices</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage {{ $bizName }}'s products, plans and prices. Add multiple currencies or billing periods to the same entry.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                                <em class="icon ni ni-help"></em>
                                <span>+ Add Price FAQ</span>
                            </button>
                            <button type="button" class="btn btn-primary" id="btnOpenDrawer">
                                <em class="icon ni ni-plus"></em>
                                <span>+ Add product / plan</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="stat-box">
                            <div class="stat-box-label">Products & plans</div>
                            <div class="stat-box-num" id="statProductsCount">{{ $stats['products_plans_count'] }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="stat-box">
                            <div class="stat-box-label">Price entries</div>
                            <div class="stat-box-num" id="statPricesCount">{{ $stats['price_entries_count'] }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="stat-box">
                            <div class="stat-box-label">Currencies</div>
                            <div class="stat-box-num" id="statCurrenciesCount">{{ $stats['currencies_count'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="card card-bordered">
                    <div class="card-inner border-bottom d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="card-title mb-1">All products & plans</h5>
                            <p class="text-muted small mb-0">Every entry belongs directly to this business.</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <label for="previewCountry" class="small text-muted mb-0 text-nowrap">Preview country:</label>
                                <select id="previewCountry" class="form-select form-select-sm" style="width: auto;">
                                    <option value="all">Default names</option>
                                    <option value="en-us">United States (en-us)</option>
                                    <option value="de">Germany (de)</option>
                                    <option value="fr-be">Belgium - French (fr-be)</option>
                                    <option value="gb">United Kingdom (gb)</option>
                                    <option value="fr">France (fr)</option>
                                </select>
                            </div>
                            <div class="form-control-wrap" style="width: 260px;">
                                <div class="form-icon form-icon-left">
                                    <em class="icon ni ni-search"></em>
                                </div>
                                <input type="text" id="catalogSearch" class="form-control form-control-sm" placeholder="Search products or plans...">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-catalog">
                            <thead>
                                <tr>
                                    <th style="width: 35%;">Product / Plan</th>
                                    <th>Price Options</th>
                                    <th>Currencies</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="catalogTableBody"></tbody>
                        </table>
                    </div>
                    <div id="catalogEmpty" class="text-center py-5 text-muted d-none">
                        No products or plans found for this business. Click <strong>+ Add product / plan</strong> to create one.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Slide-over Drawer --}}
<div class="drawer-backdrop" id="drawerBackdrop"></div>
<div class="side-drawer" id="sideDrawer" role="dialog" aria-modal="true">
    <div class="drawer-header">
        <div>
            <span class="text-muted small text-uppercase font-weight-bold">Product or Plan</span>
            <h4 class="mb-0" id="drawerTitle">Add product / plan</h4>
        </div>
        <button type="button" class="btn-close" id="btnCloseDrawer" aria-label="Close"></button>
    </div>
    <div class="drawer-body">
        <form id="productPlanForm">
            <input type="hidden" id="editProductId" value="">

            <div class="mb-4">
                <h6 class="font-weight-bold mb-1">Basic information</h6>
                <p class="text-muted small mb-3">The business is already selected.</p>

                <div class="mb-3">
                    <label for="inputName" class="form-label font-weight-bold small">Default product / plan name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="inputName" placeholder="e.g. Shared Hosting Basic" required>
                    <div class="form-text small text-muted">Used when no country-specific name is set.</div>
                </div>

                <div class="mb-3">
                    <label for="inputDesc" class="form-label font-weight-bold small">Short description <span class="text-muted">(optional)</span></label>
                    <textarea class="form-control" id="inputDesc" rows="2" placeholder="Brief description of this product / plan."></textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="inputPriceModel" class="form-label font-weight-bold small">Pricing Model <span class="text-danger">*</span></label>
                        <select class="form-select" id="inputPriceModel">
                            <option value="fixed" selected>Paid</option>
                            <option value="free">Free</option>
                            <option value="quote">Request a quote</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="inputHasPlans" class="form-label font-weight-bold small">Structure <span class="text-danger">*</span></label>
                        <select class="form-select" id="inputHasPlans">
                            <option value="1" selected>Product with Plan(s)</option>
                            <option value="0">Standalone Product (Direct Pricing)</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Names by Country / Region --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="font-weight-bold mb-0">Names by country / region <span class="text-muted small fw-normal">(optional)</span></h6>
                        <p class="text-muted small mb-0">Use an alternative official name where called differently in that market.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddLocaleName">+ Add country name</button>
                </div>
                <div id="localeNamesContainer"></div>
                <div id="emptyLocaleNotice" class="alert alert-light border small text-muted py-2 mb-0">
                    No regional names set. The default name is used in all countries.
                </div>
            </div>

            <hr class="my-4">

            {{-- Feature Mapping --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="font-weight-bold mb-0">Feature mapping</h6>
                        <p class="text-muted small mb-0">Select features from Localio. Checkbox marks them as <strong>Key feature</strong> (highlight on card).</p>
                    </div>
                </div>
                <div class="mb-2">
                    <select id="featureSelector" class="form-select form-select-sm mb-2">
                        <option value="">-- Choose a feature to add --</option>
                        @foreach($features as $feat)
                            <option value="{{ $feat['id'] }}">{{ $feat['name'] }} (ID: {{ $feat['id'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div id="selectedFeaturesList" class="d-flex flex-column "></div>
                <div id="emptyFeaturesNotice" class="alert alert-light border small text-muted py-2 mb-0">
                    No features mapped yet. Select features from the dropdown above.
                </div>
            </div>

            <hr class="my-4">

            {{-- Specifications --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="font-weight-bold mb-0">Specifications <span class="text-muted small fw-normal">(optional)</span></h6>
                        <p class="text-muted small mb-0">E.g., Storage: 20 GB.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddSpec">+ Add Specification</button>
                </div>
                <div id="specsContainer" class="d-flex flex-column gap-2"></div>
            </div>

            <hr class="my-4">

            {{-- Pricing Options --}}
            <div id="pricingSection" class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="font-weight-bold mb-0">Pricing options</h6>
                        <p class="text-muted small mb-0">Add prices for monthly, annual, or custom terms and available currencies.</p>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddTerm">+ Add billing period</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddCurrency">+ Add currency</button>
                </div>

                <div id="priceCardsContainer"></div>
                <div id="emptyPriceNotice" class="alert alert-light border small text-muted py-2 mb-0">
                    No pricing options yet. Click <strong>+ Add billing period</strong> to start.
                </div>
            </div>

            <div id="formAlert" class="alert alert-danger d-none mt-3" role="alert"></div>
        </form>
    </div>
    <div class="drawer-footer">
        <button type="button" class="btn btn-outline-secondary" id="btnCancelDrawer">Cancel</button>
        <button type="button" class="btn btn-primary" id="btnSaveProductPlan">Save product / plan</button>
    </div>
</div>

{{-- Add Price FAQ Modal --}}
<div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="priceFaqForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Pricing FAQ for {{ $bizName }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small py-2 mb-3">
                        <em class="icon ni ni-info-fill me-1"></em>
                        This FAQ will automatically be added under category <strong>Plans & Pricing</strong> (if plans exist) or <strong>Pricing</strong> (if without plans), and positioned as the <strong>second category</strong> in the business FAQs.
                    </div>
                    <div class="mb-3">
                        <label for="faqQuestion" class="form-label font-weight-bold small">Question <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="faqQuestion" name="question" placeholder="e.g. Can I upgrade or downgrade my plan later?" required>
                    </div>
                    <div class="mb-3">
                        <label for="faqAnswer" class="form-label font-weight-bold small">Answer <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="faqAnswer" name="answer" rows="4" placeholder="Detailed explanation..." required></textarea>
                    </div>
                    <div id="faqAlert" class="alert alert-danger d-none" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitFaq">Add FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@php
    $initialData = $business->offerProducts->map(function($prod) {
        return [
            'id' => $prod->id,
            'product_id' => $prod->product_id,
            'name' => $prod->name,
            'description' => $prod->description,
            'has_plans' => $prod->has_plans,
            'names_by_locale' => $prod->names_by_locale ?: [],
            'plans' => $prod->plans->map(function($pl) {
                return [
                    'id' => $pl->id,
                    'plan_id' => $pl->plan_id,
                    'name' => $pl->name,
                    'short_description' => $pl->short_description,
                    'names_by_locale' => $pl->names_by_locale ?: [],
                    'is_featured' => (bool)$pl->is_featured,
                    'features' => $pl->planFeatures->pluck('feature_id')->toArray(),
                    'featured_feature_ids' => $pl->featuredHighlights->pluck('feature_id')->toArray(),
                    'specifications' => $pl->specifications->map(fn($s) => ['code' => $s->code, 'value' => $s->value, 'unit' => $s->unit])->toArray(),
                    'prices' => $pl->prices->map(function($pr) {
                        return [
                            'id' => $pr->id,
                            'market' => $pr->country_region,
                            'currency' => $pr->currency,
                            'term' => $pr->billing_period,
                            'commitment_period' => $pr->commitment_period,
                            'customMonths' => $pr->custom_months,
                            'unit' => $pr->unit,
                            'customUnit' => $pr->custom_unit,
                            'amount' => $pr->regular_price,
                            'renewal_price' => $pr->renewal_price,
                            'intro' => $pr->intro_price,
                            'duration' => $pr->intro_duration,
                            'price_model' => $pr->price_model,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
            'direct_prices' => $prod->directPrices->map(function($pr) {
                return [
                    'id' => $pr->id,
                    'market' => $pr->country_region,
                    'currency' => $pr->currency,
                    'term' => $pr->billing_period,
                    'unit' => $pr->unit,
                    'amount' => $pr->regular_price,
                    'renewal_price' => $pr->renewal_price,
                ];
            })->toArray(),
        ];
    });
@endphp

<script>
window.ALL_FEATURES = @json($features);
window.INITIAL_RECORDS = @json($initialData);
window.BUSINESS_ID = {{ $business->id }};
window.CSRF_TOKEN = '{{ csrf_token() }}';

(function() {
    'use strict';

    let records = window.INITIAL_RECORDS || [];
    let currentPrices = [];
    let currentNames = [];
    let currentFeatures = [];
    let currentSpecs = [];
    let editingId = null;

    const terms = [
        ['1m', '1 month (monthly)'],
        ['3m', '3 months'],
        ['6m', '6 months'],
        ['12m', '12 months (yearly)'],
        ['24m', '24 months (2 years)'],
        ['36m', '36 months (3 years)'],
        ['custom', 'Other number of months'],
        ['1w', '1 week'],
        ['1d', '1 day'],
        ['once', 'One-time'],
        ['none', 'None (free)']
    ];

    const currencies = ['USD', 'EUR', 'GBP', 'MXN', 'CAD', 'AUD', 'CHF', 'BRL', 'INR', 'JPY', 'SGD', 'NZD'];
    const markets = [
        ['all', 'All applicable countries'],
        ['en-us', 'United States'],
        ['de', 'Germany'],
        ['fr-be', 'Belgium (French)'],
        ['gb', 'United Kingdom'],
        ['fr', 'France'],
        ['es', 'Spain'],
        ['it', 'Italy'],
        ['nl', 'Netherlands'],
        ['in', 'India'],
        ['au', 'Australia'],
        ['ca', 'Canada']
    ];

    const units = [
        ['', 'None (entire plan)'],
        ['user', 'Per user'],
        ['seat', 'Per seat'],
        ['device', 'Per device'],
        ['website', 'Per website'],
        ['domain', 'Per domain'],
        ['GB', 'Per GB'],
        ['license', 'Per license'],
        ['other', 'Other (specify)']
    ];

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function fmt(n) {
        return Number(n || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function summarizePrice(p) {
        if (!p.amount || isNaN(Number(p.amount))) return 'Enter price to see preview';
        const unit = p.unit ? ` / ${p.unit}` : '';
        const base = `${p.currency} ${fmt(p.amount)}${unit}`;
        
        let m = null;
        if (p.term === 'custom') m = Number(p.customMonths);
        else if (/^(\d+)m$/.test(p.term)) m = Number(p.term.replace('m', ''));

        if (m && m > 0) {
            if (m === 1) return `${base} / month`;
            const equiv = fmt(Number(p.amount) / m);
            return `${base} billed every ${m} months  •  ${p.currency} ${equiv}${unit} / month`;
        }
        return p.term === 'once' ? `${base} one-time` : `${base} / ${p.term}`;
    }

    function renderCatalog() {
        const query = (document.getElementById('catalogSearch').value || '').trim().toLowerCase();
        const previewCountry = document.getElementById('previewCountry').value;
        const tbody = document.getElementById('catalogTableBody');
        const emptyEl = document.getElementById('catalogEmpty');

        let totalPricesCount = 0;
        let uniqueCurrencies = new Set();

        const filtered = records.filter(r => {
            const matchName = (r.name || '').toLowerCase().includes(query);
            const matchDesc = (r.description || '').toLowerCase().includes(query);
            return matchName || matchDesc;
        });

        records.forEach(r => {
            (r.plans || []).forEach(pl => {
                (pl.prices || []).forEach(p => {
                    totalPricesCount++;
                    if (p.currency) uniqueCurrencies.add(p.currency);
                });
            });
            (r.direct_prices || []).forEach(p => {
                totalPricesCount++;
                if (p.currency) uniqueCurrencies.add(p.currency);
            });
        });

        document.getElementById('statProductsCount').textContent = records.length;
        document.getElementById('statPricesCount').textContent = totalPricesCount;
        document.getElementById('statCurrenciesCount').textContent = uniqueCurrencies.size;

        if (filtered.length === 0) {
            tbody.innerHTML = '';
            emptyEl.classList.remove('d-none');
            return;
        }

        emptyEl.classList.add('d-none');

        tbody.innerHTML = filtered.map(r => {
            let localizedName = r.name;
            if (previewCountry !== 'all' && r.names_by_locale && r.names_by_locale[previewCountry]) {
                localizedName = r.names_by_locale[previewCountry];
            }

            const allPrices = (r.plans && r.plans.length > 0)
                ? r.plans.flatMap(pl => pl.prices || [])
                : (r.direct_prices || []);

            const currenciesSet = [...new Set(allPrices.map(p => p.currency).filter(Boolean))];
            const termsSet = [...new Set(allPrices.map(p => p.term).filter(Boolean))];

            const planBadge = (r.plans && r.plans.length > 0)
                ? `<span class="badge bg-primary-dim me-1">${r.plans.length} ${r.plans.length === 1 ? 'Plan' : 'Plans'}</span>`
                : `<span class="badge bg-light text-muted me-1">Direct</span>`;

            const regionalNamesCount = Object.keys(r.names_by_locale || {}).length;

            return `<tr>
                <td>
                    <div class="font-weight-bold text-dark fs-14px">${esc(localizedName)}</div>
                    ${previewCountry !== 'all' && localizedName !== r.name ? `<div class="text-muted small">Default: ${esc(r.name)}</div>` : ''}
                    <div class="text-muted small mt-1">${esc(r.description || '')}</div>
                    <div class="mt-1">
                        ${planBadge}
                        ${regionalNamesCount > 0 ? `<span class="badge bg-info-dim">${regionalNamesCount} regional name(s)</span>` : ''}
                    </div>
                </td>
                <td>
                    <div class="font-weight-bold text-dark small">${allPrices.length} price ${allPrices.length === 1 ? 'entry' : 'entries'}</div>
                    <div>
                        ${termsSet.map(t => {
                            const lbl = {'1m':'Monthly','12m':'Yearly','24m':'2 years','36m':'3 years','once':'One-time','none':'Free'}[t] || t;
                            return `<span class="term-pill">${esc(lbl)}</span>`;
                        }).join('')}
                    </div>
                </td>
                <td>
                    <div>
                        ${currenciesSet.length > 0 ? currenciesSet.map(c => `<span class="curr-pill">${esc(c)}</span>`).join('') : '<span class="text-muted small">—</span>'}
                    </div>
                </td>
                <td>
                    <span class="badge bg-success-dim">Public</span>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="window.editOffer(${r.id})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="window.deleteOffer(${r.id})">Delete</button>
                </td>
            </tr>`;
        }).join('');
    }

    function openDrawer(id = null) {
        editingId = id;
        const drawer = document.getElementById('sideDrawer');
        const backdrop = document.getElementById('drawerBackdrop');
        const title = document.getElementById('drawerTitle');
        const alertBox = document.getElementById('formAlert');
        alertBox.classList.add('d-none');

        const item = records.find(r => r.id === id);
        title.textContent = item ? 'Edit product / plan' : 'Add product / plan';

        document.getElementById('editProductId').value = item ? item.id : '';
        document.getElementById('inputName').value = item ? item.name : '';
        document.getElementById('inputDesc').value = item ? (item.description || '') : '';
        document.getElementById('inputHasPlans').value = (item && !item.has_plans) ? '0' : '1';

        // Names
        currentNames = [];
        if (item && item.names_by_locale) {
            Object.keys(item.names_by_locale).forEach(k => {
                currentNames.push({ market: k, name: item.names_by_locale[k] });
            });
        }
        renderLocaleNames();

        // Prices
        currentPrices = [];
        if (item) {
            if (item.plans && item.plans.length > 0) {
                currentPrices = (item.plans[0].prices || []).map(p => ({...p}));
            } else if (item.direct_prices) {
                currentPrices = item.direct_prices.map(p => ({...p}));
            }
        }
        if (currentPrices.length === 0) {
            currentPrices.push({
                market: 'all',
                currency: 'USD',
                term: '1m',
                customMonths: '',
                unit: '',
                customUnit: '',
                amount: '',
                renewal_price: '',
                intro: '',
                duration: ''
            });
        }
        renderPriceCards();

        // Features
        currentFeatures = [];
        if (item && item.plans && item.plans.length > 0) {
            const pl = item.plans[0];
            const featIds = pl.features || [];
            const highIds = pl.featured_feature_ids || [];
            featIds.forEach(fid => {
                const fObj = (window.ALL_FEATURES || []).find(x => x.id === fid);
                currentFeatures.push({
                    id: fid,
                    name: fObj ? fObj.name : `Feature #${fid}`,
                    is_highlight: highIds.includes(fid)
                });
            });
        }
        renderFeaturesList();

        // Specifications
        currentSpecs = [];
        if (item && item.plans && item.plans.length > 0) {
            currentSpecs = (item.plans[0].specifications || []).map(s => ({...s}));
        }
        renderSpecs();

        drawer.classList.add('show');
        backdrop.classList.add('show');
    }

    function closeDrawer() {
        document.getElementById('sideDrawer').classList.remove('show');
        document.getElementById('drawerBackdrop').classList.remove('show');
        editingId = null;
    }

    function renderLocaleNames() {
        const container = document.getElementById('localeNamesContainer');
        const emptyNotice = document.getElementById('emptyLocaleNotice');

        if (currentNames.length === 0) {
            container.innerHTML = '';
            emptyNotice.style.display = 'block';
            return;
        }
        emptyNotice.style.display = 'none';

        container.innerHTML = currentNames.map((n, i) => `
            <div class="card p-2 mb-2 bg-light border">
                <div class="row g-2 align-items-center">
                    <div class="col-sm-5">
                        <select class="form-select form-select-sm" onchange="window.updateLocaleKey(${i}, this.value)">
                            ${markets.filter(m => m[0] !== 'all').map(m => `<option value="${m[0]}" ${m[0] === n.market ? 'selected' : ''}>${esc(m[1])} (${m[0]})</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Official localized name" value="${esc(n.name)}" oninput="window.updateLocaleVal(${i}, this.value)">
                    </div>
                    <div class="col-sm-2 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="window.removeLocale(${i})">?</button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function renderFeaturesList() {
        const container = document.getElementById('selectedFeaturesList');
        const emptyNotice = document.getElementById('emptyFeaturesNotice');

        if (currentFeatures.length === 0) {
            container.innerHTML = '';
            emptyNotice.style.display = 'block';
            return;
        }
        emptyNotice.style.display = 'none';

        container.innerHTML = currentFeatures.map((f, i) => `
            <div class="d-flex justify-content-between align-items-center p-2 rounded border bg-light mb-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" id="feat_high_${i}" class="form-check-input" ${f.is_highlight ? 'checked' : ''} onchange="window.toggleFeatureHighlight(${i}, this.checked)">
                    <label for="feat_high_${i}" class="form-check-label small font-weight-bold mb-0">
                        ${esc(f.name)}
                        ${f.is_highlight ? '<span class="badge bg-success ms-1">? Key feature</span>' : ''}
                    </label>
                </div>
                <button type="button" class="btn btn-sm text-danger" onclick="window.removeFeature(${i})">Remove</button>
            </div>
        `).join('');
    }

    function renderSpecs() {
        const container = document.getElementById('specsContainer');
        container.innerHTML = currentSpecs.map((s, i) => `
            <div class="row g-2 align-items-center mb-1">
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm" placeholder="Code (e.g. storage)" value="${esc(s.code)}" oninput="window.updateSpec(${i}, 'code', this.value)">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm" placeholder="Value (e.g. 20)" value="${esc(s.value)}" oninput="window.updateSpec(${i}, 'value', this.value)">
                </div>
                <div class="col-3">
                    <input type="text" class="form-control form-control-sm" placeholder="Unit (e.g. GB)" value="${esc(s.unit || '')}" oninput="window.updateSpec(${i}, 'unit', this.value)">
                </div>
                <div class="col-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="window.removeSpec(${i})">?</button>
                </div>
            </div>
        `).join('');
    }

    function renderPriceCards() {
        const container = document.getElementById('priceCardsContainer');
        const emptyNotice = document.getElementById('emptyPriceNotice');

        if (currentPrices.length === 0) {
            container.innerHTML = '';
            emptyNotice.style.display = 'block';
            return;
        }
        emptyNotice.style.display = 'none';

        container.innerHTML = currentPrices.map((p, i) => `
            <div class="price-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong class="small text-uppercase text-muted">Pricing option #${i + 1}</strong>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-lg btn-outline-secondary" onclick="window.duplicatePrice(${i})">Duplicate</button>
                        <button type="button" class="btn btn-lg btn-outline-danger" onclick="window.removePrice(${i})">Remove</button>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Country / Market</label>
                        <select class="form-select form-select-sm" onchange="window.updatePriceField(${i}, 'market', this.value)">
                            ${markets.map(m => `<option value="${m[0]}" ${m[0] === (p.market || 'all') ? 'selected' : ''}>${esc(m[1])}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Currency <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" onchange="window.updatePriceField(${i}, 'currency', this.value)">
                            ${currencies.map(c => `<option value="${c}" ${c === p.currency ? 'selected' : ''}>${c}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Billing Period <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" onchange="window.updatePriceField(${i}, 'term', this.value)">
                            ${terms.map(t => `<option value="${t[0]}" ${t[0] === p.term ? 'selected' : ''}>${t[1]}</option>`).join('')}
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Unit <span class="text-muted">(optional)</span></label>
                        <select class="form-select form-select-sm" onchange="window.updatePriceField(${i}, 'unit', this.value)">
                            ${units.map(u => `<option value="${u[0]}" ${u[0] === (p.unit || '') ? 'selected' : ''}>${u[1]}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Regular Price <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0" class="form-control form-control-sm" placeholder="0.00" value="${esc(p.amount)}" oninput="window.updatePriceField(${i}, 'amount', this.value)">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Renewal Price <span class="text-muted">(optional)</span></label>
                        <input type="number" step="any" min="0" class="form-control form-control-sm" placeholder="e.g. 15.00" value="${esc(p.renewal_price || '')}" oninput="window.updatePriceField(${i}, 'renewal_price', this.value)">
                    </div>
                </div>

                <div class="period-preview">${esc(summarizePrice(p))}</div>
            </div>
        `).join('');
    }

    window.editOffer = function(id) {
        openDrawer(id);
    };

    window.deleteOffer = function(id) {
        if (!confirm('Are you sure you want to delete this product/plan and its pricing?')) return;
        fetch(`/admin-dashboard/products-prices/delete/${window.BUSINESS_ID}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                records = records.filter(r => r.id !== id);
                renderCatalog();
            } else {
                alert(data.message || 'Error deleting product');
            }
        })
        .catch(err => alert('Network error deleting product.'));
    };

    window.updateLocaleKey = (i, val) => { currentNames[i].market = val; };
    window.updateLocaleVal = (i, val) => { currentNames[i].name = val; };
    window.removeLocale = (i) => { currentNames.splice(i, 1); renderLocaleNames(); };

    window.toggleFeatureHighlight = (i, val) => { currentFeatures[i].is_highlight = val; renderFeaturesList(); };
    window.removeFeature = (i) => { currentFeatures.splice(i, 1); renderFeaturesList(); };

    window.updateSpec = (i, field, val) => { currentSpecs[i][field] = val; };
    window.removeSpec = (i) => { currentSpecs.splice(i, 1); renderSpecs(); };

    window.updatePriceField = (i, field, val) => {
        currentPrices[i][field] = val;
        renderPriceCards();
    };
    window.removePrice = (i) => {
        currentPrices.splice(i, 1);
        renderPriceCards();
    };
    window.duplicatePrice = (i) => {
        const orig = currentPrices[i];
        currentPrices.splice(i + 1, 0, {...orig, amount: ''});
        renderPriceCards();
    };

    document.getElementById('btnSaveProductPlan').addEventListener('click', function() {
        const alertBox = document.getElementById('formAlert');
        alertBox.classList.add('d-none');

        const name = document.getElementById('inputName').value.trim();
        const desc = document.getElementById('inputDesc').value.trim();
        const priceModel = document.getElementById('inputPriceModel').value;
        const hasPlans = document.getElementById('inputHasPlans').value === '1';

        if (!name) {
            alertBox.textContent = 'Please enter the default product / plan name.';
            alertBox.classList.remove('d-none');
            return;
        }

        const namesByLocale = {};
        currentNames.forEach(n => {
            if (n.market && n.name.trim()) {
                namesByLocale[n.market] = n.name.trim();
            }
        });

        const payloadPlans = [{
            name: name,
            short_description: desc,
            names_by_locale: namesByLocale,
            is_featured: false,
            features: currentFeatures.map(f => f.id),
            featured_feature_ids: currentFeatures.filter(f => f.is_highlight).map(f => f.id),
            specifications: currentSpecs.filter(s => s.code && s.value),
            prices: currentPrices
        }];

        const payload = {
            product_id: document.getElementById('editProductId').value || null,
            name: name,
            description: desc,
            names_by_locale: namesByLocale,
            has_plans: hasPlans,
            price_model: priceModel,
            plans: hasPlans ? payloadPlans : [],
            prices: !hasPlans ? currentPrices : []
        };

        const btn = document.getElementById('btnSaveProductPlan');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        fetch(`/admin-dashboard/products-prices/store/${window.BUSINESS_ID}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.textContent = 'Save product / plan';
            if (data.success) {
                location.reload();
            } else {
                alertBox.textContent = data.message || 'Error saving product';
                alertBox.classList.remove('d-none');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.textContent = 'Save product / plan';
            alertBox.textContent = 'Network error saving product.';
            alertBox.classList.remove('d-none');
        });
    });

    document.getElementById('priceFaqForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const faqAlert = document.getElementById('faqAlert');
        faqAlert.classList.add('d-none');

        const q = document.getElementById('faqQuestion').value.trim();
        const a = document.getElementById('faqAnswer').value.trim();
        if (!q || !a) return;

        const btn = document.getElementById('btnSubmitFaq');
        btn.disabled = true;

        fetch(`/admin-dashboard/products-prices/add-faq/${window.BUSINESS_ID}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ question: q, answer: a })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                alert(data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('addFaqModal'));
                if (modal) modal.hide();
                document.getElementById('priceFaqForm').reset();
            } else {
                faqAlert.textContent = data.message || 'Error adding FAQ';
                faqAlert.classList.remove('d-none');
            }
        })
        .catch(() => {
            btn.disabled = false;
            faqAlert.textContent = 'Network error saving FAQ';
            faqAlert.classList.remove('d-none');
        });
    });

    document.getElementById('btnOpenDrawer').onclick = () => openDrawer();
    document.getElementById('btnCloseDrawer').onclick = closeDrawer;
    document.getElementById('btnCancelDrawer').onclick = closeDrawer;
    document.getElementById('drawerBackdrop').onclick = closeDrawer;
    document.getElementById('catalogSearch').oninput = renderCatalog;
    document.getElementById('previewCountry').onchange = renderCatalog;

    document.getElementById('btnAddLocaleName').onclick = () => {
        currentNames.push({ market: 'de', name: '' });
        renderLocaleNames();
    };

    document.getElementById('featureSelector').onchange = function() {
        const fid = parseInt(this.value, 10);
        if (!fid) return;
        if (!currentFeatures.some(f => f.id === fid)) {
            const fObj = (window.ALL_FEATURES || []).find(x => x.id === fid);
            currentFeatures.push({
                id: fid,
                name: fObj ? fObj.name : `Feature #${fid}`,
                is_highlight: false
            });
            renderFeaturesList();
        }
        this.value = '';
    };

    document.getElementById('btnAddSpec').onclick = () => {
        currentSpecs.push({ code: '', value: '', unit: '' });
        renderSpecs();
    };

    document.getElementById('btnAddTerm').onclick = () => {
        const p = currentPrices[0] || {};
        currentPrices.push({
            market: p.market || 'all',
            currency: p.currency || 'USD',
            term: '12m',
            unit: p.unit || '',
            amount: '',
            renewal_price: ''
        });
        renderPriceCards();
    };

    document.getElementById('btnAddCurrency').onclick = () => {
        const p = currentPrices[0] || {};
        currentPrices.push({
            market: 'de',
            currency: 'EUR',
            term: p.term || '1m',
            unit: p.unit || '',
            amount: '',
            renewal_price: ''
        });
        renderPriceCards();
    };

    renderCatalog();
})();
</script>
@endsection
