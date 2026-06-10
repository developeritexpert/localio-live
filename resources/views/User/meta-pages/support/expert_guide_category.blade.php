@extends('user_layout.master')
@section('content')
<section class="banner_sec help-cntr-bnr dark expert-guide-banner" style="background-color: #003F7D;">
         <div class="bubble-wrp">
            <img src="{{asset('front/img/small-bnnr-bg.png') }}" alt="">
         </div>
         <div class="banner_content">
            <div class="container">
               <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                  <div class="banner_text_col">
                     <div class="banner_content_inner">
                        <h1>Expert Guide-Category</h1>
                        {{-- <p>Lorem ipsum dolor sit amet, consectetur </p> --}}
                        <p>We have a vast knowledge base with articles, guides, how-to, instructions, and answers to our most frequently asked questions.
                        </p>
                        @livewire('hlep-center-search',['placeholder'=>static_text('help_search_bar_text') ?? 'Search...'])
                     </div>
                  </div>
                  <div class="banner_image_col">
                     {{-- <div class="banner_image">
                        <img src="{{asset('front/img/hlp-cntr-bnr.png') }}" class="banner_top_image">
                     </div> --}}
                  </div>
               </div>
            </div>
         </div>
</section>

      <!-- section how we can help you -->
      <section class="como_sec como_sec_2  p_120 light">
        <div class="container">
            <div class="knowledge-tabs mb-5 text-center">
                @foreach($categories as $category)
                    @if($category->translationForCurrentLang)
                        @php
                            $isActive = ($currentCategorySlug === $category->translationForCurrentLang->slug) ? 'active' : '';
                        @endphp
                        <a href="{{ route('expert-guide-category', [
                            'locale' => session('lang_code', 'en-us'),
                            'slug' => $category->translationForCurrentLang->slug
                        ]) }}"
                        class="category-tab {{ $isActive }}">
                            {{ $category->translationForCurrentLang->name }}
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <h2 class="mb-4">{{ $articles->translationForCurrentLang->name ?? 'Category Title' }}</h2>
                    <div class="category-content-box p-4 rounded shadow-sm">
                        {{-- <h2 class="mb-4">{{ $articles->translationForCurrentLang->name ?? 'Category Title' }}</h2> --}}

                        <ul class="list-unstyled m-0">
                            @if(isset($articles) && $articles->articles)
                                @foreach ($articles->articles as $article)
                                    @php
                                        $translation = $article->articleTranslations->first();
                                    @endphp
                                    @if($translation)
                                    <a href="{{ route('expert-guide-article', [
                                        'locale' => session('lang_code', 'en-us'),
                                        'cat_slug' => $articles->translationForCurrentLang->slug,
                                        'art_slug' => $translation->slug
                                    ]) }}">
                                        <li class="d-flex justify-content-between align-items-start ">
                                            <div>
                                                <strong>{{ $translation->preview_title ?? $translation->title ?? 'no title' }}</strong><br>
                                                <small>{!! $translation->preview_description ?? $translation->description ?? 'no description' !!}</small>
                                            </div>

                                                <i class="fa-solid fa-chevron-right text-success"></i>
                                        </li>
                                    </a>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @livewire('latest-reviews')

    <script>
        $(window).on('load', function() {
            $('body').addClass('CategExrtPgCls');
        });
    </script>
@endsection
