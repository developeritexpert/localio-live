@extends('user_layout.master')
@section('meta_title', 'Vendor Request Status')
@section('meta_description', 'Notification about vendor request approval')

@section('content')
<!-- Banner Section -->
<section class="banner_sec help-cntr-bnr inr-bnr dark" style="background-color: #003F7D;">
    <div class="banner_content">
        <div class="container">
            <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                <div class="banner_text_col">
                    <div class="banner_content_inner bnr_inr_contnt text-center">
                        <h1>Vendor Request Status</h1>

                    </div>
                </div>
                <div class="banner_image_col">
                    <!-- Optional banner image -->
                    <img src="{{ asset('front/img/ctgry-bannr.png') }}" alt="" class="banner_top_image">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Status Details Section -->
<section class="status_details_sec" style="padding: 80px 0; background-color: #f8f9fa;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="status_card" style="background: white; border-radius: 10px; padding: 40px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <div class="status_header text-center mb-4">
                        <div class="status_icon" style="font-size: 60px; color: #28a745; margin-bottom: 20px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 style="color: #003F7D; margin-bottom: 15px;">Request Sent to Admin</h2>
                        <p class="lead" style="color: #6c757d;">our vendor application has been submitted to the admin. The admin will review your request and either approve or reject it.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary mt-4" style="padding: 10px 30px; font-size: 16px;">
                            Back to Home
                        </a>

                </div>
            </div>
        </div>
    </div>
</section>


@endsection
