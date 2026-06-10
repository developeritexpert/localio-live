<div>
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ $isEdit ? 'Edit' : 'Create' }} Exclusive Deal</h3>
                </div>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner">
                <form wire:submit.prevent="save">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="applies-type">Applies To Type</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="applies-type" wire:model.live="appliesType">
                                        <option value="product">Product</option>
                                        <option value="category">Category</option>
                                    </select>
                                </div>
                                @error('appliesType') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="applies-id">Select {{ ucfirst($appliesType) }}</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="applies-id" wire:model="appliesId">
                                        <option value="">Select {{ ucfirst($appliesType) }}</option>
                                        @if($appliesType == 'product')
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        @elseif($appliesType == 'category')
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @error('appliesId') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="country-code">Country Code (ISO Format)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="country-code" placeholder="e.g. US, CA" wire:model="countryCode">
                                </div>
                                @error('countryCode') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="price-type">Price Type</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="price-type" wire:model="priceType">
                                        <option value="base_price">Base Price</option>
                                        <option value="standard_price">Standard Price</option>
                                        <option value="pro_price">Pro Price</option>
                                    </select>
                                </div>
                                @error('priceType') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="discount-percent">Discount Percentage</label>
                                <div class="form-control-wrap">
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discount-percent" min="0" max="100" step="0.01" placeholder="0.00" wire:model="discountPercent">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                @error('discountPercent') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="status">Status</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="status" wire:model="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="starts-at">Start Date</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" id="starts-at" wire:model="startsAt">
                                </div>
                                @error('startsAt') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="ends-at">End Date</label>
                                <div class="form-control-wrap">
                                    <input type="date" class="form-control" id="ends-at" wire:model="endsAt">
                                </div>
                                @error('endsAt') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-localio">{{ $isEdit ? 'Update' : 'Create' }} Deal</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>