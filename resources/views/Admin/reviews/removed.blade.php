@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">
                    <em class="icon ni ni-archive text-secondary me-2"></em> Removed & Rejected Reviews Archive
                </h3>
                <p class="text-soft">Permanent audit trail of all reviews removed after publication or rejected during intake. Records are never physically deleted.</p>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('reviews') }}" class="btn btn-outline-secondary btn-sm">
                    <em class="icon ni ni-arrow-left"></em> All Reviews
                </a>
            </div>
        </div>
    </div>

    <div class="card card-bordered">
        <div class="card-inner p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Status</th>
                            <th>Business</th>
                            <th>Author</th>
                            <th>Rating</th>
                            <th>Original Content</th>
                            <th>AI & Moderation Record</th>
                            <th>Decision & Reason</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        @php
                            $translation = $review->translations->first();
                            $bizName     = $review->business?->translations?->first()?->name ?? $review->business?->name ?? 'Unknown';
                            $latestLog   = $review->moderationLogs->first();
                        @endphp
                        <tr>
                            <td class="fw-bold fs-12px">#{{ $review->id }}</td>
                            <td>
                                <span class="badge bg-{{ $review->status === 'rejected' ? 'danger' : 'secondary' }} text-uppercase fs-11px">
                                    {{ $review->status }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-medium text-dark">{{ \Illuminate\Support\Str::limit($bizName, 20) }}</span>
                            </td>
                            <td>
                                <div class="fs-12px">
                                    <div class="fw-medium">{{ $review->user?->first_name ?? 'User' }} {{ $review->user?->last_name ?? '' }}</div>
                                    <div class="text-soft fs-11px">{{ $review->user?->email }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-outline-warning text-warning">? {{ number_format($review->rating, 1) }}</span>
                            </td>
                            <td>
                                <div style="max-width: 250px;">
                                    <div class="fw-bold text-dark fs-12px text-truncate">{{ $translation?->title ?: 'No title' }}</div>
                                    <div class="text-soft fs-11px text-truncate">{{ $translation?->description ?: 'No text' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="fs-11px">
                                    @if($review->moderation_api_passed === false)
                                        <span class="badge bg-danger-dim text-danger mb-1">OpenAI Mod Flagged</span><br>
                                    @endif
                                    @if($review->localio_ai_decision)
                                        <span class="badge bg-warning-dim text-warning mb-1">Luna: {{ $review->localio_ai_decision }}</span><br>
                                        <span class="text-soft">Model: <code>{{ $review->localio_ai_model ?: 'gpt-5.6-luna' }}</code></span><br>
                                    @endif
                                    @if($review->escalation_source)
                                        <span class="text-soft">Escalation: <code>{{ $review->escalation_source }}</code></span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fs-12px" style="max-width: 220px;">
                                    @if($latestLog && !empty($latestLog->metadata['reason']))
                                        <div class="text-danger fw-medium">{{ $latestLog->metadata['reason'] }}</div>
                                    @elseif($latestLog && !empty($latestLog->metadata['removal_reason']))
                                        <div class="text-danger fw-medium">{{ $latestLog->metadata['removal_reason'] }}</div>
                                    @elseif($review->laravel_fail_reason)
                                        <div class="text-danger fw-medium">{{ str_replace('_', ' ', $review->laravel_fail_reason) }}</div>
                                    @else
                                        <span class="text-soft">Decision recorded by moderator</span>
                                    @endif
                                    @if($latestLog && $latestLog->actor)
                                        <div class="text-soft fs-11px mt-1">By: {{ $latestLog->actor->first_name }} (Moderator)</div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-soft fs-11px text-nowrap">
                                <div>Submitted: {{ $review->created_at?->format('M d, Y') }}</div>
                                <div>Updated: {{ $review->updated_at?->format('M d, Y') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-soft">No removed or rejected reviews on record.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($reviews->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
