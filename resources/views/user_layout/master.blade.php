<?php
    $currentLangCode = getCurrentLocale();
    $currentLangObj = \App\Models\Language::where('lang_code', $currentLangCode)->first();
    $htmlLangAttr = $currentLangObj?->html_lang ?? ('en-US');
    $canonicalUrl = \App\Services\SeoService::getCanonicalUrl();
?>
<!DOCTYPE html>
<html lang="{{ $htmlLangAttr }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="canonical" href="@yield('canonical_url', $canonicalUrl)">
    <?php

    use App\Models\HomeContent; // Import your model
    use App\Models\Category;
    use App\Models\Language;
    use Illuminate\Support\Facades\Cache;

    $lang_id = getCurrentLanguageID();
    $layoutHeadData = Cache::remember("layout_head_v1_{$lang_id}", 600, function () use ($lang_id) {
        $languages = Language::all();
        $favicon = function_exists('getSiteFavicon') ? getSiteFavicon($lang_id) : 'front/img/icon.svg';

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
                'subCategories.targetCategory.translation' => function ($query) use ($lang_id) {
                    $query->where('lang_id', $lang_id);
                },
            ])
            ->get();

        $rawMetaTitle = HomeContent::where('meta_key', 'meta_home_title')->value('meta_value') ?? 'How to find the Best Product';
        $rawMetaDescription = HomeContent::where('meta_key', 'Meta_home_description')->value('meta_value') ?? (HomeContent::where('meta_key', 'meta_home_description')->value('meta_value') ?? 'Default Description');

        return compact('languages', 'favicon', 'categories', 'sidebarCategories', 'rawMetaTitle', 'rawMetaDescription');
    });

    $languages = $layoutHeadData['languages'];
    $favicon = $layoutHeadData['favicon'];
    $categories = $layoutHeadData['categories'];
    $sidebarCategories = $layoutHeadData['sidebarCategories'];
    $metaTitle = format_meta_text($layoutHeadData['rawMetaTitle'], 'How to find the Best Product');
    $metaDescription = format_meta_text($layoutHeadData['rawMetaDescription'], 'Default Description');

    ?>
    <title>@yield('meta_title', $metaTitle)</title>
    @hasSection('meta_description')
    <meta name="description" content="@yield('meta_description')">
    @else
    <meta name="description" content="{{ $metaDescription }}">
    @endif
    @hasSection('meta_robots')
    <meta name="robots" content="@yield('meta_robots')">
    @endif

    {{-- Open Graph / Facebook / LinkedIn / WhatsApp --}}
    @php
        $resolvedPageTitle = View::hasSection('meta_title') ? trim(View::getSection('meta_title')) : $metaTitle;
        $resolvedPageDesc = View::hasSection('meta_description') ? trim(View::getSection('meta_description')) : $metaDescription;
        $ogType = View::hasSection('og_type') ? trim(View::getSection('og_type')) : 'website';
        $defaultOgImage = asset('front/img/icon.svg');
    @endphp
    <meta property="og:site_name" content="Localio">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="@yield('og_title', $resolvedPageTitle)">
    <meta property="og:description" content="@yield('og_description', $resolvedPageDesc)">
    <meta property="og:url" content="@yield('og_url', $canonicalUrl)">
    <meta property="og:image" content="@yield('og_image', $defaultOgImage)">
    <meta property="og:locale" content="{{ str_replace('-', '_', $htmlLangAttr) }}">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter_title', $resolvedPageTitle)">
    <meta name="twitter:description" content="@yield('twitter_description', $resolvedPageDesc)">
    <meta name="twitter:image" content="@yield('twitter_image', View::hasSection('og_image') ? View::getSection('og_image') : $defaultOgImage)">

    {{-- Multilingual hreflang alternate links --}}
    @hasSection('hreflang')
        @yield('hreflang')
    @else
        @php
            $hreflangLinks = \App\Services\SeoService::getHreflangLinks();
        @endphp
        @foreach($hreflangLinks as $hLink)
    <link rel="alternate" hreflang="{{ $hLink['hreflang'] }}" href="{{ $hLink['href'] }}">
        @endforeach
    @endif

    {{-- Global Structured Data (Organization & WebSite) --}}
    <script type="application/ld+json">
    {!! \App\Services\SeoService::getWebSiteAndOrgSchema() !!}
    </script>

    {{-- Page-specific Structured Data (Breadcrumbs, Product, etc.) --}}
    @yield('structured_data')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="clear-session-url" content="{{ route('clear.register.session') }}">


    {{-- user Id session --}}
    <script>
    window.userId = @json(session('user_id'));
    </script>


    <!-- Optimized Preconnect & DNS-Prefetch (Max 2 preconnects as per Lighthouse best practices) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="dns-prefetch" href="https://static.addtoany.com">

    <!-- Preload Critical Font Awesome Webfont -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
    <!-- Preload primary Montserrat woff2 to eliminate font-swap CLS on right_tool_sec -->
    <link rel="preload" href="https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WlhyyTh89Y.woff2" as="font" type="font/woff2" crossorigin>
    <!-- Override font-display to 'optional' so browser uses fallback if font isn't ready, eliminating CLS -->
    <style>@font-face{font-family:'Montserrat';font-display:optional;src:url('https://fonts.gstatic.com/s/montserrat/v31/JTUSjIg1_i6t8kCHKm459WlhyyTh89Y.woff2') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;}</style>

    @php
        $isHomeRoute = (Route::currentRouteName() === 'home' || request()->is('/') || request()->is($currentLangCode ?? 'en-us'));
    @endphp
    @if ($isHomeRoute)
    <!-- Verbatim Critical Above-the-Fold CSS (0 Render-Blocking, 100% Exact Cascade) -->
    <style id="critical-home-css">{!! file_get_contents(public_path('front/css/critical-home.min.css')) !!}</style>
    <link rel="preload" href="{{ asset('front/css/app.bundle.min.css') }}?v=6" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('front/css/app.bundle.min.css') }}?v=6"></noscript>
    @else
    <!-- Single Combined Minified Stylesheet -->
    <link rel="stylesheet" href="{{ asset('front/css/app.bundle.min.css') }}?v=6">
    @endif

    <!-- Non-Critical CSS (Asynchronously loaded to eliminate render-blocking) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"></noscript>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css"
        integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css"></noscript>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css"
        integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css"></noscript>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@14.6.3/distribute/nouislider.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@14.6.3/distribute/nouislider.min.css"></noscript>

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet"></noscript>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"></noscript>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"></noscript>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"></noscript>

    @include('partials.favicon')
    @livewireStyles
    @stack('styles')
        {{-- Standalone unpkg Alpine removed (Livewire 3.6 bundles Alpine); inline styles bundled into app.bundle.min.css --}}
