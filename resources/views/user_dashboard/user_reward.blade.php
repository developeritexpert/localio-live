@extends('user_dashboard_layout.master')

@section('content')

       <div class="col-lg-9 p-0">
          <div class="user_content user_info">
             <div class="uer_nm">
                <h1>My Rewards</h1>
             </div>
             <div class="reward-main">
                <div class="reward-main-inner">
                   <div class="rewrd-inner-hd">
                      <h4>My Points</h4>
                   </div>
                   <div class="rewrd-innr-btm d-flex">
                      <div class="points-div d-flex">
                         <div class="points-img">
                            <img src="{{asset('user-dashboard-theme/img/points-img.png')}}" alt="">
                         </div>
                         <div class="points-text">
                            <p>Available Points</p>
                            <h2 class="m-0">1147</h2>
                         </div>
                      </div>
                      <div class="points-div d-flex">
                         <div class="points-img">
                            <img src="{{asset('user-dashboard-theme/img/points-img.png')}}" alt="">
                         </div>
                         <div class="points-text">
                            <p>Redeemed Points</p>
                            <h2 class="m-0">120</h2>
                         </div>
                      </div>
                   </div>
                </div>
                <div class="reward-main-inner mt-30">
                   <div class="rewrd-inner-hd">
                      <h4>My Rewards</h4>
                   </div>
                   <div class="rewrd-innr-btm">
                      <div class="row">
                         <div class="col-lg-4">
                            <div class="rewrd-innr-crd">
                               <div class="gift-img">
                                  <img src="{{asset('user-dashboard-theme/img/gift-img.svg')}}"  alt="">
                               </div>
                               <h6>Lorem Ipsum is simply dummy text</h6>
                               <div class="copy-cod-txt d-flex">
                                  <p class="m-0">#LOCALIO1234</p>
                                  <div class="copy-svg">
                                     <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.134 4.11621H1.87986C1.15365 4.11621 0.564941 4.70492 0.564941 5.43113V18.6853C0.564941 19.4115 1.15365 20.0002 1.87986 20.0002H15.134C15.8602 20.0002 16.4489 19.4115 16.4489 18.6853V5.43113C16.4489 4.70492 15.8602 4.11621 15.134 4.11621Z" fill="white"/>
                                        <path d="M19.245 15.8895C19.4177 15.8895 19.5887 15.8555 19.7482 15.7894C19.9077 15.7233 20.0527 15.6265 20.1748 15.5044C20.2969 15.3823 20.3937 15.2373 20.4598 15.0778C20.5259 14.9182 20.5599 14.7473 20.5599 14.5746V1.31492C20.5592 0.966403 20.4204 0.632373 20.174 0.385936C19.9275 0.1395 19.5935 0.000729873 19.245 0H5.98533C5.63659 0 5.30213 0.138535 5.05554 0.38513C4.80895 0.631725 4.67041 0.966179 4.67041 1.31492V3.05801H15.1345C15.7624 3.05801 16.3645 3.30743 16.8085 3.75141C17.2525 4.19538 17.5019 4.79754 17.5019 5.42541V15.8895H19.245Z" fill="white"/>
                                     </svg>
                                  </div>
                               </div>
                               <div class="apply-btn">
                                  <a class="blue-btn" href="#">Apply Coupon</a>
                               </div>
                               <div class="three-dots">
                                  <img src="{{asset('user-dashboard-theme/img/three-dots.svg')}}" alt="">
                               </div>
                            </div>
                         </div>
                         <div class="col-lg-4">
                            <div class="rewrd-innr-crd">
                               <div class="gift-img">
                                  <img src="{{asset('user-dashboard-theme/img/gift-img.svg')}}"  alt="">
                               </div>
                               <h6>Lorem Ipsum is simply dummy text</h6>
                               <div class="copy-cod-txt d-flex">
                                  <p class="m-0">#LOCALIO1234</p>
                                  <div class="copy-svg">
                                     <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.134 4.11621H1.87986C1.15365 4.11621 0.564941 4.70492 0.564941 5.43113V18.6853C0.564941 19.4115 1.15365 20.0002 1.87986 20.0002H15.134C15.8602 20.0002 16.4489 19.4115 16.4489 18.6853V5.43113C16.4489 4.70492 15.8602 4.11621 15.134 4.11621Z" fill="white"/>
                                        <path d="M19.245 15.8895C19.4177 15.8895 19.5887 15.8555 19.7482 15.7894C19.9077 15.7233 20.0527 15.6265 20.1748 15.5044C20.2969 15.3823 20.3937 15.2373 20.4598 15.0778C20.5259 14.9182 20.5599 14.7473 20.5599 14.5746V1.31492C20.5592 0.966403 20.4204 0.632373 20.174 0.385936C19.9275 0.1395 19.5935 0.000729873 19.245 0H5.98533C5.63659 0 5.30213 0.138535 5.05554 0.38513C4.80895 0.631725 4.67041 0.966179 4.67041 1.31492V3.05801H15.1345C15.7624 3.05801 16.3645 3.30743 16.8085 3.75141C17.2525 4.19538 17.5019 4.79754 17.5019 5.42541V15.8895H19.245Z" fill="white"/>
                                     </svg>
                                  </div>
                               </div>
                               <div class="apply-btn">
                                  <a class="blue-btn" href="#">Apply Coupon</a>
                               </div>
                               <div class="three-dots">
                                  <img src="{{asset('user-dashboard-theme/img/three-dots.svg')}}" alt="">
                               </div>
                            </div>
                         </div>
                         <div class="col-lg-4">
                            <div class="rewrd-innr-crd">
                               <div class="gift-img">
                                  <img src="{{asset('user-dashboard-theme/img/gift-img.svg')}}"  alt="">
                               </div>
                               <h6>Lorem Ipsum is simply dummy text</h6>
                               <div class="copy-cod-txt d-flex">
                                  <p class="m-0">#LOCALIO1234</p>
                                  <div class="copy-svg">
                                     <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.134 4.11621H1.87986C1.15365 4.11621 0.564941 4.70492 0.564941 5.43113V18.6853C0.564941 19.4115 1.15365 20.0002 1.87986 20.0002H15.134C15.8602 20.0002 16.4489 19.4115 16.4489 18.6853V5.43113C16.4489 4.70492 15.8602 4.11621 15.134 4.11621Z" fill="white"/>
                                        <path d="M19.245 15.8895C19.4177 15.8895 19.5887 15.8555 19.7482 15.7894C19.9077 15.7233 20.0527 15.6265 20.1748 15.5044C20.2969 15.3823 20.3937 15.2373 20.4598 15.0778C20.5259 14.9182 20.5599 14.7473 20.5599 14.5746V1.31492C20.5592 0.966403 20.4204 0.632373 20.174 0.385936C19.9275 0.1395 19.5935 0.000729873 19.245 0H5.98533C5.63659 0 5.30213 0.138535 5.05554 0.38513C4.80895 0.631725 4.67041 0.966179 4.67041 1.31492V3.05801H15.1345C15.7624 3.05801 16.3645 3.30743 16.8085 3.75141C17.2525 4.19538 17.5019 4.79754 17.5019 5.42541V15.8895H19.245Z" fill="white"/>
                                     </svg>
                                  </div>
                               </div>
                               <div class="apply-btn">
                                  <a class="blue-btn" href="#">Apply Coupon</a>
                               </div>
                               <div class="three-dots">
                                  <img src="{{asset('user-dashboard-theme/img/three-dots.svg')}}" alt="">
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
