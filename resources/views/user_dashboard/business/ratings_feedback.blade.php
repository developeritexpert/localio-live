@section('title', 'Ratings & Feedback - Manage ' . ($business->translations->first()->name ?? 'Business') . ' | Localio')

@extends('user_dashboard_layout.master')

@section('content')
@php
    $bizName = $business->translations->first()->name ?? 'Business';
@endphp

<div class="col-lg-9 p-0">
    <div class="user_content">
        
        <!-- Header -->
        <div class="uer_nm pb-3 mb-4" style="border-bottom: 1px solid #eef2f6;">
            <h1>Ratings & Feedback</h1>
            <p class="text-muted mb-0" style="font-size: 15px;">Understand community perception, criteria breakdowns, pros & cons, and review trends for <strong>{{ $bizName }}</strong>.</p>
        </div>

        <!-- 3 Metric KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card p-3 border rounded shadow-sm text-center h-100" style="background: #f8fafc;">
                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Overall Rating</small>
                    <div class="my-2">
                        <span class="font-weight-bold" style="font-size: 38px; color: #003f7d;">{{ number_format($avgRating, 1) }}</span>
                        <span class="text-muted" style="font-size: 18px;">/ 5</span>
                    </div>
                    <div class="rating-stars mb-1" style="color: #f59e0b;">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= round($avgRating) ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                    </div>
                    <small class="text-muted">Based on {{ $totalReviews }} total reviews</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 border rounded shadow-sm text-center h-100" style="background: #f8fafc;">
                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Recommendation Rate</small>
                    <div class="my-2">
                        <span class="font-weight-bold" style="font-size: 38px; color: #10b981;">{{ $recommendRate }}%</span>
                    </div>
                    <div class="text-success mb-1">
                        <i class="fas fa-thumbs-up fa-lg"></i>
                    </div>
                    <small class="text-muted">Of reviewers recommend {{ $bizName }}</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 border rounded shadow-sm h-100" style="background: #f8fafc;">
                    <small class="text-muted text-uppercase font-weight-bold text-center d-block mb-2" style="font-size: 11px; letter-spacing: 0.5px;">Rating Breakdown</small>
                    <div class="d-flex flex-column gap-1" style="font-size: 12.5px;">
                        @for($star = 5; $star >= 1; $star--)
                            @php
                                $cnt = $ratingDistribution[$star] ?? 0;
                                $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100) : 0;
                            @endphp
                            <div class="d-flex align-items-center">
                                <span style="width: 45px;" class="text-muted">{{ $star }} star</span>
                                <div class="progress flex-grow-1 mx-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $pct }}%;"></div>
                                </div>
                                <span style="width: 30px; text-align: right;" class="text-muted">{{ $cnt }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Criterion Ratings Breakdown -->
        <div class="card p-4 mb-4 border rounded shadow-sm">
            <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 17px;">
                <i class="fas fa-sliders-h text-primary me-2"></i> Criterion Ratings Breakdown
            </h5>

            @if($criteriaRatings->isNotEmpty())
                <div class="row g-3">
                    @foreach($criteriaRatings as $crit)
                        @php
                            $pct = round(($crit->avg_score / 5) * 100);
                        @endphp
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-weight-bold text-dark" style="font-size: 14px;">{{ $crit->name }}</span>
                                    <span class="font-weight-bold text-primary" style="font-size: 15px;">{{ number_format($crit->avg_score, 1) }} / 5</span>
                                </div>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar" style="width: {{ $pct }}%; background: #003f7d;"></div>
                                </div>
                                <small class="text-muted" style="font-size: 12px;">Based on {{ $crit->total_ratings }} responses</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted text-center py-3">No criterion ratings recorded yet.</div>
            @endif
        </div>

        <!-- Section 3: Community Pros & Cons -->
        <div class="card p-4 mb-4 border rounded shadow-sm">
            <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 17px;">
                <i class="fas fa-tags text-success me-2"></i> Community Pros & Cons
            </h5>
            <p class="text-muted mb-4" style="font-size: 14px;">The most frequent strengths and weaknesses highlighted by community reviewers.</p>

            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-success mb-3">
                        <i class="fas fa-plus-circle me-1"></i> Top Community Pros
                    </h6>
                    @if($topPros->isNotEmpty())
                        <div class="d-flex flex-column gap-2">
                            @foreach($topPros as $pro)
                                <div class="p-2 px-3 rounded d-flex align-items-center justify-content-between" style="background: #f0fdf4; border-left: 3px solid #16a34a;">
                                    <span style="font-size: 13.5px; color: #166534; font-weight: 500;">{{ $pro->text }}</span>
                                    <span class="badge bg-success rounded-pill">{{ $pro->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <small class="text-muted">No pros recorded yet.</small>
                    @endif
                </div>

                <div class="col-md-6">
                    <h6 class="font-weight-bold text-danger mb-3">
                        <i class="fas fa-minus-circle me-1"></i> Top Community Cons
                    </h6>
                    @if($topCons->isNotEmpty())
                        <div class="d-flex flex-column gap-2">
                            @foreach($topCons as $con)
                                <div class="p-2 px-3 rounded d-flex align-items-center justify-content-between" style="background: #fef2f2; border-left: 3px solid #dc2626;">
                                    <span style="font-size: 13.5px; color: #991b1b; font-weight: 500;">{{ $con->text }}</span>
                                    <span class="badge bg-danger rounded-pill">{{ $con->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <small class="text-muted">No cons recorded yet.</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 4: Rating / Review Trends -->
        <div class="card p-4 border rounded shadow-sm">
            <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 17px;">
                <i class="fas fa-chart-line text-info me-2"></i> Review Volume & Rating Trends (Past 6 Months)
            </h5>

            @if($trends->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center" style="font-size: 13.5px;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Month</th>
                                <th>New Reviews</th>
                                <th>Monthly Average Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trends as $tr)
                                <tr>
                                    <td class="text-start font-weight-bold text-dark">{{ $tr->month }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-3 py-1 font-weight-bold">{{ $tr->count }}</span>
                                    </td>
                                    <td>
                                        <span class="text-warning"><i class="fas fa-star me-1"></i></span>
                                        <strong>{{ number_format($tr->avg_rating, 1) }}</strong> / 5
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-3 text-muted">Insufficient historical data to show trends.</div>
            @endif
        </div>

    </div>
</div>
@endsection

