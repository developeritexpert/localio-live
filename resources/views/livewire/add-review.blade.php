<div>
    @if($show)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050; overflow-y: auto;">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
                    
                    <!-- Top header with business info and close button -->
                    <div class="modal-header border-0 px-3 pt-3 pb-2 px-md-4 pt-md-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2 gap-sm-3">
                            <div style="width: 38px; height: 38px; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <img src="{{ asset($businessIcon ?? 'front/img/big-asana.png') }}" alt="{{ $businessName }}" style="width: 100%; height: 100%; object-fit: contain;">
                            </div>
                            <div>
                                <h4 class="m-0 fw-bold header-title-responsive" style="color: #002655; font-size: 16px;">Reviewing {{ $businessName }}</h4>
                                <span class="text-muted d-none d-sm-inline" style="font-size: 11px;">Share your experience with the community</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" wire:click="$set('show', false)" style="box-shadow: none; font-size: 12px;"></button>
                    </div>

                    <div class="modal-body p-3 p-md-4">
                        <div class="row g-4">
                            
                            <!-- Left Column: Review Form Wizard -->
                            <div class="col-12 col-lg-8 review-left-col pe-lg-4">
                                
                                @if($step === 1)
                                    <!-- Step 1: Ratings and Recommendation -->
                                    <div class="step-content" wire:key="step-1">
                                        <h5 class="fw-bold mb-3" style="color: #002655; font-size: 14px;">Rate the following:</h5>
                                        
                                         <div class="row">
                                             @foreach($criteria as $criterion)
                                                 @php
                                                     $cId = $criterion['id'];
                                                     $value = $criteriaRatings[$cId] ?? 0;
                                                 @endphp
                                                 <div class="mb-3 col-md-6">
                                                     <label class="form-label fw-semibold text-secondary" style="font-size: 13px; margin-bottom: 4px;">
                                                         {{ $criterion['name'] }}:
                                                     </label>
                                                     <div class="d-flex align-items-center gap-1 star-rating mt-1" data-rating-name="criteria_{{ $cId }}">
                                                         @for ($i = 1; $i <= 5; $i++)
                                                             <i class="fa fa-star star-item {{ $i <= $value ? 'filled' : '' }}"
                                                                data-value="{{ $i }}"
                                                                wire:click="$set('criteriaRatings.{{ $cId }}', {{ $i }})"
                                                                style="cursor: pointer; transition: all 0.15s ease-in-out;"></i>
                                                         @endfor
                                                     </div>
                                                     @error('criteriaRatings.' . $cId)
                                                         <small class="text-danger d-block mt-1" style="font-size: 11px;">Rating is required.</small>
                                                     @enderror
                                                 </div>
                                             @endforeach
                                         </div>

                                         <hr class="my-3">

                                         <div class="mb-3">
                                             <label class="form-label fw-bold text-secondary mb-2" style="font-size: 13px;">Would you recommend this business?</label>
                                             <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-4">
                                                 <div class="form-check custom-radio">
                                                     <input class="form-check-input" type="radio" name="recommend" id="recommend_yes" value="1" wire:model="recommend" style="cursor: pointer;">
                                                     <label class="form-check-label fw-semibold" for="recommend_yes" style="cursor: pointer; color: #1e3050; font-size: 13px;">
                                                         Yes, I recommend
                                                     </label>
                                                 </div>
                                                 <div class="form-check custom-radio">
                                                     <input class="form-check-input" type="radio" name="recommend" id="recommend_no" value="0" wire:model="recommend" style="cursor: pointer;">
                                                     <label class="form-check-label fw-semibold" for="recommend_no" style="cursor: pointer; color: #1e3050; font-size: 13px;">
                                                         No, I do not recommend
                                                     </label>
                                                 </div>
                                             </div>
                                             @error('recommend')
                                                 <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small>
                                             @enderror
                                         </div>

                                         <hr class="my-3">

                                         <div class="d-flex justify-content-end mt-3">
                                             <button type="button" class="btn px-4 py-2 text-white fw-bold w-100 w-sm-auto" wire:click="goToStep2" style="background-color: #06498b; border-radius: 30px; font-size: 13px; transition: background 0.2s;">
                                                 Continue <i class="fas fa-arrow-right ms-2" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>

                                @elseif($step === 2)
                                    <!-- Step 2: Title and Comment -->
                                    <div class="step-content" wire:key="step-2">
                                        <h5 class="fw-bold mb-3" style="color: #002655; font-size: 14px;">Share your experience:</h5>

                                        <div class="mb-3">
                                            <label for="title2" class="form-label fw-bold text-secondary" style="font-size: 13px;">Title:</label>
                                            <input type="text" id="title2" class="form-control px-3 py-2" wire:model.defer="title2" placeholder="Give your review a title" style="border-radius: 6px; border: 1px solid #ced4da; font-size: 13px;">
                                            @error('title2') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="comment" class="form-label fw-bold text-secondary" style="font-size: 13px;">Comment:</label>
                                            <textarea id="comment" class="form-control px-3 py-2" wire:model.defer="comment" rows="4" placeholder="Share your experience..." style="border-radius: 6px; border: 1px solid #ced4da; font-size: 13px;"></textarea>
                                            @error('comment') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                         <hr class="my-3">
                                         <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                             <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-semibold w-50 w-sm-auto" wire:click="setStep(1)" style="border-radius: 30px; font-size: 13px; color: #4a5568 !important; border: 1px solid #cbd5e0 !important; background-color: #ffffff !important; transition: all 0.2s;">
                                                 <i class="fas fa-arrow-left me-1 me-sm-2" style="font-size: 11px;"></i> Back
                                             </button>
                                             <button type="button" class="btn px-4 py-2 text-white fw-bold w-50 w-sm-auto" wire:click="goToStep3" style="background-color: #06498b; border-radius: 30px; font-size: 13px; transition: background 0.2s;">
                                                 Continue <i class="fas fa-arrow-right ms-1 ms-sm-2" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>

                                @elseif($step === 3)
                                    <!-- Step 3: Pros and Cons (Optional) -->
                                    <div class="step-content" wire:key="step-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold m-0" style="color: #002655; font-size: 14px;">Pros & Cons (Optional):</h5>
                                            <button type="button" class="btn btn-link text-decoration-none fw-bold p-0" wire:click="skipOptional" style="color: #06498b; font-size: 13px;">Skip & Finish</button>
                                        </div>

                                        <div class="mb-3">
                                            <label for="pros" class="form-label fw-bold text-secondary" style="font-size: 13px;">Pros (optional):</label>
                                            <textarea id="pros" class="form-control px-3 py-2" wire:model.defer="pros" rows="3" placeholder="What did you like about it?" style="border-radius: 6px; border: 1px solid #ced4da; font-size: 13px;"></textarea>
                                            @error('pros') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="cons" class="form-label fw-bold text-secondary" style="font-size: 13px;">Cons (optional):</label>
                                            <textarea id="cons" class="form-control px-3 py-2" wire:model.defer="cons" rows="3" placeholder="What could be improved?" style="border-radius: 6px; border: 1px solid #ced4da; font-size: 13px;"></textarea>
                                            @error('cons') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                         <hr class="my-3">
                                         <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                             <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-semibold w-50 w-sm-auto" wire:click="setStep(2)" style="border-radius: 30px; font-size: 13px; color: #4a5568 !important; border: 1px solid #cbd5e0 !important; background-color: #ffffff !important; transition: all 0.2s;">
                                                 <i class="fas fa-arrow-left me-1" style="font-size: 11px;"></i> Back
                                             </button>
                                             <button type="button" class="btn px-4 py-2 text-white fw-bold w-50 w-sm-auto" wire:click="submit" wire:loading.attr="disabled" style="background-color: #06498b; border-radius: 30px; font-size: 13px; transition: background 0.2s;">
                                                 Save & Finish <i class="fas fa-paper-plane ms-1" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>
                                @endif

                            </div>

                            <!-- Right Column: Sidebar Progress Panel (Capterra style) -->
                            <div class="col-lg-4 ps-lg-4 d-none d-lg-block">
                                <div class="p-4 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; position: sticky; top: 10px;">
                                    <h5 class="fw-bold mb-3" style="color: #002655; font-size: 14px;">Review Progress</h5>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        <!-- Step 1 Indicator -->
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; border-radius: 50%; font-size: 12px;
                                                background: {{ $step >= 1 ? '#06498b' : '#e2e8f0' }};
                                                color: {{ $step >= 1 ? '#ffffff' : '#64748b' }};">
                                                @if($step > 1) <i class="fas fa-check" style="font-size: 10px;"></i> @else 1 @endif
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size: 13px; color: {{ $step === 1 ? '#06498b' : '#334155' }};">My Experience</span>
                                                <small class="text-muted" style="font-size: 11px;">Ratings & Recommendation</small>
                                            </div>
                                        </div>

                                        <!-- Step 2 Indicator -->
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; border-radius: 50%; font-size: 12px;
                                                background: {{ $step >= 2 ? '#06498b' : '#e2e8f0' }};
                                                color: {{ $step >= 2 ? '#ffffff' : '#64748b' }};">
                                                @if($step > 2) <i class="fas fa-check" style="font-size: 10px;"></i> @else 2 @endif
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size: 13px; color: {{ $step === 2 ? '#06498b' : '#334155' }};">Product Review</span>
                                                <small class="text-muted" style="font-size: 11px;">Title & Comments</small>
                                            </div>
                                        </div>

                                        <!-- Step 3 Indicator -->
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; border-radius: 50%; font-size: 12px;
                                                background: {{ $step >= 3 ? '#06498b' : '#e2e8f0' }};
                                                color: {{ $step >= 3 ? '#ffffff' : '#64748b' }};">
                                                3
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size: 13px; color: {{ $step === 3 ? '#06498b' : '#334155' }};">Pros & Cons</span>
                                                <small class="text-muted" style="font-size: 11px;">Optional details</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-top" style="font-size: 11px; color: #64748b; line-height: 1.5;">
                                        <i class="fas fa-lightbulb text-warning me-2"></i><strong>Tips for writing reviews:</strong> Please be honest and specific. Contrast the software with other tools you have used to help other buyers.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .custom-radio .form-check-input:checked {
                background-color: #06498b;
                border-color: #06498b;
            }
            .star-item {
                color: #ffe896 !important;
                font-size: 24px !important;
                margin-right: 4px;
            }
            .star-item.filled,
            .star-item.js-hovered {
                color: #ffb300 !important;
            }
            @media (max-width: 991.98px) {
                .modal-dialog {
                    margin: 10px;
                    max-width: calc(100% - 20px);
                }
                .review-left-col {
                    border-right: none !important;
                }
            }
            @media (max-width: 575.98px) {
                .star-item {
                    font-size: 20px !important;
                }
                .header-title-responsive {
                    font-size: 14px !important;
                }
            }
        </style>
    @endif
</div>
