@extends('vendor_dashboard_layout.master')

@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>Review Management</h1>
       </div>
   <div class="mi_detail">
      <div class="review_sys">

         @if (isset($business->reviews) && $business->reviews->isNotEmpty())
         <div class="crt_main ">
            @foreach ($business->reviews as $review)
               <div class="review_list">
                  <div class="review_part-1">
                        <div class="review_p1_imgs">
                           <div class="review_p1_imgs-1">
                              <div class="img_review">
                                    <div class="img_review-1">
                                       <img class="prn_re" src="{{ asset($review->user->profile_image ?? dimage()) }}" alt="">
                                       <div class="img_review-2_1">
                                          <p class="m-0">{{ ($review->user->first_name ?? '') . ' ' . ($review->user->last_name ?? '') ?: 'User Name' }}</p>

                                       
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
                                    </div>

                                    <div class="img_review-2">
                                       {{ $review->created_at->diffForHumans() }}
                                    </div>
                              </div>
                           </div>

                           <div class="review_p1_imgs-2">
                              <span>{{ $review->translations->first()->title ?? 'Review Title' }}</span>
                              <p>{{ $review->translations->first()->description ?? 'Review Description' }}</p>
                           </div>
                        </div>
                  </div>

            
               </div>
            @endforeach
         </div>
         @else
            <div class="crt_main" style="display:flex;justify-content:center;align-items:center;flex-direction:column;">
               <img src="{{ dashboardDefaultImage() }}" alt="No Favorites" class="img-fluid mb-3" style="width: 280px; height: 280px;">
               <h5 class="text-muted">No Reviews yet.</h5>
            </div>
         @endif
      </div>


   </div>
@endsection
