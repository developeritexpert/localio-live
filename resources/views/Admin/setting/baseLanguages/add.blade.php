@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Base Language</h4>
                <div class="nk-block-des text-soft">
                    <p>Create a base language supported by Google Cloud Translate.</p>
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
                        <!-- Language Name Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Base Language Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" placeholder="e.g. Spanish (Latin America)" required />
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Google Translate Code Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="code">Google Cloud Translate Code</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="{{ old('code') }}" placeholder="e.g. es-419, pt-BR, en-US" required />
                                </div>
                                <div class="form-text text-muted">Use standard BCP-47 / Google Cloud Translate code (e.g. es-ES, es-419, pt-BR, pt-PT, en-US, en-GB).</div>
                                @error('code')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Language Tag Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="language_tag">Language Combination Tag</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="language_tag" name="language_tag"
                                        value="{{ old('language_tag') }}" placeholder="e.g. Spanish, English, Portuguese, French" required />
                                </div>
                                <div class="form-text text-muted">Used for grouping reviews and translations (e.g. all Spanish variations share "Spanish").</div>
                                @error('language_tag')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Master Language Toggle -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_master">Master Language</label>
                                <div class="form-control-wrap d-flex align-items-center gap-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_master" name="is_master"
                                            value="1" {{ old('is_master') == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_master">Primary source for translations (US English)</label>
                                    </div>
                                </div>
                                @error('is_master')
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
