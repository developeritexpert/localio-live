@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Country/region</h4>
            </div>
            <div></div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('country.addProcc') }}" class="form-validate" novalidate="novalidate" method="post">
                    @csrf
                    <div class="row g-gs">
                        <!-- Country/region Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Country/region</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="e.g. United States - English"
                                        value="{{ old('name') }}" />
                                </div>
                                <div class="form-text text-muted">As shown in the footer dropdown (e.g. United States – English, Deutschland – Deutsch).</div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- URL Locale Code -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="lang_code">URL Locale Code <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="lang_code" name="lang_code"
                                        placeholder="e.g. en-us, de-de, es-mx, fr-fr"
                                        value="{{ old('lang_code') }}" required />
                                </div>
                                <div class="form-text text-muted">Used in URLs: localio.com/<strong>en-us</strong>/... — must be lowercase (e.g. en-us, de-de, es-mx).</div>
                                @error('lang_code')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Affiliate Disclaimer Banner -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="show_disclaimer">Affiliate Disclaimer Banner</label>
                                <div class="form-control-wrap d-flex align-items-center gap-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="show_disclaimer" name="show_disclaimer"
                                            value="1"
                                            {{ old('show_disclaimer', 0) == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="show_disclaimer"></label>
                                    </div>
                                    <span id="disclaimerText" style="font-weight: 600;">
                                        {{ old('show_disclaimer', 0) == 1 ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                                @error('show_disclaimer')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit -->
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
            $('#show_disclaimer').on('change', function() {
                $('#disclaimerText').text($(this).is(':checked') ? 'Enabled' : 'Disabled');
            });

            // Auto-format lang_code: lowercase, only letters/digits/hyphens
            $('#lang_code').on('input', function() {
                $(this).val($(this).val().toLowerCase().replace(/[^a-z0-9-]/g, ''));
            });
        });
    </script>
@endsection
