@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Feature</h4>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('features') }}" class="btn btn-outline-light btn-sm">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Back</span>
                </a>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('features.store') }}" class="form-validate" method="post">
                    @csrf
                    <div class="row g-gs">
                        <!-- Feature name capitalized [feature_cap] -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="feature_cap">Feature name capitalized <code class="text-primary">[feature_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="feature_cap" name="feature_cap"
                                        value="{{ old('feature_cap', old('category_cap', old('name'))) }}" placeholder="e.g. Access logs" required />
                                    <input type="hidden" id="name" name="name"
                                        value="{{ old('name', old('feature_cap', old('category_cap'))) }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Access logs</small>
                                @error('feature_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Feature name title case [features_tc] -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="features_tc">Feature name title case <code class="text-primary">[features_tc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="features_tc" name="features_tc"
                                        value="{{ old('features_tc', old('category_tc')) }}" placeholder="e.g. Access Logs" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Access Logs</small>
                                @error('features_tc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Feature name lowercase [feature_lc] -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="feature_lc">Feature name lowercase <code class="text-primary">[feature_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="feature_lc" name="feature_lc"
                                        value="{{ old('feature_lc', old('category_lc')) }}" placeholder="e.g. access logs" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: access logs</small>
                                @error('feature_lc')
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
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" style="background:#F9633B">Save Feature</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const featCapInput = document.getElementById('feature_cap');
            const featTcInput  = document.getElementById('features_tc');
            const featLcInput  = document.getElementById('feature_lc');
            const nameInput    = document.getElementById('name');

            let userModifiedTc = false;
            let userModifiedLc = false;

            if (featTcInput && featTcInput.value.trim() !== '') userModifiedTc = true;
            if (featLcInput && featLcInput.value.trim() !== '') userModifiedLc = true;

            if (featTcInput) {
                featTcInput.addEventListener('input', function() {
                    userModifiedTc = (this.value.trim() !== '');
                });
            }
            if (featLcInput) {
                featLcInput.addEventListener('input', function() {
                    userModifiedLc = (this.value.trim() !== '');
                });
            }

            function toTitleCase(str) {
                return str.toLowerCase().replace(/(?:^|\s|\/|-)\w/g, function(match) {
                    return match.toUpperCase();
                });
            }

            if (featCapInput) {
                featCapInput.addEventListener('input', function() {
                    const val = this.value.trim();
                    if (nameInput) nameInput.value = this.value;

                    if (!userModifiedTc && featTcInput) {
                        featTcInput.value = val ? toTitleCase(val) : '';
                    }
                    if (!userModifiedLc && featLcInput) {
                        featLcInput.value = val ? val.toLowerCase() : '';
                    }
                });
            }
        });
    </script>
@endsection
