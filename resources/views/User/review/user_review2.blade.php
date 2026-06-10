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
    <section class="review-section">
        <div class="container">
            <div class="row review-row">
                <div class="col-lg-4">
                    <div class="review-col">

                        {{-- <div class="review-star-box">
                            <ul class="progress-list">
                                @for ($i = 5; $i >= 1; $i--)
                                    @php
                                        $count = $ratingsCount[$i] ?? 0;
                                        $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                    @endphp
                                    <li class="progress-list-item">
                                        <div class="star-div">
                                            <ol class="star-div-ol">
                                                @for ($j = 1; $j <= 5; $j++)
                                                    @if ($j <= $i)
                                                        <li><img src="{{ asset('front/img/star-lft.svg') }}" alt="Star"></li>
                                                    @else
                                                        <li><img src="{{ asset('front/img/empty-star-img.svg') }}" alt="Star"></li>
                                                    @endif
                                                @endfor
                                            </ol>
                                        </div>
                                        <div class="progress-box">
                                            <div class="progress-fill" style="width: {{ $percent }}%;"></div>
                                        </div>
                                        <div class="profres-value">
                                            <p>{{ $percent }}%</p>
                                        </div>
                                    </li>
                                @endfor
                            </ul>
                        </div> --}}

                        <div class="review-star-box">
                            <ul class="progress-list">
                                @for ($i = 5; $i >= 1; $i--)
                                    @php
                                        $count = $ratingsCount[$i] ?? 0;
                                        $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                    @endphp
                                    <li class="progress-list-item">
                                        <div class="star-div">
                                            <ol class="star-div-ol">
                                                @for ($j = 1; $j <= 5; $j++)
                                                    <li>
                                                        @if ($j <= $i)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-warning"></i>
                                                        @endif
                                                    </li>
                                                @endfor
                                            </ol>
                                        </div>
                                        <div class="progress-box">
                                            <div class="progress-fill" style="width: {{ $percent }}%;"></div>
                                        </div>
                                        <div class="profres-value">
                                            <p>{{ $percent }}%</p>
                                        </div>
                                    </li>
                                @endfor
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="review-col">
                        <div class="review-cntnt-box">
                            <div class="review-cntnt-hd">
                                <div class="review-cntnt-hd-top">
                                    <h2>Top Reviews from Around the World</h2>
                                </div>
                                </div>
                                <p><strong>{{$business->translations->first()->name }}</strong></p>
                            </div>

                            {{-- <div class="review-cntnt-btm">
                                @if (isset($reviews) && $reviews->isNotEmpty())
                                    @foreach ($reviews as $review)
                                        <div class="review-card">
                                            <div class="r-crd-hd">
                                                <div class="crd-img">
                                                    @if($review->user && $review->user->profile_image)
                                                    <img src="{{ asset($review->user->profile_image)  }}" class="img-fluid profile-circle" style="width: 65px; height: 65px; object-fit: cover; border-radius: 50%;" alt="User Image">
                                                    @else
                                                    <img src="{{ dimage() }}" class="img-fluid profile-circle" style="width: 65px; height: 65px; object-fit: cover; border-radius: 50%;" alt="Default Image">
                                                    @endif
                                                </div>
                                                <div class="crd-img-txt">
                                                    <h6> @if ($review->user && $review->user->user_type === 'admin')
                                                        {{ $review->public_name ?? 'Public' }}
                                                    @else
                                                        {{ $review->user->first_name ?? 'Anonymous' }}
                                                    @endif</h6>
                                                    <p>{{ $review->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            <div class="crd-stars">
                                                <ul class="star-list">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                    <li>
                                                        <img src="{{ asset('front/img/' . ($i <= $review->rating ? 'star-img.svg' : 'empty-star-img.svg')) }}" alt="Star">
                                                    </li>
                                                @endfor
                                                </ul>
                                                <span class="m-0">{{ number_format($review->rating, 1) }}</span>
                                            </div>
                                            <div class="review-txt">
                                                <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                </p>

                                            </div>
                                            <div class="translaton-txt">
                                                <a href="{{ route('user.product_detail',['locale'=>getCurrentLocale(),'id'=>$business->translations->first()->slug]) }}">View Original</a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div> --}}

                             {{-- Old Star  --}}
                            {{-- Update Star --}}
                            <div class="review-cntnt-btm">
                                @if ($reviews->isNotEmpty())
                                    @foreach ($reviews as $review)
                                        <div class="review-card" data-review-id="{{ $review->id }}">
                                            <div class="r-crd-hd">
                                                <div class="crd-img">
                                                    @if($review->user && $review->user->profile_image)
                                                        <img src="{{ asset($review->user->profile_image) }}" class="img-fluid profile-circle"
                                                             style="width: 65px; height: 65px; object-fit: cover; border-radius: 50%;" alt="User Image">
                                                    @else
                                                        <img src="{{ dimage() }}" class="img-fluid profile-circle"
                                                             style="width: 65px; height: 65px; object-fit: cover; border-radius: 50%;" alt="Default Image">
                                                    @endif
                                                </div>
                                                <div class="crd-img-txt">
                                                    <h6>
                                                        @if ($review->user && $review->user->user_type === 'admin')
                                                            {{ $review->public_name ?? 'Public' }}
                                                        @else
                                                            {{ $review->user->first_name ?? 'Anonymous' }}
                                                        @endif
                                                    </h6>
                                                    <p>{{ $review->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>

                                            {{-- Old Star --}}
                                            {{-- <div class="crd-stars">
                                                <ul class="star-list">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <li>
                                                            <img src="{{ asset('front/img/' . ($i <= $review->rating ? 'star-img.svg' : 'empty-star-img.svg')) }}" alt="Star">
                                                        </li>
                                                    @endfor
                                                </ul>
                                                <span class="m-0">{{ number_format($review->rating, 1) }}</span>
                                            </div> --}}



                                            {{-- Update star --}}
                                            @php
                                                $rating = $review->rating;
                                            @endphp

                                            <div class="crd-stars">
                                                <ul class="star-list">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <li>
                                                            @if ($i <= floor($rating))
                                                                <i class="fas fa-star text-warning"></i>
                                                            @elseif ($i - 0.5 <= $rating)
                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                            @else
                                                                <i class="far fa-star text-warning"></i>
                                                            @endif
                                                        </li>
                                                    @endfor
                                                </ul>
                                                <span class="m-0">{{ number_format($rating, 1) }}</span>
                                            </div>

                                            <div class="review-txt">
                                                <p class="review-text">
                                                    {{ strip_tags($review->translations->firstWhere('language_id', getCurrentLanguageID())?->description ?? $review->description ?? 'No Review Available') }}
                                                </p>
                                            </div>

                                            <div class="translaton-txt">
                                                <a href="javascript:void(0);"
                                                    class="btn-toggle-translation"
                                                    data-mode="translation"
                                                    data-review-id="{{ $review->id }}"
                                                    data-type="original"
                                                    data-language-id="{{ getCurrentLanguageID() }}">
                                                    View Original
                                                </a>

                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            @livewire('add-review')

                            <div class="pagination-wrap mt-4">
                                {{ $reviews->links('pagination::bootstrap-4') }}
                            </div>

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
    $(window).on('load', function() {
        $('body').addClass('ReviewUserPgCls');
    });
</script>
@endpush
