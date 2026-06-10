<div>
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">{{ $isEditing ? 'Edit Product' : 'Add Product' }}</h4>
                <div class="nk-block-des text-soft">
                    <p>{{ $isEditing ? 'Update product information and settings' : 'Create a new product' }}</p>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="save" class="form-validate" id="productForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Product Name</label>
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" wire:model="name" id="name"
                                                    placeholder="Product Name">
                                            </div>
                                        </div>
                                        @error('name')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label class="form-label" for="product-link">Affiliate Link</label>
                                        <input type="text" class="form-control" wire:model="product_link" id="product-link"
                                            placeholder="Affiliate Link">
                                        @error('product_link')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label class="form-label" for="overview">Product Overview</label>
                                        <div wire:ignore>
                                            <textarea class="description form-control" id="overview" rows="3">{{ $overview }}</textarea>
                                        </div>
                                        @error('overview')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label class="form-label" for="description">Product Description</label>
                                        <div wire:ignore>
                                            <textarea class="description form-control" id="description" rows="5">{{ $description }}</textarea>
                                        </div>
                                        @error('description')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" wire:model="lang_code" />
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="card shadow-sm border-0">
                                    <div class="col-md-12 mt-1 d-flex justify-content-between">
                                        @if($isEditing && $product)
                                            <a href="#" class="btn btn-link text-center"><span><b>View Page</b></span></a>
                                        @else
                                            <div></div>
                                        @endif
                                        <button type="submit" class="btn btn-primary" id="submitBtn" wire:loading.attr="disabled" wire:target="save">
                                            <span wire:loading.remove wire:target="save">
                                                {{ $isEditing ? 'Update Product' : 'Add Product' }}
                                            </span>
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <!-- Product Business -->
                                        <div class="form-group">
                                            <label class="form-label font-weight-bold">Product Business</label>
                                            <div wire:ignore>
                                                <select class="form-control" id="product-businesses" multiple="multiple">
                                                    @foreach ($businesses as $business)
                                                        <option value="{{ $business->id }}"
                                                            {{ in_array($business->id, $product_businesses) ? 'selected' : '' }}>
                                                            {{ $business->translations->first()->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('product_businesses')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Product Category -->
                                        <div class="form-group mt-3">
                                            <label class="form-label font-weight-bold">Product Category</label>
                                            <div wire:ignore>
                                                <select class="form-control" id="product-category" multiple="multiple">
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ in_array($category->id, $product_category) ? 'selected' : '' }}>
                                                            {{ isset($category->categoryTranslations) && $category->categoryTranslations->first() 
                                                                ? $category->categoryTranslations->first()->name 
                                                                : ($category->translations->first()->name ?? $category->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('product_category')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Product Prices -->
                                        <h5 class="mt-4 font-weight-bold">Product Prices</h5>
                                        <div id="price-container">
                                            @foreach ($prices as $index => $price)
                                                <div class="input-group mb-2">
                                                    <input type="number" step="0.01" class="form-control" wire:model.defer="prices.{{ $index }}"
                                                        placeholder="Enter Price" required>
                                                    <select class="form-select price-tenure" wire:model.defer="tenures.{{ $index }}" required>
                                                        <option value="">Select Price Type</option>
                                                        <option value="base_price">Base Price</option>
                                                        <option value="standard_price">Standard Price</option>
                                                        <option value="pro_price">Pro Price</option>
                                                    </select>
                                                    <button type="button" class="btn btn-danger" 
                                                        wire:click="removePriceRow({{ $index }})">X</button>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('prices.*')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                        @error('tenures.*')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                        @error('basePriceError')
                                            <div class="text-danger mt-2" id="basePriceError">
                                                <em class="icon ni ni-alert-circle"></em> {{ $message }}
                                            </div>
                                        @enderror
                                        <button type="button" wire:click="addPriceRow" class="btn btn-outline-primary mt-2">
                                            <i class="fas fa-plus"></i> Add Price
                                        </button>

                                        <!-- File Upload Section -->
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold">Product Icon</label>
                                                    <input type="file" class="form-control-file" wire:model="product_icon" id="product-icon" accept="image/*">
                                                    
                                                    @if ($isUploadingIcon)
                                                        <div class="spinner-border spinner-border-sm text-primary mt-2" role="status">
                                                            <span class="visually-hidden">Uploading...</span>
                                                        </div>
                                                        <small class="text-muted">Uploading...</small>
                                                    @endif
                                                    
                                                    @if ($product_icon)
                                                        <div class="img-preview mt-2">
                                                            <img src="{{ $product_icon->temporaryUrl() }}" class="img-thumbnail" width="100">
                                                        </div>
                                                    @elseif ($existingIcon)
                                                        <div class="img-preview mt-2">
                                                            <img src="{{ asset($existingIcon) }}" class="img-thumbnail" width="100" alt="Product Icon">
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
                                                    <input type="file" class="form-control-file" wire:model="product_image" id="product-image" accept="image/*">
                                                    
                                                    @if ($isUploadingImage)
                                                        <div class="spinner-border spinner-border-sm text-primary mt-2" role="status">
                                                            <span class="visually-hidden">Uploading...</span>
                                                        </div>
                                                        <small class="text-muted">Uploading...</small>
                                                    @endif
                                                    
                                                    @if ($product_image)
                                                        <div class="img-preview mt-2">
                                                            <img src="{{ $product_image->temporaryUrl() }}" class="img-thumbnail" width="100">
                                                        </div>
                                                    @elseif ($existingImage)
                                                        <div class="img-preview mt-2">
                                                            <img src="{{ asset($existingImage) }}" class="img-thumbnail" width="100" alt="Product Image">
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
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
   
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            initializeSelect2();
            initEditors();
            
            // Re-initialize on Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                initializeSelect2();
            });
        });
        
        function initializeSelect2() {
            // Initialize Select2 for product businesses with custom styling
            $('#product-businesses').select2({
                placeholder: "Select businesses",
                allowClear: true,
                width: '100%',
                theme: 'default',
                dropdownAutoWidth: true,
                dropdownParent: $('#product-businesses').parent()
            }).on('change', function() {
                @this.call('setBusinesses', [$(this).val()]);
            });
            
            // Initialize Select2 for product categories with custom styling
            $('#product-category').select2({
                placeholder: "Select categories",
                allowClear: true,
                width: '100%',
                theme: 'default', 
                dropdownAutoWidth: true,
                dropdownParent: $('#product-category').parent()
            }).on('change', function() {
                @this.call('setCategories', [$(this).val()]);
            });
        }
        
        function initEditors() {
            if (typeof ClassicEditor !== 'undefined') {
                // Use CKEditor if available
                ClassicEditor.create(document.querySelector('#overview'))
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            @this.set('overview', editor.getData());
                        });
                        
                        // Set initial data if needed
                        if (@this.get('overview')) {
                            editor.setData(@this.get('overview'));
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
                
                ClassicEditor.create(document.querySelector('#description'))
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            @this.set('description', editor.getData());
                        });
                        
                        // Set initial data if needed
                        if (@this.get('description')) {
                            editor.setData(@this.get('description'));
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
            } else {
                // Fall back to simple textarea
                $('#overview').on('change', function() {
                    @this.set('overview', $(this).val());
                });
                
                $('#description').on('change', function() {
                    @this.set('description', $(this).val());
                });
            }
        }
    </script>
    @endpush
</div>