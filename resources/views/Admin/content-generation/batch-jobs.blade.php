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
                    {{-- Bulk Download Dropdown --}}
                    <div class="dropdown">
                        <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Download batch jobs as JSON files">
                            <em class="icon ni ni-download-cloud me-1"></em> Download Batch Jobs
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow">
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('content-gen.batch-jobs.download-bulk', ['status' => 'pending']) }}" title="Download all pending batch jobs inside a single ZIP archive">
                                <span><em class="icon ni ni-file-zip text-warning me-2 fs-5"></em> Download All Pending (ZIP)</span>
                                @if(isset($pendingCount) && $pendingCount > 0)
                                    <span class="badge bg-warning ms-3">{{ $pendingCount }}</span>
                                @endif
                            </a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('content-gen.batch-jobs.download-bulk', ['status' => 'all']) }}" title="Download all batch jobs inside a single ZIP archive">
                                <span><em class="icon ni ni-archive text-primary me-2 fs-5"></em> Download All Jobs (ZIP)</span>
                                @if(isset($totalCount) && $totalCount > 0)
                                    <span class="badge bg-secondary ms-3">{{ $totalCount }}</span>
                                @endif
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:void(0)" onclick="startDirectBulkDownload('pending')" title="Download individual .json files directly to your Downloads folder">
                                <span><em class="icon ni ni-files text-info me-2 fs-5"></em> Bulk Download Pending (.json files)</span>
                                @if(isset($pendingCount) && $pendingCount > 0)
                                    <span class="badge bg-info ms-3">{{ $pendingCount }}</span>
                                @endif
                            </a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:void(0)" onclick="startDirectBulkDownload('all')" title="Download all .json files directly to your Downloads folder">
                                <span><em class="icon ni ni-files text-secondary me-2 fs-5"></em> Bulk Download All (.json files)</span>
                            </a>
                        </div>
                    </div>

                    <button type="button" class="btn btn-success btn-sm" onclick="openGlobalUploadModal()" title="Paste or upload ChatGPT result JSON — auto-detects batch ID">
                        <em class="icon ni ni-upload-cloud me-1"></em> Upload Batch Result
                    </button>
                    <a href="{{ route('content-gen.download-categories') }}" class="btn btn-primary btn-sm" title="Download all category names with their IDs as JSON">
                        <em class="icon ni ni-folder me-1"></em> Categories & IDs
                    </a>
                    <a href="{{ route('content-gen.download-countries') }}" class="btn btn-primary btn-sm" title="Download all supported country IDs and names as JSON">
                        <em class="icon ni ni-globe me-1"></em> Country IDs
                    </a>
                    <a href="{{ route('content-gen.download-pros-cons') }}" class="btn btn-primary btn-sm" title="Download all Pros & Cons with their IDs in each category as JSON">
                        <em class="icon ni ni-check-circle me-1"></em> Pros & Cons & IDs
                    </a>
                    <a href="{{ route('content-gen.download-features') }}" class="btn btn-primary btn-sm" title="Download all Features with their IDs in each category as JSON">
                        <em class="icon ni ni-grid-add me-1"></em> Features & IDs
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="openExampleModal()">
                        <em class="icon ni ni-file-code me-1"></em> Result JSON Format
                    </button>
                    <a href="{{ route('content-gen.poll-now') }}" class="btn btn-info btn-sm">
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
                            <input type="number" name="batch_size" class="form-control text-center" value="{{ $batchSize ?? 1 }}" min="1" max="50" style="width: 70px;">
                            <button type="submit" class="btn btn-primary">Save Setting</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <span class="text-soft small">Each batch job merges N businesses into one downloadable JSON for ChatGPT prompt testing and optimization.</span>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Selection Action Bar --}}
        <div id="bulkSelectionBar" class="alert alert-primary py-2 px-3 mb-3 d-none align-items-center justify-content-between shadow-sm">
            <div class="d-flex align-items-center">
                <em class="icon ni ni-check-circle-fill text-primary fs-5 me-2"></em>
                <span class="fw-bold text-dark"><span id="selectedCount">0</span> batch job(s) selected</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-xs btn-primary" onclick="downloadSelected('zip')" title="Download selected jobs as a ZIP archive">
                    <em class="icon ni ni-file-zip me-1"></em> Download Selected (ZIP)
                </button>
                <button type="button" class="btn btn-xs btn-info" onclick="downloadSelected('direct')" title="Download selected jobs as individual .json files directly">
                    <em class="icon ni ni-files me-1"></em> Download Selected (.json)
                </button>
                <button type="button" class="btn btn-xs btn-danger" onclick="confirmDeleteSelected()" title="Delete selected jobs and queue items">
                    <em class="icon ni ni-trash me-1"></em> Delete Selected
                </button>
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="clearJobSelection()">
                    Clear Selection
                </button>
            </div>
        </div>

        {{-- Batch Jobs Table --}}
        <div class="card card-bordered card-stretch">
            <div class="card-inner p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="35" class="text-center">
                                    <input type="checkbox" id="selectAllJobsCheck" class="form-check-input" title="Select all on this page" onchange="toggleSelectAllJobs(this)">
                                </th>
                                <th>#</th>
                                <th>Provider</th>
                                <th>Batch ID</th>
                                <th>Fields</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th>Submitted</th>
                                <th>Completed</th>
                                <th class="text-end" style="min-width: 280px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input job-check" value="{{ $job->id }}" onchange="onJobSelectionChange()">
                                    </td>
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
                                        {{-- Download JSON file [ID].json --}}
                                        @php
                                            $cleanId = $job->batch_id ? preg_replace('/^BATCH-(?:FEAT-)?MANUAL-/i', '', $job->batch_id) : $job->id;
                                        @endphp
                                        <a href="{{ route('content-gen.batch-jobs.download', $job->id) }}" class="btn btn-xs btn-outline-primary me-1" title="Download {{ $cleanId }}.json">
                                            <em class="icon ni ni-download me-1"></em> Download
                                        </a>

                                        {{-- Copy JSON for ChatGPT --}}
                                        <button type="button" class="btn btn-xs btn-info me-1" onclick="copyBatchJson({{ $job->id }}, this)" title="Copy batch JSON for ChatGPT to clipboard">
                                            <em class="icon ni ni-copy me-1"></em> Copy JSON
                                        </button>

                                        {{-- View Items --}}
                                        <a href="{{ route('content-gen.batch-job-items', $job->id) }}" class="btn btn-xs btn-secondary me-1" title="View individual queue items">
                                            <em class="icon ni ni-list-check me-1"></em> Items ({{ $job->queue_items_count }})
                                        </a>

                                        {{-- Delete Batch Job --}}
                                        <button type="button" class="btn btn-xs btn-danger" onclick="confirmDeleteBatch({{ $job->id }}, '{{ $job->batch_id }}')" title="Delete this batch job">
                                            <em class="icon ni ni-trash me-1"></em> Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted py-5">No batch jobs yet. Go to <a href="{{ route('content-gen.businesses') }}">Businesses</a> to create one.</td></tr>
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

