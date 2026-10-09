@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">
                    <em class="icon ni ni-report text-danger me-2"></em> Reported Reviews Queue
                </h3>
                <p class="text-soft">User and business reports submitted on published reviews. Automatically triggers reassessment via Moderation API.</p>
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
                            <th style="width: 80px;">Report #</th>
                            <th>Review</th>
                            <th>Business</th>
                            <th>Reporter</th>
                            <th>Reason Type</th>
                            <th>Details</th>
                            <th>Reassessment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        @php
                            $review = $report->review;
                            $bizName = $review?->business?->translations?->first()?->name ?? $review?->business?->name ?? 'Unknown';
                            $translation = $review?->translations?->first();
                        @endphp
                        <tr>
                            <td class="fw-bold fs-12px">#{{ $report->id }}</td>
                            <td>
                                @if($review)
                                    <div>
                                        <a href="{{ route('reviews', ['search' => $review->id]) }}" class="fw-medium text-primary">Review #{{ $review->id }}</a>
                                        <span class="badge bg-outline-warning ms-1">? {{ number_format($review->rating, 1) }}</span>
                                    </div>
                                    <div class="text-soft fs-11px text-truncate" style="max-width: 200px;">{{ $translation?->title ?: $translation?->description }}</div>
                                @else
                                    <span class="text-soft">[Deleted Review]</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-medium text-dark">{{ \Illuminate\Support\Str::limit($bizName, 20) }}</span>
                            </td>
                            <td>
                                <div class="fs-12px">
                                    <div class="fw-medium">{{ $report->user?->first_name ?? 'User' }} {{ $report->user?->last_name ?? '' }}</div>
                                    <span class="badge bg-{{ $report->reporter_type === 'business' ? 'info' : 'light text-dark' }} fs-10px">
                                        {{ ucfirst($report->reporter_type ?: 'user') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-danger-dim text-danger fs-11px">
                                    {{ strtoupper(str_replace('_', ' ', $report->report_type ?: $report->report_reason)) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-soft fs-12px text-truncate" style="max-width: 220px;" title="{{ $report->report_details }}">
                                    {{ $report->report_details ?: 'No additional details provided' }}
                                </div>
                            </td>
                            <td>
                                @if($report->remoderation_triggered)
                                    <span class="badge bg-success-dim text-success fs-11px">
                                        {{ str_replace('_', ' ', $report->remoderation_result ?: 'Triggered') }}
                                    </span>
                                @else
                                    <span class="text-soft fs-11px">Pending</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $report->status === 'reviewed' ? 'success' : 'warning' }} fs-11px">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="text-soft fs-11px text-nowrap">
                                {{ $report->created_at?->format('M d, Y') }}
                            </td>
                            <td class="text-end pe-3">
                                @if($review)
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#removeModal-{{ $review->id }}">
                                        Remove Review
                                    </button>

                                    <!-- Remove Modal -->
                                    <div class="modal fade" id="removeModal-{{ $review->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('admin.reviews.remove', $review->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-danger"><em class="icon ni ni-trash me-1"></em> Remove Review #{{ $review->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-soft mb-2">Review will be unpublished due to verified report violation.</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Removal Reason <span class="text-danger">*</span></label>
                                                            <textarea name="removal_reason" class="form-control" rows="3" required placeholder="State reason based on report #{{ $report->id }}..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Removal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-soft">No review reports submitted.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($reports->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
