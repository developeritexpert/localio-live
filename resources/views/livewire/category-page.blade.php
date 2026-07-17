<div>
    <style>
        /* Toggle switch styling */
          .cat-heading-block h1 {
                /* font-size: 24px; */
                font-size: 28px !important;
                font-weight: 700;
                padding: 0 !important;
                margin-bottom: 4px !important;
                color: #002347 !important;
            }
        .toggle-bg {
            transition: background-color 0.2s;
        }

        .toggle-dot {
            transition: transform 0.3s;
        }

        input:checked~.toggle-bg {
            background-color: #4c51bf;
        }

        input:checked~.toggle-dot {
            transform: translateX(100%);
        }

        /* Active filters display */
        .active-filter {
            background-color: #e9f3ff;
            border-radius: 4px;
            padding: 4px 8px;
            margin-right: 8px;
            margin-bottom: 8px;
            display: inline-flex;
            align-items: center;
        }

        .remove-filter {
            margin-left: 4px;
            color: #666;
        }

        .remove-filter:hover {
            color: #ff0000;
        }
        .automotive-card {
            transition: none;
        }

        .automotive-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Force header search bar always visible on this page */
        #myID.search-box {
            visibility: visible !important;
            display: block !important;
        }

        /* Page top padding so content clears the fixed navbar */
        .top-automotive-sec.cat_pg {
            /* padding-top: 160px !important; */
            margin-top: 4rem !important;
        }

        /* Category heading block */
        .cat-heading-block {
            /* margin-left: 27%; */
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e8eef6;
            text-align: left;
        }
        .cat-heading-block h1 {
            font-size: 28px ;
            font-weight: 700;
            padding: 0 ;
            margin-bottom: 4px;
            line-height: 1.2;
            text-align: left;
        }
        .cat-heading-block p {
            font-size: 15px;
            color: #7a8ea8;
            margin: 0;
            font-weight: 400;
            text-align: left;
        }

        /* USP grid: 2 per row */
        .usp-grid-container {
            display: grid !important;
            grid-template-columns: auto auto !important;
            justify-content: start !important;
            gap: 8px 45px !important;
            width: 100% !important;
        }

        /* Filter section horizontal dividers */
        .filter-section {
            border-bottom: 1px solid #e8eef6;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }

        /* Compare button alignment */
        .automotive-card .blue-chkbox {
            bottom: 105px !important;
            transition: all 0.3s ease;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            /* .top-automotive-sec.cat_pg {
                padding-top: 110px !important;
            } */
            .cat-heading-block {
                margin-left: 0;
            }
            .cat-heading-block h1 {
                /* font-size: 24px; */
                font-size: 28px !important;
                font-weight: 700;
                padding: 0 !important;
                margin-bottom: 4px !important;
                color: #002347 !important;
            }
            .usp-grid-container {
                grid-template-columns: 1fr !important;
                gap: 8px 0px !important;
            }
            .automotive-card .blue-chkbox {
                position: relative !important;
                bottom: auto !important;
                right: auto !important;
                border-radius: 8px !important;
                display: block !important;
                width: 100% !important;
                text-align: center !important;
                margin-top: 15px !important;
                padding: 12px 15px !important;
            }
            .key-feature-price {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            .starting-price-box, .free-trial-box {
                width: 100% !important;
                min-width: 100% !important;
                margin-bottom: 10px !important;
            }
        }
    </style>

    @section('meta_title', isset($category->translations->name) && isset($category->translations->name) ?
        $category->translations->name : 'Category Page')
        @if (session()->has('message'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div>
            <!-- section top-rated automaotive -->
            <section class="top-automotive-sec cat_pg light">
                <div class="top-auto-btm">
                    <div class="container">
                        <div class="top-auto-choice">
                            <div class="cat-heading-block">
                                <h1>{{ $category->translations->name ?? 'Products' }}</h1>
                                <p>{{ strip_tags($category->translations->description ?? 'Browse and compare the best options') }}</p>
                            </div>
                            <div class="auto-choice-row d-flex">
                                <div class="auto-choice-lft">
                                    <div class="col-md-12">
                                        <!-- Rating Filter Section -->
                                        <div class="filter-section">
                                            <h3>User Reviews</h3>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    wire:model.live="selectedRatings" value="4"
                                                    id="rating-4">
                                                <label class="form-check-label" for="rating-4">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= 4)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-warning"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="categories1">& up</span>
                                                    <span class="categories2">({{ $ratingCounts[4] ?? 0 }})</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <!-- Rating Filter Section - Styled like the image -->

                                            <link rel="stylesheet"
                                                href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" />

                                            <div class="filter-section border-bottom">
                                                <h3 class="fw-semibold text-dark">Price Range</h3>

                                                <div class="price-slider-container">
                                                    <div
                                                        class="price-inputs d-flex gap-2 align-items-center mt-3">
                                                        <div class="price-input">
                                                            <span class="currency">$</span>
                                                            <input type="number" id="minPriceInput"
                                                                wire:model.live="minPrice" min="0" max="2000"
                                                                class="form-control form-control-sm">
                                                        </div>
                                                        <span class="price-separator">to</span>
                                                        <div class="price-input">
                                                            <span class="currency">$</span>
                                                            <input type="number" id="maxPriceInput"
                                                                wire:model.live="maxPrice" min="0" max="2000"
                                                                class="form-control form-control-sm">
                                                        </div>
                                                    </div>

                                                    <div id="priceRangeSlider" style="margin-top: 20px;" wire:ignore>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion" id="filterAccordion">
                                                @foreach ($filters as $filter)
                                                    @php
                                                        $filterName =
                                                            $filter->translations
                                                                ->where('language_id', $lang_id)
                                                                ->first()->name ?? $filter->name;
                                                        $filterType = $filter->filterType
                                                            ? $filter->filterType->slug
                                                            : 'checkbox';
                                                    @endphp

                                                    <div class="filter-section">
                                                        <h3>
                                                            {{ $filterName }}
                                                        </h3>

                                                        <div class="accordion-body">
                                                            @if ($filterType === 'checkbox')
                                                                @foreach ($filter->options as $option)
                                                                    @php
                                                                        $optionName =
                                                                            $option->translations
                                                                                ->where('language_id', $lang_id)
                                                                                ->first()->name ?? $option->name;
                                                                    @endphp
                                                                    <div class="form-check" style="margin-bottom: 8px;">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            wire:model.live="selectedOptions"
                                                                            value="{{ $option->id }}"
                                                                            id="option-{{ $option->id }}">
                                                                        <label class="form-check-label"
                                                                            for="option-{{ $option->id }}">
                                                                            {{ $optionName }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                                @elseif($filterType === 'radio')
                                                                @foreach ($filter->options as $option)
                                                                    @php
                                                                        $optionName = $option->translations
                                                                            ->where('language_id', $lang_id)
                                                                            ->first()->name ?? $option->name;
                                                                    @endphp
                                                                    <div class="form-check" style="margin-bottom: 8px;">
                                                                        <input type="radio"
                                                                            class="form-check-input"
                                                                            name="filter_{{ $filter->id }}"
                                                                            wire:key="radio-{{ $filter->id }}-{{ $option->id }}"
                                                                            wire:click="toggleFilterOption({{ $option->id }})"
                                                                            {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}
                                                                            value="{{ $option->id }}"
                                                                            id="option-{{ $option->id }}">
                                                                        <label class="form-check-label"
                                                                            for="option-{{ $option->id }}">
                                                                            {{ $optionName }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @elseif($filterType === 'dropdown')
                                                                @php
                                                                    $selected = $filter->options->firstWhere(
                                                                        'id',
                                                                        $selectedOptions[0] ?? null,
                                                                    );
                                                                    $selectedOptionName = $selected
                                                                        ? $selected->translations
                                                                                ->where('language_id', $lang_id)
                                                                                ->first()->name ?? $selected->name
                                                                        : __('Select');
                                                                @endphp

                                                                <div class="">
                                                                    <select
                                                                        class="form-select w-full p-2 border border-gray-300 rounded-md text-sm"
                                                                        wire:model.live="selectedOptions.0">
                                                                        <option value="">{{ __('Select') }}
                                                                        </option>
                                                                        @foreach ($filter->options as $option)
                                                                            @php
                                                                                $optionName =
                                                                                    $option->translations
                                                                                        ->where('language_id', $lang_id)
                                                                                        ->first()->name ??
                                                                                    $option->name;
                                                                            @endphp
                                                                            <option value="{{ $option->id }}">
                                                                                {{ $optionName }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @elseif($filterType === 'toggle')
                                                                @foreach ($filter->options as $option)
                                                                    @php
                                                                        $optionTranslation = $option->translations
                                                                            ->where('language_id', $lang_id)
                                                                            ->first();
                                                                        $optionName =
                                                                            $optionTranslation->name ?? $option->name;
                                                                        $onLabel =
                                                                            $optionTranslation->on_label ??
                                                                            ($option->on_label ?? 'On');
                                                                        $offLabel =
                                                                            $optionTranslation->off_label ??
                                                                            ($option->off_label ?? 'Off');
                                                                        $isChecked = in_array(
                                                                            $option->id,
                                                                            $selectedOptions,
                                                                        );
                                                                    @endphp
                                                                    <div class="toggle-switch mb-2">
                                                                        <label
                                                                            class="toggle-label flex items-center cursor-pointer">
                                                                            <div class="relative">
                                                                                <input type="checkbox"
                                                                                    wire:model.live="selectedOptions"
                                                                                    value="{{ $option->id }}"
                                                                                    class="sr-only peer"
                                                                                    {{ $isChecked ? 'checked' : '' }}>

                                                                                <div
                                                                                    class="w-12 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 transition-colors">
                                                                                </div>
                                                                                <div
                                                                                    class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6">
                                                                                </div>
                                                                            </div>
                                                                            <div class="ml-3">
                                                                                <span
                                                                                    class="font-medium">{{ $optionName }}</span><br>
                                                                                <span class="text-xs text-gray-500">
                                                                                    {{ $isChecked ? $onLabel : $offLabel }}
                                                                                </span>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @elseif($filterType === 'slider')
                                                                <div x-data="{
                                                                    min: {{ $filter->options->min('min_value') ?? 0 }},
                                                                    max: {{ $filter->options->max('max_value') ?? 100 }},
                                                                    currentMin: {{ $minPrice }},
                                                                    currentMax: {{ $maxPrice }},
                                                                    unit: '{{ $filter->options->first() ? $filter->options->first()->translations->where('language_id', $lang_id)->first()->unit ?? ($filter->options->first()->unit ?? '') : '' }}',

                                                                    init() {
                                                                        this.$nextTick(() => {
                                                                            this.setupSlider();
                                                                        });
                                                                    },

                                                                    setupSlider() {
                                                                        const slider = this.$refs.slider;
                                                                        if (typeof noUiSlider !== 'undefined' && slider) {
                                                                            if (slider.noUiSlider) {
                                                                                slider.noUiSlider.destroy();
                                                                            }

                                                                            noUiSlider.create(slider, {
                                                                                start: [this.currentMin, this.currentMax],
                                                                                connect: true,
                                                                                range: {
                                                                                    'min': this.min,
                                                                                    'max': this.max
                                                                                }
                                                                            });

                                                                            slider.noUiSlider.on('update', (values) => {
                                                                                this.currentMin = Math.round(values[0]);
                                                                                this.currentMax = Math.round(values[1]);
                                                                            });

                                                                            slider.noUiSlider.on('end', () => {
                                                                                $wire.setPriceRange(this.currentMin, this.currentMax);
                                                                            });
                                                                        }
                                                                    }
                                                                }" class="range-slider py-4">
                                                                    <div class="values-display flex justify-between mb-2">
                                                                        <span x-text="currentMin + ' ' + unit"></span>
                                                                        <span x-text="currentMax + ' ' + unit"></span>
                                                                    </div>

                                                                    <div x-ref="slider" class="slider-element"></div>
                                                                </div>
                                                            @elseif($filterType === 'color')
                                                                <div class="color-options flex flex-wrap gap-2">
                                                                    @foreach ($filter->options as $option)
                                                                        @php
                                                                            $isSelected = in_array(
                                                                                $option->id,
                                                                                $selectedOptions,
                                                                            );
                                                                            $optionTranslation = $option->translations
                                                                                ->where('language_id', $lang_id)
                                                                                ->first();
                                                                            $colorName =
                                                                                $optionTranslation->name ??
                                                                                $option->name;
                                                                            // Get color value from option or fallback to a default
                                                                            $colorValue =
                                                                                $optionTranslation->color_value ??
                                                                                '#cccccc';
                                                                        @endphp

                                                                        <button
                                                                            wire:click="toggleFilterOption({{ $option->id }})"
                                                                            class="color-option w-6 h-6 rounded-full border {{ $isSelected ? 'border-black' : 'border-gray-300' }}"
                                                                            style="background-color: {{ $colorValue }}; position: relative;"
                                                                            title="{{ $colorName }}">
                                                                            @if ($isSelected)
                                                                                <span
                                                                                    class="absolute inset-0 flex items-center justify-center text-white">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        width="12" height="12"
                                                                                        viewBox="0 0 24 24" fill="none"
                                                                                        stroke="currentColor"
                                                                                        stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round">
                                                                                        <polyline points="20 6 9 17 4 12">
                                                                                        </polyline>
                                                                                    </svg>
                                                                                </span>
                                                                            @endif
                                                                        </button>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Products Section -->
                                <div class="auto-choice-rgt">
                                    <!-- Product Count and Sort -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            @if($products->count() >0)
                                            <p class="m-0">Showing {{ $productsCount }}-{{ $products->count() }} of {{ $products->count() }}
                                              </p>
                                            @else
                                            <p class="m-0">Showing {{ $products->count() }} results
                                            </p>
                                            @endif
                                        </div>
                                        <x-social-icon/>
                                    </div>
                                    <!-- No products message -->
                                    @if ($noMatchingProducts)
                                        <div class="alert alert-info">
                                            <p class="mb-2 text-center fs-4 text-secondary">Sorry, we don't have any products that match your filters. Try adjusting them to see more options.</p>
                                            {{-- <button wire:click="resetFilters" class="btn btn-primary">
                                                <i class="fa fa-refresh me-1"></i> Reset All Filters
                                            </button> --}}
                                        </div>
                                    @endif
                                    <!-- Products List -->
                                    @php
                                        $badgeLabel = static_text('category_badge_label') ?? 'Top Choice';

                                        // dd($badgeLabel);
                                    @endphp
                                    <div>

                                        @foreach ($products as $item)
                                            {{-- <div class="automotive-card auto-bg " data-aos="fade-up" style="--badge-label:'{{ addslashes($badgeLabel) }}';" --}}
                                            <div class="automotive-card auto-bg " data-aos="fade-up"
                                                data-aos-duration="1000" wire:key="product-{{ $item->id }}">
                                                 @if (!empty($badgeLabel))
                                                    <span class="badge-label">{{ $badgeLabel }}</span>
                                                @endif
                                                <div class="auto-choice-card">
                                                    <!-- Product card content remains the same -->
                                                    <div class="auto-choice-hd">
                                                        <div class="inn_sl_hed">
                                                            <a
                                                            href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}">
                                                            <div class="sli_img choice_img">
                                                                <img class="slider_img"
                                                                    src="{{ asset($item->icon_id) }}"
                                                                    alt="No Images For This Product">
                                                            </div>
                                                             </a>
                                                            <div class="sl_h">
                                                                <div class="inn_h">
                                                                    <div class="sl_main">
                                                                        <h6 class="head">
                                                                            {{ $item->translations->first()->name }}</h6>
                                                                        <div class="wishlist">
                                                                            <livewire:wishlist :product-id="$item->id"
                                                                                :key="'wishlist-instance-' .
                                                                                    $item->id" />
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @php
                                                                    $averageRating = round(
                                                                        $item->reviews
                                                                            ->where('status', 'active')
                                                                            ->avg('rating'),
                                                                        1,
                                                                    );
                                                                    $ratingCount = $item->reviews
                                                                        ->where('status', 'active')
                                                                        ->count();
                                                                @endphp

                                                                <div class="tp-btm d-flex flex-col-mob">
                                                                    <div class="inn_ul">
                                                                        <div class="rating-stars">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($averageRating))
                                                                                    <i
                                                                                        class="fas fa-star text-warning"></i>
                                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                                    <i
                                                                                        class="fas fa-star-half-alt text-warning"></i>
                                                                                @else
                                                                                    <i
                                                                                        class="far fa-star text-warning"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                    <div class="rate_box">
                                                                        {{ number_format($averageRating, 1) }} |
                                                                        {{ $ratingCount }} Reviews
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="auto-choice-btn d-flex flex-column gap-2" style="min-width: 170px;">
                                                            <a href="{{ $item->affiliate_link ?? $item->permanent_url }}"
                                                                class="cta cta_orange justify-content-center"
                                                                target="_blank" rel="noopener noreferrer"
                                                                style="display: flex !important; width: 100%; align-items: center;">
                                                                Visit website
                                                                <div class="right-arw" style="margin-left: 5px;">
                                                                    <i class="fa-solid fa-arrow-right"></i>
                                                                </div>
                                                            </a>
                                                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}"
                                                                class="cta cta_outline justify-content-center"
                                                                style="display: flex !important; width: 100%; align-items: center; border: 1px solid #06498b; color: #06498b;">
                                                                View details
                                                            </a>
                                                        </div>
                                                    </div>

                                                    {{-- USP Grid (replaces description and key features) --}}
                                                    <div class="slider_content_sec my-3" style="width: 100% !important; max-width: 100% !important;">
                                                        <div class="main_feature_lg" style="width: 100% !important; max-width: 100% !important;">
                                                            <div class="feture_box lft_check_box size18" style="border: none; padding: 0; background: transparent; min-height: auto; width: 100% !important; max-width: 100% !important;">
                                                                <div class="usp-grid-container">
                                                                    @if ($item->usps->count() > 0)
                                                                        @foreach ($item->usps->take(4) as $usp)
                                                                            <div class="d-flex align-items-center size18">
                                                                                <div class="grn_chk" style="width: 18px; margin-right: 8px; flex-shrink: 0;">
                                                                                    <img src="{{ asset('front/img/tick-img.png') }}" style="width: 100%; height: auto;">
                                                                                </div>
                                                                                <p class="m-0" style="font-size: 13px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $usp->text }}</p>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="d-flex align-items-center size18">
                                                                            <div class="grn_chk" style="width: 18px; margin-right: 8px; flex-shrink: 0;">
                                                                                <img src="{{ asset('front/img/tick-img.png') }}" style="width: 100%; height: auto;">
                                                                            </div>
                                                                            <p class="m-0" style="font-size: 13px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Free domain & SSL certificate</p>
                                                                        </div>
                                                                        <div class="d-flex align-items-center size18">
                                                                            <div class="grn_chk" style="width: 18px; margin-right: 8px; flex-shrink: 0;">
                                                                                <img src="{{ asset('front/img/tick-img.png') }}" style="width: 100%; height: auto;">
                                                                            </div>
                                                                            <p class="m-0" style="font-size: 13px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Customizable automatic updates</p>
                                                                        </div>
                                                                        <div class="d-flex align-items-center size18">
                                                                            <div class="grn_chk" style="width: 18px; margin-right: 8px; flex-shrink: 0;">
                                                                                <img src="{{ asset('front/img/tick-img.png') }}" style="width: 100%; height: auto;">
                                                                            </div>
                                                                            <p class="m-0" style="font-size: 13px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Scalable performance management</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @php
                                                    $allPrices = $item->products
                                                        ->flatMap(function ($product) {
                                                            return $product->prices;
                                                        })
                                                        ->sortBy(function ($price) {
                                                            $now = Illuminate\Support\Carbon::now();
                                                            if ($price->discount_price && $price->discount_expiration_date && $now->lte(Illuminate\Support\Carbon::parse($price->discount_expiration_date))) {
                                                                return $price->discount_price;
                                                            } elseif ($price->renewal_price) {
                                                                return $price->renewal_price;
                                                            } else {
                                                                return $price->price;
                                                            }
                                                        });

                                                    $startingPrice = $allPrices->first();
                                                    $displayPrice = null;

                                                    if ($startingPrice) {
                                                        $now = Illuminate\Support\Carbon::now();
                                                        if ($startingPrice->discount_price && $startingPrice->discount_expiration_date && $now->lte(Illuminate\Support\Carbon::parse($startingPrice->discount_expiration_date))) {
                                                            $displayPrice = $startingPrice->discount_price;
                                                        } elseif ($startingPrice->renewal_price) {
                                                            $displayPrice = $startingPrice->renewal_price;
                                                        } else {
                                                            $displayPrice = $startingPrice->price;
                                                        }
                                                    }
                                                    @endphp

                                                    <div class="key-feature-price d-flex gap-3 mt-3 align-items-stretch" style="display: flex !important; justify-content: flex-start !important; gap: 15px !important; flex-wrap: wrap;">
                                                        @if ($startingPrice)
                                                            <div class="starting-price-box p-3 rounded d-flex flex-column justify-content-between" style="background: #fff; border: 1px solid #eef0f4; box-shadow: 0 4px 12px rgba(0,0,0,0.03); min-width: 220px; text-align: center; margin-bottom: 15px;">
                                                                <div>
                                                                    <h6 style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Starting price</h6>
                                                                    <h3 style="font-weight: 700; color: #06498b; font-size: 24px; margin-bottom: 6px;">
                                                                        {{ $startingPrice->currency }}{{ number_format($displayPrice, 2) }}
                                                                    </h3>
                                                                    <p style="font-size: 12px; color: #888; margin-bottom: 12px;">Flat Rate, Per One_time</p>
                                                                </div>
                                                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}#pricing" style="font-size: 13px; font-weight: 600; color: #f9633b; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">View pricing</a>
                                                            </div>
                                                        @endif

                                                        <div class="free-trial-box p-3 rounded d-flex flex-column align-items-center justify-content-center" style="background: #fff; border: 1px solid #eef0f4; box-shadow: 0 4px 12px rgba(0,0,0,0.03); min-width: 200px; text-align: center; margin-bottom: 15px;">
                                                            <div class="trial-icon-circle d-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px; background-color: #06498b; border-radius: 50%; color: #fff;">
                                                                <i class="fa fa-check" style="font-size: 20px;"></i>
                                                            </div>
                                                            <h6 style="font-size: 14px; font-weight: 700; color: #06498b; margin-bottom: 2px;">Free Trial</h6>
                                                            <p class="m-0" style="font-size: 13px; color: #555; font-weight: 600;">Available</p>
                                                        </div>
                                                    </div>

                                                </div>
                                                <livewire:compare-products :item="$item"
                                                    :wire:key="'compare-products-'.$item->id" />
                                            </div>
                                        @endforeach
                                        <!-- Empty state when no products at all -->
                                        @if ($products->isEmpty() && !$noMatchingProducts)
                                            <div class="alert alert-warning">
                                                <p>No products are currently available in this category.</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center mt-4">
                                        @if ($products->count() > 0)
                                            {{ $products->links() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:compare-bar />
            </section>
            <!-- Remaining sections stay the same -->
        </div>
        <section class="subs_sec light p_120 ">
            {{-- <div class="container">
                <div class="subs_content">
                    <div class="sub_img">
                        <img src="{{ asset('front/img/subs.png') }}">
                    </div>
                    <h2 data-aos="fade-up" data-aos-duration="1000">{{ static_text('top_rated_mail_section_titile') }}</h2>
                    <p data-aos="fade-up" data-aos-duration="1000">{{static_text('top_rated_mail_section_desc')}}
                    </p>
                    <div class="mail_field" data-aos="fade-up" data-aos-duration="1000">
                        <div class="email_box">
                            <input type="email" id="email" name="email" placeholder="Email Address*">
                        </div>
                        <div class="accor-btn sbs_bttn">
                            <a href="javascript:void(0)" class="cta cta_white">{{ static_text('subscribe')}}</a>
                        </div>
                    </div>
                    <div class="checkbox_field" data-aos="fade-up" data-aos-duration="1000" style="margin-top: 10px; display: flex; justify-content: center;">
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" id="agree_terms" name="agree_terms" required>
                            <span>{{ static_text('mail_below_text') }}</span>
                        </label>
                    </div>
                </div>
            </div> --}}
            <x-news-letter-subscription/>
        </section>
        <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize slider after Livewire is ready
                if (typeof window.Livewire !== 'undefined') {
                    // For Livewire 3
                    document.addEventListener('livewire:initialized', function() {
                        initPriceSlider();
                    });
                } else {
                    // For Livewire 2 or fallback
                    window.addEventListener('livewire:load', function() {
                        initPriceSlider();
                    });

                    // Fallback if neither event fires
                    setTimeout(initPriceSlider, 500);
                }

                function initPriceSlider() {
                    const slider = document.getElementById('priceRangeSlider');
                    if (!slider || typeof noUiSlider === 'undefined') return;

                    // Always clean up existing slider
                    if (slider.noUiSlider) {
                        slider.noUiSlider.destroy();
                    }

                    // Find the Livewire component
                    const wireEl = slider.closest('[wire\\:id]');
                    if (!wireEl) return;

                    const wireId = wireEl.getAttribute('wire:id');
                    if (!wireId) return;

                    // Get component reference - works with both Livewire 2 and 3
                    let component;
                    if (window.Livewire) {
                        component = window.Livewire.find ? window.Livewire.find(wireId) : window.Livewire.get(wireId);
                    }

                    if (!component) return;

                    // Get current values from component - compatible with both Livewire versions
                    const getComponentValue = (prop) => {
                        // Try Livewire 3 method first
                        if (typeof component.get === 'function') {
                            return component.get(prop);
                        }
                        // Fallback to direct property access (Livewire 2)
                        return component[prop];
                    };

                    const minValue = parseInt(getComponentValue('minPrice')) || 0;
                    const maxValue = parseInt(getComponentValue('maxPrice')) || 2000;

                    // Create slider with current values
                    noUiSlider.create(slider, {
                        start: [minValue, maxValue],
                        connect: true,
                        range: {
                            'min': 0,
                            'max': 2000
                        },
                        step: 50
                    });

                    // Prevent multiple rapid updates
                    let updateTimeout;

                    // Update internal values but don't trigger Livewire yet
                    slider.noUiSlider.on('update', function(values) {
                        const min = Math.round(values[0]);
                        const max = Math.round(values[1]);

                        // Just update the input fields visually
                        if (document.getElementById('minPriceInput')) {
                            document.getElementById('minPriceInput').value = min;
                        }

                        if (document.getElementById('maxPriceInput')) {
                            document.getElementById('maxPriceInput').value = max;
                        }
                    });

                    // Only when slider interaction ends, update Livewire once
                    slider.noUiSlider.on('end', function(values) {
                        clearTimeout(updateTimeout);

                        updateTimeout = setTimeout(() => {
                            const min = Math.round(values[0]);
                            const max = Math.round(values[1]);

                            // Get fresh component reference - works with both Livewire versions
                            let freshComponent;
                            if (window.Livewire) {
                                freshComponent = window.Livewire.find ? window.Livewire.find(wireId) :
                                    window.Livewire.get(wireId);
                            }

                            if (freshComponent) {
                                // Version-agnostic way to call Livewire method
                                if (typeof freshComponent.call === 'function') {
                                    // Livewire 3
                                    freshComponent.call('setPriceRange', min, max);
                                } else if (typeof freshComponent.setPriceRange === 'function') {
                                    // Livewire 2
                                    freshComponent.setPriceRange(min, max);
                                }
                            }
                        }, 300);
                    });
                }

                // Re-initialize slider on Livewire updates
                let pendingInit = false;

                // For Livewire 3
                document.addEventListener('livewire:update', function() {
                    handleUpdate();
                });

                // For Livewire 2
                window.addEventListener('livewire:update', function() {
                    handleUpdate();
                });

                function handleUpdate() {
                    if (!pendingInit) {
                        pendingInit = true;
                        setTimeout(() => {
                            initPriceSlider();
                            pendingInit = false;
                        }, 200);
                    }
                }
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const compareBar = document.getElementById('compareBar');
                const compareCount = document.getElementById('compareCount');

                // Safety check - only proceed if elements exist
                if (!compareBar || !compareCount) {
                    console.warn("Compare bar elements not found.");
                    return;
                }

                // Initialize comparison bar based on initial session state
                let initialCount = @json(session('compared_products', [])).length || 0;
                updateCompareBar(initialCount);

                function updateCompareBar(count) {
                    // Safety check inside the function too
                    if (!compareCount) return;

                    compareCount.textContent = count;

                    if (!compareBar) return;

                    if (count > 0) {
                        compareBar.style.display = 'block';
                    } else {
                        compareBar.style.display = 'none';
                    }
                }

                // Livewire v3 event listeners
                document.addEventListener('livewire:initialized', () => {
                    // Listen for our custom event
                    if (window.Livewire) {
                        Livewire.on('comparison-updated', (eventData) => {
                            updateCompareBar(eventData.count);
                        });
                    }
                });
            });
        </script>
     <script>
        window.addEventListener('scroll-to-middle', function() {
            const offset = window.innerHeight * 0.55;
            window.scrollTo({
                top: offset,
                behavior: 'smooth'
            });
        });
    </script>
    </div>
</div>
