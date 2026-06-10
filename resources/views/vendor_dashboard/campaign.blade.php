@extends('vendor_dashboard_layout.master')
@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>{{ __('file.new_ad_campaign') }}</h1>
       </div>
       <div class="mi_detail">
          <div class="campaign_main_div">
             <div class="campaign">
                <p class="mt-0">
                    {{ __('file.chosse_you_goal') }}
                </p>

                <div class="campaign_1">
                   <div class="campaign_1part">

                      <div class="img_p_1">
                         <img src="{{asset('vender_dashboard/img/campaign_1.png')}}" alt="">
                      </div>
                      <p class="m-0">{{ __('file.Traffic') }}</p>
                   </div>
                   <div class="campaign_1part">

                      <div class="img_p_1">
                         <img src="{{asset('vender_dashboard/img/campaign_1part2.png')}}" alt="">
                      </div>
                      <p class="m-0">{{ __('file.Lead_Form') }}</p>
                   </div>
                   <div class="campaign_1part">

                      <div class="img_p_1">
                         <img src="{{asset('vender_dashboard/img/campaign_1part3.png')}}" alt="">
                      </div>
                      <p class="m-0">{{ __('file.Conversions') }}</p>
                   </div>
                   <div class="campaign_1part">

                      <div class="img_p_1">
                         <img src="{{asset('vender_dashboard/img/campaign_1part4.png')}}" alt="">
                      </div>
                      <p class="m-0">{{ __('file.Purchases') }}</p>
                   </div>
                </div>
             </div>
             <div class="campaign">
                <p class="mt-0">
                    {{ __('file.Campaign_Details') }}
                </p>
                <div class="div_form_imp">
                   <div class="info_for_c">
                      <div class="info_for_c_1">
                         <label>{{ __('file.Campaign_Name') }}</label>
                         <input type="text" placeholder="Add here">
                      </div>
                      <div class="info_for_c_1">
                         <label>{{ __('file.Brand_Name') }}</label>
                         <input type="text" placeholder="Add here">
                      </div>
                   </div>
                   <div class="info_for_c">
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Industry') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Language') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                   </div>
                   <div class="info_for_c">
                      <div class="info_for_c_1">
                         <label>{{ __('file.Start_Date') }}</label>
                         <input type="text" placeholder="Add here">
                      </div>
                      <div class="info_for_c_1">
                         <label>{{ __('file.Start_Date') }}</label>
                         <input type="text" placeholder="Add here">
                      </div>
                   </div>
                </div>


             </div>
             <div class="campaign">
                <p class="mt-0">
                   {{ __('file.Ad_Set_Targeting') }}
                </p>
                <div class="div_form_imp">
                   <div class="info_for_c">
                      <div class="info_for_c_1">
                         <label> {{ __('file.Ad_set_Name') }}</label>
                         <input type="text" placeholder="Add here">
                      </div>
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Language') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                   </div>
                   <div class="info_for_c">
                      <div class="info_for_c_1">
                         <label>{{ __('file.Name_your_Audience') }}</label>
                         <input type="text" placeholder="Add here">
                      </div>
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Location_Targeting') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                   </div>
                   <div class="info_for_c">
                      <div class="info_for_c_1">
                         <label>{{ __('file.Keywords') }}</label>
                         <input type="text" placeholder="Add here">
                      </div>
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Publishers') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                   </div>

                   <div class="info_for_c">
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Categories') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Targeting_Devices') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                   </div>
                   <div class="info_for_c">
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Categories') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                      <div class="info_for_c_1 options-s">
                         <label>{{ __('file.Targeting_Devices') }}</label>
                         <input type="text" id="dropdown-input" placeholder="Add here" readonly="">
                         <div class="dropdown-list" id="dropdown-list">
                            <div data-value="option1">Option 1</div>
                            <div data-value="option2">Option 2</div>
                            <div data-value="option3">Option 3</div>
                            <div data-value="option4">Option 4</div>
                         </div>



                      </div>
                   </div>
                </div>


             </div>
             <div class="campaign">
                <p class="mt-0">
                   {{ __('file.Bidding_Strategy') }}
                </p>

                <div class="campaign_2">
                   <p class="m-0">{{ __('file.What_are_you_optimizing_for?') }}</p>

                   <div class="compaign_input">
                      <label class="radio-container">
                         <input type="radio" name="optimizes" checked="">
                         <span class="checkmark"></span> Impressions
                      </label>
                      <label class="radio-container">
                         <input type="radio" name="optimizes">
                         <span class="checkmark"></span>Clicks
                      </label>
                   </div>

                   <div class="compaign_input_2">
                      <div class="info_for_c_1">
                         <label>  {{ __('file.Daily_Budget') }}</label>
                         <input type="text" placeholder="Add here">
                      </div>
                   </div>


                   <p class="m-0">{{ __('file.What_are_you_optimizing_for?') }}</p>

                   <div class="compaign_input compaign_input_3">
                      <label class="radio-container">
                         <input type="radio" name="optimize" checked="">
                         <div class="checkmark"></div>
                         <div class="op_select">
                            Balanced
                            <span>Spend the budget throughout the whole day </span>
                         </div>
                      </label>
                      <label class="radio-container">
                         <input type="radio" name="optimize">
                         <div class="checkmark"></div>
                         <div class="op_select">Accelerated
                            <span>
                               Spend the budget as soon as possible
                            </span>
                         </div>
                      </label>
                   </div>

                </div>
             </div>
             <div class="campaign">
                <p class="mt-0">
                   Ad Details
                </p>
                <div class="div_form_imp">
                   <div class="info_for_c">
                      <div class="info_for_c_1">
                         <label>Tittle</label>
                         <input type="text" placeholder="Add here">
                      </div>
                      <div class="info_for_c_1">
                         <label>Website URL</label>
                         <input type="text" placeholder="Add here">
                      </div>
                   </div>
                   <div class="info_for_c">
                      <div class="info_for_c_1 fw_iput">
                         <label>Ad Content</label>
                         <input type="text" placeholder="Add here">
                      </div>
                   </div>

                   <div class="info_for_c upload_i_v">
                      <div class="label_updt">
                         <label for="">Video</label>
                         <div class="upload_c">


                            <div class="upload_img_v">
                               <img src="{{asset('vender_dashboard/img/upaod1.png')}}" alt="">
                            </div>
                            Add Video

                            <input type="file" class="upd_c" id="fileInput_video" accept=".mp4">



                         </div>
                      </div>
                      <div class="label_updt">
                         <label for="">
                            Images
                         </label>
                         <div class="upload_c">


                            <div class="upload_img_v">
                               <img src="{{asset('vender_dashboard/img/upload_img.png')}}" alt="">
                            </div>
                            Add Images

                            <input type="file" class="upd_c" id="fileInput_img" accept=".jpg , .png">



                         </div>
                      </div>
                   </div>




                   <div class="info_for_c">
                      <div class="info_for_c_1 fw_iput">
                         <label>Call To Action</label>
                         <input type="text" placeholder="Add here">
                      </div>

                   </div>
                </div>


             </div>
             <div class="btn_u">
                <a href="">Launch</a>
             </div>
          </div>

       </div>

    </div>
 </div>
 @endsection
