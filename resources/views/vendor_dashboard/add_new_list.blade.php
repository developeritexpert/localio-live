@extends('vendor_dashboard_layout.master')
@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="new-listing">
        <h1>{{ __('file.add-new-list') }}</h1>
        <div class="new-form">
            <form action="">
                <div class="form-block">
                    <label for="product-name">{{ __('file.Product_name') }}</label>
                    <input type="text" id="product-name" name="product-name" placeholder="Add here">
                </div>
                <div class="form-block">
                    <label for="product-category">{{ __('file.Product_Category') }}</label>
                    <input type="text" id="product-category" name="product-category" placeholder="Add here">
                </div>
                <div class="form-block">
                    <label for="Website-url">{{ __('file.Website_URL') }}</label>
                    <input type="text" id="Website-url" name="Website-url" placeholder="Add here">
                </div>
            </form>
        </div>

        <div class="new-btn">
            <a href="javascript:void(0)" class="btn unq_btn">{{ __('file.create_listing') }}</a>
        </div>
       </div>
    </div>
</div>
@endsection
