@extends('admin_layout.master')

@section('title', 'Batch Jobs')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Batch Jobs</h3>
                    <p class="nk-block-des text-soft">
                        Manage AI content generation batch jobs. Download input JSON for ChatGPT and upload completed JSON results.
                        @if($lastPollAt)
                            &bull; <span class="text-success"><em class="icon ni ni-clock me-1"></em>Last poll: {{ \Carbon\Carbon::parse($lastPollAt)->diffForHumans() }}</span>
                        @endif
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <a href="{{ route('content-gen.download-categories') }}" class="btn btn-outline-primary btn-sm" title="Download all category names with their IDs as JSON">
                        <em class="icon ni ni-folder me-1"></em> Categories & IDs
                    </a>
                    <a href="{{ route('content-gen.download-countries') }}" class="btn btn-outline-primary btn-sm" title="Download all supported country codes and names as JSON">
                        <em class="icon ni ni-globe me-1"></em> Country Codes
                    </a>
                    <a href="{{ route('content-gen.download-pros-cons') }}" class="btn btn-outline-primary btn-sm" title="Download all Pros & Cons with their IDs in each category as JSON">
                        <em class="icon ni ni-check-circle me-1"></em> Pros & Cons & IDs
                    </a>
                    <a href="{{ route('content-gen.download-features') }}" class="btn btn-outline-primary btn-sm" title="Download all Features with their IDs in each category as JSON">
                        <em class="icon ni ni-grid-add me-1"></em> Features & IDs
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openExampleModal()">
                        <em class="icon ni ni-file-code me-1"></em> Result JSON Format
                    </button>
                    <a href="{{ route('content-gen.poll-now') }}" class="btn btn-outline-info btn-sm">
                        <em class="icon ni ni-reload me-1"></em> Poll Status Now
                    </a>
                    <a href="{{ route('content-gen.businesses') }}" class="btn btn-primary btn-sm">
                        <em class="icon ni ni-arrow-left me-1"></em> Back to Businesses
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
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-cross-circle me-1"></em> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Configuration Section for Batch Size --}}
        <div class="card card-bordered mb-3">
            <div class="card-inner py-2">
                <form action="{{ route('content-gen.batch-setting') }}" method="POST" class="row g-2 align-items-center">
                    @csrf
                    <div class="col-auto">
                        <span class="fw-bold small text-dark"><em class="icon ni ni-setting me-1 text-primary"></em>Batch Configuration:</span>
                    </div>
                    <div class="col-auto">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">Businesses per batch</span>
                            <input type="number" name="batch_size" class="form-control text-center" value="{{ $batchSize ?? 5 }}" min="1" max="50" style="width: 70px;">
                            <button type="submit" class="btn btn-primary">Save Setting</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <span class="text-soft small">Each batch job merges N businesses into one downloadable JSON for ChatGPT prompt testing and optimization.</span>
                    </div>
                </form>
            </div>
        </div>

        {{-- Batch Jobs Table --}}
        <div class="card card-bordered card-stretch">
            <div class="card-inner p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Provider</th>
                                <th>Batch ID</th>
                                <th>Fields</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th>Submitted</th>
                                <th>Completed</th>
                                <th class="text-end" style="min-width: 250px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td class="text-muted small">{{ $job->id }}</td>
                                    <td>
                                        @if($job->provider === 'manual')
                                            <span class="badge bg-primary">Manual (ChatGPT)</span>
                                        @elseif($job->provider === 'openai')
                                            <span class="badge bg-success">OpenAI API</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Anthropic API</span>
                                        @endif
                                    </td>
                                    <td><code class="small">{{ $job->batch_id ?? '-' }}</code></td>
                                    <td>
                                        @if(str_starts_with($job->prompt_type ?? '', 'feature:'))
                                            <span class="badge badge-dim bg-primary me-1"><em class="icon ni ni-spark me-1"></em>Feature</span>
                                            @php
                                                $fFields = explode(',', str_replace('feature:', '', $job->prompt_type));
                                            @endphp
                                            @foreach($fFields as $f)
                                                <span class="badge badge-dim bg-secondary mb-1" style="font-size: 11px;">
                                                    {{ \App\Http\Controllers\Admin\ContentGenerationController::$featureFieldLabels[trim($f)] ?? trim($f) }}
                                                </span>
                                            @endforeach
                                        @elseif($job->prompt_type)
                                            <span class="badge badge-dim bg-info me-1"><em class="icon ni ni-building me-1"></em>Business</span>
                                            @foreach(explode(',', $job->prompt_type ?? '') as $f)
                                                <span class="badge badge-dim bg-secondary mb-1" style="font-size: 11px;">
                                                    {{ \App\Models\AiContentQueue::$fieldLabels[trim($f)] ?? trim($f) }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-soft small">All</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($job->status) {
                                                'completed'  => 'bg-success',
                                                'processing' => 'bg-info',
                                                'failed'     => 'bg-danger',
                                                default      => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($job->status) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">{{ $job->completed_items }}</span>
                                        / {{ $job->total_items }}
                                        @if($job->failed_items > 0)
                                            <span class="text-danger ms-1">({{ $job->failed_items }} failed)</span>
                                        @endif
                                    </td>
                                    <td class="text-soft small">{{ $job->submitted_at ? $job->submitted_at->diffForHumans() : '-' }}</td>
                                    <td class="text-soft small">{{ $job->completed_at ? $job->completed_at->diffForHumans() : '-' }}</td>
                                    <td class="text-end text-nowrap">
                                        {{-- Copy JSON for ChatGPT --}}
                                        <button type="button" class="btn btn-xs btn-outline-info me-1" onclick="copyBatchJson({{ $job->id }}, this)" title="Copy batch JSON for ChatGPT to clipboard">
                                            <em class="icon ni ni-copy me-1"></em> Copy JSON
                                        </button>

                                        {{-- Upload Result JSON --}}
                                        @php
                                            $isFeat = str_starts_with($job->prompt_type ?? '', 'feature:');
                                        @endphp
                                        <button type="button" class="btn btn-xs btn-outline-success me-1" onclick="openUploadModal({{ $job->id }}, '{{ $job->batch_id }}', {{ $isFeat ? 'true' : 'false' }})" title="Upload ChatGPT result JSON for this batch">
                                            <em class="icon ni ni-upload me-1"></em> Upload Result
                                        </button>

                                        {{-- View Items --}}
                                        <a href="{{ route('content-gen.batch-job-items', $job->id) }}" class="btn btn-xs btn-outline-secondary" title="View individual queue items">
                                            <em class="icon ni ni-list-check me-1"></em> Items ({{ $job->queue_items_count }})
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-5">No batch jobs yet. Go to <a href="{{ route('content-gen.businesses') }}">Businesses</a> or <a href="{{ route('content-gen.features') }}">Features</a> to create one.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($jobs->hasPages())
                <div class="card-inner">{{ $jobs->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Upload Batch Result Modal --}}
<div class="modal fade" id="uploadResultModal" tabindex="-1" role="dialog" aria-labelledby="uploadResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="uploadResultModalLabel">
                    <em class="icon ni ni-upload-cloud text-success me-2"></em>Upload Batch Result JSON
                </h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="closeUploadModal()">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form id="uploadResultForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Upload the output JSON generated by ChatGPT for Batch <strong id="uploadModalBatchId" class="text-dark"></strong>.
                        You can either upload a <code>.json</code> file or paste the JSON text directly below.
                        Localio will parse the results and automatically apply them across all fields to the corresponding businesses.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Upload JSON File</label>
                        <input type="file" name="result_file" class="form-control form-control-sm" accept=".json,application/json">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold small mb-0">Or Paste JSON Text</label>
                        <button type="button" class="btn btn-xs btn-outline-light text-dark" onclick="insertSampleResult()">
                            <em class="icon ni ni-copy me-1"></em> Insert Sample Result
                        </button>
                    </div>
                    <textarea name="result_text" id="resultTextInput" class="form-control font-monospace text-dark" rows="12" style="font-size: 12px; line-height: 1.4; background: #f8fafc;" placeholder="Paste the output JSON from ChatGPT here..."></textarea>

                    <div class="mt-3 p-2 bg-light rounded border d-flex justify-content-between align-items-center small text-muted">
                        <span><em class="icon ni ni-info me-1 text-primary"></em> Supports either direct array <code>[...]</code> or wrapped <code>{"businesses": [...]}</code>. Markdown backticks (```json) are automatically stripped.</span>
                        <a href="javascript:void(0)" onclick="openExampleModal()" class="text-primary fw-bold text-nowrap ms-2">View Format Example</a>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeUploadModal()">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">
                        <em class="icon ni ni-check me-1"></em> Process & Apply Result
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Example Format Modal --}}
<div class="modal fade" id="exampleFormatModal" tabindex="-1" role="dialog" aria-labelledby="exampleFormatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="exampleFormatModalLabel">
                    <em class="icon ni ni-file-code text-primary me-2"></em>Expected Output JSON Schema & Example
                </h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="closeExampleModal()">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body p-4">
                <ul class="nav nav-tabs nav-tabs-mb-icon mb-3" id="formatExampleTabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0)" onclick="switchExampleTab('business')">
                            <em class="icon ni ni-building me-1"></em> Businesses Output Format
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" onclick="switchExampleTab('feature')">
                            <em class="icon ni ni-spark me-1"></em> Features Output Format
                        </a>
                    </li>
                </ul>

                {{-- Business Format Panel --}}
                <div id="businessFormatPanel">
                    <div class="alert alert-info py-2 px-3 mb-3 small">
                        <div class="fw-bold mb-1"><em class="icon ni ni-info-fill me-1"></em> Availability in Active Countries:</div>
                        <ul class="mb-0 ps-3">
                            <li><strong>Available globally in all countries:</strong> <code>"active_countries": {"active_all_countries": true}</code></li>
                            <li><strong>Restricted to specific countries only:</strong> <code>"active_countries": {"active_all_countries": false, "country_codes": ["US", "GB", "CA", "AU", "DE"]}</code></li>
                        </ul>
                    </div>
                    <div class="alert alert-warning py-2 px-3 mb-3 small">
                        <div class="fw-bold mb-1"><em class="icon ni ni-alert-circle me-1"></em> Pros, Cons & Features Strict Selection (By ID):</div>
                        <ul class="mb-0 ps-3">
                            <li><strong>Pros & Cons:</strong> Select strictly from the available category pro/con IDs: <code>"pros_cons": {"pro_ids": [20, 22, 24], "con_ids": [21, 23]}</code> (or <code>"pros": [20, 22]</code>, <code>"cons": [21]</code>). Arbitrary free-form pros and cons cannot be added.</li>
                            <li><strong>Features:</strong> Assign by existing category feature IDs: <code>"features": [1007, 1008, 1012]</code> (or <code>"feature_ids": [1007, 1008]</code>).</li>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted small mb-0">
                            Ask ChatGPT to output a JSON array of businesses matching this exact structure:
                        </p>
                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="copyExampleJson()">
                            <em class="icon ni ni-copy me-1"></em> Copy Example JSON
                        </button>
                    </div>
                    <pre class="bg-dark text-light p-3 rounded" style="max-height: 440px; overflow-y: auto; font-size: 12px; line-height: 1.5;"><code id="exampleJsonCode">{{ $batchResultExample ?? '' }}</code></pre>
                </div>

                {{-- Feature Format Panel --}}
                <div id="featureFormatPanel" style="display:none;">
                    <div class="alert alert-primary py-2 px-3 mb-3 small">
                        <div class="fw-bold mb-1"><em class="icon ni ni-spark me-1"></em> Category Feature Content Schema:</div>
                        <span class="text-muted">Supports either nested <code>"header": {"page_title": "...", "description": "..."}</code> or flat <code>{"page_title": "...", "description": "..."}</code>.</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted small mb-0">
                            Ask ChatGPT to output a JSON array of features matching this exact structure:
                        </p>
                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="copyExampleJson()">
                            <em class="icon ni ni-copy me-1"></em> Copy Example JSON
                        </button>
                    </div>
                    <pre class="bg-dark text-light p-3 rounded" style="max-height: 440px; overflow-y: auto; font-size: 12px; line-height: 1.5;"><code id="featureExampleJsonCode">{{ $featureBatchResultExample ?? '' }}</code></pre>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeExampleModal()">Close</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="copyExampleJson()">
                    <em class="icon ni ni-copy me-1"></em> Copy JSON
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const sampleResultJson = `{!! addslashes($batchResultExample ?? '') !!}`;
const sampleFeatureResultJson = `{!! addslashes($featureBatchResultExample ?? '') !!}`;
let currentModalIsFeature = false;
let currentExampleTab = 'business';

function openUploadModal(jobId, batchId, isFeature = false) {
    currentModalIsFeature = isFeature;
    const form = document.getElementById('uploadResultForm');
    form.action = `/admin-dashboard/content-generation/batch-jobs/${jobId}/upload-result`;
    const label = (batchId || ('#' + jobId)) + (isFeature ? ' (Features)' : ' (Businesses)');
    document.getElementById('uploadModalBatchId').textContent = label;
    document.getElementById('resultTextInput').value = '';
    if (window.jQuery && typeof $('#uploadResultModal').modal === 'function') {
        $('#uploadResultModal').modal('show');
    } else if (window.bootstrap && bootstrap.Modal) {
        new bootstrap.Modal(document.getElementById('uploadResultModal')).show();
    } else {
        const el = document.getElementById('uploadResultModal');
        el.classList.add('show');
        el.style.display = 'block';
    }
}

function closeUploadModal() {
    if (window.jQuery && typeof $('#uploadResultModal').modal === 'function') {
        $('#uploadResultModal').modal('hide');
    }
    const el = document.getElementById('uploadResultModal');
    if (el) {
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
    }
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

function openExampleModal() {
    if (window.jQuery && typeof $('#exampleFormatModal').modal === 'function') {
        $('#exampleFormatModal').modal('show');
    } else if (window.bootstrap && bootstrap.Modal) {
        new bootstrap.Modal(document.getElementById('exampleFormatModal')).show();
    } else {
        const el = document.getElementById('exampleFormatModal');
        el.classList.add('show');
        el.style.display = 'block';
    }
}

function closeExampleModal() {
    if (window.jQuery && typeof $('#exampleFormatModal').modal === 'function') {
        $('#exampleFormatModal').modal('hide');
    }
    const el = document.getElementById('exampleFormatModal');
    if (el) {
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
    }
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// Global modal dismiss handler for DashLite / Bootstrap 4 & 5 compatibility
if (window.jQuery) {
    $(document).on('click', '[data-bs-dismiss="modal"], [data-dismiss="modal"], .btn-close, .close', function(e) {
        const modal = $(this).closest('.modal');
        if (modal.length) {
            modal.modal('hide');
            modal.removeClass('show').css('display', 'none').attr('aria-hidden', 'true');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
        }
    });
}

function copyBatchJson(jobId, btn) {
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<em class="icon ni ni-loader me-1"></em> Copying...';

    fetch(`/admin-dashboard/content-generation/batch-jobs/${jobId}/json`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP error ' + res.status);
        return res.json();
    })
    .then(data => {
        const text = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        } else {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            return Promise.resolve();
        }
    })
    .then(() => {
        btn.classList.remove('btn-outline-info');
        btn.classList.add('btn-success');
        btn.innerHTML = '<em class="icon ni ni-check me-1"></em> Copied!';
        setTimeout(() => {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-info');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }, 2000);
    })
    .catch(err => {
        alert('Failed to copy JSON: ' + err.message);
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}

function insertSampleResult() {
    document.getElementById('resultTextInput').value = currentModalIsFeature ? sampleFeatureResultJson : sampleResultJson;
}

function switchExampleTab(tab) {
    currentExampleTab = tab;
    const links = document.querySelectorAll('#formatExampleTabs .nav-link');
    if (tab === 'feature') {
        document.getElementById('businessFormatPanel').style.display = 'none';
        document.getElementById('featureFormatPanel').style.display = 'block';
        if (links[0]) links[0].classList.remove('active');
        if (links[1]) links[1].classList.add('active');
    } else {
        document.getElementById('businessFormatPanel').style.display = 'block';
        document.getElementById('featureFormatPanel').style.display = 'none';
        if (links[0]) links[0].classList.add('active');
        if (links[1]) links[1].classList.remove('active');
    }
}

function copyExampleJson() {
    const textToCopy = currentExampleTab === 'feature' ? sampleFeatureResultJson : sampleResultJson;
    navigator.clipboard.writeText(textToCopy).then(() => {
        alert('Example JSON copied to clipboard!');
    }).catch(err => {
        const textarea = document.createElement('textarea');
        textarea.value = textToCopy;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Example JSON copied to clipboard!');
    });
}
</script>
@endsection
