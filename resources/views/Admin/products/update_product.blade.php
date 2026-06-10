@extends('admin_layout.master')
@section('content')
     <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Edit Product</h4>
                <div class="nk-block-des text-soft">
                    <p>Update product information and settings</p>
                </div>
            </div>
            {{-- <div class="nk-block-head-content">
                <a href="{{ route('product-list') }}" class="btn btn-outline-light d-none d-sm-inline-flex">
                    <em class="icon ni ni-arrow-left"></em><span>Back to List</span>
                </a>
            </div> --}}
        </div>

        <form action="{{ route('product-update-procc', $product->id) }}" class="form-validate" id="productForm" method="post"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {{-- <label class="form-label" for="name">Product Name</label> --}}
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                {{-- <input type="text" class="form-control" name="name" id="name"
                                                    placeholder="Product Name"
                                                    value="{{ old('name', $product->translation->name ?? '') }}"> --}}

                                                    <x-google-input
                                                    type="text"
                                                    name="name"
                                                    id="name"
                                                    label="Product Name"
                                                    value="{{ old('name', $product->translation->name ?? '') }}"
                                                />


                                            </div>
                                        </div>
                                        @error('name')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        {{-- <label class="form-label" for="product-link">Product Link</label>
                                        <input type="text" class="form-control" name="product_link" id="product-link"
                                            value="{{ old('product_link', $product->product_link ?? '') }}"
                                            placeholder="Affiliate Link"> --}}

                                            <x-google-input
                                            type="text"
                                            name="product_link"
                                            id="product-link"
                                            label="Product Link"
                                            value="{{ old('product_link', $product->product_link ?? '') }}"
                                        />


                                        @error('product_link')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                           
                                {{-- <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label class="form-label" for="overview">Product Overview</label>
                                        <textarea class="description form-control" id="overview" name="overview" rows="3">{{ old('overview', $product->translation->overview ?? '') }}</textarea>
                                        @error('overview')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label class="form-label" for="description">Product Description</label>
                                        <textarea class="description form-control" id="description" name="description" rows="5">{{ old('description', $product->translation->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}
                            </div>

                            <input type="hidden" name="lang_code" value="{{ getCurrentLanguageID() }}" />
                        </div>
                    </div>

                
                    <!--    <div class="card card-bordered">
                                <div class="card-inner">
                    
                                    <h5 class="card-title"> Product Price</h5>

                                    <div id="price-container">

                                        @foreach ($product->prices as $price)
                                            <div class="price-box mb-3  rounded position-relative">
                                                <div class="">
                                                    <label class="form-label">Default Price :</label>
                                                </div>
                                                {{-- Main Price + Currency + Tenure --}}
                                            
                                                    {{-- <input type="number" class="form-control" name="prices"
                                                        value="{{ $price->price }}" placeholder="Enter Price" required> --}}

                                                        <x-google-input
                                                            type="number"
                                                            name="prices"
                                                    
                                                            label="Default Price"
                                                            value="{{ $price->price }}"
                                                        />

                                                    {{-- Currency --}}
                                                    {{-- <select class="form-select" name="currencies" required>
                                                        @foreach ($currencies as $currency)
                                                            <option value="{{ $currency->symbol }}"
                                                                {{ $price->currency == $currency->symbol ? 'selected' : '' }}>
                                                                {{ $currency->code }}
                                                            </option>
                                                        @endforeach
                                                    </select> --}}
                                                    <x-google-input
                                                        type="select"
                                                        name="currencies"
                                                        :alwaysActive="true" 
                                                        label="Currency"
                                                        :value="$price->currency"
                                                        :options="$currencies->pluck('code', 'symbol')->toArray()"
                                                        />
                                            

                                                {{-- Time Unit + Description --}}
                                            
                                                    {{-- <select class="form-select" name="time_units" required>
                                                        <option value="">Time Unit</option>
                                                        @foreach (['one_time', 'day', 'week', 'month', 'quarter', 'year'] as $unit)
                                                            <option value="{{ $unit }}"
                                                                {{ $price->time_unit == $unit ? 'selected' : '' }}>
                                                                {{ ucfirst($unit) }}
                                                            </option>
                                                        @endforeach
                                                    </select> --}}

                                                    <x-google-input
                                                        type="select"
                                                        name="time_units"
                                                        :alwaysActive="true" 
                                                        label="Time Unit"
                                                        :value="$price->time_unit"
                                                        :options="[
                                                            'one_time' => 'One time',
                                                            'day' => 'Day',
                                                            'week' => 'Week',
                                                            'month' => 'Month',
                                                            'quarter' => 'Quarter',
                                                            'year' => 'Year'
                                                        ]"
                                                    />
                                                


                                                    {{-- <input type="text" class="form-control" name="price_descriptions"
                                                        value="{{ $price->additional_info }}"
                                                        placeholder="e.g. per user, per license"> --}}

                                                        <x-google-input
                                                            type="text"
                                                            name="price_descriptions"
                                                        
                                                            label="Price Description"
                                                            value="{{ $price->additional_info }}"
                                                        />
                                    

                                                    {{-- Renewal Price --}}
                                                
            
                                                        <div class="">
                                                            <label class="form-label">Renewal:</label>
                                                        </div>
                                                    
                                                            {{-- <input type="number" class="form-control" name="renewal_prices"
                                                                value="{{ $price->renewal_price }}"
                                                                placeholder="Renewal Price (optional)"> --}}

                                                                <x-google-input
                                                                    type="number"
                                                                    name="renewal_prices"
                                                                
                                                                    label="Renewal Price "
                                                                value="{{ $price->renewal_price }}"
                                                                />
                                                            
                                                            {{-- <select class="form-select" name="renewal_time_units">
                                                                <option value="">Time Unit</option>
                                                                @foreach (['one_time', 'day', 'week', 'month', 'quarter', 'year'] as $unit)
                                                                    <option value="{{ $unit }}"
                                                                        {{ $price->renewal_time_units == $unit ? 'selected' : '' }}>
                                                                        {{ ucfirst($unit) }}
                                                                    </option>
                                                                @endforeach
                                                            </select> --}}

                                                            <x-google-input
                                                                type="select"
                                                                name="renewal_time_units"
                                                        
                                                                label="Time Unit"
                                                                :value="$price->renewal_time_units"
                                                                :options="[
                                                                    'one_time' => 'One time',
                                                                    'day' => 'Day',
                                                                    'week' => 'Week',
                                                                    'month' => 'Month',
                                                                    'quarter' => 'Quarter',
                                                                    'year' => 'Year'
                                                                ]"
                                                                :alwaysActive="true" 
                                                            />

                                                
                                                    

                                                {{-- Discount Price --}}
                                                

                                                    <div class="">
                                                        <label class="form-label">Discount:</label>
                                                    </div>
                                            
                                                        {{-- <input type="number" class="form-control" name="discount_prices"
                                                            value="{{ $price->discount_price }}"
                                                            placeholder="Discount Price (optional)"> --}}

                                                            <x-google-input
                                                                type="number"
                                                                name="discount_prices"
                                                                
                                                                label="Discount Price (optional)"
                                                                :value="$price->discount_price"
                                                            
                                                            />

                                                        {{-- <select class="form-select" name="discount_time_units">
                                                            <option value="">Time Unit</option>
                                                            @foreach (['one_time', 'day', 'week', 'month', 'quarter', 'year'] as $unit)
                                                                <option value="{{ $unit }}"
                                                                    {{ $price->discount_time_units == $unit ? 'selected' : '' }}>
                                                                    {{ ucfirst($unit) }}
                                                                </option>
                                                            @endforeach
                                                        </select> --}}

                                                        <x-google-input
                                                            type="select"
                                                            name="discount_time_units"
                                                        
                                                            label="Time Unit"
                                                            :value="$price->discount_time_units"
                                                            :options="[
                                                                'one_time' => 'One time',
                                                                'day' => 'Day',
                                                                'week' => 'Week',
                                                                'month' => 'Month',
                                                                'quarter' => 'Quarter',
                                                                'year' => 'Year'
                                                            ]"
                                                            :alwaysActive="true" 
                                                        />

                                            
                                                    
                                    

                                                </div>
                                                    

                                                {{-- Discount Expiration --}}
                                                <div class="mb-2">
                                                    {{-- <label class="form-label">Discount Expiration Date (optional):</label>
                                                    <input type="date" class="form-control" name="discount_expiration_dates"
                                                        value="{{ $price->discount_expiration_date }}"> --}}

                                                        <x-google-input
                                                            type="date"
                                                            id="discount_expiration_dates"
                                                            name="discount_expiration_dates"
                                                            label="Discount Expiration Date (optional)"
                                                            :value="$price->discount_expiration_date"
                                                            :alwaysActive="true" 
                                                        />
                                                        

                                                    <div class="form-text">
                                                        If set, the discount will only be shown until this date. After that, the
                                                        original or renewal price is displayed and the discounted price is
                                                        removed from our system.
                                                    </div>
                                            
                                            
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('prices.*')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror

                                </div>
                            </div> -->

                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <!-- Product Prices -->
                                    <h5 class="card-title"> Product Price</h5>
                            
                                    <div id="price-container">
                                        @foreach ($product->prices as $price)
                                            <div class="price-box mb-3 p-3 rounded position-relative">
                            
                                                {{-- Row 1: Default Price & Currency --}}
                                                <div class="mb-2">
                                                    <label class="form-label">Default Price :</label>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <x-google-input
                                                            type="number"
                                                            name="prices"
                                                            label="Default Price"
                                                            value="{{ $price->price }}"
                                                        />
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <x-google-input
                                                            type="select"
                                                            name="currencies"
                                                            :alwaysActive="true" 
                                                            label="Currency"
                                                            :value="$price->currency"
                                                            :options="$currencies->pluck('code', 'symbol')->toArray()"
                                                        />
                                                    </div>
                                                </div>
                            
                                                {{-- Row 2: Time Unit & Price Description --}}
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <x-google-input
                                                            type="select"
                                                            name="time_units"
                                                            :alwaysActive="true" 
                                                            label="Time Unit"
                                                            :value="$price->time_unit"
                                                            :options="[
                                                                'one_time' => 'One time',
                                                                'day' => 'Day',
                                                                'week' => 'Week',
                                                                'month' => 'Month',
                                                                'quarter' => 'Quarter',
                                                                'year' => 'Year'
                                                            ]"
                                                        />
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <x-google-input
                                                            type="text"
                                                            name="price_descriptions"
                                                            label="Price Description"
                                                            value="{{ $price->additional_info }}"
                                                        />
                                                    </div>
                                                </div>
                            
                                                {{-- Row 3: Renewal Price & Time Unit --}}
                                                <div class="mb-2">
                                                    <label class="form-label">Renewal:</label>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <x-google-input
                                                            type="number"
                                                            name="renewal_prices"
                                                            label="Renewal Price"
                                                            value="{{ $price->renewal_price }}"
                                                        />
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <x-google-input
                                                            type="select"
                                                            name="renewal_time_units"
                                                            label="Time Unit"
                                                            :value="$price->renewal_time_units"
                                                            :options="[
                                                                'one_time' => 'One time',
                                                                'day' => 'Day',
                                                                'week' => 'Week',
                                                                'month' => 'Month',
                                                                'quarter' => 'Quarter',
                                                                'year' => 'Year'
                                                            ]"
                                                            :alwaysActive="true" 
                                                        />
                                                    </div>
                                                </div>
                            
                                                {{-- Row 4: Discount Price & Time Unit --}}
                                                <div class="mb-2">
                                                    <label class="form-label">Discount:</label>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <x-google-input
                                                            type="number"
                                                            name="discount_prices"
                                                            label="Discount Price (optional)"
                                                            :value="$price->discount_price"
                                                        />
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <x-google-input
                                                            type="select"
                                                            name="discount_time_units"
                                                            label="Time Unit"
                                                            :value="$price->discount_time_units"
                                                            :options="[
                                                                'one_time' => 'One time',
                                                                'day' => 'Day',
                                                                'week' => 'Week',
                                                                'month' => 'Month',
                                                                'quarter' => 'Quarter',
                                                                'year' => 'Year'
                                                            ]"
                                                            :alwaysActive="true" 
                                                        />
                                                    </div>
                                                </div>
                            
                                                {{-- Row 5: Discount Expiration Date --}}
                                                <div class="row">
                                                    <div class="col-md-12 mb-2">
                                                        <x-google-input
                                                            type="date"
                                                            id="discount_expiration_dates"
                                                            name="discount_expiration_dates"
                                                            label="Discount Expiration Date (optional)"
                                                            :value="$price->discount_expiration_date"
                                                            :alwaysActive="true" 
                                                        />
                                                        <div class="form-text">
                                                            If set, the discount will only be shown until this date. After that,
                                                            the original or renewal price is displayed and the discounted price is
                                                            removed from our system.
                                                        </div>
                                                    </div>
                                                </div>
                            
                                            </div>
                                        @endforeach
                                    </div>
                            
                                    @error('prices.*')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            
                </div> 
                <div class="col-md-4 ">
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="card ">
                                    <div class="col-md-12 mt-1 d-flex justify-content-between">
                                        <a href="#" class="btn btn-link text-center"><span><b>View
                                                    Page</b></span></a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn"><span>Update
                                                Product</span></button>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label d-block text-left">Product Status</label>
                                        <div class="d-flex align-items-center justify-content-left">
                                            <div class="custom-control custom-switch">
                                                <!-- Hidden input to ensure "private" is sent if unchecked -->
                                                <input type="hidden" name="status" value="private">

                                                <input type="checkbox" class="custom-control-input"
                                                    id="productStatusSwitch" name="status" value="public"
                                                    {{ $product->status == 'public' ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="productStatusSwitch">Public</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Product Business -->
                                        <div class="form-group mt-3">
                                            {{-- <label class="form-label font-weight-bold">Countries/Regions
                                                Availability</label>
                                            <select class="form-control select2-multiple" name="product_countries[]"
                                                id="product-countries" multiple>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ in_array($country->id, $selectedCountries) ? 'selected' : '' }}>
                                                        {{ $country->name }} ({{ $country->country_code }})
                                                    </option>
                                                @endforeach
                                            </select> --}}

                                            @php
                                            $countryOptions = $countries->mapWithKeys(function($country) {
                                                return [$country->id => $country->name . ' (' . $country->country_code . ')'];
                                            })->toArray();

                                            @endphp
                                            
                                            <x-google-input
                                                type="select"
                                                name="product_countries"
                                                id="product-countries"
                                                label="Countries/Regions Availability"
                                                :options="$countryOptions"
                                                :selectedValues="$selectedCountries"
                                                :alwaysActive="true"
                                                multiple
                                                class="select2-multiple"
                                            />
                                            
                                            @error('product_countries')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            {{-- <label class="form-label font-weight-bold">Linked Business</label>
                                            <select class="form-control product-businesses"     id="product-businesses" name="product_businesses[]"
                                                multiple>
                                                @foreach ($businesses as $business)
                                                    <option value="{{ $business->id }}"
                                                        @if (
                                                            (isset($product_business) && $product_business->contains('id', $business->id)) ||
                                                                in_array($business->id, old('product_businesses', []))) selected @endif>
                                                        {{ $business->translations->first()->name }}
                                                    </option>
                                                @endforeach
                                            </select> --}}

                                            @php
                                                $businessOptions = $businesses->pluck('translations')->mapWithKeys(function ($translations, $id) {
                                                    $name = optional($translations->first())->name ?? 'Business #' . $id;
                                                    return [$translations->first()->business_id => $name];
                                                });
                                            
                                                $selectedBusinessIds = old('product_businesses')
                                                    ?: (isset($product_business) ? $product_business->pluck('id')->values()->all() : []);
                                            @endphp
                                            
                                            <x-google-input
                                                type="select"
                                                name="product_businesses" 
                                                id="product-businesses"
                                                label="Linked Business"
                                                :options="$businessOptions"
                                                :selectedValues="$selectedBusinessIds"
                                                :alwaysActive="true"
                                                multiple
                                                class="product-businesses"
                                            />
                                        
                                        



                                            @error('product_businesses')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mt-3" style="display: none">
                                            <label class="form-label font-weight-bold">Product Category</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="category-display" readonly
                                                    placeholder="Category will be set based on business selection">
                                                <input type="hidden" name="product_category" id="product-category"
                                                    value="{{ old('product_category', isset($product) ? $product->categories->first()->id ?? '' : '') }}">
                                            </div>
                                            @error('product_category')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                   
                                        {{-- <!-- File Upload Section -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold">Product Icon</label>
                                                    <input type="file" class="form-control-file" name="product_icon">
                                                    @if ($product->product_icon)
                                                        <div class="img-preview mt-2">
                                                            <img src="{{ asset($product->product_icon) }}"
                                                                alt="Product Icon" class="img-thumbnail" width="100">
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('product_icon')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold">Product Image</label>
                                                    <input type="file" class="form-control-file" name="product_image">
                                                    @if ($product->product_image)
                                                        <div class="img-preview mt-2">
                                                            <img src="{{ asset($product->product_image) }}"
                                                                alt="Product Image" class="img-thumbnail" width="100">
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('product_image')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="mt-1">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">Product Icon</label>
                                        <input type="file" class="form-control-file" name="product_icon">
                                        @if ($product->product_icon)
                                            <div class="img-preview mt-2">
                                                <img src="{{ asset($product->product_icon) }}"
                                                    alt="Product Icon" class="img-thumbnail" width="100">
                                            </div>
                                        @endif
                                    </div>
                                    @error('product_icon')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">Product Image</label>
                                        <input type="file" class="form-control-file" name="product_image">
                                        @if ($product->product_image)
                                            <div class="img-preview mt-2">
                                                <img src="{{ asset($product->product_image) }}"
                                                    alt="Product Image" class="img-thumbnail" width="100">
                                            </div>
                                        @endif
                                    </div>
                                    @error('product_image')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>                                                
                            </div>                
                        </div>
                    </div>
                </div>
                <div class="col-md-8 mt-4">
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <h5 class="card-title">Product Filters</h5>
                            <div class="filter-container">
                                <div id="filter-container">

                                    <div id="filter-fields">
                                        <!-- Dynamic filter fields will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            $('.product-businesses').select2({
                placeholder: "Select Business",
                maximumSelectionLength: 1,
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <script>
        const currencyOptions = @json(
            $currencies->map(function ($currency) {
                return [
                    'id' => $currency->id,
                    'code' => $currency->code,
                ];
            }));
    </script>
    <script>
        $(document).ready(function() {

            // Real-time price validation
          

            function validatePrices() {


                let isValid = true;
       


                // Reset state
                $('input, select').removeClass('is-invalid is-warning is-valid');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.price-error').remove();

                // $('.invalid-feedback, .price-error').remove();

                const regularPrice = parseFloat($('input[name="prices"]').val()) || 0;
                const discountPrice = parseFloat($('input[name="discount_prices"]').val()) || 0;
                const renewalPrice = parseFloat($('input[name="renewal_prices"]').val()) || 0;
                const discountExpiration = $('#discount_expiration_dates').val();
                const discountTimeUnit = $('select[name="discount_time_units"]').val();
                const renewalTimeUnit = $('select[name="renewal_time_units"]').val();

                // Run discount validations if any discount data is provided
                if (discountPrice > 0 || discountExpiration || discountTimeUnit) {
                    if (discountPrice >= regularPrice) {
                        showPriceError('discount_prices', 'Discount price must be less than regular price');
                        isValid = false;
                    } else {
                        const discountPercentage = ((regularPrice - discountPrice) / regularPrice) * 100;
                        if (discountPercentage < 1) {
                            showPriceError('discount_prices', 'Discount should provide at least 1% savings');
                            isValid = false;
                        } else if (discountPercentage > 99) {
                            showPriceError('discount_prices', 'Discount cannot be more than 99% off');
                            isValid = false;
                        } else {
                            showPriceSuccess('discount_prices', `${discountPercentage.toFixed(0)}% discount`);
                        }
                    }

                    if (!discountExpiration) {
                        showPriceError('discount_expiration_dates', 'Expiration date is required for discount price');
                        isValid = false;
                    } else {
                        const expDate = new Date(discountExpiration);
                        const now = new Date();
                        const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);
                        const twoYearsFromNow = new Date(now.getTime() + 2 * 365 * 24 * 60 * 60 * 1000);

                        if (expDate <= tomorrow) {
                            showPriceError('discount_expiration_dates', 'Expiration must be at least 24 hours from now');
                            isValid = false;
                        } else if (expDate > twoYearsFromNow) {
                            showPriceError('discount_expiration_dates', 'Expiration should not be more than 2 years from now');
                            isValid = false;
                        }
                    }

                    if (!discountTimeUnit) {
                        showPriceError('discount_time_units', 'Time unit is required for discount price');
                        isValid = false;
                    }
                }


                if (renewalPrice > 0) {
                    if (renewalPrice < regularPrice * 0.5) {
                        showPriceWarning('renewal_prices', 'Renewal price seems unusually low. Please verify.');
                    }

                    if (!renewalTimeUnit) {
                        showPriceError('renewal_time_units', 'Time unit is required for renewal price');
                        isValid = false;
                    }
                }

                return isValid;
            }


            function showPriceError(fieldName, message) {
                const field = $(`[name="${fieldName}"]`);
                field.addClass('is-invalid');
                field.closest('.form-group, .input-group, div').append(
                    `<div class="price-error text-danger small mt-1">${message}</div>`);
            }

            function showPriceWarning(fieldName, message) {
                const field = $(`[name="${fieldName}"]`);
                field.addClass('is-warning');
                field.closest('.form-group, .input-group, div').append(
                    `<div class="price-error text-warning small mt-1">${message}</div>`);
            }

            function showPriceSuccess(fieldName, message) {
                const field = $(`[name="${fieldName}"]`);
                field.addClass('is-valid');
                field.closest('.form-group, .input-group, div').append(
                    `<div class="price-error text-success small mt-1">${message}</div>`);
            }

            
            // function updateSubmitButton(isValid) {
            //     const submitBtn = $('#submitBtn');
            //     if (isValid) {
                 
            //         submitBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
            //     } else {
            //            console.log("press");
            //         submitBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
            //     }
            // }

            // Bind validation to input events
            $('input[name="prices"], input[name="discount_prices"], input[name="renewal_prices"]').on('input blur',
                function() {
                    // Remove invalid classes first
                    $(this).removeClass('is-invalid is-valid is-warning');

                    // Debounce validation
                    clearTimeout(window.priceValidationTimer);
                    window.priceValidationTimer = setTimeout(validatePrices, 300);
                });

            $('input[name="discount_expiration_dates"], select[name="discount_time_units"], select[name="renewal_time_units"]')
                .on('change', function() {
                    validatePrices();
                });

            // Form submission validation
            $('#productForm').on('submit', function(e) {
                console.log('Submit triggered');
                const isPriceValid = validatePrices();
                const isFormValid = validateBasicFields();

                console.log('Validation status:', { isPriceValid, isFormValid });
                // Clear any existing validation states
                $('.is-invalid, .is-valid, .is-warning').removeClass('is-invalid is-valid is-warning');
                $('.price-error').remove();

                // Run comprehensive validation
                // const isPriceValid = validatePrices();
                // const isFormValid = validateBasicFields();

                if (!isPriceValid || !isFormValid) {
                    e.preventDefault();

                    // Scroll to first error
                    const firstError = $('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }

                    // Show general error message
                    // showNotification('Please fix the validation errors before submitting.', 'error');
                    return false;
                }

                // Show loading state
                // $('#submitBtn').prop('disabled', true).html(
                //     '<span class="spinner-border spinner-border-sm me-2"></span>Updating...');
            });

            function validateBasicFields() {
                let isValid = true;
                console.log('Debug:');
                console.log('name:', $('input[name="name"]').val());
                console.log('prices:', $('input[name="prices"]').val());
                console.log('currencies:', $('select[name="currencies"]').val());
                console.log('time_units:', $('select[name="time_units"]').val());
                console.log('businesses:', $('select[name="product_businesses[]"]').val());

                // Product name validation
                const productName = $('input[name="name"]').val().trim();
                if (!productName) {
                    showFieldError('name', 'Product name is required');
                    isValid = false;
                } else if (productName.length > 255) {
                    showFieldError('name', 'Product name cannot exceed 255 characters');
                    isValid = false;
                }

                // Regular price validation
                const regularPrice = parseFloat($('input[name="prices"]').val()) || 0;
                if (regularPrice <= 0) {
                    showFieldError('prices', 'Regular price is required and must be greater than 0');
                    isValid = false;
                }

                // Currency validation
                const currency = $('select[name="currencies"]').val();
                if (!currency) {
                    showFieldError('currencies', 'Currency selection is required');
                    isValid = false;
                }

                // Time unit validation
                const timeUnit = $('select[name="time_units"]').val();
                if (!timeUnit) {
                    showFieldError('time_units', 'Time unit selection is required');
                    isValid = false;
                }

                // Business validation
                // const businesses = $('select[name="product_businesses[]"]').val();
                const businesses = ($('#product-businesses').select2('val') || []);



                if (!businesses || businesses.length === 0) {
                    showFieldError('product_businesses[]', 'At least one business must be selected');
                    isValid = false;
                }

                return isValid;
            }

            function showFieldError(fieldName, message) {
                const field = $(`[name="${fieldName}"]`);

                if (!field.length) return;

                field.addClass('is-invalid');

                // Remove any existing error feedback (from previous attempts)
                field.siblings('.invalid-feedback, .text-danger').remove();

                // Append error message right after the field
                field.after(`<div class="invalid-feedback d-block">${message}</div>`);

                // Also add 'error' class to parent wrapper (if styled that way)
                const wrapper = field.closest('.input-box');
                if (wrapper.length) {
                    wrapper.addClass('error');
                }
            }


            function showNotification(message, type = 'info') {
                // Create notification element
                const alertClass = type === 'error' ? 'alert-danger' : 'alert-info';
                const notification = $(`
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                `);

                // Add to body
                $('body').append(notification);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    notification.alert('close');
                }, 5000);
            }

            // File validation
            $('input[type="file"]').on('change', function() {
                const file = this.files[0];
                const fieldName = $(this).attr('name');

                // Remove previous errors
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();

                if (!file) return;

                // Check file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if (!allowedTypes.includes(file.type)) {
                    $(this).addClass('is-invalid');
                    $(this).after(
                        '<div class="invalid-feedback">Please select an image file (JPEG, PNG, JPG, GIF, SVG)</div>'
                    );
                    $(this).val('');
                    return;
                }

                // Check file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    $(this).addClass('is-invalid');
                    $(this).after('<div class="invalid-feedback">Image size must be less than 2MB</div>');
                    $(this).val('');
                    return;
                }

                // Show success state
                $(this).addClass('is-valid');
            });

            // Initialize validation on page load
            setTimeout(validatePrices, 500);

            // Add helpful tooltips
            $('input[name="discount_prices"]').attr('title', 'Discount price must be less than regular price');
            $('input[name="discount_expiration_dates"]').attr('title',
                'Required when discount price is set. Must be at least 24 hours from now.');
            $('input[name="renewal_prices"]').attr('title', 'Optional renewal price after initial period');
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize select2 for multiple selection controls
            if ($.fn.select2) {
                console.log('Select2 is loaded ✅');

           

                // Add select all / clear all buttons
                $('<div class="select2-buttons mt-2">' +
                    '<button type="button" class="btn btn-sm btn-outline-primary select-all-countries me-2">Select All</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-secondary clear-all-countries">Clear All</button>' +
                    '</div>').insertAfter('#product-countries');

                // Button handlers for countries
                $('.select-all-countries').on('click', function() {
                    $('#product-countries option').prop('selected', true);
                    $('#product-countries').trigger('change');
                });

                $('.clear-all-countries').on('click', function() {
                    $('#product-countries option').prop('selected', false);
                    $('#product-countries').trigger('change');
                });
                     // Initialize select2 for country selection
                $('#product-countries').select2({
                    placeholder: "Select Countries/Regions",
                    allowClear: true,
                    width: '100%'
                });
            } else {
                console.error('Select2 is NOT loaded ❌');
            }

            // Status Switch
            $("#productStatusSwitch").on("change", function() {
                if ($(this).is(":checked")) {
                    $(this).next("label").text("Public");
                } else {
                    $(this).next("label").text("Private");
                }
            });
        });
        // ----- BUSINESS AND CATEGORY SECTION -----
        $(document).ready(function() {
            // Cache DOM elements
            const $productCategory = $('#product-category'); // Hidden input
            const $categoryDisplay = $('#category-display'); // Display input
            const $productBusinesses = $('.product-businesses');
            const $filterContainer = $('#filter-container');
            const $filterFields = $('#filter-fields');
            const $categoryMessage = $('#category-message');

            // Initialize selected filters data structure
            // This should be populated from PHP with existing filter selections
            let oldFilterSelections = [];

            // Check if the variable exists in PHP context
            @if (isset($selectedFilters) && !empty($selectedFilters))
                oldFilterSelections = @json($selectedFilters);
            @endif

            // Initialize select2 for dynamic filter selects
            function initializeSelect2Filters() {
                $('.product-filters').select2({
                    placeholder: "Select options",
                    allowClear: true,
                    width: '100%'
                });

                // Add select all / clear all buttons for each filter
                $('.product-filters').each(function() {
                    const filterId = $(this).data('filter-id');
                    const filterWrapperId = `filter-wrapper-${filterId}`;
                });

                // Button handlers for filter options
                $('.select-all-options').off('click').on('click', function() {
                    const filterId = $(this).data('filter-id');
                    $(`select[data-filter-id="${filterId}"] option`).prop('selected', true);
                    $(`select[data-filter-id="${filterId}"]`).trigger('change');
                });

                $('.clear-all-options').off('click').on('click', function() {
                    const filterId = $(this).data('filter-id');
                    $(`select[data-filter-id="${filterId}"] option`).prop('selected', false);
                    $(`select[data-filter-id="${filterId}"]`).trigger('change');
                });
            }

            // Handle business change to update category
            $productBusinesses.on('change', function() {
                const businessId = $(this).val();

                // If array with 1 element, get the first element
                const singleBusinessId = Array.isArray(businessId) ? businessId[0] : businessId;

                if (singleBusinessId) {
                    // Show loading state in category field
                    $categoryDisplay.val('Loading...');

                    // Find the associated category for this business
                    $.ajax({
                        url: "/get-business-category",
                        type: "GET",
                        data: {
                            business_id: singleBusinessId
                        },
                        success: function(response) {
                            if (response.success && response.category_id) {
                                // Set the hidden category input
                                $productCategory.val(response.category_id);

                                // Set display field with category name
                                $categoryDisplay.val(response.category_name);

                                // Load filters for this category
                                loadFilters(response.category_id);
                            } else {
                                // Handle case where no category is found
                                $productCategory.val('');
                                $categoryDisplay.val('No category found');
                                $filterFields.html(
                                    '<div class="alert alert-warning"><em class="icon ni ni-alert-circle"></em> No category associated with this business</div>'
                                );
                                $categoryMessage.show();
                            }
                        },
                        error: function(xhr) {
                            console.error('Error fetching business category:', xhr);
                            $categoryDisplay.val('Error loading category');
                            $filterFields.html(
                                '<div class="alert alert-danger"><em class="icon ni ni-cross-circle"></em> Failed to load category information</div>'
                            );
                        }
                    });
                } else {
                    // Clear category if no business selected
                    $productCategory.val('');
                    $categoryDisplay.val('');
                    $filterFields.html(
                        '<div class="alert alert-info"><em class="icon ni ni-info"></em> Select a business to load available filters</div>'
                    );
                    $categoryMessage.show();
                }
            });

            // Function to load filters for a category
            function loadFilters(categoryId) {
                if (!categoryId) {
                    $filterFields.html(
                        '<div class="alert alert-info"><em class="icon ni ni-info"></em> Select a business to load available filters</div>'
                    );
                    $categoryMessage.show();
                    return;
                }

                // Show loading state
                $filterFields.html(
                    '<div class="d-flex justify-content-center my-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                );

                function renderFilterComponentHtml(filter, selectedValues = []) {
                        const filterName = getFilterName(filter);
                        
                        // Create component-like HTML structure
                        let html = `
                            <div class="input-box mt-0 active" id="filter-wrapper-${filter.id}">
                                <label class="input-label" for="filter-${filter.id}">${filterName}</label>
                                <select class="input-1 product-filters" 
                                        name="filters[${filter.id}][]"
                                        id="filter-${filter.id}"
                                        multiple="multiple" 
                                        data-filter-id="${filter.id}"
                                        onfocus="this.parentNode.classList.add('active')">`;

                        if (filter.options && filter.options.length > 0) {
                            filter.options.forEach(option => {
                                const optionName = option.translations && option.translations.length > 0 ?
                                    option.translations[0].name :
                                    'Option #' + option.id;

                                const isSelected = isOptionSelected(filter.id, option.id) ? 'selected' : '';
                                html += `<option value="${option.id}" ${isSelected}>${optionName}</option>`;
                            });
                        } else {
                            html += `<option disabled>No options available</option>`;
                        }

                        html += `</select>
                                <div class="form-text text-muted">Select applicable ${filterName} options for this product</div>
                            </div>`;

                        return html;
                    }

                // Fetch filters for selected category
                $.ajax({
                    url: "/fetch-filters",
                    type: "GET",
                    data: {
                        categories: categoryId
                    },
                    success: function(response) {
                        if (response.success && response.filters && response.filters.length > 0) {
                let filtersHtml = '';

                response.filters.forEach(filter => {
                    filtersHtml += renderFilterComponentHtml(filter);
                });

                $filterFields.html(filtersHtml);
                initializeSelect2Filters();
                $categoryMessage.hide();
            } else {
                $filterFields.html(`
                    <div class="alert alert-warning">
                        <em class="icon ni ni-alert-circle"></em>
                        No filters available for this category
                    </div>`);
            }
                    },
                    error: function(xhr) {
                        console.error('Error loading filters:', xhr);
                        $filterFields.html(`
                            <div class="alert alert-danger">
                                <em class="icon ni ni-cross-circle"></em>
                                Failed to load filters: ${xhr.statusText || 'Unknown error'}
                            </div>`);
                    }


                });
            }

            function getFilterName(filter) {
                return filter.translations && filter.translations.length > 0 ? 
                    filter.translations[0].name : 
                    'Filter #' + filter.id;
            }
// next

            // Helper function to check if an option was previously selected
            function isOptionSelected(filterId, optionId) {
                if (!Array.isArray(oldFilterSelections) || oldFilterSelections.length === 0) {
                    return false;
                }

                for (const selection of oldFilterSelections) {
                    if (parseInt(selection.filter_id) === parseInt(filterId)) {
                        if (Array.isArray(selection.options)) {
                            for (const option of selection.options) {
                                if (parseInt(option.id) === parseInt(optionId)) {
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            }

            // If a business is already selected, trigger the change event to load filters
            if ($productBusinesses.val() && $productBusinesses.val().length > 0) {
                $productBusinesses.trigger('change');
            } else if ($productCategory.val()) {
                // If we have a category but no business selected, load filters directly
                loadFilters($productCategory.val());
            }
        });

        // ----- FORM VALIDATION -----


        // ----- IMAGE HANDLING -----
        // Validate uploaded images
        $('input[type="file"]').on('change', function(event) {
            let file = event.target.files[0];
            let inputField = $(this);

            // Remove any existing error message
            inputField.siblings('.text-danger').remove();

            if (!file) return;

            // Check file type
            if (!file.type.match('image.*')) {
                inputField.after('<div class="text-danger mt-1">Please select an image file</div>');
                inputField.val('');
                return;
            }

            // Check file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                inputField.after('<div class="text-danger mt-1">Image must be smaller than 2MB</div>');
                inputField.val('');
                return;
            }

            // Show preview
            let reader = new FileReader();
            reader.onload = function(e) {
                // Remove existing preview
                inputField.siblings('.img-preview').remove();

                // Add new preview
                let imgPreview = $('<div class="img-preview mt-2"></div>');
                imgPreview.append(
                    `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" width="100">`);
                imgPreview.append(
                    '<div class="text-center text-soft small mt-1">New image (not saved yet)</div>');

                inputField.after(imgPreview);
            };
            reader.readAsDataURL(file);
        });

        // If a business is already selected, trigger the change event to load filters
        if ($productBusinesses.val()) {
            $productBusinesses.trigger('change');
        }

        // Check for price tenure changes to validate uniqueness
        $(document).on('change', '.price-tenure', function() {
            validateUniquePriceTypes();
        });
        // Enhanced Frontend Validation for Product Form
    </script>
@endsection
