<!DOCTYPE html>
<html lang="en">

<head>
    @php
      $lang_id = getCurrentLanguageID();

        $favicon = \App\Models\HeaderContent::where([
        ['lang_id', $lang_id],
        ['type', 'file']
        ])->where('meta_key', 'favicon_icon')->pluck('meta_value', 'meta_key')->first();
    @endphp


    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>@yield('title', 'User Dashboard | Localio')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
            integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css"
            integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css"
            integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.27/dist/sweetalert2.min.css" rel="stylesheet">

        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('user-dashboard-theme/css/custom1.css') }}?{{ time() }}" />
        <link rel="stylesheet" href="{{ asset('user-dashboard-theme/css/responsive1.css') }}?{{ time() }}" />
        <link rel="stylesheet" href="{{ asset('user-dashboard-theme/Basis Grotesque Pro/stylesheet.css') }}?{{ time() }}">
        <link rel="shortcut icon" href="{{ url('front/img/icon.svg') }}">
        <!-- CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('front/css/style.css') }}?{{ time() }}"> --}}
        {{-- <link rel="stylesheet" href="{{ asset('front/css/responsive.css') }}?{{ time() }}"> --}}

        <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        @if($favicon)
        <link rel="shortcut icon" href="{{ asset($favicon) }}">
        @endif
        @livewireStyles
        <style>
            .profile-circle {
                max-width: 45px;
                height: 44px;
                border-radius: 50%;
                object-fit: cover;
                border: 1px solid #ddd;
                vertical-align: middle;
            }

            .profile-img {
                max-width: 100%;
                height: 89px;
                border-radius: 50%;
                overflow: hidden;
                /* border: 1px solid #ddd; */
                display: flex;
                align-items: center;
                justify-content: center;
                vertical-align: middle;
            }

            .profile-img img {
                max-width: 84px;
                height: 93%;
                object-fit: cover;
                border-radius: 50%;
            }

            .menu-toggler {
                display: none !important;
            }
        </style>
        
        <style>
            /* Responsive tweaks for top mobile header */
            @media (max-width: 991px) {
                header.main_dhdr,
                .main_dhdr {
                    background-color: #003f7d !important;
                    padding: 0 !important;
                    margin: 0 !important;
                }
                /* Center the logo in the navbar */
                .main_dhdr nav.navbar {
                    position: relative !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: space-between !important;
                    height: 56px !important;
                    background-color: #003f7d !important;
                    padding: 0 15px !important;
                }
                .main_dhdr .hdr_lft {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    position: static !important;
                    width: auto !important;
                }
                .main_dhdr .hdr_lft .navbar-brand {
                    position: absolute !important;
                    left: 50% !important;
                    top: 50% !important;
                    transform: translate(-50%, -50%) !important;
                    margin: 0 !important;
                    display: block !important;
                }
                .main_dhdr nav .hdr_lft a img.img-fluid {
                    max-height: 28px !important;
                    width: auto !important;
                    object-fit: contain !important;
                }
                .menu-toggler {
                    display: flex !important;
                    flex-direction: column !important;
                    justify-content: space-between !important;
                    position: absolute !important;
                    left: 15px !important;
                    top: 50% !important;
                    transform: translateY(-50%) !important;
                    width: 22px !important;
                    height: 14px !important;
                    padding: 0 !important;
                    border: none !important;
                    background: transparent !important;
                    cursor: pointer !important;
                }
                .menu-toggler .bar {
                    width: 22px !important;
                    height: 2px !important;
                    background-color: #fff !important;
                    transition: all 0.3s ease !important;
                    margin: 0 !important;
                    display: block !important;
                }
                .hdr_ryt {
                    position: absolute !important;
                    right: 15px !important;
                    top: 50% !important;
                    transform: translateY(-50%) !important;
                    display: block !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .hdr_info {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: flex-end !important;
                    gap: 15px !important;
                }
                .notf.drop_menu a img {
                    width: 20px !important;
                    height: 20px !important;
                    object-fit: contain !important;
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
                /* Reduce gap below mobile header */
                .user_dashbord .user_content {
                    padding-top: 76px !important;
                }
            }
            .main_dhdr nav.navbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    row-gap: 8px;
}

#navbarSupportedContent {
    order: 5;
    flex: 0 0 100%;
    width: 100%;
}

