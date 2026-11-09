@extends('admin_layout.master')

@section('title', 'Content Generation - Category Features')

@section('content')
<style>
.pagination {
    margin-bottom: 0 !important;
}
.page-item.active .page-link {
    background-color: #0971fe !important;
    border-color: #0971fe !important;
    color: #fff !important;
}
.page-link {
    color: #526484;
}
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">

        {{-- Page Header --}}
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Category Features Content Generation</h3>
                    <p class="nk-block-des text-soft">
                        Generate and manage headlines, subtexts, and SEO copy for each category feature.
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
                    <a href="{{ route('admin.category-feature-content.index') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-edit me-1"></em> Manual Content List
                    </a>
                    <a href="{{ route('content-gen.batch-jobs') }}" class="btn btn-secondary btn-sm">
                        <em class="icon ni ni-list me-1"></em> Batch Jobs
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

        {{-- Configuration & Filters Card --}}
        <div class="card card-bordered mb-3">
            <div class="card-inner py-3">
                <form method="GET" action="{{ route('content-gen.features') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1 small fw-bold">Select Category / Subcategory</label>
                        <select name="category_id" class="form-select form-select-sm select2">
                            <option value="">All Categories & Subcategories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->display_name ?? (($cat->parent_id ? '— ' : '') . ($cat->translation?->name ?? $cat->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1 small fw-bold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1 small fw-bold">Search Feature Name</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="e.g. Free Domain, SSL, Chat Support...">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                        <a href="{{ route('content-gen.features') }}" class="btn btn-sm btn-light">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Batch Submit Form --}}
        <form id="batchForm" action="{{ route('content-gen.features.submit-batch') }}" method="POST">
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
                            <input type="number" name="batch_size" class="form-control form-control-sm text-center" value="{{ $batchSize ?? 1 }}" min="1" max="50" style="width:65px" title="Features merged per batch job">
                            <span class="text-soft small">/ batch</span>
                        </div>
                        <div class="col-auto">
                            <span class="text-soft small">Fields:</span>
                        </div>
                        @foreach($fields as $field)
                        <div class="col-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input field-checkbox" type="checkbox" name="fields[]" value="{{ $field }}" id="field_{{ $field }}" checked>
                                <label class="form-check-label small" for="field_{{ $field }}">{{ \App\Http\Controllers\Admin\ContentGenerationController::$featureFieldLabels[$field] ?? ucfirst($field) }}</label>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-auto ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">Select All Rows</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Clear Rows</button>
                            <button type="submit" class="btn btn-sm btn-primary" id="submitBatchBtn" disabled>
                                <em class="icon ni ni-send me-1"></em> Submit Batch (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Features Table --}}
            <div class="card card-bordered card-stretch">
                <div class="card-inner p-0">
                    <div class="table-responsive">
                        <table class="table table-hover content-gen-table mb-0" id="featureTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width:40px">
                                        <input type="checkbox" id="selectAllCheck" class="form-check-input" title="Select all on page">
                                    </th>
                                    <th style="min-width:180px">Feature Name</th>
                                    <th style="min-width:140px">Category / Subcategory</th>
                                    <th class="text-center" style="min-width:110px">Overall Status</th>
                                    @foreach($fields as $field)
                                        <th class="text-center" style="min-width:110px">
                                            {{ \App\Http\Controllers\Admin\ContentGenerationController::$featureFieldLabels[$field] ?? ucfirst($field) }}
                                        </th>
                                    @endforeach
                                    <th class="text-center" style="min-width:140px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($features as $feature)
                                    @php
                                        $fName   = optional($feature->translations->first())->name ?? 'Feature #' . $feature->id;
                                        $catName = optional($feature->category)->translation?->name ?? (optional($feature->category)->name ?? 'General');
                                        $content = $existingContents->get($feature->id);

                                        // Determine completeness
                                        $totalFields = count($fields);
                                        $completedFields = 0;
                                        $hasPending = false;
                                        $hasCompletedQueue = false;

                                        foreach($fields as $field) {
                                            $qi = $fieldStatusMap[$feature->id][$field] ?? null;
                                            if ($qi && $qi->status === 'completed') {
                                                $completedFields++;
                                                $hasCompletedQueue = true;
                                            } elseif ($qi && in_array($qi->status, ['processing', 'pending'])) {
                                                $hasPending = true;
                                            } elseif ($content) {
                                                $isSaved = match($field) {
                                                    'header'              => !empty($content->page_title) || !empty($content->description),
                                                    'top_rated'           => !empty($content->top_rated_listing_headline) || !empty($content->top_rated_listing_subtext),
                                                    'top_10'              => !empty($content->compare_alternatives_headline) || !empty($content->compare_alternatives_subtext),
                                                    'pros_cons'           => !empty($content->community_says_headline) || !empty($content->community_says_subtext),
                                                    'explore_comparisons' => !empty($content->explore_subcategories_headline) || !empty($content->popular_comparisons_headline),
                                                    'metatags'            => !empty($content->meta_title) || !empty($content->meta_description),
                                                    default               => false,
                                                };
                                                if ($isSaved) $completedFields++;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="feature_ids[]" value="{{ $feature->id }}" class="form-check-input feature-check">
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $fName }}</div>
                                            <span class="small text-muted">ID: {{ $feature->id }}</span>

                                        </td>
                                        <td>
                                            <span class="badge badge-dim bg-primary">{{ $catName }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($completedFields === $totalFields)
                                                <span class="badge bg-success">Complete</span>
                                            @elseif($hasPending)
                                                <span class="badge bg-info"><em class="icon ni ni-loader me-1"></em>Generating</span>
                                            @elseif($completedFields > 0)
                                                <span class="badge bg-warning">{{ $completedFields }}/{{ $totalFields }}</span>
                                            @else
                                                <span class="badge bg-light text-muted">Not Started</span>
                                            @endif
                                        </td>

                                        {{-- 6 Content Fields --}}
                                        @foreach($fields as $field)
                                            @php
                                                $qi = $fieldStatusMap[$feature->id][$field] ?? null;
                                                $savedInDb = false;
                                                if ($content) {
                                                    $savedInDb = match($field) {
                                                        'header'              => !empty($content->page_title) || !empty($content->description),
                                                        'top_rated'           => !empty($content->top_rated_listing_headline) || !empty($content->top_rated_listing_subtext),
                                                        'top_10'              => !empty($content->compare_alternatives_headline) || !empty($content->compare_alternatives_subtext),
                                                        'pros_cons'           => !empty($content->community_says_headline) || !empty($content->community_says_subtext),
                                                        'explore_comparisons' => !empty($content->explore_subcategories_headline) || !empty($content->popular_comparisons_headline),
                                                        'metatags'            => !empty($content->meta_title) || !empty($content->meta_description),
                                                        default               => false,
                                                    };
                                                }
                                            @endphp
                                            <td class="text-center">
                                                @if(!$qi && !$savedInDb)
                                                    <span class="text-muted small">--</span>
                                                @elseif($qi && $qi->status === 'completed')
                                                    <div class="d-inline-flex align-items-center">
                                                        <span class="badge badge-dim bg-success" title="{{ Str::limit((string)($qi->result ?? ''), 80) }}">
                                                            <em class="icon ni ni-check"></em>
                                                        </span>
                                                        <a href="{{ route('content-gen.view-result', $qi->id) }}" class="btn btn-icon btn-xs btn-dim btn-outline-primary ms-1" title="View Result">
                                                            <em class="icon ni ni-eye"></em>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-icon btn-xs btn-dim btn-outline-secondary ms-1"
                                                            title="Re-generate"
                                                            onclick="retryField({{ $feature->id }}, '{{ $field }}')"
                                                        >
                                                            <em class="icon ni ni-reload"></em>
                                                        </button>
                                                    </div>
                                                @elseif($qi && in_array($qi->status, ['processing', 'pending']))
                                                    <span class="badge badge-dim bg-info" title="AI Generation in progress">
                                                        <em class="icon ni ni-loader"></em>
                                                    </span>
                                                @elseif($qi && $qi->status === 'failed')
                                                    <div class="d-inline-flex align-items-center">
                                                        <span class="badge badge-dim bg-danger" title="{{ $qi->error_message }}">
                                                            <em class="icon ni ni-cross"></em>
                                                        </span>
                                                        <button type="button"
                                                            class="btn btn-icon btn-xs btn-dim btn-outline-warning ms-1"
                                                            title="Retry"
                                                            onclick="retryField({{ $feature->id }}, '{{ $field }}')"
                                                        >
                                                            <em class="icon ni ni-reload"></em>
                                                        </button>
                                                    </div>
                                                @elseif($savedInDb)
                                                    <div class="d-inline-flex align-items-center">
                                                        <span class="badge badge-dim bg-success" title="Saved in database">
                                                            <em class="icon ni ni-check-circle"></em>
                                                        </span>
                                                        <button type="button"
                                                            class="btn btn-icon btn-xs btn-dim btn-outline-secondary ms-1"
                                                            title="Generate with AI"
                                                            onclick="retryField({{ $feature->id }}, '{{ $field }}')"
                                                        >
                                                            <em class="icon ni ni-spark"></em>
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach

                                        {{-- Actions --}}
                                        <td class="text-center">
                                            <div class="d-inline-flex" style="gap: 10px;">
                                                <button type="button" class="btn btn-xs btn-primary" title="Generate all fields for this feature" onclick="generateAll({{ $feature->id }})">
                                                    <em class="icon ni ni-spark"></em>
                                                </button>

                                                @if($hasCompletedQueue)
                                                    <button type="button" class="btn btn-xs btn-success" title="Apply AI content to database" onclick="applyAllCompleted({{ $feature->id }})">
                                                        <em class="icon ni ni-check"></em>
                                                    </button>
                                                @endif

                                                @if($content)
                                                    <a href="{{ route('admin.category-feature-content.edit', $content->id) }}" class="btn btn-xs btn-secondary" title="Edit Content Manually">
                                                        <em class="icon ni ni-edit"></em>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.category-feature-content.create', ['category_id' => $feature->category_id, 'feature_id' => $feature->id]) }}" class="btn btn-xs btn-secondary" title="Add Content Manually" style="margin-left: 15px;">
                                                        <em class="icon ni ni-plus"></em>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($fields) + 5 }}" class="text-center py-4 text-muted">
                                            No features found matching the selected criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($features->hasPages())
                    <div class="card-inner py-5 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing <strong>{{ $features->firstItem() }}</strong> to <strong>{{ $features->lastItem() }}</strong> of <strong>{{ $features->total() }}</strong> features
                        </div>
                        <div>
                            {{ $features->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </form>

        {{-- Hidden Single Field Retry Form --}}
        <form id="retryForm" action="{{ route('content-gen.features.retry-field') }}" method="POST" style="display:none">
            @csrf
            <input type="hidden" name="feature_id" id="retry_feature_id">
            <input type="hidden" name="field" id="retry_field">
            <input type="hidden" name="provider" id="retry_provider">
        </form>

        {{-- Hidden Apply All Completed Form --}}
        <form id="applyAllForm" action="{{ route('content-gen.features.apply-all') }}" method="POST" style="display:none">
            @csrf
            <input type="hidden" name="feature_id" id="apply_feature_id">
        </form>

    </div>
</div>

<script>
function updateCount() {
    const checked = document.querySelectorAll('.feature-check:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('submitBatchBtn').disabled = (checked === 0);
}

document.querySelectorAll('.feature-check').forEach(c => c.addEventListener('change', updateCount));

function selectAll() {
    document.querySelectorAll('.feature-check').forEach(c => c.checked = true);
    document.getElementById('selectAllCheck').checked = true;
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('.feature-check').forEach(c => c.checked = false);
    document.getElementById('selectAllCheck').checked = false;
    updateCount();
}

document.getElementById('selectAllCheck').addEventListener('change', function() {
    document.querySelectorAll('.feature-check').forEach(c => c.checked = this.checked);
    updateCount();
});

function selectAllFields() {
    document.querySelectorAll('.field-checkbox').forEach(c => c.checked = true);
}

function clearAllFields() {
    document.querySelectorAll('.field-checkbox').forEach(c => c.checked = false);
}

function generateAll(featureId) {
    document.querySelectorAll('.feature-check').forEach(c => c.checked = false);
    const fCheck = document.querySelector('.feature-check[value="' + featureId + '"]');
    if (fCheck) fCheck.checked = true;
    selectAllFields();
    updateCount();
    if (confirm('Generate all fields for this feature?')) {
        document.getElementById('batchForm').submit();
    }
}

function retryField(featureId, field) {
    const provider = document.querySelector('[name="provider"]').value;
    if (confirm('Generate content for field "' + field + '"?')) {
        document.getElementById('retry_feature_id').value = featureId;
        document.getElementById('retry_field').value = field;
        document.getElementById('retry_provider').value = provider;
        document.getElementById('retryForm').submit();
    }
}

function applyAllCompleted(featureId) {
    if (confirm('Apply completed AI content into the category feature record?')) {
        document.getElementById('apply_feature_id').value = featureId;
        document.getElementById('applyAllForm').submit();
    }
}
</script>
@endsection
