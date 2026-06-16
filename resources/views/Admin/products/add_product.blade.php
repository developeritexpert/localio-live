@extends('admin_layout.master')
@section('content')

    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Product</h4>
            </div>
        </div>

        <form action="{{ route('product-add-procc') }}" class="form-validate" id="productForm" method="post"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="row g-3 mt-2">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {{-- <label class="form-label" for="name">Product Name</label> --}}
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                {{-- <input type="text" class="form-control" name="name" id="name"
                                                    placeholder="Product Name" value="{{ old('name') }}"> --}}

                                                    <x-google-input
                                                    type="text"
                                                    name="name"
                                                    id="name"
                                                    label="Product Name"
                                                    value="{{ old('name') }}"
                                                />


                                            </div>
                                        </div>
                                        @error('name')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">

                                    {{-- <input type="text" class="form-control mt-2" name="product_link" id="product-link"
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
                                    <div class="form-group d-flex align-items-center justify-content-between">
                                        {{-- <label class="form-label" for="product-link">Product Link</label> --}}
                                        <div class=""></div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_affiliate"
                                                name="is_affiliate" value="1"
                                                {{ old('is_affiliate', $is_affiliate ?? 0) ? 'checked' : '' }}>

                                            <label class="form-check-label ms-2" for="is_affiliate">Affiliate</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <input type="hidden" name="lang_code" value="{{ getCurrentLanguageID() }}" />
                        </div>
                    </div>
                        <!-- Product Prices -->
                      <!--  <div class="card card-bordered mb-3">
                            <div class="card-inner">
                                <h5 class="card-title">Product Price</h5>

                                <div id="price-container">
                                    <div class="price-box mb-3  p-3 rounded position-relative">
                                        <div class="">
                                            <label class="form-label">Default Price :</label>
                                        </div>
                                        {{-- <div class="input-group mb-2"> --}}
                                            {{-- <input type="number" class="form-control" name="prices"
                                                placeholder="Enter Price" required> --}}
                                                <x-google-input
                                                    type="number"
                                                    name="prices"
                                                    label="Default Price"
                                                />
                                            {{-- Currency --}}
                                            {{-- <select class="form-select" name="currencies" required>
                                                @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->symbol }}">
                                                        {{ $currency->code }}
                                                    </option>
                                                @endforeach
                                            </select> --}}

                                            <x-google-input
                                            type="select"
                                            name="currencies"

                                            label="Currency"
                                            :alwaysActive="true"
                                            :options="$currencies->pluck('code', 'symbol')->toArray()"
                                            />


                                            <div class="text-danger price-type-error d-none">This price type is already
                                                selected.</div>


                                            {{-- <select class="form-select" name="time_units" required>
                                                <option value="">Time Unit</option>
                                                <option value="one_time">One Time</option>
                                                <option value="day">Day</option>
                                                <option value="week">Week</option>
                                                <option value="month">Month</option>
                                                <option value="quarter">Quarter</option>
                                                <option value="year">Year</option>
                                            </select> --}}

                                            <x-google-input
                                            type="select"
                                            name="time_units"
                                            :alwaysActive="true"
                                            label="Time Unit"
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
                                                placeholder="e.g. per user, per license"> --}}

                                                <x-google-input
                                                type="text"
                                                name="price_descriptions"

                                                label="Price Description"

                                            />


                                               {{-- reneweal price --}}
                                               <div class=" d-flex flex-column" >
                                                    <div class="">
                                                        <label class="form-label">Renewal:</label>
                                                    </div>

                                                        {{-- <input type="number" class="form-control" name="renewal_prices"
                                                            placeholder="Renewal Price (optional)"> --}}

                                                            <x-google-input
                                                            type="number"
                                                            name="renewal_prices"
                                                            label="Renewal Price "
                                                        />


                                                    {{-- <select class="form-select" name="renewal_time_units">
                                                        <option value="">Time Unit</option>
                                                        <option value="one_time">One Time</option>
                                                        <option value="day">Day</option>
                                                        <option value="week">Week</option>
                                                        <option value="month">Month</option>
                                                        <option value="quarter">Quarter</option>
                                                        <option value="year">Year</option>
                                                    </select> --}}

                                                    <x-google-input
                                                        type="select"
                                                        name="renewal_time_units"

                                                        label="Time Unit"

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

                                        {{-- discount --}}
                                        <div class="input-group mb-2 d-flex flex-column">
                                            <div class="">
                                                <label class="form-label">Discount:</label>
                                            </div>

                                                {{-- <input type="number" class="form-control" name="discount_prices"
                                                    placeholder="Discount Price (optional)"> --}}

                                                <x-google-input
                                                    type="number"
                                                    name="discount_prices"

                                                    label="Discount Price (optional)"
                                                />

                                                {{-- <select class="form-select" name="discount_time_units">
                                                    <option value="">Time Unit</option>
                                                    <option value="one_time">One Time</option>
                                                    <option value="day">Day</option>
                                                    <option value="week">Week</option>
                                                    <option value="month">Month</option>
                                                    <option value="quarter">Quarter</option>
                                                    <option value="year">Year</option>
                                                </select> --}}

                                                <x-google-input
                                                type="select"
                                                name="discount_time_units"

                                                label="Time Unit"

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

                                        <div class="mb-2">
                                            {{-- <label class="form-label">Discount Expiration Date (optional):</label> --}}
                                            {{-- <input type="date" class="form-control" name="discount_expiration_dates"> --}}
                                            <x-google-input
                                                type="date"
                                                id="discount_expiration_dates"
                                                name="discount_expiration_dates"
                                                label="Discount Expiration Date (optional)"

                                                :alwaysActive="true"
                                            />
                                            <div class="form-text">
                                                If set, the discount will only be shown until this date. After that, the
                                                original or renewal price is displayed and the discounted price is removed
                                                from our system.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                @error('prices.*')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> -->

                        <!-- Product Prices -->
