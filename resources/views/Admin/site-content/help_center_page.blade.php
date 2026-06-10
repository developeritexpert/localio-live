@extends('admin_layout.master')

@section('content')
    <?php
    use Illuminate\Support\Facades\Redis;
    $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
    ?>
    <div class="nk-block nk-block-lg pages-help-center">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Help Center</h4>
            </div>
        </div>


        <form action="{{ route('admin.home_page_category.update') ?? '' }}" class="form-validate" novalidate="novalidate"
            method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered">
                        {{-- <div class="card-inner"> --}}
                            <div class="nk-block">
                            </div>
                            <div class="card border">
                                <div class="card-header mt-3">
                                    Help Center page
                                </div>
                                {{-- @php
                                    $content=$help->first();
                                    dd($content);
                                @endphp --}}
                                <div class="card-body">

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_headline">Banner Heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="banner_headline"
                                                name="banner_headline"
                                                value="{{ old('banner_headline', $help->banner_headline ?? '') }}">
                                            @error('banner_headline')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_description">Banner Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="banner_description"
                                                name="banner_description"
                                                value="{{ old('banner_description', $help->banner_description ?? '') }}">
                                            @error('banner_description')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="main_heading">Main Heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="main_heading" name="main_heading"
                                                value="{{ old('main_heading', $help->main_heading ?? '') }}">
                                            @error('main_heading')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_img">Banner Image</label>
                                        <div class="form-control-wrap">
                                            <input type="file" class="form-control" id="banner_img" name="banner_img" />
                                            @error('banner_img')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            @if (isset($help->banner_img))
                                                <img src="{{ asset($help->banner_img) }}" alt="banner_img" class="mt-2"
                                                    style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="left_section_title">FAQ Left Heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="left_section_title"
                                                name="left_section_title"
                                                value="{{ old('left_section_title', $help->left_section_title ?? '') }}">
                                            @error('left_section_title')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="left_section_description">FAQ Left
                                            Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="left_section_description"
                                                name="left_section_description"
                                                value="{{ old('left_section_description', $help->left_section_description ?? '') }}">
                                            @error('left_section_description')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="faq_section_title">FAQ Heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="faq_section_title"
                                                name="faq_section_title"
                                                value="{{ old('faq_section_title', $help->faq_section_title ?? '') }}">

                                            @error('faq_section_title')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="faq_section_description">FAQ Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="faq_section_description"
                                                name="faq_section_description"
                                                value="{{ old('faq_section_description', $help->faq_section_description ?? '') }}">
                                            @error('faq_section_description')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>




                                    <div class="col-md-12">
                                        {{-- Knowledge Base Section --}}
                                        <div class="card border">
                                            <div class="card-header mt-3">Knowledge Base</div>
                                            <div class="card-body">
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="knowledge_base_image">Image</label>
                                                    <div class="form-control-wrap">
                                                        <input type="file" class="form-control" id="knowledge_base_image" name="knowledge_base_image" onchange="previewImage(event, 'kb_preview')" />
                                                        @error('knowledge_base_image')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                        <img id="kb_preview" src="#" alt="Preview" class="mt-2 d-none" style="max-height: 150px;" />

                                                        @if (!empty($help->knowledge_base_image))
                                                            <div class="mt-2">
                                                                <img src="{{ asset( $help->knowledge_base_image) }}" alt="Current Image" style="max-height: 150px;" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="knowledge_base_title">Knowledge Base Title</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="knowledge_base_title" name="knowledge_base_title" placeholder="Enter Knowledge Base Title..." value="{{ old('knowledge_base_title', $help->knowledge_base_title ?? '') }}" />
                                                        @error('knowledge_base_title')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="knowledge_base_description">Knowledge Base Description</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input" id="knowledge_base_description" name="knowledge_base_description" placeholder="Enter Knowledge Base Description..." value="{{ old('knowledge_base_description', $help->knowledge_base_description ?? '') }}" />
                                                        @error('knowledge_base_description')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Help Center Section --}}
                                        <div class="card border mt-2">
                                            <div class="card-header mt-3">Help Center</div>
                                            <div class="card-body">
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="help_center_image">Image</label>
                                                    <div class="form-control-wrap">
                                                        <input type="file" class="form-control" id="help_center_image" name="help_center_image" onchange="previewImage(event, 'hc_preview')" />
                                                        @error('help_center_image')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                        <img id="hc_preview" src="#" alt="Preview" class="mt-2 d-none" style="max-height: 150px;" />

                                                        @if (!empty($help->help_center_image))
                                                            <div class="mt-2">
                                                                <img src="{{ asset( $help->help_center_image) }}" alt="Current Image" style="max-height: 150px;" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="help_center_title">Help Center Title</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="help_center_title" name="help_center_title" placeholder="Enter Help Center Title..." value="{{ old('help_center_title', $help->help_center_title ?? '') }}" />
                                                        @error('help_center_title')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="help_center_description">Help Center Description</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input" id="help_center_description" name="help_center_description" placeholder="Enter Help Center Description..." value="{{ old('help_center_description', $help->help_center_description ?? '') }}" />
                                                        @error('help_center_description')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>






                                    {{-- <div id="right_tool_list" class="right-tools-container">
                                        <!-- Popular items will be appended here -->
                                    </div>
                                    <div id="right_tool" class="right-tools-container">
                                        <div class="col-md-12 mt-4">
                                            <div class="card border">
                                                <div class="card-header">
                                                    Right Tools
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Image</th>
                                                                <th>Title</th>
                                                                <th>Description</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($help->categories ?? [] as $index => $category)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>
                                                                        @if (!empty($category->image))
                                                                            <img src="{{ asset($category->image) }}"
                                                                                alt="Category Image"
                                                                                style="width: 100px; height: auto;">
                                                                        @else
                                                                            N/A
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $category->title ?? 'No Title' }}</td>
                                                                    <td>{{ $category->description ?? 'No Description' }}
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn btn-danger btn-sm"
                                                                            href="{{ route('admin.help_center.category.delete', $category->id) }}">Delete</a>


                                                                        <button type="button"
                                                                            class="btn btn-success btn-sm update-category-item"
                                                                            data-id="{{ $category->id }}"
                                                                            data-title="{{ $category->title }}"
                                                                            data-description="{{ $category->description }}"
                                                                            data-image="{{ !empty($category->image) ? asset($category->image) : '' }}">
                                                                            Edit
                                                                        </button>

                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="5">No help center categories available.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="updated-rtt-item-details" class="mt-4" style="display: none;">
                                            <p><strong>Title:</strong></p>
                                            <input type="hidden" class="form-control" id="updated-rtt-id"
                                                name="RTT[id]" value="{{ old('title', $item->title ?? '') }}" />
                                            <input type="text" class="form-control" id="updated-rtt-title"
                                                name="RTT[title]" value="{{ old('title', $item->title ?? '') }}" />

                                            <p><strong>Description:</strong></p>
                                            <input type="text" class="form-control" id="updated-rtt-description"
                                                name="RTT[description]"
                                                value="{{ old('description', $item->description ?? '') }}" />


                                            <p><strong>Image:</strong></p>
                                            <img id="updated-rtt-image" style="width: 100px; height: auto;" />
                                            <input type="file" id="update-rtt-image-input" name="RTT[image]" />

                                            <button type="button" id="save-rtt-button"
                                                style="display: none;">Save</button>
                                        </div>
                                    </div> --}}



                                </div>
                            </div>
                        </div>
                    </div>

                {{-- </div> --}}
                <div class="col-lg-4">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="nk-block">
                                            <div class="col-md-12 mt-1 d-flex justify-content-between">
                                                <a href="{{ route('contact') }}" class="btn btn-link text-center">
                                                    <span><b>View Page</b></span>
                                                </a>
                                                <button class="addCategory btn btn-primary  text-center btn-localio"><em
                                                        class=""></em><span>Update Content</span></button>
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">

                                                @php
                                                    $languages = \App\Models\Language::where('status', 1)->get();
                                                @endphp

                                                <label class="form-label font-weight-bold">Country-Region</label>
                                                <select class="form-control" name="language" id="languageDropdown">
                                                    @foreach ($languages as $language)
                                                        <option value="{{ $language->id }}"
                                                            {{ $lang_code == $language->lang_code ? 'selected' : '' }}>

                                                            {{ $language->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label class="form-label" for="permanent_url">Permanent Url</label>
                                                    <input type="text" class="form-control" id="permanent_url"
                                                        name="permanent_url"
                                                        value="{{ $help->permanent_url ?? '' }}" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                                                <input type="text" id="meta_title" name="meta_title"
                                                    class="form-control" placeholder="Enter meta title"
                                                    value="{{ old('meta_title', $help->meta_title ?? '') }}">
                                                @error('meta_title')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group mt-3">
                                                <label for="meta_description" class="form-label fw-bold">Meta
                                                    Description</label>
                                                <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                                                    placeholder="Enter meta description">{{ old('meta_description', $help->meta_description ?? '') }}</textarea>
                                                @error('meta_description')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="categories_hidden_inputs"></div>

        </form>

    </div>
    <script>
        $(document).ready(function() {
            $('#languageDropdown').on('change', function() {
                var langId = $(this).val(); // Get selected language ID
                var csrfToken = "{{ csrf_token() }}"; // Get CSRF token

                $.ajax({
                    url: "{{ route('admin.getContentByLanguage') }}", // Correct route
                    type: "POST",
                    data: {
                        language_id: langId,
                        _token: csrfToken
                    },
                    success: function(response) {


                        // Reload page after successful response
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText);
                        alert("Error occurred: " + xhr.responseText);
                    }
                });
                // alert(langId);
            });
        });
    </script>
    <script>
    // Right tool items array
    let righttoolItems = [];

    // Common validation functions
    function showValidationError(fieldId, message) {
        const field = $(`#${fieldId}`);
        field.addClass('is-invalid');

        // Remove any existing error message
        field.parent().find('.error-message').remove();

        // Append new error message
        field.after(`<div class="error-message text-danger mt-1">${message}</div>`);
    }

    function clearValidationErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.error-message').remove();
    }

    function clearRightToolForm() {
        $('#right_tool_title, #right_tool_description').val('');
        $('#right_tool_image').val('');
        clearValidationErrors();
    }

    // Add right tool item
    $('#add-Right-tool-item').on('click', function() {
        clearValidationErrors();
        const title = $('#right_tool_title').val().trim();
        const description = $('#right_tool_description').val().trim();
        const imageInput = $('#right_tool_image')[0];
        const imageFile = imageInput.files[0];
        let isValid = true;

        // Validate title
        if (!title) {
            showValidationError('right_tool_title', 'Title is required');
            isValid = false;
        }

        // Validate description
        if (!description) {
            showValidationError('right_tool_description', 'Description is required');
            isValid = false;
        }

        // Validate image
        if (!imageFile) {
            showValidationError('right_tool_image', 'Image is required');
            isValid = false;
        } else {
            // Validate image type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(imageFile.type)) {
                showValidationError('right_tool_image', 'Image must be jpeg, png, jpg, or gif');
                isValid = false;
            }

            // Validate image size (max 2MB)
            if (imageFile.size > 2 * 1024 * 1024) {
                showValidationError('right_tool_image', 'Image size must be less than 2MB');
                isValid = false;
            }
        }

        if (isValid) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const itemCard = $('<div class="item-card mt-2 p-2 border">').html(`
                    <h5>${title}</h5>
                    <p>${description}</p>
                    <img src="${e.target.result}" alt="Image" style="width: 100px; height: auto;">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                `);

                const hiddenInputs = [
                    $('<input type="hidden" name="categories[title][]" />').val(title),
                    $('<input type="hidden" name="categories[description][]" />').val(description),
                    $('<input type="hidden" name="categories[image][]" />').val(e.target.result)
                ];

                // Append the preview card (optional)
                $('#right_tool_list').append(itemCard);

                // Append hidden inputs to a container inside the form
                $('#categories_hidden_inputs').append(hiddenInputs);

                righttoolItems.push({
                    title,
                    description,
                    image: e.target.result,
                    card: itemCard,
                    hiddenInputs
                });

                clearRightToolForm();
            };
            reader.readAsDataURL(imageFile);
        }
    });

   // Update category item - show update form
