@extends('admin_layout.master')
@section('content')

    <?php
    use Illuminate\Support\Facades\Redis;
    $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
    $lang_id = Redis::get('admin_lang_id');

    ?>

    <div id="update-message" style="display: none; color: green; font-weight: bold;"></div>

    <div class="nk-block nk-block-lg pages-who-we-are">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Who We Are</h4>
            </div>
        </div>

        <form action="{{ route('admin.who_we_are.update') }}" method="POST" enctype="multipart/form-data"
            class="form-validate">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="nk-block">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label" for="main_heading">Main Heading</label>
                                <input type="text" class="form-control" id="main_heading" name="main_heading"
                                    value="{{ $whoWeAre->main_heading ?? '' }}" />
                            </div>

                            <!-- Sub Heading -->
                            <div class="col-md-12">
                                <label class="form-label" for="sub_heading">Sub Heading</label>
                                <input type="text" class="form-control" id="sub_heading" name="sub_heading"
                                    value="{{ $whoWeAre->sub_heading ?? '' }}" />
                            </div>

                            <!-- Background Top Image -->
                            {{-- <div class="col-md-12">
                                <label class="form-label" for="bg_top_img">Background Top Image</label>
                                <input type="file" class="form-control" id="bg_top_img" name="bg_top_img" />
                                @if (isset($whoWeAre->bg_top_img))
                                    <img src="{{ asset($whoWeAre->bg_top_img) }}" alt="Background Top Image" class="mt-2"
                                        style="width: 100px; height: auto;">
                                @endif
                            </div> --}}
                            <div class="col-md-12">
                                <label class="form-label" for="top_left_section_img">Top  Section Image</label>
                                <input type="file" class="form-control" id="top_left_section_img"
                                    name="top_left_section_img" />
                                @if (isset($whoWeAre->top_left_section_img))
                                    <img src="{{ asset($whoWeAre->top_left_section_img) }}" alt="Top left Section Image"
                                        class="mt-2" style="width: 100px; height: auto;">
                                @endif
                            </div>

                            <!-- Top Right Section Image -->
                            {{-- <div class="col-md-12">
                                <label class="form-label" for="top_right_section_img">Top Right Section Image</label>
                                <input type="file" class="form-control" id="top_right_section_img"
                                    name="top_right_section_img" />
                                @if (isset($whoWeAre->top_right_section_img))
                                    <img src="{{ asset($whoWeAre->top_right_section_img) }}" alt="Top Right Section Image"
                                        class="mt-2" style="width: 100px; height: auto;">
                                @endif
                            </div> --}}

                            <!-- Middle Page Heading -->
                            <div class="col-md-12">
                                <label class="form-label" for="mp_heading">Middle Page Heading</label>
                                <input type="text" class="form-control" id="mp_heading" name="mp_heading"
                                    value="{{ $whoWeAre->mp_heading ?? '' }}" />
                            </div>

                            <!-- Middle Page Sub Heading -->
                            <div class="col-md-12">
                                <label class="form-label" for="mp_sub_heading">Middle Page Sub Heading</label>
                                <input type="text" class="form-control" id="mp_sub_heading" name="mp_sub_heading"
                                    value="{{ $whoWeAre->mp_sub_heading ?? '' }}" />
                            </div>

                            <!-- Top Card Title -->
                            <div class="col-md-12">
                                <label class="form-label" for="top_card_title">Top Card Title</label>
                                <input type="text" class="form-control" id="top_card_title" name="top_card_title"
                                    value="{{ $whoWeAre->top_card_title ?? '' }}" />
                            </div>

                            <!-- Top Card Image -->
                            <div class="col-md-12">
                                <label class="form-label" for="top_card_image">Top Card Image</label>
                                <input type="file" class="form-control" id="top_card_image" name="top_card_image" />
                                @if (isset($whoWeAre->top_card_image))
                                    <img src="{{ asset($whoWeAre->top_card_image) }}" alt="Top Card Image" class="mt-2"
                                        style="width: 100px; height: auto;">
                                @endif
                            </div>

                            <!-- Top Card Description -->
                            <div class="col-md-12">
                                <label class="form-label" for="top_card_desc">Top Card Description</label>
                                <textarea class="form-control" id="top_card_desc" name="top_card_desc">{{ $whoWeAre->top_card_desc ?? '' }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <div class="card border">
                                    <div class="card-header mt-3">
                                        Most Popular Section
                                        <button type="button" class="btn btn-success btn-sm float-end"
                                            id="add-popular-item">Add
                                            Item</button>
                                    </div>
                                    <div class="card-body">

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="image">Image</label>
                                            <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="image" />

                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="title">Title</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="title"
                                                    placeholder="Enter Here Title.." />

                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="description">Description</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control site_text_input"
                                                    id="description" placeholder="Enter Here Description..." />

                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div id="popular-items-list" class="popular-items-container">
                                <!-- Popular items will be appended here -->
                            </div>
                            <div id="popular-items" class="popular-items-container">
                                <div class="col-md-12 mt-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            Most Popular Items
                                        </div>
                                        <div class="card-body">


                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Image</th>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($pageTileTranslationPopular as $index => $item)
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
                                                                <td>{{ $translation->description ?? 'No Description' }}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-danger btn-sm"
                                                                        href="{{ route('admin.page_tile_translation.delete', $item->id) }}">Delete</a>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-success btn-sm update-item"
                                                                        data-id="{{ $translation->id }}"
                                                                        data-title="{{ $translation->title }}"
                                                                        data-des="{{ $translation->description }}"
                                                                        data-image="{{ asset($translation->image) }}">Edit</button>
                                                                </td>
                                                        @endforeach
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5">No popular items available.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="updated-item-details" class="mt-4" style="display: none;">
                                    <p><strong>Title:</strong></p>
                                    <input type="hidden" class="form-control" id="updated-id" name="MPS[id]"
                                        value="{{ old('title', $item->title ?? '') }}" />
                                    <input type="text" class="form-control" id="updated-title" name="MPS[title]"
                                        value="{{ old('title', $item->title ?? '') }}" />

                                    <p><strong>Description:</strong></p>
                                    <input type="text" class="form-control" id="updated-description"
                                        name="MPS[description]"
                                        value="{{ old('description', $item->description ?? '') }}" />


                                    <p><strong>Image:</strong></p>
                                    <img id="updated-image" style="width: 100px; height: auto;" />
                                    <input type="file" id="update-image-input" name="MPS[image]" />

                                    <button type="button" id="save-mps-button" style="display: none;">Save</button>
                                </div>
                            </div>




                            <!-- Specialists Heading -->
                            <div class="col-md-12">
                                <label class="form-label" for="specialists_heading">Specialists Heading</label>
                                <input type="text" class="form-control" id="specialists_heading"
                                    name="specialists_heading" value="{{ $whoWeAre->specialists_heading ?? '' }}" />
                            </div>
                            <div class="card border mt-3">
                                <div class="card-header">
                                    Specialists Section
                                    <button type="button" class="btn btn-success btn-sm float-end"
                                        id="add-specialists-item">Add
                                        Item</button>
                                </div>
                                <div class="card-body">
                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="specialists_title">Title</label>
                                        <input type="text" class="form-control" id="specialists_title"
                                            name="specialists_title[]" placeholder="Enter Title">
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="specialists_description">Description</label>
                                        <input type="text" class="form-control" id="specialists_description"
                                            name="specialists_description[]" placeholder="Enter Description">
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="specialists_img">Image</label>
                                        <input type="file" class="form-control" id="specialists_img"
                                            name="specialists_img[]">
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="specialists_small_img">small Image</label>
                                        <input type="file" class="form-control" id="specialists_small_img"
                                            name="specialists_small_img[]">
                                    </div>
                                </div>
                            </div>

                            <div id="specialists-items-list">
                                <!-- Specialists items will be appended here -->
                            </div>
                            <div id="specialist-items" class="specialist-items-container">

                                <div class="col-md-12 mt-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            Specialists Section
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Image</th>
                                                        <th>Small Image</th>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($specilistTileTranslation as $index => $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                @if ($item->translations->first()->img)
                                                                    <img src="{{ asset($item->translations->first()->img) }}"
                                                                        alt="Item Image"
                                                                        style="width: 100px; height: auto;">
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($item->translations->first()->small_img)
                                                                    <img src="{{ asset($item->translations->first()->small_img) }}"
                                                                        alt="Item Image"
                                                                        style="width: 100px; height: auto;">
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->translations->first()->title ?? 'No title' }}
                                                            </td>
                                                            <td>{{ $item->translations->first()->description ?? 'No Description' }}
                                                            </td>
                                                            <td>
                                                                <!-- Delete Button -->
                                                                <a class="btn btn-danger btn-sm"
                                                                    href="{{ route('admin.page_tile_translation.delete', $item->id) }}">Delete</a>
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm update-specialist-item"
                                                                    data-id="{{ $item->translations->first()->id }}"
                                                                    data-title="{{ $item->translations->first()->title }}"
                                                                    data-desc="{{ $item->translations->first()->description }}"
                                                                    data-img="{{ asset($item->translations->first()->img) }}"
                                                                    data-small_img="{{ asset($item->translations->first()->small_img) }}">Edit</button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No popular items found.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="updated-special-details" class="mt-4 p-3 border rounded" style="display: none; background-color: #f8f9fa;">
                                    <h5>Edit Specialist Item</h5>

                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Title:</strong></label>
                                        <input type="hidden" class="form-control" id="updated-s-id" name="SS[id]"
                                            value="{{ old('title', $specilistTileTranslation->first()->title ?? '') }}" />
                                        <input type="text" class="form-control" id="updated-s-title" name="SS[title]"
                                            value="{{ old('title', $specilistTileTranslation->first()->title ?? '') }}" />
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Description:</strong></label>
                                        <input type="text" class="form-control" id="updated-s-description"
                                            name="MPS[description]"
                                            value="{{ old('description', $specilistTileTranslation->first()->description ?? '') }}" />
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Image:</strong></label><br>
                                        <img id="updated-s-image" style="width: 100px; height: auto;" class="mb-2" />
                                        <input type="file" id="update-special-image-input" name="SS[img]" class="form-control" />
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Small Image:</strong></label><br>
                                        <img id="updated-small-image" style="width: 100px; height: auto;" class="mb-2" />
                                        <input type="file" id="update-small-image-input" name="SS[small_img]" class="form-control" />
                                    </div>

                                    <div class="mt-4">
                                        <button type="button" id="save-ss-button" class="btn btn-primary">Save Changes</button>
                                        <button type="button" id="cancel-ss-edit" class="btn btn-secondary ms-2">Cancel</button>
                                    </div>
                                </div>

                            </div>

                            </br>

                            <!-- Service Software Heading -->
                            <div class="col-md-12">
                                <label class="form-label" for="ss_heading">Service Software Heading</label>
                                <input type="text" class="form-control" id="ss_heading" name="ss_heading"
                                    value="{{ $whoWeAre->ss_heading ?? '' }}" />
                            </div>

                            <!-- Service Software Description -->
                            <div class="col-md-12">
                                <label class="form-label" for="ss_sub_desc">Service Software Description</label>
                                <textarea class="form-control" id="ss_sub_desc" name="ss_sub_desc">{{ $whoWeAre->ss_sub_desc ?? '' }}</textarea>
                            </div>

                            <!-- Portfolio Button Text -->
                            <div class="col-md-12">
                                <label class="form-label" for="protfolio_btn">Portfolio Button Text</label>
                                <input type="text" class="form-control" id="protfolio_btn" name="protfolio_btn"
                                    value="{{ $whoWeAre->protfolio_btn ?? '' }}" />
                            </div>

                            <!-- Status -->
                            <div class="col-md-12">
                                <label class="form-label" for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1"
                                        {{ isset($whoWeAre) && $whoWeAre->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0"
                                        {{ isset($whoWeAre) && $whoWeAre->status == 0 ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="nk-block">
                                            <div class="col-md-12 mt-1 d-flex justify-content-between">
                                                <a href="your-view-page-url" class="btn btn-link text-center">
                                                    <span><b>View Page</b></span>
                                                </a>
                                                <button type="submit"
                                                    class="addCategory btn btn-primary btn-localio text-center"><em
                                                        class=""></em><span>Update
                                                        Content</span></button>
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
                                                        value="{{ $whoWeAre->permanent_url ?? '' }}" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                                                <input type="text" id="meta_title" name="meta_title"
                                                    class="form-control" placeholder="Enter meta title"
                                                    value="{{ $whoWeAre->meta_title ?? '' }}">
                                            </div>
                                            <div class="form-group mt-3">
                                                <label for="meta_description" class="form-label fw-bold">Meta
                                                    Description</label>
                                                <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                                                    placeholder="Enter meta description">{{ $whoWeAre->meta_description ?? '' }}</textarea>
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
        $(document).ready(function() {
            let addedItems = [];
            let addedSpecialistsItems = [];

            // Add validation error display container after form fields
            $('.form-control').after(
                '<div class="validation-error" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>'
                );

            // Function to show validation error
            function showValidationError(element, message) {
                $(element).next('.validation-error').text(message).show();
                $(element).addClass('border-danger');
            }

            // Function to clear validation errors
            function clearValidationErrors() {
                $('.validation-error').hide();
                $('.form-control').removeClass('border-danger');
            }

            // Function to show success message
            function showSuccessMessage(message) {
                $('#update-message').text(message).css({
                    'color': 'green',
                    'font-weight': 'bold',
                    'padding': '10px',
                    'background-color': '#e7f7e7',
                    'border-radius': '4px',
                    'margin-bottom': '15px'
                }).slideDown();

                setTimeout(function() {
                    $('#update-message').slideUp();
                }, 3000);
            }

            // Field validation based on controller rules
            function validateField(field, value, rules) {
                if (rules.includes('required') && !value) {
                    return 'This field is required';
                }

                if (rules.includes('max:255') && value.length > 255) {
                    return 'Maximum 255 characters allowed';
                }

                if (rules.includes('max:600') && value.length > 600) {
                    return 'Maximum 600 characters allowed';
                }

                if (field.attr('type') === 'file' && value) {
                    const file = field[0].files[0];
                    if (file) {
                        const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
                        const extension = file.name.split('.').pop().toLowerCase();

                        if (!validExtensions.includes(extension)) {
                            return 'Only image files (jpeg, jpg, png, gif, svg) are allowed';
                        }

                        if (file.size > 2048 * 1024) { // 2MB in bytes
                            return 'Image size should not exceed 2MB';
                        }
                    }
                }

                return null; // No validation error
            }

            // Form-wide validation
            function validateForm() {
                clearValidationErrors();
                let isValid = true;

                // Required fields validation
                const requiredFields = {
                    '#main_heading': 'required|string|max:255',
                    '#meta_title': 'required|string|max:255',
                    '#meta_description': 'required|string'
                };

                // Optional fields with rules
                const optionalFields = {
                    '#sub_heading': 'nullable|string|max:600',
                    '#mp_heading': 'nullable|string|max:600',
                    '#mp_sub_heading': 'nullable|string|max:600',
                    '#top_card_title': 'nullable|string|max:255',
                    '#top_card_desc': 'nullable|string',
                    '#specialists_heading': 'nullable|string|max:255',
                    '#ss_heading': 'nullable|string|max:255',
                    '#ss_sub_desc': 'nullable|string',
                    '#protfolio_btn': 'nullable|string|max:255',
                    '#bg_top_img': 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    '#top_left_section_img': 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    '#top_right_section_img': 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    '#top_card_image': 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                };

                // Validate required fields
                $.each(requiredFields, function(selector, rules) {
                    const field = $(selector);
                    const value = field.val();
                    const error = validateField(field, value, rules);

                    if (error) {
                        showValidationError(selector, error);
                        isValid = false;
                    }
                });

                // Validate optional fields if they have values
                $.each(optionalFields, function(selector, rules) {
                    const field = $(selector);
                    const value = field.val();

                    if (value || (field.attr('type') === 'file' && field[0].files.length > 0)) {
                        const error = validateField(field, value, rules);

                        if (error) {
                            showValidationError(selector, error);
                            isValid = false;
                        }
                    }
                });

                return isValid;
            }

            $('#add-popular-item').on('click', function() {
                clearValidationErrors();

                const title = $('#title').val();
                const description = $('#description').val();
                const imageInput = $('#image')[0];
                const imageFile = imageInput.files[0];

                // Validate fields
                let isValid = true;

                if (!title) {
                    showValidationError('#title', 'Please enter a title');
                    isValid = false;
                } else if (title.length > 255) {
                    showValidationError('#title', 'Title cannot exceed 255 characters');
                    isValid = false;
                }

                if (!description) {
                    showValidationError('#description', 'Please enter a description');
                    isValid = false;
                }

                if (!imageFile) {
                    showValidationError('#image', 'Please select an image');
                    isValid = false;
                } else {
                    // Validate image file
                    const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
                    const extension = imageFile.name.split('.').pop().toLowerCase();

                    if (!validExtensions.includes(extension)) {
                        showValidationError('#image',
                            'Only image files (jpeg, jpg, png, gif, svg) are allowed');
                        isValid = false;
                    } else if (imageFile.size > 2048 * 1024) { // 2MB in bytes
                        showValidationError('#image', 'Image size should not exceed 2MB');
                        isValid = false;
                    }
                }

                if (!isValid) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const itemCard = $('<div class="item-card mt-2 p-2 border">');
                    itemCard.html(`
                <h5>${title}</h5>
                <p>${description}</p>
                <img src="${e.target.result}" alt="Image" style="width: 100px; height: auto;">
                <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
            `);
                    const itemId = Date.now();
                    $('#popular-items-list').append(itemCard);

                    const hiddenTitleInput = $(
                        '<input type="hidden" name="popular_items[title][]" value="' + title + '">');
                    const hiddenDescriptionInput = $(
                        '<input type="hidden" name="popular_items[description][]" value="' +
                        description + '">');
                    const hiddenImageInput = $(
                        '<input type="hidden" name="popular_items[image][]" value="' + e.target
                        .result + '">');

                    $('#popular-items-list').append(hiddenTitleInput, hiddenDescriptionInput,
                        hiddenImageInput);

                    addedItems.push({
                        title: title,
                        description: description,
                        image: e.target.result,
                        card: itemCard,
                        hiddenInputs: [hiddenTitleInput, hiddenDescriptionInput,
                            hiddenImageInput
                        ]
                    });

                    clearForm();
                    showSuccessMessage('Item added successfully');
                };
                reader.readAsDataURL(imageFile);
            });

            $('#popular-items').on('click', '.update-item', function() {
                clearValidationErrors();

                let itemId = $(this).data('id');
                let title = $(this).data('title');
                let description = $(this).data('des');
                let imageSrc = $(this).data('image');

                $('#updated-id').val(itemId);
                $('#updated-title').val(title);
                $('#updated-description').val(description);
                $('#updated-image').attr('src', imageSrc);

                $('#updated-item-details').show();
                $('#save-mps-button').show();
            });

            $('#save-mps-button').on('click', function() {
                clearValidationErrors();

                let itemId = $('#updated-id').val();
                let title = $('#updated-title').val();
                let description = $('#updated-description').val();
                let imageFile = $('#update-image-input')[0].files[0];

                // Validate updated fields
                let isValid = true;

                if (!title) {
                    showValidationError('#updated-title', 'Please enter a title');
                    isValid = false;
                } else if (title.length > 255) {
                    showValidationError('#updated-title', 'Title cannot exceed 255 characters');
                    isValid = false;
                }

                if (!description) {
                    showValidationError('#updated-description', 'Please enter a description');
                    isValid = false;
                }

                if (imageFile) {
                    // Validate image file
                    const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
                    const extension = imageFile.name.split('.').pop().toLowerCase();

                    if (!validExtensions.includes(extension)) {
                        showValidationError('#update-image-input',
                            'Only image files (jpeg, jpg, png, gif, svg) are allowed');
                        isValid = false;
                    } else if (imageFile.size > 2048 * 1024) { // 2MB in bytes
                        showValidationError('#update-image-input', 'Image size should not exceed 2MB');
                        isValid = false;
                    }
                }

                if (!isValid) {
                    return;
                }

                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('id', itemId);
                formData.append('title', title);
                formData.append('des', description);

                if (imageFile) {
                    formData.append('image', imageFile);
                }

                $.ajax({
                    url: '{{ route('admin.page_tile_translation.update') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showSuccessMessage(response.success);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            $('#update-message').text(response.error || 'An error occurred')
                                .css('color', 'red').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Display server validation errors if any
                        if (xhr.status === 422) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.errors) {
                                    Object.keys(response.errors).forEach(function(key) {
                                        const fieldName = key.replace(/\./g, '_');
                                        showValidationError('#' + fieldName, response
                                            .errors[key][0]);
                                    });
                                }
                            } catch (e) {
                                $('#update-message').text(
                                    'There was an error updating the item').css('color',
                                    'red').show();
                            }
                        } else {
                            $('#update-message').text('There was an error updating the item')
                                .css('color', 'red').show();
                        }
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            });

            // Handle file input change for image preview
            $('#update-image-input').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        $('#updated-image').attr('src', reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#popular-items-list').on('click', '.remove-item', function() {
                const itemCard = $(this).closest('.item-card');
                const itemIndex = addedItems.findIndex(item => item.card[0] === itemCard[0]);

                if (itemIndex > -1) {
                    addedItems[itemIndex].removed = true;
                    itemCard.remove();
                    addedItems[itemIndex].hiddenInputs.forEach(input => input.remove());
                    showSuccessMessage('Item removed successfully');
                }
            });

            function clearForm() {
                $('#title, #description, #image').val('');
            }

            // Add specialists item logic
            $('#add-specialists-item').on('click', function() {
                clearValidationErrors();

                const title = $('#specialists_title').val();
                const description = $('#specialists_description').val();
                const imgFile = $('#specialists_img')[0].files[0];
                const smallImgFile = $('#specialists_small_img')[0].files[0];

                // Validate fields
                let isValid = true;

                if (!title) {
                    showValidationError('#specialists_title', 'Please enter a title');
                    isValid = false;
                } else if (title.length > 255) {
                    showValidationError('#specialists_title', 'Title cannot exceed 255 characters');
                    isValid = false;
                }

                if (!description) {
                    showValidationError('#specialists_description', 'Please enter a description');
                    isValid = false;
                }

                if (!imgFile) {
                    showValidationError('#specialists_img', 'Please select an image');
                    isValid = false;
                } else {
                    // Validate image file
                    const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
                    const extension = imgFile.name.split('.').pop().toLowerCase();

                    if (!validExtensions.includes(extension)) {
                        showValidationError('#specialists_img',
                            'Only image files (jpeg, jpg, png, gif, svg) are allowed');
                        isValid = false;
                    } else if (imgFile.size > 2048 * 1024) { // 2MB in bytes
                        showValidationError('#specialists_img', 'Image size should not exceed 2MB');
                        isValid = false;
                    }
                }

                if (!smallImgFile) {
                    showValidationError('#specialists_small_img', 'Please select a small image');
                    isValid = false;
                } else {
                    // Validate small image file
                    const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
                    const extension = smallImgFile.name.split('.').pop().toLowerCase();

                    if (!validExtensions.includes(extension)) {
                        showValidationError('#specialists_small_img',
                            'Only image files (jpeg, jpg, png, gif, svg) are allowed');
                        isValid = false;
                    } else if (smallImgFile.size > 2048 * 1024) { // 2MB in bytes
                        showValidationError('#specialists_small_img', 'Image size should not exceed 2MB');
                        isValid = false;
                    }
                }

                if (!isValid) {
                    return;
                }

                const reader1 = new FileReader();
                const reader2 = new FileReader();

                reader1.onload = function(e) {
                    const imgFileData = e.target.result;
                    reader2.onload = function(e) {
                        const smallImgFileData = e.target.result;
                        const itemCard = $('<div class="item-card mt-2 p-2 border">');
                        itemCard.html(`
                    <h5>${title}</h5>
                    <p>${description}</p>
                    <img src="${imgFileData}" alt="Image 1" style="width: 100px; height: auto;">
                    <img src="${smallImgFileData}" alt="Image 2" style="width: 100px; height: auto;">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                `);
                        $('#specialists-items-list').append(itemCard);

                        const hiddenTitleInput = $(
                            '<input type="hidden" name="specialists_items[title][]" value="' +
                            title + '">');
                        const hiddenDescriptionInput = $(
                            '<input type="hidden" name="specialists_items[description][]" value="' +
                            description + '">');
                        const hiddenImageInput1 = $(
                            '<input type="hidden" name="specialists_items[img][]" value="' +
                            imgFileData + '">');
                        const hiddenImageInput2 = $(
                            '<input type="hidden" name="specialists_items[small_img][]" value="' +
                            smallImgFileData + '">');

                        $('#specialists-items-list').append(hiddenTitleInput,
                            hiddenDescriptionInput, hiddenImageInput1, hiddenImageInput2);

                        addedSpecialistsItems.push({
                            title: title,
                            description: description,
                            image1: imgFileData,
                            image2: smallImgFileData,
                            card: itemCard,
                            hiddenInputs: [hiddenTitleInput, hiddenDescriptionInput,
                                hiddenImageInput1, hiddenImageInput2
                            ]
                        });

                        clearSpecialistsForm();
                        showSuccessMessage('Specialist item added successfully');
                    };
                    reader2.readAsDataURL(smallImgFile);
                };
                reader1.readAsDataURL(imgFile);
            });

            $('#specialist-items').on('click', '.update-specialist-item', function() {
                clearValidationErrors();

                let itemId = $(this).data('id');
                let title = $(this).data('title');
                let description = $(this).data('desc');
                let imageSrc1 = $(this).data('img');
                let imageSrc2 = $(this).data('small_img');

                $('#updated-s-id').val(itemId);
                $('#updated-s-title').val(title);
                $('#updated-s-description').val(description);
                $('#updated-s-image').attr('src', imageSrc1);
                $('#updated-small-image').attr('src', imageSrc2);

                $('#updated-special-details').show();
                $('#save-ss-button').show();
            });

            $('#save-ss-button').on('click', function() {
                clearValidationErrors();

                let itemId = $('#updated-s-id').val();
                let title = $('#updated-s-title').val();
                let description = $('#updated-s-description').val();
                let imageFile1 = $('#update-special-image-input')[0].files[0];
                let imageFile2 = $('#update-small-image-input')[0].files[0];

                // Validate updated fields
                let isValid = true;

                if (!title) {
                    showValidationError('#updated-s-title', 'Please enter a title');
                    isValid = false;
                } else if (title.length > 255) {
                    showValidationError('#updated-s-title', 'Title cannot exceed 255 characters');
                    isValid = false;
                }

                if (!description) {
                    showValidationError('#updated-s-description', 'Please enter a description');
                    isValid = false;
                }

                // Validate image files if provided
                if (imageFile1) {
                    const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
                    const extension = imageFile1.name.split('.').pop().toLowerCase();

                    if (!validExtensions.includes(extension)) {
                        showValidationError('#update-special-image-input',
                            'Only image files (jpeg, jpg, png, gif, svg) are allowed');
                        isValid = false;
                    } else if (imageFile1.size > 2048 * 1024) { // 2MB in bytes
                        showValidationError('#update-special-image-input',
                            'Image size should not exceed 2MB');
                        isValid = false;
                    }
                }

                if (imageFile2) {
                    const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
                    const extension = imageFile2.name.split('.').pop().toLowerCase();

                    if (!validExtensions.includes(extension)) {
                        showValidationError('#update-small-image-input',
                            'Only image files (jpeg, jpg, png, gif, svg) are allowed');
                        isValid = false;
                    } else if (imageFile2.size > 2048 * 1024) { // 2MB in bytes
                        showValidationError('#update-small-image-input',
                        'Image size should not exceed 2MB');
                        isValid = false;
                    }
                }

                if (!isValid) {
                    return;
                }

                let formData1 = new FormData();
                formData1.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData1.append('id', itemId);
                formData1.append('title', title);
                formData1.append('desc', description);

                if (imageFile1) {
                    formData1.append('img', imageFile1);
                }
                if (imageFile2) {
                    formData1.append('small_img', imageFile2);
                }

                $.ajax({
                    url: '{{ route('admin.page_tile_specialist_translation.update') }}',
                    type: 'POST',
                    data: formData1,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showSuccessMessage(response.msg);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            $('#update-message').text(response.msg || 'An error occurred').css(
                                'color', 'red').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Display server validation errors if any
                        if (xhr.status === 422) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.errors) {
                                    Object.keys(response.errors).forEach(function(key) {
                                        const fieldName = key.replace(/\./g, '_');
                                        showValidationError('#' + fieldName, response
                                            .errors[key][0]);
                                    });
                                }
                            } catch (e) {
                                $('#update-message').text(
                                    'There was an error updating the item').css('color',
                                    'red').show();
                            }
                        } else {
                            $('#update-message').text('There was an error updating the item')
                                .css('color', 'red').show();
                        }
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            });

            // Handle file input change for specialists image preview
            $('#update-special-image-input').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        $('#updated-s-image').attr('src', reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#update-small-image-input').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        $('#updated-small-image').attr('src', reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#specialists-items-list').on('click', '.remove-item', function() {
                const itemCard = $(this).closest('.item-card');
                const itemIndex = addedSpecialistsItems.findIndex(item => item.card[0] === itemCard[0]);

                if (itemIndex > -1) {
                    addedSpecialistsItems[itemIndex].removed = true;
                    itemCard.remove();
                    addedSpecialistsItems[itemIndex].hiddenInputs.forEach(input => input.remove());
                    showSuccessMessage('Specialist item removed successfully');
                }
            });

            function clearSpecialistsForm() {
                $('#specialists_title, #specialists_description, #specialists_img, #specialists_small_img').val('');
            }

            // Language dropdown change handler
            $('#languageDropdown').on('change', function() {
                var langId = $(this).val();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "{{ route('admin.getContentByLanguage') }}",
                    type: "POST",
                    data: {
                        language_id: langId,
                        _token: csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText);
                        $('#update-message').text("Error occurred: " + xhr.responseText).css(
                            'color', 'red').show();
                    }
                });
            });

            // Form submit validation
            $('form.form-validate').on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    showSuccessMessage('Please correct the errors before submitting');
                    return false;
                }
                return true;
            });
        });
    </script>

@endsection
