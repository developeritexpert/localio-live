<div>
    <style>
    .automotive-card.auto-bg.aos-init.aos-animate {
        background-color: #f7f9fb !important;
    }

    label.form-check-label span.filter1 {
        padding-left: 5px;
    }

    label.form-check-label {
        cursor: pointer;
    }

    .automotive-card {
        box-shadow: 0px 34px 74px 0px #0023470f;
    }

    .automotive-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .top-rated-heading-block h1 {
        font-size: 28px !important;
        font-weight: 700;
        padding: 0 !important;
        margin-bottom: 4px !important;
        color: #002347 !important;

    }

    section.top-automotive-sec.top_rate_pg.light {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .top-rated-heading-sec {
        margin-top: 115px;
        padding-top: 30px;
        background-color: #f7f9fb;
        border-bottom: 1px solid #e2e8f0;
        /* margin-bottom: 25px; */
    }

    .top-rated-heading-block {
        padding-bottom: 20px;
    }

    .top-rated-heading-sec .bread_row {
        /* margin-top:20px; */
    }

    .top-rated-heading-sec .row {
        /* padding-bottom:20px; */

    }

    /* View details button – match height of Visit website */
    .auto-choice-btn .cta_outline {
        padding: 8px 16px !important;
        height: auto !important;
        /* min-height: 44px !important; */
        box-sizing: border-box !important;
        line-height: 1.5 !important;
    }

    .automotive-card .blue-chkbox {
        bottom: 0 !important;
        transition: all 0.3s ease;
        right: unset;
        left: -30px
    }

    .usp-grid-container {
        display: grid !important;
        grid-template-columns: auto auto !important;
        justify-content: start !important;
        gap: 8px 45px !important;
        width: 100% !important;
    }

    @media (max-width: 768px) {

        /* .automotive-card {
                padding-bottom: 20px !important;
            } */
        /* .automotive-card .blue-chkbox {
                position: relative !important;
                bottom: auto !important;
                right: auto !important;
                border-radius: 8px !important;
                display: block !important;
                width: 100% !important;
                text-align: center !important;
                margin-top: 15px !important;
                padding: 12px 15px !important;
            } */
        .automotive-card .blue-chkbox label {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
        }

        .usp-grid-container {
            grid-template-columns: 1fr !important;
            gap: 8px 0px !important;
        }

        .key-feature-price {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        .starting-price-box,
        .free-trial-box {
            width: 100% !important;
            min-width: 100% !important;
            margin-bottom: 10px !important;
        }

        #myID.search-box {
            visibility: visible !important;
            display: block !important;
        }

        /* .top-automotive-sec.top_rate_pg {
            padding-top: 160px !important;
        } */
        .top-rated-heading-block {
            /* margin-left: 27%; */
            margin-bottom: 24px;
            padding-bottom: 16px;
            /* border-bottom: 2px solid #e8eef6; */
        }

        .top-rated-heading-block h1 {
            /* font-size: 34px;
            font-weight: 800;
            color: #002347;
            margin: 0 0 5px 0;
            letter-spacing: -0.5px;
            line-height: 1.2; */
            font-size: 28px !important;
            font-weight: 700;
            padding: 0 !important;
            margin-bottom: 4px !important;
            color: #002347 !important;
        }

        .top-rated-heading-block p {
            font-size: 15px;
            color: #7a8ea8;
            margin: 0;
            font-weight: 400;
        }
    }

    @media (max-width: 768px) {

        /* .top-automotive-sec.top_rate_pg {
                padding-top: 110px !important;
            } */
        .top-rated-heading-block h1 {
            font-size: 24px;
        }

        .top-auto-choice {
            padding-top: 0;
        }

        section.top-automotive-sec.top_rate_pg.light {
            margin-top: 0 !important;
        }
    }

    /* Explore categories exact styling matching all-categories page */
    .explore-categories-sec .top-products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    @media(max-width: 991px) {
        .explore-categories-sec .top-products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media(max-width: 575px) {
        .explore-categories-sec .top-products-grid {
            grid-template-columns: 1fr;
        }
    }
    .explore-categories-sec .top-product-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        background: #fdfdfd !important;
        transition: all 0.2s ease;
    }
    .explore-categories-sec .top-product-card:hover {
        box-shadow: 0px 0px 16px 0px rgb(0 0 0 / 13%) !important;
    }
    .explore-categories-sec .top-product-info h6 {
        font-size: 14px;
        font-weight: 700;
        color: #1e3050;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: color 0.2s ease;
    }
    .explore-categories-sec .top-product-card:hover .top-product-info h6 {
        color: #e56b46;
    }
    .explore-categories-sec .top-product-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: #777;
        margin-top: 4px;
    }
    .explore-categories-sec .top-product-stars {
        color: #ff5722 !important;
        font-size: 11px;
        display: flex;
        align-items: center;
    }
    .explore-categories-sec .btn-view-details {
        border: 1.5px solid #174889 !important;
        border-radius: 30px !important;
        background: transparent !important;
        color: #06498b !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .explore-categories-sec .btn-view-details:hover {
        background: #174889 !important;
        color: #fff !important;
        text-decoration: none !important;
    }
    .explore-categories-sec .btn-orng {
        background: #ff5722 !important;
        border: 1px solid #ff5722 !important;
        border-radius: 30px !important;
        color: #fff !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: opacity 0.2s ease !important;
    }
    .explore-categories-sec .btn-orng:hover {
        opacity: 0.9 !important;
        color: #fff !important;
    }
    .explore-categories-sec .subcat-link:hover {
        text-decoration: underline !important;
    }

    </style>
    <section class="top-rated-heading-sec">
        <div class="container">
            <div class=" bread_row row align-items-center mb-1">
                <div class="col-8">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb m-0"
                            style="background: transparent;padding: 0;display: flex;align-items: center;">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/' . (request()->segment(1) ?? 'en-us') . '/categories') }}"
                                    style="color: inherit; text-decoration: none; font-size: 13px;"
                                    onmouseout="this.style.color=''">All</a>
                            </li>

                            <li class="breadcrumb-item">
                                <!-- <a href="#"
                                    style="font-size: 13px; color: #1e3050 !important; text-decoration:none; font-weight: 500;">
                                    Top rated on Localio
                                </a> -->
                                <span style=" color: #1e3050 !important; text-decoration:none; font-weight: 500;">Top rated on Localio</span>
                                
                            </li>


                        </ol>
                    </nav>
                </div>
                <div class="col-4 d-flex justify-content-end">
                    <x-social-icon />
                </div>
            </div>
            <div class="top-rated-heading-block">
                <div class="row align-items-start">
                    <div class="col-md-8 text-start">
                        <h1 style="color: #1e3050; font-weight: 700; margin-bottom: 8px;">Top rated on Localio </h1>
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">Last updated on
                            {{ now()->format('F j, Y') }}</p>
                        <p style="font-size: 15px; color: #444; margin-bottom: 0;">
                            Learn more from our team about Website Builder Software pricing features and benefits in our
                            Website Builder Buyers Guide
                        </p>
                    </div>
                    <div class="col-md-4 mt-4 mt-md-0 text-start">
                        <div class="verified-insights-card"
                            style="background-color: #f8fafc; border-radius: 8px; padding: 16px; border: 1px solid #e2e8f0; text-align: left;">
                            <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                                <!-- <img src="{{ asset('user-dashboard-theme/img/bell_icon.svg') }}"
                                    style="width: 20px; height: 20px;" alt="Verified"> -->
                                    <i class="far fa-star text-warning" style="margin-top: -4px; color: #1e3050 !important;"></i>
                                <h6 style="margin: 0; font-weight: 700; color: #1e3050; font-size: 16px;">Real
                                    experiences</h6>
                            </div>
                            <p style="font-size: 13px; color: #555; margin-bottom: 8px; line-height: 1.5;">
                                Ratings and reviews are shared by real users from the Localio community.
                            </p>
                            <a href="javascript:void(0)" onclick="openRankingsModal()" class="learn_mre_btn"
                                style="font-size: 13px; color: #06498b; font-weight: 600; text-decoration: none;">How
                                rankings work</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="top-automotive-sec top_rate_pg light  " style="padding-top:25px !important;">
        <div class="top-auto-btm">
            <div class="container">
                <div class="top-auto-choice">
                    <!-- <div class="top-rated-heading-block" style=" padding-bottom: 16px; margin-bottom: 24px;">
                        <div class="row align-items-start">
                            <div class="col-md-8 text-start">
                                <h1 style="color: #1e3050; font-weight: 700; margin-bottom: 8px;">Top-rated products</h1>
                                <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">Last updated on {{ now()->format('F j, Y') }}</p>
                                <p style="font-size: 15px; color: #444; margin-bottom: 0;">
                                    Learn more from our team about Website Builder Software pricing features and benefits in our Website Builder Buyers Guide
                                </p>
                            </div>
                            <div class="col-md-4 mt-4 mt-md-0 text-start">
                                <div class="verified-insights-card" style="background-color: #f8fafc; border-radius: 8px; padding: 16px; border: 1px solid #e2e8f0; text-align: left;">
                                    <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                                        <img src="{{ asset('user-dashboard-theme/img/bell_icon.svg') }}" style="width: 20px; height: 20px;" alt="Verified">
                                        <h6 style="margin: 0; font-weight: 700; color: #1e3050; font-size: 16px;">Real Ratings</h6>
                                    </div>
                                    <p style="font-size: 13px; color: #555; margin-bottom: 8px; line-height: 1.5;">
                                        Provider data verified by our Software Research team and reviews moderated by our Reviews Verification team.
                                    </p>
                                    <a href="javascript:void(0)" onclick="openModal()" class="learn_mre_btn" style="font-size: 13px; color: #06498b; font-weight: 600; text-decoration: none;">Learn more</a>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="auto-choice-row d-flex ">
                        <div class="auto-choice-lft">
                            <div class="container">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                </div>
                                <div class="col-md-12">
                                    <!-- Rating Filter Section - Styled like the image -->
                                    <div class="filter-section">
                                        <h3 class="fw-semibold text-dark mb-2">
                                            <!-- {{ static_text('user_rating') }} -->
                                            Community rating
                                        </h3>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input"
                                                wire:model.live="selectedRatings" value="4" id="rating-4">
                                            <label class="form-check-label d-flex align-items-center gap-1" for="rating-4" style="cursor: pointer;">
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="far fa-star text-warning"></i>
                                                <span class="ms-1" style="font-weight: 500; font-size: 14px; color: #334155;">4+</span>
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                wire:model.live="selectedRatings" value="3" id="rating-3">
                                            <label class="form-check-label d-flex align-items-center gap-1" for="rating-3" style="cursor: pointer;">
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="far fa-star text-warning"></i>
                                                <i class="far fa-star text-warning"></i>
                                                <span class="ms-1" style="font-weight: 500; font-size: 14px; color: #334155;">3+</span>
                                            </label>
                                        </div>
                                    </div>

                                    <link rel="stylesheet"
                                        href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" />

                                    <div class="filter-section mt-3 mb-3 pb-3 border-bottom pric_rnge">
                                        <h3 class="fw-semibold text-dark mb-3">
                                            <!-- {{ static_text('price_range') }} -->
                                            Price range

                                        </h3>

                                        <div class="price-slider-container">
                                            <div class="price-inputs d-flex gap-2 align-items-center mt-3">
                                                <div class="price-input">
                                                    <span class="currency">$</span>
                                                    <input type="number" id="minPriceInput2" wire:model.live="minPrice"
                                                        min="0" max="5000" class="form-control form-control-sm">
                                                </div>
                                                <span class="price-separator">to</span>
                                                <div class="price-input">
                                                    <span class="currency">$</span>
                                                    <input type="number" id="maxPriceInput2" wire:model.live="maxPrice"
                                                        min="0" max="5000" class="form-control form-control-sm">
                                                </div>
                                            </div>

                                            <div id="priceRangeSlider2"
                                                data-max-price="{{ $maxPriceValue ?? $maxPrice ?? 10000 }}"
                                                style="margin-top: 20px;" wire:ignore></div>
                                        </div>
                                    </div>
                                    <div class="accordion d-none" id="filterAccordion"
                                        style="border: none; width: 100%;">
                                        @foreach ($filters as $filter)
                                        @php
                                        $currentLangId = $lang_id ?? getCurrentLanguageID();
                                        $filterName =
                                        $filter->translations->where('language_id', $currentLangId)->first()
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
                                                <div class="form-check" style="margin-bottom: 5px;">
                                                    <input type="checkbox" class="form-check-input"
                                                        wire:model.live="selectedOptions" value="{{ $option->id }}"
                                                        id="option-{{ $option->id }}"
                                                        style="margin-right: 8px; cursor: pointer;">
                                                    <label class="form-check-label" for="option-{{ $option->id }}"
                                                        style="font-size: 13px; color: #555; cursor: pointer;">
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
                                                <div class="form-check" style="margin-bottom: 5px;">
                                                    <input type="radio" class="form-check-input"
                                                        name="filter_{{ $filter->id }}"
                                                        wire:key="radio-{{ $filter->id }}-{{ $option->id }}"
                                                        wire:click="toggleFilterOption({{ $option->id }})"
                                                        {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}
                                                        value="{{ $option->id }}" id="option-{{ $option->id }}"
                                                        style="margin-right: 8px; cursor: pointer;">
                                                    <label class="form-check-label" for="option-{{ $option->id }}"
                                                        style="font-size: 13px; color: #555; cursor: pointer;">
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
                                                    <label class="toggle-label flex items-center cursor-pointer">
                                                        <div class="relative">
                                                            <input type="checkbox" wire:model.live="selectedOptions"
                                                                value="{{ $option->id }}" class="sr-only peer"
                                                                {{ $isChecked ? 'checked' : '' }}>

                                                            <div
                                                                class="w-12 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 transition-colors">
                                                            </div>
                                                            <div
                                                                class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6">
                                                            </div>
                                                        </div>
                                                        <div class="ml-3">
                                                            <span class="font-medium">{{ $optionName }}</span><br>
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

                                                    <button wire:click="toggleFilterOption({{ $option->id }})"
                                                        class="color-option w-6 h-6 rounded-full border {{ $isSelected ? 'border-black' : 'border-gray-300' }}"
                                                        style="background-color: {{ $colorValue }}; position: relative;"
                                                        title="{{ $colorName }}">
                                                        @if ($isSelected)
                                                        <span
                                                            class="absolute inset-0 flex items-center justify-center text-white">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                height="12" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
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
                        <div class="auto-choice-rgt ">
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
                                        Showing {{$from}}-{{$to}} of {{$total}}
                                    </p>
                                    @else
                                    <p class="m-0">Showing {{ $products->count() }} results</p>
                                    @endif
                                </div>
                                @php
                                $sortOptionsMap = [
                                'highest_rated' => 'Highest rated',
                                'most_reviewed' => 'Most reviewed',
                                'most_recommended' => 'Most recommended',
                                'price_low_high' => 'Price: low to high',
                                'price_high_low' => 'Price: high to low',
                                'name_a_z' => 'Name: A–Z',
                                'name_z_a' => 'Name: Z–A',
                                ];
                                $currentSort = $sortBy ?? 'highest_rated';
                                $currentLabel = $sortOptionsMap[$currentSort] ?? 'Highest rated';
                                @endphp

                                <div class="position-relative d-inline-block" id="sortDropdownWrapper">
                                    <button type="button" id="sortToggleBtn"
                                        class="sorting d-inline-flex align-items-center gap-2"
                                        style="background-color:#fdfdfd; color: #0f172a; border-radius: 20px; padding: 7px 16px; border: 1px solid #cbd5e1; outline: none; cursor: pointer;">
                                        <span>Sort: <span>{{ $currentLabel }}</span></span>
                                        <svg id="sortToggleArrow" xmlns="http://www.w3.org/2000/svg" width="14"
                                            height="14" viewBox="0 0 24 24" fill="none" stroke="#475569"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                            style="transition: transform 0.2s ease;">
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </button>

                                    {{-- Always rendered, just hidden with CSS --}}
                                    <div id="sortDropdownMenu" class="position-absolute end-0 mt-2 bg-white py-2 d-none"
                                        style="z-index: 9999; min-width: 230px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                                        @foreach($sortOptionsMap as $val => $label)
                                        <button type="button" wire:click="setSortBy('{{ $val }}')"
                                            class="d-flex align-items-center justify-content-between w-100 px-3 py-2"
                                            style="font-size: 13px; color: #1e3050; cursor: pointer; transition: background-color 0.15s ease; background: {{ $currentSort === $val ? '#f8fafc' : 'transparent' }}; border: none; text-align: left;"
                                            onmouseover="this.style.backgroundColor='#f1f5f9'"
                                            onmouseout="this.style.backgroundColor='{{ $currentSort === $val ? '#f8fafc' : 'transparent' }}'">
                                            <span
                                                style="{{ $currentSort === $val ? 'font-weight: 500;' : 'font-weight: 400;' }}">{{ $label }}</span>
                                            @if($currentSort === $val)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="#1e3050" stroke-width="2.5"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                style="flex-shrink: 0; margin-left: 8px;">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                            @endif
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @if (!empty($products))
                            @foreach ($products as $index => $item)
                            <div class="automotive-card auto-bg" data-aos="fade-up" data-aos-duration="1000"
                                wire:key="product-{{ $item->id }}">
                                <div class="auto-choice-card" style="position: relative; ">
                                    @php
                                    $topAffId = $this->topAffiliatedBusinessId;
                                    $isRecommended = $item->is_affiliate && ($item->id == $topAffId || ($topAffId ===
                                    null && $index === 0));
                                    @endphp
                                    <div class="card-compare-m">
                                        @php
                                        $activeReviews = $item->reviews ? $item->reviews->where('status', 'active') :
                                        collect();
                                        $totalRevCount = $activeReviews->count();
                                        $recCount = $activeReviews->where('recommend', 1)->count();
                                        $recPercent = $totalRevCount > 0 ? round(($recCount / $totalRevCount) * 100) :
                                        0;
                                        @endphp

                                        @if($totalRevCount > 0 && !empty($item->is_affiliate))
                                        <div class="d-flex align-items-center recommended-per"
                                            style="color: #002347; font-size: 13px; font-weight: 600;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="#002347" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                style="margin-right: 5px; flex-shrink: 0;">
                                                <path
                                                    d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3">
                                                </path>
                                            </svg>
                                            <span>{{ $recPercent }}% recommend this</span>
                                        </div>
                                        @endif
                                        @if($isRecommended)
                                        <div class="best-value-inline-container" style="padding-bottom:20px">
                                            <div class="best-value-inline">
                                                <!-- <i class="fa-regular fa-thumbs-up"></i> -->
                                                <i class="far fa-star text-warning"></i>
                                                <span style="text-transform: none !important;">Top choice</span>
                                            </div>

                                        </div>
                                        @endif

                                        <div
                                            style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: stretch; gap: 20px; width: 100%;">
                                            <!-- Left Column -->
                                            <div
                                                style="flex: 1 1 0%; min-width: 320px; display: flex; flex-direction: column; justify-content: flex-start;">
                                                <!-- Logo & Title -->
                                                <div class="auto-choice-hd"
                                                    style="border: none; padding: 0; margin-bottom: 0;">
                                                    <div class="inn_sl_hed" style="width: 100%;">
                                                        <a
                                                            href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}">
                                                            <div class="top-product-logo">
                                                                <x-business-logo :business="$item" />
                                                            </div>
                                                        </a>
                                                        <div class="sl_h">
                                                            <div class="inn_h">
                                                                <div class="sl_main">
                                                                    <h6 class="head">
                                                                        {{ $item->translations->first()->name }}</h6>
                                                                    <div class="d-none"
                                                                        wire:key="wishlist-container-{{ $item->id }}">
                                                                        @livewire('wishlist', ['productId' =>
                                                                        $item->id], key('wishlist-' . $item->id))
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="rating-group  d-flex flex-col-mob">
                                                                <span class="rate_box_num"
                                                                    style="">{{ number_format($item->reviews->avg('rating'), 1) }}</span>
                                                                <div class="">
                                                                    <div class="rating-stars " style="gap:1px;">
                                                                        @for ($i = 1; $i <= 5; $i++) @if ($i
                                                                            <=floor($item->reviews->avg('rating')))
                                                                            <i class="fas fa-star text-warning"></i>
                                                                            @elseif ($i - 0.5 <= $item->
                                                                                reviews->avg('rating'))
                                                                                <i
                                                                                    class="fas fa-star-half-alt text-warning"></i>
                                                                                @else
                                                                                <i class="far fa-star text-warning"></i>
                                                                                @endif
                                                                                @endfor
                                                                    </div>
                                                                </div>
                                                                <span class="" style="">
                                                                    ({{ $item->reviews->count() }})
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Short Description -->
                                                @if(!empty($item->translations->first()->short_description))
                                                <div class="mb-3 mt-1 text-start"
                                                    style="font-size: 14px; color: #444; line-height: 1.5; width: 100%;">
                                                    {{ $item->translations->first()->short_description }}
                                                </div>
                                                @endif

                                                <!-- Features -->
                                                @if (!empty($item->is_affiliate))
                                                <div class="slider_content_sec my-3"
                                                    style="width: 100% !important; max-width: 100% !important;">
                                                    <div class="main_feature_lg"
                                                        style="width: 100% !important; max-width: 100% !important;">
                                                        <div class="feture_box lft_check_box size18"
                                                            style="border: none; padding: 0; background: transparent; min-height: auto; width: 100% !important; max-width: 100% !important;">
                                                            <div class="usp-grid-container"
                                                                style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                                                @if ($item->usps->count() > 0)
                                                                @foreach ($item->usps->take(4) as $usp)
                                                                <div class="d-flex align-items-center size18">
                                                                    <div class="grn_chk"
                                                                        style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                        <img src="{{ asset('front/img/green-tick.svg') }}"
                                                                            style="width: 100%; height: auto;">
                                                                    </div>
                                                                    <p class="m-0"
                                                                        style="font-size: 14px; font-weight:500 !important; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                        {{ $usp->text }}</p>
                                                                </div>
                                                                @endforeach
                                                                @else
                                                                <div class="d-flex align-items-center size18">
                                                                    <div class="grn_chk"
                                                                        style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                        <img src="{{ asset('front/img/green-tick.svg') }}"
                                                                            style="width: 100%; height: auto;">
                                                                    </div>
                                                                    <p class="m-0"
                                                                        style="font-size: 14px; font-weight:500 !important; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                        Free domain & SSL certificate</p>
                                                                </div>
                                                                <div class="d-flex align-items-center size18">
                                                                    <div class="grn_chk"
                                                                        style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                        <img src="{{ asset('front/img/green-tick.svg') }}"
                                                                            style="width: 100%; height: auto;">
                                                                    </div>
                                                                    <p class="m-0"
                                                                        style="font-size: 14px; font-weight:500 !important; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                        Customizable automatic updates</p>
                                                                </div>
                                                                <div class="d-flex align-items-center size18">
                                                                    <div class="grn_chk"
                                                                        style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                        <img src="{{ asset('front/img/green-tick.svg') }}"
                                                                            style="width: 100%; height: auto;">
                                                                    </div>
                                                                    <p class="m-0"
                                                                        style="font-size: 14px; font-weight:500 !important; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                        Scalable performance management</p>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Compare Checkbox -->
                                            </div>

                                            <!-- Right Column -->
                                            <div class="rgt_rgt_bx"
                                                style="  flex: 0 0 250px; min-width: 250px; display: flex; flex-direction: column; justify-content: space-between; align-items: stretch; margin-top: 10px;">
                                                <!-- Buttons -->
                                                <div class="auto-choice-btn d-flex flex-column gap-2 mt-3"
                                                    style="width: 100%; margin: 0;">
                                                    @if($item->is_affiliate)
                                                    <a href="{{ $item->affiliate_link ?? $item->permanent_url }}"
                                                        class="btn-orng cta cta_orange justify-content-center"
                                                        target="_blank"
                                                        style="display: flex !important; width: 100%; align-items: center; border-radius: 30px;">
                                                        Visit website
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            style="width:16px;height:16px;margin-left:6px;flex-shrink:0;">
                                                            <path d="M15 3h6v6"></path>
                                                            <path d="M10 14 21 3"></path>
                                                            <path
                                                                d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    @endif
                                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}"
                                                        class="cta cta_outline justify-content-center"
                                                        style="display: flex !important; width: 100%; align-items: center; border: 1px solid #06498b; color: #06498b; border-radius: 30px;">
                                                        View details
                                                    </a>
                                                </div>

                                                @php
                                                $allPrices = $item->products
                                                ->flatMap(function ($product) {
                                                return $product->prices;
                                                })
                                                ->sortBy(function ($price) {
                                                $now = Illuminate\Support\Carbon::now();
                                                if ($price->discount_price && $price->discount_expiration_date &&
                                                $now->lte(Illuminate\Support\Carbon::parse($price->discount_expiration_date)))
                                                {
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
                                                if ($startingPrice->discount_price &&
                                                $startingPrice->discount_expiration_date &&
                                                $now->lte(Illuminate\Support\Carbon::parse($startingPrice->discount_expiration_date)))
                                                {
                                                $displayPrice = $startingPrice->discount_price;
                                                } elseif ($startingPrice->renewal_price) {
                                                $displayPrice = $startingPrice->renewal_price;
                                                } else {
                                                $displayPrice = $startingPrice->price;
                                                }
                                                }
                                                @endphp

                                                <!-- Price -->
                                                @if ($startingPrice && !empty($item->is_affiliate))
                                                <div class="text-center mt-4 w-100"
                                                    style="  padding: 15px 25px; border-radius: 8px;">
                                                    <h6
                                                        style="font-size: 13px; color: #002347; font-weight: 600; margin-bottom: 4px;">
                                                        Starting price</h6>
                                                    <h3
                                                        style="font-weight: 700 !important; color: #002347; font-size: 26px !important; margin-bottom: 2px;">
                                                        {{ $startingPrice->currency }}{{ number_format($displayPrice, 2) }}
                                                    </h3>
                                                    <p style="font-size: 11px; color: #444444; margin-bottom: 0;">Flat
                                                        Rate, Per One_time</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div style="width: 100%;">
                                        <livewire:compare-products :item="$item" :key="'compare-' . $item->id" />
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                            <!-- Pagination Links -->
                            @php
                            $currentPage = $products->currentPage();
                            $lastPage = $products->lastPage() ?? 1;
                            $maxVisible = 7;

                            $startPage = max(1, $currentPage - floor($maxVisible / 2));
                            $endPage = min($lastPage, $startPage + $maxVisible - 1);

                            if ($endPage - $startPage + 1 < $maxVisible) { $startPage=max(1, $endPage - $maxVisible +
                                1); } $showLeftDots=$startPage> 2;
                                $showRightDots = $endPage < $lastPage - 1; @endphp @if ($lastPage> 1)
                                    <div class="btn-pages">
                                        {{-- Previous Button (only if there's a previous page) --}}
                                        @if ($currentPage > 1)
                                        <a href="{{ $this->getCleanUrl($currentPage - 1) }}"
                                            wire:click.prevent="previousPage" class="pagination-btn pagination-arrow">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </a>
                                        @endif

                                        {{-- First Page --}}
                                        @if ($startPage > 1)
                                        <a href="{{ $this->getCleanUrl(1) }}" wire:click.prevent="gotoPage(1)"
                                            class="pagination-btn {{ $currentPage == 1 ? 'active' : '' }}">1</a>

                                        @if ($showLeftDots)
                                        <span class="pagination-dots">...</span>
                                        @endif
                                        @endif

                                        {{-- Page Numbers --}}
                                        @for ($page = $startPage; $page <= $endPage; $page++) <a
                                            href="{{ $this->getCleanUrl($page) }}"
                                            wire:click.prevent="gotoPage({{ $page }})"
                                            class="pagination-btn {{ $currentPage == $page ? 'active' : '' }}">
                                            {{ $page }}</a>
                                            @endfor

                                            {{-- Last Page --}}
                                            @if ($endPage < $lastPage) @if ($showRightDots) <span
                                                class="pagination-dots">...</span>
                                                @endif

                                                <a href="{{ $this->getCleanUrl($lastPage) }}"
                                                    wire:click.prevent="gotoPage({{ $lastPage }})"
                                                    class="pagination-btn {{ $currentPage == $lastPage ? 'active' : '' }}">{{ $lastPage }}</a>
                                                @endif

                                                {{-- Next Button (only if there's a next page) --}}
                                                @if ($currentPage < $lastPage) <a
                                                    href="{{ $this->getCleanUrl($currentPage + 1) }}"
                                                    wire:click.prevent="nextPage"
                                                    class="pagination-btn pagination-arrow next">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                    </a>
                                                    @endif
                                    </div>
                                    @endif

                        </div>
                        @else

                        <div class="auto-choice-rgt">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="m-0">Showing {{ $products->count() }} results </p>
                                </div>
                                <div class="d-none">
                                    <x-social-icon />
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <p class="mb-2 text-center fs-4 text-secondary">Sorry, we don't have any Products that
                                    match your filters. Try adjusting them to see more options.</p>
                            </div>
                        </div>
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
                            Sorry ,No Products available at the moment.
                        </p>
                        @endif
                    </div>
                </div>
            </div> --}}
            @endif
        </div>
        <livewire:compare-bar />
    </section>
@if(($products->currentPage() ?? ($page ?? 1)) == 1)
    <!-- Explore Top-Rated Categories Section -->
    @if(isset($exploreCategories) && $exploreCategories->isNotEmpty())
    <section class="explore-categories-sec py-5" style="background: #fdfdfd; border-top: 1px solid #e2e8f0;">
        <div class="container">
            <div class="text-start mb-4">
                <h2 style="font-size: 26px; font-weight: 700; color: #002347; margin-bottom: 8px;">Explore top-rated categories</h2>
                <p style="font-size: 15px; color: #555; margin-bottom: 0;">Discover top-rated software, services, and solutions across our most popular categories.</p>
            </div>

            <div class="explore-categories-list d-flex flex-column gap-4">
                @foreach($exploreCategories as $cat)
                @php
                    $trans = null;
                    if (isset($cat->translations)) {
                        if (is_object($cat->translations) && !($cat->translations instanceof \Illuminate\Database\Eloquent\Collection)) {
                            $trans = $cat->translations;
                        } elseif ($cat->translations instanceof \Illuminate\Database\Eloquent\Collection && $cat->translations->isNotEmpty()) {
                            $trans = $cat->translations->first();
                        }
                    }
                    $catItemName = $trans->name ?? $cat->name ?? '';
                    $catItemSlug = $trans->slug ?? $cat->slug ?? (string)$cat->id;
                    $catItemDesc = strip_tags($trans->description ?? $cat->description ?? '');
                    if (empty($catItemDesc) && !empty($catItemName)) {
                        $catItemDesc = 'Compare ' . $catItemName . ' providers offering different services and solutions. Explore providers using features, starting prices, real user reviews, and community ratings to identify the right match.';
                    }
                @endphp
                @if(!empty($catItemName))
                <div class="subcat-block p-4" style="background: #f7f9fb; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 style="font-size: 20px; font-weight: 700; color: #002347; margin: 0;">{{ $catItemName }}</h3>
                        <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catItemSlug]) }}" class="subcat-link" style="color: #002655; font-size: 13px; font-weight: 600; text-decoration: none;">
                            See all {{ $catItemName }}
                        </a>
                    </div>

                    <p style="font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 20px;">{{ $catItemDesc }}</p>

                    <div class="top-products-grid">
                        @foreach($cat->top_businesses ?? [] as $business)
                        @php
                            $bizName = $business->translations->first()->name ?? $business->name ?? '';
                        @endphp
                        @if(!empty($bizName))
                        <div class="top-product-card d-flex flex-column justify-content-between p-3" style="background:#fdfdfd !important;">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="top-product-logo" style="width: 45px; height: 45px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                    <x-business-logo :business="$business" :name="$bizName" />
                                </div>
                                <div class="top-product-info min-w-0">
                                    <h6 class="m-0 fw-bold d-flex align-items-center gap-1" style="font-size: 14px; color: #1e3050;">
                                        {{ $bizName }}
                                    </h6>
                                    <div class="rating-group" >
                                        <span class="rate_box_num">{{ number_format($business->average_rating, 1) }}</span>
                                        <div class="rating-stars" >
                                            @php $rating = round($business->average_rating); @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rating)
                                                    <i class="fas fa-star" ></i>
                                                @else
                                                    <i class="far fa-star" ></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="">({{ $business->active_reviews_count }})</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 w-100 mt-auto">
                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $business->translations->first()->slug ?? $business->slug]) }}"
                                   class="btn-view-details btn py-1 px-2 fw-medium {{ !empty($business->is_affiliate) ? 'w-50' : 'w-100' }}">
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
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Dynamic Text Sections -->
    @if(isset($textSections) && is_array($textSections) && count($textSections) > 0)
    <section class="top-rated-text-sections py-5" style="background: #ffffff; border-top: 1px solid #eef2f6;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    @foreach($textSections as $section)
                    <div class="content-section-block mb-5">
                        @if(!empty($section['h2_title']))
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 16px;">
                            {{ $section['h2_title'] }}
                        </h2>
                        @endif

                        @if(!empty($section['h2_text']))
                        <div class="rich-text-content" style="font-size: 15px; color: #444; line-height: 1.7; margin-bottom: 20px;">
                            {!! $section['h2_text'] !!}
                        </div>
                        @endif

                        @if(!empty($section['sub_sections']) && is_array($section['sub_sections']))
                            @foreach($section['sub_sections'] as $sub)
                            <div class="sub-section-block mt-4 mb-3">
                                @if(!empty($sub['h3_title']))
                                <h3 style="font-size: 18px; font-weight: 600; color: #1e3050; margin-bottom: 10px;">
                                    {{ $sub['h3_title'] }}
                                </h3>
                                @endif
                                @if(!empty($sub['h3_text']))
                                <div class="rich-text-content" style="font-size: 14.5px; color: #555; line-height: 1.7;">
                                    {!! $sub['h3_text'] !!}
                                </div>
                                @endif
                            </div>
                            @endforeach
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Top-Rated FAQs Section -->
    @if(isset($faqs) && is_array($faqs) && count($faqs) > 0)
    <section class="top-rated-faq-sec py-5" style="background: #f8fafc; border-top: 1px solid #eef2f6;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="text-start mb-4">
                        <h2 style="font-size: 26px; font-weight: 700; color: #002347; margin-bottom: 8px;">Frequently asked questions</h2>
                        <p style="font-size: 15px; color: #555; margin-bottom: 0;">Everything you need to know about Localio top-rated rankings and community reviews.</p>
                    </div>

                    <div class="accordion" id="topRatedFaqAccordion">
                        @foreach($faqs as $fIndex => $faq)
                        <div class="accordion-item mb-3" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden;">
                            <h2 class="accordion-header" id="faqHeading{{ $fIndex }}">
                                <button class="accordion-button {{ $fIndex > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $fIndex }}" aria-expanded="{{ $fIndex === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $fIndex }}" style="font-weight: 600; font-size: 16px; color: #002347; background-color: #ffffff;">
                                    {{ $faq['question'] }}
                                </button>
                            </h2>
                            <div id="faqCollapse{{ $fIndex }}" class="accordion-collapse collapse {{ $fIndex === 0 ? 'show' : '' }}" aria-labelledby="faqHeading{{ $fIndex }}" data-bs-parent="#topRatedFaqAccordion">
                                <div class="accordion-body rich-text-content" style="font-size: 14.5px; color: #555; line-height: 1.7; background-color: #ffffff;">
                                    {!! $faq['answer'] !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="subs_sec light top_rated_org_sec ">
        <x-news-letter-subscription />
    </section>
    @endif
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
<script>
function initPriceSlider() {
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
// Livewire.hook('message.processed', (message, component) => {
//     initPriceSlider(); // Re-initialize after DOM is updated by Livewire
// });
</script>
<script>
window.addEventListener('scroll-to-middle', function() {
    const offset = window.innerHeight * 0.55;
    window.scrollTo({
        top: offset,
        behavior: 'smooth'
    });
});
// Update browser URL when pagination changes
window.addEventListener('update-pagination-url', function(event) {
    window.history.pushState(null, '', event.detail.url);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('sortDropdownWrapper');
    const toggleBtn = document.getElementById('sortToggleBtn');
    const menu = document.getElementById('sortDropdownMenu');
    const arrow = document.getElementById('sortToggleArrow');
    if (!wrapper || !toggleBtn || !menu) return;

    function openMenu() {
        menu.classList.remove('d-none');
        arrow.style.transform = 'rotate(180deg)';
        lockScroll();
    }

    function closeMenu() {
        menu.classList.add('d-none');
        arrow.style.transform = '';
        unlockScroll();
    }

    function isOpen() {
        return !menu.classList.contains('d-none');
    }

    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        isOpen() ? closeMenu() : openMenu();
    });

    document.addEventListener('click', function(e) {
        if (isOpen() && !wrapper.contains(e.target)) {
            closeMenu();
        }
    });

    // Close automatically once a sort option is picked (Livewire request starts)
    document.addEventListener('livewire:navigating', closeMenu);
    menu.addEventListener('click', function(e) {
        if (e.target.closest('button')) {
            closeMenu();
        }
    });

    function preventScroll(e) {
        e.preventDefault();
    }

    function lockScroll() {
        document.addEventListener('wheel', preventScroll, {
            passive: false
        });
        document.addEventListener('touchmove', preventScroll, {
            passive: false
        });
    }

    function unlockScroll() {
        document.removeEventListener('wheel', preventScroll);
        document.removeEventListener('touchmove', preventScroll);
    }
});
</script>

</div>