@extends('user_layout.master')

@section('content')
<!-- Hero Section -->
<section class="write-review-hero text-center  text-white" style="background-color:#fdfdfd;">
    <div class="container">
        <h1 class="fw-bold mb-3" style="color: #1e3050; font-weight: 700 !important; margin-bottom: 8px; font-size:28px !important">Find the software and services you use and want to review</h1>
        <p class="text-white-50 mb-5 mx-auto" style=" font-weight: 400 !important; font-size: 16px !important; color: #444 !important; margin-bottom: 0;">
            Our community is a place for professionals to help one another find the best business solutions.
        </p>
        
        @livewire('write-review-search')
    </div>
</section>

<!-- Content Lists -->
<section class="write-review-lists  write_revw_sec py-5 bg-light" style="background-color: #f9fafb  !important;">
    <div class="container">
        
        <!-- Trending Section -->
        <div class="mb-5">
            <h2 class=" mb-4" style="color: #002347; font-size: 24px; ">Trending Software & Services</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($trendingBusinesses as $business)
                    @php
                        $ratingAvg = $business->reviews->avg('rating');
                        $count = $business->reviews->where('status', 'active')->count();
                    @endphp
                    <div class="col">
                        <div class="card h-100   p-4 text-center d-flex flex-column align-items-center" style="border-radius: 12px; background: #fff;  border: 1px solid #e2e8f0; border-radius:15px;"onmouseout="this.style.transform='none'">
                            <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; background: #f8fafc;  display: flex; align-items: center; justify-content: center; flex-shrink: 0;" class="mb-3">
                                <img src="{{ asset($business->icon_id ?? 'front/img/logo.svg') }}" alt="{{ $business->translations->first()->name ?? '' }}" style="max-width: 100%; max-height: 100%; object-fit: none;">
                            </div>
                            <h5 class="mb-2 text-truncate w-100" style="color:#002347; font-size: 16px; font-weight:600;">
                                {{ $business->translations->first()->name ?? 'Unnamed' }}
                            </h5>
                            <div class="d-flex align-items-center justify-content-center mb-4 flex-wrap" style="gap: 6px;">
                                <span style="font-size: 11px; font-weight: 500; color: #333;">{{ number_format($ratingAvg, 1) }}</span>
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
                                <span class="" style="font-size: 11px; font-weight: 500;">({{ $count }})</span>
                            </div>
                            <a href="/{{ app()->getLocale() }}/{{ $business->translations->first()->slug ?? '' }}?write_review=1" class="box-btn btn btn-outline-primary w-100 py-2  fw-semibold" style="border-radius: 20px; font-size: 14px;  background-color:#003f7d; color: #fff; transition:unset !important;">
                                Review
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recently Reviewed Section -->
        <div>
            <h2 class=" mb-4" style="color: #002347; font-size: 24px;">Recently Reviewed</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($recentlyReviewed as $business)
                    @php
                        $ratingAvg = $business->reviews->avg('rating');
                        $count = $business->reviews->where('status', 'active')->count();
                    @endphp
                    <div class="col">
                        <div class="card h-100 p-4 text-center d-flex flex-column align-items-center" style="border-radius: 15px; border:1px solid #e2e8f0; transition:unset !important; background: #fff;" >
                            <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; background: #f8fafc;display: flex; align-items: center; justify-content: center; flex-shrink: 0;" class="mb-3">
                                <img src="{{ asset($business->icon_id ?? 'front/img/logo.svg') }}" alt="{{ $business->translations->first()->name ?? '' }}" style="max-width: 100%; max-height: 100%; object-fit: none;">
                            </div>
                            <h5 class=" mb-2 text-truncate w-100" style="color: #002347; font-size: 16px; font-weight:600;">
                                {{ $business->translations->first()->name ?? 'Unnamed' }}
                            </h5>
                            <div class="d-flex align-items-center justify-content-center mb-4 flex-wrap" style="gap: 6px;">
                                <span style="font-size: 11px; font-weight: 400; color: #333;">{{ number_format($ratingAvg, 1) }}</span>
                                <div class="text-warning d-flex gap-1" style="font-size: 11px; color: #ff9d28 !important;">
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
                                <span class="text-muted" style="font-size: 11px; font-weight: 400;">({{ $count }})</span>
                            </div>
                            <a href="/{{ app()->getLocale() }}/{{ $business->translations->first()->slug ?? '' }}?write_review=1" class="box-btn btn btn-outline-primary w-100 py-2 border-2 fw-semibold" style="border-radius: 20px; font-size: 14px; transition:unset !important; color: #fff; background-color: #003f7d;" >
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
