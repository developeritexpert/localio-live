@extends('user_layout.master')
@section('content')
<section class="blog-sec terms-sec">

    <div class="container">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            @foreach ($faqCategories as $index => $category)
                @php
                    $translation = $category->translations->firstWhere('lang_id', getCurrentLanguageID());
                    $hasFaqsInLang = $category->faqs->filter(fn($faq) => $faq->translations->firstWhere('lang_id', getCurrentLanguageID()))->isNotEmpty();
                    if (!$translation || !$hasFaqsInLang) continue;

                    $categoryName = $translation->name;
                    $categorySlug = Str::slug($categoryName);
                    $isActive = $activeCategory && $activeCategory->id === $category->id;
                @endphp
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $isActive ? 'active' : ($index === 0 && !$activeCategory ? 'active' : '') }}"
                            id="pills-tab-{{ $category->id }}"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-{{ $category->id }}"
                            type="button"
                            role="tab"
                            aria-controls="pills-{{ $category->id }}"
                            aria-selected="{{ $isActive ? 'true' : ($index === 0 && !$activeCategory ? 'true' : 'false') }}"
                            data-category-slug="{{ $categorySlug }}"
                            data-category-id="{{ $category->id }}"
                            onclick="updateUrl('{{ $categorySlug }}')">
                        {{ $categoryName }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="pills-tabContent">
            @php $hasFaqsToShow = false; $firstTabShown = false; @endphp

            @foreach ($faqCategories as $index => $category)
                @php
                    $translation = $category->translations->firstWhere('lang_id', getCurrentLanguageID());
                    $categoryName = $translation?->name ?? null;
                    $translatedFaqs = $category->faqs->filter(fn($faq) => $faq->translations->firstWhere('lang_id', getCurrentLanguageID()));

                    if (!$categoryName || $translatedFaqs->isEmpty()) continue;

                    $hasFaqsToShow = true;
                    $isActive = !$firstTabShown || ($activeCategory && $activeCategory->id === $category->id);
                    $firstTabShown = true;
                @endphp

                <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                     id="pills-{{ $category->id }}"
                     role="tabpanel"
                     aria-labelledby="pills-tab-{{ $category->id }}"
                     data-category-name="{{ $categoryName }}">
                    <h3 class="mb-4 text-left">{{ $categoryName }}</h3>

                    <div class="inner-cntnt-faq">
                        <div class="accordion" id="accordionExample{{ $category->id }}">
                            @foreach ($translatedFaqs as $faqIndex => $faq)
                                @php
                                    $faqTranslation = $faq->translations->firstWhere('lang_id', getCurrentLanguageID());
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                        <button class="accordion-button {{ $faqIndex === 0 ? '' : 'collapsed' }}"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $faq->id }}"
                                                aria-expanded="{{ $faqIndex === 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $faq->id }}">
                                            {{ $faqTranslation->question }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $faq->id }}"
                                         class="accordion-collapse collapse {{ $faqIndex === 0 ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $faq->id }}"
                                         data-bs-parent="#accordionExample{{ $category->id }}">
                                        <div class="accordion-body">
                                            {!! $faqTranslation->answer !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            @if (!$hasFaqsToShow)
                <div class="text-center py-5">
                    <h3>No FAQ available in this language.</h3>
                    {{-- <p>Please Check this</p> --}}
                </div>
            @else
                <div class="text-center mt-4">
                    <button id="nextCategoryBtn" class="cta cta_orange" onclick="goToNextCategory()">
                        Next Category
                    </button>
                </div>
            @endif
        </div>
    </div>



</section>

<script>
    const faqCategories = [
        @foreach ($faqCategories as $category)
        {
            id: {{ $category->id }},
            name: "{{ $category->translations->first()->name ?? 'No Name' }}",
            slug: "{{ Str::slug($category->translations->first()->name ?? '') }}"
        },
        @endforeach
    ];

    function getCurrentCategoryIndex() {
        const activeTab = document.querySelector('.nav-link.active');
        if (activeTab) {
            const categoryId = parseInt(activeTab.getAttribute('data-category-id'));
            return faqCategories.findIndex(cat => cat.id === categoryId);
        }
        return 0;
    }

    function updateUrl(slug) {
        const newUrl = "{{ route('FaqsShow', ['locale' => app()->getLocale()]) }}/" + slug;
        window.history.pushState({}, '', newUrl);
        updateNextButton();
    }

    function updateNextButton() {
        const currentIndex = getCurrentCategoryIndex();
        const nextIndex = (currentIndex + 1) % faqCategories.length;
        const nextCategory = faqCategories[nextIndex];

        const nextBtn = document.getElementById('nextCategoryBtn');
        if (nextBtn && nextCategory) {
            nextBtn.innerHTML = 'Next: ' + nextCategory.name;
        }
    }

    function goToNextCategory() {
    const currentIndex = getCurrentCategoryIndex();
    const nextIndex = (currentIndex + 1) % faqCategories.length;
    const nextCategory = faqCategories[nextIndex];

    const nextTabButton = document.getElementById('pills-tab-' + nextCategory.id);
    if (nextTabButton) {
        nextTabButton.click(); // Simulate tab click
        window.scrollTo(0, 0); // 👈 Scroll to top on category change
    }
}


    function getSlugFromUrl() {
        const parts = window.location.pathname.split('/');
        return parts[parts.length - 1];
    }

    function activateCategoryFromSlug() {
    const slug = getSlugFromUrl();
    const category = faqCategories.find(cat => cat.slug === slug);

    if (category) {
        const tabButton = document.getElementById('pills-tab-' + category.id);
        const tabPane = document.getElementById('pills-' + category.id);

        // Deactivate all
        document.querySelectorAll('.nav-link').forEach(el => {
            el.classList.remove('active');
            el.setAttribute('aria-selected', 'false');
        });
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        // Activate selected
        if (tabButton && tabPane) {
            tabButton.classList.add('active');
            tabButton.setAttribute('aria-selected', 'true');

            tabPane.classList.add('show', 'active');

            expandFirstFaqInPane(tabPane);
            window.scrollTo(0, 0); // 👈 Scroll to top when slug activates category
        }
    }
}


    function expandFirstFaqInPane(tabPane) {
        // Collapse all
        document.querySelectorAll('.accordion-collapse').forEach(item => {
            item.classList.remove('show');
        });
        document.querySelectorAll('.accordion-button').forEach(btn => {
            btn.classList.add('collapsed');
            btn.setAttribute('aria-expanded', 'false');
        });

        const firstFaqCollapse = tabPane.querySelector('.accordion-collapse');
        const firstFaqButton = tabPane.querySelector('.accordion-button');

        if (firstFaqCollapse && firstFaqButton) {
            firstFaqCollapse.classList.add('show');
            firstFaqButton.classList.remove('collapsed');
            firstFaqButton.setAttribute('aria-expanded', 'true');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Activate category from URL slug (or default)
        activateCategoryFromSlug();

        // Delay slightly to ensure tabs are activated before updating the button
        setTimeout(updateNextButton, 100); // 👈 Key fix

        // Recalculate when user switches tabs
        document.querySelectorAll('.nav-link[data-bs-toggle="pill"]').forEach(button => {
            button.addEventListener('shown.bs.tab', function () {
                updateNextButton();

                const categoryId = button.getAttribute('data-category-id');
                const pane = document.getElementById('pills-' + categoryId);
                if (pane) {
                    expandFirstFaqInPane(pane);
                }
            });
        });
    });

    window.addEventListener('popstate', function () {
        activateCategoryFromSlug();
        setTimeout(updateNextButton, 100); // 👈 Ensures correct label on back/forward
    });
</script>

{{--FAQ CLASS--}}
<script>
    $(window).on('load', function() {
        $('body').addClass('FaqPgCls');
    });
</script>


@endsection
