@extends('admin_layout.master')

@section('title', 'Batch Jobs')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Batch Jobs</h3>
                    <p class="nk-block-des text-soft">History of all AI content generation batch requests.</p>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('content-gen.businesses') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-arrow-left me-1"></em> Back to Businesses
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-bordered card-stretch">
            <div class="card-inner p-0">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Provider</th>
                            <th>Batch ID</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Submitted</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr>
                                <td class="text-muted">{{ $job->id }}</td>
                                <td>
                                    @if($job->provider === 'openai')
                                        <span class="badge bg-success">OpenAI</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Anthropic</span>
                                    @endif
                                </td>
                                <td><code class="small">{{ $job->batch_id ?? '-' }}</code></td>
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
                                    <span class="text-success">{{ $job->completed_items }}</span>
                                    / {{ $job->total_items }}
                                    @if($job->failed_items > 0)
                                        <span class="text-danger ms-1">({{ $job->failed_items }} failed)</span>
                                    @endif
                                </td>
                                <td class="text-soft small">{{ $job->submitted_at ? $job->submitted_at->diffForHumans() : '-' }}</td>
                                <td class="text-soft small">{{ $job->completed_at ? $job->completed_at->diffForHumans() : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">No batch jobs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($jobs->hasPages())
                <div class="card-inner">{{ $jobs->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
