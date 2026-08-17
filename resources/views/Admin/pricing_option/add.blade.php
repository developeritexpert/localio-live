@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    {{ isset($pricing_data) ? 'Update Pricing Option' : 'Add Pricing Option' }}
                </h4>
            </div>
            <div>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('priceoptionsAddprocess') }}" class="form-validate" novalidate="novalidate" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pricing_option_id" value="{{ isset($pricing_data) ? $pricing_data->id : '' }}" />

                    <div class="row g-gs">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Offer Name</label>
                                <div class="form-control-wrap">
                                    @php
                                        $editName = '';
                                        if (isset($pricing_data)) {
                                            $engTrans = $pricing_data->translations->where('lang_id', 1)->first();
                                            $editName = $engTrans->name ?? (optional($pricing_data->translations->first())->name ?? $pricing_data->slug);
                                        }
                                    @endphp
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $editName) }}" required />
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Button Text -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="button_text">Button Text</label>
                                <div class="form-control-wrap">
                                    @php
                                        $editButtonText = 'Claim now';
                                        if (isset($pricing_data)) {
                                            $engTrans = $pricing_data->translations->where('lang_id', 1)->first();
                                            $editButtonText = $engTrans->button_text ?? (optional($pricing_data->translations->first())->button_text ?? 'Claim now');
                                        }
                                    @endphp
                                    <input type="text" class="form-control" id="button_text" name="button_text"
                                        value="{{ old('button_text', $editButtonText) }}" placeholder="e.g. Claim now" />
                                </div>
                                @error('button_text')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Scope Selector -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label d-block">Offer Scope</label>
                                @php
                                    $currentScope = old('scope', isset($pricing_data) ? $pricing_data->scope : 'global');
                                @endphp
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input scope-radio" id="scope_global" name="scope" value="global" {{ $currentScope === 'global' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="scope_global"><strong>Global</strong> (Applies to all categories)</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline ms-3">
                                    <input type="radio" class="custom-control-input scope-radio" id="scope_category" name="scope" value="category_specific" {{ $currentScope === 'category_specific' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="scope_category"><strong>Category Specific</strong> (Assign to selected categories)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Category Selector (Shown when Category Specific is selected) -->
                        <div class="col-md-12" id="categoryContainer" style="{{ $currentScope === 'category_specific' ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label class="form-label">Assign to Categories</label>
                                @php
                                    $assignedCategoryIds = old('categories', isset($pricing_data) ? $pricing_data->categories->pluck('id')->toArray() : []);
                                @endphp
                                <div class="card card-bordered p-3" style="max-height: 300px; overflow-y: auto;">
                                    @if(isset($allCategories) && count($allCategories) > 0)
                                        @foreach($allCategories as $cat)
                                            @php
                                                $catName = optional($cat->categoryTranslations->first())->name ?? ('Category #' . $cat->id);
                                            @endphp
                                            <div class="form-check mb-1">
                                                <input type="checkbox" class="form-check-input parent-category-checkbox" name="categories[]" value="{{ $cat->id }}" id="cat_{{ $cat->id }}" {{ in_array($cat->id, $assignedCategoryIds) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="cat_{{ $cat->id }}">{{ $catName }}</label>
                                            </div>

                                         {{--
                                            @php
                                                $subCats = ($cat->subCategories && count($cat->subCategories) > 0) ? $cat->subCategories : ($cat->children ?? []);
                                            @endphp

                                            @if($subCats && count($subCats) > 0)
                                                <div class="ms-4 mb-2">
                                                    @foreach($subCats as $subcat)
                                                        @php
                                                            $subcatName = optional($subcat->categoryTranslations->first())->name ?? ('Subcategory #' . $subcat->id);
                                                        @endphp

                                                        <div class="form-check mb-1">
                                                            <input type="checkbox"
                                                                class="form-check-input subcategory-checkbox"
                                                                name="categories[]"
                                                                value="{{ $subcat->id }}"
                                                                id="cat_{{ $subcat->id }}"
                                                                data-parent-id="{{ $cat->id }}"
                                                                {{ in_array($subcat->id, $assignedCategoryIds) ? 'checked' : '' }}>

                                                            <label class="form-check-label" for="cat_{{ $subcat->id }}">
                                                                {{ $subcatName }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            --}}
                                        @endforeach
                                    @else
                                        <p class="text-muted">No categories available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div class="col-md-12 mt-3">
                            <div class="form-group">
                                <label class="form-label d-block" for="status-toggle">Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="status-toggle" name="status"
                                        {{ isset($pricing_data) ? ($pricing_data->status ? 'checked' : '') : 'checked' }}>
                                    <label class="custom-control-label" for="status-toggle" id="status-label">
                                        {{ isset($pricing_data) && !$pricing_data->status ? 'Inactive' : 'Active' }}
                                    </label>
                                </div>
                                @error('status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">{{ isset($pricing_data) ? 'Update Pricing Option' : 'Save Pricing Option' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Scope radio toggle logic
            const scopeRadios = document.querySelectorAll('.scope-radio');
            const categoryContainer = document.getElementById('categoryContainer');

            scopeRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.value === 'category_specific') {
                        categoryContainer.style.display = 'block';
                    } else {
                        categoryContainer.style.display = 'none';
                    }
                });
            });

            // Parent checkbox selects all subcategories
            document.querySelectorAll('.parent-category-checkbox').forEach(parentCb => {
                parentCb.addEventListener('change', function () {
                    const catId = this.value;
                    document.querySelectorAll(`.subcategory-checkbox[data-parent-id="${catId}"]`).forEach(subCb => {
                        subCb.checked = this.checked;
                    });
                });
            });

            // Status toggle label
            const toggle = document.getElementById('status-toggle');
            const label = document.getElementById('status-label');

            function updateLabel() {
                if (toggle.checked) {
                    label.textContent = 'Active';
                } else {
                    label.textContent = 'Inactive';
                }
            }

            if (toggle && label) {
                toggle.addEventListener('change', updateLabel);
                updateLabel();
            }
        });
    </script>
@endsection
