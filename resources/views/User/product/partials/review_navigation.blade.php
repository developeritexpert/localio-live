<div class="compari_pck_innr">
    @foreach($reviews as $index => $review)
        <div class="compari_card_top {{ $index == 1 ? 'compari_btm' : '' }}">
            <div class="compari-img-txt d-flex">
                <div class="compari-crd-img">
                    <img src="{{ asset('front/img/' . ($review->rating >= 4 ? 'john-plus.png' : 'john-minus.png')) }}"
                        alt="{{ $review->user_name ?? 'Reviewer' }}">
                </div>
                <div class="compari-txt">
                    <h6>{{ $review->user_name ?? 'Anonymous' }}</h6>
                    <p class="m-0">{{ $review->user_title ?? 'Customer' }}</p>
                </div>
            </div>
            <p class="compari_p">"{{ Str::limit($review->content ?? $review->translations->where('locale', app()->getLocale())->first()->content ?? 'No review content', 200) }}"</p>
            <a href="javascript:void(0)" class="btn-see-full" data-review-id="{{ $review->id }}">See full review</a>

            @if($index == 1)
                <div class="compari_tabi">
                    <span class="page-indicator">{{ $currentPage }}/{{ $totalPages }}</span>
                    <a href="javascript:void(0)" class="review-nav" data-direction="next" data-business="{{ $business->id }}" data-type="{{ $type }}" data-page="{{ $currentPage }}">
                        Next <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            @endif
        </div>
    @endforeach
</div>