<!-- Product Prices -->
<div class="card card-bordered mb-3">
    <div class="card-inner">
        <h5 class="card-title">Product Price</h5>

        <div id="price-container">
            <div class="price-box mb-3 p-3 rounded position-relative">

                {{-- Row 1: Label + Default Price + Currency --}}
                <div class="mb-2">
                    <label class="form-label">Default Price</label>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-google-input
                            type="number"
                            name="prices"
                            label="Default Price"
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-google-input
                            type="select"
                            name="currencies"
                            label="Currency"
                            :alwaysActive="true"
                            :options="$currencies->pluck('code', 'symbol')->toArray()"
                        />
                    </div>
                </div>

                {{-- Row 2: No Label - Time Unit + Price Description --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-google-input
                            type="select"
                            name="time_units"
                            label="Time Unit"
                            :alwaysActive="true"
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
                        />
                    </div>
                </div>
                <hr style="border-top:1px solid #dee2e6; opacity:1; margin:20px 0;">
                {{-- Row 3: Label + Renewal Price + Time Unit --}}
                <div class="mb-2">
                    <label class="form-label">Renewal</label>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-google-input
                            type="number"
                            name="renewal_prices"
                            label="Renewal Price"
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-google-input
                            type="select"
                            name="renewal_time_units"
                            label="Time Unit"
                            :alwaysActive="true"
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
                </div>
                <hr style="border-top:1px solid #dee2e6; opacity:1; margin:20px 0;">
                {{-- Row 4: Label + Discount Price + Time Unit --}}
                <div class="mb-2">
                    <label class="form-label">Discount</label>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-google-input
                            type="number"
                            name="discount_prices"
                            label="Discount Price (optional)"
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-google-input
                            type="select"
                            name="discount_time_units"
                            label="Time Unit"
                            :alwaysActive="true"
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
                </div>

                {{-- Discount Expiration Date --}}
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <x-google-input
                            type="date"
                            id="discount_expiration_dates"
                            name="discount_expiration_dates"
                            label="Discount Expiration Date (optional)"
                            :alwaysActive="true"
                        />
                        <div class="form-text">
                            If set, the discount will only be shown until this date. After that,
                            the original or renewal price is displayed and the discounted price is removed from our system.
                        </div>
                    </div>
                </div>

                {{-- Validation --}}
                <div class="text-danger price-type-error d-none">
                    This price type is already selected.
                </div>

            </div>
        </div>

        @error('prices.*')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>



                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <h5 class="card-title">Product Filters</h5>
                            <div class="filter-container">
                                <!-- Filter Selection Area -->
                                <div id="filter-container">
                                    <div id="category-message" class="alert alert-info">
                                        <em class="icon ni ni-info"></em>
                                        Select a Business to load available filters
                                    </div>
                                    <div id="filter-fields">
                                        <!-- Dynamic filter fields will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="card shadow-sm border-0">
                                    <div class="col-md-12 mt-1 d-flex justify-content-between">
                                        <a href="#" class="btn btn-link text-center"><span><b>View
                                                    Page</b></span></a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn"><span>Add
                                                Product</span></button>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label d-block text-left">Product Status</label>
                                        <div class="d-flex align-items-center justify-content-left">
                                            <div class="custom-control custom-switch">
                                                <!-- Hidden input to ensure "draft" is sent if unchecked -->
                                                <input type="hidden" name="status" value="private">

                                                <input type="checkbox" class="custom-control-input"
                                                    id="productStatusSwitch" name="status" value="public" checked>
                                                <label class="custom-control-label"
                                                    for="productStatusSwitch">Public</label>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="card-body">

                                        {{-- Add Link Business Dropdown --}}
                                        <div class="form-group">
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
                                            label="Linked Business"
                                            :options="$businessOptions"
                                            :selectedValues="$selectedBusinessIds"
                                            :alwaysActive="true"
                                            multiple
                                            class="select2-multiple product-businesses"
                                        />
                                            @error('product_businesses')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        {{--  End Link Business Dropdown --}}

                                        <!-- Product Business -->
                                        <div class="form-group mt-3">
                                            {{-- <label class="form-label font-weight-bold">Countries/Regions
                                                Availability</label>
                                            <select class="form-control select2-multiple" name="product_countries[]"
                                                id="product-countries" multiple>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ in_array($country->id, old('product_countries', [])) ? 'selected' : '' }}>
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

                                                :alwaysActive="true"
                                                multiple
                                                class="select2-multiple"
                                            />
                                            @error('product_countries')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- comments This  --}}
                                        {{-- <div class="form-group">
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
                                            label="Linked Business"
                                            :options="$businessOptions"
                                            :selectedValues="$selectedBusinessIds"
                                            :alwaysActive="true"
                                            multiple
                                            class="select2-multiple product-businesses"
                                        />
                                            @error('product_businesses')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div> --}}
                                        {{-- End comments --}}

                                        {{-- {{dd($categories);}} --}}
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class=" mt-1">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">Product Icon</label>
                                        <input type="file" class="form-control-file" name="product_icon">
                                        @if (old('product_icon_path'))
                                            <div class="img-preview mt-2">
                                                <img src="{{ asset(old('product_icon_path')) }}" class="img-thumbnail"
                                                    width="100">
                                            </div>
                                        @endif
                                    </div>
                                    @error('product_icon')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">Product
                                            Image</label>
                                        <input type="file" class="form-control-file" name="product_image">
                                        @if (old('product_image_path'))
                                            <div class="img-preview mt-2">
                                                <img src="{{ asset(old('product_image_path')) }}" class="img-thumbnail"
                                                    width="100">
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
        </form>
    </div>
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
            // Check if select2 is available
            if ($.fn.select2) {
                console.log('Select2 is loaded ✅');



                // Add select all / clear all buttons
                $('<div class="select2-buttons mt-2">' +
                    '<button type="button" class="btn btn-sm btn-outline-primary select-all-countries me-2">Select All</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-secondary clear-all-countries">Clear All</button>' +
                    '</div>').insertAfter('#product-countries');

                // Button handlers
                $('.select-all-countries').on('click', function() {
                    $('#product-countries option').prop('selected', true);
                    $('#product-countries').trigger('change');
                });

                $('.clear-all-countries').on('click', function() {
                    $('#product-countries option').prop('selected', false);
                    $('#product-countries').trigger('change');
                });
                        // Initialize select2
                $('#product-countries').select2({
                    placeholder: "Select Countries/Regions ",
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
    </script>

    <script>
        $(document).ready(function() {
            // Initialize select2 for business dropdown (single select)
            $('.product-businesses').select2({
                placeholder: "Select Business",
                maximumSelectionLength: 1, // Limit to one selection
                allowClear: true,
                width: '100%'
            });

            // Cache DOM elements
            const $productCategory = $('#product-category'); // Hidden input
            const $categoryDisplay = $('#category-display'); // Display input
            const $productBusinesses = $('.product-businesses');
            const $filterContainer = $('#filter-container');
            const $filterFields = $('#filter-fields');

                        // This should be populated from PHP with existing filter selections
                        let oldFilterSelections = [];

        // Check if the variable exists in PHP context
        @if (isset($selectedFilters) && !empty($selectedFilters))
            oldFilterSelections = @json($selectedFilters);
        @endif

            // Initialize select2 for dynamic filter selects
            function initializeSelect2() {
                $('.product-filters').select2({
                    placeholder: "Select options",
                    allowClear: true
                });
            }

            // Handle business change to update category
            $productBusinesses.on('change', function() {
                const businessId = $(this).val();

                // If array with 1 element, get the first element
                const singleBusinessId = Array.isArray(businessId) ? businessId[0] : businessId;

                if (singleBusinessId) {
                    // Find the associated category for this business
                    $.ajax({
                        url: "/get-business-category", // You'll need to create this endpoint
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
                            }
                        }
                    });
                } else {
                    // Clear category if no business selected
                    $productCategory.val('');
                    $categoryDisplay.val('');
                    $filterFields.html(
                        '<div class="alert alert-info"><em class="icon ni ni-info"></em> Select a business to load available filters</div>'
                    );
                }
            });

            // Function to load filters for a category
            function loadFilters(categoryId) {
                if (!categoryId) {
                    $filterFields.html(
                        '<div class="alert alert-info"><em class="icon ni ni-info"></em> Select a business to load available filters</div>'
                    );
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
                // $.ajax({
                //     url: "/fetch-filters",
                //     type: "GET",
                //     data: {
                //         categories: categoryId
                //     },
                //     success: function(response) {
                //         if (response.success && response.filters.length > 0) {
                //             let filtersHtml = '';

                //             response.filters.forEach(filter => {
                //                 const filterName = filter.translations.length ? filter
                //                     .translations[0].name : 'Unknown Filter';

                //                 filtersHtml +=
                //                     `
                //     <div class="form-group mb-3">
                //         <label class="form-label font-weight-bold">${filterName}</label>
                //         <select class="form-control product-filters" name="filters[${filter.id}][]" multiple="multiple" data-filter-id="${filter.id}">`;

                //                 if (filter.options && filter.options.length > 0) {
                //                     filter.options.forEach(option => {
                //                         const optionName = option.translations
                //                             .length ? option.translations[0]
                //                             .name : 'Unknown Option';
                //                         filtersHtml +=
                //                             `<option value="${option.id}">${optionName}</option>`;
                //                     });
                //                 } else {
                //                     filtersHtml +=
                //                         `<option disabled>No options available</option>`;
                //                 }

                //                 filtersHtml += `</select>
                //         <div class="form-text text-muted">Select applicable ${filterName} options for this product</div>
                //     </div>`;
                //             });

                //             $filterFields.html(filtersHtml);

                //             // Initialize Select2 for the newly added filter fields
                //             initializeSelect2();
                //             $('#category-message').hide();
                //             // If there are old filter selections (from validation error), restore them
                //             restoreOldFilterSelections();
                //         } else {
                //             $filterFields.html(`
                //     <div class="alert alert-warning">
                //         <em class="icon ni ni-alert-circle"></em>
                //         No filters available for this category
                //     </div>`);
                //             }
                //         },
                //         error: function(xhr) {
                //             $filterFields.html(`
                //     <div class="alert alert-danger">
                //         <em class="icon ni ni-cross-circle"></em>
                //         Failed to load filters: ${xhr.statusText}
                //     </div>`);
                //             }
                //         });
                // }
                $.ajax({
                    url: "/fetch-filters",
                    type: "GET",
                    data: { categories: categoryId },
                    success: function(response) {
                        if (response.success && response.filters.length > 0) {
                            let filtersHtml = '';

                            response.filters.forEach(filter => {
                                filtersHtml += renderFilterComponentHtml(filter, []);
                            });

                            $filterFields.html(filtersHtml);
                            initializeSelect2(); // For Select2 styling
                            $('#category-message').hide();
                        } else {
                            $filterFields.html(`<div class="alert alert-warning"><em class="icon ni ni-alert-circle"></em>No filters available for this category</div>`);
                        }
                    },
                    error: function(xhr) {
                        $filterFields.html(`<div class="alert alert-danger"><em class="icon ni ni-cross-circle"></em>Failed to load filters: ${xhr.statusText}</div>`);
                    }
                });
            }
            function getFilterName(filter) {
                return filter.translations && filter.translations.length > 0 ?
                    filter.translations[0].name :
                    'Filter #' + filter.id;
            }

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


            // Function to restore old filter selections after validation errors
            function restoreOldFilterSelections() {
                // This would need server-side data to be implemented fully
                // You can pass old filter selections as JSON from the controller
                // Similar to how it's done in the update page

                // Example implementation if you had oldFilterSelections data:
                // if (typeof oldFilterSelections !== 'undefined' && oldFilterSelections.length > 0) {
                //     oldFilterSelections.forEach(function(item) {
                //         const selectElement = $(`select[name="filters[${item.filter_id}][]"]`);
                //         if (selectElement.length && item.options && item.options.length > 0) {
                //             const optionIds = item.options.map(option => option.id);
                //             selectElement.val(optionIds).trigger('change');
                //         }
                //     });
                // }
            }

            // If business is already selected (on page load), trigger change
            if ($productBusinesses.val() && $productBusinesses.val().length > 0) {
                $productBusinesses.trigger('change');
            }
        });
    </script>

    <script>
        // Initialize form elements on document ready
        $(document).ready(function() {
            function getCurrencySelectHTML() {
                let options = '<option value="">Currency</option>';
                currencyOptions.forEach(currency => {
                    options += `<option value="${currency.code}">${currency.code}</option>`;
                });
                return `<select class="form-select " name="currencies[]" required>${options}</select>`;
            }

            // Only show base price error when form is submitted, not on page load
            const basePriceError = $('#basePriceError');
            basePriceError.hide(); // Hide error on initial load

            // Add price row handler
            $('#add-price').on('click', function() {
                addPriceRow();
            });

            // Remove price row handler (delegated event)
            $(document).on('click', '.remove-price', function() {
                $(this).closest('.input-group').remove();
            });

            // Form submission validation
            $('#productForm').on('submit', function(e) {
                // Image upload validation and preview
                $('input[type="file"]').on('change', function(event) {
                    validateImageUpload($(this), event);
                });
                // Update hidden input when the switch is toggled
                $("#businessStatusSwitch").on("change", function() {
                    this.previousElementSibling.value = this.checked ? "public" : "private";
                });
            });

            // Add a new price row
            function addPriceRow() {
                let container = $('#price-container');
                let priceRow = `
    <div class="price-box mb-3 border p-3 rounded bg-light position-relative">
<button type="button"
        class="btn-close position-absolute top-0 end-0 m-2 remove-price"
        style="z-index: 10;"
        aria-label="Close">
</button>

        <div class="input-group mb-2">
            <input type="number" class="form-control" name="prices[]" placeholder="Enter Price" required>
            ${getCurrencySelectHTML()}

            <div class="text-danger price-type-error d-none">This price type is already selected.</div>
        </div>

        <div class="input-group mb-2">
            <select class="form-select" name="time_units[]" required>
                <option value="">Time Unit</option>
                <option value="one_time">One Time</option>
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
                <option value="quarter">Quarter</option>
                <option value="year">Year</option>
            </select>
            <input type="text" class="form-control" name="price_descriptions[]" placeholder="e.g. per user, per license">
        </div>

        <div class="input-group mb-2">
            <input type="number" class="form-control" name="discount_prices[]" placeholder="Discount Price (optional)">
            <select class="form-select" name="discount_time_units[]">
                <option value="">Time Unit</option>
                <option value="one_time">One Time</option>
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
                <option value="quarter">Quarter</option>
                <option value="year">Year</option>
            </select>
        </div>

        <div class="mb-2">
            <label class="form-label">Discount Expiration Date (optional):</label>
            <input type="date" class="form-control" name="discount_expiration_dates[]">
            <div class="form-text">
                If set, the discount will only be shown until this date. After that, the original or renewal price is displayed and the discounted price is removed from our system.
            </div>
        </div>

        <div class="input-group mb-2">
            <input type="number" class="form-control" name="renewal_prices[]" placeholder="Renewal Price (optional)">
            <select class="form-select" name="renewal_time_units[]">
                <option value="">Time Unit</option>
                <option value="one_time">One Time</option>
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
                <option value="quarter">Quarter</option>
                <option value="year">Year</option>
            </select>
        </div>
    </div>`;
                container.append(priceRow);
            }


            // Validate uploaded images
            function validateImageUpload(input, event) {
                let file = event.target.files[0];
                let errorContainer = input.siblings(".text-danger");

                // Remove previous error message
                errorContainer.remove();

                if (file) {
                    // Validate file type
                    if (!file.type.startsWith("image/")) {
                        input.after('<div class="text-danger mt-1">Please upload a valid image file</div>');
                        input.val("");
                        return;
                    }

                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        input.after('<div class="text-danger mt-1">File size must be less than 2MB</div>');
                        input.val("");
                        return;
                    }

                    // Show preview
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        // Remove existing preview if any
                        input.siblings(".img-preview").remove();

                        input.parent().append(`
                        <div class="img-preview mt-2">
                            <img src="${e.target.result}" class="img-thumbnail" width="100">
                            <div class="text-center text-soft small mt-1">New image (not saved yet)</div>
                        </div>
                    `);
                    };
                    reader.readAsDataURL(file);
                }
            }
            $(document).on('click', '.remove-price', function() {
                $(this).closest('.price-box').remove();
            });

        });

        // Enhanced form validation JavaScript
        $(document).ready(function() {
            // Real-time price validation
            function validatePricing() {
                const price = parseFloat($('input[name="prices"]').val()) || 0;
                const discountPrice = parseFloat($('input[name="discount_prices"]').val()) || 0;
                const renewalPrice = parseFloat($('input[name="renewal_prices"]').val()) || 0;
                const timeUnit = $('select[name="time_units"]').val();
                const discountExpiry = $('input[name="discount_expiration_dates"]').val();

                // Clear previous error messages
                $('.price-validation-error').remove();

                let hasErrors = false;

                // Validate discount price
                if (discountPrice > 0) {
                    if (discountPrice >= price) {
                        $('input[name="discount_prices"]').after(
                            '<div class="price-validation-error text-danger small mt-1">Discount price must be less than regular price ($' +
                            price + ')</div>'
                        );
                        hasErrors = true;
                    }

                    // Require expiration date for discount
                    if (!discountExpiry) {
                        $('input[name="discount_expiration_dates"]').after(
                            '<div class="price-validation-error text-danger small mt-1">Expiration date is required when discount price is set</div>'
                        );
                        hasErrors = true;
                    }
                }
                // Validate renewal time unit
                if (renewalPrice > 0 && !$('select[name="renewal_time_units"]').val()) {
                    $('select[name="renewal_time_units"]').after(
                        '<div class="price-validation-error text-danger small mt-1">Time unit is required when renewal price is set</div>'
                    );
                    hasErrors = true;
                }
                // Warn if renewal price is significantly higher
                if (renewalPrice > 0 && price > 0 && renewalPrice > (price * 1.5)) {
                    $('input[name="renewal_prices"]').after(
                        '<div class="price-validation-error text-warning small mt-1">Renewal price seems high compared to regular price</div>'
                    );
                }
                return !hasErrors;
            }
            // Validate expiration date
            function validateExpirationDate() {
                const expiryInput = $('input[name="discount_expiration_dates"]');
                const expiryDate = new Date(expiryInput.val());
                const today = new Date();
                const maxDate = new Date();
                maxDate.setFullYear(today.getFullYear() + 2);

                $('.expiry-validation-error').remove();

                if (expiryInput.val()) {
                    if (expiryDate <= today) {
                        expiryInput.after(
                            '<div class="expiry-validation-error text-danger small mt-1">Expiration date must be in the future</div>'
                        );
                        return false;
                    }

                    if (expiryDate > maxDate) {
                        expiryInput.after(
                            '<div class="expiry-validation-error text-warning small mt-1">Expiration date is more than 2 years in the future</div>'
                        );
                    }
                }

                return true;
            }

            // Real-time validation on input changes
            $('input[name="prices"], input[name="discount_prices"], input[name="renewal_prices"]').on('input blur',
                function() {
                    setTimeout(validatePricing, 100); // Small delay to ensure value is updated
                });

            $('select[name="time_units"], select[name="renewal_time_units"]').on('change', validatePricing);

            $('input[name="discount_expiration_dates"]').on('change blur', validateExpirationDate);

            // Form submission validation
            $('#productForm').on('submit', function(e) {
                let isValid = true;

                // Run all validations
                if (!validatePricing()) {
                    isValid = false;
                }

                if (!validateExpirationDate()) {
                    isValid = false;
                }

                // Additional form validations
                const requiredFields = [{
                        field: 'input[name="name"]',
                        message: 'Product name is required'
                    },
                    {
                        field: 'input[name="product_link"]',
                        message: 'Product link is required'
                    },
                    {
                        field: 'input[name="prices"]',
                        message: 'Price is required'
                    },
                    {
                        field: 'select[name="currencies"]',
                        message: 'Currency is required'
                    },
                    {
                        field: 'select[name="time_units"]',
                        message: 'Time unit is required'
                    },
                    {
                        field: 'select[name="product_businesses[]"]',
                        message: 'At least one business must be selected'
                    }
                ];

                $('.required-field-error').remove();

                requiredFields.forEach(function(item) {
                    const field = $(item.field);
                    const value = field.val();

                    if (!value || (Array.isArray(value) && value.length === 0)) {
                        field.after(
                            `<div class="required-field-error text-danger small mt-1">${item.message}</div>`
                        );
                        isValid = false;
                    }
                });

                // Validate URL format
                const productLink = $('input[name="product_link"]').val();
                if (productLink && !isValidURL(productLink)) {
                    $('.url-validation-error').remove();
                    $('input[name="product_link"]').after(
                        '<div class="url-validation-error text-danger small mt-1">Please enter a valid URL</div>'
                    );
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();

                    // Scroll to first error
                    const firstError = $('.text-danger').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                }
            });

            // Helper function to validate URL
            function isValidURL(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }

            // Show notification function
            function showNotification(message, type = 'info') {
                // Remove existing notifications
                $('.form-notification').remove();

                const alertClass = type === 'error' ? 'alert-danger' : 'alert-info';
                const notification = `
            <div class="form-notification alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

                $('#productForm').prepend(notification);

                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $('.form-notification').fadeOut();
                }, 5000);
            }

            // Enhanced discount price input with suggestions
            $('input[name="discount_prices"]').on('input', function() {
                const discountPrice = parseFloat($(this).val()) || 0;
                const regularPrice = parseFloat($('input[name="prices"]').val()) || 0;

                $('.discount-suggestion').remove();

                if (discountPrice > 0 && regularPrice > 0) {
                    const discountPercent = Math.round(((regularPrice - discountPrice) / regularPrice) *
                        100);

                    if (discountPercent > 0) {
                        $(this).after(`
                    <div class="discount-suggestion text-info small mt-1">
                        This represents a ${discountPercent}% discount
                    </div>
                `);
                    }
                }
            });

            // Auto-suggest expiration date when discount is entered
            $('input[name="discount_prices"]').on('blur', function() {
                const discountPrice = parseFloat($(this).val()) || 0;
                const expirationInput = $('input[name="discount_expiration_dates"]');

                if (discountPrice > 0 && !expirationInput.val()) {
                    // Suggest 30 days from now
                    const suggestedDate = new Date();
                    suggestedDate.setDate(suggestedDate.getDate() + 30);
                    const formattedDate = suggestedDate.toISOString().split('T')[0];

                    expirationInput.val(formattedDate);
                    expirationInput.focus();
                }
            });

            // Price formatting on blur
            $('input[name="prices"], input[name="discount_prices"], input[name="renewal_prices"]').on('blur',
                function() {
                    const value = parseFloat($(this).val());
                    if (!isNaN(value) && value > 0) {
                        $(this).val(value.toFixed(2));
                    }
                });
        });
    </script>
@endsection
