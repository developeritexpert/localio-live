<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <?php
      $lang_id = getCurrentLanguageID();

$favicon = \App\Models\HeaderContent::where([
['lang_id', $lang_id],
['type', 'file']
])->where('meta_key', 'favicon_icon')->pluck('meta_value', 'meta_key')->first();
      ?>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"
        content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fav Icon  -->
    @if($favicon)
    <link rel="shortcut icon" href="{{ asset($favicon) }}">
@endif
    <!-- <link rel="shortcut icon" href="/front/img/icon.svg"> -->

    <title>@yield('meta_title', 'localio')</title>
    <meta name="description" content="@yield('meta_description', '')">
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ asset('admin-theme/assets/css/dashlite.css?ver=3.1.2') }}?{{ time() }}">
    <link rel="stylesheet" href="{{ asset('admin-theme/coustam.css') }}?{{ time() }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('admin-theme/assets/css/theme.css?ver=3.1.2') }}?{{ time() }}">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.4.0/classic/ckeditor.js"></script>

    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v5.15.4/css/all.css">

    <!-- slick slider cdn -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />

    <!-- ckeditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/42.0.0/classic/ckeditor.js"></script>

    <style>
        .dropdown-menu.dropdown-menu-end.show {
            height: 42vh;
            overflow-y: scroll;
        }
    </style>

</head>

