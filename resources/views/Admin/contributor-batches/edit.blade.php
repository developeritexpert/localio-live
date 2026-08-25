@extends('admin_layout.master')

@section('content')

    <div class="nk-block nk-block-lg">

        <div class="nk-block-head d-flex justify-content-between">

            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    Edit Contributor Batch
                </h4>
            </div>

        </div>

        <div class="card card-bordered">

            <div class="card-inner">

                <form
                    action="{{ route('contributor-batches.update', $contributorBatch->id) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="form-validate"
                >

                    @csrf

                    <div class="row g-gs">

                        {{-- Badge Name --}}
                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="form-label" for="badge_name">
                                    Badge Name
                                </label>

                                <div class="form-control-wrap">

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="badge_name"
                                        name="badge_name"
                                        value="{{ old('badge_name', $contributorBatch->badge_name) }}"
                                        required
                                    >

                                </div>

                                @error('badge_name')
                                    <div class="error text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>


                        {{-- Minimum Reviews --}}
                        <div class="col-md-3">

                            <div class="form-group">

                                <label class="form-label" for="min_reviews">
                                    Minimum Reviews
                                </label>

                                <div class="form-control-wrap">

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="min_reviews"
                                        name="min_reviews"
                                        value="{{ old('min_reviews', $contributorBatch->min_reviews) }}"
                                        min="1"
                                        required
                                    >

                                </div>

                                @error('min_reviews')
                                    <div class="error text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>


                        {{-- Maximum Reviews --}}
                        <div class="col-md-3">

                            <div class="form-group">

                                <label class="form-label" for="max_reviews">
                                    Maximum Reviews
                                </label>

                                <div class="form-control-wrap">

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="max_reviews"
                                        name="max_reviews"
                                        value="{{ old('max_reviews', $contributorBatch->max_reviews) }}"
                                        min="1"
                                        placeholder="Leave empty for 51+"
                                    >

                                </div>

                                <small class="text-muted">
                                    Leave empty for unlimited reviews.
                                </small>

                                @error('max_reviews')
                                    <div class="error text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>


                        {{-- Badge Icon Preview --}}
<div class="col-md-6">
    <div class="form-group">

        <label class="form-label d-block">
            Badge Icon Preview
        </label>

        <div class="mb-3">
            @if($contributorBatch->icon)
                <img
                    id="iconPreview"
                    src="{{ asset('storage/' . $contributorBatch->icon) }}"
                    alt="{{ $contributorBatch->badge_name }}"
                    style="
                        width: 70px;
                        height: 70px;
                        object-fit: contain;
                        border: 1px solid #ddd;
                        border-radius: 6px;
                        padding: 5px;
                    "
                >
            @else
                <img
                    id="iconPreview"
                    src=""
                    alt="Icon Preview"
                    style="
                        width: 70px;
                        height: 70px;
                        object-fit: contain;
                        border: 1px solid #ddd;
                        border-radius: 6px;
                        padding: 5px;
                        display: none;
                    "
                >

                <span
                    id="noIconText"
                    class="text-muted"
                >
                    No icon uploaded.
                </span>
            @endif
        </div>

    </div>
</div>

                        {{-- New Icon --}}
                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="form-label" for="icon">
                                    Replace Badge Icon
                                </label>

                                <div class="form-control-wrap">

                                    <input
                                        type="file"
                                        class="form-control"
                                        id="icon"
                                        name="icon"
                                        accept=".png,.jpg,.jpeg,.webp,.svg"
                                    >

                                </div>

                                <small class="text-muted">
                                    Leave empty to keep the current icon.
                                    Allowed: PNG, JPG, JPEG, WEBP, SVG. Maximum 2MB.
                                </small>

                                @error('icon')
                                    <div class="error text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>


                        {{-- Status --}}
                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="form-label">
                                    Status
                                </label>

                                <div class="form-control-wrap">

                                    <select
                                        class="form-select"
                                        name="status"
                                        required
                                    >

                                        <option
                                            value="active"
                                            {{ old('status', $contributorBatch->status) === 'active' ? 'selected' : '' }}
                                        >
                                            Active
                                        </option>

                                        <option
                                            value="inactive"
                                            {{ old('status', $contributorBatch->status) === 'inactive' ? 'selected' : '' }}
                                        >
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                                @error('status')
                                    <div class="error text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>


                        {{-- Submit --}}
                        <div class="col-md-12 mt-4">

                            <div class="form-group">

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    style="background:#F9633B"
                                >
                                    Update Contributor Batch
                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection





<script>
    document.addEventListener('DOMContentLoaded', function () {

        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('iconPreview');
        const noIconText = document.getElementById('noIconText');

        if (!iconInput || !iconPreview) {
            return;
        }

        iconInput.addEventListener('change', function (event) {

            const file = event.target.files[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {

                // Show newly selected image
                iconPreview.src = e.target.result;
                iconPreview.style.display = 'block';

                // Hide "No icon uploaded" text
                if (noIconText) {
                    noIconText.style.display = 'none';
                }
            };

            reader.readAsDataURL(file);
        });

    });
</script>