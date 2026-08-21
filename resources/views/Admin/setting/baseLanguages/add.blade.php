@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Base Language</h4>
                <div class="nk-block-des text-soft">
                    <p>Select a country/region to define as a base language reference.</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('base-languages.index') }}" class="btn btn-outline-light">
                    <em class="icon ni ni-arrow-left"></em><span>Back to List</span>
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('base-languages.addProcc') }}" class="form-validate" method="post">
                    @csrf
                    <div class="row g-gs">
                        <!-- Country/region Dropdown -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Country/region</label>
                                <div class="form-control-wrap">
                                    <select class="form-select js-select2" id="name" name="name" required data-placeholder="Select Country/region">
                                        <option value="">Select Country/region</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->name }}" {{ old('name') == $country->name ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- BCP 47 Language Dropdown -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="bcp47_language_id">BCP 47 Language <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <select class="form-select js-select2" id="bcp47_language_id" name="bcp47_language_id"
                                        required data-placeholder="Select BCP 47 Language">
                                        <option value="">Select BCP 47 Language</option>
                                        @foreach ($bcp47Languages as $bcp47)
                                            <option value="{{ $bcp47->id }}" {{ old('bcp47_language_id') == $bcp47->id ? 'selected' : '' }}>
                                                {{ $bcp47->code }}{{ $bcp47->name ? ' — ' . $bcp47->name : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text text-muted">Same BCP 47 code can be assigned to multiple countries.</div>
                                @error('bcp47_language_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="status">Status</label>
                                <div class="form-control-wrap d-flex align-items-center gap-2">
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
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">Save Base Language</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#name').select2({
                placeholder: 'Select Country/region',
                allowClear: true,
                width: '100%'
            });

            $('#bcp47_language_id').select2({
                placeholder: 'Select BCP 47 Language',
                allowClear: true,
                width: '100%'
            });

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