</head>

@php
    $routeName = Route::currentRouteName();
    $pageClass = $routeName ? 'page-' . str_replace('.', '-', $routeName) : '';
    if (empty($pageClass) || $pageClass == 'page-') {
        $segments = request()->segments();
        // Try to construct a meaningful class from URL segments
        // typically: locale / slug / type
        if (count($segments) >= 3) {
            $pageClass = 'page-' . $segments[2]; // e.g. 'reviews', 'alternatives', 'faq'
        } elseif (count($segments) == 2) {
            $pageClass = 'page-' . $segments[1];
        } else {
            $pageClass = 'page-' . implode('-', $segments);
        }
    }
@endphp
<body class="@yield('body_class') {{ $pageClass }} page-route-{{ $routeName ?? 'none' }}">
    <?php
    $lang = getCurrentLocale();
    $currentCountryId = getCurrentCountry();

    $layoutChrome = Cache::remember("layout_chrome_v1_{$lang_id}_{$currentCountryId}", 600, function () use ($lang_id, $currentCountryId) {
        $headerLogo = \App\Models\HeaderContent::where([['lang_id', 1], ['meta_key', 'header_logo']])->first();
        $headerContent = \App\Models\HeaderContent::where([['lang_id', $lang_id], ['type', 'text']])->pluck('meta_value', 'meta_key');
        if ($headerContent->isEmpty()) {
            $headerContent = \App\Models\HeaderContent::where([['lang_id', 1], ['type', 'text']])->pluck('meta_value', 'meta_key');
        }
        $footerLogo = \App\Models\FooterContent::where([['lang_id', $lang_id], ['meta_key', 'footer_logo']])->first();
        $icons = \App\Models\FooterContent::where('lang_id', 1)
            ->whereIn('meta_key', ['facebook_icon', 'instagram_icon', 'twitter_icon', 'pinterest_icon'])
            ->get();

        $footerContents = \App\Models\FooterContent::where('lang_id', $lang_id)->where('type', 'text')->pluck('meta_value', 'meta_key');
        $footerMediaUrls = \App\Models\FooterContent::where('type', 'url')->where('lang_id', 1)->pluck('meta_value', 'meta_key');
        if ($footerContents->isEmpty()) {
            $footerContents = \App\Models\FooterContent::where('lang_id', 1)->where('type', 'text')->pluck('meta_value', 'meta_key');
        }

        $countryModel = \App\Models\Country::find($currentCountryId);
        $footerCountriesCached = \App\Models\Country::with('language')->get()->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);
        $learnMoreSections = \App\Models\LearnMoreContent::where('lang_id', $lang_id)->orderBy('sort_order')->get();
        if ($learnMoreSections->isEmpty()) {
            $learnMoreSections = \App\Models\LearnMoreContent::where('lang_id', 1)->orderBy('sort_order')->get();
        }

        return compact('headerLogo', 'headerContent', 'footerLogo', 'icons', 'footerContents', 'footerMediaUrls', 'countryModel', 'footerCountriesCached', 'learnMoreSections');
    });

    $headerLogo = $layoutChrome['headerLogo'];
    $headerContent = $layoutChrome['headerContent'];
    $footerLogo = $layoutChrome['footerLogo'];
    $icons = $layoutChrome['icons'];
    $facebookIcon = $icons->where('meta_key', 'facebook_icon')->first();
    $instagramIcon = $icons->where('meta_key', 'instagram_icon')->first();
    $twitterIcon = $icons->where('meta_key', 'twitter_icon')->first();
    $PinterestIcon = $icons->where('meta_key', 'pinterest_icon')->first();
    $footerContents = $layoutChrome['footerContents'];
    $footerMediaUrls = $layoutChrome['footerMediaUrls'];
    $countryModel = $layoutChrome['countryModel'];
    $showCommissionsBanner = false;
    if ($countryModel && $countryModel->show_disclaimer == 1) {
        $showCommissionsBanner = !in_array(Route::currentRouteName(), [
            'who-we-are',
            'privacy-policy',
            'terms-condition',
            'contact',
            'login',
            'register'
        ]);
    }
    ?>


    @if($showCommissionsBanner)
    <div class="transparency-banner">
        <div class="inr-trans">
            <div
                style=" font-size: 10px; color: #e5e4e4; line-height: 1; font-family: sans-serif; max-height: 22px; overflow: hidden; vertical-align: middle;">
                {{ static_text('localio_commissions_message')}}
                <a href="#" onclick="event.preventDefault(); openModal()"
                    style="color: #e5e4e4;  text-decoration:none; transition: color 0.2s;">Learn more</a>
            </div>
        </div>

    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const banner = document.querySelector('.transparency-banner');
        const header = document.querySelector('header .sec_head');

        if (banner && header) {
            const updateHeaderPosition = () => {
                let scroll = window.scrollY;

                if (scroll > 0) {
                    banner.style.transform = 'translateY(0)';

                    if (!document.body.classList.contains('product-page-body')) {
                        header.classList.add('header-scrolled');
                    }
                } else {
                    banner.style.transform = 'translateY(-' + scroll + 'px)';

                    if (!document.body.classList.contains('product-page-body')) {
                        header.classList.remove('header-scrolled');
                    }
                }
            };

            window.addEventListener('scroll', updateHeaderPosition);
            updateHeaderPosition();
        }
    });
    </script>
    @endif
    @include('partials.header')
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileSearch = document.getElementById('mobile-search-container');
        const mobileCatNav = document.getElementById('mobile-cat-nav-bar');
        if (mobileSearch && mobileSearch.classList.contains('home-mobile-search')) {
            window.addEventListener('scroll', function() {
                const heroSection = document.querySelector('.banner_sec') || document.querySelector('.hero_area');
                let threshold = heroSection ? (heroSection.offsetHeight / 1.5) : 250;
                
                if (window.scrollY > threshold) {
                    mobileSearch.classList.add('show-search');
                    if (mobileCatNav) mobileCatNav.classList.add('show-search');
                } else {
                    mobileSearch.classList.remove('show-search');
                    if (mobileCatNav) mobileCatNav.classList.remove('show-search');
                }
            });
        }
    });
    </script>
    <main id="main-content">
        @yield('content')
    </main>
    @livewireScripts
    @include('partials.prevent_page_expired')
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
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand"><img
                                        src="{{ asset($footerLogo->meta_value) }}"
                                        alt="{{ $footerLogo->meta_key }}" width="220" height="52" loading="lazy" decoding="async"></a>
                                @else
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand"><img
                                        src="{{ asset('front/img/foot-logo.svg') }}" alt="Localio" width="220" height="52" loading="lazy" decoding="async"></a>
                                @endif
                                <p class="mt-2">Make better buying decisions</p>
                                <ul class="foot-right-list d-none">
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
                                <p class="footer-title"> {{ $footerContents['discover'] ?? 'Discover' }}
                                    <span class="footer-arrow d-flex d-md-none">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </p>
                                <ul class="foot-col-list">
                                    <li>
                                        <a href="{{ route('category', ['locale' => session('lang_code', 'en-us')]) }}">
                                            {{ $footerContents['categories'] ?? 'Categories' }}
                                        </a>
                                    </li>
                                    <li><a
                                            href="{{ route('top-rated-product', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['top_ratedt'] ??
                                                'Top rated 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ' }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('write-review', ['locale' => session('lang_code', 'en-us')]) }}">Write
                                            review</a>
                                    </li>
                                    {{-- <li><a
                                            href="{{ route('exclusive-business-deals') }}">{{ $footerContents['exclusive_deal'] ?? 'Exclusive Deals' }}</a>
                                    </li> --}}
                                </ul>
                            </div>
                            <div class="foot-col footer-dropdown">
                                <p class="footer-title">
                                    {{ $footerContents['company'] ?? 'Legal' }}
                                    <span class="footer-arrow d-flex d-md-none">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </p>
                                <ul class="foot-col-list">
                                    <li><a
                                            href="{{ route('terms-condition', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['terms_&_conditions'] ?? 'Terms of service' }}</a>
                                    </li>
                                    <li><a
                                            href="{{ route('privacy-policy', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['privacy_policy'] ?? 'Privacy policy' }}</a>
                                    </li>
                                    <li><a
                                            href="{{ route('cookie-policy', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['cookie_policy'] ?? 'Cookie policy' }}</a>
                                    </li>
                                    <li><a
                                            href="{{ route('community-guidelines', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['community_guidelines'] ?? 'Community guidelines' }}</a>
                                    </li>
                                    <li><a
                                            href="{{ route('affiliate-disclosure', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['affiliate_disclosure'] ?? 'Affiliate disclosure' }}</a>
                                    </li>
                                    <li><a
                                            href="{{ route('copyright-dmca-policy', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['copyright_dmca_policy'] ?? 'Copyright & DMCA policy' }}</a>
                                    </li>
                                    <li><a
                                            href="{{ route('legal-notice', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['legal_notice'] ?? 'Legal notice' }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="foot-col footer-dropdown">
                                <p class="footer-title">
                                    {{ $footerContents['businesses'] ?? 'Businesses' }}
                                    <span class="footer-arrow d-flex d-md-none">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </p>
                                <ul class="foot-col-list">
                                    <li>
                                        <a href="{{ route('claim.index', ['locale' => session('lang_code', 'en-us')]) }}">
                                            Claim profile
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <div class="foot-col footer-dropdown">
                            <p class="footer-title">{{ $footerContents['help'] ?? 'Help' }}
                                <span class="footer-arrow d-flex d-md-none">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </p>
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
                                        href="{{ route('who-we-are', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['about-localio'] ?? 'About Localio' }}</a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('contact', ['locale' => session('lang_code', 'en-us')]) }}">{{ $footerContents['contact'] ?? 'Contact' }}</a>
                                </li>
                                {{-- <li>
                                        <a
                                            href="{{ route('FaqsShow', ['locale' => session('lang_code', 'en-us')]) }}">{{ $FAQs['contact'] ?? 'FAQs' }}</a>
                                </li> --}}
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="col-lg-3" style="display: none">
                    <div class="foot-row-right foot-col p_80">
                        <p class="footer-title">{{ $footerContents['follow_us'] ?? '' }}</p>
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
                                    <i class="fa-brands fa-facebook-f" style="color: #ffffff; font-size: 30px;"></i>
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
                                    <i class="fa-brands fa-instagram" style="color: #ffffff; font-size: 30px;"></i>
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
                                    <i class="fa-brands fa-twitter" style="color: #ffffff; font-size: 30px;"></i>
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
                    <p>©<?php echo date('Y'); ?> Localio. {{ static_text('all_rights_reserved_text') ?: 'All rights reserved.' }}</p>
                </div>
                <div class="ft-btm-rgt">
                    <div class="select-menu ">
                        <div class="select-btn" onclick="event.preventDefault(); event.stopPropagation(); toggleCountryModal();" style="cursor: pointer;">
                            <span class="sBtn-text">
                                {{ session('lang_code') && session('lang_name') ? ucfirst(session('lang_name')) : 'United States - English' }}
                            </span>
                            <i class="fa-solid fa-chevron-down" style="color: #ffffff;"></i>
                        </div>
                        <div class="country-modal-backdrop" id="countryModalBackdrop" onclick="event.preventDefault(); event.stopPropagation(); closeCountryModal();"></div>
                        <div class="options">
                            <div class="container">
                                <div class="country-modal-header d-flex align-items-center justify-content-between">
                                    <h3 style="color: #1e3050; margin: 0; font-weight: 700; font-size: 17px;">{{ static_text('choose_country_region_heading') ?: 'Choose your Country or region' }}</h3>
                                    <button type="button" class="close-country-menu" onclick="event.preventDefault(); event.stopPropagation(); closeCountryModal();" aria-label="Close modal" title="Close">
                                        ✕
                                    </button>
                                </div>
                                <ul class="footer-langs-container" style="list-style: none; padding-left: 0;">
                                    @php
                                        $footerCountriesList = (isset($footer_countries) && is_object($footer_countries)) ? $footer_countries->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE) : ($layoutChrome['footerCountriesCached'] ?? \App\Models\Country::with('language')->get()->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE));
                                    @endphp
                                    @foreach ($footerCountriesList as $country)
                                    @if($country->language)
                                    <li>
                                        <a href="{{ url('/' . strtolower($country->language->lang_code)) }}" onclick="document.cookie='user_locale={{ strtolower($country->language->lang_code) }};path=/;max-age=31536000';document.cookie='lang_code={{ strtolower($country->language->lang_code) }};path=/;max-age=31536000';">
                                            {{ $country->name }}
                                        </a>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </footer>
    @php
    $sections = $layoutChrome['learnMoreSections'] ?? collect();
    $sectionsAvailable = $sections->isNotEmpty();
    @endphp

    
    <div class="modal-overlay" id="learnMoreModal">
        <div class="circle_11">
            <button class="modal-close" onclick="closeModal()" aria-label="Close">✕</button>
            <div class="modal-content" style="position: relative !important; transform: none !important; top: auto !important; left: auto !important; display: block !important; opacity: 1 !important; visibility: visible !important; width: 100% !important; height: auto !important; overflow: visible !important;">
                <div class="modal-header" style="display: block !important; margin-bottom: 15px;">
                    <h2 class="modal-title" style="width: max-content; font-size: 20px; font-weight: 700; color: #1e3050; margin: 0;">
                        How Localio helps you choose
                    </h2>
                </div>

                <div class="modal-body" style="display: block !important; padding: 0;">
                    <p style="font-size: 15px; color: #4a5568; line-height: 1.6; margin-bottom: 20px;">
                        Localio brings together business information, user reviews, ratings, comparisons, and community discussions to help you explore your options and make informed decisions.
                    </p>
                </div>

                <div class="modal-disclaimer" style="display: block !important; background-color: #f1f5f9; padding: 15px 20px; border-radius: 8px; font-size: 14px; color: #4a5568; line-height: 1.5; margin-bottom: 20px;">
                    <strong>{{ static_text('disclosure_label') ?: 'Disclosure:' }}</strong> We may earn a commission from some links. Commercial relationships do not affect user reviews or user-generated ratings.
                </div>

                <div style="display: block !important;">
                    <a class="btn-g-link" href="{{ url('/affiliate-disclosure') }}" style="color: #ff5722; font-weight: 600; text-decoration: none;">
                        Read our Affiliate disclosure
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- How Rankings Work Modal -->
    <div class="modal-overlay" id="howRankingsWorkModal">
        <div class="circle_11">
            <button class="modal-close" onclick="closeRankingsModal()" aria-label="Close">✕</button>
            <div class="modal-content">
                <div class="modal-header" style="display: block">
                    <h2 class="modal-title" style="width: max-content">
                        Real experiences, transparent rankings
                    </h2>
                </div>

                <div class="modal-body" style="display: block; padding: 0;">
                    <p style="margin-bottom: 12px; font-size: 14px; line-height: 1.6; color: #4b5563;">
                        Localio brings together ratings and reviews shared by users from the Localio community about their experiences with businesses and products.
                    </p>

                    <p style="margin-bottom: 16px; font-size: 14px; line-height: 1.6; color: #4b5563;">
                        Each review reflects the individual experience and opinion of the person who submitted it. Our commercial relationships with businesses do not influence user ratings or reviews, and businesses cannot pay to change, remove, or improve them.
                    </p>

                    <h2 class="modal-title" style="width: max-content; font-size: 1.25rem; margin-top: 20px; margin-bottom: 8px; font-weight: 700; color: #1e3050;">
                        How this list is ranked
                    </h2>

                    <p style="margin-bottom: 16px; font-size: 14px; line-height: 1.6; color: #4b5563;">
                        Businesses on this page are ranked using factors such as user ratings, review volume, recency, relevance, and other quality signals. Commercial relationships may also influence the order in which businesses appear, but they never affect a business's user rating.
                    </p>

                    <div class="mt-3">
                        <a class="btn-g-link" href="{{ route('community-guidelines', ['locale' => session('lang_code', 'en-us')]) }}">{{ static_text('community_guidelines_link_text') ?: 'Learn more in our Community guidelines' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Updated Modal Structure -->
    <div id="login-modal"
        class=" login_modal modal-overlay fixed inset-0 bg-opacity-40 z-[9999] items-center justify-center p-4 login_modal"
        role="dialog" aria-labelledby="modal-title" aria-hidden="true" style="display: none;">

        <div class="modal-content bg-white rounded-2xl relative" style="width: 380px; padding: 48px 40px 40px 40px;">

            <button id="close-login-btn" class="close-btn" aria-label="Close modal">
                ✕
            </button>

            <div class="text-center">
                <div class="hd_text">
                    <h2 id="modal-title" class="modal-title text-center">{{ static_text('sign_in_or_create_account_title') ?: 'Sign in or create an account' }}</h2>
                </div>
                <p class="text-center" style="margin-bottom: 20px;">{{ static_text('continue_with_google_button') ?: 'Continue with Google' }} or use your email and password.
                </p>

                <div class="button">
                    <div class="login-container-wrapper">
                        <div class="container">
                            <div class="contact_content" data-aos="fade-up" data-aos-duration="1000">

                                <div class="scl_login">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12">
                                            <div class="login_box size18" style="text-align: center;">
                                                <div class="l_goin1">
                                                    <a href="{{ route('google.login') }}" class="login_link"
                                                        onclick="openGoogleLogin(event)"
                                                        style="background: #DB4437; display: flex; align-items: center; justify-content: center; width: 100%; border-radius: 4px; padding: 12px; color: #fff; text-decoration: none; font-weight: 500;">
                                                        <span class="scl-icn"
                                                            style="margin-right: 12px; font-size: 20px;"><i
                                                                class="fa-brands fa-google"></i></span>
                                                        <span>{{ static_text('continue_with_google_button') ?: 'Continue with Google' }}</span>
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
                                @if ($errors->has('g-recaptcha-response'))
                                <div class="text-danger mb-2">{{ $errors->first('g-recaptcha-response') }}</div>
                                @endif
                                @if (session('error'))
                                <div class="text-danger mb-2">{{ session('error') }}</div>
                                @endif
                                <!-- Continue with the rest of your form -->
                                <form class="login_form" action="{{ route('login_process') }}" method="POST"
                                    autocomplete="off">
                                    @csrf

                                    <input type="hidden" name="url_intended" id="url_intended" value="">
                                    <input type="hidden" name="is_modal" value="1">

                                    <div class="form-group">
                                        <x-google-input type="text" name="email" id="emailAddress" label="Email"
                                            :attributes="['autocomplete' => 'off']" />
                                    </div>
                                    <div class="form-group">
                                        {{-- <input type="password" class="form-control" id="password" name="password" placeholder="Password"> --}}
                                        <x-google-input type="password" name="password" id="password" label="Password"
                                            :attributes="['autocomplete' => 'off']" />

                                        <span id="togglePassword" class="eye-icon">
                                            <i class="fa fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <div class="form-row align-items-center">
                                        <div class="col frgt_btn text-right">
                                            <a href="{{ route('recover-password') }}" class="small"
                                                onmouseover="this.style.color='#f9633b'"
                                                onmouseout="this.style.color='#06498b'"
                                                style="margin-top: -6px;">{{ static_text('forgot_password') }}</a>
                                        </div>
                                    </div>
                                    <div class="accor-btn">
                                        <button type="submit" class="blue-btn cta cta_white">Continue</button>
                                    </div>
                                    <p class="recaptcha-branding-text text-center text-muted mt-3 mb-0" style="font-size: 11px; line-height: 1.4; color: #8c98a4;">
                                        Protected by reCAPTCHA (<a href="https://policies.google.com/privacy" target="_blank" style="color: #06498b; text-decoration: underline;">Privacy</a> &middot; <a href="https://policies.google.com/terms" target="_blank" style="color: #06498b; text-decoration: underline;">Terms</a>)
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- register model pop up 30-07-2026 -->
    @if(session()->has('register_profile_needed'))
    @php
    $pendingEmail = session('register_email', '');
    $pendingFirstName = session('register_first_name', '');
    $pendingLastName = session('register_last_name', '');

    @endphp
    <div id="register-profile-modal"
        class="modal-overlay fixed inset-0 z-[9999] align-items-center justify-content-center p-3"
        style="background: rgba(0, 0, 0, 0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto; display: flex;">
        <div class="modal-content bg-white shadow-lg relative border-0 my-auto"
            style="max-width: 620px; width: 100%; padding: 40px 36px 36px 36px; border-radius: 16px !important; background: #ffffff; position: relative;">
            <button type="button" id="close-profile-modal-btn" aria-label="Close modal"
                style="position: absolute; top: 14px; right: 14px; border: none; background: transparent;cursor: pointer; color: #64748b; line-height: 1; z-index: 10;">
                ✕
            </button>


            <div class="text-center mb-4">
                <div class="hd_text">
                    <h2 class="text-center">{{ static_text('create_your_profile_title') ?: 'Create your profile' }}</h2>
                </div>
                <p class="text-muted m-0" style="font-size: 13.5px;">Please provide a few more details to set up your
                    account.</p>
            </div>

            <form class="register_form" action="{{ route('register.details.store', ['locale'=> getCurrentLocale()]) }}"
                method="post" id="modalRegisterDetailsForm">
                @csrf
                @if ($errors->has('g-recaptcha-response'))
                    <div class="alert alert-danger mb-3">{{ $errors->first('g-recaptcha-response') }}</div>
                @endif
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="text" name="first_name" id="modalFirstName" class="form-control"
                                placeholder="{{ static_text('first_name_placeholder') ?: 'First name' }}" value="{{ old('first_name', $pendingFirstName) }}" required>
                            <label for="modalFirstName">First name</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="text" name="last_name" id="modalLastName" class="form-control"
                                placeholder="{{ static_text('last_name_placeholder') ?: 'Last name' }}" value="{{ old('last_name', $pendingLastName) }}" required>
                            <label for="modalLastName">Last name</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="job_title" id="modalJobTitle" class="form-control" placeholder="{{ static_text('job_title_placeholder') ?: 'Job title' }}"
                        value="{{ old('job_title') }}" required>
                    <label for="modalJobTitle">Job title</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="company_size" id="modalCompanySize" class="form-select" required>
                        <option value="" selected disabled hidden></option>
                        <option value="1" {{ old('company_size') == '1' ? 'selected' : '' }}>Freelance / Solo</option>
                        <option value="2" {{ old('company_size') == '2' ? 'selected' : '' }}>Small Business (1-50 emp.)
                        </option>
                        <option value="3" {{ old('company_size') == '3' ? 'selected' : '' }}>Mid-Market (51-1000 emp.)
                        </option>
                        <option value="4" {{ old('company_size') == '4' ? 'selected' : '' }}>Enterprise (&gt;1000 emp.)
                        </option>
                    </select>
                    <label for="modalCompanySize">{{ static_text('company_size_dropdown_label') ?: 'Company size' }}</label>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-check ">
                            <input class="form-check-input" type="checkbox" name="accept_terms" id="acceptTerms"
                                value="1" {{ old('accept_terms') ? 'checked' : '' }} required>

                            <label class="form-check-label" for="acceptTerms">
                                I agree to the
                                <a href="{{ route('terms-condition', ['locale' => getCurrentLocale()]) }}"
                                    target="_blank" class="policy-link">
                                    Terms of service
                                </a>
                                and acknowledge the
                                <a href="{{ route('privacy-policy', ['locale' => getCurrentLocale()]) }}"
                                    target="_blank" class="policy-link">
                                    Privacy policy
                                </a>.
                            </label>
                            @error('accept_terms')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="promotional_emails"
                                id="promotionalEmails" value="1" {{ old('promotional_emails') ? 'checked' : '' }}>
                            <label class="form-check-label" for="promotionalEmails">
                                I'd like to receive promotional emails from Localio. I can unsubscribe at any time.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="accor-btn mt-4">
                    <button type="submit" class="blue-btn cta cta_white register_details_btn w-100 py-3 "
                        style="text-transform:unset !important; background-color: #06498b; color: white; border-radius: 30px; font-size: 15px; transition: background 0.2s;">Sign
                        up</button>
                </div>
                <p class="recaptcha-branding-text text-center text-muted mt-3 mb-0" style="font-size: 11px; line-height: 1.4; color: #8c98a4;">
                    Protected by reCAPTCHA (<a href="https://policies.google.com/privacy" target="_blank" style="color: #06498b; text-decoration: underline;">Privacy</a> &middot; <a href="https://policies.google.com/terms" target="_blank" style="color: #06498b; text-decoration: underline;">Terms</a>)
                </p>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.getElementById('close-profile-modal-btn');

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                const modal = document.getElementById('register-profile-modal');
                if (modal) {
                    modal.style.display = 'none';
                }
                if (typeof window.updateRecaptchaVisibility === 'function') {
                    window.updateRecaptchaVisibility();
                } else if (document.body) {
                    document.body.classList.remove('recaptcha-badge-visible');
                }

                const url = document.querySelector('meta[name="clear-session-url"]').content;
                const token = document.querySelector('meta[name="csrf-token"]').content;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Session cleared:', data);
                    })
                    .catch(error => {
                        console.error('Error clearing session:', error);
                    });
            });
        }
    });
    </script>
    @endif

    <!-- register model pop end here -->
    @stack('scripts')

    {{-- login script --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ✅ SCOPED TO MODAL ONLY - target the modal container first
        const loginModal = document.getElementById('login-modal');
        if (!loginModal) return;

        // ✅ All elements are now scoped within the modal with more flexible selectors
        const form = loginModal.querySelector('.login_form');
        const emailInput = loginModal.querySelector('input[name="email"]') || loginModal.querySelector(
            '#emailAddress');
        const passwordInput = loginModal.querySelector('input[name="password"]') || loginModal.querySelector(
            '#password');
        const togglePassword = loginModal.querySelector('#togglePassword');






        // ✅ Toggle password visibility (with better error handling)
        if (togglePassword && passwordInput) {
            togglePassword?.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.querySelector('i');
                if (!icon || !passwordInput) return;

                const isPassword = passwordInput.getAttribute('type') === 'password';

                // Use setTimeout to avoid DOM/reactive reversion
                setTimeout(() => {
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.classList.toggle('fa-eye', isPassword);
                    icon.classList.toggle('fa-eye-slash', !isPassword);
                }, 0);
            });
        }

        // ✅ Form validation (scoped to modal form only)
        form.addEventListener('submit', function(e) {
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
                document.body.classList.add('recaptcha-badge-visible');
                if (typeof window.updateRecaptchaVisibility === 'function') window.updateRecaptchaVisibility();

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
                if (typeof window.updateRecaptchaVisibility === 'function') {
                    window.updateRecaptchaVisibility();
                } else {
                    document.body.classList.remove('recaptcha-badge-visible');
                }
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
            document.body.classList.add('recaptcha-badge-visible');
            if (typeof window.updateRecaptchaVisibility === 'function') window.updateRecaptchaVisibility();

            const closeBtn = document.getElementById('close-login-btn');
            setTimeout(() => {
                closeBtn?.focus();
            }, 300);
        }
    }
    </script>

    <!-- read section end -->
    <!-- AddToAny BEGIN -->
    <script>
