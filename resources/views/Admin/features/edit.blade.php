@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Edit Feature</h4>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('features.update', $feature->id) }}" class="form-validate" method="POST">
                    @csrf
                    <div class="row g-gs">
                        <!-- Feature Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Feature Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $feature->translations()->first()->name) }}">
                                </div>
                                @error('name')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                        <!--Feature description-->

                        <!-- Categories -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Applicable Category</label>
                                <div class="form-control-wrap">
                                    <select name="category_ids" class="form-select js-select2">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_ids', optional($feature->category)->id ?? null) == $category->id ? 'selected' : '' }}>
                                                {{ $category->translated_name ?? ($category->translations->name ?? 'Category #'.$category->id) }}
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
                                        <option value="1" {{ old('status', $feature->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $feature->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                                                <!-- Keep Capitalized Checkbox -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label d-block">Capitalization Format</label>
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="keep_capitalized" name="keep_capitalized" value="1"
                                        {{ old('keep_capitalized', $feature->keep_capitalized ?? 0) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="keep_capitalized">Keep capitalized in headlines (e.g. DNS, SSL, API)</label>
                                </div>
                                <small class="text-muted d-block mt-1">If checked, the first letter will not be forced to lowercase in automated titles.</small>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" style="background:#F9633B">Update Feature</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
