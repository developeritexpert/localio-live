@extends('vendor_dashboard_layout.master')
@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="uer_nm">
          <h1>{{ __('file.overview') }}</h1>
       </div>
       <div class="mi_detail product">
          <div class="row gy-4">
            <div class="overview_list">
               <div class="overlist_box">
                   {{-- <h6>{{ __('file.total-listing') }}</h6> --}}
                   <div class="overlist_value">
                       <span class="overlist_sp">
                      Add Product
                       </span>
                       <span class="overlist_sp">
                           <a class="overlst_an" href="{{ route('vendor-add-product', ['locale' => app()->getLocale()]) }}">
                               {{ __('file.view-all') }}
                               <img src="{{asset('vender_dashboard/img/arrow-rgt.svg')}}" alt="">
                           </a>
                       </span>
                   </div>
               </div>

               <div class="overlist_box">
                   {{-- <h6>Edit Products</h6> --}}
                   <div class="overlist_value">
                       <span class="overlist_sp">
                        Edit Products
                       </span>
                       <span class="overlist_sp">
                           <a class="overlst_an" href="{{ route('vendor-total-product', ['locale' => app()->getLocale()]) }}">
                               {{ __('file.view-all') }}
                               <img src="{{asset('vender_dashboard/img/arrow-rgt.svg')}}" alt="">
                           </a>
                       </span>
                   </div>
               </div>

           </div>

          </div>
       </div>


      {{-- Show the products in a listing table --}}
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <!-- Header with title and Add Product button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Your Products</h5>
                <a href="{{ route('vendor-add-product', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">
                    + Add Product
                </a>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Product Category</th>
                            <th>Product Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->name ?? 'N/A' }}</td>
                                <td>
                                    @if($product->categories && $product->categories->count() > 0)
                                        {{ $product->categories->pluck('name')->implode(', ') }}
                                    @else
                                        No data
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($product->product_link))
                                        <a href="{{ $product->product_link }}" target="_blank">Product</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="py-4">
                                        <em class="icon ni ni-package" style="font-size: 48px; opacity: 0.3;"></em>
                                        <p><strong>No Products Found</strong></p>
                                        <p>Please add a new product to get started.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{{-- End Show the products in a listing table --}}




    </div>
 </div>
@endsection