window.addEventListener('load', function() {
    var a2aScript = document.createElement('script');
    a2aScript.src = 'https://static.addtoany.com/menu/page.js';
    a2aScript.async = true;
    document.body.appendChild(a2aScript);
});
</script>
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

    // How Rankings Work Modal Functions
    function openRankingsModal() {
        const modal = document.getElementById('howRankingsWorkModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeRankingsModal() {
        const modal = document.getElementById('howRankingsWorkModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }

    // Close rankings modal when clicking outside
    document.getElementById('howRankingsWorkModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRankingsModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeRankingsModal();
        }
    });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"
        integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>

    <script src="{{ asset('front/js/script.js') }}?v=5" defer></script>
    <script src="{{ asset('front/js/tracking.js') }}?v=5" defer></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof AOS !== 'undefined') {
            AOS.init();
        }

        document.querySelectorAll('.footer-title').forEach(function(item) {
            item.addEventListener('click', function() {
                this.closest('.footer-dropdown').classList.toggle('active');
            });
        });
    });

    function changeCategory(categoryId) {
        let langCode = "{{ session('lang_code', 'en-us') }}";
        let url = new URL(window.location.href);
        url.pathname = `/${langCode}/${categoryId}`;
        window.location.href = url.href;
    }

    function changeProducts(ProductId) {
        let langCode = "{{ session('lang_code', 'en-us') }}";
        let url = new URL(window.location.href);
        url.pathname = `/${langCode}/${ProductId}`;
        window.location.href = url.href;
    }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectBtn = document.querySelector('.select-btn');
        const options = document.querySelectorAll('.option');
        const sBtnText = document.querySelector('.sBtn-text');

        options.forEach(option => {
            if (option && !option.classList.contains('selected')) {
                const selectedName = option.dataset.langName;
                if (selectedName && sBtnText) {
                    sBtnText.textContent = selectedName;
                }
            }
        });
    });
    </script>
    <!-- header search js -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function checkScroll() {
            const $myElement = $('#myID');
            @if(Route::currentRouteName() === 'top-rated-product' || Route::currentRouteName() ===
                'category.detail')
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
    <!-- SweetAlert2, noUiSlider, Swiper JS moved from <head> to end of body to eliminate render-blocking -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider@14.6.3/distribute/nouislider.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>

    @session('success')
    <script>
    @if(Session::has('success'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: @json(session('success')),
                position: 'top-right',
                toast: true,
                showConfirmButton: false,
                timer: 3000,
            });
        }
    });
    @endif
    </script>
    @endsession
    <script>
    @if(Session::has('error'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: @json(session('error')),
                position: 'top-right',
                toast: true,
                showConfirmButton: false,
                timer: 3000,
            });
        }
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
    function toggleCountryModal() {
        var menu = document.querySelector('.select-menu');
        if (!menu) return;
        if (menu.classList.contains('active') || menu.classList.contains('open')) {
            closeCountryModal();
        } else {
            openCountryModal();
        }
    }

    function openCountryModal() {
        var menu = document.querySelector('.select-menu');
        if (menu) {
            menu.classList.add('open', 'active');
        }
        document.body.classList.add('country-modal-open');
    }

    function closeCountryModal() {
        var menu = document.querySelector('.select-menu');
        if (menu) {
            menu.classList.remove('open', 'active');
        }
        document.body.classList.remove('country-modal-open');
    }

    window.toggleCountryModal = toggleCountryModal;
    window.openCountryModal = openCountryModal;
    window.closeCountryModal = closeCountryModal;

    document.addEventListener('DOMContentLoaded', function() {
        // Delegated click handler on select-btn
        $(document).on('click', '.select-btn', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            toggleCountryModal();
        });

        // Close button click on top right of country selection modal
        $(document).on('click', '.close-country-menu', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            closeCountryModal();
        });

        // Click on dark upper top backdrop to close
        $(document).on('click', '.country-modal-backdrop', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            closeCountryModal();
        });

        @if(session('show_country_modal'))
            openCountryModal();
        @endif

        // Close modal if user clicks outside of modal options and outside of select button
        $(document).on('click', function(event) {
            var menu = document.querySelector('.select-menu');
            if (menu && (menu.classList.contains('active') || menu.classList.contains('open'))) {
                if (!event.target.closest('.options') && !event.target.closest('.select-btn')) {
                    closeCountryModal();
                }
            }
        });

        // Close on Escape key press
        $(document).on('keydown', function(event) {
            if (event.key === 'Escape' || event.keyCode === 27) {
                closeCountryModal();
            }
        });
    });
    </script>

    <script>
    document.addEventListener('livewire:load', function() {
        window.Livewire.on('swal:success', function(data) {
            if ($.isArray(data)) {
                data = data[0];
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
    document.addEventListener('DOMContentLoaded', function() {
        $('.menu-item').on('click', function() {
            if ($('.menu-item.menu-active').length > 0) {
                $('body').addClass('body_hiden');
            } else {
                $('body').removeClass('body_hiden');
            }
        });

        $(document).on('click', function(event) {
            if ($('body').hasClass('body_hiden')) {
                if ($('.menu-item.menu-active').length === 0) {
                    $('body').removeClass('body_hiden');
                }
            }
        });
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
                @if (Auth::user()->profile_image && Auth::user()->profile_image !== 'front/img/default.png')
                <img src="{{ asset(Auth::user()->profile_image) }}" alt="Profile" class="profile-circle"
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

            <button class="category-sidebar-close" id="categories-sidebar-close" aria-label="Close categories sidebar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="category-sidebar-viewport">
            <div class="category-sidebar-panels-container" id="sidebar-panels-container">
                <!-- Main Panel -->
                <div class="category-sidebar-panel active" id="main-panel">
                    <div class="sidebar-menu-section d-none">
                        <h3 class="sidebar-section-title">Trending</h3>
                        <ul class="sidebar-menu-list">
                            <li>
                                <a href="{{ route('top-rated-product', ['locale' => app()->getLocale()]) }}">
                                     Bestsellers / Top Rated
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- <div class="sidebar-menu-divider"></div> --}}

                    <div class="sidebar-menu-section">
                        <h3 class="sidebar-section-title sidebar-cat">Categories</h3>
                        <ul class="sidebar-menu-list">
                            @foreach($sidebarCategories as $cat)
                            @if($cat->translation)
                            <li>
                                <a href="#category-panel" class="parent-category-item"
                                    data-category-id="{{ $cat->id }}">
                                    {{ $cat->translation->name }}
                                    <i class="fa-solid fa-chevron-right float-end mt-1"></i>
                                </a>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Dynamic Category Sub-panels (lazily hydrated from template to eliminate initial DOM parse TBT) -->
                <template id="desktop-subpanels-tpl">
                @foreach($sidebarCategories as $cat)
                @if($cat->translation)
                <div class="category-sidebar-panel sub-panel" id="sub-panel-{{ $cat->id }}">
                    <div class="sub-panel-back">
                        <a href="#main-panel" class="back-to-main-btn">
                            <i class="fa-solid fa-arrow-left me-2"></i> Categories
                        </a>
                    </div>
                    <div class="sidebar-menu-divider"></div>
                    <div class="sidebar-menu-section">
                        <h3 class="sidebar-section-title">
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $cat->translation->slug]) }}">
                                {{ $cat->translation->name }}
                            </a>
                        </h3>
                        <ul class="sidebar-menu-list">
                            @foreach($cat->subCategories as $subCat)
                            @php
                                $effectiveCat = ($subCat->target_category_id && $subCat->targetCategory) ? $subCat->targetCategory : $subCat;
                                $effectiveTrans = $effectiveCat->translation ?? $subCat->translation;
                            @endphp
                            @if($effectiveTrans)
                            <li>
                                <a
                                    href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $effectiveTrans->slug]) }}">
                                    {{ $subCat->translation->name ?? $effectiveTrans->name }}
                                </a>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                @endforeach
                </template>
            </div>
        </div>
    </div>

    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('open-categories-sidebar');
        const closeBtn = document.getElementById('categories-sidebar-close');
        const overlay = document.getElementById('categories-sidebar-overlay');
        const sidebar = document.getElementById('categories-sidebar');
        const container = document.getElementById('sidebar-panels-container');
        const subpanelsTpl = document.getElementById('desktop-subpanels-tpl');
        let desktopSubpanelsHydrated = false;

        function hydrateDesktopSubpanels() {
            if (desktopSubpanelsHydrated || !container || !subpanelsTpl) return;
            container.appendChild(subpanelsTpl.content.cloneNode(true));
            desktopSubpanelsHydrated = true;
        }

        if (openBtn && sidebar && overlay) {
            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                hydrateDesktopSubpanels();
                sidebar.classList.add('show');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }

        function closeSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            setTimeout(() => {
                if (!container) return;
                container.classList.remove('slide-active');
                container.classList.remove('slide-business-active');
                container.querySelectorAll('.category-sidebar-panel.sub-panel').forEach(panel => {
                    panel.classList.remove('active');
                });
                container.querySelectorAll('.category-sidebar-panel.business-panel').forEach(panel => {
                    panel.classList.remove('active');
                    panel.style.display = 'none';
                });
            }, 300);
        }

        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        if (container) {
            container.addEventListener('click', function(e) {
                const parentItem = e.target.closest('.parent-category-item');
                if (parentItem) {
                    hydrateDesktopSubpanels();
                    const catId = parentItem.getAttribute('data-category-id');
                    const targetPanel = document.getElementById('sub-panel-' + catId);
                    container.querySelectorAll('.category-sidebar-panel.sub-panel').forEach(panel => panel.classList.remove('active'));
                    container.querySelectorAll('.category-sidebar-panel.business-panel').forEach(panel => {
                        panel.classList.remove('active');
                        panel.style.display = 'none';
                    });
                    if (targetPanel) {
                        targetPanel.classList.add('active');
                        container.classList.remove('slide-business-active');
                        container.classList.add('slide-active');
                    }
                    return;
                }

                const subItem = e.target.closest('.subcategory-item');
                if (subItem) {
                    const subCatId = subItem.getAttribute('data-subcategory-id');
                    const targetPanel = document.getElementById('business-panel-' + subCatId);
                    container.querySelectorAll('.category-sidebar-panel.business-panel').forEach(panel => {
                        panel.classList.remove('active');
                        panel.style.display = 'none';
                    });
                    if (targetPanel) {
                        targetPanel.style.display = 'block';
                        targetPanel.classList.add('active');
                        container.classList.add('slide-business-active');
                    }
                    return;
                }

                if (e.target.closest('.back-to-main-btn')) {
                    container.classList.remove('slide-active');
                    container.classList.remove('slide-business-active');
                    return;
                }

                const backSubBtn = e.target.closest('.back-to-subcategories-btn');
                if (backSubBtn) {
                    const parentCatId = backSubBtn.getAttribute('data-parent-cat-id');
                    const targetPanel = document.getElementById('sub-panel-' + parentCatId);
                    container.querySelectorAll('.category-sidebar-panel.sub-panel').forEach(panel => panel.classList.remove('active'));
                    if (targetPanel) targetPanel.classList.add('active');
                    container.classList.remove('slide-business-active');
                    container.classList.add('slide-active');
                }
            });
        }
    });
    </script>

    <script>
    /* Conditional negative margin on Top Rated Products mobile business cards based on title line counts */
    function adjustAutomotiveRatings() {
        const cards = document.querySelectorAll('.automotive-card');
        if (!cards.length) return;

        if (window.innerWidth > 991) {
            cards.forEach(card => {
                const el = card.querySelector('.tp-btm');
                if (el) el.style.setProperty('margin-top', '', 'important');
            });
            return;
        }

        cards.forEach(card => {
            const titleEl = card.querySelector('h6.head');
            const ratingEl = card.querySelector('.tp-btm');
            if (!titleEl || !ratingEl) return;

            ratingEl.style.setProperty('margin-top', '', 'important');
            const style = window.getComputedStyle(titleEl);
            const lineHeight = parseFloat(style.lineHeight) || 20;
            const titleHeight = titleEl.getBoundingClientRect().height;

            if (titleHeight <= lineHeight * 1.5) {
                ratingEl.style.setProperty('margin-top', '-1rem', 'important');
            } else {
                ratingEl.style.setProperty('margin-top', '', 'important');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (!document.querySelector('.automotive-card')) return;
        adjustAutomotiveRatings();
        window.addEventListener('load', adjustAutomotiveRatings);
        window.addEventListener('resize', adjustAutomotiveRatings);

        const observer = new MutationObserver(() => {
            observer.disconnect();
            adjustAutomotiveRatings();
            observe();
        });
        function observe() {
            observer.observe(document.body, { childList: true, subtree: true });
        }
        observe();
    });
    </script>



<!-- Global Booking.com-style Share Popover -->
<div id="bookingSharePopover" class="booking-share-popover" style="display: none;" onclick="event.stopPropagation();">
    <div class="booking-share-arrow"></div>
    <div class="booking-share-inner">
        <h4 class="booking-share-title">{{ static_text('share_this_heading') ?: 'Share this' }}</h4>
        <div class="booking-share-list">
            <button type="button" class="booking-share-item" id="btnShareCopyLink" onclick="triggerShareCopy(event)">
                <span class="booking-share-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                </span>
                <span class="booking-share-label">{{ static_text('copy_link_button') ?: 'Copy link' }}</span>
                <span id="shareCopiedFeedback" class="booking-share-copied-badge" style="display: none;">{{ static_text('copied_toast') ?: 'Copied!' }}</span>
            </button>
            <button type="button" class="booking-share-item" id="btnShareFacebook" onclick="triggerShareFacebook(event)">
                <span class="booking-share-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </span>
                <span class="booking-share-label">Facebook</span>
            </a>
            <button type="button" class="booking-share-item" id="btnShareTwitter" onclick="triggerShareTwitter(event)">
                <span class="booking-share-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="#111827">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </span>
                <span class="booking-share-label">Post on X</span>
            </a>
        </div>
    </div>
</div>



<script>
    let currentShareUrl = window.location.href;
    let currentShareTitle = document.title;
    let activeTriggerEl = null;

    window.openShareModal = function(url, title, event) {
        if (event) {
            event.stopPropagation();
            activeTriggerEl = event.currentTarget || event.target.closest('.action-icon-btn, .share-trigger-btn, button, a');
        }
        
        currentShareUrl = url || window.location.href;
        currentShareTitle = title || document.title;
        
        const popover = document.getElementById('bookingSharePopover');
        const badge = document.getElementById('shareCopiedFeedback');
        if (badge) badge.style.display = 'none';
        
        if (!popover) return;
        
        // If already open for this element, toggle close
        if (popover.style.display === 'block' && popover._activeElement === activeTriggerEl) {
            window.closeShareModal();
            return;
        }
        
        popover._activeElement = activeTriggerEl;
        popover.style.display = 'block';
        
        if (activeTriggerEl) {
            const rect = activeTriggerEl.getBoundingClientRect();
            const popoverWidth = 280;
            const popoverHeight = popover.offsetHeight || 190;
            const arrow = popover.querySelector('.booking-share-arrow');
            
            let left = rect.left + (rect.width / 2) - (popoverWidth / 2);
            // Ensure within window bounds
            if (left < 10) left = 10;
            if (left + popoverWidth > window.innerWidth - 10) {
                left = window.innerWidth - popoverWidth - 10;
            }
            
            let top = rect.bottom + 8;
            let isBottom = false;
            
            // If overflowing viewport bottom, position above button
            if (top + popoverHeight > window.innerHeight - 10 && rect.top - popoverHeight - 8 > 10) {
                top = rect.top - popoverHeight - 8;
                isBottom = true;
            }
            
            popover.style.left = `${left}px`;
            popover.style.top = `${top}px`;
            
            if (arrow) {
                if (isBottom) {
                    arrow.classList.add('arrow-bottom');
                } else {
                    arrow.classList.remove('arrow-bottom');
                }
                const arrowCenter = rect.left + (rect.width / 2) - left;
                arrow.style.left = `${Math.max(16, Math.min(popoverWidth - 28, arrowCenter - 6))}px`;
            }
        } else {
            // Fallback center
            popover.style.left = '50%';
            popover.style.top = '50%';
            popover.style.transform = 'translate(-50%, -50%)';
        }
    };

    window.closeShareModal = function() {
        const popover = document.getElementById('bookingSharePopover');
        if (popover) {
            popover.style.display = 'none';
            popover._activeElement = null;
        }
    };

    window.triggerShareCopy = async function(event) {
        if (event) event.stopPropagation();
        const badge = document.getElementById('shareCopiedFeedback');
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(currentShareUrl);
            } else {
                let ta = document.createElement('textarea');
                ta.value = currentShareUrl;
                ta.style.position = 'fixed';
                ta.style.left = '-999999px';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
            }
            if (badge) {
                badge.style.display = 'inline-block';
                setTimeout(() => {
                    closeShareModal();
                }, 1000);
            }
        } catch(e) {
            console.error('Failed to copy', e);
        }
    };

    window.triggerShareFacebook = function(event) {
        if (event) event.stopPropagation();
        const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentShareUrl)}`;
        window.open(url, '_blank', 'width=600,height=450');
        closeShareModal();
    };

    window.triggerShareTwitter = function(event) {
        if (event) event.stopPropagation();
        const url = `https://twitter.com/intent/tweet?url=${encodeURIComponent(currentShareUrl)}&text=${encodeURIComponent(currentShareTitle)}`;
        window.open(url, '_blank', 'width=600,height=450');
        closeShareModal();
    };

    // Global click outside listener
    document.addEventListener('click', function(e) {
        const popover = document.getElementById('bookingSharePopover');
        if (popover && popover.style.display === 'block') {
            if (!popover.contains(e.target) && !e.target.closest('.share-trigger-btn, .action-icon-btn')) {
                window.closeShareModal();
            }
        }
    });

    // Close on scroll or escape
    window.addEventListener('scroll', function() {
        window.closeShareModal();
    }, { passive: true });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeShareModal();
        }
    });
</script>



<script>
    function openMobileFilterDrawer() {
        const drawer = document.querySelector('.auto-choice-lft');
        const backdrop = document.querySelector('.mobile-filter-backdrop');
        if (drawer) drawer.classList.add('mobile-filter-active');
        if (backdrop) backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileFilterDrawer() {
        const drawer = document.querySelector('.auto-choice-lft');
        const backdrop = document.querySelector('.mobile-filter-backdrop');
        if (drawer) drawer.classList.remove('mobile-filter-active');
        if (backdrop) backdrop.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeMobileFilterDrawer();
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) closeMobileFilterDrawer();
    });
</script>
    @include('partials.recaptcha-v3')
</body>

</html>
{{-- mass determation--}}
