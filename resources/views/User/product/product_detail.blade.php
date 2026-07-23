@extends('user_layout.master')
@section('body_class', 'product-page-body')

@section('meta_title', isset($business->translations->first()->name) && isset($business->translations->first()->name) ?
    $business->translations->first()->name : 'Products')
@section('content')
    @php
        $images = is_array($business->business_images)
            ? $business->business_images
            : json_decode($business->business_images ?? '[]', true);
    @endphp
    <!-- Modal script driver (Declared early to prevent race conditions on render) -->

    <script>
        console.log("Gallery script block rendered");
        
        let modalImages = {!! json_encode($images) !!};
        let currentModalIndex = 0;

        function updateModalImage(index) {
            console.log("updateModalImage called with index:", index);
            currentModalIndex = parseInt(index, 10);
            if (currentModalIndex >= 0 && currentModalIndex < modalImages.length) {
                let imgUrl = '{{ asset("") }}' + modalImages[currentModalIndex].replace(/^\/+/, '');
                console.log("Updating modal active image src to:", imgUrl);
                let activeImg = document.getElementById('modalActiveImg');
                if (activeImg) {
                    activeImg.src = imgUrl;
                } else {
                    console.error("modalActiveImg element not found");
                }
                
                const thumbs = document.querySelectorAll('.modal-thumb-item');
                thumbs.forEach(thumb => {
                    thumb.classList.remove('active-thumb');
                    thumb.style.borderColor = 'transparent';
                    thumb.style.opacity = '0.6';
                });
                
                const activeThumb = document.querySelector(`.modal-thumb-item[data-index="${currentModalIndex}"]`);
                if (activeThumb) {
                    activeThumb.classList.add('active-thumb');
                    activeThumb.style.borderColor = '#007bff';
                    activeThumb.style.opacity = '1';
                } else {
                    console.warn("Active thumbnail element not found for index:", currentModalIndex);
                }
            } else {
                console.error("Invalid currentModalIndex:", currentModalIndex, "Images count:", modalImages.length);
            }
        }

        window.openGallery = function (index) {
            console.log("Global openGallery called with index:", index);
            try {
                updateModalImage(index);
                const modalEl = document.getElementById('imageGalleryModal');
                if (modalEl) {
                    console.log("Found modalEl, initializing Bootstrap Modal instance");
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                        console.log("Bootstrap modal.show() invoked successfully");
                    } else {
                        console.error("bootstrap or bootstrap.Modal is undefined! Make sure bootstrap bundle JS is loaded.");
                        // Fallback in case bootstrap object is missing:
                        modalEl.classList.add('show');
                        modalEl.style.display = 'block';
                        document.body.classList.add('modal-open');
                        let backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                        console.log("Applied fallback modal display style rules");
                    }
                } else {
                    console.error("imageGalleryModal element not found");
                }
            } catch (err) {
                console.error("Error in openGallery function:", err);
            }
        };

        window.addEventListener('load', function () {
            console.log("Gallery window load event fired. Images parsed:", modalImages);

            const thumbContainer = document.getElementById('modalThumbContainer');
            if (thumbContainer) {
                thumbContainer.addEventListener('click', function (e) {
                    const thumbItem = e.target.closest('.modal-thumb-item');
                    if (thumbItem) {
                        const index = thumbItem.getAttribute('data-index');
                        console.log("Modal thumbnail clicked. Index:", index);
                        updateModalImage(index);
                    }
                });
            }

            const prevBtn = document.getElementById('modalPrevBtn');
            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    let nextIndex = (currentModalIndex - 1 + modalImages.length) % modalImages.length;
                    console.log("Modal prev button clicked. Next index:", nextIndex);
                    updateModalImage(nextIndex);
                });
            }

            const nextBtn = document.getElementById('modalNextBtn');
            if (nextBtn) {
                nextBtn.addEventListener('click', function () {
                    let nextIndex = (currentModalIndex + 1) % modalImages.length;
                    console.log("Modal next button clicked. Next index:", nextIndex);
                    updateModalImage(nextIndex);
                });
            }

            document.addEventListener('keydown', function (e) {
                const modalEl = document.getElementById('imageGalleryModal');
                if (modalEl && modalEl.classList.contains('show')) {
                    if (e.key === 'ArrowLeft') {
                        let nextIndex = (currentModalIndex - 1 + modalImages.length) % modalImages.length;
                        console.log("Left arrow pressed. Next index:", nextIndex);
                        updateModalImage(nextIndex);
                    } else if (e.key === 'ArrowRight') {
                        let nextIndex = (currentModalIndex + 1) % modalImages.length;
                        console.log("Right arrow pressed. Next index:", nextIndex);
                        updateModalImage(nextIndex);
                    }
                }
            });
        });
    </script>

        @livewire('add-review')
        <style>

             /* Responsive CSS for Gallery Modal */
             #imageGalleryModal .modal-dialog {
                 max-width: 95%;
                 width: 1400px;
                 transition: all 0.3s ease;
             }
             .gallery-header {
                 padding: 24px 32px 16px 32px;
                 display: flex;
                 justify-content: space-between;
                 align-items: center;
                 border-bottom: 1px solid #f0f0f0;
                 background: #ffffff;
             }
             .gallery-header-left {
                 display: flex;
                 align-items: center;
                 gap: 16px;
             }
             .gallery-header-cta {
                 margin-right: 40px;
             }
             .gallery-body {
                 height: 60vh;
                 display: flex;
                 align-items: center;
                 justify-content: center;
                 background: #fafafa;
                 position: relative;
                 padding: 24px;
             }
             .gallery-thumbnails {
                 padding: 16px 32px;
                 border-top: 1px solid #f0f0f0;
                 background: #ffffff;
                 display: flex;
                 justify-content: center;
                 gap: 12px;
                 overflow-x: auto;
                 flex-wrap: wrap;
             }

             .gallery-image-wrap {
                 width: 100%;
                 height: 100%;
                 display: flex;
                 align-items: center;
                 justify-content: center;
                 border-radius: 8px;
                 overflow: hidden;
                 padding-inline: 48px;
             }
             .crm_sec.revie_left_rgt_sec .review_text p:last-child{
                font-size:16px;
                margin-bottom:0;
             }


             @media (max-width: 768px) {
                 #imageGalleryModal .modal-dialog {
                     max-width: 98% !important;
                     width: auto !important;
                     margin: 10px auto !important;
                 }
                 .gallery-header {
                     padding: 16px !important;
                     flex-direction: column !important;
                     align-items: flex-start !important;
                     gap: 12px !important;
                 }
                 .gallery-header-cta {
                     margin-right: 0 !important;
                     width: 100% !important;
                 }
                 .gallery-header-cta a {
                     width: 100% !important;
                     justify-content: center !important;
                 }
                 .gallery-body {
                     height: 50vh !important;
                     padding: 12px !important;
                 }
                 .gallery-image-wrap {
                     padding-inline: 16px !important;
                 }
                 #imageGalleryModal .btn-close {
                     right: 16px !important;
                     top: 16px !important;
                 }
                 .gallery-thumbnails {
                     padding: 12px !important;
                     gap: 8px !important;
                 }
                 .modal-thumb-item {
                     width: 60px !important;
                     height: 40px !important;
                 }
                 #modalPrevBtn, #modalNextBtn {
                     width: 36px !important;
                     height: 36px !important;
                     font-size: 14px !important;
                 }
                 #modalPrevBtn {
                     left: 12px !important;
                 }
                 #modalNextBtn {
                     right: 12px !important;
                     margin-right: 0 !important;
                 }
             }

             /* Extra small screens (320px - 480px) */
             @media (max-width: 480px) {
                 .gallery-header {
                     padding: 12px !important;
                     gap: 8px !important;
                 }
                 .gallery-header-left {
                     gap: 12px !important;
                 }
                 .gallery-header-left img {
                     width: 44px !important;
                     height: 44px !important;
                 }
                 .gallery-header-left h3 {
                     font-size: 18px !important;
                 }
                 .gallery-header-left div div {
                     font-size: 12px !important;
                 }
                 .gallery-body {
                     height: 40vh !important;
                     padding: 8px !important;
                 }
                 .gallery-image-wrap {
                     padding-inline: 8px !important;
                 }
                 #modalPrevBtn, #modalNextBtn {
                     width: 30px !important;
                     height: 30px !important;
                     font-size: 11px !important;
                 }
                 #modalPrevBtn {
                     left: 6px !important;
                 }
                 #modalNextBtn {
                     right: 6px !important;
                 }
                 #imageGalleryModal .btn-close {
                     font-size: 16px !important;
                     right: 12px !important;
                     top: 12px !important;
                  }
              }

             /* Sidebar review cards styling */
             .sidebar-review-card .review-header {
                 display: flex;
                 justify-content: space-between;
                 align-items: center;
                 gap: 12px;
             }
             .sidebar-review-card .review-user {
                 display: flex;
                 align-items: center;
                 gap: 12px;
             }
             .sidebar-review-card .rating-stars {
                 display: flex !important;
                 gap: 2px;
                 white-space: nowrap !important;
             }
             @media (max-width: 480px) {
                 .sidebar-review-card .review-header {
                     flex-direction: column !important;
                     align-items: flex-start !important;
                     gap: 6px !important;
                 }
                 .sidebar-review-card .review-header small {
                     margin-left: 57px !important; /* Offset by avatar width + gap */
                     margin-top: -4px !important;
                     color: #888 !important;
                 }
              }

             /* Sticky Reviews Filter Sidebar Card */
             .review-sidebar-sticky {
                 position: sticky;
                 top: 20px;
             }
             @media (max-width: 991px) {
                 .review-sidebar-sticky {
                     background: #ffffff;
                     border: 1px solid #f2f4f8;
                     border-radius: 12px;
                     box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
                     position: relative;
                     top: 0;
                     margin-bottom: 30px;
                     padding: 20px;
                 }
             }

                #section1 .asn_dv {
                padding-bottom: 0;
               }
               .slider-for:not(.slick-initialized) .asan-slider-inr:not(:first-child) {
                   display: none !important;
               }
               .slider-nav:not(.slick-initialized) > div:not(:first-child) {
                   display: none !important;
               }
            .Tab-outerlnk.container-fluid {
                padding: 0;
            }
            /* .asn_dv .Tab-outerlnk #table-of-content ul li a:hover {
                background: #06498b0d;
            } */
            .asan-slider.asan-slider-btm.slider-nav {
    padding: 0;
}

.asan-slider.asan-slider-btm.slider-nav .slick-list {
    width: 100%;
}

.asan-slider.asan-slider-btm.slider-nav .slick-track {
    display: flex;
}

.asan-slider.asan-slider-btm.slider-nav .slick-track .slick-slide.slick-active {
    margin: 0 5px  !important;
}
.new-visit-anc .cta.cta_orange {
    font-size: 13px;
    padding: 18px 22px;
    width: 195px;
    justify-content: center;
}
.new-review-side .rate_box {
    font-size: 13px !important;
}
          @media (max-width: 1599px) {
                .asn_dv .Tab-outerlnk #table-of-content{
                    padding-inline: 15px !important;
                }
            }
            @media (max-width: 575px) {
             .frst_rw {
                    gap: 20px 0;
                }
                .new-visit-anc .cta.cta_orange {
                font-size: 12px;
                width: fit-content;
            }
            }

            /* pros and cons  */

               .review-breakdown-box{
                padding:22px;
            }

            .review-breakdown-box h2{
                margin-bottom:18px;
            }
/* 
            .compact-item{
                display:flex;
                align-items:center;
                gap:12px;
                padding:10px 0;
                border-bottom:1px solid #ececec;
            } */
            .compact-item {
                display: flex;
                gap: 12px;
                padding: 4px 0;
                align-items: center;
            }
            .feture_box  h2 {
                font-size: 20px !important;
            }
            .pros-cons-box .compact-item p {
                text-align: left;
                font-size: 13px;
            }
            .compact-item:last-child{
                border-bottom:none;
            }

            .compact-item p{
                margin:0;
                font-size:14px;
                font-weight:500;
                color:#222;
                line-height:1.4;
                flex:1;
            }

            .greenfonticon,
            .redboxicon{
                /* width:26px;
                height:26px; */
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                flex-shrink:0;
            }

            .greenfonticon{
                /* background:#eaf9f0; */
                color:#28a745;
            }

            .redboxicon{
                background:#fdecec;
                color:#dc3545 !important;
            }
            .innr_pr .redboxicon {
                background: transparent !important;
            }
            i.fa-solid.fa-check {
                color: #15c731 !important;
            }

            ::before {}

            i.fa-solid.fa-minus {
                color: #ff0000 !important;
            }

            
            .greenfonticon i,
            .redboxicon i{
                font-size:12px;
            }

            /* side review */
            .review-breakdown-box{
    padding:25px;
}

.sidebar-review-card{
    padding:18px 0;
    border-bottom:1px solid #ececec;
}

.sidebar-review-card:last-child{
    border-bottom:none;
    padding-bottom:0;
}

.review-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:12px;
}

.review-user{
    display:flex;
    gap:12px;
}

.review-user h6{
    margin:0 0 5px;
    font-size:15px;
    font-weight:600;
}

.review-user img{
    object-fit:cover;
}

.sidebar-review-card h5{
    font-size:15px;
    font-weight:600;
    margin-bottom:8px;
    color:#002655;
}

.sidebar-review-card p{
    margin:0;
    font-size:14px;
    color:#666;
    line-height:1.6;
}

.rating-stars i{
    font: size 12px;
}

.main_feture .feture_box {
    box-shadow: 0 8px 24px rgb(141 143 144 / 28%);
}


.review-breakdown-card{
    padding:24px;
    border-radius:14px;
}

.review-header-box{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:18px;
}

.review-header-box h5{
    margin:0;
    font-size:18px;
    font-weight:700;
    color:#002655;
}

.review-header-box small{
    display:block;
    margin-top:3px;
    color:#777;
    font-size:13px;
}

.view-review-link{
    color:#002655;
    font-size:13px;
    font-weight:600;
    text-decoration:none;
}

.view-review-link:hover{
    text-decoration:underline;
}

.overall-rating-box{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:20px;
}

.overall-rating-number{
    font-size:30px;
    font-weight:600;
    color:#222;
    line-height:1;
}

.breakdown-title{
    font-size:16px;
    color:#002655;
    font-weight:600;
    margin-bottom:16px;
}

.review-progress-item{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:18px;
    gap:15px;
}

.review-progress-item span{
    width:150px;
    font-size:13px;
    font-weight:500;
    color:#444;
}

.progress-wrap{
    flex:1;
    display:flex;
    align-items:center;
    gap:10px;
}

.progress{
    flex:1;
    height:8px;
    background:#edf6ed;
    border-radius:30px;
    overflow:hidden;
}

.progress-bar{
    background:#199429;
    border-radius:30px;
}

.progress-wrap strong{
    width:42px;
    text-align:right;
    font-size:12px;
    color:#333;
    font-weight:600;
}

.rating-stars i{
    font-size:12px;
}


.str_prc_box{
    background:#fff;
    border:1px solid #e9ecef;
    border-radius:14px;
    padding:28px 20px;
    text-align:center;
    display:flex;
    flex-direction:column;
    justify-content:center;
    min-height:220px;
}

.starting-price-title{
    font-size:20px;
    font-weight:600;
    color:#002347;
    margin-bottom:25px;
}

.starting-price-value{
    font-size:42px;
    font-weight:700;
    color:#002655;
    line-height:1;
    margin-bottom:12px;
}

.starting-price-text{
    font-size:14px;
    color:#8a8a8a;
    margin-bottom:35px;
}

