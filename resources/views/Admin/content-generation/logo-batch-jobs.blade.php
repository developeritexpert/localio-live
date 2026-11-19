@extends('admin_layout.master')

@section('title', 'Logo Batch Jobs')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">

        {{-- Page Header --}}
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Logo Batch Jobs</h3>
                    <p class="nk-block-des text-soft">
                        Manage SVG logo batch jobs. Download JSON info, generate SVGs, and bulk upload matching files.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="openBulkUpload(null)">
                        <em class="icon ni ni-upload-cloud me-1"></em> Bulk Upload Logos
                    </button>
                    <a href="{{ route('content-gen.logos') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-arrow-left me-1"></em> Logo Generation
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

        {{-- Status Tabs --}}
        <ul class="nav nav-tabs nav-tabs-mb-icon mb-3">
            <li class="nav-item {{ empty($statusFilter) ? 'active' : '' }}">
                <a class="nav-link {{ empty($statusFilter) ? 'active' : '' }}" href="{{ route('content-gen.logos.batch-jobs') }}">
                    <em class="icon ni ni-list me-1 text-primary"></em>
                    <span>All Batches</span>
                    <span class="badge rounded-pill bg-light text-dark border ms-1" style="font-size: 11px;">{{ $totalCount }}</span>
                </a>
            </li>
            <li class="nav-item {{ $statusFilter === 'pending' ? 'active' : '' }}">
                <a class="nav-link {{ $statusFilter === 'pending' ? 'active' : '' }}" href="{{ route('content-gen.logos.batch-jobs', ['status' => 'pending']) }}">
                    <em class="icon ni ni-clock me-1 text-warning"></em>
                    <span class="fw-semibold">Pending / In Progress</span>
                    <span class="badge rounded-pill bg-warning text-dark ms-1" style="font-size: 11px;">{{ $pendingCount }}</span>
                </a>
            </li>
            <li class="nav-item {{ $statusFilter === 'completed' ? 'active' : '' }}">
                <a class="nav-link {{ $statusFilter === 'completed' ? 'active' : '' }}" href="{{ route('content-gen.logos.batch-jobs', ['status' => 'completed']) }}">
                    <em class="icon ni ni-check-circle me-1 text-success"></em>
                    <span class="fw-semibold">Completed</span>
                    <span class="badge rounded-pill bg-success ms-1" style="font-size: 11px;">{{ $completedCount }}</span>
                </a>
            </li>
        </ul>

        {{-- Batch Jobs Table --}}
        <div class="card card-bordered card-preview">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-12px text-muted">
                        <tr>
                            <th style="width: 100px;">Job ID</th>
                            <th style="min-width: 150px;">Type / Target</th>
                            <th style="width: 120px;" class="text-center">Businesses</th>
                            <th style="width: 130px;">Status</th>
                            <th style="width: 160px;" class="text-center">Progress</th>
                            <th style="min-width: 160px;">Timestamps</th>
                            <th style="min-width: 200px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            @php
                                $total = $job->total_items ?: 1;
                                $done  = $job->completed_items ?: 0;
                                $pct   = min(100, round(($done / $total) * 100));
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark fs-14px">#{{ $job->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold fs-12px text-dark">SVG Logo Batch</div>
                                    <span class="badge bg-light text-secondary fs-11px">Filename: Logo-[ID].svg</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark fs-12px">{{ $job->total_items }}</span>
                                </td>
                                <td>
                                    @if($job->status === 'completed')
                                        <span class="badge bg-success">COMPLETED</span>
                                    @elseif($job->status === 'processing')
                                        <span class="badge bg-info"><em class="icon ni ni-loader me-1"></em>PROCESSING</span>
                                    @elseif($job->status === 'failed')
                                        <span class="badge bg-danger">FAILED</span>
                                    @else
                                        <span class="badge bg-warning text-dark">PENDING</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="fs-12px fw-semibold mb-1">
                                        <span class="text-success">{{ $done }}</span> / {{ $job->total_items }}
                                    </div>
                                    <div class="progress progress-sm" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: {{ $pct }}%;"></div>
                                    </div>
                                </td>
                                <td class="fs-11px text-muted">
                                    <div>Created: {{ $job->created_at ? $job->created_at->format('M d, H:i') : '—' }}</div>
                                    @if($job->completed_at)
                                        <div class="text-success">Done: {{ $job->completed_at->format('M d, H:i') }}</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('content-gen.logos.batch-jobs.download', $job->id) }}" class="btn btn-xs btn-outline-primary" title="Download JSON info for processing">
                                            <em class="icon ni ni-download me-1"></em> JSON
                                        </a>
                                        <button type="button" class="btn btn-xs btn-outline-success" onclick="openBulkUpload({{ $job->id }})" title="Bulk upload SVG logos for this batch">
                                            <em class="icon ni ni-upload me-1"></em> Upload SVGs
                                        </button>
                                        <form method="POST" action="{{ route('content-gen.logos.batch-jobs.delete', $job->id) }}" class="d-inline" onsubmit="return confirm('Delete logo batch job #{{ $job->id }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete Batch">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No logo batch jobs created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($jobs->hasPages())
                <div class="card-inner py-3 border-top">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Bulk SVG Upload Modal --}}
<div class="modal fade" id="bulkUploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('content-gen.logos.batch-jobs.upload-svg') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="batch_id" id="upload_batch_id" value="">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Upload SVG Logos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border mb-3">
                    <h6 class="fs-13px fw-bold text-dark mb-1"><em class="icon ni ni-info me-1 text-primary"></em> Filename Matching Rules:</h6>
                    <p class="fs-12px text-muted mb-2">
                        Files will be matched automatically to businesses by inspecting the filename:
                    </p>
                    <ul class="fs-12px text-muted ps-3 mb-0">
                        <li><code>Logo-[businessID].svg</code> (e.g. <code>Logo-2118.svg</code>) &bull; <strong class="text-primary">Recommended</strong></li>
                        <li><code>Logo-[batchID].svg</code> (e.g. <code>Logo-18.svg</code> when single business in batch)</li>
                        <li><code>Logo-[batchID]-[businessID].svg</code> (e.g. <code>Logo-18-2118.svg</code>)</li>
                        <li><code>Logo-[slug].svg</code> (e.g. <code>Logo-hostinger.svg</code>)</li>
                        <li>Direct ID: <code>[businessID].svg</code> (e.g. <code>2118.svg</code>)</li>
                    </ul>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fs-12px fw-semibold">Option A: Select Multiple SVG Files</label>
                        <input type="file" name="svg_files[]" class="form-control" multiple accept=".svg,image/svg+xml">
                        <span class="fs-11px text-muted">You can select 10, 50, or 200+ SVG files at once using Ctrl/Cmd + click.</span>
                    </div>

                    <div class="col-12">
                        <div class="text-center my-1"><span class="badge bg-light text-muted">— OR —</span></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fs-12px fw-semibold">Option B: Upload a ZIP Archive of SVG Files</label>
                        <input type="file" name="zip_file" class="form-control" accept=".zip">
                        <span class="fs-11px text-muted">Upload a <code>.zip</code> file containing all your named SVG logos. It will be unzipped and matched automatically.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <em class="icon ni ni-upload me-1"></em> Process & Apply SVG Logos
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openBulkUpload(batchId) {
    document.getElementById('upload_batch_id').value = batchId || '';
    var modal = new bootstrap.Modal(document.getElementById('bulkUploadModal'));
    modal.show();
}
</script>
@endsection
