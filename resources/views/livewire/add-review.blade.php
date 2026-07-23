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
                                <h4 class="m-0 fw-bold header-title-responsive" style="color: #002655; font-size: 16px; line-height:1.2 !important;">Reviewing {{ $businessName }}</h4>
                                <span class="text-muted d-none d-sm-inline" style="font-size: 12px;">Share your experience with the community</span>
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
                                        <h5 class="mb-3" style="color: #002347; font-weight:600; font-size: 14px;">Rate the following:</h5>
                                        
                                         <div class="row">
                                             @foreach($criteria as $criterion)
                                                 @php
                                                     $cId = $criterion['id'];
                                                     $value = $criteriaRatings[$cId] ?? 0;
                                                 @endphp
                                                 <div class="mb-3 col-md-6">
                                                     <label class="form-label fw-semibold " style="font-size: 12px; color:#444444; margin-bottom: 4px;">
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
                                             <label class="form-label  mb-2" style="font-size: 14px; color:#002347; font-weight:600">Would you recommend this business?</label>
                                             <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-4">
                                                 <div clm-0 fw-bold header-title-responsiveass="form-check custom-radio">
                                                     <input class="form-check-input" type="radio" name="recommend" id="recommend_yes" value="1" wire:model="recommend" style="cursor: pointer;">
                                                     <label class="form-check-label fw-semibold" for="recommend_yes" style="cursor: pointer; color: #444444; font-size: 12px;">
                                                         Yes, I recommend
                                                     </label>
                                                 </div>
                                                 <div class="form-check custom-radio">
                                                     <input class="form-check-input" type="radio" name="recommend" id="recommend_no" value="0" wire:model="recommend" style="cursor: pointer;">
                                                     <label class="form-check-label fw-semibold" for="recommend_no" style="cursor: pointer; color: #444444; font-size: 12px;">
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
                                        <h5 class=" mb-3" style="color: #002347; font-weight:600; font-size: 14px;">Share your experience:</h5>

                                        <div class="form-floating mb-3">
                                            <input type="text" id="title2" class="form-control" wire:model.defer="title2" placeholder="Title">
                                            <label for="title2">Title</label>
                                            @error('title2') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="form-floating mb-3">
                                            <textarea id="comment" class="form-control" wire:model.defer="comment" placeholder="Comment" style="height: 120px;"></textarea>
                                            <label for="comment">Comment</label>
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
                                            <h5 class=" m-0" style="color: #002347; font-weight:600; font-size: 14px;">Pros & Cons (Optional):</h5>
                                            <button type="button" class=" step_3_btn btn btn-link text-decoration-none f p-0" wire:click="skipOptional" style="color: #002347; font-weight:500; font-size: 12px;">Skip & Finish</button>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <textarea id="pros" class="form-control" wire:model.defer="pros" placeholder="Pros (optional)" style="height: 100px;"></textarea>
                                            <label for="pros">Pros (optional)</label>
                                            @error('pros') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="form-floating mb-3">
                                            <textarea id="cons" class="form-control" wire:model.defer="cons" placeholder="Cons (optional)" style="height: 100px;"></textarea>
                                            <label for="cons">Cons (optional)</label>
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
                                    <h5 class=" mb-3" style="color: #002347; font-weight:600; font-size: 14px;">Review Progress</h5>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        <!-- Step 1 Indicator -->
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; border-radius: 50%; font-size: 12px;
                                                background: {{ $step >= 1 ? '#06498b' : '#e2e8f0' }};
                                                color: {{ $step >= 1 ? '#ffffff' : '#002347' }};">
                                                @if($step > 1) <i class="fas fa-check" style="font-size: 10px;"></i> @else 1 @endif
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size: 13px; color: {{ $step === 1 ? '#06498b' : '#002347' }};">My Experience</span>
                                                <small class="text-muted" style="font-size: 12px;">Ratings & Recommendation</small>
                                            </div>
                                        </div>

                                        <!-- Step 2 Indicator -->
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; border-radius: 50%; font-size: 12px;
                                                background: {{ $step >= 2 ? '#06498b' : '#e2e8f0' }};
                                                color: {{ $step >= 2 ? '#ffffff' : '#002347' }};">
                                                @if($step > 2) <i class="fas fa-check" style="font-size: 10px;"></i> @else 2 @endif
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size: 13px; color: {{ $step === 2 ? '#06498b' : '#002347' }};">Product Review</span>
                                                <small class="text-muted" style="font-size: 12px;">Title & Comments</small>
                                            </div>
                                        </div>

                                        <!-- Step 3 Indicator -->
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; border-radius: 50%; font-size: 12px;
                                                background: {{ $step >= 3 ? '#06498b' : '#e2e8f0' }};
                                                color: {{ $step >= 3 ? '#ffffff' : '#002347' }};">
                                                3
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size: 13px; color: {{ $step === 3 ? '#06498b' : '#002347' }};">Pros & Cons</span>
                                                <small class="text-muted" style="font-size: 12px;">Optional details</small>
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
            .modal-content .step-content  .step_3_btn:hover{
                text-decoration:underline !important;
                background-color:unset;
                }
            .custom-radio .form-check-input:checked {
                background-color: #06498b;
                border-color: #06498b;
            }
            .star-item {
                color: #ffe896 !important;
                font-size: 16px !important;
                margin-right: 4px;
            }
            .star-item.filled,
            .star-item.js-hovered {
                color: #ffb300 !important;
            }
            .form-floating > .form-control:focus ~ label,
            .form-floating > .form-control:not(:placeholder-shown) ~ label,
            .form-floating > textarea:focus ~ label,
            .form-floating > textarea:not(:placeholder-shown) ~ label {
                opacity: 1 !important;
                transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem) !important;
                color: #06498b !important;
                font-weight: 600 !important;
                background-color: #ffffff !important;
                padding: 0 4px !important;
                height: auto !important;
            }
            .form-floating > label {
                padding: 0.75rem 0.75rem;
                font-size: 13px;
                color: #64748b;
            }
            .form-floating > .form-control,
            .form-floating > textarea {
                border-radius: 8px !important;
                border: 1px solid #cbd5e0 !important;
                font-size: 13px !important;
            }
            .form-floating > .form-control:focus,
            .form-floating > textarea:focus {
                border-color: #06498b !important;
                box-shadow: 0 0 0 3px rgba(6, 73, 139, 0.15) !important;
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
