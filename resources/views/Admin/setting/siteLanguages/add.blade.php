@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Site Language</h4>
            </div>
            <div>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('site-languages-addProcc') }}" class="form-validate" novalidate="novalidate"
                    method="post">
                    @csrf
                    <div class="row g-gs">
                        <!-- Country/region Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="country_id">Country/region</label>
                                <div class="form-control-wrap">
                                    <select class="form-select js-select2" id="country_id" name="country_id" required data-placeholder="Select Country/region">
                                        <option value="">Select a country/region</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ old('country_id') == $country->id ? 'selected' : '' }}>
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

                        <!-- Base Language Select (Optional) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="base_language_id">Base Language (Optional)</label>
                                <div class="form-control-wrap">
                                    <select class="form-select js-select2" id="base_language_id" name="base_language_id" data-placeholder="No base language">
                                        <option value="">No base language</option>
                                        @foreach ($baseLanguages as $bl)
                                            <option value="{{ $bl->id }}" {{ old('base_language_id') == $bl->id ? 'selected' : '' }}>
                                                {{ $bl->name }} ({{ $bl->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="form-note text-muted">Select the base language reference, or "No base language".</span>
                                @error('base_language_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Lang Code Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="lang_code">Lang code</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="lang_code" name="lang_code"
                                        value="{{ old('lang_code') }}" placeholder="e.g. en-us, fr-fr" required />
                                </div>
                                @error('lang_code')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Toggle with Dynamic Text -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="status">Status</label>
                                <div class="form-control-wrap d-flex align-items-center gap-2">
                                    <!-- Hidden input to ensure a 0 is submitted when checkbox is unchecked -->
                                    <input type="hidden" name="status" value="0">

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status"
                                            value="1" {{ old('status', 1) == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status"></label>
                                    </div>
                                    <span id="statusText" style="font-weight: 600;">
                                        {{ old('status', 1) == 1 ? 'Active' : 'Inactive' }}
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
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">Save</button>
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
                placeholder: 'No base language',
                allowClear: true,
                width: '100%'
            });

            // Dynamic status text change
            $('#status').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#statusText').text('Active');
                } else {
                    $('#statusText').text('Inactive');
                }
            });
        });
    </script>
@endsection
