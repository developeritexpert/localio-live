@extends('vendor_dashboard_layout.master')

@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>Product Management</h1>
       </div>
       <div class="mi_detail">
        <div class="review_sys">

        @foreach ($business->products as $product )
            
      
          <div class="review_list">
             <div class="review_part-1">
                <div class="review_p1_imgs">
                   <div class="review_p1_imgs-1">
                      <div class="img_review">
                         <div class="img_review-1">
                            <img class="prn_re" src="{{asset($product->product_icon )}}" alt="product image">
                            <div class="img_review-2_1"> 
                               <p class="m-0">{{$product->translations->name ?? 'Product name'}}</p>
                               <div class="star">
                                  <div class="review_points">
                                    {{$product->translations->name ?? 'Product name'}}
                                  </div>
                                  {{-- <span class="product_category">
                                    {{ $product->category->translation->name ?? 'Product category' }}
                                </span> --}}
                                
                               </div>
                            </div>
                         </div>
                         <div class="img_review-2">
                            Added {{ $product->created_at->diffForHumans() }}
                        </div>
                        
                      </div>
                   </div>
            
                </div>
             </div>
             <div class="review-btm-rgt">
               <div class="shr_dt dot">
                   <span class="elps_icn"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                   <div class="dropdown-menu_review">
                  
                       <div class="dropdown-main ">
                           <div class="dash-icon">
                              <a class="dropdown-item" href="{{ route('vendor-add-product', ['locale' => app()->getLocale(), 'product_id' => $product->id]) }}">
                                 <i class="icon ni ni-edit"></i> Edit Product
                             </a>
                             
                           </div>
                     
                       </div>
                   </div>
               </div>
           </div>
          </div>
          @endforeach


        </div>
       </div>
    </div>
</div>
@endsection