@extends('user_layout.master')
@section('content')
@section('meta_title', isset($getListed) && isset($getListed->meta_title) ? $getListed->meta_title : 'Expert Guide')
@section('meta_description', isset($getListed) && isset($getListed->meta_description) ? $getListed->meta_description : '')

{{-- Banner Section --}}
<section class="banner_sec help-cntr-bnr inr-bnr dark" style="background-color: #003F7D;">
    <div class="bubble-wrp">
        <img src="{{ asset('front/img/small-bnnr-bg.png') }}" alt="">
    </div>
    <div class="banner_content">
        <div class="container">
            <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                <div class="banner_text_col">
                    <div class="banner_content_inner bnr_inr_contnt">
                        <h1>{{ $getListed->banner_heading }}</h1>
                        <p>{{ $getListed->banner_sub_heading }}</p>
                        <div class="top-pro-btn alrd_btn">
                            {{-- Call Vendor register list page route --}}
                            <a href="{{ route('vendor-register') }}" class="cta cta_orange">{{ $getListed->banner_button }}</a>
                        </div>
                        <div class="alrd_btn">
                            <p class="alrd_pra_text">Already had a profile? <a href="#" class="arw_lnk">Log in <span class="arw_orng"><i class="fa-solid fa-arrow-right"></i></span></a></p>
                        </div>
                    </div>
                </div>
                <div class="banner_image_col">
                    <div class="banner_image">
                        <img src="{{ asset($getListed->banner_image_left) }}" class="banner_top_image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    {{-- Section 1 --}}
    <section class="myswtr_sec light p_120">
        <div class="container">
            <div class="mysft_content" data-aos="fade-up" data-aos-duration="1000">
                <div class="row ms_rw">
                    <div class="col-md-6">
                        <div class="msft_img">
                            <img src="{{ asset($getListed->section_1_image) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="msft_text">
                            <h2 class="big-bld">{{ $getListed->section_1_title }}</h2>
                            <div class="msft_list">
                                <ul>
                                    {!! $getListed->section_1_description !!}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 2 --}}
    <section class="chatwth_sec dark bg-blue p_120">
        <div class="container">
            <div class="chatwth_content">
                <div class="row cht_rw">
                    <div class="col-md-8">
                        <div class="cht_left">
                            <h2 data-aos="zoom-in" data-aos-duration="1000">{{ $getListed->section_2_title }}</h2>
                            <div class="cht_info size18" data-aos="fade-up" data-aos-duration="1000">
                                <p>{!! $getListed->section_2_description !!}</p>
                                <div class="cht_link">
                                    <a href="#" class="arw_lnk">Learn more<span class="arw_orng"><i class="fa-solid fa-arrow-right"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="cht_img">
                            <img src="{{ asset($getListed->section_2_image) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="back-image1">
            <img src="{{ asset('front/img/right-tool-vector2.png') }}" class="image-pattern2" alt="">
        </div>
        <div class="back-image2">
            <img src="{{ asset('front/img/right-tool-vector1.png') }}" class="image-pattern1" alt="">
        </div>
    </section>

{{-- Section 3 --}}
<section class="adip_sec p_120">
    <div class="container">
        <div class="adip_content text-center">
            <div class="hd_text adip_hd mb-5">
                <h2 data-aos="zoom-in" data-aos-duration="1000">{{ $getListed->section_3_title ?? '' }}</h2>
            </div>

            <div class="adp_single_content" data-aos="fade-up" data-aos-duration="1000">
                <div class="row justify-content-center align-items-center adp_rw">
                    <div class="col-md-6">
                        <div class="adip_lft">
                            <img src="{{ asset($getListed->section_3_image) }}" alt="Section Image" class="img-fluid rounded-4">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="adip_ryt bg-white ">
                            <div class="adip_box">
                                <div class="adp_txt mb-3">
                                    <p class="size20 text-start">"{{ $getListed->section_3_description ?? '' }}"</p>
                                </div>
                                <p class="adip-btm-txt text-start">John Doe, Position Here</p>
                                {{-- <div class="str_img mt-3 text-start">
                                    <a href="#" class="wtch_imgs d-flex align-items-center">
                                        <div class="wtch_img me-2">
                                            <img src="{{ asset('front/img/wtch_utb.svg') }}" alt="Watch Icon">
                                        </div>
                                        <div class="size20">
                                            <p class="blue-text mb-0">Watch Story</p>
                                        </div>
                                    </a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Optional: Carousel-like control indicator --}}
                {{-- <div class="text-center mt-4">
                    <span class="slide-count size20">1/4</span>
                </div> --}}
            </div>
        </div>
    </div>
