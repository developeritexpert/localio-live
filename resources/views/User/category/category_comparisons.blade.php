@extends('user_layout.master')
@push('styles')
<style>
    .auto-choice-rgt .btn-pages {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-top: 50px;
    }
    .auto-choice-rgt .btn-pages .pagination-btn {
        width: 44px;
        height: 44px;
        border-radius: 50% !important;
        border: 1.5px solid #174889;
        background-color: #ffffff;
        color: #174889;
        font-size: 15px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .auto-choice-rgt .btn-pages .pagination-btn.active {
        background-color: #174889 !important;
        color: #ffffff !important;
        border-color: #174889 !important;
    }
    .auto-choice-rgt .btn-pages .pagination-btn:hover {
        background-color: #174889 !important;
        color: #ffffff !important;
        border-color: #174889 !important;
    }
    .auto-choice-rgt .btn-pages .pagination-btn:hover i {
        color: #ffffff !important;
    }
    .auto-choice-rgt .btn-pages .pagination-arrow {
        background-color: #ffffff;
        border: 1.5px solid #174889;
        border-radius: 50% !important;
    }
    .auto-choice-rgt .btn-pages .pagination-arrow i {
        color: #174889;
        font-size: 13px;
    }
    .auto-choice-rgt .btn-pages .pagination-dots {
        font-size: 16px;
        color: #64748b;
        padding: 0 4px;
    }
</style>
@endpush

@php
    $catSlug = $categoryTranslation->slug ?? $category->slug;
    $parentCatName = $parentTrans->name ?? ($parentCategory->name ?? null);
    $parentCatSlug = $parentTrans->slug ?? ($parentCategory->slug ?? null);
    $catDesc = strip_tags($categoryTranslation->description ?? '');

    $vsKey = static_text('vs_keyword');
    if (empty($vsKey) || $vsKey === 'vs_keyword') {
        $vsKey = 'vs';
    }
    $vsKeySlug = \Illuminate\Support\Str::slug($vsKey);

    $compSlug = $categoryTranslation->comparison_slug ?? 'compare';

    $compTitle1 = $categoryTranslation->comparison_title ?? ('Compare ' . $catName . ' Providers');
    $compDesc1  = $categoryTranslation->comparison_description ?? '';
    $compTitle2 = $categoryTranslation->comparison_title_2 ?? '';
    $compDesc2  = $categoryTranslation->comparison_description_2 ?? '';
@endphp

@section('meta_title', format_meta_text("Compare {$catName} Providers & Alternatives | Localio"))
@section('meta_description', format_meta_text("See how top {$catName} providers compare side-by-side on features, pricing, customer reviews, and community satisfaction scores on Localio."))

@section('content')

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb Row -->
        <div class="asn_dv d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="background-color: #f7f9fb;">
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
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $catName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        Comparisons
                    </li>
                </ol>
            </nav>
            <div class="col-4 d-flex justify-content-end">
                <x-social-icon />
            </div>
        </div>

        <!-- Category Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <!-- Category Avatar -->
                   
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin: 0; line-height: 1.2;">
                                Compare {{ $catName }}
                            </h1>
                        </div>
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 14px; color: #888;">
                            Last updated on {{ now()->format('F j, Y') }}
                        </p>
                        <p style="font-size: 16px; color: #444; margin: 0; font-weight: 400;">
                            See how top {{ strtolower($catName) }} providers compare side-by-side on features, pricing, reviews, and community satisfaction.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Content Section: Descriptions Left, Sidebar Right -->
<section class="py-5 common_detail_sec" style="background-color: #ffffff;">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side: Description content -->
            <div class="col-lg-8 col-12">
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

            <!-- Right Side: Popular Comparisons Widget only -->
            <div class="col-lg-4 col-12 d-flex flex-column gap-4">
                @if($paginatedComparisons->total() > 0)
                    <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid #f0f0f0;">
                            <h6 style="font-size: 14px; font-weight: 600; color: #002347; margin: 0;">Popular comparisons</h6>
                            <a href="#grid-comparisons-section" style="color: #002347; font-weight: 600; font-size: 13px; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                View all comparisons
                            </a>
                        </div>

                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 14px;">
                            @foreach($paginatedComparisons->take(5) as $popComp)
                                <li>
                                    <a href="{{ $popComp['url'] }}" style="color: #2b6cb0; text-decoration: none; font-weight: 600;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        {{ $popComp['business_1_name'] }} VS {{ $popComp['business_2_name'] }}
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
<section id="grid-comparisons-section" class="compare-section all_comparisons_sec py-5" style="background-color: #f7f9fb !important; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div class="hd_text mb-4" data-aos="fade-up" data-aos-duration="1000">
            <h2 style="font-size: 26px; font-weight: 700; color: #1e3050; margin-bottom: 6px;">
                All {{ $catName }} Comparisons
            </h2>
            <p style="font-size: 15px; color: #64748b; margin: 0;">
                Compare top {{ strtolower($catName) }} providers side-by-side on features, reviews, and community satisfaction.
            </p>
        </div>

        <div class="row g-4" data-aos="fade-up" data-aos-duration="1000">
            @forelse($paginatedComparisons as $comp)
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="comparison-box p-3 rounded-3 border h-100 d-flex flex-column justify-content-between" style="background-color: #f8fafc !important; border-radius: 12px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
                        <div class="cmpr_bx d-flex align-items-center justify-content-between mb-3">
                            <!-- Business 1 -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$comp['business_1']" :name="$comp['business_1_name']" />
                                </div>
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $comp['business_1_name'] }}</div>
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ number_format($comp['business_1_rating'], 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- VS -->
                            <div class="px-2 vs_circle text-muted flex-shrink-0" style="font-size: 16px; font-family: sans-serif;">vs</div>

                            <!-- Business 2 -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1; justify-content: flex-end;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$comp['business_2']" :name="$comp['business_2_name']" />
                                </div>
                                <div style="min-width: 0; text-align: left;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $comp['business_2_name'] }}</div>
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ number_format($comp['business_2_rating'], 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compare Button -->
                        <div class="cmpre_btn w-100 mt-auto">
                            <a href="{{ $comp['url'] }}" class="cta cta_btn text-decoration-none w-100" style="padding: 8px 20px !important; border-radius: 50px !important; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: center; width: 100%;">
                                Compare
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">No comparisons available for this category yet.</div>
            @endforelse
        </div>

        @php
            $currentPage = $paginatedComparisons->currentPage();
            $lastPage = $paginatedComparisons->lastPage() ?? 1;
            $maxVisible = 7;

            $startPage = max(1, $currentPage - floor($maxVisible / 2));
            $endPage = min($lastPage, $startPage + $maxVisible - 1);

            if ($endPage - $startPage + 1 < $maxVisible) {
                $startPage = max(1, $endPage - $maxVisible + 1);
            }
            $showLeftDots = $startPage > 2;
            $showRightDots = $endPage < $lastPage - 1;
        @endphp

        @if ($lastPage > 1)
            <div class="auto-choice-rgt d-flex justify-content-center mt-5">
                <div class="btn-pages">
                    @if ($currentPage > 1)
                        <a href="{{ $paginatedComparisons->url($currentPage - 1) }}" class="pagination-btn pagination-arrow">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @if ($startPage > 1)
                        <a href="{{ $paginatedComparisons->url(1) }}" class="pagination-btn {{ $currentPage == 1 ? 'active' : '' }}">1</a>
                        @if ($showLeftDots)
                            <span class="pagination-dots">...</span>
                        @endif
                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        <a href="{{ $paginatedComparisons->url($page) }}" class="pagination-btn {{ $currentPage == $page ? 'active' : '' }}">
                            {{ $page }}
                        </a>
                    @endfor

                    @if ($endPage < $lastPage)
                        @if ($showRightDots)
                            <span class="pagination-dots">...</span>
                        @endif
                        <a href="{{ $paginatedComparisons->url($lastPage) }}" class="pagination-btn {{ $currentPage == $lastPage ? 'active' : '' }}">
                            {{ $lastPage }}
                        </a>
                    @endif

                    @if ($currentPage < $lastPage)
                        <a href="{{ $paginatedComparisons->url($currentPage + 1) }}" class="pagination-btn pagination-arrow next">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
