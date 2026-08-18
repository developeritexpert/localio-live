@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Edit Base Language</h4>
                <div class="nk-block-des text-soft">
                    <p>Update base language country/region reference and BCP 47 code.</p>
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
                <form action="{{ route('base-languages.updateProcc', $baseLanguage->id) }}" class="form-validate" method="post">
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
                                            <option value="{{ $country->name }}"
                                                {{ old('name', $baseLanguage->name) == $country->name ? 'selected' : '' }}>
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

                        <!-- BCP 47 Language Tag Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="code">BCP 47 Language Tag</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="{{ old('code', $baseLanguage->code) }}" placeholder="e.g. es-ES, es-419, pt-BR, pt-PT, en-US, en-GB" required />
                                </div>
                                <div class="form-text text-muted">Use standard BCP 47 code (e.g. es-ES, es-419, pt-BR, pt-PT, en-US, en-GB).</div>
                                @error('code')
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
                                            value="1" {{ old('status', $baseLanguage->status) == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status"></label>
                                    </div>
                                    <span id="statusText" style="font-weight: 600;">
                                        {{ old('status', $baseLanguage->status) == 1 ? 'Active' : 'Inactive' }}
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
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">Update Base Language</button>
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
