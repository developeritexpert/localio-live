<section class="sfwr_sec cat_page_secs light p_120" style="background: #fdfdfd !important;">
<style>
    .top-products-grid .top-product-card {
        cursor: pointer !important;
        text-decoration: none !important;
        color: inherit !important;
    }
    .top-products-grid .top-product-card:hover {
        box-shadow: 0px 0px 16px 0px rgb(0 0 0 / 13%);
        border-color: #cbd5e1 !important;
        text-decoration: none !important;
    }
</style>

    <div class="container">
        <h1 class="popular-categories-title" style="font-size: 28px !important;">Browse all categories</h1>
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
                            if (isset($subcat->translations) && is_object($subcat->translations) && !empty($subcat->translations->all_categories_description)) {
                                $descText = $subcat->translations->all_categories_description;
                            } elseif (isset($subcat->translations) && $subcat->translations instanceof \Illuminate\Database\Eloquent\Collection && $subcat->translations->isNotEmpty() && !empty($subcat->translations->first()->all_categories_description)) {
                                $descText = $subcat->translations->first()->all_categories_description;
                            } elseif (method_exists($subcat, 'translation') && $subcat->translation && !empty($subcat->translation->all_categories_description)) {
                                $descText = $subcat->translation->all_categories_description;
                            } elseif (isset($subcat->translations) && is_object($subcat->translations) && !empty($subcat->translations->description)) {
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
                                <h2 style="margin: 0;">{{ $catName }}</h3>
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="subcat-link" >See all {{ $catName }}</a>
                            </div>

                            <p>{{ $desc }}</p>

                            <div class="top-products-grid">
                                @foreach($subcat->top_businesses ?? [] as $business)
                                @php
                                    $bizName = $business->translations->first()->name ?? $business->name ?? null;
                                    $bizSlug = $business->translations->first()->slug ?? $business->slug ?? null;
                                @endphp
                                @if(!empty($bizName))
                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bizSlug]) }}"
                                   class="top-product-card d-flex align-items-center gap-2 p-3 text-decoration-none"
                                   style="background: #ffffff !important; border: 1px solid #e2e8f0; border-radius: 10px;">
                                    <div class="top-product-logo" style="width: 45px; height: 45px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                        <x-business-logo :business="$business" :name="$bizName" />
                                    </div>
                                    <div class="top-product-info min-w-0 flex-grow-1">
                                        <h6 class="m-0 fw-bold d-flex align-items-center gap-1" style="font-size: 14px; color: #002347; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $bizName }}
                                        </h6>
                                        @if($business->should_show_rating)
                                        <div class="d-flex align-items-center gap-1 mt-1" style="font-size: 11px; color: #777;">
                                            <span class="fw-medium text-dark">{{ formatRating($business->display_rating) }}</span>
                                            <div class="d-flex" style="color: #ffc107;">
                                                @php $rating = round($business->display_rating); @endphp
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $rating)
                                                        <i class="fas fa-star" style="margin-right:1px;"></i>
                                                    @else
                                                        <i class="far fa-star" style="margin-right:1px; color:#ffe896;"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            @if($business->should_show_review_count)
                                            <span class="fw-medium text-dark">({{ $business->approved_reviews_count }})</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </a>
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
