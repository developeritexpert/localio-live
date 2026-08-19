@extends('user_layout.master')

@section('content')
<!-- Hero Section -->
<section class="write-review-hero text-center  text-white" style="background-color:#fdfdfd;">
    <div class="container">
        <h1 class="fw-bold mb-3" style="color: #1e3050; font-weight: 700 !important; margin-bottom: 8px; font-size:28px !important"> Share your experience </h1>
        <p class="text-white-50 mb-5 mx-auto" style=" font-weight: 400 !important; font-size: 16px !important; color: #444 !important; margin-bottom: 0;">
            Help the Localio community make better choices by sharing your experience with a business or product
        </p>
        
        @livewire('write-review-search')
    </div>
</section>

<!-- Content Lists -->
<section class="write-review-lists  write_revw_sec py-5 bg-light" style="background-color: #f9fafb  !important;">
    <div class="container">
        
        <!-- Trending Section -->
        <div class="mb-5">
            <h2  style="color: #002347; font-size: 24px; ">Popular with our community</h2>
            <p class=" mb-4">See what the Localio community is reviewing right now</p>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($trendingBusinesses as $business)
                    @php
                        $ratingAvg = $business->reviews->avg('rating');
                        $count = $business->reviews->where('status', 'active')->count();
                    @endphp
                    <div class="col">
                        <div class="card h-100   p-4 text-center d-flex flex-column align-items-center" style="border-radius: 12px; background: #fff;  border: 1px solid #e2e8f0; border-radius:15px;"onmouseout="this.style.transform='none'">
                            <div style="" class="mb-3 top-product-logo">
                                <x-business-logo :business="$business" />
                            </div>
                            <h5 class="mb-1 text-truncate w-100" style="color:#002347; font-size: 16px; font-weight:600;">
                                {{ $business->translations->first()->name ?? 'Unnamed' }}
                            </h5>
                            <div class="rating-group d-flex align-items-center justify-content-center mb-4 flex-wrap" style="gap: 6px;">
                                <span style="">{{ number_format($ratingAvg, 1) }}</span>
                                <div class="text-warning d-flex" style="font-size: 11px; color: #ff9d28 !important;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($ratingAvg))
                                            <i class="fas fa-star" style="font-size: 11px;"></i>
                                        @elseif ($i - 0.5 <= $ratingAvg)
                                            <i class="fas fa-star-half-alt" style="font-size: 11px;"></i>
                                        @else
                                            <i class="far fa-star" style="font-size: 11px;"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="" style="">({{ $count }})</span>
                            </div>
                            <a href="/{{ app()->getLocale() }}/{{ $business->translations->first()->slug ?? '' }}?write_review=1" class="blue-btn  w-100 py-2  fw-semibold" style="border-radius: 20px; font-size: 14px;  background-color:#003f7d; color: #fff; transition:unset !important;">
                                Review
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

                <!-- Be the first to review Section -->
        @if(isset($unreviewedBusinesses) && $unreviewedBusinesses->count() > 0)
        <div class="mb-5">
            <h2 style="color: #002347; font-size: 24px;">Be the first to review</h2>
            <p class="mb-4">Share your experience and help our community</p>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($unreviewedBusinesses as $business)
                    <div class="col">
                        <div class="card h-100 p-4 text-center d-flex flex-column align-items-center justify-content-between" style="border-radius: 15px; border:1px solid #e2e8f0; transition:unset !important; background: #fff;">
                            <div class="d-flex flex-column align-items-center w-100">
                                <div class="mb-3 top-product-logo">
                                    <x-business-logo :business="$business" />
                                </div>
                                <h5 class="mb-4 text-truncate w-100" style="color: #002347; font-size: 16px; font-weight:600;">
                                    {{ $business->translations->first()->name ?? 'Unnamed' }}
                                </h5>
                            </div>
                            <a href="/{{ app()->getLocale() }}/{{ $business->translations->first()->slug ?? '' }}?write_review=1" class=" blue-btn btn btn-outline-primary w-100 py-2 border-2 fw-semibold" style="border-radius: 20px; font-size: 14px; transition:unset !important; color: #fff; background-color: #003f7d;">
                                Review
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recently Reviewed Section -->
        <div>
            <h2 style="color: #002347; font-size: 24px;">Recently reviewed</h2>
            <p class=" mb-4">The latest experiences shared by the Localio community</p>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($recentlyReviewed as $business)
                    @php
                        $ratingAvg = $business->reviews->avg('rating');
                        $count = $business->reviews->where('status', 'active')->count();
                    @endphp
                    <div class="col">
                        <div class="card h-100 p-4 text-center d-flex flex-column align-items-center" style="border-radius: 15px; border:1px solid #e2e8f0; transition:unset !important; background: #fff;" >
                            <div style="" class="mb-3 top-product-logo">
                                <x-business-logo :business="$business" />
                            </div>
                            <h5 class=" mb-2 text-truncate w-100" style="color: #002347; font-size: 16px; font-weight:600;">
                                {{ $business->translations->first()->name ?? 'Unnamed' }}
                            </h5>
                            <div class="rating-group d-flex align-items-center justify-content-center mb-4 flex-wrap" style="gap: 6px;">
                                <span style="f color: #333;">{{ number_format($ratingAvg, 1) }}</span>
                                <div class="text-warning d-flex " style="font-size: 11px; color: #ff9d28 !important;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($ratingAvg))
                                            <i class="fas fa-star" style="font-size: 11px;"></i>
                                        @elseif ($i - 0.5 <= $ratingAvg)
                                            <i class="fas fa-star-half-alt" style="font-size: 11px;"></i>
                                        @else
                                            <i class="far fa-star" style="font-size: 11px;"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="" style="">({{ $count }})</span>
                            </div>
                            <a href="/{{ app()->getLocale() }}/{{ $business->translations->first()->slug ?? '' }}?write_review=1" class=" blue-btn btn btn-outline-primary w-100 py-2 border-2 fw-semibold" style="border-radius: 20px; font-size: 14px; transition:unset !important; color: #fff; background-color: #003f7d;" >
                                Review
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</section>
@endsection
