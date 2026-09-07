@props(['business'])

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
@endphp

<style>
    .more-about-business-card {
        border-radius: 16px !important;
        /* box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04) !important; */
    }
    .more-about-link-item {
        color: #1e3050;
        transition: all 0.2s ease;
        padding: 11px 4px;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .more-about-link-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .more-about-link-item:first-child {
        padding-top: 0;
    }
    .more-about-link-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .more-about-link-icon {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .more-about-link-icon img {
        width: 28px;
        height: 28px;
    }
    .more-about-link-text {
        font-size: 14px;
        font-weight: 500;
        color: #002347;
    }
    .more-about-link-arrow {
        font-size: 14px;
        color: #9aacbe;
        transition: transform 0.2s ease, color 0.2s ease;
        flex-shrink: 0;
    }
    .more-about-link-item:hover .more-about-link-text {
        color: #033b74;
    }
    
    .more-about-link-item:hover .more-about-link-arrow {
        color: #033b74;
    }
</style>

<div class="boxshadow_border bg-white p-4 more-about-business-card" style="border-radius: 16px !important;">
    <div class="pb-2 mb-1" style="border-bottom: 1px solid #f0f0f0;">
        <h5 class="m-0 card-h-title">More about {{ $bName }}</h5>
    </div>

    <div class="more-about-links-list d-flex flex-column">

        {{-- Overview --}}
        <a href="{{ route('user.product_detail', ['locale' => $currentLocale, 'id' => $bSlug]) }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/overview.svg') }}" alt="Overview">
                </span>
                <span class="more-about-link-text">{{ $bName }} profile</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

        {{-- Alternatives --}}
        <a href="{{ route('business.alternatives', ['locale' => $currentLocale, 'business_slug' => $bSlug, 'alternatives_slug' => $expectedAlternativesSlug]) }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/alternatives.svg') }}" alt="Alternatives">
                </span>
                <span class="more-about-link-text">Alternatives</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

        {{-- FAQs --}}
        <a href="{{ route('business.all_faqs', ['locale' => $currentLocale, 'business_slug' => $bSlug, 'faq_slug' => $expectedFaqSlug]) }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/FAQs.svg') }}" alt="FAQs">
                </span>
                <span class="more-about-link-text">FAQs</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

        {{-- Compare --}}
        <a href="{{ route('business.all_comparisons', ['locale' => $currentLocale, 'business_slug' => $bSlug]) }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/compare.svg') }}" alt="Compare">
                </span>
                <span class="more-about-link-text">Compare</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

        {{-- Reviews --}}
        <a href="{{ route('ReviewShow', ['locale' => $currentLocale, 'slug' => $bSlug, 'reviews_slug' => $expectedReviewsSlug]) }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/reviews.svg') }}" alt="Reviews">
                </span>
                <span class="more-about-link-text">Reviews</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

        <a href="{{ route('business.discussions', ['locale' => $currentLocale, 'business_slug' => $bSlug]) }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <i class="far fa-comments text-primary" style="font-size: 18px;"></i>
                </span>
                <span class="more-about-link-text">Community / Discussions</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

    </div>
</div>