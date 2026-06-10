@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Filter and Options</h4>
            </div>
            <div class="nk-block-head-content">
                <a class="btn btn-outline-primary btn-preview" data-toggle="modal" data-target="#previewModal">
                    <em class="icon ni ni-eye"></em><span>Preview</span>
                </a>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('filter-addProcc') }}" class="form-validate" novalidate="novalidate" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row g-gs">
                        <!-- Name Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Filter Label</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" placeholder="e.g., Storage Space" />
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                data-target="#duplicateModal">
                                <em class="icon ni ni-copy"></em><span>Duplicate filter from category</span>
                            </button>
                        </div>

                        <input type="hidden" class="form-control" id="slug" name="slug"
                            value="{{ old('slug') }}" />

                        <!-- Categories Selection -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-control-wrap" style="display:none">
                                    <select class="form-select js-select2 d-none" id="category_select" name="category_id"
                                        data-search="on">
                                        @if ($categories)
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @error('category_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Filter Type Selection -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="type">Filter Type</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="filter_type" name="filter_type_id">
                                        @foreach ($filterTypes as $filterType)
                                            <option value="{{ $filterType->id }}" data-slug="{{ $filterType->slug }}"
                                                {{ old('filter_type_id') == $filterType->id ? 'selected' : '' }}>
                                                {{ $filterType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('type')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6" style="display: none">
                                <div class="form-group pt-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="is_required"
                                            id="is_required" {{ old('is_required') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_required">
                                            Required Filter
                                        </label>
                                    </div>
                                    <div class="form-note text-muted">
                                        If checked, Page Will be loaded with this selected
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div id="duplicated-filters-container" class="mt-4">
                            <!-- Duplicated filters will appear here -->
                        </div>
                        <!-- Filter Type-Specific Configuration -->
                        <div class="col-md-12 mt-3">
                            <div class="filter-type-config-header" style="display: none;">
                                <h6 class="overline-title text-primary mb-3">Filter Configuration</h6>
                                <hr>
                            </div>
                        </div>
                        <!-- Dynamic Options Fields - For Dropdown, Checkbox, Radio -->
                        <div class="col-md-12 type-fields" id="options-fields" style="display: none;">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="form-group">
                                        <label class="form-label">Options <span class="text-danger">*</span></label>
                                        <div class="form-note mb-3">
                                            Add options for customers to choose from. Drag to reorder.
                                        </div>


                                        <div id="options-container" class="sortable-options">
                                            <!-- Options will be added here -->
                                        </div>


                                        <button type="button" id="add-option" class="btn btn-primary btn-dim mt-3">
                                            <em class="icon ni ni-plus"></em> Add Option
                                        </button>


                                        @error('options')
                                            <div class="error text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                        @error('options.*')
                                            <div class="error text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Range Slider Fields -->
                        <div class="col-md-12 type-fields" id="range-slider-fields" style="display: none;">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="min_value">Min Value <span
                                                        class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="number" step="0.01"
                                                        class="form-control @error('min_value') error @enderror"
                                                        id="min_value" name="min_value"
                                                        value="{{ old('min_value', 0) }}">
                                                </div>
                                                @error('min_value')
                                                    <div class="error text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="max_value">Max Value <span
                                                        class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="number" step="0.01"
                                                        class="form-control @error('max_value') error @enderror"
                                                        id="max_value" name="max_value"
                                                        value="{{ old('max_value', 100) }}">
                                                </div>
                                                @error('max_value')
                                                    <div class="error text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="unit">Unit (Optional)</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="unit"
                                                        name="unit" placeholder="e.g., GB"
                                                        value="{{ old('unit') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Toggle Fields -->
                        <div class="col-md-12 type-fields" id="toggle-fields" style="display: none;">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="on_label">On Label</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="on_label"
                                                        name="on_label" placeholder="e.g., Yes"
                                                        value="{{ old('on_label', 'Yes') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="off_label">Off Label</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="off_label"
                                                        name="off_label" placeholder="e.g., No"
                                                        value="{{ old('off_label', 'No') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="default_toggle" id="default_toggle"
                                                        {{ old('default_toggle') ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="default_toggle">
                                                        Default to On
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Preview</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div id="preview-content" class="p-3">
                        <!-- Preview content will be loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Duplicate Modal -->
    <div class="modal fade" id="duplicateModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Duplicate Filters from Category</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="source_category">Select Source Category</label>
                        <select class="form-select select2" id="source_category">
                            @if ($categories)
                                <option>Select a Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group mt-3" id="available-filters">
                        <!-- Available filters will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="duplicate-selected">Duplicate Selected</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        $(document).ready(function() {
            // Initialize Sortable for options
            var sortable = new Sortable(document.getElementById('options-container'), {
                handle: '.handle',
                animation: 150
            });

            // Update the slug field based on the name field
            $('#name').on('input', function() {
                let name = $(this).val().toLowerCase();
                let slug = name.replace(/\s+/g, "-").replace(/\//g, "-");
                $('#slug').val(slug);
            });

            // Control form fields based on filter type
            $('#filter_type').change(function() {
                var filterType = $(this).find('option:selected').data('slug');
                console.log(filterType);
                $('.type-fields').hide();

                if (filterType === 'dropdown' || filterType === 'checkbox' || filterType === 'radio') {
                    $('#options-fields').show();
                } else if (filterType === 'slider') {
                    $('#range-slider-fields').show();
                } else if (filterType === 'toggle') {
                    $('#toggle-fields').show();
                }
            });

            // Trigger change event to initialize form correctly
            $('#filter_type').trigger('change');

            // Add dynamic option fields
            let optionCounter = $('.option-group').length;
            $('#add-option').click(function() {
                $('#options-container').append(`
                    <div class="form-group row option-group mt-2">
                        <div class="col-lg-9 col-md-9 col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text handle"><em class="icon ni ni-menu"></em></span>
                                </div>
                                <input type="text" name="options[]" class="form-control" placeholder="Enter option label">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="default_options[${optionCounter}]" id="default_option_${optionCounter}">
                                <label class="custom-control-label" for="default_option_${optionCounter}">Default</label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-sm remove-option"><em class="icon ni ni-trash-fill"></em></button>
                        </div>
                    </div>
                `);
                optionCounter++;

                // Refresh sortable to include new elements
                sortable.destroy();
                sortable = new Sortable(document.getElementById('options-container'), {
                    handle: '.handle',
                    animation: 150
                });
            });

            // Remove option field, ensuring at least one remains
            $('#options-container').on('click', '.remove-option', function() {
                if ($('.option-group').length > 1) {
                    $(this).closest('.option-group').remove();
                }
            });
            // Fix for preview functionality
            $('.btn-preview').on('click', function() {
                let formData = $('form').serializeArray();
                let data = {};
                formData.forEach(el => {
                    if (el.name !== 'options[]') {
                        data[el.name] = el.value;
                    }
                });

                console.log(data); // Fixed from formdata to data

                // Get selected filter type slug
                let typeSlug = $('#filter_type option:selected').data('slug');
                data['filter_type_slug'] = typeSlug;

                // For checkbox default options
                data['default_options'] = {};
                $('input[name^="default_options"]:checked').each(function() {
                    let index = $(this).attr('name').match(/\[(\d+)\]/)[1];
                    data['default_options'][index] = true;
                });

                // For options array
                data['options'] = [];
                $('input[name="options[]"]').each(function() {
                    data['options'].push($(this).val());
                });
                // console.log(data);

                $.post("{{ route('filter.preview') }}", data, function(html) {
                    $('#preview-content').html(html);
                }).fail(function() {
                    $('#preview-content').html(
                        '<div class="alert alert-danger">Failed to load preview.</div>');
                });
            });

            // Source category change event
            $('#source_category').on('change', function() {
                let catId = $(this).val();
                if (!catId) return;

                $('#available-filters').html('<p>Loading...</p>');

                $.get("{{ url('admin-dashboard/filter/fetch-filters') }}/" + catId, function(html) {
                    $('#available-filters').html(html);
                }).fail(function() {
                    $('#available-filters').html(
                        '<div class="text-danger">Failed to load filters.</div>');
                });
            });

            // Duplicate Selected button click event
            let filterCounter = 0;

            $('#duplicate-selected').on('click', function() {
                let selected = [];
                $('.duplicate-filter:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    alert('Please select at least one filter to duplicate.');
                    return;
                }

                $.ajax({
                    url: "{{ route('filter.fetch') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        filter_ids: selected
                    },
                    success: function(response) {
                        if (response.status && response.filters.length) {
                            response.filters.forEach(filter => {
                                filterCounter++;
                                const id = filterCounter;
                                const typeSlug = filter.filter_type ? filter.filter_type
                                    .slug : 'dropdown';

                                let html = `
                    <div class="duplicated-filter card card-bordered p-3 mb-3">
                        <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Duplicated Filter</h5>
                            <button type="button" class="btn btn-icon btn-sm btn-danger remove-duplicated">
                                <em class="icon ni ni-cross"></em>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Filter Label</label>
                                <input type="text" name="filters[${id}][name]" class="form-control" value="${filter.name}">
                                <input type="hidden" name="filters[${id}][slug]" value="${filter.slug}">
                                <input type="hidden" name="filters[${id}][category_id]" value="${$('#category_select').val()}">
                                <input type="hidden" name="filters[${id}][filter_type_id]" value="${filter.filter_type_id}">
                                <input type="hidden" name="filters[${id}][is_required]" value="${filter.is_required ? 1 : 0}">
                            </div>`;

                                // Add specific fields based on filter type
                                if (['dropdown', 'checkbox', 'radio'].includes(
                                        typeSlug)) {
                                    html += `<div class="form-group">
                            <label class="form-label">Options</label>
                            <div class="options-list">`;

                                    // Add each option
                                    if (filter.options && filter.options.length) {
                                        filter.options.forEach((opt, i) => {
                                            html += `
                                <div class="input-group mb-2">
                                    <input type="text" name="filters[${id}][options][]" class="form-control" value="${opt.name}">
                                </div>`;
                                        });
                                    }

                                    html += `</div>
                        </div>`;
                                }

                                // Add range slider fields
                                if (typeSlug === 'slider') {
                                    html += `
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Min Value</label>
                                                <input type="number" step="0.01" class="form-control" name="filters[${id}][min_value]" value="${filter.min_value || 0}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Max Value</label>
                                                <input type="number" step="0.01" class="form-control" name="filters[${id}][max_value]" value="${filter.max_value || 100}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Unit</label>
                                                <input type="text" class="form-control" name="filters[${id}][unit]" value="${filter.unit || ''}">
                                            </div>
                                        </div>
                                    </div>`;
                                }

                                // Add toggle fields
                                if (typeSlug === 'toggle') {
                                    html += `
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">On Label</label>
                                                <input type="text" class="form-control" name="filters[${id}][on_label]" value="${filter.on_label || 'Yes'}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Off Label</label>
                                                <input type="text" class="form-control" name="filters[${id}][off_label]" value="${filter.off_label || 'No'}">
                                            </div>
                                        </div>
                                    </div>`;
                                }

                                html += `</div></div>`;

                                $('#duplicated-filters-container').append(html);
                            });

                            // Close the modal after adding filters
                            $('#duplicateModal').modal('hide');
                        } else {
                            alert('No filters were received from the server.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred while fetching filters: ' + error);
                    }
                });
            });

            // Remove duplicated filter
            $(document).on('click', '.remove-duplicated', function() {
                $(this).closest('.duplicated-filter').remove();
            });
        });
    </script>
@endsection
