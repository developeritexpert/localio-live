@extends('vendor_dashboard_layout.master')

@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>New Ad Campaign</h1>
       </div>
       <div class="mi_detail">
        <div class="campaign_main_div">
          <div class="campaign">
             <p class="mt-0">
                Campaign
             </p>

            <div class="compaign_search">
                <div class="form">
                    <input type="search" class="search-box" placeholder="Enter a product, category, or what you’d like to compare...">
                    <button class="btn cta_dark active"><i class="fa-solid fa-magnifying-glass"></i></button>
                 </div>


                 <div class="info_for_c_1 options-s">

                    <input type="text" id="dropdown-input" placeholder="This Month" readonly="">
                    <div class="dropdown-list" id="dropdown-list">
                        <div data-value="option1">Option 1</div>
                        <div data-value="option2">Option 2</div>
                        <div data-value="option3">Option 3</div>
                        <div data-value="option4">Option 4</div>
                    </div>



                 </div>
            </div>


            <div class="btn_all">
             <a href="" class="btn_campaign">
                 <div class="plus_img">
                     <img src="{{asset('vender_dashboard/img/plus.svg')}}" alt="">
                 </div>
                 New Campaign
             </a>
            </div>


            <div class="campaign_table">
             <table class="table table_cs table-bordered">
                <thead>
                  <tr>
                    <th><input type="checkbox" class="big_box"  data-bs-toggle="toggle"></th>
                    <th>off/on</th>
                    <th>Campaign</th>
                    <th>Delivery</th>
                    <th>Amount spent</th>
                    <th>Budget</th>
                    <th>Impressions</th>
                    <th>CPM</th>
                    <th>Reach</th>
                    <th>Links Click</th>
                    <th>Reach</th>
                    <th>Links Click</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><input type="checkbox"  data-bs-toggle="toggle"></td>
                    <td>    <label class="toggle-switch">
                      <input type="checkbox" checked>
                      <span class="slider"></span>
                  </label>
                </td>
                    <td>Lorem Ipsum is simply dummy</td>
                    <td>In draft</td>
                    <td>$200</td>
                    <td>Using ad set budget</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td><td>-</td>
                  <td>-</td>
                  </tr>
                  <tr>
                   <td><input type="checkbox"  data-bs-toggle="toggle"></td>
                   <td>    <label class="toggle-switch">
                     <input type="checkbox" checked>
                     <span class="slider"></span>
                 </label>
               </td>
                   <td>Lorem Ipsum is simply dummy</td>
                   <td>In draft</td>
                   <td>$200</td>
                   <td>Using ad set budget</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td><td>-</td>
                   <td>-</td>

                 </tr>
                 <tr>
                   <td><input type="checkbox"  data-bs-toggle="toggle"></td>
                   <td>    <label class="toggle-switch">
                     <input type="checkbox">
                     <span class="slider"></span>
                 </label>
               </td>
                   <td>Lorem Ipsum is simply dummy</td>
                   <td>off</td>
                   <td>$200</td>
                   <td>Using ad set budget</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td> <td>-</td>
                   <td>-</td>
                 </tr>
                 <tr>
                   <td><input type="checkbox"  data-bs-toggle="toggle"></td>
                   <td>    <label class="toggle-switch">
                     <input type="checkbox">
                     <span class="slider"></span>
                 </label>
               </td>
                   <td>Lorem Ipsum is simply dummy</td>
                   <td>off</td>
                   <td>$200</td>
                   <td>Using ad set budget</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td><td>-</td>
                   <td>-</td>
                 </tr>
                 <tr>
                   <td><input type="checkbox"  data-bs-toggle="toggle"></td>
                   <td>    <label class="toggle-switch">
                     <input type="checkbox">
                     <span class="slider"></span>
                 </label>
               </td>
                   <td>Lorem Ipsum is simply dummy</td>
                   <td>off</td>
                   <td>$200</td>
                   <td>Using ad set budget</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td> <td>-</td>
                   <td>-</td>
                 </tr>
                 <tr>
                   <td><input type="checkbox"  data-bs-toggle="toggle"></td>
                   <td>    <label class="toggle-switch">
                     <input type="checkbox">
                     <span class="slider"></span>
                 </label>
               </td>
                   <td>Lorem Ipsum is simply dummy</td>
                   <td>off</td>
                   <td>$200</td>
                   <td>Using ad set budget</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td> <td>-</td>
                   <td>-</td>
                 </tr>
                 <tr>
                   <td><input type="checkbox"  data-bs-toggle="toggle"></td>
                   <td>    <label class="toggle-switch">
                     <input type="checkbox">
                     <span class="slider"></span>
                 </label>
               </td>
                   <td>Lorem Ipsum is simply dummy</td>
                   <td>off</td>
                   <td>$200</td>
                   <td>Using ad set budget</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td>
                   <td>-</td><td>-</td>
                   <td>-</td>
                 </tr>

                 <tr>
                   <td>

                   </td>
                   <td>
               </td>
                   <td>Result From 7 campaigns</td>
                   <td></td>
                   <td>$1400</td>
                   <td></td>
                   <td></td>
                   <td></td>
                   <td></td>
                   <td></td><td>-</td>
                   <td>-</td>

                 </tr>


                </tbody>
              </table>
            </div>


          </div>

        </div>

       </div>

       </div>


    </div>
@endsection
