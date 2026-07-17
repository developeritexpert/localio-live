<div>
    <section class="banner_sec help-cntr-bnr top-auto-bnr dark " style="background-color: #003F7D;">
        <div class="bubble-wrp" data-aos="fade-up" data-aos-duration="1000">
        </div>
        <div class="banner_content">
            <div class="container">
                <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                    <div class="banner_text_col">
                        <div class="banner_content_inner">
                            <div class="hd-share-flex d-flex align-items-center">
                                <h1>{{ static_text('exclusive_deals_title') }}</h1>
                            </div>
                            <p class="fw_700">{{ static_text('exclusive_deals_des') }}</p>
                        </div>
                    </div>
                    <div class="banner_image_col">
                        <div class="banner_image">
                            <img src="{{ asset('front/img/top-rated-bnr-bg.png') }}" class="banner_top_image">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <section class="top-automotive-sec light  ">
        <div class="top-auto-btm">
            <div class="container">
                <div class="top-auto-choice">
                    <div class="auto-choice-row d-flex ">
                        <div class="auto-choice-lft">
                            <div class="search-box">
                                {{-- <input type="text" wire:model.live="searchTerm"
                                    placeholder="{{ static_text('product_search_placeholder') }}">
                                <i class="fa fa-search"></i> --}}
                                <input type="text" wire:model.live.debounce.500ms="searchTerm"
                                placeholder="Search product">
                              <i class="fa fa-search"></i>
                            </div>
                            <div class="container">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                </div>
                                <div class="col-md-12">
                                    <!-- Rating Filter Section - Styled like the image -->
                                    <div class="filter-section">
                                        <h5>
                                            {{ static_text('user_rating') }}</h5>

                                        @foreach ([5, 4, 3, 2, 1] as $rating)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    wire:model.live="selectedRatings" value="{{ $rating }}"
                                                    id="rating-{{ $rating }}">
                                                <label class="form-check-label" for="rating-{{ $rating }}">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $rating)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-warning"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="exclusive-business">&
                                                        up</span>
                                                    <span class="exclusive-business1">
                                                        ({{ $ratingCounts[$rating] ?? 0 }})
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <link rel="stylesheet"
                                        href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" />

                                    <div class="filter-section mb-4 pb-3 border-bottom">
                                        <h3 class="fw-semibold text-dark mb-3">{{ static_text('price_range') }}</h3>

                                        <div class="price-slider-container">
                                            <div
                                                class="price-inputs d-flex justify-content-between align-items-center mt-3">
                                                <div class="price-input">
                                                    <span class="currency">$</span>
                                                    <input type="number" id="minPriceInput2" wire:model.live="minPrice"
                                                        min="0" max="5000"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <span class="price-separator">to</span>
                                                <div class="price-input">
                                                    <span class="currency">$</span>
                                                    <input type="number" id="maxPriceInput2" wire:model.live="maxPrice"
                                                        min="0" max="5000"
                                                        class="form-control form-control-sm">
                                                </div>
                                            </div>

                                            <div id="priceRangeSlider2" data-max-price="{{ $maxPriceValue }}"
                                                style="margin-top: 20px;" wire:ignore></div>
                                        </div>
                                    </div>
                                    <div class="accordion" id="filterAccordion" style="border: none; width: 100%;">
                                        @foreach ($filters as $filter)
                                            @php
                                                $filterName =
                                                    $filter->translations->where('language_id', $lang_id)->first()
                                                        ->name ?? $filter->name;
                                                $filterType = $filter->filterType
                                                    ? $filter->filterType->slug
                                                    : 'checkbox';
                                            @endphp

                                            <div class="filter-section">
                                                <h3>
                                                    {{ $filterName }}
                                                </h3>

                                                <div class="accordion-body" style="padding: 0;">
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
                                                                $optionName =
                                                                    $option->translations
                                                                        ->where('language_id', $lang_id)
                                                                        ->first()->name ?? $option->name;
                                                            @endphp
                                                            <div class="form-check" style="margin-bottom: 8px;">
                                                                <input type="radio" class="form-check-input"
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
                                                        <div class="mb-4">
                                                            <select
                                                                class="form-select w-full p-2 border border-gray-300 rounded-md text-sm"
                                                                wire:model.live="selectedOptions.{{ $filter->id }}">
                                                                <option value="">{{ __('Select') }}</option>
                                                                @foreach ($filter->options as $option)
                                                                    @php
                                                                        $optionName =
                                                                            $option->translations
                                                                                ->where('language_id', $lang_id)
                                                                                ->first()->name ?? $option->name;
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
                                                                $optionName = $optionTranslation->name ?? $option->name;
                                                                $onLabel =
                                                                    $optionTranslation->on_label ??
                                                                    ($option->on_label ?? 'On');
                                                                $offLabel =
                                                                    $optionTranslation->off_label ??
                                                                    ($option->off_label ?? 'Off');
                                                                $isChecked = in_array($option->id, $selectedOptions);
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
                                                                        $optionTranslation->name ?? $option->name;
                                                                    // Get color value from option or fallback to a default
                                                                    $colorValue =
                                                                        $optionTranslation->color_value ?? '#cccccc';
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
                                                                                stroke="currentColor" stroke-width="2"
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
                        @if ($products->count())
                            <div class="auto-choice-rgt">
                                <!-- Product Count and Sort -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        @if ($products->count() > 0)
                                            @php
                                                $currentPage = $products->currentPage();
                                                $perPage = $products->perPage();
                                                $total = $products->total();
                                                $from = ($currentPage - 1) * $perPage + 1;
                                                $to = min($currentPage * $perPage, $total);
                                            @endphp
                                            <p class="m-0">
                                                Showing {{ $from }} - {{ $to }} of
                                                {{ $total }} results
                                            </p>
                                        @else
                                            <p class="m-0">Showing {{ $products->count() }} results</p>
                                        @endif
                                    </div>
                                    <div wire:ignore>
                                    <x-social-icon/>
                                </div>
                                </div>

                                @if ($products->isNotEmpty())
                                {{-- {{ dd($products); }} --}}
                                    @foreach ($products as $item)
                                        <div class="automotive-card automotive_card_expert  auto-bg"
                                            data-aos="fade-up" data-aos-duration="1000"
                                            wire:key="product-{{ $item->id }}">


                                            <div class="auto-choice-card position-relative">

                                                {{-- Best Value Badge --}}
                                                @php
                                                    $hasExclusiveDeal = false;
                                                    $maxDiscountPercentage = 0;

                                                    foreach ($item->products as $product) {
                                                        if (
                                                            isset($product->has_exclusive_deal) &&
                                                            $product->has_exclusive_deal &&
                                                            $product->discount_percentage > $maxDiscountPercentage
                                                        ) {
                                                            $hasExclusiveDeal = true;
                                                            $maxDiscountPercentage = $product->discount_percentage;
                                                        }
                                                    }
                                                @endphp
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
                                                                    <div
                                                                        wire:key="wishlist-container-{{ $item->id }}">
                                                                        @livewire('wishlist', ['productId' => $item->id], key('wishlist-' . $item->id))
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tp-btm d-flex flex-col-mob">
                                                                <div class="inn_ul">
                                                                    <div class="rating-stars">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            @if ($i <= floor($item->reviews->avg('rating')))
                                                                                <i
                                                                                    class="fas fa-star text-warning"></i>
                                                                            @elseif ($i - 0.5 <= $item->reviews->avg('rating'))
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
                                                                    {{ number_format($item->reviews->avg('rating'), 1) }}
                                                                    |
                                                                    {{ $item->reviews->count() }} Reviews
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        <div class="auto-choice-btn">
                                                            <a href="{{ $item->affiliate_link ?? $item->permanent_url }}"
                                                                class="cta cta_orange d-flex align-items-center justify-content-center">
                                                                Visit website
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                            </a>
                                                        </div>
                                                </div>

                                                <div class="text-choice">
                                                    <p>{{ $item->translations->first()->description }}</p>
                                                    <a
                                                        onmouseover="this.style.color='#F9633B'"
                                                        onmouseout="this.style.color='#002347'"
                                                        onmousedown="this.style.color='#F9633B'"
                                                        onmouseup="this.style.color='#F9633B'"
                                                        href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}">
                                                        Read More
                                                    </a>
                                                </div>
                                                {{-- Key Features and Regular Pricing --}}
                                                <div class="key-feature-price d-flex align-items-center">
                                                    @if ($hasExclusiveDeal)
                                                        <div class="top-deals-section">
                                                            <h6 class="deals-title">

                                                                Top Deals – Now {{ $maxDiscountPercentage }}% Off
                                                            </h6>

                                                            @php
                                                                $dealsData = [];
                                                                foreach ($item->products as $product) {
                                                                    if (
                                                                        isset($product->has_exclusive_deal) &&
                                                                        $product->has_exclusive_deal
                                                                    ) {
                                                                        $translation = $product->translations->first();
                                                                        if ($translation) {
                                                                            $dealsData[] = [
                                                                                'name' => $translation->name,
                                                                                'original_price' =>
                                                                                    $product->original_price,
                                                                                'discounted_price' =>
                                                                                    $product->discounted_price,
                                                                                'currency' => $product->currency,
                                                                                'discount_percentage' =>
                                                                                    $product->discount_percentage,
                                                                            ];
                                                                        }
                                                                    }
                                                                }
                                                            @endphp

                                                            <div class="deals-list">
                                                                @foreach (array_slice($dealsData, 0, 3) as $deal)
                                                                    <div class="deal-item">
                                                                        <div class="deal-check">

                                                                        </div>
                                                                        <div class="deal-content">
                                                                            <!-- <p class="deal-description">{{ $deal['name'] }}</p> -->

                                                                        </div>
                                                                    </div>
                                                                @endforeach

                                                                <ul class="list-unstyled key-fea-lst">
                                                                    @if (!empty($item->features))
                                                                        @foreach ($item->features as $feature)
                                                                            <li class="d-flex align-items-center">
                                                                                <div class="grn_chk">
                                                                                    <i
                                                                                        class="fas fa-check-circle text-success"></i>
                                                                                </div>
                                                                                <p class="m-0">
                                                                                    {{ $feature->translations->first()->name }}
                                                                                </p>
                                                                            </li>
                                                                        @endforeach
                                                                    @endif
                                                                </ul>

                                                                @if (count($dealsData) > 3)
                                                                    <div class="deal-item">
                                                                        <div class="deal-check">
                                                                            <i
                                                                                class="fas fa-check-circle text-success"></i>
                                                                        </div>
                                                                        <div class="deal-content">
                                                                            <p class="deal-description">And
                                                                                {{ count($dealsData) - 3 }} more
                                                                                exclusive deals...</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <!-- <div class="save-percentage-badge">
                                                        Save {{ $maxDiscountPercentage }}%
                                                    </div> -->
                                                        </div>
                                                    @endif

                                                    @php
                                                        $allPrices = $item->products
                                                            ->flatMap(function ($product) {
                                                                return $product->prices;
                                                            })
                                                            ->sortBy(function ($price) {
                                                                $now = Illuminate\Support\Carbon::now();
                                                                if (
                                                                    $price->discount_price &&
                                                                    $price->discount_expiration_date &&
                                                                    $now->lte(
                                                                        Illuminate\Support\Carbon::parse(
                                                                            $price->discount_expiration_date,
                                                                        ),
                                                                    )
                                                                ) {
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
                                                            if (
                                                                $startingPrice->discount_price &&
                                                                $startingPrice->discount_expiration_date &&
                                                                $now->lte(
                                                                    Illuminate\Support\Carbon::parse(
                                                                        $startingPrice->discount_expiration_date,
                                                                    ),
                                                                )
                                                            ) {
                                                                $displayPrice = $startingPrice->discount_price;
                                                            } elseif ($startingPrice->renewal_price) {
                                                                $displayPrice = $startingPrice->renewal_price;
                                                            } else {
                                                                $displayPrice = $startingPrice->price;
                                                            }
                                                        }
                                                    @endphp

                                                    @if ($startingPrice)
                                                        <div class="starting-price p-0">
                                                            <h6 class="m-0"> <span class="original-price">Original
                                                                    Price:
                                                                    {{ $deal['currency'] }}{{ number_format($deal['original_price'], 2) }}/year</span>
                                                            </h6>
                                                            <div class="deal-pricing">
                                                                <!-- <span class="original-price">Original Price: $499.00/year</span> -->
                                                                <span class="discount-text">
                                                                    <strong>94% OFF</strong> <i
                                                                        class="fa-solid fa-arrow-right"></i> Now:
                                                                    <strong
                                                                        class="discounted-price">$30.00/year</strong>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="save-percentage-badge">
                                                    Save {{ $maxDiscountPercentage }}%
                                                </div>
                                            </div>
                                            <livewire:compare-products :item="$item" :key="'compare-' . $item->id" />
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Pagination Links -->
                                @php
                                // Get pagination data from Livewire component
                                $currentPage = $this->page ?? 1;
                                $lastPage = $products->lastPage() ?? 1;
                                $maxVisible = 7;

                                // Calculate visible page range
                                $startPage = max(1, $currentPage - floor($maxVisible / 2));
                                $endPage = min($lastPage, $startPage + $maxVisible - 1);

                                // Adjust start page if we're near the end
                                if ($endPage - $startPage + 1 < $maxVisible) {
                                    $startPage = max(1, $endPage - $maxVisible + 1);
                                }

                                $showLeftDots = $startPage > 2;
                                $showRightDots = $endPage < $lastPage - 1;
                            @endphp

                           <div class="btn-pages">
                             @if($lastPage > 1)
                                {{-- Previous Button --}}
                                <button
                                    wire:click="previousPage"
                                    class="pagination-btn pagination-arrow {{ $currentPage <= 1 ? 'disabled' : '' }}"
                                    {{ $currentPage <= 1 ? 'disabled' : '' }}
                                >
                                <i class="fa-solid fa-chevron-left"></i>
                                </button>

                                {{-- First Page --}}
                                @if($startPage > 1)
                                    <button
                                        wire:click="gotoPage(1)"
                                        class="pagination-btn {{ $currentPage == 1 ? 'active' : '' }}"
                                    >
                                        1
                                    </button>

                                    @if($showLeftDots)
                                        <span class="pagination-dots">...</span>
                                    @endif
                                @endif

                                {{-- Page Numbers --}}
                                @for($page = $startPage; $page <= $endPage; $page++)
                                    <button
                                        wire:click="gotoPage({{ $page }})"
                                        class="pagination-btn {{ $currentPage == $page ? 'active' : '' }}"
                                    >
                                        {{ $page }}
                                    </button>
                                @endfor

                                {{-- Last Page --}}
                                @if($endPage < $lastPage)
                                    @if($showRightDots)
                                        <span class="pagination-dots">...</span>
                                    @endif

                                    <button
                                        wire:click="gotoPage({{ $lastPage }})"
                                        class="pagination-btn {{ $currentPage == $lastPage ? 'active' : '' }}"
                                    >
                                        {{ $lastPage }}
                                    </button>
                                @endif

                                {{-- Next Button --}}
                                <button
                                    wire:click="nextPage"
                                    class="pagination-btn pagination-arrow next {{ $currentPage >= $lastPage ? 'disabled' : '' }}"
                                    {{ $currentPage >= $lastPage ? 'disabled' : '' }}
                                >
                                   <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            @endif
                           </div>
                        @else

                        <div class="auto-choice-rgt">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="m-0">Showing {{ $products->count() }} results </p>
                                </div>
                                <x-social-icon/>
                            </div>

                            <div class="alert alert-info">
                                <p class="mb-2 text-center fs-4 text-secondary">Sorry, we don't have any Deals that match your filters. Try adjusting them to see more options.</p>
                            </div>
                        </div>

                           {{-- <p class="m-0">Showing {{ $products->count() }} results</p> --}}


                            {{-- <div class="auto-choice-rgt" style="position: relative; min-height: 300px;">
                                <div style="position: absolute; top: 0; left: 0; z-index: 1050; margin: 1rem;">
                                    <div class="alert alert-info text-start shadow" style="max-width: 320px;">
                                        @if ($this->hasActiveFilters())
                                        <p class="mb-3">{{ static_text('no_prod_mach_fil') }}</p>
                                        <button wire:click="clearFilters" class="btn btn-primary btn-sm">
                                            <i class="fa fa-refresh me-1"></i> {{ static_text('reset_filter') }}
                                        </button>
                                        @else
                                        <p class="mb-0">
                                            Sorry ,No Exclusive-deals available at the moment.
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div> --}}
                        @endif
                     </div>
                 </div>
             </div>


     </div>

    </section>

    <!-- In layout/footer or outside Livewire main wrapper -->
    <livewire:compare-bar />

    <section class="subs_sec light p_120 ">
        <x-news-letter-subscription/>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
    <script>
        function initPriceSlider() {
            // Give a short delay to make sure DOM is fully ready
            setTimeout(() => {
                const slider = document.getElementById('priceRangeSlider2');
                const minInput = document.getElementById('minPriceInput2');
                const maxInput = document.getElementById('maxPriceInput2');
                // Get the dynamic maximum price from the data attribute
                const maxPriceValue = slider.dataset.maxPrice ? parseInt(slider.dataset.maxPrice) : 10000;

                if (!slider || !minInput || !maxInput || typeof noUiSlider === 'undefined') {
                    console.warn("Slider init failed.");
                    return;
                }

                if (slider.noUiSlider) {
                    slider.noUiSlider.destroy();
                }

                noUiSlider.create(slider, {
                    start: [parseInt(minInput.value) || 0, parseInt(maxInput.value) || maxPriceValue],
                    connect: true,
                    range: {
                        min: 0,
                        max: maxPriceValue
                    },
                    step: Math.max(1, Math.floor(maxPriceValue / 100)) // Dynamic step based on max price
                });

                slider.noUiSlider.on('update', function(values) {
                    const min = Math.round(values[0]);
                    const max = Math.round(values[1]);
                    minInput.value = min;
                    maxInput.value = max;
                });

                slider.noUiSlider.on('change', function() {
                    minInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    maxInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                });

                minInput.addEventListener('change', function() {
                    slider.noUiSlider.set([this.value, null]);
                });

                maxInput.addEventListener('change', function() {
                    slider.noUiSlider.set([null, this.value]);
                });
            }, 100); // slight delay to ensure DOM is updated
        }

        document.addEventListener('DOMContentLoaded', initPriceSlider);
        document.addEventListener('livewire:load', initPriceSlider);

        // Set up a listener for Livewire events that might update max price
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('set-price-range', (data) => {
                const slider = document.getElementById('priceRangeSlider2');
                if (slider && data.maxPrice) {
                    slider.dataset.maxPrice = data.maxPrice;
                    // Re-initialize slider with the new max value
                    initPriceSlider();
                }
            });
        });
        Livewire.hook('message.processed', (message, component) => {
            initPriceSlider(); // Re-initialize after DOM is updated by Livewire
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
