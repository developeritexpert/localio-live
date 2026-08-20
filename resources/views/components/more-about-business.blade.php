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
@endphp
<style>
        /* new sec css */
    .more-about-business-card {
        border-radius: 16px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04) !important;
    }
    .more-about-link-item {
        color: #1e3050;
        transition: all 0.2s ease;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .more-about-link-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .more-about-link-item:first-child {
        padding-top: 0;
    }
    .more-about-link-text {
        font-size: 15px;
        font-weight: 500;
        color: #1e3050;
        transition: color 0.2s ease;
    }
    .more-about-link-arrow {
        font-size: 14px;
        color: #1e3050;
        transition: transform 0.2s ease, color 0.2s ease;
    }
    .more-about-link-item:hover .more-about-link-text {
        color: #06498b;
    }
    .more-about-link-item:hover .more-about-link-arrow {
        color: #06498b;
        transform: translateX(4px);
    }
    /* new sec css end*/
   
</style>
<div class="boxshadow_border bg-white p-4 more-about-business-card" style="border-radius: 16px !important;">
    <div class="pb-2" style="border-bottom: 1px solid #f0f0f0;">
        <h5 class="m-0 card-h-title">More about {{ $bName }}</h5>
    </div>

    <div class="more-about-links-list d-flex flex-column">
        <a href="{{ route('user.product_detail', ['locale' => $currentLocale, 'id' => $bSlug]) }}" class="more-about-link-item d-flex align-items-center justify-content-between text-decoration-none">
            <span class="more-about-link-text">{{ $bName }} overview</span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        <a href="{{ route('business.alternatives', ['locale' => $currentLocale, 'business_slug' => $bSlug, 'alternatives_slug' => $expectedAlternativesSlug]) }}" class="more-about-link-item d-flex align-items-center justify-content-between text-decoration-none">
            <span class="more-about-link-text">{{ $bName }} alternatives</span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        <a href="{{ route('business.all_faqs', ['locale' => $currentLocale, 'business_slug' => $bSlug, 'faq_slug' => $expectedFaqSlug]) }}" class="more-about-link-item d-flex align-items-center justify-content-between text-decoration-none">
            <span class="more-about-link-text">{{ $bName }} FAQs</span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        <a href="{{ route('business.all_comparisons', ['locale' => $currentLocale, 'business_slug' => $bSlug]) }}" class="more-about-link-item d-flex align-items-center justify-content-between text-decoration-none">
            <span class="more-about-link-text">Compare {{ $bName }}</span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
        <a href="{{ route('user.product_detail', ['locale' => $currentLocale, 'id' => $bSlug]) }}#sectionDiscussions" class="more-about-link-item d-flex align-items-center justify-content-between text-decoration-none">
            <span class="more-about-link-text">{{ $bName }} discussions</span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>
    </div>
</div>
