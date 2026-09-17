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
                        History of all AI content generation batch requests.
                        @if($lastPollAt)
                            &bull; <span class="text-success"><em class="icon ni ni-clock me-1"></em>Last poll: {{ \Carbon\Carbon::parse($lastPollAt)->diffForHumans() }}</span>
                        @else
                            &bull; <span class="text-warning"><em class="icon ni ni-alert-circle me-1"></em>Scheduler poll not recorded yet</span>
                        @endif
                    </p>
                </div>
                <div class="nk-block-head-content d-flex gap-2">
                    <a href="{{ route('content-gen.poll-now') }}" class="btn btn-outline-info btn-sm">
                        <em class="icon ni ni-reload me-1"></em> Poll Status Now
                    </a>
                    <a href="{{ route('content-gen.businesses') }}" class="btn btn-outline-primary btn-sm">
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
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td class="text-muted small">{{ $job->id }}</td>
                                    <td>
                                        @if($job->provider === 'openai')
                                            <span class="badge bg-success">OpenAI</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Anthropic</span>
                                        @endif
                                    </td>
                                    <td><code class="small">{{ $job->batch_id ?? '-' }}</code></td>
                                    <td>
                                        @if($job->prompt_type)
                                            @foreach(explode(',', $job->prompt_type ?? '') as $f)
                                                <span class="badge badge-dim bg-secondary mb-1">
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
                                    <td class="text-end">
                                        <a href="{{ route('content-gen.batch-job-items', $job->id) }}" class="btn btn-xs btn-outline-primary">
                                            <em class="icon ni ni-list-check me-1"></em> View Items ({{ $job->queue_items_count }})
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-5">No batch jobs yet.</td></tr>
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
@endsection
