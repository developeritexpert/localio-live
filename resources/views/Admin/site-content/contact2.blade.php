@extends('admin_layout.master')

@section('content')
    <?php
    use Illuminate\Support\Facades\Redis;
    $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
    ?>
    <div class="nk-block nk-block-lg pages-contact">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Contact</h4>
            </div>
        </div>

        <form action="{{ route('admin.page-contact-content.update') }}" class="form-validate" method="POST" enctype="multipart/form-data">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered">
                        {{-- <div class="card-inner"> --}}
                            <div class="nk-block">
                            </div>
                            <div class="card border">
                                <div class="card-header mt-3">
                                    Contact page
                                </div>
                                <div class="card-body">

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="contact_heading">Contact heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="contact_heading"
                                                name="contact_heading" value="{{ $contact->contact_heading ?? '' }}" />
                                        </div>
                                        @error('contact_heading')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- added the description field in admin --}}
                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="contact_description">Contact Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="contact_description" name="contact_description" rows="4">{{ $contact->contact_description ?? '' }}</textarea>
                                        </div>
                                        @error('contact_description')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="image_first">Banner Image </label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="image_first"
                                                    name="image_first" />
                                                @if (isset($contact->image_first))
                                                <img src="{{ asset($contact->image_first) }}" alt="image_first"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('image_first')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    {{-- <div class="form-group col-lg-12">
                                        <label class="form-label" for="image_second">Image second</label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="image_second"
                                                    name="image_second" />
                                            @if (isset($contact->image_second))
                                                <img src="{{ asset($contact->image_second) }}" alt="image_second"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('image_second')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div> --}}

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="footer_heading">Footer Heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="footer_heading"
                                                name="footer_heading" value="{{ $contact->footer_heading ?? '' }}" />
                                        </div>
                                        @error('footer_heading')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Right Tools Section -->
                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Right Tools
                                                <button type="button" class="btn btn-success btn-sm float-end"
                                                    id="add-Right-tool-item">Add Item</button>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="right_tool_image">Image</label>
                                                    <div class="form-control-wrap">
                                                        <input type="file" class="form-control" id="right_tool_image" />
                                                    </div>
                                                    <div class="error-message text-danger mt-1"></div>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="right_tool_title">Title</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="right_tool_title"
                                                            placeholder="Enter Here Title.." />
                                                    </div>
                                                    <div class="error-message text-danger mt-1"></div>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="right_tool_description">Description</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            id="right_tool_description"
                                                            placeholder="Enter Here Description..." />
                                                    </div>
                                                    <div class="error-message text-danger mt-1"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Container for new right tool items -->
                                    <div id="right_tool_list" class="right-tools-container mt-3">
                                        <!-- New items will be appended here -->
                                    </div>

                                    <!-- Container for existing right tool items -->
                                    <div id="right_tool" class="right-tools-container">
                                        <div class="col-md-12 mt-4">
                                            <div class="card border">
                                                <div class="card-header">
                                                    Existing Right Tools
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Image</th>
                                                                <th>Title</th>
                                                                <th>Description</th>
                                                                <th colspan="2">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($pageTileTranslationRightTool as $index => $item)
                                                                @foreach ($item->translations as $translation)
                                                                    <tr>
                                                                        <td>{{ $loop->parent->iteration }}</td>
                                                                        <td>
                                                                            @if ($translation->image)
                                                                                <img src="{{ asset($translation->image) }}"
                                                                                    alt="Item Image"
                                                                                    style="width: 100px; height: auto;">
                                                                            @else
                                                                                N/A
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $translation->title ?? 'No Title' }}</td>
                                                                        <td>{{ $translation->description ?? 'No Description' }}</td>
                                                                        <td>
                                                                            <a class="btn btn-danger btn-sm"
                                                                                href="{{ route('admin.page_tile_translation.delete', $item->id) }}">Delete</a>
                                                                        </td>
                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-success btn-sm update-rtt-item"
                                                                                data-id="{{ $translation->id }}"
                                                                                data-title="{{ $translation->title }}"
                                                                                data-description="{{ $translation->description }}"
                                                                                data-image="{{ asset($translation->image) }}">Edit</button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6">No right tool items available.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit form for existing items -->
                                        <div id="updated-rtt-item-details" class="mt-4 p-3 border" style="display: none;">
                                            <h5>Edit Right Tool Item</h5>
                                            <div class="form-group mt-3">
                                                <label class="form-label"><strong>Title:</strong></label>
                                                <input type="hidden" id="updated-rtt-id" name="RTT[id]" />
                                                <input type="text" class="form-control" id="updated-rtt-title" name="RTT[title]" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <div class="form-group mt-3">
                                                <label class="form-label"><strong>Description:</strong></label>
                                                <input type="text" class="form-control" id="updated-rtt-description" name="RTT[description]" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <div class="form-group mt-3">
                                                <label class="form-label"><strong>Current Image:</strong></label>
                                                <div class="mb-2">
                                                    <img id="updated-rtt-image" style="width: 100px; height: auto;" />
                                                </div>
                                                <label class="form-label">Upload New Image (optional):</label>
                                                <input type="file" class="form-control" id="update-rtt-image-input" name="RTT[image]" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <button type="button" id="save-rtt-button" class="btn btn-primary mt-3">Save Changes</button>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12 mt-3">
                                        <label class="form-label" for="g_button">Get button</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="g_button" name="g_button"
                                                value="{{ $contact->g_button ?? '' }}" />
                                        </div>
                                        @error('g_button')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- </div> --}}

                <!-- Right sidebar -->
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
                                                <button type="submit" class="btn btn-primary text-center">
                                                    <span>Update Content</span>
                                                </button>
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
                                                        value="{{ $contact->permanent_url ?? '' }}" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                                                <input type="text" id="meta_title" name="meta_title"
                                                    class="form-control" placeholder="Enter meta title"
                                                    value="{{ $contact->meta_title ?? '' }}">
                                                @error('meta_title')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-3">
                                                <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                                                <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                                                    placeholder="Enter meta description">{{ $contact->meta_description ?? '' }}</textarea>
                                                @error('meta_description')
                                                <div class="alert alert-danger">{{ $message }}</div>
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
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $('#languageDropdown').on('change', function () {
                var langId = $(this).val(); // Get selected language ID
                var csrfToken = "{{ csrf_token() }}"; // Get CSRF token

                $.ajax({
                    url: "{{ route('admin.getContentByLanguage') }}", // Correct route
                    type: "POST",
                    data: {
                        language_id: langId,
                        _token: csrfToken
                    },
                    success: function (response) {


                    // Reload page after successful response
                    if (response.success) {
                        location.reload();
                    }
                    },
                    error: function (xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                    alert("Error occurred: " + xhr.responseText);
                }
                });
                // alert(langId);
            });
        });
    </script>

 <script>
 $(document).ready(function() {
    // Initialize the items array
    let rightToolItems = [];

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
    // Right Tool Items
    $('#add-Right-tool-item').on('click', function() {
        clearValidationErrors();

        const title = $('#right_tool_title').val();
        const description = $('#right_tool_description').val();
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
                const base64Image = e.target.result;

                const itemCard = $('<div class="item-card mt-2 p-2 border">');
                itemCard.html(`
                    <h5>${title}</h5>
                    <p>${description}</p>
                    <img src="${base64Image}" alt="Image" style="width: 100px; height: auto;">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                `);

                $('#right_tool_list').append(itemCard);

                // Create hidden inputs for form submission - matches controller expectations
                const hiddenTitleInput = $('<input type="hidden" name="right_tool[title][]" value="' + title + '">');
                const hiddenDescriptionInput = $('<input type="hidden" name="right_tool[description][]" value="' + description + '">');
                const hiddenImageInput = $('<input type="hidden" name="right_tool[image][]" value="' + base64Image + '">');

                $('#right_tool_list').append(hiddenTitleInput, hiddenDescriptionInput, hiddenImageInput);

                rightToolItems.push({
                    title: title,
                    description: description,
                    image: base64Image,
                    card: itemCard,
                    hiddenInputs: [hiddenTitleInput, hiddenDescriptionInput, hiddenImageInput]
                });

                clearRightToolForm();
            };
            reader.readAsDataURL(imageFile);
        }
    });

    // Remove Right Tool Item
    $('#right_tool_list').on('click', '.remove-item', function() {
        const itemCard = $(this).closest('.item-card');
        const itemIndex = rightToolItems.findIndex(item => item.card[0] === itemCard[0]);

        if (itemIndex > -1) {
            rightToolItems[itemIndex].removed = true;
            itemCard.remove();
            rightToolItems[itemIndex].hiddenInputs.forEach(input => input.remove());
        }
    });

    // Edit Right Tool Item
    $('.update-rtt-item').on('click', function() {
        let itemId = $(this).data('id');
        let title = $(this).data('title');
        let description = $(this).data('description');
        let imageSrc = $(this).data('image');

        // Populate the form fields with current data
        $('#updated-rtt-id').val(itemId);
        $('#updated-rtt-title').val(title);
        $('#updated-rtt-description').val(description);
        $('#updated-rtt-image').attr('src', imageSrc);

        // Show the update form and save button
        $('#updated-rtt-item-details').show();
        $('#save-rtt-button').show();
    });

    // Handle file input change for image preview
    $('#update-rtt-image-input').on('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate image type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showValidationError('update-rtt-image-input', 'Image must be jpeg, png, jpg, or gif');
                return;
            }

            // Validate image size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                showValidationError('update-rtt-image-input', 'Image size must be less than 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function() {
                $('#updated-rtt-image').attr('src', reader.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Save updated Right Tool Item
    $('#save-rtt-button').on('click', function() {
        clearValidationErrors();
        let isValid = true;

        let itemId = $('#updated-rtt-id').val();
        let title = $('#updated-rtt-title').val();
        let description = $('#updated-rtt-description').val();
            // console.log(description)
        // Validate fields
        if (!title) {
            showValidationError('updated-rtt-title', 'Title is required');
            isValid = false;
        }

        if (!description) {
            showValidationError('updated-rtt-description', 'Description is required');
            isValid = false;
        }

        if (!isValid) return;

        let imageFile = $('#update-rtt-image-input')[0].files[0];
        let formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('id', itemId);
        formData.append('title', title);
        formData.append('description', description);


        if (imageFile) {
            // Validate image type before sending
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(imageFile.type)) {
                showValidationError('update-rtt-image-input', 'Image must be jpeg, png, jpg, or gif');
                return;
            }

            formData.append('image', imageFile);
        }
        console.log(formData);
        $.ajax({
            url: '/admin/page-right-tool-translation/update',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const successMsg = $('<div class="alert alert-success">Item updated successfully!</div>');
                    $('#updated-rtt-item-details').before(successMsg);
                    setTimeout(() => successMsg.fadeOut(500, function() { $(this).remove(); }), 3000);

                    // Optionally reload the page after a delay
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);

                // Handle validation errors from server
                if (xhr.status === 422 && xhr.responseJSON) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        showValidationError('updated-rtt-' + field.replace('.', '-'), errors[field][0]);
                    }
                } else {
                    // Show general error
                    const errorMsg = $('<div class="alert alert-danger">Error updating item. Please try again.</div>');
                    $('#updated-rtt-item-details').before(errorMsg);
                    setTimeout(() => errorMsg.fadeOut(500, function() { $(this).remove(); }), 3000);
                }
            }
        });
    });

    // Make sure Save button is visible and properly styled
    $('#save-rtt-button').css({
      'display': '',
      'margin-top': '15px',
      'background-color': '#4CAF50',
      'color': 'white',
      'padding': '10px 15px',
      'border': 'none',
      'border-radius': '4px',
      'cursor': 'pointer'
    }).text('Save Changes');

    // Form submission validation - matches controller validation
    $('form').on('submit', function(e) {
        clearValidationErrors();
        let isValid = true;

        // Validate required fields
        const requiredFields = [
            'contact_heading',
            'footer_heading',
            'g_button',
            'meta_title',
            'meta_description'
        ];

        requiredFields.forEach(field => {
            const value = $(`#${field}`).val();
            if (!value || !value.trim()) {
                showValidationError(field, 'This field is required');
                isValid = false;
            }
        });

        // Validate uploaded images
        const imageFields = ['image_first', 'image_second'];
        imageFields.forEach(field => {
            const fileInput = document.getElementById(field);
            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];

                // Validate image type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    showValidationError(field, 'Image must be jpeg, png, jpg, or gif');
                    isValid = false;
                }

                // Validate image size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    showValidationError(field, 'Image size must be less than 2MB');
                    isValid = false;
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid:first').offset().top - 100
            }, 200);
        }
    });

    // Fix for Edit button to properly show the edit form
    $('.update-rtt-item').on('click', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        const description = $(this).data('description');
        const imageSrc = $(this).data('image');

        // Populate the edit form
        $('#updated-rtt-id').val(id);
        $('#updated-rtt-title').val(title);
        $('#updated-rtt-description').val(description);
        $('#updated-rtt-image').attr('src', imageSrc);

        // Show the edit form
        $('#updated-rtt-item-details').show();
        $('#save-rtt-button').show();

        // Scroll to the edit form
        $('html, body').animate({
            scrollTop: $('#updated-rtt-item-details').offset().top - 100
        }, 200);
    });
});
  </script>
@endsection
