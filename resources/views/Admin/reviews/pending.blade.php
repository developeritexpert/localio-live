@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">
                    <em class="icon ni ni-shield-alert text-warning me-2"></em>
                    Pending Review Queue
                    @if($reviews->isNotEmpty())
                        <span class="badge bg-danger ms-2">{{ $reviews->count() }}</span>
                    @endif
                </h3>
                <p class="text-soft">Reviews requiring human moderation. Sorted by AI risk level and age.</p>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('reviews') }}" class="btn btn-outline-secondary btn-sm">
                    <em class="icon ni ni-arrow-left"></em> All Reviews
                </a>
            </div>
        </div>
    </div>

    @if($reviews->isEmpty())
        <div class="card card-bordered">
            <div class="card-inner text-center py-5">
                <em class="icon ni ni-check-circle-cut" style="font-size: 3rem; color: #1ee0ac;"></em>
                <h5 class="mt-3 text-success">Queue is clear!</h5>
                <p class="text-soft">No reviews are pending human moderation.</p>
            </div>
        </div>
    @else
        @foreach($reviews as $review)
        @php
            $translation   = $review->translations->first();
            $bizName       = $review->business?->translations?->first()?->name ?? 'Unknown Business';
            $riskLevel     = $review->localio_ai_risk_level ?? null;
            $riskClass     = match($riskLevel) {
                'CRITICAL' => 'danger',
                'HIGH'     => 'warning',
                'MEDIUM'   => 'info',
                'LOW'      => 'secondary',
                default    => 'light',
            };
            $escalationLabel = match($review->escalation_source) {
                'laravel'          => ['label' => 'Laravel Rule',       'color' => 'warning'],
                'moderation_api'   => ['label' => 'OpenAI Moderation',  'color' => 'danger'],
                'localio_ai'       => ['label' => 'Localio AI',         'color' => 'primary'],
                'user_report'      => ['label' => 'User Report',        'color' => 'info'],
                'business_report'  => ['label' => 'Business Report',    'color' => 'info'],
                'manual'           => ['label' => 'Manual',             'color' => 'secondary'],
                default            => ['label' => 'Unknown',            'color' => 'light'],
            };
        @endphp

        <div class="card card-bordered mb-4" id="review-{{ $review->id }}">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-{{ $riskClass }}">
                        {{ $riskLevel ? $riskLevel . ' RISK' : 'RISK UNKNOWN' }}
                    </span>
                    <span class="badge bg-{{ $escalationLabel['color'] }}">
                        Escalated by: {{ $escalationLabel['label'] }}
                    </span>
                    <span class="text-soft small">Review #{{ $review->id }} &middot; {{ $review->created_at?->diffForHumans() }}</span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    {{-- APPROVE --}}
                    <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve and publish this review?')">
                            <em class="icon ni ni-check"></em> Approve
                        </button>
                    </form>
                    {{-- REJECT --}}
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $review->id }}">
                        <em class="icon ni ni-cross"></em> Reject
                    </button>
                    {{-- REMOVE (only if was published before) --}}
                    @if(in_array($review->status, ['removed', 'published']))
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeModal{{ $review->id }}">
                        <em class="icon ni ni-trash"></em> Remove
                    </button>
                    @endif
                </div>
            </div>

            <div class="card-inner">
                {{-- SECTION 1: Review Content --}}
                <div class="mb-4">
                    <h6 class="overline-title text-primary mb-3">📄 Review Content</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th class="text-soft" style="width:140px">Business</th><td><strong>{{ $bizName }}</strong></td></tr>
                                <tr><th class="text-soft">User</th><td>{{ $review->user?->first_name }} {{ $review->user?->last_name }} <span class="text-soft">({{ $review->user?->email }})</span></td></tr>
                                <tr><th class="text-soft">Rating</th><td>{{ $review->rating }} / 5</td></tr>
                                <tr><th class="text-soft">Recommend</th><td>{{ $review->recommend ? 'Yes' : 'No' }}</td></tr>
                                <tr><th class="text-soft">Submitted</th><td>{{ $review->created_at?->format('d M Y, H:i') }}</td></tr>
                                <tr><th class="text-soft">Locale</th><td>{{ $review->submitted_locale ?? 'N/A' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-lighter rounded">
                                <p class="fw-bold mb-1">{{ $translation?->title ?? 'No title' }}</p>
                                <p class="mb-0 text-soft">{{ $translation?->description ?? 'No content' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="divider">

                {{-- SECTION 2: Automated Check Results --}}
                <div class="mb-4">
                    <h6 class="overline-title text-primary mb-3">🤖 Automated Check Results</h6>
                    <div class="row g-3">
                        {{-- Layer 1 --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-bold mb-2 small text-uppercase text-muted">Layer 1 — Laravel Rules</p>
                                @if($review->laravel_check_status === 'pass')
                                    <span class="badge bg-success">PASS</span>
                                @elseif($review->laravel_check_status === 'fail')
                                    <span class="badge bg-danger">FAIL</span>
                                    <p class="mt-2 mb-0 small text-soft">Reason: <strong>{{ str_replace('_', ' ', $review->laravel_fail_reason ?? 'unknown') }}</strong></p>
                                @else
                                    <span class="badge bg-secondary">NOT RUN</span>
                                @endif
                            </div>
                        </div>

                        {{-- Layer 2 --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-bold mb-2 small text-uppercase text-muted">Layer 2 — OpenAI Moderation</p>
                                @if($review->moderation_api_passed === true)
                                    <span class="badge bg-success">PASS</span>
                                @elseif($review->moderation_api_passed === false)
                                    <span class="badge bg-danger">FLAGGED</span>
                                    @if(!empty($review->moderation_api_result))
                                        @php $cats = collect($review->moderation_api_result['categories'] ?? [])->filter()->keys()->implode(', ') @endphp
                                        @if($cats)
                                        <p class="mt-2 mb-0 small text-soft">Categories: <strong>{{ $cats }}</strong></p>
                                        @endif
                                    @endif
                                @else
                                    <span class="badge bg-secondary">NOT RUN</span>
                                @endif
                            </div>
                        </div>

                        {{-- Layer 3 --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-bold mb-2 small text-uppercase text-muted">Layer 3 — Localio AI (GPT)</p>
                                @if($review->localio_ai_decision)
                                    <span class="badge bg-{{ $review->localio_ai_decision === 'PASS' ? 'success' : 'danger' }}">
                                        {{ $review->localio_ai_decision }}
                                    </span>
                                    @if($review->localio_ai_flags && count($review->localio_ai_flags))
                                        <p class="mt-2 mb-1 small text-soft">Flags:</p>
                                        @foreach($review->localio_ai_flags as $flag)
                                            <span class="badge bg-warning text-dark me-1 mb-1">{{ str_replace('_', ' ', $flag) }}</span>
                                        @endforeach
                                    @endif
                                    @if($review->localio_ai_confidence)
                                        <p class="mt-2 mb-1 small text-soft">Confidence: <strong>{{ $review->localio_ai_confidence }}%</strong></p>
                                    @endif
                                    @if($review->localio_ai_reason)
                                        <p class="mt-1 mb-0 small text-soft fst-italic">"{{ $review->localio_ai_reason }}"</p>
                                    @endif
                                    <div class="mt-2 pt-2 border-top">
                                        <p class="small mb-0 text-soft">
                                            Submitted locale: <strong>{{ $review->submitted_locale ?? 'N/A' }}</strong><br>
                                            Detected locale: <strong>{{ $review->source_locale ?? 'N/A' }}</strong><br>
                                            Locale status: 
                                            <span class="badge bg-{{ $review->locale_status === 'MATCH' ? 'success' : ($review->locale_status === 'REASSIGNED' ? 'info' : 'warning') }}">
                                                {{ $review->locale_status ?? 'N/A' }}
                                            </span>
                                        </p>
                                    </div>
                                @elseif($review->localio_ai_status === 'skipped')
                                    <span class="badge bg-secondary">SKIPPED</span>
                                    <p class="mt-2 mb-0 small text-soft">Skipped due to earlier escalation.</p>
                                @elseif($review->localio_ai_status === 'processing')
                                    <span class="badge bg-info">BATCH PROCESSING</span>
                                @else
                                    <span class="badge bg-secondary">NOT RUN</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Moderation Log --}}
                @if($review->moderationLogs->isNotEmpty())
                <hr class="divider">
                <div>
                    <h6 class="overline-title text-primary mb-3">📋 Moderation History</h6>
                    <ul class="list-group list-group-flush">
                        @foreach($review->moderationLogs->take(5) as $log)
                        <li class="list-group-item px-0 py-2">
                            <span class="badge bg-secondary me-2">{{ strtoupper(str_replace('_', ' ', $log->layer ?? 'system')) }}</span>
                            <strong>{{ str_replace('_', ' ', $log->action) }}</strong>
                            @if($log->from_status && $log->to_status)
                                <span class="text-soft"> · {{ $log->from_status }} → {{ $log->to_status }}</span>
                            @endif
                            <span class="text-soft ms-2 small">{{ $log->created_at?->diffForHumans() }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.reviews.reject', $review->id) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Review #{{ $review->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">Reason <span class="text-soft">(optional — sent to reviewer)</span></label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Content violates community guidelines."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Remove Modal --}}
        <div class="modal fade" id="removeModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.reviews.remove', $review->id) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Remove Review #{{ $review->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-soft">This will remove the review from public display. The full record is kept internally.</p>
                            <div class="form-group">
                                <label class="form-label">Reason <span class="text-soft">(optional)</span></label>
                                <textarea name="reason" class="form-control" rows="2" placeholder="e.g. Reported for harassment."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Remove</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @endforeach
    @endif
</div>
@endsection
