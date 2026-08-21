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
                <form action="{{ route('site-language-updateProcc', $siteLanguage->id) }}" class="form-validate"
                    novalidate="novalidate" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $siteLanguage->id }}" id="id">
                    <div class="row g-gs">
                        <!-- Country Select -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="country_id">Country/region</label>
                                <div class="form-control-wrap">
                                    <select class="form-control js-select2" name="country_id" id="country_id" required data-placeholder="Select Country/region">
                                        <option value="">Select Country/region</option>
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

                        <!-- BCP 47 Language Select -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="base_language_id">BCP 47 Language (Optional)</label>
                                <div class="form-control-wrap">
                                    <select class="form-control js-select2" name="base_language_id" id="base_language_id" data-placeholder="No BCP 47 language">
                                        <option value="">No BCP 47 language</option>
                                        @foreach ($bcp47Languages as $bcp47)
                                            <option value="{{ $bcp47->id }}"
                                                {{ old('base_language_id', $siteLanguage->base_language_id) == $bcp47->id ? 'selected' : '' }}>
                                                {{ $bcp47->code }}{{ $bcp47->name ? ' — ' . $bcp47->name : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="form-note text-muted">Select the BCP 47 translation language for this country/region.</span>
                                @error('base_language_id')
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
                                        value="{{ old('lang_code', $siteLanguage->lang_code) }}" required />
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
            $('#country_id').select2({
                placeholder: 'Select Country/region',
                allowClear: true,
                width: '100%'
            });

            $('#base_language_id').select2({
                placeholder: 'No BCP 47 language',
                allowClear: true,
                width: '100%'
            });

            // Update text for Status toggle
            $('#status').on('change', function() {
                $('#statusText').text($(this).is(':checked') ? 'Active' : 'Inactive');
            });

            // Sanitize lang_code input (letters, numbers, hyphens only)
            $('#lang_code').on('input', function() {
                const sanitized = $(this).val().replace(/[^a-zA-Z0-9-]/g, '');
                $(this).val(sanitized);
            });
        });
    </script>
@endsection
