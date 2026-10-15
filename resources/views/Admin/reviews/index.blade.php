@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <!-- Header -->
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">
                    <em class="icon ni ni-comments text-primary me-2"></em> All Reviews
                </h3>
                <p class="text-soft">Manage, filter, and monitor all reviews across the 3-layer moderation pipeline.</p>
            </div>
            <div class="nk-block-head-content">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reviews.batches') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-layers me-1"></em> Batches
                        @php
                            $waitingBatchCount = \App\Models\Review::readyForBatch()->count();
                        @endphp
                        @if($waitingBatchCount > 0)
                            <span class="badge bg-primary ms-1">{{ $waitingBatchCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.reviews.pending') }}" class="btn btn-warning btn-sm">
                        <em class="icon ni ni-shield-alert me-1"></em> Pending Queue
                        @php
                            $pendingCount = \App\Models\Review::where('status', \App\Models\Review::STATUS_PENDING_REVIEW)->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('review-add') }}" class="btn btn-primary btn-sm">
                        <em class="icon ni ni-plus"></em> Add Review
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat Badges -->
    @if(isset($stats))
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-2">
            <div class="card card-bordered p-2 text-center bg-lighter">
                <span class="text-soft fs-12px">Total</span>
                <span class="fw-bold fs-16px text-dark">{{ $stats['total'] }}</span>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card card-bordered p-2 text-center bg-info-dim">
                <span class="text-info fs-12px">Processing</span>
                <span class="fw-bold fs-16px text-info">{{ $stats['processing'] }}</span>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card card-bordered p-2 text-center bg-success-dim">
                <span class="text-success fs-12px">Published</span>
                <span class="fw-bold fs-16px text-success">{{ $stats['published'] }}</span>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card card-bordered p-2 text-center bg-warning-dim">
                <span class="text-warning fs-12px">Pending Review</span>
                <span class="fw-bold fs-16px text-warning">{{ $stats['pending_review'] }}</span>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card card-bordered p-2 text-center bg-danger-dim">
                <span class="text-danger fs-12px">Rejected</span>
                <span class="fw-bold fs-16px text-danger">{{ $stats['rejected'] }}</span>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card card-bordered p-2 text-center bg-secondary-dim">
                <span class="text-secondary fs-12px">Removed</span>
                <span class="fw-bold fs-16px text-secondary">{{ $stats['removed'] }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="card card-bordered mb-4">
        <div class="card-inner py-3">
            <form action="{{ route('reviews') }}" method="GET" class="row g-2 align-items-center">
                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label fs-12px mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="removed" {{ request('status') === 'removed' ? 'selected' : '' }}>Removed</option>
                    </select>
                </div>

                <!-- Escalation Source Filter -->
                <div class="col-md-3">
                    <label class="form-label fs-12px mb-1">Escalation Source</label>
                    <select name="escalation_source" class="form-select form-select-sm">
                        <option value="">All Sources</option>
                        <option value="laravel_rule" {{ request('escalation_source') === 'laravel_rule' ? 'selected' : '' }}>Laravel Rule</option>
                        <option value="openai_moderation" {{ request('escalation_source') === 'openai_moderation' ? 'selected' : '' }}>OpenAI Moderation</option>
                        <option value="localio_ai" {{ request('escalation_source') === 'localio_ai' ? 'selected' : '' }}>Localio AI</option>
                        <option value="user_report" {{ request('escalation_source') === 'user_report' ? 'selected' : '' }}>User Report</option>
                        <option value="business_report" {{ request('escalation_source') === 'business_report' ? 'selected' : '' }}>Business Report</option>
                        <option value="manual" {{ request('escalation_source') === 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>

                <!-- AI Flags Filter -->
                <div class="col-md-3">
                    <label class="form-label fs-12px mb-1">Localio AI Flags</label>
                    <select name="ai_flag" class="form-select form-select-sm">
                        <option value="">All Flags</option>
                        <option value="SUSPECTED_FAKE" {{ request('ai_flag') === 'SUSPECTED_FAKE' ? 'selected' : '' }}>Suspected Fake</option>
                        <option value="SPAM" {{ request('ai_flag') === 'SPAM' ? 'selected' : '' }}>Spam</option>
                        <option value="PROMOTIONAL" {{ request('ai_flag') === 'PROMOTIONAL' ? 'selected' : '' }}>Promotional</option>
                        <option value="RATING_MISMATCH" {{ request('ai_flag') === 'RATING_MISMATCH' ? 'selected' : '' }}>Rating Mismatch</option>
                        <option value="CONFLICT_OF_INTEREST" {{ request('ai_flag') === 'CONFLICT_OF_INTEREST' ? 'selected' : '' }}>Conflict of Interest</option>
                        <option value="WRONG_BUSINESS" {{ request('ai_flag') === 'WRONG_BUSINESS' ? 'selected' : '' }}>Wrong Business</option>
                        <option value="DUPLICATE_LANGUAGE" {{ request('ai_flag') === 'DUPLICATE_LANGUAGE' ? 'selected' : '' }}>Duplicate Language</option>
                        <option value="SYNTHETIC_CONTENT" {{ request('ai_flag') === 'SYNTHETIC_CONTENT' ? 'selected' : '' }}>Synthetic Content</option>
                        <option value="LEGAL_ALLEGATION" {{ request('ai_flag') === 'LEGAL_ALLEGATION' ? 'selected' : '' }}>Legal Allegation</option>
                        <option value="SUSPICIOUS_ACTIVITY" {{ request('ai_flag') === 'SUSPICIOUS_ACTIVITY' ? 'selected' : '' }}>Suspicious Activity</option>
                        <option value="OTHER" {{ request('ai_flag') === 'OTHER' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label fs-12px mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="User, business, title..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><em class="icon ni ni-search"></em></button>
                        @if(request()->anyFilled(['status', 'escalation_source', 'ai_flag', 'search']))
                            <a href="{{ route('reviews') }}" class="btn btn-outline-secondary" title="Clear Filters"><em class="icon ni ni-cross"></em></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card card-bordered">
        <div class="card-inner p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Business</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Excerpt</th>
                            <th>Status</th>
                            <th>Escalation</th>
                            <th>AI Flags</th>
                            <th>Reports</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        @php
                            $translation = $review->translations->first();
                            $bizName     = $review->business?->translations?->first()?->name ?? $review->business?->name ?? 'Unknown';
                            $statusClass = match($review->status) {
                                'published', 'active' => 'success',
                                'processing'          => 'info',
                                'pending_review'      => 'warning',
                                'rejected'            => 'danger',
                                'removed'             => 'secondary',
                                default               => 'light',
                            };
                            $escalationBadge = match($review->escalation_source) {
                                'laravel_rule'       => ['label' => 'Laravel',   'class' => 'warning'],
                                'openai_moderation'  => ['label' => 'OpenAI Mod','class' => 'danger'],
                                'localio_ai'         => ['label' => 'Localio AI','class' => 'primary'],
                                'user_report'        => ['label' => 'User Rep',  'class' => 'info'],
                                'business_report'    => ['label' => 'Biz Rep',   'class' => 'info'],
                                'manual'             => ['label' => 'Manual',    'class' => 'secondary'],
                                default              => null,
                            };
                        @endphp
                        <tr>
                            <td class="fw-bold fs-12px">#{{ $review->id }}</td>
                            <td>
                                <span class="fw-medium text-dark">{{ \Illuminate\Support\Str::limit($bizName, 22) }}</span>
                            </td>
                            <td>
                                <div class="fs-12px">
                                    <div class="fw-medium text-dark">{{ $review->user->first_name ?? 'User' }} {{ $review->user->last_name ?? '' }}</div>
                                    <div class="text-soft fs-11px">{{ $review->user->email ?? '' }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-outline-warning text-warning fw-bold">
                                    ? {{ number_format($review->rating, 1) }}
                                </span>
                            </td>
                            <td>
                                <div style="max-width: 250px;">
                                    <div class="fw-medium text-dark text-truncate fs-12px">{{ $translation->title ?? 'No title' }}</div>
                                    <div class="text-soft text-truncate fs-11px">{{ $translation->description ?? '' }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusClass }} text-uppercase fs-11px">
                                    {{ str_replace('_', ' ', $review->status) }}
                                </span>
                            </td>
                            <td>
                                @if($escalationBadge)
                                    <span class="badge bg-outline-{{ $escalationBadge['class'] }} fs-11px">
                                        {{ $escalationBadge['label'] }}
                                    </span>
                                @else
                                    <span class="text-soft fs-11px">�</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($review->localio_ai_flags))
                                    @php
                                        $flags = is_array($review->localio_ai_flags) ? $review->localio_ai_flags : json_decode($review->localio_ai_flags, true);
                                    @endphp
                                    @if(is_array($flags) && count($flags) > 0)
                                        <span class="badge bg-danger-dim text-danger fs-11px" title="{{ implode(', ', $flags) }}">
                                            {{ str_replace('_', ' ', $flags[0]) }}
                                            @if(count($flags) > 1) +{{ count($flags) - 1 }} @endif
                                        </span>
                                    @else
                                        <span class="text-soft fs-11px">�</span>
                                    @endif
                                @else
                                    <span class="text-soft fs-11px">�</span>
                                @endif
                            </td>
                            <td>
                                @if($review->reports_count > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $review->reports_count }}</span>
                                @else
                                    <span class="text-soft fs-11px">0</span>
                                @endif
                            </td>
                            <td class="text-soft fs-11px text-nowrap">
                                {{ $review->created_at ? $review->created_at->format('M d, Y') : '' }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <ul class="link-list-opt no-bdr">
                                            @if($review->status === \App\Models\Review::STATUS_PENDING_REVIEW)
                                                <li><a href="{{ route('admin.reviews.pending') }}#review-{{ $review->id }}"><em class="icon ni ni-shield-alert text-warning"></em><span>Open in Queue</span></a></li>
                                            @endif
                                            @if(in_array($review->status, [\App\Models\Review::STATUS_PENDING_REVIEW, \App\Models\Review::STATUS_PROCESSING]))
                                                <li>
                                                    <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" onsubmit="return confirm('Approve and publish this review?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-link dropdown-item text-success"><em class="icon ni ni-check-circle"></em><span>Approve & Publish</span></button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $review->id }}"><em class="icon ni ni-cross-circle text-danger"></em><span>Reject</span></a>
                                                </li>
                                            @endif
                                            @if(in_array($review->status, [\App\Models\Review::STATUS_PUBLISHED, 'active']))
                                                <li>
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#removeModal-{{ $review->id }}"><em class="icon ni ni-trash text-danger"></em><span>Remove Review</span></a>
                                                </li>
                                            @endif
                                            <li><a href="{{ route('review-status-update', ['id' => $review->id]) }}"><em class="icon ni ni-swap-alt"></em><span>Toggle Visibility</span></a></li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Modals for Reject / Remove -->
                                <div class="modal fade" id="rejectModal-{{ $review->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger"><em class="icon ni ni-cross-circle me-1"></em> Reject Review #{{ $review->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <p class="text-soft mb-2">Rejection is recorded internally and reviewer is notified.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                                        <textarea name="reason" class="form-control" rows="3" required placeholder="State why this newly submitted review violates review policy..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="removeModal-{{ $review->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.reviews.remove', $review->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger"><em class="icon ni ni-trash me-1"></em> Remove Published Review #{{ $review->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <p class="text-soft mb-2">This review was previously published. It will be removed from public view while maintaining permanent internal audit history.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Removal Reason <span class="text-danger">*</span></label>
                                                        <textarea name="removal_reason" class="form-control" rows="3" required placeholder="Specify reason for removing this published review..."></textarea>
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
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-soft">No reviews matching the selected filters.</td>
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
