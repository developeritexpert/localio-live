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
                        <h1>Vendor Help</h1>
                        <p>How to find right Automative Software</p>
                        
                    </div>
                </div>
                <div class="banner_image_col">
                    <div class="banner_image">
                  
                            <img src="{{ asset('front/img/hlp-cntr-bnr.png') }}" class="banner_top_image">
                     
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hlp-you-center p_120 light">
    <div class="container">
        <!-- Header Section -->
        <div class="hlp-you-wrp text-center mb-4" data-aos="zoom-in" data-aos-duration="1000">
            <h2 class="text-center mb_30">How We Can Help You!</h2>
        </div>

        <div class="vndr_srch">
            <div data-aos="fade-up" data-aos-duration="1000">
                <div style="max-width: 600px; width: 100%;">
                    @livewire('hlep-center-search', ['placeholder' => static_text('help_search_bar_text') ?? 'How can we help you?'])
                </div>
            </div>
        </div>
     


        <!-- FAQ Section -->
        <div class="mt-5">
            <x-faq-section type="vendor" />
        </div>
    </div>
</section>


@endsection