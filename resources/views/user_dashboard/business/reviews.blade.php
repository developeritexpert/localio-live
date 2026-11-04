@section('title', 'Customer Reviews - Manage ' . ($business->translations->first()->name ?? 'Business') . ' | Localio')

@extends('user_dashboard_layout.master')

@section('content')
@php
    $bizName = $business->translations->first()->name ?? 'Business';
@endphp

<div class="col-lg-9 p-0">
    <div class="user_content">
        
        <!-- Header -->
        <div class="uer_nm d-flex align-items-center justify-content-between flex-wrap gap-2 pb-3 mb-4" style="border-bottom: 1px solid #eef2f6;">
            <div>
                <h1>Customer Reviews</h1>
                <p class="text-muted mb-0" style="font-size: 15px;">Read genuine customer reviews, publish official responses, and monitor feedback for <strong>{{ $bizName }}</strong>.</p>
            </div>
            <div>
                <a href="{{ route('user.business.get_reviews', ['business_id' => $business->id, 'locale' => app()->getLocale()]) }}" class="btn btn-primary" style="background: #003f7d; border-color: #003f7d; border-radius: 8px; font-weight: 500; padding: 10px 18px;">
                    <i class="fas fa-paper-plane me-1"></i> Get More Reviews
                </a>
            </div>
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

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('user.business.reviews', ['business_id' => $business->id, 'locale' => app()->getLocale()]) }}" class="card p-3 mb-4 bg-light border-0" style="border-radius: 10px;">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="rating" class="form-select" onchange="this.form.submit()" style="height: 42px !important; min-height: 42px !important; font-size: 14px !important; font-weight: 400 !important; color: #1e293b !important; background-color: #fff !important; border: 1px solid #d1d5db !important; border-radius: 8px !important; padding: 8px 36px 8px 12px !important; line-height: 1.5 !important; cursor: pointer; display: block; width: 100%;">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="filter_reply" class="form-select" onchange="this.form.submit()" style="height: 42px !important; min-height: 42px !important; font-size: 14px !important; font-weight: 400 !important; color: #1e293b !important; background-color: #fff !important; border: 1px solid #d1d5db !important; border-radius: 8px !important; padding: 8px 36px 8px 12px !important; line-height: 1.5 !important; cursor: pointer; display: block; width: 100%;">
                        <option value="">All Response Status</option>
                        <option value="unreplied" {{ request('filter_reply') == 'unreplied' ? 'selected' : '' }}>Awaiting Response</option>
                        <option value="replied" {{ request('filter_reply') == 'replied' ? 'selected' : '' }}>Responded</option>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <span class="text-muted" style="font-size: 13.5px;">Showing {{ $reviews->total() }} reviews</span>
                </div>
            </div>
        </form>

        <!-- Reviews List -->
        @if($reviews->isNotEmpty())
            <div class="reviews-list-container">
                @foreach($reviews as $rev)
                    @php
                        $userDisplayName = $rev->user ? $rev->user->displayName() : 'Anonymous';
                        $trans = $rev->translations->first();
                        $revTitle = $trans?->title ?? 'Review';
                        $revComment = $trans?->description ?? '';
                        $reply = $rev->businessReply;
                    @endphp

                    <div class="card p-3 mb-3 border rounded shadow-sm review-manage-card" id="manage-review-{{ $rev->id }}">
                        <!-- Top Header -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 font-weight-bold" style="width: 38px; height: 38px; font-size: 15px;">
                                    {{ strtoupper(substr($userDisplayName, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark" style="font-size: 14.5px;">{{ $userDisplayName }}</div>
                                    <small class="text-muted">{{ $rev->created_at ? $rev->created_at->format('M d, Y') : '' }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="rating-stars" style="color: #f59e0b;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= round($rev->rating) ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                    <span class="font-weight-bold ms-1" style="color: #0f172a; font-size: 14px;">{{ number_format($rev->rating, 1) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <h5 class="font-weight-bold mb-1" style="font-size: 16px; color: #0f172a;">{{ $revTitle }}</h5>
                        <p class="text-secondary mb-2" style="font-size: 14px; line-height: 1.5;">{!! nl2br(e($revComment)) !!}</p>

                        <!-- Pros & Cons if selected -->
                        @php
                            $pros = $rev->selectedProCons->where('type', 'pro');
                            $cons = $rev->selectedProCons->where('type', 'con');
                        @endphp
                        @if($pros->isNotEmpty() || $cons->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 my-2" style="gap: 6px;">
                                @foreach($pros as $p)
                                    <span class="badge bg-success text-white" style="font-weight: 500; font-size: 11.5px;">+ {{ $p->text }}</span>
                                @endforeach
                                @foreach($cons as $c)
                                    <span class="badge bg-danger text-white" style="font-weight: 500; font-size: 11.5px;">- {{ $c->text }}</span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Existing Reply Display -->
                        <div id="reply-container-{{ $rev->id }}">
                            @if($reply)
                                <div class="bg-light p-3 rounded mt-2 border-start border-primary border-3" style="border-left-width: 4px !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="font-weight-bold text-primary" style="font-size: 13.5px;">
                                            <i class="fas fa-reply me-1" style="transform: rotate(180deg);"></i> Official response from {{ $bizName }}
                                        </div>
                                        <small class="text-muted" style="font-size: 12px;" id="reply-date-{{ $rev->id }}">{{ $reply->updated_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-2 text-dark" style="font-size: 13.5px; line-height: 1.5;" id="reply-text-display-{{ $rev->id }}">{!! nl2br(e($reply->reply_text)) !!}</p>
                                    @if($membership->hasPermission('can_reply_reviews'))
                                        <button type="button" class="btn btn-sm btn-link text-primary p-0" onclick="toggleReplyBox({{ $rev->id }})">
                                            <i class="fas fa-edit me-1"></i> Edit Response
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Response Form (Hidden by default if reply exists) -->
                        <div id="reply-form-{{ $rev->id }}" style="{{ $reply ? 'display: none;' : '' }}" class="mt-3 pt-3 border-top">
                            @if($membership->hasPermission('can_reply_reviews'))
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label font-weight-bold mb-0" style="font-size: 13px; color: #003f7d;">
                                        <i class="fas fa-reply me-1" style="transform: rotate(180deg);"></i> Write official response as {{ $bizName }}
                                    </label>
                                    @if($reply)
                                        <button type="button" class="btn btn-sm btn-link text-muted p-0" onclick="toggleReplyBox({{ $rev->id }})">Cancel</button>
                                    @endif
                                </div>
                                <textarea id="reply-input-{{ $rev->id }}" class="form-control mb-2" rows="2" placeholder="Thank the customer or address their feedback professionally..." style="font-size: 13.5px;">{{ $reply?->reply_text ?? '' }}</textarea>
                                <div class="d-flex align-items-center justify-content-between">
                                    <small class="text-muted">Your response will appear publicly under this review on Localio.</small>
                                    <button type="button" class="btn btn-sm btn-primary px-3" onclick="submitReply({{ $rev->id }})" style="background: #003f7d; border-color: #003f7d;">
                                        <i class="fas fa-paper-plane me-1"></i> {{ $reply ? 'Update Response' : 'Publish Response' }}
                                    </button>
                                </div>
                            @else
                                <div class="text-muted" style="font-size: 13px;">
                                    <i class="fas fa-lock me-1"></i> You do not have permission to reply to reviews. Contact your team administrator.
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $reviews->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-comments fa-3x mb-3 text-secondary"></i>
                <h5>No reviews found</h5>
                <p>There are no customer reviews matching your selected filters.</p>
            </div>
        @endif

    </div>
</div>

<script>
    function toggleReplyBox(reviewId) {
        const form = document.getElementById(`reply-form-${reviewId}`);
        if (form.style.display === 'none') {
            form.style.display = 'block';
            document.getElementById(`reply-input-${reviewId}`).focus();
        } else {
            form.style.display = 'none';
        }
    }

    function submitReply(reviewId) {
        const input = document.getElementById(`reply-input-${reviewId}`);
        const replyText = input.value.trim();

        if (!replyText) {
            alert('Please enter your response before submitting.');
            input.focus();
            return;
        }

        fetch(`{{ url('/user-dashboard/business/' . $business->id . '/reviews') }}/${reviewId}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reply_text: replyText })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Error submitting response.');
                return;
            }

            // Update UI with response
            const container = document.getElementById(`reply-container-${reviewId}`);
            container.innerHTML = `
                <div class="bg-light p-3 rounded mt-2 border-start border-primary border-3" style="border-left-width: 4px !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="font-weight-bold text-primary" style="font-size: 13.5px;">
                            <i class="fas fa-reply me-1" style="transform: rotate(180deg);"></i> Official response from {{ $bizName }}
                        </div>
                        <small class="text-muted" style="font-size: 12px;">${data.date_human || 'Just now'}</small>
                    </div>
                    <p class="mb-2 text-dark" style="font-size: 13.5px; line-height: 1.5;">${data.reply_text.replace(/\n/g, '<br>')}</p>
                    <button type="button" class="btn btn-sm btn-link text-primary p-0" onclick="toggleReplyBox(${reviewId})">
                        <i class="fas fa-edit me-1"></i> Edit Response
                    </button>
                </div>
            `;
            document.getElementById(`reply-form-${reviewId}`).style.display = 'none';
        })
        .catch(() => {
            alert('A network error occurred while publishing your response.');
        });
    }
</script>
@endsection

