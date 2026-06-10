@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Update Site Language</h4>
            </div>
            <div>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ url('admin-dashboard/site-language/updateProcc') }}" class="form-validate"
                    novalidate="novalidate" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $siteLanguage->id }}" id="id">
                    <div class="row g-gs">
                        <!-- Country Select -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="country_id">Country</label>
                                <div class="form-control-wrap">
                                    <select class="form-control js-select2" name="country_id" id="country_id">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ old('country_id', $siteLanguage->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('country_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Base Language Select -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="base_language_id">Base Language (Optional)</label>
                                <div class="form-control-wrap">
                                    <select class="form-control js-select2" name="base_language_id" id="base_language_id">
                                        <option value="">-- Select Base Language --</option>
                                        @foreach ($languagesforBase as $language)
                                            <option value="{{ $language->id }}"
                                                {{ old('base_language_id', $siteLanguage->base_language_id ?? '') == $language->id ? 'selected' : '' }}>
                                                {{ $language->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('base_language_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Language Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Language</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $siteLanguage->name) }}" />
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Lang Code -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="lang_code">Lang code</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="lang_code" name="lang_code"
                                        value="{{ old('lang_code', $siteLanguage->lang_code) }}" />
                                </div>
                                @error('lang_code')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Status Toggle with Hidden Fallback -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="status">Status</label>
                                <div class="form-control-wrap d-flex align-items-center gap-2">
                                    <!-- Hidden fallback -->
                                    <input type="hidden" name="status" value="0">
                                    <!-- Checkbox -->
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status"
                                            value="1"
                                            {{ old('status', $siteLanguage->status ?? 1) == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status"></label>
                                    </div>
                                    <span id="statusText" style="font-weight: 600;">
                                        {{ old('status', $siteLanguage->status ?? 1) == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                @error('status')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Active Translation Toggle -->
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_active_translation">Active Translation</label>
                                <div class="form-control-wrap d-flex align-items-center gap-2">
                                    <!-- Hidden fallback -->
                                    <input type="hidden" name="is_active_translation" value="0">
                                    <!-- Checkbox -->
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active_translation"
                                            name="is_active_translation" value="1"
                                            {{ old('is_active_translation', $siteLanguage->is_active_translation ?? 0) == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active_translation"></label>
                                    </div>
                                    <span id="isActiveTranslationText" style="font-weight: 600;">
                                        {{ old('is_active_translation', $siteLanguage->is_active_translation ?? 0) == 1 ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                                @error('is_active_translation')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> --}}

                        <!-- Submit Button -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for dropdowns
            $('#country_id, #base_language_id').select2({
                placeholder: 'Select an option',
                allowClear: true,
                width: '100%'
            });

            // Update text for Status toggle
            $('#status').on('change', function() {
                $('#statusText').text($(this).is(':checked') ? 'Active' : 'Inactive');
            });

            // Update text for Active Translation toggle
            $('#is_active_translation').on('change', function() {
                $('#isActiveTranslationText').text($(this).is(':checked') ? 'Yes' : 'No');
            });

            // Sanitize lang_code input (letters, numbers, hyphens only)
            $('#lang_code').on('input', function() {
                const sanitized = $(this).val().replace(/[^a-zA-Z0-9-]/g, '');
                $(this).val(sanitized);
            });
        });
    </script>
@endsection
