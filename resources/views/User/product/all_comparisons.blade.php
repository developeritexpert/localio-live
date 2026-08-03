@extends('user_layout.master')

@section('content')
@php
    $lang_id = getCurrentLanguageID();
    $translation = $business->translations->first();
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? 'providers';
    $parentCatName = $parentCatTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $compSlug = $catTrans->comparison_slug ?? 'compare';
    $bName = $translation->name ?? 'Business';
    $subHeadline = static_text('business_comparisons_subheadline') !== 'business_comparisons_subheadline' 
        ? static_text('business_comparisons_subheadline') 
        : 'See how ' . $bName . ' compares to other ' . $catName . ' providers.';

    $compTitle1 = $translation->comparison_title ?? ('Compare ' . $bName);
    $compDesc1  = $translation->comparison_description ?? '';
    $compTitle2 = $translation->comparison_title_2 ?? '';
    $compDesc2  = $translation->comparison_description_2 ?? '';

    $vsKey = static_text('vs_keyword');
    if (empty($vsKey) || $vsKey === 'vs_keyword') {
        $vsKey = 'vs';
    }
    $vsKeySlug = Str::slug($vsKey);
@endphp

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 comparsn_bnr_sec" style="background-color: #fdfdfd; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="background-color: #fdfdfd;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
                    @if($parentCatName)
                        <li class="breadcrumb-item">
                            @if($parentCatSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentCatSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $parentCatName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $parentCatName }}</span>
                            @endif
                        </li>
                    @endif
                    @if($catName)
                        <li class="breadcrumb-item">
                            @if($catSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $catName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $catName }}</span>
                            @endif
                        </li>
                    @endif
                    @if($business)
                        <li class="breadcrumb-item">
                            <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $business->translations->first()->slug ?? '']) }}" style="color: #64748b; text-decoration: none;">{{ $bName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        Comparison
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <!-- Business Icon -->
                    <div class="asn-img" style="width: 55px; height: 55px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2">
                            <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin: 0; line-height: 1.2;">
                               Compare {{ $bName }} 
                            </h1>
                            <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                        </div>
                        <p style="font-size: 15px; color: #64748b; margin: 4px 0 0 0;">
                            {{ $subHeadline }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-end text-start">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Visit website <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Top Content Section: Both Titles & Descriptions on Left, Both Widgets on Right -->
<section class="py-5" style="background-color: #ffffff;">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side: Title 1 & Description 1 AND Title 2 & Description 2 -->
            <div class="col-lg-8 col-12">
                <!-- Section 1 -->
                @if($compTitle1)
                    <div class="mb-5">
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                            {{ $compTitle1 }}
                        </h2>
                        @if($compDesc1)
                            <div class="content_box" style="font-size: 15px; color: #4a5568; line-height: 1.6;">
                                {!! $compDesc1 !!}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 2 -->
                @if($compTitle2 || $compDesc2)
                    <div class="mb-4">
                        @if($compTitle2)
                            <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                                {{ $compTitle2 }}
                            </h2>
                        @endif
                        @if($compDesc2)
                            <div class="content_box" style="font-size: 15px; color: #4a5568; line-height: 1.6;">
                                {!! $compDesc2 !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Right Side: Rating Breakdown Widget & Popular Comparisons Widget -->
            <div class="col-lg-4 col-12 d-flex flex-column gap-4">
                <!-- Widget 1: Rating Breakdown -->
                <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <!-- Overall Rating Box -->
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3" style="border-bottom: 1px solid #f0f0f0;">
                        <div>
                            <div style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1;">
                                {{ number_format($averageRating, 1) }}
                            </div>
                            <div style="margin-top: 8px; margin-bottom: 4px; display: flex; gap: 4px;">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($averageRating))
                                        <i class="fas fa-star text-warning" style="font-size: 16px;"></i>
                                    @elseif ($i - 0.5 <= $averageRating)
                                        <i class="fas fa-star-half-alt text-warning" style="font-size: 16px;"></i>
                                    @else
                                        <i class="far fa-star text-warning" style="font-size: 16px;"></i>
                                    @endif
                                @endfor
                            </div>
                            <div style="color: #718096; font-size: 13px;">{{ number_format($totalReviews) }} reviews</div>
                        </div>
                        <a href="#grid-comparisons-section" class="view-review-link" style="color: #06498b; font-weight: 600; font-size: 14px; text-decoration: none; padding-top: 5px;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                            View all comparisons
                        </a>
                    </div>

                    @if(isset($criteria) && count($criteria) > 0)
                        <h6 style="font-size: 14px; font-weight: 700; color: #002347; margin-bottom: 12px;">Review breakdown</h6>
                        <div class="mb-3">
                            @foreach ($criteria as $criterion)
                                <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                    <p class="m-0" style="font-size: 13px; font-weight: 500; color: #444;">{{ $criterion->name }}</p>
                                    <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                        <progress class="progress-bar w-100" value="{{ $criterion->average_rating * 20 }}" max="100" style="height: 8px;"></progress>
                                        <span style="font-size: 12px; font-weight: 600; color: #333; margin-left: 8px; min-width: 35px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}/5</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center pt-2 mt-2" style="border-top: 1px solid #f0f0f0;">
                        <span style="font-weight: 600; color: #002347; font-size: 13.5px;">Recommended by users</span>
                        <strong style="color: #002347; font-size: 13.5px;">{{ $recommendPercent }}%</strong>
                    </div>
                </div>

                <!-- Widget 2: Popular Comparisons -->
                @if(count($peerComparisons) > 0)
                    <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid #f0f0f0;">
                            <h6 style="font-size: 15px; font-weight: 700; color: #002347; margin: 0;">Popular comparisons</h6>
                            <a href="#grid-comparisons-section" style="color: #06498b; font-weight: 600; font-size: 13.5px; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                View all comparisons
                            </a>
                        </div>

                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 14px;">
                            @foreach($peerComparisons->take(5) as $popPeer)
                                @php
                                    $popPeerName = $popPeer->translations->first()->name ?? 'Business';
                                    $popUrl = route('product-comparison.seo', [
                                        'locale' => app()->getLocale(),
                                        'comparison_slug' => $compSlug,
                                        'comparison_businesses' => Str::slug($bName) . '-' . $vsKeySlug . '-' . Str::slug($popPeerName)
                                    ]);
                                @endphp
                                <li>
                                    <a href="{{ $popUrl }}" style="color: #2b6cb0; text-decoration: none; font-weight: 500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        {{ $bName }} VS {{ $popPeerName }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Comparisons Grid Section -->
<section id="grid-comparisons-section" class="all_comparisons_sec py-5" style="background-color: #f8fafc !important; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div class="hd_text mb-4" data-aos="fade-up" data-aos-duration="1000">
            <h2 style="font-size: 26px; font-weight: 700; color: #1e3050; margin-bottom: 6px;">
                Compare {{ $bName }}
            </h2>
            <p style="font-size: 15px; color: #64748b; margin: 0;">
                See how {{ $bName }} compares to other {{ $catName }} providers.
            </p>
        </div>

        <div class="row g-4" data-aos="fade-up" data-aos-duration="1000">
            @forelse($peerComparisons as $peer)
                @php
                    $peerName = $peer->translations->first()->name ?? 'Business';
                    $peerRating = $peer->average_rating ?? 0;
                    $seoUrl = route('product-comparison.seo', [
                        'locale' => app()->getLocale(),
                        'comparison_slug' => $compSlug,
                        'comparison_businesses' => Str::slug($bName) . '-' . $vsKeySlug . '-' . Str::slug($peerName)
                    ]);
                @endphp
                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <div class="comparison-box p-3 bg-white rounded-3 border d-flex flex-column justify-content-between h-100" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 2px 6px rgba(0,0,0,0.03); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(0,0,0,0.08)'; this.style.borderColor='#cbd5e0';" onmouseout="this.style.boxShadow='0 2px 6px rgba(0,0,0,0.03)'; this.style.borderColor='#e2e8f0';">
                        <div class="cmpr_bx d-flex align-items-center justify-content-between mb-3">
                            <!-- Business A -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $bName }}</div>
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ number_format($businessRating, 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- VS Keyword -->
                            <div class="vs_txt px-2 fw-bold text-center flex-shrink-0" style="font-size: 13px; color: #1e3050; font-family: sans-serif;">
                                {{ strtoupper($vsKey) }}
                            </div>

                            <!-- Business B (Peer) -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1; justify-content: flex-end;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <img src="{{ asset($peer->icon_id ?? 'no-image.png') }}" alt="{{ $peerName }}" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                                <div style="min-width: 0; text-align: left;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $peerName }}</div>
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ number_format($peerRating, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compare Button -->
                        <div class="mt-2 text-center">
                            <a href="{{ $seoUrl }}" class="btn w-100" style="background-color: #06498b; color: #ffffff; font-weight: 600; font-size: 13.5px; padding: 7px 16px; border-radius: 20px; text-decoration: none;" onmouseover="this.style.backgroundColor='#002347';" onmouseout="this.style.backgroundColor='#06498b';">
                                Compare
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">No comparisons available for this business yet.</div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $peerComparisons->links('pagination::bootstrap-4') }}
        </div>
    </div>
</section>
@endsection
