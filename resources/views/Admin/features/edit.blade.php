@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Edit Feature</h4>
            </div>
            <div class="nk-block-head-content">
                @if($feature->category_id)
                <a href="{{ route('admin.category-feature-content.create', ['category_id' => $feature->category_id, 'feature_id' => $feature->id]) }}" class="btn btn-outline-primary btn-sm">
                    <em class="icon ni ni-file-text"></em>
                    <span>Feature Category Content</span>
                </a>
                @endif
                <a href="{{ route('features') }}" class="btn btn-outline-light btn-sm ms-2">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Back</span>
                </a>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('features.update', $feature->id) }}" class="form-validate" method="POST">
                    @csrf
                    <div class="row g-gs">
                        @php
                            $featTrans = $feature->translations()->first();
                            $valCap    = old('feature_cap', $featTrans?->feature_cap ?? ($featTrans?->category_cap ?? ($featTrans?->name ?? '')));
                            $valTc     = old('features_tc', $featTrans?->features_tc ?? ($featTrans?->feature_tc ?? ($featTrans?->category_tc ?? '')));
                            $valLc     = old('feature_lc', $featTrans?->feature_lc ?? ($featTrans?->category_lc ?? ''));
                            $valWithLc = old('feature_with_lc', $featTrans?->feature_with_lc ?? '');
                        @endphp
                        <!-- Feature name capitalized [feature_cap] -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="feature_cap">Feature name capitalized <code class="text-primary">[feature_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="feature_cap" name="feature_cap"
                                        value="{{ $valCap }}" placeholder="e.g. Access logs" required />
                                    <input type="hidden" id="name" name="name"
                                        value="{{ old('name', $valCap) }}" />
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
                                        value="{{ $valTc }}" placeholder="e.g. Access Logs" />
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
                                        value="{{ $valLc }}" placeholder="e.g. access logs" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: access logs</small>
                                @error('feature_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Feature name with lowercase [feature_with_lc] -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="feature_with_lc">Feature name with lowercase <code class="text-primary">[feature_with_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="feature_with_lc" name="feature_with_lc"
                                        value="{{ $valWithLc }}" placeholder="e.g. with a free SSL certificate" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: with a free SSL certificate or with daily backups</small>
                                @error('feature_with_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const featCapInput    = document.getElementById('feature_cap');
            const featTcInput     = document.getElementById('features_tc');
            const featLcInput     = document.getElementById('feature_lc');
            const featWithLcInput = document.getElementById('feature_with_lc');
            const nameInput       = document.getElementById('name');

            let userModifiedTc     = false;
            let userModifiedLc     = false;
            let userModifiedWithLc = (featWithLcInput && featWithLcInput.value.trim() !== '');

            if (featWithLcInput) {
                featWithLcInput.addEventListener('input', function() {
                    userModifiedWithLc = (this.value.trim() !== '');
                });
            }

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
                    if (!userModifiedWithLc && featWithLcInput) {
                        featWithLcInput.value = val ? ('with ' + val.toLowerCase()) : '';
                    }
                });
            }
        });
    </script>
@endsection
