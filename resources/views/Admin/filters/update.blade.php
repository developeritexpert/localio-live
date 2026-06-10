@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Update Filter</h4>
            </div>
            <div class="nk-block-head-content">
                <a class="btn btn-outline-primary btn-preview" data-toggle="modal" data-target="#previewModal">
                    <em class="icon ni ni-eye preview"></em><span>Preview</span>
                </a>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('update-filter-updateProcc') }}" class="form-validate" novalidate="novalidate"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $filter->id }}">
                    <div class="row g-gs">
                        <!-- Name Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Filter Label</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $filter->translations->first()?->name ?? $filter->name) }}">
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6" style="display: none">
                            <div class="form-group">
                                <label class="form-label" for="slug">Filter Slug</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="slug" name="slug"
                                        value="{{ old('slug', $filter->translations->first()->slug ?? $filter->slug) }}" />
                                </div>
                                @error('slug')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Categories Selection -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Category</label>
                                <div class="form-control-wrap">
                                    <select class="form-select js-select2" id="category_select" name="category_id"
                                        data-search="on">
                                        @if ($categories)
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $filter->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->translations->name ?? $category->name }}
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="type">Filter Type</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="filter_type" name="filter_type_id">
                                        @foreach ($filterTypes as $filterType)
                                            <option value="{{ $filterType->id }}" data-slug="{{ $filterType->slug }}"
                                                {{ old('filter_type_id', $filter->filter_type_id) == $filterType->id ? 'selected' : '' }}>
                                                {{ $filterType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('filter_type_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Required Switch -->
                        <div class="col-md-12" style='display: none;'>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="is_required" id="is_required"
                                        {{ old('is_required', $filter->is_required) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_required">Required Filter</label>
                                </div>
                            </div>
                        </div>
                        <!-- Dynamic Options Fields - For Dropdown, Checkbox, Radio -->
                        <div class="col-md-12 type-fields" id="options-fields">
                            <label class="form-label">Options</label>
                            <div id="options-container" class="sortable-options">
                                @if ($filter->filterType && in_array($filter->filterType->slug, ['dropdown', 'checkbox', 'radio']))
                                    @foreach ($filter->options as $option)
                                        <div class="form-group row option-group mt-2">
                                            <div class="col-lg-9 col-md-9 col-sm-9">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text handle"><em
                                                                class="icon ni ni-menu"></em></span>
                                                    </div>
                                                    <input type="text" name="options[{{ $option->id }}]"
                                                        class="form-control" placeholder="Enter option label"
                                                        value="{{ $option->translations->first()->name ?? $option->name }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="default_options[{{ $option->id }}]"
                                                        id="default_option_{{ $option->id }}"
                                                        {{ old("default_options.$option->id", $option->is_default) ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="default_option_{{ $option->id }}">Default</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-1 col-md-1 col-sm-1 d-flex align-items-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-option"><em
                                                        class="icon ni ni-trash-fill"></em></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div id="new-options-container"></div>
                            <button type="button" id="add-option" class="btn btn-primary btn-localio mt-3">
                                <em class="icon ni ni-plus"></em> Add Option
                            </button>
                                @error('options')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                                @error('new_options')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                        </div>

                        <!-- Range Slider Fields -->
                        <div class="col-md-12 type-fields" id="range-slider-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="min_value">Min Value</label>
                                        <div class="form-control-wrap">
                                            <input type="number" step="0.01" class="form-control" id="min_value"
                                                name="min_value"
                                                value="{{ old('min_value', $filter->options->first()->min_value ?? '') }}">
                                        </div>
                                        @error('min_value')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="max_value">Max Value</label>
                                        <div class="form-control-wrap">
                                            <input type="number" step="0.01" class="form-control" id="max_value"
                                                name="max_value"
                                                value="{{ old('max_value', $filter->options->first()->max_value ?? '') }}">
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
                                            <input type="text" class="form-control" id="unit" name="unit"
                                                placeholder="e.g., GB"
                                                value="{{ old('unit', $filter->options->first()->unit ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="default_range">Default Range (Optional)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="default_range"
                                                name="default_range" placeholder="e.g., 10,50"
                                                value="{{ old('default_range', $filter->options->first()->default_range ?? '') }}">
                                            <small class="text-muted">Enter as min,max (e.g., 10,50)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Toggle Fields -->
                        <div class="col-md-12 type-fields" id="toggle-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="form-label" for="on_label">On Label</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="on_label" name="on_label"
                                                placeholder="e.g., Yes"
                                                value="{{ old('on_label', $filter->options->first()->on_label ?? 'Yes') }}">
                                        </div>
                                        @error('on_label')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="form-label" for="off_label">Off Label</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="off_label" name="off_label"
                                                placeholder="e.g., No"
                                                value="{{ old('off_label', $filter->options->first()->off_label ?? 'No') }}">
                                        </div>
                                        @error('off_label')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label" for="default_toggle">Default State</label>
                                        <div class="custom-control custom-switch mt-2">
                                            <input type="checkbox" class="custom-control-input" name="default_toggle"
                                                id="default_toggle"
                                                {{ old('default_toggle', $filter->options->first()->default_toggle ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="default_toggle">On</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary">Update</button>
                                <a href="{{ route('categoryfilters', ['id' => $filter->category_id]) }}"
                                    class="btn btn-lg btn-outline-secondary">Cancel</a>
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
                console.log('Selected filter type:', filterType);
                $('.type-fields').hide();
                $('.filter-type-config-header').show();

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
            let optionCounter = $('.option-group').length || 0;
            $('#add-option').click(function() {
                optionCounter++;
                $('#options-container').append(`
            <div class="form-group row option-group mt-2">
                <div class="col-lg-9 col-md-9 col-sm-9">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text handle"><em class="icon ni ni-menu"></em></span>
                        </div>
                        <input type="text" name="new_options[]" class="form-control" placeholder="Enter option label">
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" name="new_default_options[${optionCounter}]" id="new_default_option_${optionCounter}">
                        <label class="custom-control-label" for="new_default_option_${optionCounter}">Default</label>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 d-flex align-items-center">
                    <button type="button" class="btn btn-danger btn-sm remove-option"><em class="icon ni ni-trash-fill"></em></button>
                </div>
            </div>
        `);

                // Refresh sortable to include new elements
                sortable.destroy();
                sortable = new Sortable(document.getElementById('options-container'), {
                    handle: '.handle',
                    animation: 150
                });
            });

            // Remove option field
            $(document).on('click', '.remove-option', function() {
                if ($('.option-group').length > 1) {
                    $(this).closest('.option-group').remove();
                } else {
                    alert('You must have at least one option');
                }
            });

            // Preview functionality
            $('.btn-preview').on('click', function() {
                let formData = $('form').serializeArray();
                let data = {};
                formData.forEach(el => data[el.name] = el.value);

                // Get selected filter type slug
                let typeSlug = $('#filter_type option:selected').data('slug');
                data['filter_type_slug'] = typeSlug;

                // For existing options with default options checked
                data['default_options'] = {};
                $('input[name^="default_options"]:checked').each(function() {
                    let index = $(this).attr('name').match(/\[(\d+)\]/)[1];
                    data['default_options'][index] = true;
                });

                // For new options with default options checked
                data['new_default_options'] = {};
                $('input[name^="new_default_options"]:checked').each(function() {
                    let index = $(this).attr('name').match(/\[(\d+)\]/)[1];
                    data['new_default_options'][index] = true;
                });

                // For existing options
                data['options'] = {};
                $('input[name^="options["]').each(function() {
                    let key = $(this).attr('name').match(/\[(\d+)\]/)[1];
                    data['options'][key] = $(this).val();
                });

                // For new options
                data['new_options'] = [];
                $('input[name="new_options[]"]').each(function() {
                    data['new_options'].push($(this).val());
                });

                $.ajax({
                    url: "/admin-dashboard/filter/preview",
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        ...data
                    },
                    success: function(html) {
                        $('#preview-content').html(html);
                    },
                    error: function() {
                        $('#preview-content').html(
                            '<div class="alert alert-danger">Failed to load preview.</div>');
                    }
                });
            });

            // Source category change event
            $('#source_category').on('change', function() {
                let catId = $(this).val();
                if (!catId) return;

                $('#available-filters').html(
                    '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
                );

                $.get("/admin-dashboard/filter/fetch-filters/" + catId, function(html) {
                    $('#available-filters').html(html);
                }).fail(function() {
                    $('#available-filters').html(
                        '<div class="alert alert-danger">Failed to load filters.</div>');
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
                    url: "/admin-dashboard/filter/fetch",
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        filter_ids: selected
                    },
                    success: function(response) {
                        if (response.status && response.filters.length) {
                            $('#duplicated-filters-container').html(
                                ''); // Clear existing duplicated filters

                            response.filters.forEach(filter => {
                                filterCounter++;
                                const id = filterCounter;
                                const typeSlug = filter.filter_type ? filter.filter_type
                                    .slug : 'dropdown';

                                let html = `
                        <div class="duplicated-filter card card-bordered p-3 mb-3">
                            <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Duplicated Filter: ${filter.name}</h5>
                                <button type="button" class="btn btn-icon btn-sm btn-danger remove-duplicated">
                                    <em class="icon ni ni-cross"></em>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Filter Label</label>
                                    <input type="text" name="duplicated[${id}][name]" class="form-control" value="${filter.name}">
                                    <input type="hidden" name="duplicated[${id}][slug]" value="${filter.slug}">
                                    <input type="hidden" name="duplicated[${id}][category_id]" value="${$('#category_select').val()}">
                                    <input type="hidden" name="duplicated[${id}][filter_type_id]" value="${filter.filter_type_id}">
                                    <input type="hidden" name="duplicated[${id}][is_required]" value="${filter.is_required ? 1 : 0}">
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
                                            const isDefault = opt.is_default ?
                                                'checked' : '';
                                            html += `
                                    <div class="input-group mb-2">
                                        <input type="text" name="duplicated[${id}][options][]" class="form-control" value="${opt.name}">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                           name="duplicated[${id}][default_options][${i}]"
                                                           id="duplicated_${id}_default_${i}" ${isDefault}>
                                                    <label class="custom-control-label" for="duplicated_${id}_default_${i}">Default</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                        });
                                    }

                                    html += `</div>
                            </div>`;
                                }

                                // Add range slider fields
                                if (typeSlug === 'slider') {
                                    const minValue = filter.options && filter.options[
                                        0] ? filter.options[0].min_value || 0 : 0;
                                    const maxValue = filter.options && filter.options[
                                            0] ? filter.options[0].max_value || 100 :
                                        100;
                                    const unit = filter.options && filter.options[0] ?
                                        filter.options[0].unit || '' : '';
                                    const defaultRange = filter.options && filter
                                        .options[0] ? filter.options[0].default_range ||
                                        '' : '';

                                    html += `
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Min Value</label>
                                        <input type="number" step="0.01" class="form-control"
                                               name="duplicated[${id}][min_value]" value="${minValue}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Max Value</label>
                                        <input type="number" step="0.01" class="form-control"
                                               name="duplicated[${id}][max_value]" value="${maxValue}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Unit</label>
                                        <input type="text" class="form-control"
                                               name="duplicated[${id}][unit]" value="${unit}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Default Range</label>
                                        <input type="text" class="form-control"
                                               name="duplicated[${id}][default_range]" value="${defaultRange}" placeholder="min,max">
                                    </div>
                                </div>
                            </div>`;
                                }

                                // Add toggle fields
                                if (typeSlug === 'toggle') {
                                    const onLabel = filter.options && filter.options[
                                            0] ? filter.options[0].on_label || 'Yes' :
                                        'Yes';
                                    const offLabel = filter.options && filter.options[
                                            0] ? filter.options[0].off_label || 'No' :
                                        'No';
                                    const defaultToggle = filter.options && filter
                                        .options[0] && filter.options[0]
                                        .default_toggle ? 'checked' : '';

                                    html += `
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="form-label">On Label</label>
                                        <input type="text" class="form-control"
                                               name="duplicated[${id}][on_label]" value="${onLabel}">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="form-label">Off Label</label>
                                        <input type="text" class="form-control"
                                               name="duplicated[${id}][off_label]" value="${offLabel}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Default</label>
                                        <div class="custom-control custom-switch mt-1">
                                            <input type="checkbox" class="custom-control-input"
                                                   name="duplicated[${id}][default_toggle]"
                                                   id="duplicated_toggle_default_${id}" ${defaultToggle}>
                                            <label class="custom-control-label" for="duplicated_toggle_default_${id}">On</label>
                                        </div>
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
                        console.error(xhr.responseText);
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
