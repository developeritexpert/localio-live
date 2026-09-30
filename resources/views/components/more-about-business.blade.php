@props(['business', 'current' => null, 'hide' => null, 'active' => null])

@php
    $lang_id = getCurrentLanguageID();
    $bTranslation = $business->translations->firstWhere('language_id', $lang_id) ?? $business->translations->first();
    $bName = $bTranslation->name ?? $business->name ?? 'Business';
    $bSlug = $bTranslation->slug ?? $business->slug ?? '';
    $currentLocale = app()->getLocale();
    $languageObj = \App\Models\Language::where('lang_code', $currentLocale)->first();
    $expectedFaqSlug = !empty($languageObj->faq_slug) ? $languageObj->faq_slug : 'faqs';
    $expectedAlternativesSlug = !empty($languageObj->alternatives_slug) ? $languageObj->alternatives_slug : 'alternatives';
    $expectedComparisonsSlug = !empty($languageObj->comparisons_slug) ? $languageObj->comparisons_slug : 'comparisons';
    $expectedReviewsSlug = !empty($languageObj->reviews_slug) ? $languageObj->reviews_slug : 'reviews';
    $expectedDiscussionsSlug = !empty($languageObj->discussions_slug) ? $languageObj->discussions_slug : 'discussions';

    // Normalize explicit prop if provided
    $rawKey = strtolower(trim((string)($hide ?? $current ?? $active ?? '')));
    $targetHide = '';
    if (in_array($rawKey, ['faq', 'faqs'])) {
        $targetHide = 'faqs';
    } elseif (in_array($rawKey, ['alternative', 'alternatives'])) {
        $targetHide = 'alternatives';
    } elseif (in_array($rawKey, ['compare', 'comparisons', 'comparison'])) {
        $targetHide = 'compare';
    } elseif (in_array($rawKey, ['review', 'reviews'])) {
        $targetHide = 'reviews';
    } elseif (in_array($rawKey, ['discussion', 'discussions', 'community'])) {
        $targetHide = 'discussions';
    } elseif (in_array($rawKey, ['overview', 'profile', 'detail'])) {
        $targetHide = 'overview';
    }

    // 1. Auto-detect from Route Name
    if (!$targetHide) {
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName() ?? '';
        if ($routeName === 'business.all_faqs') {
            $targetHide = 'faqs';
        } elseif ($routeName === 'business.alternatives') {
            $targetHide = 'alternatives';
        } elseif ($routeName === 'business.all_comparisons') {
            $targetHide = 'compare';
        } elseif ($routeName === 'ReviewShow') {
            $targetHide = 'reviews';
        } elseif (in_array($routeName, ['business.discussions', 'business.discussions_slug'])) {
            $targetHide = 'discussions';
        } elseif ($routeName === 'user.product_detail') {
            $targetHide = 'overview';
        }
    }

    // 2. Auto-detect from Controller Action (if route was matched by wildcard handler)
    if (!$targetHide) {
        $actionName = \Illuminate\Support\Facades\Route::currentRouteAction() ?? '';
        if (str_contains($actionName, 'businessFaqs')) {
            $targetHide = 'faqs';
        } elseif (str_contains($actionName, 'businessAlternatives')) {
            $targetHide = 'alternatives';
        } elseif (str_contains($actionName, 'allBusinessComparisons')) {
            $targetHide = 'compare';
        } elseif (str_contains($actionName, 'allReview')) {
            $targetHide = 'reviews';
        } elseif (str_contains($actionName, 'businessDiscussions')) {
            $targetHide = 'discussions';
        }
    }

    // 3. Auto-detect from URL segments / route parameters
    if (!$targetHide) {
        $routeSecondSeg = request()->route('second_segment');
        $segments = request()->segments();
        $lastSeg = !empty($segments) ? end($segments) : '';
        $checkSeg = $routeSecondSeg ?: $lastSeg;

        if ($checkSeg) {
            if (in_array($checkSeg, [$expectedFaqSlug, 'faqs', 'faq'])) {
                $targetHide = 'faqs';
            } elseif (in_array($checkSeg, [$expectedAlternativesSlug, 'alternatives'])) {
                $targetHide = 'alternatives';
            } elseif (in_array($checkSeg, [$expectedComparisonsSlug, 'comparisons', 'compare'])) {
                $targetHide = 'compare';
            } elseif (in_array($checkSeg, [$expectedReviewsSlug, 'reviews', 'all-review'])) {
                $targetHide = 'reviews';
            } elseif (in_array($checkSeg, [$expectedDiscussionsSlug, 'discussions', 'discussion', 'community'])) {
                $targetHide = 'discussions';
            }
        }
    }

    // 4. Auto-detect Livewire context (e.g. BusinessAlternatives component)
    if (!$targetHide && isset($this) && $this instanceof \App\Livewire\BusinessAlternatives) {
        $targetHide = 'alternatives';
    }

    $currentUrlNorm = rtrim(url()->current(), '/');

    $overviewUrl = route('user.product_detail', ['locale' => $currentLocale, 'id' => $bSlug]);
    $alternativesUrl = route('business.alternatives', ['locale' => $currentLocale, 'business_slug' => $bSlug, 'alternatives_slug' => $expectedAlternativesSlug]);
    $faqsUrl = route('business.all_faqs', ['locale' => $currentLocale, 'business_slug' => $bSlug, 'faq_slug' => $expectedFaqSlug]);
    $compareUrl = route('business.all_comparisons', ['locale' => $currentLocale, 'business_slug' => $bSlug]);
    $reviewsUrl = route('ReviewShow', ['locale' => $currentLocale, 'slug' => $bSlug, 'reviews_slug' => $expectedReviewsSlug]);
    $discussionsUrl = (!empty($languageObj->discussions_slug) && $languageObj->discussions_slug !== 'discussions')
        ? route('business.discussions_slug', ['locale' => $currentLocale, 'business_slug' => $bSlug, 'discussions_slug' => $expectedDiscussionsSlug])
        : route('business.discussions', ['locale' => $currentLocale, 'business_slug' => $bSlug]);

    $hideOverview = ($targetHide === 'overview') || ($currentUrlNorm === rtrim($overviewUrl, '/'));
    $hideAlternatives = ($targetHide === 'alternatives') || ($currentUrlNorm === rtrim($alternativesUrl, '/'));
    $hideFaqs = ($targetHide === 'faqs') || ($currentUrlNorm === rtrim($faqsUrl, '/'));
    $hideCompare = ($targetHide === 'compare') || ($currentUrlNorm === rtrim($compareUrl, '/'));
    $hideReviews = ($targetHide === 'reviews') || ($currentUrlNorm === rtrim($reviewsUrl, '/'));
    $hideDiscussions = ($targetHide === 'discussions') || ($currentUrlNorm === rtrim($discussionsUrl, '/'));