.starting-price-link{
    color:#002655;
    font-size:15px;
    font-weight:600;
    text-decoration:none;
    transition:.3s;
}

.starting-price-link:hover{
    color:#002655;
    text-decoration:underline;
}
.feture_box.str_prc_box .starting-price-text {
    text-align: center !important;
    color:#444444;
    font-size:12px;
}

.thre_revi_rgt .feture_box  h2 {
    font-size: 16px !important;
}
/* Rating hidden in normal view, visible only in sticky scroll header */
.main-view-rating-hide {
    display: none !important;
}
.fixed-div .main-view-rating-hide {
    display: flex !important;
    align-items: center;
    gap: 8px;
    margin-top: 4px;
}
.fixed-div .hide-on-sticky {
    display: none !important;
}


            /* 20-0702026 */
            h5.card-title.mb-3 {
                font-size: 24px !important;
                font-weight: 600 !important;
                line-height: 1.3 !important;
                color: #002347 !important;
            }

            /* 21-july-26 */
            .asan-slider.asan-slider-btm .slick-track .hover_main , 
            .asan-slider.asan-slider-btm .slick-track .slick-slide:hover {
                border: 1px solid rgb(0, 0, 0);
                border-radius: 6px !important;
            }
    </style>
    <div data-business-id="{{ $business->id }}">
        <section class="product_sec">
            <div class="inner_banner_sec">
                <div class="container-fluid" style="display: flex; justify-content: space-between;">
                    <div class="inner_banr_content">
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/' . (request()->segment(1) ?? 'en-us') . '/categories') }}"
                                       style="color: inherit; transition: none;" onmouseover="this.style.color='#f26522'"
                                       onmouseout="this.style.color=''">All</a>
                                </li>
                                @if ($business->category && $business->category->parent)
                                    @php
                                        $parentTranslation = $business->category->parent->translation()->first();
                                    @endphp
                                    @if ($parentTranslation)
                                        <li class="breadcrumb-item">
                                            <a href="javascript:void(0);"
                                               onclick="changeCategory('{{ $parentTranslation->slug }}')"
                                               style="color: inherit; transition: none;" onmouseover="this.style.color='#f26522'"
                                               onmouseout="this.style.color=''">
                                                {{ $parentTranslation->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endif
                                @if ($business->category)
                                    @php
                                        $categoryTranslation = $business->category->translation()->first();
                                    @endphp
                                    @if ($categoryTranslation)
                                        <li class="breadcrumb-item active" aria-current="page">
                                            <!-- {{ $categoryTranslation->name }} -->
                                            <a href="javascript:void(0);"
                                               onclick="changeCategory('{{ $categoryTranslation->slug }}')"
                                               style="color: inherit; transition: none;" onmouseover="this.style.color='#f26522'"
                                               onmouseout="this.style.color=''">
                                                {{ $categoryTranslation->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endif
                            </ol>
                        </nav>
                    </div>
                    <div class="inside_sec_text">
                        <x-social-icon />
                    </div>
                </div>
            </div>

        </section>

        <!-- Products Section -->
        <section class="asn_main_sec asn_main_sec_2" id="section1">
            <div class="asn_dv">
                @php
                    $activeReviews = $business->reviews->where('status', 'active');
                    $ratingCount = $activeReviews->count();
                    $averageRating = $ratingCount > 0 ? $activeReviews->avg('rating') : 0;
                @endphp
                <div class="container-fluid">
                    <div class="asn_dv_contnt ">
                        <div class="asn-img">
                            <img src="{{ asset($business->icon_id) }}" alt="{{ $business->translations->first()->name }}">
                        </div>
                        <div class="div_prent_ever">



                            <div class="row frst_rw align-items-center">
                                <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="ans_lft">
                                        <div class="asn-rating">
                                            <div class="an_lkd">
                                                <h1 style="color: #000;" class="mb-0 p-1">
                                                    {{ $business->translations->first()->name }} <span class="hide-on-sticky">Review {{ date('Y') }}</span> </h1>

                                                {{-- <h6 style="color: #000;" class="mb-0 p-1">Review
                                                    {{ $business->created_at->format('Y') }}</h6> --}}

                                                <livewire:wishlist :product-id="$business->id"
                                                    :wire:key="'wishlist-'.$business->id" />

                                            </div>
                                            <p class="text-muted size16  hide-on-sticky" style="color: #666; font-size: 16px;  margin-bottom: 0;">Real reviews, community discussions & alternatives</p>
                                            <div class="main-view-rating-hide">
                                                <div style="display: flex; gap: 2px;">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= floor($averageRating))
                                                            <i class="fas fa-star text-warning" style="font-size: 14px;"></i>
                                                        @elseif ($i - 0.5 <= $averageRating)
                                                            <i class="fas fa-star-half-alt text-warning" style="font-size: 14px;"></i>
                                                        @else
                                                            <i class="far fa-star text-warning" style="font-size: 14px;"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span style="font-size: 14px; font-weight: 500; color: #555;">
                                                    {{ number_format($averageRating, 1) }} | {{ $ratingCount }} {{ $ratingCount === 1 ? 'Review' : 'Reviews' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="ans_ryt">
                                        <div class="site_vsit">
                                            <ul class="list-unstyled">
                                            </ul>
                                            <div class="top-pro-btn tp_visit new-visit-anc">
                                                <a href="{{ $business->getTrackedUrl() }}"
                                                    data-track="{{ json_encode([
                                                        'type' => 'click',
                                                        // 'product_id' => $product->id,
                                                        'business_id' => $business->id,
                                                        'action' => 'visit_website',
                                                        'label' => 'Visit Website',
                                                    ]) }}"
                                                    class="cta cta_orange d-flex align-items-center" target="_blank"
                                                    tabindex="0">
                                                    Visit website
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                </a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="frst_re_2 row">
                                <div class="tp-btm d-flex col-lg-6">
                                    {{-- add under this class an_lkd --}}
                                    {{-- <p class="size20 m-0">
                                        Pricing, Features, Alternatives & User Reviews
                                    </p> --}}
                                </div>

                                {{-- replace this under the visit button top-pro-btn tp_visit  --}}

                                @php
                                    $ratingCount = $business->reviews->where('status', 'active')->count();
                                @endphp
                                <!-- <div class="right_bottom col-lg-6 hd_str"
                                     @auth
                                     onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                     @else
                                     onclick="openLoginModal()"
                                     @endauth>



                                    {{-- Change Review  --}}
                                    <div class="inn_ul">
                                        <div class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= floor($averageRating))
                                                    <i class="fas fa-star text-warning"></i>
                                                @elseif ($i - 0.5 <= $averageRating)
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="rate_box">
                                        {{ number_format($averageRating, 1) }} |
                                        @if ($ratingCount === 0)
                                            0 Ratings
                                        @elseif ($ratingCount === 1)
                                            1 Rating
                                        @else
                                            {{ $ratingCount }} Ratings
                                        @endif
                                    </div>
                                </div>  -->

                            </div>
                        </div>
                    </div>
                </div>
                 {{-- Added the change Stacture content table code --}}
                 <div class="Tab-outerlnk container-fluid">
                    <div class="inner_table2">

                        @php

                            $name = $business->translations->first()->name ?? 'This Business';

                            // Fixed dynamic top content
                            $staticBottomSections = [
                                // ['id' => 'section9', 'label' => "Software like"],
                                // ['id' => 'section15', 'label' => 'FAQ'],
                                ['id' => 'section9', 'label' => 'Alternatives'],
                                ['id' => 'section15' , 'label' => 'FAQs'],  

                                ['id' => 'section14', 'label' => "Reviews"],
                                ['id' => 'sectionDiscussions', 'label' => "Discussions"],
                                
                                // ['id' => 'section16', 'label' => 'Inbox'],
                            ];

                            // Dynamic middle sections from topics
                            // Start dynamic topic sections at a high number to avoid conflict
                            $dynamicTopics = collect($business->category->topics ?? [])
                                ->map(function ($topic, $index) use ($name) {
                                    $translatedHeading =
                                        $topic->translations->first()?->title ?? 'Topic ' . ($index + 1);
                                    $label = str_replace('{business_name}', $name, $translatedHeading);

                                    return [
                                        'id' => 'section' . (100 + $index), // Avoid conflicts with static IDs
                                        'label' => $label,
                                    ];
                                })
                                ->toArray();

                            // dd($dynamicTopics);

                            // Fixed bottom static sections

                            $staticTopSections = [
                                ['id' => 'section1', 'label' => 'Description'],
                                // ['id' => 'section2', 'label' => "$name"],
                                // ['id' => 'section3', 'label' => "Pricing"],
                                // ['id' => 'section4', 'label' => "Pros & cons"],
                                // ['id' => 'features' , 'label' => 'Features'],
                                // ['id' => 'section15' , 'label' => 'FAQs'],  
                                // ['id' => 'softweretopic', 'label' => 'Software Topic'],
                                // ['id' => 'business-integration', 'label' => 'Integration']

                            ];

                            $tableOfContents = array_merge(
                                $staticTopSections,
                                //$dynamicTopics,
                                $staticBottomSections,
                            );
                        @endphp

                        <div class="inner_table2">
                            <div class="table_st">
                                <div id="table-of-content" class="feture_box p-3 shadow  bg-white bar-option"
                                    style="top: 90px; max-height: max-content; overflow-y: auto;">
                                    <ul class="list-unstyled toc-links small">
                                        @foreach ($tableOfContents as $i => $item)
                                            @php
                                                $isLastDynamic =
                                                    str_starts_with($item['id'], 'section') &&
                                                    (int) filter_var($item['id'], FILTER_SANITIZE_NUMBER_INT) ===
                                                        100 + count($dynamicTopics) - 1;
                                            @endphp

                                            <li class=" {{ $isLastDynamic ? 'mb-0' : '' }}">
                                                <a href="#{{ $item['id'] }}" class="text-blue-600 d-block">
                                                    {{ $item['label'] }}
                                                </a>
                                            </li>

                                             @if ($isLastDynamic)
                                                <li class="my-1"></li>
                                            @endif
                                        @endforeach


                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                {{-- End this table content Stacture code --}}

            </div>
           
        </section>


              <section class="revie_img_sec">
                   <div class="container">
                       <div class="image_revie_inr">
                            <div class="is-asana-wrp imges_left_sde" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="row sld_rw">
                                        <div class="col-lg-12">
                                            <div class="is-asana-lft">
                                                <h2>What is {{ $business->translations->first()->name }}</h2>
                                                <div class="is_text">
                                                    {!! $business->translations->first()->description !!}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PROS & CONS SECTION --}}
                                        @if($business->proCons->count() > 0)
                                        <div class="col-lg-12 mt-4 mb-4">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-3" style="font-weight: 700;">Pros</h5>
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach($business->proCons->where('type', 'pro') as $pro)
                                                                <li class="d-flex align-items-start mb-2">
                                                                    <span class="me-2" style="font-size: 18px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                                    <span>{{ $pro->text }}</span>
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-3" style="font-weight: 700;">Cons</h5>
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach($business->proCons->where('type', 'con') as $con)
                                                                <li class="d-flex align-items-start mb-2">
                                                                    <span class="me-2" style="font-size: 18px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                                    <span>{{ $con->text }}</span>
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(!empty($business->pro_cons_summary))
                                            <div class="mt-3 text-muted" style="font-size: 14px;">
                                                <em>{{ $business->pro_cons_summary }}</em>
                                            </div>
                                            @endif
                                        </div>
                                        @endif

                                        {{-- OFFERINGS SECTION --}}
                                        @if($business->offerings->count() > 0)
                                        @php $offering = $business->offerings->first(); @endphp
                                        <div class="col-lg-12 mt-4 mb-4">
                                            <div class="offering-section mb-5">
                                                @if($offering->headline)
                                                    <h3 class="mb-3" style="font-weight: 700;">{{ $offering->headline }}</h3>
                                                @endif
                                                
                                                @if($offering->top_text)
                                                    <div class="mb-3">
                                                        {{ $offering->top_text }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        {{-- <div class="col-lg-12">
                                    <div class="is-asana-rgt">
                                        <div class="is-asan-slider">
                                            <div class="asan-slider slider-for">
                                                <div class="asan-slider-inr"><img
                                                        src="{{ asset('front/img/video-img.png') }}" alt="">
                                                    </div>
                                                    <div class="asan-slider-inr"><img
                                                            src="{{ asset('front/img/video-img3.png') }}" alt="">
                                                    </div>
                                                    <div class="asan-slider-inr"><img
                                                            src="{{ asset('front/img/video-img.png') }}" alt="">
                                                    </div>
                                                    <div class="asan-slider-inr"><img
                                                            src="{{ asset('front/img/video-img3.png') }}" alt="">
                                                    </div>
                                                    <div class="asan-slider-inr"><img
                                                            src="{{ asset('front/img/video-img.png') }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="asan-slider asan-slider-btm slider-nav">
                                                    <div><img src="{{ asset('front/img/small-video-img.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div><img src="{{ asset('front/img/sm-video-img3.png') }}"
                                                            alt=""></div>
                                                    <div><img src="{{ asset('front/img/small-video-img.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div><img src="{{ asset('front/img/sm-video-img3.png') }}"
                                                            alt=""></div>
                                                    <div><img src="{{ asset('front/img/small-video-img.png') }}"
                                                            alt="">
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    </div> --}}


                                        {{-- Business Image slider --}}
                                        @php
                                            // Safely decode the business_images
                                            $images = is_array($business->business_images)
                                                ? $business->business_images
                                                : json_decode($business->business_images ?? '[]', true);
                                        @endphp

                                         @if (!empty($images))
                                            <div class="col-lg-12">
                                                <div class="is-asana-rgt">
                                                    <div class="row is-asan-slider">
                                                        <!-- Main Slider -->
                                                        <div class="col-md-12 asan-slider slider-for">
                                                            @foreach ($images as $index => $image)
                                                                <div class="asan-slider-inr" style="cursor: pointer;">
                                                                    <img src="{{ asset($image) }}"
                                                                        onclick="openGallery({{ $index }})"
                                                                        alt="Business Image {{ $index + 1 }}"
                                                                        style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px;">
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Thumbnail Slider -->
                                                        <div class="col-md-12 asan-slider asan-slider-btm slider-nav"
                                                            style="margin-top: 15px !important;">
                                                            @foreach ($images as $index => $image)
                                                                <div style="padding: 0 5px; cursor: pointer;">
                                                                    <img src="{{ asset($image) }}"
                                                                        alt="Thumbnail {{ $index + 1 }}"
                                                                        style="width: 150px; height: 100px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 2px solid transparent;">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                         @endif

                                         <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Wait for slick to initialize
                                                setTimeout(() => {
                                                    var $slider = $('.asan-slider-btm');
                                                    
                                                    // Give the active slide the class initially
                                                    $slider.find('.slick-current').addClass('hover_main');
                                                    
                                                    // On hover, add class and remove from others
                                                    $slider.on('mouseenter', '.slick-slide', function() {
                                                        $slider.find('.slick-slide').removeClass('hover_main');
                                                        $(this).addClass('hover_main');
                                                    });
                                                }, 500);
                                            });
                                         </script>
                                         {{-- End Business Images  --}}

                                         @if (!empty($business->translations->first()->after_image_description))
                                             <div class="col-lg-12 mt-4 after-image-desc">
                                                 <div class="is_text">
                                                     {!! $business->translations->first()->after_image_description !!}
                                                 </div>
                                             </div>
                                         @endif

                                    </div>
                                </div>
                            </div>
                            <div class="thre_revi_rgt">
                                <!-- <div class="lcl_text" style="margin-bottom: 20px;">
                                    <p class="sml_text">{{ static_text('localio_commissions_message') }}
                                        <a class="big-bld" type="button" onclick="openModal()">Learn more</a>
                                    </p>
                                </div> -->
                                
                                @php
                                    $productBadgeLabel = static_text('product_badge_label') ?? 'Key Features';
                                    // dd($productBadgeLabel);
                                @endphp

                                {{-- changes here --}}
                                {{-- <div class="main_feture" style="--product-badge-label: '{{ addslashes($productBadgeLabel) }}';"> --}}
                                <div class="main_feture">
                                    <div class=" fetru_row d-flex justify-content-between" data-aos="fade-up" data-aos-duration="1000">
                                        @if ($business->usps->count() > 0)
                                            {{-- Dynamic USPs from admin --}}
                                            <div class="main_feature_lg">
                                                <div class="feture_box lft_check_box size15">
                                                    <ul class="list-unstyled">
                                                        @foreach ($business->usps->take(5) as $usp)
                                                            <li class="d-flex flex-row align-items-center size15">
                                                                <div class="grn_chk">
                                                                    <img src="{{ asset('front/img/tick-img.png') }}">
                                                                </div>
                                                                <p class="m-0">{{ $usp->text }}</p>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Fallback static USPs when none are set in admin --}}
                                            <div class="main_feature_lg">
                                                <div class="feture_box lft_check_box size15">
                                                    <ul class="list-unstyled">
                                                        <li class="d-flex flex-row align-items-center size15">
                                                            <div class="grn_chk">
                                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                                            </div>
                                                            <p class="m-0">Free domain & SSL certificate</p>
                                                        </li>
                                                        <li class="d-flex flex-row align-items-center size15">
                                                            <div class="grn_chk">
                                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                                            </div>
                                                            <p class="m-0">Customizable automatic updates</p>
                                                        </li>
                                                        <li class="d-flex flex-row align-items-center size15">
                                                            <div class="grn_chk">
                                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                                            </div>
                                                            <p class="m-0">Scalable performance management</p>
                                                        </li>
                                                        <li class="d-flex flex-row align-items-center size15">
                                                            <div class="grn_chk">
                                                                <img src="{{ asset('front/img/tick-img.png') }}">
                                                            </div>
                                                            <p class="m-0">DDoS & malware protection</p>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Replace This Feature Section side of the Free trial box --}}
                                        {{-- <div class="main_feature_lg">
                                            <div class="feture_box">
                                                <h2 class="size22 big-bld">Localio Review Breakdown</h2>
                                                <ul class="p-0 m-0">
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Ease of Use</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $easeOfUseAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $easeOfUseAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Customer Service</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $customerServiceAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $customerServiceAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Features</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $exclusiveFeatureAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between">
                                                        <span class="lyt-text">Value for Money</span>
                                                        <div class="prgs_br">
                                                            <progress class="progress-bar" value="{{ $valueForMoneyAvg * 20 }}"
                                                                max="100"></progress>
                                                            <output>{{ $valueForMoneyAvg }}/5</output>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div> --}}

                                    

                                        {{-- Add Here Feature Localio Review Breakdown --}}
                                       <div class="main_feature_lg">
                                            <div class="feture_box review-breakdown-card">

                                                {{-- Header & Overall Rating --}}
                                                <div class="review-header-box" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                                    <div class="overall-rating-box" style="display: flex; flex-direction: column; align-items: flex-start;">
                                                        <span class="overall-rating-number" style="font-size: 48px; font-weight: 700; color: #002347; line-height: 1;">
                                                            {{ number_format($averageRating,1) }}
                                                        </span>

                                                        <div class="rating-stars" style="margin-top: 10px; margin-bottom: 6px; display: flex; gap: 4px;">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= floor($averageRating))
                                                                    <i class="fas fa-star text-warning" style="font-size: 18px;"></i>
                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                    <i class="fas fa-star-half-alt text-warning" style="font-size: 18px;"></i>
                                                                @else
                                                                    <i class="far fa-star text-warning" style="font-size: 18px;"></i>
                                                                @endif
                                                            @endfor
                                                        </div>

                                                        <span style="color: #666; font-size: 14px;">{{ number_format($totalReviews) }} reviews</span>
                                                    </div>

                                                    <a href="#section14" class="view-review-link" style="color: #06498b; font-weight: 600; font-size: 14px; text-decoration: none; padding-top: 5px;">
                                                        View all reviews
                                                    </a>
                                                </div>

                                                <h2 class="breakdown-title" style="margin-bottom: 15px;">
                                                    Review breakdown
                                                </h2>

                                                {{-- Breakdown --}}
                                                <div class="review-progress-list ">
                                                    @foreach ($criteria as $criterion)
                                                    <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                        <p class="m-0" style="font-size: 13px; font-weight: 500; color: #444;">{{ $criterion->name }}</p>
                                                        <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                                            <progress class="progress-bar w-100"
                                                                value="{{ $criterion->average_rating * 20 }}"
                                                                max="100" style="height: 8px;"></progress>
                                                            <span style="font-size: 12px; font-weight: 600; color: #333; margin-left: 8px; min-width: 35px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}/5</span>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>

                                                <div class="recommendation-rate mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                                    <span style="font-weight: 600; color: #1e3050; font-size: 14px;">Recommended by users</span>
                                                    <strong style="color: #06498b; font-size: 16px;">{{ $recommendPercent }}%</strong>
                                                </div>

                                                <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                                    <span style="font-weight: 600; color: #1e3050; font-size: 14px;">Do you recommend {{ $business->translations->first()->name ?? 'this business' }}?</span>
                                                    <div style="display: flex; gap: 8px;">
                                                        @auth
                                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 36px; height: 36px; border-radius: 50%; background-color: #06498b; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#053b70';" onmouseout="this.style.backgroundColor='#06498b';">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 36px; height: 36px; border-radius: 50%; background-color: #06498b; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#053b70';" onmouseout="this.style.backgroundColor='#06498b';">
                                                                <i class="fas fa-thumbs-down"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)" onclick="openLoginModal()" style="width: 36px; height: 36px; border-radius: 50%; background-color: #06498b; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#053b70';" onmouseout="this.style.backgroundColor='#06498b';">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="openLoginModal()" style="width: 36px; height: 36px; border-radius: 50%; background-color: #06498b; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#053b70';" onmouseout="this.style.backgroundColor='#06498b';">
                                                                <i class="fas fa-thumbs-down"></i>
                                                            </a>
                                                        @endauth
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        {{-- End Feature Localio Review Breakdown --}}
                                        <div class="innr_price_trail">

                                            <div class="main_feature_sm">
    <div class="feture_box str_prc_box">

        <h6 class="starting-price-title">
            Starting price
        </h6>

        @if ($startingPrice)
            <h2 class="starting-price-value">
                {{ $currency }}{{ $startingPrice }}
            </h2>
        @else
            <h2 class="starting-price-value">
                {{ $currency }}9
            </h2>
        @endif

        <p class="starting-price-text">
            Flat Rate, Per {{ ucfirst($timeUnit) }}
        </p>

        <a href="{{ $business->getTrackedUrl() }}"
            data-track="{{ json_encode([
                'type' => 'click',
                'business_id' => $business->id,
                'action' => 'view_pricing',
                'label' => 'View pricing',
            ]) }}"
            target="_blank"
            class="starting-price-link">
            View pricing
        </a>

    </div>