#navbarSupportedContent .left_menu {
    width: 100%;
}

#navbarSupportedContent .menu {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 25px;
}

#navbarSupportedContent .menu .menu-item a {
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    white-space: nowrap;
}

#navbarSupportedContent .menu .menu-item a:hover {
    color: #F9633B;
}

#navbarSupportedContent .close_btn_mobile {
    display: none;
}

/* amazon sidebar */

.category-sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    z-index: 99998;
}
.category-sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}
.category-sidebar {
    position: fixed;
    top: 0;
    left: -380px;
    width: 380px;
    max-width: 85%;
    height: 100vh;
    background: #fff;
    box-shadow: 4px 0 15px rgba(0,0,0,0.25);
    z-index: 99999;
    transition: left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    font-family: inherit;
}
.category-sidebar.show {
    left: 0;
}
.category-sidebar-header {
    background: #003f7d;
    color: #fff;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 55px;
}
.category-sidebar-header .user-greeting {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
}
.category-sidebar-header .user-greeting:hover {
    color: #fff;
    text-decoration: none;
}
.category-sidebar-header .user-greeting .avatar-icon {
    font-size: 24px;
}
.category-sidebar-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
    padding: 5px;
}
.category-sidebar-viewport {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
}
.category-sidebar-panels-container {
    display: flex;
    width: 300%;
    height: 100%;
    transition: transform 0.25s ease-in-out;
}
.category-sidebar-panels-container.slide-active {
    transform: translateX(-33.333%);
}
.category-sidebar-panels-container.slide-business-active {
    transform: translateX(-66.666%);
}
.category-sidebar-panel {
    width: 33.333%;
    height: 100%;
    padding: 20px 0;
    overflow-y: auto;
    box-sizing: border-box;
}
.category-sidebar-panel.sub-panel,
.category-sidebar-panel.business-panel {
    display: none;
}
.category-sidebar-panel.sub-panel.active,
.category-sidebar-panel.business-panel.active {
    display: block;
}
.sidebar-menu-section {
    padding: 0 20px;
}
.sidebar-section-title {
    font-size: 14px !important;
    text-transform: uppercase;
    color: #111  !important;
    font-weight: 700  !important;
    margin-bottom: 12px !important;
    letter-spacing: 0.5px !important;
}
.sidebar-menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.sidebar-menu-list li {
    margin-bottom: 5px;
}
.sidebar-menu-list li a {
    display: block;
    padding: 10px 12px;
    color: #444;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.15s ease, color 0.15s ease;
}
.sidebar-menu-list li a:hover {
    background: #eaeded;
    color: #f26522;
}
.sidebar-menu-divider {
    height: 1px;
    background: #e5e5e5;
    margin: 15px 0;
}
.sub-panel-back {
    padding: 0 20px 10px;
}
.sub-panel-back a {
    display: inline-flex;
    align-items: center;
    color: #111;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
}
.sub-panel-back a:hover {
    color: #f26522;
}
.view-all-link {
    color: #004692 !important;
}
        </style>
    </head>

