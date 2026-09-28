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
   
</style>

<div class="boxshadow_border bg-white p-4 more-about-business-card" style="border-radius: 16px !important;">
    <div class="pb-2 mb-1" style="border-bottom: 1px solid #f0f0f0;">
        <p class="m-0 card-h-title">{{ static_text('more_about_business_heading') ?: 'More about' }} {{ $bName }}</p>
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
                <span class="more-about-link-text">{{ static_text('tab_alternatives') ?: 'Alternatives' }}</span>
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
                <span class="more-about-link-text">{{ static_text('tab_faqs') ?: 'FAQs' }}</span>
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
                <span class="more-about-link-text">{{ static_text('tab_compare') ?: 'Compare' }}</span>
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
                <span class="more-about-link-text">{{ static_text('tab_reviews') ?: 'Reviews' }}</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

        {{-- Discussions --}}
        <a href="{{ route('business.discussions', ['locale' => $currentLocale, 'business_slug' => $bSlug]) }}"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <img src="{{ asset('front/img/discussions.svg') }}" alt="Discussions">
                </span>
                <span class="more-about-link-text">{{ static_text('tab_discussions') ?: 'Discussions' }}</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

    </div>
</div>