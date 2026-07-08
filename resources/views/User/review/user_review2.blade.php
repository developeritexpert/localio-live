@extends('user_layout.master')
@section('content')

    <section class="banner_sec help-cntr-bnr inr-bnr dark" style="background-color: #003F7D;">
        <div class="bubble-wrp">
            <img src="https://localio.com/public/front/img/small-bnnr-bg.png" alt="">
        </div>
        <div class="banner_content">
            <div class="container">
                <div class="banner_row review-bnnr" data-aos="fade-up" data-aos-duration="1000">
                    <div class="banner_text_col">
                        <div class="banner_content_inner bnr_inr_contnt">
                            <h1>What They Say</h1>
                            <p class="expert-p">Each story here is a mirror — reflecting courage, transformation, and
                                the quiet power of inner growth.</p>
                        </div>
                    </div>
                    <div class="banner-image-col">
                        <img src="{{ asset('front/img/banner1-img.png') }}" alt="banner-iamge">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- working section -->
    <!-- review section -->
    <!-- review section -->
    <section class="review-section" style="overflow: visible !important;">
        <style>
            .review-sidebar-sticky {
                position: sticky !important;
                position: -webkit-sticky !important;
                top: 125px !important;
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
            .review-section, .review-row-prod-inr, .review-col {
                overflow: visible !important;
            }

            /* Responsiveness for Review Section */
            @media (max-width: 991px) {
                .review-row-prod-inr {
                    display: block !important;
                }
                .review-sidebar-sticky {
                    background: #ffffff;
                    border: 1px solid #f2f4f8;
                    border-radius: 12px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
                    position: relative !important;
                    top: 0 !important;
                    margin-bottom: 30px !important;
                    padding: 20px !important;
                }
            }
            @media (max-width: 575px) {
                .review-sidebar-sticky h2 {
                    font-size: 22px !important;
                }
            }
        </style>
        <div class="container">
            <div class="row review-row review-row-prod-inr">
                <!-- Left Column (Sticky Sidebar with info/filters) -->
                <div class="col-lg-4">
                    <div class="review-col review-sidebar-sticky">
                        
                        <!-- Localio Reviews Header -->
                        <h2 style="font-size: 26px; font-weight: 700; margin-bottom: 20px; color: #1e3050; line-height: 1.3;">
                            Localio {{ $business->translations->first()->name }} Reviews
                        </h2>

                        <!-- Sort by Dropdown -->
                        <div class="selct_box" style="margin-bottom: 25px;">
                            <form method="GET" id="sort-form" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                <label for="rating-select" style="font-size: 15px; font-weight: 600; color: #555; margin: 0; white-space: nowrap;">Sort by:</label>
                                <select class="form-select" id="rating-select" name="sort" style="padding: 5px 30px 5px 10px; font-size: 14px; border-radius: 6px; cursor: pointer; width: auto; min-width: 140px; border: 1px solid #ced4da;">
                                    <option value="recent" {{ request('sort') == 'recent' || !request('sort') ? 'selected' : '' }}>Most Recent</option>
                                    <option value="best" {{ request('sort') == 'best' ? 'selected' : '' }}>Best Rating</option>
                                    <option value="high-to-low" {{ request('sort') == 'high-to-low' ? 'selected' : '' }}>High to Low</option>
                                    <option value="low-to-high" {{ request('sort') == 'low-to-high' ? 'selected' : '' }}>Low to High</option>
                                </select>
                            </form>
                        </div>

                        <!-- Overall Rating -->
                        <div class="rating-filter-header" style="margin-bottom: 20px;">
                            <h4 style="font-size: 16px !important; font-weight: 500 !important; color: #333 !important; margin-bottom: 8px !important;">Overall rating</h4>
                            <div class="overall-stars" style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-size: 38px; font-weight: 500; color: #000; line-height: 1;">{{ number_format($averageRating, 1) }}</span>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <div style="display: flex; align-items: center; gap: 2px;">
                                        @for ($j = 1; $j <= 5; $j++)
                                            @if ($j <= floor($averageRating))
                                                <i class="fas fa-star text-warning" style="font-size: 16px;"></i>
                                            @elseif ($j - 0.5 <= $averageRating)
                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 16px;"></i>
                                            @else
                                                <i class="far fa-star text-warning" style="font-size: 16px;"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p style="font-size: 13px; margin: 0; color: #777; font-weight: 500;">Based on {{ $totalReviews }} reviews</p>
                                </div>
                            </div>
                        </div>

                        <!-- Filter by Rating Title Row -->
                        <div class="filter-by-title-row" style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 6px;">
                            <span style="font-size: 16px; font-weight: 700; color: #1e3050;">Filter by rating</span>
                            <span class="clear-filters-btn" id="clear-filters" style="display: none; color: #007bff; font-size: 13px; cursor: pointer;">Clear &times;</span>
                        </div>

                        <!-- Star Breakdown Checkboxes -->
                        <div class="review-star-box">
                            <ul class="progress-list" style="list-style: none; padding: 0; margin: 0;">
                                @for ($i = 5; $i >= 1; $i--)
                                    @php
                                        $count = $ratingsCount[$i] ?? 0;
                                        $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                    @endphp
                                    <li class="progress-list-item" style="display: flex; align-items: center; margin-bottom: 10px; gap: 8px;">
                                        <input type="checkbox" class="rating-filter-checkbox" value="{{ $i }}" id="star-check-{{ $i }}" style="cursor: pointer; width: 16px; height: 16px; margin: 0; accent-color: #0056b3;">
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

                <!-- Right Column (Reviews List) -->
                <div class="col-lg-8">
                    <div class="review-col">
                        <div class="review-cntnt-box" style="margin-bottom: 20px;">
                            <div class="review-cntnt-hd">
                                <div class="review-cntnt-hd-top">
                                    <h2>Top Reviews from Around the World</h2>
                                </div>
                            </div>
                            <p><strong>{{ $business->translations->first()->name }}</strong></p>
                        </div>

                        <!-- Review List AJAX Container -->
                        <div id="reviews-list-container">
                            @include('User.review.partials.reviews_list')
                        </div>

                        @livewire('add-review')

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


{{-- Translations Review Script --}}
@push('scripts')
<!-- Load jQuery FIRST -->
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
                // Show original
                const originalText = button.data('original-text');
                reviewTextContainer.text(originalText);
                button.text('View Translation');
                button.data('mode', 'original');
                return;
            }

            // Save original text if not already saved
            if (!button.data('original-text')) {
                button.data('original-text', reviewTextContainer.text());
            }

            // Check if translation was already fetched (optional cache)
            const cachedTranslation = button.data('cached-translation');
            if (cachedTranslation) {
                reviewTextContainer.text(cachedTranslation);
                button.text('View Original');
                button.data('mode', 'translation');
                return;
            }

            // Fetch translation from server
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
                        button.data('cached-translation', response.translated); // Cache it
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

    // Intercept pagination clicks
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

        // Use customUrl (from pagination) or construct one from window.location.href
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
            // Update the browser history url
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

<script>
    $(window).on('load', function() {
        $('body').addClass('ReviewUserPgCls');
    });
</script>
@endpush
