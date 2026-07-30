@extends('user_layout.master')

@section('content')
@php
    $lang_id = getCurrentLanguageID();
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? 'providers';
    $parentCatName = $parentCatTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $compSlug = $catTrans->comparison_slug ?? 'compare';
    $bName = $business->translations->first()->name ?? 'Business';
    $subHeadline = static_text('business_comparisons_subheadline') !== 'business_comparisons_subheadline' 
        ? static_text('business_comparisons_subheadline') 
        : 'See how ' . $bName . ' compares to other ' . $catName . ' providers.';
@endphp

<!-- Upper Header Section ( identical to business details page header, without in-page navigation) -->
<section class=" help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 comparsn_bnr_sec" style="background-color: #fdfdfd; color: #1e3050;  border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="asn_dv d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="background-color: #fdfdfd;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb " style="background: transparent; padding: 0; font-size: 14px; margin-bottom:0;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" style="color: #64748b; text-decoration: none;">All</a>
                    </li>
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
                        <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                            @if($catSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" style="color: #1e3050; font-weight: 500; text-decoration: none;">{{ $catName }}</a>
                            @else
                                {{ $catName }}
                            @endif
                        </li>
                    @endif
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
                        <div class=" an_lkd d-flex align-items-center gap-2 ">
                            <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin: 0; line-height: 1.2;">
                                {{ $bName }} Comparisons
                            </h1>
                            <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                        </div>
                        <p style="font-size: 15px; color: #64748b; margin: 4px 0 0 0;">
                            {{ $subHeadline }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-end text-start ">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 12px rgba(255, 87, 34, 0.25);" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Visit website <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Comparisons Grid Section -->
<section class="all_comparisons_sec py-5" style="background-color: #ffffff !important;">
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
                <div class="col-lg-6 col-12">
                    <a href="{{ $seoUrl }}" style="text-decoration: none; color: inherit; display: block;">
                        <div class="comparison-box p-3 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 2px 6px rgba(0,0,0,0.03); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(0,0,0,0.06)'; this.style.borderColor='#cbd5e0';" onmouseout="this.style.boxShadow='0 2px 6px rgba(0,0,0,0.03)'; this.style.borderColor='#e2e8f0';">
                            <div class=" cmpr_bx d-flex align-items-center justify-content-between">
                                <!-- Business A -->
                                <div class=" d-flex align-items-center gap-2" style="min-width: 0; flex: 1;">
                                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                        <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="fw-semibold text-truncate" style="font-size: 14.5px; color: #1e3050;">{{ $bName }}</div>
                                        <div class="d-flex align-items-center gap-1" style="font-size: 12.5px; color: #64748b;">
                                            <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                            <span style="font-weight: 600; color: #475569;">{{ number_format($businessRating, 1) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- VS Keyword -->
                                <div class="vs_txt px-3 fw-bold text-center flex-shrink-0" style="font-size: 15px; color: #1e3050; font-family: sans-serif;">
                                    {{ strtoupper($vsKey) }}
                                </div>

                                <!-- Business B (Peer) -->
                                <div class="  d-flex align-items-center gap-2" style="min-width: 0; flex: 1; justify-content: flex-end;">
                                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                        <img src="{{ asset($peer->icon_id ?? 'no-image.png') }}" alt="{{ $peerName }}" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="min-width: 0; text-align: left;">
                                        <div class="fw-semibold text-truncate" style="font-size: 14.5px; color: #1e3050;">{{ $peerName }}</div>
                                        <div class="d-flex align-items-center gap-1" style="font-size: 12.5px; color: #64748b;">
                                            <i class="fas fa-star text-warning" style="font-size: 11px;"></i>
                                            <span style="font-weight: 600; color: #475569;">{{ number_format($peerRating, 1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
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
