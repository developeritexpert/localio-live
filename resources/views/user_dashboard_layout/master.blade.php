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
        <link rel="stylesheet" href="{{ asset('front/css/style.css') }}?{{ time() }}">
        <link rel="stylesheet" href="{{ asset('front/css/responsive.css') }}?{{ time() }}">
        <link rel="stylesheet" href="{{ asset('user-dashboard-theme/css/custom1.css') }}?{{ time() }}" />
        <link rel="stylesheet" href="{{ asset('user-dashboard-theme/css/responsive1.css') }}?{{ time() }}" />
        <link rel="stylesheet" href="{{ asset('user-dashboard-theme/Basis Grotesque Pro/stylesheet.css') }}?{{ time() }}">
        <link rel="shortcut icon" href="{{ url('front/img/icon.svg') }}">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        @if($favicon)
        <link rel="shortcut icon" href="{{ asset($favicon) }}">
        @endif
        @livewireStyles
        <style>
            /* .profile-circle {
                max-width: 45px;
                height: 44px;
                border-radius: 50%;
                object-fit: cover;
                border: 1px solid #ddd;
                vertical-align: middle;
            } */

            /* .profile-img {
                max-width: 100%;
                height: 89px;
                border-radius: 50%;
                overflow: hidden;
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
            } */

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

/* #navbarSupportedContent {
    order: 5;
    flex: 0 0 100%;
    width: 100%;
}

#navbarSupportedContent .left_menu {
    width: 100%;
} */

/* #navbarSupportedContent .menu {
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
} */

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
    @include('partials.header')
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
    @include('partials.prevent_page_expired')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"
        integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('front/js/script.js') }}?{{ time() }}"></script>
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
