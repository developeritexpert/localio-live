@extends('user_layout.master')
@push('styles')
    <style>
      .top-rated-heading-sec {
        margin-top: 95px;
        padding-top: 30px;
        background-color: #f7f9fb;
        border-bottom: 1px solid #e2e8f0;
        }
    </style>
@endpush

@php
    $catSlug = $categoryTranslation->slug ?? $category->slug;
    $parentCatName = $parentTrans->name ?? ($parentCategory->name ?? null);
    $parentCatSlug = $parentTrans->slug ?? ($parentCategory->slug ?? null);
    $catDesc = strip_tags($categoryTranslation->description ?? '');
    $matchedLang = \App\Models\Language::where('lang_code', request()->segment(1))->first();
    $currentLangId = isset($lang_id) && $lang_id ? $lang_id : ($matchedLang ? $matchedLang->id : getCurrentLanguageID());
    $resolvedFaqMeta = \App\Services\MetaTagService::resolveCategoryFaqMeta($category, $currentLangId);
    $pageFaqTitle = !empty($faqMetaTitle) ? $faqMetaTitle : $resolvedFaqMeta['title'];
    $pageFaqDesc = !empty($faqMetaDesc) ? $faqMetaDesc : $resolvedFaqMeta['description'];
@endphp

@section('meta_title', format_meta_text($pageFaqTitle))
@if(!empty($faqMetaDesc))
@section('meta_description', format_meta_text($pageFaqDesc))
@endif

@section('content')