</div>

                                            <div class="main_feature_sm">
                                                <div class="fre_trail feture_box size22">
                                                    <div class="grn_check_big">
                                                        <img src="{{ asset('front/img/new-grn-chk.svg') }}">
                                                    </div>
                                                    <h6 class="blue-text big-bld">Free Trial
                                                        Available
                                                    </h6>
                                                    <div class="accor-btn">
                                                        <a class="cta cta_white blue_t_org_btn"
                                                            data-track="{{ json_encode([
                                                                'type' => 'click',
                                                                // 'product_id' => $product->id,
                                                                'business_id' => $business->id,
                                                                'action' => 'claim_now',
                                                                'label' => 'Claim Now',
                                                            ]) }}"
                                                            type="button">Claim Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                      @php
                                        $pros = collect();
                                        $cons = collect();

                                        $reviews = $business->reviews
                                            ->where('status', 'active')
                                            ->shuffle(); // Random reviews

                                        foreach ($reviews as $review) {

                                            $translation = $review->translations
                                                ->where('language_id', getCurrentLanguageID())
                                                ->first() ?? $review->translations->first();

                                            if (!$translation) {
                                                continue;
                                            }

                                            // ---------- Pros ----------
                                            if (!empty($translation->pros)) {

                                                $text = trim(strip_tags($translation->pros));

                                                if (str_word_count($text) <= 6) {
                                                    $pros->push($text);
                                                }
                                            }

                                            // ---------- Cons ----------
                                            if (!empty($translation->cons)) {

                                                $text = trim(strip_tags($translation->cons));

                                                if (str_word_count($text) <= 6) {
                                                    $cons->push($text);
                                                }
                                            }

                                            if ($pros->count() >= 2 && $cons->count() >= 2) {
                                                break;
                                            }
                                        }

                                        $pros = $pros->unique()->take(2);
                                        $cons = $cons->unique()->take(2);

                                        // Default values
                                        $defaultPros = ['Service staff is good', 'Easy to use and navigate'];
                                        foreach ($defaultPros as $defaultPro) {
                                            if ($pros->count() >= 2) {
                                                break;
                                            }
                                            if (!$pros->contains($defaultPro)) {
                                                $pros->push($defaultPro);
                                            }
                                        }

                                        $defaultCons = ['Service not as well as expected', 'Pricing could be more flexible'];
                                        foreach ($defaultCons as $defaultCon) {
                                            if ($cons->count() >= 2) {
                                                break;
                                            }
                                            if (!$cons->contains($defaultCon)) {
                                                $cons->push($defaultCon);
                                            }
                                        }
                                    @endphp
                                                <div class="main_feature_lg">
                                            <div class="feture_box review-breakdown-box">

                                                 <div class="review-header-box pb-3" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 15px;">
                                                     <h2 class="size22 big-bld m-0">Highlighted reviews </h2>
                                                     <a href="#section14" class="view-review-link">
                                                         View all reviews
                                                     </a>
                                                 </div>

                                                  @foreach($topReviews->take(2) as $review)
                                                      <div class="sidebar-review-card" style="margin-bottom: 20px;">

                                                          <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">

                                                              <div class="review-user" style="display: flex; align-items: center; gap: 12px;">

                                                                  @if($review->user && $review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                                                                      <img src="{{ asset($review->user->profile_image) }}"
                                                                          class="rounded-circle"
                                                                          width="45"
                                                                          height="45">
                                                                  @else
                                                                      <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                                          <span style="color: white; font-weight: bold; font-size: 20px;">{{ strtoupper(substr($review->user->first_name ?? 'A', 0, 1)) }}</span>
                                                                      </div>
                                                                  @endif

                                                                    <div>
                                                                        <h6 style="margin: 0; font-size: 14px; font-weight: 700; color: #1e3050;">{{ $review->user ? $review->user->displayName() : 'Anonymous' }}</h6>
                                                                        @if($review->user && $review->user->job_title)
                                                                            <div style="font-size: 12px; color: #777; margin-top: 2px; line-height: 1.2;">{{ $review->user->job_title }}</div>
                                                                        @endif
                                                                    </div>
                                                              </div>

                                                              <div style="text-align: right; flex-shrink: 0;">
                                                                  <small class="text-muted" style="font-size: 11px; white-space: nowrap;">{{ $review->created_at->diffForHumans() }}</small>
                                                              </div>

                                                         </div>

                                                        <h5 style="margin-top: 10px; margin-bottom: 4px; font-size: 15px; font-weight: 700; color: #1e3050;">
                                                            {{ $review->translations->first()->title ?? 'Review' }}
                                                        </h5>

                                                        <div class="rating-stars-wrapper" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                                            <div class="rating-stars">
                                                                @for($i=1;$i<=5;$i++)
                                                                    @if($i<=floor($review->rating))
                                                                        <i class="fas fa-star text-warning" style="font-size: 12px !important;"></i>
                                                                    @elseif($i-0.5<=$review->rating)
                                                                        <i class="fas fa-star-half-alt text-warning" style="font-size: 12px !important;"></i>
                                                                    @else
                                                                        <i class="far fa-star text-warning" style="font-size: 12px !important;"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                        </div>

                                                        <p style="font-size: 13.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                                            {{ \Illuminate\Support\Str::limit(strip_tags($review->translations->first()->description ?? ''),90) }}
                                                        </p>

                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>                                        <!-- Recent discussions box -->
                                        <div class="main_feature_lg mt-4">
                                            <div class="feture_box review-breakdown-box">
                                                <div class="review-header-box pb-3" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                                                    <h2 class="size22 big-bld m-0">Recent discussions</h2>
                                                    <a href="#sectionDiscussions" class="view-review-link">
                                                        View all discussions
                                                    </a>
                                                </div>

                                                <!-- Static discussions data using exact same classes and structure -->
                                                <div class="sidebar-review-card" style="margin-bottom: 20px;">
                                                    <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                                        <div class="review-user" style="display: flex; align-items: center; gap: 12px;">
                                                            <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                                <span style="color: white; font-weight: bold; font-size: 20px;">M</span>
                                                            </div>
                                                            <div>
                                                                <h6 style="margin: 0; font-size: 14px; font-weight: 700; color: #1e3050;">Marc L.</h6>
                                                                <div style="font-size: 12px; color: #777; margin-top: 2px;">
                                                                    Product Manager • Small Business (1-50 emp.)
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;">
                                                            <small class="text-muted" style="font-size: 11px; white-space: nowrap;">2 hours ago</small>
                                                        </div>
                                                    </div>

                                                    <h5 style="cursor: pointer;" onclick="document.getElementById('sectionDiscussions')?.scrollIntoView({behavior: 'smooth'})">
                                                        Is there a free tier for API access or is it trial only?
                                                    </h5>
                                                    <p style="font-size: 13.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                                        We are looking to integrate this into our workflow and want to test the latency over a few weeks...
                                                    </p>
                                                </div>

                                                <div class="sidebar-review-card" style="margin-bottom: 0;">
                                                    <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                                        <div class="review-user" style="display: flex; align-items: center; gap: 12px;">
                                                            <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                                <span style="color: white; font-weight: bold; font-size: 20px;">S</span>
                                                            </div>
                                                            <div>
                                                                <h6 style="margin: 0; font-size: 14px; font-weight: 700; color: #1e3050;">Sarah J.</h6>
                                                                <div style="font-size: 12px; color: #777; margin-top: 2px;">
                                                                    CTO • Mid-Market (51-1000 emp.)
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;">
                                                            <small class="text-muted" style="font-size: 11px; white-space: nowrap;">1 day ago</small>
                                                        </div>
                                                    </div>

                                                    <h5 style="cursor: pointer;" onclick="document.getElementById('sectionDiscussions')?.scrollIntoView({behavior: 'smooth'})">
                                                        How does the performance compare to alternatives in large datasets?
                                                    </h5>
                                                    <p style="font-size: 13.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                                        We noticed some latency spikes during queries with more than 10k items. Anyone else facing this?
                                                    </p>
                                                </div>
                                            </div>
                                        </div></div>
                                    </div>
                                </div>


                            </div>
                       </div>     
                   </div>         
              </section>

        
{{-- -------------------------------------------------------------- --}}

        <!-- Products Section -->

        <!-- Products Section -->

        <!-- section whatis -->
        <div class="con_table pb-0 all_sec_wrp" id="section2">
            <div class="container">

                <div class="row pt-0 ">
                    <div class="col-lg-12">
                        <div class="inner_table_1">
                            <section class="is-asana light">

                                
                            </section>

                            {{-- Added The New Section preview reviews --}}
                            <section class="reviews-section">
                               
                            </section>
                            {{-- End preview reviews section  --}}

                            <!-- Product Pricing Section -->
                            <!-- <section class="product_pricing_sec p_50 pb-0" id="section3">

                                <div class="section_title text-center mb-4" data-aos="fade-up" data-aos-duration="1000">
                                    <h2> {{ $business->translations->first()->name }} Pricing Plans </h2>
                                </div>
                                <div class="pricing_plans_row d-flex align-items-center">
                                    <div class="row table_st_1">
                                        @if ($business->products->count() > 0)
                                            @foreach ($business->products as $product)
                                                @php
                                                    $lang_id = getCurrentLanguageID();
                                                    $productTranslation = $product->translations;
                                                    $price = $product->prices->first();

                                                    // Determine which price to display
                                                    $displayPrice = null;
                                                    $originalPrice = null;
                                                    $timeUnit = 'month';
                                                    $isDiscounted = false;

                                                    if ($price) {
                                                        $today = \Carbon\Carbon::now()->startOfDay();
                                                        $timeUnit = $price->time_unit;

                                                        // Check if discount is available and not expired
                                                        if (
                                                            $price->discount_price &&
                                                            (!$price->discount_expiration_date ||
                                                                \Carbon\Carbon::parse(
                                                                    $price->discount_expiration_date,
                                                                )->endOfDay() >= $today)
                                                        ) {
                                                            $displayPrice = $price->discount_price;
                                                            $originalPrice = $price->price;
                                                            $timeUnit =
                                                                $price->discount_time_units ?? $price->time_unit;
                                                            $isDiscounted = true;
                                                        }
                                                        // Check if renewal price is available
                                                        elseif ($price->renewal_price) {
                                                            $displayPrice = $price->renewal_price;
                                                            $timeUnit = $price->renewal_time_units ?? $price->time_unit;
                                                        }
                                                        // Use regular price as fallback
                                                        else {
                                                            $displayPrice = $price->price;
                                                        }

                                                        // Format time unit for display
                                                        $timeUnitDisplay = $timeUnit;
                                                        if ($timeUnit == 'one_time') {
                                                            $timeUnitDisplay = 'One-Time';
                                                        } elseif ($timeUnit == 'quarter') {
                                                            $timeUnitDisplay = '3 months';
                                                        }
                                                    }
                                                @endphp

                                                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up"
                                                    data-aos-duration="1000">
                                                    <div class="pricing_card">
                                                        <div class="pricing_header">
                                                            <h6>{{ $productTranslation ? $productTranslation->name : 'Plan' }}
                                                            </h6>
                                                        </div>
                                                        <div class="pricing_amount">
                                                            @if ($displayPrice !== null)
                                                                <h3 class="blue-text">
                                                                    {{ $price->currency }}{{ number_format($displayPrice, 2) }}
                                                                    @if ($timeUnitDisplay)
                                                                        <span class="big_text">/
                                                                            {{ $timeUnitDisplay }}</span>
                                                                    @endif
                                                                </h3>

                                                                @if ($isDiscounted && $originalPrice)
                                                                    <p class="original_price">
                                                                        <s>{{ $price->currency }}
                                                                            {{ number_format($originalPrice, 2) }}</s>
                                                                        <span class="discount_badge">Sale</span>
                                                                    </p>
                                                                @endif

                                                                @if ($price->additional_info)
                                                                    <p class="additional_info">
                                                                        {{ $price->additional_info }}
                                                                    </p>
                                                                @endif
                                                            @else
                                                                <h3 class="blue-text">Contact for Pricing</h3>
                                                            @endif
                                                        </div>
                                                        @if ($isDiscounted && $price->discount_expiration_date)
                                                            <div class="discount_timer">
                                                                <p>Offer ends:
                                                                    {{ \Carbon\Carbon::parse($price->discount_expiration_date)->format('M d, Y') }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                        <div class="pricing_action d-flex justify-content-center">
                                                            <a href="{{ $product->product_link ?? ($business->affiliate_link ?? '#') }}"
                                                                data-track="{{ json_encode([
                                                                    'type' => 'click',
                                                                    'product_id' => $product->id,
                                                                    'business_id' => $business->id,
                                                                    'action' => 'try_for_free',
                                                                    'label' => 'Try for free',
                                                                ]) }}"
                                                                target="_blank" class="cta cta_orange"
                                                                style="  border: none;">
                                                                Try for free
                                                                <span class="right-arw">
                                                                    <img src="{{ asset('front/img/right-arrw.svg') }}"
                                                                        alt="">
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12 text-center">
                                                <p>No pricing plans available for this product.</p>
                                            </div>
                                        @endif
                                    </div>

                                </div>

                                <div class="lcl_text mt-4">
                                    <p class="sml_text">* Prices may vary. Visit the official website for the most current
                                        pricing.</p>
                                </div>

                            </section> -->

                            {{-- pros and Cons --}}
                            @php
                                $prosList = collect();
                                $consList = collect();

                                foreach ($business->reviews as $review) {
                                    $translation = $review->translations->firstWhere('language_id', $lang_id);
                                    if ($translation) {
                                        // Assuming pros and cons are comma-separated strings or arrays
                                        $prosList = $prosList->merge(
                                            array_filter(array_map('trim', explode(',', $translation->pros))),
                                        );
                                        $consList = $consList->merge(
                                            array_filter(array_map('trim', explode(',', $translation->cons))),
                                        );
                                    }
                                }

                                $uniquePros = $prosList->unique()->take(5); // Limit for layout
                                $uniqueCons = $consList->unique()->take(5);
                            @endphp

                            <!-- <section class="pros-cons p_50 light" id="section4">
                                <div>
                                    <div style="margin-bottom: 30px;">
                                        <h2>{{ $business->translations->first()->name ?? 'Business' }} pros and cons</h2>

                                        <p style="color:; line-height: 1.6; margin-bottom: 20px;">
                                            To find out what users like and dislike about
                                            {{ $business->translations->first()->name ?? 'this business' }}, we analyzed
                                            the most common positive and negative aspects mentioned in user reviews.
                                        </p>
                                    </div>

                                    {{-- Two Column Layout --}}
                                    <div class="tikcrsotr">
                                        {{-- Pros --}}
                                        <div class="pros_ot border shadow-sm">
                                            <h3>Things I like</h3>
                                            @foreach ($uniquePros as $pro)
                                                <div class="pr_pros">
                                                    <div class="innr_pr">
                                                        <div class="greenfonticon">
                                                            <i class="fa-solid fa-check"></i>
                                                        </div>
                                                        <div>
                                                            <h5>{{ ucfirst($pro) }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Cons --}}
                                        <div class="cons_ot border shadow-sm">
                                            <h4>Things that could be improved</h4>

                                            @foreach ($uniqueCons as $con)
                                                <div class="pr_cons">
                                                    <div class="innr_pr">
                                                        <div class="redboxicon">
                                                            <i class="fa-solid fa-minus"></i>
                                                        </div>
                                                        <div>
                                                            <h5>{{ ucfirst($con) }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </section> -->

                            <!-- section popular-altrnative -->
                            

                            {{-- Feature title and description section --}}
                            {{-- <section class="business-features light" id="features">
                                <div class="container" data-aos="fade-up" data-aos-duration="1000">
                                    <h2 class="text-feature">Features</h2>
                                    @if($business->features && $business->features->isNotEmpty())
                                     <div class="feature-section">
                                        <div class="row">

                                            @foreach($business->features as $feature)
                                                @php
                                                    $translation = $feature->translations->first();

                                                    // Feature-specific reviews
                                                    $featureReviews = $feature->reviews ?? collect();
                                                    $avgRating = $featureReviews->count() ? $featureReviews->avg('rating') : 0;

                                                    // Fallback to overall business rating
                                                    $averageRating = $avgRating ?: ($business->reviews->where('status', 'active')->count() ? $business->reviews->where('status', 'active')->avg('rating') : 0);
                                                    $ratingCount = $business->reviews->where('status', 'active')->count();
                                                @endphp

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="feature-card">
                                                        <!-- Feature title with rating -->
                                                        <div class="feature-texts">
                                                            <h6 class="feature-title-hd">{{ $translation->name ?? $feature->name ?? 'Unnamed Feature' }}</h6>
                                                            <p class="feature-content">{{ number_format($averageRating, 1) }} ({{ $ratingCount }})</p>
                                                        </div>

                                                        <!-- Feature description -->
                                                        @if(!empty($translation->description))
                                                            <p class="text-muted mb-3">{{ $translation->description }}</p>
                                                        @else
                                                            <p class="text-muted mb-3">No description available.</p>
                                                        @endif

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                     </div>
                                    @else
                                        <p class="text-center text-muted">No features have been added yet for this business.</p>
                                    @endif
                                </div>
                            </section> --}}

                            <!-- <section class="business-features light" id="features">
                                <div class="container" data-aos="fade-up" data-aos-duration="1000">
                                    <h2 class="text-feature">Features</h2>
                                    <p class="text-feature-desc mb-4" style="color: #666; font-size: 15px; margin-top: 10px;">
                                        Features will be defined in admin for each sub-category. And then when rating a business of that sub-category, users will see these features and can rate them. The same features are shown on the details page of each business.
                                    </p>
                                    @if($business->features && $business->features->isNotEmpty())
                                        <div class="feature-section">
                                            <div class="row">
                                                @foreach($business->features as $feature)
                                                    @php
                                                        $translation = $feature->translations->first();
                                                        $featureReviews = $feature->reviews ?? collect();
                                                        $avgRating = $featureReviews->count() ? $featureReviews->avg('rating') : 0;
                                                        $ratingCount = $featureReviews->count();
                                                    @endphp

                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="feature-card border rounded p-4 mb-3 shadow-sm bg-white">
                                                            <div class="feature-texts">
                                                                <h6 class="feature-title-hd mb-0">
                                                                    {{ $translation->name ?? $feature->name ?? 'Unnamed Feature' }}
                                                                </h6>

                                                                <p class="feature-content m-0 open-review-popup"
                                                                   data-feature="{{ $feature->id }}">
                                                                    <i class="fa-solid fa-star text-warning"></i>
                                                                    <span>{{ number_format($avgRating, 1) }}</span>
                                                                </p>
                                                            </div>
                                                            <hr class="feature-card-divider" style="border: 0; border-top: 1px solid #eee; margin-top: 10px; margin-bottom: 15px;">

                                                            @if(!empty($translation->description))
                                                                <p class="text-muted mb-0">{{ $translation->description }}</p>
                                                            @else
                                                                <p class="text-muted mb-0">No description available.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-center text-muted">No features have been added yet for this business.</p>
                                    @endif
                                </div>
                            </section> -->

                            <!-- Key Feature Popup Modal -->
                            <div class="modal fade" id="reviewPopup" tabindex="-1" aria-labelledby="reviewPopupLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="reviewPopupLabel">Submit Your Review</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body text-center">
                                            <input type="hidden" id="popupFeatureId">
                                            <input type="hidden" id="popupRating">

                                            <!-- Stars -->
                                            <div class="mb-3">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="popup-star fs-4 mx-1" data-value="{{ $i }}" style="cursor:pointer; color:#ccc;">&#9733;</span>
                                                @endfor
                                            </div>

                                            <!-- Comment -->
                                            <textarea id="popupComment" class="form-control mb-3" rows="3" placeholder="Write a review (optional)"></textarea>

                                            <!-- Submit Button -->
                                            <button id="submitPopupReview" class="btn btn-primary w-100">Submit Review</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End Feature section --}}


                            {{-- Top Reviews --}}
                            {{-- Comments This Section  --}}
                            {{-- <section class="review_sec p_50 " id="section6">
                                <div class="container">
                                    <div class="review_content" data-aos="fade-up" data-aos-duration="1000">
                                        <h2>Top Reviews</h2>
                                        @if ($topReviews->isNotEmpty())
                                            @foreach ($topReviews as $review)
                                                <div class="review_detl">
                                                    <div class="reviw_hd">
                                                        <div class="ans_lft">
                                                            <div class="asn-img">
                                                                @if ($review->user && $review->user->profile_image && $review->user->profile_image !== 'front/img/default.png')
                                                                     <img src="{{ asset($review->user->profile_image) }}"
                                                                         class="img-fluid profile-circle"
                                                                         style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                         alt="User Image">
                                                                 @else
                                                                     <div class="profile-circle" style="width: 70px; height: 70px; border-radius: 50%; background-color: #06498b ; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                                         <span style="color: white; font-weight: bold; font-size: 28px;">
                                                                             @if ($review->user && $review->user->user_type === 'admin')
                                                                                 {{ strtoupper(substr($review->public_name ?? 'P', 0, 1)) }}
                                                                             @else
                                                                                 {{ strtoupper(substr($review->user->first_name ?? 'A', 0, 1)) }}
                                                                             @endif
                                                                         </span>
                                                                     </div>
                                                                 @endif
                                                            </div>
                                                            <div class="asn-rating">
                                                                <h6>
                                                                    @if ($review->user && $review->user->user_type === 'admin')
                                                                        {{ $review->public_name ?? 'Public' }}
                                                                    @else
                                                                        {{ $review->user->first_name ?? 'Anonymous' }}
                                                                    @endif
                                                                </h6>

                                                                <div class="rating light"> --}}
                                                                    {{-- Change Review --}}
                                                                    {{-- <div class="inn_ul">
                                                                        <div class="rating-stars">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($averageRating))
                                                                                    <i
                                                                                        class="fas fa-star text-warning"></i>
                                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                                    <i
                                                                                        class="fas fa-star-half-alt text-warning"></i>
                                                                                @else
                                                                                    <i
                                                                                        class="far fa-star text-warning"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                    <div class="rate_box">
                                                                        {{ number_format($averageRating, 1) }} | --}}
                                                                        {{-- {{ $ratingCount }} ratings --}}
                                                                        {{-- @if ($ratingCount === 0)
                                                                        0 Ratings
                                                                        @elseif ($ratingCount === 1)
                                                                            1 Rating
                                                                        @else
                                                                            {{ $ratingCount }} Ratings
                                                                        @endif
                                                                    </div> --}}


                                                                     {{-- Average + Ratings count --}}
                                                                     {{-- <div class="rate_box">
                                                                        {{ number_format($averageRating, 1) }} |
                                                                        @if ($ratingCount === 0)
                                                                            0 Ratings
                                                                        @elseif ($ratingCount === 1)
                                                                            1 Rating
                                                                        @else
                                                                            {{ $ratingCount }} Ratings
                                                                        @endif
                                                                    </div> --}}

                                                                    {{-- <span
                                                                    class="rating-score size18">{{ number_format($review->rating, 1) }}</span>
                                                                <div class="rating_str">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= floor($review->rating))
                                                                        <i class="fas fa-star text-warning"></i>
                                                                        @elseif ($i - 0.5 <= $review->rating)
                                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                                            @else
                                                                            <i class="far fa-star text-warning"></i>
                                                                            @endif
                                                                            @endfor
                                                                </div>  --}}


                                                                {{-- </div>
                                                            </div>
                                                        </div>
                                                        <p class="m-0">{{ $review->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    <div class="review_text size18">
                                                        <p class="size22 big-bld">
                                                            {{ $review->translations->first()->title ?? 'Review' }}
                                                        </p>
                                                        <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                        </p> --}}

                                                        {{-- Show The Pros and Cons --}}
                                                        {{-- <div class="pros-cons-box row mt-4">
                                                    <div class="col-md-6">
                                                        <div class="pros-box p-3 border border-success rounded bg-light">
                                                            <h6 class="text-success mb-2"><i class="fas fa-thumbs-up me-1"></i> Pros</h6>
                                                            <p class="mb-0">{{ $review->translations->first()->pros }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-3 mt-md-0">
                                                            <div class="cons-box p-3 border border-danger rounded bg-light">
                                                                <h6 class="text-danger mb-2"><i class="fas fa-thumbs-down me-1"></i> Cons</h6>
                                                                <p class="mb-0">{{ $review->translations->first()->cons }}</p>
                                                            </div>
                                                        </div>
                                                        </div> --}}

                                                    {{-- </div>
                                                </div>
                                            @endforeach

                                            <div class="btm-bttn light">
                                                <a class="cta cta_white"
                                                    href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()->slug]) }}">View
                                                    All Reviews</a>
                                            </div>
                                        @else
                                            <p>No reviews found for this business.</p>
                                        @endif
                                    </div>
                                </div>

                            </section> --}}

                            <section class="choice-business d-none" id="softweretopic">
                            <!-- @php
                                $langId = getCurrentLanguageID();
                                $businessName =
                                    $business->translations->firstWhere('lang_id', $langId)?->name ?? 'This Business';
                            @endphp

                            @foreach ($business->category->topics as $topic)
                                @php
                                    $topicTranslation = $topic->translations->firstWhere('lang_id', $langId);
                                    $title = str_replace(
                                        '{business_name}',
                                        $businessName,
                                        $topicTranslation?->title ?? '',
                                    );
                                    $descriptionEntry = $business->topicDescriptions->firstWhere(
                                        'topic_id',
                                        $topic->id,
                                    );
                                    $paragraphs = explode("\n", $descriptionEntry?->description ?? '');
                                @endphp

                                @if ($title && $descriptionEntry)
                                    {{-- <section class="worth-it p_50 light" id="section{{ 100 + $loop->index }}">
                                        <h3>{{ $title }}</h3>

                                        @foreach ($paragraphs as $para)
                                            <p style="color: line-height: 1.6; margin-bottom: 15px;">
                                                {{-- {{ trim($para) }} --}}
                                                {{-- {!! trim($para) !!}
                                            </p>
                                        @endforeach
                                    </section> --}}

                                        <div class="worth-it light" id="section{{ 100 + $loop->index }}">
                                        <h2>{{ $title }}</h2>

                                        @foreach ($paragraphs as $para)
                                            {{-- <p> --}}
                                                {!! trim($para) !!}
                                            {{-- </p> --}}
                                        @endforeach
                                    </div>

                                @endif
                            @endforeach -->
                        </section>

                      {{-- Business Integration Section --}}
                                @php
                                // Decode items safely
                                $items = [];
                                if (!empty($business->integration?->items)) {
                                    $items = is_array($business->integration->items) ? $business->integration->items : json_decode($business->integration->items, true);
                                }

                            @endphp

                            <!-- @if($items && count($items) > 0)
                            <section class="business-integration-section" id="business-integration">
                                <div class="container">
                                    {{-- Section Title --}}
                                    <h2 class="mb-3">{{ $business->integration->title }}</h2>
                                    <p class="mb-4">{{ $business->integration->description }}</p>
                                    <p class="mb-2 text-secondary">{{ $business->integration->subheading ?? 'Popular Integrations' }}</p>

                                    {{-- Integration Items --}}
                                    <div class="integration-items">
                                        @foreach($items as $item)
                                        <div class="integration-inboxs">
                                            <a href="{{ $item['link'] ?? '#' }}" target="_blank" class="integration-item">

                                                @php
                                                    $icon_path = $item['icon'] ?? null;
                                                    if($icon_path && file_exists(public_path($icon_path))) {
                                                        $icon_url = asset($icon_path);
                                                    } else {
                                                        $icon_url = asset('images/placeholder.png');
                                                    }
                                                @endphp

                                                <img src="{{ $icon_url }}" alt="{{ $item['name'] ?? 'No Icon' }}">
                                                <span>{{ $item['name'] ?? '' }}</span>
                                            </a>
                                        </div>

                                        @endforeach
                                    </div>
                                </div>
                            </section>
                            @else
                            <p class="text-center text-muted">No integration items available.</p>
                            @endif -->

                            <!-- section software-like -->
                            {{-- faq --}}
                            

                            <section class="software-like p_50 product_integra_sec " id="section9">
                                <div class="sftwre-like-innr">
                                    <div class="sftwre-asana-hd text-center" data-aos="fade-up" data-aos-duration="1000">
                                        {{-- <h2>Software like {{ $business->translations->first()->name }}</h2> --}}
                                        <h2>{{ $business->translations->first()->name }} Alternatives & Competitors</h2>
                                        <p>Based on other buyer's searches, these are the products that could be a good fit
                                            for you.
                                        </p>
                                    </div>
                                    <div class="sft_ware_test"
                                        style="display: flex; justify-content:center; align-items: center;">

                                        <div class="sftware-alternative d-flex" data-aos="fade-up"
                                            data-aos-duration="1000">
                                            <div class="sftware-alternative-pck" data-aos="fade-up"
                                                data-aos-duration="1000"
                                                onclick="if(!event.target.closest('a')) { window.location.href = '{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $business->translations->first()->slug]) }}'; }"
                                                style="cursor: pointer; padding: 25px 20px;">
                                                <div class="ans_lft p_top_btm_sftwre pt-0 pb-3" style="border-bottom: 1px solid #eee;">
                                                    <div class="asn-img">
                                                        <img
                                                            src="{{ asset($business->icon_id ?? 'front/img/sftare-img1.svg') }}">
                                                    </div>
                                                    <div class="asn-rating">
                                                        <h6 class="m-0 fw_700">{{ $business->translations->first()->name }}</h6>
                                                        @php
                                                            $ratingCount = $business->reviews
                                                                ->where('status', 'active')
                                                                ->count();
                                                        @endphp
                                                        <div class="overall-rating-header d-flex align-items-center mt-2 flex-wrap" style="gap: 5px;">
                                                            <div class="rating-stars" style="display: flex; gap: 2px;">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <= floor($averageRating))
                                                                        <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                                                    @elseif ($i - 0.5 <= $averageRating)
                                                                        <i class="fas fa-star-half-alt text-warning" style="font-size: 12px;"></i>
                                                                    @else
                                                                        <i class="far fa-star text-warning" style="font-size: 12px;"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                            <span class="rate_box_text text-muted" style="font-size: 12px; font-weight: 500;">
                                                                {{ number_format($averageRating, 1) }} | {{ $ratingCount }} Reviews
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="over-rate-progress p_top_btm_sftwre pt-3 pb-3" style="border-bottom: 1px solid #eee;">
                                                    <h6 class="fw_700 mb-3" style="color: #002655; font-size: 14px;">Review breakdown</h6>
                                                    @foreach ($criteria as $criterion)
                                                    <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                        <p class="m-0" style="font-size: 12px; color: #555;">{{ $criterion->name }}</p>
                                                        <div class="prgs_br d-flex align-items-center">
                                                            <progress class="progress-bar"
                                                                value="{{ $criterion->average_rating * 20 }}"
                                                                max="100"></progress>
                                                            <span style="font-size: 12px; font-weight: 600; color: #333; margin-left: 8px; min-width: 32px; text-align: right;">{{ $criterion->average_rating }}/5</span>
                                                        </div>
                                                    </div>
                                                    @endforeach

                                                    <div class="recommendation-rate mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                                        <span style="font-weight: 600; color: #002655; font-size: 12px;">Recommended by users</span>
                                                        <strong style="color: #06498b; font-size: 14px;">{{ $recommendPercent }}%</strong>
                                                    </div>
                                                </div>

                                                <div class="start-from p_top_btm_sftwre pt-3 pb-3">
                                                    <h6 style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 14px;">Starting price</h6>
                                                    <h3 class="m-0 mt-1" style="font-weight: 700; color: #333; font-size: 24px; line-height:1!important;">
                                                        <span>{{ $currency }}{{ $startingPrice }}</span>
                                                    </h3>
                                                    <small class="text-muted" style="font-size: 13px;">{{ $additional_info }}</small>
                                                </div>

                                                <div class="sftwre-alt-btn pt-2">
                                                    <a href="{{ $business->getTrackedUrl() }}"
                                                        target="_blank"
                                                        class="cta btn_blue w-100 d-flex align-items-center justify-content-center"
                                                        style=" color: #002347; border-radius: 25px; padding: 10px 20px; font-weight:500; text-decoration: none; font-size: 14px; ">
                                                         View details
                                                    </a>
                                                </div>
                                            </div>

                                            @foreach ($alternativeBusiness as $altbusiness)
                                                <div class="sftware-alternative-pck" data-aos="fade-up"
                                                    data-aos-duration="1000"
                                                    onclick="if(!event.target.closest('a')) { window.location.href = '{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $altbusiness->translations->first()->slug]) }}'; }"
                                                    style="cursor: pointer; padding: 25px 20px;">
                                                    @php
                                                        $price = getBusinessesWithStartingPrice($altbusiness);
                                                        if (!empty($price) && isset($price[0]['starting_price'])) {
                                                            $businessprice = $price[0]['starting_price'];
                                                            $startingPrice = $businessprice['amount'];
                                                            $currency = $businessprice['currency'] ?? '$';
                                                            $timeUnit = ucfirst($businessprice['time_unit'] ?? 'month');
                                                            $additional_info =
                                                                $businessprice['additional_info'] ?? 'NA';
                                                        }

                                                        // Get reviews for the current altbusiness
                                                        $altReviews = \App\Models\Review::where(
                                                            'business_id',
                                                            $altbusiness->id,
                                                        )->get();

                                                        $altEaseOfUseAvg = round(
                                                            $altReviews->avg('ease_of_use_rating'),
                                                            1,
                                                        );
                                                        $altValueForMoneyAvg = round(
                                                            $altReviews->avg('value_for_money_rating'),
                                                            1,
                                                        );
                                                        $altCustomerServiceAvg = round(
                                                            $altReviews->avg('customer_service_rating'),
                                                            1,
                                                        );
                                                        $altExclusiveFeatureAvg = round(
                                                            $altReviews->avg('exclusive_service_rating'),
                                                            1,
                                                        );

                                                        $altRatingAvg = $altbusiness->reviews->avg('rating');
                                                        $count = $altbusiness->reviews->where('status', 'active')->count();
                                                    @endphp

                                                    <div class="ans_lft p_top_btm_sftwre pt-0 pb-3" style="border-bottom: 1px solid #eee;">
                                                        <div class="asn-img">
                                                            <img src="{{ asset($altbusiness->icon_id ?? 'front/img/top-rate-img2.svg') }}"
                                                                alt="">
                                                        </div>
                                                        <div class="asn-rating">
                                                            @if ($altbusiness->translations->isNotEmpty())
                                                                <h6 class="m-0 fw_700">
                                                                    {{ $altbusiness->translations->first()->name }}
                                                                </h6>
                                                            @else
                                                                <h6 class="m-0 fw_700">Name not available</h6>
                                                            @endif
                                                            <div class="overall-rating-header d-flex align-items-center mt-2 flex-wrap" style="gap: 5px;">
                                                                <div class="rating-stars" style="display: flex; gap: 2px;">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= floor($altRatingAvg))
                                                                            <i class="fas fa-star text-warning" style="font-size: 13px;"></i>
                                                                        @elseif ($i - 0.5 <= $altRatingAvg)
                                                                            <i class="fas fa-star-half-alt text-warning" style="font-size: 13px;"></i>
                                                                        @else
                                                                            <i class="far fa-star text-warning" style="font-size: 13px;"></i>
                                                                        @endif
                                                                    @endfor
                                                                </div>
                                                                <span class="rate_box_text text-muted" style="font-size: 12px; font-weight: 500;">
                                                                    {{ number_format($altRatingAvg, 1) }} | {{ $count }} Reviews
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="over-rate-progress p_top_btm_sftwre pt-3 pb-3" style="border-bottom: 1px solid #eee;">
                                                        <h6 class="fw_700 mb-3" style="color: #002655; font-size: 14px;">Review breakdown</h6>
                                                        <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                            <p class="m-0" style="font-size: 14px; color: #555;">Ease of Use</p>
                                                            <div class="prgs_br d-flex align-items-center">
                                                                <progress class="progress-bar"
                                                                    value="{{ ($altEaseOfUseAvg ?? 0) * 20 }}"
                                                                    max="100">
                                                                </progress>
                                                                <span style="font-size: 12px; font-weight: 600; color: #333; margin-left: 8px; min-width: 32px; text-align: right;">{{ $altEaseOfUseAvg ?? 0 }}/5</span>
                                                            </div>
                                                        </div>
                                                        <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                            <p class="m-0" style="font-size: 12px; color: #555;">Customer Service</p>
                                                            <div class="prgs_br d-flex align-items-center">
                                                                <progress class="progress-bar"
                                                                    value="{{ ($altCustomerServiceAvg ?? 0) * 20 }}"
                                                                    max="100">
                                                                </progress>
                                                                <span style="font-size: 12px; font-weight: 600; color: #333; margin-left: 8px; min-width: 32px; text-align: right;">{{ $altCustomerServiceAvg ?? 0 }}/5</span>
                                                            </div>
                                                        </div>
                                                        <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                            <p class="m-0" style="font-size: 12px; color: #555;">Features</p>
                                                            <div class="prgs_br d-flex align-items-center">
                                                                <progress class="progress-bar"
                                                                    value="{{ ($altExclusiveFeatureAvg ?? 0) * 20 }}"
                                                                    max="100">
                                                                </progress>
                                                                <span style="font-size: 12px; font-weight: 600; color: #333; margin-left: 8px; min-width: 32px; text-align: right;">{{ $altExclusiveFeatureAvg ?? 0 }}/5</span>
                                                            </div>
                                                        </div>
                                                        <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                            <p class="m-0" style="font-size: 12px; color: #555;">Value for Money</p>
                                                            <div class="prgs_br d-flex align-items-center">
                                                                <progress class="progress-bar"
                                                                    value="{{ ($altValueForMoneyAvg ?? 0) * 20 }}"
                                                                    max="100">
                                                                </progress>
                                                                <span style="font-size: 12px; font-weight: 600; color: #333; margin-left: 8px; min-width: 32px; text-align: right;">{{ $altValueForMoneyAvg ?? 0 }}/5</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="start-from p_top_btm_sftwre pt-3 pb-3">
                                                        <h6 style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 14px;">Starting price</h6>
                                                        <h3 class="m-0 mt-1" style="font-weight: 700; color: #333; font-size: 24px; line-height:1!important; ">
                                                            <span>{{ $currency }}{{ $startingPrice }}</span>
                                                        </h3>
                                                        <small class="text-muted" style="font-size: 12px;">{{ $additional_info }}</small>
                                                    </div>

                                                    <div class="sftwre-alt-btn pt-2">
                                                        <a href="{{ $altbusiness->getTrackedUrl() }}"
                                                            target="_blank"
                                                            class="cta btn_blue w-100 d-flex align-items-center justify-content-center"
                                                            style="  border-radius: 25px; padding: 10px 20px; font-weight: 500; text-decoration: none; font-size: 14px;  ">
                                                            View details 
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>



                                    </div>
                                </div>

                                {{-- <div class="sft_btm">
                          <a class="cta"
                            onclick="changeCategory('{{ $business->category->translation()->first()->slug }}')">View
                            All
                            Alternatives</a>
                            </div> --}}

                            </section>

                            {{-- faq --}}
                            <section class="faq-section  faq-section_1 product_inr_faq p_50 pt-2 light" id="section15">
                                <div class="container">
                                    <div class="faq-inner">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="d-flex flex-column w-auto">
                                                    {{-- <h2>Frequently Asked Questions (FAQs)</h2>
                                                    <p>
                                                        Find quick answers to the most common questions about using Localio
                                                        to discover, filter, and connect with the best local businesses and
                                                        products.
                                                    </p> --}}

                                                    @php
                                                    use App\Models\StaticContentKey;

                                                    $faq_title = StaticContentKey::where('key', 'faq_title')->first();
                                                    $faq_description = StaticContentKey::where('key', 'faq_description')->first();
                                                    //dd($faq_title, $faq_description);
                                                @endphp

                                                    <h2>{{ $faq_title?->default_value ?? '' }}</h2>
                                                    <p>{{ $faq_description?->default_value ?? '' }}</p>


                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="faq-accor">
                                                    <div class="accordion" id="accordionExample">
                                                        @forelse ($business->faqs as $index => $faq)
                                                            @php $translation = $faq->translations->first(); @endphp
                                                            @if ($translation)
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header"
                                                                        id="heading{{ $index }}">
                                                                        <button
                                                                            class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}"
                                                                            type="button" data-bs-toggle="collapse"
                                                                            data-bs-target="#collapse{{ $index }}"
                                                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                                            aria-controls="collapse{{ $index }}">
                                                                            <span>{{ $translation->question }}</span>
                                                                        </button>
                                                                    </h2>
                                                                    <div id="collapse{{ $index }}"
                                                                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                                                        aria-labelledby="heading{{ $index }}"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body">
                                                                            {{ $translation->answer }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @empty
                                                            <p>No FAQs available for this business.</p>
                                                        @endforelse

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- busines bar --}}
                            <section class="about_asn_2 light pt-0 p_50">

                                <div class="about_asn_content">
                                    <div class="hd_content asan-text-para">
                                        {!! $product->translations->overview ?? "" !!}
                                    </div>

                                   {{-- review section  --}}
                                    {{-- <div class="asn_dv asv_orng asv_blue" data-aos="fade-up" data-aos-duration="1000">
                                        <div class="asn_dv_contnt">
                                            <div class="row frst_rw">

                                                {{-- Rating logic --}}
                                                {{-- @php
                                                    $ratingCount = $business->reviews
                                                        ->where('status', 'active')
                                                        ->count();
                                                    $averageRating =
                                                        $business->reviews->count() > 0
                                                            ? $business->reviews->avg('rating')
                                                            : 0;
                                                @endphp --}}
                                                {{-- LEFT COLUMN --}}
                                                {{-- <div class="col-md-6">
                                                    <div class="ans_lft">
                                                        <div class="asn-img">
                                                            <img
                                                                src="{{ asset($business->icon_id ?? 'front/img/tdot-rd.svg') }}">
                                                        </div> --}}

                                                        {{-- Business name + review info + inline tags --}}
                                                        {{-- <div class="asn-rating"> --}}
                                                            {{-- Top Row: Name + Heart + Review Year --}}
                                                            {{-- <div class="d-flex justify-content-between align-items-center">
                                                                <h6 class="light mb-0">
                                                                    {{ $business->translations->first()->name }}
                                                                </h6>

                                                                <div class="d-flex align-items-center gap-2"
                                                                    style="color: #FFF">
                                                                    <livewire:wishlist :product-id="$business->id"
                                                                        :wire:key="'wishlist-'.$business->id" /> --}}
                                                                    {{-- <span style="font-size: 14px; color: #ffffff;">Review
                                                            {{ date('Y') }}</span> --}}
                                                                {{-- </div>
                                                            </div> --}}

                                                            {{-- Bottom Row: Inline Info --}}
                                                            {{-- <div class="str_fl">
                                                                <p class="text-white mb-0"> --}}
                                                                    {{-- Star ratings below button --}}
                                                                {{-- <div class="tp-btm d-flex flex-col-mob mt-2">
                                                                    <div class="inn_ul">
                                                                        <div class="rating-stars">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($i <= floor($averageRating))
                                                                                    <i
                                                                                        class="fas fa-star text-warning"></i>
                                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                                    <i
                                                                                        class="fas fa-star-half-alt text-warning"></i>
                                                                                @else
                                                                                    <i
                                                                                        class="far fa-star text-warning"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                    <div class="rate_box" style="color:white;">
                                                                        {{ number_format($averageRating, 1) }} |
                                                                        {{-- {{ $ratingCount }} --}}
                                                                        {{-- @if ($ratingCount === 0)
                                                                            0 Ratings
                                                                        @elseif ($ratingCount === 1)
                                                                            1 Rating
                                                                        @else
                                                                            {{ $ratingCount }} Ratings
                                                                        @endif
                                                                    </div>
                                                                </div> --}}
                                                                {{-- End star rating --}}
                                                                {{-- </p>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div> --}}

                                                {{-- RIGHT COLUMN --}}
                                                {{-- <div class="col-md-6">
                                                    <div class="ans_ryt">
                                                        <div class="site_vsit">
                                                            <div class="accor-btn acr_wht">
                                                                <a href="{{ $link }}"
                                                                    class="cta cta_orange d-flex align-items-center"
                                                                    tabindex="0">
                                                                    Visit website
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                                </a>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                {{-- END RIGHT COLUMN --}}
                                            {{-- </div>

                                        </div>
                                    </div> --}}
                                    {{-- End Review  --}}
                                </div>
                                {{-- <div class="asan-text-para" data-aos="fade-up" data-aos-duration="1000">
                                <h6 class="fw_700 h6_26"> Usability and Experience</h6>
                                <p>{{ $business->translations->first()->description }} </p>
                                </div>
                                @if (!empty($business->pricingOptions) && $business->pricingOptions->first()?->translations->isNotEmpty())
                                @php
                                $pricingOption = $business->pricingOptions->first();
                                $translation =
                                $pricingOption->translations->firstWhere('lang_id', getCurrentLanguageID()) ??
                                $pricingOption->translations->first();
                                @endphp

                                <div class="asn_dv asv_orng trial-avil" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="asn_dv_contnt">
                                        <div class="row frst_rw">
                                            <div class="col-md-6">
                                                <div class="ans_lft">
                                                    <div class="asn-img">
                                                        <img src="{{ asset('front/img/tick-white.svg') }}">
                                                    </div>
                                                    <div class="asn-rating">
                                                        <p class="m-0">{{ $translation->name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="ans_ryt">
                                                    <div class="site_vsit">
                                                        <div class="accor-btn acr_wht">
                                                            <a href="{{ $link }}"
                                                                class="cta d-flex align-items-center" tabindex="0">Claim
                                                                Now</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif --}}
                            </section>
                            {{-- <div class="crm-review-innr crm-review-innr_2 " data-aos="fade-up" data-aos-duration="1000">
                    <div class="row pt-0 p_50">
                        <div class="col-xl-5">
                            <div class="sales-crm-pack crm-pack-lft">
                                <div class="inn_sl_hed">
                                    <div class="sli_img choice_img">
                                        <img class="slider_img"
                                            src="{{ asset($business->icon_id ?? 'front/img/big-asana.png') }}"
                                        alt="">
                                        </div>
                                        <div class="sl_h">
                                            <div class="inn_h d-flex align-items-center">
                                                <h6 class="head">{{ $business->translations->first()->name }}
                                                </h6>
                                                <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                                            </div>
                                            <div class="tp-btm d-flex flex-col-mob pt-2">
                                                <div class="inn_ul">
                                                    <div class="rating-stars">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= floor($averageRating))
                                                            <i class="fas fa-star text-warning"></i>
                                                            @elseif ($i - 0.5 <= $averageRating)
                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                                @else
                                                                <i class="far fa-star text-warning"></i>
                                                                @endif
                                                                @endfor
                                                    </div>
                                                </div>
                                                <div class="rate_box">
                                                    {{ number_format($averageRating, 1) }} | {{ $ratingCount }}
                                                    ratings
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        </div>
                                        </div>


                                        <div class="col-xl-4 ">
                                            <div class="sales-crm-pack">
                                                <div class="feture_box">
                                                    <h6 class="size22 big-bld">Localio Review Breakdown</h6>
                                                    <ul class="p-0 m-0">
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Ease of Use</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $easeOfUseAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $easeOfUseAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Customer Service</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $customerServiceAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $customerServiceAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Features</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $exclusiveFeatureAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex justify-content-between">
                                                            <span class="lyt-text">Value for Money</span>
                                                            <div class="prgs_br">
                                                                <progress class="progress-bar"
                                                                    value="{{ $valueForMoneyAvg * 20 }}"
                                                                    max="100"></progress>
                                                                <output>{{ $valueForMoneyAvg }}/5</output>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xl-3 ">
                                            <div class="sales-crm-pack">
                                                <div class="fre_trail feture_box size22">
                                                    <div class="grn_check_big">
                                                        <img src="{{ asset('front/img/new-grn-chk.png') }}">
                                                    </div>
                                                    <h6 class="blue-text big-bld">Free Trial <br>
                                                        Available
                                                    </h6>
                                                    <div class="accor-btn p-0">
                                                        <a class="cta cta_white">Claim Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        </div> --}}


                            <!-- scetion crm sec -->
                            <section class="crm_sec revie_left_rgt_sec" id="section14" style="overflow: visible !important;">
                                <style>
                                    .review-sidebar-sticky {
                                        position: sticky !important;
                                        position: -webkit-sticky !important;
                                        top: 100px !important;
                                        height: fit-content !important;
                                        z-index: 10;
                                    }
                                    .rating-filter-checkbox {
                                        width: 18px;
                                        height: 18px;
                                        margin-right: 10px;
                                        cursor: pointer;
                                        accent-color: #0056b3;
                                    }
                                    .review-star-box {
                                        border: none !important;
                                        background: none !important;
                                        box-shadow: none !important;
                                        padding: 0 !important;
                                    }
                                    .rating-filter-header h4 {
                                        font-size: 16px;
                                        font-weight: 600;
                                        color: #777;
                                        margin-bottom: 5px;
                                    }
                                    .overall-stars {
                                        display: flex;
                                        align-items: center;
                                        gap: 8px;
                                        margin-bottom: 5px;
                                    }
                                    .overall-stars i {
                                        font-size: 18px;
                                    }
                                    .filter-by-title-row {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        margin-top: 20px;
                                        margin-bottom: 15px;
                                        font-weight: 600;
                                        font-size: 15px;
                                        color: #333;
                                        border-bottom: 1px solid #eee;
                                        padding-bottom: 8px;
                                    }
                                    .clear-filters-btn {
                                        color: #007bff;
                                        text-decoration: none;
                                        font-size: 13px;
                                        cursor: pointer;
                                    }
                                    .clear-filters-btn:hover {
                                        text-decoration: underline;
                                    }
                                    .review-row-prod-inr {
                                        display: flex !important;
                                        align-items: stretch !important;
                                    }
                                    .crm_sec, .crm_review_box, .revie_left_rgt_sec, .review_sec, div[data-business-id], .all_sec_wrp {
                                        overflow: visible !important;
                                    }
                                    .review_sec .review_detl {
                                        border: 1px solid rgba(0, 0, 0, 0.08) !important;
                                        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important;
                                        border-radius: 20px !important;
                                        transition: box-shadow 0.3s ease, border-color 0.3s ease;
                                    }
                                    .review_sec .review_detl:hover {
                                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07) !important;
                                        border-color: rgba(0, 0, 0, 0.12) !important;
                                    }

                                    /* Responsiveness for Review Section */
                                    @media (max-width: 991px) {
                                        .crm_sec {
                                            background: transparent !important;
                                            padding: 0 !important;
                                        }
                                        .review-row-prod-inr {
                                            display: block !important;
                                        }
                                        .review-sidebar-sticky {
                                            position: static !important;
                                            margin-bottom: 30px;
                                        }
                                    }
                                    @media (max-width: 575px) {
                                        .review_sec .review_detl {
                                            padding: 20px 15px !important;
                                            border-radius: 15px !important;
                                        }
                                        .review_sec .review_detl .reviw_hd {
                                            flex-direction: column;
                                            align-items: flex-start;
                                            gap: 10px;
                                        }
                                        .review_sec .review_detl .reviw_hd p {
                                            margin: 0;
                                            font-size: 13px;
                                        }
                                        .ans_lft {
                                            gap: 0px 12px;
                                        }
                                        .asn-img img {
                                            width: 55px !important;
                                            height: 55px !important;
                                        }
                                        .review_text.size18 {
                                            font-size: 15px !important;
                                        }
                                        .review_text.size18 .size22.big-bld {
                                            font-size: 18px !important;
                                            line-height: 1.3;
                                        }
                                        .review-sidebar-sticky h2 {
                                            font-size: 22px !important;
                                        }
                                    }
                                </style>
                                <div class="crm_review_box review_sec" id="all-reviews">
                                    
                                    <!-- Review Prompt Banner -->
                                    <div class="review-prompt-banner" id="reviewPromptBanner" style="background-color: #f7fafc; border-radius: 12px; padding: 20px 24px; margin-bottom: 40px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #e2e8f0; flex-wrap: wrap; gap: 20px;">
                                        <div style="display: flex; align-items: center; gap: 16px;">
                                            <div class="banner-icon" style="width: 52px; height: 52px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0; overflow: hidden;">
                                                <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                                            </div>
                                            <div>
                                                <h4 style="margin: 0 0 4px 0; font-size: 17px !important; font-weight: 700 !important; color: #1e3050 !important;">Have you used {{ $business->translations->first()->name ?? 'this product' }} before?</h4>
                                                <p style="margin: 0; font-size: 13.5px; color: #4a5568;">Answer a few questions to help the community.</p>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 12px; align-items: center;">
                                            @auth
                                                <button onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true }); document.getElementById('reviewPromptBanner').style.display = 'none';" style="padding: 8px 24px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                                                    <i class="fas fa-check" style="color: #06498b;"></i> Yes
                                                </button>
                                                <button onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false }); document.getElementById('reviewPromptBanner').style.display = 'none';" style="padding: 8px 24px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                                                    <i class="fas fa-times" style="color: #e53e3e;"></i> No
                                                </button>
                                            @else
                                                <button onclick="openLoginModal()" style="padding: 8px 24px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                                                    <i class="fas fa-check" style="color: #06498b;"></i> Yes
                                                </button>
                                                <button onclick="openLoginModal()" style="padding: 8px 24px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                                                    <i class="fas fa-times" style="color: #e53e3e;"></i> No
                                                </button>
                                            @endauth
                                        </div>
                                    </div>

                                    <div class="row review-row-prod-inr">
                                        
                                        <!-- Left Column (Sticky Header, Sort, Overall rating, filter) -->
                                        <div class="col-lg-4">
                                            <div class="review-sidebar-sticky">
                                                
                                                <!-- Localio Reviews Header -->
                                                <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; color: #1e3050; line-height: 1.3;">
                                                    User reviews
                                                </h2>

                                                <div class="overall-rating-box" style="background-color: #fff; border-radius: 12px; padding: 24px; border: 1px solid #06498b1a; box-shadow: 0 8px 24px rgb(141 143 144 / 28%); margin-bottom: 24px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                                                    <span style="font-size: 48px; font-weight: 700; color: #002347; line-height: 1; margin-bottom: 12px;">{{ number_format($averageRating, 1) }}</span>
                                                    <div class="overall-stars" style="display: flex; align-items: center; justify-content: center; gap: 4px; margin-bottom: 0;">
                                                        @for ($j = 1; $j <= 5; $j++)
                                                            @if ($j <= floor($averageRating))
                                                                <i class="fas fa-star text-warning" style="font-size: 18px;"></i>
                                                            @elseif ($j - 0.5 <= $averageRating)
                                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 18px;"></i>
                                                            @else
                                                                <i class="far fa-star text-warning" style="font-size: 18px;"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span style="font-size: 14px; color: #666;">{{ number_format($ratingCount) }} reviews</span>
                                                </div>

                                                <!-- Filter by Rating Title Row -->
                                                <div class="filter-by-title-row" style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 6px;">
                                                    <span style="font-size: 15px; font-weight: 600; color: #002655;">Filter by rating</span>
                                                </div>

                                                <!-- Star Breakdown Checkboxes -->
                                                <div class="review-star-box">
                                                    <ul class="progress-list" style="list-style: none; padding: 0; margin: 0;">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            @php
                                                                $count = $ratingCounts[$i] ?? 0;
                                                                $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                                            @endphp
                                                            <li class="progress-list-item" style="display: flex; align-items: center; margin-bottom: 10px; gap: 8px;">
                                                                <input type="checkbox" class="rating-filter-checkbox" value="{{ $i }}" id="star-check-{{ $i }}" style="cursor: pointer; width: 16px; height: 16px; margin: 0;">
                                                                <label for="star-check-{{ $i }}" style="display: flex; align-items: center; width: 100%; cursor: pointer; margin: 0;">
                                                                    <span style="display: inline-flex; align-items: center; width: 45px; font-size: 14px; color: #555; flex-shrink: 0;">
                                                                        <i class="far fa-star text-warning" style="margin-right: 4px;"></i> {{ $i }}
                                                                    </span>
                                                                    <div class="progress-box" style="flex-grow: 1; height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden; margin-left: 4px; margin-right: 10px;">
                                                                        <div class="progress-fill" style="width: {{ $percent }}%; height: 100%; background: #4a4a4a;"></div>
                                                                    </div>
                                                                    <span style="font-size: 13px; color: #888; min-width: 35px; text-align: right; flex-shrink: 0; white-space: nowrap;">({{ $count }})</span>
                                                                </label>
                                                            </li>               
                                                        @endfor
                                                    </ul>
                                                </div>

                                             </div>
                                        </div>

                                        <!-- Right Column (Write Review button & Reviews List) -->
                                        <div class="col-lg-8">
                                            
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                                                <div class="selct_box">
                                                    <form method="GET" id="sort-form" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                                        <label for="rating-select" style="font-size: 14px; font-weight: 600; color: #555; margin: 0; white-space: nowrap;">Sort by:</label>
                                                        <select class="form-select" id="rating-select" name="sort" style="padding: 5px 30px 5px 10px; font-size: 13px; border-radius: 6px; cursor: pointer; width: auto; min-width: 130px; border: 1px solid #ced4da;">
                                                            <option value="recent" {{ request('sort') == 'recent' || !request('sort') ? 'selected' : '' }}>Most Recent</option>
                                                            <option value="best" {{ request('sort') == 'best' ? 'selected' : '' }}>Best Rating</option>
                                                            <option value="high-to-low" {{ request('sort') == 'high-to-low' ? 'selected' : '' }}>High to Low</option>
                                                            <option value="low-to-high" {{ request('sort') == 'low-to-high' ? 'selected' : '' }}>Low to High</option>
                                                        </select>
                                                    </form>
                                                </div>

                                                <a class="write-review-link"
                                                    @auth
                                                        onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                                    @else
                                                        onclick="openLoginModal()" 
                                                    @endauth
                                                    style="cursor: pointer; font-size: 15px; font-weight: 600; color: #06498b; text-decoration: none;"
                                                >Write review</a>
                                            </div>

                                            <div id="reviews-list-container">
                                                @include('User.product.partials.reviews_list')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const checkboxes = document.querySelectorAll('.rating-filter-checkbox');
                                    const sortSelect = document.getElementById('rating-select');
                                    const clearBtn = document.getElementById('clear-filters');
                                    const container = document.getElementById('reviews-list-container');

                                    const sortForm = document.getElementById('sort-form');
                                    if (sortForm) {
                                        sortForm.addEventListener('submit', function (e) {
                                            e.preventDefault();
                                        });
                                    }

                                    if (sortSelect) {
                                        sortSelect.addEventListener('change', fetchReviews);
                                    }

                                    checkboxes.forEach(cb => {
                                        cb.addEventListener('change', function() {
                                            updateClearButtonVisibility();
                                            fetchReviews();
                                        });
                                    });

                                    if (clearBtn) {
                                        clearBtn.addEventListener('click', function() {
                                            checkboxes.forEach(cb => cb.checked = false);
                                            updateClearButtonVisibility();
                                            fetchReviews();
                                        });
                                    }

                                    function updateClearButtonVisibility() {
                                        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                                        if (clearBtn) {
                                            clearBtn.style.display = anyChecked ? 'inline' : 'none';
                                        }
                                    }

                                    function fetchReviews() {
                                        const selectedStars = Array.from(checkboxes)
                                            .filter(cb => cb.checked)
                                            .map(cb => cb.value);

                                        const sortValue = sortSelect ? sortSelect.value : 'recent';

                                        container.style.opacity = '0.5';

                                        const url = new URL(window.location.href);
                                        url.searchParams.set('sort', sortValue);
                                        if (selectedStars.length > 0) {
                                            url.searchParams.set('stars', selectedStars.join(','));
                                        } else {
                                            url.searchParams.delete('stars');
                                        }

                                        fetch(url.toString(), {
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(response => response.text())
                                        .then(html => {
                                            container.innerHTML = html;
                                            container.style.opacity = '1';
                                            if (typeof AOS !== 'undefined') {
                                                AOS.refresh();
                                            }
                                        })
                                        .catch(err => {
                                            console.error('Error fetching reviews:', err);
                                            container.style.opacity = '1';
                                        });
                                    }
                                });
                                </script>     

                                </div>
                            </section>
                        </div>
                    </div>

                    {{-- Comments this table and added this header change layout --}}
                    {{-- <div class="col-lg-3">
                        <div class="inner_table2">

                            @php

                                $name = $business->translations->first()->name ?? 'This Business';

                                // Fixed dynamic top content
                                $staticBottomSections = [
                                    ['id' => 'section9', 'label' => "Software like $name"],
                                    ['id' => 'section15', 'label' => 'FAQ'],
                                    ['id' => 'section14', 'label' => "Localio $name Reviews"],
                                    ['id' => 'section16', 'label' => 'Get the Best Picks in Your Inbox'],
                                ];

                                // Dynamic middle sections from topics
                                // Start dynamic topic sections at a high number to avoid conflict
                                $dynamicTopics = collect($business->category->topics ?? [])
                                    ->map(function ($topic, $index) use ($name) {
                                        $translatedHeading =
                                            $topic->translations->first()?->title ?? 'Topic ' . ($index + 1);
                                        $label = str_replace('{business_name}', $name, $translatedHeading);

                                        return [
                                            'id' => 'section' . (100 + $index), // Avoid conflicts with static IDs
                                            'label' => $label,
                                        ];
                                    })
                                    ->toArray();

                                // dd($dynamicTopics);

                                // Fixed bottom static sections

                                $staticTopSections = [
                                    ['id' => 'section1', 'label' => 'User Reviews Breakdown'],
                                    ['id' => 'section2', 'label' => "What is $name"],
                                    ['id' => 'section3', 'label' => "$name Pricing Plans"],
                                    ['id' => 'section4', 'label' => "$name Pros and cons"],
                                    ['id' => 'section5', 'label' => 'Compare With A Popular Alternative'],
                                    ['id' => 'section6', 'label' => 'Top Reviews'],
                                ];

                                $tableOfContents = array_merge(
                                    $staticTopSections,
                                    $dynamicTopics,
                                    $staticBottomSections,
                                );
                            @endphp

                            <div class="inner_table2">
                                <div class="table_st">
                                    <div id="table-of-content" class="feture_box p-3 shadow rounded bg-white"
                                        style="top: 90px; max-height: max-content; overflow-y: auto;">
                                        <h6 class="blue-text big-bld mb-3">Table of Content</h6>
                                        <ul class="list-unstyled toc-links small">
                                            @foreach ($tableOfContents as $i => $item)
                                                @php
                                                    $isLastDynamic =
                                                        str_starts_with($item['id'], 'section') &&
                                                        (int) filter_var($item['id'], FILTER_SANITIZE_NUMBER_INT) ===
                                                            100 + count($dynamicTopics) - 1;
                                                @endphp

                                                <li class="py-1 {{ $isLastDynamic ? 'mb-0' : '' }}">
                                                    <a href="#{{ $item['id'] }}" class="text-blue-600 d-block">
                                                        {{ $item['label'] }}
                                                    </a>
                                                </li>

                                                {{-- Add divider spacing (not <br>) after last dynamic topic --}}
                                                 {{-- @if ($isLastDynamic)
                                                    <li class="my-1"></li>
                                                @endif
                                            @endforeach


                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div> --}}
                    {{-- End this table code --}}

                </div>


            </div>
            <!-- <div id="sectionDiscussions" class="mt-5" style="border-top: 1px solid #eee; padding-top: 30px;"> -->
            <div id="sectionDiscussions">
                <!-- Reddit-style Discussions section placeholder -->
            </div>
            <div class="mt-5">
                <section class="subs_sec light p_50 d-none" id="section16">
                    <x-news-letter-subscription />
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== FILTER FUNCTIONALITY =====
            // 1. Sort reviews
            const sortSelect = document.getElementById('reviewSort');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    applyFilters();
                });
            }

            // 2. Filter by star rating
            const starFilterButtons = document.querySelectorAll('.filter-stars .btn');
            if (starFilterButtons) {
                starFilterButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        // Remove active class from all buttons
                        starFilterButtons.forEach(btn => btn.classList.remove('active'));
                        // Add active class to clicked button
                        this.classList.add('active');
                        applyFilters();
                    });
                });
            }

            // Function to apply all filters
            function applyFilters() {
                const sortBy = sortSelect ? sortSelect.value : 'newest';

                // Get active star filter
                const activeStarFilter = document.querySelector('.filter-stars .btn.active');
                let starFilter = 'all';
                if (activeStarFilter && !activeStarFilter.textContent.includes('All')) {
                    starFilter = activeStarFilter.textContent.trim().split(' ')[0];
                }

                // Build the query string
                const params = new URLSearchParams(window.location.search);
                params.set('sort', sortBy);
                params.set('stars', starFilter);

                // Redirect with filter parameters
                window.location.href = `${window.location.pathname}?${params.toString()}`;
            }

            // ===== REVIEW FORM ENHANCEMENTS =====
            // Star rating interaction in the form
            const ratingInputs = document.querySelectorAll('.rating-select input[type="radio"]');
            const ratingLabels = document.querySelectorAll('.rating-select label');
            const ratingText = document.querySelector('.rating-text');

            const ratingDescriptions = {
                5: 'Excellent! Highly recommend',
                4: 'Very Good',
                3: 'Good, meets expectations',
                2: 'Fair, some issues',
                1: 'Poor, not recommended'
            };

            // Set initial state of stars based on any selected input
            function updateStars() {
                let selectedRating = null;

                ratingInputs.forEach(input => {
                    if (input.checked) {
                        selectedRating = parseInt(input.value);
                    }
                });

                ratingLabels.forEach(label => {
                    const starValue = parseInt(label.getAttribute('for').replace('star', ''));
                    const starIcon = label.querySelector('.star-icon');

                    if (selectedRating !== null && starValue <= selectedRating) {
                        starIcon.classList.remove('far');
                        starIcon.classList.add('fas');
                        starIcon.classList.add('text-warning');
                    } else {
                        starIcon.classList.remove('fas');
                        starIcon.classList.remove('text-warning');
                        starIcon.classList.add('far');
                    }
                });

                if (selectedRating !== null && ratingText) {
                    ratingText.textContent = ratingDescriptions[selectedRating];
                }
            }

            // Initialize stars
            updateStars();

            // Handle star selection
            ratingLabels.forEach(label => {
                label.addEventListener('mouseenter', function() {
                    const starValue = parseInt(this.getAttribute('for').replace('star', ''));

                    ratingLabels.forEach(label => {
                        const labelValue = parseInt(label.getAttribute('for').replace(
                            'star', ''));
                        const starIcon = label.querySelector('.star-icon');

                        if (labelValue <= starValue) {
                            starIcon.classList.remove('far');
                            starIcon.classList.add('fas');
                            starIcon.classList.add('text-warning');
                        } else {
                            starIcon.classList.remove('fas');
                            starIcon.classList.remove('text-warning');
                            starIcon.classList.add('far');
                        }
                    });

                    if (ratingText) {
                        ratingText.textContent = ratingDescriptions[starValue];
                    }
                });

                label.addEventListener('mouseleave', function() {
                    updateStars();
                });
            });

            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    updateStars();
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initializeRatings();
            setupStarHoverEffects();
            animateStars();
        });

        function initializeRatings() {
            const ratingContainers = document.querySelectorAll('.tab_star_li');

            ratingContainers.forEach(container => {
                const ratingValue = parseFloat(container.dataset.rating) || 0;
                container.innerHTML = '';

                for (let i = 1; i <= 5; i++) {
                    const starWrapper = document.createElement('span');
                    starWrapper.classList.add('rating-on');
                    starWrapper.setAttribute('data-rating', i);
                    starWrapper.setAttribute('role', 'img');
                    starWrapper.setAttribute('aria-label', `Rated ${i} out of 5`);

                    const filledStar = document.createElement('span');
                    filledStar.textContent = '★';
                    filledStar.style.position = 'absolute';
                    filledStar.style.left = '0';
                    filledStar.style.top = '0';
                    filledStar.style.overflow = 'hidden';
                    filledStar.style.width = '0%';
                    filledStar.style.color = '#FFC107';
                    filledStar.style.zIndex = '1';

                    const baseStar = document.createElement('span');
                    baseStar.textContent = '★';
                    baseStar.style.color = '#e0e0e0';
                    baseStar.style.position = 'relative';
                    baseStar.style.zIndex = '0';

                    // Append both spans
                    starWrapper.style.position = 'relative';
                    starWrapper.style.display = 'inline-block';
                    starWrapper.style.width = '20px';
                    starWrapper.style.height = '20px';

                    if (i <= ratingValue) {
                        filledStar.style.width = '100%';
                    } else if (i - 0.5 <= ratingValue) {
                        filledStar.style.width = '50%';
                    }

                    starWrapper.appendChild(filledStar);
                    starWrapper.appendChild(baseStar);
                    container.appendChild(starWrapper);
                }
            });
        }

        function setupStarHoverEffects() {
            document.querySelectorAll('.tab_star_li').forEach(container => {
                const stars = container.querySelectorAll('.rating-on');

                stars.forEach(star => {
                    star.addEventListener('mouseenter', function() {
                        const hoverRating = parseInt(this.getAttribute('data-rating'));

                        stars.forEach((s, index) => {
                            const filled = s.querySelector('span:first-child');
                            if (index < hoverRating) {
                                filled.style.width = '100%';
                            } else {
                                filled.style.width = '0%';
                            }
                        });
                    });
                });

                container.addEventListener('mouseleave', function() {
                    initializeRatings(); // Reset to original
                });
            });
        }

        function animateStars() {
            setTimeout(() => {
                document.querySelectorAll('.tab_star_li .rating-on').forEach((star, index) => {
                    star.style.opacity = '0';
                    setTimeout(() => {
                        star.style.opacity = '1';
                        star.style.transition = 'opacity 0.3s ease';
                    }, index * 100);
                });
            }, 300);
        }

        jQuery(window).scroll(function() {
            var scroll = jQuery(window).scrollTop();
            if (scroll >= 200) {
                jQuery(".asn_main_sec > .asn_dv").addClass("fixed-div");
            } else {
                jQuery(".asn_main_sec > .asn_dv").removeClass("fixed-div");
            }
        });
    </script>
    <script>
        const form = document.getElementById('some-form');
        console.log(form); // should not be null
        if (form) {
            form.submit();
        } else {
            console.warn('Form not found');
        }
    </script>

    <!-- jQuery (required for Bootstrap JS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        window.addEventListener('review-submitted', () => {
            window.location.reload();
        });
    </script>


    {{-- Content table script --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const links = document.querySelectorAll('#table-of-content a');
            const sections = Array.from(links).map(link => {
                const id = link.getAttribute('href').substring(1);
                return document.getElementById(id);
            });

            // Smooth scroll on click
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    links.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    const targetId = this.getAttribute('href');
                    const target = document.querySelector(targetId);
                    if (target) {
                        const offset = 100;
                        const targetPosition = target.getBoundingClientRect().top + window.scrollY -
                            offset;

                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            function updateActiveSection() {
                const scrollPosition = window.scrollY + 120;
                let currentSectionId = null;

                sections.forEach(section => {
                    if (section && section.offsetTop <= scrollPosition) {
                        currentSectionId = section.id;
                    }
                });

                // Default to the first section if we are at the top or above it
                if (!currentSectionId && sections.length > 0 && sections[0]) {
                    currentSectionId = sections[0].id;
                }

                links.forEach(link => {
                    link.classList.remove('active');
                    const href = link.getAttribute('href').substring(1);
                    if (href === currentSectionId) {
                        link.classList.add('active');
                    }
                });
            }

            // Active section highlight on scroll
            window.addEventListener('scroll', updateActiveSection);

            // Initialize on page load
            updateActiveSection();
        });
    </script>

    {{-- Business image slider script --}}

    {{-- <script>
    $(document).ready(function () {
        // Initialize main slider (big image)
        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            adaptiveHeight: true
        });

        // Initialize thumbnail slider (small images)
        $('.slider-nav').slick({
            slidesToShow: 4, // Adjust as needed
            slidesToScroll: 1,
            vertical: true,
            arrows: false,
            infinite: false,
            centerMode: false,
            focusOnSelect: false,
            swipe: false,
            draggable: false
            //Do NOT set `asNavFor`
        });

        //  Fix: Use `data-slick-index` safely
        $('.slider-nav').on('mouseenter', '.slick-slide', function () {
            let $slide = $(this);
            let index = $slide.attr('data-slick-index');

            if (index !== undefined) {
                $('.slider-for').slick('slickGoTo', parseInt(index));

                // Optional: highlight active thumbnail
                $('.slider-nav img').css('border-color', 'transparent');
                $slide.find('img').css('border-color', '#007bff');
            }
        });

        // Set initial border
        setTimeout(function () {
            $('.slider-nav .slick-current img').css('border-color', '#007bff');
        }, 100);
    });
        </script> --}}

        <script>
            $(document).ready(function () {
                let slidersReady = false;

                // Initialize main slider (big image)
                $('.slider-for').on('init', function () {
                    slidersReady = true;
                }).slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    fade: true,
                    adaptiveHeight: true
                });

                // Initialize thumbnail slider (small images)
                $('.slider-nav').on('init', function() {
                    // Set initial border for active thumbnail after nav slider is initialized
                    setTimeout(() => {
                        $('.slider-nav .slick-slide[data-slick-index="0"] img').css('border-color', '#007bff');
                    }, 50);
                }).slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    vertical: true,
                    arrows: false,
                    infinite: false,
                    centerMode: false,
                    focusOnSelect: false,
                    swipe: false,
                    draggable: false
                });

                // Hover to change main image (only after sliders are ready)
                $('.slider-nav').on('mouseenter', '.slick-slide', function () {
                    if (!slidersReady) return; // Ensure sliders are fully initialized

                    let $thumb = $(this);
                    let index = $thumb.data('slick-index');

                    if (typeof index !== 'undefined') {
                        // Change main image to the clicked thumbnail's index
                        $('.slider-for').slick('slickGoTo', parseInt(index, 10), true);

                        // Optional: Set active border color for the current thumbnail
                        $('.slider-nav img').css('border-color', 'transparent');
                        $thumb.find('img').css('border-color', '#007bff');
                    }
                });

                // Fallback: Reset to active thumbnail on mouse leave
                $('.slider-nav').on('mouseleave', function () {
                    let currentIndex = $('.slider-for').slick('slickCurrentSlide');
                    let $currentThumb = $('.slider-nav [data-slick-index="' + currentIndex + '"] img');
                    $('.slider-nav img').css('border-color', 'transparent');
                    $currentThumb.css('border-color', '#007bff');
                });

                // Click handlers to open the gallery modal using CustomEvent delegation
                $('.slider-for').on('click', '.asan-slider-inr', function () {
                    let index = $(this).attr('data-gallery-index');
                    if (typeof index !== 'undefined') {
                        window.dispatchEvent(new CustomEvent('open-gallery-modal', { detail: { index: parseInt(index, 10) } }));
                    }
                });

                $('.slider-nav').on('click', '[data-gallery-index]', function () {
                    let index = $(this).attr('data-gallery-index');
                    if (typeof index !== 'undefined') {
                        window.dispatchEvent(new CustomEvent('open-gallery-modal', { detail: { index: parseInt(index, 10) } }));
                    }
                });

                // Preload all images in the slider to avoid delay when switching images
                function preloadSliderImages() {
                    $('.slider-for img').each(function () {
                        const img = new Image();
                        img.src = $(this).attr('src');
                    });
                }

                $(window).on('load', function () {
                    preloadSliderImages(); // Preload images on window load
                });
            });
        </script>

         {{-- End Business image slider script --}}

    <script>
        function applyStarHoverEffects(container) {
            const stars = container.querySelectorAll('.star-item');

            stars.forEach(star => {
                star.addEventListener('mouseenter', () => {
                    const hoverValue = parseInt(star.dataset.value);

                    stars.forEach(s => {
                        const value = parseInt(s.dataset.value);
                        s.classList.toggle('js-hovered', value <= hoverValue);
                    });
                });

                star.addEventListener('mouseleave', () => {
                    stars.forEach(s => s.classList.remove('js-hovered'));
                });
            });
        }

        function setupAllStarRatings() {
            const containers = document.querySelectorAll('.star-rating');

            containers.forEach(container => {
                if (!container.dataset.hoverSetup) {
                    applyStarHoverEffects(container);
                    container.dataset.hoverSetup = 'true';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            setupAllStarRatings();

            const observer = new MutationObserver(() => {
                setupAllStarRatings();
            });

            observer.observe(document.body, { childList: true, subtree: true });
        });

        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', () => {
                setupAllStarRatings();
            });
        });
    </script>

    {{-- Preview Section Script --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const swiperContainer = document.querySelector(".myReviewSlider");
        const wrapper = swiperContainer.querySelector(".swiper-wrapper");
        let slides = wrapper.querySelectorAll(".swiper-slide");
        let slideCount = slides.length;

        const slidesPerView = 1;
        const slidesPerGroup = 1;
        const minLoopSlides = 2;

        // Duplicate slides if less than minimum
        if (slideCount < minLoopSlides) {
            const clonesNeeded = minLoopSlides - slideCount;
            for (let i = 0; i < clonesNeeded; i++) {
                const clone = slides[i % slideCount].cloneNode(true);
                wrapper.appendChild(clone);
            }
            slides = wrapper.querySelectorAll(".swiper-slide");
            slideCount = slides.length;
        }

        const reviewSwiper = new Swiper(".myReviewSlider", {
            slidesPerView: slidesPerView,
            slidesPerGroup: slidesPerGroup,
            spaceBetween: 20,
            loop: slideCount > 1,
            autoplay: false,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                0: { slidesPerView: 1, slidesPerGroup: 1 },
                768: { slidesPerView: slidesPerView, slidesPerGroup: slidesPerGroup }
            }
        });
    
        // Add 'active' class to the first slide initially
        slides.forEach(slide => slide.classList.remove('active'));
        slides[reviewSwiper.activeIndex].classList.add('active');

        // Update 'active' class on slide change
        reviewSwiper.on('slideChange', function () {
            slides.forEach(slide => slide.classList.remove('active'));
            slides[reviewSwiper.activeIndex].classList.add('active');
        });

        // Show/hide navigation buttons
        const navButtons = document.querySelectorAll('.swiper-button-next, .swiper-button-prev');
        navButtons.forEach(el => {
            if (slideCount > 1) {
                el.style.display = 'block';
                el.classList.remove('carousel-disabled');
            } else {
                el.style.display = 'none';
                el.classList.add('carousel-disabled');
            }
        });
    });
    </script>

