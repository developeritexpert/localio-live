@extends('user_layout.master')

@push('styles')
    <style>
        .faq-category-nav {
            gap:0 25px;
        }
        </style>
@endpush

@section('meta_title', format_meta_text('Frequently Asked Questions | Localio'))
@section('meta_description', format_meta_text('Find answers to common questions about Localio, categories, business reviews, and software ratings.'))

@section('content')

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/' . app()->getLocale()) }}" style="color: #64748b; text-decoration: none;">Home</a>
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

        <!-- Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="margin: 0; line-height: 1.2; color: #1e3050;">
                                Frequently Asked Questions
                            </h1>
                        </div>
                        <p style="font-size: 16px; color: #444; margin-top: 4px; margin-bottom: 0; font-weight:400;">
                            Find answers to common questions about Localio, categories, business reviews, and software ratings.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="revie_img_sec py-5" style="background-color: #f8fafc; border-top: 1px solid #eef2f6;">
    <div class="container">
        <div class="row justify-content-center">

            <!-- FAQs Content, Search & Accordion -->
            <div class="col-lg-10 col-12">
                
                <!-- Search Option -->
                <div class="faq-search-wrapper">
                    <p style="font-size: 20px; font-weight: 600 !important; color: #002347; margin-bottom: 14px;">
                        Search FAQs
                    </p>
                    <div class="faq-search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="faqSearchInput" placeholder="Search questions..." autocomplete="off">
                    </div>
                </div>

                <!-- Main FAQ Section: Headline & In-Page Navigation -->
                <div class="main-faq-container">
                    
                    <!-- In-Page Category Navigation -->
                    @if(isset($faqCategories) && count($faqCategories) > 1)
                    <div class="faq-category-nav" id="faqCategoryNav">
                        <button type="button" class="faq-cat-tab active" data-category-id="all">
                            All
                        </button>
                        @foreach($faqCategories as $category)
                            @php
                                $translation = $category->translations->firstWhere('lang_id', getCurrentLanguageID());
                                $hasFaqsInLang = $category->faqs->filter(fn($faq) => $faq->translations->firstWhere('lang_id', getCurrentLanguageID()))->isNotEmpty();
                                if (!$translation || !$hasFaqsInLang) continue;
                                $categoryName = $translation->name;
                            @endphp
                            <button type="button" class="faq-cat-tab" data-category-id="{{ \Illuminate\Support\Str::slug($categoryName) }}">
                                {{ $categoryName }}
                            </button>
                        @endforeach
                    </div>
                    @endif

                    <!-- FAQs Accordion List - Grouped by Category -->
                    <div id="faqAccordionContainer">
                        @php 
                            $totalFaqCount = 0; 
                            $itemGlobalIndex = 0;
                        @endphp

                        @if(isset($faqCategories) && count($faqCategories) > 0)
                            @foreach($faqCategories as $category)
                                @php
                                    $translation = $category->translations->firstWhere('lang_id', getCurrentLanguageID());
                                    $translatedFaqs = $category->faqs->filter(fn($faq) => $faq->translations->firstWhere('lang_id', getCurrentLanguageID()));
                                    if (!$translation || $translatedFaqs->isEmpty()) continue;

                                    $categoryName = $translation->name;
                                    $catSlug = \Illuminate\Support\Str::slug($categoryName);
                                @endphp

                                <div class="faq-category-group mb-4" data-category-group="{{ $catSlug }}">
                                    @if(count($faqCategories) > 1)
                                    <h2 class="category-group-headline mb-3" style=" color: #1e3050;">
                                        {{ $categoryName }}
                                    </h2>
                                    @endif

                                    <div class="accordion" id="topRatedFaqAccordion_{{ $catSlug }}">
                                        @foreach($translatedFaqs as $faq)
                                            @php
                                                $faqTranslation = $faq->translations->firstWhere('lang_id', getCurrentLanguageID());
                                                $totalFaqCount++;
                                                $itemGlobalIndex++;
                                                $question = $faqTranslation->question ?? '';
                                                $answer = $faqTranslation->answer ?? '';
                                            @endphp
                                            @if(!empty($question))
                                            <div class="accordion-item mb-3" style="border-radius: 8px !important; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                                                <h3 class="accordion-header" id="faqHeading{{ $itemGlobalIndex }}">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $itemGlobalIndex }}" aria-expanded="false" aria-controls="faqCollapse{{ $itemGlobalIndex }}" style="font-weight: 600; font-size: 16px; color: #002347; background-color: #ffffff;">
                                                        {{ $question }}
                                                    </button>
                                                </h3>
                                                <div id="faqCollapse{{ $itemGlobalIndex }}" class="accordion-collapse collapse" role="region" aria-labelledby="faqHeading{{ $itemGlobalIndex }}" data-bs-parent="#topRatedFaqAccordion_{{ $catSlug }}">
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
                                                                        <i class="far fa-thumbs-up"></i>
                                                                    </button>
                                                                    <button type="button" class="btn-vote-faq" onclick="voteFaqItem(this, false)" title="Not helpful">
                                                                        <i class="far fa-thumbs-down"></i>
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
                            @endforeach
                        @endif

                        <div id="noFaqSearchResults" class="p-4 text-center text-muted bg-light rounded border" style="display: none;">
                            <p class="m-0">No questions found matching your search.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // 1. Real-Time Search Filter
    document.getElementById('faqSearchInput')?.addEventListener('input', function() {
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
