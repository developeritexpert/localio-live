@extends('admin_layout.master')

@section('title', 'Business Translations Matrix')

@section('content')
<style>
/* Sticky columns and responsive grid */
.translations-table-wrapper {
    position: relative;
    overflow-x: auto;
    max-width: 100%;
}
.translations-table {
    border-collapse: separate;
    border-spacing: 0;
}
.translations-table thead th {
    position: sticky;
    top: 0;
    z-index: 4;
    background-color: #f5f6fa !important;
}
.sticky-col-check {
    position: sticky;
    left: 0;
    z-index: 5 !important;
    background-color: #ffffff !important;
    width: 45px;
    min-width: 45px;
    max-width: 45px;
    text-align: center;
}
thead th.sticky-col-check {
    z-index: 6 !important;
    background-color: #f5f6fa !important;
}
.sticky-col-biz {
    position: sticky;
    left: 45px;
    z-index: 5 !important;
    background-color: #ffffff !important;
    min-width: 230px;
    max-width: 260px;
    box-shadow: 2px 0 4px rgba(0,0,0,0.04);
}
thead th.sticky-col-biz {
    z-index: 6 !important;
    background-color: #f5f6fa !important;
    box-shadow: 2px 0 4px rgba(0,0,0,0.04);
}
.sticky-col-cat {
    position: sticky;
    left: 275px;
    z-index: 4 !important;
    background-color: #ffffff !important;
    min-width: 130px;
}
thead th.sticky-col-cat {
    z-index: 5 !important;
    background-color: #f5f6fa !important;
}
.sticky-col-country {
    position: sticky;
    left: 405px;
    z-index: 4 !important;
    background-color: #ffffff !important;
    min-width: 130px;
    box-shadow: 3px 0 5px rgba(0,0,0,0.06);
}
thead th.sticky-col-country {
    z-index: 5 !important;
    background-color: #f5f6fa !important;
    box-shadow: 3px 0 5px rgba(0,0,0,0.06);
}

