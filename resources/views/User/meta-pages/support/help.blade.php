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
                            <h1>{{ $help->banner_headline ?? '' }}</h1>
                            <p>{{ $help->banner_description ?? '' }} </p>
                             @livewire('hlep-center-search',['placeholder'=>static_text('help_search_bar_text') ?? 'Search...'])
                        </div>
                    </div>
                    <div class="banner_image_col">
                        <div class="banner_image">
                            @if ($help && $help->banner_img)
                                <img src="{{ asset($help->banner_img ?? '') }}" class="banner_top_image">
                            @else
                                <img src="{{ asset('front/img/hlp-cntr-bnr.png') }}" class="banner_top_image">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- section how we can help you -->
    <section class="hlp-you-center p_120 light">
        <div class="container">
            <div class="hlp-you-wrp">
                <h2 class="text-center mb_30" data-aos="zoom-in" data-aos-duration="1000">{{ $help->main_heading ?? '' }}
                </h2>
                <div class="hlp-you-cards">
                    <div class="row hlp-crd-row" style="justify-content: center;">
                        <div class="col-lg-6 col-md-6 col-sm-6" data-aos="fade-up" data-aos-duration="1000">
                            <a href="{{ route('expert-guide') }}">
                                <div class="hlp-you-box" style="height: 100%;">
                                    <div class="hlp-you-img">
                                        @if (!empty($help->knowledge_base_image))
                                            <img src="{{ asset( $help->knowledge_base_image) }}" alt="Knowledge Base">
                                        @else
                                            <img src="{{ asset('front/img/hlp-img-1.svg') }}" alt="Knowledge Base">
                                        @endif
                                    </div>
                                    <div class="hlp-you-cntnt text-center">
                                        <h3>{{ $help->knowledge_base_title ?? 'Knowledge Base' }}</h3>
                                        <p>{{ $help->knowledge_base_description ?? '' }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6" data-aos="fade-up" data-aos-duration="1000">
                            <a href="{{ route('FaqsShow') }}">
                                <div class="hlp-you-box" style="height: 100%;">
                                    <div class="hlp-you-img">
                                        @if (!empty($help->help_center_image))
                                            <img src="{{ asset( $help->help_center_image) }}" alt="Help Center">
                                        @else
                                            <img src="{{ asset('front/img/hlp-img-1.svg') }}" alt="Help Center">
                                        @endif
                                    </div>
                                    <div class="hlp-you-cntnt text-center">
                                        <h3>{{ $help->help_center_title ?? 'Help Center' }}</h3>
                                        <p>{{ $help->help_center_description ?? '' }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- section FAQ -->
    <x-faq-section />
    <section class="como_secotr2  light">
        <div class="container">
            <div class="hlpcenter">
                <h2>{{ static_text('knowledge_base') }}</h2>
                <p>{{ static_text('knowledge_base_desc') }}</p>
            </div>
            <section class="knowledge_page2">
                <div class="container">
                    <div class="row knowledge_page_container row-cols-1 row-cols-md-2 g-4">
                        @foreach ($categories as $category)
                            @php
                                $translation = $category->translationForCurrentLang;
                            @endphp
                            @if ($translation)
                                <div class="col">
                                    <div class="border border_box rounded p-3 h-100">
                                        <h5 class="mb-3">
                                            {{ $translation->name }}
                                        </h5>
                                        <hr>
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($category->articles->take(3)  as $article)
                                                @php
                                                    $artTrans = $article->translationForCurrentLang;
                                                @endphp
                                                @if ($artTrans)
                                                    <li class="mb-2">
                                                        <a
                                                            href="{{ route('expert-guide-article', [
                                                                'locale' => session('lang_code', 'en-us'),
                                                                'cat_slug' => $translation->slug,
                                                                'art_slug' => $artTrans->slug,
                                                            ]) }}">
                                                            <div class="linkContainer">
                                                                <b style="display:flex; justify-content: space-between;">
                                                                    {{ $artTrans->preview_title ?? ($artTrans->title ?? 'No Title Available') }}
                                                                    <i class="fa-solid fa-chevron-right"></i>
                                                                </b>
                                                                <div class="small text-muted mt-1">
                                                                    {{ $artTrans->preview_description ?? $artTrans->preview_title }}
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                        <!-- View More Button -->
                                        <div>
                                            <a href="{{ route('expert-guide-category',['locale'=>getCurrentLocale(),'slug'=>$translation->slug])}}" >
                                                View More
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>

        </div>
    </section>
    @livewire('latest-reviews')

    <script>
        $(window).on('load', function() {
            $('body').addClass('HelpSuppPgCls');
        });
    </script>
@endsection
