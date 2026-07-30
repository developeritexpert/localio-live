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
    $reviewsWord = static_text('reviews_word') !== 'reviews_word' ? static_text('reviews_word') : 'reviews';
    $subHeadline = static_text('business_reviews_subheadline') !== 'business_reviews_subheadline' 
        ? static_text('business_reviews_subheadline') 
        : 'Real reviews, community discussions & alternatives';
@endphp

<!-- Upper Header Section ( identical to business details page header, without in-page navigation) -->
<section class="banner_sec help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2" style="background-color: #f8fafc; color: #1e3050; padding: 25px 0 35px 0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="asn_dv d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="background: transparent; padding: 0; font-size: 14px;">
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
        <div class="row align-items-center justify-content-between g-3 mt-1">
            <div class="col-md-8 col-12">
                <div class="d-flex align-items-center gap-3">
                    <!-- Business Icon -->
                    <div class="asn-img" style="width: 64px; height: 64px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin: 0; line-height: 1.2;">
                                {{ $bName }} {{ $reviewsWord }} reviews
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
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 12px rgba(255, 87, 34, 0.25);" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Visit website <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Review Content Section -->
<section class="review-section py-5" style="background-color: #ffffff; overflow: visible !important;">
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
        .clear-filters-btn:hover {
            text-decoration: underline;
        }
        @media (max-width: 991px) {
            .review-sidebar-sticky {
                position: relative !important;
                top: 0 !important;
                margin-bottom: 30px !important;
            }
        }
    </style>

    <div class="container">
        
        <!-- Review Prompt Banner -->
        <div class="review-prompt-banner mb-5" id="reviewPromptBanner" style="background-color: #f8fafc; border-radius: 16px; padding: 22px 28px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #e2e8f0; flex-wrap: wrap; gap: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <div style="display: flex; align-items: center; gap: 18px;">
                <div class="banner-icon" style="width: 52px; height: 52px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.06); flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                    <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 17px !important; font-weight: 700 !important; color: #1e3050 !important;">Have you used {{ $bName }} before?</h4>
                    <p style="margin: 0; font-size: 14px; color: #64748b;">Answer a few questions to help the community.</p>
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                @auth
                    <button onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true });" style="padding: 8px 26px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                        <i class="fas fa-check" style="color: #06498b;"></i> Yes
                    </button>
                    <button onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false });" style="padding: 8px 26px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                        <i class="fas fa-times" style="color: #e53e3e;"></i> No
                    </button>
                @else
                    <button onclick="openLoginModal()" style="padding: 8px 26px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                        <i class="fas fa-check" style="color: #06498b;"></i> Yes
                    </button>
                    <button onclick="openLoginModal()" style="padding: 8px 26px; border-radius: 30px; border: 1px solid #cbd5e0; background: #ffffff; color: #2d3748; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.backgroundColor='#f7fafc';" onmouseout="this.style.borderColor='#cbd5e0'; this.style.backgroundColor='#ffffff';">
                        <i class="fas fa-times" style="color: #e53e3e;"></i> No
                    </button>
                @endauth
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column (Overall Rating Summary Card & Star Filter) -->
            <div class="col-lg-4 col-12">
                <div class="review-sidebar-sticky">
                    
                    <h2 style="font-size: 20px; font-weight: 700; color: #1e3050; margin-bottom: 16px;">
                        User reviews
                    </h2>

                    <!-- Rating Summary Card -->
                    <div class="p-4 bg-white rounded-3 border mb-4" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);">
                        <!-- Average Rating Number & Stars -->
                        <div class="d-flex flex-column align-items-start mb-3">
                            <span style="font-size: 48px; font-weight: 700; color: #1e3050; line-height: 1; margin-bottom: 8px;">{{ number_format($averageRating, 1) }}</span>
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
                            <span style="font-size: 14px; color: #64748b;">{{ number_format($ratingCount) }} reviews</span>
                        </div>

                        <!-- Criteria Breakdown -->
                        @if(isset($criteria) && count($criteria) > 0)
                            <h5 style="font-size: 14px; font-weight: 600; color: #1e3050; margin-top: 20px; margin-bottom: 14px;">Review breakdown</h5>
                            <div class="mb-3">
                                @foreach ($criteria as $criterion)
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span style="font-size: 13.5px; font-weight: 500; color: #334155; white-space: nowrap;">{{ $criterion->name }}</span>
                                        <div class="d-flex align-items-center ms-2" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                            <div class="progress rounded-pill flex-grow-1 mx-2" style="height: 6px; background-color: #e2e8f0;">
                                                <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ ($criterion->average_rating / 5) * 100 }}%; background-color: #22c55e;" aria-valuenow="{{ $criterion->average_rating }}" aria-valuemin="0" aria-valuemax="5"></div>
                                            </div>
                                            <span style="font-size: 12.5px; font-weight: 600; color: #475569; width: 32px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}/5</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(isset($recommendPercent) && $recommendPercent > 0)
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                <span style="font-size: 13.5px; font-weight: 600; color: #1e3050;">Recommended by users</span>
                                <span style="font-size: 15px; font-weight: 700; color: #1e3050;">{{ $recommendPercent }}%</span>
                            </div>
                        @endif
                    </div>

                    <!-- Filter by Rating Section -->
                    <div class="p-4 bg-white rounded-3 border" style="border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <span style="font-size: 15px; font-weight: 700; color: #1e3050;">Filter by rating</span>
                            <span class="clear-filters-btn" id="clear-filters" style="display: none; color: #007bff; font-size: 13px; cursor: pointer;">Clear &times;</span>
                        </div>

                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @for ($i = 5; $i >= 1; $i--)
                                @php
                                    $count = $ratingsCount[$i] ?? 0;
                                    $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                @endphp
                                <li style="display: flex; align-items: center; margin-bottom: 12px; gap: 8px;">
                                    <input type="checkbox" class="rating-filter-checkbox" value="{{ $i }}" id="star-check-{{ $i }}" style="cursor: pointer; width: 16px; height: 16px; margin: 0; accent-color: #0056b3;">
                                    <label for="star-check-{{ $i }}" style="display: flex; align-items: center; width: 100%; cursor: pointer; margin: 0;">
                                        <span style="display: inline-flex; align-items: center; width: 45px; font-size: 13.5px; color: #475569; flex-shrink: 0;">
                                            <i class="far fa-star text-warning" style="margin-right: 4px;"></i> {{ $i }}
                                        </span>
                                        <div style="flex-grow: 1; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-left: 4px; margin-right: 10px;">
                                            <div style="width: {{ $percent }}%; height: 100%; background: #475569; border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-size: 13px; color: #94a3b8; min-width: 30px; text-align: right; flex-shrink: 0;">({{ $count }})</span>
                                    </label>
                                </li>
                            @endfor
                        </ul>
                    </div>

                </div>
            </div>

            <!-- Right Column (Reviews List Container) -->
            <div class="col-lg-8 col-12">
                <!-- Sorting Bar & Write Review Button -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-2 border-bottom">
                    <form method="GET" id="sort-form" class="d-flex align-items-center gap-2 m-0">
                        <label for="rating-select" style="font-size: 14.5px; font-weight: 600; color: #475569; margin: 0; white-space: nowrap;">Sort by:</label>
                        <select class="form-select form-select-sm" id="rating-select" name="sort" style="padding: 6px 32px 6px 12px; font-size: 14px; border-radius: 8px; cursor: pointer; width: auto; min-width: 140px; border: 1px solid #cbd5e0; color: #1e3050; font-weight: 500;">
                            <option value="recent" {{ request('sort') == 'recent' || !request('sort') ? 'selected' : '' }}>Most Recent</option>
                            <option value="best" {{ request('sort') == 'best' ? 'selected' : '' }}>Best Rating</option>
                            <option value="high-to-low" {{ request('sort') == 'high-to-low' ? 'selected' : '' }}>High to Low</option>
                            <option value="low-to-high" {{ request('sort') == 'low-to-high' ? 'selected' : '' }}>Low to High</option>
                        </select>
                    </form>

                    <div>
                        @auth
                            <button onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} });" style="font-size: 14px; font-weight: 600; color: #002347; text-decoration: none; background: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-pen" style="font-size: 12px;"></i> Write review
                            </button>
                        @else
                            <button onclick="openLoginModal()" style="font-size: 14px; font-weight: 600; color: #002347; text-decoration: none; background: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-pen" style="font-size: 12px;"></i> Write review
                            </button>
                        @endauth
                    </div>
                </div>

                <!-- Review List AJAX Container -->
                <div id="reviews-list-container">
                    @include('User.review.partials.reviews_list')
                </div>

                @livewire('add-review')
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $(document).on('click', '.btn-toggle-translation', function () {
            const button = $(this);
            const reviewId = button.data('review-id');
            const languageId = button.data('language-id');
            const type = button.data('type');
            const card = button.closest('.review-card');
            const reviewTextContainer = card.find('.review-text');

            const currentMode = button.data('mode') || 'original';

            if (currentMode === 'translation') {
                const originalText = button.data('original-text');
                reviewTextContainer.text(originalText);
                button.text('View Translation');
                button.data('mode', 'original');
                return;
            }

            if (!button.data('original-text')) {
                button.data('original-text', reviewTextContainer.text());
            }

            const cachedTranslation = button.data('cached-translation');
            if (cachedTranslation) {
                reviewTextContainer.text(cachedTranslation);
                button.text('View Original');
                button.data('mode', 'translation');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/{{ app()->getLocale() }}/review/translation`,
                type: 'POST',
                data: {
                    review_id: reviewId,
                    language_id: languageId,
                    type: type
                },
                success: function (response) {
                    if (response.translated) {
                        reviewTextContainer.text(response.translated);
                        button.text('View Original');
                        button.data('cached-translation', response.translated);
                        button.data('mode', 'translation');
                    } else {
                        alert('Translation not available.');
                        button.text('View Translation');
                        button.data('mode', 'original');
                    }
                },
                error: function () {
                    alert('Error fetching translation.');
                    button.text('View Translation');
                    button.data('mode', 'original');
                }
            });
        });
    });
</script>

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
        sortSelect.addEventListener('change', () => fetchReviews());
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

    $(document).on('click', '#reviews-list-container .pagination a', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            fetchReviews(url);
        }
    });

    function updateClearButtonVisibility() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (clearBtn) {
            clearBtn.style.display = anyChecked ? 'inline' : 'none';
        }
    }

    function fetchReviews(customUrl) {
        const selectedStars = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        const sortValue = sortSelect ? sortSelect.value : 'recent';

        container.style.opacity = '0.5';

        const url = new URL(customUrl || window.location.href);
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
            window.history.pushState({}, '', url.toString());
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
@endpush