{{-- Hidden Delete Batch Form --}}
<form id="deleteBatchForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

{{-- Hidden Bulk Delete Batch Form --}}
<form id="bulkDeleteForm" action="{{ route('content-gen.batch-jobs.bulk-delete') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds" value="">
</form>

{{-- Direct Multi-File Download Progress Modal --}}
<div class="modal fade" id="directDownloadModal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title">
                    <em class="icon ni ni-download-cloud text-primary me-2"></em>Downloading Batch Job Files
                </h5>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small text-dark" id="directDownloadStatus">
                        <span class="spinner-border spinner-border-sm me-1 text-primary"></span> Preparing files...
                    </span>
                    <span class="badge bg-primary" id="directDownloadPercent">0%</span>
                </div>
                <div class="progress mb-3" style="height: 14px; border-radius: 6px;">
                    <div id="directDownloadBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                </div>
                <div class="small text-muted d-flex justify-content-between">
                    <span id="directDownloadCurrentFile">Initializing...</span>
                    <span id="directDownloadStats" class="fw-semibold">0 / 0</span>
                </div>
                <div class="alert alert-light border mt-3 py-2 px-3 small text-muted">
                    <em class="icon ni ni-info me-1 text-info"></em>
                    Files are saved directly to your browser's default Downloads folder as <code>[ID].json</code>.
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-sm btn-secondary" id="btnCancelDirectDownload" onclick="cancelDirectDownload()">Cancel</button>
                <button type="button" class="btn btn-sm btn-success d-none" id="btnCloseDirectDownload" onclick="closeDirectDownloadModal()">Done</button>
            </div>
        </div>
    </div>
