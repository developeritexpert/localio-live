@extends('user_layout.master')
@section('content')

@php
    $lang_id = getCurrentLanguageID();
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatName = $parentCatTrans->name ?? '';
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $bName = $business->translations->first()->name ?? 'Business';
    $stAltSub = static_text('business_alternatives_subheadline');
    if (!empty($stAltSub) && $stAltSub !== 'business_alternatives_subheadline') {
        $subHeadline = $stAltSub;
    } else {
        $subHeadline = 'Find the best alternatives to ' . $bName . '.';
    }
@endphp

<!-- Upper Header Section (identical to review details page header) -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="asn_dv d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="background-color: #f7f9fb;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb " style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
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
                        Alternatives
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon :business="$business" />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head">
                    @php
                        $bTrans = $business ? ($business->translations->where('language_id', getCurrentLanguageID())->first() ?? $business->translations->first()) : null;
                        $altTitle = !empty($bTrans->alternatives_title) ? $bTrans->alternatives_title : ($bName . ' alternatives');
                    @endphp
                    <div class="an_lkd d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin: 0; line-height: 1.2;">
                            {{ $bName }} alternatives
                        </h1>
                    </div>
                    <p class="text-muted" style="font-size: 13px; margin-bottom: 14px; color: #888;">
                        Last updated on {{ now()->format('F j, Y') }}
                    </p>
                    <p style="font-size: 16px; color: #444; margin: 0; font-weight: 400;">
                        {{ $subHeadline }}
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-end text-start mt-md-0 mt-3">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="btn d-none" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Visit website <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>

@livewire('business-alternatives', ['businessId' => $business->id, 'hasUpperHeader' => true])
@endsection
