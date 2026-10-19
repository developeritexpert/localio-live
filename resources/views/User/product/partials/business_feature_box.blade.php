@php
    $lang_id = getCurrentLanguageID();
    $langCode = app()->getLocale();
    $bName = $business->translations->firstWhere('language_id', $lang_id)?->name 
        ?? $business->translations->first()?->name 
        ?? $business->title 
        ?? 'Business';

    $catTrans = $business->category->translation ?? null;
    $catName = strtolower($catTrans->name ?? $business->category->name ?? 'web hosting');
    $categorySlug = $business->category->parent->translation->slug ?? $business->category->parent->slug ?? 'hosting';
    $subcategorySlug = $catTrans->slug ?? $business->category->slug ?? 'web-hosting';

    // Find the offer products that have plans with features
    $offerProductsWithPlans = $business->offerProducts()
        ->where('status', 'public')
        ->whereHas('plans.planFeatures')
        ->with([
            'plans' => function($q) {
                $q->where('status', 'public')->orderBy('sort_order', 'asc');
            }, 
            'plans.planFeatures.feature.translations'
        ])
        ->orderBy('sort_order', 'asc')
        ->get();

    $activeOfferProduct = $offerProductsWithPlans->first();
    $plans = $activeOfferProduct ? $activeOfferProduct->plans : collect();
    $totalPlansCount = $plans->count();

    // Map features across plans of the active product
    $mappedFeatures = [];
    $groupMap = [
        'Plans' => 'Key features',
        'Tools' => 'Management',
        'Build' => 'Management',
        'DNS'   => 'Management',
        'Domains' => 'Management',
    ];

    if ($activeOfferProduct) {
        foreach ($plans as $plan) {
            foreach ($plan->planFeatures as $pf) {
                $fId = $pf->feature_id;
                if (!isset($mappedFeatures[$fId])) {
                    $feat = $pf->feature;
                    $ft = $feat ? ($feat->translations->firstWhere('lang_id', $lang_id) ?? $feat->translations->first()) : null;
                    $fName = $ft?->name ?? $feat?->name ?? "Feature #{$fId}";
                    $fSlug = Str::slug($fName);
                    $fGroup = $feat?->group_key ?? 'Other';
                    $displayGroup = $groupMap[$fGroup] ?? $fGroup;

                    $mappedFeatures[$fId] = [
                        'id' => $fId,
                        'name' => $fName,
                        'slug' => $fSlug,
                        'group' => $displayGroup,
                        'raw_group' => $fGroup,
                        'url' => url("/{$langCode}/{$categorySlug}/{$subcategorySlug}/{$fSlug}"),
                        'is_highlight' => false,
                        'plans' => [],
                        'plan_ids' => [],
                        'plan_matrix' => [],
                    ];
                }

                if ($pf->is_featured_highlight) {
                    $mappedFeatures[$fId]['is_highlight'] = true;
                }

                if (!in_array($plan->name, $mappedFeatures[$fId]['plans'])) {
                    $mappedFeatures[$fId]['plans'][] = $plan->name;
                    $mappedFeatures[$fId]['plan_ids'][] = (string)$plan->id;
                }
                $mappedFeatures[$fId]['plan_matrix'][$plan->id] = true;
            }
        }
    }

    // Availability text calculation
    foreach ($mappedFeatures as $fId => &$fData) {
        if ($totalPlansCount > 0 && count($fData['plans']) >= $totalPlansCount) {
            $fData['availability_text'] = 'Available in all plans';
        } else {
            $fData['availability_text'] = implode(' · ', $fData['plans']);
        }
    }
    unset($fData);

    // Filter tabs calculation
    $availableGroups = collect($mappedFeatures)->pluck('group')->unique()->values()->all();
    $defaultTabs = ['Key features', 'Performance', 'Security', 'Management', 'Support'];
    $tabsToShow = [];
    foreach ($defaultTabs as $dt) {
        if ($dt === 'Key features' || in_array($dt, $availableGroups)) {
            $tabsToShow[] = $dt;
        }
    }
    foreach ($availableGroups as $ag) {
        if (!in_array($ag, $tabsToShow) && $ag !== 'Other') {
            $tabsToShow[] = $ag;
        }
    }