</div>

{{-- Upload Batch Result Modal --}}
<div class="modal fade" id="uploadResultModal" tabindex="-1" role="dialog" aria-labelledby="uploadResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="uploadResultModalLabel">
                    <em class="icon ni ni-upload-cloud text-success me-2"></em>Upload / Paste Batch Result JSON
                </h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="closeUploadModal()">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form id="uploadResultForm" action="{{ route('content-gen.batch-jobs.upload-global-result') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-center">
                        <em class="icon ni ni-info-fill fs-5 me-2 text-info"></em>
                        <div>
                            <strong>Automatic Batch Detection:</strong> Paste your completed JSON result from ChatGPT below. Localio automatically detects the <code>batch_id</code> (or matches the businesses) and applies the generated content directly.
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold small mb-0">
                                <em class="icon ni ni-files me-1 text-primary"></em> Upload JSON File(s)
                            </label>
                            <span class="badge bg-outline-info" style="font-size: 11px;">
                                Multi-file supported (up to 50+ files at once)
                            </span>
                        </div>
                        <input type="file" name="result_files[]" id="resultFilesInput" class="form-control form-control-sm" accept=".json,application/json" multiple>
                        
                        <div id="fileSelectionInfo" class="mt-2 p-2 bg-light rounded border d-none">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-dark">
                                    <em class="icon ni ni-check-circle-fill text-success me-1"></em> <span id="fileCountBadge">0</span> file(s) selected
                                </span>
                                <div>
                                    <span class="badge bg-white border text-muted me-1" id="fileSizeBadge">0 KB</span>
                                    <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" id="btnClearFiles">Clear</button>
                                </div>
                            </div>
                            <div id="fileNamesList" class="custom-scrollbar text-muted" style="max-height: 85px; overflow-y: auto; font-size: 11px; line-height: 1.4;">
                            </div>
                        </div>
                    </div>

                    <div id="uploadProgressContainer" class="mb-3 p-3 bg-lighter rounded border d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-primary" id="uploadProgressStatus">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading & processing batch files...
                            </span>
                            <span class="badge bg-primary" id="uploadProgressPercent">0%</span>
                        </div>
                        <div class="progress mb-2" style="height: 14px; border-radius: 6px;">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                        </div>
                        <div class="small text-muted d-flex justify-content-between" id="uploadProgressDetail">
                            <span id="uploadProgressText">Processing...</span>
                            <span id="uploadProgressStats"></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold small mb-0">Or Paste JSON Text</label>
                        <button type="button" class="btn btn-xs btn-light text-dark" onclick="insertSampleResult()">
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
                    <button type="submit" id="btnSubmitUpload" class="btn btn-sm btn-success">
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
                    <div class="alert alert-primary py-2 px-3 mb-3 small">
                        <div class="fw-bold mb-1"><em class="icon ni ni-layers me-1"></em> Categories & Subcategories Format:</div>
                        <div>Specify the primary category or subcategory, plus any secondary category and subcategory IDs:</div>
                        <ul class="mb-0 ps-3 mt-1">
                            <li><strong>Case 1: Primary category is a Category:</strong><br>
                                <code>"categories": { "primary_category_id": 11, "primary_subcategory_id": null, "category_ids": [11, 30], "subcategory_ids": [19, 20, 23, 32, 33, 50] }</code>
                            </li>
                            <li class="mt-2"><strong>Case 2: Primary category is a Subcategory:</strong><br>
                                <code>"categories": { "primary_category_id": null, "primary_subcategory_id": 23, "category_ids": [], "subcategory_ids": [23, 20] }</code>
                            </li>
                        </ul>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-3 small">
                        <div class="fw-bold mb-1"><em class="icon ni ni-info-fill me-1"></em> Availability in Active Countries:</div>
                        <ul class="mb-0 ps-3">
                            <li><strong>Available globally in all countries:</strong> <code>"active_countries": {"active_all_countries": true}</code></li>
                            <li><strong>Restricted to specific countries only (by Country ID):</strong> <code>"active_countries": {"active_all_countries": false, "country_ids": [1, 2, 3, 4, 5]}</code> <span class="text-muted">(Use "Country IDs" button above to view all country IDs)</span></li>
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
                        <button type="button" class="btn btn-xs btn-primary" onclick="copyExampleJson()">
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
                        <button type="button" class="btn btn-xs btn-primary" onclick="copyExampleJson()">
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

