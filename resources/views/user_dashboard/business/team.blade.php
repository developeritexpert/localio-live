@section('title', 'Team & Permissions - Manage ' . ($business->translations->first()->name ?? 'Business') . ' | Localio')

@extends('user_dashboard_layout.master')

@section('content')
@php
    $bizName = $business->translations->first()->name ?? 'Business';
    $isOwner = $membership->isOwner();
    $isAdmin = $membership->isAdmin();
@endphp

<div class="col-lg-9 p-0">
    <div class="user_content">
        
        <!-- Header -->
        <div class="uer_nm d-flex align-items-center justify-content-between flex-wrap gap-2 pb-3 mb-4" style="border-bottom: 1px solid #eef2f6;">
            <div>
                <h1>Team & Permissions</h1>
                <p class="text-muted mb-0" style="font-size: 15px;">Invite colleagues, assign operational roles, and manage access for <strong>{{ $bizName }}</strong>.</p>
            </div>
            @if($membership->hasPermission('can_manage_team'))
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteTeamModal" style="background: #003f7d; border-color: #003f7d; border-radius: 8px; font-weight: 500; padding: 10px 18px;">
                    <i class="fas fa-user-plus me-1"></i> Invite Team Member
                </button>
            @endif
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

        <!-- Team Members List -->
        <div class="card p-0 border rounded shadow-sm overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Member</th>
                            <th>Role</th>
                            <th>Job Title</th>
                            <th>Permissions</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                            @php
                                $u = $m->user;
                                $displayName = $u ? $u->displayName() : ($m->work_email ?: 'Pending Invite');
                                $email = $m->work_email ?: ($u?->email ?? '');
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light border text-primary d-flex align-items-center justify-content-center me-3 font-weight-bold" style="width: 40px; height: 40px; font-size: 15px;">
                                            {{ strtoupper(substr($displayName, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-dark">
                                                {{ $displayName }}
                                                @if($m->user_id === Auth::id())
                                                    <span class="badge bg-secondary ms-1" style="font-size: 10px;">You</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($m->role === 'owner')
                                        <span class="badge bg-primary text-white" style="font-size: 12px;">Owner</span>
                                    @elseif($m->role === 'admin')
                                        <span class="badge bg-info text-white" style="font-size: 12px;">Admin</span>
                                    @else
                                        <span class="badge bg-light text-dark border" style="font-size: 12px;">Member</span>
                                    @endif
                                </td>
                                <td class="text-secondary">{{ $m->job_title ?: '—' }}</td>
                                <td>
                                    @if($m->isOwner())
                                        <span class="text-success font-weight-bold" style="font-size: 12.5px;"><i class="fas fa-check-double me-1"></i> Full Access</span>
                                    @else
                                        @php
                                            $activePerms = [];
                                            if ($m->hasPermission('can_reply_reviews')) $activePerms[] = 'Reply Reviews';
                                            if ($m->hasPermission('can_invite_reviews')) $activePerms[] = 'Invite Reviews';
                                            if ($m->hasPermission('can_manage_team')) $activePerms[] = 'Manage Team';
                                            if ($m->hasPermission('can_suggest_corrections')) $activePerms[] = 'Edit Info';
                                        @endphp
                                        <small class="text-muted">{{ implode(', ', $activePerms) ?: 'View Only' }}</small>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($membership->hasPermission('can_manage_team') && !$m->isOwner())
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editMemberModal-{{ $m->id }}">
                                                <i class="fas fa-sliders-h"></i> Permissions
                                            </button>
                                            <form method="POST" action="{{ route('user.business.remove_member', ['business_id' => $business->id, 'member_id' => $m->id]) }}" onsubmit="return confirm('Are you sure you want to remove this team member?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($m->isOwner())
                                        <small class="text-muted"><i class="fas fa-crown text-warning"></i> Primary Owner</small>
                                    @endif
                                </td>
                            </tr>

                            <!-- Edit Member Modal -->
                            @if(!$m->isOwner())
                                <div class="modal fade" id="editMemberModal-{{ $m->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('user.business.update_member', ['business_id' => $business->id, 'member_id' => $m->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title font-weight-bold">Edit Member Permissions</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label font-weight-bold">Member: {{ $displayName }}</label>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label font-weight-bold" style="display: block; margin-bottom: 8px;">Role</label>
                                                        <select name="role" class="form-select" style="height: 44px !important; min-height: 44px !important; font-size: 14px !important; font-weight: 400 !important; color: #1e293b !important; background-color: #fff !important; border: 1px solid #d1d5db !important; border-radius: 8px !important; padding: 8px 36px 8px 12px !important; line-height: 1.5 !important; cursor: pointer; display: block; width: 100%;">
                                                            <option value="member" {{ $m->role === 'member' ? 'selected' : '' }}>Member (Custom Permissions)</option>
                                                            <option value="admin" {{ $m->role === 'admin' ? 'selected' : '' }}>Admin (Full Management Access)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label font-weight-bold" style="display: block; margin-bottom: 8px;">Job Title</label>
                                                        <input type="text" name="job_title" class="form-control" value="{{ $m->job_title }}" style="height: 44px; font-size: 14px; border-radius: 8px; border: 1px solid #d1d5db; padding: 8px 12px; display: block; width: 100%;">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label font-weight-bold">Permissions</label>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="perm_reply_reviews" id="pr-{{ $m->id }}" {{ $m->hasPermission('can_reply_reviews') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="pr-{{ $m->id }}">Respond publicly to customer reviews</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="perm_report_reviews" id="prep-{{ $m->id }}" {{ $m->hasPermission('can_report_reviews') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="prep-{{ $m->id }}">Report reviews for guideline violations</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="perm_invite_reviews" id="pi-{{ $m->id }}" {{ $m->hasPermission('can_invite_reviews') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="pi-{{ $m->id }}">Send review invitations to customers</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="perm_suggest_corrections" id="pc-{{ $m->id }}" {{ $m->hasPermission('can_suggest_corrections') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="pc-{{ $m->id }}">Suggest profile corrections / report info</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary" style="background:#003f7d; border-color:#003f7d;">Save Permissions</button>
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
        </div>

        <!-- Transfer Ownership / Leave Profile Section -->
        <div class="card p-4 border rounded shadow-sm border-danger bg-light">
            <h5 class="font-weight-bold text-danger mb-2">
                <i class="fas fa-sign-out-alt me-1"></i> Leave Business Profile
            </h5>
            <p class="text-muted mb-3" style="font-size: 13.5px;">
                The claimed business profile is never permanently tied to one user account. You can leave this management team at any time. If you are the owner, ensure another administrator exists so they can continue managing the profile.
            </p>
            <form method="POST" action="{{ route('user.business.leave', ['business_id' => $business->id]) }}" onsubmit="return confirm('Are you sure you want to leave this business profile? You will no longer have access to manage it.');">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-door-open me-1"></i> Leave Business Profile
                </button>
            </form>
        </div>

    </div>
</div>

<!-- Modal: Invite Team Member -->
<div class="modal fade" id="inviteTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('user.business.invite_member', ['business_id' => $business->id]) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Invite New Team Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold" style="display: block; margin-bottom: 8px;">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="colleague@yourcompany.com" required style="height: 44px; font-size: 14px; border-radius: 8px; border: 1px solid #d1d5db; padding: 8px 12px; display: block; width: 100%;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold" style="display: block; margin-bottom: 8px;">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required style="height: 44px !important; min-height: 44px !important; font-size: 14px !important; font-weight: 400 !important; color: #1e293b !important; background-color: #fff !important; border: 1px solid #d1d5db !important; border-radius: 8px !important; padding: 8px 36px 8px 12px !important; line-height: 1.5 !important; cursor: pointer; display: block; width: 100%;">
                            <option value="member" selected>Member (Operational Access)</option>
                            <option value="admin">Admin (Full Team & Operational Access)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold" style="display: block; margin-bottom: 8px;">Job Title / Department</label>
                        <input type="text" name="job_title" class="form-control" placeholder="e.g. PR Specialist, Support Lead" style="height: 44px; font-size: 14px; border-radius: 8px; border: 1px solid #d1d5db; padding: 8px 12px; display: block; width: 100%;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Permissions</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="perm_reply_reviews" id="invite-perm-reply" checked>
                            <label class="form-check-label" for="invite-perm-reply">Respond publicly to customer reviews</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="perm_report_reviews" id="invite-perm-report" checked>
                            <label class="form-check-label" for="invite-perm-report">Report reviews for guideline violations</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="perm_invite_reviews" id="invite-perm-invite" checked>
                            <label class="form-check-label" for="invite-perm-invite">Send review invitations to customers</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="perm_suggest_corrections" id="invite-perm-corr" checked>
                            <label class="form-check-label" for="invite-perm-corr">Suggest profile corrections / report info</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#003f7d; border-color:#003f7d;">
                        <i class="fas fa-paper-plane me-1"></i> Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

