<div>
    <div class="mi_detail">
        <div class="info_fiter_d_g compaign_search">
            <div class="info_fiter_d_g1">
                <p class="m-0">Filter:</p>

                <!-- Rating Filter -->
                <div class="info_for_c_1">
                    <select wire:model.live="filterRating" class="form-select" style="height: auto;">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars & up</option>
                        <option value="3">3 Stars & up</option>
                        <option value="2">2 Stars & up</option>
                        <option value="1">1 Star & up</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="info_for_c_1">
                    <select wire:model.live="filterDate" class="form-select" style="height: auto;">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
            </div>

            <!-- Search -->
            <div class="info_fiter_d_g2">
                <div class="form">
                    <input type="search" wire:model.live.500ms="searchTerm" class="search-box" placeholder="Search by user or review">
                    <button class="btn cta_dark active"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Review List Remains Same -->
    <div class="review_sys">
        @forelse ($this->filteredReviews as $review)

            <div class="review_list">
                <div class="review_part-1">
                    <div class="review_p1_imgs">
                        <div class="review_p1_imgs-1">
                            <div class="img_review">
                                <div class="img_review-1">
                                    <img class="prn_re" src="{{ asset($review->user->profile_image ?? dimage()) }}" alt="">
                                    <div class="img_review-2_1">
                                        <p class="m-0">{{ ($review->user->first_name ?? '') . ' ' . ($review->user->last_name ?? '') ?: 'User Name' }}</p>


                                            {{-- @for ($i = 0; $i < $review->rating; $i++)
                                                <div class="review_star">
                                                    <img src="{{ asset('vender_dashboard/img/star.svg') }}" alt="">
                                                </div>
                                            @endfor --}}

                                            @for($i=1;$i<=5;$i++)
                                                @if ($i <=floor($review->rating))
                                                <i class="fas fa-star text-warning"></i>
                                                @elseif ($i - 0.5 <= $review->rating)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                @else
                                                <i class="far fa-star text-warning"></i>
                                                @endif


                                            @endfor


                                    </div>
                                </div>
                                <div class="img_review-2">
                                    {{ $review->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @php
                            $translation = $review->translations->where('language_id', getCurrentLanguageID())->first();
                            // dd($review->id,$translation);
                        @endphp

                        <div class="review_p1_imgs-2">
                            <span>{{ $translation->title ?? '' }}</span>
                            <p>{{ $translation->description ?? '' }}</p>
                        </div>

                    </div>
                </div>
                {{-- Hiden Input field --}}
               <!-- Feedback Trigger (inside the three-dot dropdown) -->
               <div class="review_part-2">
                <div class="shr_dt dot">

                    <div class="dash-icon">
                        <a href="#" class="hidden-field" wire:click="openModal({{ $review->id }})">
                            <i class="icon ni ni-edit"></i>Answer Review
                        </a>
                    </div>

                </div>
            </div>

            <!-- Feedback Modal -->
            @if($showModal)
            <div class="ModelDiv" >
                <div class="modal-dialog modal-dialog-centered" wire:keydown.escape.window="closeModal">
                    <div class="modal-content">
                        <form wire:submit.prevent="submit">
                            <div class="modal-header">
                                <h5 class="modal-title">Send Feedback</h5>
                                <button type="button" class="btn-close" wire:click="closeModal"></button>
                            </div>
                            <div class="modal-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <div class="mb-3">
                                    <label for="feedbackMessage" class="form-label">Message</label>
                                    <textarea wire:model.defer="message" class="form-control" id="feedbackMessage" rows="4"></textarea>
                                    @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-check">
                                    <input wire:model="isInappropriate" class="form-check-input" type="checkbox" id="inappropriateCheck">
                                    <label class="form-check-label" for="inappropriateCheck">
                                        Mark as inappropriate
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Close</button>
                                <button type="submit" class="btn btn-dark">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

        </div>
    @empty
    <div class="crt_main" style="display:flex;justify-content:center;align-items:center;flex-direction:column;">
        <img src="{{ dashboardDefaultImage() }}" alt="No Favorites" class="img-fluid mb-3" style="width: 280px; height: 280px;">
        <h5 class="text-muted">No Reviews yet.</h5>
     </div>
    @endforelse
</div>
</div>


<script>
window.addEventListener('feedback-sent', () => {
    document.querySelectorAll('[x-data]')[0].__x.$data.open = false;
});
</script>