<!-- Upper Header Section -->
<section class="top-rated-heading-sec help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style=" background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/' . app()->getLocale()) }}" style="color: #64748b; text-decoration: none;">All</a>
                    </li>
                    @if($parentCatName && $parentCatSlug)
                        <li class="breadcrumb-item">
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentCatSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $parentCatName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item">
                        <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $catName }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #002347; font-weight: 500;">
                        FAQs
                    </li>
                </ol>
            </nav>
            <div class="col-4 d-flex justify-content-end">
                <x-social-icon />
            </div>
        </div>

                        @php
                            $resolvedFaqH1 = \App\Services\ContentTemplateService::resolveCategorySection($category, 'faq_h1', $lang_id ?? 1);
                            $faqH1Title = !empty($resolvedFaqH1['headline']) 
                                ? $resolvedFaqH1['headline'] 
                                : (!empty($categoryTranslation->faq_h1_title) ? $categoryTranslation->faq_h1_title : ($catName . ' FAQs'));

                            $resolvedFaqHeader = \App\Services\ContentTemplateService::resolveCategorySection($category, 'faq_header_intro', $lang_id ?? 1);
                            $faqHeader1 = $resolvedFaqHeader['headline'] ?? null;
                            $faqHeader2 = $resolvedFaqHeader['subtext'] ?? null;
                        @endphp
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 1.2; color: #1e3050;">
                                {!! \App\Services\ContentTemplateService::cleanHeading($faqH1Title) !!}
                            </h1>
                        </div>
        <!-- Category Header Row -->
        <div class="faq-header-sec row  justify-content-between">
            <div class="col-md-6 col-12">
                <div class="top_head d-flex align-items-center gap-3 mt-2">
                    <div>
                        <div class="faq-subtext-container ck-content" style="font-size: 16px; color: #444; margin-top: 4px; margin-bottom: 0; font-weight: 400;">
                            @if(!empty($faqHeader1) || !empty($faqHeader2))
                                @if(!empty($faqHeader1))
                                    <p style="margin-bottom: 8px;">{!! $faqHeader1 !!}</p>
                                @endif
                                @if(!empty($faqHeader2))
                                    <p style="margin: 0;">{!! $faqHeader2 !!}</p>
                                @endif
                            @elseif(!empty($categoryTranslation->faq_subtext))
                                {!! $categoryTranslation->faq_subtext !!}
                            @else
                                <p style="margin: 0;">
                                    Frequently asked questions and answers about {{ strtolower($catName) }} on Localio.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12 text-md-end mt-3 mt-md-0 cat_img_header">
                @if(isset($category) && $category->top_rated_image && $category->topRatedImageMedia)
                    <div class="cat-hero-img-wrap position-relative d-inline-block">
                        <img src="{{ asset($category->topRatedImageMedia->dir_path . '/' . $category->topRatedImageMedia->file_name) }}" alt="{{ $category->name ?? '' }}" class="cat-hero-main-img" style="height: 100%;">
                        @if(!empty($category->faq_overlay_image) && $category->faqOverlayMedia)
                            @php $pos = $category->faq_overlay_position ?? 'right'; @endphp
                            <img src="{{ asset($category->faqOverlayMedia->dir_path . '/' . $category->faqOverlayMedia->file_name) }}" alt="FAQ Overlay" class="cat-hero-overlay-img cat-hero-overlay-{{ $pos }}">
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Content & Right Sidebar Section -->
<section class="revie_img_sec py-5" style="background-color: #ffffff;">
    <div class="container">
        <div class="row g-4">

            <!-- Left Side: FAQs Content, Search & Accordion -->
            <div class="col-lg-8 col-12">
                
                <!-- Section 2: Search Option -->
                <div class="faq-search-wrapper">
                    @php
                        $resolvedFaqSearch = \App\Services\ContentTemplateService::resolveCategorySection($category, 'faq_search_headline', $lang_id ?? 1);
                        $faqSearchHeadline = !empty($resolvedFaqSearch['headline']) ? $resolvedFaqSearch['headline'] : ('Search ' . $catName . ' FAQs');

                        $resolvedFaqPlaceholder = \App\Services\ContentTemplateService::resolveCategorySection($category, 'faq_search_placeholder', $lang_id ?? 1);
                        $faqSearchPlaceholder = !empty($resolvedFaqPlaceholder['headline']) ? $resolvedFaqPlaceholder['headline'] : 'Search questions...';
                    @endphp
                    <p style="font-size: 20px; font-weight: 600 !important; color: #002347; margin-bottom: 14px;">
                        {!! \App\Services\ContentTemplateService::cleanHeading($faqSearchHeadline) !!}
                    </p>
                    <div class="faq-search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="faqSearchInput" placeholder="{{ \App\Services\ContentTemplateService::cleanPlaceholder($faqSearchPlaceholder) }}" value="{{ request('q') }}" autocomplete="off">
                    </div>
                </div>

                <!-- Main FAQ Section: Headline & Accordion List -->
                <div class="main-faq-container">
                    @php
                        $resolvedFaqAccordion = \App\Services\ContentTemplateService::resolveCategorySection($category, 'faq_accordion_headline', $lang_id ?? 1);
                        $faqAccordionHeadline = !empty($resolvedFaqAccordion['headline']) ? $resolvedFaqAccordion['headline'] : 'Frequently asked questions';
                    @endphp
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin: 0;">
                            {!! \App\Services\ContentTemplateService::cleanHeading($faqAccordionHeadline) !!}
                        </h2>
                    </div>

                    @php
                        $uniqueCategories = [];
                        if (!empty($categoryTranslation->faq_categories)) {
                            $decodedCats = is_array($categoryTranslation->faq_categories) ? $categoryTranslation->faq_categories : json_decode($categoryTranslation->faq_categories, true);
                            if (is_array($decodedCats)) {
                                $uniqueCategories = $decodedCats;
                            }
                        }
                        
                        // If faq_categories is empty, infer from $faqs
                        if (empty($uniqueCategories) && isset($faqs) && is_array($faqs)) {
                            foreach ($faqs as $faq) {
                                $cat = $faq['category'] ?? 'General';
                                if (!in_array($cat, $uniqueCategories)) {
                                    $uniqueCategories[] = $cat;
                                }
                            }
                        }
                        
                        // Default to General if completely empty but we have FAQs
                        if (empty($uniqueCategories) && isset($faqs) && count($faqs) > 0) {
                            $uniqueCategories = ['General'];
                        }
                        
                        // Group FAQs by category
                        $groupedFaqs = [];
                        foreach ($uniqueCategories as $cat) {
                            $groupedFaqs[$cat] = [];
                        }
                        
                        if (isset($faqs) && is_array($faqs)) {
                            foreach ($faqs as $faq) {
                                $cat = $faq['category'] ?? 'General';
                                if (!isset($groupedFaqs[$cat])) {
                                    $groupedFaqs[$cat] = [];
                                    if (!in_array($cat, $uniqueCategories)) {
                                        $uniqueCategories[] = $cat;
                                    }
                                }
                                $groupedFaqs[$cat][] = $faq;
                            }
                        }
                    @endphp

                    <!-- FAQs Accordion List - Exact Top-Rated Style -->
                    <div id="faqAccordionContainer">
                        @if(isset($faqs) && count($faqs) > 0)
                        
                            @if(count($uniqueCategories) > 1)
                            <div class="faq-category-nav">
                                <button class="faq-cat-tab active" data-category-id="all">
                                    All
                                </button>
                                @foreach($uniqueCategories as $cat)
                                    <button class="faq-cat-tab" data-category-id="{{ Str::slug($cat) }}">
                                        {{ $cat }}
                                    </button>
                                @endforeach
                            </div>
                            @endif

                            @php $isFirstFaqOverall = true; @endphp
                            @foreach($uniqueCategories as $cat)
                                @if(isset($groupedFaqs[$cat]) && count($groupedFaqs[$cat]) > 0)
                                <div class="faq-category-group" data-category-group="{{ Str::slug($cat) }}">
                                    @if(count($uniqueCategories) > 1)
                                    <h3 style="">
                                        {{ $cat }}
                                    </h3>
                                    @endif
                                    
                                    <div class="accordion" id="categoryFaqAccordion_{{ Str::slug($cat) }}">
                                        @foreach($groupedFaqs[$cat] as $fIndex => $faq)
                                            @php 
                                                $uniqueId = Str::slug($cat) . '_' . $fIndex; 
                                                $question = $faq['question'] ?? '';
                                                $answer = $faq['answer'] ?? '';
                                                $openThisFaq = $isFirstFaqOverall && !empty($question);
                                                if (!empty($question)) {
                                                    $isFirstFaqOverall = false;
                                                }
                                            @endphp
                                            @if(!empty($question))
                                            <div class="accordion-item mb-3" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                                                <h3 class="accordion-header" id="headingFaq{{ $uniqueId }}">
                                                    <button class="accordion-button {{ $openThisFaq ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq{{ $uniqueId }}" aria-expanded="{{ $openThisFaq ? 'true' : 'false' }}" aria-controls="collapseFaq{{ $uniqueId }}" style="font-weight: 600; font-size: 16px; color: #002347; background-color: #ffffff;">
                                                        {{ $question }}
                                                    </button>
                                                </h3>
                                                <div id="collapseFaq{{ $uniqueId }}" class="accordion-collapse collapse {{ $openThisFaq ? 'show' : '' }}" aria-labelledby="headingFaq{{ $uniqueId }}" data-bs-parent="#categoryFaqAccordion_{{ Str::slug($cat) }}">
                                                    <div class="accordion-body rich-text-content" style="background-color: #ffffff;">
                                                        <div class="faq-answer-text">
                                                            {!! $answer !!}
                                                        </div>

                                                        <!-- FAQ Feedback Row -->
                                                        <div class="faq-feedback-row">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span>Was this answer helpful?</span>
                                                                <div class="faq-feedback-actions">
                                                                    <button type="button" class="btn-vote-faq" onclick="voteFaqItem(this, true)" title="Helpful">
                                                                        <i class="far fa-thumbs-up"></i> Yes
                                                                    </button>
                                                                    <button type="button" class="btn-vote-faq" onclick="voteFaqItem(this, false)" title="Not helpful">
                                                                        <i class="far fa-thumbs-down"></i> No
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endforeach
                            
                        @else
                            <div class="p-4 text-center text-muted bg-light rounded border">
                                <p class="m-0">No FAQs available for {{ $catName }} at this time.</p>
                            </div>
                        @endif

                        <div id="noFaqSearchResults" class="p-4 text-center text-muted bg-light rounded border" style="display: none;">
                            <p class="m-0">No questions found matching your search. Try different keywords or ask the community below.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Ask Community Bottom Banner -->
                <div class="ask-community-banner">
                    <div>
                        <h2 >
                            Still have a question about {{ $catName }}?
                        </h2>
                        <p style="font-size: 15px; color: #e2e8f0; margin-bottom: 0;">
                            Ask the Localio community
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="blue-btn ask-community-btn ">
                            Ask a question <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
            <!-- End Left Side -->

            <!-- Right Side: Sidebar -->
            <div class="col-lg-4 col-12">
                <div class="d-flex flex-column gap-4">

                    <!-- 1. Recent Reviews Box -->
                    <div class="boxshadow_border bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <h2 class="m-0 card-h-title">Recent reviews</h2>
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="view-review-link" >
                                View all
                            </a>
                        </div>

                        @if(isset($recentReviews) && $recentReviews->count() > 0)
                            @foreach($recentReviews as $rev)
                                @php
                                    $revTrans = $rev->translations->first();
                                    $u = $rev->user;
                                    if ($u && $u->user_type === 'admin') {
                                        $displayName = $rev->public_name ?? 'Public';
                                    } elseif ($u) {
                                        $displayName = $u->displayName();
                                    } else {
                                        $displayName = 'Anonymous';
                                    }
                                    $bName = $rev->business?->translations?->first()?->name ?? $rev->business?->name ?? 'Business';
                                @endphp
                                <div class="sidebar-review-card {{ !$loop->last ? 'pb-3 mb-3 border-bottom' : '' }}">
                                    <div class="review-header d-flex justify-content-between align-items-start">
                                        <div class="review-user d-flex align-items-center gap-2">
                                            @if($u && $u->profile_image && $u->profile_image !== 'front/img/default.png')
                                                <img src="{{ asset($u->profile_image) }}" class="rounded-circle" width="38" height="38" style="object-fit:cover;">
                                            @else
                                                <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <span style="color: white; font-weight: bold; font-size: 16px;">{{ strtoupper(substr($displayName, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <p style="line-height:1; margin: 0; font-size: 14px; font-weight: 600; color: #002347;">
                                                @if(isset($rev) && $rev->user_id)
                                                    <a href="{{ optional($rev->user ?? ($u ?? null))->profileUrl() ?? route('user.profile', ['id' => $rev->user_id]) }}" style="color: inherit; text-decoration: none;">
                                                        {{ $displayName }}
                                                    </a>
                                                @else
                                                    {{ $displayName }}
                                                @endif
                                                </p>
        
                                                @php
                                                    $badge = isset($review) && $review->user ? $review->user->getContributorBadge() : (isset($u) ? $u->getContributorBadge() : null);
                                                @endphp
                                                @if($badge)
                                                    <div class="user-contributor-badge" style="font-size: 13px; color: #64748b; margin-top: 2px;">
                                                        {!! $badge->icon !!} {{ $badge->name }}
                                                    </div>
                                                @endif

                                                @if($u && $u->job_title)
                                                    <div style="font-size: 11px; color: #777; margin-top: 1px;">{{ $u->job_title }}</div>
                                                @elseif(!empty($bName))
                                                    <div style="font-size: 11px; color: #777; margin-top: 1px;">on {{ $bName }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $rev->created_at ? $rev->created_at->diffForHumans() : '' }}</small>
                                    </div>
                                    <p style="margin-top: 8px; margin-bottom: 3px; font-size: 14px; font-weight: 600;">
                                        {{ $revTrans->title ?? 'Review' }}
                                    </p>
                                    <div class="rating-stars mb-1" style="font-size: 11px; color: #ff5722;">
                                        @for($s=1; $s<=5; $s++)
                                            @if($s<=floor($rev->rating))
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($s-0.5<=$rev->rating)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    @if($revTrans && !empty($revTrans->description))
                                        <p style="font-weight: 400; font-size: 14px; line-height: 1.4; color: #002347; margin-bottom: 0;">{{ \Illuminate\Support\Str::limit(strip_tags($revTrans->description), 85) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="sidebar-review-card mb-3 pb-3 border-bottom">
                                <div class="review-header d-flex justify-content-between align-items-start">
                                    <div class="review-user d-flex align-items-center gap-2">
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="color: white; font-weight: bold; font-size: 16px;">B</span>
                                        </div>
                                        <div>
                                            <p style="line-height:1; margin: 0; font-size: 14px; font-weight: 600; color: #002347;">Beck K.</p>
                                            <div style="font-size: 11px; color: #777;">Fugiat aute anim eni</div>
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">1 week ago</small>
                                </div>
                                <p style="margin-top: 8px; margin-bottom: 3px; font-size: 14px; font-weight: 600;">
                                    Rem sint adipisicin
                                </p>
                                <div class="rating-stars mb-1" style="font-size: 11px; color: #ff5722;">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <p style="font-weight: 400; font-size: 13px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">Numquam officia rem</p>
                            </div>
                        @endif
                    </div>

                    <!-- 2. Recent Discussions Box -->
                    <div class="boxshadow_border bg-white p-4 not-on-mobile" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <h2 class="m-0 card-h-title">Recent discussions</h2>
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="view-review-link">
                                View all
                            </a>
                        </div>

                        <div class="sidebar-review-card mb-3 pb-3 border-bottom">
                            <div class="review-header d-flex justify-content-between align-items-start">
                                <div class="review-user d-flex align-items-center gap-2">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-weight: bold; font-size: 16px;">M</span>
                                    </div>
                                    <div>
                                        <p style="line-height:1; margin: 0; font-size: 14px; font-weight: 600; color: #002347;">Marc L.</p>
                                        <div style="font-size: 11px; color: #777;">Product Manager • Small Business</div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">2 hours ago</small>
                            </div>
                            <p style="margin-top: 8px; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #002347;">
                                Is there a free tier for API access or is it trial only?
                            </p>
                            <p style="font-size: 14px; line-height: 1.4; color: #002347; margin-bottom: 0;">
                                We are looking to integrate this into our workflow and want to test latency...
                            </p>
                        </div>

                        <div class="sidebar-review-card">
                            <div class="review-header d-flex justify-content-between align-items-start">
                                <div class="review-user d-flex align-items-center gap-2">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-weight: bold; font-size: 16px;">S</span>
                                    </div>
                                    <div>
                                        <p style="line-height:1; margin: 0; font-size: 14px; font-weight: 600; color: #002347;">Sarah J.</p>
                                        <div style="font-size: 11px; color: #777;">CTO • Mid-Market</div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">1 day ago</small>
                            </div>
                            <p style="margin-top: 8px; margin-bottom: 0; font-size: 13.5px; font-weight: 600;">
                                How does the performance compare to alternatives in large datasets?
                            </p>
                            <p style="font-size: 14px; line-height: 1.4; color: #002347; margin-bottom: 0;">
                                We noticed some latency spikes during queries with more than 10k items...
                            </p>
                        </div>
                    </div>

                    <!-- 3. Top 3 Box -->
                    @if(isset($topBusinesses) && $topBusinesses->count() > 0)
                    <div class="boxshadow_border bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <h2 class="m-0 card-h-title">Web hosting top 3 </h2>
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="view-review-link">
                                View all
                            </a>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach($topBusinesses as $topBiz)
                                @php
                                    $topBizTrans = $topBiz->translations->firstWhere('lang_id', $lang_id) ?? $topBiz->translations->first();
                                    $topBizName = $topBizTrans->name ?? $topBiz->name ?? 'Business';
                                    $topBizRating = $topBiz->display_rating;
                                    $affiliateUrl = $topBiz->getTrackedUrl();
                                @endphp
                                <a href="{{ $affiliateUrl }}" target="_blank" rel="nofollow noopener" class="top-biz-sidebar-item d-flex align-items-center justify-content-between p-2 rounded text-decoration-none" style=" border: 1px solid #f1f5f9; background: #fdfdfd;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="top-product-small-logo" style="width: 38px; height: 38px; flex-shrink: 0; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #e2e8f0;">
                                            <x-business-logo :business="$topBiz" :name="$topBizName" />
                                        </div>
                                        <div>
                                            <p class="m-0" style="font-size: 13.5px; font-weight: 600; color: #1e3050;">{{ $topBizName }}</p>
                                            @if($topBiz->should_show_rating)
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <div class="d-flex align-items-center" style="color: #ff5722; font-size: 11px;">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($topBizRating >= $i)
                                                            <i class="fas fa-star"></i>
                                                        @elseif ($topBizRating >= $i - 0.5)
                                                            <i class="fas fa-star-half-alt"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span style="font-size: 11.5px; font-weight: 600; color: #64748b; margin-left: 3px;">{{ formatRating($topBizRating) }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <i class="fas fa-external-link-alt text-muted" style="font-size: 12px; margin-right: 4px;"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            <!-- End Right Side -->

        </div>
    </div>
</section>


<!-- Explore Subcategories Section -->
@if(isset($exploreSubcategories) && isset($exploreSubcategories['items']) && count($exploreSubcategories['items']) > 0)
@php
    $resolvedFaqExplore = \App\Services\ContentTemplateService::resolveCategorySection($category, 'faq_explore', $currentLangId ?? 1);
    $exploreHeadline = !empty($resolvedFaqExplore['headline']) 
        ? $resolvedFaqExplore['headline'] 
        : (!empty($categoryTranslation->faq_explore_headline) 
            ? $categoryTranslation->faq_explore_headline 
            : ($exploreSubcategories['title'] ?? ('Explore ' . $catName . ' categories')));
    $exploreSubtext = !empty($resolvedFaqExplore['subtext']) ? $resolvedFaqExplore['subtext'] : ($categoryTranslation->faq_explore_subtext ?? null);
    $currentFaqSlug = $faqSlug ?? 'faqs';
@endphp
<section class="explore-subcategories-sec py-5" style="background: #f7f9fb; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div class="text-start mb-4">
            <h2 class="explore-cat-h2" style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 4px; text-transform: math-auto;">
                {!! \App\Services\ContentTemplateService::cleanHeading($exploreHeadline) !!}
            </h2>
            @if(!empty($exploreSubtext))
                <div class="explore-cat-subtext" style="color: #475569; margin: 0; font-size: 15px;">
                    {!! $exploreSubtext !!}
                </div>
            @else
                <p style="color: #475569; margin: 0; font-size: 15px;">
                    Browse specialized categories to find the exact tools and solutions for your requirements.
                </p>
            @endif
        </div>

        <div class="row g-3">
            @foreach($exploreSubcategories['items'] as $subcatItem)
            @php
                $subTrans = null;
                if (isset($subcatItem->translations)) {
                    if (is_object($subcatItem->translations) && !($subcatItem->translations instanceof \Illuminate\Database\Eloquent\Collection)) {
                        $subTrans = $subcatItem->translations;
                    } elseif ($subcatItem->translations instanceof \Illuminate\Database\Eloquent\Collection && $subcatItem->translations->isNotEmpty()) {
                        $subTrans = $subcatItem->translations->first();
                    }
                }
                if (!$subTrans && isset($subcatItem->categoryTranslations)) {
                    $subTrans = $subcatItem->categoryTranslations->where('lang_id', $currentLangId)->first() ?? $subcatItem->categoryTranslations->first();
                }

                $subName = $subTrans->name ?? $subcatItem->name ?? 'Subcategory';
                $subSlug = $subTrans->slug ?? $subcatItem->slug ?? (string)$subcatItem->id;
                $subFaqUrl = url('/' . app()->getLocale() . '/' . $subSlug . '/' . $currentFaqSlug);

                // Count FAQ entries for this subcategory
                $faqCount = 0;
                if (!empty($subTrans->faqs)) {
                    $decodedFaqs = is_array($subTrans->faqs) ? $subTrans->faqs : json_decode($subTrans->faqs, true);
                    if (is_array($decodedFaqs) && count($decodedFaqs) > 0) {
                        $faqCount = count($decodedFaqs);
                    }
                }
                if ($faqCount === 0 && isset($subcatItem->categoryTranslations)) {
                    foreach ($subcatItem->categoryTranslations as $transItem) {
                        if (!empty($transItem->faqs)) {
                            $decodedFaqs = is_array($transItem->faqs) ? $transItem->faqs : json_decode($transItem->faqs, true);
                            if (is_array($decodedFaqs) && count($decodedFaqs) > 0) {
                                $faqCount = count($decodedFaqs);
                                break;
                            }
                        }
                    }
                }
                if ($faqCount === 0) {
                    $catFaqsJson = \App\Models\CategoryTranslation::where('category_id', $subcatItem->id)
                        ->whereNotNull('faqs')
                        ->where('faqs', '!=', '')
                        ->where('faqs', '!=', '[]')
                        ->value('faqs');
                    if (!empty($catFaqsJson)) {
                        $decodedFaqs = is_array($catFaqsJson) ? $catFaqsJson : json_decode($catFaqsJson, true);
                        if (is_array($decodedFaqs) && count($decodedFaqs) > 0) {
                            $faqCount = count($decodedFaqs);
                        }
                    }
                }
                // Fallback: If no custom FAQs exist in DB, category FAQ pages display the 4 standard fallback FAQs
                if ($faqCount === 0) {
                    $faqCount = 4;
                }
            @endphp
            <div class="col-lg-4 col-md-6 col-6">
                <a href="{{ $subFaqUrl }}" class="text-decoration-none d-block h-100">
                    <div class="explore-cat-card p-3 border rounded-3 bg-white h-100 d-flex flex-column align-items-center justify-content-between">
                        <div class="mb-3 w-100 d-flex align-items-center justify-content-center" style="min-height: 100px;">
                            @if(isset($subcatItem->topRatedImageMedia))
                                <img src="{{ asset($subcatItem->topRatedImageMedia->dir_path . '/' . $subcatItem->topRatedImageMedia->file_name) }}" alt="{{ $subName }}" style=" object-fit: contain;">
                            @else
                                <!-- Placeholder if no SVG is uploaded -->
                                <div style="width: 100%; height: 100px; background-color: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;"></div>
                            @endif
                        </div>
                        
                        <div class="exp-cating d-flex justify-content-between align-items-center w-100 mt-auto pt-2" style="border-top: 1px solid #f1f5f9;">
                            <p class="m-0 fw-bold text-start" style="font-size: 14px; color: #1e3050;">{{ $subName }}</p>
                            <span class="badge rounded-pill fw-normal" style="color: #64748b; background-color: #f8fafc; border: 1px solid #e2e8f0; font-size: 11px; padding: 5px 10px;">
                                {{ $faqCount }} {{ $faqCount == 1 ? 'FAQ' : 'FAQs' }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


<!-- Category Community Discussions Section -->
@php
    $catTransForDisc = ($category->translations instanceof \Illuminate\Database\Eloquent\Collection)
        ? $category->translations->first()
        : ($category->translations ?? $category->translation);
    $catDiscName = $catTransForDisc->name ?? $category->name ?? 'Category';

    $totalDiscs = $category->discussions()->count();
    $resolvedFaqDisc = \App\Services\ContentTemplateService::resolveCategorySection(
        $category, 
        'faq_page_discussions', 
        $lang_id ?? 1,
        ['[count]' => number_format($totalDiscs)]
    );

    $faqDiscHeadline = null;
    $faqDiscSubtext = null;
    if (!empty($resolvedFaqDisc['headline'])) {
        $faqDiscHeadline = $resolvedFaqDisc['headline'];
        if (!str_contains($faqDiscHeadline, '(') && !str_contains($faqDiscHeadline, (string)$totalDiscs)) {
            $faqDiscHeadline .= ' (' . number_format($totalDiscs) . ')';
        }
    }
    if (!empty($resolvedFaqDisc['subtext'])) {
        $faqDiscSubtext = $resolvedFaqDisc['subtext'];
    }
@endphp
<section class="category-discussions-sec py-5" id="categoryDiscussionsSection" style="background: #ffffff; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <x-community-discussion :category="$category" :bName="$catDiscName" :headline="$faqDiscHeadline" :subtext="$faqDiscSubtext" />
    </div>
</section>

@endsection

@push('scripts')
<script>
    // 1. Real-Time Search Filter
    const mainFaqSearchInput = document.getElementById('faqSearchInput');
    mainFaqSearchInput?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('#faqAccordionContainer .accordion-item');
        const groups = document.querySelectorAll('.faq-category-group');
        let totalVisible = 0;

        items.forEach(item => {
            const question = item.querySelector('.accordion-button')?.textContent.toLowerCase() || '';
            const answer = item.querySelector('.faq-answer-text')?.textContent.toLowerCase() || '';
            const isMatch = question.includes(query) || answer.includes(query);

            if (isMatch) {
                item.style.display = 'block';
                totalVisible++;
            } else {
                item.style.display = 'none';
            }
        });

        // Hide empty category groups
        groups.forEach(group => {
            const visibleInGroup = group.querySelectorAll('.accordion-item[style="display: block;"]').length;
            if (query !== '' && visibleInGroup === 0) {
                group.style.display = 'none';
            } else {
                group.style.display = 'block';
            }
        });

        const noResults = document.getElementById('noFaqSearchResults');
        if (noResults) {
            noResults.style.display = (totalVisible === 0 && query !== '') ? 'block' : 'none';
        }
    });

    // Auto-trigger search filter if pre-filled via query parameter ?q=
    if (mainFaqSearchInput && mainFaqSearchInput.value && mainFaqSearchInput.value.trim() !== '') {
        mainFaqSearchInput.dispatchEvent(new Event('input'));
    }

    // 2. In-Page Category Tab Filter
    document.querySelectorAll('.faq-cat-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.faq-cat-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const categoryId = this.dataset.categoryId;
            const groups = document.querySelectorAll('.faq-category-group');

            // Reset search input
            const searchInput = document.getElementById('faqSearchInput');
            if (searchInput) searchInput.value = '';

            groups.forEach(group => {
                const groupCat = group.dataset.categoryGroup;
                if (categoryId === 'all' || groupCat === categoryId) {
                    group.style.display = 'block';
                    group.querySelectorAll('.accordion-item').forEach(c => c.style.display = 'block');
                } else {
                    group.style.display = 'none';
                }
            });

            const noResults = document.getElementById('noFaqSearchResults');
            if (noResults) noResults.style.display = 'none';
        });
    });

    // 3. Helpful / Not Helpful Feedback Handler
    function voteFaqItem(btn, isHelpful) {
        const parentActions = btn.closest('.faq-feedback-actions');
        if (!parentActions) return;

        parentActions.querySelectorAll('.btn-vote-faq').forEach(b => {
            b.classList.remove('voted-yes', 'voted-no');
            const icon = b.querySelector('i');
            if (icon) {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });

        if (isHelpful) {
            btn.classList.add('voted-yes');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            }
        } else {
            btn.classList.add('voted-no');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            }
        }
    }
</script>
@endpush
