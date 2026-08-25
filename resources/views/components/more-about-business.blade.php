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
    .more-about-link-icon svg {
        width: 28px;
        height: 28px;
        color: #007bff;
        stroke: #007bff;
    }
    .more-about-link-text {
        font-size: 15px;
        font-weight: 500;
        color: #1e3050;
        transition: color 0.2s ease;
    }
    .more-about-link-arrow {
        font-size: 14px;
        color: #9aacbe;
        transition: transform 0.2s ease, color 0.2s ease;
        flex-shrink: 0;
    }
    .more-about-link-item:hover .more-about-link-text {
        color: #007bff;
    }
    .more-about-link-item:hover .more-about-link-icon svg {
        color: #0056d2;
        stroke: #0056d2;
    }
    .more-about-link-item:hover .more-about-link-arrow {
        color: #007bff;
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
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/>
                        <path d="M9 21V12h6v9"/>
                    </svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="5" y="2" width="14" height="20" rx="2"/>
                        <line x1="9" y1="7" x2="15" y2="7"/>
                        <line x1="9" y1="11" x2="15" y2="11"/>
                        <line x1="9" y1="15" x2="13" y2="15"/>
                    </svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3"/>
                        <circle cx="12" cy="17" r="0.8" fill="#007bff" stroke="none"/>
                    </svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3v3m0 12v3M5.6 6.4l2.1 2.1m8.6 8.6 2.1 2.1M3 12h3m12 0h3M5.6 17.6l2.1-2.1M16.3 6.4l2.1-2.1"/>
                        <circle cx="12" cy="12" r="4"/>
                    </svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </span>
                <span class="more-about-link-text">Reviews</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

        {{-- Discussions --}}
        <a href="{{ route('user.product_detail', ['locale' => $currentLocale, 'id' => $bSlug]) }}#sectionDiscussions"
           class="more-about-link-item">
            <span class="more-about-link-left">
                <span class="more-about-link-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </span>
                <span class="more-about-link-text">Discussions</span>
            </span>
            <i class="fa-solid fa-arrow-right more-about-link-arrow"></i>
        </a>

    </div>
</div>