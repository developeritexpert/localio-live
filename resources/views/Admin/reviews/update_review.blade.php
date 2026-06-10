@extends('admin_layout.master')
@section('content')

<div class="nk-block nk-block-lg">
    <!-- Page Header -->
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Update Review: {{$language->name}}</h3>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                        <em class="icon ni ni-more-v"></em>
                    </a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <!-- Optional page tools go here -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Form -->
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <form action="{{ url('admin-dashboard/review-status-update/' . $review->id) }}" method="POST">
                @csrf
                @php
                    $ease_of_use_rating = old('ease_of_use_rating', $review->ease_of_use_rating);
                    $value_for_money_rating = old('value_for_money_rating', $review->value_for_money_rating);
                    $customer_service_rating = old('customer_service_rating', $review->customer_service_rating);
                    $exclusive_service_rating = old('exclusive_service_rating', $review->exclusive_service_rating);
                @endphp

                <div class="row g-3">
                    <!-- Ease of Use Rating -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="ease_of_use_rating">Ease of Use</label>
                            <div class="d-flex align-items-center star-rating" data-input-id="ease_of_use_rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ $i <= $ease_of_use_rating ? 'fa-star' : 'fa-star-o' }} text-warning fs-5 me-1"
                                       data-value="{{ $i }}" style="cursor: pointer;"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="ease_of_use_rating" id="ease_of_use_rating" value="{{ $ease_of_use_rating }}">
                            @error('ease_of_use_rating')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Value for Money Rating -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="value_for_money_rating">Value for Money</label>
                            <div class="d-flex align-items-center star-rating" data-input-id="value_for_money_rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ $i <= $value_for_money_rating ? 'fa-star' : 'fa-star-o' }} text-warning fs-5 me-1"
                                       data-value="{{ $i }}" style="cursor: pointer;"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="value_for_money_rating" id="value_for_money_rating" value="{{ $value_for_money_rating }}">
                            @error('value_for_money_rating')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Customer Service Rating -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="customer_service_rating">Customer Service</label>
                            <div class="d-flex align-items-center star-rating" data-input-id="customer_service_rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ $i <= $customer_service_rating ? 'fa-star' : 'fa-star-o' }} text-warning fs-5 me-1"
                                       data-value="{{ $i }}" style="cursor: pointer;"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="customer_service_rating" id="customer_service_rating" value="{{ $customer_service_rating }}">
                            @error('customer_service_rating')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Exclusive Service Rating -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="exclusive_service_rating">Exclusive Service</label>
                            <div class="d-flex align-items-center star-rating" data-input-id="exclusive_service_rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ $i <= $exclusive_service_rating ? 'fa-star' : 'fa-star-o' }} text-warning fs-5 me-1"
                                       data-value="{{ $i }}" style="cursor: pointer;"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="exclusive_service_rating" id="exclusive_service_rating" value="{{ $exclusive_service_rating }}">
                            @error('exclusive_service_rating')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Business Dropdown -->
                    @if (isset($businesses))
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Business Name</label>
                                <select name="business_id" class="form-control js-select2">
                                    @foreach ($businesses as $business)
                                        <option value="{{ $business->id }}"
                                            {{ $business->id == $review->business_id ? 'selected' : '' }}>
                                            {{ $business->translations->first()->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="business_id" value="{{ $review->business_id }}">
                                @error('business_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="col-md-12 mt-3">
                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="4"
                                      style="text-align: left">{{ old('description', $reviewTranslation->description ?? $defaultTranslation->description ?? '') }}</textarea>
                            @error('description')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Review Status Switch -->
                    <div class="col-md-12 mt-1">
                        <div class="form-group d-flex align-items-center">
                            <label class="form-label d-block me-3">Review Status:</label>
                            <label class="me-2 mb-0"><b>Awaiting Approval</b></label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customSwitch1" name="status"
                                    {{ ($review->status ?? 'inactive') === 'active' ? 'checked' : '' }}
                                    {{ getCurrentLocale() !== 'en-us' ? 'disabled' : '' }}>
                                <label class="custom-control-label" for="customSwitch1"></label>
                            </div>
                            <label class="ms-2 mb-0"><b>Approved</b></label>
                        </div>
                        <input type="hidden" name="status" id="statusHidden1" value="{{ $review->status ?? 'inactive' }}">
                        @error('status')
                            <div class="error text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-12 mt-4">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <span>Update Review</span>
                            </button>
                        </div>
                    </div>
                </div> <!-- End of form rows -->
            </form>
        </div>
    </div>
</div>

    <script>
        $(document).ready(function() {
            const switchInput = $("#customSwitch1");
            const hiddenInput = $("#statusHidden1");

            // Set initial checkbox state
            switchInput.prop("checked", hiddenInput.val() === "active");

            // Update hidden input when the switch is toggled, only if it's not disabled
            if (!switchInput.prop("disabled")) {
                switchInput.on("change", function() {
                    const newValue = this.checked ? "active" : "inactive";
                    hiddenInput.val(newValue);
                });
            }
        });
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".star-rating").forEach(container => {
            const stars = container.querySelectorAll(".fa");
            const inputId = container.getAttribute("data-input-id");
            const hiddenInput = document.getElementById(inputId);
            let selectedRating = parseInt(hiddenInput.value) || 0;

            const renderStars = (rating) => {
                stars.forEach(star => {
                    const val = parseInt(star.dataset.value);
                    star.classList.remove('fa-star', 'fa-star-o');
                    star.classList.add(val <= rating ? 'fa-star' : 'fa-star-o');
                });
            };

            renderStars(selectedRating);

            stars.forEach(star => {
                const val = parseInt(star.dataset.value);
                star.addEventListener('mouseenter', () => renderStars(val));
                star.addEventListener('mouseleave', () => renderStars(selectedRating));
                star.addEventListener('click', () => {
                    selectedRating = val;
                    hiddenInput.value = selectedRating;
                    renderStars(selectedRating);
                });
            });
        });
    });
    </script>
@endsection
