@extends('admin_layout.master')

@section('title', 'Review Moderation Batch Jobs')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">
                        <em class="icon ni ni-layers text-primary me-2"></em> Review Moderation Batches
                    </h3>
                    <p class="nk-block-des text-soft">
                        Manage Layer 3 GPT-5.6-Luna Batch API jobs for Localio review integrity and locale verification.
                        @if($lastPollAt)
                            &bull; <span class="text-success"><em class="icon ni ni-clock me-1"></em>Last poll: {{ \Carbon\Carbon::parse($lastPollAt)->diffForHumans() }}</span>
                        @endif
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="openUploadModal()" title="Upload or paste completed batch results JSON">
                        <em class="icon ni ni-upload-cloud me-1"></em> Upload Batch Result
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="openExampleModal()">
                        <em class="icon ni ni-file-code me-1"></em> Expected JSON Schema
                    </button>
                    <a href="{{ route('admin.reviews.batches.poll-now') }}" class="btn btn-info btn-sm">
                        <em class="icon ni ni-reload me-1"></em> Poll Status Now
                    </a>
                    <form action="{{ route('admin.reviews.batches.dispatch-now') }}" method="POST" class="d-inline" onsubmit="return confirm('Dispatch Layer 3 GPT-5.6-Luna batch for all awaiting reviews?');">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm" {{ $waitingForBatchCount === 0 ? 'disabled' : '' }}>
                            <em class="icon ni ni-send me-1"></em> Dispatch Batch Now ({{ $waitingForBatchCount }})
                        </button>
                    </form>
                    <a href="{{ route('admin.reviews.settings') }}" class="btn btn-outline-secondary btn-sm">
                        <em class="icon ni ni-setting me-1"></em> Settings
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
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-info me-1"></em> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-bordered bg-lighter">
                    <div class="card-inner py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fs-12px text-soft text-uppercase fw-bold">Awaiting Layer 3 Batch</span>
                                <div class="fs-22px fw-bold {{ $waitingForBatchCount > 0 ? 'text-primary' : 'text-dark' }}">
                                    {{ $waitingForBatchCount }}
                                </div>
                            </div>
                            <div class="nk-iv-wg2-sub text-end">
                                <em class="icon ni ni-clock fs-32px text-primary opacity-50"></em>
                            </div>
                        </div>
                        <div class="fs-11px text-soft mt-1">
                            Passed Layer 1+2 &bull; Status: PROCESSING
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-bordered bg-info-dim">
                    <div class="card-inner py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fs-12px text-info text-uppercase fw-bold">Processing Batches</span>
                                <div class="fs-22px fw-bold text-info">{{ $processingBatchesCount }}</div>
                            </div>
                            <div class="nk-iv-wg2-sub text-end">
                                <em class="icon ni ni-loader fs-32px text-info opacity-75"></em>
                            </div>
                        </div>
                        <div class="fs-11px text-soft mt-1">
                            Currently running at OpenAI
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-bordered bg-success-dim">
                    <div class="card-inner py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fs-12px text-success text-uppercase fw-bold">Completed Batches</span>
                                <div class="fs-22px fw-bold text-success">{{ $completedBatchesCount }}</div>
                            </div>
                            <div class="nk-iv-wg2-sub text-end">
                                <em class="icon ni ni-check-circle fs-32px text-success opacity-75"></em>
                            </div>
                        </div>
                        <div class="fs-11px text-soft mt-1">
                            Fully evaluated & applied
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-bordered {{ $failedBatchesCount > 0 ? 'bg-danger-dim' : 'bg-lighter' }}">
                    <div class="card-inner py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fs-12px {{ $failedBatchesCount > 0 ? 'text-danger' : 'text-soft' }} text-uppercase fw-bold">Failed Batches</span>
                                <div class="fs-22px fw-bold {{ $failedBatchesCount > 0 ? 'text-danger' : 'text-dark' }}">{{ $failedBatchesCount }}</div>
                            </div>
                            <div class="nk-iv-wg2-sub text-end">
                                <em class="icon ni ni-alert-circle fs-32px {{ $failedBatchesCount > 0 ? 'text-danger' : 'text-soft' }} opacity-50"></em>
                            </div>
                        </div>
                        <div class="fs-11px text-soft mt-1">
                            Escrowed reviews to human review
                        </div>
                    </div>
                </div>
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
                                <th>Batch ID</th>
                                <th>Model / Target</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th>Submitted</th>
                                <th>Completed</th>
                                <th class="text-end" style="min-width: 220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td><strong>#{{ $job->id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <code class="fs-12px">{{ $job->batch_id }}</code>
                                            @if(str_starts_with($job->batch_id, 'batch_local_'))
                                                <span class="badge bg-secondary-dim text-secondary fs-10px">Offline / Local</span>
                                            @elseif(str_starts_with($job->batch_id, 'batch_manual_'))
                                                <span class="badge bg-info-dim text-info fs-10px">Manual Import</span>
                                            @else
                                                <span class="badge bg-primary-dim text-primary fs-10px">OpenAI Batch</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <em class="icon ni ni-cpu me-1 text-primary"></em>{{ config('openai.review_ai_model', 'gpt-5.6-luna') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($job->status === 'completed')
                                            <span class="badge bg-success-dim text-success"><em class="icon ni ni-check-circle me-1"></em>Completed</span>
                                        @elseif($job->status === 'processing')
                                            <span class="badge bg-warning-dim text-warning"><em class="icon ni ni-loader me-1"></em>Processing</span>
                                        @elseif($job->status === 'pending')
                                            <span class="badge bg-info-dim text-info"><em class="icon ni ni-clock me-1"></em>Ready to Run</span>
                                        @elseif($job->status === 'failed')
                                            <span class="badge bg-danger-dim text-danger" title="{{ $job->error_message }}"><em class="icon ni ni-cross-circle me-1"></em>Failed</span>
                                        @else
                                            <span class="badge bg-light text-dark border">{{ ucfirst($job->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $job->total_items ?? 0 }}</span>
                                        @if($job->status === 'completed')
                                            <span class="text-success small ms-1">({{ $job->completed_items ?? 0 }} ok)</span>
                                            @if($job->failed_items > 0)
                                                <span class="text-danger small ms-1">({{ $job->failed_items }} fail)</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fs-12px">{{ $job->submitted_at ? $job->submitted_at->format('M d, H:i') : '—' }}</span>
                                        @if($job->submitted_at)
                                            <div class="fs-11px text-soft">{{ $job->submitted_at->diffForHumans() }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fs-12px">{{ $job->completed_at ? $job->completed_at->format('M d, H:i') : '—' }}</span>
                                        @if($job->completed_at)
                                            <div class="fs-11px text-soft">{{ $job->completed_at->diffForHumans() }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.reviews.batches.items', $job) }}" class="btn btn-outline-light btn-sm text-dark" title="View reviews in this batch">
                                                <em class="icon ni ni-eye me-1"></em> Items
                                            </a>
                                            <a href="{{ route('admin.reviews.batches.download', $job) }}" class="btn btn-outline-primary btn-sm" title="Download Input JSON / JSONL">
                                                <em class="icon ni ni-download me-1"></em> JSON
                                            </a>
                                            @if($job->status !== 'completed')
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="openUploadModal('{{ $job->id }}', '{{ $job->batch_id }}')" title="Upload result JSON">
                                                    <em class="icon ni ni-upload"></em>
                                                </button>
                                            @endif
                                            <form action="{{ route('admin.reviews.batches.delete', $job) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this batch record? Unprocessed reviews will be returned to queue.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Batch">
                                                    <em class="icon ni ni-trash"></em>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-soft">
                                        <em class="icon ni ni-layers fs-36px mb-2 text-muted"></em>
                                        <p class="mb-0">No review moderation batch jobs created yet.</p>
                                        <small class="text-muted">Reviews that pass Layer 1 and Layer 2 are automatically queued here for Layer 3 GPT-5.6-Luna integrity moderation.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($jobs->hasPages())
                <div class="card-footer border-top py-2">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Upload Result Modal --}}
<div class="modal fade" id="uploadResultModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="uploadResultForm" action="{{ route('admin.reviews.batches.upload-global-result') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <em class="icon ni ni-upload-cloud text-success me-1"></em> Upload Review Moderation Batch Results
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-soft fs-13px mb-3">
                        Upload the completed output JSONL or JSON response from OpenAI Batch API or manual ChatGPT execution. The system parses each review evaluation, applies Layer 3 rules, verifies locales, and publishes passing reviews automatically.
                    </p>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Select JSON / JSONL File(s)</label>
                            <span class="badge bg-outline-info fs-11px">Multi-file supported (up to 50+ files)</span>
                        </div>
                        <input type="file" name="result_files[]" id="reviewResultFilesInput" class="form-control" accept=".json,.jsonl,.txt" multiple>
                        <span class="form-note">Accepts standard OpenAI batch output files (<code class="fs-11px">.jsonl</code>) or structured JSON arrays.</span>
                        
                        <div id="reviewFileSelectionInfo" class="mt-2 p-2 bg-light rounded border d-none">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-dark">
                                    <em class="icon ni ni-check-circle-fill text-success me-1"></em> <span id="reviewFileCountBadge">0</span> file(s) selected
                                </span>
                                <div>
                                    <span class="badge bg-white border text-muted me-1" id="reviewFileSizeBadge">0 KB</span>
                                    <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" id="btnReviewClearFiles">Clear</button>
                                </div>
                            </div>
                            <div id="reviewFileNamesList" class="custom-scrollbar text-muted" style="max-height: 85px; overflow-y: auto; font-size: 11px; line-height: 1.4;">
                            </div>
                        </div>
                    </div>

                    <div id="reviewUploadProgressContainer" class="mb-3 p-3 bg-lighter rounded border d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-primary" id="reviewUploadProgressStatus">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading & processing batch files...
                            </span>
                            <span class="badge bg-primary" id="reviewUploadProgressPercent">0%</span>
                        </div>
                        <div class="progress mb-2" style="height: 14px; border-radius: 6px;">
                            <div id="reviewUploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                        </div>
                        <div class="small text-muted d-flex justify-content-between" id="reviewUploadProgressDetail">
                            <span id="reviewUploadProgressText">Processing...</span>
                            <span id="reviewUploadProgressStats"></span>
                        </div>
                    </div>

                    <div class="text-center my-2 text-soft fw-bold fs-12px">— OR PASTE JSON TEXT DIRECTLY —</div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Result JSON Text</label>
                            <button type="button" class="btn btn-link btn-xs text-primary" onclick="insertSampleResult()">
                                <em class="icon ni ni-file-code me-1"></em> Insert Sample Format
                            </button>
                        </div>
                        <textarea name="result_text" id="resultTextInput" class="form-control font-monospace fs-12px" rows="9" placeholder='Paste JSON Lines or array here...'></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSubmitReviewUpload" class="btn btn-success">
                        <em class="icon ni ni-check-circle me-1"></em> Process & Apply Results
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Expected JSON Schema Modal --}}
<div class="modal fade" id="exampleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <em class="icon ni ni-file-code text-primary me-1"></em> GPT-5.6-Luna Expected Output Schema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-soft fs-13px mb-3">
                    Each item in the batch results must evaluate review integrity and return complete Localio locale information:
                </p>
                <div class="position-relative">
                    <pre class="bg-dark text-light p-3 rounded fs-12px" style="max-height: 420px; overflow-y: auto;"><code>{
  "custom_id": "review-123",
  "decision": "PASS",               // PASS | HUMAN_REVIEW
  "risk_level": "LOW",              // LOW | MEDIUM | HIGH | CRITICAL
  "flags": [],                      // SUSPECTED_FAKE, SPAM, PROMOTIONAL, RATING_MISMATCH,
                                    // CONFLICT_OF_INTEREST, WRONG_BUSINESS, DUPLICATE_LANGUAGE,
                                    // SYNTHETIC_CONTENT, LEGAL_ALLEGATION, SUSPICIOUS_ACTIVITY, OTHER
  "submitted_locale": "en-US",      // Locale active when user submitted review
  "detected_locale": "en-US",       // Exact supported Localio locale detected by AI
  "locale_status": "MATCH",         // MATCH | REASSIGNED | HUMAN_REVIEW
  "confidence": 96,                 // 0 - 100
  "reason": "Authentic organic customer experience with detailed product feedback."
}</code></pre>
                </div>
                <div class="alert alert-info py-2 px-3 mt-3 fs-12px mb-0">
                    <em class="icon ni ni-info me-1"></em>
                    <strong>Locale Logic:</strong> If <code>locale_status = REASSIGNED</code>, the system saves <code>source_locale = detected_locale</code> and publishes without human moderation. If <code>locale_status = HUMAN_REVIEW</code>, the review is escrowed to human moderators regardless of the integrity score.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
const sampleResultJson = `[
  {
    "custom_id": "review-1",
    "decision": "PASS",
    "risk_level": "LOW",
    "flags": [],
    "submitted_locale": "en-US",
    "detected_locale": "en-US",
    "locale_status": "MATCH",
    "confidence": 98,
    "reason": "Genuine customer review matching business context."
  }
]`;

function openUploadModal(jobId = null, batchId = null) {
    const form = document.getElementById('uploadResultForm');
    if (jobId) {
        form.action = `/admin-dashboard/reviews/batches/${jobId}/upload-result`;
    } else {
        form.action = `{{ route('admin.reviews.batches.upload-global-result') }}`;
    }
    const modal = new bootstrap.Modal(document.getElementById('uploadResultModal'));
    modal.show();
}

function openExampleModal() {
    const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
    modal.show();
}

function insertSampleResult() {
    document.getElementById('resultTextInput').value = sampleResultJson;
}
</script>
@endsection
