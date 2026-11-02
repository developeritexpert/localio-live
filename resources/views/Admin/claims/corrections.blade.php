@extends('admin_layout.master')

@section('title', 'Profile Correction Requests | Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1">Profile Corrections & Reports</h1>
            <p class="text-muted mb-0">Review private suggestions and inaccurate information reports submitted by claimed businesses.</p>
        </div>
        <div>
            <a href="{{ route('admin.claims.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-briefcase me-1"></i> View Business Claims
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
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.claims.corrections', ['status' => 'pending']) }}">
                Pending Review <span class="badge bg-warning text-dark ms-1">{{ $counts['pending'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'applied' ? 'active' : '' }}" href="{{ route('admin.claims.corrections', ['status' => 'applied']) }}">
                Applied / Resolved <span class="badge bg-success ms-1">{{ $counts['applied'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'declined' ? 'active' : '' }}" href="{{ route('admin.claims.corrections', ['status' => 'declined']) }}">
                Declined <span class="badge bg-danger ms-1">{{ $counts['declined'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.claims.corrections', ['status' => 'all']) }}">
                All Requests <span class="badge bg-secondary ms-1">{{ $counts['all'] }}</span>
            </a>
        </li>
    </ul>

    <!-- Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            @if($corrections->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Date</th>
                                <th>Business</th>
                                <th>Topic</th>
                                <th>Reported / Current</th>
                                <th>Suggested Correction</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($corrections as $corr)
                                @php
                                    $biz = $corr->business;
                                    $bizName = $biz?->translations?->first()?->name ?? 'Business #' . $corr->business_id;
                                    $user = $corr->user;
                                @endphp
                                <tr>
                                    <td class="ps-3 text-muted" style="white-space: nowrap;">
                                        {{ $corr->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $bizName }}</div>
                                        <small class="text-muted">By: {{ $user ? $user->displayName() : 'User' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-capitalize">
                                            {{ str_replace('_', ' ', $corr->issue_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="max-width: 200px;" class="text-muted">
                                            {{ $corr->current_info ?: '—' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="max-width: 320px;">
                                            <p class="mb-1 text-dark">{{ $corr->suggested_correction }}</p>
                                            @if($corr->supporting_link)
                                                <a href="{{ $corr->supporting_link }}" target="_blank" class="text-primary" style="font-size: 12px;">
                                                    <i class="fas fa-external-link-alt me-1"></i> Supporting Link
                                                </a>
                                            @endif
                                            @if($corr->admin_notes)
                                                <small class="text-muted d-block mt-1">
                                                    <strong>Admin Note:</strong> {{ $corr->admin_notes }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($corr->status === 'applied')
                                            <span class="badge bg-success">Applied</span>
                                        @elseif($corr->status === 'declined')
                                            <span class="badge bg-danger">Declined</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($corr->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#actionModal-{{ $corr->id }}-applied">
                                                <i class="fas fa-check me-1"></i> Apply
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#actionModal-{{ $corr->id }}-declined">
                                                <i class="fas fa-times me-1"></i> Decline
                                            </button>
                                        @else
                                            <small class="text-muted">Reviewed</small>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Action Modals -->
                                @if($corr->status === 'pending')
                                    <!-- Applied Modal -->
                                    <div class="modal fade" id="actionModal-{{ $corr->id }}-applied" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.claims.update_correction', ['id' => $corr->id]) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="applied">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold">Mark Correction as Applied</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Admin Note (Visible to business manager)</label>
                                                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="e.g. Verified and updated business pricing details."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Confirm Applied</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Declined Modal -->
                                    <div class="modal fade" id="actionModal-{{ $corr->id }}-declined" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.claims.update_correction', ['id' => $corr->id]) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="declined">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold">Decline Correction</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Reason / Note (Visible to business manager)</label>
                                                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="e.g. Could not verify information from official sources."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Declined</button>
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
                    {{ $corrections->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                    <h5>No correction requests found</h5>
                    <p>There are no profile corrections matching this status.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

