<div>
    @if($show)
        <div class="modal show d-block mod_detail_pg" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050; overflow-y: auto;">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
                    
                    <!-- Top header with Left Headline & Business Info / Close Button -->
                    <!-- <div class="modal-header border-0 px-3 pt-3 pb-0  px-md-4 pt-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="margin-bottom:0;">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 22px; height: 22px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background-color: #f1f5f9;">
                                    <img src="{{ asset($businessIcon ?? 'front/img/big-asana.png') }}" alt="{{ $businessName }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <span class="fw-bold" style="color: #002655; font-size: 14px;">{{ $businessName }}</span>
                            </div>
                            <h4 class="m-0 fw-bold header-title-responsive" style="color: #002655; font-size: 18px; line-height:1.2 !important;">
                                Share your experience
                            </h4>
                        </div>

                        <div>
                            <button type="button" class="btn-close" wire:click="closeModal" style="box-shadow: none; font-size: 12px;"></button>
                        </div>
                    </div> -->

                    <div class="modal-body p-3 p-md-4 pt-2">
                        <div>
                            <button type="button" class="btn-close" wire:click="closeModal" style="box-shadow: none; font-size: 12px;"></button>
                        </div>
                        <div class="row g-4">
                            
                            <!-- Left Column: Review Form Wizard -->
                            <div class="col-12 col-lg-8 review-left-col pe-lg-4">
                                <div class="modal-header border-0  p-0  d-flex justify-content-between align-items-center flex-wrap gap-2" style="margin-bottom:20px;">
                                    <div class="d-flex flex-column gap-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 22px; height: 22px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background-color: #f1f5f9;">
                                                <img src="{{ asset($businessIcon ?? 'front/img/big-asana.png') }}" alt="{{ $businessName }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <span class="fw-bold" style="color: #002655; font-size: 14px;">{{ $businessName }}</span>
                                        </div>
                                        <h2 class="m-0 fw-bold header-title-responsive" style="color: #002655; font-size: 22px; line-height:1.2 !important;">
                                            @if($step === 3)
                                                Pros & cons (optional)
                                            @else 
                                                @if($step === 2)
                                                    Write your review
                                                @else
                                                    Share your experience
                                                @endif
                                            @endif
                                        </h2>
                                    </div>

                                    
                                </div>
                                @if($step === 1)
                                    <!-- Step 1: Overall Ratings and Recommendation -->
                                    <div class="step-content " wire:key="step-1">
                                        <!-- <h5 class="mb-3" style="color: #002347; font-weight:600; font-size: 14px;">Overall ratings</h5> -->
                                        
                                         <div class="row">
                                             @foreach($criteria as $criterion)
                                                 @php
                                                     $cId = $criterion['id'];
                                                     $value = $criteriaRatings[$cId] ?? 0;
                                                 @endphp
                                                 <div class="mb-3 col-md-6">
                                                     <label class="form-label fw-semibold " style="font-size: 12px; color:#444444; margin-bottom: 4px;">
                                                         {{ $criterion['name'] }}
                                                     </label>
                                                     <div class="d-flex align-items-center gap-1 star-rating mt-1" data-rating-name="criteria_{{ $cId }}">
                                                         @for ($i = 1; $i <= 5; $i++)
                                                             <i class="star-item {{ $i <= $value ? 'fas fa-star filled' : 'far fa-star' }}"
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
                                             <label class="form-label mb-2" style="font-size: 14px; color:#002347; font-weight:600">
                                                 Would you recommend {{ $businessName }}?
                                             </label>
                                             <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-4">
                                                 <div class="form-check custom-radio">
                                                     <input class="form-check-input" type="radio" name="recommend" id="recommend_yes" value="1" wire:model="recommend" style="cursor: pointer;">
                                                     <label class="form-check-label fw-semibold" for="recommend_yes" style="cursor: pointer; color: #444444; font-size: 12px;">
                                                         Yes, I recommend it
                                                     </label>
                                                 </div>
                                                 <div class="form-check custom-radio">
                                                     <input class="form-check-input" type="radio" name="recommend" id="recommend_no" value="0" wire:model="recommend" style="cursor: pointer;">
                                                     <label class="form-check-label fw-semibold" for="recommend_no" style="cursor: pointer; color: #444444; font-size: 12px;">
                                                         No, I don't recommend it
                                                     </label>
                                                 </div>
                                             </div>
                                             @error('recommend')
                                                 <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small>
                                             @enderror
                                         </div>

                                         <hr class="my-3">

                                         <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                             <button type="button" class="btn out_ln_btn btn-outline-secondary w-50 w-sm-auto" wire:click="closeModal" style="font-weight:500; padding:12px 25px; border-radius: 30px; font-size: 14px; color: #002347; border: 1px solid #06498b !important; background-color: #ffffff;">
                                                 Cancel
                                             </button>
                                             <button type="button" class="btn text-white w-50 w-sm-auto" wire:click="goToStep2" style="padding:12px 25px; font-weight:500; background-color: #06498b; border-radius: 30px; font-size: 14px; transition: background 0.2s;">
                                                 Continue <i class="fas fa-arrow-right ms-1 ms-sm-2" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>

                                @elseif($step === 2)
                                    <!-- Step 2: Title and Review -->
                                    <div class="step-content step2" wire:key="step-2">

                                        <div class="form-floating text-inpt mb-3">
                                            <input type="text" id="title2" class="form-control" wire:model.defer="title2" placeholder="Title">
                                            <label for="title2">Title</label>
                                            @error('title2') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="form-floating mb-3">
                                            <textarea id="comment" class="form-control" wire:model.defer="comment" placeholder="Your review" style="height: 120px;"></textarea>
                                            <label for="comment">Your review</label>
                                            @error('comment') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                         <hr class="my-3">
                                         <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                             <button type="button" class="btn out_ln_btn btn-outline-secondary  w-50 w-sm-auto" wire:click="setStep(1)" style=" font-weight:500; padding:12px 25px; border-radius: 30px; font-size: 14px; color: #002347; border: 1px solid #06498b !important; background-color: #ffffff ; ">
                                                 <i class="fas fa-arrow-left me-1 me-sm-2" style="font-size: 11px;"></i> Back
                                             </button>
                                             <button type="button" class="btn text-white  w-50 w-sm-auto" wire:click="submitStep2" style="padding:12px 25px; font-weight:500; background-color: #06498b; border-radius: 30px; font-size: 14px; ">
                                                 Submit review <i class="fas fa-arrow-right ms-1 ms-sm-2" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>

                                @elseif($step === 3)
                                    <!-- Step 3: Pros & Cons (Optional) -->
                                    <div class="step-content" wire:key="step-3">

                                        <div class="form-floating mb-3">
                                            <textarea id="pros" class="form-control" wire:model.defer="pros" placeholder="Pros (What you liked)" style="height: 100px;"></textarea>
                                            <label for="pros">Pros (What you liked)</label>
                                            @error('pros') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="form-floating mb-3">
                                            <textarea id="cons" class="form-control" wire:model.defer="cons" placeholder="Cons (What you disliked)" style="height: 100px;"></textarea>
                                            <label for="cons">Cons (What you disliked)</label>
                                            @error('cons') <small class="text-danger d-block mt-1" style="font-size: 11px;">{{ $message }}</small> @enderror
                                        </div>

                                         <hr class="my-3">
                                         <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                             <button type="button" class="btn out_ln_btn btn-outline-secondary  w-50 w-sm-auto" wire:click="setStep(2)" style=" padding:12px 25px; font-weight:500; border-radius: 30px; font-size: 14px; color: #002347; border: 1px solid #06498b; background-color: #ffffff ; ">
                                                 <i class="fas fa-arrow-left me-1" style="font-size: 11px;"></i> Back
                                             </button>
                                             <button type="button" class="btn  text-white  w-50 w-sm-auto" wire:click="submit" wire:loading.attr="disabled" style="padding:12px 25px; font-weight:500; background-color: #06498b; border-radius: 30px; font-size: 14px; ">
                                                 Submit pros & cons <i class="fas fa-arrow-right ms-1" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>
                                @endif

                            </div>

                            <!-- Right Column: Sidebar Progress Panel -->
                            <div class="col-lg-4   d-lg-block" style="display:flex !important; align-items:center;">
                                <div class="p-3 p-md-4 rounded-3 mt-0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; position: sticky; top: 0;">
                                    <!-- <h5 class="mb-3" style="color: #002347; font-weight:600; font-size: 14px;">Review progress</h5> -->
                                    
                                    <div class="d-flex flex-column gap-3 d-none">
                                        <!-- Step 1 Indicator -->
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; border-radius: 50%; font-size: 12px;
                                                background: {{ $step >= 1 ? '#06498b' : '#e2e8f0' }};
                                                color: {{ $step >= 1 ? '#ffffff' : '#002347' }};">
                                                @if($step > 1) <i class="fas fa-check" style="font-size: 10px;"></i> @else 1 @endif
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size: 13px; color: {{ $step === 1 ? '#06498b' : '#002347' }};">Ratings</span>
                                                <small class="text-muted" style="font-size: 12px;">Rate your experience</small>
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
                                                <span class="fw-semibold d-block" style="font-size: 13px; color: {{ $step === 2 ? '#06498b' : '#002347' }};">Your Review</span>
                                                <small class="text-muted" style="font-size: 12px;">Title & review</small>
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

                                    <!-- Tips Section (Dynamic according to Step) -->
                                    <div class="" style="font-size: 12px; color: #64748b; line-height: 1.5;">
                                        @if($step === 1)
                                            <div class="mb-1" style="font-weight: 600; color: #002347;">
                                                <i class="fas fa-lightbulb text-warning me-1"></i> Rate honestly
                                            </div>
                                            <p class="m-0 text-muted" style="font-size: 11px;">Your ratings should reflect your overall experience with this business.</p>
                                        @elseif($step === 2)
                                            <div class="mb-1" style="font-weight: 600; color: #002347;">
                                                <i class="fas fa-lightbulb text-warning me-1"></i> Be specific
                                            </div>
                                            <p class="m-0 text-muted" style="font-size: 11px;">Include details that may help others make an informed decision.</p>
                                        @elseif($step === 3)
                                            <div class="fw-bold text-dark mt-1">What stood out?</div>
                                            <p class="m-0 text-muted" style="font-size: 11px;">List the biggest strengths and weaknesses you experienced.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .step-content.step2 .text-inpt>.form-control:not(:placeholder-shown){
                padding:12px 20px !important;
            
            }
            .step-content.step2  .form-floating.text-inpt>.form-control{
                height: unset !important;
                min-height: unset !important;
                padding:12px 20px !important;
            }


            .modal-content .step-content .step_3_btn:hover{
                text-decoration:underline !important;
                background-color:unset;
            }
            .custom-radio .form-check-input:checked {
                background-color: #06498b;
                border-color: #06498b;
            }
            .star-rating .star-item {
                color: #70757a !important;
                font-size: 22px !important;
                margin-right: 4px;
            }
            .star-rating .star-item.filled,
            .star-rating .star-item.js-hovered {
                color: #ff5722 !important;
            }
            .form-floating > .form-control:focus ~ label,
            .form-floating > .form-control:not(:placeholder-shown) ~ label,
            .form-floating > textarea:focus ~ label,
            .form-floating > textarea:not(:placeholder-shown) ~ label {
                opacity: 1 !important;
                transform: scale(0.85) translateY(-6px) translateX(0.15rem) !important;
                color: #80868b !important;
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
            .modal-dialog .modal-body .out_ln_btn:hover{
              background-color:#06498b !important;
              color:#fff !important;
            }
            .modal-body{
                line-height: 1;
                position: relative;
            }
            .modal-content .modal-header {
                position: relative;
            }

          .modal-content button.btn-close {
                padding: 10px;
                position: absolute;
                z-index: 99;
                top: 20px;
                right: 20px;
                border-radius: 50%;
            }
             .modal-content button.btn-close:hover{
                background-color:#f3f4f6;
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
                .star-rating .star-item {
                    font-size: 22px !important;
                }
                .header-title-responsive {
                    font-size: 14px !important;
                }
            }
        </style>

        <script>
            (function() {
                function setupStarHovers() {
                    document.querySelectorAll('.star-rating').forEach(function(group) {
                        const stars = group.querySelectorAll('.star-item');
                        stars.forEach(function(star, index) {
                            star.onmouseenter = function() {
                                stars.forEach(function(s, idx) {
                                    if (idx <= index) {
                                        s.classList.remove('far');
                                        s.classList.add('fas', 'js-hovered');
                                    } else {
                                        s.classList.remove('fas', 'js-hovered');
                                        s.classList.add('far');
                                    }
                                });
                            };
                        });
                        group.onmouseleave = function() {
                            stars.forEach(function(s) {
                                s.classList.remove('js-hovered');
                                if (s.classList.contains('filled')) {
                                    s.classList.remove('far');
                                    s.classList.add('fas');
                                } else {
                                    s.classList.remove('fas');
                                    s.classList.add('far');
                                }
                            });
                        };
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupStarHovers);
                } else {
                    setupStarHovers();
                }
                document.addEventListener('livewire:load', setupStarHovers);
                if (window.Livewire) {
                    Livewire.hook('message.processed', setupStarHovers);
                }
            })();
        </script>
    @endif
</div>
