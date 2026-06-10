<div class="dropdown-flex-container d-flex">
    <!-- Left Side: Discounted Products List -->
    <div class="products-list col-md-9 pe-2">
        <div class="dropdown_content">
            <ul class="hdr_dropul" style="list-style: none; padding: 0; margin: 0;">
                @forelse ($exclusive_products as $product)
                    <li class="dropdown_item-1 mb-3"
                        onmouseover="
                            this.querySelector('.hdr_insdiecont').style.color = '#F9633B';
                            this.querySelector('.header_img').style.transform = 'scale(1.1)';
                            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                        "
                        onmouseout="
                            this.querySelector('.hdr_insdiecont').style.color = '';
                            this.querySelector('.header_img').style.transform = 'scale(1)';
                            this.style.boxShadow = '';
                        "
                        style="padding: 8px 0;">

                        <div class="exclusive_offer">

                            <a href="javascript:void(0);" wire:click="redirectToProduct({{ $product->id }})"
                                style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                                
                                <div class="ab_img" style="flex-shrink: 0;">
                                    <img src="{{ asset($product->businesses->first()->icon_id ?? 'images/no-image.png') }}"
                                        class="header_img" alt="{{ $product->translations->name ?? 'Product' }}">
                                </div>
    
                                <div class="hdr_insdiecont">
                                    <div class="exclusive_heading">
                                        {{ $product->translations->name ?? 'Product' }}
                                        @php
                                            $original = $product->prices->first()->price ?? 0;
                                            $discounted = $product->prices->first()->discount_price ?? $original; // fallback to original if no discount
                                            $discountPercentage =
                                                $original > 0 ? round((($original - $discounted) / $original) * 100) : 0;
                                        @endphp
    
                                    </div>

                                    <div class="exclusive_discount">
                                         @if ($discountPercentage > 0)
                                            <small class="d-block text-success">{{ $discountPercentage }}% OFF</small>
                                        @endif
                                    </div>
    
                                    <div class="exclusive_pricing">
    
                                        @if ($product->prices->first())
                                            <small class="d-block text-muted">
                                                <div style="text-decoration: line-through;">${{ number_format($product->prices->first()->price, 2) }}
                                                </div>
                                                <div class="text-danger ms-2">${{ number_format($product->prices->first()->discount_price, 2) }}
                                                </div>
                                            </small>
                                        @endif
    
                                    </div>
    
                                </div>
                            </a>

                            <!-- Business Info -->
                            @if ($product->businesses->first())
                                <div class="business-info mt-2">
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted">
                                            by
                                            <strong>{{ $product->businesses->first()->translations->first()?->name ?? 'Business' }}</strong>
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>


                    </li>
                @empty
                    <li class="dropdown_item-1">
                        <a href="javascript:void(0);">No Exclusive Discounts Found</a>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
    <!-- Search Input -->
    <div class="search-input food_input search_box col-md-3 ps-2">
        <div class="dropdown_content">
            <div class="inside_dropdown_cont">
                <div class="header_drop_inpt d-flex align-items-center" onclick="event.stopPropagation()"
                    style="position: relative;">
                    <div class="inside_text flex-grow-1">
                        <input type="text" wire:model="searchTerm" wire:keydown="performSearch"
                            placeholder="Search Discounted Products..." class="form-control"
                            onclick="event.stopPropagation()">
                    </div>
                     <div class="drop_serach_btn ms-2"
                    style="background-color: #F9633B; border-radius: 50px;"
                    onmouseover="this.style.backgroundColor = '#002347';"
                    onmouseout="this.style.backgroundColor = '#F9633B';">
                   <i class="fa-solid fa-magnifying-glass"></i>
               </div>


                </div>
                <!-- Search Results Dropdown -->
                @if (!empty($searchResults) || $searchTerm)
                    <div class="search-results"
style="position: absolute; width:100%; background-color: white; border-radius: 8px; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); z-index: 10;  overflow-y: auto;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @forelse ($searchResults as $result)
                                <li wire:click="redirectToProduct({{ $result['product_id'] }})"
                                    style="padding: 10px 15px; display: flex; width:100%; justify-content:space-between; align-items-center; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background-color 0.2s ease-in-out;"
                                    onmouseover="this.style.backgroundColor='#f4f4f4'"
                                    onmouseout="this.style.backgroundColor='white'">
                                    <div>
                                        <i class="fa-solid fa-magnifying-glass" ></i>
                                        </div>
                                    <div>
                                        <div style="color:black; margin-right:10px">{{ $result['name'] }}</div>
                                        <small class="text-success">{{ $result['discount_percentage'] }}% OFF</small>
                                    </div>
                                </li>
                            @empty
                                <li style="padding: 10px 15px; color: #888; font-size: 14px; width:100%;">
                                    No results found for "{{ $searchTerm }}"
                                </li>
                            @endforelse
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        @php
            $trendingProducts = $exclusive_products->take(3);
        @endphp

        <div class="mt-3 ps-1">
            <div class="d-flex align-items-center flex-wrap gap-2">
                @if($trendingProducts->count()>0)
                <span class="discount-label">Trending:</span>
                @foreach ($trendingProducts as $product)
                    <button type="button" wire:click="redirectToProduct({{ $product->id }})" class="trending-category-btn">
                        {{ Str::limit($product->translations->name ?? 'Product', 15) }}
                    </button>
                @endforeach
              
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('show-product', function(e) {
            const {
                productId
            } = e.detail;
            viewProduct(productId);
        });

        function viewProduct(productId) {
            @this.call('redirectToProduct', productId);
        }
    </script>
@endpush
