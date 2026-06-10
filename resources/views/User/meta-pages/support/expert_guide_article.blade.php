@extends('user_layout.master')
@section('content')
<section class="banner_sec help-cntr-bnr dark" style="background-color: #003F7D;">
    <div class="bubble-wrp">
        <img src="{{ asset('front/img/small-bnnr-bg.png') }}" alt="">
    </div>
    <div class="banner_content">
        <div class="container">
            <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                <div class="banner_text_col">
                    <div class="banner_content_inner">
                        <h1>Expert Guide-Article</h1>r
                        <p>We have a vast knowledge base with articles, guides, how-tor, instructions, and answers to our most frequently asked questions.
                        </p>e
                        @livewire('hlep-center-search',['placeholder'=>static_text('help_search_bar_text') ?? 'Search...'])
                    </div>
                </div>
                <div class="banner_image_col">r
                    {{-- <div class="banner_image">
                        <img src="{{ asset('front/img/hlp-cntr-bnr.png') }}" class="banner_top_image" alt="Help Center Banner">
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</section>

<section class="como_secotr p_120 light">
    <div class="container">
        <div class="row knwledge-page2-row">
            <!-- Left -->
            <div class="col-lg-4">
                <div class="knwlege-detail-lft">
                    <!-- Categories -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h5">All Categories</h2>
                            <hr>
                            <div class="knwlge-cntnt">
                                <ul class="list-unstyled">
                                    @foreach($categories as $category)
                                        @if($category->translationForCurrentLang)
                                            <li>
                                                <a href="{{ route('expert-guide-category', [
                                                    'locale' => session('lang_code', 'en-us'),
                                                    'slug' => $category->translationForCurrentLang->slug
                                                ]) }}"
                                                   class="{{ $currentCategorySlug === $category->translationForCurrentLang->slug ? 'text-highlight' : '' }}">
                                                    {{ $category->translationForCurrentLang->name }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Article TOC -->
                    @if($translationarticle->contents->isNotEmpty())
                        <div class="card mb-4">
                            <div class="card-body">
                                <h2 class="h5">Content</h2>
                                <hr>
                                <div class="knwlge-cntnt">
                                    <ul class="list-unstyled">
                                        @foreach ($translationarticle->contents as $index => $content)
                                            @if ($content->section_title)
                                                <li>
                                                    <a href="#section-{{ $translationarticle->id }}-{{ $index }}">
                                                        <i class="fa-solid fa-chevron-right me-1"></i>
                                                        {{ $content->section_title }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body knwlege-detail-rgt">
                        <h2>{{ $translationarticle->translationForCurrentLang->title ?? 'Untitled' }}</h2>
                        <div class="cntnt-box mt-3">
                            <p><strong>{!! $translationarticle->translationForCurrentLang->description ?? '' !!}</strong></p>

                            @foreach ($translationarticle->contents as $index => $content)
                                @if ($content)
                                    <hr>
                                    <h3 id="section-{{ $translationarticle->id }}-{{ $index }}">
                                        {!! $content->section_title ?? '' !!}
                                    </h3>
                                    <div>{!! $content->section_content ?? '' !!}</div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Related Articles -->
                        @if($relatedArticles->isNotEmpty())
                            <div class="mt-5">
                                <h4>Related Articles</h4>
                                <div class="related-articles-card">
                                    @foreach($relatedArticles as $article)
                                        <a href="{{ route('expert-guide-article', [
                                            'locale' => session('lang_code', 'en-us'),
                                            'cat_slug' => $article->category->translationForCurrentLang->slug ?? '',
                                            'art_slug' => $article->translationForCurrentLang->slug ?? ''
                                        ]) }}" class="related-article-item">
                                            <div class="text-content">
                                                <div class="title">
                                                    {{ $article->translationForCurrentLang->preview_title ?? $article->translationForCurrentLang->title ?? 'Untitled' }}
                                                </div>
                                                <div class="desc">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($article->translationForCurrentLang->preview_description ?? $article->translationForCurrentLang->description ?? ''), 120) }}
                                                </div>
                                            </div>
                                            <div class="icon">
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@livewire('latest-reviews')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const headerOffset = 120;

        // Smooth scroll to content sections
        document.querySelectorAll('.knwlge-cntnt a[href^="#section-"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetEl = document.getElementById(targetId);

                if (targetEl) {
                    const offsetTop = targetEl.getBoundingClientRect().top + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Highlight active link on scroll
        const sections = document.querySelectorAll('.cntnt-box [id^="section-"]');
        const navLinks = document.querySelectorAll('.knwlge-cntnt a[href^="#section-"]');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - headerOffset - 5;
                if (window.scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    });
</script>

<script>
    $(window).on('load', function() {
        $('body').addClass('ExprtGuideArtPgCls');
    });
</script>
@endsection
