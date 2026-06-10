@extends('user_layout.master')
@section('content')

    <section class="pvc_sec py-5" id="pvc_section">
        <div class="container">
            @if ($privacy_policy->isNotEmpty())
                <div class="row">
                    <!-- Left Sidebar -->
                    <div class="col-md-4">
                        <div class="policy-nav">
                            <h4>{{ $privacy_policy[0]->title }}</h4>
                            <ol class="list-unstyled ps-3">
                                @foreach ($privacy_policy as $index => $policy)
                                    <li class="mb-2">
                                        <a href="#question-{{ $index }}">{{ $index + 1 }}. {{ $policy->title }}</a>
                                    </li>
                                @endforeach
                            </ol>
                            <h6 class="t_condition"><a href="{{route('terms-condition')}}">{{ $terms[0]->title }}</a></h6>
                        </div>

                    </div>

                    <!-- Right Content -->
                    <div class="col-md-8">
                        @foreach ($privacy_policy as $index => $policy)
                            <div class="mb-5">
                                <h5 id="question-{{ $loop->index }}">{{ $policy->title }}</h5>
                                <div>{!! $policy->description !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p>{{ static_text('no_policy') }}</p>
            @endif
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click event listeners to all anchor links within the policy navigation
        document.querySelectorAll('.policy-nav a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                // Get the target element
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    // Get the header height (or use your fixed value of 196px)
                    const headerHeight = 196; // Adjust this value if your header height changes

                    // Calculate the correct scroll position with offset
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerHeight;

                    // Scroll to the element with offset
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Optional: Add some visual indication that the section was clicked
                    targetElement.classList.add('highlight');
                    setTimeout(() => {
                        targetElement.classList.remove('highlight');
                    }, 2000);
                }
            });
        });
    });
    </script>

<script>
    $(window).on('load', function() {
        $('body').addClass('TrmsPricyPgCls');
    });
</script>


