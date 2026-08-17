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
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="comparison_slug" name="comparison_slug"
                                            value="{{ isset($category_data) ? ($category_data['comparison_slug'] ?? '') : old('comparison_slug') }}" 
                                            placeholder="e.g. business-name-comparison" readonly />
                                        <button class="btn btn-light" type="button" id="edit_comparison_slug_btn" title="Edit Comparison Slug">
                                            <em class="icon ni ni-edit"></em>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">Auto-assigned as <code>[category-name]-comparison</code>. Click the pencil icon to edit manually.</small>
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

                        <div class="col-md-12" id="important_category_group">
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
                                        Mark as Important (Header Navigation)
                                    </label>
                                </div>
                            </div>
                        </div>



                        

                        <!-- Rating Criteria Section -->
                        <div class="col-md-12 mt-4" id="rating_criteria_section">
                            <h5 class="title mb-1">Rating Criteria</h5>
                            <p class="text-muted small mb-3">Configure default rating criteria descriptions and define category or subcategory specific criteria.</p>

                            <!-- Default Rating Criteria (3 Mandatory Defaults) -->
                            <div class="card card-bordered bg-light mb-3">
                                <div class="card-inner py-3">
                                    <h6 class="title mb-2 text-primary"><i class="fa fa-lock me-1"></i> Default Rating Criteria (Global Defaults)</h6>
                                    <p class="text-muted small mb-3">These 3 criteria apply to all categories and cannot be removed. You can add category-specific short descriptions for each.</p>
                                    <div class="row g-2">
                                        @foreach($default_criteria as $def)
                                            <div class="col-md-12 mb-3 p-2 bg-white rounded border">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="fw-bold me-2">{{ $def['name'] }}</span>
                                                    <span class="badge bg-secondary">Default</span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm" name="default_rating_criteria[{{ $def['key'] }}][description]" value="{{ $def['description'] ?? '' }}" placeholder="Add short description for {{ $def['name'] }} (shown in review modal)" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Inherited Main Category Criteria (if subcategory) -->
                            @if(isset($inherited_criteria) && count($inherited_criteria) > 0)
                                <div class="card card-bordered bg-light mb-3">
                                    <div class="card-inner py-3">
                                        <h6 class="title mb-2 text-info"><i class="fa fa-sitemap me-1"></i> Inherited Main Category Criteria</h6>
                                        <p class="text-muted small mb-3">These criteria are inherited from the parent main category.</p>
                                        <div class="row g-2">
                                            @foreach($inherited_criteria as $inherited)
                                                <div class="col-md-12 p-2 bg-white rounded border mb-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="fw-bold me-2">{{ $inherited->name }}</span>
                                                        <span class="badge bg-info">Inherited from Main Category</span>
                                                    </div>
                                                    @if($inherited->description)
                                                        <small class="text-muted d-block">{{ $inherited->description }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Category / Subcategory Specific Custom Criteria -->
                            <div class="card card-bordered mb-3">
                                <div class="card-inner py-3">
                                    <h6 class="title mb-2"><i class="fa fa-tags me-1"></i> Additional Criteria for this Category / Subcategory</h6>
                                    <p class="text-muted small mb-3">Add custom criteria specific to this category or subcategory.</p>
                                    
                                    <div id="criteria_wrapper">
                                        @if(isset($custom_criteria) && count($custom_criteria) > 0)
                                            @foreach($custom_criteria as $index => $criterion)
                                                <div class="criteria-card border p-3 rounded mb-2 bg-white position-relative">
                                                    <div class="row g-2 align-items-center">
                                                        <div class="col-md-5">
                                                            <label class="form-label mb-1 small fw-bold">Criteria Name</label>
                                                            <input type="text" class="form-control" name="existing_rating_criteria[{{ $criterion->id }}][name]" value="{{ $criterion->name }}" placeholder="e.g. Performance, Scalability" required />
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label mb-1 small fw-bold">Short Description Text</label>
                                                            <input type="text" class="form-control" name="existing_rating_criteria[{{ $criterion->id }}][description]" value="{{ $criterion->description }}" placeholder="Description shown below criteria in review modal" />
                                                        </div>
                                                        <div class="col-md-1 text-end">
                                                            <label class="form-label mb-1 d-block opacity-0">Remove</label>
                                                            <button type="button" class="btn btn-outline-danger remove-criteria"><i class="fa fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add_criteria_btn">
                                        <i class="fa fa-plus me-1"></i> Add Additional Criteria
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 mt-4">
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

            function toggleParentDropdown() {
                if (isParentCheckbox && isParentCheckbox.checked) {
                    parentCategoryGroup.style.display = 'none';
                    if (parentCategorySelect) {
                        parentCategorySelect.value = '';
                    }
                } else {
                    parentCategoryGroup.style.display = 'block';
                }
            }

            if (isParentCheckbox) {
                isParentCheckbox.addEventListener('change', toggleParentDropdown);
            }

            // Comparison Slug Auto-generate & Pencil Edit Toggle
            const nameInput = document.getElementById('name');
            const comparisonSlugInput = document.getElementById('comparison_slug');
            const editComparisonSlugBtn = document.getElementById('edit_comparison_slug_btn');
            let isSlugManuallyEdited = {{ (isset($category_data) && !empty($category_data['comparison_slug'])) || old('comparison_slug') ? 'true' : 'false' }};

            function generateSlug(text) {
                return text
                    .toString()
                    .toLowerCase()
                    .trim()
                    .replace(/&/g, '-and-')
                    .replace(/[\s\W-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            function updateComparisonSlug() {
                if (!isSlugManuallyEdited && nameInput && comparisonSlugInput) {
                    const rawSlug = generateSlug(nameInput.value);
                    comparisonSlugInput.value = rawSlug ? rawSlug + '-comparison' : '';
                }
            }

            if (nameInput && comparisonSlugInput) {
                if (!comparisonSlugInput.value && nameInput.value.trim()) {
                    updateComparisonSlug();
                }

                nameInput.addEventListener('input', function() {
                    updateComparisonSlug();
                });

                comparisonSlugInput.addEventListener('input', function() {
                    isSlugManuallyEdited = true;
                });

                comparisonSlugInput.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.value = generateSlug(this.value);
                    }
                });
            }

            if (editComparisonSlugBtn && comparisonSlugInput) {
                editComparisonSlugBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (comparisonSlugInput.readOnly) {
                        comparisonSlugInput.readOnly = false;
                        comparisonSlugInput.focus();
                        isSlugManuallyEdited = true;
                        editComparisonSlugBtn.innerHTML = '<em class="icon ni ni-check"></em>';
                        editComparisonSlugBtn.classList.remove('btn-outline-light', 'btn-white');
                        editComparisonSlugBtn.classList.add('btn-primary');
                        editComparisonSlugBtn.title = 'Lock Slug';
                    } else {
                        comparisonSlugInput.readOnly = true;
                        editComparisonSlugBtn.innerHTML = '<em class="icon ni ni-edit"></em>';
                        editComparisonSlugBtn.classList.add('btn-outline-light', 'btn-white');
                        editComparisonSlugBtn.classList.remove('btn-primary');
                        editComparisonSlugBtn.title = 'Edit Comparison Slug';
                    }
                });
            }

            // Rating Criteria Logic
            const addCriteriaBtn = document.getElementById('add_criteria_btn');
            const criteriaWrapper = document.getElementById('criteria_wrapper');
            let newCriteriaIndex = 0;

            if (addCriteriaBtn && criteriaWrapper) {
                addCriteriaBtn.addEventListener('click', function() {
                    newCriteriaIndex++;
                    const card = document.createElement('div');
                    card.className = 'criteria-card border p-3 rounded mb-2 bg-white position-relative';
                    card.innerHTML = `
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label mb-1 small fw-bold">Criteria Name</label>
                                <input type="text" class="form-control" name="new_rating_criteria[${newCriteriaIndex}][name]" placeholder="e.g. Server management" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-1 small fw-bold">Short Description Text</label>
                                <input type="text" class="form-control" name="new_rating_criteria[${newCriteriaIndex}][description]" placeholder="Description shown below criteria in review modal" />
                            </div>
                            <div class="col-md-1 text-end">
                                <label class="form-label mb-1 d-block opacity-0">Remove</label>
                                <button type="button" class="btn btn-outline-danger remove-criteria"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    `;
                    criteriaWrapper.appendChild(card);
                });

                criteriaWrapper.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-criteria');
                    if (removeBtn) {
                        removeBtn.closest('.criteria-card').remove();
                    }
                });
            }
        });
    </script>
@endsection
