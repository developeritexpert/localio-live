@extends('admin_layout.master')

@section('title', 'Batch Job Items - #' . $job->id)

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Batch Job #{{ $job->id }} Items</h3>
                    <p class="nk-block-des text-soft">
                        Batch ID: <code>{{ $job->batch_id ?? '-' }}</code>
                        &bull; Provider: <strong>{{ ucfirst($job->provider) }}</strong>
                        &bull; Fields: <strong>{{ $job->prompt_type ?? 'All' }}</strong>
                    </p>
                </div>
                <div class="nk-block-head-content d-flex gap-2">
                    <a href="{{ route('content-gen.poll-now') }}" class="btn btn-outline-info btn-sm">
                        <em class="icon ni ni-reload me-1"></em> Poll Status Now
                    </a>
                    <a href="{{ route('content-gen.batch-jobs') }}" class="btn btn-outline-secondary btn-sm">
                        <em class="icon ni ni-arrow-left me-1"></em> Back to Batch Jobs
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

        {{-- Job Overview Summary Card --}}
        <div class="card card-bordered mb-3">
            <div class="card-inner py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <span class="text-soft small d-block">Status</span>
                        @php
                            $badgeClass = match($job->status) {
                                'completed'  => 'bg-success',
                                'processing' => 'bg-info',
                                'failed'     => 'bg-danger',
                                default      => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} fs-12px">{{ ucfirst($job->status) }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-soft small d-block">Progress</span>
                        <strong>{{ $job->completed_items }}</strong> / {{ $job->total_items }} Completed
                        @if($job->failed_items > 0)
                            <span class="text-danger ms-1">({{ $job->failed_items }} failed)</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <span class="text-soft small d-block">Submitted</span>
                        <span>{{ $job->submitted_at ? $job->submitted_at->format('Y-m-d H:i') : '-' }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-soft small d-block">Completed</span>
                        <span>{{ $job->completed_at ? $job->completed_at->format('Y-m-d H:i') : ($job->status === 'processing' ? 'In progress...' : '-') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="card card-bordered">
            <div class="card-inner p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Business</th>
                                <th>Field</th>
                                <th>Request ID</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td class="text-muted small">{{ $item->id }}</td>
                                    <td>
                                        <strong>{{ $businessNames[$item->resource_id] ?? ('Business #' . $item->resource_id) }}</strong>
                                        <div class="text-soft small">ID: {{ $item->resource_id }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-dim bg-primary">
                                            {{ \App\Models\AiContentQueue::$fieldLabels[$item->content_field] ?? $item->content_field }}
                                        </span>
                                    </td>
                                    <td><code class="small">{{ $item->openai_request_id }}</code></td>
                                    <td>
                                        @if($item->status === 'completed')
                                            <span class="badge bg-success"><em class="icon ni ni-check me-1"></em>Completed</span>
                                        @elseif($item->status === 'processing' || $item->status === 'pending')
                                            <span class="badge bg-info"><em class="icon ni ni-loader me-1"></em>{{ ucfirst($item->status) }}</span>
                                        @elseif($item->status === 'failed')
                                            <span class="badge bg-danger" title="{{ $item->error_message }}"><em class="icon ni ni-cross me-1"></em>Failed</span>
                                            @if($item->error_message)
                                                <div class="text-danger small mt-1" style="max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $item->error_message }}">
                                                    {{ $item->error_message }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-soft small">{{ $item->updated_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            @if($item->status === 'completed' && !empty($item->result))
                                                <a href="{{ route('content-gen.view-result', $item->id) }}" class="btn btn-xs btn-outline-primary" title="View Result">
                                                    <em class="icon ni ni-eye me-1"></em> View
                                                </a>
                                                <form action="{{ route('content-gen.apply-result', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apply this result to the live business record?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-primary" title="Apply to Business">
                                                        <em class="icon ni ni-save"></em>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('content-gen.retry-failed') }}" method="POST" class="d-inline" onsubmit="return confirm('Trigger generation for this item?');">
                                                @csrf
                                                <input type="hidden" name="business_id" value="{{ $item->resource_id }}">
                                                <input type="hidden" name="field" value="{{ $item->content_field }}">
                                                <input type="hidden" name="provider" value="{{ $job->provider }}">
                                                <button type="submit" class="btn btn-xs btn-outline-warning" title="Re-generate">
                                                    <em class="icon ni ni-reload"></em>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        No items recorded for this batch job.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($items->hasPages())
                <div class="card-inner">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
