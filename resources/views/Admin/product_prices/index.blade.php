@extends('admin_layout.master')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                
                {{-- Header & Breadcrumbs --}}
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Products & Prices</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage products, pricing plans, feature mappings, and price-specific FAQs for all businesses.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#jsonBatchModal">
                                <em class="icon ni ni-upload-cloud"></em>
                                <span>Batch JSON Upload</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Flash Messages --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                {{-- Tabs & Filters Card --}}
                <div class="card card-bordered card-preview">
                    <div class="card-inner border-bottom">
                        <div class="d-flex justify-content-between align-items-center flex-wrap ">
                            {{-- Tabs --}}
                            <ul class="nav nav-tabs nav-tabs-card mb-0">
                                <li class="nav-item">
                                    <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}" href="{{ route('admin.products-prices.index', ['tab' => 'all', 'search' => $search]) }}">
                                        All Businesses <span class="badge bg-light text-dark ms-1">{{ $counts['all'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $tab === 'with_products' ? 'active' : '' }}" href="{{ route('admin.products-prices.index', ['tab' => 'with_products', 'search' => $search]) }}">
                                        With Products / Plans <span class="badge bg-success ms-1">{{ $counts['with_products'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $tab === 'without_products' ? 'active' : '' }}" href="{{ route('admin.products-prices.index', ['tab' => 'without_products', 'search' => $search]) }}">
                                        Without Products / Plans <span class="badge bg-warning ms-1">{{ $counts['without_products'] }}</span>
                                    </a>
                                </li>
                            </ul>

                            {{-- Search Form --}}
                            <form method="GET" action="{{ route('admin.products-prices.index') }}" class="d-flex ">
                                <input type="hidden" name="tab" value="{{ $tab }}">
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left">
                                        <em class="icon ni ni-search"></em>
                                    </div>
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search business name..." value="{{ $search }}">
                                </div>
                                <button type="submit" class="btn btn-sm btn-secondary">Search</button>
                                @if(!empty($search))
                                <a href="{{ route('admin.products-prices.index', ['tab' => $tab]) }}" class="btn btn-sm btn-light">Reset</a>
                                @endif
                            </form>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="card-inner p-0">
                        <table class="table table-tranx is-compact">
                            <thead>
                                <tr class="tb-tnx-head">
                                    <th class="tb-tnx-id" style="width: 32%;"><span>Business</span></th>
                                    <th><span>Category</span></th>
                                    <th><span>Products & Plans</span></th>
                                    <th><span>Currencies</span></th>
                                    <th style="min-width: 170px;"><span>Last Updated</span></th>
                                    <th class="tb-tnx-action text-end"><span>Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businesses as $biz)
                                @php
                                    $bizTrans = $biz->translations->first();
                                    $bizName = $bizTrans->name ?? ('Business #' . $biz->id);
                                    $catName = $biz->category?->translations?->first()?->name ?? '—';
                                    
                                    $productCount = $biz->offerProducts->count();
                                    $planCount = $biz->offerProducts->sum(fn($p) => $p->plans->count());

                                    // Collect currencies
                                    $currencies = [];
                                    foreach ($biz->offerProducts as $prod) {
                                        foreach ($prod->plans as $pl) {
                                            foreach ($pl->prices as $pr) {
                                                if ($pr->currency) $currencies[$pr->currency] = true;
                                            }
                                        }
                                        foreach ($prod->directPrices as $pr) {
                                            if ($pr->currency) $currencies[$pr->currency] = true;
                                        }
                                    }

                                    // Last Updated Column calculation
                                    $lastUpdatedTime = $biz->pricing_updated_at ?: $biz->updated_at;
                                @endphp
                                <tr>
                                    <td class="tb-tnx-id">
                                        <div class="user-card">
                                            @if($biz->icon_id && $biz->icon)
                                                <div class="user-avatar bg-light border me-2">
                                                    <img src="{{ asset($biz->icon) }}" alt="{{ $bizName }}" style="max-height: 28px; width: auto; object-fit: contain;">
                                                </div>
                                            @else
                                                <div class="user-avatar bg-primary-dim me-2">
                                                    <span>{{ strtoupper(substr($bizName, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                            <div class="user-info">
                                                <span class="tb-lead font-weight-bold">{{ $bizName }}</span>
                                                <span class="sub-text text-muted">ID: #{{ $biz->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-outline-secondary">{{ $catName }}</span>
                                    </td>
                                    <td>
                                        @if($productCount > 0)
                                            <span class="badge bg-info">
                                                {{ $productCount }} {{ Str::plural('Product', $productCount) }}
                                            </span>
                                            @if($planCount > 0)
                                                <span class="badge bg-primary ms-1">
                                                    {{ $planCount }} {{ Str::plural('Plan', $planCount) }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">No products / plans</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($currencies))
                                            @foreach(array_keys($currencies) as $curr)
                                                <span class="badge bg-light text-dark border">{{ $curr }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lastUpdatedTime)
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold text-dark" style="font-size: 13px;">
                                                    {{ \Carbon\Carbon::parse($lastUpdatedTime)->format('M d, Y H:i') }}
                                                </span>
                                                <span class="text-muted" style="font-size: 11px;">
                                                    ({{ \Carbon\Carbon::parse($lastUpdatedTime)->diffForHumans() }})
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td class="tb-tnx-action text-end">
                                        <a href="{{ route('admin.products-prices.manage', $biz->id) }}" class="btn btn-sm btn-primary">
                                            <em class="icon ni ni-setting"></em>
                                            <span>Manage Products & Prices</span>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No businesses found matching your criteria.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($businesses->hasPages())
                    <div class="card-inner border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $businesses->firstItem() ?? 0 }} to {{ $businesses->lastItem() ?? 0 }} of {{ $businesses->total() }} businesses
                            </div>
                            <div>
                                {{ $businesses->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Batch JSON Modal --}}
<div class="modal fade" id="jsonBatchModal" tabindex="-1" aria-labelledby="jsonBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.products-prices.batch-import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="jsonBatchModalLabel">Batch JSON Upload (Products & Prices)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Upload a <code>.json</code> file or paste a JSON structure containing businesses, products, plans, specifications, prices, and feature mappings.
                    </p>

                    <div class="mb-3">
                        <label for="json_file" class="form-label font-weight-bold">Upload JSON file</label>
                        <input class="form-control" type="file" id="json_file" name="json_file" accept=".json">
                    </div>

                    <div class="text-center my-2 text-muted">— OR —</div>

                    <div class="mb-3">
                        <label for="json_content" class="form-label font-weight-bold">Paste JSON payload</label>
                        <textarea class="form-control font-monospace" id="json_content" name="json_content" rows="12" placeholder='{
  "batch_id": "IMPORT-2026",
  "businesses": [
    {
      "business_id": 44,
      "products": [ ... ]
    }
  ]
}'></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
