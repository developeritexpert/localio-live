@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    {{ isset($category_data) ? 'Update Category' : 'Add Category' }}
                </h4>
            </div>
            <div>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('add-category-process') }}" class="form-validate" novalidate="novalidate" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="category_id" value="{{isset($category_data)?$category_data['id']:''}}" />
                    <div class="row g-gs">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="name">Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{isset($category_data)?$category_data['name']:old('name') }}" />
                                </div>
                                @error('name')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Title (Most Popular Section)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ isset($category_data) ? ($category_data['title'] ?? '') : old('title') }}" />
                                </div>
                                @error('title')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="comparison_slug">Comparison Slug (SEO Route)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="comparison_slug" name="comparison_slug"
                                        value="{{ isset($category_data) ? ($category_data['comparison_slug'] ?? '') : old('comparison_slug') }}" placeholder="e.g. software-comparison" />
                                </div>
                                @error('comparison_slug')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="description">Description</label>
                                <div class="form-control-wrap">
                                    <textarea style="width: 100%; height: 151px;" name="description" rows="2" cols="20">{{ isset($category_data) ? strip_tags($category_data['description']) : old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label d-block" for="is_parent">
                                    Category Type
                                </label>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="is_parent"
                                        name="is_parent"
                                        value="1"
                                        {{ (!isset($category) || $category->parent_id === null) ? 'checked' : '' }}
                                        {{ $hasSubcategories ? 'disabled' : '' }}
                                        {{ $hasItems ? 'disabled' : '' }}
                                    >
                                    <label class="form-check-label" for="is_parent">
                                        This is a parent category
                                    </label>
                                </div>
                                @if($hasSubcategories)
                                    <small class="text-warning d-block mt-1">Cannot convert to subcategory: this category contains active subcategories.</small>
                                    <input type="hidden" name="is_parent" value="1" />
                                @endif
                                @if($hasItems)
                                    <small class="text-warning d-block mt-1">Cannot convert to parent category: this category contains active businesses or products.</small>
                                    <input type="hidden" name="is_parent" value="0" />
                                @endif
                                @error('is_parent')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12" id="parent_category_group" style="display: {{ (!isset($category) || $category->parent_id === null) ? 'none' : 'block' }}">
                            <div class="form-group">
                                <label class="form-label" for="parent_id">Select Parent Category</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="parent_id" id="parent_id">
                                        <option value="">-- Select Parent --</option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}" 
                                                {{ (isset($category) && $category->parent_id == $parent->id) ? 'selected' : '' }}>
                                                {{ $parent->translation->name ?? 'Unnamed Parent' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('parent_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12" id="important_category_group" style="display: {{ (!isset($category) || $category->parent_id === null) ? 'none' : 'block' }}">
                            <div class="form-group">
                                <label class="form-label d-block" for="is_important">
                                    Important Category
                                </label>

                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="is_important"
                                        name="is_important"
                                        value="1"
                                        {{ isset($category_data) && $category_data['is_important'] == 1 ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="is_important">
                                        Mark as Important
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="image">Upload Image</label>
                                <div class="dz-message">
                                    <input type="file" class="form-control" name="image" id="image" >
                                </div>
                                @error('image')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @isset($category_image_url)
                            <div class="col-12">
                                <img src="{{ asset($category_image_url) }}" alt="Category Image" class="img-fluid rounded-circle" style="height: 50px;">
                            </div>
                        @endisset

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="image">Upload Icon</label>
                                <div class="dz-message">
                                    <input type="file" class="form-control" name="category_icon" id="categoryIcon"  >
                                </div>
                                @error('category_icon')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @isset($category_icon_url)
                            <div class="col-12">
                                <img src="{{ asset($category_icon_url) }}" alt="Category Image" class="img-fluid rounded-circle" style="height: 50px;">
                            </div>
                        @endisset

                        <!-- Rating Criteria Section -->
                        <div class="col-md-12 mt-4" id="rating_criteria_section">
                            <h5 class="title mb-3">Rating Criteria</h5>
                            <p class="text-muted small">Add the criteria users will rate businesses on (e.g. "Value for money", "Ease of use").</p>
                            <div id="criteria_wrapper">
                                @if(isset($rating_criteria) && count($rating_criteria) > 0)
                                    @foreach($rating_criteria as $index => $criterion)
                                        <div class="d-flex mb-2 criteria-row">
                                            <input type="text" class="form-control" name="existing_rating_criteria[{{ $criterion->id }}]" value="{{ $criterion->name }}" placeholder="Enter criteria name" required />
                                            <button type="button" class="btn btn-danger ms-2 remove-criteria"><i class="fa fa-trash"></i></button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="d-flex mb-2 criteria-row">
                                        <input type="text" class="form-control" name="new_rating_criteria[]" placeholder="Enter criteria name (e.g. Ease of use)" />
                                        <button type="button" class="btn btn-danger ms-2 remove-criteria"><i class="fa fa-trash"></i></button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add_criteria_btn">
                                <i class="fa fa-plus me-1"></i> Add Another Criteria
                            </button>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 mt-5">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">{{isset($category_data)?'Update Category':'Save Category' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isParentCheckbox = document.getElementById('is_parent');
            const parentCategoryGroup = document.getElementById('parent_category_group');
            const parentCategorySelect = document.getElementById('parent_id');
            const importantCategoryGroup = document.getElementById('important_category_group');
            const importantCategoryCheckbox = document.getElementById('is_important');

            function toggleParentDropdown() {
                if (isParentCheckbox && isParentCheckbox.checked) {
                    parentCategoryGroup.style.display = 'none';
                    importantCategoryGroup.style.display = 'none';
                    if (parentCategorySelect) {
                        parentCategorySelect.value = '';
                    }
                    if (importantCategoryCheckbox) {
                        importantCategoryCheckbox.checked = false;
                    }
                } else {
                    parentCategoryGroup.style.display = 'block';
                    importantCategoryGroup.style.display = 'block';
                }
            }

            if (isParentCheckbox) {
                isParentCheckbox.addEventListener('change', toggleParentDropdown);
            }

            // Rating Criteria Logic
            const addCriteriaBtn = document.getElementById('add_criteria_btn');
            const criteriaWrapper = document.getElementById('criteria_wrapper');

            if (addCriteriaBtn && criteriaWrapper) {
                addCriteriaBtn.addEventListener('click', function() {
                    const row = document.createElement('div');
                    row.className = 'd-flex mb-2 criteria-row';
                    row.innerHTML = `
                        <input type="text" class="form-control" name="new_rating_criteria[]" placeholder="Enter criteria name" required />
                        <button type="button" class="btn btn-danger ms-2 remove-criteria"><i class="fa fa-trash"></i></button>
                    `;
                    criteriaWrapper.appendChild(row);
                });

                criteriaWrapper.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-criteria');
                    if (removeBtn) {
                        const rows = criteriaWrapper.querySelectorAll('.criteria-row');
                        if (rows.length > 1) {
                            removeBtn.closest('.criteria-row').remove();
                        } else {
                            // If it's the last one, just clear the value
                            removeBtn.closest('.criteria-row').querySelector('input').value = '';
                        }
                    }
                });
            }
        });
    </script>
@endsection