/* Floating bottom horizontal scrollbar */
.floating-scrollbar-bar {
    position: fixed;
    bottom: 0;
    left: 280px; /* offset sidebar */
    right: 0;
    height: 16px;
    overflow-x: auto;
    overflow-y: hidden;
    z-index: 999;
    background: #f8f9fa;
    border-top: 1px solid #e2e8f0;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
}
@media (max-width: 991px) {
    .floating-scrollbar-bar {
        left: 0;
    }
}
.floating-scrollbar-content {
    height: 1px;
}
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">
        {{-- Page Header --}}
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Business Translations Matrix</h3>
                    <p class="nk-block-des text-soft">
                        Select businesses to batch for translation. Only changed English content or missing translations are queued.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <a href="{{ route('translations.reviews') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-comments me-1"></em> Review Translations
                    </a>
                    <a href="{{ route('translations.batch-jobs') }}" class="btn btn-primary btn-sm">
                        <em class="icon ni ni-layers me-1"></em> Batch Jobs
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-check-circle me-1"></em> {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-alert-circle me-1"></em> {!! session('warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-cross-circle me-1"></em> {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Translation Legend & Stats --}}
        <div class="card card-bordered mb-3">
            <div class="card-inner py-2 px-3 bg-light">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span class="fw-bold fs-13px text-dark">Status Legend:</span>
                        <span class="d-inline-flex align-items-center fs-12px" title="Translation is complete and matches latest English text">
                            <em class="icon ni ni-check-circle-fill text-success fs-15px me-1"></em> Fully Translated
                        </span>
                        <span class="d-inline-flex align-items-center fs-12px" title="English content was edited! Only the modified content needs re-translation">
                            <em class="icon ni ni-alert-circle-fill text-warning fs-15px me-1"></em> Source Changed (Outdated)
                        </span>
                        <span class="d-inline-flex align-items-center fs-12px" title="Some fields translated, some missing">
                            <em class="icon ni ni-pie-fill text-info fs-15px me-1"></em> Partially Translated
                        </span>
                        <span class="d-inline-flex align-items-center fs-12px" title="No translations exist for this locale">
                            <em class="icon ni ni-cross-circle-fill text-danger fs-15px me-1 opacity-50"></em> Missing
                        </span>
                    </div>
                    <div class="text-muted fs-12px">
                        Showing <strong>{{ $businesses->total() }}</strong> businesses &bull; <strong>{{ count($languages) }}</strong> BCP47 locales
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card card-bordered mb-3">
            <div class="card-inner py-2">
                <form method="GET" action="{{ route('translations.businesses') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fs-12px mb-1">Search Business</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Title, slug, ID..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->translation->name ?? ($cat->category_cap ?? ($cat->name ?? 'Category #' . $cat->id)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Country</label>
                        <select name="country_id" class="form-select form-select-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $cnt)
                                <option value="{{ $cnt->id }}" {{ request('country_id') == $cnt->id ? 'selected' : '' }}>{{ $cnt->country_name ?? $cnt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('translations.businesses') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Batch Form --}}
        <form id="businessBatchForm" action="{{ route('translations.businesses.submit-batch') }}" method="POST">
            @csrf

            {{-- Batch Controls Bar (Matching Content Generation) --}}
            <div class="card card-bordered mb-3">
                <div class="card-inner py-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <span class="text-soft small fw-semibold">Provider:</span>
                        </div>
                        <div class="col-auto">
                            <select name="provider" class="form-select form-select-sm" style="width:200px" title="Batch Generation Mode">
                                <option value="manual" selected>Manual Batch (JSON for ChatGPT)</option>
                                <option value="openai">OpenAI (ChatGPT API)</option>
                                <option value="anthropic">Anthropic (Claude API)</option>
                            </select>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-1">
                            <span class="text-soft small fw-semibold">Batch Size:</span>
                            <input type="number" name="batch_size" class="form-control form-control-sm text-center" value="{{ $batchSize ?? 10 }}" min="1" max="50" style="width:60px" title="Businesses merged per batch job">
                            <span class="text-soft small">/ batch</span>
                        </div>
                        <div class="col-auto">
                            <span class="text-soft small fw-semibold">Locales:</span>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <em class="icon ni ni-globe me-1"></em> <span id="localesSelectedLabel">All Locales ({{ count($languages) }})</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-3" style="width: 320px; max-height: 360px; overflow-y: auto;">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <span class="fw-bold fs-12px">Target Locales</span>
                                        <div>
                                            <a href="javascript:void(0)" onclick="selectAllLocales(true)" class="fs-11px text-primary me-2">All</a>
                                            <a href="javascript:void(0)" onclick="selectAllLocales(false)" class="fs-11px text-secondary">None</a>
                                        </div>
                                    </div>
                                    <div class="row g-1">
                                        @foreach($languages as $lang)
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input locale-check" type="checkbox" name="target_locales[]" value="{{ $lang['code'] }}" id="loc_{{ $lang['code'] }}" checked onchange="updateLocaleCount()">
                                                    <label class="form-check-label fs-11px" for="loc_{{ $lang['code'] }}">
                                                        <strong>{{ $lang['code'] }}</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-auto">
                            <span class="text-soft small fw-semibold">Fields:</span>
                        </div>
                        @foreach($translatableFields as $fKey => $fLabel)
                            <div class="col-auto">
                                <div class="form-check form-check-inline me-1">
                                    <input class="form-check-input field-checkbox" type="checkbox" name="fields[]" value="{{ $fKey }}" id="f_{{ $fKey }}" checked>
                                    <label class="form-check-label small" for="f_{{ $fKey }}">{{ $fLabel }}</label>
                                </div>
                            </div>
                        @endforeach

                        <div class="col-auto ms-auto d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Clear</button>
                            <button type="submit" class="btn btn-sm btn-primary ms-1" id="submitBatchBtn" disabled>
                                <em class="icon ni ni-send me-1"></em> Submit Batch (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Matrix Table with Sticky Columns --}}
            <div class="card card-bordered card-preview">
                <div class="table-responsive translations-table-wrapper" id="matrixTableWrapper">
                    <table class="table table-hover align-middle mb-0 translations-table" id="matrixTable">
                        <thead class="table-light">
                            <tr class="fs-12px text-muted">
                                <th class="sticky-col-check">
                                    <input type="checkbox" id="selectAllCheck" class="form-check-input" title="Select all on page">
                                </th>
                                <th class="sticky-col-biz">Business / ID</th>
                                <th class="sticky-col-cat">Category</th>
                                <th class="sticky-col-country">Country</th>
                                @foreach($languages as $lang)
                                    <th class="text-center" style="min-width: 75px;" title="{{ $lang['name'] }}">
                                        <span class="badge bg-outline-secondary text-dark" style="font-size: 11px;">{{ $lang['code'] }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matrix as $row)
                                <tr>
                                    <td class="sticky-col-check">
                                        <input type="checkbox"
                                               name="business_ids[]"
                                               value="{{ $row['business']->id }}"
                                               class="form-check-input business-check"
                                               onchange="updateCount()">
                                    </td>
                                    <td class="sticky-col-biz">
                                        <div class="fw-bold text-dark fs-13px text-truncate" style="max-width: 220px;" title="{{ $row['business']->title ?? 'Business #' . $row['business']->id }}">
                                            {{ $row['business']->title ?? 'Business #' . $row['business']->id }}
                                        </div>
                                        <span class="text-muted fs-11px">ID: {{ $row['business']->id }} &bull; Slug: {{ $row['business']->slug ?? 'N/A' }}</span>
                                    </td>
                                    <td class="sticky-col-cat">
                                        <span class="badge bg-light text-dark text-truncate" style="max-width: 120px;">
                                            {{ $row['business']->category->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="sticky-col-country">
                                        <span class="badge bg-light text-secondary text-truncate" style="max-width: 120px;">
                                            {{ $row['business']->countries->first()->country_name ?? $row['business']->countries->first()->name ?? '—' }}
                                        </span>
                                    </td>
                                    @foreach($languages as $lang)
                                        @php
                                            $loc = $row['locales'][$lang['code']] ?? ['status' => 'missing'];
                                            $status = $loc['status'] ?? 'missing';
                                        @endphp
                                        <td class="text-center">
                                            @if($status === 'full')
                                                <em class="icon ni ni-check-circle-fill text-success fs-16px" title="Fully Translated & Up-to-Date ({{ $lang['code'] }})"></em>
                                            @elseif($status === 'outdated')
                                                <span title="Source content updated in en-US! {{ $loc['outdated_count'] ?? 1 }} field(s) changed">
                                                    <em class="icon ni ni-alert-circle-fill text-warning fs-16px"></em>
                                                </span>
                                            @elseif($status === 'partial')
                                                <em class="icon ni ni-pie-fill text-info fs-16px" title="Partially Translated ({{ $loc['completed_count'] ?? 0 }} complete, {{ $loc['missing_count'] ?? 0 }} missing)"></em>
                                            @else
                                                <em class="icon ni ni-cross-circle-fill text-danger fs-16px opacity-50" title="Missing Translation ({{ $lang['code'] }})"></em>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($languages) + 4 }}" class="text-center py-4 text-muted">
                                        No businesses found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($businesses->hasPages())
                    <div class="card-inner py-3 border-top">
                        {{ $businesses->links() }}
                    </div>
                @endif
            </div>
        </form>

        {{-- Floating Synchronized Horizontal Scrollbar --}}
        <div id="floatingScrollbar" class="floating-scrollbar-bar">
            <div id="floatingScrollbarContent" class="floating-scrollbar-content"></div>
        </div>
    </div>
</div>

<script>
// Checkbox Management
function updateCount() {
    const checked = document.querySelectorAll('.business-check:checked').length;
    const countSpan = document.getElementById('selectedCount');
    const submitBtn = document.getElementById('submitBatchBtn');

    if (countSpan) countSpan.textContent = checked;
    if (submitBtn) submitBtn.disabled = (checked === 0);

    const allBoxes = document.querySelectorAll('.business-check');
    const selectAllCheck = document.getElementById('selectAllCheck');
    if (selectAllCheck && allBoxes.length > 0) {
        selectAllCheck.checked = (checked === allBoxes.length);
        selectAllCheck.indeterminate = (checked > 0 && checked < allBoxes.length);
    }
}

function selectAll() {
    document.querySelectorAll('.business-check').forEach(cb => cb.checked = true);
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('.business-check').forEach(cb => cb.checked = false);
    updateCount();
}

document.getElementById('selectAllCheck')?.addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.business-check').forEach(cb => cb.checked = checked);
    updateCount();
});

