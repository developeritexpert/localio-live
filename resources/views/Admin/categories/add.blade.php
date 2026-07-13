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
        });
    </script>
@endsection
