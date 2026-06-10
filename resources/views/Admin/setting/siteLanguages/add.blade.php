@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Site Language</h4>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ url('admin-dashboard/site-languages/addProcc') }}" class="form-validate"
                    novalidate="novalidate" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-gs">
                        <!-- Country Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="country">Country</label>
                                <div class="form-control-wrap">
                                    <select class="form-select select2" id="country_id" name="country_id" multiple>
                                        <option value="">Select a country</option>
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

                        <!-- Base Language Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="base_language_id">Base Language (Optional)</label>
                                <div class="form-control-wrap">
                                    <select class="form-select select2" id="base_language_id" name="base_language_id"
                                        multiple>
                                        <option value="">-- Select Base Language --</option>
                                        @foreach ($languagesforBase as $language)
                                            <option value="{{ $language->id }}"
                                                {{ old('base_language_id') == $language->id ? 'selected' : '' }}>
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

                        <!-- Language Name Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Language</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" />
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden Slug Field -->
                        <input type="hidden" class="form-control" id="slug" name="slug"
                            value="{{ old('slug') }}" />

                        <!-- Lang Code Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="lang_code">Lang code</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="lang_code" name="lang_code"
                                        value="{{ old('lang_code') }}" />
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
                        <!-- is_active_translation Toggle -->
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_active_translation">Active Translation</label>
                                <div class="form-control-wrap d-flex align-items-center gap-2">
                                    <!-- Hidden input -->
                                    <input type="hidden" name="is_active_translation" value="0">

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active_translation"
                                            name="is_active_translation" value="1"
                                            {{ old('is_active_translation', 0) == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active_translation"></label>
                                    </div>
                                    <span id="isActiveTranslationText" style="font-weight: 600;">
                                        {{ old('is_active_translation', 0) == 1 ? 'Enabled' : 'Disabled' }}
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
                placeholder: 'Select a country',
                maximumSelectionLength: 1,
                allowClear: true,
                width: '100%'
            });

            $('#base_language_id').select2({
                placeholder: 'Select base language',
                maximumSelectionLength: 1,
                allowClear: true,
                width: '100%'
            });

            // Update the slug field based on the name field
            $('#name').on('input', function() {
                let name = $(this).val().toLowerCase();
                let slug = name.replace(/\s+/g, "-").replace(/\//g, "-");
                $('#slug').val(slug);
            });

            // Slug input: sanitize spaces and slashes
            $('#slug').on('change', function() {
                this.value = this.value.toLowerCase().replace(/\s+/g, '-').replace(/\//g, '-');
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
