<?php
    $lang_id = getCurrentLanguageID();
    $headerLogo = \App\Models\HeaderContent::where([['lang_id', 1], ['meta_key', 'header_logo']])->first();
    $headerContent = \App\Models\HeaderContent::where([['lang_id', $lang_id], ['type', 'text']])->pluck('meta_value', 'meta_key');
    if ($headerContent->isEmpty()) {
        $headerContent = \App\Models\HeaderContent::where([['lang_id', 1], ['type', 'text']])->pluck('meta_value', 'meta_key');
    }
    $pageClass = $pageClass ?? '';
    $routeName = $routeName ?? Route::currentRouteName() ?? 'none';
?>
    <header>
        <section class="sec_head {{ $pageClass }} page-route-{{ $routeName ?? 'none' }}">
            <div class="bottom_header dark">
                <div class="container-fluid">
                    <div class="header_row">
                        <div class="search_logo">
                            <div class="logo_col">
                                <!-- <a href="{{ url('/' ?? '') }}" class="brand"><img src="{{ asset('front/img/logo.svg') }}"></a>               -->
                                @if (isset($headerLogo) && $headerLogo)
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand"><img
                                        src="{{ asset($headerLogo->meta_value) }}"
                                        alt="{{ $headerLogo->meta_key }}"></a>
                                @else
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand"><img
                                        src="{{ asset('front/img/logo.svg') }}"></a>
                                @endif
                            </div>
                        </div>
                        {{-- <div id="myID" class="search-box">
                                <input type="text"
                                    placeholder="{{ $headerContent['header_search_placeholder'] ?? '' }}">
                        <i class="fa fa-search"></i>
                    </div> --}}
                    @if (!in_array(Route::currentRouteName(), ['login', 'register']))
                    <livewire:header-search-bar />
                    @endif

                    <div class="header_button_col">
                        <div class="Header_buttons">
                            @if (!auth()->user())
                            <a href="{{ route('write-review', ['locale' => app()->getLocale()]) }}"
                                class="cta cta_trans">{{ $headerContent['login_btn_lable'] ?? 'Login' }}</a>
                            <a href="javascript:void(0);" onclick="openLoginModal()"
                                class="cta cta_orange wht-t-org-btn">
                                {{ $headerContent['sign_up_btn_lable'] ?? 'Sign up' }}
                            </a>

                            @else
                            <a href="{{ route('write-review', ['locale' => app()->getLocale()]) }}"
                                class="cta cta_trans">Write review</a>
                            <x-user-profile />
                            <!-- <a href="{{ url('/logout') }}"
                                        class="cta cta_orange">{{ $headerContent['sign_out_btn_lable'] ?? 'Sign out' }}</a> -->
                            @endif
                        </div>
                    </div>

                    {{-- <div class="header_button_col">
                       <div class="Header_buttons">
                            @guest
                                <a href="javascript:void(0);"
                                onclick="openLoginModal()"
                                class="cta cta_trans">
                                    {{ $headerContent['login_btn_lable'] ?? 'Login' }}
                    </a>

                    <a href="javascript:void(0);" onclick="openLoginModal()" class="cta cta_orange wht-t-org-btn">
                        {{ $headerContent['sign_up_btn_lable'] ?? 'Sign Up' }}
                    </a>
                    @else
                    <x-user-profile />
                    @endguest
                </div>
            </div> --}}
            </div>
            </div>
            </div>
            <div class="top_header dark">
                <div class="container-fluid">
                    <nav id="mobile_res" class="navbar navbar-expand-lg">
                        <button class="navbar-toggler mobile-sidebar-btn collapsed" type="button">
                            <span class="bar"></span>
                            <span class="bar"></span>
                            <span class="bar"></span>

                        </button>
                        <?php

                        use App\Models\Category;
                        use App\Models\Language;

                        if (Session::has('userDetails')) {
                            $lang_id = Session::get('userDetails')['lang_id'];
                        } else {
                            $lang_id = 1;
                        }
                        $lang_id = getCurrentLanguageID();

                        $categories = Category::whereHas('translation', function ($query) use ($lang_id) {
                            $query->where('lang_id', $lang_id)
                                ->where('is_important', 1);
                        })
                        ->with(['translation' => function ($query) use ($lang_id) {
                            $query->where('lang_id', $lang_id);
                        }])
                        ->with('media')
                        ->get();

                        $sidebarCategories = Category::onlyParents()
                            ->whereHas('translation', function ($query) use ($lang_id) {
                                $query->where('lang_id', $lang_id);
                            })
                            ->with([
                                'translation' => function ($query) use ($lang_id) {
                                    $query->where('lang_id', $lang_id);
                                },
                                'subCategories' => function ($query) {
                                    $query->where('status', 1);
                                },
                                'subCategories.translation' => function ($query) use ($lang_id) {
                                    $query->where('lang_id', $lang_id);
                                },
                                'subCategories.businesses' => function ($query) {
                                    $query->where('status', 1);
                                },
                                'subCategories.businesses.translations' => function ($query) use ($lang_id) {
                                    $query->where('lang_id', $lang_id);
                                }
                            ])
                            ->get();

                        $mobileBusinesses = \App\Models\Business::where('status', 1)
                            ->whereHas('translations', function ($query) use ($lang_id) {
                                $query->where('lang_id', $lang_id);
                            })
                            ->with(['translations' => function ($query) use ($lang_id) {
                                $query->where('lang_id', $lang_id);
                            }])
                            ->limit(3)
                            ->get();

                        ?>




                        <!-- mobile nav -->
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <div class="left_menu">
                                <ul class="menu">
                                    <li class="menu-item cat_menu_item">
                                        <a href="javascript:void(0);" id="open-categories-sidebar"><i
                                                class="fa-solid fa-bars me-2"></i>{{ $headerContent['Categories'] ?? 'All' }}</a>
                                    </li>
                                    <li class=" menu-item cat_menu_item">
                                        <a
                                            href="{{ route('top-rated-product', ['locale' => session('lang_code', 'en-us')]) }}">{{ $headerContent['top_rated'] ?? 'Top rated' }}</a>
                                    </li>
                                    @foreach($categories as $category)
                                    <li class="menu-item">
                                        <a href="{{ route('category.detail', [
                                                    'locale' => app()->getLocale(),
                                                    'slug' => $category->translation->slug
                                                ]) }}">
                                            {{ $category->translation->name }}
                                        </a>
                                    </li>
                                    @endforeach



                                </ul>
                            </div>
                            <div class="close_btn_mobile">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.9819 0.490874L9.99988 7.50855L16.9815 0.527235C17.1357 0.363097 17.3215 0.231792 17.5277 0.141194C17.7339 0.0505961 17.9562 0.00257135 18.1815 0C18.6637 0 19.1261 0.191544 19.4671 0.532495C19.808 0.873445 19.9996 1.33587 19.9996 1.81805C20.0038 2.04095 19.9625 2.26236 19.8781 2.4687C19.7936 2.67504 19.6679 2.86195 19.5087 3.01796L12.4362 9.99928L19.5087 17.0715C19.8083 17.3646 19.9841 17.7616 19.9996 18.1805C19.9996 18.6627 19.808 19.1251 19.4671 19.4661C19.1261 19.807 18.6637 19.9986 18.1815 19.9986C17.9497 20.0082 17.7186 19.9695 17.5026 19.885C17.2866 19.8005 17.0906 19.672 16.9269 19.5077L9.99988 12.49L3.00009 19.4895C2.84646 19.6482 2.66294 19.7748 2.4601 19.8622C2.25727 19.9496 2.03914 19.9959 1.8183 19.9986C1.3361 19.9986 0.873656 19.807 0.532691 19.4661C0.191726 19.1251 0.000173751 18.6627 0.000173751 18.1805C-0.00406521 17.9576 0.0372916 17.7362 0.121706 17.5299C0.206121 17.3235 0.331813 17.1366 0.491068 16.9806L7.56359 9.99928L0.491068 2.92706C0.191413 2.63392 0.0156999 2.23695 0.000173751 1.81805C0.000173751 1.33587 0.191726 0.873445 0.532691 0.532495C0.873656 0.191544 1.3361 0 1.8183 0C2.25465 0.00545415 2.67282 0.181805 2.9819 0.490874Z"
                                        fill="white" />
                                </svg>
                            </div>
                        </div>
                        <div class="search_logo_2">
                            <div class="logo_col">
                                <!-- <a href="{{ url('/' ?? '') }}" class="brand"><img src="{{ asset('front/img/logo.svg') }}"></a>               -->
                                @if (isset($headerLogo) && $headerLogo)
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand"><img
                                        src="{{ asset($headerLogo->meta_value) }}"
                                        alt="{{ $headerLogo->meta_key }}"></a>
                                @else
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand"><img
                                        src="{{ asset('front/img/logo.svg') }}"></a>
                                @endif
                            </div>
                        </div>
                        <style>
    .mobile-sidebar {
        position: fixed;
        top: 0;
        left: -320px;
        width: 320px;
        height: 100vh;
        background: #002347;
        z-index: 99999;
        transition: .3s;
        overflow-y: auto;
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.5);
    }

    .mobile-sidebar.active {
        left: 0;
    }

    .mobile-sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .5);
        opacity: 0;
        visibility: hidden;
        transition: .3s;
        z-index: 99998;
    }

    .mobile-sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .mobile-sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .mobile-sidebar-close {
        border: none;
        background: none;
        color: #fff !important;
        font-size: 24px;
        cursor: pointer;
        transition: color .2s ease;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-sidebar-close:hover {
        color: #F9633B !important;
        background: #90909050;
    }

    @media(min-width:992px) {

        .mobile-sidebar,
        .mobile-sidebar-overlay,
        .mobile-sidebar-btn {
            display: none;
        }
    }

    .mobile-sidebar-logo img {
        height: 28px;
    }

    .mobile-sidebar-body {
        padding: 20px 0;
    }

    .mobile-sidebar-body .menu_section {
        margin-bottom: 25px;
    }

    .mobile-sidebar-body .menu_section .section_heading {
        color: #fff !important;
        padding-left: 18px;
        margin: 0 !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
    }

    /* .mobile-sidebar-body .section_divider {
                margin: 8px auto 15px !important;
                width: 90% !important;
                border-color: rgba(255, 255, 255, 0.1) !important;
            } */

    .mobile-sidebar-body .section_divider {
        margin: 8px auto 5px !important;
        width: 90% !important;
        border-color: #ffffff33 !important;
    }

    .mobile-sidebar-body .flat_list {
        padding: 0 25px;
        margin: 0;
        list-style: none;
    }

    .mobile-sidebar-body .flat_list li {
        padding: 8px 0 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .mobile-sidebar-body .flat_list li:last-child {
        border-bottom: none;
    }

    .mobile-sidebar-body .flat_list li a {
        color: #fff !important;

        text-decoration: none;
        font-size: 15px;
        font-weight: 500 !important;
        transition: all 0.2s ease;
        display: block;
    }

    .mobile-sidebar-body .flat_list li a:hover {
        color: #F9633B !important;
        padding-left: 5px;
    }

    /* Responsive tweaks for top mobile header */
    @media (max-width: 991px) {
        nav#mobile_res .search_logo_2 .brand img {
            max-height: 28px !important;
            width: auto !important;
            object-fit: contain;
        }

        .top_header .navbar-toggler .bar {
            width: 22px !important;
            height: 2px !important;
            margin: 4px 0 !important;
        }

        .top_header .navbar-toggler:not(.collapsed) .bar:nth-child(1) {
            transform: translateY(6px) rotate(45deg) !important;
        }

        .top_header .navbar-toggler:not(.collapsed) .bar:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg) !important;
        }

        #phone_screen {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
        }

        .profile-icon {
            width: 22px !important;
            height: 22px !important;
        }

        .user_img.drop_menu {
            position: relative !important;
            flex: 0 0 28px !important;
            width: 28px !important;
            height: 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .usr_profile {
            width: 28px !important;
            height: 28px !important;
            min-width: 28px !important;
            max-height: 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
        }

        .usr_profile img,
        .user_img img {
            width: 28px !important;
            height: 28px !important;
            min-width: 28px !important;
            max-width: 28px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Mobile profile dropdown alignment and styling */
        .user_img.drop_menu .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            transform: translateY(10px) !important;
            width: 260px !important;
            border-radius: 12px !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15) !important;
            background-color: #fff !important;
            padding: 0 !important;
            z-index: 100000 !important;
        }

        .user_img.drop_menu .dropdown-menu .user_detail {
            display: flex !important;
            align-items: center !important;
            padding: 15px !important;
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #eee !important;
            border-radius: 12px 12px 0 0 !important;
        }

        .user_img.drop_menu .dropdown-menu .user_detail .user_img {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            background-color: #003f7d !important;
            color: #fff !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 13px !important;
            font-weight: 600 !important;
        }

        .user_img.drop_menu .dropdown-menu .user_detail .user_img img {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            max-width: 32px !important;
        }

        .user_img.drop_menu .dropdown-menu .user_detail .user_name {
            padding-left: 10px !important;
        }

        .user_img.drop_menu .dropdown-menu .user_detail .user_name h5 {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #003f7d !important;
            margin-bottom: 2px !important;
        }

        .user_img.drop_menu .dropdown-menu .user_detail .user_name p {
            font-size: 11px !important;
            color: #666 !important;
            margin: 0 !important;
        }

        .user_img.drop_menu .dropdown-menu .dropdown-item {
            display: flex !important;
            align-items: center !important;
            padding: 10px 15px !important;
            font-size: 13px !important;
            color: #333 !important;
            font-weight: 500 !important;
            border-bottom: none !important;
            background: none !important;
        }

        .user_img.drop_menu .dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fa !important;
            color: #F9633B !important;
        }

        .user_img.drop_menu .dropdown-menu .dropdown-item i {
            margin-right: 10px !important;
            font-size: 15px !important;
            width: 18px !important;
            text-align: center !important;
            color: #555 !important;
        }

        .user_img.drop_menu .dropdown-menu .dropdown-item:hover i {
            color: #F9633B !important;
        }

        .user_img.drop_menu .dropdown-menu .dash-icon {
            border-bottom: 1px solid #f1f1f1 !important;
        }

        .user_img.drop_menu .dropdown-menu .dash-icon:last-of-type {
            border-bottom: none !important;
        }

    }
                        @media (max-width: 991px) {

                            /* Backdrop */
                            .msp_backdrop {
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(0, 0, 0, 0.5);
                                z-index: 1040;
                                opacity: 0;
                                visibility: hidden;
                                transition: opacity 0.3s ease;
                            }

                            .msp_backdrop.msp_active {
                                opacity: 1;
                                visibility: visible;
                            }

                            /* Sliding Panel */
                            .msp_panel {
                                position: fixed;
                                top: 0;
                                right: 0;
                                width: 320px;
                                max-width: 85%;
                                height: 100%;
                                background-color: #002347;
                                color: #ffffff;
                                z-index: 1050;
                                transform: translateX(100%);
                                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                display: flex;
                                flex-direction: column;
                                overflow: hidden;
                                text-align: left;
                            }

                            .msp_panel.msp_active {
                                transform: translateX(0);
                            }

                            /* Header */
                            .msp_panel_header {
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                padding: 16px 20px;
                                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                                flex-shrink: 0;
                            }

                            .msp_brand img {
                                max-height: 32px;
                            }

                            .msp_close_btn {
                                background: transparent;
                                border: none;
                                padding: 6px;
                                cursor: pointer;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }

                            .msp_close_btn svg path {
                                fill: #ffffff;
                            }

                            /* Sliding Views Container */
                            .msp_views_wrapper {
                                position: relative;
                                width: 100%;
                                flex: 1;
                                display: flex;
                                overflow: hidden;
                            }

                            .msp_view {
                                min-width: 100%;
                                width: 100%;
                                height: 100%;
                                display: flex;
                                flex-direction: column;
                                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
                                overflow-y: auto;
                                -webkit-overflow-scrolling: touch;
                            }

                            .msp_view_main {
                                transform: translateX(0);
                                justify-content: space-between;
                            }

                            .msp_view_sub {
                                transform: translateX(0);
                            }

                            .msp_views_wrapper.msp_show_sub .msp_view_main {
                                transform: translateX(-100%);
                            }

                            .msp_views_wrapper.msp_show_sub .msp_view_sub {
                                transform: translateX(-100%);
                            }

                            /* View 1: Centered Intro & Buttons */
                            .msp_intro_section {
                                flex: 1;
                                display: flex;
                                flex-direction: column;
                                justify-content: center;
                                padding: 24px 24px 20px;
                            }

                            .msp_intro {
                                padding: 0;
                                text-align: left;
                            }

                            .msp_title {
                                color: #fff !important;
                                font-size: 22px !important;
                                font-weight: 700 !important;
                                margin: 0 0 10px !important;
                            }

                            .msp_subtitle {
                                color: rgba(255, 255, 255, 0.75) !important;
                                font-size: 13.5px;
                                margin: 0 0 28px !important;
                                line-height: 1.45;
                            }

                            .msp_buttons {
                                display: flex;
                                flex-direction: column;
                                gap: 12px;
                            }

                            .msp_btn {
                                display: block;
                                text-align: center;
                                padding: 12px 14px;
                                border-radius: 8px;
                                font-size: 15px;
                                font-weight: 600;
                                text-decoration: none;
                                transition: all 0.2s ease;
                            }

                            .msp_btn_outline {
                                border: 1px solid rgba(255, 255, 255, 0.85);
                                color: #fff !important;
                                background: transparent;
                            }

                            .msp_btn_outline:hover {
                                border-color: #fff;
                                color: #002347 !important;
                                background-color: #fff;
                            }

                            .msp_btn_signin {
                                display: block !important;
                                text-align: center !important;
                                border-radius: 8px !important;
                                font-size: 15px !important;
                                font-weight: 600 !important;
                                padding: 12px 14px !important;
                                line-height: 1.5 !important;
                            }

                            /* View 1: Bottom Navigation Links */
                            .msp_bottom_nav {
                                flex-shrink: 0;
                                border-top: 1px solid rgba(255, 255, 255, 0.12);
                                background: rgba(0, 0, 0, 0.18);
                                padding-bottom: 16px;
                            }

                            .msp_nav_item {
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                padding: 16px 24px;
                                color: #ffffff !important;
                                text-decoration: none !important;
                                font-size: 16px;
                                font-weight: 600;
                                border-bottom: 1px solid rgba(255, 255, 255, 0.06);
                                transition: background-color 0.2s ease, color 0.2s ease, padding-left 0.2s ease;
                                cursor: pointer;
                            }

                            .msp_nav_item:last-child {
                                border-bottom: none;
                            }

                            .msp_nav_item:hover,
                            .msp_nav_item:active {
                                background-color: rgba(255, 255, 255, 0.08);
                                color: #F9633B !important;
                                padding-left: 28px;
                            }

                            .msp_nav_arrow {
                                font-size: 14px;
                                color: rgba(255, 255, 255, 0.6);
                                transition: transform 0.2s ease, color 0.2s ease;
                            }

                            .msp_nav_item:hover .msp_nav_arrow {
                                transform: translateX(4px);
                                color: #F9633B;
                            }

                            /* View 2: Sub-Links View */
                            .msp_sub_top {
                                padding: 14px 20px;
                                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                                background: rgba(0, 0, 0, 0.2);
                                flex-shrink: 0;
                            }

                            .msp_back_btn {
                                background: none;
                                border: none;
                                color: #ffffff;
                                font-size: 16px;
                                font-weight: 600;
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                cursor: pointer;
                                padding: 4px 0;
                                transition: color 0.2s ease;
                            }

                            .msp_back_btn i {
                                font-size: 14px;
                                color: #F9633B;
                                transition: transform 0.2s ease;
                            }

                            .msp_back_btn:hover i {
                                transform: translateX(-3px);
                            }

                            .msp_back_btn:hover {
                                color: #F9633B;
                            }

                            .msp_sub_content {
                                flex: 1;
                                overflow-y: auto;
                                padding: 8px 0 20px;
                            }

                            .msp_sub_list {
                                list-style: none;
                                padding: 0;
                                margin: 0;
                            }

                            .msp_sub_list li {
                                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                            }

                            .msp_sub_list li:last-child {
                                border-bottom: none;
                            }

                            .msp_sub_list li a {
                                display: block;
                                padding: 15px 24px;
                                color: #ffffff !important;
                                text-decoration: none !important;
                                font-size: 15px;
                                font-weight: 500;
                                transition: all 0.2s ease;
                            }

                            .msp_sub_list li a:hover,
                            .msp_sub_list li a:active {
                                background-color: rgba(255, 255, 255, 0.06);
                                color: #F9633B !important;
                                padding-left: 30px;
                            }
                        }
                        </style>

                        <div id="phone_screen" class="mobile_display">
                            <!-- Profile Icon -->
                            @guest
                            <div class="d-flex justify-content-end align-items-center">
                                <img src="{{ asset('front/img/Vector.svg') }}" alt="Profile" class="profile-icon"
                                    onclick="msp_openPanel()">
                            </div>
                            @endguest

                            @auth
                            <div class="user_img drop_menu">
                                <div class="usr_profile">
                                    @if (Auth::user()->profile_image && Auth::user()->profile_image !==
                                    'front/img/default.png')
                                    <img src="{{ asset(Auth::user()->profile_image) }}" class="img-fluid profile-circle"
                                        style='border-radius: 50%; width: 40px; height: 40px; object-fit: cover; flex-shrink: 0;'>
                                    @else
                                    <div class="profile-circle"
                                        style="width: 40px; height: 40px; min-width: 40px; min-height: 40px; border-radius: 50%; background-color: #f76b1c; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span
                                            style="color: white; font-weight: bold; font-size: 18px; line-height: 1; margin-top: 2px;">
                                            {{ strtoupper(substr(Auth::user()->first_name ?? 'A', 0, 1)) }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                <div class="dropdown-menu dropdown-menu-right" style="margin-right: 20px;">
                                    <div class="dropdown-main">
                                        <div class="user_detail">
                                            <div class="user_img">
                                                @if (Auth::user()->profile_image && Auth::user()->profile_image !==
                                                'front/img/default.png')
                                                <img src="{{ asset(Auth::user()->profile_image) }}"
                                                    class="img-fluid profile-circle"
                                                    style='border-radius: 50%; width: 50px; height: 50px; object-fit: cover; flex-shrink: 0;'>
                                                @else
                                                <div class="profile-circle"
                                                    style="width: 50px; height: 50px; min-width: 50px; min-height: 50px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <span
                                                        style="color: white; font-weight: bold; font-size: 20px; line-height: 1; margin-top: 2px;">
                                                        {{ strtoupper(substr(Auth::user()->first_name ?? 'A', 0, 1)) }}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="user_name">
                                                <h5>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h5>
                                                <p>{{ Auth::user()->email }}</p>
                                            </div>
                                        </div>

                                        <div class="dash-icon">
                                            @if(Auth::user()->user_type === 'user')
                                            <a class="dropdown-item"
                                                href="{{ route('user-dashboard', ['locale' => app()->getLocale()]) }}">
                                                <i class="fa-solid fa-envelope-open-text"></i>Dashboard
                                            </a>
                                            @elseif(Auth::user()->user_type === 'vendor')
                                            <a class="dropdown-item"
                                                href="{{ route('vendor-overview', ['locale' => app()->getLocale()]) }}">
                                                <i class="fa-solid fa-envelope-open-text"></i>Dashboard
                                            </a>
                                            @endif
                                        </div>

                                        <div class="dash-icon">
                                            @if(Auth::user()->user_type === 'user')
                                            <a class="dropdown-item"
                                                href="{{ route('user-profile', ['locale' => app()->getLocale()]) }}">
                                                <i class="fa fa-user"></i>My discussions
                                            </a>
                                            @elseif(Auth::user()->user_type === 'vendor')
                                            <a class="dropdown-item"
                                                href="{{ route('vendor-profile', ['locale' => app()->getLocale()]) }}">
                                                <i class="fa fa-user"></i>My profile
                                            </a>
                                            @endif
                                        </div>

                                        @if(Auth::user()->user_type === 'user')
                                        <div class="dash-icon">
                                            <a class="dropdown-item"
                                                href="{{ route('user-support', ['locale' => app()->getLocale()]) }}">
                                                <i class="fa-solid fa-headset"></i>Support
                                            </a>
                                        </div>
                                        @endif

                                        <div class="dash-icon">
                                            @if(Auth::user()->user_type === 'user')
                                            <a class="dropdown-item"
                                                href="{{ route('user-change-password', ['locale' => app()->getLocale()]) }}">
                                                <i class="fa-solid fa-key"></i>Change password
                                            </a>
                                            @endif
                                        </div>

                                        <div class="dash-icon">
                                            @if(Auth::user()->user_type === 'user')
                                            <a class="dropdown-item"
                                                href="{{ route('user-email-preferences', ['locale' => app()->getLocale()]) }}">
                                                <i class="fa-solid fa-envelope"></i>Email preferences
                                            </a>
                                            @elseif(Auth::user()->user_type === 'vendor')
                                            <a class="dropdown-item"
                                                href="{{ route('vendor-configuration', ['locale' => app()->getLocale()]) }}">
                                                <i class="fa-solid fa-gear"></i>Configuration
                                            </a>
                                            @endif
                                        </div>

                                        <div class="dash-icon">
                                            <a class="dropdown-item" href="{{ route('logout') }}">
                                                <i class="fa fa-power-off"></i>Sign out
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endauth

                            <!-- Custom Mobile Profile Panel -->
                            <div class="msp_backdrop" id="mspBackdrop" onclick="msp_closePanel()"></div>

                            <div class="msp_panel" id="mspPanel">

                                <div class="msp_panel_header">
                                    <div class="msp_logo_col">
                                        @if (isset($headerLogo) && $headerLogo)
                                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
                                            class="msp_brand">
                                            <img src="{{ asset($headerLogo->meta_value) }}"
                                                alt="{{ $headerLogo->meta_key }}">
                                        </a>
                                        @else
                                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
                                            class="msp_brand">
                                            <img src="{{ asset('front/img/logo.svg') }}">
                                        </a>
                                        @endif
                                    </div>

                                    <button type="button" class="msp_close_btn" onclick="msp_closePanel()"
                                        aria-label="Close">
                                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M2.9819 0.490874L9.99988 7.50855L16.9815 0.527235C17.1357 0.363097 17.3215 0.231792 17.5277 0.141194C17.7339 0.0505961 17.9562 0.00257135 18.1815 0C18.6637 0 19.1261 0.191544 19.4671 0.532495C19.808 0.873445 19.9996 1.33587 19.9996 1.81805C20.0038 2.04095 19.9625 2.26236 19.8781 2.4687C19.7936 2.67504 19.6679 2.86195 19.5087 3.01796L12.4362 9.99928L19.5087 17.0715C19.8083 17.3646 19.9841 17.7616 19.9996 18.1805C19.9996 18.6627 19.808 19.1251 19.4671 19.4661C19.1261 19.807 18.6637 19.9986 18.1815 19.9986C17.9497 20.0082 17.7186 19.9695 17.5026 19.885C17.2866 19.8005 17.0906 19.672 16.9269 19.5077L9.99988 12.49L3.00009 19.4895C2.84646 19.6482 2.66294 19.7748 2.4601 19.8622C2.25727 19.9496 2.03914 19.9959 1.8183 19.9986C1.3361 19.9986 0.873656 19.807 0.532691 19.4661C0.191726 19.1251 0.000173751 18.6627 0.000173751 18.1805C-0.00406521 17.9576 0.0372916 17.7362 0.121706 17.5299C0.206121 17.3235 0.331813 17.1366 0.491068 16.9806L7.56359 9.99928L0.491068 2.92706C0.191413 2.63392 0.0156999 2.23695 0.000173751 1.81805C0.000173751 1.33587 0.191726 0.873445 0.532691 0.532495C0.873656 0.191544 1.3361 0 1.8183 0C2.25465 0.00545415 2.67282 0.181805 2.9819 0.490874Z"
                                                fill="white" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="msp_views_wrapper" id="mspViewsWrapper">

                                    <!-- VIEW 1: MAIN VIEW -->
                                    <div class="msp_view msp_view_main" id="mspViewMain">
                                        <div class="msp_intro_section">
                                            <div class="msp_intro">
                                                <h2 class="msp_title">Get Started!</h2>
                                                <p class="msp_subtitle">Create an Account and Secure Your Exclusive Logo Today.</p>

                                                <div class="msp_buttons">
                                                    <a href="{{ route('write-review', ['locale' => app()->getLocale()]) }}"
                                                        class="msp_btn msp_btn_outline">Write a review</a>
                                                    <a href="javascript:void(0);" onclick="msp_closePanel(); openLoginModal();"
                                                        class="cta cta_orange wht-t-org-btn msp_btn_signin">
                                                        Sign in
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="msp_bottom_nav">
                                            <a href="javascript:void(0);" class="msp_nav_item" onclick="msp_showSubView('discover')">
                                                <span>{{ $footerContents['discover'] ?? 'Discover' }}</span>
                                                <i class="fa-solid fa-chevron-right msp_nav_arrow"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="msp_nav_item" onclick="msp_showSubView('legal')">
                                                <span>{{ $footerContents['company'] ?? 'Legal' }}</span>
                                                <i class="fa-solid fa-chevron-right msp_nav_arrow"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="msp_nav_item" onclick="msp_showSubView('localio')">
                                                <span>Localio</span>
                                                <i class="fa-solid fa-chevron-right msp_nav_arrow"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- VIEW 2: SUB-LINKS VIEW -->
                                    <div class="msp_view msp_view_sub" id="mspViewSub">
                                        <div class="msp_sub_top">
                                            <button type="button" class="msp_back_btn" onclick="msp_backToMain()" aria-label="Back">
                                                <i class="fa-solid fa-chevron-left"></i>
                                                <span id="mspSubTitle">Discover</span>
                                            </button>
                                        </div>

                                        <div class="msp_sub_content">
                                            <!-- Discover Sublinks -->
                                            <ul class="msp_sub_list" id="mspListDiscover">
                                                <li>
                                                    <a href="{{ route('category', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['categories'] ?? 'All categories' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('top-rated-product', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['top_ratedt'] ?? 'Top rated' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('write-review', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        Write review
                                                    </a>
                                                </li>
                                            </ul>

                                            <!-- Legal Sublinks -->
                                            <ul class="msp_sub_list" id="mspListLegal" style="display: none;">
                                                <li>
                                                    <a href="{{ route('terms-condition', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['terms_&_conditions'] ?? 'Terms of service' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('privacy-policy', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['privacy_policy'] ?? 'Privacy policy' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('cookie-policy', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['cookie_policy'] ?? 'Cookie policy' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('community-guidelines', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['community_guidelines'] ?? 'Community guidelines' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('affiliate-disclosure', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['affiliate_disclosure'] ?? 'Affiliate disclosure' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('copyright-dmca-policy', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['copyright_dmca_policy'] ?? 'Copyright & DMCA policy' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('legal-notice', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['legal_notice'] ?? 'Legal notice' }}
                                                    </a>
                                                </li>
                                            </ul>

                                            <!-- Localio Sublinks -->
                                            <ul class="msp_sub_list" id="mspListLocalio" style="display: none;">
                                                <li>
                                                    <a href="{{ route('who-we-are', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['about-localio'] ?? 'About Localio' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('contact', ['locale' => session('lang_code', app()->getLocale())]) }}">
                                                        {{ $footerContents['contact'] ?? 'Contact' }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <script>
                        function msp_openPanel() {
                            var panel = document.getElementById('mspPanel');
                            var backdrop = document.getElementById('mspBackdrop');
                            if (panel) panel.classList.add('msp_active');
                            if (backdrop) backdrop.classList.add('msp_active');
                            document.body.style.overflow = 'hidden';
                        }

                        function msp_closePanel() {
                            var panel = document.getElementById('mspPanel');
                            var backdrop = document.getElementById('mspBackdrop');
                            if (panel) panel.classList.remove('msp_active');
                            if (backdrop) backdrop.classList.remove('msp_active');
                            document.body.style.overflow = '';
                            msp_backToMain();
                        }

                        function msp_showSubView(catKey) {
                            var titles = {
                                'discover': "{{ $footerContents['discover'] ?? 'Discover' }}",
                                'legal': "{{ $footerContents['company'] ?? 'Legal' }}",
                                'localio': 'Localio'
                            };
                            var titleEl = document.getElementById('mspSubTitle');
                            if (titleEl && titles[catKey]) {
                                titleEl.textContent = titles[catKey];
                            }

                            var lists = document.querySelectorAll('.msp_sub_list');
                            lists.forEach(function(list) {
                                list.style.display = 'none';
                            });

                            var targetId = 'mspList' + catKey.charAt(0).toUpperCase() + catKey.slice(1);
                            var targetList = document.getElementById(targetId);
                            if (targetList) {
                                targetList.style.display = 'block';
                            }

                            var wrapper = document.getElementById('mspViewsWrapper');
                            if (wrapper) {
                                wrapper.classList.add('msp_show_sub');
                            }
                        }

                        function msp_backToMain() {
                            var wrapper = document.getElementById('mspViewsWrapper');
                            if (wrapper) {
                                wrapper.classList.remove('msp_show_sub');
                            }
                        }
                        </script>


                        <style>
                        /* Explicitly hide on desktop, same pattern as .mobile-sidebar */
                        @media (min-width: 992px) {

                            .mobile-cat-panel,
                            .mobile-cat-overlay {
                                display: none !important;
                            }
                        }

                        @media (max-width: 991px) {

                            .section_heading_link {
                                display: block;
                                text-decoration: none;
                            }

                            .section_heading_link .section_heading {
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                cursor: pointer;
                            }

                            .section_heading_link .fa-chevron-right {
                                font-size: 13px;
                                color: rgba(255, 255, 255, 0.6);
                            }

                            /* Overlay */
                            .mobile-cat-overlay {
                                position: fixed;
                                inset: 0;
                                background: rgba(0, 0, 0, 0.5);
                                opacity: 0;
                                visibility: hidden;
                                transition: 0.3s;
                                z-index: 100001;
                            }

                            .mobile-cat-overlay.active {
                                opacity: 1;
                                visibility: visible;
                            }

                            /* Panel: now slides in from the LEFT — same edge as .mobile-sidebar */
                            .mobile-cat-panel {
                                position: fixed;
                                top: 0;
                                left: -320px;
                                width: 320px;
                                max-width: 85%;
                                height: 100vh;
                                background: #002347;
                                z-index: 100002;
                                /* higher than .mobile-sidebar so it sits above it */
                                transition: left 0.3s ease;
                                overflow-y: auto;
                                box-shadow: 4px 0 15px rgba(0, 0, 0, 0.5);
                            }

                            .mobile-cat-panel.active {
                                left: 0;
                            }

                            .mobile-cat-panel-header {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                padding: 20px;
                                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                            }

                            .mobile-cat-panel-title {
                                color: #fff !important;
                                font-size: 16px !important;
                                font-weight: 700 !important;
                                margin: 0 !important;
                                text-transform: uppercase;
                            }

                            .mobile-cat-back {
                                border: none;
                                background: none;
                                color: #fff !important;
                                font-size: 15px;
                                font-weight: 600;
                                cursor: pointer;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                            }

                            .mobile-cat-back:hover {
                                color: #F9633B !important;
                            }

                            .mobile-cat-close {
                                border: none;
                                background: none;
                                color: #fff !important;
                                font-size: 22px;
                                cursor: pointer;
                            }

                            .mobile-cat-close:hover {
                                color: #F9633B !important;
                            }

                            .mobile-cat-panel-body {
                                padding: 10px 0;
                            }

                            .mobile-cat-list {
                                list-style: none;
                                margin: 0;
                                padding: 0;
                            }

                            .mobile-cat-item {
                                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                            }

                            .mobile-cat-toggle {
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                padding: 14px 20px;
                                color: #fff !important;
                                font-size: 15px;
                                font-weight: 600 !important;
                                text-decoration: none;
                                cursor: pointer;
                            }

                            .mobile-cat-toggle:hover {
                                color: #F9633B !important;
                            }

                            .mobile-cat-arrow {
                                font-size: 12px;
                                transition: transform 0.25s ease;
                                color: rgba(255, 255, 255, 0.6);
                                transform: rotate(268deg);
                            }

                            .mobile-cat-toggle.active .mobile-cat-arrow {
                                transform: rotate(90deg);
                                color: #F9633B;
                            }

                            .mobile-cat-sublist {
                                list-style: none;
                                margin: 0;
                                padding: 0 20px;
                                max-height: 0;
                                overflow: hidden;
                                transition: max-height 0.3s ease;
                                background: rgba(255, 255, 255, 0.03);
                            }

                            .mobile-cat-sublist li {
                                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                            }

                            .mobile-cat-sublist li:last-child {
                                border-bottom: none;
                            }

                            .mobile-cat-sublist li a {
                                display: block;
                                padding: 10px 0;
                                color: rgba(255, 255, 255, 0.85) !important;
                                font-size: 14px;
                                font-weight: 500 !important;
                                text-decoration: none;
                                transition: color 0.2s ease;
                            }

                            .mobile-cat-sublist li a:hover {
                                color: #F9633B !important;
                                padding-left: 5px;
                            }
                            
                            /* Mobile sidebar dashboard overrides */
                            .mobile-sidebar-body .left-text {
                                margin-top: 10px;
                            }
                            .mobile-sidebar-body .left-text ul li a.nav-link {
                                border-radius: 150px !important;
                                margin-right: 15px;
                                margin-left: 15px;
                                display: block;
                                transition: background 0.2s, color 0.2s;
                            }
                            .mobile-sidebar-body .left-text ul li a.nav-link.active {
                                background: #f7f9fb !important;
                                color: #012555 !important;
                            }
                            .mobile-sidebar-body .left-text ul li a.nav-link.active .side-links .icons-links {
                                border-color: #012555 !important;
                            }
                            .mobile-sidebar-body .left-text ul li a.nav-link.active img {
                                filter: unset !important;
                            }
                            .mobile-sidebar-body .left-text ul li a.nav-link:not(.active) img {
                                filter: brightness(0) invert(1) !important;
                            }
                        }
                        </style>


                        <!-- mobile nav -->

                        <div class="mobile-sidebar">

                            {{-- Header: Logo on left, Close button on right --}}
                            <div class="mobile-sidebar-header">
                                <div class="mobile-sidebar-logo">
                                    @if (isset($headerLogo) && $headerLogo)
                                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand">
                                        <img src="{{ asset($headerLogo->meta_value) }}"
                                            alt="{{ $headerLogo->meta_key }}">
                                    </a>
                                    @else
                                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand">
                                        <img src="{{ asset('front/img/logo.svg') }}">
                                    </a>
                                    @endif
                                </div>

                                <button class="mobile-sidebar-close">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <div class="mobile-sidebar-body">
                                @if (request()->is('*user-dashboard*') || request()->routeIs('user-*') || request()->is('*user-*'))
                                    {{-- User Dashboard Mobile Navigation (matching desktop sidebar) --}}
                                    <div class="left-text">
                                        <ul class="list-unstyled dash-tab mb-0" id="menu">
                                            <li class="nav-links" style="margin-bottom: 20px;">
                                                <a href="{{ route('user-dashboard', ['locale' => app()->getLocale()]) }}"
                                                    class="nav-link nav_sv {{ request()->routeIs('user-dashboard') ? 'active' : '' }}" style="font-size: 18px; font-weight: 600; color: #fff; padding: 10px 14px; display: block; text-decoration: none;">
                                                    <div class="side-links" style="display: flex; gap: 15px; align-items: center;">
                                                        <span class="icons-links" style="border: 1px solid #ffffff; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 100%;">
                                                            <img src="{{ asset('user-dashboard-theme/img/my_account.svg') }}" alt="" style="width: 20px;">
                                                        </span>
                                                        <span class="icons-text">My account</span>
                                                    </div>
                                                </a>
                                            </li>

                                            <li class="nav-links" style="margin-bottom: 20px;">
                                                <a href="{{ route('user-deal', ['locale' => app()->getLocale()]) }}"
                                                    class="nav-link {{ request()->routeIs('user-deal') ? 'active' : '' }}" style="font-size: 18px; font-weight: 600; color: #fff; padding: 10px 14px; display: block; text-decoration: none;">
                                                    <div class="side-links" style="display: flex; gap: 15px; align-items: center;">
                                                        <span class="icons-links" style="border: 1px solid #ffffff; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 100%;">
                                                            <img src="{{ asset('user-dashboard-theme/img/my_profile.svg') }}" alt="" style="width: 20px;">
                                                        </span>
                                                        <span class="icons-text">My deals</span>
                                                    </div>
                                                </a>
                                            </li>

                                            <li class="nav-links" style="margin-bottom: 20px;">
                                                <a href="{{ route('user-product', ['locale' => app()->getLocale()]) }}"
                                                    class="nav-link nav_sv {{ request()->routeIs('user-product') ? 'active' : '' }}" style="font-size: 18px; font-weight: 600; color: #fff; padding: 10px 14px; display: block; text-decoration: none;">
                                                    <div class="side-links" style="display: flex; gap: 15px; align-items: center;">
                                                        <span class="icons-links" style="border: 1px solid #ffffff; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 100%;">
                                                            <img src="{{ asset('user-dashboard-theme/img/saved_product.svg') }}" alt="" style="width: 20px;">
                                                        </span>
                                                        <span class="icons-text">My favorites</span>
                                                    </div>
                                                </a>
                                            </li>

                                            <li class="nav-links" style="margin-bottom: 20px;">
                                                <a href="{{ route('user-review', ['locale' => app()->getLocale()]) }}"
                                                    class="nav-link nav_sv {{ request()->routeIs('user-review') ? 'active' : '' }}" style="font-size: 18px; font-weight: 600; color: #fff; padding: 10px 14px; display: block; text-decoration: none;">
                                                    <div class="side-links" style="display: flex; gap: 15px; align-items: center;">
                                                        <span class="icons-links" style="border: 1px solid #ffffff; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 100%;">
                                                            <img src="{{ asset('user-dashboard-theme/img/my_review.svg') }}" alt="" style="width: 20px;">
                                                        </span>
                                                        <span class="icons-text">My reviews</span>
                                                    </div>
                                                </a>
                                            </li>

                                            <li class="nav-links" style="margin-bottom: 20px;">
                                                <a href="{{ route('user-profile', ['locale' => app()->getLocale()]) }}"
                                                    class="nav-link {{ request()->routeIs('user-profile') ? 'active' : '' }}" style="font-size: 18px; font-weight: 600; color: #fff; padding: 10px 14px; display: block; text-decoration: none;">
                                                    <div class="side-links" style="display: flex; gap: 15px; align-items: center;">
                                                        <span class="icons-links" style="border: 1px solid #ffffff; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 100%;">
                                                            <img src="{{ asset('user-dashboard-theme/img/my_profile_per.svg') }}" alt="" style="width: 20px;">
                                                        </span>
                                                        <span class="icons-text">My discussions</span>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    {{-- Categories heading is now clickable -> opens right-side panel --}}
                                    <div class="menu_section">
                                        <a href="javascript:void(0);" id="open-mobile-cat-panel"
                                            class="section_heading_link">
                                            <h4 class="section_heading">
                                                {{ $headerContent['Categories'] ?? 'Categories ' }}
                                            </h4>
                                        </a>
                                        <hr class="section_divider">

                                        <ul class="flat_list">
                                            <li>
                                                <a
                                                    href="{{ route('top-rated-product', ['locale' => session('lang_code', 'en-us')]) }}">
                                                    {{ $headerContent['top_rated_product'] ?? 'Top Rated Products' }}
                                                </a>
                                            </li>
                                            @foreach($categories as $category)
                                            <li>
                                                <a href="{{ route('category.detail', [
                                                        'locale' => app()->getLocale(),
                                                        'slug' => $category->translation->slug
                                                    ]) }}">
                                                    {{ $category->translation->name }}
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                        </div>

                        <div class="mobile-sidebar-overlay"></div>
                        <div class="mobile-cat-overlay" id="mobile-cat-overlay"></div>
                        <div class="mobile-cat-panel" id="mobile-cat-panel">

                            <div class="mobile-cat-panel-header">
                                <button class="mobile-cat-back" id="mobile-cat-back">
                                    <i class="fa-solid fa-chevron-left"></i>
                                    {{ $headerContent['Categories'] ?? 'Categories' }}
                                </button>
                                <button class="mobile-cat-close" id="mobile-cat-close">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <div class="mobile-cat-panel-body">
                                <ul class="mobile-cat-list">
                                    @foreach($sidebarCategories as $cat)
                                    @if($cat->translation)
                                    <li class="mobile-cat-item">
                                        <a href="javascript:void(0);" class="mobile-cat-toggle"
                                            data-target="mobile-cat-sub-{{ $cat->id }}">
                                            {{ $cat->translation->name }}
                                            <i class="fa-solid fa-chevron-down mobile-cat-arrow"></i>
                                        </a>

                                        <ul class="mobile-cat-sublist" id="mobile-cat-sub-{{ $cat->id }}">
                                            @foreach($cat->subCategories as $subCat)
                                            @if($subCat->translation)
                                            <li>
                                                <a href="{{ route('category.detail', [
                                                                        'locale' => app()->getLocale(),
                                                                        'slug' => $subCat->translation->slug
                                                                    ]) }}">
                                                    {{ $subCat->translation->name }}
                                                </a>
                                            </li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>

                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const openBtn = document.getElementById('open-mobile-cat-panel');
                            const closeBtn = document.getElementById('mobile-cat-close');
                            const backBtn = document.getElementById('mobile-cat-back');
                            const overlay = document.getElementById('mobile-cat-overlay');
                            const panel = document.getElementById('mobile-cat-panel');
                            const toggles = document.querySelectorAll('.mobile-cat-toggle');

                            if (openBtn) {
                                openBtn.addEventListener('click', function() {
                                    panel.classList.add('active');
                                    overlay.classList.add('active');
                                    document.body.style.overflow = 'hidden';
                                });
                            }

                            function closeCatPanel() {
                                panel.classList.remove('active');
                                overlay.classList.remove('active');
                                document.body.style.overflow = '';
                            }

                            if (closeBtn) closeBtn.addEventListener('click', closeCatPanel);
                            if (backBtn) backBtn.addEventListener('click', closeCatPanel);
                            if (overlay) overlay.addEventListener('click', closeCatPanel);

                            toggles.forEach(function(toggle) {
                                toggle.addEventListener('click', function() {
                                    const targetId = this.getAttribute('data-target');
                                    const sublist = document.getElementById(targetId);
                                    const isActive = this.classList.contains('active');

                                    toggles.forEach(function(t) {
                                        if (t !== toggle) {
                                            t.classList.remove('active');
                                            const otherSublist = document
                                                .getElementById(t.getAttribute(
                                                    'data-target'));
                                            if (otherSublist) otherSublist.style
                                                .maxHeight = null;
                                        }
                                    });

                                    if (isActive) {
                                        toggle.classList.remove('active');
                                        sublist.style.maxHeight = null;
                                    } else {
                                        toggle.classList.add('active');
                                        sublist.style.maxHeight = sublist.scrollHeight + 'px';
                                    }
                                });
                            });

                        });
                        </script>
                    </nav>
                    
                    @if (!in_array(Route::currentRouteName(), ['login', 'register']))
                    @php
                        $isHomePage = (request()->is('/') || request()->is(app()->getLocale()) || Route::currentRouteName() === 'home' || (isset($pageClass) && $pageClass === 'page-home'));
                    @endphp
                    <div id="mobile-search-container" class="mobile-search-container d-block d-lg-none {{ $isHomePage ? 'home-mobile-search' : '' }}">
                        <livewire:header-search-bar />
                    </div>

                    <!-- Mobile Quick Category Nav Bar (Below Search Bar) -->
                    <div id="mobile-cat-nav-bar" class="mobile-cat-nav-bar d-block d-lg-none {{ $isHomePage ? 'home-mobile-cat-nav' : '' }}">
                        <div class="mobile-cat-nav-scroll">
                            <a href="{{ route('top-rated-product', ['locale' => session('lang_code', 'en-us')]) }}"
                               class="mobile-cat-nav-link {{ request()->is('*top-rated*') ? 'active' : '' }}">
                                {{ $headerContent['top_rated'] ?? 'Top rated' }}
                            </a>
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $cat)
                                @php
                                    $catSlug = $cat->translation->slug ?? $cat->slug ?? '';
                                    $catName = $cat->translation->name ?? $cat->name ?? '';
                                    $isCatActive = request()->is('*' . $catSlug . '*') || (request()->segment(2) === $catSlug) || (request()->segment(3) === $catSlug);
                                @endphp
                                <a href="{{ route('category.detail', [
                                            'locale' => app()->getLocale(),
                                            'slug' => $catSlug
                                        ]) }}"
                                   class="mobile-cat-nav-link {{ $isCatActive ? 'active' : '' }}">
                                    {{ $catName }}
                                </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </section>
    </header>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle mobile sidebar drawer
        jQuery(document).on('click', '.mobile-sidebar-btn', function() {
            jQuery('.mobile-sidebar').addClass('active');
            jQuery('.mobile-sidebar-overlay').addClass('active');
        });

        jQuery(document).on('click', '.mobile-sidebar-close, .mobile-sidebar-overlay', function() {
            jQuery('.mobile-sidebar').removeClass('active');
            jQuery('.mobile-sidebar-overlay').removeClass('active');
        });
    });
</script>
