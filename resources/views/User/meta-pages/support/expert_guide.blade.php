@extends('user_layout.master')

@section('meta_title', isset($expertGuide) && isset($expertGuide->meta_title) ? $expertGuide->meta_title : 'Expert Guide')
@section('meta_description', isset($expertGuide) && isset($expertGuide->meta_description) ? $expertGuide->meta_description : '')
@section('content')
<section class="banner_sec help-cntr-bnr inr-bnr dark" style="background-color: #003F7D;">
    <div class="bubble-wrp">
        <img src="{{ asset('front/img/small-bnnr-bg.png') }}" alt="">
    </div>
    <div class="banner_content">
        <div class="container ">
                {{-- <div class="banner_text_col">
                    <div class="banner_content_inner bnr_inr_contnt">
                        <h1>{{ $expertGuide->title ?? '' }}</h1>
                        <p class="expert-p">{{ $expertGuide->description ?? '' }}</p>
                    </div>
                </div> --}}

                <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                    <div class="banner_text_col">
                       <div class="banner_content_inner">
                          <h1>{{ $expertGuide->title ?? 'Expert Guide' }}</h1>
                          <p>{{ $expertGuide->description ?? 'Description about the Expert Guide' }}</p>
                          @livewire('hlep-center-search',['placeholder'=>static_text('help_search_bar_text') ?? 'Search...'])
                       </div>
                    </div>
                    <div class="banner_image_col">
                       <div class="banner_image">
                          <img src="{{asset('front/img/hlp-cntr-bnr.png') }}" class="banner_top_image">
                       </div>
                    </div>
                </div>

        </div>
    </div>
</section>

<section class="read_sec_outer expert_sec p_120 pb-0 light">
    <div class="container">
        <div class="hd_text" data-aos="zoom-in" data-aos-duration="1000">
            <h2>{{ $expertGuide->education_title ?? '' }}</h2>
            <p>{{ $expertGuide->education_description ?? '' }}</p>
        </div>
    </div>
    <div class="read_content_sec light p_50">
        <div class="container">
            <div class="row" data-aos="fade-up" data-aos-duration="1000">
                @if (isset($categories) && $categories->isNotEmpty())
                    @foreach ($categories as $category)
                        @php $translation = $category->expertGuideCategoryTranslation->first(); @endphp
                        <div class="col-lg-3 col-md-6">
                            <a class="in_cont_box"
                               href="{{ route('expert-guide-category', ['locale' => session('lang_code', 'en-us'), 'slug' => $translation->slug ?? '' ]) }}">
                                <div class="read_img">
                                    <div class="blog_thumb"><img class="r_img" src="{{ asset($category->image) }}" alt=""></div>
                                </div>
                                <div class="read_content_in">
                                    <div class="read_cont_h">
                                        <h3 class="read_text">{{ $translation->name ?? 'No Title' }}</h3>
                                    </div>
                                    <div class="read_para">
                                        <p>{!! $translation->description ?? '<p>No Description</p>' !!}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>
@livewire('latest-reviews')

<script>
    $(window).on('load', function() {
        $('body').addClass('ExpertGuidePgCls');
    });
</script>
@endsection