@endphp

<div class="rating-criteria-section mb-5" id="section-features">
    <!-- Section Header -->
    <div class="mb-3">
        <h2 class="m-0" style="font-weight: 700; font-size: 24px; color: #0f172a; letter-spacing: -0.01em;">Features</h2>
        <p class="mt-2 text-muted" style="font-size: 14.5px; line-height: 1.5; color: #475569;">
            Explore {{ $bName }} features and discover other {{ $catName }} providers offering the capabilities you’re looking for.
        </p>
    </div>

    <!-- Inner Interactive Feature Container -->
    <div class="feature-box-card rounded-4 p-4" style="background: #ffffff; border: 1px solid #eaecf0; box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);">
        
        <!-- Filter Tabs Row -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4" id="featureGroupTabs">
            @foreach($tabsToShow as $idx => $tabName)
                @php $isActive = $idx === 0; @endphp
                <button type="button" 
                    class="btn btn-sm feat-tab-btn {{ $isActive ? 'active' : '' }}" 
                    data-tab-group="{{ $tabName }}"
                    style="{{ $isActive ? 'background: #0f172a; color: #ffffff; border: 1px solid #0f172a;' : 'background: #ffffff; color: #475569; border: 1px solid #e2e8f0;' }} border-radius: 9999px; padding: 7px 18px; font-weight: 500; font-size: 13.5px; transition: all 0.15s ease;">
                    {{ $tabName }}
                </button>
            @endforeach
        </div>

        <!-- Subheader: Product Category & Plan Dropdown -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-3 border-bottom" style="border-color: #f1f5f9 !important;">
            <!-- Left: Product Category -->
            <div>
                <div style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 2px;">
                    Product category
                </div>
                <div class="d-flex align-items-center gap-2" style="font-weight: 600; font-size: 15px; color: #0f172a;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748b;">
                        <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                        <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                        <line x1="6" y1="6" x2="6.01" y2="6"></line>
                        <line x1="6" y1="18" x2="6.01" y2="18"></line>
                    </svg>
                    <span>{{ $activeOfferProduct->localized_name ?: ($activeOfferProduct->name ?? 'Web hosting') }}</span>
                </div>
            </div>

            <!-- Right: Features Available In (Plan Filter Dropdown) -->
            <div class="d-flex flex-column align-items-end">
                <span style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px;">Features available in</span>
                <select id="featPlanFilterSelect" class="form-select form-select-sm" style="border-radius: 9999px; border-color: #cbd5e1; font-size: 13px; font-weight: 500; padding-left: 14px; padding-right: 32px; width: auto; min-width: 130px; cursor: pointer; color: #0f172a;">
                    <option value="all">All plans</option>
                    @foreach($plans as $pl)
                        <option value="{{ $pl->id }}">{{ $pl->localized_name ?: $pl->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- VIEW A: Feature Cards Grid (Default View) -->
        <div id="featCardsView" class="mt-3">
            <div class="row g-3" id="featCardsGrid">
                @foreach($mappedFeatures as $f)
                    @php
                        $isHighlight = $f['is_highlight'];
                        $planIdsStr = implode(',', $f['plan_ids']);
                    @endphp
                    <div class="col-md-6 col-12 feat-card-item" 
                         data-feature-id="{{ $f['id'] }}"
                         data-group="{{ $f['group'] }}"
                         data-is-highlight="{{ $isHighlight ? '1' : '0' }}"
                         data-plan-ids="{{ $planIdsStr }}">
                        <a href="{{ $f['url'] }}" 
                           class="feature-single-card h-100 p-3 rounded-3 d-flex align-items-start justify-content-between position-relative text-decoration-none"
                           style="background: #ffffff; border: 1px solid #eaecf0; transition: border-color 0.2s, box-shadow 0.2s; color: inherit;">
                            <div class="d-flex align-items-start gap-3">
                                <!-- Circled Green Checkmark Icon matching screenshot -->
                                <span class="feat-check-icon d-inline-flex align-items-center justify-content-center" 
                                      style="margin-top: 2px; flex-shrink: 0;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" stroke="#10b981" stroke-width="1.8" fill="#ecfdf5"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>
                                </span>
                                <div>
                                    <div class="feat-card-title" style="font-weight: 700; font-size: 14.5px; color: #0f172a; line-height: 1.35; margin-bottom: 2px;">
                                        {{ $f['name'] }}
                                    </div>
                                    <div class="feat-card-plans text-muted" style="font-size: 12.5px; color: #64748b !important; line-height: 1.3;">
                                        {{ $f['availability_text'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="feat-link-arrow text-muted" style="margin-top: 3px; flex-shrink: 0; color: #94a3b8; transition: color 0.15s ease;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 17L17 7M17 7H7M17 7V17"></path>
                                </svg>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- VIEW B: Feature Comparison Table View -->
        <div id="featTableView" class="mt-3 table-responsive" style="display: none;">
            <table class="table mb-0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e2e8f0; text-align: left;">
                            Feature
                        </th>
                        @foreach($plans as $pl)
                            <th class="text-center" style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e2e8f0; width: {{ round(60 / max(1, $plans->count())) }}%;">
                                {{ $pl->localized_name ?: $pl->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($mappedFeatures as $f)
                        @php
                            $isHighlight = $f['is_highlight'];
                            $planIdsStr = implode(',', $f['plan_ids']);
                        @endphp
                        <tr class="feat-table-row" 
                            data-feature-id="{{ $f['id'] }}"
                            data-group="{{ $f['group'] }}"
                            data-is-highlight="{{ $isHighlight ? '1' : '0' }}"
                            data-plan-ids="{{ $planIdsStr }}"
                            style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s ease;">
                            <td style="padding: 13px 16px; font-size: 13.5px; font-weight: 500; color: #1e293b; vertical-align: middle;">
                                <a href="{{ $f['url'] }}" class="text-decoration-none" style="color: inherit;">
                                    {{ $f['name'] }}
                                </a>
                            </td>
                            @foreach($plans as $pl)
                                @php $hasFeature = !empty($f['plan_matrix'][$pl->id]); @endphp
                                <td class="text-center" style="padding: 13px 16px; vertical-align: middle;">
                                    @if($hasFeature)
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

        <!-- Footer Row: Count & Toggle Button -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 mt-3 border-top" style="border-color: #f1f5f9 !important;">
            <div id="featCountShown" style="font-size: 13px; color: #64748b; font-weight: 500;">
                {{ count($mappedFeatures) }} features shown
            </div>

            <!-- View Toggle Button (Compare features by plan <-> Show feature cards) -->
            <button type="button" 
                    id="btnToggleFeatView" 
                    class="btn btn-sm"
                    style="border-radius: 9999px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 500; color: #0f172a; padding: 7px 18px; display: inline-flex; align-items: center; gap: 8px; background: #ffffff; transition: all 0.15s ease;">
                <!-- Table Icon (shown in Cards view) -->
                <svg id="btnFeatViewIconTable" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                    <path d="M3 9h18"></path>
                    <path d="M9 21V9"></path>
                </svg>
                <!-- Cards/Grid Icon (shown in Table view) -->
                <svg id="btnFeatViewIconCards" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                </svg>
                <span id="btnFeatViewLabel">Compare features by plan</span>
            </button>
        </div>

        <!-- Disclaimer for Interactive Design -->
        <div id="featInteractiveDisclaimer" class="mt-3 pt-2 text-muted" style="font-size: 12px; color: #94a3b8 !important; line-height: 1.5; border-top: 1px dashed #e2e8f0; display: none;">
            Interactive design example using hypothetical plan assignments. Actual features and plan availability must be verified using Localio's feature IDs and product/plan data.
        </div>

    </div>
</div>

<style>
.feature-single-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.feature-single-card:hover .feat-link-arrow {
    color: #2563eb !important;
}
.feat-tab-btn:hover:not(.active) {
    background: #f8fafc !important;
    color: #0f172a !important;
    border-color: #cbd5e1 !important;
}
.feat-table-row:hover td {
    background-color: #f8fafc;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const groupTabs = document.querySelectorAll('#featureGroupTabs .feat-tab-btn');
    const planSelect = document.getElementById('featPlanFilterSelect');
    const cardsView = document.getElementById('featCardsView');
    const tableView = document.getElementById('featTableView');
    const toggleBtn = document.getElementById('btnToggleFeatView');
    const toggleLabel = document.getElementById('btnFeatViewLabel');
    const iconTable = document.getElementById('btnFeatViewIconTable');
    const iconCards = document.getElementById('btnFeatViewIconCards');
    const countBadge = document.getElementById('featCountShown');
    const disclaimer = document.getElementById('featInteractiveDisclaimer');

    let currentGroup = 'Key features';
    let currentPlanId = 'all';
    let isTableView = false;

    function applyFeatureFilters() {
        let visibleCount = 0;

        // Filter cards
        const cardItems = document.querySelectorAll('.feat-card-item');
        cardItems.forEach(card => {
            const cardGroup = card.getAttribute('data-group');
            const isHighlight = card.getAttribute('data-is-highlight') === '1';
            const planIds = (card.getAttribute('data-plan-ids') || '').split(',');

            let groupMatch = false;
            if (currentGroup === 'Key features') {
                groupMatch = isHighlight || cardGroup === 'Key features';
            } else {
                groupMatch = (cardGroup.toLowerCase() === currentGroup.toLowerCase());
            }

            let planMatch = (currentPlanId === 'all') || planIds.includes(currentPlanId);

            if (groupMatch && planMatch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Filter table rows
        const tableRows = document.querySelectorAll('.feat-table-row');
        tableRows.forEach(row => {
            const rowGroup = row.getAttribute('data-group');
            const isHighlight = row.getAttribute('data-is-highlight') === '1';
            const planIds = (row.getAttribute('data-plan-ids') || '').split(',');

            let groupMatch = false;
            if (currentGroup === 'Key features') {
                groupMatch = isHighlight || rowGroup === 'Key features';
            } else {
                groupMatch = (rowGroup.toLowerCase() === currentGroup.toLowerCase());
            }

            let planMatch = (currentPlanId === 'all') || planIds.includes(currentPlanId);

            if (groupMatch && planMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        if (countBadge) {
            countBadge.textContent = visibleCount + ' features shown';
        }
    }

    // Tab Click Listener
    groupTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            groupTabs.forEach(t => {
                t.classList.remove('active');
                t.style.background = '#ffffff';
                t.style.color = '#475569';
                t.style.borderColor = '#e2e8f0';
                t.style.fontWeight = '500';
            });
            this.classList.add('active');
            this.style.background = '#0f172a';
            this.style.color = '#ffffff';
            this.style.borderColor = '#0f172a';
            this.style.fontWeight = '600';

            currentGroup = this.getAttribute('data-tab-group');
            applyFeatureFilters();
        });
    });

    // Plan Filter Dropdown Change
    if (planSelect) {
        planSelect.addEventListener('change', function () {
            currentPlanId = this.value;
            applyFeatureFilters();
        });
    }

    // Toggle View (Cards vs Table)
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            isTableView = !isTableView;
            if (isTableView) {
                cardsView.style.display = 'none';
                tableView.style.display = 'block';
                toggleLabel.textContent = 'Show feature cards';
                iconTable.style.display = 'none';
                iconCards.style.display = 'inline-block';
                if (disclaimer) disclaimer.style.display = 'block';
            } else {
                cardsView.style.display = 'block';
                tableView.style.display = 'none';
                toggleLabel.textContent = 'Compare features by plan';
                iconTable.style.display = 'inline-block';
                iconCards.style.display = 'none';
                if (disclaimer) disclaimer.style.display = 'none';
            }
        });
    }

    // Initial filter run
    applyFeatureFilters();
});
</script>