<body>
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

                        ?>
    <header class="main_dhdr">
        <div class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="hdr_lft">
                    <button class="menu-toggler">
                        <span class="bar bar1"></span>
                        <span class="bar bar2"></span>
                        <span class="bar bar3"></span>
                    </button>
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('front/img/logo.svg') }}" class="img-fluid">
                    </a>
                </div>
                {{-- <div class="form">
                    <input type="search" class="search-box"
                        placeholder="Search for a company or category...">
                    <button class="btn cta_dark active"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div> --}}
                <livewire:user.search-bar placeholder="Search..." />


                <!-- <div class="hdr_ryt">
                    <div class="hdr_info"> -->
                        <a href="{{ route('write-review', ['locale' => app()->getLocale()]) }}"
                            class="cta cta_trans">Write review</a>
                        <x-user-profile/>
                    <!-- </div>
                </div> -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <div class="left_menu">
                        <ul class="menu">
                            <li class="menu-item cat_menu_item">
                                <a href="javascript:void(0);" id="open-categories-sidebar"><i class="fa-solid fa-bars me-2"></i>{{ $headerContent['Categories'] ?? 'All' }}</a>
                            </li>
                            <li class=" menu-item cat_menu_item">
                                <a href="{{ route('top-rated-product', ['locale' => session('lang_code', 'en-us')]) }}">{{ $headerContent['top_rated_product'] ?? 'Top Rated Products' }}</a>
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
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.9819 0.490874L9.99988 7.50855L16.9815 0.527235C17.1357 0.363097 17.3215 0.231792 17.5277 0.141194C17.7339 0.0505961 17.9562 0.00257135 18.1815 0C18.6637 0 19.1261 0.191544 19.4671 0.532495C19.808 0.873445 19.9996 1.33587 19.9996 1.81805C20.0038 2.04095 19.9625 2.26236 19.8781 2.4687C19.7936 2.67504 19.6679 2.86195 19.5087 3.01796L12.4362 9.99928L19.5087 17.0715C19.8083 17.3646 19.9841 17.7616 19.9996 18.1805C19.9996 18.6627 19.808 19.1251 19.4671 19.4661C19.1261 19.807 18.6637 19.9986 18.1815 19.9986C17.9497 20.0082 17.7186 19.9695 17.5026 19.885C17.2866 19.8005 17.0906 19.672 16.9269 19.5077L9.99988 12.49L3.00009 19.4895C2.84646 19.6482 2.66294 19.7748 2.4601 19.8622C2.25727 19.9496 2.03914 19.9959 1.8183 19.9986C1.3361 19.9986 0.873656 19.807 0.532691 19.4661C0.191726 19.1251 0.000173751 18.6627 0.000173751 18.1805C-0.00406521 17.9576 0.0372916 17.7362 0.121706 17.5299C0.206121 17.3235 0.331813 17.1366 0.491068 16.9806L7.56359 9.99928L0.491068 2.92706C0.191413 2.63392 0.0156999 2.23695 0.000173751 1.81805C0.000173751 1.33587 0.191726 0.873445 0.532691 0.532495C0.873656 0.191544 1.3361 0 1.8183 0C2.25465 0.00545415 2.67282 0.181805 2.9819 0.490874Z" fill="white"/>
                        </svg>
                    </div>
                </div>

            </nav>
            <!-- Category Sidebar Drawer (Amazon Style) -->
<div class="category-sidebar-overlay" id="categories-sidebar-overlay"></div>
<div class="category-sidebar" id="categories-sidebar">
    <div class="category-sidebar-header">
@if(Auth::check())
    <a href="{{ route('user-profile', ['locale' => app()->getLocale()]) }}" class="user-greeting">
        @if (Auth::user()->profile_image && Auth::user()->profile_image !== 'front/img/default.png')
            <img src="{{ asset(Auth::user()->profile_image) }}"
                 alt="Profile"
                 class="profile-circle"
                 style="width:40px; height:40px; border-radius:50%; object-fit:cover; flex-shrink:0;">
        @else
            <div class="profile-circle"
                 style="width:40px; height:40px; border-radius:50%; background:#f76b1c; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <span style="color:#fff; font-weight:bold; font-size:18px;">
                    {{ strtoupper(substr(Auth::user()->first_name ?? 'A', 0, 1)) }}
                </span>
            </div>
        @endif

        <span class="ms-2">Hello {{ Auth::user()->first_name }}</span>
    </a>
