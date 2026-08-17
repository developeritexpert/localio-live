<div>
<style>
    button.btn.btn-sm.rounded-pill.transition-all.d-inline-flex.align-items-center.gap-1.btn-success.text-white.shadow-sm {
        background: #033b74 !important;
    }
    button.btn.btn-sm.rounded-pill.transition-all.d-inline-flex.align-items-center.gap-1.btn-danger.text-white.shadow-sm {
        background: #033b74 !important;
    }
    .recommend-card-btn {
        border: 2px solid #e2e8f0;
        background-color: #ffffff;
        color: #002347;
        border-radius: 30px;
        padding: 12px 28px;
        font-size: 15px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        outline: none;
    }
    .recommend-card-btn:hover {
        border-color: #06498b;
        background-color: #f8fafc;
    }
    .recommend-card-btn.selected {
        border-color: #06498b;
        background-color: #06498b;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(6, 73, 139, 0.25);
    }
        .pro-con-box {
        background-color: #fafafa;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
    }
    .selected-chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #e2e8f0;
    }
    .selected-chip-badge {
        background-color: #003f7d !important;
        color: #ffffff !important;
        border: 1px solid #003f7d !important;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1.3;
    }
    .selected-chip-badge:hover {
        background-color: #002b56 !important;
        color: #ffffff !important;
    }
    .selected-chip-badge i.fa-times {
        font-size: 11px;
        opacity: 0.8;
    }
    .selected-chip-badge:hover i.fa-times {
        opacity: 1;
    }
    .suggestions-chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        max-height: 115px;
        overflow-y: auto;
        padding: 2px 2px 2px 0;
    }
    .suggestions-chips-container::-webkit-scrollbar {
        width: 5px;
    }
    .suggestions-chips-container::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
    }
    .pro-con-chip-btn {
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #334155;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.15s ease;
        outline: none;
        line-height: 1.4;
        text-align: left;
    }
    .pro-con-chip-btn:hover {
        border-color: #003f7d;
        background-color: #f1f5f9;
        color: #003f7d;
    }
    .clear-search-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent !important;
        border: none !important;
        color: #94a3b8 !important;
        cursor: pointer !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 22px !important;
        height: 22px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 13px !important;
        line-height: 1 !important;
        box-shadow: none !important;
        border-radius: 50% !important;
        z-index: 5;
    }
    .clear-search-btn:hover {
        color: #0f172a !important;
        background: #f1f5f9 !important;
    }
