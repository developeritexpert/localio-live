@section('title', 'Get Reviews - Manage ' . ($business->translations->first()->name ?? 'Business') . ' | Localio')

@extends('user_dashboard_layout.master')

@section('content')
@php
    $bizName = $business->translations->first()->name ?? 'Business';
@endphp

<div class="col-lg-9 p-0">
    <div class="user_content">
        
        <!-- Header -->
        <div class="uer_nm pb-3 mb-4" style="border-bottom: 1px solid #eef2f6;">
            <h1>Get Reviews</h1>
            <p class="text-muted mb-0" style="font-size: 15px;">Invite your customers to review <strong>{{ $bizName }}</strong> on Localio and share your direct review link.</p>
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

        <!-- Card 1: Direct Review Link -->
        <div class="card p-4 mb-4 border rounded shadow-sm" style="background: #f8fafc;">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                    <i class="fas fa-link"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark" style="font-size: 16px;">Direct Review Link</h5>
                    <small class="text-muted">Customers who open this link are taken directly to the review submission modal for {{ $bizName }}.</small>
                </div>
            </div>

            <div class="input-group my-3">
                <input type="text" id="direct-review-link" class="form-control" value="{{ $directReviewUrl }}" readonly style="background: #fff; font-family: monospace; font-size: 14px; height: 46px; border: 1px solid #d1d5db; border-radius: 8px 0 0 8px; padding: 10px 14px;">
                <button class="btn btn-primary px-4" type="button" onclick="copyReviewLink()" style="background: #003f7d; border-color: #003f7d; border-radius: 0 8px 8px 0;">
                    <i class="fas fa-copy me-1"></i> <span id="copy-btn-text">Copy Link</span>
                </button>
            </div>
            <small class="text-muted"><i class="fas fa-lightbulb text-warning me-1"></i> Add this link to your website, thank-you emails, or customer support signatures.</small>
        </div>

        <!-- Card 2: Send Review Invitations via Email -->
        <div class="card p-4 mb-4 border rounded shadow-sm">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark" style="font-size: 16px;">Send Review Invitations by Email</h5>
                    <small class="text-muted">Paste your customers' email addresses to automatically send them official Localio review invitations.</small>
                </div>
            </div>

            @if($membership->hasPermission('can_invite_reviews'))
                <form method="POST" action="{{ route('user.business.send_invitations', ['business_id' => $business->id]) }}">
                    @csrf
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; font-size: 14px; color: #1e293b; margin-bottom: 8px;">Customer Email Addresses <span class="text-danger">*</span></label>
                        <textarea name="emails" class="form-control" rows="5" placeholder="Paste email addresses here (separated by commas or one per line)...
customer1@example.com
customer2@example.com, customer3@example.com" required style="font-size: 14px; font-family: monospace; border: 1px solid #d1d5db; border-radius: 8px; padding: 12px 14px; display: block; width: 100%;"></textarea>
                        <small class="text-muted mt-1 d-block">Up to 50 email addresses per batch.</small>
                    </div>

                    <div class="alert alert-light border p-3 mb-3 bg-light" style="font-size: 13.5px;">
                        <div class="font-weight-bold text-dark mb-1"><i class="fas fa-envelope text-primary me-1"></i> What customers receive:</div>
                        <div class="text-muted">
                            "<strong>{{ $bizName }}</strong> invites you to share your genuine experience and leave a review on Localio. Your honest feedback helps the community make informed decisions."
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-4" style="background: #10b981; border-color: #10b981;">
                            <i class="fas fa-paper-plane me-1"></i> Send Invitations Now
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-lock me-1"></i> You do not have permission to send review invitations. Please contact your team administrator.
                </div>
            @endif
        </div>

        <!-- Card 3: Invitation History -->
        <div class="card p-4 border rounded shadow-sm">
            <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 16px;">
                <i class="fas fa-history text-muted me-1"></i> Sent Invitations History
            </h5>

            @if($invitations->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="font-size: 13.5px;">
                        <thead class="table-light">
                            <tr>
                                <th>Recipient Email</th>
                                <th>Sent Date</th>
                                <th>Invited By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invitations as $inv)
                                <tr>
                                    <td class="font-weight-bold text-dark">{{ $inv->recipient_email }}</td>
                                    <td class="text-muted">{{ $inv->created_at ? $inv->created_at->format('M d, Y H:i') : '' }}</td>
                                    <td>{{ $inv->sender ? $inv->sender->displayName() : 'Team' }}</td>
                                    <td>
                                        @if($inv->status === 'reviewed')
                                            <span class="badge bg-success">Reviewed</span>
                                        @elseif($inv->status === 'opened')
                                            <span class="badge bg-info text-white">Opened</span>
                                        @else
                                            <span class="badge bg-secondary">Sent</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $invitations->links() }}
                </div>
            @else
                <div class="text-center py-4 text-muted" style="font-size: 14px;">
                    No invitations have been sent yet. Use the form above to send your first batch!
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function copyReviewLink() {
        const input = document.getElementById('direct-review-link');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);

        const btnText = document.getElementById('copy-btn-text');
        btnText.textContent = 'Copied!';
        setTimeout(() => {
            btnText.textContent = 'Copy Link';
        }, 2000);
    }
</script>
@endsection

