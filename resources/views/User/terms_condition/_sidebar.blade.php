@php
    $currentRoute = Route::currentRouteName();
    $legalNavItems = [
        [
            'title' => 'Terms of service',
            'route' => 'terms-condition',
            'slug'  => 'terms-of-service',
        ],
        [
            'title' => 'Privacy policy',
            'route' => 'privacy-policy',
            'slug'  => 'privacy-policy',
        ],
        [
            'title' => 'Cookie policy',
            'route' => 'cookie-policy',
            'slug'  => 'cookie-policy',
        ],
        [
            'title' => 'Community guidelines',
            'route' => 'community-guidelines',
            'slug'  => 'community-guidelines',
        ],
        [
            'title' => 'Affiliate disclosure',
            'route' => 'affiliate-disclosure',
            'slug'  => 'affiliate-disclosure',
        ],
        [
            'title' => 'Copyright & DMCA policy',
            'route' => 'copyright-dmca-policy',
            'slug'  => 'copyright-dmca-policy',
        ],
        [
            'title' => 'Legal notice',
            'route' => 'legal-notice',
            'slug'  => 'legal-notice',
        ],
    ];
@endphp

<style>
    .policy-nav .legal-sidebar-menu {
        list-style: none;
        padding-left: 0;
    }
    .policy-nav .legal-sidebar-menu li {
        margin-bottom: 14px;
    }
    .policy-nav .legal-sidebar-menu li a {
        font-size: 16px;
        color: #334155;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease, font-weight 0.2s ease;
        display: block;
    }
    .policy-nav .legal-sidebar-menu li a:hover {
        color: #2563eb;
    }
    .policy-nav .legal-sidebar-menu li.active a,
    .policy-nav .legal-sidebar-menu li a.active {
        font-weight: 700;
        color: #2563eb;
    }
</style>

<div class="policy-nav">
    <ul class="legal-sidebar-menu">
        @foreach ($legalNavItems as $item)
            @php
                $isActive = ($currentRoute == $item['route']) || (isset($activeSlug) && $activeSlug == $item['slug']);
            @endphp
            <li class="{{ $isActive ? 'active' : '' }}">
                <a href="{{ route($item['route'], ['locale' => session('lang_code', 'en-us')]) }}" 
                   class="{{ $isActive ? 'active' : '' }}">
                    {{ $item['title'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
