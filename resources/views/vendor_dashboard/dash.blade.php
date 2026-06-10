@extends('vendor_dashboard_layout.master')

@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>{{ __('file.overview') }}</h1>
       </div>
       <div class="mi_detail">
          <div class="row gy-4">
            <div class="overview_list">
               <div class="overlist_box">
                   <h6>{{ __('file.total-listing') }}</h6>
                   <div class="overlist_value">
                       <span class="overlist_sp">
                           {{$business->products->count()}}
                       </span>
                       <span class="overlist_sp">
                           <a class="overlst_an" href="{{ route('vendor-total-product') }}">
                               {{ __('file.view-all') }}
                               <img src="{{asset('vender_dashboard/img/arrow-rgt.svg')}}" alt="">
                           </a>
                       </span>
                   </div>
               </div>
           
               <div class="overlist_box">
                   <h6>{{ __('file.profile-views') }}</h6>
                   <div class="overlist_value">
                       <span class="overlist_sp">
                           {{$user_activity->count()}}
                       </span>
                       <span class="overlist_sp">
                           <a class="overlst_an" href="{{ route('vendor-business-view') }}">
                               {{ __('file.view-all') }}
                               <img src="{{asset('vender_dashboard/img/arrow-rgt.svg')}}" alt="">
                           </a>
                       </span>
                   </div>
               </div>
           
               <div class="overlist_box">
                   <h6>{{ __('file.review') }}</h6>
                   <div class="overlist_value">
                       <span class="overlist_sp">
                           {{$business->reviews->count()}}
                       </span>
                       <span class="overlist_sp">
                           <a class="overlst_an" href="{{ route('vendor-review') }}">
                               {{ __('file.view-all') }}
                               <img src="{{asset('vender_dashboard/img/arrow-rgt.svg')}}" alt="">
                           </a>
                       </span>
                   </div>
               </div>
           </div>
           
          </div>
       </div>

       <div class="prf_revbox">
          <div class="row">
             <div class="col-lg-9">
                <div class="profile_viewbox">
                   <div class="row">

                      <div class="col-lg-9">
                        <div class="chart-container">
                           <canvas id="profileViewsChart"></canvas>
                       </div>                       
                      </div>

                      <div class="col-lg-3">
                        <div class="prof_rgt">
                           <div class="totl_viewbox">
                              <div class="hd_box">
                                 <h6>  {{ __('file.Total_Views') }}</h6>
                              </div>
                              <div class="custom-select">
                                 <div class="select-box">
                                    <span class="selected">{{ __('file.this-month') }}</span>
                                    <i class="fa-solid fa-chevron-down"></i>
                                 </div>
                                 <div class="options">
                                  
                                       <div data-value="this">{{ __('file.this-month') }}</div>
                                       <div data-value="last">{{ __('file.last-month') }}</div>
                                    
                                 </div>
                              </div>
                           </div>
                           <div class="profile_view">
                              <div class="prof_hd">
                                 <h6>
                                   {{ __('file.profile-views') }}
                                 </h6>
                                 <div class="prof_vlu">
                                    <span class="prof_spvlu" id="profile-views-count">{{ $thisMonthViewsTotal }}</span>

                                 </div>
                              </div>
                              <div class="prof_hd">
                                 <h6>
                                   {{ __('file.Engagement_Metrics') }}
                                 </h6>
                                 <div class="prof_vlu">
                                    <span class="prof_spvlu" id="engagement-count">{{ $thisMonthEngagement }}</span>
                                 </div>
                               
                              </div>
                              {{-- <div class="prof_hd">
                                 <h6>
                                   {{ __('file.Conversion_Tracking') }}
                                 </h6>
                                 <div class="prof_vlu">
                                    <span class="prof_spvlu">
                                       700
                                    </span>
                                 </div>
                                 <div class="vlue_compare">
                                   {{ __('file.Compared_to') }} (<span class="plse_vlue">+10.26%</span>)
                                 </div>
                              </div> --}}
                           </div>

                        </div>
                       
                      </div>
                   </div>
                </div>
                <div class="col-lg-3">
                   <div class="Recent_rwbox">

                   </div>
                </div>
             </div>
             <div class="col-lg-3">
                <div class="recent_reviw">
                   <div class="revire_hd">
                      <h5>
                        {{ __('file.Recent_Reviews') }}
                         </h6>
                   </div>
                   <div class="review_scroll">

                     @foreach ($business->reviews as $review)
                      <div class="review_box">
                         <div class="rewbox_hd">
                            <div class="rew_img">
                               <img src="{{asset($review->business->icon_id ?? 'default.jpg')}}" alt="image">
                            </div>
                            <div class="rew_content">
                               <h6>
                                 {{ $review->business->translations->first()->name ?? 'Business Name' }}
                               </h6>
                               <div class="rew_str">
                                  {{ number_format($review->rating, 1)  }} <ul>


                                    <li>
                                       <div class="rating-container">
                                          @for($i=1;$i<=5;$i++)
                                          @if ($i <=floor($review->rating))
                                          <i class="fas fa-star text-warning"></i>
                                          @elseif ($i - 0.5 <= $review->rating)
                                             <i class="fas fa-star-half-alt text-warning"></i>
                                          @else
                                             <i class="far fa-star text-warning"></i>
                                          @endif
                                 
                              
                                          @endfor
                                       </div>
                                    </li>
                                  </ul>
                               </div>
                            </div>
                         </div>
                         <div class="rew_para">
                            <h5 class="impres_hd">
                              {{ $review->translations->first()->title ?? 'Review Title' }}
                            </h5>
                            <p class="imp_para">
                              {{ $review->translations->first()->description ?? 'Review description' }}
                            </p>
                         </div>
                      </div>
                      @endforeach

                </div>
             </div>
          </div>
       </div>
    </div>
 </div>
 @endsection

 @push('scripts')


