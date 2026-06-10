@extends('vendor_dashboard_layout.master')
@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>Overview</h1>
       </div>


       <div class="btn_all">
        <a href="" class="btn_campaign">
            <div class="plus_img">
                <img src="{{asset('vender_dashboard/img/plus.svg')}}" alt="">
            </div>
            New Campaign
        </a>
       </div>


       <div class="mi_detail">
          <div class=" gy-4">
            <div class="overview_p">
                <div class="overview_list overview_list_4">
                    <div class="overlist_box ">
                       <h6>Clicks
                        </h6>
                       <div class="overlist_value">
                          <span class="overlist_sp">
                            3.85k
                          </span>

                       </div>
                    </div>
                    <div class="overlist_box ">
                        <h6>Impressions

                         </h6>
                        <div class="overlist_value">
                           <span class="overlist_sp">
                            35.1k
                           </span>

                        </div>
                     </div>
                     <div class="overlist_box ">
                        <h6>Avg. CPC

                         </h6>
                        <div class="overlist_value">
                           <span class="overlist_sp">
                            $2.17
                           </span>

                        </div>
                     </div>
                     <div class="overlist_box ">
                        <h6>Cost

                         </h6>
                        <div class="overlist_value">
                           <span class="overlist_sp">
                            $8.34k

                           </span>

                        </div>
                     </div>
                 </div>

                 <div class="overview_img2">
                    <img src="{{asset('vender_dashboard/img/Group 100000280.png')}}" alt="">
                 </div>
            </div>
          </div>
       </div>


    </div>
 </div>
 @endsection
