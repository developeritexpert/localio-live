<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $lang_id = getCurrentLanguageID();

    $favicon = \App\Models\HeaderContent::where([['lang_id', $lang_id], ['type', 'file']])
        ->where('meta_key', 'favicon_icon')
        ->pluck('meta_value', 'meta_key')
        ->first();
    ?>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>vendor dashboard</title>
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
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vender_dashboard/css/vendr_dash.css') }}?{{ time() }}" />
    <link rel="stylesheet" href="{{ asset('vender_dashboard/css/vendr_dash_resp.css') }}?{{ time() }}" />
    <link rel="stylesheet" href="{{ asset('vender_dashboard/css/19feb.css') }}?{{ time() }}" />
    <link rel="stylesheet" href="{{ asset('vender_dashboard/css/20feb.css') }}?{{ time() }}" />
    <link rel="stylesheet" href="{{ asset('vender_dashboard/css/21feb.css') }}?{{ time() }}" />

    <link rel="stylesheet" href="{{ asset('user-dashboard-theme/css/custom1.css') }}?{{ time() }}" />
    <link rel="stylesheet" href="{{ asset('user-dashboard-theme/css/responsive1.css') }}?{{ time() }}" />


    <link rel="stylesheet" href="{{ asset('vender_dashboard/Basis Grotesque Pro/stylesheet.css') }}?{{ time() }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.27/dist/sweetalert2.min.css" rel="stylesheet">
    @if ($favicon)
        <link rel="shortcut icon" href="{{ asset($favicon) }}">
    @endif
    @livewireStyles
    <style>
        .profile-circle {
         max-width: 48px;
         height: 54px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        .profile-img {
            width: 107px;
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
    </style>
</head>

<body>


    <header class="main_dhdr">
        <div class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="hdr_lft">
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('front/img/logo.svg') }}" class="img-fluid">
                    </a>
                    <button class="menu-toggler" style="display: none;">
                        <span class="bar bar1"></span>
                        <span class="bar bar2"></span>
                        <span class="bar bar3"></span>
                </div>
                </button>
                {{-- <div class="form">
                    <input type="search" class="search-box"
                        placeholder="Enter a product, category, or what you’d like to compare...">
                    <button class="btn cta_dark active"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div> --}}
                <livewire:user.search-bar placeholder="Search for products or categories..." />




                <!-- <div class="hdr_ryt">
                    <div class="hdr_info"> -->
                        <x-user-profile/>
                    <!-- </div>
                </div> -->
            </nav>
        </div>
    </header>


    <section class="user_dashbord">
        <div class="row">
            <div class="col-lg-3 p-0">
                <div class="dashboard_lft">
                    <div class="left-text">
                        <ul class="list-unstyled dash-tab mb-0" id="menu">
                            <li class="nav-links">
                                <a href="{{ route('vendor-overview', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link ">
                                    <div class="side-links">
                                        <span class="icons-links">
                                            <img src="{{ asset('vender_dashboard/img/my_account.svg') }}"
                                                alt="">
                                        </span>
                                        <span class="icons-text">Dashboard</span>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-links">
                                <a href="{{ route('vendor-edit-list', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link ">
                                    <div class="side-links">
                                        <div class="side_flex">
                                            <div class="sidein_box">
                                                <span class="icons-links">
                                                    <img src="{{ asset('vender_dashboard/img/manage list_img.svg') }}"
                                                        alt="">
                                                </span>
                                                <span class="icons-text">Business Profile</span>
                                            </div>

                                        </div>
                                    </div>
                                </a>

                            </li>

                            {{-- <li class="nav-links">
                                <a href="{{ route('vendor-analytics', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link nav_sv">
                                    <div class="side-links">
                                        <div class="side_flex">
                                            <div class="sidein_box">
                                                <span class="icons-links">
                                                    <img src="{{ asset('vender_dashboard/img/Analytics & Reports_img.svg') }}"
                                                        alt="">
                                                </span>
                                                <span class="icons-text"></span>
                                            </div>
                                            <div class="sidein_box">
                                                <span class="arrow">
                                                    <i class="fa-solid fa-angle-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="sublist">

                                    <li class="sublist_li">
                                        <a class="sublist_inside" href="{{ route('vendor-add-product', ['locale' => app()->getLocale()]) }}">Add New Product
                                        </a>
                                    </li>

                                    <li class="sublist_li">
                                        <a class="sublist_inside"
                                            href="{{ route('vendor-total-product', ['locale' => app()->getLocale()]) }}">Edit Product
                                        </a>
                                    </li>

                                </ul>
                            </li> --}}


                            <li class="nav-links">
                                <a href="{{ route('vendor-product-offer', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link ">
                                    <div class="side-links">
                                        <div class="side_flex">
                                            <div class="sidein_box">
                                                <span class="icons-links">
                                                    <img src="{{ asset('vender_dashboard/img/Analytics & Reports_img.svg') }}"
                                                    alt="">
                                                </span>
                                                <span class="icons-text">Products & Offers</span>
                                            </div>

                                        </div>
                                    </div>
                                </a>

                            </li>


                            {{-- <li class="nav-links">
                                <a href="#" class="nav-link nav_sv">
                                    <div class="side-links">
                                        <div class="side_flex">
                                            <div class="sidein_box">
                                                <span class="icons-links">
                                                    <img src="{{ asset('vender_dashboard/img/Advertising & Promotions_img.svg') }}"
                                                        alt="">
                                                </span>
                                                <span class="icons-text">Reviews & Feedback</span>
                                            </div>
                                            <div class="sidein_box">
                                                <span class="arrow">
                                                    <i class="fa-solid fa-angle-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>



                                <ul class="sublist">
                                    <li class="sublist_li">
                                        <a class="sublist_inside"
                                            href="{{ route('vendor-review', ['locale' => app()->getLocale()]) }}">{{ __('file.view-user-review') }}
                                        </a>
                                    </li>
                                    <li class="sublist_li">
                                        <a class="sublist_inside"
                                            href="{{ route('vendor-review-managment', ['locale' => app()->getLocale()]) }}">{{ __('file.respond-to-review') }}
                                        </a>
                                    </li>
                                </ul>


                            </li> --}}



                            <li class="nav-links">
                                <a href="{{ route('vendor-review-managment', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link ">
                                    <div class="side-links">
                                        <div class="side_flex">
                                            <div class="sidein_box">
                                                <span class="icons-links">
                                                    <img src="{{ asset('vender_dashboard/img/Advertising & Promotions_img.svg') }}"
                                                    alt="">
                                                </span>
                                                <span class="icons-text">Reviews & Feedback</span>
                                            </div>

                                        </div>
                                    </div>
                                </a>

                            </li>


                            {{-- <li class="nav-links">
                                <a href="#" class="nav-link nav_sv">
                                    <div class="side-links">
                                        <div class="side_flex">
                                            <div class="sidein_box">
                                                <span class="icons-links">
                                                    <img src="{{ asset('vender_dashboard/img/Review Management_img.svg') }}"
                                                        alt="">
                                                </span>
                                                <span class="icons-text">Analytics</span>
                                            </div>
                                            <div class="sidein_box">
                                                <span class="arrow">
                                                    <i class="fa-solid fa-angle-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <ul class="sublist">

                                    <li class="sublist_li">
                                        <a class="sublist_inside"
                                            href="{{ route('vendor-analytics', ['locale' => app()->getLocale()]) }}">Analytics & Reports

                                        </a>
                                    </li>


                                    <li class="sublist_li">
                                        <a class="sublist_inside"
                                            href="{{ route('vendor-campaign', ['locale' => app()->getLocale()]) }}">Vendor Campaign
                                        </a>
                                    </li>
                                    <li class="sublist_li">
                                        <a class="sublist_inside"
                                            href="{{ route('vendor-managing-campaign', ['locale' => app()->getLocale()]) }}">Manage Campaign
                                        </a>
                                    </li>
                                </ul>


                            </li> --}}


                            <li class="nav-links">
                                <a href="{{ route('vendor-analytics', ['locale' => app()->getLocale()]) }}"
                                    class="nav-link ">
                                    <div class="side-links">
                                        <div class="side_flex">
                                            <div class="sidein_box">
                                                <span class="icons-links">
                                                    <img src="{{ asset('vender_dashboard/img/Review Management_img.svg') }}"
                                                    alt="">
                                                </span>
                                                <span class="icons-text">Analytics</span>
                                            </div>

                                        </div>
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
    use App\Models\Language;
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
                        <span class="arrow"><i class="fa-solid fa-chevron-down"></i></span> <!-- Downward arrow -->
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
    <script src="{{ asset('vender_dashboard/js/script.js') }}?{{ time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.27/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


    <script>
        AOS.init();
    </script>
   @stack('scripts')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Livewire.on('swal:toast', (data) => {
                // ✅ Fix: if it's array-wrapped, extract the first item
                const toastData = Array.isArray(data) ? data[0] : data;

                Swal.fire({
                    toast: true,
                    icon: toastData.type || 'success',
                    text: toastData.message || 'Operation completed.',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
        });
    </script>






    {{-- <script>
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
    </script> --}}



    <script>
        $(document).ready(function () {
            // Mark current link as active
            let currentPath = window.location.pathname;

            $(".sublist_inside").each(function () {
                let href = $(this).attr("href");

                // Check if the current path matches
                if (currentPath === new URL(href, window.location.origin).pathname) {
                    $(this).addClass("active"); // Highlight current sublink

                    // Show its submenu
                    let sublist = $(this).closest(".sublist");
                    sublist.addClass("show");

                    // Highlight the parent main nav link
                    sublist.closest(".nav-links").find("> .nav-link").addClass("active");
                }
            });

            // Optionally: highlight top-level nav links directly matched
            $(".nav-link").each(function () {
                let href = $(this).attr("href");

                if (currentPath === new URL(href, window.location.origin).pathname) {
                    $(this).addClass("active");
                }
            });
        });
    </script>

    <script>
        $(function() {
            AOS.init();
        });
    </script>

    <script>
        $(document).ready(function() {
            window.Livewire.on('swal:success', function(data) {
                console.log("SweetAlert Success Data:", data); // Debugging output

                if ($.isArray(data)) {
                    data = data[0]; // Extract the object from the array
                }

                Swal.fire({
                    title: data.title ?? "Success",
                    text: data.text ?? "Action completed successfully!",
                    icon: data.icon ?? "success",
                    confirmButtonText: "OK"
                });
            });

            window.Livewire.on('swal:error', function(data) {
                console.log("SweetAlert Error Data:", data); // Debugging output

                if ($.isArray(data)) {
                    data = data[0]; // Extract the object from the array
                }

                Swal.fire({
                    title: data.title ?? "Error",
                    text: data.text ?? "Something went wrong!",
                    icon: data.icon ?? "error",
                    confirmButtonText: "OK"
                });
            });
        });
    </script>
</body>

</html>