$(document).on('click', '.update-category-item', function() {
    // Clear previous errors first
    clearAllErrors();

    const itemId = $(this).data('id');
    const title = $(this).data('title');
    const description = $(this).data('description');
    const imageSrc = $(this).data('image');

    // Populate form fields
    $('#updated-rtt-id').val(itemId);
    $('#updated-rtt-title').val(title);
    $('#updated-rtt-description').val(description);
    if (imageSrc) {
        $('#updated-rtt-image').attr('src', imageSrc);
    } else {
        $('#updated-rtt-image').attr('src', '');
    }

    // Show the update form and save button
    $('#updated-rtt-item-details').show();
    $('#save-rtt-button').text('Save').prop('disabled', false).show();
});

// Function to display error for a specific field
function displayFieldError(fieldId, errorMessage) {
    const field = $('#' + fieldId);
    field.addClass('is-invalid');

    // Remove any existing error message
    field.next('.error-feedback').remove();

    // Add new error message
    field.after('<div class="error-feedback text-danger mt-1">' + errorMessage + '</div>');
}

// Function to clear all error messages and validation styles
function clearAllErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.error-feedback').remove();
}

// Save updated category item
$('#save-rtt-button').on('click', function() {
    clearAllErrors();

    const itemId = $('#updated-rtt-id').val();
    const title = $('#updated-rtt-title').val().trim();
    const description = $('#updated-rtt-description').val().trim();
    const imageInput = $('#update-rtt-image-input')[0];
    const imageFile = imageInput.files[0];

    // Validate fields on client side
    let isValid = true;

    if (!title) {
        displayFieldError('updated-rtt-title', 'Title is required');
        isValid = false;
    }

    if (!description) {
        displayFieldError('updated-rtt-description', 'Description is required');
        isValid = false;
    }

    if (imageFile) {
        // Validate image type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!validTypes.includes(imageFile.type)) {
            displayFieldError('update-rtt-image-input', 'Please select a valid image (JPEG, PNG, JPG, GIF)');
            isValid = false;
        }

        // Validate image size (max 2MB)
        if (imageFile.size > 2 * 1024 * 1024) {
            displayFieldError('update-rtt-image-input', 'Image size must be less than 2MB');
            isValid = false;
        }
    }

    if (isValid) {
        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}");
        formData.append('id', itemId);
        formData.append('title', title);
        formData.append('description', description);

        if (imageFile) {
            formData.append('image', imageFile);
        }

        // Show loading indicator
        $('#save-rtt-button').text('Saving...').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.help_center.category.update') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Show success message
                $('<div class="alert alert-success mt-2">Category updated successfully</div>')
                    .insertBefore('#updated-rtt-item-details')
                    .delay(3000)
                    .fadeOut(500, function() { $(this).remove(); });

                // Reload the page after a short delay
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                // Reset button
                $('#save-rtt-button').text('Save').prop('disabled', false);

                console.error(xhr.responseText);

                // Handle validation errors from server
                if (xhr.status === 422 && xhr.responseJSON) {
                    const errors = xhr.responseJSON.errors;

                    // Display each error under the appropriate field
                    if (errors.title) {
                        displayFieldError('updated-rtt-title', errors.title[0]);
                    }
                    if (errors.description) {
                        displayFieldError('updated-rtt-description', errors.description[0]);
                    }
                    if (errors.image) {
                        displayFieldError('update-rtt-image-input', errors.image[0]);
                    }
                } else {
                    // Generic error message
                    $('<div class="alert alert-danger mt-2">An error occurred while updating the category</div>')
                        .insertBefore('#updated-rtt-item-details')
                        .delay(5000)
                        .fadeOut(500, function() { $(this).remove(); });
                }
            }
        });
    }
});

    // Image preview on file change for update form
    $('#update-rtt-image-input').on('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image (JPEG, PNG, JPG, GIF)');
                this.value = '';
                return;
            }

            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Image size must be less than 2MB');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#updated-rtt-image').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
// Cancel button
$('#cancel-rtt-button').on('click', function() {
    // Clear form and hide it
    clearAllErrors();
    $('#updated-rtt-id').val('');
    $('#updated-rtt-title').val('');
    $('#updated-rtt-description').val('');
    $('#updated-rtt-image').attr('src', '');
    $('#update-rtt-image-input').val('');
    $('#updated-rtt-item-details').hide();
});
    // Remove item from preview
    $('#right_tool_list').on('click', '.remove-item', function() {
        const card = $(this).closest('.item-card');
        const index = righttoolItems.findIndex(item => item.card[0] === card[0]);

        if (index !== -1) {
            righttoolItems[index].hiddenInputs.forEach(input => input.remove());
            card.remove();
            righttoolItems.splice(index, 1);
        }
    });
    </script>
@endsection