function resetUploadModalState(label = 'Auto-detect from files') {
    const labelEl = document.getElementById('uploadModalBatchId');
    if (labelEl) labelEl.textContent = label;
    const textInput = document.getElementById('resultTextInput');
    if (textInput) textInput.value = '';
    const fileInput = document.getElementById('resultFilesInput');
    if (fileInput) fileInput.value = '';
    const fileInfo = document.getElementById('fileSelectionInfo');
    if (fileInfo) fileInfo.classList.add('d-none');
    const fileNamesList = document.getElementById('fileNamesList');
    if (fileNamesList) fileNamesList.innerHTML = '';
    const progressContainer = document.getElementById('uploadProgressContainer');
    if (progressContainer) progressContainer.classList.add('d-none');
    const progressBar = document.getElementById('uploadProgressBar');
    if (progressBar) progressBar.style.width = '0%';
    const progressPercent = document.getElementById('uploadProgressPercent');
    if (progressPercent) progressPercent.textContent = '0%';
    const progressStats = document.getElementById('uploadProgressStats');
    if (progressStats) progressStats.textContent = '';
    const btnSubmit = document.getElementById('btnSubmitUpload');
    if (btnSubmit) {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<em class="icon ni ni-check me-1"></em> Process & Apply Result';
    }
}