</style>

    @if($show)
        <div class="modal show d-block mod_detail_pg" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050; overflow-y: auto;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background: #ffffff;">

                    <div class="modal-body p-3 p-md-4 pt-2">
                        <div>
                            <button type="button" class="btn-close" wire:click="closeModal" style="box-shadow: none; font-size: 12px;" aria-label="Close"></button>
                        </div>
                        
                        <div class="row g-4">
                            
                            <!-- Left Column: Review Form Wizard -->
                            <div class="col-12 {{ $step === 1 ? 'col-lg-12' : 'col-lg-9 pe-lg-4 review-left-col' }}">
                                <div class="modal-header border-0 p-0 d-flex justify-content-between align-items-center flex-wrap gap-2" style="margin-bottom:20px;">
                                    <div class="d-flex flex-column gap-4 w-100">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="top-product-small-logo">
                                                <img src="{{ asset($businessIcon ?? 'front/img/big-asana.png') }}" alt="{{ $businessName }}">
                                            </div>
                                            <span class="fw-bold" style="color: #002655; font-size: 14px;">{{ $businessName }}</span>
                                        </div>
                                        <h2 class="m-0 fw-bold header-title-responsive" style="color: #002655; font-size: 22px; line-height:1.2 !important;">
                                            @if($step === 1)
                                                Do you recommend {{ $businessName }}?
                                            @elseif($step === 2)
                                                Share your experience
                                            @elseif($step === 3)
                                                Write your review
                                            @elseif($step === 4)
                                                Pros & cons (optional)
                                            @endif
                                        </h2>
                                    </div>
                                </div>

                                @if($step === 1)
                                    <!-- Step 1: Recommendation (Separate First Page) -->
                                    <div class="step-content text-center py-4" wire:key="step-1">
                                        <div class="d-flex justify-content-center align-items-center gap-3 gap-md-4 flex-wrap my-4">
                                            <button type="button" 
                                                    wire:click="$set('recommend', 1)"
                                                    class="recommend-card-btn {{ $recommend === 1 ? 'selected' : '' }}">
                                                <i class="fas fa-thumbs-up" style="font-size: 16px;"></i>
                                                <span>Yes, I recommend it</span>
                                            </button>

                                            <button type="button" 
                                                    wire:click="$set('recommend', 0)"
                                                    class="recommend-card-btn {{ $recommend === 0 ? 'selected' : '' }}">
                                                <i class="fas fa-thumbs-down" style="font-size: 16px;"></i>
                                                <span>No, I don't recommend it</span>
                                            </button>
                                        </div>
                                        @error('recommend')
                                            <small class="text-danger d-block mt-2 text-center" style="font-size: 12px;">{{ $message }}</small>
                                        @enderror

                                        <hr class="my-4">

                                        <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                            <button type="button" class="btn out_ln_btn btn-outline-secondary w-50 w-sm-auto" wire:click="closeModal" style="font-weight:500; padding:12px 25px; border-radius: 30px; font-size: 14px; color: #002347; border: 1px solid #06498b !important; background-color: #ffffff;">
                                                Cancel
                                            </button>
                                            <button type="button" class="blue-btn btn text-white w-50 w-sm-auto" wire:click="goToStep2" style="padding:12px 25px; font-weight:500; background-color: #174889; border-radius: 30px; font-size: 14px;">
                                                Continue <i class="fas fa-arrow-right ms-1 ms-sm-2" style="font-size: 11px;"></i>
                                            </button>
                                        </div>
                                    </div>

                                @elseif($step === 2)
                                    <!-- Step 2: Rating Criteria Star Ratings -->
                                    <div class="step-content" wire:key="step-2">
                                         <div class="row">
                                             @foreach($criteria as $criterion)
                                                 @php
                                                     $cId = $criterion['id'];
                                                     $value = $criteriaRatings[$cId] ?? 0;
                                                 @endphp
                                                 <div class="mb-3 col-md-6">
                                                     <label class="form-label fw-semibold mb-0" style="font-size: 12px; color:#002347;">
                                                         {{ $criterion['name'] }}
                                                     </label>
                                                     @if(!empty($criterion['description']))
                                                         <small class="text-muted d-block" style="font-size: 11px; line-height: 1.3; color: #64748b; margin-bottom: 4px;">
                                                             {{ $criterion['description'] }}
                                                         </small>
                                                     @endif
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

                                         <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                             <button type="button" class="btn out_ln_btn btn-outline-secondary w-50 w-sm-auto" wire:click="setStep(1)" style="font-weight:500; padding:12px 25px; border-radius: 30px; font-size: 14px; color: #002347; border: 1px solid #06498b !important; background-color: #ffffff;">
                                                 <i class="fas fa-arrow-left me-1 me-sm-2" style="font-size: 11px;"></i> Back
                                             </button>
                                             <button type="button" class="blue-btn btn text-white w-50 w-sm-auto" wire:click="goToStep3" style="padding:12px 25px; font-weight:500; background-color: #174889; border-radius: 30px; font-size: 14px;">
                                                 Continue <i class="fas fa-arrow-right ms-1 ms-sm-2" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>

                                @elseif($step === 3)
                                    <!-- Step 3: Title and Review Description -->
                                    <div class="step-content step2" wire:key="step-3">
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

                                         <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                             <button type="button" class="btn out_ln_btn btn-outline-secondary w-50 w-sm-auto" wire:click="setStep(2)" style="font-weight:500; padding:12px 25px; border-radius: 30px; font-size: 14px; color: #002347; border: 1px solid #06498b !important; background-color: #ffffff;">
                                                 <i class="fas fa-arrow-left me-1 me-sm-2" style="font-size: 11px;"></i> Back
                                             </button>
                                             <button type="button" class="btn text-white w-50 w-sm-auto" wire:click="submitStep3" style="padding:12px 25px; font-weight:500; background-color: #06498b; border-radius: 30px; font-size: 14px;">
                                                 Continue <i class="fas fa-arrow-right ms-1 ms-sm-2" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>

                                @elseif($step === 4)
                                    <!-- Step 4: Pros & Cons (Compact Search & Tag Cloud) -->
                                    <div class="step-content" wire:key="step-4">
                                        
                                        <!-- Pros Section -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="fw-bold mb-0" style="color: #002347; font-size: 14px;">
                                                    <i class="fas fa-plus-circle text-success me-1"></i> Pros <span class="fw-normal text-muted" style="font-size: 12px;">(What you liked)</span>
                                                </label>
                                                <span class="badge bg-light text-secondary border" style="font-size: 11px;">
                                                    {{ count($selectedPros) }}/5 selected
                                                </span>
                                            </div>
                                            
                                            <div class="pro-con-box">
                                                <div class="mb-2 position-relative" style="display: flex; align-items: center;">
                                                    <i class="fas fa-search position-absolute text-muted" style="left: 12px; font-size: 12px; pointer-events: none; z-index: 5;"></i>
                                                    <input type="text" 
                                                           wire:model.live.debounce.100ms="proSearch" 
                                                           class="form-control" 
                                                           placeholder="Search or filter pros..." 
                                                           style="border-radius: 20px; font-size: 12px; padding: 6px 32px 6px 32px; border: 1px solid #e2e8f0; background: #fff; width: 100%;">
                                                    @if(!empty($proSearch))
                                                        <button type="button" wire:click="clearProSearch" class="clear-search-btn" title="Clear search">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                @if(count($selectedPros) > 0)
                                                    <div class="selected-chips-container">
                                                        @foreach($availablePros as $pro)
                                                            @if(in_array($pro['id'], $selectedPros))
                                                                <button type="button" 
                                                                        wire:key="selected-pro-{{ $pro['id'] }}"
                                                                        wire:click="togglePro({{ $pro['id'] }})"
                                                                        class="selected-chip-badge"
                                                                        title="Click to remove">
                                                                    <i class="fas fa-check" style="font-size: 10px;"></i>
                                                                    <span>{{ $pro['text'] }}</span>
                                                                    <i class="fas fa-times ms-1"></i>
                                                                </button>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="suggestions-chips-container">
                                                    @forelse($filteredPros as $pro)
                                                        <button type="button" 
                                                                wire:key="pro-chip-{{ $pro['id'] }}"
                                                                wire:click="togglePro({{ $pro['id'] }})"
                                                                class="pro-con-chip-btn">
                                                            <i class="fas fa-plus text-success" style="font-size: 10px;"></i>
                                                            <span>{{ $pro['text'] }}</span>
                                                        </button>
                                                    @empty
                                                        <p class="text-muted small m-0 py-1" style="font-size: 12px;">
                                                            @if(!empty($proSearch))
                                                                No matching pros found for "{{ $proSearch }}".
                                                            @elseif(count($selectedPros) >= 5)
                                                                Maximum of 5 pros selected.
                                                            @else
                                                                All available pros selected.
                                                            @endif
                                                        </p>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cons Section -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="fw-bold mb-0" style="color: #002347; font-size: 14px;">
                                                    <i class="fas fa-minus-circle text-danger me-1"></i> Cons <span class="fw-normal text-muted" style="font-size: 12px;">(What you disliked)</span>
                                                </label>
                                                <span class="badge bg-light text-secondary border" style="font-size: 11px;">
                                                    {{ count($selectedCons) }}/5 selected
                                                </span>
                                            </div>
                                            
                                            <div class="pro-con-box">
                                                <div class="mb-2 position-relative" style="display: flex; align-items: center;">
                                                    <i class="fas fa-search position-absolute text-muted" style="left: 12px; font-size: 12px; pointer-events: none; z-index: 5;"></i>
                                                    <input type="text" 
                                                           wire:model.live.debounce.100ms="conSearch" 
                                                           class="form-control" 
                                                           placeholder="Search or filter cons..." 
                                                           style="border-radius: 20px; font-size: 12px; padding: 6px 32px 6px 32px; border: 1px solid #e2e8f0; background: #fff; width: 100%;">
                                                    @if(!empty($conSearch))
                                                        <button type="button" wire:click="clearConSearch" class="clear-search-btn" title="Clear search">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                @if(count($selectedCons) > 0)
                                                    <div class="selected-chips-container">
                                                        @foreach($availableCons as $con)
                                                            @if(in_array($con['id'], $selectedCons))
                                                                <button type="button" 
                                                                        wire:key="selected-con-{{ $con['id'] }}"
                                                                        wire:click="toggleCon({{ $con['id'] }})"
                                                                        class="selected-chip-badge"
                                                                        title="Click to remove">
                                                                    <i class="fas fa-check" style="font-size: 10px;"></i>
                                                                    <span>{{ $con['text'] }}</span>
                                                                    <i class="fas fa-times ms-1"></i>
                                                                </button>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="suggestions-chips-container">
                                                    @forelse($filteredCons as $con)
                                                        <button type="button" 
                                                                wire:key="con-chip-{{ $con['id'] }}"
                                                                wire:click="toggleCon({{ $con['id'] }})"
                                                                class="pro-con-chip-btn">
                                                            <i class="fas fa-minus text-danger" style="font-size: 10px;"></i>
                                                            <span>{{ $con['text'] }}</span>
                                                        </button>
                                                    @empty
                                                        <p class="text-muted small m-0 py-1" style="font-size: 12px;">
                                                            @if(!empty($conSearch))
                                                                No matching cons found for "{{ $conSearch }}".
                                                            @elseif(count($selectedCons) >= 5)
                                                                Maximum of 5 cons selected.
                                                            @else
                                                                All available cons selected.
                                                            @endif
                                                        </p>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                         <hr class="my-3">
                                         <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                             <button type="button" class="btn out_ln_btn btn-outline-secondary w-50 w-sm-auto" wire:click="setStep(3)" style="padding:12px 25px; font-weight:500; border-radius: 30px; font-size: 14px; color: #002347; border: 1px solid #06498b; background-color: #ffffff;">
                                                 <i class="fas fa-arrow-left me-1" style="font-size: 11px;"></i> Back
                                             </button>
                                             <button type="button" class="btn text-white w-50 w-sm-auto" wire:click="submit" wire:loading.attr="disabled" style="padding:12px 25px; font-weight:500; background-color: #06498b; border-radius: 30px; font-size: 14px;">
                                                 Submit review <i class="fas fa-arrow-right ms-1" style="font-size: 11px;"></i>
                                             </button>
                                         </div>
                                     </div>
                                @endif
                            </div>

                            @if($step > 1)
                            <!-- Right Column: Sidebar Progress Panel (Steps 2, 3, 4) -->
                            <div class="col-lg-3 d-lg-block" style="display:flex !important; align-items:center;">
                                <div class="p-3 p-md-4 rounded-3 mt-0 w-100" style="background-color: #f8fafc; border: 1px solid #e2e8f0; position: sticky; top: 0;">
                                    <!-- Tips Section (Dynamic according to Step) -->
                                    <div style="font-size: 12px; color: #64748b; line-height: 1.5;">
                                        @if($step === 2)
                                            <div class="mb-1" style="font-weight: 600; color: #002347;">
                                                <i class="fas fa-lightbulb text-warning me-1"></i> Rate honestly
                                            </div>
                                            <p class="m-0 text-muted" style="font-size: 12px;">Your ratings should reflect your overall experience with this business.</p>
                                        @elseif($step === 3)
                                            <div class="mb-1" style="font-weight: 600; color: #002347;">
                                                <i class="fas fa-lightbulb text-warning me-1"></i> Be specific
                                            </div>
                                            <p class="m-0 text-muted" style="font-size: 12px;">Include details that may help others make an informed decision.</p>
                                        @elseif($step === 4)
                                            <div class="mb-1" style="font-weight: 600; color: #002347;">
                                                <i class="fas fa-lightbulb text-warning me-1"></i> What stood out?
                                            </div>
                                            <p class="m-0 text-muted" style="font-size: 12px;">List the biggest strengths and weaknesses you experienced.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .step-content.step2 .form-floating.text-inpt > .form-control {
                font-size: 15px;
            }
            .modal-content .step-content .step_3_btn:hover {
                text-decoration: underline !important;
                background-color: unset;
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
                color: #80868b !important;
                background-color: #ffffff !important;
                padding: 0 10px !important;
                height: auto !important;
            }
            .step-content.step2 .form-floating.text-inpt > label {
                margin-left: 5px;
            }
            .step-content.step2 .form-floating label {
                margin-left: 5px;
            }
            .form-floating > label {
                padding: 17px 20px;
                color: #64748b;
            }
            .form-floating > .form-control,
            .form-floating > textarea {
                border-radius: 4px !important;
                border: 1px solid #dadce0 !important;
                color: #202124;
            }
            .modal-dialog .modal-body .out_ln_btn:hover {
                background-color: #174889 !important;
                color: #fff !important;
            }
            .modal-body {
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
            .modal-content button.btn-close:hover {
                background-color: #f3f4f6;
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
