@extends('admin_layout.master')

@section('title', 'Translation Batch Jobs')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Translation Batch Jobs</h3>
                    <p class="nk-block-des text-soft">
                        Manage OpenAI and manual ChatGPT translation batch jobs. Download input JSON for ChatGPT and upload completed JSON results.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <em class="icon ni ni-upload-cloud me-1"></em> Upload Batch Result
                    </button>
                    <a href="{{ route('translations.poll-status') }}" class="btn btn-info btn-sm">
                        <em class="icon ni ni-reload me-1"></em> Poll Status Now
                    </a>
                    <a href="{{ route('translations.reviews') }}" class="btn btn-primary btn-sm">
                        <em class="icon ni ni-comments me-1"></em> Review Queue
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
                <em class="icon ni ni-alert-circle me-1"></em> {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Batch Jobs Table -->
        <div class="card card-bordered card-preview">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-12px text-muted">
                        <tr>
                            <th style="width: 80px;">Job ID</th>
                            <th style="min-width: 140px;">Type / Model</th>
                            <th style="width: 100px;" class="text-center">Requests</th>
                            <th style="width: 130px;">Status</th>
                            <th style="width: 140px;" class="text-center">Progress</th>
                            <th style="min-width: 140px;">Timestamps</th>
                            <th style="min-width: 180px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">#{{ $batch->id }}</span>
                                    @if($batch->openai_batch_id)
                                        <div class="text-muted fs-11px font-monospace">{{ \Illuminate\Support\Str::limit($batch->openai_batch_id, 14) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold fs-12px text-dark">{{ ucfirst(str_replace('_', ' ', $batch->type ?? 'review_translation')) }}</div>
                                    <span class="badge bg-light text-secondary fs-11px">{{ $batch->model ?? 'gpt-5.6-luna' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark fs-12px">{{ $batch->request_count }}</span>
                                </td>
                                <td>
                                    @if($batch->status === 'completed')
                                        <span class="badge bg-success">COMPLETED</span>
                                    @elseif($batch->status === 'submitted' || $batch->status === 'in_progress')
                                        <span class="badge bg-info">SUBMITTED</span>
                                    @elseif($batch->status === 'waiting')
                                        <span class="badge bg-warning">WAITING</span>
                                    @elseif($batch->status === 'failed')
                                        <span class="badge bg-danger">FAILED</span>
                                    @else
                                        <span class="badge bg-secondary">{{ strtoupper($batch->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2 fs-12px">
                                        <span class="text-success"><em class="icon ni ni-check"></em> {{ $batch->successful_count ?? 0 }}</span>
                                        <span class="text-danger"><em class="icon ni ni-cross"></em> {{ $batch->failed_count ?? 0 }}</span>
                                    </div>
                                    @php
                                        $pct = $batch->request_count > 0 ? round((($batch->successful_count + $batch->failed_count) / $batch->request_count) * 100) : 0;
                                    @endphp
                                    <div class="progress progress-sm mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-success" style="width: {{ $pct }}%;"></div>
                                    </div>
                                </td>
                                <td class="fs-11px text-muted">
                                    <div>Created: {{ $batch->created_at ? $batch->created_at->format('M d, H:i') : '—' }}</div>
                                    @if($batch->completed_at)
                                        <div class="text-success">Done: {{ \Carbon\Carbon::parse($batch->completed_at)->format('M d, H:i') }}</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('translations.batch-jobs.download', $batch->id) }}" class="btn btn-xs btn-outline-primary" title="Download Clean JSON for ChatGPT">
                                            <em class="icon ni ni-download"></em> JSON
                                        </a>
                                        <button type="button" class="btn btn-xs btn-outline-success" onclick="openJobUpload({{ $batch->id }})" title="Upload Completed ChatGPT Result">
                                            <em class="icon ni ni-upload"></em> Result
                                        </button>
                                        <form method="POST" action="{{ route('translations.batch-jobs.delete', $batch->id) }}" class="d-inline" onsubmit="return confirm('Delete batch job #{{ $batch->id }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No translation batch jobs created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($batches->hasPages())
                <div class="card-inner py-3 border-top">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Upload Result Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('translations.batch-jobs.upload-result') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="batch_id" id="upload_batch_id" value="">
            <div class="modal-header">
                <h5 class="modal-title">Upload Batch Result JSON</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted fs-12px mb-3">
                    Upload the completed JSON file returned by ChatGPT or paste the JSON text directly. The batch ID will be automatically detected.
                </p>
                <div class="mb-3">
                    <label class="form-label fs-12px">Upload JSON File</label>
                    <input type="file" name="result_file" class="form-control" accept=".json,.txt">
                </div>
                <div class="mb-3">
                    <label class="form-label fs-12px">Or Paste JSON Content</label>
                    <textarea name="result_text" class="form-control font-monospace fs-11px" rows="7" placeholder='{"batch_id": 1, "translations": [{"custom_id": "review_123_de-DE", "translated_title": "...", "translated_text": "..."}]}'></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Process Result</button>
            </div>
        </form>
    </div>
</div>

<script>
function openJobUpload(batchId) {
    document.getElementById('upload_batch_id').value = batchId;
    var modal = new bootstrap.Modal(document.getElementById('uploadModal'));
    modal.show();
}
</script>
@endsection