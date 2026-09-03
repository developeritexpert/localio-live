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
                    <a href="{{ route('content-gen.poll-now') }}" class="btn btn-info btn-sm">
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
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Live</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Offline</option>
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
                            <select name="provider" class="form-select form-select-sm" style="width:150px">
                                <option value="openai">OpenAI (ChatGPT)</option>
                                <option value="anthropic">Anthropic (Claude)</option>
                            </select>
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
                    <table class="table table-hover content-gen-table" id="businessTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:40px">
                                    <input type="checkbox" id="selectAllCheck" class="form-check-input" title="Select all on page">
                                </th>
                                <th>Business</th>
                                <th>Category</th>
                                <th class="text-center" style="min-width:120px">Overall Status</th>
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
                                    $isLive      = $business->status === 'active';
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
                                    @foreach($fields as $field)
                                        @php $qi = $bizFields[$field] ?? null; @endphp
                                        <td class="text-center">
                                            @if(!$qi)
                                                <span class="text-muted small">--</span>
                                            @elseif($qi->status === 'completed')
                                                <span class="badge badge-dim bg-success" title="{{ Str::limit($qi->result, 100) }}">
                                                    <em class="icon ni ni-check"></em>
                                                </span>
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
                                            <button type="button" class="btn btn-xs btn-primary" onclick="generateAll({{ $business->id }})">
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
                                    <td colspan="{{ 5 + count($fields) }}" class="text-center text-muted py-5">
                                        No businesses found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
</script>
@endsection
