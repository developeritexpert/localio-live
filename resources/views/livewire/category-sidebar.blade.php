
<section class="sfwr_sec cat_page_secs light p_120" style="background: #fdfdfd !important;">
    <div class="container">
        <h1 class="popular-categories-title" style="font-size: 28px !important; font-weight: 700 !important;">Browse all categories</h1>
        <div class="sfwr_content">
            <div class="row gy-4">
                <div class="col-lg-3 mb-4">
                    <div class="parent-cat-sidebar">
                        <ul>
                            @foreach ($categories as $category)
                            @php
                                $catItemName = null;
                                $catItemSlug = null;
                                if (isset($category->translations)) {
                                    if (is_object($category->translations) && !empty($category->translations->name)) {
                                        $catItemName = $category->translations->name;
                                        $catItemSlug = $category->translations->slug ?? null;
                                    } elseif ($category->translations instanceof \Illuminate\Database\Eloquent\Collection && $category->translations->isNotEmpty()) {
                                        $catItemName = $category->translations->first()->name ?? null;
                                        $catItemSlug = $category->translations->first()->slug ?? null;
                                    }
                                }
                                if (empty($catItemName) && method_exists($category, 'translation') && $category->translation) {
                                    $catItemName = $category->translation->name ?? null;
                                    $catItemSlug = $category->translation->slug ?? null;
                                }
                                if (empty($catItemName)) {
                                    $catItemName = $category->name ?? null;
                                }
                                if (empty($catItemSlug)) {
                                    $catItemSlug = $category->slug ?? (string)$category->id;
                                }
                            @endphp
                            @if(!empty($catItemName))
                            <li wire:key="cat-{{ $category->id }}">
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catItemSlug]) }}"
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                    <span>{{ $catItemName }}</span>
                                    <!-- <i class="fas fa-chevron-right" style="font-size: 11px; color: #f26522;"></i> -->
                                </a>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-lg-9 position-relative">
                    <div class="parent-cat-main subcat_grid" wire:loading.class="subcat_fade">
                        @forelse ($subCategories as $subcat)
                        @php
                            $catSlug = null;
                            if (isset($subcat->translations) && is_object($subcat->translations) && !empty($subcat->translations->slug)) {
                                $catSlug = $subcat->translations->slug;
                            } elseif (isset($subcat->translations) && $subcat->translations instanceof \Illuminate\Database\Eloquent\Collection && $subcat->translations->isNotEmpty()) {
                                $catSlug = $subcat->translations->first()->slug ?? null;
                            }
                            if (empty($catSlug) && method_exists($subcat, 'translation') && $subcat->translation) {
                                $catSlug = $subcat->translation->slug ?? null;
                            }
                            if (empty($catSlug)) {
                                $catSlug = $subcat->slug ?? (string)$subcat->id;
                            }

                            $catName = null;
                            if (isset($subcat->translations) && is_object($subcat->translations) && !empty($subcat->translations->name)) {
                                $catName = $subcat->translations->name;
                            } elseif (isset($subcat->translations) && $subcat->translations instanceof \Illuminate\Database\Eloquent\Collection && $subcat->translations->isNotEmpty()) {
                                $catName = $subcat->translations->first()->name ?? null;
                            }
                            if (empty($catName) && method_exists($subcat, 'translation') && $subcat->translation) {
                                $catName = $subcat->translation->name ?? null;
                            }
                            if (empty($catName)) {
                                $catName = $subcat->name ?? null;
                            }

                            $descText = '';
                            if (isset($subcat->translations) && is_object($subcat->translations) && !empty($subcat->translations->description)) {
                                $descText = $subcat->translations->description;
                            } elseif (isset($subcat->translations) && $subcat->translations instanceof \Illuminate\Database\Eloquent\Collection && $subcat->translations->isNotEmpty()) {
                                $descText = $subcat->translations->first()->description ?? '';
                            } elseif (method_exists($subcat, 'translation') && $subcat->translation) {
                                $descText = $subcat->translation->description ?? '';
                            }
                            $desc = strip_tags($descText);
                            if(empty($desc) && !empty($catName)) {
                                $desc = $catName . ' solutions designed to help you manage your workflow efficiently.';
                            }
                        @endphp
                        @if(!empty($catName))
                        <div class="subcat-block" wire:key="sub-{{ $subcat->id }}" style="background: #f7f9fb;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h2 style="font-size: 20px; font-weight: 700; color: #002347; margin: 0;">{{ $catName }}</h3>
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="subcat-link" style="color: #002655; font-size: 13px; font-weight: 600; text-decoration: none;">See all {{ $catName }}</a>
                            </div>

                            <p>{{ $desc }}</p>

                            <div class="top-products-grid">
                                @foreach($subcat->top_businesses ?? [] as $business)
                                @php
                                    $bizName = $business->translations->first()->name ?? $business->name ?? null;
                                @endphp
                                @if(!empty($bizName))
                                <div class="top-product-card d-flex flex-column justify-content-between p-3" style="background:#fdfdfd !important;">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="top-product-logo" style="width: 45px; height: 45px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                            @if($business->icon_id)
                                                <img src="{{ asset($business->icon_id) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;">
                                            @else
                                                <div class="avatar-placeholder" style="width: 100%; height: 100%; border-radius: 10px; font-size: 18px; font-weight: 700; background: linear-gradient(135deg, #002347 0%, #00438a 100%); color: #fff; display: flex; align-items: center; justify-content: center;">
                                                    {{ strtoupper(substr($bizName, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="top-product-info min-w-0">
                                            <h6 class="m-0 fw-bold d-flex align-items-center gap-1" style="font-size: 14px; color: #1e3050;">
                                                {{ $bizName }}
                                                <!-- <span style="font-size: 12px; color: #64748b; cursor: pointer;">♡</span> -->
                                            </h6>
                                            <div class="d-flex align-items-center gap-1 mt-1" style="font-size: 11px; color: #777;">
                                                <span class="fw-medium text-dark">{{ number_format($business->average_rating, 1) }}</span>
                                                <div class="d-flex" style="color: #ffc107;">
                                                    @php $rating = round($business->average_rating); @endphp
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $rating)
                                                            <i class="fas fa-star" style="margin-right:1px;"></i>
                                                        @else
                                                            <i class="far fa-star" style="margin-right:1px; color:#ffe896;"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <!-- <span class="fw-semibold text-dark">{{ number_format($business->average_rating, 1) }}</span> -->
                                                <!-- <span>|</span> -->
                                                <span class="fw-medium text-dark">({{ $business->active_reviews_count }})</span>
                                                <!-- <span>{{ $business->active_reviews_count }} {{ $business->active_reviews_count == 1 ? 'review' : 'reviews' }}</span> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 w-100 mt-auto">
                                        <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $business->translations->first()->slug ?? $business->slug]) }}"
                                        class="btn-view-details btn py-1 px-2 fw-medium w-50">
                                            View details
                                        </a>
                                        @if(!empty($business->is_affiliate))
                                        <a href="{{ $business->getTrackedUrl() }}"
                                           target="_blank"
                                           class="btn-orng btn py-1 px-2 fw-medium w-50 text-white"
                                           style="border-radius: 30px; font-size: 11px; text-align: center; transition:unset !important;">
                                            Visit website <i class="fas fa-external-link-alt" style="font-size: 9px; margin-left: 2px;"></i>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @empty
                        <p>No categories found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
