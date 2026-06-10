@extends('vendor_dashboard_layout.master')
@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>Analytics & Reports</h1>
       </div>
       <div class="mi_detail">
         <!-- Summary Cards -->
         <div class="row gy-4 mb-4">
             <div class="overview_list overview_list_2">
                <div class="overlist_box views">
                    <h6><i class="fas fa-eye me-2"></i>Total Views</h6>
                    <div class="overlist_value">
                        <span class="overlist_sp">{{ number_format($totalViews) }}</span>
                    </div>
                </div>
                
                <div class="overlist_box clicks">
                    <h6><i class="fas fa-mouse-pointer me-2"></i>Total Clicks</h6>
                    <div class="overlist_value">
                        <span class="overlist_sp">{{ number_format($totalClicks) }}</span>
                    </div>
                </div>
                
                <div class="overlist_box engagement">
                    <h6><i class="fas fa-heart me-2"></i>Wishlist</h6>
                    <div class="overlist_value">
                        <span class="overlist_sp">{{ number_format($wishlist) }}</span>
                    </div>
                </div>
                
               
             </div>
         </div>
         


        
         <div class="row">
            <!-- Monthly Trends Line Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Monthly Engagement Trends</h5>
                        <div id="monthlyTrends"></div>
                    </div>
                </div>
            </div>
        
            <!-- Engagement by Country Pie Chart -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Engagement by Country</h5>
                        <div id="countryEngagement"></div>
                    </div>
                </div>
            </div>
        </div>
        
         
     </div>
 </div>
</div>
</div>
</div>

    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Injected from Laravel controller
    const countryData = @json($countryEngagements);

    // Get labels and values from associative array
    const countryLabels = Object.keys(countryData);
    const countryEngagements = Object.values(countryData);

    // Monthly Trends Line Chart
    const monthlyViews = @json($monthlyViews);
    const monthlyClicks = @json($monthlyClicks);

    var monthlyTrends = new ApexCharts(document.querySelector("#monthlyTrends"), {
        chart: { type: 'area', height: 300 },
        colors: ['#1B4FDE', '#F35C2C'],
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        series: [
            { name: "Views", data: monthlyViews },
            { name: "Clicks", data: monthlyClicks }
        ],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        tooltip: {
            theme: 'dark',
            y: { formatter: val => val.toLocaleString() }
        }
    });
    monthlyTrends.render();

    // Engagement by Country Pie Chart
    var countryEngagement = new ApexCharts(document.querySelector("#countryEngagement"), {
        chart: { type: 'donut', height: 300 },
        labels: countryLabels,
        series: countryEngagements,
        colors: ['#1B4FDE', '#F35C2C', '#4facfe', '#43e97b', '#ffc107', '#6c757d'],
        legend: { position: 'bottom' },
        tooltip: {
            y: {
                formatter: function (value, opts) {
                    const total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    return `${value.toLocaleString()} (${percentage}%)`;
                }
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                            }
                        }
                    }
                }
            }
        }
    });
    countryEngagement.render();
</script>


@endpush
