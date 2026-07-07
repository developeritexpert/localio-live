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
    @else
        <div class="p-4 text-center text-muted">
            <p>No reviews found matching the selected criteria.</p>
        </div>
    @endif
</div>

<div class="pagination-wrap mt-4">
    {{ $reviews->links('pagination::bootstrap-4') }}
</div>
