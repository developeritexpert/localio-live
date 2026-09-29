@guest
<!-- section right-tool -->
<section class="right_tool_sec dark p_80" style="position: relative;">
    <div class="container">
        <div class="right-tool-wrp text-center" data-aos="fade-up" data-aos-duration="1000">
            <div class="otr_rgtool">
                <h2>{{ static_text('join_community_heading') ?: 'Join Localio' }}</h2>
                <p class="text-white size18 mt-2" style="max-width: 700px; margin: 0 auto 30px;">{{ static_text('join_community_subheading') ?: 'Write reviews, join discussions, and help others make better buying decisions.' }}</p>
            </div>
            <div class="right-tool-pack">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="tool-card">
                            <div class="tool-card-img">
                                <i class="fa-solid fa-user" style="color: #06498b; font-size: 24px;"></i>
                            </div>
                            <div class="tool-crd-bdy">
                                <p class="tool_hed">{{ static_text('join_benefit_share_exp') ?: 'Share your experience' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="tool-card">
                            <div class="tool-card-img">
                                <i class="fa-solid fa-comments" style="color: #06498b; font-size: 24px;"></i>
                            </div>
                            <div class="tool-crd-bdy">
                                <p class="tool_hed">{{ static_text('join_benefit_discussions') ?: 'Join discussions' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="tool-card">
                            <div class="tool-card-img">
                                <i class="fa-solid fa-star" style="color: #06498b; font-size: 24px;"></i>
                            </div>
                            <div class="tool-crd-bdy">
                                <p class="tool_hed">{{ static_text('join_benefit_reputation') ?: 'Build your reputation' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-tool-btn text-center">
                <a href="javascript:void(0)" onclick="openLoginModal()" class="blue-btn cta">{{ static_text('get_started_button') ?: 'Get started' }}</a>
            </div>
        </div>
    </div>

    <div class="back-image1" style="position: absolute; left: 0; bottom: 0; pointer-events: none; z-index: 1;">
        <img src="{{ asset('front/img/right-tool-vector1.png') }}" class="image-pattern1" alt="" style="max-height: 100%;">
    </div>
    <div class="back-image2" style="position: absolute; right: 0; bottom: 0; pointer-events: none; z-index: 1;">
        <img src="{{ asset('front/img/right-tool-vector2.png') }}" class="image-pattern2" alt="" style="max-height: 100%;">
    </div>
</section>
@endguest