{{-- Added This Script View All Reviews Scrolling --}}
<script>
    $(document).ready(function () {
        // Smooth scroll on "View All Reviews"
        $("#scrollToReviews").on("click", function (e) {
            e.preventDefault();

            // target the full reviews section
            let reviewsSection = $("#nav-tabContent");

            if (reviewsSection.length) {
                $("html, body").animate({
                    scrollTop: reviewsSection.offset().top
                }, 600); // 600ms = smooth duration
            }
        });
    });
</script>


{{-- Key Feature Review Script --}}
<script>
    $(function () {
        let selectedRating = 0;
        let selectedFeatureId = null;
        let businessId = "{{ $business->id ?? '' }}";

        // Setup CSRF token
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Open popup when clicking rating text
        $('.open-review-popup').on('click', function () {
            selectedFeatureId = $(this).data('feature');
            $('#popupFeatureId').val(selectedFeatureId);
            $('#reviewPopup').modal('show');
        });

        // Star selection
        $('.popup-star').on('mouseover', function () {
            const value = $(this).data('value');
            $('.popup-star').each(function () {
                $(this).css('color', $(this).data('value') <= value ? '#fbc02d' : '#ccc');
            });
        }).on('mouseout', function () {
            $('.popup-star').each(function () {
                $(this).css('color', $(this).data('value') <= selectedRating ? '#fbc02d' : '#ccc');
            });
        }).on('click', function () {
            selectedRating = $(this).data('value');
            $('#popupRating').val(selectedRating);
            $('.popup-star').each(function () {
                $(this).css('color', $(this).data('value') <= selectedRating ? '#fbc02d' : '#ccc');
            });
        });

        // Submit review via AJAX
        $('#submitPopupReview').on('click', function (e) {
            e.preventDefault();

            const featureId = $('#popupFeatureId').val();
            const rating = $('#popupRating').val();
            const comment = $('#popupComment').val();

            if (!rating) {
                toastr.warning('Please select a rating before submitting.');
                return;
            }

            $.ajax({
                url: "{{ route('business.feature.review.store', app()->getLocale()) }}",
                type: "POST",
                dataType: "json",
                data: {
                    business_id: businessId,
                    feature_id: featureId,
                    rating: rating,
                    comment: comment
                },
                beforeSend: function() {
                    $('#submitPopupReview').prop('disabled', true);
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#reviewPopup').modal('hide');
                        $('#popupComment').val('');
                        $('.popup-star').css('color', '#ccc');
                        selectedRating = 0;
                    } else {
                        toastr.error(response.message || 'Something went wrong.');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        toastr.error('You must be logged in to submit a review.');
                    } else if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        toastr.error(errors);
                    } else {
                        toastr.error(' Error submitting review. Please try again.');
                    }
                },
                 complete: function() {
                    $('#submitPopupReview').prop('disabled', false);
                }
            });
        });
    </script>
    <!-- Big Premium Gallery Modal (Bootstrap 5) -->
    <div class="modal fade" id="imageGalleryModal" tabindex="-1" aria-hidden="true" style="z-index: 999999;">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                
                <!-- Close Button -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" 
                        style="position: absolute; right: 24px; top: 24px; z-index: 10000; font-size: 20px; font-weight: bold; background: none; border: none; color: #555;">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <!-- Header: Icon, Name, Rating | CTA -->
                <div class="gallery-header">
                    <div class="gallery-header-left">
                        <div style="width: 56px; height: 56px; border-radius: 8px; overflow: hidden; background: #f9f9f9; display: flex; align-items: center; justify-content: center; border: 1px solid #eaeaea; flex-shrink: 0;">
                            <img src="{{ asset($business->icon_id ?? 'front/img/big-asana.png') }}" 
                                 alt="{{ $business->translations->first()->name }}" 
                                 style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 22px; font-weight: 700; color: #002347;">
                                {{ $business->translations->first()->name }}
                            </h3>
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #555; white-space: nowrap; flex-wrap: nowrap;">
                                <div style="color: #ff9d28; display: flex; gap: 2px; flex-shrink: 0; white-space: nowrap;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($averageRating))
                                            <i class="fas fa-star"></i>
                                        @elseif ($i - 0.5 <= $averageRating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span style="font-weight: 600; color: #333;">{{ number_format($averageRating, 1) }}</span>
                                <span style="color: #888;">({{ $ratingCount }} reviews)</span>
                            </div>
                        </div>
                    </div>

                    <!-- CTA button -->
                    <div class="gallery-header-cta">
                        <a href="{{ $business->affiliate_link ?? $business->permanent_url }}" 
                           target="_blank"
                           class="cta cta_orange"
                           style="padding: 12px 24px; font-weight: 600; border-radius: 30px; text-decoration: none; display: flex; align-items: center; gap: 8px;"
                        >
                            Visit Website
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Body: Large image + arrows -->
                <div class="gallery-body">
                    <!-- Left Arrow -->
                    <button type="button" id="modalPrevBtn"
                            style="position: absolute; left: 24px; top: 50%; transform: translateY(-50%); background: #ffffff; border: 1px solid #eaeaea; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-size: 18px; color: #333; z-index: 1000; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#007bff'; this.style.color='#ffffff';"
                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#333';"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <!-- Active Image -->
                    <div class="gallery-image-wrap">
                        <img id="modalActiveImg" src="" 
                             alt="Active View" 
                             style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; display: block; width: auto; height: auto;">
                    </div>

                    <!-- Right Arrow -->
                    <button type="button" id="modalNextBtn"
                            style="position: absolute; right: 24px; top: 50%; transform: translateY(-50%); background: #ffffff; border: 1px solid #eaeaea; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-size: 18px; color: #333; z-index: 1000; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#007bff'; this.style.color='#ffffff';"
                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#333';"
                    >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Footer: Thumbnails at bottom -->
                <div id="modalThumbContainer" class="gallery-thumbnails">
                    @foreach ($images as $index => $image)
                        <div class="modal-thumb-item" data-index="{{ $index }}"
                             style="width: 90px; height: 60px; border-radius: 6px; overflow: hidden; cursor: pointer; border: 3px solid transparent; opacity: 0.6; transition: all 0.2s; flex-shrink: 0; box-sizing: border-box;"
                             onmouseover="this.style.opacity='1'"
                             onmouseout="if(!$(this).hasClass('active-thumb')) this.style.opacity='0.6'"
                        >
                            <img src="{{ asset($image) }}" 
                                 alt="Thumbnail {{ $index + 1 }}" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


@endsection