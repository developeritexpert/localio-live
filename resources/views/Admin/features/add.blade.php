@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Feature</h4>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('features.store') }}" class="form-validate" method="post">
                    @csrf
                    <div class="row g-gs">
                        <!-- Feature Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Feature Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}">
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Add feature description --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Feature Description</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="{{ old('description') }}">
                                </div>
                                @error('description')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Applicable Categories</label>
                                <div class="form-control-wrap">
                                    <select name="category_ids" class="form-select js-select2">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ collect(old('category_ids'))->contains($category->id) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category_ids')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" name="status">
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" style="background:#F9633B">Save
                                    Feature</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script>
        $(document).ready(function() {
            $('#name').on('input', function() {
                let slug = $(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '');
                $('#slug').val(slug);
            });
        });
    </script>
@endsection
