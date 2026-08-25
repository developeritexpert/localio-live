@extends('admin_layout.master')

@section('content')

    <div class="nk-block nk-block-lg">

        <div class="nk-block-head d-flex justify-content-between">

            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    Add Contributor Batch
                </h4>
            </div>

        </div>

        <div class="card card-bordered">

            <div class="card-inner">

                <form
                    action="{{ route('contributor-batches.store') }}"
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
                                        value="{{ old('badge_name') }}"
                                        placeholder="e.g. New contributor"
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
                                        value="{{ old('min_reviews') }}"
                                        min="1"
                                        placeholder="1"
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
                                        value="{{ old('max_reviews') }}"
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


                        {{-- Icon --}}
                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="form-label" for="icon">
                                    Badge Icon
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

                                        <option value="active"
                                            {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>

                                        <option value="inactive"
                                            {{ old('status') === 'inactive' ? 'selected' : '' }}>
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
                                    Save Contributor Batch
                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection