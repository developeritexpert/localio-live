@extends('admin_layout.master')

@section('title', 'Business Claims Management | Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1">Business Claims</h1>
            <p class="text-muted mb-0">Review and approve requests from owners, employees, and agencies to manage business profiles.</p>
        </div>
        <div>
            <a href="{{ route('admin.claims.corrections') }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i> View Profile Corrections
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Status Tabs -->
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'pending_manual_review' ? 'active' : '' }}" href="{{ route('admin.claims.index', ['status' => 'pending_manual_review']) }}">
                Pending Manual Review <span class="badge bg-warning text-dark ms-1">{{ $counts['pending'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.claims.index', ['status' => 'approved']) }}">
                Approved Claims <span class="badge bg-success ms-1">{{ $counts['approved'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('admin.claims.index', ['status' => 'rejected']) }}">
                Rejected <span class="badge bg-danger ms-1">{{ $counts['rejected'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.claims.index', ['status' => 'all']) }}">
                All Claims <span class="badge bg-secondary ms-1">{{ $counts['all'] }}</span>
            </a>
        </li>
    </ul>

    <!-- Claims Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            @if($claims->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Business</th>
                                <th>Claimant User</th>
                                <th>Connection & Role</th>
                                <th>Verification Method</th>
                                <th>Evidence / Details</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($claims as $c)
                                @php
                                    $biz = $c->business;
                                    $bizName = $biz?->translations?->first()?->name ?? 'Business #' . $c->business_id;
                                    $user = $c->user;
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="font-weight-bold text-dark">{{ $bizName }}</div>
                                        <small class="text-muted"><i class="fas fa-globe me-1"></i> {{ $biz?->website_domain ?: 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $user ? $user->displayName() : 'User #' . $c->user_id }}</div>
                                        <small class="text-muted">{{ $user?->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-capitalize mb-1">{{ $c->connection_type }}</span>
                                        @if($c->agency_authorized)
                                            <span class="badge bg-info text-white" style="font-size: 10px;">Agency Authorized ✓</span>
                                        @endif
                                        <div class="text-secondary" style="font-size: 13px;">{{ $c->job_title ?: 'No Title' }}</div>
                                    </td>
                                    <td>
                                        @if($c->verification_method === 'automatic')
                                            <span class="badge bg-success">Automatic (Email Code)</span>
                                            <div class="text-muted" style="font-size: 12px;">{{ $c->work_email }}</div>
                                        @else
                                            <span class="badge bg-warning text-dark">Manual Review</span>
                                            @if($c->work_email)
                                                <div class="text-muted" style="font-size: 12px;">{{ $c->work_email }}</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($c->evidence_text)
                                            <div style="font-size: 13px; max-width: 250px;">{{ Str::limit($c->evidence_text, 80) }}</div>
                                        @endif
                                        @if($c->evidence_channel)
                                            <div style="font-size: 12px;" class="text-primary">
                                                <i class="fas fa-external-link-alt me-1"></i> {{ Str::limit($c->evidence_channel, 30) }}
                                            </div>
                                        @endif
                                        @if($c->evidence_document)
                                            <div class="mt-1">
                                                <a href="{{ asset($c->evidence_document) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2" style="font-size: 11px;">
                                                    <i class="fas fa-file-download me-1"></i> View Document
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($c->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($c->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($c->status === 'pending_manual_review')
                                            <span class="badge bg-warning text-dark">Pending Review</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $c->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($c->status === 'pending_manual_review')
                                            <form method="POST" action="{{ route('admin.claims.approve', ['id' => $c->id]) }}" class="d-inline" onsubmit="return confirm('Approve this claim and grant owner access?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check me-1"></i> Approve
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#rejectClaimModal-{{ $c->id }}">
                                                <i class="fas fa-times me-1"></i> Reject
                                            </button>
                                        @elseif($c->admin_notes)
                                            <small class="text-muted d-block" style="font-size: 11.5px;">Note: {{ Str::limit($c->admin_notes, 30) }}</small>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Reject Modal -->
                                @if($c->status === 'pending_manual_review')
                                    <div class="modal fade" id="rejectClaimModal-{{ $c->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.claims.reject', ['id' => $c->id]) }}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold">Reject Claim for {{ $bizName }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Reason for Rejection <span class="text-danger">*</span></label>
                                                            <textarea name="reason" class="form-control" rows="3" placeholder="Explain why the evidence was insufficient or how the user can properly verify..." required></textarea>
                                                            <small class="text-muted">This explanation will be emailed to the claimant.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">
                    {{ $claims->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                    <h5>No claims found</h5>
                    <p>There are no business claim requests under this status.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

