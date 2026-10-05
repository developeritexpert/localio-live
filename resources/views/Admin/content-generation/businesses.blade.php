@extends('admin_layout.master')

@section('title', 'Content Generation - Businesses')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">

        {{-- Page Header --}}
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Content Generation</h3>
                    <p class="nk-block-des text-soft">
                        Select businesses and fields, then submit a batch to OpenAI or Claude.
                        @if($lastPollAt)
                            &bull; <span class="text-success"><em class="icon ni ni-clock me-1"></em>Last poll: {{ \Carbon\Carbon::parse($lastPollAt)->diffForHumans() }}</span>
                        @else
                            &bull; <span class="text-warning"><em class="icon ni ni-alert-circle me-1"></em>Scheduler poll not recorded yet</span>
                        @endif
                    </p>
                </div>
                <div class="nk-block-head-content d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importJsonModal">
                        <em class="icon ni ni-plus-circle me-1"></em> Add Businesses (JSON)
                    </button>
                    <a href="{{ route('content-gen.poll-now') }}"  class="btn btn-info btn-sm">
                        <em class="icon ni ni-reload me-1"></em> Poll Status Now
                    </a>
                    <a href="{{ route('content-gen.batch-jobs') }}" class="btn btn-secondary btn-sm">
                        <em class="icon ni ni-list me-1"></em> <span>Batch Jobs</span>
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

        {{-- Filters --}}
        <div class="card card-bordered mb-3">
            <div class="card-inner py-2">
                <form method="GET" action="{{ route('content-gen.businesses') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1 small">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                    <div class="col-md-4">
                        <label class="form-label mb-1 small">Search business</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Business name...">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('content-gen.businesses') }}" class="btn btn-sm btn-light">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Batch Submit Form --}}
        <form id="batchForm" action="{{ route('content-gen.submit-batch') }}" method="POST">
            @csrf

            {{-- Batch Controls Bar --}}
            <div class="card card-bordered mb-3">
                <div class="card-inner py-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <span class="text-soft small">Provider:</span>
                        </div>
                        <div class="col-auto">
                            <select name="provider" class="form-select form-select-sm" style="width:200px" title="Batch Generation Mode">
                                <option value="manual" selected>Manual Batch (JSON for ChatGPT)</option>
                                <option value="openai">OpenAI (ChatGPT API)</option>
                                <option value="anthropic">Anthropic (Claude API)</option>
                            </select>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-1">
                            <span class="text-soft small">Batch Size:</span>
                            <input type="number" name="batch_size" class="form-control form-control-sm text-center" value="{{ $batchSize ?? 5 }}" min="1" max="50" style="width:65px" title="Businesses merged per batch job">
                            <span class="text-soft small">/ batch</span>
                        </div>
                        <div class="col-auto">
                            <span class="text-soft small">Fields:</span>
                        </div>
                        @foreach($fields as $field)
                        <div class="col-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input field-checkbox" type="checkbox" name="fields[]" value="{{ $field }}" id="field_{{ $field }}" checked>
                                <label class="form-check-label small" for="field_{{ $field }}">{{ \App\Models\AiContentQueue::$fieldLabels[$field] }}</label>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-auto ms-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="deselectAll()">Clear</button>
                            <button type="submit" class="btn btn-sm btn-primary ms-2" id="submitBatchBtn" disabled>
                                <em class="icon ni ni-send"></em> Submit Batch (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Businesses Table --}}
            <div class="card card-bordered card-stretch">
                <div class="card-inner p-0">
                    <div class="table-responsive">
                    <table class="table table-hover content-gen-table" id="businessTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:40px">
                                    <input type="checkbox" id="selectAllCheck" class="form-check-input" title="Select all on page">
                                </th>
                                <th>Business</th>
                                <th>Category</th>
                                <th class="text-center" style="min-width:120px">Overall Status</th>
                                <th class="text-center" style="min-width:85px">Logo</th>
                                @foreach($fields as $field)
                                    <th class="text-center" style="min-width:105px">
                                        {{ \App\Models\AiContentQueue::$fieldLabels[$field] }}
                                        @php $cfgPrompt = $configuredPrompts[$field] ?? null; @endphp
                                        @if($cfgPrompt && $cfgPrompt->hasOpenAIPromptId())
                                            <span class="badge bg-success" style="font-size:9px" title="Saved OpenAI Prompt ID: {{ $cfgPrompt->openai_prompt_id }}">ID</span>
                                        @elseif($cfgPrompt)
                                            <span class="badge bg-info" style="font-size:9px" title="Prompt configured (custom text)">Text</span>
                                        @else
                                            <span class="badge bg-light text-muted" style="font-size:9px" title="Default prompt text will be used">Default</span>
                                        @endif
                                    </th>
                                @endforeach
                                <th class="text-center" style="min-width:150px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($businesses as $business)
                                @php
                                    $name        = optional($business->translations->first())->name ?? 'Business #' . $business->id;
                                    $catName     = optional($business->category)->name ?? '-';
                                    $isLive      = $business->status == 1 || $business->status === 'active';
                                    $bizFields   = $fieldStatusMap[$business->id] ?? [];

                                    $hasAny        = count($bizFields) > 0;
                                    $allComplete   = $hasAny && count(array_filter($fields, fn($f) => ($bizFields[$f]?->status ?? '') === 'completed')) === count($fields);
                                    $hasCompleted  = count(array_filter($fields, fn($f) => ($bizFields[$f]?->status ?? '') === 'completed')) > 0;
                                    $hasFailed     = count(array_filter($fields, fn($f) => ($bizFields[$f]?->status ?? '') === 'failed')) > 0;
                                    $hasProcessing = count(array_filter($fields, fn($f) => in_array($bizFields[$f]?->status ?? '', ['processing', 'pending']))) > 0;
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
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="status-dot {{ $isLive ? 'bg-success' : 'bg-danger' }}" title="{{ $isLive ? 'Live' : 'Offline' }}"></span>
                                            <span class="fw-medium">{{ $name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-soft">{{ $catName }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(!$hasAny)
                                            <span class="badge badge-dim bg-light text-dark">Not started</span>
                                        @elseif($hasProcessing && !$hasFailed)
                                            <span class="badge bg-info"><em class="icon ni ni-loader me-1"></em>Processing</span>
                                        @elseif($allComplete)
                                            <span class="badge bg-success"><em class="icon ni ni-check me-1"></em>Completed</span>
                                        @elseif($hasFailed && !$allComplete)
                                            <span class="badge bg-danger"><em class="icon ni ni-cross me-1"></em>Failed</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        @endif
                                    </td>
                                    <td class="text-center" id="logo-cell-{{ $business->id }}">
                                        <input type="file" id="logo-input-{{ $business->id }}" class="d-none" accept=".svg,image/svg+xml,image/*" onchange="uploadBusinessLogo({{ $business->id }}, this)">
                                        @if($business->hasLogo())
                                            @php $rawIcon = $business->getRawOriginal('icon_id') ?? $business->icon_id; @endphp
                                            @if($rawIcon)
                                                <img src="{{ asset($rawIcon) }}" alt="Logo" class="rounded border p-1" style="width: 32px; height: 32px; object-fit: contain; background: #fff; cursor: pointer;" title="Logo uploaded. Click to change / replace logo." onclick="document.getElementById('logo-input-{{ $business->id }}').click()">
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-icon btn-dim btn-outline-secondary btn-sm" title="Upload SVG Logo" onclick="document.getElementById('logo-input-{{ $business->id }}').click()" style="cursor: pointer; width: 30px; height: 30px;">
                                                <em class="icon ni ni-upload-cloud" style="font-size: 16px;"></em>
                                            </button>
                                        @endif
                                    </td>
                                    @foreach($fields as $field)
                                        @php $qi = $bizFields[$field] ?? null; @endphp
                                        <td class="text-center">
                                            @if(!$qi)
                                                @if($field === 'rating' && $business->admin_rating !== null && (float)$business->admin_rating > 0)
                                                    <span class="badge badge-dim bg-warning text-dark" title="Current admin rating: {{ number_format((float)$business->admin_rating, 1) }} (active until 10 reviews)">
                                                        <em class="icon ni ni-star-fill text-warning me-1"></em>{{ number_format((float)$business->admin_rating, 1) }}
                                                    </span>
                                                    <button type="button"
                                                        class="btn btn-icon btn-xs btn-dim btn-outline-secondary ms-1"
                                                        title="Generate rating with AI"
                                                        onclick="retryField({{ $business->id }}, 'rating')"
                                                    >
                                                        <em class="icon ni ni-reload"></em>
                                                    </button>
                                                @else
                                                    <span class="text-muted small">--</span>
                                                @endif
                                            @elseif($qi->status === 'completed')
                                                @if($field === 'rating' && $business->admin_rating !== null)
                                                    <span class="badge badge-dim bg-success" title="Admin rating: {{ number_format((float)$business->admin_rating, 1) }} (active until 10 reviews)">
                                                        <em class="icon ni ni-star-fill text-warning me-1"></em>{{ number_format((float)$business->admin_rating, 1) }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-dim bg-success" title="{{ Str::limit($qi->result, 100) }}">
                                                        <em class="icon ni ni-check"></em>
                                                    </span>
                                                @endif
                                                <a href="{{ route('content-gen.view-result', $qi->id) }}" class="btn btn-icon btn-xs btn-dim btn-outline-primary ms-1" title="View Result">
                                                    <em class="icon ni ni-eye"></em>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-icon btn-xs btn-dim btn-outline-secondary ms-1"
                                                    title="Re-generate"
                                                    onclick="retryField({{ $business->id }}, '{{ $field }}')"
                                                >
                                                    <em class="icon ni ni-reload"></em>
                                                </button>
                                            @elseif($qi->status === 'processing' || $qi->status === 'pending')
                                                <span class="badge badge-dim bg-info"><em class="icon ni ni-loader"></em></span>
                                            @elseif($qi->status === 'failed')
                                                <span class="badge badge-dim bg-danger" title="{{ $qi->error_message }}"><em class="icon ni ni-cross"></em></span>
                                                <button type="button"
                                                    class="btn btn-icon btn-xs btn-dim btn-outline-warning ms-1"
                                                    title="Retry"
                                                    onclick="retryField({{ $business->id }}, '{{ $field }}')"
                                                >
                                                    <em class="icon ni ni-reload"></em>
                                                </button>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <div class="d-inline-flex">
                                            <button type="button" class="btn btn-xs text-white fw-bold me-1" style="background-color: #ff6a00; border-color: #ff6a00;" onclick="generateAll({{ $business->id }})">
                                                Generate all
                                            </button>
                                            @if($hasCompleted)
                                                <button type="button" class="btn btn-xs btn-primary" title="Apply all completed fields to this business" onclick="applyAllCompleted({{ $business->id }})">
                                                    <em class="icon ni ni-save me-1"></em> Apply
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 6 + count($fields) }}" class="text-center text-muted py-5">
                                        No businesses found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
                @if($businesses->hasPages())
                    <div class="card-inner">
                        {{ $businesses->links() }}
                    </div>
                @endif
            </div>
        </form>

        {{-- Retry hidden form --}}
        <form id="retryForm" action="{{ route('content-gen.retry-failed') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="business_id" id="retry_business_id">
            <input type="hidden" name="field" id="retry_field">
            <input type="hidden" name="provider" id="retry_provider" value="openai">
        </form>

        {{-- Apply All Completed hidden form --}}
        <form id="applyAllForm" action="{{ route('content-gen.apply-all') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="business_id" id="apply_business_id">
        </form>

    </div>
</div>

<style>
.status-dot { display:inline-block; width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.content-gen-table th, .content-gen-table td { vertical-align: middle; }
.content-gen-table th { font-size:0.8rem; text-transform: uppercase; letter-spacing:.04em; color:#8094ae; font-weight:600; }
.badge-dim.bg-success { background:#d1f8eb !important; color:#1ee0ac !important; border:1px solid #aef3d8; }
.badge-dim.bg-danger  { background:#fde8e8 !important; color:#e85347 !important; border:1px solid #f8b9b9; }
.badge-dim.bg-info    { background:#dff0ff !important; color:#559bfb !important; border:1px solid #b0d4fd; }
.badge-dim.bg-light   { background:#f5f6fa !important; color:#8094ae !important; border:1px solid #dbdfea; }
</style>

<script>
function updateCount() {
    const count = document.querySelectorAll('.business-check:checked').length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('submitBatchBtn').disabled = count === 0;
}

function selectAll() {
    document.querySelectorAll('.business-check').forEach(c => c.checked = true);
    document.getElementById('selectAllCheck').checked = true;
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('.business-check').forEach(c => c.checked = false);
    document.getElementById('selectAllCheck').checked = false;
    updateCount();
}

document.getElementById('selectAllCheck').addEventListener('change', function() {
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
                <img src="${data.logo_url}" alt="Logo" class="rounded border p-1" style="width: 32px; height: 32px; object-fit: contain; background: #fff; cursor: pointer;" title="Logo uploaded. Click to change / replace logo." onclick="document.getElementById('logo-input-${businessId}').click()">
            `;
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

function generateAll(businessId) {
    document.querySelectorAll('.business-check').forEach(c => c.checked = false);
    const bCheck = document.querySelector('.business-check[value="' + businessId + '"]');
    if (bCheck) bCheck.checked = true;
    document.querySelectorAll('.field-checkbox').forEach(c => c.checked = true);
    updateCount();
    if (confirm('Generate all fields for this business?')) {
        document.getElementById('batchForm').submit();
    }
}

function retryField(businessId, field) {
    const provider = document.querySelector('[name=provider]').value;
    if (confirm('Generate / re-generate "' + field + '" for this business?')) {
        document.getElementById('retry_business_id').value = businessId;
        document.getElementById('retry_field').value = field;
        document.getElementById('retry_provider').value = provider;
        document.getElementById('retryForm').submit();
    }
}

function applyAllCompleted(businessId) {
    if (confirm('Apply all completed AI generated content to this business record?')) {
        document.getElementById('apply_business_id').value = businessId;
        document.getElementById('applyAllForm').submit();
    }
}

function insertSampleJson() {
    const sample = JSON.stringify([
        {
            "name": "HostGator",
            "url": "https://www.hostgator.com",
            "category": "Web Hosting",
            "short_description": "HostGator is a leading provider of secure, scalable, and affordable web hosting solutions.",
            "rating": 4.6,
            "usps": [
                "99.9% uptime guarantee",
                "Free domain registration for the first year",
                "24/7/365 phone, chat, and email support"
            ],
            "reviews_description": "HostGator provides verified reliable cloud, VPS, and shared hosting for modern businesses."
        },
        {
            "name": "Bluehost",
            "url": "https://www.bluehost.com",
            "category": "Web Hosting",
            "short_description": "Recommended by WordPress.org, Bluehost delivers fast WordPress and cloud solutions.",
            "rating": 4.8
        }
    ], null, 2);
    document.getElementById('jsonInputTextarea').value = sample;
}
</script>

{{-- Add Businesses with JSON Modal --}}
<div class="modal fade" id="importJsonModal" tabindex="-1" aria-labelledby="importJsonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="importJsonModalLabel">
                    <em class="icon ni ni-plus-circle text-primary me-2"></em>Add Businesses via JSON
                </h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form id="importJsonForm" action="{{ route('content-gen.import-json') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Add one or multiple businesses at once by uploading a JSON/JSONL file or pasting JSON below. Supports single objects, arrays, or wrapped payloads (<code>{"businesses": [...]}</code>). Markdown code blocks are automatically cleaned.
                    </p>

                    {{-- Option 1: File Upload --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small mb-1">
                            <em class="icon ni ni-upload-cloud me-1 text-primary"></em> Upload .json or .jsonl File
                        </label>
                        <input type="file" name="json_file" id="jsonFileInput" class="form-control form-control-sm" accept=".json,.jsonl,application/json,text/plain">
                        <span class="text-muted small">Select a file from your computer (ideal for large batches of 50+ businesses).</span>
                    </div>

                    <div class="text-center my-2 position-relative">
                        <hr class="m-0">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-bold">OR PASTE JSON BELOW</span>
                    </div>

                    {{-- Option 2: Paste JSON Textarea --}}
                    <div class="mb-2 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold small mb-0">Paste JSON / JSONL Payload</label>
                            <button type="button" class="btn btn-xs btn-outline-light text-dark" onclick="insertSampleJson()">
                                <em class="icon ni ni-copy me-1"></em> Insert Sample JSON
                            </button>
                        </div>
                        <textarea name="json_data" id="jsonInputTextarea" class="form-control font-monospace text-dark" rows="9" style="font-size: 13px; line-height: 1.5; background: #f8fafc;" placeholder='[
  {
    "name": "HostGator",
    "url": "https://www.hostgator.com",
    "category": "Web Hosting"
  },
  {
    "name": "Bluehost",
    "url": "https://www.bluehost.com",
    "category": "Web Hosting"
  }
]'></textarea>
                    </div>

                    {{-- Deduplication Toggle --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="update_existing" value="1" id="updateExistingCheck" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="updateExistingCheck">
                            Update existing businesses if already in directory (matched by name or URL)
                        </label>
                    </div>

                    <div class="alert alert-light border mt-3 py-2 px-3 small text-muted">
                        <em class="icon ni ni-info-fill text-info me-1"></em>
                        <strong>Supported fields:</strong> <code>name</code>, <code>url</code>, <code>category</code> / <code>category_id</code>, <code>subcategories</code>, <code>short_description</code>, <code>what_is</code>, <code>rating</code>, <code>usps</code>, <code>pros_cons</code>, <code>faqs</code>, and <code>services</code>.
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <em class="icon ni ni-check me-1"></em> Import Businesses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.content-gen-table th {
    white-space: nowrap;
    font-size: 11px;
    letter-spacing: 0.3px;
    padding: 10px 8px !important;
}
.content-gen-table td {
    padding: 10px 8px !important;
    vertical-align: middle;
}
</style>
@endsection
