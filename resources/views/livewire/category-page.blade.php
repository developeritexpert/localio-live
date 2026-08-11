<div>
    <style>
        .auto-choice-hd {
    margin-bottom: 15px !important;
}
.automotive-card .slider_content_sec {
    margin-top: 5px !important;
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
/* .top-rated-heading-sec .breadcrumb-item a, .asn_main_sec .breadcrumb-item a{
    display: block;
} */
    li.breadcrumb-item.active {
    color:#002347 !important;
    font-weight:500;
    }
    li.breadcrumb-item.active:hover{
        color:unset !important;
        text-decoration:none !important;
    }
    li.breadcrumb-item:hover{
        color:unset !important;
        text-decoration:underline !important;
    }
        .top-rated-heading-sec {
            margin-top: 125px;
            padding-top: 25px !important;
            background-color: #f7f9fb;
            border-bottom: 1px solid #e8eef6;
            /* margin-bottom: 25px; */
            padding-bottom: 20px;
        }

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
            box-shadow: 0px 0px 16px 0px rgb(0 0 0 / 13%) !important;
        }

        /* Force header search bar always visible on this page */
        #myID.search-box {
            visibility: visible !important;
            display: block !important;
        }

        /* Page top padding so content clears the fixed navbar */
        .top-automotive-sec.cat_pg {
            /* padding-top: 160px !important; */
            /* margin-top: 125px !important; */
            padding-top:0 !important;
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
        /* .automotive-card .blue-chkbox {
            bottom: 105px !important;
            transition: all 0.3s ease;
        } */
          .automotive-card .blue-chkbox {
            bottom: 0 !important;
            transition: all 0.3s ease;
            right:unset;
            left:-30px
        }
        /* Both Visit website and View details buttons – same height */
        .auto-choice-btn .cta {
            padding: 10px 16px !important;
            height: auto !important;
            min-height: 44px !important;
            box-sizing: border-box !important;
            line-height: 1.4 !important;
        }
          .form-check-input {           
            margin-top: 0;           
        }
         .price-separator,
        .price-input input {
            font-size: 12px;
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
    <section class="top-rated-heading-sec">
       <div class="container">
                                <div class="row align-items-center mb-3">
                                    <div class="col-8">
                                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                                            <ol class="breadcrumb m-0" style="background: transparent;padding: 0;display: flex;align-items: center;">
                                        <li class="breadcrumb-item">
                                            <a href="{{ url('/' . (request()->segment(1) ?? 'en-us') . '/categories') }}"
                                               style="color: inherit; text-decoration: none; font-size: 13px;" 
                                               onmouseout="this.style.color=''">All</a>
                                        </li>
                                        @if($category->parent)
                                            <li class="breadcrumb-item">
                                                <a href="{{ route('category.detail', ['locale' => request()->segment(1) ?? 'en-us', 'slug' => $category->parent->translations->slug ?? '']) }}"
                                                   style="color: inherit; text-decoration: none; font-size: 13px;"
                                                   onmouseout="this.style.color=''">
                                                    {{ $category->parent->translations->name ?? 'Main Category' }}
                                                </a>
                                            </li>
                                        @endif
                                        <li class="breadcrumb-item active" aria-current="page" style="font-size: 13px; color: #6c757d; text-decoration:none;">
                                            {{ $category->translations->name ?? 'Category' }}
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
                                <h1 style="color: #1e3050; font-weight: 700; margin-bottom: 8px;">{{ $category->translations->name ?? 'Products' }}</h1>
                                <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">Last updated on {{ now()->format('F j, Y') }}</p>
                                <p style="font-size: 15px; color: #444; margin-bottom: 0;">
                                    {{ strip_tags($category->translations->description ?? 'Browse and compare the best options') }}
                                </p>
                            </div>
                            <div class="col-md-4 mt-4 mt-md-0 text-start">
                                <div class="verified-insights-card" style="background-color: #fcfcfc; border-radius: 8px; padding: 16px; border: 1px solid #e2e8f0; text-align: left;">
                                    <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                                        <img src="{{ asset('user-dashboard-theme/img/bell_icon.svg') }}" style="width: 20px; height: 20px;" alt="Verified">
                                        <h6 style="margin: 0; font-weight: 700; color: #1e3050; font-size: 16px;">Real experiences. Transparent rankings.</h6>
                                    </div>
                                    <p style="font-size: 13px; color: #555; margin-bottom: 8px; line-height: 1.5;">
                                        Ratings and reviews are shared by real users from the Localio community.
                                    </p>
                                    <a href="javascript:void(0)" onclick="openModal()" class="learn_mre_btn" style="font-size: 13px; color: #06498b; font-weight: 600; text-decoration: none;">How rankings work</a>
                                </div>
                            </div>
                        </div>
                    </div>
       </div>
    </section>
                <section class="top-automotive-sec cat_pg light" style="padding-top:25px !important; background-color: #fdfdfd !important;">
                <div class="top-auto-btm">
                    <div class="container">
                        <div class="top-auto-choice">
                            <!-- <div class="top-rated-heading-block" style="padding-bottom: 16px; margin-bottom: 24px; border-bottom: 1px solid #e8eef6 !important;">
                                <div class="row align-items-start">
                                    <div class="col-md-8 text-start">
                                        <h1 style="color: #1e3050; font-weight: 700; margin-bottom: 8px; font-size: 24px !important;">{{ $category->translations->name ?? 'Products' }}</h1>
                                        <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">Last updated on {{ now()->format('F j, Y') }}</p>
                                        <p style="font-size: 15px; color: #444; margin-bottom: 0;">
                                            {{ strip_tags($category->translations->description ?? 'Browse and compare the best options') }}
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
                                            User rating
                                        </h3>

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
                                                <span class="filter1">&
                                                    up</span>
                                                <span class="filter2">
                                                    ({{ $ratingCounts[4] ?? 0 }})
                                                </span>
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
                                            <div
                                                class="price-inputs d-flex gap-2 align-items-center mt-3">
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

                                            <div id="priceRangeSlider2" data-max-price="{{ $maxPriceValue ?? $maxPrice ?? 10000 }}"
                                                style="margin-top: 20px;" wire:ignore></div>
                                        </div>
                                    </div>
                                    @if($isParentCategory)
                                        @if(isset($subCategories) && count($subCategories) > 0)
                                            <div class="filter-section mt-3 mb-3">
                                                <h3 class="fw-semibold text-dark mb-3" style="font-size: 16px;">Categories</h3>
                                                @foreach ($subCategories as $subcat)
                                                    @php
                                                        $subcatName = $subcat->translations->name ?? $subcat->name;
                                                    @endphp
                                                    <div class="form-check" style="margin-bottom: 8px;">
                                                        <input type="checkbox" class="form-check-input"
                                                            wire:model.live="selectedSubCategories"
                                                            value="{{ $subcat->id }}"
                                                            id="subcat-{{ $subcat->id }}"
                                                            style="margin-right: 8px; cursor: pointer;">
                                                        <label class="form-check-label"
                                                            for="subcat-{{ $subcat->id }}"
                                                            style="font-size: 13px; color: #555; cursor: pointer;">
                                                            {{ $subcatName }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <div class="accordion" id="filterAccordion" style="border: none; width: 100%;">
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
                                                                        wire:model.live="selectedOptions"
                                                                        value="{{ $option->id }}"
                                                                        id="option-{{ $option->id }}"
                                                                        style="margin-right: 8px; cursor: pointer;">
                                                                    <label class="form-check-label"
                                                                        for="option-{{ $option->id }}"
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
                                                                        value="{{ $option->id }}"
                                                                        id="option-{{ $option->id }}"
                                                                        style="margin-right: 8px; cursor: pointer;">
                                                                    <label class="form-check-label"
                                                                        for="option-{{ $option->id }}"
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
                                    @endif
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

                                     <div class="position-relative d-inline-block">
                                         {{-- Trigger pill button --}}
                                         <button type="button"
                                                 wire:click="toggleSortDropdown"
                                                 class=" d-inline-flex align-items-center gap-2"
                                                 style="background-color: #fdfdfd; color: #0f172a; border-radius: 20px; padding: 7px 16px; font-size: 13.5px; font-weight: 600; border: 1px solid #cbd5e1; outline: none; cursor: pointer;"
                                                 >
                                             <span>Sort: <span>{{ $currentLabel }}</span></span>
                                             <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s ease; {{ $showSortDropdown ? 'transform: rotate(180deg);' : '' }}">
                                                 <polyline points="6 9 12 15 18 9"/>
                                             </svg>
                                         </button>

                                         {{-- Floating card menu — pure Livewire, no Alpine --}}
                                         @if($showSortDropdown)
                                         <div class="position-absolute end-0 mt-2 bg-white py-2"
                                              style="z-index: 9999; min-width: 230px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                                             @foreach($sortOptionsMap as $val => $label)
                                                 <button type="button"
                                                         wire:click="setSortBy('{{ $val }}')"
                                                         class="d-flex align-items-center justify-content-between w-100 px-3 py-2"
                                                         style="font-size: 14px; color: #1e3050; cursor: pointer; transition: background-color 0.15s ease; background: {{ $currentSort === $val ? '#f8fafc' : 'transparent' }}; border: none; text-align: left;"
                                                         onmouseover="this.style.backgroundColor='#f1f5f9'"
                                                         onmouseout="this.style.backgroundColor='{{ $currentSort === $val ? '#f8fafc' : 'transparent' }}'">
                                                     <span style="{{ $currentSort === $val ? 'font-weight: 700;' : 'font-weight: 400;' }}">{{ $label }}</span>
                                                     @if($currentSort === $val)
                                                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1e3050" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-left: 8px;">
                                                             <polyline points="20 6 9 17 4 12"/>
                                                         </svg>
                                                     @endif
                                                 </button>
                                             @endforeach
                                         </div>
                                         @endif
                                     </div>
                                </div>
                                @if (!empty($products))
                                    @foreach ($products as $index => $item)
                                        <div class="automotive-card" data-aos="fade-up"
                                            data-aos-duration="1000" wire:key="product-{{ $item->id }}">
                                            <div class="auto-choice-card" style="position: relative; ">
                                                @php
                                                     $topAffId = $this->topAffiliatedBusinessId;
                                                     $isRecommended = $item->is_affiliate && ($item->id == $topAffId || ($topAffId === null && $index === 0));
                                                @endphp
                                                <div class="card-compare-m">
                                                    @if($isRecommended)
                                                        <div style="margin-bottom: 25px;">
                                                            <span style="background-color: #f8fafc; color: #06498b; border: 1px solid #06498b; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;">
                                                                <i class="far fa-star text-warning" style="margin-right: 4px;  color: #06498b !important;"></i> Recommended
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <div  style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: stretch; gap: 20px; width: 100%;">
                                                        <!-- Left Column -->
                                                        <div style="flex: 1 1 0%; min-width: 320px; display: flex; flex-direction: column; justify-content: flex-start;">
                                                            <!-- Logo & Title -->
                                                            <div class="auto-choice-hd" style="border: none; padding: 0; margin-bottom: 0;">
                                                                <div class="inn_sl_hed" style="width: 100%;">
                                                                    <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}">
                                                                        <div class="sli_img choice_img">
                                                                            <img class="slider_img" src="{{ asset($item->icon_id) }}" alt="No Images For This Product">
                                                                        </div>
                                                                    </a>
                                                                    <div class="sl_h">
                                                                        <div class="inn_h">
                                                                            <div class="sl_main">
                                                                                <h6 class="head">{{ $item->translations->first()->name }}</h6>
                                                                                <div class="d-none" wire:key="wishlist-container-{{ $item->id }}">
                                                                                    @livewire('wishlist', ['productId' => $item->id], key('wishlist-' . $item->id))
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="rating-group d-flex align-items-center" style="gap: 6px;">
                                                                             <span class="rate_box_num" style="">{{ number_format($item->reviews->avg('rating'), 1) }}</span>
                                                                             <div class="inn_ul d-inline-flex m-0">
                                                                                 <div class="rating-stars ">
                                                                                     @for ($i = 1; $i <= 5; $i++)
                                                                                         @if ($i <= floor($item->reviews->avg('rating')))
                                                                                             <i class="fas fa-star text-warning"></i>
                                                                                         @elseif ($i - 0.5 <= $item->reviews->avg('rating'))
                                                                                             <i class="fas fa-star-half-alt text-warning"></i>
                                                                                         @else
                                                                                             <i class="far fa-star text-warning"></i>
                                                                                         @endif
                                                                                     @endfor
                                                                                 </div>
                                                                             </div>
                                                                             <span class="rate_box_count " style="">({{ $item->reviews->count() }})</span>
                                                                         </div>  </div>
                                                                    <!-- </div> -->
                                                                </div>
                                                            </div>

                                                            <!-- Short Description -->
                                                            @if(!empty($item->translations->first()->short_description))
                                                                <div class="mb-3 mt-1 text-start" style="font-size: 14px; color: #444; line-height: 1.5; width: 100%;">
                                                                    {{ $item->translations->first()->short_description }}
                                                                </div>
                                                            @endif

                                                            <!-- Features -->
                                                            @if (!empty($item->is_affiliate))
                                                                <div class="slider_content_sec my-3" style="width: 100% !important; max-width: 100% !important;">
                                                                    <div class="main_feature_lg" style="width: 100% !important; max-width: 100% !important;">
                                                                        <div class="feture_box lft_check_box size18" style="border: none; padding: 0; background: transparent; min-height: auto; width: 100% !important; max-width: 100% !important;">
                                                                            <div class="usp-grid-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                                                                @if ($item->usps->count() > 0)
                                                                                    @foreach ($item->usps->take(4) as $usp)
                                                                                        <div class="d-flex align-items-center size18">
                                                                                            <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                                <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                            </div>
                                                                                            <p class="m-0" style="font-size: 14px;color: #333;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-weight: 500;">{{ $usp->text }}</p>
                                                                                        </div>
                                                                                    @endforeach
                                                                                @else
                                                                                    <div class="d-flex align-items-center size18">
                                                                                        <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                            <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                        </div>
                                                                                        <p class="m-0" style="font-size: 14px;color: #333;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-weight: 500;">Free domain & SSL certificate</p>
                                                                                    </div>
                                                                                    <div class="d-flex align-items-center size18">
                                                                                        <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                            <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                        </div>
                                                                                        <p class="m-0" style="font-size: 14px;color: #333;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-weight: 500;">Customizable automatic updates</p>
                                                                                    </div>
                                                                                    <div class="d-flex align-items-center size18">
                                                                                        <div class="grn_chk" style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                                            <img src="{{ asset('front/img/green-tick.svg') }}" style="width: 100%; height: auto;">
                                                                                        </div>
                                                                                        <p class="m-0" style="font-size: 14px;color: #333;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-weight: 500;">Scalable performance management</p>
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
                                                        <div  class="rgt_rgt_bx" style="  flex: 0 0 250px; min-width: 250px; display: flex; flex-direction: column; justify-content: space-between; align-items: stretch; margin-top: 10px;">
                                                            <!-- Buttons -->
                                                            <div class="auto-choice-btn d-flex flex-column gap-2" style="width: 100%; margin: 0;">
                                                                @if($item->is_affiliate)
                                                                <a href="{{ $item->affiliate_link ?? $item->permanent_url }}"
                                                                    class="btn-orng cta cta_orange justify-content-center"
                                                                    target="_blank" style="display: flex !important; width: 100%; align-items: center; border-radius: 30px;">
                                                                    Visit website
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                                </a>
                                                                @endif
                                                                <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $item->translations()->first()->slug]) }}"
                                                                    class="cta cta_outline justify-content-center" style="display: flex !important; width: 100%; align-items: center; border: 1px solid #06498b; color: #06498b; border-radius: 30px;">
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

                                                            <!-- Price -->
                                                            @if ($startingPrice && !empty($item->is_affiliate))
                                                                <div class="text-center mt-4 w-100" style="  padding: 15px 25px; border-radius: 8px;">
                                                                    <h6 style="font-size: 13px; color: #002347; font-weight: 600; margin-bottom: 4px;">Starting price</h6>
                                                                    <h3 style="font-weight: 700 !important; color: #002347; font-size: 26px !important; margin-bottom: 2px;">
                                                                        {{ $startingPrice->currency }}{{ number_format($displayPrice, 2) }}
                                                                    </h3>
                                                                    <p style="font-size: 11px; color: #444444; margin-bottom: 0;">Flat Rate, Per One_time</p>
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

                                if ($endPage - $startPage + 1 < $maxVisible) {
                                    $startPage = max(1, $endPage - $maxVisible + 1);
                                }

                                $showLeftDots = $startPage > 2;
                                $showRightDots = $endPage < $lastPage - 1;
                            @endphp

                            @if ($lastPage > 1)
                                <div class="btn-pages">
                                    {{-- Previous Button (only if there's a previous page) --}}
                                    @if ($currentPage > 1)
                                        <button
                                            wire:click="previousPage"
                                            class="pagination-btn pagination-arrow">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </button>
                                    @endif

                                    {{-- First Page --}}
                                    @if ($startPage > 1)
                                        <button
                                            wire:click="gotoPage(1)"
                                            class="pagination-btn {{ $currentPage == 1 ? 'active' : '' }}"
                                        >1</button>

                                        @if ($showLeftDots)
                                            <span class="pagination-dots">...</span>
                                        @endif
                                    @endif

                                    {{-- Page Numbers --}}
                                    @for ($page = $startPage; $page <= $endPage; $page++)
                                        <button
                                            wire:click="gotoPage({{ $page }})"
                                            class="pagination-btn {{ $currentPage == $page ? 'active' : '' }}"
                                        >{{ $page }}</button>
                                    @endfor

                                    {{-- Last Page --}}
                                    @if ($endPage < $lastPage)
                                        @if ($showRightDots)
                                            <span class="pagination-dots">...</span>
                                        @endif

                                        <button
                                            wire:click="gotoPage({{ $lastPage }})"
                                            class="pagination-btn {{ $currentPage == $lastPage ? 'active' : '' }}"
                                        >{{ $lastPage }}</button>
                                    @endif

                                    {{-- Next Button (only if there's a next page) --}}
                                    @if ($currentPage < $lastPage)
                                        <button
                                            wire:click="nextPage"
                                            class="pagination-btn pagination-arrow next">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </button>
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
                                    
                                </div>

                                <div class="alert alert-info" style="background-color:#f7f9fb;">
                                    <p class="mb-2 text-center fs-4 text-secondary">Sorry, we don't have any Products that match your filters. Try adjusting them to see more options.</p>
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
                <livewire:compare-bar :category-id="$category->id" />
            </section>
            <!-- Remaining sections stay the same -->
        <section class="subs_sec light ">
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
            function initPriceSlider() {
                setTimeout(() => {
                    const slider = document.getElementById('priceRangeSlider2');
                    const minInput = document.getElementById('minPriceInput2');
                    const maxInput = document.getElementById('maxPriceInput2');
                    // Get the dynamic maximum price from the data attribute
                    const maxPriceValue = slider && slider.dataset.maxPrice ? parseInt(slider.dataset.maxPrice) : 10000;

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
                        initPriceSlider();
                    }
                });
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