<body class="nk-body bg-lighter npc-general has-sidebar ">
    <div class="spinner-container">
        <div class="spinner"></div>
    </div>
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <div class="nk-sidebar nk-sidebar-fixed is-dark " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-menu-trigger">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none"
                            data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                        <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex"
                            data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                    </div>
                    <div class="nk-sidebar-brand">
                        <a href="{{ url('admin-dashboard') ?? '' }}" class="logo-link nk-sidebar-logo">
                            <img class="logo-light logo-img" src="{{ asset('front/img/logo.svg') }}"
                                srcset="{{ asset('front/img/logo.svg') }}" alt="logo">
                            <img class="logo-dark logo-img" src="{{ asset('front/img/logo-dark.svg') }}"
                                srcset="{{ asset('front/img/logo.svg') }}" alt="logo-dark">
                        </a>
                    </div>

                </div><!-- .nk-sidebar-element -->
                <!-- <div class="nk-sidebar-element nk-sidebar-body">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                <li class="nk-menu-heading">
                                    <a href="{{ url('admin-dashboard') ?? '' }}"><h6 class="overline-title text-primary-alt">Dashboard</h6></a>
                                </li>
                                <li class="nk-menu-item has-sub">
                                    <a href="{{ url('admin-dashboard/categories') ?? '#' }}" class="nk-menu-link ">
                                        <span class="nk-menu-icon"><i class="icon fas fa-icons"></i></span>
                                        <span class="nk-menu-text">categories</span>
                                    </a>

                                </li>
                                <li class="nk-menu-item has-sub">
                                    <a href="{{ url('admin-dashboard/filters') ?? '#' }}" class="nk-menu-link ">
                                        <span class="nk-menu-icon"><em class="icon ni ni-filter-fill"></em></span>
                                        <span class="nk-menu-text">Filter</span>
                                    </a>

                                </li>
                                 <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-setting-fill"></em></span>
                                        <span class="nk-menu-text">Setting</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/site-languages') }}" class="nk-menu-link"><span class="nk-menu-text">Site - Languages</span></a>
                                        </li>

                                    </ul>
                                </li>
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-article"></em></span>
                                        <span class="nk-menu-text">Store Front</span>
                                    </a>
                                    <ul class="nk-menu-sub">

                                        <li class="nk-menu-item has-sub">
                                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                                <span class="nk-menu-icon"><em class="icon ni ni-article"></em></span>
                                                <span class="nk-menu-text">Article Section</span>
                                            </a>
                                            <ul class="nk-menu-sub" >
                                                <li class="nk-menu-item">
                                                    <a href="{{ url('/admin-dashboard/article-category') }}" class="nk-menu-link"><span class="nk-menu-text">Article-Category</span></a>
                                                </li>
                                                <li class="nk-menu-item">
                                                    <a href="{{ url('/admin-dashboard/article') }}" class="nk-menu-link"><span class="nk-menu-text">Article</span></a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nk-menu-item has-sub">
                                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                                <span class="nk-menu-icon"><em class="icon ni ni-article"></em></span>
                                                <span class="nk-menu-text">Policies Section</span>
                                            </a>
                                            <ul class="nk-menu-sub" >
                                                <li class="nk-menu-item">
                                                    <a href="{{ url('/admin-dashboard/policies') }}" class="nk-menu-link"><span class="nk-menu-text">Policies</span></a>
                                                </li>
                                                <li class="nk-menu-item">
                                                    <a href="{{ url('/admin-dashboard/rules') }}" class="nk-menu-link"><span class="nk-menu-text">Rules</span></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> -->
            </div>
            <!-- sidebar @e -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <div class="nk-header nk-header-fixed is-light">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ms-n1">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon"
                                    data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="{{ url('admin-dashboard') ?? '' }}" class="logo-link">
                                    <img class="logo-light logo-img" src="{{ asset('admin-theme/images/logo.png') }}"
                                        srcset="{{ asset('admin-theme/images/logo2x.png 2x') }}" alt="logo">
                                    <img class="logo-dark logo-img"
                                        src="{{ asset('admin-theme/images/logo-dark.png') }}"
                                        srcset="{{ asset('admin-theme/images/logo-dark2x.png 2x') }}"
                                        alt="logo-dark">
                                </a>
                            </div>
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <li>
                                        <div class="dropdown">
                                            <a href="#"
                                                class="dropdown-toggle dropdown-indicator btn btn-outline-light btn-white"
                                                data-bs-toggle="dropdown">
                                                Site Language: {{ Cookie::get('language_code', config('app.locale')) }}
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    @foreach (\App\Models\SiteLanguages::where('status', 'active')->get() as $siteLanguage)
                                                        <li>
                                                            <a
                                                                href="{{ url('set-site-active-language/' . $siteLanguage->handle) }}">
                                                                <span>{{ $siteLanguage->name }} -
                                                                    {{ $siteLanguage->handle }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="dropdown language-dropdown d-none d-sm-block me-n1">
                                        <!-- <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                                            <div class="quick-icon border border-light">
                                                <img class="icon" src="{{ asset('admin-theme/images/flags/english-sq.png') }}" alt="">
                                            </div>
                                        </a> -->
                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-s1">
                                            <ul class="language-list">
                                                <li>
                                                    <a href="#" class="language-item">
                                                        <img src="{{ asset('admin-theme/images/flags/english.png') }}"
                                                            alt="" class="language-flag">
                                                        <span class="language-name">English</span>
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                    </li><!-- .dropdown -->
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <em class="icon ni ni-user-alt"></em>
                                                </div>
                                                <div class="user-info d-none d-md-block">
                                                    <div class="user-status">Administrator</div>
                                                    <div class="user-name dropdown-indicator">
                                                        {{ Auth::user()->first_name ?? '' }}</div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span>AB</span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ Auth::user()->name ?? '' }}</span>
                                                        <span class="sub-text">{{ Auth::user()->email ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <!-- <li><a href="html/user-profile-regular.html"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li> -->
                                                    <!-- <li><a href="{{ url('admin-dashboard/setting') ?? '' }}"><em class="icon ni ni-setting-alt"></em><span>Account Setting</span></a></li> -->
                                                    <!-- <li><a href="html/user-profile-activity.html"><em class="icon ni ni-activity-alt"></em><span>Login Activity</span></a></li> -->
                                                    <li><a class="dark-switch" href="#"><em
                                                                class="icon ni ni-moon"></em><span>Dark Mode</span></a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="{{ route('logout') }}"><em
                                                                class="icon ni ni-signout"></em><span>Sign
                                                                out</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @start -->
                <div class="nk-content ">
                    @yield('content')
                </div>
                <!-- content @end -->
                <!-- footer @s -->
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; 2024 by <a
                                    href="{{ url('admin-dashboard') ?? '' }}" target="_blank">localio</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="{{ asset('admin-theme/assets/js/bundle.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/scripts.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/charts/gd-default.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/example-toastr.js?ver=3.1.2') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
        integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- remove confermation pop up -->
    <script>
        $('body').delegate('.removeConfermation', 'click', function(e) {
            event.preventDefault();
            url = $(this).attr('data-url');
            Swal.fire({
                title: "Are you sure?",
                text: "You lost your all related data if you remove this",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
            // console.log(url);
        });
    </script>
    @if (Session::get('error'))
        <script>
            toastr.clear();
            NioApp.Toast('{{ Session::get('error') }}', 'error', {
                position: 'top-right'
            });
        </script>
    @endif
    @if (Session::get('success'))
        <script>
            toastr.clear();
            NioApp.Toast('{{ Session::get('success') }}', 'info', {
                position: 'top-right'
            });
        </script>
    @endif
    <!-- script make theme dark mode dinamic: -->
    <script>
        $(document).ready(function() {
            var theme = localStorage.getItem('siteTheme');
            if (theme && theme === 'dark') {
                $('body').addClass('nk-body bg-lighter npc-general has-sidebar no-touch nk-nio-theme dark-mode');
            }
            $('.dark-switch').on('click', function() {
                if ($(this).hasClass('active')) {
                    localStorage.setItem('siteTheme', 'light');
                } else {
                    localStorage.setItem('siteTheme', 'dark');
                }
            });
        });
    </script>
    <!-- script make theme dark mode dinamic end: -->

    <!--ckeditor script -->
    <script>
        var editorElements = document.querySelectorAll('.description');

        editorElements.forEach(function(element) {
            ClassicEditor
                .create(element)
                .catch(error => {
                    console.error(error);
                });
        });
        var vareditorElements = document.querySelectorAll('.variation_description');

        vareditorElements.forEach(function(element) {
            ClassicEditor
                .create(element)
                .catch(error => {
                    console.error(error);
                });
        });

        function initializeEditors(container) {
            var var_editorElements = container.querySelectorAll('.variation_description');

            var_editorElements.forEach(function(element) {
                ClassicEditor
                    .create(element)
                    .then(editor => {
                        editor.setData('');
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
        }
    </script>
    @livewireScripts
</body>

</html>
