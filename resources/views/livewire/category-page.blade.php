<div>
    @include('User.partials.business_detail_styles')
    <style>
    [x-cloak] { display: none !important; }
    .table-business-link {
        display: inline-flex !important;
        align-items: center;
        padding: 6px 12px;
        margin: -6px -12px;
        border-radius: 10px;
        background-color: transparent;
        text-decoration: none !important;
        color: inherit;
        width: fit-content;
        max-width: 100%;
    }
    .table-business-link:hover {
        background-color: #e9edf5 !important;
        text-decoration: none !important;

    }
  
    .comparison-box:hover {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }
    .cat-compare-item {
        padding: 10px 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #1e3050;
    }
    .cat-compare-item:hover {
        background: #f1f5f9;
        color: #002347;
    }
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

    li.breadcrumb-item.active {
        color: #002347 !important;
        font-weight: 500;
    }

    li.breadcrumb-item.active:hover {
        color: unset !important;
        text-decoration: none !important;
    }

    li.breadcrumb-item:hover {
        color: unset !important;
        text-decoration: underline !important;
    }
        .top-rated-heading-sec {
            padding-top: 30px !important;
            background-color: #f7f9fb;
            border-bottom: 1px solid #e8eef6;
        }

    .top-rated-heading-sec {
        margin-top: 115px;
        padding-top: 25px;
        background-color: #f7f9fb;
        border-bottom: 1px solid #e8eef6;
        padding-bottom: 20px;
    }

    /* Toggle switch styling */
    .cat-heading-block h1 {
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
        padding-top: 0 !important;
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
        font-size: 28px;
        font-weight: 700;
        padding: 0;
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
        right: unset;
        left: -30px
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

        .starting-price-box,
        .free-trial-box {
            width: 100% !important;
            min-width: 100% !important;
            margin-bottom: 10px !important;
        }
    }

    /* Category Page Enhanced Sections Styling */

    .rich-text-content ul, .rich-text-content ol {
        margin-left: 20px;
        margin-bottom: 12px;
        color: #475569;
    }
    .rich-text-content li {
        margin-bottom: 6px;
    }
    .rich-text-content a {
        color: #06498b;
        text-decoration: underline;
    }

    /* .top-product-medium-logo {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    } */
    /* .top-product-medium-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    } */
    .explore-subcat-card {
        background: #fdfdfd;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .explore-subcat-card:hover {
        border-color: #06498b;
        box-shadow: 0 4px 12px rgba(6,73,139,0.08);
        transform: translateY(-2px);
    }
    /* Active Feature Filter Chip */
    .active-feat-chip {
        background: #ffffff;
        border: 1px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 5px 14px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        text-decoration: none !important;
        cursor: pointer !important;
        transition: all 0.18s ease-in-out !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        outline: none !important;
    }
    .active-feat-chip:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #002347 !important;
        box-shadow: 0 3px 8px rgba(0,0,0,0.07) !important;
    }
    .active-feat-chip:hover i {
        color: #e11d48 !important;
    }
    .clear-features-link {
        color: #007bff !important;
        font-size: 13px !important;
        font-weight: 400 !important;
        text-decoration: none !important;
        margin-left: 8px !important;
        cursor: pointer !important;
        transition: color 0.15s ease !important;
    }
    .clear-features-link:hover {
        color: #0056b3 !important;
        text-decoration: underline !important;
    }
    .explore-subcat-card:hover h6 {
        color: #e56b46 !important;
    }
    .btn-view-details-outline {
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
    .btn-view-details-outline:hover {
        background: #174889 !important;
        color: #fff !important;
        text-decoration: none !important;
    }
    .btn-orng-pill {
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
    .btn-orng-pill:hover {
        opacity: 0.9 !important;
        color: #fff !important;
    }
    strong {
        font-weight: 600;
    }
    </style>

    @php
        $catMetaTitle = !empty($category->translations->meta_title) ? $category->translations->meta_title : ($category->translations->name ?? 'Category Page');
        $catMetaDescription = !empty($category->translations->meta_description) ? $category->translations->meta_description : strip_tags($category->translations->description ?? '');
    @endphp
    @if (session()->has('message'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
        <!-- section top-rated automaotive -->
        <section class="top-rated-heading-sec cat_main_page_sec">
            <div class="container">
                <div class=" bread_row row align-items-center mb-2">
                    <div class="col-8 ">
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb m-0"
                                style="background: transparent;padding: 0;display: flex;align-items: center;">
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
                                <li class="breadcrumb-item active" aria-current="page"
                                    style="font-size: 13px; color: #6c757d; text-decoration:none;">
                                    {{ $category->translations->name ?? 'Category' }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-4  d-flex justify-content-end">
                        <x-social-icon />
                    </div>
                </div>
                <div class="top-rated-heading-block">
                       @php
                                $currentLangId = $lang_id ?? getCurrentLanguageID();
                                $c1Obj = \App\Models\StaticContentKey::where('key', 'country1')->first();
                                $c2Obj = \App\Models\StaticContentKey::where('key', 'country2')->first();
                                $c1 = $c1Obj ? $c1Obj->getTranslation($currentLangId) : '';
                                $c2 = $c2Obj ? $c2Obj->getTranslation($currentLangId) : '';
                                $currentYear = date('Y');

                                $applyDynamicVars = function($text) use ($c1, $c2, $currentYear) {
                                    if(!$text) return $text;
                                    $text = str_replace('[year]', $currentYear, $text);
                                    $text = str_replace('[country1]', $c1, $text);
                                    $text = str_replace('[country2]', $c2, $text);
                                    // Remove trailing or double spaces that might occur if a variable is empty
                                    return trim(preg_replace('/\s+/', ' ', $text));
                                };
                            @endphp

                            @php
                                $baseTitle = !empty($category->translations->page_title) ? $category->translations->page_title : ($category->translations->name ?? 'Products');
                                if (!empty($category->page_title_addition)) {
                                    $baseTitle .= ' ' . $category->page_title_addition;
                                }
                            @endphp
                            <h1>
                                {{ $applyDynamicVars($baseTitle) }}
                            </h1>

                            <p class="text-meta">Last updated {{ now()->subDay()->format('F j, Y') }}</p>
                    <div class="host-parra row align-items-start">
                        <div class="col-md-6 text-start">
                            
                            <div class="category-description-text">
                                {!! \App\Services\ContentTemplateService::resolveCategoryIntro($category, $currentLangId) !!}
                            </div>
                        </div>
                        <div class="col-md-6 mt-4 mt-md-0 text-end cat_img_header">
                            @if(isset($category) && $category->top_rated_image && $category->topRatedImageMedia)
                                <div class="cat-hero-img-wrap position-relative d-inline-block">
                                    <img src="{{ asset($category->topRatedImageMedia->dir_path . '/' . $category->topRatedImageMedia->file_name) }}" alt="{{ $category->name ?? '' }}" class="cat-hero-main-img" style="height: 100%;">
                                    @if(!empty($category->category_overlay_image) && $category->categoryOverlayMedia)
                                        @php $pos = $category->category_overlay_position ?? 'right'; @endphp
                                        <img src="{{ asset($category->categoryOverlayMedia->dir_path . '/' . $category->categoryOverlayMedia->file_name) }}" alt="Category Overlay" class="cat-hero-overlay-img cat-hero-overlay-{{ $pos }}">
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="top-automotive-sec cat_pg light"
            style="padding-top:25px !important; background-color: #fdfdfd !important;">
            <div class="top-auto-btm">
                <div class="container">
                    <div class="d-flex flex-wrap justify-content-between align-items-start" style="margin-bottom: 16px; border-bottom: 1px solid #e8eef6 !important; padding-bottom: 20px;">
                        @php
    $resolvedTopRated = \App\Services\ContentTemplateService::resolveCategorySection($category, 'compare_top_rated', $currentLangId);
    $topRatedH2Text = !empty($resolvedTopRated['headline']) ? $resolvedTopRated['headline'] : ($category->translation->top_rated_listing_headline ?? ('Best ' . strtolower($category->name) . ' services'));
    if (!empty($category->top_rated_listing_headline_addition)) {
        $topRatedH2Text .= ' ' . $category->top_rated_listing_headline_addition;
    }
    $topRatedSubText = !empty($resolvedTopRated['subtext']) ? $resolvedTopRated['subtext'] : ($category->translation->top_rated_listing_subtext ?? '');
@endphp
<div class="host-pro flex-grow-1" style="max-width: 50%;">
                            <h2 style="margin-bottom: 8px;">
                               {{ $applyDynamicVars($topRatedH2Text) }}
                            </h2>
                            @if(!empty($topRatedSubText))
                                <style>
                                    .top-rated-subtext-container p { margin-bottom: 0 !important; }
                                    .top-rated-subtext-container p:not(:last-child) { margin-bottom: 10px !important; }
                                </style>
                                <div class="top-rated-subtext-container ck-content">
                                    {!! $topRatedSubText !!}
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-shrink-0 mt-3 mt-md-0" style="width: 100%; max-width: 500px;">
                            <div class="verified-insights-card">
                                <div class="d-flex align-items-center justify-content-between mb-1 gap-2">
                                    <p style="margin: 0; font-weight: 600; color: #002347; font-size: 16px;">{{ static_text('community_insights_title') ?: 'Real user ratings' }}</p>
                                    <img src="{{ asset('front/img/top_rated_img.png') }}" alt="Real users" style="height: 30px; width: auto; object-fit: contain;">
                                </div>
                                <p>
                                    {{ static_text('community_insights_desc') ?: 'Ratings and reviews come from real Localio users.' }}
                                </p>

                                 <a href="javascript:void(0)" onclick="openRankingsModal()" class="learn_mre_btn">{{ static_text('how_rankings_work') ?: 'How rankings work' }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="top-auto-choice">
                        <!-- <div class="top-rated-heading-block" style="padding-bottom: 16px; margin-bottom: 24px; border-bottom: 1px solid #e8eef6 !important;">
                                <div class="row align-items-start">
                                    <div class="col-md-6 text-start">
                                        <h1 style="color: #1e3050; font-weight: 700; margin-bottom: 8px; font-size: 24px !important;">{{ $category->translations->name ?? 'Products' }}</h1>
                                        <p class="text-meta">Last updated {{ now()->subDay()->format('F j, Y') }}</p>
                                        <p style="font-size: 15px; color: #444; margin-bottom: 0;">
                                            {{ strip_tags($category->translations->description ?? 'Browse and compare the best options') }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 mt-4 mt-md-0 text-start">
                                        <div class="verified-insights-card">
                                            <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                                                <img src="{{ asset('user-dashboard-theme/img/bell_icon.svg') }}" style="width: 20px; height: 20px;" alt="Verified">
                                                <h6 style="margin: 0; font-weight: 700; color: #1e3050; font-size: 16px;">Real Ratings</h6>
                                            </div>
                                            <p>
                                                Provider data verified by our Software Research team and reviews moderated by our Reviews Verification team.
                                            </p>
                                            <a href="javascript:void(0)" onclick="openModal()" class="learn_mre_btn">Learn more</a>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        <div class="auto-choice-row d-flex ">
                            <div class="auto-choice-lft">
                                <div class="container">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                    </div>
                                    <div class="col-md-12 sticky-top" style="top: 100px; z-index: 10; padding-bottom: 20px;">
                                        <!-- Rating Filter Section - Styled like the image -->
                                        <div class="filter-section">
                                            <p class="fw-semibold text-dark mb-2">
                                                {{ static_text('user_rating') ?: 'User rating' }}
                                            </p>

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

                                        <!-- Categories / Filters Section -->
                                        @if($isParentCategory)
                                            @if(isset($subCategories) && count($subCategories) > 0)
                                            <div class="filter-section mt-3 mb-3 pb-3 border-bottom">
                                                <p class="fw-semibold text-dark mb-3" style="font-size: 16px;">Categories</p>
                                                <div class="d-flex flex-column gap-2 custom-scrollbar" style="max-height: 180px; overflow-y: auto;">
                                                    @foreach ($subCategories as $subcat)
                                                    @php
                                                    $subcatName = $subcat->translations->name ?? $subcat->name;
                                                    @endphp
                                                    <div class="form-check" style="margin-bottom: 4px;">
                                                        <input type="checkbox" class="form-check-input"
                                                            wire:model.live="selectedSubCategories" value="{{ $subcat->id }}"
                                                            id="subcat-{{ $subcat->id }}"
                                                            style="margin-right: 8px; cursor: pointer;">
                                                        <label class="form-check-label" for="subcat-{{ $subcat->id }}"
                                                            style="font-size: 13px; color:#5b6b7a; cursor: pointer;">
                                                            {{ $subcatName }}
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                </div>
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

                                                <div class="filter-section mt-3 mb-3 pb-3 border-bottom">
                                                    <p class="fw-semibold text-dark mb-3" style="font-size: 16px;">
                                                        {{ $filterName }}
                                                    </p>

                                                    <div class="accordion-body custom-scrollbar" style="padding: 0; max-height: 180px; overflow-y: auto;">
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
                                                                style="font-size: 13px; color: #5b6b7a; cursor: pointer;">
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
                                                                style="font-size: 13px; color: #5b6b7a; cursor: pointer;">
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
                                        @endif

                                        <!-- Price Range Section -->
                                        <link rel="stylesheet"
                                            href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" />

                                        <div class="filter-section mt-3 mb-3 pb-3 border-bottom pric_rnge">
                                            <p class="fw-semibold text-dark mb-3" style="font-size: 16px;">
                                                {{ static_text('price_range') ?: 'Price range' }}
                                            </p>

                                            <div class="price-slider-container">
                                                <div class="price-inputs d-flex gap-2 align-items-center mt-3">
                                                    <div class="price-input">
                                                        <span class="currency">$</span>
                                                        <input type="number" id="minPriceInput2"
                                                            wire:model.live="minPrice" min="0" max="5000"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                    <span class="price-separator">to</span>
                                                    <div class="price-input">
                                                        <span class="currency">$</span>
                                                        <input type="number" id="maxPriceInput2"
                                                            wire:model.live="maxPrice" min="0" max="5000"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                </div>

                                                <div id="priceRangeSlider2"
                                                    data-max-price="{{ $maxPriceValue ?? $maxPrice ?? 10000 }}"
                                                    style="margin-top: 20px;" wire:ignore></div>
                                            </div>
                                        </div>

                                        <!-- Features Filter Section -->
                                        @if(isset($availableFeatures) && count($availableFeatures) > 0)
                                        <div class="filter-section mt-3 mb-3">
                                            <p class="fw-semibold text-dark mb-3" style="font-size: 16px;">
                                                Features
                                            </p>
                                            @php
                                                $sortedFeatures = collect($availableFeatures)->sortBy(function($feat) use ($lang_id) {
                                                    $currentLangId = $lang_id ?? getCurrentLanguageID();
                                                    $featTrans = $feat->translations->where('language_id', $currentLangId)->first() ?? $feat->translations->first();
                                                    return strtolower($featTrans->name ?? $feat->name ?? 'Feature');
                                                })->values();
                                            @endphp
                                            <div class="d-flex flex-column gap-2 custom-scrollbar" style="max-height: 180px; overflow-y: auto;">
                                                @foreach($sortedFeatures as $feat)
                                                    @php
                                                        $currentLangId = $lang_id ?? getCurrentLanguageID();
                                                        $featTrans = $feat->translations->where('language_id', $currentLangId)->first() ?? $feat->translations->first();
                                                        $featName = $featTrans->name ?? $feat->name ?? 'Feature';
                                                        $featId = (string)$feat->id;
                                                        $isChecked = in_array($featId, array_map('strval', $selectedFeatures ?? []));
                                                    @endphp
                                                    <div class="form-check" style="margin-bottom: 6px;" wire:key="feat-wrap-{{ $featId }}">
                                                        <input type="checkbox" class="form-check-input"
                                                               wire:model.live="selectedFeatures"
                                                               wire:key="feat-input-{{ $featId }}-{{ $isChecked ? '1' : '0' }}"
                                                               value="{{ $featId }}"
                                                               id="feature-check-{{ $featId }}"
                                                               style="margin-right: 8px; cursor: pointer;">
                                                        <label class="form-check-label" for="feature-check-{{ $featId }}"
                                                               style="font-size: 13px; color: #5b6b7a; cursor: pointer; {{ $isChecked ? 'font-weight: 600; color: #002347;' : '' }}">
                                                            {{ $featName }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        
                                    </div>

                                </div>

                            </div>
                            @if ($products->count())
                            <div class="auto-choice-rgt ">
                                
                                <!-- Product Count and Sort -->
                                @if(!empty($selectedFeatures) && count($selectedFeatures) > 0)
                                    <div class="active-feature-chips d-flex align-items-center flex-wrap gap-2 mb-3">
                                        <span class="text-muted small fw-semibold" style="font-size: 13px;">Selected features:</span>
                                        @foreach($availableFeatures ?? [] as $fItem)
                                            @if(in_array((string)$fItem->id, array_map('strval', $selectedFeatures ?? [])))
                                                @php
                                                    $fTrans = $fItem->translations->firstWhere('language_id', $lang_id) ?? $fItem->translations->first();
                                                    $fName = $fTrans->name ?? $fItem->name ?? 'Feature';
                                                @endphp
                                                <button type="button" class="active-feat-chip" wire:click="toggleFeature({{ $fItem->id }})" title="Click to remove {{ $fName }}">
                                                    <span>{{ $fName }}</span>
                                                    <i class="fas fa-times text-muted" style="font-size: 11px;"></i>
                                                </button>
                                            @endif
                                        @endforeach
                                        <a href="javascript:void(0)" wire:click="clearAllFeatures" class="clear-features-link">Clear features</a>
                                    </div>
                                @endif

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
                                        <p class="m-0 showing-p">
                                            Showing {{$from}}-{{$to}} of {{$total}}
                                        </p>
                                        @elseif($products->count() > 0)
                                        <p class="m-0 showing-p">Showing {{ $products->count() }} results</p>
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

                                    <div class="dropdown d-inline-block">
                                        <button type="button" class="sorting d-inline-flex align-items-center gap-2"
                                            id="categorySortDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false"
                                            style="background-color:#fdfdfd; color: #0f172a; border-radius: 20px; padding: 7px 16px; border: 1px solid #cbd5e1; outline: none; cursor: pointer;">
                                            <span>Sort: <span style="font-weight: 600;">{{ $currentLabel }}</span></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                height="14" viewBox="0 0 24 24" fill="none" stroke="#475569"
                                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="6 9 12 15 18 9" />
                                            </svg>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-end shadow-sm"
                                            aria-labelledby="categorySortDropdownBtn"
                                            style="min-width: 230px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.15); padding: 6px 0; margin-top: 6px; overflow: hidden;">
                                            @foreach($sortOptionsMap as $val => $label)
                                            <button type="button" wire:click="setSortBy('{{ $val }}')"
                                                class="dropdown-item d-flex align-items-center justify-content-between px-3 py-2"
                                                style="font-size: 13px; color: #1e3050; cursor: pointer; background: {{ $currentSort === $val ? '#f8fafc' : 'transparent' }}; border: none; text-align: left;"
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
                                <div class="automotive-card"
                                    wire:key="product-{{ $item->id }}">
                                    <div class="auto-choice-card" style="position: relative; ">
                                        @php
                                        $topAffId = $this->topAffiliatedBusinessId;
                                        $isRecommended = $item->is_affiliate && ($item->id == $topAffId || ($topAffId
                                        === null && $index === 0));
                                        @endphp
                                        <div class="card-compare-m">
                                            @php
                                            $activeReviews = $item->reviews ? $item->reviews->where('status', 'active')
                                            : collect();
                                            $totalRevCount = $activeReviews->count();
                                            $recCount = $activeReviews->where('recommend', 1)->count();
                                            $recPercent = $totalRevCount > 0 ? round(($recCount / $totalRevCount) * 100)
                                            : 0;
                                            @endphp



                                            @if($isRecommended)
                                            <div class="best-value-inline-container" style="padding-bottom:20px">
                                                <div class="best-value-inline">
                                                    <!-- <i class="fa-regular fa-thumbs-up"></i> -->
                                                    <i class="far fa-star text-warning"></i>
                                                    <span style="text-transform: none !important;">{{ static_text('category_badge_label') ?: 'Top rated' }}</span>
                                                </div>

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
                                                    <span>{{ $recPercent }}% {{ static_text('recommend_this') ?: 'of users recommend' }}</span>
                                                </div>
                                            @endif

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
                                                                        <p class="head">
                                                                            {{ $item->translations->first()->name }}
                                                                        </p>
                                                                        <div class="d-none"
                                                                            wire:key="wishlist-container-{{ $item->id }}">
                                                                            @livewire('wishlist', ['productId' =>
                                                                            $item->id], key('wishlist-' . $item->id))
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="rating-group d-flex align-items-center"
                                                                    style="gap: 6px;">
                                                                    <span class="rate_box_num"
                                                                        style="">{{ formatRating($item->reviews->avg('rating')) }}</span>
                                                                    <div class="">
                                                                        <div class="rating-stars ">
                                                                            @for ($i = 1; $i <= 5; $i++) @if ($i
                                                                                <=floor($item->reviews->avg('rating')))
                                                                                <i class="fas fa-star text-warning"></i>
                                                                                @elseif ($i - 0.5 <= $item->
                                                                                    reviews->avg('rating'))
                                                                                    <i
                                                                                        class="fas fa-star-half-alt text-warning"></i>
                                                                                    @else
                                                                                    <i
                                                                                        class="far fa-star text-warning"></i>
                                                                                    @endif
                                                                                    @endfor
                                                                        </div>
                                                                    </div>
                                                                    <span class="rate_box_count "
                                                                        style="">({{ $item->reviews->count() }})</span>
                                                                </div>
                                                            </div>
                                                            <!-- </div> -->
                                                        </div>
                                                    </div>

                                                    <!-- Short Description -->
                                                    @if(!empty($item->translations->first()->short_description))
                                                    <div class="mb-3 mt-1 text-start"
                                                        style="font-size: 14px; color: #002347; line-height: 1.5; width: 100%;">
                                                        {{ $item->translations->first()->short_description }}
                                                    </div>
                                                    @endif

                                                    <!-- Features -->
                                                    @if (!empty($item->is_affiliate))
                                                    <div class="slider_content_sec my-3"
                                                        style="width: 100% !important; max-width: 100% !important;">
                                                        <div class="main_feature_lg">
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
                                                                            style="font-size: 14px;color: #002347; overflow: hidden;text-overflow: ellipsis;font-weight: 500;">
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
                                                                            style="font-size: 14px;color: #002347;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-weight: 500;">
                                                                            Free domain & SSL certificate</p>
                                                                    </div>
                                                                    <div class="d-flex align-items-center size18">
                                                                        <div class="grn_chk"
                                                                            style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                            <img src="{{ asset('front/img/green-tick.svg') }}"
                                                                                style="width: 100%; height: auto;">
                                                                        </div>
                                                                        <p class="m-0"
                                                                            style="font-size: 14px;color: #002347;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-weight: 500;">
                                                                            Customizable automatic updates</p>
                                                                    </div>
                                                                    <div class="d-flex align-items-center size18">
                                                                        <div class="grn_chk"
                                                                            style="width: 16px; margin-right: 8px; flex-shrink: 0;">
                                                                            <img src="{{ asset('front/img/green-tick.svg') }}"
                                                                                style="width: 100%; height: auto;">
                                                                        </div>
                                                                        <p class="m-0"
                                                                            style="font-size: 14px;color: #002347;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-weight: 500;">
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
                                                            style="display: flex !important; width: 100%; align-items: center;">
                                                            {{ static_text('visit_website') ?: 'Visit website' }}
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
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
                                                            style="display: flex !important; width: 100%; align-items: center; border: 1px solid #06498b; color: #06498b;">
                                                            {{ static_text('profile_and_reviews') ?: 'Profile & reviews' }}
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
        style="padding: 15px 25px; border-radius: 8px;">

        <h6
            style="font-size: 13px; color: #002347; font-weight: 600; margin-bottom: 4px;">
            Starting price
        </h6>

        <h3
            style="font-weight: 700 !important; color: #002347; font-size: 26px !important; margin-bottom: 2px;">
            {{ $startingPrice->currency }}{{ number_format($displayPrice, 2) }}
        </h3>

        @if (
            !empty($startingPrice->additional_info) &&
            !in_array(
                strtoupper(trim((string) $startingPrice->additional_info)),
                ['NA', 'N/A', 'NULL']
            )
        )
            <p style="font-size: 11px; margin-bottom: 0;">
                {{ $startingPrice->additional_info }}
            </p>
        @elseif(!empty($startingPrice->time_unit) && !in_array(strtolower($startingPrice->time_unit), ['one_time', 'na', 'n/a', 'null']))
            <p style="font-size: 11px; margin-bottom: 0;">
                per {{ strtolower($startingPrice->time_unit) }}
            </p>
        @endif

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

                                if ($endPage - $startPage + 1 < $maxVisible) { $startPage=max(1, $endPage - $maxVisible
                                    + 1); } $showLeftDots=$startPage> 2;
                                    $showRightDots = $endPage < $lastPage - 1; @endphp @if ($lastPage> 1)
                                        <div class="btn-pages">
                                            {{-- Previous Button (only if there's a previous page) --}}
                                            @if ($currentPage > 1)
                                            <button wire:click="previousPage" class="pagination-btn pagination-arrow">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>
                                            @endif

                                            {{-- First Page --}}
                                            @if ($startPage > 1)
                                            <button wire:click="gotoPage(1)"
                                                class="pagination-btn {{ $currentPage == 1 ? 'active' : '' }}">1</button>

                                            @if ($showLeftDots)
                                            <span class="pagination-dots">...</span>
                                            @endif
                                            @endif

                                            {{-- Page Numbers --}}
                                            @for ($page = $startPage; $page <= $endPage; $page++) <button
                                                wire:click="gotoPage({{ $page }})"
                                                class="pagination-btn {{ $currentPage == $page ? 'active' : '' }}">
                                                {{ $page }}</button>
                                                @endfor

                                                {{-- Last Page --}}
                                                @if ($endPage < $lastPage) @if ($showRightDots) <span
                                                    class="pagination-dots">...</span>
                                                    @endif

                                                    <button wire:click="gotoPage({{ $lastPage }})"
                                                        class="pagination-btn {{ $currentPage == $lastPage ? 'active' : '' }}">{{ $lastPage }}</button>
                                                    @endif

                                                    {{-- Next Button (only if there's a next page) --}}
                                                    @if ($currentPage < $lastPage) <button wire:click="nextPage"
                                                        class="pagination-btn pagination-arrow next">
                                                        <i class="fa-solid fa-chevron-right"></i>
                                                        </button>
                                                        @endif
                                        </div>
                                        @endif

                            </div>
                            @else

                            <div class="auto-choice-rgt">
                                @if($products->count() > 0)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <p class="m-0" style="font-size: 13px; color:#5b6b7a;">Showing {{ $products->count() }} results </p>
                                    </div>
                                </div>
                                @endif

                                <div class="alert alert-info py-4 px-3" style="background-color:#f7f9fb; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    @if ($this->hasActiveFilters())
                                        <h4 class="mb-2 text-center fw-bold" style="color: #1e3050; font-size: 20px;">No matches found</h4>
                                        <p class="m-0 text-center text-secondary" style="font-size: 15px;">Try adjusting or removing some filters to see more options.</p>
                                    @else
                                        <h4 class="m-0 text-center fw-bold" style="color: #1e3050; font-size: 20px;">No matches found</h4>
                                    @endif
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
                                {{ static_text('no_prod_mach_fil') ?: 'No products found matching your selected filters.' }}
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
        



        
        <!-- Top Affiliated Businesses Section -->
        @if(isset($topAffiliatedBusinesses) && count($topAffiliatedBusinesses) > 0)
        <section class="revie_img_sec border_bt bg_new_grey">
            <div class="container">
                <div class="mb-4">
                    @foreach($topAffiliatedBusinesses as $index => $biz)
                    @php
                        $curLangId = getCurrentLanguageID();
                        $bTrans = $biz->translations->firstWhere('lang_id', $curLangId) ?? $biz->translations->first();
                        $bName = $bTrans->name ?? $biz->name ?? '';
                        $bSlug = $bTrans->slug ?? $biz->slug ?? (string)$biz->id;
                        $longDescription = $bTrans->long_preview_description ?? '';
                        $hasBizTrackedUrl = !empty($biz->getTrackedUrl());
                        $affiliateUrl = $hasBizTrackedUrl ? $biz->getTrackedUrl() : route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bSlug]);
                        
                        // Fetch criteria ratings
                        $criteriaRatings = [];
                        foreach($biz->category->ratingCriteria ?? [] as $criterion) {
                            $avg = $biz->reviews()->join('review_ratings', 'reviews.id', '=', 'review_ratings.review_id')->where('review_ratings.criteria_id', $criterion->id)->avg('review_ratings.rating');
                            if ($avg !== null) {
                                $criteriaRatings[$criterion->name] = round($avg, 1);
                            }
                        }
                        $recommendPercent = $biz->reviews()->count() > 0 ? round(($biz->reviews()->where('recommend', 1)->count() / $biz->reviews()->count()) * 100) : 0;
                    @endphp
                    
                    <div class="image_revie_inr m-30 {{ !$loop->last ? 'border-bottom border-gray-200' : '' }}">
                        <!-- <div class="row gx-5"> -->
                            <!-- Left Column: Description, Image, Pros/Cons -->
                            <div class="is-asana-wrp imges_left_sde" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="row sld_rw">
                                        <h2 style=" font-weight: 700 !important; margin-bottom: 24px;">
                                            #{{ $index + 1 }} {{ $bName }}
                                        </h2>
                                    
                                    @if(!empty($longDescription))
                                    <div class="rich-text-content mb-4" style="font-size: 16px; color: #374151; line-height: 1.8;">
                                        {!! $longDescription !!}
                                    </div>
                                    @endif
                                    
                                    @php
                                        $sliderImages = is_array($biz->business_images) ? $biz->business_images : json_decode($biz->business_images ?? '[]', true);
                                        if (empty($sliderImages)) {
                                            $sliderImages = is_array($biz->screenshot_urls) ? $biz->screenshot_urls : json_decode($biz->screenshot_urls ?? '[]', true);
                                        }
                                        $sliderImages = array_values(array_filter((array) $sliderImages));
                                    @endphp
                                    @if(!empty($sliderImages) && count($sliderImages) > 0)
                                    <div class="business-preview-images mb-5">
                                        @php
                                            $firstImg = Str::startsWith($sliderImages[0], ['http://', 'https://']) ? $sliderImages[0] : asset($sliderImages[0]);
                                        @endphp
                                        <div class="business-main-preview mb-3">
                                            <a href="{{ $affiliateUrl }}" target="_blank" rel="sponsored" class="d-block border rounded-3 overflow-hidden" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                                <img src="{{ $firstImg }}" alt="{{ $bName }}" id="main-preview-img-{{ $biz->id }}" class="img-fluid w-100 preview-main-display" >
                                            </a>
                                        </div>
                                        @if(count($sliderImages) > 1)
                                        <div class="business-preview-thumbnails d-flex flex-wrap align-items-center justify-content-start">
                                            @foreach($sliderImages as $thumbIndex => $thumbImg)
                                                @php
                                                    $tSrc = Str::startsWith($thumbImg, ['http://', 'https://']) ? $thumbImg : asset($thumbImg);
                                                @endphp
                                                <div class="preview-thumb-item {{ $thumbIndex === 0 ? 'active' : '' }}"
                                                     onmouseenter="switchCategoryPreview('{{ $biz->id }}', '{{ $tSrc }}', this)"
                                                     onclick="switchCategoryPreview('{{ $biz->id }}', '{{ $tSrc }}', this)">
                                                    <img src="{{ $tSrc }}" alt="{{ $bName }} preview {{ $thumbIndex + 1 }}" class="preview-thumb-img">
                                                </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    <!-- Pros and Cons Section -->
                                    @php
                                        $aggregatedProCons = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                            ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                            ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                            ->where('reviews.business_id', $biz->id)
                                            ->where('reviews.status', 'active')
                                            ->select('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as reviews_count'))
                                            ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text')
                                            ->orderByDesc('reviews_count')
                                            ->get();
                                            
                                        $topPros = $aggregatedProCons->where('type', 'pro')->values()->take(5);
                                        $topCons = $aggregatedProCons->where('type', 'con')->values()->take(5);
                                    @endphp
                                    <div class="col-lg-12 mt-2 mb-4 pros-cons-wrapper mt-4">
                                        <div class="pros-cons-header mb-3">
                                            <h3 style="margin-bottom: 8px;">
                                                {{ $bName }} pros and cons
                                            </h3>
                                        </div>
                                        
                                        <div class="row g-4 d-flex">
                                            <div class="col-md-6">
                                                <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                                    <div class="card-body">
                                                        <p class="card-titles mb-3" style="font-weight: 500 !important; font-size:16px !important;">Pros</p>
                                                        <ul class="list-unstyled mb-0">
                                                            @forelse($topPros as $pro)
                                                            <li class="d-flex align-items-start mb-2">
                                                                <span class="me-2" style="font-size: 18px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                                <span style="color: #202124;">{{ $pro->text }}
                                                                    <small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $pro->reviews_count }} {{ $pro->reviews_count == 1 ? 'review' : 'reviews' }}</small>
                                                                </span>
                                                            </li>
                                                            @empty
                                                            <li class="text-muted" style="font-size: 14px;">No pros available</li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                                    <div class="card-body">
                                                        <p class="card-titles mb-3" style="font-weight: 500 !important; font-size:16px !important;">Cons</p>
                                                        <ul class="list-unstyled mb-0">
                                                            @forelse($topCons as $con)
                                                            <li class="d-flex align-items-start mb-2">
                                                                <span class="me-2" style="font-size: 18px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                                <span style="color: #202124;">{{ $con->text }}
                                                                    <small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $con->reviews_count }} {{ $con->reviews_count == 1 ? 'review' : 'reviews' }}</small>
                                                                </span>
                                                            </li>
                                                            @empty
                                                            <li class="text-muted" style="font-size: 14px;">No cons available</li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column: Stats and Sidebar (Matching Product Detail Design) -->
                            <div class="thre_revi_rgt">
                                <div class="main_feture">
                                    <div class="fetru_row d-flex justify-content-between">

                                        <!-- Review Breakdown Card -->
                                        <x-review-breakdown-card :business="$biz" />

                                        <!-- Features / USPs Checklist -->
                                        @if($biz->usps->count() > 0)
                                        <div class="main_feature_lg">
                                            <div class="feture_box lft_check_box size15">
                                                <ul class="list-unstyled">
                                                    @foreach ($biz->usps->take(5) as $usp)
                                                        <li class="d-flex flex-row align-items-center size15">
                                                            <div class="grn_chk" >
                                                                <img src="{{ asset('front/img/green-tick.svg') }}" >
                                                            </div>
                                                            <p class="m-0">{{ $usp->text }}</p>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        @endif

                                        



                                        <!-- More about box -->
                                        <x-more-about-business :business="$biz" />
                                    </div>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                    @endforeach
                    

                </div>
            </div>
        </section>
        @endif
        <!-- Top Affiliated Businesses compare section -->
        @if(isset($topAffiliatedBusinesses) && count($topAffiliatedBusinesses) > 0)
            <section class="comparison-table-sec">
                <!-- Comparison Tables -->
                <div class="container">
                    <div class="comparison-table-section my-5 pt-4">
                        @php
                            $resolvedTop10 = \App\Services\ContentTemplateService::resolveCategorySection($category, 'top_10', $currentLangId);
                            $compAlternativesHeadline = !empty($resolvedTop10['headline']) ? $resolvedTop10['headline'] : (!empty($category->translations->compare_alternatives_headline) 
                                ? $category->translations->compare_alternatives_headline 
                                : ('Compare ' . ($category->translations->name ?? $category->name) . ' alternatives'));
                            $compAlternativesSubtext = !empty($resolvedTop10['subtext']) ? $resolvedTop10['subtext'] : ($category->translations->compare_alternatives_subtext ?? '');
                        @endphp
                        <h2>
                            {{ $compAlternativesHeadline }}
                        </h2>
                        @if(!empty($compAlternativesSubtext))
                            <style>
                                .compare-alternatives-subtext p { margin-bottom: 0 !important; }
                                .compare-alternatives-subtext p:not(:last-child) { margin-bottom: 10px !important; }
                            </style>
                            <div class="compare-alternatives-subtext ck-content mb-3">
                                {!! $compAlternativesSubtext !!}
                            </div>
                        @else
                            <div style="margin-bottom: 16px;"></div>
                        @endif

                        <div class="table-responsive rounded-3 border bg-white mb-5" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                            <table class="table align-middle mb-0" style="font-size: 14px; color: #1e3050;">
                                <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <tr>
                                        <th scope="col" style="padding: 14px 18px; font-weight: 500; color: #002347; width: 240px; min-width: 240px; max-width: 240px;"></th>
                                        <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347; width: 140px; min-width: 140px; max-width: 140px;">Overall rating</th>
                                        @php
                                            $allCriteria = [];
                                            if (count($topAffiliatedBusinesses) > 0) {
                                                foreach($topAffiliatedBusinesses[0]->category->ratingCriteria ?? [] as $criterion) {
                                                    $allCriteria[] = $criterion;
                                                }
                                            }
                                        @endphp
                                        @foreach($allCriteria as $criterion)
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 500; color: #002347;">{{ $criterion->name }}</th>
                                        @endforeach
                                        <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 500; color: #002347;">Recommend</th>
                                        <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 500; color: #002347; width: 140px; min-width: 140px; max-width: 140px;">Starting price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $allStartingPrices = collect(getBusinessesWithStartingPrice($topAffiliatedBusinesses))->keyBy('business.id');
                                    @endphp
                                    @foreach($topAffiliatedBusinesses as $biz)
                                        @php
                                            $bName = $biz->translations->first()->name ?? $biz->name ?? '';
                                            $bSlug = $biz->translations->first()->slug ?? $biz->slug ?? '';
                                            $affiliateUrl = $biz->getTrackedUrl() ?? $biz->affiliate_link ?? $biz->permanent_url ?? route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bSlug]);
                                            $recommendPercent = $biz->reviews()->count() > 0 ? round(($biz->reviews()->where('recommend', 1)->count() / $biz->reviews()->count()) * 100) : 0;

                                            $bizStartingPrice = $allStartingPrices->get($biz->id)['starting_price'] ?? null;
                                            if (!$bizStartingPrice && $biz->products) {
                                                $firstPrice = $biz->products->flatMap->prices->first();
                                                if ($firstPrice) {
                                                    $bizStartingPrice = [
                                                        'amount' => $firstPrice->price,
                                                        'currency' => $firstPrice->currency ?? '$',
                                                        'time_unit' => $firstPrice->time_unit ?? 'month'
                                                    ];
                                                }
                                            }
                                            $bizCurrency = $bizStartingPrice['currency'] ?? '$';
                                            $bizAmount = isset($bizStartingPrice['amount']) ? number_format($bizStartingPrice['amount'], 2) : null;
                                            $bizTimeUnit = strtolower($bizStartingPrice['time_unit'] ?? '');
                                        @endphp
                                    <tr style="border-bottom: 1px solid #f1f5f9; {{ $loop->first ? 'background-color: #f8fafc;' : '' }}">
                                        <td style="padding: 14px 18px; font-weight: 500; color: #002347;">
                                            <a href="{{ $affiliateUrl }}" target="_blank" rel="sponsored noopener" class="table-business-link d-flex align-items-center gap-2 text-decoration-none" style="color: inherit;">
                                                <div class="top-product-small-logo">
                                                    <x-business-logo :business="$biz" :name="$bName" />
                                                </div>
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <span class="table-business-name" style="color: #002347; font-weight: 500;">{{ $bName }}</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#002347" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="text-center fw-bold" style="padding: 14px; color: #002347; white-space: nowrap;">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                                <span>{{ formatRating($biz->average_rating ?? 0) }}</span>
                                            </span>
                                        </td>
                                        @foreach($allCriteria as $criterion)
                                            @php
                                                $avg = $biz->reviews()->join('review_ratings', 'reviews.id', '=', 'review_ratings.review_id')->where('review_ratings.criteria_id', $criterion->id)->avg('review_ratings.rating');
                                            @endphp
                                            <td class="text-center" style="padding: 14px; color: #5b6b7a; font-weight:500">
                                                {{ $avg !== null ? formatRating($avg) : '-' }}
                                            </td>
                                        @endforeach
                                        <td class="text-center fw-semibold" style="padding: 14px; color: #002347;">
                                            {{ $recommendPercent }}%
                                        </td>
                                        <td class="text-center" style="padding: 14px; color: #002347; font-weight: 500; white-space: nowrap;">
                                            @if($bizStartingPrice && $bizAmount !== null && !empty($biz->is_affiliate))
                                                <span style="font-weight: 600;">{{ $bizCurrency }}{{ $bizAmount }}</span>
                                                @if(!empty($bizStartingPrice['additional_info']) && !in_array(strtoupper(trim((string)$bizStartingPrice['additional_info'])), ['NA', 'N/A', 'NULL']))
                                                    <span style="font-size: 12px; color: #5b6b7a;"> {{ $bizStartingPrice['additional_info'] }}</span>
                                                @elseif(!empty($bizTimeUnit) && !in_array($bizTimeUnit, ['one_time', 'na', 'n/a', 'null']))
                                                    <span style="font-size: 12px; color: #5b6b7a;">/{{ $bizTimeUnit === 'month' ? 'mo' : ($bizTimeUnit === 'year' ? 'yr' : $bizTimeUnit) }}</span>
                                                @endif
                                            @else
                                                <span style="color: #5b6b7a;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            <!-- community section -->
            <section class="community-feedback-sec border_bt">
                <!-- Comparison Tables -->
                <div class="container">
                    <div class="comparison-table-section">
                        <div class="community-feedback-table-section">
                            <div class="mb-3">
                                @php
                                    $resolvedProsCons = \App\Services\ContentTemplateService::resolveCategorySection($category, 'pros_cons', $currentLangId);
                                    $commFeedbackHeadline = !empty($resolvedProsCons['headline']) ? $resolvedProsCons['headline'] : (!empty($category->translations->community_says_headline) 
                                        ? $category->translations->community_says_headline 
                                        : 'What the Localio community says');
                                    $commFeedbackSubtext = !empty($resolvedProsCons['subtext']) ? $resolvedProsCons['subtext'] : ($category->translations->community_says_subtext ?? '');
                                @endphp
                                <h2 style="margin-bottom: 4px;">{{ $commFeedbackHeadline }}</h2>
                                @if(!empty($commFeedbackSubtext))
                                    <style>
                                        .community-says-subtext p { margin-bottom: 0 !important; }
                                        .community-says-subtext p:not(:last-child) { margin-bottom: 10px !important; }
                                    </style>
                                    <div style="font-size: 16px; color: #64748b; margin: 0;" class="community-says-subtext ck-content">
                                        {!! $commFeedbackSubtext !!}
                                    </div>
                                @else
                                    <p style="font-size: 16px; color: #64748b; margin: 0;">Most mentioned pros and cons shared by real users.</p>
                                @endif
                            </div>
                            
                            <div class="table-responsive rounded-3 border bg-white" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                <table class="table align-middle mb-0" style="font-size: 14px; color: #1e3050;">
                                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                        <tr>
                                            <th scope="col" style="padding: 14px 18px; font-weight: 500; color: #002347; width: 240px; min-width: 240px; max-width: 240px;"></th>
                                            <th scope="col" class="text-center" style="padding: 14px 14px; font-weight: 700; color: #002347; width: 140px; min-width: 140px; max-width: 140px;">Overall rating</th>
                                            <th scope="col" style="padding: 14px 18px; font-weight: 500; color: #002347; width: 38%;">Most mentioned pro</th>
                                            <th scope="col" style="padding: 14px 18px; font-weight: 500; color: #002347; width: 38%;">Most mentioned con</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topAffiliatedBusinesses as $biz)
                                            @php
                                                $bName = $biz->translations->first()->name ?? $biz->name ?? '';
                                                $bSlug = $biz->translations->first()->slug ?? $biz->slug ?? '';
                                                $affiliateUrl = $biz->getTrackedUrl() ?? $biz->affiliate_link ?? $biz->permanent_url ?? route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bSlug]);
                                                $topPro = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                                    ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                                    ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                                    ->where('reviews.business_id', $biz->id)
                                                    ->where('reviews.status', 'active')
                                                    ->where('category_pro_cons.type', 'pro')
                                                    ->select('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as reviews_count'))
                                                    ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text')
                                                    ->orderByDesc('reviews_count')
                                                    ->first();
    
                                                $topCon = \Illuminate\Support\Facades\DB::table('review_pro_cons')
                                                    ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
                                                    ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
                                                    ->where('reviews.business_id', $biz->id)
                                                    ->where('reviews.status', 'active')
                                                    ->where('category_pro_cons.type', 'con')
                                                    ->select('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text', \Illuminate\Support\Facades\DB::raw('COUNT(review_pro_cons.review_id) as reviews_count'))
                                                    ->groupBy('category_pro_cons.id', 'category_pro_cons.type', 'category_pro_cons.text')
                                                    ->orderByDesc('reviews_count')
                                                    ->first();
                                            @endphp
                                        <tr style="border-bottom: 1px solid #f1f5f9; {{ $loop->first ? 'background-color: #f8fafc;' : '' }}">
                                            <td style="padding: 14px 18px; font-weight: 500; color: #002347;">
                                                <a href="{{ $affiliateUrl }}" target="_blank" rel="sponsored noopener" class="table-business-link d-flex align-items-center gap-2 text-decoration-none" style="color: inherit;">
                                                    <div class="top-product-small-logo">
                                                        <x-business-logo :business="$biz" :name="$bName" />
                                                    </div>
                                                    <div class="d-flex align-items-center flex-nowrap">
                                                        <span class="table-business-name" style="color: #002347; font-weight: 500;">{{ $bName }}</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#002347" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="text-center fw-bold" style="padding: 14px; color: #002347; white-space: nowrap;">
                                                <span class="d-inline-flex align-items-center gap-1">
                                                    <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                                    <span>{{ formatRating($biz->average_rating ?? 0) }}</span>
                                                </span>
                                            </td>
                                            <td style="padding: 14px 18px; color: #334155; font-weight: 500;">
                                                @if($topPro)
                                                    <div class="d-flex align-items-start">
                                                        <span class="me-2" style="font-size: 16px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                        <span style="color: #202124; font-size: 14px;">{{ \Illuminate\Support\Str::limit($topPro->text, 40) }} ({{ $topPro->reviews_count }})</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="padding: 14px 18px; color: #334155; font-weight: 500;">
                                                @if($topCon)
                                                    <div class="d-flex align-items-start">
                                                        <span class="me-2" style="font-size: 16px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                        <span style="color: #202124; font-size: 14px;">{{ \Illuminate\Support\Str::limit($topCon->text, 40) }} ({{ $topCon->reviews_count }})</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- here we add now comparition section -->
                <!-- Popular Category Comparisons Section -->
        @if(isset($popularComparisons) && count($popularComparisons) > 0)
        @php
            $catTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection 
                ? $category->translations->first() 
                : ($category->translations ?? $category->translation);
            $catCurrentName = $catTrans->name ?? $category->name ?? 'Category';
            $resolvedPopComp = \App\Services\ContentTemplateService::resolveCategorySection($category, 'popular_comparisons', $currentLangId);
            $popCompHeadline = !empty($resolvedPopComp['headline']) ? $resolvedPopComp['headline'] : (!empty($catTrans->popular_comparisons_headline) ? $catTrans->popular_comparisons_headline : 'Popular comparisons');
            $popCompSubtext = !empty($resolvedPopComp['subtext']) ? $resolvedPopComp['subtext'] : ($catTrans->popular_comparisons_subtext ?? null);
        @endphp
        <section class="compare-section p_50 light" id="section-compare" style="background-color: #fdfdfd !important;">
            <div class="container">
                <div class="hd_text mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <h2 style="margin-bottom: 8px;">
                        {{ $popCompHeadline }}
                    </h2>
                    @if(!empty($popCompSubtext))
                        <div class="popular-comparisons-subtext" style="color: #002347; margin: 0;">
                            {!! $popCompSubtext !!}
                        </div>
                    @else
                        <p style="color: #002347; margin: 0;">
                            See how top {{ strtolower($catCurrentName) }} providers compare against each other.
                        </p>
                    @endif
                </div>

                <div class="row g-3" data-aos="fade-up" data-aos-duration="1000">
                    @foreach($popularComparisons as $comp)
                    <div class="col-lg-4 col-md-4 col-12">
                        <a href="{{ $comp['url'] }}" class="comparison-box p-3 rounded-3 border h-100 d-flex flex-column justify-content-center text-decoration-none" style="background-color: #fff !important; border-radius: 12px !important; color: inherit; text-decoration: none; display: flex;">
                            <div class="d-flex align-items-center justify-content-between gap-2">

                                <!-- Business 1 -->
                                <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1 1 0%;">
                                    <div class="top-product-medium-logo">
                                        <x-business-logo :business="$comp['business_1']" :name="$comp['business_1_name']" />
                                    </div>

                                    <div style="min-width: 0;">
                                        <div class="fw-semibold text-dark text-truncate" style="font-size: 13px; color: #1e293b !important;" title="{{ $comp['business_1_name'] }}">
                                            {{ $comp['business_1_name'] }}
                                        </div>
                                        <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                            <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                            <span>{{ formatRating($comp['business_1_rating']) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- VS Circle -->
                                <div class="px-2 vs_circle text-muted flex-shrink-0" style="font-size: 16px; font-family: sans-serif;">vs</div>

                                <!-- Business 2 -->
                                <div class="d-flex align-items-center gap-2 justify-content-end" style="min-width: 0; flex: 1 1 0%;">
                                    <div class="top-product-medium-logo">
                                        <x-business-logo :business="$comp['business_2']" :name="$comp['business_2_name']" />
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="fw-semibold text-dark text-truncate" style="font-size: 13px; color: #1e293b !important;" title="{{ $comp['business_2_name'] }}">
                                            {{ $comp['business_2_name'] }}
                                        </div>
                                        <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                            <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                            <span>{{ formatRating($comp['business_2_rating']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

                <!-- Interactive Compare Tool (Bottom of Section) -->
                @php
                    $resolvedCompTool = \App\Services\ContentTemplateService::resolveCategorySection($category, 'compare_tool', $currentLangId);
                    $compToolHeadline = !empty($resolvedCompTool['headline']) ? $resolvedCompTool['headline'] : (!empty($catTrans->compare_tool_headline)
                        ? $catTrans->compare_tool_headline
                        : ('Compare ' . $catCurrentName . ' with any provider'));
                    $compToolSubtext = !empty($resolvedCompTool['subtext']) ? $resolvedCompTool['subtext'] : ($catTrans->compare_tool_subtext ?? null);
                    $compSlug = $catTrans->comparison_slug ?? 'compare';
                    $vsKeyword = static_text('vs_keyword') ?: 'vs';
                    if (empty($vsKeyword) || $vsKeyword === 'vs_keyword') $vsKeyword = 'vs';
                    $vsKeySlug = \Illuminate\Support\Str::slug($vsKeyword);
                @endphp
                <div class="d-block category-compare-tool-card mt-4 p-4 rounded-4 bg-white border" style="margin-top: 60px !important;" data-aos="fade-up" data-aos-duration="1000" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 16px rgba(0, 35, 71, 0.04); overflow: visible !important;">
                    <div class="mb-3">
                        <p style="font-size: 20px; font-weight: 600; color: #002347; margin-bottom: 6px;">
                            {{ $compToolHeadline }}
                        </p>
                        @if(!empty($compToolSubtext))
                            <div class="compare-tool-subtext" style="color: #5b6b7a;margin: 0;">
                                {!! $compToolSubtext !!}
                            </div>
                        @else
                            <p style="color: #5b6b7a;  margin: 0;">
                                Select another provider from the search box below to generate an instant side-by-side comparison.
                            </p>
                        @endif
                    </div>

                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px !important; overflow: visible !important;">
                        <div class="d-flex align-items-center gap-3 flex-wrap flex-md-nowrap" style="position: relative; overflow: visible !important;">
                            
                            <!-- Left Business Selector (50% equal width) -->
                            <div class="compare-box-col position-relative" style="flex: 1 1 0%; min-width: 220px; overflow: visible !important; position: relative;">
                                <!-- Search Input Wrapper -->
                                <div id="catCompareLeftInputWrapper" style="position: relative; display: flex; align-items: center; width: 100%;">
                                    <i class="fas fa-search" style="position: absolute; left: 16px; color: #94a3b8; font-size: 14px; pointer-events: none; z-index: 2;"></i>
                                    <input type="text" id="catCompareLeftInput" class="form-control" placeholder="{{ static_text("compare_search_placeholder") ?: "Search by name..." }}" style="font-size: 14px; border: 1.5px solid #cbd5e1; outline: none; padding-left: 42px; padding-right: 16px; height: 48px; width: 100%; border-radius: 10px; background-color: #ffffff;" autocomplete="off">
                                </div>
                                <!-- Dropdown -->
                                <div id="catCompareLeftDropdown" class="custom-search-dropdown" style="display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; width: 100%; max-height: 280px; overflow-y: auto; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); z-index: 999999;"></div>

                                <!-- Selected Pill -->
                                <div id="catCompareLeftPill" class="align-items-center justify-content-between gap-2 px-3 py-2 bg-white border" style="display: none !important; border-color: #cbd5e1 !important; height: 48px; border-radius: 10px !important;">
                                    <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                        <div class="top-product-small-logo" id="catCompareLeftLogo" style="width: 28px; height: 28px; flex-shrink: 0;"></div>
                                        <span id="catCompareLeftName" style="font-size: 14px; font-weight: 600; color: #1e3050; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                                    </div>
                                    <button type="button" id="btnRemoveLeftBusiness" class="btn p-0 border-0 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; border-radius: 6px; background: #f1f5f9; color: #64748b; font-size: 12px; cursor: pointer; flex-shrink: 0;" title="Remove selection">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- VS Label -->
                            <span class="fw-bold text-muted px-1 text-center" style="font-size: 15px; font-family: sans-serif; flex-shrink: 0;">vs</span>

                            <!-- Right Business Selector (50% equal width) -->
                            <div class="compare-box-col position-relative" style="flex: 1 1 0%; min-width: 220px; overflow: visible !important; position: relative;">
                                <!-- Search Input Wrapper -->
                                <div id="catCompareRightInputWrapper" style="position: relative; display: flex; align-items: center; width: 100%;">
                                    <i class="fas fa-search" style="position: absolute; left: 16px; color: #94a3b8; font-size: 14px; pointer-events: none; z-index: 2;"></i>
                                    <input type="text" id="catCompareRightInput" class="form-control" placeholder="{{ static_text("compare_search_placeholder") ?: "Search by name..." }}" style="font-size: 14px; border: 1.5px solid #cbd5e1; outline: none; padding-left: 42px; padding-right: 16px; height: 48px; width: 100%; border-radius: 10px; background-color: #ffffff;" autocomplete="off">
                                </div>
                                <!-- Dropdown -->
                                <div id="catCompareRightDropdown" class="custom-search-dropdown" style="display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; width: 100%; max-height: 280px; overflow-y: auto; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); z-index: 999999;"></div>

                                <!-- Selected Pill -->
                                <div id="catCompareRightPill" class="align-items-center justify-content-between gap-2 px-3 py-2 bg-white border" style="display: none !important; border-color: #cbd5e1 !important; height: 48px; border-radius: 10px !important;">
                                    <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                        <div class="top-product-small-logo" id="catCompareRightLogo" style="width: 28px; height: 28px; flex-shrink: 0;"></div>
                                        <span id="catCompareRightName" style="font-size: 14px; font-weight: 600; color: #1e3050; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                                    </div>
                                    <button type="button" id="btnRemoveRightBusiness" class="btn p-0 border-0 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; border-radius: 6px; background: #f1f5f9; color: #64748b; font-size: 12px; cursor: pointer; flex-shrink: 0;" title="Remove selection">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Compare Action Button -->
                            <button type="button" id="btnRunCategoryCompare" class="blue-btn btn px-4 fw-semibold" style="background-color: #174889; border-color: #174889; color: #ffffff; font-size: 14px; white-space: nowrap; height: 48px; border-radius: 10px; opacity: 0.6; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; min-width: 110px; cursor: not-allowed;" disabled>
                                Compare
                            </button>
                        </div>
                    </div>
                </div>


            </div>
        </section>
        @endif
        
        @php
            $catTransForCompare = $category->translations instanceof \Illuminate\Database\Eloquent\Collection 
                ? $category->translations->first() 
                : ($category->translations ?? $category->translation);
            $safeCompSlug = $compSlug ?? ($catTransForCompare->comparison_slug ?? 'compare');
            if (empty($safeCompSlug)) $safeCompSlug = 'compare';
            $safeVsKeySlug = $vsKeySlug ?? (\Illuminate\Support\Str::slug(static_text('vs_keyword') ?: 'vs') ?: 'vs');
        @endphp
        <!-- Category Compare Data for JS -->
        <script id="cat-compare-data" type="application/json">
        {!! json_encode([
            'businesses' => $allSearchableBusinesses ?? [],
            'compSlug' => $safeCompSlug,
            'vsKeySlug' => $safeVsKeySlug,
            'locale' => app()->getLocale(),
        ]) !!}
        </script>

        <script>
        (function() {
            function initCategoryCompare() {
                const dataEl = document.getElementById('cat-compare-data');
                if (!dataEl) return;

                let config = {};
                try {
                    config = JSON.parse(dataEl.textContent);
                } catch (e) {
                    console.error('Failed to parse compare data', e);
                    return;
                }

                const searchableBusinesses = Array.isArray(config.businesses) ? config.businesses : [];
                const compSlug = config.compSlug || 'compare';
                const vsKeySlug = config.vsKeySlug || 'vs';
                const currentLocale = config.locale || 'en-us';

                const leftInput = document.getElementById('catCompareLeftInput');
                const leftInputWrapper = document.getElementById('catCompareLeftInputWrapper');
                const leftDropdown = document.getElementById('catCompareLeftDropdown');
                const leftPill = document.getElementById('catCompareLeftPill');
                const leftLogo = document.getElementById('catCompareLeftLogo');
                const leftName = document.getElementById('catCompareLeftName');
                const btnRemoveLeft = document.getElementById('btnRemoveLeftBusiness');

                const rightInput = document.getElementById('catCompareRightInput');
                const rightInputWrapper = document.getElementById('catCompareRightInputWrapper');
                const rightDropdown = document.getElementById('catCompareRightDropdown');
                const rightPill = document.getElementById('catCompareRightPill');
                const rightLogo = document.getElementById('catCompareRightLogo');
                const rightName = document.getElementById('catCompareRightName');
                const btnRemoveRight = document.getElementById('btnRemoveRightBusiness');

                const btnCompare = document.getElementById('btnRunCategoryCompare');

                if (!leftInput || !rightInput || !btnCompare) return;

                let selectedLeft = null;
                let selectedRight = null;

                function normalize(str) {
                    return (str || '')
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .trim();
                }

                function updateCompareButtonState() {
                    if (selectedLeft && selectedRight && selectedLeft.slug !== selectedRight.slug) {
                        btnCompare.disabled = false;
                        btnCompare.style.opacity = '1';
                        btnCompare.style.cursor = 'pointer';
                    } else {
                        btnCompare.disabled = true;
                        btnCompare.style.opacity = '0.6';
                        btnCompare.style.cursor = 'not-allowed';
                    }
                }

                function setBusinessLogo(container, name, icon) {
                    if (!container) return;
                    if (icon && icon.trim() !== '') {
                        container.innerHTML = '<img src="' + icon + '" alt="' + name + '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">';
                    } else {
                        const initial = name ? name.trim().charAt(0).toUpperCase() : 'B';
                        container.innerHTML = '<div class="business-initial-logo" style="width: 100%; height: 100%; font-size: 12px; font-weight: 700; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #002347; color: #ffffff;">' + initial + '</div>';
                    }
                }

                function selectLeft(b) {
                    selectedLeft = b;
                    leftName.textContent = b.name;
                    setBusinessLogo(leftLogo, b.name, b.icon);
                    leftInputWrapper.style.display = 'none';
                    leftDropdown.style.display = 'none';
                    leftPill.style.setProperty('display', 'flex', 'important');
                    updateCompareButtonState();
                }

                function clearLeft() {
                    selectedLeft = null;
                    leftInput.value = '';
                    leftPill.style.setProperty('display', 'none', 'important');
                    leftInputWrapper.style.display = 'flex';
                    leftInput.focus();
                    updateCompareButtonState();
                }

                function selectRight(b) {
                    selectedRight = b;
                    rightName.textContent = b.name;
                    setBusinessLogo(rightLogo, b.name, b.icon);
                    rightInputWrapper.style.display = 'none';
                    rightDropdown.style.display = 'none';
                    rightPill.style.setProperty('display', 'flex', 'important');
                    updateCompareButtonState();
                }

                function clearRight() {
                    selectedRight = null;
                    rightInput.value = '';
                    rightPill.style.setProperty('display', 'none', 'important');
                    rightInputWrapper.style.display = 'flex';
                    rightInput.focus();
                    updateCompareButtonState();
                }

                if (btnRemoveLeft) {
                    btnRemoveLeft.onclick = function(e) {
                        e.stopPropagation();
                        clearLeft();
                    };
                }
                if (btnRemoveRight) {
                    btnRemoveRight.onclick = function(e) {
                        e.stopPropagation();
                        clearRight();
                    };
                }

                function renderDropdown(dropdown, input, side) {
                    const rawQuery = input.value;
                    const normQuery = normalize(rawQuery);
                    const excludeSlug = side === 'left' ? (selectedRight ? selectedRight.slug : null) : (selectedLeft ? selectedLeft.slug : null);

                    let matches = searchableBusinesses.filter(function(b) {
                        return b.slug !== excludeSlug;
                    });

                    if (normQuery.length > 0) {
                        matches = matches.filter(function(b) {
                            const normName = normalize(b.name);
                            const normSlug = normalize(b.slug).replace(/-/g, ' ');
                            return normName.indexOf(normQuery) !== -1 || normSlug.indexOf(normQuery) !== -1;
                        });
                    }

                    if (matches.length === 0) {
                        dropdown.innerHTML = '<div class="p-3 text-muted small text-center">No businesses found matching "' + rawQuery + '"</div>';
                        dropdown.style.display = 'block';
                        return;
                    }

                    let html = '';
                    matches.slice(0, 10).forEach(function(b) {
                        const initial = b.name ? b.name.trim().charAt(0).toUpperCase() : 'B';
                        const iconHtml = (b.icon && b.icon.trim() !== '')
                            ? '<img src="' + b.icon + '" width="24" height="24" style="border-radius:6px; object-fit:cover; flex-shrink:0;">'
                            : '<div style="width:24px;height:24px;border-radius:6px;background:#002347;color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">' + initial + '</div>';
                        
                        html += '<div class="cat-compare-item" data-slug="' + b.slug + '" data-name="' + encodeURIComponent(b.name) + '" data-icon="' + (b.icon || '') + '" style="padding: 10px 16px; cursor: pointer; display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: #1e3050; transition: background 0.15s ease;">' + iconHtml + '<span>' + b.name + '</span></div>';
                    });

                    dropdown.innerHTML = html;
                    dropdown.style.display = 'block';

                    const items = dropdown.querySelectorAll('.cat-compare-item');
                    items.forEach(function(item) {
                        item.onclick = function(e) {
                            e.stopPropagation();
                            const b = {
                                name: decodeURIComponent(this.getAttribute('data-name')),
                                slug: this.getAttribute('data-slug'),
                                icon: this.getAttribute('data-icon')
                            };
                            if (side === 'left') {
                                selectLeft(b);
                            } else {
                                selectRight(b);
                            }
                        };
                        item.onmouseover = function() {
                            this.style.background = '#f1f5f9';
                            this.style.color = '#002347';
                        };
                        item.onmouseout = function() {
                            this.style.background = '#ffffff';
                            this.style.color = '#1e3050';
                        };
                    });
                }

                leftInput.onfocus = function() { renderDropdown(leftDropdown, leftInput, 'left'); };
                leftInput.oninput = function() { renderDropdown(leftDropdown, leftInput, 'left'); };
                leftInput.onkeydown = function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const firstItem = leftDropdown.querySelector('.cat-compare-item');
                        if (firstItem) firstItem.click();
                    }
                };

                rightInput.onfocus = function() { renderDropdown(rightDropdown, rightInput, 'right'); };
                rightInput.oninput = function() { renderDropdown(rightDropdown, rightInput, 'right'); };
                rightInput.onkeydown = function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const firstItem = rightDropdown.querySelector('.cat-compare-item');
                        if (firstItem) firstItem.click();
                    }
                };

                document.addEventListener('click', function(e) {
                    if (leftInputWrapper && !leftInputWrapper.contains(e.target) && leftDropdown && !leftDropdown.contains(e.target)) {
                        leftDropdown.style.display = 'none';
                    }
                    if (rightInputWrapper && !rightInputWrapper.contains(e.target) && rightDropdown && !rightDropdown.contains(e.target)) {
                        rightDropdown.style.display = 'none';
                    }
                });

                btnCompare.onclick = function() {
                    if (!selectedLeft || !selectedRight || selectedLeft.slug === selectedRight.slug) return;
                    const targetUrl = '/' + currentLocale + '/' + compSlug + '/' + selectedLeft.slug + '-' + vsKeySlug + '-' + selectedRight.slug;
                    window.location.href = targetUrl;
                };
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCategoryCompare);
            } else {
                initCategoryCompare();
            }
            document.addEventListener('livewire:navigated', initCategoryCompare);
            document.addEventListener('livewire:initialized', initCategoryCompare);
        })();
        </script>
        

        <!-- listing section -->
        <!-- 3. Explore Subcategories Section -->
        @if(isset($exploreSubcategories) && isset($exploreSubcategories['items']) && count($exploreSubcategories['items']) > 0)
        @php
            $catTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection 
                ? $category->translations->first() 
                : ($category->translations ?? $category->translation);
            $resolvedExploreSub = \App\Services\ContentTemplateService::resolveCategorySection($category, 'explore_subcategories', $currentLangId);
            $exploreHeadline = !empty($resolvedExploreSub['headline']) ? $resolvedExploreSub['headline'] : (!empty($catTrans->explore_subcategories_headline) ? $catTrans->explore_subcategories_headline : ($exploreSubcategories['title'] ?? 'Explore categories'));
            $exploreSubtext = !empty($resolvedExploreSub['subtext']) ? $resolvedExploreSub['subtext'] : ($catTrans->explore_subcategories_subtext ?? null);
        @endphp
        <section class="explore-subcategories-sec py-5" style="background: #f7f9fb; border-top: 1px solid #e2e8f0;border-bottom: 1px solid #e2e8f0;">
            <div class="container">
                <div class="text-start mb-4">
                    <h2 class="explore-cat-h2" style="text-transform: math-auto;">
                        {{ $exploreHeadline }}
                    </h2>
                    @if(!empty($exploreSubtext))
                        <div class="explore-cat-subtext" style="color: #002347; margin: 0;">
                            {!! $exploreSubtext !!}
                        </div>
                    @else
                        <p style="color: #002347; margin: 0;">
                            Browse specialized categories to find the exact tools and solutions for your requirements.
                        </p>
                    @endif
                </div>

                <div class="row g-3">
                    @foreach($exploreSubcategories['items'] as $subcatItem)
                    @php
                        $subTrans = null;
                        if (isset($subcatItem->translations)) {
                            if (is_object($subcatItem->translations) && !($subcatItem->translations instanceof \Illuminate\Database\Eloquent\Collection)) {
                                $subTrans = $subcatItem->translations;
                            } elseif ($subcatItem->translations instanceof \Illuminate\Database\Eloquent\Collection && $subcatItem->translations->isNotEmpty()) {
                                $subTrans = $subcatItem->translations->first();
                            }
                        }
                        $subName = $subTrans->name ?? $subcatItem->name ?? 'Subcategory';
                        $subSlug = $subTrans->slug ?? $subcatItem->slug ?? (string)$subcatItem->id;
                    @endphp
                    <div class="col-lg-4 col-md-6 col-6">
                        <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $subSlug]) }}" class="text-decoration-none d-block h-100">
                            <div class="explore-cat-card p-3 border rounded-3 bg-white h-100 d-flex flex-column align-items-center justify-content-between">
                                
                                <div class="mb-3 w-100 d-flex align-items-center justify-content-center">
                                    @if(isset($subcatItem->topRatedImageMedia))
                                        <img src="{{ asset($subcatItem->topRatedImageMedia->dir_path . '/' . $subcatItem->topRatedImageMedia->file_name) }}" alt="{{ $subName }}" style=" object-fit: contain;">
                                    @else
                                        <!-- Placeholder if no SVG is uploaded -->
                                        <div style="width: 100%; height: 100px; background-color: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;"></div>
                                    @endif
                                </div>
                                
                                <div class="exp-cating d-flex justify-content-between align-items-center w-100 mt-auto pt-2" style="border-top: 1px solid #f1f5f9;">
                                    <p class="m-0 fw-bold text-start" style="font-size: 14px; color: #1e3050;">{{ $subName }}</p>
                                    <span class="badge rounded-pill fw-normal" style="color: #64748b; background-color: #f8fafc; border: 1px solid #e2e8f0; font-size: 11px; padding: 5px 10px;">
                                        {{ $subcatItem->businesses()->count() ?? 0 }} listings
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif


        

        <!-- 5. Category Specific FAQ Section -->
        @if(isset($faqs) && is_array($faqs) && count($faqs) > 0)
        @php
            $catTrans = $category->translations instanceof \Illuminate\Database\Eloquent\Collection 
                ? $category->translations->first() 
                : ($category->translations ?? $category->translation);
            $catFaqName = $catTrans->name ?? $category->name ?? 'Category';
            $resolvedCatFaqs = \App\Services\ContentTemplateService::resolveCategorySection($category, 'category_page_faqs', $currentLangId);
            $faqHeadline = !empty($resolvedCatFaqs['headline']) ? $resolvedCatFaqs['headline'] : (!empty($catTrans->category_faqs_headline) ? $catTrans->category_faqs_headline : 'Frequently asked questions');
            $faqSubtext = !empty($resolvedCatFaqs['subtext']) ? $resolvedCatFaqs['subtext'] : ($catTrans->category_faqs_subtext ?? null);
            $catFaqSlug = $catTrans->slug ?? $category->slug ?? '';
            $categoryFaqsUrl = url('/' . app()->getLocale() . '/' . $catFaqSlug . '/faqs');
        @endphp
        <section class="category-faqs-sec cat-faq-sec py-5" style="">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="row align-items-center mb-4">
                            <div class="col-lg-7 col-md-6 col-12 text-start mb-3 mb-md-0">
                                <h2 style="margin-bottom: 8px; font-size: 24px; font-weight: 700; color: #002347;">
                                    {{ $faqHeadline }}
                                </h2>
                                @if(!empty($faqSubtext))
                                    <div class="category-faqs-subtext" style="color: #475569; margin: 0; font-size: 15px;">
                                        {!! $faqSubtext !!}
                                    </div>
                                @else
                                    <p style="color: #475569; margin: 0; font-size: 15px;">
                                        Common questions and answers about {{ strtolower($catFaqName) }} solutions and comparison criteria.
                                    </p>
                                @endif
                            </div>

                            <div class="col-lg-5 col-md-6 col-12">
                                <form action="{{ $categoryFaqsUrl }}" method="GET" class="category-faq-search-form" id="categoryFaqSearchForm">
                                    <div class="input-group" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1.5px solid #cbd5e1; background: #ffffff;">
                                        <input type="text" name="q" id="categoryFaqSearchInput" class="form-control border-0 shadow-none px-3 py-2" placeholder="Search {{ strtolower($catFaqName) }} FAQs..." aria-label="Search FAQs" autocomplete="off" style="font-size: 14px; color: #1e293b; height: 46px;">
                                        <span class="input-group-text bg-white border-0 ps-3 pe-3" style="color: #94a3b8;">
                                            <i class="fas fa-search" style="font-size: 15px;"></i>
                                        </span>
                                        <!-- <button type="submit" class="btn px-3 fw-semibold d-flex align-items-center gap-1" style="background-color: #002347; color: #ffffff; font-size: 13.5px; border-radius: 0; height: 46px;" title="Search FAQs">
                                            <span>Search</span>
                                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                                        </button> -->
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="accordion" id="categoryFaqAccordion">
                            @foreach($faqs as $fIndex => $faq)
                            <div class="accordion-item mb-3 category-faq-item" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                <h3 class="accordion-header" id="headingFaq{{ $fIndex }}">
                                    <button class="accordion-button {{ $fIndex !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq{{ $fIndex }}" aria-expanded="{{ $fIndex === 0 ? 'true' : 'false' }}" aria-controls="collapseFaq{{ $fIndex }}" style="font-weight: 600; font-size: 16px; color: #002347; background-color: #fff;">
                                        {{ $faq['question'] }}
                                    </button>
                                </h3>
                                <div id="collapseFaq{{ $fIndex }}" class="accordion-collapse collapse {{ $fIndex === 0 ? 'show' : '' }}" aria-labelledby="headingFaq{{ $fIndex }}" data-bs-parent="#categoryFaqAccordion">
                                    <div class="accordion-body rich-text-content category-faq-answer" style="background-color: #fff;">
                                        {!! $faq['answer'] !!}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div id="categoryFaqNoResults" class="text-center py-4 px-3 mb-3 bg-white rounded-3 shadow-sm d-none" style="border: 1px dashed #cbd5e1;">
                            <p class="text-muted mb-2" style="font-size: 14.5px;">
                                No preview FAQs match "<span id="categoryFaqSearchTerm" class="fw-semibold text-dark"></span>".
                            </p>
                            <a id="categoryFaqSearchAllLink" href="{{ $categoryFaqsUrl }}" class="btn btn-sm btn-primary px-3 py-2" style="background-color: #002347; border-color: #002347; font-size: 13px; border-radius: 6px;">
                                Search all FAQs on the FAQs page <i class="fas fa-arrow-right ms-1" style="font-size: 11px;"></i>
                            </a>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ $categoryFaqsUrl }}" class="btn-g-link">
                                View all FAQs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const catFaqInput = document.getElementById('categoryFaqSearchInput');
            const catFaqItems = document.querySelectorAll('#categoryFaqAccordion .category-faq-item');
            const catFaqNoResults = document.getElementById('categoryFaqNoResults');
            const catFaqSearchTerm = document.getElementById('categoryFaqSearchTerm');
            const catFaqSearchAllLink = document.getElementById('categoryFaqSearchAllLink');
            const baseFaqUrl = "{{ $categoryFaqsUrl }}";

            if (catFaqInput && catFaqItems.length > 0) {
                catFaqInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    catFaqItems.forEach(item => {
                        const qText = item.querySelector('.accordion-button')?.textContent.toLowerCase() || '';
                        const aText = item.querySelector('.category-faq-answer')?.textContent.toLowerCase() || '';
                        if (query === '' || qText.includes(query) || aText.includes(query)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (catFaqNoResults) {
                        if (visibleCount === 0 && query !== '') {
                            catFaqNoResults.classList.remove('d-none');
                            if (catFaqSearchTerm) catFaqSearchTerm.textContent = catFaqInput.value.trim();
                            if (catFaqSearchAllLink) {
                                catFaqSearchAllLink.href = baseFaqUrl + '?q=' + encodeURIComponent(catFaqInput.value.trim());
                            }
                        } else {
                            catFaqNoResults.classList.add('d-none');
                        }
                    }
                });
            }
        });
        </script>
        @endif

        <!-- 6. Category Community Discussions Section -->
        @php
            $catTransForDisc = $category->translations instanceof \Illuminate\Database\Eloquent\Collection 
                ? $category->translations->first() 
                : ($category->translations ?? $category->translation);
            $catDiscName = $catTransForDisc->name ?? $category->name ?? 'Category';
        @endphp
        <section class="category-discussions-sec py-5" id="categoryDiscussionsSection" style="background: #ffffff; border-top: 1px solid #e2e8f0;">
            <div class="container">
                <x-community-discussion :category="$category" :bName="$catDiscName" />
            </div>
        </section>

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
    <div class="checkbox_field" data-aos="fade-up" data-aos-duration="1000"
        style="margin-top: 10px; display: flex; justify-content: center;">
        <label style="display: flex; align-items: center; gap: 5px;">
            <input type="checkbox" id="agree_terms" name="agree_terms" required>
            <span>{{ static_text('mail_below_text') }}</span>
        </label>
    </div>
</div>
</div> --}}
<x-news-letter-subscription />
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
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
window.addEventListener('scroll-to-top', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
</script>



<script>
function switchCategoryPreview(bizId, imgSrc, thumbElement) {
    var mainImg = document.getElementById('main-preview-img-' + bizId);
    if (mainImg && mainImg.src !== imgSrc) {
        mainImg.src = imgSrc;
    }
    if (thumbElement && thumbElement.parentElement) {
        var siblings = thumbElement.parentElement.querySelectorAll('.preview-thumb-item');
        siblings.forEach(function(el) {
            el.classList.remove('active');
        });
        thumbElement.classList.add('active');
    }
}
</script>



</div>
