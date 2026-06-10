<div class="col-lg-9 p-0">
    <div class="user_content">
       <div class="new-listing">
        <h1>{{ __('file.add-new-list') }}</h1>
     
            @php
                use App\Models\Currency;
            @endphp

        <div class="new-form mb-2">
            {{-- Product Name and Link --}}
            <div class="form-block">
                <label for="product-name">{{ __('file.Product_name') }}</label>
                <input type="text" wire:model.defer="product_name" class="form-control" id="product-name" placeholder="Add here">
                @error('product_name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-block">
                <label for="product-link">Product Link</label>
                <input type="text" wire:model.defer="product_link" class="form-control" id="product-link" placeholder="Add here">
                @error('product_link') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            {{-- <div class="form-group d-flex align-items-center justify-content-between">
                <div></div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_affiliate" wire:model.defer="is_affiliate">
                    <label class="form-check-label ms-2" for="is_affiliate">Affiliate</label>
                </div>
            </div> --}}
        </div>

        <div class="new-form mb-2">
            {{-- Pricing Fields --}}
            <div class="form-block">
                <label class="form-label">Default Price :</label>
                <input type="number" wire:model.defer="price" class="form-control" placeholder="Enter Price">
                @error('price') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-block">
                <label class="form-label">Currency</label>
                <select class="form-select" wire:model.defer="selected_currency" style="height: auto">
                    @foreach($currencies as $symbol => $code)
                        <option value="{{ $symbol }}">{{ $code }}</option>
                    @endforeach
                </select>
                @error('selected_currency') <div class="text-danger">{{ $message }}</div> @enderror
            </div>


            <div class="form-block">
                <label class="form-label">Time Unit</label>
                <select class="form-select" wire:model.defer="time_unit" style="height: auto">
                    <option value="">Time Unit</option>
                    <option value="one_time">One Time</option>
                    <option value="day">Day</option>
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                    <option value="quarter">Quarter</option>
                    <option value="year">Year</option>
                </select>
            </div>

            <div class="form-block">
                <label class="form-label">Price Description</label>
                <input type="text" wire:model.defer="additional_info" class="form-control" placeholder="e.g. per user, per license">
            </div>

            {{-- Renewal --}}
            <div class="form-block">
                <label class="form-label">Renewal:</label>
                <input type="number" wire:model.defer="renewal_price" class="form-control" placeholder="Renewal Price (optional)">
            </div>

            <div class="form-block">
                <label class="form-label">Renewal Time Unit</label>
                <select class="form-select" wire:model.defer="renewal_time_unit" style="height: auto">
                    <option value="">Time Unit</option>
                    <option value="one_time">One Time</option>
                    <option value="day">Day</option>
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                    <option value="quarter">Quarter</option>
                    <option value="year">Year</option>
                </select>
            </div>

            {{-- Discount --}}
            <div class="form-block">
                <label class="form-label">Discount:</label>
                <input type="number" wire:model.defer="discount_price" class="form-control" placeholder="Discount Price (optional)">
            </div>

            <div class="form-block">
                <label class="form-label">Discount Time Unit</label>
                <select class="form-select" wire:model.defer="discount_time_unit" style="height: auto">
                    <option value="">Time Unit</option>
                    <option value="one_time">One Time</option>
                    <option value="day">Day</option>
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                    <option value="quarter">Quarter</option>
                    <option value="year">Year</option>
                </select>
            </div>

            <div class="form-block">
                <label class="form-label">Discount Expiration Date (optional):</label>
                <input type="date" wire:model.defer="discount_expiration_date" class="form-control">
            </div>
        </div>

        {{-- <div class="new-form mb-2">
          
            <div class="form-block">
                <label>Product Icon</label>
                <input type="file" wire:model="product_icon" class="form-control">
            </div>

            <div class="form-block">
                <label>Product Image</label>
                <input type="file" wire:model="product_image" class="form-control">
            </div>
        </div> --}}

        {{-- You can call a method on button click to store this: --}}

            <div class="new-btn">
                <button class="btn unq_btn" wire:click="submitForApproval">Save Product</button>
            </div>

     
       </div>
    </div>
</div>

