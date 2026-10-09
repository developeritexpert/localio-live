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
                        <span class="badge bg-danger ms-2">{{ $reviews->total() }}</span>
                    @endif
                </h3>
                <p class="text-soft">Reviews requiring human moderation. Ordered by Priority (Risk Level) and Age.</p>
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
                <p class="text-soft">No reviews are currently pending human moderation.</p>
            </div>
        </div>
    @else
        @foreach($reviews as $review)
        @php
            $translation   = $review->translations->first();
            $bizName       = $review->business?->translations?->first()?->name ?? $review->business?->name ?? 'Unknown Business';
            $riskLevel     = $review->localio_ai_risk_level ?? null;
            $riskClass     = match($riskLevel) {
                'CRITICAL' => 'danger',
                'HIGH'     => 'warning',
                'MEDIUM'   => 'info',
                'LOW'      => 'secondary',
                default    => 'light',
            };
            $escalationLabel = match($review->escalation_source) {
                'laravel_rule', 'laravel'          => ['label' => 'Laravel Rule',       'color' => 'warning'],
                'openai_moderation', 'moderation_api' => ['label' => 'OpenAI Moderation',  'color' => 'danger'],
                'localio_ai'                       => ['label' => 'Localio AI',         'color' => 'primary'],
                'user_report'                      => ['label' => 'User Report',        'color' => 'info'],
                'business_report'                  => ['label' => 'Business Report',    'color' => 'info'],
                'manual'                           => ['label' => 'Manual',             'color' => 'secondary'],
                default                            => ['label' => 'Unknown',            'color' => 'light'],
            };
        @endphp

        <div class="card card-bordered mb-4 border-start border-4 border-{{ $riskClass }}" id="review-{{ $review->id }}">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap py-3 bg-white">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-{{ $riskClass }} text-uppercase">
                        {{ $riskLevel ? $riskLevel . ' RISK' : 'RISK UNKNOWN' }}
                    </span>
                    <span class="badge bg-outline-{{ $escalationLabel['color'] }}">
                        Escalated by: {{ $escalationLabel['label'] }}
                    </span>
                    <span class="text-soft small">Review #{{ $review->id }} &middot; Submitted {{ $review->created_at?->diffForHumans() }}</span>
                </div>
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    {{-- APPROVE FORM (handles verified locale if needed) --}}
                    @if($review->locale_status === 'HUMAN_REVIEW')
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveLocaleModal{{ $review->id }}">
                            <em class="icon ni ni-check"></em> Assign Locale & Approve
                        </button>
                    @else
                        <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve and publish this review?')">
                                <em class="icon ni ni-check"></em> Approve & Publish
                            </button>
                        </form>
                    @endif

                    {{-- REJECT (for newly submitted reviews) --}}
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $review->id }}">
                        <em class="icon ni ni-cross"></em> Reject
                    </button>

                    {{-- REMOVE (if ever published) --}}
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeModal{{ $review->id }}">
                        <em class="icon ni ni-trash"></em> Remove
                    </button>
                </div>
            </div>

            <div class="card-inner">
                {{-- SECTION 1: Review Content --}}
                <div class="mb-4">
                    <h6 class="overline-title text-primary mb-3"><em class="icon ni ni-article me-1"></em> Section 1 � Review Content & Author Info</h6>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="p-3 bg-light rounded">
                                <table class="table table-sm table-borderless mb-0 fs-12px">
                                    <tr><th class="text-soft" style="width:130px">Business</th><td><strong>{{ $bizName }}</strong></td></tr>
                                    <tr>
                                        <th class="text-soft">Author</th>
                                        <td>
                                            <strong>{{ $review->user?->first_name }} {{ $review->user?->last_name }}</strong><br>
                                            <span class="text-soft">{{ $review->user?->email }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-soft">Account Age</th>
                                        <td>{{ $review->user ? now()->diffInDays($review->user->created_at) . ' days' : 'N/A' }}</td>
                                    </tr>
                                    <tr><th class="text-soft">Star Rating</th><td><span class="text-warning fw-bold">? {{ number_format($review->rating, 1) }}</span> / 5</td></tr>
                                    <tr><th class="text-soft">Recommend?</th><td>{{ $review->recommend ? 'Yes' : 'No' }}</td></tr>
                                    <tr><th class="text-soft">Submitted Locale</th><td><code>{{ $review->submitted_locale ?? 'N/A' }}</code></td></tr>
                                    <tr><th class="text-soft">Reviewer IP</th><td><code>{{ $review->ip_address ?? 'Not recorded' }}</code></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="p-3 bg-lighter rounded h-100 border">
                                <h6 class="fw-bold text-dark mb-2">{{ $translation?->title ?: 'No title submitted' }}</h6>
                                <p class="text-dark mb-0 fs-13px" style="white-space: pre-wrap;">{{ $translation?->description ?: 'No text submitted' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="divider">

                {{-- SECTION 2: Automated Checks Breakdown --}}
                <div class="mb-4">
                    <h6 class="overline-title text-primary mb-3"><em class="icon ni ni-cpu me-1"></em> Section 2 � Automated Moderation Audit</h6>
                    <div class="row g-3">
                        {{-- Layer 1 --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold small text-uppercase text-muted">Layer 1: Laravel Rules</span>
                                    @if($review->laravel_check_status === 'pass')
                                        <span class="badge bg-success">PASS</span>
                                    @elseif($review->laravel_check_status === 'fail')
                                        <span class="badge bg-danger">FAIL</span>
                                    @else
                                        <span class="badge bg-secondary">NOT RUN</span>
                                    @endif
                                </div>
                                @if($review->laravel_check_status === 'fail')
                                    <div class="alert alert-danger p-2 mb-0 fs-12px">
                                        <strong>Trigger:</strong> {{ str_replace('_', ' ', $review->laravel_fail_reason ?? 'hard rule violation') }}
                                    </div>
                                @else
                                    <p class="text-soft fs-12px mb-0">Deterministic rules: length, duplicates, frequency, links, and conflict-of-interest passed.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Layer 2 --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold small text-uppercase text-muted">Layer 2: Moderation API</span>
                                    @if($review->moderation_api_passed === true)
                                        <span class="badge bg-success">PASS</span>
                                    @elseif($review->moderation_api_passed === false)
                                        <span class="badge bg-danger">FLAGGED</span>
                                    @else
                                        <span class="badge bg-secondary">SKIPPED</span>
                                    @endif
                                </div>
                                <div class="fs-12px">
                                    <span class="text-soft">Model:</span> <code>omni-moderation-latest</code> (free)
                                </div>
                                @if($review->moderation_api_passed === false && !empty($review->moderation_api_result))
                                    @php
                                        $cats = collect($review->moderation_api_result['categories'] ?? [])->filter()->keys()->toArray();
                                    @endphp
                                    <div class="alert alert-danger p-2 mt-2 mb-0 fs-12px">
                                        <strong>Categories:</strong> {{ implode(', ', $cats) ?: 'Content policy violation' }}
                                    </div>
                                @elseif($review->moderation_api_passed === true)
                                    <p class="text-soft fs-12px mt-2 mb-0">No harmful safety violations detected (harassment, hate, sexual, violence clean).</p>
                                @endif
                            </div>
                        </div>

                        {{-- Layer 3 --}}
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold small text-uppercase text-muted">Layer 3: Localio Luna AI</span>
                                    @if($review->localio_ai_decision === 'PASS')
                                        <span class="badge bg-success">PASS</span>
                                    @elseif($review->localio_ai_decision === 'HUMAN_REVIEW')
                                        <span class="badge bg-danger">HUMAN REVIEW</span>
                                    @elseif($review->localio_ai_status === 'skipped')
                                        <span class="badge bg-secondary">SKIPPED</span>
                                    @elseif($review->localio_ai_status === 'processing')
                                        <span class="badge bg-info">BATCH IN PROGRESS</span>
                                    @else
                                        <span class="badge bg-secondary">NOT RUN</span>
                                    @endif
                                </div>
                                <div class="fs-12px mb-1">
                                    <span class="text-soft">Model:</span> <code>{{ $review->localio_ai_model ?: 'gpt-5.6-luna' }}</code>
                                </div>
                                @if($review->localio_ai_decision)
                                    @if(!empty($review->localio_ai_flags))
                                        @php
                                            $flags = is_array($review->localio_ai_flags) ? $review->localio_ai_flags : json_decode($review->localio_ai_flags, true);
                                        @endphp
                                        <div class="mt-1">
                                            @foreach($flags as $flag)
                                                <span class="badge bg-warning text-dark me-1 mb-1 fs-11px">{{ str_replace('_', ' ', $flag) }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($review->localio_ai_confidence)
                                        <div class="fs-12px text-soft mt-1">Confidence: <strong>{{ $review->localio_ai_confidence }}%</strong></div>
                                    @endif
                                    @if($review->localio_ai_reason)
                                        <div class="alert alert-warning p-2 mt-2 mb-2 fs-12px fst-italic">
                                            "{{ $review->localio_ai_reason }}"
                                        </div>
                                    @endif
                                    <div class="p-2 bg-light rounded fs-11px mt-2">
                                        <div>Submitted locale: <code>{{ $review->submitted_locale ?: 'N/A' }}</code></div>
                                        <div>Detected locale: <code>{{ $review->source_locale ?: 'Unknown' }}</code></div>
                                        <div>Locale status:
                                            <span class="badge bg-{{ $review->locale_status === 'MATCH' ? 'success' : ($review->locale_status === 'REASSIGNED' ? 'info' : 'warning') }} fs-10px">
                                                {{ $review->locale_status ?: 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: Moderation Audit Log --}}
                @if($review->moderationLogs->isNotEmpty())
                <hr class="divider">
                <div>
                    <h6 class="overline-title text-primary mb-2"><em class="icon ni ni-history me-1"></em> Section 3 � Audit History Log</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped fs-12px mb-0">
                            <thead>
                                <tr>
                                    <th>Layer</th>
                                    <th>Action</th>
                                    <th>Transition</th>
                                    <th>Actor</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($review->moderationLogs->take(6) as $log)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ strtoupper($log->layer ?? 'system') }}</span></td>
                                    <td><code>{{ $log->action }}</code></td>
                                    <td>
                                        @if($log->from_status || $log->to_status)
                                            <span class="badge bg-light text-dark">{{ $log->from_status ?? 'start' }}</span> ? <span class="badge bg-light text-dark">{{ $log->to_status }}</span>
                                        @else
                                            �
                                        @endif
                                    </td>
                                    <td>{{ $log->actor ? $log->actor->first_name : ucfirst($log->actor_type) }}</td>
                                    <td class="text-soft">{{ $log->created_at?->format('M d, H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Approve with Locale Assignment Modal --}}
        @if($review->locale_status === 'HUMAN_REVIEW')
        <div class="modal fade" id="approveLocaleModal{{ $review->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-success"><em class="icon ni ni-check-circle me-1"></em> Assign Locale & Approve Review #{{ $review->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-soft mb-3">AI flagged this review for <strong>HUMAN_REVIEW on locale</strong>. Please confirm the correct Localio locale before publishing.</p>
                            <div class="mb-3">
                                <label class="form-label">Supported Localio Locale <span class="text-danger">*</span></label>
                                <select name="assigned_locale" class="form-select" required>
                                    @foreach($supportedLocales as $loc)
                                        <option value="{{ $loc }}" {{ $review->submitted_locale === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Moderator Notes <span class="text-soft">(optional)</span></label>
                                <input type="text" name="notes" class="form-control" placeholder="e.g. Verified German language content">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Assign Locale & Publish</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectModal{{ $review->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.reviews.reject', $review->id) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger"><em class="icon ni ni-cross-circle me-1"></em> Reject Newly Submitted Review #{{ $review->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-soft mb-2">This review was never published. It will be permanently marked as <strong>REJECTED</strong> and kept in the internal audit trail.</p>
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required placeholder="State reason why this review violates guidelines..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Remove Modal --}}
        <div class="modal fade" id="removeModal{{ $review->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.reviews.remove', $review->id) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger"><em class="icon ni ni-trash me-1"></em> Remove Review #{{ $review->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-soft mb-2">This review will be marked as <strong>REMOVED</strong> from public visibility while preserving complete internal history.</p>
                            <div class="mb-3">
                                <label class="form-label">Removal Reason <span class="text-danger">*</span></label>
                                <textarea name="removal_reason" class="form-control" rows="3" required placeholder="State removal justification..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Removal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        @if($reviews->hasPages())
        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