</section>



    {{-- Claim Section (dynamic loop) --}}
    <section class="our_busn light p_120">
        <div class="container">
            <div class="our_busn_content" data-aos="zoom-in" data-aos-duration="1000">
                <div class="hd_text">
                    <h2>{{ $getListed->section_3_title }}</h2>
                    <p>{{ $getListed->section_3_description }}</p>
                </div>

                @if(!empty($getListed->section_3_button))
                <div class="accor-btn mange-btn">
                    <a href="#" class="cta cta_white">{{ $getListed->section_3_button }}</a>
                </div>
                @endif

                <div class="row mid_rw">
                    @php
                            $claimItems = is_array($getListed->claim_section)
                            ? $getListed->claim_section
                            : json_decode($getListed->claim_section, true);
                    @endphp

                    @foreach ($claimItems ?? [] as $index => $claim)
                        <div class="col-md-4">
                            <div class="our_busn_box">
                                <div class="or_busn_bg">
                                    <h4 class="big-bld m-0">{{ $index + 1 }}</h4>
                                    @if (!empty($claim['icon']))
                                        <img src="{{ asset('images/icons/' . $claim['icon']) }}" alt="icon" class="mt-2" style="width: 32px;">
                                    @endif
                                </div>
                                <div class="our_btm_text">
                                    <h6 class="big-bld">{{ $claim['title'] ?? '' }}</h6>
                                    <p>{{ $claim['description'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>


    <x-faq-section type="vendor" />


<section class="right_tool_sec dark p_80">
   <div class="container">
      <div class="right-tool-wrp text-center" data-aos="fade-up" data-aos-duration="1000">
         <div class="otr_rgtool">
            <h2>Find the Right Tool</h2>
         </div>
         <div class="right-tool-pack">
            <div class="row">
               <div class="col-lg-4">
                  <div class="tool-card">
                     <div class="tool-card-img">
                        <img src="{{asset('front/img/right-tool-img1.png') }}" alt="">
                     </div>
                     <div class="tool-crd-bdy">
                        <h3 class="tool_hed">Verified User Reviews</h3>
                        <p class="size18">Read real feedback from verified users to help you make the right choice.
                        </p>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="tool-card">
                     <div class="tool-card-img">
                        <img src="{{asset('front/img/right-tool-img2.png') }}" alt="">
                     </div>
                     <div class="tool-crd-bdy">
                        <h3 class="tool_hed">Feature and Price Comparisons</h3>
                        <p class="size18">Easily compare software based on key features, pricing, and customer
                           ratings. </p>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="tool-card">
                     <div class="tool-card-img">
                        <img src="{{asset('front/img/right-tool-img3.png') }}" alt="">
                     </div>
                     <div class="tool-crd-bdy">
                        <h3 class="tool_hed">Independent Insights</h3>
                        <p class="size18">Access unbiased, data-driven research to get the most value from your
                           software. </p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="right-tool-btn text-center">
            <a href="{{route('category')}}" class="cta">Get Started</a>
         </div>
      </div>
   </div>
   <div class="back-image1">

      <img src="{{asset('front/img/right-tool-vector1.png') }}" class="image-pattern1" alt="">

   </div>

   <div class="back-image2">

      <img src="{{asset('front/img/right-tool-vector2.png') }}" class="image-pattern2" alt="">

   </div>
</section>

@endsection