@else
    <a href="{{ route('login', ['locale' => session('lang_code', 'en-us')]) }}" class="user-greeting">
        <i class="fa-solid fa-circle-user avatar-icon"></i>
        <span>Hello, sign in</span>
    </a>
@endif

    <button class="category-sidebar-close" id="categories-sidebar-close">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>        
    
    <div class="category-sidebar-viewport">
        <div class="category-sidebar-panels-container" id="sidebar-panels-container">
            <!-- Main Panel -->
            <div class="category-sidebar-panel active" id="main-panel">
                <div class="sidebar-menu-section">
                    <h3 class="sidebar-section-title">Categories</h3>
                    <ul class="sidebar-menu-list">
                        @foreach($sidebarCategories as $cat)
                            @if($cat->translation)
                                <li>
                                    <a href="javascript:void(0);" class="parent-category-item" data-category-id="{{ $cat->id }}">
                                        {{ $cat->translation->name }}
                                        <i class="fa-solid fa-chevron-right float-end mt-1"></i>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <!-- Dynamic Category Sub-panels -->
            @foreach($sidebarCategories as $cat)
                @if($cat->translation)
                    <div class="category-sidebar-panel sub-panel" id="sub-panel-{{ $cat->id }}">
                        <div class="sub-panel-back">
                            <a href="javascript:void(0);" class="back-to-main-btn">
                                <i class="fa-solid fa-arrow-left me-2"></i> All categories
                            </a>
                        </div>
                        <div class="sidebar-menu-divider"></div>
                        <div class="sidebar-menu-section">
                            <h3 class="sidebar-section-title">{{ $cat->translation->name }}</h3>
                            <ul class="sidebar-menu-list">
                                @foreach($cat->subCategories as $subCat)
                                    @if($subCat->translation)
                                        <li>
                                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $subCat->translation->slug]) }}">
                                                {{ $subCat->translation->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
        </div>
    </header>
    <section class="user_dashbord">
        <div class="row">
            <div class="col-lg-3 p-0">
                <div class="dashboard_lft">
                    <div class="left-text">
                        <ul class="list-unstyled dash-tab mb-0" id="menu">
                            <li class="nav-links">
                                <a href="{{ route('user-dashboard', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link nav_sv">
                                    <div class="side-links">
                                        <span class="icons-links">
                                            <img src="{{ asset('user-dashboard-theme/img/my_account.svg') }}" alt="">
                                        </span>
                                        <span class="icons-text">My account</span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-links">
                                <a href="{{ route('user-deal', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link">
                                    <div class="side-links">
                                        <span class="icons-links">
                                            <img src="{{ asset('user-dashboard-theme/img/my_profile.svg') }}" alt="">
                                        </span>
                                        <span class="icons-text">My deals</span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-links">
                                <a href="{{ route('user-product', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link nav_sv">
                                    <div class="side-links">
                                        <span class="icons-links">
                                            <img src="{{ asset('user-dashboard-theme/img/saved_product.svg') }}" alt="">
                                        </span>
                                        <span class="icons-text">My favorites</span>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-links">
                                <a href="{{ route('user-review', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link nav_sv">
                                    <div class="side-links">
                                        <span class="icons-links">
                                            <img src="{{ asset('user-dashboard-theme/img/my_review.svg') }}" alt="">
                                        </span>
                                        <span class="icons-text">My reviews</span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-links">
                                <a href="{{ route('user-profile', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link">
                                    <div class="side-links">
                                        <span class="icons-links">
                                            <img src="{{ asset('user-dashboard-theme/img/my_profile_per.svg') }}" alt="">
                                        </span>
                                        <span class="icons-text">My discussions</span>
                                    </div>
                                </a>
                            </li>



                        </ul>
                    </div>
                </div>
            </div>
            
            @yield('content')
        </div>
    </section>
    <?php
    // use App\Models\Language;

    $siteLanguages = Language::where('status', 1)->get();
    ?>
    <footer class="ds_ftr">
        <div class="container-fluid">
            <div class="foot_end_box">
                <div class="reserve_box">
                    ©
                    <?php echo date('Y'); ?> Localio. All rights reserved.
                </div>
                <div class="reserve_box">
                    <div class="custom-select" onclick="toggleSelect()">
                        <div class="lang-selector">
                        <span id="selected-option">{{ 'United States- English' }}</span>
                        <span class="arrow"><i class="fa-solid fa-chevron-up"></i></span> <!-- Downward arrow -->
                        </div>
                    </div>
                    <div class="dropdown-options">
                        <ul class="options">
                            <div class="container footer-langs-container">
                                @foreach ($siteLanguages as $siteLanguage)
                                <li>
                                    <a href="">
                                        {{ $siteLanguage->name }}
                                    </a>
                                </li>
                                @endforeach
                            </div>
                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </footer>
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"
        integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('user-dashboard-theme/js/script.js') }}?{{ time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.27/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- copper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        AOS.init();
    </script>


@stack('scripts')

<script>
    function updateInputBoxClasses() {
        document.querySelectorAll('.input-box input').forEach(function (input) {
            const wrapper = input.closest('.input-box');
            if (input.value?.trim()) {
                wrapper?.classList.add('active');
            } else {
                wrapper?.classList.remove('active');
            }
        });

        // Always keep .active on selects if wrapper exists
        document.querySelectorAll('.input-box select').forEach(function (select) {
            const wrapper = select.closest('.input-box');
            wrapper?.classList.add('active'); // Always add active for selects
        });
    }

    document.addEventListener('DOMContentLoaded', updateInputBoxClasses);

    document.addEventListener('livewire:load', function () {
        updateInputBoxClasses();

        Livewire.hook('message.processed', () => {
            setTimeout(updateInputBoxClasses, 50);
        });
    });

    window.addEventListener('inputs:updated', function () {
        setTimeout(updateInputBoxClasses, 50);
    });

    // Manual input handling
    document.addEventListener('input', function (e) {
        if (e.target.matches('.input-box input')) {
            const wrapper = e.target.closest('.input-box');
            if (e.target.value?.trim()) {
                wrapper?.classList.add('active');
            } else {
                wrapper?.classList.remove('active');
            }
        }

        if (e.target.matches('.input-box select')) {
            const wrapper = e.target.closest('.input-box');
            wrapper?.classList.add('active'); // Always active for selects
        }
    });
</script>


<script>
    $(document).ready(function () {
        const navLinks = $(".nav-links a");

        // Get last stored nav link
        const storedPage = localStorage.getItem("activePage");
        const currentPage = window.location.href;

        // ✅ Apply 'active' class to the nav-link that matches current page
        navLinks.each(function () {
            if (this.href === currentPage) {
                $(this).addClass("active");
            }
        });

        // ✅ Only update localStorage on click (no redirect logic here)
        navLinks.on("click", function () {
            const clickedHref = new URL($(this).attr("href"), window.location.origin).href;
            localStorage.setItem("activePage", clickedHref);
        });
    });
</script>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('open-categories-sidebar');
            const closeBtn = document.getElementById('categories-sidebar-close');
            const overlay = document.getElementById('categories-sidebar-overlay');
            const sidebar = document.getElementById('categories-sidebar');
            const container = document.getElementById('sidebar-panels-container');
            const subPanels = document.querySelectorAll('.category-sidebar-panel.sub-panel');
            const businessPanels = document.querySelectorAll('.category-sidebar-panel.business-panel');

            if (openBtn && sidebar && overlay) {
                openBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden'; // prevent scrolling main body
                });
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
                // Reset view back to main panel
                setTimeout(() => {
                    container.classList.remove('slide-active');
                    container.classList.remove('slide-business-active');
                    subPanels.forEach(panel => {
                        panel.classList.remove('active');
                    });
                    businessPanels.forEach(panel => {
                        panel.classList.remove('active');
                        panel.style.display = 'none';
                    });
                }, 300);
            }

            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);

            // Slide to category sub-menu (2nd level)
            document.querySelectorAll('.parent-category-item').forEach(item => {
                item.addEventListener('click', function () {
                    const catId = this.getAttribute('data-category-id');
                    const targetPanel = document.getElementById('sub-panel-' + catId);
                    
                    // Hide all sub panels, show target sub panel
                    subPanels.forEach(panel => {
                        panel.classList.remove('active');
                    });
                    // Hide business panels
                    businessPanels.forEach(panel => {
                        panel.classList.remove('active');
                        panel.style.display = 'none';
                    });
                    if (targetPanel) {
                        targetPanel.classList.add('active');
                        container.classList.remove('slide-business-active');
                        container.classList.add('slide-active');
                    }
                });
            });

            // Slide to sub-category business-menu (3rd level)
            document.querySelectorAll('.subcategory-item').forEach(item => {
                item.addEventListener('click', function () {
                    const subCatId = this.getAttribute('data-subcategory-id');
                    const targetPanel = document.getElementById('business-panel-' + subCatId);
                    
                    // Hide all business panels, show target business panel
                    businessPanels.forEach(panel => {
                        panel.classList.remove('active');
                        panel.style.display = 'none';
                    });
                    if (targetPanel) {
                        targetPanel.style.display = 'block';
                        targetPanel.classList.add('active');
                        container.classList.add('slide-business-active');
                    }
                });
            });

            // Back to main menu
            document.querySelectorAll('.back-to-main-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    container.classList.remove('slide-active');
                    container.classList.remove('slide-business-active');
                });
            });

            // Back to sub-categories menu (from 3rd level back to 2nd level)
            document.querySelectorAll('.back-to-subcategories-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const parentCatId = this.getAttribute('data-parent-cat-id');
                    const targetPanel = document.getElementById('sub-panel-' + parentCatId);
                    
                    // Hide business panels, make sure parent sub-panel is active
                    subPanels.forEach(panel => {
                        panel.classList.remove('active');
                    });
                    if (targetPanel) {
                        targetPanel.classList.add('active');
                    }
                    container.classList.remove('slide-business-active');
                    container.classList.add('slide-active');
                });
            });
        });
    </script>


