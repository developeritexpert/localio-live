@extends('user_layout.master')
@section('meta_title', isset($categoriesContents['meta_title']) && isset($categoriesContents['meta_title']) ? $categoriesContents['meta_title'] : 'Categories')
@section('content')
<h1>hello haia</h1>
<section class="banner_sec help-cntr-bnr inr-bnr dark " style="background-color: #003F7D;">
    <div class="bubble-wrp">

        @if ($backgroundImage)
        <img src="{{ asset($backgroundImage->meta_value) }}" alt="">
        @else
        <img src="{{ asset('front/img/small-bnnr-bg.png') }}" alt="">
        @endif


    </div>
    <div class="banner_content">
        <div class="container">
            <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                <div class="banner_text_col">
                    <div class="banner_content_inner">
                        <h1>{{ $categoriesContents['heading'] ?? '' }}</h1>
                        <p>
                            {{ $categoriesContents['description'] ?? '' }}
                        </p>
                        @livewire('search-bar', ['placeholder' => $homeContents['placeholder_text'] ?? 'Search...'])

                    </div>
                </div>
                <div class="banner_image_col">
                    <div class="banner_image">
                        @if ($headerImage)
                        <img src="{{ asset($headerImage->meta_value) }}" alt="">
                        @else
                        <img src="{{ asset('front/img/ctgry-bannr.png') }}" class="banner_top_image">
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sfwr_sec light p_120">
    <div class="container">
        <div class="sfwr_content">
            <!-- <h2 data-aos="zoom-in" data-aos-duration="1000">What type of software are you looking for?</h2> -->
            <h2 data-aos="zoom-in" data-aos-duration="1000">{{ $categoriesContents['main_heading'] ?? '' }}</h2>
            <div class="row gy-4">
                @foreach ($categories as $category)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="sfwr_box"
                        onclick="changeCategory('{{ $category->translation()->first()->slug }}')"
                        style="cursor: pointer; transition: box-shadow 0.3s ease;"
                        onmouseover="this.style.boxShadow='0 4px 20px rgba(0, 0, 0, 0.2)'"
                        onmouseout="this.style.boxShadow='none'">
                        <div class="sfwr_hd">
                            <div class="img-name">
                                <div class="sfwr_img">
                                    <img src="{{ asset(($category->imageMedia?->dir_path ?? 'images') . '/' . ($category->imageMedia?->file_name ?? 'default-category.png')) }}" alt="Category Icon">
                                </div>
                            </div>

                            <div class="sfwr_name">
                                <h6 class="big-bld">
                                    {{ $category->translations->name ?? 'No Name' }}
                                </h6>
                            </div>

                            <div class="sfwr_text">
                                <p class="list-unstyled m-0">
                                    {!! $category->translations->description ?? 'No Description Available' !!}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</section>
@livewire('latest-reviews')

<script>
    $(window).on('load', function() {
        $('body').addClass('CatgryPgCls');
    });
</script>
@endsection
