
<section class="sfwr_sec cat_page_secs light p_120">
    <div class="container">
        <div class="sfwr_content">
            {{-- <h2 data-aos="zoom-in" data-aos-duration="1000">
                {{ $categoriesContents['main_heading'] ?? '' }}
            </h2> --}}

            <div class="row gy-4">
                

                <div class="col-lg-3 mb-4">

                    <div class="parent-cat-sidebar">
<h2 style="font-size: 32px; font-weight: 700; color: #06498b; margin-bottom: 20px; text-align: left;">All Categories</h2>

                        <ul>
                            @foreach ($categories as $category)
                            <li class="{{ $selectedCategoryId == $category->id ? 'active' : '' }}"
                                wire:click="selectCategory({{ $category->id }})"
                                wire:key="cat-{{ $category->id }}"
                                wire:loading.class="cat_loading"
                                wire:target="selectCategory({{ $category->id }})">
                                {{ $category->translations->name ?? 'No Name' }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                
               <div class="col-lg-9 position-relative">

    {{-- Loading overlay --}}
    {{-- <div wire:loading class="subcat_loading_overlay">
        <div class="subcat_spinner"></div>
    </div> --}}

    <div class="parent-cat-main subcat_grid" wire:loading.class="subcat_fade">

        @forelse ($subCategories as $subcat)
        <div class="subcat-block" wire:key="sub-{{ $subcat->id }}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 style="font-size: 20px; font-weight: 700; color: #002347; margin: 0;">{{ $subcat->translations->name ?? 'Subcategory' }}</h3>
                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $subcat->translation()->first()->slug ?? $subcat->slug]) }}" class="subcat-link" style="color: #002655; font-size: 13px; font-weight: 600; text-decoration: none;">See all {{ $subcat->translations->name ?? 'Software' }}</a>
            </div>

            @php
                $desc = strip_tags($subcat->translations->description ?? '');
                if(empty($desc)) {
                    $desc = ($subcat->translations->name ?? 'Software') . ' solutions designed to help you manage your workflow efficiently.';
                }
            @endphp
            <p>{{ $desc }}</p>

            <div class="top-products-grid">
                @foreach($subcat->top_businesses ?? [] as $business)
                <div class="top-product-card d-flex flex-column justify-content-between p-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="top-product-logo" style="width: 45px; height: 45px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                            @if($business->icon_id)
                                <img src="{{ asset($business->icon_id) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;">
                            @else
                                <div class="avatar-placeholder" style="width: 100%; height: 100%; border-radius: 50%; font-size: 18px; font-weight: 700; background: linear-gradient(135deg, #002347 0%, #00438a 100%); color: #fff; display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($business->translations->first()->name ?? 'B', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="top-product-info min-w-0">
                            <h6 class="m-0 fw-bold d-flex align-items-center gap-1" style="font-size: 14px; color: #1e3050;">
                                {{ $business->translations->first()->name ?? 'Business Name' }}
                                <span style="font-size: 12px; color: #64748b; cursor: pointer;">♡</span>
                            </h6>
                            <div class="d-flex align-items-center gap-1 mt-1" style="font-size: 11px; color: #777;">
                                <div class="d-flex" style="color: #ffb300;">
                                    @php $rating = round($business->average_rating); @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rating)
                                            <i class="fas fa-star" style="margin-right:1px;"></i>
                                        @else
                                            <i class="far fa-star" style="margin-right:1px; color:#ffe896;"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="fw-semibold text-dark">{{ number_format($business->average_rating, 1) }}</span>
                                <span>|</span>
                                <span>{{ $business->active_reviews_count }} reviews</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 w-100 mt-auto">
                        <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $business->translations->first()->slug ?? $business->slug]) }}"
                        class="btn-view-details btn py-1 px-2 fw-semibold w-50">
                            View details
                        </a>
                        <a href="{{ $business->getTrackedUrl() }}"
                           target="_blank"
                           class="btn py-1 px-2 fw-semibold w-50 text-white"
                           style="background-color: #f26522; border-radius: 30px; font-size: 11px; text-align: center; text-decoration: none; transition: background 0.2s;">
                            Visit website <i class="fas fa-external-link-alt" style="font-size: 9px; margin-left: 2px;"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <p>No sub-categories found.</p>
        @endforelse

    </div>
</div>
            </div>
        </div>
    </div>
</section>