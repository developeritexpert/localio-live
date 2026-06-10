@extends('vendor_dashboard_layout.master')

@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>Profile Views Overview</h1>
       </div>
       <div class="mi_detail">
        <div class="review_sys">


        {{-- Filter --}}
        {{-- <div class="mb-3">
            <select class="form-select w-auto" onchange="filterByMonth(this.value)">
                <option value="current">This Month</option>
                <option value="last">Last Month</option>
                <option value="all">All Time</option>
            </select>
        </div> --}}

        {{-- Summary --}}
        <div class="row g-3 mb-4 text-center">
            <div class="col-md-4">
                <div class="p-4 border rounded bg-light h-100 d-flex flex-column justify-content-center align-items-center">
                    <h1 class="fs-2 mb-1" id="totalVisits">{{ $totalVisits }}</h1>
                    <p class="mb-0 text-muted">Total Visits</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 border rounded bg-light h-100 d-flex flex-column justify-content-center align-items-center">
                    <h1 class="fs-2 mb-1" id="totalEngagements">{{ $totalEngagements }}</h1>
                    <p class="mb-0 text-muted">Engagements</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 border rounded bg-light h-100 d-flex flex-column justify-content-center align-items-center">
                    <h1 class="fs-2 mb-1" id="avgDuration">{{ $avgDuration }}min</h1>
                    <p class="mb-0 text-muted">Avg. Duration</p>
                </div>
            </div>
        </div>
        
        
        

        {{-- Recent Activities --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">Anonymous Activity</h5>
        
                <!-- Totals Section -->
                <div class="row text-center mb-4">
                    <div class="col">
                        <div class="fw-bold fs-4">{{ $anonymousViews + $anonymousEngagements }}</div>
                        <div class="text-muted small">Total Anonymous Interactions</div>
                    </div>
                    <div class="col">
                        <div class="fw-bold fs-4">{{ $anonymousEngagements }}</div>
                        <div class="text-muted small">Total Engagements</div>
                    </div>
                </div>
        
                <!-- Anonymous Breakdown -->
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            Anonymous Users (Views)
                        </div>
                        <span class="badge bg-primary">{{ $anonymousViews }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            Anonymous Users (Engagements)
                        </div>
                        <span class="badge bg-success">{{ $anonymousEngagements }}</span>
                    </li>
                </ul>
            </div>
        </div>
        
        
        
        
    </div>

        </div>
    </div>
</div>
@endsection