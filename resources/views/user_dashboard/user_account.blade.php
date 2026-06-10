@section('title', 'User Dashboard | Localio')
@extends('user_dashboard_layout.master')

@section('content')


       <div class="col-lg-9 p-0">
          <div class="user_content">
             <div class="uer_nm">
                <h1>My Account</h1>
             </div>
             <div class="mi_detail">
                <div class="row gy-4">


                  <div class="col-lg-6">
                     <a class="acc-box" href="{{route('user-deal', ['locale' => app()->getLocale()])}}">
                        <div class="acc-img">
                           <img src="{{asset('user-dashboard-theme/img/mt_reward.svg')}}" class="img-fluid">
                        </div>
                        <div class="acc-text">

                           <h2>My Deals</h2>
                           <p>Your exclusive offers and discounts.</p>
                        </div>
                     </a>
                  </div>



                  <div class="col-lg-6">
                     <a class="acc-box" href="{{ route('user-product', ['locale' => app()->getLocale()]) }}">
                        <div class="acc-img">
                           <img src="{{asset('user-dashboard-theme/img/saved_prdt.svg')}}" class="img-fluid">
                        </div>
                        <div class="acc-text">
                           <h2>My Favorites</h2>
                           <p>Your shortlist of top picks</p>
                        </div>
                     </a>
                  </div>



                   <div class="col-lg-6">
                     <a class="acc-box" href="{{ route('user-review', ['locale' => app()->getLocale()]) }}">
                        <div class="acc-img">
                           <img src="{{asset('user-dashboard-theme/img/my_rview.svg')}}" class="img-fluid">
                        </div>
                        <div class="acc-text">
                           <h2>My Reviews</h2>
                           <p>Your published reviews, all in one place</p>
                        </div>
                     </a>
                  </div>


                  <div class="col-lg-6">
                     <a class="acc-box" href="{{route('user-profile', ['locale' => app()->getLocale()])}}">
                        <div class="acc-img">
                           <img src="{{asset('user-dashboard-theme/img/my_profle.svg')}}" class="img-fluid">
                        </div>
                        <div class="acc-text">
                           <h2>My Profile</h2>
                           <p>Add your industry, job function, company size to get personalized picks</p>
                        </div>
                     </a>
                  </div>



                </div>
             </div>
          </div>
       </div>

 @endsection