function openGlobalUploadModal() {
    currentModalIsFeature = false;
    resetUploadModalState('Auto-detect from files');
    const form = document.getElementById('uploadResultForm');
    form.action = "{{ route('content-gen.batch-jobs.upload-global-result') }}";
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

function confirmDeleteBatch(jobId, batchId) {
    const label = batchId ? `batch "${batchId}"` : `batch #${jobId}`;
    if (confirm(`Are you sure you want to delete ${label}? All queue items for this batch job will be permanently removed.`)) {
        const form = document.getElementById('deleteBatchForm');
        form.action = `/admin-dashboard/content-generation/batch-jobs/${jobId}`;
        form.submit();
    }
}
let currentExampleTab = 'business';

function openUploadModal(jobId, batchId, isFeature = false) {
    currentModalIsFeature = isFeature;
    const label = (batchId || ('#' + jobId)) + (isFeature ? ' (Features)' : ' (Businesses)');
    resetUploadModalState(label);
    const form = document.getElementById('uploadResultForm');
    form.action = `/admin-dashboard/content-generation/batch-jobs/${jobId}/upload-result`;
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

// Multi-file upload handlers and batch processor
document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById("resultFilesInput");
    const fileInfo = document.getElementById("fileSelectionInfo");
    const fileCountBadge = document.getElementById("fileCountBadge");
    const fileSizeBadge = document.getElementById("fileSizeBadge");
    const fileNamesList = document.getElementById("fileNamesList");
    const btnClearFiles = document.getElementById("btnClearFiles");
    const form = document.getElementById("uploadResultForm");
    const textInput = document.getElementById("resultTextInput");

    if (fileInput) {
        fileInput.addEventListener("change", function() {
            const files = Array.from(this.files);
            if (files.length > 0) {
                fileCountBadge.textContent = files.length;
                let totalBytes = files.reduce((acc, f) => acc + f.size, 0);
                fileSizeBadge.textContent = totalBytes > 1048576 
                    ? (totalBytes / 1048576).toFixed(2) + " MB" 
                    : (totalBytes / 1024).toFixed(1) + " KB";

                fileNamesList.innerHTML = files.map((f, i) => `<div>${i + 1}. <span class="text-dark fw-semibold">${f.name}</span> (${(f.size / 1024).toFixed(1)} KB)</div>`).join("");
                fileInfo.classList.remove("d-none");
            } else {
                fileInfo.classList.add("d-none");
                fileNamesList.innerHTML = "";
            }
        });
    }

    if (btnClearFiles && fileInput) {
        btnClearFiles.addEventListener("click", function() {
            fileInput.value = "";
            fileInfo.classList.add("d-none");
            fileNamesList.innerHTML = "";
        });
    }

    if (form) {
        form.addEventListener("submit", async function(e) {
            const files = fileInput && fileInput.files ? Array.from(fileInput.files) : [];
            const textVal = textInput ? textInput.value.trim() : "";

            if (files.length === 0 && !textVal) {
                e.preventDefault();
                alert("Please select at least one JSON file or paste the JSON text.");
                return;
            }

            // If files are selected, handle via chunked progress uploader (5 files per batch)
            if (files.length > 0) {
                e.preventDefault();

                const progressContainer = document.getElementById("uploadProgressContainer");
                const progressBar = document.getElementById("uploadProgressBar");
                const progressPercent = document.getElementById("uploadProgressPercent");
                const progressStatus = document.getElementById("uploadProgressStatus");
                const progressText = document.getElementById("uploadProgressText");
                const progressStats = document.getElementById("uploadProgressStats");
                const btnSubmit = document.getElementById("btnSubmitUpload");

                progressContainer.classList.remove("d-none");
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Processing...`;

                const chunkSize = 5;
                const totalFiles = files.length;
                let processedFiles = 0;
                let totalItems = 0;
                let totalFields = 0;
                let allErrors = [];
                const token = form.querySelector("input[name='_token']").value;
                const actionUrl = form.action;

                for (let i = 0; i < totalFiles; i += chunkSize) {
                    const chunk = files.slice(i, i + chunkSize);
                    const formData = new FormData();
                    formData.append("_token", token);
                    chunk.forEach(f => formData.append("result_files[]", f));

                    progressStatus.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading & processing files ${i + 1} to ${Math.min(i + chunkSize, totalFiles)} of ${totalFiles}...`;
                    progressText.textContent = `Batch ${Math.floor(i / chunkSize) + 1} of ${Math.ceil(totalFiles / chunkSize)}`;

                    try {
                        const response = await fetch(actionUrl, {
                            method: "POST",
                            headers: {
                                "Accept": "application/json",
                                "X-Requested-With": "XMLHttpRequest"
                            },
                            body: formData
                        });

                        const result = await response.json();
                        if (result.success || response.ok) {
                            processedFiles += (result.processed_files || chunk.length);
                            totalItems += (result.total_applied_items || 0);
                            totalFields += (result.total_applied_fields || 0);
                            if (result.errors && result.errors.length) {
                                allErrors.push(...result.errors);
                            }
                        } else {
                            allErrors.push(`Chunk ${Math.floor(i / chunkSize) + 1} error: ` + (result.message || "Failed to process files."));
                        }
                    } catch (err) {
                        allErrors.push(`Network error on files ${i + 1}-${Math.min(i + chunkSize, totalFiles)}: ` + err.message);
                    }

                    const pct = Math.min(100, Math.round((Math.min(i + chunkSize, totalFiles) / totalFiles) * 100));
                    progressBar.style.width = pct + "%";
                    progressPercent.textContent = pct + "%";
                    progressStats.textContent = `${Math.min(i + chunkSize, totalFiles)} / ${totalFiles} files`;
                }

                progressBar.style.width = "100%";
                progressPercent.textContent = "100%";

                if (allErrors.length === 0) {
                    progressStatus.innerHTML = `<em class="icon ni ni-check-circle-fill text-success me-1"></em> All ${totalFiles} batch files processed successfully!`;
                    progressText.textContent = `Applied to ${totalItems} records across ${totalFields} fields. Reloading...`;
                    setTimeout(() => window.location.reload(), 1500);
                } else if (processedFiles > 0) {
                    progressStatus.innerHTML = `<em class="icon ni ni-alert-fill text-warning me-1"></em> ${processedFiles} of ${totalFiles} files processed with warnings.`;
                    progressText.textContent = allErrors.slice(0, 3).join("; ");
                    setTimeout(() => window.location.reload(), 3000);
                } else {
                    progressStatus.innerHTML = `<em class="icon ni ni-cross-circle-fill text-danger me-1"></em> Processing failed!`;
                    progressText.textContent = allErrors.join("; ");
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = `<em class="icon ni ni-check me-1"></em> Retry Upload`;
                }
            }
        });
    }
});

