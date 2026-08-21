@extends('user_layout.master')
@section('meta_title', format_meta_text(($document->title ?? ($documentTitle ?? 'Legal Notice')) . ' | Localio'))
@section('content')

    <section class="pvc_sec py-5" id="pvc_section">
        <div class="container">
            <div class="row">
                <!-- Left Sidebar -->
                <div class="col-md-3">
                    @include('User.terms_condition._sidebar', ['activeSlug' => $activeSlug ?? ''])
                </div>

                <!-- Right Content -->
                <div class="col-md-9">
                    <div class="mb-5">
                        <h2 class="mb-4">{{ $document->title ?? $documentTitle }}</h2>
                        <div class="legal-document-content">
                            {!! $document->description ?? '' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                             <p class="size18">Read real feedback from verified users to help you make the right choice.</p>
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
                             <p class="size18">Easily compare software based on key features, pricing, and customer ratings. </p>
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
                             <p class="size18">Access unbiased, data-driven research to get the most value from your software. </p>
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
              <div class="right-tool-btn text-center">
                 <a href="{{route('category')}}" class="blue-btn cta">Get Started</a>
              </div>
           </div>
        </div>
        <div class="back-image1">
           <img src="{{asset('front/img/right-tool-vector1.png') }}" class="image-pattern1" alt="">
        </div>
           <img src="{{asset('front/img/right-tool-vector2.png') }}" class="image-pattern2" alt="">
        </div>
     </section>

@endsection

<script>
    $(window).on('load', function() {
        $('body').addClass('TrmsPricyPgCls');
    });
</script>
