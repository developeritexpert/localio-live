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
@endphp

@section('meta_title', format_meta_text("Compare {$catName} Providers & Alternatives | Localio"))
@section('meta_description', format_meta_text("See how top {$catName} providers compare side-by-side on features, pricing, customer reviews, and community satisfaction scores on Localio."))

@section('content')

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="margin-top: 100px; background-color: #f7f9fb; color: #1e3050; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/' . app()->getLocale()) }}" style="color: #64748b; text-decoration: none;">Home</a>
                    </li>
                    @if($parentCatName && $parentCatSlug)
                        <li class="breadcrumb-item">
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentCatSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $parentCatName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item">
                        <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $catName }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        Comparisons
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Category Comparisons Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-3">
                    <div class="asn-img" style="width: 55px; height: 55px; border-radius: 50%; background: #002347; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                        @if(!empty($category->icon) && file_exists(public_path($category->icon)))
                            <img src="{{ asset($category->icon) }}" alt="{{ $catName }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <span style="color: #ffffff; font-weight: 700; font-size: 24px; text-transform: uppercase;">
                                {{ strtoupper(substr($catName, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 1.2; color: #1e3050;">
                                Compare {{ $catName }} Providers
                            </h1>
                        </div>
                        <p style="font-size: 16px; color: #444; margin-top: 4px; margin-bottom: 0; font-weight:400;">
                            See how top {{ strtolower($catName) }} providers compare to each other on features, reviews, and community scores.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-end text-start mt-md-0 mt-3">
                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Explore {{ strtolower($catName) }} <i class="fas fa-arrow-right" style="font-size: 13px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Top Content Section: Overview on Left, Widgets on Right -->
<section class="py-5 common_detail_sec" style="background-color: #ffffff;">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side -->
            <div class="col-lg-8 col-12">
                <div class="mb-4">
                    <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                        What to consider when comparing {{ strtolower($catName) }} providers
                    </h2>
                    <div class="is_text" style="font-size: 15px; color: #475569; line-height: 1.7;">
                        @if(!empty($catDesc))
                            <p>{{ $catDesc }}</p>
                        @endif
                        <p>
                            Choosing the right {{ strtolower($catName) }} solution requires evaluating essential capabilities, ease of use, pricing transparency, customer service responsiveness, and overall value. Use our side-by-side comparison cards below to explore how top competitors match up across authentic customer ratings.
                        </p>
                    </div>
                </div>

                <!-- Quick Navigation Jump to Comparisons -->
                <div class="p-4 bg-light rounded-3 border mb-4" style="border-radius: 12px !important; border: 1px solid #e2e8f0 !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h5 style="font-size: 16px; font-weight: 700; color: #002347; margin-bottom: 4px;">
                                Ready to compare?
                            </h5>
                            <p style="font-size: 13.5px; color: #64748b; margin: 0;">
                                Select any two providers below for an in-depth feature-by-feature and review comparison.
                            </p>
                        </div>
                        <a href="#grid-comparisons-section" class="btn btn-sm btn-primary rounded-pill px-4 py-2" style="background-color: #002347; border-color: #002347; font-weight: 600; font-size: 13.5px;">
                            Browse Comparisons ↓
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side: Sidebar (2 Boxes) -->
            <div class="col-lg-4 col-12">
                <div class="d-flex flex-column gap-4">

                    <!-- 1. Recent Reviews Box -->
                    <div class="boxshadow_border bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <h5 class="m-0 card-h-title" style="font-size: 16px; font-weight: 700; color: #002347;">Recent reviews</h5>
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="view-review-link" style="color: #06498b; font-size: 13px; font-weight: 600; text-decoration: none;">
                                View all
                            </a>
                        </div>

                        @if(isset($recentReviews) && $recentReviews->count() > 0)
                            @foreach($recentReviews as $rev)
                                @php
                                    $revTrans = $rev->translations->first();
                                    $u = $rev->user;
                                    if ($u && $u->user_type === 'admin') {
                                        $displayName = $rev->public_name ?? 'Public';
                                    } elseif ($u) {
                                        $displayName = $u->displayName();
                                    } else {
                                        $displayName = 'Anonymous';
                                    }
                                    $bName = $rev->business?->translations?->first()?->name ?? $rev->business?->name ?? 'Business';
                                @endphp
                                <div class="sidebar-review-card {{ !$loop->last ? 'pb-3 mb-3 border-bottom' : '' }}">
                                    <div class="review-header d-flex justify-content-between align-items-start">
                                        <div class="review-user d-flex align-items-center gap-2">
                                            @if($u && $u->profile_image && $u->profile_image !== 'front/img/default.png')
                                                <img src="{{ asset($u->profile_image) }}" class="rounded-circle" width="38" height="38" style="object-fit:cover;">
                                            @else
                                                <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <span style="color: white; font-weight: bold; font-size: 16px;">{{ strtoupper(substr($displayName, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">{{ $displayName }}</h6>
                                                @if($u && $u->job_title)
                                                    <div style="font-size: 11px; color: #777; margin-top: 1px;">{{ $u->job_title }}</div>
                                                @elseif(!empty($bName))
                                                    <div style="font-size: 11px; color: #777; margin-top: 1px;">on {{ $bName }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $rev->created_at ? $rev->created_at->diffForHumans() : '' }}</small>
                                    </div>
                                    <h6 style="margin-top: 8px; margin-bottom: 3px; font-size: 14px; font-weight: 600; color: #1e3050;">
                                        {{ $revTrans->title ?? 'Review' }}
                                    </h6>
                                    <div class="rating-stars mb-1" style="font-size: 11px; color: #ff5722;">
                                        @for($s=1; $s<=5; $s++)
                                            @if($s<=floor($rev->rating))
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($s-0.5<=$rev->rating)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    @if($revTrans && !empty($revTrans->description))
                                        <p style="font-size: 13px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">{{ \Illuminate\Support\Str::limit(strip_tags($revTrans->description), 85) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="sidebar-review-card mb-3 pb-3 border-bottom">
                                <div class="review-header d-flex justify-content-between align-items-start">
                                    <div class="review-user d-flex align-items-center gap-2">
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="color: white; font-weight: bold; font-size: 16px;">B</span>
                                        </div>
                                        <div>
                                            <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">Beck K.</h6>
                                            <div style="font-size: 11px; color: #777;">Fugiat aute anim eni</div>
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">1 week ago</small>
                                </div>
                                <h6 style="margin-top: 8px; margin-bottom: 3px; font-size: 14px; font-weight: 600; color: #1e3050;">
                                    Rem sint adipisicin
                                </h6>
                                <div class="rating-stars mb-1" style="font-size: 11px; color: #ff5722;">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <p style="font-size: 13px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">Numquam officia rem</p>
                            </div>
                        @endif
                    </div>

                    <!-- 2. Recent Discussions Box -->
                    <div class="boxshadow_border bg-white p-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0;">
                        <div class="review-header-box pb-3 mb-3" style="border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <h5 class="m-0 card-h-title" style="font-size: 16px; font-weight: 700; color: #002347;">Recent discussions</h5>
                            <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" class="view-review-link" style="color: #06498b; font-size: 13px; font-weight: 600; text-decoration: none;">
                                View all
                            </a>
                        </div>

                        <div class="sidebar-review-card mb-3 pb-3 border-bottom">
                            <div class="review-header d-flex justify-content-between align-items-start">
                                <div class="review-user d-flex align-items-center gap-2">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-weight: bold; font-size: 16px;">M</span>
                                    </div>
                                    <div>
                                        <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">Marc L.</h6>
                                        <div style="font-size: 11px; color: #777;">Product Manager • Small Business</div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">2 hours ago</small>
                            </div>
                            <h6 style="margin-top: 8px; font-size: 13.5px; font-weight: 600; color: #1e3050;">
                                Is there a free tier for API access or is it trial only?
                            </h6>
                            <p style="font-size: 12.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                We are looking to integrate this into our workflow and want to test latency...
                            </p>
                        </div>

                        <div class="sidebar-review-card">
                            <div class="review-header d-flex justify-content-between align-items-start">
                                <div class="review-user d-flex align-items-center gap-2">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-weight: bold; font-size: 16px;">S</span>
                                    </div>
                                    <div>
                                        <h6 style="margin: 0; font-size: 13.5px; font-weight: 600; color: #1e3050;">Sarah J.</h6>
                                        <div style="font-size: 11px; color: #777;">CTO • Mid-Market</div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">1 day ago</small>
                            </div>
                            <h6 style="margin-top: 8px; font-size: 13.5px; font-weight: 600; color: #1e3050;">
                                How does the performance compare to alternatives in large datasets?
                            </h6>
                            <p style="font-size: 12.5px; line-height: 1.4; color: #4a5568; margin-bottom: 0;">
                                We noticed some latency spikes during queries with more than 10k items...
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Comparisons Grid Section -->
<section id="grid-comparisons-section" class="compare-section all_comparisons_sec py-5" style="background-color: #f7f9fb !important; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div class="hd_text mb-4">
            <h2 style="font-size: 26px; font-weight: 700; color: #1e3050; margin-bottom: 6px;">
                All {{ $catName }} Comparisons
            </h2>
            <p style="font-size: 15px; color: #64748b; margin: 0;">
                Compare top {{ strtolower($catName) }} providers side-by-side on features, reviews, and community satisfaction.
            </p>
        </div>

        <div class="row g-4">
            @forelse($paginatedComparisons as $comp)
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="comparison-box p-3 rounded-3 border h-100 d-flex flex-column justify-content-between" style="background-color: #ffffff !important; border-radius: 12px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
                        <div class="cmpr_bx d-flex align-items-center justify-content-between mb-3">
                            <!-- Business 1 -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #002347; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$comp['business_1']" :name="$comp['business_1_name']" style="width: 100%; height: 100%; border-radius: 50%;" />
                                </div>
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-truncate" style="font-size: 13.5px; color: #1e3050;">{{ $comp['business_1_name'] }}</div>
                                    <div class="d-flex align-items-center gap-1" style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-star text-warning" style="font-size: 10px;"></i>
                                        <span style="font-weight: 600; color: #475569;">{{ number_format($comp['business_1_rating'], 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- VS Badge -->
                            <div class="px-2 vs_circle text-muted flex-shrink-0" style="font-size: 15px; font-family: sans-serif; font-weight: 600;">vs</div>

                            <!-- Business 2 -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1; justify-content: flex-end;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #002347; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    <x-business-logo :business="$comp['business_2']" :name="$comp['business_2_name']" style="width: 100%; height: 100%; border-radius: 50%;" />
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
                            <a href="{{ $comp['url'] }}" class="cta cta_btn text-decoration-none w-100" style="padding: 8px 20px !important; border-radius: 50px !important; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: center; width: 100%; background: #174889; color: #fff;">
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
                    {{-- Previous Button (only if there's a previous page) --}}
                    @if ($currentPage > 1)
                        <a href="{{ $paginatedComparisons->url($currentPage - 1) }}" class="pagination-btn pagination-arrow">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- First Page --}}
                    @if ($startPage > 1)
                        <a href="{{ $paginatedComparisons->url(1) }}" class="pagination-btn {{ $currentPage == 1 ? 'active' : '' }}">1</a>
                        @if ($showLeftDots)
                            <span class="pagination-dots">...</span>
                        @endif
                    @endif

                    {{-- Page Numbers --}}
                    @for ($page = $startPage; $page <= $endPage; $page++)
                        <a href="{{ $paginatedComparisons->url($page) }}" class="pagination-btn {{ $currentPage == $page ? 'active' : '' }}">
                            {{ $page }}
                        </a>
                    @endfor

                    {{-- Last Page --}}
                    @if ($endPage < $lastPage)
                        @if ($showRightDots)
                            <span class="pagination-dots">...</span>
                        @endif
                        <a href="{{ $paginatedComparisons->url($lastPage) }}" class="pagination-btn {{ $currentPage == $lastPage ? 'active' : '' }}">
                            {{ $lastPage }}
                        </a>
                    @endif

                    {{-- Next Button (only if there's a next page) --}}
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