// Locale Selection Management
function updateLocaleCount() {
    const total = document.querySelectorAll('.locale-check').length;
    const selected = document.querySelectorAll('.locale-check:checked').length;
    const label = document.getElementById('localesSelectedLabel');
    if (!label) return;

    if (selected === total) {
        label.textContent = `All Locales (${total})`;
    } else if (selected === 0) {
        label.textContent = 'No Locales Selected';
    } else {
        label.textContent = `${selected} of ${total} Locales`;
    }
}

function selectAllLocales(check) {
    document.querySelectorAll('.locale-check').forEach(cb => cb.checked = check);
    updateLocaleCount();
}

// Bidirectional Synchronized Scrollbar
document.addEventListener('DOMContentLoaded', function() {
    const tableWrapper = document.getElementById('matrixTableWrapper');
    const table = document.getElementById('matrixTable');
    const floatingBar = document.getElementById('floatingScrollbar');
    const floatingContent = document.getElementById('floatingScrollbarContent');

    if (!tableWrapper || !table || !floatingBar || !floatingContent) return;

    function syncDimensions() {
        const scrollWidth = table.scrollWidth;
        floatingContent.style.width = scrollWidth + 'px';

        // Check if horizontal scrollbar is actually needed
        if (scrollWidth <= tableWrapper.clientWidth) {
            floatingBar.style.display = 'none';
        } else {
            floatingBar.style.display = 'block';
        }
    }

    let isSyncingFloating = false;
    let isSyncingTable = false;

    floatingBar.addEventListener('scroll', function() {
        if (!isSyncingFloating) {
            isSyncingTable = true;
            tableWrapper.scrollLeft = floatingBar.scrollLeft;
        }
        isSyncingFloating = false;
    });

    tableWrapper.addEventListener('scroll', function() {
        if (!isSyncingTable) {
            isSyncingFloating = true;
            floatingBar.scrollLeft = tableWrapper.scrollLeft;
        }
        isSyncingTable = false;
    });

    window.addEventListener('resize', syncDimensions);
    setTimeout(syncDimensions, 200);
});
</script>
@endsection
