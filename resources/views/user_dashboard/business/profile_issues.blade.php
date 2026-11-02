@section('title', 'Profile Issues & Corrections - Manage ' . ($business->translations->first()->name ?? 'Business') . ' | Localio')

@extends('user_dashboard_layout.master')

@section('content')
@php
    $bizName = $business->translations->first()->name ?? 'Business';
@endphp

<div class="col-lg-9 p-0">
    <div class="user_content">
        
        <!-- Header -->
        <div class="uer_nm pb-3 mb-4" style="border-bottom: 1px solid #eef2f6;">
            <h1>Profile Issues & Corrections</h1>
            <p class="text-muted mb-0" style="font-size: 15px;">Report inaccurate information or suggest updates for <strong>{{ $bizName }}</strong> directly to Localio editors.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Editorial Policy Notice -->
        <div class="alert alert-info border d-flex align-items-center mb-4" style="background: #f0f7ff; border-color: #bcd9fb !important;">
            <i class="fas fa-shield-alt text-primary fa-2x me-3" style="margin-right: 14px;"></i>
            <div style="font-size: 13.5px; color: #1e3a5f;">
                <strong>Localio Editorial Policy:</strong> To protect review integrity and community trust, businesses do not directly edit public profile information. Correction requests are sent privately to the Localio editorial team, who verify the details and update the live page accordingly.
            </div>
        </div>

        <!-- Suggest Correction Form -->
        <div class="card p-4 mb-4 border rounded shadow-sm">
            <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 17px;">
                <i class="fas fa-edit text-primary me-2"></i> Suggest a Correction or Update
            </h5>

            @if($membership->hasPermission('can_suggest_corrections'))
                <form method="POST" action="{{ route('user.business.store_correction', ['business_id' => $business->id]) }}">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
                        <div>
                            <label style="display: block; font-size: 13.5px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Topic / Category of Information <span class="text-danger">*</span></label>
                            <select name="issue_type" class="form-select" required style="height: 46px !important; min-height: 46px !important; font-size: 14px !important; font-weight: 400 !important; color: #1e293b !important; background-color: #fff !important; border: 1px solid #d1d5db !important; border-radius: 8px !important; padding: 10px 36px 10px 14px !important; line-height: 1.5 !important; cursor: pointer; display: block; width: 100%;">
                                <option value="general_info">General Information / Description</option>
                                <option value="pricing">Pricing, Plans & Free Trials</option>
                                <option value="features">Features, Specifications & Capabilities</option>
                                <option value="contact">Official Website, Headquarters or Support Contact</option>
                                <option value="other">Other Information</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 13.5px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Supporting Link / Source (Optional)</label>
                            <input type="url" name="supporting_link" class="form-control" style="height: 46px; min-height: 46px; font-size: 14px; border-radius: 8px; border: 1px solid #d1d5db; padding: 10px 14px; display: block; width: 100%;" placeholder="https://yourwebsite.com/official-docs-or-pricing">
                        </div>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Current Inaccurate Information (Optional)</label>
                        <input type="text" name="current_info" class="form-control" style="height: 46px; min-height: 46px; font-size: 14px; border-radius: 8px; border: 1px solid #d1d5db; padding: 10px 14px; display: block; width: 100%;" placeholder="e.g. Current pricing is listed as $9.99/mo...">
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Suggested Correction & Verification Details <span class="text-danger">*</span></label>
                        <textarea name="suggested_correction" class="form-control" rows="4" style="font-size: 14px; border-radius: 8px; border: 1px solid #d1d5db; padding: 12px 14px; line-height: 1.5; display: block; width: 100%;" placeholder="Describe the accurate information clearly, including any relevant dates or verified details..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4" style="background: #003f7d; border-color: #003f7d;">
                            <i class="fas fa-paper-plane me-1"></i> Submit Correction to Localio
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-lock me-1"></i> You do not have permission to submit correction requests. Please contact your team administrator.
                </div>
            @endif
        </div>

        <!-- Correction Requests History Table -->
        <div class="card p-4 border rounded shadow-sm">
            <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 17px;">
                <i class="fas fa-history text-muted me-2"></i> Correction Request History
            </h5>

            @if($corrections->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="font-size: 13.5px;">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Topic</th>
                                <th>Suggested Correction</th>
                                <th>Submitted By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($corrections as $corr)
                                <tr>
                                    <td class="text-muted" style="white-space: nowrap;">{{ $corr->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-capitalize">
                                            {{ str_replace('_', ' ', $corr->issue_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="max-width: 350px;">
                                            <p class="mb-1 text-dark" style="line-height: 1.4;">{{ Str::limit($corr->suggested_correction, 120) }}</p>
                                            @if($corr->admin_notes)
                                                <small class="text-primary d-block">
                                                    <strong>Localio Note:</strong> {{ $corr->admin_notes }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $corr->user ? $corr->user->displayName() : 'Team' }}</td>
                                    <td>
                                        @if($corr->status === 'applied')
                                            <span class="badge bg-success">Applied</span>
                                        @elseif($corr->status === 'declined')
                                            <span class="badge bg-danger">Declined</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Under Review</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $corrections->links() }}
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    No correction requests submitted yet.
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

