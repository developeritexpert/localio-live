@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">
                    <em class="icon ni ni-layers text-primary me-2"></em> Moderation Cases
                </h3>
                <p class="text-soft">Automated detection of clustered manipulation (IP clusters, burst submission spikes, coordinated rings).</p>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('reviews') }}" class="btn btn-outline-secondary btn-sm">
                    <em class="icon ni ni-arrow-left"></em> All Reviews
                </a>
            </div>
        </div>
    </div>

    @if($cases->isEmpty())
        <div class="card card-bordered">
            <div class="card-inner text-center py-5">
                <em class="icon ni ni-shield-check" style="font-size: 3rem; color: #1ee0ac;"></em>
                <h5 class="mt-3 text-success">No Suspicious Cases Detected</h5>
                <p class="text-soft">No coordinated review rings or IP clusters have been detected in recent activity.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($cases as $case)
            @php
                $statusClass = match($case->status) {
                    'open'          => 'danger',
                    'investigating' => 'warning',
                    'resolved'      => 'success',
                    default         => 'secondary',
                };
            @endphp
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-outline-dark fw-bold">{{ $case->case_number }}</span>
                            <h6 class="mb-0 text-dark">{{ $case->title }}</h6>
                            <span class="badge bg-{{ $statusClass }} text-uppercase fs-11px">{{ $case->status }}</span>
                        </div>
                        <div class="text-soft fs-12px">
                            Detected: {{ $case->created_at?->diffForHumans() }}
                        </div>
                    </div>
                    <div class="card-inner">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded fs-12px">
                                    <div class="mb-2"><strong>Target Business:</strong> {{ $case->business?->translations?->first()?->name ?? 'N/A' }}</div>
                                    <div class="mb-2"><strong>Case Type:</strong> <code>{{ str_replace('_', ' ', $case->type) }}</code></div>
                                    <div class="mb-2"><strong>Total Reviews Attached:</strong> <span class="badge bg-danger rounded-pill">{{ $case->reviews_count }}</span></div>
                                    @if(!empty($case->signals['ip_address']))
                                        <div class="mb-1"><strong>Common IP:</strong> <code>{{ $case->signals['ip_address'] }}</code></div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <p class="text-dark fs-13px mb-3">{{ $case->summary }}</p>
                                <h6 class="overline-title text-muted mb-2">Attached Reviews in this Cluster</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered fs-12px mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Review ID</th>
                                                <th>Author</th>
                                                <th>Rating</th>
                                                <th>Status</th>
                                                <th>Submitted</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($case->reviews->take(5) as $cr)
                                            <tr>
                                                <td><a href="{{ route('reviews', ['search' => $cr->id]) }}">#{{ $cr->id }}</a></td>
                                                <td>{{ $cr->user?->first_name }} {{ $cr->user?->last_name }}</td>
                                                <td>? {{ number_format($cr->rating, 1) }}</td>
                                                <td><span class="badge bg-outline-{{ $cr->status === 'published' ? 'success' : 'warning' }} fs-10px">{{ $cr->status }}</span></td>
                                                <td class="text-soft">{{ $cr->created_at?->format('M d, H:i') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($cases->hasPages())
        <div class="mt-4">
            {{ $cases->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
