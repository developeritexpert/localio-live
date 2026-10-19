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
                        <div class="nk-block-head-content d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#jsonExampleModal">
                                <em class="icon ni ni-file-code"></em>
                                <span>View Example JSON</span>
                            </button>
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
                                    <th class="tb-tnx-id" style="width: 28%;"><span>Business</span></th>
                                    <th><span>Category</span></th>
                                    <th><span>Products & Plans</span></th>
                                    <th><span>Currencies</span></th>
                                    <th><span>FAQ</span></th>
                                    <th style="min-width: 170px;"><span>Last Updated</span></th>
                                    <th class="tb-tnx-action text-end"><span>Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businesses as $biz)
                                @php
                                    $bizTrans = $biz->translations->first();
                                    $bizName = $bizTrans->name ?? ('Business #' . $biz->id);
                                    $catName = $biz->category?->translations?->name ?? $biz->category?->name ?? '—';
                                    
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

                                    // Product / Price FAQs count
                                    $faqCount = $biz->faqCategories->sum('faqs_count');

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
                                        @if($faqCount > 0)
                                            <span class="badge bg-info-dim text-info">
                                                <em class="icon ni ni-help me-1"></em>{{ $faqCount }} {{ Str::plural('FAQ', $faqCount) }}
                                            </span>
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
                                    <td colspan="7" class="text-center py-4 text-muted">
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

{{-- Batch JSON Upload Modal --}}
<div class="modal fade" id="jsonBatchModal" tabindex="-1" aria-labelledby="jsonBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.products-prices.batch-import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="jsonBatchModalLabel">
                        <em class="icon ni ni-upload-cloud text-primary me-2"></em>Batch JSON Upload (Products, Prices & FAQs)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Upload a <code>.json</code> or <code>.jsonl</code> file or paste a JSON structure containing businesses, products, plans, specifications, prices, features, and pricing FAQs.
                    </p>

                    <div class="mb-3">
                        <label for="json_file" class="form-label font-weight-bold small">Upload JSON file (.json or .jsonl)</label>
                        <input class="form-control" type="file" id="json_file" name="json_file" accept=".json,.jsonl,application/json,text/plain">
                    </div>

                    <div class="text-center my-3 position-relative">
                        <hr class="m-0">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-bold">OR PASTE JSON BELOW</span>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="json_content" class="form-label font-weight-bold small mb-0">Paste JSON payload</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary" onclick="openExampleModal()">
                                    <em class="icon ni ni-eye me-1"></em> View Example JSON
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertSampleJson()">
                                    <em class="icon ni ni-copy me-1"></em> Insert Sample JSON
                                </button>
                            </div>
                        </div>
                        <textarea class="form-control font-monospace" id="json_content" name="json_content" rows="12" style="font-size: 12.5px; line-height: 1.5; background: #f8fafc;" placeholder='{
  "batch_id": "IMPORT-2026",
  "businesses": [
    {
      "business_id": 44,
      "products": [ ... ],
      "pricing_faqs": [ ... ]
    }
  ]
}'></textarea>
                    </div>

                    <div class="alert alert-light border py-2 px-3 small text-muted">
                        <em class="icon ni ni-info-fill text-info me-1"></em>
                        <strong>Flexible Business Matching:</strong> Businesses are matched by <code>business_id</code> / <code>id</code>, <code>name</code> (or <code>business_name</code>), or <code>url</code> / <code>website</code>. Pricing FAQs are automatically placed under the <strong>Plans & Pricing</strong> category.
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <em class="icon ni ni-check me-1"></em> Process Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Example Format Modal --}}
<div class="modal fade" id="jsonExampleModal" tabindex="-1" aria-labelledby="jsonExampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="jsonExampleModalLabel">
                    <em class="icon ni ni-file-code text-primary me-2"></em>Products, Prices & FAQs JSON Format Example
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-primary py-2 px-3 mb-3 small">
                    <div class="fw-bold mb-1"><em class="icon ni ni-layers me-1"></em> Supported JSON Structure:</div>
                    <ul class="mb-0 ps-3">
                        <li><strong>Businesses:</strong> Match businesses using <code>business_id</code> (recommended), <code>name</code>, or <code>url</code>.</li>
                        <li><strong>Products:</strong> Contain product details, optional <code>categories</code> (with <code>primary_category_id</code>), and plans.</li>
                        <li><strong>Plans:</strong> Support <code>specifications</code> (e.g. storage, websites), <code>features</code> (array of feature IDs), and multiple currency <code>prices</code> (USD, EUR, GBP).</li>
                        <li><strong>Pricing FAQs:</strong> Provide an array under <code>pricing_faqs</code> with <code>question</code> and <code>answer</code>; they are automatically linked to the business.</li>
                    </ul>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold">Example Payload (Ready to copy or insert):</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-xs btn-primary" onclick="copyExampleJson(this)">
                            <em class="icon ni ni-copy me-1"></em> Copy Example JSON
                        </button>
                        <button type="button" class="btn btn-xs btn-success" onclick="insertExampleAndOpenUpload()">
                            <em class="icon ni ni-plus-circle me-1"></em> Insert to Upload Form
                        </button>
                    </div>
                </div>

                <pre class="bg-dark text-light p-3 rounded" style="max-height: 460px; overflow-y: auto; font-size: 12px; line-height: 1.5;"><code id="exampleJsonCode"></code></pre>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copyExampleJson(this)">
                    <em class="icon ni ni-copy me-1"></em> Copy JSON
                </button>
                <button type="button" class="btn btn-success" onclick="insertExampleAndOpenUpload()">
                    <em class="icon ni ni-plus-circle me-1"></em> Insert to Upload Form
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const sampleProductsPricesJson = `{
  "batch_id": "IMPORT-2026",
  "businesses": [
    {
      "business_id": 44,
      "name": "One.com",
      "products": [
        {
          "name": "Web Hosting",
          "description": "Fast and reliable web hosting with daily backups, free SSL, and custom email.",
          "categories": {
            "primary_category_id": 11,
            "primary_subcategory_id": 20,
            "category_ids": [11],
            "subcategory_ids": [20, 23]
          },
          "plans": [
            {
              "name": "Beginner",
              "short_description": "Great for small personal websites and simple blogs.",
              "badge": "Popular",
              "is_featured": false,
              "specifications": [
                { "code": "storage", "value": "50 GB", "unit": "GB" },
                { "code": "websites", "value": "1", "unit": "site" },
                { "code": "ram", "value": "512 MB", "unit": "MB" }
              ],
              "features": [2095, 2097, 2102],
              "featured_feature_ids": [2095],
              "prices": [
                {
                  "country_region": "all",
                  "currency": "USD",
                  "billing_period": "1m",
                  "regular_price": 3.99,
                  "renewal_price": 9.99,
                  "intro_price": 1.99,
                  "intro_duration": "3 months"
                },
                {
                  "country_region": "all",
                  "currency": "EUR",
                  "billing_period": "1m",
                  "regular_price": 3.49,
                  "renewal_price": 8.99
                }
              ]
            },
            {
              "name": "Explorer",
              "short_description": "For growing websites with higher traffic and more storage.",
              "badge": "Best Value",
              "is_featured": true,
              "specifications": [
                { "code": "storage", "value": "100 GB", "unit": "GB" },
                { "code": "websites", "value": "5", "unit": "sites" },
                { "code": "ram", "value": "1 GB", "unit": "GB" }
              ],
              "features": [2095, 2097, 2101, 2102],
              "featured_feature_ids": [2095, 2101],
              "prices": [
                {
                  "country_region": "all",
                  "currency": "USD",
                  "billing_period": "1m",
                  "regular_price": 7.99,
                  "renewal_price": 14.99
                }
              ]
            }
          ]
        }
      ],
      "pricing_faqs": [
        {
          "question": "Can I upgrade or downgrade my plan at any time?",
          "answer": "Yes, you can upgrade your plan or change billing cycles directly from your account control panel."
        },
        {
          "question": "Is there a money-back guarantee?",
          "answer": "Yes, we offer a 30-day money-back guarantee on all hosting plans."
        }
      ]
    }
  ]
}`;

