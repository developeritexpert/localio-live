@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">
                    <em class="icon ni ni-undo text-warning me-2"></em> Review Appeals Queue
                </h3>
                <p class="text-soft">Appeals submitted by reviewers challenging rejection or removal decisions. Evaluated by human moderators with full audit context.</p>
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
                            <th style="width: 80px;">Appeal #</th>
                            <th>Review</th>
                            <th>Business</th>
                            <th>Reviewer</th>
                            <th>Appeal Statement</th>
                            <th>Status</th>
                            <th>Moderator Decision</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appeals as $appeal)
                        @php
                            $review      = $appeal->review;
                            $bizName     = $review?->business?->translations?->first()?->name ?? $review?->business?->name ?? 'Unknown';
                            $translation = $review?->translations?->first();
                            $statusClass = match($appeal->status) {
                                'open'         => 'warning',
                                'under_review' => 'info',
                                'resolved'     => 'success',
                                default        => 'light',
                            };
                        @endphp
                        <tr>
                            <td class="fw-bold fs-12px">#{{ $appeal->id }}</td>
                            <td>
                                @if($review)
                                    <div>
                                        <a href="{{ route('reviews', ['search' => $review->id]) }}" class="fw-medium text-primary">Review #{{ $review->id }}</a>
                                        <span class="badge bg-outline-{{ $review->status === 'rejected' ? 'danger' : 'secondary' }} fs-10px">{{ $review->status }}</span>
                                    </div>
                                    <div class="text-soft fs-11px text-truncate" style="max-width: 180px;">{{ $translation?->title ?: $translation?->description }}</div>
                                @else
                                    <span class="text-soft">[Deleted Review]</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-medium text-dark">{{ \Illuminate\Support\Str::limit($bizName, 20) }}</span>
                            </td>
                            <td>
                                <div class="fs-12px">
                                    <div class="fw-medium">{{ $appeal->user?->first_name ?? 'User' }} {{ $appeal->user?->last_name ?? '' }}</div>
                                    <div class="text-soft fs-11px">{{ $appeal->user?->email }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="p-2 bg-light rounded fs-12px text-dark" style="max-width: 250px;">
                                    "{{ $appeal->appeal_reason }}"
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusClass }} text-uppercase fs-11px">
                                    {{ str_replace('_', ' ', $appeal->status) }}
                                </span>
                            </td>
                            <td>
                                @if($appeal->moderator_decision)
                                    <span class="badge bg-outline-{{ $appeal->moderator_decision === 'restored' ? 'success' : 'danger' }} fs-11px">
                                        {{ str_replace('_', ' ', $appeal->moderator_decision) }}
                                    </span>
                                    @if($appeal->moderator)
                                        <div class="text-soft fs-10px">by {{ $appeal->moderator->first_name }}</div>
                                    @endif
                                @else
                                    <span class="text-soft fs-11px">Pending Review</span>
                                @endif
                            </td>
                            <td class="text-soft fs-11px text-nowrap">
                                {{ $appeal->created_at?->format('M d, Y') }}
                            </td>
                            <td class="text-end pe-3">
                                @if($appeal->status !== 'resolved')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#appealModal-{{ $appeal->id }}">
                                        Take Decision
                                    </button>

                                    <!-- Appeal Action Modal -->
                                    <div class="modal fade" id="appealModal-{{ $appeal->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.reviews.appeals.action', $appeal->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><em class="icon ni ni-shield-check me-1"></em> Review Appeal #{{ $appeal->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="p-3 bg-lighter rounded mb-3 fs-12px">
                                                            <strong>Reviewer Appeal Statement:</strong><br>
                                                            <span class="text-dark">"{{ $appeal->appeal_reason }}"</span>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Moderator Decision <span class="text-danger">*</span></label>
                                                            <select name="decision" class="form-select" required>
                                                                <option value="restore">Restore Review (Mark Published)</option>
                                                                <option value="confirm_rejection">Confirm Rejection / Removal</option>
                                                                <option value="request_info">Request Additional Information</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Internal Explanation / Notes</label>
                                                            <textarea name="notes" class="form-control" rows="3" placeholder="Provide context for this decision..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Decision</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-success fs-12px"><em class="icon ni ni-check-circle"></em> Resolved</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-soft">No review appeals pending.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($appeals->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $appeals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
