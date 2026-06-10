@extends('admin_layout.master')
@section('content')
<?php
    use Illuminate\Support\Facades\Redis;
    $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
    ?>
    <div class="nk-block nk-block-lg pages-vendor-listed">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Vendor Listed</h4>
            </div>
        </div>

        <form action="{{ route('admin.vendor-listed.update') }}" class="form-validate" method="POST" enctype="multipart/form-data">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered">
                        {{-- <div class="card-inner"> --}}
                            <div class="nk-block">
                            </div>

                            <!-- Banner Section -->
                            <div class="card border">
                                <div class="card-header mt-3">
                                    Banner Section
                                </div>
                                <div class="card-body">
                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_heading">Banner Heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="banner_heading"
                                                name="banner_heading" value="{{ $vendorList->banner_heading ?? '' }}" />
                                        </div>
                                        @error('banner_heading')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_sub_heading">Banner Sub Heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="banner_sub_heading"
                                                name="banner_sub_heading" value="{{ $vendorList->banner_sub_heading ?? '' }}" />
                                        </div>
                                        @error('banner_sub_heading')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_button">Banner Button</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="banner_button"
                                                name="banner_button" value="{{ $vendorList->banner_button ?? '' }}" />
                                        </div>
                                        @error('banner_button')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_image_left">Banner Image Left</label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="banner_image_left"
                                                    name="banner_image_left" />
                                                @if (isset($vendorList->banner_image_left))
                                                <img src="{{ asset($vendorList->banner_image_left) }}" alt="banner_image_left"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('banner_image_left')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_image_right">Banner Image Right</label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="banner_image_right"
                                                    name="banner_image_right" />
                                            @if (isset($vendorList->banner_image_right))
                                                <img src="{{ asset($vendorList->banner_image_right) }}" alt="banner_image_right"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('banner_image_right')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Section 1 -->
                            <div class="card border mt-3">
                                <div class="card-header mt-3">
                                    Section 1
                                </div>
                                <div class="card-body">
                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_1_title">Section 1 Title</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="section_1_title"
                                                name="section_1_title" value="{{ $vendorList->section_1_title ?? '' }}" />
                                        </div>
                                        @error('section_1_title')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_1_description">Section 1 Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="section_1_description"
                                                name="section_1_description" rows="3">{{ $vendorList->section_1_description ?? '' }}</textarea>
                                        </div>
                                        @error('section_1_description')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_1_image">Section 1 Image</label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="section_1_image"
                                                    name="section_1_image" />
                                                @if (isset($vendorList->section_1_image))
                                                <img src="{{ asset($vendorList->section_1_image) }}" alt="section_1_image"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('section_1_image')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2 -->
                            <div class="card border mt-3">
                                <div class="card-header mt-3">
                                    Section 2
                                </div>
                                <div class="card-body">
                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_2_title">Section 2 Title</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="section_2_title"
                                                name="section_2_title" value="{{ $vendorList->section_2_title ?? '' }}" />
                                        </div>
                                        @error('section_2_title')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_2_description">Section 2 Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="section_2_description"
                                                name="section_2_description" rows="3">{{ $vendorList->section_2_description ?? '' }}</textarea>
                                        </div>
                                        @error('section_2_description')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_2_image">Section 2 Image</label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="section_2_image"
                                                    name="section_2_image" />
                                                @if (isset($vendorList->section_2_image))
                                                <img src="{{ asset($vendorList->section_2_image) }}" alt="section_2_image"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('section_2_image')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3 -->
                            <div class="card border mt-3">
                                <div class="card-header mt-3">
                                    Section 3
                                </div>
                                <div class="card-body">
                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_3_title">Section 3 Title</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="section_3_title"
                                                name="section_3_title" value="{{ $vendorList->section_3_title ?? '' }}" />
                                        </div>
                                        @error('section_3_title')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_3_description">Section 3 Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="section_3_description"
                                                name="section_3_description" rows="3">{{ $vendorList->section_3_description ?? '' }}</textarea>
                                        </div>
                                        @error('section_3_description')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_3_image">Section 3 Image</label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="section_3_image"
                                                    name="section_3_image" />
                                                @if (isset($vendorList->section_3_image))
                                                <img src="{{ asset($vendorList->section_3_image) }}" alt="section_3_image"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('section_3_image')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_3_button">Section 3 Button</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="section_3_button"
                                                name="section_3_button" value="{{ $vendorList->section_3_button ?? '' }}" />
                                        </div>
                                        @error('section_3_button')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Claim Section -->
                            <div class="col-md-12 mt-3">
                                <div class="card border">
                                    <div class="card-header mt-3">
                                        Claim Section
                                        <button type="button" class="btn btn-success btn-sm float-end"
                                            id="add-claim-item">Add Item</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="claim_title">Title</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="claim_title"
                                                    placeholder="Enter Title..." />
                                            </div>
                                            <div class="error-message text-danger mt-1"></div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="claim_description">Description</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" id="claim_description"
                                                    placeholder="Enter Description..." rows="3"></textarea>
                                            </div>
                                            <div class="error-message text-danger mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Container for new claim items -->
                            <div id="claim_list" class="claim-container mt-3">
                                <!-- New items will be appended here -->
                            </div>

                            <!-- Container for existing claim items -->
                            <div id="claim_section" class="claim-container">
                                <div class="col-md-12 mt-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            Existing Claim Items
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th colspan="2">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $claimItems = [];
                                                        if (isset($vendorList->claim_section)) {
                                                            $claimItems = is_string($vendorList->claim_section)
                                                                ? json_decode($vendorList->claim_section, true)
                                                                : $vendorList->claim_section;
                                                            $claimItems = is_array($claimItems) ? array_values(array_filter($claimItems)) : [];
                                                        }
                                                    @endphp

                                                    @forelse ($claimItems as $index => $item)
                                                        @if(isset($item['title']) && isset($item['description']))
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>

                                                                <td class="claim-title">
                                                                    {{ $item['title'] }}
                                                                    <input type="hidden" name="claim_section[{{ $index }}][title]" value="{{ $item['title'] }}">
                                                                </td>

                                                                <td class="claim-description">
                                                                    {{ Str::limit($item['description'], 100) }}
                                                                    <input type="hidden" name="claim_section[{{ $index }}][description]" value="{{ $item['description'] }}">
                                                                </td>

                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-claim-item"
                                                                        data-index="{{ $index }}">Delete</button>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-success btn-sm edit-claim-item"
                                                                        data-index="{{ $index }}"
                                                                        data-title="{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}"
                                                                        data-description="{{ htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') }}">
                                                                        Edit
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No claim items available.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit form for existing items -->
                                <div id="edit-claim-item-details" class="mt-4 p-3 border rounded" style="display: none; background-color: #f8f9fa;">
                                    <h5>Edit Claim Item</h5>
                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Title:</strong></label>
                                        <input type="hidden" id="edit-claim-index" />
                                        <input type="text" class="form-control" id="edit-claim-title" />
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Description:</strong></label>
                                        <textarea class="form-control" id="edit-claim-description" rows="3"></textarea>
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" id="save-claim-button" class="btn btn-primary">Save Changes</button>
                                        <button type="button" id="cancel-edit-claim" class="btn btn-secondary ms-2">Cancel</button>
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
                                                <a href="#" class="btn btn-link text-center">
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
                                                <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                                                <input type="text" id="meta_title" name="meta_title"
                                                    class="form-control" placeholder="Enter meta title"
                                                    value="{{ $vendorList->meta_title ?? '' }}">
                                                @error('meta_title')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-3">
                                                <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                                                <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                                                    placeholder="Enter meta description">{{ $vendorList->meta_description ?? '' }}</textarea>
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
                var langId = $(this).val();
                var csrfToken = "{{ csrf_token() }}";

                $.ajax({
                    url: "{{ route('admin.getContentByLanguage') }}",
                    type: "POST",
                    data: {
                        language_id: langId,
                        _token: csrfToken
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function (xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                    alert("Error occurred: " + xhr.responseText);
                }
                });
            });
        });
    </script>

    <script>

    $(document).ready(function() {
        let claimItems = [];

        // Common validation functions
        function showValidationError(fieldId, message) {
            const field = $(`#${fieldId}`);
            field.addClass('is-invalid');
            field.parent().find('.error-message').remove();
            field.after(`<div class="error-message text-danger mt-1">${message}</div>`);
        }

        function clearValidationErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.error-message').remove();
        }

        function clearClaimForm() {
            $('#claim_title, #claim_description').val('');
            clearValidationErrors();
        }

        // Add Claim Item
        $('#add-claim-item').on('click', function() {

            clearValidationErrors();

            const title = $('#claim_title').val();
            const description = $('#claim_description').val();
            let isValid = true;

            if (!title) {
                showValidationError('claim_title', 'Title is required');
                isValid = false;
            }

            if (!description) {
                showValidationError('claim_description', 'Description is required');
                isValid = false;
            }

            if (isValid) {
                const itemCard = $('<div class="item-card mt-2 p-2 border">');
                itemCard.html(`
                    <h5>${title}</h5>
                    <p>${description}</p>
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                `);

                $('#claim_list').append(itemCard);

                const hiddenTitleInput = $('<input type="hidden" name="claim_section[' + claimItems.length + '][title]" value="' + title + '">');
                const hiddenDescriptionInput = $('<input type="hidden" name="claim_section[' + claimItems.length + '][description]" value="' + description + '">');
                $('#claim_list').append(hiddenTitleInput, hiddenDescriptionInput);

                claimItems.push({
                    title: title,
                    description: description,
                    card: itemCard,
                    hiddenInputs: [hiddenTitleInput, hiddenDescriptionInput]
                });

                clearClaimForm();
            }
        });

        // Remove Claim Item (for newly added items)
        $('#claim_list').on('click', '.remove-item', function() {
            const itemCard = $(this).closest('.item-card');
            const itemIndex = claimItems.findIndex(item => item.card[0] === itemCard[0]);

            if (itemIndex > -1) {
                claimItems[itemIndex].removed = true;
                itemCard.remove();
                claimItems[itemIndex].hiddenInputs.forEach(input => input.remove());
            }
        });

        // Remove existing claim item (from database)
        $(document).on('click', '.remove-claim-item', function() {
            if (confirm('Are you sure you want to delete this claim item?')) {
                const row = $(this).closest('tr');
                const itemIndex = $(this).data('index');

                // Hide the row and mark for deletion
                row.hide();

                // Add a hidden input to mark this item for deletion
                const deleteInput = $('<input type="hidden" name="claim_section_delete[]" value="' + itemIndex + '">');
                $('#claim_section').append(deleteInput);

                // Alternatively, you could remove the row entirely:
                // row.remove();
            }
        });

        // Edit Claim Item
        $(document).on('click', '.edit-claim-item', function() {

            clearValidationErrors();

            let itemIndex = $(this).data('index');
            let title = $(this).data('title');
            let description = $(this).data('description');

            // Decode HTML entities if needed
            title = $('<div>').html(title).text();
            description = $('<div>').html(description).text();

            $('#edit-claim-index').val(itemIndex);
            $('#edit-claim-title').val(title);
            $('#edit-claim-description').val(description);

            $('#edit-claim-item-details').show();
            $('#save-claim-button').show();

            // Scroll to edit form
            $('html, body').animate({
                scrollTop: $('#edit-claim-item-details').offset().top - 100
            }, 300);
        });

        // Save edited Claim Item
        $('#save-claim-button').on('click', function () {
            clearValidationErrors();
            let isValid = true;

            let itemIndex = $('#edit-claim-index').val();
            let title = $('#edit-claim-title').val().trim();
            let description = $('#edit-claim-description').val().trim();

            if (!title) {
                showValidationError('edit-claim-title', 'Title is required');
                isValid = false;
            }

            if (!description) {
                showValidationError('edit-claim-description', 'Description is required');
                isValid = false;
            }

            if (!isValid) return;

            const row = $(`.edit-claim-item[data-index="${itemIndex}"]`).closest('tr');

            // Update visible content in the table
            row.find('.claim-title').contents().first().replaceWith(title);
            row.find('.claim-description').contents().first().replaceWith(description);

            // Update the data attributes for future edits
            row.find('.edit-claim-item').attr('data-title', title);
            row.find('.edit-claim-item').attr('data-description', description);

            // Update hidden inputs
            row.find(`input[name="claim_section[${itemIndex}][title]"]`).val(title);
            row.find(`input[name="claim_section[${itemIndex}][description]"]`).val(description);

            // Show success message
            const successMsg = $('<div class="alert alert-success alert-dismissible fade show">Item updated successfully!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
            $('#edit-claim-item-details').before(successMsg);
            setTimeout(() => successMsg.fadeOut(500, function () { $(this).remove(); }), 3000);

            // Hide edit form
            $('#edit-claim-item-details').hide();
            $('#edit-claim-title').val('');
            $('#edit-claim-description').val('');
            $('#edit-claim-index').val('');
        });

        // Cancel edit
        $(document).on('click', '#cancel-edit-claim', function() {
            $('#edit-claim-item-details').hide();
            $('#edit-claim-title').val('');
            $('#edit-claim-description').val('');
            $('#edit-claim-index').val('');
            clearValidationErrors();
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            clearValidationErrors();
            let isValid = true;

            const requiredFields = [
                'banner_heading',
                'banner_sub_heading',
                'banner_button',
                'section_1_title',
                'section_1_description',
                'section_2_title',
                'section_2_description',
                'section_3_title',
                'section_3_description',
                'section_3_button',
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
            const imageFields = ['banner_image_left', 'banner_image_right', 'section_1_image', 'section_2_image', 'section_3_image'];
            imageFields.forEach(field => {
                const fileInput = document.getElementById(field);
                if (fileInput && fileInput.files.length > 0) {
                    const file = fileInput.files[0];

                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        showValidationError(field, 'Image must be jpeg, png, jpg, or gif');
                        isValid = false;
                    }

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
    });

    </script>
@endsection