function closeUploadModal() {
    resetUploadModalState();
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
        btn.classList.remove('btn-info');
        btn.classList.add('btn-success');
        btn.innerHTML = '<em class="icon ni ni-check me-1"></em> Copied!';
        setTimeout(() => {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-info');
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

// ==========================================
// Batch Jobs Selection & Bulk Download Logic
// ==========================================
let isDirectDownloadCancelled = false;

function triggerBlobDownload(jsonObj, filename) {
    const text = typeof jsonObj === 'string' ? jsonObj : JSON.stringify(jsonObj, null, 2);
    const blob = new Blob([text], { type: 'application/json;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(() => URL.revokeObjectURL(url), 1000);
}

function openDirectDownloadModal() {
    if (window.jQuery && typeof $('#directDownloadModal').modal === 'function') {
        $('#directDownloadModal').modal('show');
    } else if (window.bootstrap && bootstrap.Modal) {
        new bootstrap.Modal(document.getElementById('directDownloadModal')).show();
    } else {
        const el = document.getElementById('directDownloadModal');
        if (el) {
            el.classList.add('show');
            el.style.display = 'block';
        }
    }
}

function closeDirectDownloadModal() {
    if (window.jQuery && typeof $('#directDownloadModal').modal === 'function') {
        $('#directDownloadModal').modal('hide');
    }
    const el = document.getElementById('directDownloadModal');
    if (el) {
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
    }
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
}

function cancelDirectDownload() {
    isDirectDownloadCancelled = true;
    const statusEl = document.getElementById('directDownloadStatus');
    if (statusEl) {
        statusEl.innerHTML = '<span class="text-danger"><em class="icon ni ni-cross-circle me-1"></em> Cancelling...</span>';
    }
}

async function startDirectBulkDownload(status = 'pending', customIds = null) {
    isDirectDownloadCancelled = false;
    openDirectDownloadModal();

    const statusEl = document.getElementById('directDownloadStatus');
    const barEl = document.getElementById('directDownloadBar');
    const pctEl = document.getElementById('directDownloadPercent');
    const fileEl = document.getElementById('directDownloadCurrentFile');
    const statsEl = document.getElementById('directDownloadStats');
    const btnCancel = document.getElementById('btnCancelDirectDownload');
    const btnClose = document.getElementById('btnCloseDirectDownload');

    statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1 text-primary"></span> Contacting server...';
    fileEl.textContent = 'Fetching batch jobs list...';
    barEl.style.width = '0%';
    pctEl.textContent = '0%';
    statsEl.textContent = '0 / 0';
    btnCancel.classList.remove('d-none');
    btnClose.classList.add('d-none');

    let url = "{{ route('content-gen.batch-jobs.bulk-payloads') }}";
    if (customIds && customIds.length) {
        url += '?ids=' + encodeURIComponent(customIds.join(','));
    } else {
        url += '?status=' + encodeURIComponent(status);
    }

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        if (!data.success || !data.items || data.items.length === 0) {
            statusEl.innerHTML = '<span class="text-warning"><em class="icon ni ni-alert-circle me-1"></em> No batch jobs found to download.</span>';
            fileEl.textContent = 'Zero files matched criteria.';
            btnCancel.classList.add('d-none');
            btnClose.classList.remove('d-none');
            return;
        }

        const items = data.items;
        const total = items.length;
        statsEl.textContent = `0 / ${total}`;

        for (let i = 0; i < total; i++) {
            if (isDirectDownloadCancelled) {
                statusEl.innerHTML = '<span class="text-warning"><em class="icon ni ni-alert-circle me-1"></em> Download stopped by user.</span>';
                fileEl.textContent = `Downloaded ${i} of ${total} files.`;
                btnCancel.classList.add('d-none');
                btnClose.classList.remove('d-none');
                return;
            }

            const item = items[i];
            fileEl.textContent = `Saving ${item.filename}...`;
            statusEl.innerHTML = `<span class="spinner-border spinner-border-sm me-1 text-primary"></span> Downloading file ${i + 1} of ${total}...`;

            triggerBlobDownload(item.payload, item.filename);

            const pct = Math.round(((i + 1) / total) * 100);
            barEl.style.width = pct + '%';
            pctEl.textContent = pct + '%';
            statsEl.textContent = `${i + 1} / ${total}`;

            // Small delay between downloads so the browser can queue the files safely
            await new Promise(r => setTimeout(r, 120));
        }

        barEl.style.width = '100%';
        pctEl.textContent = '100%';
        statusEl.innerHTML = '<span class="text-success"><em class="icon ni ni-check-circle-fill me-1"></em> All files downloaded successfully!</span>';
        fileEl.textContent = `Saved ${total} .json file(s) to your Downloads folder.`;
        btnCancel.classList.add('d-none');
        btnClose.classList.remove('d-none');
    } catch (err) {
        statusEl.innerHTML = '<span class="text-danger"><em class="icon ni ni-cross-circle me-1"></em> Error downloading files!</span>';
        fileEl.textContent = err.message;
        btnCancel.classList.add('d-none');
        btnClose.classList.remove('d-none');
    }
}

function getSelectedJobIds() {
    return Array.from(document.querySelectorAll('.job-check:checked')).map(cb => cb.value);
}

function toggleSelectAllJobs(master) {
    const checkboxes = document.querySelectorAll('.job-check');
    checkboxes.forEach(cb => cb.checked = master.checked);
    onJobSelectionChange();
}

function onJobSelectionChange() {
    const selected = getSelectedJobIds();
    const bar = document.getElementById('bulkSelectionBar');
    const countEl = document.getElementById('selectedCount');
    const master = document.getElementById('selectAllJobsCheck');
    const allCbs = document.querySelectorAll('.job-check');

    if (countEl) countEl.textContent = selected.length;

    if (master && allCbs.length > 0) {
        master.checked = selected.length === allCbs.length;
        master.indeterminate = selected.length > 0 && selected.length < allCbs.length;
    }

    if (bar) {
        if (selected.length > 0) {
            bar.classList.remove('d-none');
            bar.classList.add('d-flex');
        } else {
            bar.classList.add('d-none');
            bar.classList.remove('d-flex');
        }
    }
}

function clearJobSelection() {
    document.querySelectorAll('.job-check').forEach(cb => cb.checked = false);
    const master = document.getElementById('selectAllJobsCheck');
    if (master) {
        master.checked = false;
        master.indeterminate = false;
    }
    onJobSelectionChange();
}

function downloadSelected(type) {
    const selected = getSelectedJobIds();
    if (selected.length === 0) {
        alert('Please select at least one batch job.');
        return;
    }

    if (type === 'zip') {
        window.location.href = "{{ route('content-gen.batch-jobs.download-bulk') }}?ids=" + selected.join(',');
    } else {
        startDirectBulkDownload('custom', selected);
    }
}

function confirmDeleteSelected() {
    const selected = getSelectedJobIds();
    if (selected.length === 0) {
        alert('Please select at least one batch job to delete.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${selected.length} selected batch job(s)? All associated queue items will be permanently removed.`)) {
        document.getElementById('bulkDeleteIds').value = selected.join(',');
        document.getElementById('bulkDeleteForm').submit();
    }
}
</script>
@endsection
