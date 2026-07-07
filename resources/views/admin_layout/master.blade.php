<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    @livewireStyles
    <?php
    $lang_id = getCurrentLanguageID();
    $favicon = \App\Models\HeaderContent::where([['lang_id', $lang_id], ['type', 'file']])
        ->where('meta_key', 'favicon_icon')
        ->pluck('meta_value', 'meta_key')
        ->first();

    ?>

    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"
        content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fav Icon  -->
    @if ($favicon)
        <link rel="shortcut icon" href="{{ asset($favicon) }}">
    @endif
    <!-- Page Title  -->
    <title>Localio || Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('admin-theme/assets/css/admin-dash.css') }}?{{ time() }}">
    <link rel="stylesheet" href="{{ asset('admin-theme/assets/css/dashlite.css?ver=3.1.2') }}?{{ time() }}">
    {{-- <link rel="stylesheet" href="{{ asset('admin-theme/coustam.css') }}"> --}}
    <link id="skin-default" rel="stylesheet" href="{{ asset('admin-theme/assets/css/theme.css?ver=3.1.2')}}?{{ time() }}">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.4.0/classic/ckeditor.js"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="{{ asset('front/admin/style.css') }}?{{ time() }}">
    <!-- slick slider cdn -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- ckeditor -->
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/42.0.0/classic/ckeditor.js"></script> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

                </div>
                <!-- .nk-sidebar-element -->
                <div class="nk-sidebar-element nk-sidebar-body">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">

                                <!-- Businesses -->
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em> -->
                                             <em class="icon ni ni-building"></em>
                                        </span>
                                        <span class="nk-menu-text">Businesses</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('business.listing.livewire') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">All Businesses </span>
                                            </a>
                                        </li>
                                        {{-- <li class="nk-menu-item">
                                            <a href="{{ route('business') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">All Businesses (old)</span>
                                            </a>
                                        </li> --}}
                                        <li class="nk-menu-item">
                                            <a href="{{ route('categories') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Business Categories</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('priceoptions') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Offer Options</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Products -->
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em>  -->
                                             <em class="icon ni ni-box"></em>
                                    </span>
                                        <span class="nk-menu-text">Products</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('products') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">All Products</span>
                                            </a>
                                        </li>

                                    </ul>
                                </li>

                                <!-- Deals -->
                                {{-- <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-article"></em></span>
                                        <span class="nk-menu-text">Deals</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('exclusive-deals.index') }}"" class="nk-menu-link">
                                                <span class="nk-menu-text">Add Deals</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li> --}}

                                <!-- Category Settings -->

                                <li class="nk-menu-item">
                                    <a href="{{ route('features') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                             <em class="icon ni ni-cpu"></em>
                                        </span>
                                        <span class="nk-menu-text">Features</span>
                                    </a>
                                </li>

                                <!-- Filter -->
                                <li class="nk-menu-item">
                                    <a href="{{ route('filters') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-filter-fill"></em></span>
                                        <span class="nk-menu-text">Filters</span>
                                    </a>
                                </li>

                                 <!-- Contact us Queries -->
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.queries.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-filter-fill"></em></span>
                                        <span class="nk-menu-text">Contact us Queries</span>
                                    </a>
                                </li>

                                {{-- user section --}}
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                        <span class="nk-menu-text">Users</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-all-user') }}" class="nk-menu-link"><span
                                                    class="nk-menu-text">All User</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-all-vendors') }}" class="nk-menu-link"><span
                                                    class="nk-menu-text">All Venodors</span></a>
                                        </li>


                                        {{-- Vendor Register List --}}
                                        <li class="nk-menu-item">
                                            <a href="{{ route('allVendorRegisterRequest') }}" class="nk-menu-link"><span
                                                class="nk-menu-text">Vendor Register</span></a>
                                        </li>

                                    </ul>
                                </li>

                                <!-- Globals  -->
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-setting-fill"></em> -->
                                             <em class="icon ni ni-globe"></em>
                                        </span>
                                        <span class="nk-menu-text">Globals</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('header-page') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Header</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('footer-page') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Footer</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('site-page') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Site Content</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('learn-modal') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Learn More Modal Content</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Pages -->
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-setting-fill"></em> -->
                                             <em class="icon ni ni-layers-fill"></em>
                                        </span>
                                        <span class="nk-menu-text">Pages</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('home-content') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Home</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('who_we_are_content') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Who We Are</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.page-expert-guide.update') }}"
                                                class="nk-menu-link">
                                                <span class="nk-menu-text">Expert Guide</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.page-contact.update') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Contact</span>
                                            </a>
                                        </li>
                                        {{-- <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/categories-page') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Categories</span>
                                            </a>
                                        </li> --}}
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.page-help-center') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Help Center</span>
                                            </a>
                                        </li>
                                        {{-- <li class="nk-menu-item">
                                            <a href="{{ route('top-product-page-content') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Top Product</span>
                                            </a>
                                        </li> --}}
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.policies') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Privacy Policy</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('terms') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Terms and Conditions</span>
                                            </a>
                                        </li>

                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.vendor-listed') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Vendor Listed</span>
                                            </a>
                                        </li>

                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.vendor-how-it-work') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Vendor How it work</span>
                                            </a>
                                        </li>



                                    </ul>
                                </li>
                                <!-- Reviews -->
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em> -->
                                             <em class="icon ni ni-chat"></em>
                                        </span>
                                        <span class="nk-menu-text">Reviews</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('reviews') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Published Reviews</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.unpublished.reviews') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Unpublished Reviews</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em> -->
                                             <em class="icon ni ni-mail"></em>
                                        </span>
                                        <span class="nk-menu-text">AI Prompts</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        {{-- <li class="nk-menu-item">
                                            <a href="{{ route('ai-configurations.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Configuration</span>
                                            </a>
                                        </li> --}}
                                        <li class="nk-menu-item">
                                            <a href="{{ route('ai-prompts.index')  }}" class="nk-menu-link">
                                                <span class="nk-menu-text">All Prompts</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('ai-prompt.business-prompt') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Business Prompts</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('ai-prompts.index', ['type' => 'seo']) }}" class="nk-menu-link">
                                                <span class="nk-menu-text">SEO Prompts</span>
                                            </a>
                                        </li>

                                        <li class="nk-menu-item">
                                            <a href="{{ route('ai-configurations.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Configuration</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>


                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em> -->
                                             <em class="icon ni ni-mail"></em>
                                        </span>
                                        <span class="nk-menu-text">Email</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('mail-templates.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Email Template</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                {{-- Expert Guide --}}
                                {{-- <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                             <em class="icon ni ni-user-check"></em>
                                        </span>
                                        <span class="nk-menu-text">Expert Guide</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-expert-guide-category') }}"
                                                class="nk-menu-link">
                                                <span class="nk-menu-text">Category</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-expert-guide-article') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Article</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li> --}}

                                <!-- FAQs Section -->
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em> -->
                                             <em class="icon ni ni-info"></em>
                                    </span>
                                        <span class="nk-menu-text">FAQs Section</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('faqs') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">FAQs</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('faqs-category') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">FAQs Category</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Support Section -->
                                {{-- <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                             <em class="icon ni ni-help"></em>
                                    </span>
                                        <span class="nk-menu-text">Support</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="#" class="nk-menu-link">
                                                <span class="nk-menu-text">Tickets</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li> --}}

                                {{-- Ad Tracking Stats --}}
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em> -->
                                             <em class="icon ni ni-invest"></em>
                                    </span>
                                        <span class="nk-menu-text">Ad Tracking Stats</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-ad-tracking-status') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Affiliate Tracking Stats</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                {{-- support ticket --}}
                                {{-- <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">

                                             <em class="icon ni ni-chat-circle-fill"></em>
                                    </span>
                                        <span class="nk-menu-text">Support</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('faqs') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Tickets</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li> --}}
                                {{-- support ticket --}}

                                {{-- vendor request --}}
                                {{-- <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <em class="icon ni ni-edit"></em>
                                    </span>
                                        <span class="nk-menu-text">Vendors Change Requests</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-vendor-change-request') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Business Change Request</span>
                                            </a>
                                        </li>

                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-vendor-product-change-request') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Product Change Request</span>
                                            </a>
                                        </li>


                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-vendor-review-feedback') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">User Review Feedback</span>
                                            </a>
                                        </li>
                                    </ul>






                                </li> --}}

                                {{-- Language/region --}}
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em> -->
                                            <em class="icon ni ni-location"></em>
                                    </span>
                                        <span class="nk-menu-text">Countries/Regions</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('site-languages') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Language</span>
                                            </a>
                                        </li>

                                        <li class="nk-menu-item">
                                            <a href="{{ route('country.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Countries List</span>
                                            </a>
                                        </li>
                                    </ul>

                                </li>
                                {{-- Language/region --}}




                                {{-- site content --}}
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon">
                                            <!-- <em class="icon ni ni-article"></em> -->
                                            <em class="icon ni ni-text2"></em>
                                    </span>
                                        <span class="nk-menu-text">Site Content</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin-all-static-content') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">Content</span>
                                            </a>
                                        </li>
                                    </ul>

                                </li>



                                <!-- Settings -->
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-setting-fill"></em></span>
                                        <span class="nk-menu-text">Settings</span>
                                    </a>
                                    <ul class="nk-menu-sub">

                                        <li class="nk-menu-item">
                                            <a href="{{ route('dbrefresh.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">DB-Refresh</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item has-sub">
                                            <a href="{{ route('admin-Default-edit') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">
                                                    Configuration</span>
                                            </a>

                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
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
                                    {{-- <li>
                                         <div class="dropdown ">
                                            <a href="#"
                                                class="dropdown-toggle dropdown-indicator btn btn-outline-light btn-white"
                                                data-bs-toggle="dropdown">

                                                {{ session('lang_name') }}

                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    @php
                                                        $languages = \App\Models\Language::where('status', 1)->get();
                                                    @endphp
                                                    @foreach ($languages as $language)
                                                        <li
                                                            class="{{ session('lang_code') == $language->lang_code ? 'active' : '' }}">
                                                            <a
                                                                href="{{ route('set-admin-languages', ['lang_id' => $language->id]) }}">
                                                                <span>
                                                                    {{ $language->name }}
                                                                </span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li> --}}
                                    @php
                                        // use Illuminate\Support\Facades\Cookie;

                                        // dd([
                                        //     'session' => session()->all(),
                                        //     'cookies' => [
                                        //         'lang_code' => Cookie::get('lang_code'),
                                        //         'lang_id'   => Cookie::get('lang_id'),
                                        //     ],
                                        // ]);
                                        // $languages = \App\Models\Language::where('status', 1)->get();
                                        // dd($languages-lan);
                                        // $selectedLanguage=\App\Models\Language::where('lang_code',session('lang_code'))->first();
                                        // dd($selectedLanguage->name);
                                    @endphp


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
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1"
                                            style="height: fit-content">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span>
                                                            {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}</span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ Auth::user()->first_name }}
                                                            {{ Auth::user()->last_name }}</span>
                                                        <span class="sub-text">{{ Auth::user()->email }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <!-- <li><a href="html/user-profile-regular.html"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li> -->
                                                    <li><a href="{{ url('admin-dashboard/setting') ?? '' }}"><em
                                                                class="icon ni ni-setting-alt"></em><span>Account
                                                                Setting</span></a></li>
                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="{{ url('/logout') }}"><em
                                                                class="icon ni ni-signout"></em><span>Sign
                                                                out</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>

                                    <!-- <li class="dropdown notification-dropdown me-n1">
                                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                                            <div class="icon-status icon-status-info"><em class="icon ni ni-bell"></em></div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end dropdown-menu-s1">
                                            <div class="dropdown-head">
                                                <span class="sub-title nk-dropdown-title">Notifications</span>
                                                <a href="#">Mark All as Read</a>
                                            </div>
                                            <div class="dropdown-body">
                                                <div class="nk-notification">
                                                    <div class="nk-notification-item dropdown-inner">
                                                        <div class="nk-notification-icon">
                                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                                                        </div>
                                                        <div class="nk-notification-content">
                                                            <div class="nk-notification-text">You have requested to <span>Widthdrawl</span></div>
                                                            <div class="nk-notification-time">2 hrs ago</div>
                                                        </div>
                                                    </div>
                                                    <div class="nk-notification-item dropdown-inner">
                                                        <div class="nk-notification-icon">
                                                            <em class="icon icon-circle bg-success-dim ni ni-curve-down-left"></em>
                                                        </div>
                                                        <div class="nk-notification-content">
                                                            <div class="nk-notification-text">Your <span>Deposit Order</span> is placed</div>
                                                            <div class="nk-notification-time">2 hrs ago</div>
                                                        </div>
                                                    </div>
                                                    <div class="nk-notification-item dropdown-inner">
                                                        <div class="nk-notification-icon">
                                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                                                        </div>
                                                        <div class="nk-notification-content">
                                                            <div class="nk-notification-text">You have requested to <span>Widthdrawl</span></div>
                                                            <div class="nk-notification-time">2 hrs ago</div>
                                                        </div>
                                                    </div>
                                                    <div class="nk-notification-item dropdown-inner">
                                                        <div class="nk-notification-icon">
                                                            <em class="icon icon-circle bg-success-dim ni ni-curve-down-left"></em>
                                                        </div>
                                                        <div class="nk-notification-content">
                                                            <div class="nk-notification-text">Your <span>Deposit Order</span> is placed</div>
                                                            <div class="nk-notification-time">2 hrs ago</div>
                                                        </div>
                                                    </div>
                                                    <div class="nk-notification-item dropdown-inner">
                                                        <div class="nk-notification-icon">
                                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                                                        </div>
                                                        <div class="nk-notification-content">
                                                            <div class="nk-notification-text">You have requested to <span>Widthdrawl</span></div>
                                                            <div class="nk-notification-time">2 hrs ago</div>
                                                        </div>
                                                    </div>
                                                    <div class="nk-notification-item dropdown-inner">
                                                        <div class="nk-notification-icon">
                                                            <em class="icon icon-circle bg-success-dim ni ni-curve-down-left"></em>
                                                        </div>
                                                        <div class="nk-notification-content">
                                                            <div class="nk-notification-text">Your <span>Deposit Order</span> is placed</div>
                                                            <div class="nk-notification-time">2 hrs ago</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-foot center">
                                                <a href="#">View All</a>
                                            </div>
                                        </div>
                                    </li> -->
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
    {{-- select 2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
        integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- remove confermation pop up -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>



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

    <script>
        window.addEventListener('show-toast', event => {
            const type = event.detail.type || 'info';
            const message = event.detail.message || 'Something happened';
            toastr.clear();
            NioApp.Toast(message, type, { position: 'top-right' });
        });
    </script>


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

                }
            }
        });
    </script>

    <script>
        // Global function to fix Select2 in modals and other containers
        window.initializeSelect2 = function(selector) {
            $(selector).select2({
                width: '100%',
                dropdownAutoWidth: true,
                dropdownParent: $(selector).parent()
            });
        };
    </script>
    @livewireScripts


    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script>
        document.addEventListener('livewire:load', function() {
            $('.selectpicker').selectpicker(); // Initialize the selectpicker
        });

        Livewire.hook('message.processed', (message, component) => {
            $('.selectpicker').selectpicker('refresh'); // Refresh the selectpicker after Livewire updates
        });
    </script>

    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

    <script>
        // Initialize toastr options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000"
        };

        // Set current category ID for JS access
        $(function() {
            const urlPath = window.location.pathname;
            const categoryMatch = urlPath.match(/\/admin-dashboard\/filters\/(\d+)/);
            if (categoryMatch) {
                $('body').attr('data-category-id', categoryMatch[1]);
            }
        });
    </script>

    <!-- Push filter scripts into the stack -->
    @push('scripts')
        <script src="{{ asset('js/filter-system.js') }}"></script>
    @endpush
</body>

</html>