<script>
    $(document).ready(function() {
        // Livewire Success
        window.Livewire?.on('swal:success', function(data) {
            console.log("Livewire SweetAlert Success Data:", data);
            if ($.isArray(data)) {
                data = data[0];
            }
            Swal.fire({
                title: data.title ?? "Success",
                text: data.text ?? "Action completed successfully!",
                icon: data.icon ?? "success",
                confirmButtonText: "OK"
            });
        });

        // Livewire Error
        window.Livewire?.on('swal:error', function(data) {
            console.log("Livewire SweetAlert Error Data:", data);
            if ($.isArray(data)) {
                data = data[0];
            }
            Swal.fire({
                title: data.title ?? "Error",
                text: data.text ?? "Something went wrong!",
                icon: data.icon ?? "error",
                confirmButtonText: "OK"
            });
        });

        // ✅ Non-Livewire Flash Success from Session
        @if(session('success'))
            Swal.fire({
                title: "Success",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        @endif

        // ✅ Non-Livewire Flash Error from Session
        @if(session('error'))
            Swal.fire({
                title: "Error",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonText: "OK"
            });
        @endif
    });
</script>




<script>
    function togglePassword(icon) {
        const wrapper = icon.closest('.password-wrapper');
        const input = wrapper.querySelector('input');

        if (input.type === "password") {
            input.type = "text";
            icon.querySelector('i').classList.remove('fa-eye-slash');
            icon.querySelector('i').classList.add('fa-eye');
        } else {
            input.type = "password";
            icon.querySelector('i').classList.remove('fa-eye');
            icon.querySelector('i').classList.add('fa-eye-slash');
        }
    }
</script>

</body>

</html>
