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
                                                <p class="msp_subtitle">Write reviews, join discussions, and share your experience.</p>

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


                        

                        <!-- mobile nav -->
                        <div class="mobile-sidebar" id="mobile-sidebar">
                            <div class="mobile-sidebar-track" id="mobile-sidebar-track">

                                {{-- VIEW 1: Main Mobile Menu View --}}
                                <div class="mobile-sidebar-view mobile-view-main" id="mobile-view-main">
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

                                        <button type="button" class="mobile-sidebar-close" aria-label="Close menu">
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
                                            {{-- "Categories" heading is NOT a link --}}
                                            <div class="menu_section">
                                                <h3 class="section_heading">
                                                    {{ $headerContent['Categories'] ?? 'Categories' }}
                                                </h3>
                                                <hr class="section_divider">

                                                <ul class="flat_list">
                                                    <li>
                                                        <a href="{{ route('top-rated-product', ['locale' => session('lang_code', 'en-us')]) }}">
                                                            <span>{{ $headerContent['top_rated'] ?? ($headerContent['top_rated_product'] ?? 'Top rated') }}</span>
                                                        </a>
                                                    </li>
                                                    @foreach($sidebarCategories as $cat)
                                                        @if($cat->translation)
                                                        <li>
                                                            <a href="javascript:void(0);" class="mobile-cat-nav-btn" data-target="mobile-subcat-pane-{{ $cat->id }}">
                                                                <span>{{ $cat->translation->name }}</span>
                                                                <i class="fa-solid fa-chevron-right mobile-cat-right-arrow"></i>
                                                            </a>
                                                        </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- VIEW 2: Category & Subcategories View --}}
                                <div class="mobile-sidebar-view mobile-view-subcats" id="mobile-view-subcats">
                                    <div class="mobile-subcat-header">
                                        <button type="button" class="mobile-subcat-back-btn" id="mobile-subcat-back-btn">
                                            <i class="fa-solid fa-chevron-left"></i>
                                            <span>All Categories</span>
                                        </button>
                                        <button type="button" class="mobile-sidebar-close" aria-label="Close menu">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>

                                    <div class="mobile-subcat-body">
                                        @foreach($sidebarCategories as $cat)
                                            @if($cat->translation)
                                                <div class="mobile-subcat-pane" id="mobile-subcat-pane-{{ $cat->id }}" style="display: none;">
                                                    {{-- Category Title (clickable to category page, orange text, WITHOUT arrow to top/bottom) --}}
                                                    <div class="mobile-subcat-parent-heading">
                                                        <a href="{{ route('category.detail', [
                                                                'locale' => app()->getLocale(),
                                                                'slug' => $cat->translation->slug
                                                            ]) }}" class="mobile-subcat-parent-link">
                                                            {{ $cat->translation->name }}
                                                        </a>
                                                    </div>

                                                    {{-- Subcategories List (each clickable to its subcategory page) --}}
                                                    <ul class="mobile-subcat-items-list">
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
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="mobile-sidebar-overlay"></div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const sidebar = document.getElementById('mobile-sidebar');
                            const track = document.getElementById('mobile-sidebar-track');
                            const overlay = document.querySelector('.mobile-sidebar-overlay');
                            const subcatView = document.getElementById('mobile-view-subcats');
                            const backBtn = document.getElementById('mobile-subcat-back-btn');
                            const catTriggers = document.querySelectorAll('.mobile-cat-nav-btn');
                            const subcatPanes = document.querySelectorAll('.mobile-subcat-pane');

                            function openSidebar() {
                                if (sidebar) sidebar.classList.add('active');
                                if (overlay) overlay.classList.add('active');
                                document.body.style.overflow = 'hidden';
                            }

                            function closeSidebar() {
                                if (sidebar) sidebar.classList.remove('active');
                                if (overlay) overlay.classList.remove('active');
                                document.body.style.overflow = '';
                                setTimeout(function() {
                                    if (track) track.classList.remove('slide-subcat');
                                }, 300);
                            }

                            // Open mobile sidebar from hamburger button
                            jQuery(document).on('click', '.mobile-sidebar-btn', function(e) {
                                e.preventDefault();
                                openSidebar();
                            });

                            // Close mobile sidebar
                            jQuery(document).on('click', '.mobile-sidebar-close, .mobile-sidebar-overlay', function(e) {
                                e.preventDefault();
                                closeSidebar();
                            });

                            // Clicking a category in View 1 -> switch to View 2
                            catTriggers.forEach(function(trigger) {
                                trigger.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const targetId = this.getAttribute('data-target');

                                    // Hide all panes and show selected pane
                                    subcatPanes.forEach(function(pane) {
                                        pane.style.display = 'none';
                                    });

                                    const activePane = document.getElementById(targetId);
                                    if (activePane) {
                                        activePane.style.display = 'block';
                                    }

                                    if (subcatView) subcatView.scrollTop = 0;
                                    if (track) track.classList.add('slide-subcat');
                                });
                            });

                            // Clicking "< All Categories" -> switch back to View 1
                            if (backBtn) {
                                backBtn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    if (track) track.classList.remove('slide-subcat');
                                });
                            }
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
