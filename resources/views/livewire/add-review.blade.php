<div>
    @if($show)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); transition: opacity 0.3s;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content p-4 rounded shadow-sm">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold">Leave a Review</h5>
                        <button type="button" class="btn-close" wire:click="$set('show', false)"></button>
                    </div>

                    <div class="modal-body">
                        <h6 class="fw-semibold mb-3">Rate the following:</h6>
                        <div class="row">
                    
                            @php
                            $ratings = [
                                'ease_of_use_rating' => $ease_of_use_rating,
                                'value_for_money_rating' => $value_for_money_rating,
                                'customer_service_rating' => $customer_service_rating,
                                'exclusive_service_rating' => $exclusive_service_rating,
                            ];
                        @endphp
                        
                        @foreach($ratings as $name => $value)
                            <div class="mb-3 col-md-6">
                                <label class="form-label">
                                    {{ ucwords(str_replace('_', ' ', $name)) }}:
                                </label>
                                <div class="d-flex align-items-center star-rating" data-rating-name="{{ $name }}">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star text-warning fs-5 me-1 star-item {{ $i <= $value ? 'filled' : '' }}"
                                        data-value="{{ $i }}"
                                        wire:click="$set('{{ $name }}', {{ $i }})"
                                        style="cursor: pointer;"></i>
                                    @endfor
                       
                                </div>
                                <input type="hidden" id="{{ $name }}" value="{{ $value }}">
                                @error($name) <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        @endforeach
                        
                        </div>

                        <hr class="my-4">

                        {{-- Title --}}
                        <div class="mb-3">
                            <label for="title2" class="form-label">Title:</label>
                            <input type="text" id="title2" class="form-control" wire:model.defer="title2" placeholder="Give your review a title">
                            @error('title2') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Comment --}}
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment:</label>
                            <textarea id="comment" class="form-control" wire:model.defer="comment" rows="4" placeholder="Share your experience..."></textarea>
                            @error('comment') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        {{-- Pros & Cons --}}
                           <div class="mb-3">
                            <label for="pros" class="form-label">Pros:</label>
                            <textarea id="pros" class="form-control" wire:model.defer="pros" rows="3" placeholder="What did you like about it?"></textarea>
                            @error('pros') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cons" class="form-label">Cons:</label>
                            <textarea id="cons" class="form-control" wire:model.defer="cons" rows="3" placeholder="What could be improved?"></textarea>
                            @error('cons') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-top-0">
                        <button class="btn btn-outline-secondary" wire:click="$set('show', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="submit" wire:loading.attr="disabled">
                            <i class="fas fa-paper-plane me-1"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
