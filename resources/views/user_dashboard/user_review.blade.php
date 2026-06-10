@section('title', 'Your Reviews | Localio')

@extends('user_dashboard_layout.master')

@section('content')
    @php
        $business = $reviews->first()?->business;
    @endphp
    <div class="col-lg-9 p-0">
        <div class="user_content">
            <div class="uer_nm">
                <h1>My Reviews</h1>
            </div>
         @if (isset($reviews) && $reviews->isNotEmpty())

            <div class="crt_main ">
  
                    @foreach ($reviews as $review)
                        <div class="cart_dv review_dv">
                            <div class="crt-lft-top d-flex">
                                <div class="cart_img crt-lft-img">
                                    {{-- @if ($review->user && $review->user->profile_image)
                                        <img src="{{ asset($review->user->profile_image) }}" alt="User Image">
                                    @else
                                        <img src="{{ dimage() }}" alt="Default Image">
                                    @endif --}}

                                    @if ($review->business && $review->business->icon_id)
                                    <img src="{{ asset($review->business->icon_id) }}" alt="Business Image">
                                    @endif

                                
                                </div>
                                <div class="cart_text">
                                    <h4>
                                        {{ optional($review->business?->translations->first())->name ?? 'Business Name' }}
                                    </h4>
                                    
                                    <div class="crt-ratings d-flex">
                                        <div class="star-div d-flex">
                                            <div class="stars">
                                                <ul class="list-unstyled m-0 d-flex">
                                                    {{-- @auth
                                                    onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                               @else
                                              onclick="window.location.href = '/login'" @endauth> --}}
                                              <p class="mt-1 pl-1">{{ number_format($review->rating, 1) }} </p>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <li>
                                                            <img src="{{ asset('front/img/' . ($i <= $review->rating ? 'star-img.svg' : 'empty-star-img.svg')) }}"
                                                                alt="Star">
                                                        </li>
                                                    @endfor
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="review-btm d-flex">
                                <div class="review-btm-lft">
                                    <h6>{{ strip_tags($review->translations->first()->title ?? 'No title Available') }}</h6>
                                    <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                    <p>
                                    <div class="month-ago">
                                        
                                    </div>
                                </div>
                                <div class="review-btm-rgt">
                                    {{-- <div class="shr_dt dot">
                                        <span class="elps_icn"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                                        <div class="dropdown-menu_review">
                                            <div class="user_name">
                                                <p class="text-center">Manage Logo</p>
                                            </div>
                                            <div class="dropdown-main ">
                                                <div class="dash-icon">
                                                    <a class="dropdown-item" href="#"><i
                                                            class="fa-brands fa-slack"></i>Logo Details
                                                    </a>
                                                </div>
                                                <div class="dash-icon">
                                                    <a class="dropdown-item" href="#"><i
                                                            class="fa-solid fa-download"></i>Download Logo
                                                    </a>
                                                </div>
                                                <div class="dash-icon">
                                                    <a class="dropdown-item" href="#"><i
                                                            class="fas fa-wallet"></i>Customization</a>
                                                </div>
                                                <div class="dash-icon">
                                                    <a class="dropdown-item" href="#"><i
                                                            class="fa-solid fa-envelope-open-text"></i>Manage
                                                        Logo Backup</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    @endforeach
             
            </div>
         @else
            <div class="crt_main" style="display:flex;justify-content:center;align-items:center;flex-direction:column;">
                <img src="{{ dashboardDefaultImage() }}" alt="No Favorites" class="img-fluid mb-3" style="width: 280px; height: 280px;">
                <h5 class="text-muted">You haven't added any Reviews yet.</h5>
            </div>
        @endif
      </div>
    </div>
    @livewire('add-review')
@endsection