<script>
   const chartStats = {
       this: {
           views: @json($thisMonthData),
           totalViews: {{ $thisMonthViewsTotal }},
           engagement: {{ $thisMonthEngagement }}
       },
       last: {
           views: @json($lastMonthData),
           totalViews: {{ $lastMonthViewsTotal }},
           engagement: {{ $lastMonthEngagement }}
       }
   };
</script>


<script>
   const ctx = document.getElementById('profileViewsChart').getContext('2d');

   // Both datasets
   const chartConfig = {
       type: 'line',
       data: {
           labels: [...Array(30).keys()].map(i => i + 1),
           datasets: [
               {
                   label: 'This Month',
                   data: chartStats.this.views,
                   borderColor: '#1B4FDE',
                   backgroundColor: 'rgba(27, 79, 222, 0.1)',
                   fill: true,
                   tension: 0.4,
                   pointBackgroundColor: '#1B4FDE',
                   hidden: false, // Show by default
               },
               {
                   label: 'Last Month',
                   data: chartStats.last.views,
                   borderColor: '#F35C2C',
                   backgroundColor: 'transparent',
                   fill: false,
                   tension: 0.4,
                   pointBackgroundColor: '#F35C2C',
                   hidden: true, // Hide by default
               }
           ]
       },
       options: {
           responsive: true,
           scales: {
               y: {
                   beginAtZero: true,
                   ticks: {
                       stepSize: 20
                   }
               }
           },
           plugins: {
               legend: {
                   display: true,
                   position: 'bottom',
                   labels: {
                       boxWidth: 10,
                       usePointStyle: true
                   }
               }
           }
       }
   };

   const chart = new Chart(ctx, chartConfig);

   // Handle dropdown selection
   document.querySelectorAll('.options div').forEach(option => {
       option.addEventListener('click', function () {
           const selected = this.getAttribute('data-value'); // 'this' or 'last'

           // Toggle visibility of datasets
           chart.data.datasets[0].hidden = selected !== 'this'; // Blue
           chart.data.datasets[1].hidden = selected !== 'last'; // Orange
           chart.update();

           // Update stats
           document.getElementById('profile-views-count').textContent = chartStats[selected].totalViews;
           document.getElementById('engagement-count').textContent = chartStats[selected].engagement;

           // Update dropdown label
           document.querySelector('.selected').textContent = this.textContent;
       });
   });
</script>






 @endpush
 