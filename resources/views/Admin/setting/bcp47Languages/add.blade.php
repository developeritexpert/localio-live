@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add BCP 47 Language</h4>
                <div class="nk-block-des text-soft">
                    <p>Define a new BCP 47 language code (e.g. es-419, en-US, pt-BR). You can assign it to multiple countries.</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('bcp47-languages.index') }}" class="btn btn-outline-light">
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
                <form action="{{ route('bcp47-languages.addProcc') }}" class="form-validate" method="post">
                    @csrf
                    <div class="row g-gs">
                        <!-- BCP 47 Code -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="code">BCP 47 Language Tag <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="{{ old('code') }}"
                                        placeholder="e.g. en-US, es-419, pt-BR, de-DE" required />
                                </div>
                                <div class="form-text text-muted">Standard BCP 47 tag. This code is unique in the system.</div>
                                @error('code')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Name / Description -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Name / Description</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}"
                                        placeholder="e.g. Spanish (Latin America)" />
                                </div>
                                <div class="form-text text-muted">Optional friendly label for this BCP 47 code.</div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
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
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">Save BCP 47 Language</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#status').on('change', function () {
                $('#statusText').text($(this).is(':checked') ? 'Active' : 'Inactive');
            });
        });
    </script>
@endsection
