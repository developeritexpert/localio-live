<!DOCTYPE html>
<html lang="en">

<head>
    <?php

    use App\Models\HomeContent; // Import your model
    $languages = App\Models\Language::all();
    $lang_id = getCurrentLanguageID();

    $favicon = \App\Models\HeaderContent::where([['lang_id', $lang_id], ['type', 'file']])
        ->where('meta_key', 'favicon_icon')
        ->pluck('meta_value', 'meta_key')
        ->first();

    // Fetch meta title and description from database
    $metaTitle = HomeContent::where('meta_key', 'meta_home_title')->value('meta_value') ?? 'Default Title';
    $metaDescription = HomeContent::where('meta_key', 'Meta_home_description')->value('meta_value') ?? 'Default Description';

    ?>
    <title>@yield('meta_title', 'How to find the Best Product')</title>
    <meta name="description" content="@yield('meta_description', '')">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">


    {{-- user Id session --}}
    <script>
        window.userId = @json(session('user_id'));
    </script>


    <!-- Font Awesome (latest version) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap 5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Slick Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css" integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- noUiSlider -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@14.6.3/distribute/nouislider.min.css">
    <script src="https://cdn.jsdelivr.net/npm/nouislider@14.6.3/distribute/nouislider.min.js"></script>

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!--  Preview Reviews Slider -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <title><?= htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8') ?></title>
    @if ($favicon)
    <link rel="shortcut icon" href="{{ asset($favicon) }}">
    @endif
    @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}?{{ time() }}">
    <link rel="stylesheet" href="{{ asset('front/css/responsive.css') }}?{{ time() }}">
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @media (max-width: 767px) {

            .footer-dropdown .footer-title{
                display:flex;
                justify-content:space-between;
                align-items:center;
                cursor:pointer;
                margin-bottom:0;
            }

            .footer-dropdown .foot-col-list{
                max-height:0;
                overflow:hidden;
                transition:max-height .3s ease;
            }

            .footer-dropdown.active .foot-col-list{
                max-height:200px;
            }

            .footer-arrow{
                display:flex;
                align-items:center;
            }

            .footer-arrow i{
                transition:transform .3s ease;
                font-size:14px;
            }

            .footer-dropdown.active .footer-arrow i{
                transform:rotate(180deg);
            }
        }

        .mobile-sidebar{
                position:fixed;
                top:0;
                left:-320px;
                width:320px;
                height:100vh;
                background:#002347;
                z-index:99999;
                transition:.3s;
                overflow-y:auto;
                box-shadow: 4px 0 15px rgba(0, 0, 0, 0.5);
            }

            .mobile-sidebar.active{
                left:0;
            }

            .mobile-sidebar-overlay{
                position:fixed;
                inset:0;
                background:rgba(0,0,0,.5);
                opacity:0;
                visibility:hidden;
                transition:.3s;
                z-index:99998;
            }

            .mobile-sidebar-overlay.active{
                opacity:1;
                visibility:visible;
            }

            .mobile-sidebar-header{
                display:flex;
                justify-content:space-between;
                align-items:center;
                padding:20px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .mobile-sidebar-close{
                border:none;
                background:none;
                color:#fff !important;
                font-size:24px;
                cursor:pointer;
                transition:color .2s ease;
            }

            .mobile-sidebar-close:hover{
                color:#F9633B !important;
            }

            @media(min-width:992px){
                .mobile-sidebar,
                .mobile-sidebar-overlay,
                .mobile-sidebar-btn{
                    display:none;
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
                padding-left: 20px;
                margin: 0 !important;
                font-size: 18px !important;
                font-weight: 600 !important;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }

            .mobile-sidebar-body .section_divider {
                margin: 8px auto 15px !important;
                width: 90% !important;
                border-color: rgba(255, 255, 255, 0.1) !important;
            }

            .mobile-sidebar-body .flat_list {
                padding: 0 20px;
                margin: 0;
                list-style: none;
            }

            .mobile-sidebar-body .flat_list li {
                padding: 10px 0 !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            .mobile-sidebar-body .flat_list li:last-child {
                border-bottom: none;
            }

            .mobile-sidebar-body .flat_list li a {
                color: rgba(255, 255, 255, 0.8) !important;
                text-decoration: none;
                font-size: 15px;
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
                /* Restore top description padding & negative-margin bottom cards section */
                .popular-accordion-wrp .accordion-body,
                div#accordionExample .accordion-item .accordion-collapse .accordion-body {
                    padding: 16px !important;
                    padding-top: 0px !important;
                }
                .accor-bdy-btm {
                    margin-left: -16px !important;
                    margin-right: -16px !important;
                    padding-top: 24px !important;
                }
                .accor-bdy-btm .accor-bdy-row {
                    margin: 0 !important;
                }
                .accor-bdy-btm .accor-bdy-row > div {
                    padding: 0 16px !important;
                    margin-bottom: 24px !important;
                }
                .accor-bdy-btm .accor-bdy-row > div:last-child {
                    margin-bottom: 0 !important;
                }

                /* Polished Mobile Business Card Refactoring (Yelp/Airbnb Style) */
                .top-rate-innr {
                    display: flex !important;
                    flex-direction: column !important;
                    padding: 20px !important;
                    border-radius: 16px !important;
                    background-color: #fff !important;
                    box-shadow: 0 8px 30px rgba(0, 35, 71, 0.06) !important;
                    border: 1px solid rgba(6, 73, 139, 0.08) !important;
                    position: relative !important;
                    width: 100% !important;
                    height: 100% !important;
                }
                .inn_sl_hed {
                    display: flex !important;
                    align-items: flex-start !important;
                    justify-content: space-between !important;
                    gap: 12px !important;
                    position: relative !important;
                    width: 100% !important;
                    margin: 0 !important;
                }
                /* Best Value offset adjust */
                .inn_sl_hed.mst_hdn.mt-4 {
                    margin-top: 28px !important;
                }
                .sli_img {
                    flex: 0 0 54px !important;
                    width: 54px !important;
                    height: 54px !important;
                    border-radius: 50% !important;
                    overflow: hidden !important;
                    background-color: #f8f9fa !important;
                    border: 1px solid rgba(0, 0, 0, 0.05) !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    align-self: center !important; /* Vertically center logo with title block */
                }
                .sli_img img,
                .sli_img .slider_img {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                    border-radius: 50% !important;
                }
                .sl_h {
                    flex: 1 1 auto !important;
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 4px !important;
                    padding-right: 8px !important;
                }
                .sl_h h6 {
                    font-size: 16px !important;
                    font-weight: 700 !important;
                    color: #002347 !important;
                    margin: 0 !important;
                    line-height: 1.3 !important;
                }
                .rate_box {
                    display: flex !important;
                    align-items: center !important;
                    flex-wrap: wrap !important;
                    gap: 6px !important;
                    font-size: 12px !important;
                    color: #555 !important;
                }
                .rate_box .stars {
                    display: flex !important;
                    align-items: center !important;
                    gap: 1px !important;
                }
                .rate_box .stars i {
                    font-size: 11px !important;
                }
                .rate_box span {
                    font-weight: 600 !important;
                }
                .wishlist {
                    flex: 0 0 44px !important;
                    width: 44px !important;
                    height: 44px !important;
                    min-width: 44px !important;
                    min-height: 44px !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    background-color: rgba(255, 255, 255, 0.9) !important;
                    border-radius: 50% !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
                    cursor: pointer !important;
                    align-self: flex-start !important; /* Pin heart icon to top right */
                    margin-top: 4px !important; /* Visual micro-alignment with first line of title */
                    position: static !important; /* Use flex flow instead of absolute positioning */
                }
                .wishlist i {
                    font-size: 18px !important;
                    color: #555 !important;
                }
                .tp-btm {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    justify-content: flex-start !important;
                    gap: 8px !important;
                    margin: 6px 0 0 0 !important; /* tight 6px top spacing directly below title */
                    padding: 0 !important;
                }
                .content_para {
                    font-size: 13px !important;
                    line-height: 1.5 !important;
                    color: #4a5568 !important;
                    margin: 0 !important;
                    min-height: auto !important;
                }
                /* Polished CTA button centering & full-width target */
                .top-rate-innr .text-center {
                    margin-top: auto !important;
                }
                .top-rate-innr .text-center .cta_orange {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    width: 100% !important;
                    height: 42px !important;
                    padding: 0 !important;
                    font-size: 14px !important;
                    font-weight: 700 !important;
                    border-radius: 21px !important;
                    background-color: #F9633B !important;
                    color: white !important;
                    box-shadow: 0 4px 12px rgba(249, 99, 59, 0.2) !important;
                    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
                    border: none !important;
                }
                .top-rate-innr .text-center .cta_orange:active {
                    transform: scale(0.98) !important;
                }

                /* Wishlist container overlap and wrapping fix on all mobile cards */
                .sl_main > a {
                    display: flex !important;
                    align-items: flex-start !important;
                    justify-content: space-between !important;
                    gap: 12px !important;
                    width: 100% !important;
                }
                .sl_main h6.head,
                .sl_main h6 {
                    flex: 1 1 auto !important;
                    padding-right: 0 !important; /* Clear old absolute padding offset */
                    font-size: 16px !important;
                    line-height: 1.3 !important;
                    margin: 0 !important;
                }
                .sl_main div[wire\:key*="wishlist-container"] {
                    position: static !important;
                    flex: 0 0 44px !important;
                    width: 44px !important;
                    height: 44px !important;
                    margin: 0 !important;
                }
                .sl_main div[wire\:key*="wishlist-container"] .wishlist {
                    position: static !important;
                    width: 44px !important;
                    height: 44px !important;
                    min-width: 44px !important;
                    min-height: 44px !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    background-color: rgba(255, 255, 255, 0.9) !important;
                    border-radius: 50% !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
                    align-self: flex-start !important;
                    margin: 0 !important;
                }

                /* Show only 1 star rating in most popular cards on mobile (Disabled)
                .popular-accordion-wrp .tab_star_li i:nth-child(n+2) {
                    display: none !important;
                }
                .popular-accordion-wrp .tab_star_li i:first-child {
                    font-weight: 900 !important;
                    color: #fbbf24 !important;
                }
                */

                /* Unified CSS Grid Card Header Layout Refactor on Mobile */
                /* High-reliability Flexbox Card Header Column Layout on Mobile */
                .top-rate-innr .inn_sl_hed,
                .reviews_block .inn_sl_hed,
                .automotive-card .inn_sl_hed {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: flex-start !important;
                    justify-content: flex-start !important;
                    gap: 16px !important;
                    width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    position: relative !important;
                }
                .reviews_block .inn_sl_hed {
                    margin: 0 0 24px 0 !important; /* Specific bottom margin for reviews */
                }
                .top-rate-innr .sli_img,
                .reviews_block .inn_sl_hed .sli_img,
                .automotive-card .inn_sl_hed > a {
                    flex: 0 0 48px !important;
                    width: 48px !important;
                    height: 48px !important;
                    min-width: 48px !important;
                    min-height: 48px !important;
                    max-width: 48px !important;
                    max-height: 48px !important;
                    margin: 0 !important;
                    display: block !important;
                }
                .top-rate-innr .sli_img img,
                .reviews_block .inn_sl_hed .sli_img img,
                .reviews_block .inn_sl_hed .sli_img .header_img,
                .automotive-card .sli_img.choice_img img,
                .automotive-card .sli_img.choice_img .slider_img {
                    width: 48px !important;
                    height: 48px !important;
                    border-radius: 50% !important;
                    object-fit: cover !important;
                    margin: 0 !important;
                }
                .top-rate-innr .sl_h,
                .reviews_block .sl_h,
                .automotive-card .sl_h {
                    flex: 1 1 auto !important;
                    display: flex !important;
                    flex-direction: column !important;
                    justify-content: flex-start !important;
                    align-items: flex-start !important;
                    gap: 6px !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    min-width: 0 !important;
                }
                .top-rate-innr .inn_h,
                .top-rate-innr .inn_h a,
                .reviews_block .sl_h .inn_h,
                .reviews_block .sl_h .inn_h a,
                .automotive-card .sl_h .inn_h,
                .automotive-card .sl_h .inn_h a {
                    display: block !important;
                    width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
             .most-populr-sec.light.p_120 .wishlist i.far.fa-heart.text-gray-600.hover\:text-red-600 {
                margin-left: -20px;
            }
            .slick-list.draggable i.far.fa-heart.text-gray-600.hover\:text-red-600,
                .slick-list.draggable i.fas.fa-heart.text-red-600.hover\:text-red-700 {
                    margin-left: -20px;
                }
                    
                .top-rate-innr .sl_main,
                .reviews_block .sl_main,
                .automotive-card .sl_main {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: flex-start !important;
                    justify-content: space-between !important;
                    gap: 16px !important;
                    width: 100% !important;
                    margin: 0 !important;
                    padding: 0 48px 0 0 !important; /* Space on the right for absolute wishlist badge */
                    position: relative !important;
                }
                .top-rate-innr .sl_main h6.head,
                .reviews_block .sl_main h6.head,
                .automotive-card .sl_main h6.head {
                    flex: 1 1 auto !important;
                    font-size: 16px !important;
                    line-height: 1.25 !important;
                    font-weight: 700 !important;
                    color: #002347 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    word-break: break-word !important;
                    align-self: flex-start !important;
                }
                .top-rate-innr .wishlist,
                .reviews_block .wishlist,
                .automotive-card .wishlist,
                .automotive-card div[wire\:key*="wishlist-container"] {
                    flex: 0 0 44px !important;
                    width: 44px !important;
                    height: 44px !important;
                    min-width: 44px !important;
                    min-height: 44px !important;
                    margin: 0 !important;
                    position: absolute !important;
                    right: 0 !important;
                    top: 0 !important;
                    background-color: rgba(255, 255, 255, 0.9) !important;
                    border-radius: 50% !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
                    align-self: flex-start !important;
                }
                .top-rate-innr .tp-btm,
                .reviews_block .tp-btm,
                .automotive-card .tp-btm,
                .automotive-card .auto-choice-hd .tp-btm,
                .automotive-card .tp-btm.flex-col-mob,
                .automotive-card .auto-choice-hd .tp-btm.flex-col-mob {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    justify-content: flex-start !important;
                    gap: 8px !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .reviews_block .tp-btm .inn_ul,
                .automotive-card .tp-btm .inn_ul {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    gap: 2px !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .reviews_block .tp-btm .inn_ul i.fa-angle-down {
                    display: none !important;
                }
                .reviews_block .tp-btm .rate_box,
                .automotive-card .tp-btm .rate_box {
                    font-size: 13px !important;
                    color: #718096 !important;
                    font-weight: 500 !important;
                    white-space: nowrap !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                /* Show only 1 star rating in latest reviews cards on mobile (Disabled)
                .reviews_block .tab_star_li span:nth-child(n+2) {
                    display: none !important;
                }
                .reviews_block .tab_star_li span:first-child::before {
                    font-family: "Font Awesome 5 Free" !important;
                    font-weight: 900 !important;
                    content: "\f005" !important;
                    color: #fe9d27 !important;
                    display: inline-block !important;
                    font-size: 12px;
                }
                */
                /* Center heart icon inside the circular badge */
                [class*="wishlist"],
                [class*="wishlist"] div,
                [class*="wishlist"] a,
                [class*="wishlist"] i,
                [class*="wishlist"] > div a {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                [class*="wishlist"] a,
                [class*="wishlist"] div a,
                [class*="wishlist"] > div a {
                    position: static !important;
                    transform: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    top: auto !important;
                    right: auto !important;
                    left: auto !important;
                    bottom: auto !important;
                }
                /* Reset nested wishlist styles to prevent double circle/shadow rendering */
                .wishlist .wishlist {
                    background: transparent !important;
                    background-color: transparent !important;
                    box-shadow: none !important;
                    border: none !important;
                    width: 100% !important;
                    height: 100% !important;
                    min-width: 0 !important;
                    min-height: 0 !important;
                    position: static !important;
                }

                /* Automotive/Product Cards layout refactor on mobile */
                .automotive-card .inn_sl_hed {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: flex-start !important;
                    justify-content: flex-start !important;
                    gap: 16px !important;
                    width: 100% !important;
                    margin: 0 0 16px 0 !important;
                    padding: 0 !important;
                    position: relative !important;
                }
                .automotive-card .inn_sl_hed > a {
                    flex: 0 0 48px !important;
                    width: 48px !important;
                    height: 48px !important;
                    min-width: 48px !important;
                    min-height: 48px !important;
                    max-width: 48px !important;
                    max-height: 48px !important;
                    margin: 0 !important;
                    display: block !important;
                }
                .automotive-card .sli_img.choice_img {
                    width: 100% !important;
                    height: 100% !important;
                    margin: 0 !important;
                }
                .automotive-card .sli_img.choice_img img,
                .automotive-card .sli_img.choice_img .slider_img {
                    width: 48px !important;
                    height: 48px !important;
                    border-radius: 50% !important;
                    object-fit: cover !important;
                    margin: 0 !important;
                }
                .automotive-card .sl_h {
                    flex: 1 1 auto !important;
                    display: flex !important;
                    flex-direction: column !important;
                    justify-content: flex-start !important;
                    gap: 6px !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    min-width: 0 !important;
                }
                .automotive-card .sl_h .inn_h {
                    width: 100% !important;
                    display: block !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .automotive-card .sl_main {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: flex-start !important;
                    justify-content: space-between !important;
                    gap: 16px !important;
                    width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .automotive-card .sl_main h6.head {
                    flex: 1 1 auto !important;
                    font-size: 16px !important;
                    line-height: 1.25 !important;
                    font-weight: 700 !important;
                    color: #002347 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    word-break: break-word !important;
                }
                .automotive-card .sl_main div[wire\:key*="wishlist-container"] {
                    position: static !important;
                    flex: 0 0 44px !important;
                    width: 44px !important;
                    height: 44px !important;
                    min-width: 44px !important;
                    min-height: 44px !important;
                    margin: 0 !important;
                }
                .automotive-card .sl_main .wishlist {
                    flex: 0 0 44px !important;
                    width: 44px !important;
                    height: 44px !important;
                    min-width: 44px !important;
                    min-height: 44px !important;
                    position: static !important;
                    background-color: rgba(255, 255, 255, 0.9) !important;
                    border-radius: 50% !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
                    align-self: flex-start !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .automotive-card .tp-btm,
                .automotive-card .auto-choice-hd .tp-btm,
                .automotive-card .tp-btm.flex-col-mob,
                .automotive-card .auto-choice-hd .tp-btm.flex-col-mob {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    justify-content: flex-start !important;
                    gap: 8px !important;
                    margin: 4px 0 0 0 !important; /* tight 4px spacing directly below title */
                    padding: 0 !important;
                }
                .automotive-card .tp-btm .inn_ul {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    gap: 2px !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .automotive-card .tp-btm .rate_box {
                    font-size: 13px !important;
                    color: #718096 !important;
                    font-weight: 500 !important;
                    white-space: nowrap !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
            }

            @media (max-width: 360px) {
                .top-rate-innr .tp-btm,
                .reviews_block .tp-btm,
                .automotive-card .tp-btm,
                .automotive-card .auto-choice-hd .tp-btm,
                .automotive-card .tp-btm.flex-col-mob,
                .automotive-card .auto-choice-hd .tp-btm.flex-col-mob {
                    flex-direction: column !important;
                    align-items: flex-start !important;
                    gap: 4px !important;
                }
            }

                /* G2 Style 'Most popular categories' Section Redesign */
                .most-popular-g2-layout {
                    margin-top: 20px;
                }
                .popular-categories-sidebar {
                    background: #fff;
                    padding: 24px;
                    border-radius: 16px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                    border: 1px solid rgba(0, 0, 0, 0.05);
                }
                .popular-categories-title {
                    font-size: 24px !important;
                    font-weight: 800 !important;
                    color: #002347 !important;
                    margin-top: 0 !important;
                    margin-bottom: 24px !important;
                    line-height: 1.3 !important;
                }
                .see-all-category-container {
                    height: 31px !important; /* Matches line-height height of h2 popular-categories-title */
                    margin-top: 0 !important;
                    margin-bottom: 24px !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: flex-end !important;
                }
                .popular-categories-nav {
                    gap: 8px !important;
                }
                .popular-category-tab-btn {
                    background: transparent !important;
                    border: none !important;
                    border-left: 3px solid transparent !important;
                    border-radius: 0 !important;
                    color: #4a5568 !important;
                    font-size: 16px !important;
                    font-weight: 600 !important;
                    padding: 12px 16px !important;
                    display: flex !important;
                    align-items: center !important;
                    gap: 12px !important;
                    width: 100% !important;
                    transition: all 0.2s ease-in-out !important;
                    text-align: left !important;
                }
                .popular-category-tab-btn:hover {
                    color: #002347 !important;
                    background-color: #f7fafc !important;
                }
                .popular-category-tab-btn.active {
                    color: #F9633B !important;
                    background-color: #fff8f6 !important;
                    border-left-color: #F9633B !important;
                    font-weight: 700 !important;
                }
                .category-icon-wrapper {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    width: 32px !important;
                    height: 32px !important;
                    border-radius: 6px !important;
                    background-color: #f7fafc !important;
                    padding: 4px !important;
                    flex-shrink: 0 !important;
                }
                .popular-category-tab-btn.active .category-icon-wrapper {
                    background-color: #ffebe6 !important;
                }
                .category-btn-icon {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: contain !important;
                }
                .category-btn-text {
                    flex-grow: 1 !important;
                }
                .see-all-category-link {
                    font-size: 16px !important;
                    font-weight: 700 !important;
                    color: #F9633B !important;
                    text-decoration: none !important;
                    transition: color 0.2s !important;
                }
                .see-all-category-link:hover {
                    color: #d84b25 !important;
                    text-decoration: underline !important;
                }
                /* Mobile tweaks */
                @media (max-width: 991px) {
                    .popular-categories-sidebar {
                        padding: 16px;
                    }
                    .popular-categories-nav {
                        flex-direction: row !important;
                        overflow-x: auto !important;
                        flex-wrap: nowrap !important;
                        padding-bottom: 8px !important;
                    }
                    .popular-category-tab-btn {
                        width: auto !important;
                        white-space: nowrap !important;
                        border-left: none !important;
                        border-bottom: 3px solid transparent !important;
                        padding: 8px 16px !important;
                    }
                    .popular-category-tab-btn.active {
                        border-left-color: transparent !important;
                        border-bottom-color: #F9633B !important;
                    }
                    .see-all-category-container {
                        height: auto !important;
                        justify-content: flex-start !important;
                        margin-bottom: 16px !important;
                    }
                }

    </style>

</head>

<body class="@yield('body_class')">
    <?php
    $lang = getCurrentLocale();

    $headerLogo = \App\Models\HeaderContent::where([['lang_id', 1], ['meta_key', 'header_logo']])->first();
    $headerContent = \App\Models\HeaderContent::where([['lang_id', $lang_id], ['type', 'text']])->pluck('meta_value', 'meta_key');
    if ($headerContent->isEmpty()) {
        $headerContent = \App\Models\HeaderContent::where([['lang_id', 1], ['type', 'text']])->pluck('meta_value', 'meta_key');
    }
    $footerLogo = \App\Models\FooterContent::where([['lang_id', $lang_id], ['meta_key', 'footer_logo']])->first();
    $icons = \App\Models\FooterContent::where('lang_id', 1)
        ->whereIn('meta_key', ['facebook_icon', 'instagram_icon', 'twitter_icon' , 'pinterest_icon'])
        ->get();

    $facebookIcon = $icons->where('meta_key', 'facebook_icon')->first();
    $instagramIcon = $icons->where('meta_key', 'instagram_icon')->first();
    $twitterIcon = $icons->where('meta_key', 'twitter_icon')->first();
    $PinterestIcon = $icons->where('meta_key', 'pinterest_icon')->first();

    $footerContents = \App\Models\FooterContent::where('lang_id', $lang_id)->where('type', 'text')->pluck('meta_value', 'meta_key');
    $footerMediaUrls = \App\Models\FooterContent::where('type', 'url')->where('lang_id', 1)->pluck('meta_value', 'meta_key');
    // {{dd($footerLogo);}}
    if ($footerContents->isEmpty()) {
        $footerContents = \App\Models\FooterContent::where('lang_id', 1)->where('type', 'text')->pluck('meta_value', 'meta_key');
    }

    $currentCountryId = getCurrentCountry();
    $countryModel = \App\Models\Country::find($currentCountryId);
    $showCommissionsBanner = false;
    if ($countryModel && $countryModel->show_disclaimer == 1) {
        $showCommissionsBanner = !in_array(Route::currentRouteName(), [
            'who-we-are',
            'privacy-policy',
            'terms-condition',
            'contact',
            'FaqsShow',
            'login',
            'register'
        ]);
    }
    ?>
    @if($showCommissionsBanner)
        <div class="transparency-banner" style="position: relative; top: 0; left: 0; width: 100%; height: 34px; background-color: #003f7d; z-index: 10; text-align: center;  box-sizing: border-box;">
           <div class="inr-trans">
        <div style="display: inline-block; font-size: 11.5px; color: #ffffff; line-height: 1; font-family: sans-serif; max-height: 22px; overflow: hidden; vertical-align: middle;">
                {{ static_text('localio_commissions_message') }}
                <a href="#" onclick="event.preventDefault(); openModal()" style="color: #ffffff; font-weight: 600; text-decoration: underline; margin-left: 4px; transition: color 0.2s;" onmouseover="this.style.color='#f9633b'" onmouseout="this.style.color='#ffffff'">Learn more.</a>
            </div>
</div>
        </div>
        <style>
            header .sec_head {
                top: 34px !important;
            }
            /* .product-page-body header .sec_head {
                margin-top: 34px !important;
            } */
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const banner = document.querySelector('.transparency-banner');
                const header = document.querySelector('header .sec_head');
                if (banner && header) {
                    const updateHeaderPosition = () => {
                        let scroll = window.scrollY;
                        if (scroll > 34) {
                            banner.style.transform = 'translateY(-34px)';
                            if (!document.body.classList.contains('product-page-body')) {
                                header.style.setProperty('top', '0px', 'important');
                            }
                        } else {
                            banner.style.transform = 'translateY(-' + scroll + 'px)';
                            if (!document.body.classList.contains('product-page-body')) {
                                header.style.setProperty('top', (34 - scroll) + 'px', 'important');
                            }
                        }
                    };
                    window.addEventListener('scroll', updateHeaderPosition);
                    updateHeaderPosition();
                }
            });
        </script>
    @endif
    <header>
        <section class="sec_head">
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
                            <a href="{{ route('login', ['locale' => session('lang_code', 'en-us')]) }}"
                                class="cta cta_trans">{{ $headerContent['login_btn_lable'] ?? 'Login' }}</a>
                            <a href="{{ route('register', ['locale' => session('lang_code', 'en-us')]) }}"
                                class="cta cta_orange wht-t-org-btn">{{ $headerContent['sign_up_btn_lable'] ?? 'Sign Up' }}</a>
                            @else
                            <x-user-profile />
                            <!-- <a href="{{ url('/logout') }}"
                                        class="cta cta_orange">{{ $headerContent['sign_out_btn_lable'] ?? 'Sign out' }}</a> -->
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="top_header dark">
                <div class="container-fluid">
                    <nav  id="mobile_res" class="navbar navbar-expand-lg">
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
                                            <a href="javascript:void(0);" id="open-categories-sidebar"><i class="fa-solid fa-bars me-2"></i>{{ $headerContent['Categories'] ?? 'All' }}</a>
                                        </li>
                                        <li class=" menu-item cat_menu_item">
                                            <a
                                                href="{{ route('top-rated-product', ['locale' => session('lang_code', 'en-us')]) }}">{{ $headerContent['top_rated_product'] ?? 'Top Rated Products' }}</a>
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
                                    

                                        {{-- @foreach($categories as $category)
                                                <li class="menu-item cat_menu_item dropdown dropdown-6 mobile-drop">
                                                    <a href="{{ route('category.detail', [
                                                        'locale' => app()->getLocale(),
                                                        'slug' => $category->translation->slug
                                                    ]) }}">
                                                        {{ $category->translation->name }}
                                                    </a>
                                                </li>
                                        @endforeach --}}
                                         {{-- @foreach($categories as $category)
                                            <li class="menu-item">
                                                <a href="{{ route('category.show', $category->slug) }}">
                                                    {{ $category->name }}
                                                </a>
                                            </li>
                                        @endforeach --}}
                                        {{-- <li class=" menu-item cat_menu_item dropdown dropdown-6 mobile-drop">
                                            <a href="#">{{ $headerContent['exclusive'] ?? 'Exclusive Deals' }}</a>
                                            <span class="dropdown_toggle"><i class="fa-solid fa-chevron-down"></i></span>
                                            <ul
                                                class="dropdown_menu dropdown_menu--animated dropdown_menu-6 mob-drp-contnt">

                                                <div class="dropdown-ul-inner">
                                                    <div class="oter_dropul">
                                                        <div class="row">
                                                            <livewire:deals-search />
                                                        </div>
                                                    </div>
                                                </div>
                                            </ul>
                                        </li> --}}
                                    </ul>
                                </div>
                                {{-- <div class="right_menu">
                                    <ul>
                                        <li style="cursor: pointer">
                                            <a
                                                href="{{ route('expert-guide', ['locale' => session('lang_code', 'en-us')]) }}">{{ $headerContent['expert_guide'] ?? 'Expert Guides' }}</a>
                                        </li>
                                        <li style="cursor: pointer">
                                            <a
                                                href="{{ route('help-center', ['locale' => session('lang_code', 'en-us')]) }}">{{ $headerContent['help_center'] ?? 'Help Center' }}</a>
                                        </li>
                                    </ul>
                                </div> --}}


                                <div class="close_btn_mobile">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.9819 0.490874L9.99988 7.50855L16.9815 0.527235C17.1357 0.363097 17.3215 0.231792 17.5277 0.141194C17.7339 0.0505961 17.9562 0.00257135 18.1815 0C18.6637 0 19.1261 0.191544 19.4671 0.532495C19.808 0.873445 19.9996 1.33587 19.9996 1.81805C20.0038 2.04095 19.9625 2.26236 19.8781 2.4687C19.7936 2.67504 19.6679 2.86195 19.5087 3.01796L12.4362 9.99928L19.5087 17.0715C19.8083 17.3646 19.9841 17.7616 19.9996 18.1805C19.9996 18.6627 19.808 19.1251 19.4671 19.4661C19.1261 19.807 18.6637 19.9986 18.1815 19.9986C17.9497 20.0082 17.7186 19.9695 17.5026 19.885C17.2866 19.8005 17.0906 19.672 16.9269 19.5077L9.99988 12.49L3.00009 19.4895C2.84646 19.6482 2.66294 19.7748 2.4601 19.8622C2.25727 19.9496 2.03914 19.9959 1.8183 19.9986C1.3361 19.9986 0.873656 19.807 0.532691 19.4661C0.191726 19.1251 0.000173751 18.6627 0.000173751 18.1805C-0.00406521 17.9576 0.0372916 17.7362 0.121706 17.5299C0.206121 17.3235 0.331813 17.1366 0.491068 16.9806L7.56359 9.99928L0.491068 2.92706C0.191413 2.63392 0.0156999 2.23695 0.000173751 1.81805C0.000173751 1.33587 0.191726 0.873445 0.532691 0.532495C0.873656 0.191544 1.3361 0 1.8183 0C2.25465 0.00545415 2.67282 0.181805 2.9819 0.490874Z" fill="white"/>
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
                                        data-bs-toggle="offcanvas" data-bs-target="#profileOffcanvas" aria-controls="profileOffcanvas">
                                </div>
                                @endguest
                                @auth
                                    
                                 <div class="user_img drop_menu">
                                    <div class="usr_profile">
                                        @if (Auth::user()->profile_image)
                                        <img src="{{ asset(Auth::user()->profile_image) }}" class="img-fluid profile-circle" style=' border-radius: 50%;'>
                                        @else
                                        <img src="{{ dimage()}}" class="img-fluid">
                                        @endif
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-right" style="margin-right: 20px;">
                                        <div class="dropdown-main ">
                                            <div class="user_detail">
                                                <div class="user_img">
                                                    @if (Auth::user()->profile_image)
                                                    <img src="{{ asset(Auth::user()->profile_image) }}" class="img-fluid profile-circle" style=' border-radius: 50%;'>
                                                    @else
                                                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{
                                                    strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div class="user_name">
                                                    <h5>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h5>
                                                    <p>{{ Auth::user()->email }}</p>
                                                </div>
                                            </div>
                                            <div class="dash-icon">
                                                @if(Auth::user()->user_type ==='user')
                                                <a class="dropdown-item"
                                                    href="{{ route('user-dashboard', ['locale' => app()->getLocale()]) }}"><i
                                                        class="fa-solid fa-envelope-open-text"></i></i>Dashboard
                                                </a>
                                                @elseif(Auth::user()->user_type ==='vendor')
                                                <a class="dropdown-item"
                                                    href="{{ route('vendor-overview', ['locale' => app()->getLocale()]) }}"><i
                                                        class="fa-solid fa-envelope-open-text"></i></i>Dashboard
                                                </a>
                                                @endif

                                            </div>

                                            <div class="dash-icon">
                                                @if(Auth::user()->user_type ==='user')
                                                <a class="dropdown-item"
                                                    href="{{ route('user-profile', ['locale' => app()->getLocale()]) }}"><i
                                                        class="fa fa-user"></i>My discussions</a>
                                                @elseif(Auth::user()->user_type ==='vendor')
                                                <a class="dropdown-item"
                                                    href="{{ route('vendor-profile', ['locale' => app()->getLocale()]) }}"><i
                                                        class="fa fa-user"></i>My Profile</a>
                                                @endif
                                            </div>

                                            @if(Auth::user()->user_type ==='user')
                                            <div class="dash-icon">
                                                <a class="dropdown-item"
                                                    href="{{ route('user-support', ['locale' => app()->getLocale()]) }}">
                                                    <i class="fa-solid fa-headset"></i>Support
                                                </a>
                                            </div>
                                            @endif

                                            <div class="dash-icon">

                                                @if(Auth::user()->user_type ==='user')
                                                <a class="dropdown-item"
                                                href="{{ route('user-configuration', ['locale' => app()->getLocale()]) }}"><i class="fa-solid fa-gear"></i>Configuration</a>
                                                @elseif(Auth::user()->user_type ==='vendor')
                                                <a class="dropdown-item"
                                                    href="{{ route('vendor-configuration', ['locale' => app()->getLocale()]) }}"><i class="fa-solid fa-gear"></i>Configuration</a>
                                                @endif
                                                </div>

                                            <div class="dash-icon">
                                                <a class="dropdown-item" href="{{ route('logout') }}"><i
                                                        class="fa fa-power-off"></i>Sign Out</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endauth



                                <!-- Offcanvas Profile Dropdown -->
                                <div class="offcanvas offcanvas-end" tabindex="-1" id="profileOffcanvas" aria-labelledby="offcanvasLabel">
                                    <div class="offcanvas-header">
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


                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.9819 0.490874L9.99988 7.50855L16.9815 0.527235C17.1357 0.363097 17.3215 0.231792 17.5277 0.141194C17.7339 0.0505961 17.9562 0.00257135 18.1815 0C18.6637 0 19.1261 0.191544 19.4671 0.532495C19.808 0.873445 19.9996 1.33587 19.9996 1.81805C20.0038 2.04095 19.9625 2.26236 19.8781 2.4687C19.7936 2.67504 19.6679 2.86195 19.5087 3.01796L12.4362 9.99928L19.5087 17.0715C19.8083 17.3646 19.9841 17.7616 19.9996 18.1805C19.9996 18.6627 19.808 19.1251 19.4671 19.4661C19.1261 19.807 18.6637 19.9986 18.1815 19.9986C17.9497 20.0082 17.7186 19.9695 17.5026 19.885C17.2866 19.8005 17.0906 19.672 16.9269 19.5077L9.99988 12.49L3.00009 19.4895C2.84646 19.6482 2.66294 19.7748 2.4601 19.8622C2.25727 19.9496 2.03914 19.9959 1.8183 19.9986C1.3361 19.9986 0.873656 19.807 0.532691 19.4661C0.191726 19.1251 0.000173751 18.6627 0.000173751 18.1805C-0.00406521 17.9576 0.0372916 17.7362 0.121706 17.5299C0.206121 17.3235 0.331813 17.1366 0.491068 16.9806L7.56359 9.99928L0.491068 2.92706C0.191413 2.63392 0.0156999 2.23695 0.000173751 1.81805C0.000173751 1.33587 0.191726 0.873445 0.532691 0.532495C0.873656 0.191544 1.3361 0 1.8183 0C2.25465 0.00545415 2.67282 0.181805 2.9819 0.490874Z" fill="white"/>
                                            </svg>
                                        </button>
                                    </div>
                                     <div class="offcanvas-body">
                                     <h2>Get Started!</h2>

                        <p>Create an Account and Secure Your Exclusive Logo Today. </p>

                         <div class="Header_buttons">
                                                        <a href="{{route('login')}}" class="cta cta_trans">Login</a>
                            <a href="{{route('register')}}" class="cta cta_orange">Sign Up</a>
                                                    </div>
                                </div>

                                    </div>


                            </div>





                        <!-- mobile nav -->

                       <div class="mobile-sidebar">

                            {{-- Header: Logo on left, Close button on right --}}
                            <div class="mobile-sidebar-header">
                                <div class="mobile-sidebar-logo">
                                    @if (isset($headerLogo) && $headerLogo)
                                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand">
                                            <img src="{{ asset($headerLogo->meta_value) }}" alt="{{ $headerLogo->meta_key }}">
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

                                {{-- Categories: flat list, no dropdown, top 3 only --}}
                                <div class="menu_section">
                                    <h4 class="section_heading">{{ $headerContent['Categories'] ?? 'Categories' }}</h4>
                                    <hr class="section_divider">
                                    <ul class="flat_list">
                                        @foreach($categories->take(3) as $category)
                                            @php
                                                $translation = $category->translation ?? $category->translations->first();
                                            @endphp
                                            @if($translation)
                                                <li>
                                                    <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $translation->slug]) }}">
                                                        {{ $translation->name }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Top Rated Products: flat list, no dropdown, top 3 only --}}
                                <div class="menu_section">
                                    <h4 class="section_heading">{{ $headerContent['top_rated_product'] ?? 'Top Rated Products' }}</h4>
                                    <hr class="section_divider">
                                    <ul class="flat_list">
                                        @foreach($mobileBusinesses as $business)
                                            @php
                                                $translation = $business->translations->first();
                                            @endphp
                                            @if($translation)
                                                <li>
                                                    <a href="javascript:void(0);" onclick="changeProducts('{{ $translation->slug }}')">
                                                        {{ $translation->name }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Information --}}
                                <div class="menu_section">
                                    <h3 class="section_heading">Information</h3>
                                    <hr class="section_divider">
                                    <ul class="flat_list">
                                        <li>
                                            <a href="{{ route('contact', ['locale' => session('lang_code', 'en-us')]) }}">
                                                {{-- {{ $headerContent['expert_guide'] ?? 'Expert Guides' }} --}}
                                                Contact Us
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('FaqsShow', ['locale' => session('lang_code', 'en-us')]) }}">
                                                {{ $headerContent['FAQs'] ?? 'FAQs' }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div class="mobile-sidebar-overlay"></div>
                    </nav>
                </div>
            </div>

        </section>
    </header>
    @yield('content')
    @livewireScripts
    <!-- content section end -->
    <!-- footer  -->
    @php
    use App\Models\FooterContent;
    @endphp
    <footer class="dark">
        <div class="container">
            <div class="footer-wrp ">
                <div class="row foot-row">
                    <div class="col-lg-12">
                        <div class="foot-row-lft p_80">
                            <div class="foot-logo">

                                @if (isset($footerLogo) && $footerLogo)
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
                                    class="brand"><img src="{{ asset($footerLogo->meta_value) }}"
                                        alt="{{ $footerLogo->meta_key }}"></a>
                                @else
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
                                    class="brand"><img src="{{ asset('front/img/foot-logo.svg') }}"></a>
                                @endif

                                <ul class="foot-right-list">
                                    <li>
                                        <a href="{{ $footerMediaUrls['facebook_url'] ?? '#' }}" target="_blank">
                                            @if (isset($facebookIcon))
                                            <img class="media_icon" src="{{ asset($facebookIcon->meta_value) }}"
                                                alt="{{ $facebookIcon->meta_key }}"
                                                style="width: 30px; height: 30px; filter: brightness(0) invert(1);">
                                            @else
                                            <i class="fa-brands fa-facebook-f"
                                                style="color: #ffffff; font-size: 30px;"></i>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ $footerMediaUrls['instagram_url'] ?? '#' }}" target="_blank">
                                            @if (isset($instagramIcon))
                                            <img class="media_icon" src="{{ asset($instagramIcon->meta_value) }}"
                                                alt="{{ $instagramIcon->meta_key }}"
                                                style="width: 30px; height: 30px; filter: brightness(0) invert(1);">
                                            @else
                                            <i class="fa-brands fa-instagram"
                                                style="color: #ffffff; font-size: 30px;"></i>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ $footerMediaUrls['twitter_url'] ?? '#' }}" target="_blank">
                                            @if (isset($twitterIcon))
                                            <img class="media_icon" src="{{ asset($twitterIcon->meta_value) }}"
                                                alt="{{ $twitterIcon->meta_key }}"
                                                style="width: 30px; height: 30px; filter: brightness(0) invert(1);">
                                            @else
                                            <i class="fa-brands fa-twitter"
                                                style="color: #ffffff; font-size: 30px;"></i>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ $footerMediaUrls['pinterest_url'] ?? '#' }}" target="_blank">
                                            @if (isset($PinterestIcon))
                                            <img class="media_icon" src="{{ asset($PinterestIcon->meta_value) }}"
                                                alt="{{ $PinterestIcon->meta_key }}"
                                                style="width: 30px; height: 30px; filter: brightness(0) invert(1);">
                                            @else
                                            <i class="fa-brands fa-pinterest-p"
                                                style="color: #ffffff; font-size: 30px;"></i>
                                            @endif
                                        </a>
                                    </li>
                                </ul>

                            </div>

                            <div class="foot-col footer-dropdown">
                                <h6 class="footer-title"> {{ $footerContents['discover'] ?? 'Discover' }}
                                    <span class="footer-arrow d-flex d-md-none">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </h6>
                                <ul class="foot-col-list">
                                    <li>
                                        <a
                                            href="{{ route('category', ['locale' => session('lang_code', 'en-us')]) }}">
                                            {{ $footerContents['categories'] ?? 'Categories' }}
                                        </a>
                                    </li>
                                    <li><a
                                            href="{{ route('top-rated-product', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['top_rated_product'] ??
                                                'Top-Rated Products
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ' }}
                                        </a>
                                    </li>
                                    {{-- <li><a
                                            href="{{ route('exclusive-business-deals') }}">{{ $footerContents['exclusive_deal'] ?? 'Exclusive Deals' }}</a>
                                    </li> --}}
                                </ul>
                            </div>
                            <div class="foot-col footer-dropdown">
                                <h6 class="footer-title">
                                    {{ $footerContents['company'] ?? 'Company' }}
                                    <span class="footer-arrow d-flex d-md-none">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </h6>
                                <ul class="foot-col-list">
                                    <li><a
                                            href="{{ route('who-we-are', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['who_we_are'] ??
                                                'Who We Are
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ' }}</a>
                                    </li>
                                    <li><a
                                            href="{{ route('privacy-policy', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['privacy_policy'] ??
                                                'Privacy Policy
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ' }}</a>
                                    </li>
                                    <li><a
                                            href="{{ route('terms-condition', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['terms_and_conditions'] ?? 'Terms & Conditions' }}</a>
                                    </li>
                                </ul>
                            </div>
                            {{-- <div class="foot-col">
                                <h6>{{ $footerContents['vendors'] ?? 'Vendors' }}</h6>
                                <ul class="foot-col-list">
                                    <li><a
                                            href="{{ route('vendor-get-listed', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['get_listed'] ?? 'Get Listed' }}</a>
                                    </li>
                                    <li>
                                            <a href="{{ route('vendor-how-it-work', ['locale' => session('lang_code', 'en-us')]) }}">
                                                How It Works</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('vendor-help', ['locale' => session('lang_code', 'en-us')]) }}">
                                            Vendor Help </a>
                                        
                                </ul>
                            </div> --}}
                            <div class="foot-col footer-dropdown">
                                <h6 class="footer-title">{{ $footerContents['help'] ?? 'Help' }}
                                    <span class="footer-arrow d-flex d-md-none">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </h6>
                                <ul class="foot-col-list">
                                    {{-- <li>
                                        <a href="{{ route('expert-guide', ['locale' => session('lang_code', 'en-us')]) }}"
                                            style="white-space: nowrap; text-decoration: none; display: inline-block;">
                                            {{ $footerContents['expert_guides'] ?? 'Expert Guides' }}
                                        </a>
                                    </li> --}}
                                    {{-- <li>
                                        <a
                                            href="{{ route('help-center', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['help_center'] ?? 'Help Center' }}</a>
                                    </li> --}}
                                    <li>
                                        <a
                                            href="{{ route('contact', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['contact'] ?? 'Contact' }}</a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('FaqsShow', ['locale' => session('lang_code', 'en-us')]) }}">{{ $FAQs['contact'] ?? 'FAQs' }}</a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3" style="display: none">
                        <div class="foot-row-right foot-col p_80">
                            <h6> {{ $footerContents['follow_us'] ?? '' }}</h6>
                            @php
                            $lang_id = getCurrentLanguageID(); // Ensure this function exists
                            $footerContents = FooterContent::where('lang_id', $lang_id)
                            ->pluck('meta_value', 'meta_key')
                            ->toArray();

                            // Fetch social media links
                            $footerMediaUrls = FooterContent::whereIn('meta_key', [
                            'facebook_url',
                            'instagram_url',
                            'twitter_url',
                            ])
                            ->where('lang_id', $lang_id)
                            ->pluck('meta_value', 'meta_key')
                            ->toArray();
                            @endphp
                            <ul class="foot-right-list">
                                <li>
                                    <a href="{{ $footerMediaUrls['facebook_url'] ?? '#' }}" target="_blank">
                                        @if (isset($facebookIcon))
                                        <img class="media_icon" src="{{ asset($facebookIcon->meta_value) }}"
                                            alt="{{ $facebookIcon->meta_key }}"
                                            style="width: 30px; height: 30px; filter: brightness(0) invert(1);">
                                        @else
                                        <i class="fa-brands fa-facebook-f"
                                            style="color: #ffffff; font-size: 30px;"></i>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ $footerMediaUrls['instagram_url'] ?? '#' }}" target="_blank">
                                        @if (isset($instagramIcon))
                                        <img class="media_icon" src="{{ asset($instagramIcon->meta_value) }}"
                                            alt="{{ $instagramIcon->meta_key }}"
                                            style="width: 30px; height: 30px; filter: brightness(0) invert(1);">
                                        @else
                                        <i class="fa-brands fa-instagram"
                                            style="color: #ffffff; font-size: 30px;"></i>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ $footerMediaUrls['twitter_url'] ?? '#' }}" target="_blank">
                                        @if (isset($twitterIcon))
                                        <img class="media_icon" src="{{ asset($twitterIcon->meta_value) }}"
                                            alt="{{ $twitterIcon->meta_key }}"
                                            style="width: 30px; height: 30px; filter: brightness(0) invert(1);">
                                        @else
                                        <i class="fa-brands fa-twitter"
                                            style="color: #ffffff; font-size: 30px;"></i>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
                {{-- <pre>
                        {{ print_r($languages, true) }}
                </pre> --}}
                <div class="foot-btm d-flex justify-content-between">
                    <div class="ft-btm-lft">
                        <p>©<?php echo date('Y'); ?> Localio. All rights reserved.</p>
                    </div>
                    <div class="ft-btm-rgt">
                        <div class="select-menu ">
                            <div class="select-btn">
                                <span class="sBtn-text">
                                    {{ session('lang_code') && session('lang_name') ? ucfirst(session('lang_name')) : 'United States - English' }}
                                </span>
                                <i class="fa-solid fa-chevron-down" style="color: #ffffff;"></i>
                            </div>
                            <ul class="options">
                                <div class="container">
                                    <h3 style="color: black;">Choose your Country/Region</h3>
                                <div class="footer-langs-container">
                                    @foreach ($languages as $language)
                                    <li>
                                        <a href="{{ route('set-site-languages', ['lang_id' => $language->id]) }}">
                                            {{ $language->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </div>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    @php
    $langId = getCurrentLanguageID();

    // Try to get content for the current language
    $sections = \App\Models\LearnMoreContent::where('lang_id', $langId)->orderBy('sort_order')->get();

    // If not found, fallback to lang_id = 1
    if ($sections->isEmpty()) {
    $sections = \App\Models\LearnMoreContent::where('lang_id', 1)->orderBy('sort_order')->get();
    }

    $sectionsAvailable = $sections->isNotEmpty();
    @endphp

    <div class="modal-overlay" id="learnMoreModal">
        <div class="circle_11">
            <button class="modal-close" onclick="closeModal()" aria-label="Close">&times;</button>
            <div class="modal-content">


                @if ($sectionsAvailable)
                @php
                $header = $sections->firstWhere('sort_order', 1);
                @endphp

                <div class="modal-header" style="display: block">
                    <h2 class="modal-title" style="width: max-content">
                        {{ $header->title ?? 'About Our Independent Reviews' }}
                    </h2>
                    <p class="modal-subtitle">
                        {{ $header->content ?? 'Transparency in our review process' }}
                    </p>
                </div>
                <div class="modal-body">
                    @foreach ($sections->where('sort_order', '>', 1) as $section)
                    <h2 class="modal-title" style="width: max-content">
                        {{ $section->title ?? 'About Our Independent Reviews' }}
                    </h2>
                    <p>{{ $section->content ?? 'Default content paragraph.' }}</p>
                    @endforeach
                </div>
                <div class="modal-disclaimer">
                    <strong>Disclosure:</strong> We may earn affiliate commissions from qualifying purchases. This
                    does not affect our editorial independence or review criteria.
                </div>
                @else
                {{-- Extra fallback if there's no content at all in DB --}}
                <div class="modal-header">
                    <h2 class="modal-title">About Our Independent Reviews</h2>
                    <p class="modal-subtitle">Transparency in our review process</p>
                </div>

                <div class="modal-body">
                    <p>Localio provides unbiased research and comprehensive reviews to help you make informed
                        decisions. Our independent analysis ensures you get accurate, trustworthy information about
                        products and services.</p>

                    <p>Our team of experts thoroughly evaluates each product based on multiple criteria including
                        functionality, user experience, value for money, and customer support. We maintain strict
                        editorial independence to ensure our recommendations serve your best interests.</p>

                    <p>When you make a purchase through our affiliate links, we may receive a small commission at no
                        additional cost to you. This helps us continue providing free, high-quality reviews and
                        maintain our platform.</p>
                </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Updated Modal Structure -->
    <div id="login-modal"
        class=" login_modal modal-overlay fixed inset-0 bg-opacity-40 z-[9999] items-center justify-center p-4 login_modal"
        role="dialog"
        aria-labelledby="modal-title"
        aria-hidden="true"
        style="display: none;">

        <div class="modal-content bg-white rounded-2xl relative" style="width: 380px; padding: 48px 40px 40px 40px;">

            <button id="close-login-btn" class="close-btn" aria-label="Close modal">
                ✕
            </button>

            <div class="text-center">
                <h2 id="modal-title" class="modal-title">
                    <div class="hd_text">
                        <h2 class="text-center">{{static_text('login_to_your_account')}}</h2>

                    </div>
                </h2>
                    <p class="text-center">{{ static_text('welcome_back') }}</p>

                {{-- <p class="modal-description">
                    Please sign in to add this business to your wishlist.
                </p> --}}

                <div class="button">
                    <section class="">
                        <div class="container">
                            <div class="contact_content" data-aos="fade-up" data-aos-duration="1000">

                                <div class="scl_login">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="login_box size18">
                                                <div class="l_goin1">
                                                <a href="{{ route('google.login') }}" class="login_link" style="background: #DB4437;">
                                                    <span class="scl-icn"><i class="fa-brands fa-google"></i></span>
                                                    {{-- Login with Google --}}
                                                </a>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="login_box size18">
                                                <div class="l_goin2">
                                                <a href="{{ route('login.facebook') }}" class="login_link">
                                                    <span class="scl-icn"><i class="fa-brands fa-facebook"></i></span>
                                                    {{-- Login with Facebook --}}
                                                </a>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Your existing form -->
                                <div class="or-separator">
                                    <span class="size16">or</span>
                                </div>
                                @if (session('loginerror'))
                                    <div class="text-danger">{{ session('loginerror') }}</div>
                                @endif
                                <!-- Continue with the rest of your form -->
                                <form class="login_form" action="{{ route('login_process') }}" method="POST" autocomplete="off">
                                    @csrf

                                    <input type="hidden" name="url_intended" id="url_intended" value="">

                                    <div class="form-group">
                                    <x-google-input type="text" name="email" id="emailAddress" label="Email"  :attributes="['autocomplete' => 'off']" />
                                    </div>
                                    <div class="form-group">
                                        {{-- <input type="password" class="form-control" id="password" name="password" placeholder="Password"> --}}
                                        <x-google-input type="password" name="password" id="password"  label="Password" :attributes="['autocomplete' => 'off']" />

                                        <span id="togglePassword" class="eye-icon">
                                            <i class="fa fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <div class="form-row align-items-center">
                                        <div class="col frgt_btn text-right">
                                            <a href="{{ route('recover-password') }}" class="small"  onmouseover="this.style.color='#f9633b'"
                                            onmouseout="this.style.color='#06498b'" style="margin-top: -6px;">{{ static_text('forgot_password') }}</a>
                                        </div>
                                    </div>
                                    <div class="accor-btn">
                                        <button type="submit" class="cta cta_white">{{ static_text('login') }}</button>
                                    </div>
                                    <p class="new-accnt text-center" style="margin-top: 10px;margin-bottom: 2px;">
                                        {{ static_text('new_to_localio') }} <a href="{{ route('register') }}" class="sk_blu big-bld"
                                        onmouseover="this.style.color='#f9633b'"
                                        onmouseout="this.style.color='#06498b'"
                                        >{{ static_text('sign_up') }}</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- login script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ✅ SCOPED TO MODAL ONLY - target the modal container first
            const loginModal = document.getElementById('login-modal');
            if (!loginModal) return;

            // ✅ All elements are now scoped within the modal with more flexible selectors
            const form = loginModal.querySelector('.login_form');
            const emailInput = loginModal.querySelector('input[name="email"]') || loginModal.querySelector('#emailAddress');
            const passwordInput = loginModal.querySelector('input[name="password"]') || loginModal.querySelector('#password');
            const togglePassword = loginModal.querySelector('#togglePassword');






            // ✅ Toggle password visibility (with better error handling)
            if (togglePassword && passwordInput) {
                togglePassword?.addEventListener('click', function (e) {
                    e.preventDefault();
                    const icon = this.querySelector('i');
                    if (!icon || !passwordInput) return;



                    // Use setTimeout to avoid DOM/reactive reversion
                    setTimeout(() => {
                        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                        icon.classList.toggle('fa-eye', isPassword);
                        icon.classList.toggle('fa-eye-slash', !isPassword);


                    }, 0);
                });


            }

            // ✅ Form validation (scoped to modal form only)
            form.addEventListener('submit', function (e) {
                console.log('Form submitted');

                document.getElementById('url_intended').value = window.location.href;

                removeErrors();
                let valid = true;

                if (!emailInput || !passwordInput) {
                    console.error('Email or password input not found for validation');
                    return;
                }

                const emailValue = emailInput.value.trim();
                const passwordValue = passwordInput.value.trim();

                // Email validation
                if (!emailValue) {
                    showError(emailInput, "Email is required.");
                    valid = false;
                } else if (!validateEmail(emailValue)) {
                    showError(emailInput, "Enter a valid email address.");
                    valid = false;
                }

                // Password validation
                if (!passwordValue) {
                    showError(passwordInput, "Password is required.");
                    valid = false;
                } else if (passwordValue.length < 8) {
                    showError(passwordInput, "Password must be at least 8 characters.");
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    console.log('Form validation failed');
                } else {
                    console.log('Form validation passed');
                }
            });

            // ✅ Error handling functions (scoped to modal)
            function showError(input, message) {
                const parentGroup = input.closest('.form-group');
                const inputBox = input.closest('.input-box') || input.parentElement;

                if (!parentGroup) {
                    console.error('Parent form-group not found for input:', input);
                    return;
                }

                if (inputBox) {
                    inputBox.classList.add('error');
                }

                // Only add error if it doesn't already exist
                if (!parentGroup.querySelector('.text-danger')) {
                    const error = document.createElement('div');
                    error.className = 'text-danger small mt-1';
                    error.innerText = message;
                    parentGroup.appendChild(error);
                }
            }

            function removeErrors() {
                // Only remove errors within the modal form
                const errors = form.querySelectorAll('.text-danger');
                errors.forEach(error => error.remove());

                const inputBoxes = form.querySelectorAll('.input-box.error');
                inputBoxes.forEach(box => box.classList.remove('error'));
            }

            function validateEmail(email) {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return regex.test(email);
            }

            function removeErrorOnTyping(input) {
                if (!input) return;

                input.addEventListener('input', () => {
                    const parentGroup = input.closest('.form-group');
                    const inputBox = input.closest('.input-box') || input.parentElement;

                    if (inputBox?.classList.contains('error')) {
                        inputBox.classList.remove('error');
                        const errorMessage = parentGroup?.querySelector('.text-danger');
                        if (errorMessage) errorMessage.remove();
                    }
                });
            }

            // ✅ Apply real-time error removal (scoped to modal inputs)
            if (emailInput) removeErrorOnTyping(emailInput);
            if (passwordInput) removeErrorOnTyping(passwordInput);

        });
    </script>
    {{-- login script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('login-modal');
            const closeBtn = document.getElementById('close-login-btn');

            // Livewire event: Show modal
            Livewire.on('show-login-modal', () => {
                // console.log('✅ show-login-modal received');
                if (modal) {
                    modal.style.display = 'flex'; // explicitly set to flex
                    modal.classList.remove('hidden'); // for good measure
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('modal-open');

                    setTimeout(() => {
                        closeBtn?.focus();
                    }, 300);
                }
            });


            // Livewire event: Close modal
            Livewire.on('close-login-modal', () => {
                // console.log('❌ close-login-modal received');
                if (modal) {
                    modal.style.display = 'none'; // hide properly
                    modal.classList.add('hidden'); // for safety
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                }
            });


            // Close button click handler
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    Livewire.dispatch('close-login-modal');
                });
            }

            // Close on overlay click
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        Livewire.dispatch('close-login-modal');
                    }
                });
            }

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal?.classList.contains('hidden')) {
                    Livewire.dispatch('close-login-modal');
                }
            });
        });
    </script>
    <script>
        function openLoginModal() {
            const modal = document.getElementById('login-modal');
            if (modal) {
                modal.style.display = 'flex'; // show modal
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');

                const closeBtn = document.getElementById('close-login-btn');
                setTimeout(() => {
                    closeBtn?.focus();
                }, 300);
            }
        }
    </script>

    <!----------------------------------------- read section end --------------------------------------- -->
    <!-- AddToAny BEGIN -->
    <script async src="https://static.addtoany.com/menu/page.js"></script>
    <script>
        window.addEventListener('open-modal', () => {
            const modal = document.getElementById('learnMoreModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        // Modal Functions
        function openModal() {
            const modal = document.getElementById('learnMoreModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('learnMoreModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('learnMoreModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"
        integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script src="{{ asset('front/js/script.js') }}?{{ time() }}"></script>
    <script src="{{ asset('front/js/tracking.js') }}?{{ time() }}"></script>
    <script>
        window.livewireDebug = true;
    </script>

    <script>
        $(function() {
            AOS.init();
        });

        function changeCategory(categoryId) {
            let langCode = "{{ session('lang_code', 'en-us') }}";
            let url = new URL(window.location.href);
            url.pathname = `/${langCode}/categories/${categoryId}`;
            window.location.href = url.href;
        }

        function changeProducts(ProductId) {
            let langCode = "{{ session('lang_code', 'en-us') }}";
            let url = new URL(window.location.href);
            url.pathname = `/${langCode}/products/${ProductId}`;
            window.location.href = url.href;
        }

        document.querySelectorAll('.footer-title').forEach(function(item){
            item.addEventListener('click', function(){
                this.closest('.footer-dropdown').classList.toggle('active');
            });
        });

            $('.mobile-sidebar-btn').on('click', function () {
                $('.mobile-sidebar').addClass('active');
                $('.mobile-sidebar-overlay').addClass('active');
            });

            $('.mobile-sidebar-close, .mobile-sidebar-overlay').on('click', function () {
    $('.mobile-sidebar').removeClass('active');
    $('.mobile-sidebar-overlay').removeClass('active');
});


    </script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const selectBtn = document.querySelector('.select-btn');
            const options = document.querySelectorAll('.option');
            const sBtnText = document.querySelector('.sBtn-text');

            options.forEach(option => {
                if (!this.classList.contains('selected')) {
                    const selectedName = this.dataset.langName;
                    sBtnText.textContent = selectedName;
                }
            });
        });
    </script>
    <!-- header search js -->
    <script>
        $(document).ready(function() {
            function checkScroll() {
                const $myElement = $('#myID');
                @if (Route::currentRouteName() === 'top-rated-product' || Route::currentRouteName() === 'category.detail')
                    $myElement.show();
                    return;
                @endif

                if ($(window).scrollTop() > 460) {
                    $myElement.show();
                } else {
                    $myElement.hide();
                }
            }
            checkScroll();
            $(window).on('scroll', checkScroll);
        });
    </script>
    @session('success')
    <script>
        @if(Session::has('success'))
        Swal.fire({
            icon: 'success',
            title: "{{ session('success') }}",
            text: '{{ Session::get('
            sy success ') }}',
            position: 'top-right',
            toast: true,
            showConfirmButton: false,
            timer: 3000, // Auto close after 3 seconds
        });
        @endif
    </script>
    @endsession
    <script>
        @if(Session::has('error'))
        Swal.fire({
            icon: 'error',
            title: "{{ session('error') }}",
            text: '{{ Session::get('
            error ') }}',
            position: 'top-right',
            toast: true,
            showConfirmButton: false,
            timer: 3000, // Auto close after 3 seconds
        });
        @endif
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const msg = sessionStorage.getItem('flash_success');
            if (msg) {
                Swal.fire({
                    icon: 'success',
                    title: msg,
                    toast: true,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 3000,
                });
                sessionStorage.removeItem('flash_success');
            }
        });
    </script>
    <script>
        // When the user clicks on the select button, toggle the dropdown menu
        $('.select-btn').on('click', function() {
            $(this).closest('.select-menu').toggleClass('open');
        });

        // Optional: Close the dropdown if the user clicks outside of it
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.select-menu').length) {
                $('.select-menu').removeClass('open');
            }
        });
    </script>

    <script>
        document.addEventListener('livewire:load', function() {
            window.Livewire.on('swal:success', function(data) {
                console.log("SweetAlert Success Data:", data); // Debugging output

                if ($.isArray(data)) {
                    data = data[0]; // Extract the object from the array
                }

                Swal.fire({
                    toast: true,
                    position: 'top-right',
                    icon: data.icon ?? "success",
                    title: data.title ?? "Success",
                    text: data.text ?? "Action completed successfully!",
                    showConfirmButton: false,
                    timer: 5000
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.input-box input, .input-box select').forEach((el) => {
                if (el.value) {
                    el.parentNode.classList.add('active');
                }
            });
        });
    </script>

    <script>
        function setFocus(focused, element) {
            const box = element.closest('.input-box');
            if (focused) {
                box.classList.add('active');
            } else {
                if (!element.value) {
                    box.classList.remove('active');
                }
            }
        }
    </script>

    <script>


        $(document).ready(function(){
            $('.menu-item').on('click',function(){
                if ($('.menu-item.menu-active').length > 0) {
                    $('body').addClass('body_hiden');
                } else {
                    $('body').removeClass('body_hiden');
                }
            });
        });

        $(document).on('click', function(event) {
            if($('body').hasClass('body_hiden')){
                if ($('.menu-item.menu-active').length === 0) {
                    $('body').removeClass('body_hiden');
                }
            }

        });
    </script>
    <script>
        setInterval(() => {
            fetch('/refresh-csrf')
                .then(response => response.json())
                .then(data => {
                    const token = data.token;

                    // ✅ Update meta tag
                    const csrfTag = document.querySelector('meta[name="csrf-token"]');
                    if (csrfTag) {
                        csrfTag.setAttribute('content', token);
                    }

                    // ✅ Update Livewire's token (important!)
                    if (window.Livewire) {
                        window.Livewire.findAllComponents().forEach(component => {
                            component.csrftoken = token;
                        });
                    }

                    console.log('✅ CSRF refreshed');
                });
        }, 10 * 60 * 1000); // every 10 minutes
    </script>



    <!-- Category Sidebar Drawer (Amazon Style) -->
    <div class="category-sidebar-overlay" id="categories-sidebar-overlay"></div>
    <div class="category-sidebar" id="categories-sidebar">
        <div class="category-sidebar-header">
            @if(Auth::check())
                <a href="{{ route('user-profile', ['locale' => app()->getLocale()]) }}" class="user-greeting">
                    <i class="fa-solid fa-circle-user avatar-icon"></i>
                    <span>Hello, {{ Auth::user()->first_name }}</span>
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
                    <!-- <div class="sidebar-menu-section">
                        <h3 class="sidebar-section-title">Trending</h3>
                        <ul class="sidebar-menu-list">
                            <li>
                                <a href="{{ route('top-rated-product', ['locale' => app()->getLocale()]) }}">
                                     Bestsellers / Top Rated
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="sidebar-menu-divider"></div> -->
                    
                    <div class="sidebar-menu-section">
                        <h3 class="sidebar-section-title">Shop by Category</h3>
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
                                    <i class="fa-solid fa-arrow-left me-2"></i> Main Menu
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

    <style>
        /* Amazon-like Categories Sidebar styling */
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
            width: 300%; /* We have Main Panel + Sub Panel + Business Panel side-by-side */
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
        /* Conditional negative margin on Top Rated Products mobile business cards based on title line counts */
        function adjustAutomotiveRatings() {
            if (window.innerWidth > 991) {
                document.querySelectorAll('.automotive-card .tp-btm').forEach(el => {
                    el.style.setProperty('margin-top', '', 'important');
                });
                return;
            }

            document.querySelectorAll('.automotive-card').forEach(card => {
                const titleEl = card.querySelector('h6.head');
                const ratingEl = card.querySelector('.tp-btm');
                if (!titleEl || !ratingEl) return;

                // Reset style first to measure correctly
                ratingEl.style.setProperty('margin-top', '', 'important');

                const style = window.getComputedStyle(titleEl);
                const lineHeight = parseFloat(style.lineHeight) || 20; // fallback to 20px
                const titleHeight = titleEl.getBoundingClientRect().height;

                // If title fits in one line (height is less than 1.5x line-height)
                if (titleHeight <= lineHeight * 1.5) {
                    ratingEl.style.setProperty('margin-top', '-1rem', 'important');
                } else {
                    ratingEl.style.setProperty('margin-top', '', 'important');
                }
            });
        }

        // Run immediately to prevent layout shift before events fire
        adjustAutomotiveRatings();

        window.addEventListener('DOMContentLoaded', adjustAutomotiveRatings);
        window.addEventListener('load', adjustAutomotiveRatings);
        window.addEventListener('resize', adjustAutomotiveRatings);
        
        // Use MutationObserver to watch for Livewire updates and rerun instantly
        const observer = new MutationObserver((mutations) => {
            observer.disconnect();
            adjustAutomotiveRatings();
            observe();
        });

        function observe() {
            const target = document.body;
            observer.observe(target, {
                childList: true,
                subtree: true
            });
        }
        
        observe();
    </script>
</body>
</html>