@endphp

<div class="boxshadow_border bg-white p-4 more-about-business-card" style="border-radius: 16px !important;">
    <div class="pb-2 mb-1" style="border-bottom: 1px solid #f0f0f0;">
        <p class="m-0 card-h-title">{{ static_text('more_about_business_heading') ?: 'More about' }} {{ $bName }}</p>
    </div>

    <div class="more-about-links-list d-flex flex-column">

        {{-- Overview --}}
        @if(!$hideOverview)
        <a href="{{ $overviewUrl }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/overview.svg') }}" alt="Overview">
                </span>
                <span class="more-about-link-text">{{ $bName }} profile</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        @endif

        {{-- Alternatives --}}
        @if(!$hideAlternatives)
        <a href="{{ $alternativesUrl }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/alternatives.svg') }}" alt="Alternatives">
                </span>
                <span class="more-about-link-text">{{ static_text('tab_alternatives') ?: 'Alternatives' }}</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        @endif

        {{-- FAQs --}}
        @if(!$hideFaqs)
        <a href="{{ $faqsUrl }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/FAQs.svg') }}" alt="FAQs">
                </span>
                <span class="more-about-link-text">{{ static_text('tab_faqs') ?: 'FAQs' }}</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        @endif

        {{-- Compare --}}
        @if(!$hideCompare)
        <a href="{{ $compareUrl }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/compare.svg') }}" alt="Compare">
                </span>
                <span class="more-about-link-text">{{ static_text('tab_compare') ?: 'Compare' }}</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        @endif

        {{-- Reviews --}}
        @if(!$hideReviews)
        <a href="{{ $reviewsUrl }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/reviews.svg') }}" alt="Reviews">
                </span>
                <span class="more-about-link-text">{{ static_text('tab_reviews') ?: 'Reviews' }}</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        @endif

        {{-- Discussions --}}
        @if(!$hideDiscussions)
        <a href="{{ $discussionsUrl }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/discussions.svg') }}" alt="Discussions">
                </span>
                <span class="more-about-link-text">{{ static_text('tab_discussions') ?: 'Discussions' }}</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        @endif

    </div>
</div>
