@extends('admin_layout.master')
@section('content')
<div class="nk-block-head nk-block-head-sm">

    <div class="nk-block-between">
         <div class="nk-block-head-content">
             <h3 class="nk-block-title page-title">Admin Dashboard</h3>
             <div class="nk-block-des text-soft">

             </div>
         </div><!-- .nk-block-head-content -->
         <div class="nk-block-head-content">
             <div class="toggle-wrap nk-block-tools-toggle">
                 <div class="toggle-expand-content" data-content="pageMenu">
                     <ul class="nk-block-tools g-3">
                         <li>
                             <input type="hidden" name="start_date" id="start_date" value="2025-03-03">
                             <input type="hidden" name="end_date" id="end_date" value="2025-04-01">
                             <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                  <i class="fa fa-calendar"></i>&nbsp;
                                  <span>March 3, 2025 - April 1, 2025</span> <i class="fa fa-caret-down"></i>
                              </div>
                        </li>
                         <li class="nk-block-tools-opt">
                             <div class="drodown">
                                 <button class=" btn  btn-primary" type="button" id="searchBtn"><span>Search</span></button>

                             </div>
                         </li>
                     </ul>
                 </div><!-- .toggle-expand-content -->
             </div><!-- .toggle-wrap -->
         </div>
     </div><!-- .nk-block-between -->

</div>

<div class="nk-block">

    <div class="row g-gs">

             <!-- First Row -->
             <div class="col-md-6">
                 <div class="card card-bordered card-full">
                     <div class="card-inner">
                         <div class="card-title-group align-start mb-0">
                             <div class="card-title">
                                 <h6 class="subtitle">Active Users</h6>
                             </div>
                             <div class="card-tools">
                                 <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total sales" data-bs-original-title="Total sales"></em>
                             </div>
                         </div>
                         <div class="card-amount">
                             <span class="amount" id="totalSales">{{$user->count()}}</span>
                             <span class="change up text-danger"><em class="icon ni ni-arrow-long-up"></em>1.93%</span>
                         </div>
                         <div class="invest-data">
                             <div class="invest-data-amount g-2">
                                 <div class="invest-data-history">
                                     <div class="title">This Month</div>
                                     <div class="amount" id="thisMonthSales">0 <span class="currency currency-usd">USD</span></div>
                                 </div>
                                 <div class="invest-data-history">
                                     <div class="title">This Week</div>
                                     <div class="amount" id="thisWeekSales">0 <span class="currency currency-usd">USD</span></div>
                                 </div>
                             </div>
                             <div class="invest-data-ck">
                                 <canvas class="iv-data-chart" id="totalDeposit"></canvas>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             <div class="col-md-6">
                 <div class="card card-bordered card-full">
                     <div class="card-inner">
                         <div class="card-title-group align-start mb-0">
                             <div class="card-title">
                                 <h6 class="subtitle">Total Product</h6>
                             </div>
                             <div class="card-tools">
                                 <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total orders" data-bs-original-title="Total orders"></em>
                             </div>
                         </div>
                         <div class="card-amount">
                             <span class="amount" id="totalOrder">{{$products->count()}}</span>
                             <span class="change down text-danger"><em class="icon ni ni-arrow-long-down"></em>1.93%</span>
                         </div>
                         <div class="invest-data">
                             <div class="invest-data-amount g-2">
                                 <div class="invest-data-history">
                                     <div class="title">This Month</div>
                                     <div class="amount" id="thisMonthOrders">0</div>
                                 </div>
                                 <div class="invest-data-history">
                                     <div class="title">This Week</div>
                                     <div class="amount" id="thisWeekOrders">0</div>
                                 </div>
                             </div>
                             <div class="invest-data-ck">
                                 <canvas class="iv-data-chart" id="totalWithdraw"></canvas>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

         <div class="row mt-3">
             <!-- Second Row -->
             <div class="col-md-6">
                 <div class="card card-bordered card-full">
                     <div class="card-inner">
                         <div class="card-title-group align-start mb-0">
                             <div class="card-title">
                                 <h6 class="subtitle">Total Category</h6>
                             </div>
                             <div class="card-tools">
                                 <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Documents" data-bs-original-title="Total Documents"></em>
                             </div>
                         </div>
                         <div class="card-amount">
                             <span class="amount" id="totalDocument">{{$categories->count()}}</span>
                         </div>
                         <div class="invest-data">
                             <div class="invest-data-amount g-2">
                                 <div class="invest-data-history">
                                     <div class="title">This Month</div>
                                     <div class="amount" id="thisMonthDocuments">0</div>
                                 </div>
                                 <div class="invest-data-history">
                                     <div class="title">This Week</div>
                                     <div class="amount" id="thisWeekDocuments">0</div>
                                 </div>
                             </div>
                             <div class="invest-data-ck">
                                 <canvas class="iv-data-chart" id="totalBalance"></canvas>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             <div class="col-md-6">
                 <div class="card card-bordered card-full">
                     <div class="card-inner">
                         <div class="card-title-group align-start mb-0">
                             <div class="card-title">
                                 <h6 class="subtitle">Total Businessess</h6>
                             </div>
                             <div class="card-tools">
                                 <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Users" data-bs-original-title="Total Users"></em>
                             </div>
                         </div>
                         <div class="card-amount">
                             <span class="amount" id="totaluser">{{$businesses->count()}}</span>
                         </div>
                         <div class="invest-data">
                             <div class="invest-data-amount g-2">
                                 <div class="invest-data-history">
                                     <div class="title">This Month</div>
                                     <div class="amount" id="thisMonthUsers">0</div>
                                 </div>
                                 <div class="invest-data-history">
                                     <div class="title">This Week</div>
                                     <div class="amount" id="thisWeekUsers">0</div>
                                 </div>
                             </div>
                             <div class="invest-data-ck">
                                 <canvas class="iv-data-chart" id="totalBalance"></canvas>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>




   </div>
</div>
@endsection
