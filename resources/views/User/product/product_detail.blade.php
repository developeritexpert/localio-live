
@extends('user_layout.master')

@section('body_class', 'product-page-body')

@section('meta_title', isset($business->translations->first()->name) && isset($business->translations->first()->name) ?
    $business->translations->first()->name : 'Products')
@section('content')
    @php
        $images = is_array($business->business_images)
            ? $business->business_images
            : json_decode($business->business_images ?? '[]', true);

        if (empty($images) && !empty($business->screenshot_urls)) {
            $urls = is_array($business->screenshot_urls)
                ? $business->screenshot_urls
                : json_decode($business->screenshot_urls ?? '[]', true);
            $urls = array_values(array_filter((array)$urls));
            if (!empty($urls)) {
                $images = $urls;
            }
        }
    @endphp

    <script>
        console.log("Gallery script block rendered");

        let modalImages = {!! json_encode($images) !!};
        let currentModalIndex = 0;

        function updateModalImage(index) {
            console.log("updateModalImage called with index:", index);
            currentModalIndex = parseInt(index, 10);
            if (currentModalIndex >= 0 && currentModalIndex < modalImages.length) {
                let rawImg = modalImages[currentModalIndex];
                let imgUrl = (rawImg.startsWith('http://') || rawImg.startsWith('https://'))
                    ? rawImg
                    : '{{ asset("") }}' + rawImg.replace(/^\/+/, '');
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

        @if(auth()->check() && session()->has('pending_review_business_id'))
            @php
                $pendingBusId = session('pending_review_business_id');
                $pendingRec = session('pending_review_recommend');
                session()->forget(['pending_review_business_id', 'pending_review_recommend']);
            @endphp
            <script>
                (function() {
                    let attempts = 0;
                    function triggerPendingReviewModal() {
                        attempts++;
                        if (window.Livewire) {
                            if (typeof Livewire.dispatch === 'function') {
                                Livewire.dispatch('openReviewModal', { businessId: {{ $pendingBusId }}, recommend: {{ json_encode($pendingRec) }} });
                            } else if (typeof Livewire.emit === 'function') {
                                Livewire.emit('openReviewModal', {{ $pendingBusId }}, {{ json_encode($pendingRec) }});
                            }
                        }
                        if (attempts < 5) {
                            setTimeout(triggerPendingReviewModal, 500);
                        }
                    }
                    if (document.readyState === 'complete' || document.readyState === 'interactive') {
                        setTimeout(triggerPendingReviewModal, 300);
                    } else {
                        document.addEventListener('DOMContentLoaded', function() {
                            setTimeout(triggerPendingReviewModal, 300);
                        });
                    }
                })();
            </script>
        @endif
        <style>
            .rating-star i {
                font-size: 9px !important;
            }
            /* Sticky Right Sidebar Navigation */
            .revie_img_sec .image_revie_inr {
                display: flex !important;
                align-items: stretch !important;
                gap: 40px !important;
            }

            .revie_img_sec .thre_revi_rgt {
                position: relative !important;
                display: flex !important;
                flex-direction: column !important;
                width: 38% !important;
                height: auto !important;
                min-height: 100% !important;
                align-self: stretch !important;
            }

            .revie_img_sec .thre_revi_rgt .main_feture {
                width: 100% !important;
                height: 100% !important;
                display: flex !important;
                flex-direction: column !important;
            }

            .revie_img_sec .thre_revi_rgt .fetru_row {
                display: flex !important;
                flex-direction: column !important;
                gap: 20px !important;
                justify-content: flex-start !important;
                align-items: stretch !important;
                width: 100% !important;
                height: 100% !important;
            }

            .sticky-sidebar-nav-wrapper {
                position: -webkit-sticky !important;
                position: sticky !important;
                top: 10px !important;
                z-index: 30 !important;
                width: 100% !important;
                margin-top: 0 !important;
                align-self: flex-start !important;
            }

            .sticky-sidebar-nav-card {
                background: #ffffff;
                border-radius: 14px;
                box-shadow: 0 8px 24px rgba(0, 35, 71, 0.08);
                padding: 24px 20px;
                border: 1px solid #eef2f6;
                width: 100%;
                box-sizing: border-box;
            }

            .sticky-nav-links {
                display: flex;
                flex-direction: column;
                gap: 4px;
                margin: 12px 0;
            }

            .sticky-nav-item {
                display: flex;
                align-items: center;
                padding: 9px 14px;
                border-radius: 8px;
                color: #4a5568;
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s ease-in-out;
                position: relative;
            }

            .sticky-nav-item:hover {
                background-color: #f4f7fb;
                color: #003f7d;
                font-weight: 600;
                transform: translateX(4px);
            }

            .sticky-nav-item.active {
                background-color: #eaf1f9;
                color: #003f7d;
                font-weight: 700;
            }

            .sticky-nav-item.active::before {
                content: '';
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                height: 60%;
                width: 3.5px;
                background-color: #003f7d;
                border-radius: 0 3px 3px 0;
            }

          
            a.badge.rounded-pill.bg-light.text-dark.border.px-3.py-2.text-decoration-none:hover {
                background-color: #e4e7ea !important;
            }
            .sidebar-review-card .review-header div small {
                font-size: 12px !important;
                }
            .transparency-banner{
                position:  !important;
            }

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
                     margin-left: 57px !important;
                     margin-top: -4px !important;
                     color: #888 !important;
                 }
              }

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

            .asan-slider.asan-slider-btm.slider-nav {
                padding: 0;
            }

            .transparency-banner{
                position:relative !important;
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

               .review-breakdown-box{
                padding:22px;
            }

            .review-breakdown-box h2{
                margin-bottom:18px;
            }

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

                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                flex-shrink:0;
            }

            .greenfonticon{

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
                /* font: size 12px; */
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
                /* font-size:12px; */
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
                font-size:14px;
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
                font-size: 14px !important;
            }

            .main-view-rating-hide {
                display: none !important;
            }
            .fixed-div .main-view-rating-hide {
                display: flex !important;
                align-items: center;
                gap: 3px;
                margin-top: 4px;
            }
            .fixed-div .hide-on-sticky {
                display: none !important;
            }

            h5.card-title.mb-3 {
                font-size: 24px !important;
                font-weight: 600 !important;
                line-height: 1.3 !important;
                color: #002347 !important;
            }

            .asan-slider.asan-slider-btm .slick-track .hover_main ,
            .asan-slider.asan-slider-btm .slick-track .slick-slide:hover {
                border: 1px solid rgb(0, 0, 0);
                border-radius: 6px !important;
            }
            section#section-compare {
                padding: 40px 20px !important;
            }
            a.view-more-link:hover {
                text-decoration: underline !important;
            }



            .community-rating {
                font-size: 18px;
                font-weight: 600;
                color:#002347;
            }

            .community-base-rating {
                font-size: 12px;
                font-weight: 500;
            }

            .rating_bar_span {
                font-size: 16px;
                font-weight: 500;
                color: #002347;
            }
    </style>
    <div data-business-id="{{ $business->id }}">
        <section class="product_sec">
            <div class="inner_banner_sec">
                <div class="container-fluid" style="display: flex; justify-content: space-between;">
                    <div class="inner_banr_content">
                        <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @if ($business->category && $business->category->parent)
                                    @php
                                        $parentTranslation = $business->category->parent->translation()->first();
                                    @endphp
                                    @if ($parentTranslation)
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentTranslation->slug]) }}"
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
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $categoryTranslation->slug]) }}"
                                               style="color: inherit; transition: none;" onmouseover="this.style.color='#f26522'"
                                               onmouseout="this.style.color=''">
                                                {{ $categoryTranslation->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endif
                                <li class="breadcrumb-item active" aria-current="page">
                                    <span>{{ $business->translations->first()->name ?? '' }}</span>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="inside_sec_text">
                        <x-social-icon :business="$business" />
                    </div>
                </div>
            </div>

        </section>

        <section class="asn_main_sec asn_main_sec_2" id="section1">
            <div class="asn_dv">
                @php
                    $activeReviews = $business->reviews->where('status', 'active');
                    $ratingCount = $activeReviews->count();
                    $hasUserReviews = $ratingCount > 0;
                    if ($hasUserReviews) {
                        $averageRating = $activeReviews->avg('rating');
                    } elseif ($business->admin_rating !== null && (float)$business->admin_rating > 0) {
                        $averageRating = (float)$business->admin_rating;
                    } else {
                        $averageRating = null;
                    }
                @endphp
                <div class="container-fluid">
                    <div class="asn_dv_contnt ">
                        <div class="top-product-logo">
                            <x-business-logo :business="$business" />
                        </div>
                        <div class="div_prent_ever">

                            <div class="row frst_rw align-items-center">
                                <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="ans_lft">
                                        <div class="asn-rating">
                                            <div class="an_lkd">
                                                <h1 style="color: #000;" class="mb-0 p-1">
                                                    {{ $business->translations->first()->name }} <span class="hide-on-sticky">review {{ date('Y') }}</span> </h1>
                                            </div>
                                            <p class="text-muted size16  hide-on-sticky" style="color: #666; font-size: 16px;  margin-bottom: 0;">Real reviews, community discussions & alternatives</p>
                                            @if ($averageRating !== null)
                                                <div class="main-view-rating-hide">
                                                    <span style="font-size: 14px; font-weight: 500; color: #555;">
                                                        {{ number_format($averageRating, 1) }}
                                                    </span>
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
                                                    @if ($hasUserReviews)
                                                        <span style="font-size: 14px; font-weight: 500; color: #555;">
                                                            ({{ $ratingCount }})
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="ans_ryt">
                                        <div class="site_vsit">
                                            <ul class="list-unstyled">
                                            </ul>
                                            @if($business->is_affiliate)
                                            <div class="top-pro-btn tp_visit new-visit-anc">
                                                <a href="{{ $business->getTrackedUrl() }}"
                                                    data-track="{{ json_encode([
                                                        'type' => 'click',
                                                        // 'product_id' => $product->id,
                                                        'business_id' => $business->id,
                                                        'action' => 'visit_website',
                                                        'label' => 'Visit Website',
                                                    ]) }}"
                                                    class="btn-orng cta cta_orange d-flex align-items-center" target="_blank"
                                                    tabindex="0">
                                                    Visit website
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-left:6px;flex-shrink:0;"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
                                                </a>
                                            </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="frst_re_2 row">
                                <div class="tp-btm d-flex col-lg-6">

                                </div>

                                @php
                                    $ratingCount = $business->reviews->where('status', 'active')->count();
                                @endphp

                            </div>
                        </div>
                    </div>
                </div>

                 <div class="Tab-outerlnk container-fluid d-none">
                    <div class="inner_table2">

                        @php

                            $name = $business->translations->first()->name ?? 'This Business';

                            // Fixed dynamic top content
                            $staticBottomSections = [
                                ['id' => 'section9', 'label' => 'Alternatives'],
                            ];

                            if (isset($business->faqs) && $business->faqs->count() > 0) {
                                $staticBottomSections[] = ['id' => 'section15', 'label' => 'FAQs'];
                            }

                            $staticBottomSections = array_merge($staticBottomSections, [
                                ['id' => 'section-compare', 'label' => 'Compare'],
                                ['id' => 'section14', 'label' => "Reviews"],
                                ['id' => 'sectionDiscussions', 'label' => "Discussions"],
                            ]);

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
                    <!-- after this -->
                </div>

            </div>

        </section>

              <section class="revie_img_sec">
                   <div class="container">
                       <div class="image_revie_inr mt-3">
                            <div class="is-asana-wrp imges_left_sde" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="row sld_rw">
                                        <div class="col-lg-12">
                                            <div class="is-asana-lft">
                                                <h2>{{ $business->translations->first()->description_title ?: ('What is ' . ($business->translations->first()->name ?? '')) }}</h2>
                                                <div class="is_text">
                                                    {!! $business->translations->first()->description !!}
                                                </div>
                                            </div>
                                        </div>

                                        @if(isset($aggregatedPros) && isset($aggregatedCons) && count($aggregatedPros) > 0 && count($aggregatedCons) > 0)
                                        <div class="col-lg-12 mt-2 mb-4">
                                            <div class="pros-cons-header mb-3">
                                                <h2 style="font-weight: 600; color: #1e3050; font-size: 24px; margin-bottom: 8px;">
                                                    {{ $business->translations->first()->name ?? 'Business' }} pros and cons
                                                </h2>
                                                @if(!empty($business->pro_cons_intro))
                                                    <p class="" style="font-size: 15px; margin-bottom: 20px; line-height: 1.6;">
                                                        {!! $business->pro_cons_intro !!}
                                                    </p>
                                                @endif
                                            </div>

                                             <div class="row g-4">
                                                 <div class="col-md-6">
                                                     <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                                         <div class="card-body">
                                                             <h5 class="card-title mb-3" style="font-weight: 500 !important; font-size:20px !important;">Pros</h5>
                                                             <ul class="list-unstyled mb-0">
                                                                 @foreach($aggregatedPros as $pro)
                                                                 <li class="d-flex align-items-start mb-2">
                                                                     <span class="me-2" style="font-size: 18px; color: rgb(33, 172, 33) !important;"><i class="fas fa-plus-circle"></i></span>
                                                                     <span style="color: #202124;">{{ $pro->text }}
                                                                        <small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $pro->review_count }} {{ $pro->review_count == 1 ? 'review' : 'reviews' }}</small>
                                                                    </span>
                                                                 </li>
                                                                 @endforeach
                                                             </ul>
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <div class="card card-bordered h-100" style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                                                         <div class="card-body">
                                                             <h5 class="card-title mb-3" style="font-weight: 500 !important; font-size:20px !important;">Cons</h5>
                                                             <ul class="list-unstyled mb-0">
                                                                 @foreach($aggregatedCons as $con)
                                                                 <li class="d-flex align-items-start mb-2">
                                                                     <span class="me-2" style="font-size: 18px; color: rgb(247, 40, 60) !important;"><i class="fas fa-minus-circle"></i></span>
                                                                     <span style="color: #202124;">{{ $con->text }}
                                                                        <small class="badge font-weight-normal" style="background: #f7f9fb; color: #002347; font-weight: 500; font-size: 12px;">from {{ $con->review_count }} {{ $con->review_count == 1 ? 'review' : 'reviews' }}</small>
                                                                    </span>
                                                                 </li>
                                                                 @endforeach
                                                             </ul>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                             @if(!empty($business->pro_cons_summary))
                                             <div class="mt-4">
                                                 <div>{!! $business->pro_cons_summary !!}</div>
                                             </div>
                                             @endif
                                         </div>
                                         @endif

                                        @if($business->is_affiliate && $business->offerings->count() > 0)
                                        @php $offering = $business->offerings->first(); @endphp
                                        <div class="col-lg-12 mt-4 mb-2">
                                            <div class="offering-section ">
                                                @if($offering->headline)
                                                    <h2 class="mb-2" style="font-weight: 600; font-size:24px;">{{ $offering->headline }}</h2>
                                                @endif

                                                @if($offering->top_text)
                                                    <div class="mb-2">
                                                        {!! $offering->top_text !!}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        @php
                                            // Safely decode the business_images
                                            $images = is_array($business->business_images)
                                                ? $business->business_images
                                                : json_decode($business->business_images ?? '[]', true);

                                            // Fallback to screenshot_urls if no business_images are uploaded
                                            if (empty($images) && !empty($business->screenshot_urls)) {
                                                $urls = is_array($business->screenshot_urls)
                                                    ? $business->screenshot_urls
                                                    : json_decode($business->screenshot_urls ?? '[]', true);
                                                $urls = array_values(array_filter((array)$urls));
                                                if (!empty($urls)) {
                                                    $images = $urls;
                                                }
                                            }
                                        @endphp

                                         @if ($business->is_affiliate && !empty($images))
                                            <div class="col-lg-12 mb-4">
                                                <div class="is-asana-rgt">
                                                    <div class="row is-asan-slider">

                                                        <div class="col-md-12 asan-slider slider-for">
                                                            @foreach ($images as $index => $image)
                                                                @php
                                                                    $imgSrc = Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image);
                                                                @endphp
                                                                 <div class="asan-slider-inr">
                                                                     <a href="{{ $business->getTrackedUrl() }}"
                                                                        data-track="{{ json_encode([
                                                                            'type' => 'click',
                                                                            'business_id' => $business->id,
                                                                            'action' => 'visit_website',
                                                                            'label' => 'Visit Website',
                                                                        ]) }}"
                                                                        target="_blank" style="display: block;">
                                                                         <img src="{{ $imgSrc }}"
                                                                             alt="Business Image {{ $index + 1 }}"
                                                                             style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px;">
                                                                     </a>
                                                                 </div>
                                                            @endforeach
                                                        </div>

                                                        <div class="col-md-12 asan-slider asan-slider-btm slider-nav"
                                                            style="margin-top: 15px !important;">
                                                            @foreach ($images as $index => $image)
                                                                @php
                                                                    $imgSrc = Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image);
                                                                @endphp
                                                                <div style="padding: 0 5px; cursor: pointer;">
                                                                    <img src="{{ $imgSrc }}"
                                                                        alt="Thumbnail {{ $index + 1 }}"
                                                                        style="width: 150px; height: 100px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 2px solid transparent;">
                                                                </div>
                                                            @endforeach
                                                        </div>
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

                                         @if (!empty($business->translations->first()->after_image_description))
                                             <div class="col-lg-12 mt-2 after-image-desc">
                                                 <div class="is_text">
                                                     {!! $business->translations->first()->after_image_description !!}
                                                 </div>
                                             </div>
                                         @endif

                                    </div>

                                        @php
                                            $businessName = optional($business->translations->first())->name ?? 'Business';
                                            $langCode = app()->getLocale() ?: 'en';

                                            // Primary / secondary subcategory info
                                            $primarySubcat = $business->subCategories->first() ?? $business->category;
                                            $parentCat = $business->category?->parent ?? $business->category;
                                            $categorySlug = $parentCat?->translations?->slug ?? ($business->category?->translations?->slug ?? 'category');
                                            $subcategorySlug = $primarySubcat?->translations?->slug ?? 'all';

                                            // Helper closure to calculate rating average for a key/name
                                            $getRatingForCriterion = function($criterionName, $criterionKey = null) use ($criteria) {
                                                foreach ($criteria as $cr) {
                                                    if (($criterionKey && $cr->default_key === $criterionKey) || strtolower($cr->name) === strtolower($criterionName)) {
                                                        return $cr->average_rating;
                                                    }
                                                }
                                                return 0.0;
                                            };
                                        @endphp

                                        <div class="col-lg-12 mt-3">
                                            @php
                                                $effReviewCount = $ratingCount ?? ($business->reviews->where('status', 'active')->count() ?: 0);
                                            @endphp

                                            @php
                                                $featRating = $getRatingForCriterion('Features', 'features');
                                                $featRatingVal = $featRating > 0 ? $featRating : ($averageRating > 0 ? $averageRating : 4.0);
                                                $featPercent = min(100, max(0, ($featRatingVal / 5) * 100));
                                            @endphp
                                            <div class="rating-criteria-section mb-5 p-4 bg-white rounded shadow-sm border">
                                                <div class="mb-3">
                                                    <h2 class="m-0" style="font-weight: 600; font-size: 24px; color: #002347;">Features</h2>
                                                </div>

                                                @if(!empty($ratingTexts['features']['intro_text']))
                                                    <div class="mb-3">{!! $ratingTexts['features']['intro_text'] !!}</div>
                                                @endif

                                                <div class="community-rating-box p-3 mb-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                                    <div class="community-rating mb-2" style="">Community rating</div>
                                                    <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                                        <span class="rating_bar_span">Features</span>
                                                        <div class="progress" style="height: 8px; width: 140px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0;">
                                                            <div class="progress-bar" role="progressbar" style="width: {{ $featPercent }}%; background-color: #22c55e; border-radius: 10px;" aria-valuenow="{{ $featRatingVal }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                        </div>
                                                        <span class="rating_bar_span">{{ number_format($featRatingVal, 1) }}</span>
                                                    </div>
                                                    <div class="community-base-rating">Based on {{ $effReviewCount }} {{ $effReviewCount == 1 ? 'rating' : 'ratings' }}</div>
                                                </div>

                                                @if(!empty($ratingTexts['features']['end_text']))
                                                    <div class="mb-3">{!! $ratingTexts['features']['end_text'] !!}</div>
                                                @endif

                                                @if($business->features->count() > 0)
                                                    <div class="features-chips-container mt-3">
                                                        <div class="fw-bold mb-2" style="font-size: 14px; color: #1e3050;">Key features</div>
                                                        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                                            @foreach($business->features as $feat)
                                                                @php
                                                                    $featName = optional($feat->translations->first())->name ?? $feat->name;
                                                                    $featSlug = Str::slug($featName);
                                                                    $featUrl = url("/{$langCode}/{$categorySlug}/{$subcategorySlug}/{$featSlug}");
                                                                @endphp
                                                                <a href="{{ $featUrl }}" class="badge rounded-pill bg-light text-dark border px-3 py-2 text-decoration-none" style="font-weight: 500; font-size: 13px; transition: all 0.2s;">
                                                                    {{ $featName }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            @php
                                                $easeRating = $getRatingForCriterion('Ease of use', 'ease_of_use');
                                                $easeRatingVal = $easeRating > 0 ? $easeRating : ($averageRating > 0 ? $averageRating : 4.2);
                                                $easePercent = min(100, max(0, ($easeRatingVal / 5) * 100));
                                            @endphp
                                            <div class="rating-criteria-section mb-5 p-4 bg-white rounded shadow-sm border">
                                                <div class="mb-3">
                                                    <h2 class="m-0" style="font-weight: 600; font-size: 24px; color: #002347;">Ease of use</h2>
                                                </div>

                                                @if(!empty($ratingTexts['ease_of_use']['intro_text']))
                                                    <div class="mb-3">{!! $ratingTexts['ease_of_use']['intro_text'] !!}</div>
                                                @endif

                                                <div class="community-rating-box p-3 mb-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                                    <div class="community-rating mb-2">Community rating</div>
                                                    <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                                        <span class="rating_bar_span">ease of use</span>
                                                        <div class="progress" style="height: 8px; width: 140px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0;">
                                                            <div class="progress-bar" role="progressbar" style="width: {{ $easePercent }}%; background-color: #22c55e; border-radius: 10px;" aria-valuenow="{{ $easeRatingVal }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                        </div>
                                                        <span class="rating_bar_span">{{ number_format($easeRatingVal, 1) }}</span>

                                                    </div>
                                                    <div class="community-base-rating">Based on {{ $effReviewCount }} {{ $effReviewCount == 1 ? 'rating' : 'ratings' }}</div>
                                                </div>

                                                @if(!empty($ratingTexts['ease_of_use']['end_text']))
                                                    <div class="mb-3">{!! $ratingTexts['ease_of_use']['end_text'] !!}</div>
                                                @endif
                                            </div>

                                            @foreach($criteria as $cr)
                                                @if(!in_array($cr->default_key, ['features', 'ease_of_use', 'value_for_money']) && !in_array(strtolower($cr->name), ['features', 'ease of use', 'value for money']))
                                                    @php
                                                        $crKey = (string)$cr->id;
                                                        $crRating = $cr->average_rating;
                                                        $crRatingVal = $crRating > 0 ? $crRating : 4.0;
                                                        $crPercent = min(100, max(0, ($crRatingVal / 5) * 100));
                                                        $crDisplayName = ucfirst($cr->name);
                                                    @endphp
                                                    <div class="rating-criteria-section mb-5 p-4 bg-white rounded shadow-sm border">
                                                        <div class="mb-3">
                                                            <h2 class="m-0" style="font-weight: 600; font-size: 24px; color: #002347;">{{ $crDisplayName }}</h2>
                                                        </div>

                                                        @if(!empty($ratingTexts[$crKey]['intro_text']))
                                                            <div class="mb-3">{!! $ratingTexts[$crKey]['intro_text'] !!}</div>
                                                        @endif

                                                        <div class="community-rating-box p-3 mb-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0; ">
                                                            <div class="community-rating mb-2">Community rating</div>
                                                            <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                                                <span class="rating_bar_span">service management</span>
                                                                <div class="progress" style="height: 8px; width: 140px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0;">
                                                                    <div class="progress-bar" role="progressbar" style="width: {{ $crPercent }}%; background-color: #22c55e; border-radius: 10px;" aria-valuenow="{{ $crRatingVal }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                </div>
                                                                <span class="rating_bar_span">{{ number_format($crRatingVal, 1) }}</span>

                                                            </div>
                                                            <div class="community-base-rating">Based on {{ $effReviewCount }} {{ $effReviewCount == 1 ? 'rating' : 'ratings' }}</div>
                                                        </div>

                                                        @if(!empty($ratingTexts[$crKey]['end_text']))
                                                            <div class="mb-3">{!! $ratingTexts[$crKey]['end_text'] !!}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach

                                            @php
                                                $vfmRating = $getRatingForCriterion('Value for money', 'value_for_money');
                                                $vfmRatingVal = $vfmRating > 0 ? $vfmRating : ($averageRating > 0 ? $averageRating : 4.1);
                                                $vfmPercent = min(100, max(0, ($vfmRatingVal / 5) * 100));
                                            @endphp
                                            <div class="rating-criteria-section mb-5 p-4 bg-white rounded shadow-sm border">
                                                <div class="mb-3">
                                                    <h2 class="m-0" style="font-weight: 600; font-size: 24px; color: #002347;">Value for money</h2>
                                                </div>

                                                @if(!empty($ratingTexts['value_for_money']['intro_text']))
                                                    <div class="mb-3">{!! $ratingTexts['value_for_money']['intro_text'] !!}</div>
                                                @endif

                                                <div class="community-rating-box p-3 mb-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                                    <div class="community-rating mb-2">Community rating</div>
                                                    <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                                        <span class="rating_bar_span">value for money</span>
                                                        <div class="progress" style="height: 8px; width: 140px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 0;">
                                                            <div class="progress-bar" role="progressbar" style="width: {{ $vfmPercent }}%; background-color: #22c55e; border-radius: 10px;" aria-valuenow="{{ $vfmRatingVal }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                        </div>
                                                        <span class="rating_bar_span">{{ number_format($vfmRatingVal, 1) }}</span>

                                                    </div>
                                                    <div class="community-base-rating">Based on {{ $effReviewCount }} {{ $effReviewCount == 1 ? 'rating' : 'ratings' }}</div>
                                                </div>

                                                @if(!empty($ratingTexts['value_for_money']['end_text']))
                                                    <div class="mb-3">{!! $ratingTexts['value_for_money']['end_text'] !!}</div>
                                                @endif
                                            </div>
                                        </div>
                                    <!-- all new part here -->
                                             <div class="con_table pb-0 all_sec_wrp" id="section2">
                                                <div class="">

                                                    <div class=" ">
                                                        <div class="">
                                                            <div class="inner_table_1">
                                                                <section class="is-asana light">

                                                                </section>

                                                                <section class="reviews-section">

                                                                </section>

                                                                @php
                                                                    $currentLangId = $lang_id ?? getCurrentLanguageID();
                                                                    $prosList = collect();
                                                                    $consList = collect();

                                                                    foreach ($business->reviews as $review) {
                                                                        $translation = $review->translations->firstWhere('language_id', $currentLangId);
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

                                                                                <div class="mb-3">
                                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                                        <span class="popup-star fs-4 mx-1" data-value="{{ $i }}" style="cursor:pointer; color:#ccc;">&#9733;</span>
                                                                                    @endfor
                                                                                </div>

                                                                                <textarea id="popupComment" class="form-control mb-3" rows="3" placeholder="Write a review (optional)"></textarea>

                                                                                <button id="submitPopupReview" class="btn btn-primary w-100">Submit Review</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <section class="choice-business d-none" id="softweretopic">

                                                            </section>

                                                                    @php
                                                                    // Decode items safely
                                                                    $items = [];
                                                                    if (!empty($business->integration?->items)) {
                                                                        $items = is_array($business->integration->items) ? $business->integration->items : json_decode($business->integration->items, true);
                                                                    }

                                                                @endphp

                                                                <section class="software-like p_50 product_integra_sec " id="section9">
                                                                <div class="container">
                                                                    <div class="sftwre-like-innr">
                                                                        <div class="sftwre-asana-hd text-center" data-aos="fade-up" data-aos-duration="1000">

                                                                            <h2>{{ $business->translations->first()->name }} Alternatives & Competitors</h2>
                                                                            <p>Based on other buyer's searches, these are the products that could be a good fit
                                                                                for you.
                                                                            </p>
                                                                        </div>
                                                                        <div class="sft_ware_test"
                                                                            style="display: flex; justify-content:center; align-items: center;">

                                                                            <div class="sftware-alternative d-flex" data-aos="fade-up"
                                                                                data-aos-duration="1000">
                                                                                @foreach ($alternativeBusiness as $altbusiness)
                                                                                    @if ($altbusiness->id == $business->id)
                                                                                        @continue
                                                                                    @endif
                                                                                    <div class="sftware-alternative-pck" data-aos="fade-up"
                                                                                        data-aos-duration="1000"
                                                                                        onclick="if(!event.target.closest('a')) { window.location.href = '{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $altbusiness->translations->first()->slug]) }}'; }"
                                                                                        style="cursor: pointer; padding: 25px 20px;">
                                                                                        @php
                                                                                            $altStartingPrice = 'N/A';
                                                                                            $altCurrency = '$';
                                                                                            $altAdditionalInfo = 'NA';
                                                                                            $altPrice = getBusinessesWithStartingPrice($altbusiness);
                                                                                            if (!empty($altPrice) && isset($altPrice[0]['starting_price'])) {
                                                                                                $altBusinessPrice = $altPrice[0]['starting_price'];
                                                                                                $altStartingPrice = $altBusinessPrice['amount'] ?? 'N/A';
                                                                                                $altCurrency = $altBusinessPrice['currency'] ?? '$';
                                                                                                $altTimeUnit = ucfirst($altBusinessPrice['time_unit'] ?? 'month');
                                                                                                $altAdditionalInfo =
                                                                                                    $altBusinessPrice['additional_info'] ?? 'NA';
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
                                                                                            <div class="top-product-logo">
                                                                                                <x-business-logo :business="$altbusiness" />
                                                                                            </div>
                                                                                            <div class="asn-rating">
                                                                                                @if ($altbusiness->translations->isNotEmpty())
                                                                                                    <h6 class="m-0 fw_700">
                                                                                                        {{ $altbusiness->translations->first()->name }}
                                                                                                    </h6>
                                                                                                @else
                                                                                                    <h6 class="m-0 fw_700">Name not available</h6>
                                                                                                @endif
                                                                                                <div class="rating-group" >
                                                                                                    <span class="rate_box_num fw-medium" style="">{{ number_format($altRatingAvg, 1) }}</span>
                                                                                                    <div class="rating-stars" style="">
                                                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                                                            @if ($i <= floor($altRatingAvg))
                                                                                                                <i class="fas fa-star text-warning"></i>
                                                                                                            @elseif ($i - 0.5 <= $altRatingAvg)
                                                                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                                                                            @else
                                                                                                                <i class="far fa-star text-warning"></i>
                                                                                                            @endif
                                                                                                        @endfor
                                                                                                    </div>
                                                                                                    <span class="rate_box_text" style="">
                                                                                                        ({{ $count }})
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="over-rate-progress p_top_btm_sftwre pt-3 pb-3" style="border-bottom: 1px solid #eee;">
                                                                                            <h6 class="fw_700 mb-3" style="color: #002347; font-size:12px;">Review breakdown</h6>
                                                                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                                                <p class="m-0" style="font-size: 12px; color: #555;">Ease of Use</p>
                                                                                                <div class="prgs_br d-flex align-items-center">
                                                                                                    <progress class="progress-bar"
                                                                                                        value="{{ ($altEaseOfUseAvg ?? 0) * 20 }}"
                                                                                                        max="100">
                                                                                                    </progress>
                                                                                                    <span style="font-size: 12px; font-weight: 600; color: #333;  min-width: 32px; text-align: right;">{{ $altEaseOfUseAvg ?? 0 }} </span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                                                <p class="m-0" style="font-size: 12px; color: #555;">Customer Service</p>
                                                                                                <div class="prgs_br d-flex align-items-center">
                                                                                                    <progress class="progress-bar"
                                                                                                        value="{{ ($altCustomerServiceAvg ?? 0) * 20 }}"
                                                                                                        max="100">
                                                                                                    </progress>
                                                                                                    <span style="font-size: 12px; font-weight: 600; color: #333;  min-width: 32px; text-align: right;">{{ $altCustomerServiceAvg ?? 0 }} </span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                                                <p class="m-0" style="font-size: 12px; color: #555;">Features</p>
                                                                                                <div class="prgs_br d-flex align-items-center">
                                                                                                    <progress class="progress-bar"
                                                                                                        value="{{ ($altExclusiveFeatureAvg ?? 0) * 20 }}"
                                                                                                        max="100">
                                                                                                    </progress>
                                                                                                    <span style="font-size: 12px; font-weight: 600; color: #333; min-width: 32px; text-align: right;">{{ $altExclusiveFeatureAvg ?? 0 }} </span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                                                                <p class="m-0" style="font-size: 12px; color: #555;">Value for Money</p>
                                                                                                <div class="prgs_br d-flex align-items-center">
                                                                                                    <progress class="progress-bar"
                                                                                                        value="{{ ($altValueForMoneyAvg ?? 0) * 20 }}"
                                                                                                        max="100">
                                                                                                    </progress>
                                                                                                    <span style="font-size: 12px; font-weight: 600; color: #333;  min-width: 32px; text-align: right;">{{ $altValueForMoneyAvg ?? 0 }} </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="start-from p_top_btm_sftwre pt-3 pb-3">
                                                                                            <h6 style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 14px;">Starting price</h6>
                                                                                            <h3 class="m-0 mt-1" style="font-weight: 700; color: #333; font-size: 24px; line-height:1!important; ">
                                                                                                <span>{{ $altCurrency }}{{ $altStartingPrice }}</span>
                                                                                            </h3>
                                                                                            <small class="text-muted" style="font-size: 12px;">{{ $altAdditionalInfo }}</small>
                                                                                        </div>

                                                                                        <div class="sftwre-alt-btn pt-2">
                                                                                            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $altbusiness->translations->first()->slug]) }}"
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

                                                                    <div class="text-center mt-4">
                                                                        @php
                                                                            $languageObj = \App\Models\Language::where('lang_code', app()->getLocale())->first();
                                                                            $expectedAlternativesSlug = !empty($languageObj->alternatives_slug) ? $languageObj->alternatives_slug : 'alternatives';
                                                                        @endphp
                                                                        <a href="{{ route('business.alternatives', ['locale' => app()->getLocale(), 'business_slug' => $business->translations->first()->slug ?? $business->slug ?? '', 'alternatives_slug' => $expectedAlternativesSlug]) }}"
                                                                        class="view-more-link"
                                                                        style="font-size: 14px; font-weight: 600; color: #002347; text-decoration: none;">
                                                                            View more alternatives
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                </section>

                                                                @if(isset($business->faqs) && $business->faqs->count() > 0)
                                                                <section class="faq-section  faq-section_1 product_inr_faq p_50 pt-2 light" id="section15" style="background-color:#fdfdfd;">
                                                                    <div class="container">
                                                                        <div class="faq-inner">
                                                                            <div class="row">
                                                                                <div class="">
                                                                                    <div class="d-flex flex-column w-auto">

                                                                                        @php
                                                                                            $faq_title = \App\Models\StaticContentKey::where('key', 'faq_title')->first();
                                                                                            $faq_description = \App\Models\StaticContentKey::where('key', 'faq_description')->first();
                                                                                        @endphp

                                                                                        <h2>{{ $faq_title?->default_value ?? '' }}</h2>
                                                                                        <p>{{ $faq_description?->default_value ?? '' }}</p>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="">
                                                                                    <div class="faq-accor">
                                                                                        <div class="accordion" id="accordionExample">
                                                                                            @forelse ($business->faqs->take(10) as $index => $faq)
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

                                                                                        @php
                                                                                            $currentLangObj = \App\Models\Language::where('lang_code', app()->getLocale())->first();
                                                                                            $faqSlugVal = !empty($currentLangObj->faq_slug) ? $currentLangObj->faq_slug : 'faqs';
                                                                                        @endphp
                                                                                        <div class="mt-4 text-center">
                                                                                            <a href="{{ route('business.all_faqs', ['locale' => app()->getLocale(), 'business_slug' => $business->translations->first()->slug, 'faq_slug' => $faqSlugVal]) }}"
                                                                                            class="view-more-link"
                                                                                            style="font-size: 14px; font-weight: 600; color: #002347; text-decoration: none;">
                                                                                                View more FAQs
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </section>
                                                                @endif

                                                                @php
                                                                    $bName = $business->translations->first()->name ?? 'Business';
                                                                    $catTrans = $business->category->translation ?? null;
                                                                    $catName = $catTrans->name ?? 'providers';
                                                                    $compSlug = $catTrans->comparison_slug ?? 'compare';
                                                                @endphp
                                                                <section class="compare-section p_50 light" id="section-compare" style="background-color: #f7f9fb !important;">
                                                                    <div class="container">
                                                                        <div class="hd_text mb-4" data-aos="fade-up" data-aos-duration="1000">
                                                                            <h2 style="font-size: 26px; font-weight: 700; color: #1e3050; margin-bottom: 8px;">
                                                                                Compare {{ $bName }}
                                                                            </h2>
                                                                            <p style="font-size: 15px; color: #64748b; margin: 0;">
                                                                                See how {{ $bName }} compares to other {{ $catName }} providers.
                                                                            </p>
                                                                        </div>

                                                                        <div class="row g-3" data-aos="fade-up" data-aos-duration="1000">
                                                                            @forelse($peerComparisons as $peer)
                                                                                @php
                                                                                    $peerName = $peer->translations->first()->name ?? 'Business';
                                                                                    $peerRating = $peer->average_rating ?? 0;
                                                                                    $vsKey = static_text('vs_keyword');
                                                                                    if (empty($vsKey) || $vsKey === 'vs_keyword') {
                                                                                        $vsKey = 'vs';
                                                                                    }
                                                                                    $vsKey = Str::slug($vsKey);
                                                                                    $seoUrl = route('product-comparison.seo', [
                                                                                        'locale' => app()->getLocale(),
                                                                                        'comparison_slug' => $compSlug,
                                                                                        'comparison_businesses' => Str::slug($bName) . '-' . $vsKey . '-' . Str::slug($peerName)
                                                                                    ]);
                                                                                @endphp
                                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                                    <div class="comparison-box p-3 rounded-3 border h-100 d-flex flex-column justify-content-between" style="background-color: #f8fafc !important; border-radius: 12px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
                                                                                        <div class="d-flex align-items-center justify-content-between mb-3">

                                                                                            <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                                                                                <div class="top-product-medium-logo">
                                                                                                    <x-business-logo :business="$business" :name="$bName" />
                                                                                                </div>

                                                                                                <div style="min-width: 0;">
                                                                                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 13px; color: #1e293b !important;">{{ $bName }}</div>
                                                                                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                                                                                        <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                                                                                        <span>{{ number_format($averageRating, 1) }}</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="px-2 vs_circle text-muted flex-shrink-0" style="font-size: 16px; font-family: sans-serif;">vs</div>

                                                                                            <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                                                                                <div class="top-product-medium-logo">
                                                                                                <x-business-logo :business="$peer" :name="$peerName" />
                                                                                            </div>
                                                                                                <div style="min-width: 0;">
                                                                                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 13px; color: #1e293b !important;">{{ $peerName }}</div>
                                                                                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                                                                                        <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                                                                                        <span>{{ number_format($peerRating, 1) }}</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="cmpre_btn w-100 mt-auto">
                                                                                            <a href="{{ $seoUrl }}" class="cta cta_btn text-decoration-none w-100" style="padding: 8px 20px !important; border-radius: 50px !important; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: center; width: 100%;">
                                                                                                Compare
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @empty
                                                                                <div class="col-12 text-muted p-0">No comparisons available for this business yet.</div>
                                                                            @endforelse
                                                                        </div>

                                                                        @if(count($peerComparisons) > 0)
                                                                            @php
                                                                                $cCompSlug = !empty($languageObj->comparisons_slug) ? $languageObj->comparisons_slug : 'comparisons';
                                                                                $bSlugStr = $business->translations->first()->slug ?? $business->slug;
                                                                            @endphp
                                                                            <div class="mt-4" data-aos="fade-up" data-aos-duration="1000">
                                                                                <a href="{{ url(app()->getLocale() . '/' . $bSlugStr . '/' . $cCompSlug) }}" class="view-more-link" style="font-size: 14px; font-weight: 600; color: #002347; text-decoration: none;">
                                                                                    View more comparisons
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </section>

                                                                <section class="about_asn_2 light pt-0 p_50 d-none">

                                                                    <div class="about_asn_content">
                                                                        <div class="hd_content asan-text-para">
                                                                            {!! $product->translations->overview ?? "" !!}
                                                                        </div>

                                                                    </div>

                                                                </section>

                                                                <section class="crm_sec revie_left_rgt_sec" id="section14" style="overflow: visible !important; background-color:#fdfdfd;">
                                                                    <div class="container">
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

                                                                            }
                                                                            .review_sec .review_detl:hover {
                                                                                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07) !important;
                                                                                border-color: rgba(0, 0, 0, 0.12) !important;
                                                                            }

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

                                                                            <div class="review-prompt-banner" id="reviewPromptBanner" style="background-color: #f7fafc; border-radius: 12px; padding: 20px 24px; margin-bottom: 40px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #e2e8f0; flex-wrap: wrap; gap: 20px;">
                                                                                <div style="display: flex; align-items: center; gap: 16px;">
                                                                                    <div class="top-product-logo" style="width: 52px; height: 52px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0; overflow: hidden;">
                                                                                        <x-business-logo :business="$business" />
                                                                                    </div>
                                                                                    <div>
                                                                                        <h4 style="margin: 0 0 4px 0; font-size: 17px !important; font-weight: 700 !important; color: #1e3050 !important;">Would you recommend {{ $business->translations->first()->name ?? 'this product' }} to others?</h4>
                                                                                        <p style="margin: 0; font-size: 13.5px; color: #4a5568;">Share your experience with the community</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div style="display: flex; gap: 12px; align-items: center;">
                                                                                    @auth
                                                                                        <button onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true }); document.getElementById('reviewPromptBanner').style.display = 'none';" style="padding: 8px 26px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                                                                                            <i class="fas fa-thumbs-up"></i> 
                                                                                            
                                                                                        </button>
                                                                                        <button onclick="document.getElementById('reviewPromptBanner').style.display = 'none';" style="padding: 8px 26px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                                                                                            <i class="fas fa-thumbs-down"></i> 
                                                                                        </button>
                                                                                    @else
                                                                                        <button onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true }); document.getElementById('reviewPromptBanner').style.display = 'none';" style="padding: 8px 26px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                                                                                            <i class="fas fa-thumbs-up"></i> 
                                                                                        </button>
                                                                                        <button onclick="document.getElementById('reviewPromptBanner').style.display = 'none';" style="padding: 8px 26px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                                                                                            <i class="fas fa-thumbs-down"></i> 
                                                                                        </button>
                                                                                    @endauth
                                                                                </div>
                                                                            </div>

                                                                            <div class="row review-row-prod-inr">

                                                                                <div class="col-lg-12">
                                                                                    <div class="review-sidebar-sticky">

                                                                                        <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; color: #1e3050; line-height: 1.3;">
                                                                                            User reviews
                                                                                        </h2>
                                                                                        <div class="review-filter-sec">
                                                                                            <div class="user-reviews-summary-card p-4 bg-white rounded-3 border mb-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">

                                                                                                <div class="d-flex flex-column align-items-start mb-3">
                                                                                                    <span style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1; margin-bottom: 6px;">{{ number_format($averageRating, 1) }}</span>
                                                                                                    <div class="d-flex align-items-center gap-1 mb-1">
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
                                                                                                    <span style="font-size: 12px; color: #666;">{{ number_format($ratingCount) }} {{ $ratingCount == 1 ? 'review' : 'reviews' }}</span>
                                                                                                </div>

                                                                                                <h5 style="font-size: 14px; font-weight: 600; color: #002347; margin-top: 18px; margin-bottom: 14px;">Review breakdown</h5>

                                                                                                <div class="mb-3">
                                                                                                    @foreach ($criteria as $criterion)
                                                                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                                                                        <span style="font-size: 13px; font-weight: 500; color: #334155; white-space: nowrap;">{{ $criterion->name }}</span>
                                                                                                        <div class="d-flex align-items-center ms-2" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                                                                                            <progress class="progress-bar w-100" value="{{ $criterion->average_rating * 20 }}" max="100" style="height: 8px; border-radius: 4px;"></progress>
                                                                                                            <span style="font-size: 12px; font-weight: 600; color: #334155; margin-left: 8px; min-width: 32px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    @endforeach
                                                                                                </div>

                                                                                                <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="border-top-color: #f1f5f9 !important;">
                                                                                                    <span style="font-weight: 600; color: #002347; font-size: 14px;">Recommended by users</span>
                                                                                                    <strong style="color: #002347; font-size: 14px; font-weight:600">{{ $recommendPercent }}%</strong>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="wrap-review-filter">

                                                                                                <div class="filter-by-title-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 6px;">
                                                                                                    <span style="font-size: 15px; font-weight: 600; color: #002655;">Filter by rating</span>
                                                                                                    <span class="clear-filters-btn" id="clear-filters" style="display: none; color: #007bff; font-size: 13px; cursor: pointer;">Clear filter</span>
                                                                                                </div>

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
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-lg-12">

                                                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;  margin-top: 25px;">
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
                                                                                                onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                                                                            @endauth
                                                                                            style="cursor: pointer; font-size: 15px; font-weight: 600; color: #06498b; text-decoration: none;"
                                                                                        ><i class="fas fa-pencil-alt me-1"></i>Write review</a>
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


                                                                <!-- this is end section -->
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div id="sectionDiscussions">

                                                </div>
                                                <div class="mt-5">
                                                    <section class="subs_sec light p_50 d-none" id="section16">
                                                        <x-news-letter-subscription />
                                                    </section>
                                                </div>
                                            </div>
                                    <!-- all new part section end here  -->
                            </div>

                            <div class="thre_revi_rgt">

                                @php
                                    $productBadgeLabel = static_text('product_badge_label') ?? 'Key Features';
                                    // dd($productBadgeLabel);
                                @endphp

                                <div class="main_feture">
                                    <div class="fetru_row d-flex justify-content-between">

                                        @if ($business->is_affiliate && $business->usps->count() > 0)

                                            <div class="main_feature_lg">
                                                <div class="feture_box lft_check_box size15">
                                                    <ul class="list-unstyled">
                                                        @foreach ($business->usps->take(5) as $usp)
                                                            <li class="d-flex flex-row align-items-center size15">
                                                                <div class="grn_chk">
                                                                    <img src="{{ asset('front/img/green-tick.svg') }}">
                                                                </div>
                                                                <p class="m-0">{{ $usp->text }}</p>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @elseif ($business->is_affiliate)

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

                                       <div class="main_feature_lg">
                                            <div class="feture_box review-breakdown-card">

                                                @if ($averageRating !== null)
                                                    <div class="review-header-box top_review_bx" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom:15px;">
                                                        <div class="overall-rating-box" style="display: flex; flex-direction: column; align-items: flex-start;">
                                                            <span class="overall-rating-number" style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1;">
                                                                {{ number_format($averageRating,1) }}
                                                            </span>

                                                            <div class="" style="margin-top: 10px; margin-bottom: 6px; display: flex; gap: 4px;">
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

                                                            @if ($hasUserReviews)
                                                                <span class="f-12" style="color: #666;">Community rating · {{ number_format($totalReviews) }} {{ $totalReviews == 1 ? 'review' : 'reviews' }}</span>
                                                            @endif
                                                        </div>

                                                        @if ($hasUserReviews)
                                                            <a href="#section14" class="view-review-link" style="color: #06498b; font-weight: 600; font-size: 14px; text-decoration: none; padding-top: 5px;">
                                                                View all reviews
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if ($hasUserReviews)
                                                    <h2 class="breakdown-title" style="margin-bottom: 15px;">
                                                        Review breakdown
                                                    </h2>

                                                    <div class="review-progress-list ">
                                                        @foreach ($criteria as $criterion)
                                                        <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                                            <p class="m-0" style="font-size: 12px; font-weight: 500; color: #444;">{{ $criterion->name }}</p>
                                                            <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                                                <progress class="progress-bar w-100"
                                                                    value="{{ $criterion->average_rating * 20 }}"
                                                                    max="100" style="height: 8px;"></progress>
                                                                <span style="font-size: 12px; font-weight: 600; color: #333;  min-width: 35px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}</span>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="recommendation-rate mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                                        <span style="font-weight: 600; color: #002347; font-size: 14px;">Recommended by users</span>
                                                        <strong style="color: #002347; font-size: 14px; font-weight:600;">{{ $recommendPercent }}%</strong>
                                                    </div>
                                                @endif

                                                <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                                    <span style="font-weight: 600; color: #1e3050; font-size: 14px;">Do you recommend {{ $business->translations->first()->name ?? 'this business' }}?</span>
                                                    <div style="display: flex; gap: 8px;">
                                                        @auth
                                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; " onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; " onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                                                <i class="fas fa-thumbs-down"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; " onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 30px; height: 30px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; " onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                                                <i class="fas fa-thumbs-down"></i>
                                                            </a>
                                                        @endauth
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        @php
                                            $hasFreeTrial = false;
                                            if ($business->relationLoaded('products')) {
                                                $hasFreeTrial = $business->products->flatMap->pricingOptions->contains('slug', 'free-trial');
                                            }
                                            if (!$hasFreeTrial && $business->pricingOptions) {
                                                $hasFreeTrial = $business->pricingOptions->contains('slug', 'free-trial');
                                            }
                                            $hasStartingPrice = !empty($startingPrice) && is_numeric($startingPrice) && $startingPrice > 0;
                                        @endphp

                                        @if($business->is_affiliate && ($hasStartingPrice || $hasFreeTrial))
                                        <div class="innr_price_trail">

                                            @if($hasStartingPrice)
                                            <div class="main_feature_sm">
                                                <div class="feture_box str_prc_box">

                                                    <h6 class="starting-price-title">
                                                        Starting price
                                                    </h6>

                                                    <h2 class="starting-price-value">
                                                        {{ $currency }}{{ $startingPrice }}
                                                    </h2>

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
                                            @endif

                                            @if($hasFreeTrial)
                                            <div class="main_feature_sm">
                                                <div class="fre_trail feture_box size22">
                                                    <div class="grn_check_big">
                                                        <img src="{{ asset('front/img/new-grn-chk.svg') }}">
                                                    </div>
                                                    <h6 class="blue-text big-bld">Free trial
                                                        available
                                                    </h6>
                                                    <div class="accor-btn">
                                                        <a class="cta cta_white blue_t_org_btn"
                                                            data-track="{{ json_encode([
                                                                'type' => 'click',
                                                                'business_id' => $business->id,
                                                                'action' => 'claim_now',
                                                                'label' => 'Claim Now',
                                                            ]) }}"
                                                            type="button" style="text-transform:none !important;">Claim now</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
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
                                                                        <h6 style="margin: 0; font-size: 14px; font-weight: 600; color: #1e3050;">{{ $review->user ? $review->user->displayName() : 'Anonymous' }}</h6>
                                                                        @if($review->user && $review->user->job_title)
                                                                            <div style="font-size: 12px; color: #777; margin-top: 2px; line-height: 1.2;">{{ $review->user->job_title }}</div>
                                                                        @endif
                                                                    </div>
                                                              </div>

                                                              <div style="text-align: right; flex-shrink: 0;">
                                                                  <small class="text-muted" style="font-size: 11px; white-space: nowrap;">{{ $review->created_at->diffForHumans() }}</small>
                                                              </div>

                                                         </div>

                                                        <h5 style="margin-top: 10px; margin-bottom: 4px; font-size: 15px; font-weight: 600; color: #1e3050;">
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
                                        </div>
                                        <div class="main_feature_lg">
                                            <div class="feture_box review-breakdown-box">
                                                <div class="review-header-box pb-3" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                                                    <h2 class="size22 big-bld m-0">Recent discussions</h2>
                                                    <a href="#sectionDiscussions" class="view-review-link">
                                                        View all discussions
                                                    </a>
                                                </div>

                                                <div class="sidebar-review-card" style="margin-bottom: 20px;">
                                                    <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                                        <div class="review-user" style="display: flex; align-items: center; gap: 12px;">
                                                            <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                                <span style="color: white; font-weight: bold; font-size: 20px;">M</span>
                                                            </div>
                                                            <div>
                                                                <h6 style="margin: 0; font-size: 14px; font-weight: 600; color: #1e3050;">Marc L.</h6>
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
                                        </div>
                                        
                                    
                                        <!-- here we start sticky part -->
                                        <div class="main_feature_lg sticky-sidebar-nav-wrapper">
                                            <div class="feture_box review-breakdown-box sticky-sidebar-nav-card" id="stickySidebarNav">
                                                <div class="sticky-nav-header text-center">
                                                    <div class="sticky-nav-logo mb-2 d-flex justify-content-center">
                                                        <div style="width: 44px; height: 44px;">
                                                            <x-business-logo :business="$business" />
                                                        </div>
                                                    </div>
                                                    <h3 class="sticky-nav-title mb-1"
                                                        style="font-size: 17px; font-weight: 700; color: #002347;">
                                                        {{ $business->translations->first()->name ?? '' }}
                                                    </h3>
                                                    @if ($averageRating !== null)
                                                        <div
                                                            class="rating-group sticky-nav-rating d-flex align-items-center justify-content-center gap-1 mb-3">
                                                            <span
                                                                style="">{{ number_format($averageRating, 1) }}</span>
                                                            <div class="rating-star" style="">
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
                                                            @if ($hasUserReviews)
                                                                <span class=""
                                                                    style="">({{ $ratingCount }})</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if ($business->is_affiliate)
                                                        <a href="{{ $business->getTrackedUrl() }}"
                                                            data-track="{{ json_encode([
                                                                'type' => 'click',
                                                                'business_id' => $business->id,
                                                                'action' => 'visit_website',
                                                                'label' => 'Visit Website',
                                                            ]) }}"
                                                            target="_blank" class="btn-orng cta cta_orange w-100 mb-3"
                                                            style="border-radius: 30px; font-size: 14px; font-weight: 600; padding: 9px 18px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                                            Visit website <i class="fas fa-external-link-alt ms-2"
                                                                style="font-size: 11px;"></i>
                                                        </a>
                                                    @endif
                                                </div>


                                                <div class="sticky-nav-links">
                                                    <a href="#section1" class="sticky-nav-item active" data-target="section1">
                                                        <span>Overview</span>
                                                    </a>
                                                    <a href="#section9" class="sticky-nav-item" data-target="section9">
                                                        <span>Alternatives</span>
                                                    </a>
                                                    <a href="#section15" class="sticky-nav-item" data-target="section15">
                                                        <span>FAQs</span>
                                                    </a>
                                                    <a href="#section-compare" class="sticky-nav-item" data-target="section-compare">
                                                        <span>Compare</span>
                                                    </a>
                                                    <a href="#section14" class="sticky-nav-item" data-target="section14">
                                                        <span>Reviews</span>
                                                    </a>
                                                    <a href="#sectionDiscussions" class="sticky-nav-item" data-target="sectionDiscussions">
                                                        <span>Discussions</span>
                                                    </a>
                                                </div>


                                                <div class="sticky-nav-footer pt-3 mt-2 border-top text-center">
                                                    @auth
                                                        <button type="button"
                                                            onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                                            class="btn-write-review-link"
                                                            style="background: transparent; border: none; color: #003f7d; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                                            <i class="fas fa-pencil-alt "></i> Write a review
                                                        </button>
                                                    @else
                                                        <a href="javascript:void(0)"
                                                            onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                                            class="btn-write-review-link"
                                                            style="background: transparent; border: none; color: #003f7d; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                                            <i class="fas fa-pencil-alt "></i> Write a review
                                                        </a>
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                        <!-- here we end sticky part -->
                                    </div>
                                </div>

                            </div>
                       </div>
                   <!-- </div> -->
              </section>


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

        // jQuery(window).scroll(function() {
        //     var scroll = jQuery(window).scrollTop();
        //     if (scroll >= 200) {
        //         jQuery(".asn_main_sec > .asn_dv").addClass("fixed-div");
        //     } else {
        //         jQuery(".asn_main_sec > .asn_dv").removeClass("fixed-div");
        //     }
        // });
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        window.addEventListener('review-submitted', () => {
            window.location.reload();
        });
    </script>

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

    <div class="modal fade" id="imageGalleryModal" tabindex="-1" aria-hidden="true" style="z-index: 999999;">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="position: absolute; right: 24px; top: 24px; z-index: 10000; font-size: 20px; font-weight: bold; background: none; border: none; color: #555;">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="gallery-header">
                    <div class="gallery-header-left">
                        <div style="width: 56px; height: 56px; border-radius: 8px; overflow: hidden; background: #f9f9f9; display: flex; align-items: center; justify-content: center; border: 1px solid #eaeaea; flex-shrink: 0;">
                            <x-business-logo :business="$business" />
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
                                <span style="color: #888;">({{ $ratingCount }} {{ $ratingCount == 1 ? 'review' : 'reviews' }})</span>
                            </div>
                        </div>
                    </div>

                    @if($business->is_affiliate)
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
                    @endif
                </div>

                <div class="gallery-body">

                    <button type="button" id="modalPrevBtn"
                            style="position: absolute; left: 24px; top: 50%; transform: translateY(-50%); background: #ffffff; border: 1px solid #eaeaea; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-size: 18px; color: #333; z-index: 1000; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#007bff'; this.style.color='#ffffff';"
                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#333';"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="gallery-image-wrap">
                        <img id="modalActiveImg" src=""
                             alt="Active View"
                             style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; display: block; width: auto; height: auto;">
                    </div>

                    <button type="button" id="modalNextBtn"
                            style="position: absolute; right: 24px; top: 50%; transform: translateY(-50%); background: #ffffff; border: 1px solid #eaeaea; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-size: 18px; color: #333; z-index: 1000; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#007bff'; this.style.color='#ffffff';"
                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#333';"
                    >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('write_review')) {
                // Remove the parameter from the address bar for clean UX
                const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({path: cleanUrl}, '', cleanUrl);

                @auth
                    setTimeout(() => {
                        Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} });
                    }, 500);
                @else
                    setTimeout(() => {
                        Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} });
                    }, 500);
                @endauth
            }
        });
    
        // Sticky Sidebar Navigation Active Link & Smooth Scroll
        document.addEventListener('DOMContentLoaded', function() {
            const stickyNavLinks = document.querySelectorAll('.sticky-nav-links .sticky-nav-item');
            if (stickyNavLinks.length > 0) {
                stickyNavLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        const targetId = this.getAttribute('data-target') || (this.getAttribute('href') ? this.getAttribute('href').replace('#', '') : '');
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            e.preventDefault();
                            const headerOffset = 90;
                            const elementPosition = targetElement.getBoundingClientRect().top;
                            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });

                            stickyNavLinks.forEach(l => l.classList.remove('active'));
                            this.classList.add('active');
                        }
                    });
                });

                // Update active link on scroll
                const navSectionTargets = [
                    { id: 'section1', target: 'section1' },
                    { id: 'section9', target: 'section9' },
                    { id: 'section15', target: 'section15' },
                    { id: 'section-compare', target: 'section-compare' },
                    { id: 'section14', target: 'section14' },
                    { id: 'sectionDiscussions', target: 'sectionDiscussions' }
                ];

                window.addEventListener('scroll', function() {
                    const scrollPos = window.scrollY + 130;
                    let activeId = '';

                    for (const item of navSectionTargets) {
                        const el = document.getElementById(item.id);
                        if (el) {
                            const top = el.offsetTop;
                            const height = el.offsetHeight;
                            if (scrollPos >= top && scrollPos < top + height) {
                                activeId = item.target;
                            }
                        }
                    }

                    if (activeId) {
                        stickyNavLinks.forEach(link => {
                            if (link.getAttribute('data-target') === activeId || link.getAttribute('href') === '#' + activeId) {
                                link.classList.add('active');
                            } else {
                                link.classList.remove('active');
                            }
                        });
                    }
                }, { passive: true });
            }
        });

    </script>

@endsection