document.addEventListener('DOMContentLoaded', function () {
    const codeEl = document.getElementById('exampleJsonCode');
    if (codeEl) {
        codeEl.textContent = sampleProductsPricesJson;
    }
});

function insertSampleJson() {
    const textarea = document.getElementById('json_content');
    if (textarea) {
        textarea.value = sampleProductsPricesJson;
        textarea.focus();
        textarea.classList.add('border-primary');
        setTimeout(() => textarea.classList.remove('border-primary'), 1000);
    }
}

function copyExampleJson(btn) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(sampleProductsPricesJson).then(() => {
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<em class="icon ni ni-check me-1"></em> Copied!';
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                }, 2000);
            }
        });
    }
}

function openExampleModal() {
    // Hide upload modal if open, then show example modal
    const batchModalEl = document.getElementById('jsonBatchModal');
    const exampleModalEl = document.getElementById('jsonExampleModal');
    
    if (batchModalEl) {
        const batchModal = bootstrap.Modal.getInstance(batchModalEl);
        if (batchModal) batchModal.hide();
    }
    
    if (exampleModalEl) {
        const exampleModal = new bootstrap.Modal(exampleModalEl);
        exampleModal.show();
    }
}

function insertExampleAndOpenUpload() {
    insertSampleJson();
    const exampleModalEl = document.getElementById('jsonExampleModal');
    const batchModalEl = document.getElementById('jsonBatchModal');
    
    if (exampleModalEl) {
        const exampleModal = bootstrap.Modal.getInstance(exampleModalEl);
        if (exampleModal) exampleModal.hide();
    }
    
    if (batchModalEl) {
        const batchModal = bootstrap.Modal.getOrCreateInstance(batchModalEl);
        setTimeout(() => batchModal.show(), 300);
    }
}
</script>
@endsection
