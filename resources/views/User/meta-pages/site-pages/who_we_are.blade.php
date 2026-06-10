@extends('user_layout.master')
@section('meta_title', isset($whoWeAre) && isset($whoWeAre->meta_title) ? $whoWeAre->meta_title : 'Who We Are')
@section('meta_description', isset($whoWeAre) && isset($whoWeAre->meta_description) ? $whoWeAre->meta_description : '')

@section('content')
<section class="banner_sec help-cntr-bnr inr-bnr dark" style="background-color: #003F7D;">

    <div class="banner_content">
        <div class="container">
            <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                <div class="banner_text_col">
                    <div class="banner_content_inner bnr_inr_contnt">
                        <h1>{{ $whoWeAre->main_heading ?? '' }}</h1>
                        <p>{{ $whoWeAre->sub_heading ?? '' }}</p>
                    </div>
                </div>
                {{-- <div class="banner_image_col" style="display: flex; align-items: center; justify-content: center;">
                    <div style="position: relative; display: flex; justify-content: flex-start; align-items: flex-end; gap: 59px; padding: 40px;">
                        <img
                            src="{{ isset($whoWeAre->top_left_section_img) ? asset($whoWeAre->top_left_section_img) : asset('default-left.jpg') }}"
                            alt="Left Image"
                            style="width: 280px; height: auto; border-radius: 20px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2); z-index: 2; transform: translateY(-30px);"
                        >
                        <img
                            src="{{ isset($whoWeAre->top_right_section_img) ? asset($whoWeAre->top_right_section_img) : asset('default-right.jpg') }}"
                            alt="Right Image"
                            style="width: 280px; height: auto; border-radius: 20px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2); margin-left: -40px; z-index: 1;"
                        >
                    </div>
                </div> --}}


                <div class="banner_image_col">
                    <div class="banner_image">
                        @if ($whoWeAre->top_left_section_img)
                        <img src="{{ asset($whoWeAre->top_left_section_img) }}" alt="">
                        @else
                        <img src="{{ asset('front/img/ctgry-bannr.png') }}" class="banner_top_image">
                        @endif

                    </div>
                </div>


            </div>
        </div>
    </div>
</section>

<!-- section most-popular -->
<section class="most-populr-sec ms_dv light p_120">
    <div class="container">
        <div class="most-popular-wrp">
            <div class="hd_text">
                <h2 data-aos="zoom-in" data-aos-duration="1000" class="text-center">{{ $whoWeAre->mp_heading ?? '' }}</h2>
                <p>{{ $whoWeAre->mp_sub_heading ?? '' }}</p>
            </div>
            <div class="popular-accordion-wrp mst_wrp" data-aos="fade-up" data-aos-duration="1000">
                <div class="row align-items-center ">
                    <div class="col-md-6">
                        <div class="accor-lft-img">
                            <img src="{{ !empty($whoWeAre->top_card_image) ? asset($whoWeAre->top_card_image) : asset('default-image.jpg') }}" alt="">


                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="accor-txt-contnt">
                            <h6>{{ $whoWeAre->top_card_title ?? '' }}</h6>
                            <p>{{ $whoWeAre->top_card_desc ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>


                <div class="new_objectives">
                    <div class="container">


                    <div class="row">
                        @forelse($pageTileTranslationPopular as $index => $item)
                            @foreach ($item->translations as $translation)
                                <div class="col-lg-4">
                                    <div class="inner_part_boxx">
                                        <div class="part_1_boxs">
                                            @if ($item->image)
                                            <img class="img-fluid" style="max-height: 80px;"
                                            src="{{ asset($translation->image ?? 'default-image.jpg') }}" alt="">
                                            @endif
                                        </div>
                                        <div class="part_2_boxs">
                                            <h6 class="big-bld">{{ $translation->title ?? '' }}</h6>
                                            <p>{{ $translation->description ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @empty

                            @endforelse
                    </div>
                </div>
                </div>

            </div>

        </div>
    </div>
</section>

<section class="succes_sec p_120">
    <div class="container">
        <div class="succes_content">
            <div class="hd_text" data-aos="zoom-in" data-aos-duration="1000">
                <h2>{{ $whoWeAre->specialists_heading ?? '' }}</h2>
            </div>
            <div class="row succes_rw">

                @foreach ($specilistTileTranslation as $index => $item)
                <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="succs_box">
                        <div class="succes_img">
                            <img src="{{ asset($item->translations->first()->img) }}">
                        </div>
                        <div class="succes_infp">
                            <div class="succes_text">
                                <h6 class="big-bld">{{ $item->translations->first()->title ?? '' }}</h6>
                                <p>{{ $item->translations->first()->description ?? '' }}
                                </p>
                            </div>
                            <div class="succs_grp">
                                <img src="{{ asset($item->translations->first()->small_img) }}">
                            </div>

                        </div>

                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<section class="portf_sec p_120">
    <div class="container">
        <div class="succes_content" data-aos="fade-up" data-aos-duration="1000">
            <h2>{{ $whoWeAre->ss_heading ?? '' }}</h2>
            <p>{{ $whoWeAre->ss_sub_desc ?? '' }}
            </p>
            <div class="top-pro-btn  snd_bttn">
                <a  class="cta cta_orange">{{ $whoWeAre->protfolio_btn ?? '' }}</a>
            </div>
        </div>
    </div>
</section>

<script>
    $(window).on('load', function() {
        $('body').addClass('WhoWeArePgCls');
    });
</script>
@endsection
