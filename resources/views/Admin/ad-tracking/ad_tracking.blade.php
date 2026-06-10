@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg affiliate-tracking-stats">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Affiliate Tracking Stats</h3>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-gs">
        <div class="col-md-3">
            <div class="card card-bordered text-center">
                <div class="card-inner">
                    <h4>{{ number_format($stats['total_clicks']) }}</h4>
                    <p>Total Clicks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-bordered text-center">
                <div class="card-inner">
                    <h4>{{ number_format($stats['total_conversions']) }}</h4>
                    <p>Conversions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-bordered text-center">
                <div class="card-inner">
                    <h4>${{ number_format($stats['total_revenue'], 2) }}</h4>
                    <p>Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-bordered text-center">
                <div class="card-inner">
                    <h4>{{ $stats['conversion_rate'] }}%</h4>
                    <p>Conversion Rate</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Conversions Table --}}
    <div class="card card-bordered card-preview mt-4">
        <div class="card-inner">
            <div class="card-title-group">
                <div class="card-title">
                    <h6 class="title">Recent Conversions</h6>
                </div>
            </div>

            <div class="table-responsive">
                <table class="datatable-init table nowrap">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">Date</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Business</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Revenue</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_conversions as $click)
                        <tr class="nk-tb-item">
                            <td class="nk-tb-col">{{ $click->converted_at->format('M j, Y') }}</td>
                            <td class="nk-tb-col">{{ $click->business->translations->first()->name ?? 'N/A' }}</td>
                            <td class="nk-tb-col">${{ number_format($click->revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr class="nk-tb-item">
                            <td class="nk-tb-col text-center" colspan="3">No conversions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
