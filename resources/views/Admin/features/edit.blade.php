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
                            $valCap = old('category_cap', $featTrans?->category_cap ?? ($featTrans?->feature_cap ?? ($featTrans?->name ?? '')));
                            $valTc  = old('category_tc', $featTrans?->category_tc ?? ($featTrans?->feature_tc ?? ''));
                            $valLc  = old('category_lc', $featTrans?->category_lc ?? ($featTrans?->feature_lc ?? ''));
                        @endphp
                        <!-- Feature name capitalized [category_cap] -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="category_cap">Feature name capitalized <code class="text-primary">[category_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="category_cap" name="category_cap"
                                        value="{{ $valCap }}" placeholder="e.g. Access logs" required />
                                    <input type="hidden" id="name" name="name"
                                        value="{{ old('name', $valCap) }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Access logs</small>
                                @error('category_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Feature name title case [category_tc] -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="category_tc">Feature name title case <code class="text-primary">[category_tc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="category_tc" name="category_tc"
                                        value="{{ $valTc }}" placeholder="e.g. Access Logs" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Access Logs</small>
                                @error('category_tc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Feature name lowercase [category_lc] -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="category_lc">Feature name lowercase <code class="text-primary">[category_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="category_lc" name="category_lc"
                                        value="{{ $valLc }}" placeholder="e.g. access logs" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: access logs</small>
                                @error('category_lc')
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
            const catCapInput = document.getElementById('category_cap');
            const catTcInput  = document.getElementById('category_tc');
            const catLcInput  = document.getElementById('category_lc');
            const nameInput   = document.getElementById('name');

            let userModifiedTc = false;
            let userModifiedLc = false;

            if (catTcInput && catTcInput.value.trim() !== '') userModifiedTc = true;
            if (catLcInput && catLcInput.value.trim() !== '') userModifiedLc = true;

            if (catTcInput) {
                catTcInput.addEventListener('input', function() {
                    userModifiedTc = (this.value.trim() !== '');
                });
            }
            if (catLcInput) {
                catLcInput.addEventListener('input', function() {
                    userModifiedLc = (this.value.trim() !== '');
                });
            }

            function toTitleCase(str) {
                return str.toLowerCase().replace(/(?:^|\s|\/|-)\w/g, function(match) {
                    return match.toUpperCase();
                });
            }

            if (catCapInput) {
                catCapInput.addEventListener('input', function() {
                    const val = this.value.trim();
                    if (nameInput) nameInput.value = this.value;

                    if (!userModifiedTc && catTcInput) {
                        catTcInput.value = val ? toTitleCase(val) : '';
                    }
                    if (!userModifiedLc && catLcInput) {
                        catLcInput.value = val ? val.toLowerCase() : '';
                    }
                });
            }
        });
    </script>
@endsection
