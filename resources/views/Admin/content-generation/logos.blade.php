@extends('admin_layout.master')

@section('title', 'Content Generation - Logos')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">

        {{-- Page Header --}}
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Logo Generation</h3>
                    <p class="nk-block-des text-soft">
                        Select businesses to batch for logo creation, or upload SVG logos directly.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex gap-2">
                    <a href="{{ route('content-gen.logos.batch-jobs') }}" class="btn btn-primary btn-sm">
                        <em class="icon ni ni-layers me-1"></em> <span>Logo Batch Jobs</span>
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
                <em class="icon ni ni-cross-circle me-1"></em> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Status Tabs: Pending / Completed / All --}}
        <ul class="nav nav-tabs nav-tabs-mb-icon mb-3">
            <li class="nav-item {{ $activeTab === 'pending' ? 'active' : '' }}">
                <a class="nav-link {{ $activeTab === 'pending' ? 'active' : '' }}" href="{{ route('content-gen.logos', array_merge(request()->except(['tab', 'page']), ['tab' => 'pending'])) }}">
                    <em class="icon ni ni-clock me-1 text-warning"></em>
                    <span class="fw-semibold">Pending Logos</span>
                    <span class="badge rounded-pill bg-warning text-dark ms-1" style="font-size: 11px;">{{ $pendingCount }}</span>
                </a>
            </li>
            <li class="nav-item {{ $activeTab === 'completed' ? 'active' : '' }}">
                <a class="nav-link {{ $activeTab === 'completed' ? 'active' : '' }}" href="{{ route('content-gen.logos', array_merge(request()->except(['tab', 'page']), ['tab' => 'completed'])) }}">
                    <em class="icon ni ni-check-circle me-1 text-success"></em>
                    <span class="fw-semibold">Completed Logos</span>
                    <span class="badge rounded-pill bg-success ms-1" style="font-size: 11px;">{{ $completedCount }}</span>
                </a>
            </li>
            <li class="nav-item {{ $activeTab === 'all' ? 'active' : '' }}">
                <a class="nav-link {{ $activeTab === 'all' ? 'active' : '' }}" href="{{ route('content-gen.logos', array_merge(request()->except(['tab', 'page']), ['tab' => 'all'])) }}">
                    <em class="icon ni ni-list me-1 text-primary"></em>
                    <span>All Businesses</span>
                    <span class="badge rounded-pill bg-light text-dark border ms-1" style="font-size: 11px;">{{ $allCount }}</span>
                </a>
            </li>
        </ul>

        {{-- Filters --}}
        <div class="card card-bordered mb-3">
            <div class="card-inner py-2">
                <form method="GET" action="{{ route('content-gen.logos') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" class="fw-bold" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->translation->name ?? ($cat->name ?? 'Category #' . $cat->id) }}
                                </option>
                                @if(isset($cat->children) && $cat->children->isNotEmpty())
                                    @foreach($cat->children as $sub)
                                        <option value="{{ $sub->id }}" {{ request('category_id') == $sub->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;— {{ $sub->translation->name ?? ($sub->name ?? 'Sub #' . $sub->id) }}
                                        </option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1 small">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="1" {{ (request('status') === '1' || request('status') === 'active') ? 'selected' : '' }}>Live</option>
                            <option value="0" {{ (request('status') === '0' || request('status') === 'inactive') ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Search business</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Title, slug, ID...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1 small">Show</label>
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="30" {{ (request('per_page', 30) == 30) ? 'selected' : '' }}>30 entries</option>
                            <option value="100" {{ (request('per_page') == 100) ? 'selected' : '' }}>100 entries</option>
                            <option value="200" {{ (request('per_page') == 200) ? 'selected' : '' }}>200 entries</option>
                            <option value="500" {{ (request('per_page') == 500) ? 'selected' : '' }}>500 entries</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('content-gen.logos', ['tab' => $activeTab]) }}" class="btn btn-sm btn-light">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Logo Batch Submit Form --}}
        <form id="logoBatchForm" action="{{ route('content-gen.logos.submit-batch') }}" method="POST">
            @csrf
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            {{-- Batch Controls Bar --}}
            <div class="card card-bordered mb-3">
                <div class="card-inner py-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <span class="text-soft small fw-semibold">Task:</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-primary fs-12px"><em class="icon ni ni-img me-1"></em>SVG Logo Generation</span>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-1 ms-2">
                            <span class="text-soft small fw-semibold">Batch Size:</span>
                            <input type="number" name="batch_size" class="form-control form-control-sm text-center" value="{{ $batchSize ?? 25 }}" min="1" max="100" style="width:65px" title="Businesses merged per batch job">
                            <span class="text-soft small">/ batch</span>
                        </div>

                        <div class="col-auto ms-auto d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Clear</button>
                            <button type="submit" class="btn btn-sm btn-primary ms-1" id="submitBatchBtn" disabled>
                                <em class="icon ni ni-send me-1"></em> Send to Batches (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Businesses Table --}}
            <div class="card card-bordered card-stretch">
                <div class="card-inner p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="logoTable">
                            <thead class="table-light fs-12px text-muted">
                                <tr>
                                    <th class="text-center" style="width:40px">
                                        <input type="checkbox" id="selectAllCheck" class="form-check-input" title="Select all on page">
                                    </th>
                                    <th style="min-width: 250px;">Business</th>
                                    <th style="min-width: 150px;">Category</th>
                                    <th style="min-width: 160px;">Official Website URL</th>
                                    <th class="text-center" style="width: 100px;">Logo</th>
                                    <th class="text-center" style="width: 130px;">Logo Status</th>
                                    <th class="text-center" style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businesses as $business)
                                    @php
                                        $name    = optional($business->translations->first())->name ?? $business->title ?? 'Business #' . $business->id;
                                        $catName = optional($business->category)->name ?? '—';
                                        $hasLogo = $business->hasLogo();
                                        $rawIcon = $business->getRawOriginal('icon_id') ?? $business->icon_id;
                                        $bizUrl  = $business->permanent_url ?: (optional($business->websites->first())->website_url ?: '');
                                        $qItem   = $logoQueueItems[$business->id] ?? null;
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   name="business_ids[]"
                                                   value="{{ $business->id }}"
                                                   class="form-check-input business-check"
                                                   onchange="updateCount()">
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark fs-13px">{{ $name }}</div>
                                            <span class="text-muted fs-11px">ID: {{ $business->id }} &bull; Slug: {{ $business->slug ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $catName }}</span>
                                        </td>
                                        <td>
                                            @if($bizUrl)
                                                <a href="{{ $bizUrl }}" target="_blank" rel="noopener noreferrer" class="text-primary text-truncate d-inline-block fs-12px" style="max-width: 180px;" title="{{ $bizUrl }}">
                                                    <em class="icon ni ni-external me-1"></em>{{ preg_replace('#^https?://(www\.)?#', '', rtrim($bizUrl, '/')) }}
                                                </a>
                                            @else
                                                <span class="text-muted fs-11px">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center" id="logo-cell-{{ $business->id }}">
                                            <input type="file" id="logo-input-{{ $business->id }}" class="d-none" accept=".svg,image/svg+xml,image/*" onchange="uploadBusinessLogo({{ $business->id }}, this)">
                                            @if($hasLogo && $rawIcon)
                                                <img src="{{ asset($rawIcon) }}" alt="Logo" class="rounded border p-1" style="width: 36px; height: 36px; object-fit: contain; background: #fff; cursor: pointer;" title="Logo uploaded. Click to replace." onclick="document.getElementById('logo-input-{{ $business->id }}').click()">
                                            @else
                                                <button type="button" class="btn btn-icon btn-dim btn-outline-secondary btn-sm" title="Upload SVG Logo" onclick="document.getElementById('logo-input-{{ $business->id }}').click()" style="width: 34px; height: 34px;">
                                                    <em class="icon ni ni-upload-cloud" style="font-size: 16px;"></em>
                                                </button>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($hasLogo)
                                                <span class="badge bg-success"><em class="icon ni ni-check me-1"></em>Completed</span>
                                            @elseif($qItem && in_array($qItem->status, ['pending', 'processing']))
                                                <span class="badge bg-info"><em class="icon ni ni-loader me-1"></em>Batched</span>
                                            @else
                                                <span class="badge bg-warning text-dark">No Logo</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="document.getElementById('logo-input-{{ $business->id }}').click()">
                                                <em class="icon ni ni-upload me-1"></em> Upload SVG
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No businesses found matching your criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination Footer & Per-Page Selector --}}
                <div class="card-inner py-2 d-flex flex-wrap align-items-center justify-content-between  border-top">
                    <div>
                        {{ $businesses->links() }}
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-12px text-muted">Show per page:</span>
                        <div class="btn-group btn-group-sm">
                            @foreach([30, 100, 200, 500] as $opt)
                                <a href="{{ request()->fullUrlWithQuery(['per_page' => $opt]) }}"
                                   class="btn {{ (request('per_page', 30) == $opt) ? 'btn-primary' : 'btn-outline-light' }}">
                                    {{ $opt }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
function updateCount() {
    const count = document.querySelectorAll('.business-check:checked').length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('submitBatchBtn').disabled = (count === 0);

    const allBoxes = document.querySelectorAll('.business-check');
    const selectAllCheck = document.getElementById('selectAllCheck');
    if (selectAllCheck && allBoxes.length > 0) {
        selectAllCheck.checked = (count === allBoxes.length);
        selectAllCheck.indeterminate = (count > 0 && count < allBoxes.length);
    }
}

function selectAll() {
    document.querySelectorAll('.business-check').forEach(c => c.checked = true);
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('.business-check').forEach(c => c.checked = false);
    updateCount();
}

document.getElementById('selectAllCheck')?.addEventListener('change', function() {
    document.querySelectorAll('.business-check').forEach(c => c.checked = this.checked);
    updateCount();
});

function uploadBusinessLogo(businessId, input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const cell = document.getElementById('logo-cell-' + businessId);
    const originalHtml = cell.innerHTML;
    cell.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status"></span>';

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('business_id', businessId);
    formData.append('logo', file);

    fetch('{{ route("content-gen.upload-logo") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            cell.innerHTML = `
                <input type="file" id="logo-input-${businessId}" class="d-none" accept=".svg,image/svg+xml,image/*" onchange="uploadBusinessLogo(${businessId}, this)">
                <img src="${data.logo_url}" alt="Logo" class="rounded border p-1" style="width: 36px; height: 36px; object-fit: contain; background: #fff; cursor: pointer;" title="Logo uploaded. Click to replace." onclick="document.getElementById('logo-input-${businessId}').click()">
            `;
            const statusCell = cell.nextElementSibling;
            if (statusCell) {
                statusCell.innerHTML = '<span class="badge bg-success"><em class="icon ni ni-check me-1"></em>Completed</span>';
            }
        } else {
            alert(data.message || 'Logo upload failed');
            cell.innerHTML = originalHtml;
        }
    })
    .catch(err => {
        alert('Upload failed: ' + err.message);
        cell.innerHTML = originalHtml;
    });
}
</script>
@endsection
