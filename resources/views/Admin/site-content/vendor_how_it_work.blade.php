@extends('admin_layout.master')
@section('content')

<?php
    use Illuminate\Support\Facades\Redis;
    $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
    ?>
    <div class="nk-block nk-block-lg pages-vendor-how-it-work">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">How It Works</h4>
            </div>
        </div>

        <form action="{{ route('admin.how-it-works.update') }}" class="form-validate" method="POST" enctype="multipart/form-data">
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
                                        <label class="form-label" for="banner_title">Banner Title</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="banner_title"
                                                name="banner_title" value="{{ $howItWorks->banner_title ?? '' }}" />
                                        </div>
                                        @error('banner_title')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_description">Banner Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="banner_description"
                                                name="banner_description" rows="3">{{ $howItWorks->banner_description ?? '' }}</textarea>
                                        </div>
                                        @error('banner_description')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_image_left">Banner Image Left</label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="banner_image_left"
                                                    name="banner_image_left" />
                                                @if (isset($howItWorks->banner_image_left))
                                                <img src="{{ asset($howItWorks->banner_image_left) }}" alt="banner_image_left"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('banner_image_left')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- <div class="form-group col-lg-12">
                                        <label class="form-label" for="banner_image_right">Banner Image Right</label>
                                        <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="banner_image_right"
                                                    name="banner_image_right" />
                                            @if (isset($howItWorks->banner_image_right))
                                                <img src="{{ asset($howItWorks->banner_image_right) }}" alt="banner_image_right"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('banner_image_right')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div> --}}
                                </div>
                            </div>

                            <!-- Main Heading -->
                            <div class="card border mt-3">
                                <div class="card-header mt-3">
                                    Main Heading
                                </div>
                                <div class="card-body">
                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="main_heading">Main Heading</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="main_heading"
                                                name="main_heading" value="{{ $howItWorks->main_heading ?? '' }}" />
                                        </div>
                                        @error('main_heading')
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
                                                name="section_1_title" value="{{ $howItWorks->section_1_title ?? '' }}" />
                                        </div>
                                        @error('section_1_title')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_1_description">Section 1 Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="section_1_description"
                                                name="section_1_description" rows="3">{{ $howItWorks->section_1_description ?? '' }}</textarea>
                                        </div>
                                        @error('section_1_description')
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
                                                name="section_2_title" value="{{ $howItWorks->section_2_title ?? '' }}" />
                                        </div>
                                        @error('section_2_title')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_2_description">Section 2 Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="section_2_description"
                                                name="section_2_description" rows="3">{{ $howItWorks->section_2_description ?? '' }}</textarea>
                                        </div>
                                        @error('section_2_description')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_2_button">Section 2 Button</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="section_2_button"
                                                name="section_2_button" value="{{ $howItWorks->section_2_button ?? '' }}" />
                                        </div>
                                        @error('section_2_button')
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
                                                name="section_3_title" value="{{ $howItWorks->section_3_title ?? '' }}" />
                                        </div>
                                        @error('section_3_title')
                                             <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label class="form-label" for="section_3_description">Section 3 Description</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" id="section_3_description"
                                                name="section_3_description" rows="3">{{ $howItWorks->section_3_description ?? '' }}</textarea>
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
                                                @if (isset($howItWorks->section_3_image))
                                                <img src="{{ asset($howItWorks->section_3_image) }}" alt="section_3_image"
                                                    class="mt-2" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                        @error('section_3_image')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- <!-- Heading Section -->
                            <div class="col-md-12 mt-3">
                                <div class="card border">
                                    <div class="card-header mt-3">
                                        Heading Section
                                        <button type="button" class="btn btn-success btn-sm float-end"
                                            id="add-heading-item">Add Item</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="heading_icon">Image</label>
                                            <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="heading_icon" accept=".jpg,.jpeg,.png,.gif,.svg" />
                                            </div>
                                            <div class="error-message text-danger mt-1"></div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="heading_title">Title</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="heading_title"
                                                    placeholder="Enter Title..." />
                                            </div>
                                            <div class="error-message text-danger mt-1"></div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="heading_description">Description</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" id="heading_description"
                                                    placeholder="Enter Description..." rows="3"></textarea>
                                            </div>
                                            <div class="error-message text-danger mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Container for new heading items -->
                            <div id="heading_list" class="heading-container mt-3">
                                <!-- New items will be appended here -->
                            </div>

                            <!-- Container for existing heading items -->
                            <div id="heading_section" class="heading-container">
                                <div class="col-md-12 mt-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            Existing Heading Items
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
                                                    @php
                                                        $headingItems = [];
                                                        if (isset($howItWorks->heading_section)) {
                                                            $headingItems = is_string($howItWorks->heading_section)
                                                                ? json_decode($howItWorks->heading_section, true)
                                                                : $howItWorks->heading_section;
                                                            $headingItems = is_array($headingItems) ? array_values(array_filter($headingItems)) : [];
                                                        }
                                                    @endphp

                                                    @forelse ($headingItems as $index => $item)
                                                        @if(isset($item['icon']) && isset($item['title']) && isset($item['description']))
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>

                                                                <td class="heading-icon">
                                                                    <img src="path/to/icon" alt="icon" style="max-width: 50px; max-height: 50px;" />
                                                                    <input type="hidden" name="heading_section[{{ $index }}][icon]" value="{{ $item['icon'] }}">
                                                                </td>


                                                                <td class="heading-title">
                                                                    {{ $item['title'] }}
                                                                    <input type="hidden" name="heading_section[{{ $index }}][title]" value="{{ $item['title'] }}">
                                                                </td>

                                                                <td class="heading-description">
                                                                    {{ Str::limit($item['description'], 100) }}
                                                                    <input type="hidden" name="heading_section[{{ $index }}][description]" value="{{ $item['description'] }}">
                                                                </td>

                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-heading-item"
                                                                        data-index="{{ $index }}">Delete</button>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-success btn-sm edit-heading-item"
                                                                        data-index="{{ $index }}"
                                                                        data-icon="{{ htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') }}"
                                                                        data-title="{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}"
                                                                        data-description="{{ htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') }}">
                                                                        Edit
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">No heading items available.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit form for existing items -->
                                <div id="edit-heading-item-details" class="mt-4 p-3 border rounded" style="display: none; background-color: #f8f9fa;">
                                    <h5>Edit Heading Item</h5>
                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Image:</strong></label>
                                        <input type="hidden" id="edit-heading-index" />
                                        <input type="file" class="form-control" id="edit-heading-image" accept=".jpg,.jpeg,.png,.gif,.svg" />
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Title:</strong></label>
                                        <input type="text" class="form-control" id="edit-heading-title" />
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label"><strong>Description:</strong></label>
                                        <textarea class="form-control" id="edit-heading-description" rows="3"></textarea>
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" id="save-heading-button" class="btn btn-primary">Save Changes</button>
                                        <button type="button" id="cancel-edit-heading" class="btn btn-secondary ms-2">Cancel</button>
                                    </div>
                                </div>
                            </div> --}}

                                    <!-- Right Tools Section -->
                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Right Tools
                                                <button type="button" class="btn btn-success btn-sm float-end"
                                                id="add-how-it-work-item">Add Item</button>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="how_it_work_image">Image</label>
                                                    <div class="form-control-wrap">
                                                        <input type="file" class="form-control"  id="how_it_work_image" />
                                                    </div>
                                                    <div class="error-message text-danger mt-1"></div>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="how_it_work_title">Title</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"id="how_it_work_title"
                                                            placeholder="Enter Here Title.." />
                                                    </div>
                                                    <div class="error-message text-danger mt-1"></div>
                                                </div>

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="how_it_work_description">Description</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            id="how_it_work_description"
                                                            placeholder="Enter Here Description..." />
                                                    </div>
                                                    <div class="error-message text-danger mt-1"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Container for new right tool items -->
                                    <div id="how_it_work_list" class="mt-3">

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
                                                                            class="btn btn-success btn-sm update-hiw-item"
                                                                            data-id="{{ $translation->id }}"
                                                                            data-title="{{ $translation->title }}"
                                                                            data-description="{{ $translation->description }}"
                                                                            data-image="{{ asset($translation->image) }}">
                                                                            Edit
                                                                        </button>

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
                                                <input type="hidden" id="updated-rtt-id" name="RTT[id][]" />
                                                <input type="text" class="form-control" id="updated-rtt-title" name="RTT[title][]" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <div class="form-group mt-3">
                                                <label class="form-label"><strong>Description:</strong></label>
                                                <input type="text" class="form-control" id="updated-rtt-description" name="RTT[description][]" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <div class="form-group mt-3">
                                                <label class="form-label"><strong>Current Image:</strong></label>
                                                <div class="mb-2">
                                                    <img id="updated-rtt-image" style="width: 100px; height: auto;" />
                                                </div>
                                                <label class="form-label">Upload New Image (optional):</label>
                                                <input type="file" class="form-control" id="update-rtt-image-input" name="RTT[image][]" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <button type="button" id="save-rtt-button" class="btn btn-primary mt-3">Save Changes</button>
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
                                                    value="{{ $howItWorks->meta_title ?? '' }}">
                                                @error('meta_title')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-3">
                                                <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                                                <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                                                    placeholder="Enter meta description">{{ $howItWorks->meta_description ?? '' }}</textarea>
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
    $(document).ready(function () {
        let howItWorkItems = [];

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

        function clearHowItWorkForm() {
            $('#how_it_work_title, #how_it_work_description').val('');
            $('#how_it_work_image').val('');
            clearValidationErrors();
        }

        $('#add-how-it-work-item').on('click', function () {
            clearValidationErrors();

            const title = $('#how_it_work_title').val();
            const description = $('#how_it_work_description').val();
            const imageInput = $('#how_it_work_image')[0];
            const imageFile = imageInput.files[0];
            let isValid = true;

            if (!title) {
                showValidationError('how_it_work_title', 'Title is required');
                isValid = false;
            }

            if (!description) {
                showValidationError('how_it_work_description', 'Description is required');
                isValid = false;
            }

            if (!imageFile) {
                showValidationError('how_it_work_image', 'Image is required');
                isValid = false;
            } else {
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(imageFile.type)) {
                    showValidationError('how_it_work_image', 'Image must be jpeg, png, jpg, or gif');
                    isValid = false;
                }
                if (imageFile.size > 2 * 1024 * 1024) {
                    showValidationError('how_it_work_image', 'Image size must be less than 2MB');
                    isValid = false;
                }
            }

            if (isValid) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const base64Image = e.target.result;

                    const itemCard = $('<div class="item-card mt-2 p-2 border">');
                    itemCard.html(`
                        <h5>${title}</h5>
                        <p>${description}</p>
                        <img src="${base64Image}" alt="Image" style="width: 100px; height: auto;">
                        <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                    `);

                    $('#how_it_work_list').append(itemCard);

                    const hiddenTitleInput = $(`<input type="hidden" name="how_it_work[title][]" value="${title}">`);
                    const hiddenDescriptionInput = $(`<input type="hidden" name="how_it_work[description][]" value="${description}">`);
                    const hiddenImageInput = $(`<input type="hidden" name="how_it_work[image][]" value="${base64Image}">`);

                    $('#how_it_work_list').append(hiddenTitleInput, hiddenDescriptionInput, hiddenImageInput);

                    howItWorkItems.push({
                        title,
                        description,
                        image: base64Image,
                        card: itemCard,
                        hiddenInputs: [hiddenTitleInput, hiddenDescriptionInput, hiddenImageInput],
                    });

                    clearHowItWorkForm();
                };
                reader.readAsDataURL(imageFile);
            }
        });

        $('#how_it_work_list').on('click', '.remove-item', function () {
            const itemCard = $(this).closest('.item-card');
            const index = howItWorkItems.findIndex(i => i.card[0] === itemCard[0]);
            if (index > -1) {
                itemCard.remove();
                howItWorkItems[index].hiddenInputs.forEach(input => input.remove());
            }
        });

        $('.update-hiw-item').on('click', function () {
            const id = $(this).data('id');
            const title = $(this).data('title');
            const description = $(this).data('description');
            const imageSrc = $(this).data('image');

            $('#updated-rtt-id').val(id);
            $('#updated-rtt-title').val(title);
            $('#updated-rtt-description').val(description);
            $('#updated-rtt-image').attr('src', imageSrc);

            $('#updated-rtt-item-details').show();
            $('#save-rtt-button').show();

            $('html, body').animate({
                scrollTop: $('#updated-rtt-item-details').offset().top - 100
            }, 200);
        });


        $('#update-hiw-image-input').on('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    showValidationError('update-hiw-image-input', 'Image must be jpeg, png, jpg, or gif');
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    showValidationError('update-hiw-image-input', 'Image size must be less than 2MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function () {
                    $('#updated-hiw-image').attr('src', reader.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $('#save-rtt-button').on('click', function () {
            clearValidationErrors();
            let isValid = true;

            const itemId = $('#updated-rtt-id').val();
            const title = $('#updated-rtt-title').val();
            const description = $('#updated-rtt-description').val();
            const imageFile = $('#update-rtt-image-input')[0].files[0];

            if (!title) {
                showValidationError('updated-rtt-title', 'Title is required');
                isValid = false;
            }
            if (!description) {
                showValidationError('updated-rtt-description', 'Description is required');
                isValid = false;
            }

            if (!isValid) return;

            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('id', itemId);
            formData.append('title', title);
            formData.append('description', description);

            if (imageFile) {
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(imageFile.type)) {
                    showValidationError('update-rtt-image-input', 'Image must be jpeg, png, jpg, or gif');
                    return;
                }
                formData.append('image', imageFile);
            }

            $.ajax({
                url: '/admin/page-right-tool-translation/update', // ❗ Make sure this matches your route
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        const successMsg = $('<div class="alert alert-success">Item updated successfully!</div>');
                        $('#updated-rtt-item-details').before(successMsg);
                        setTimeout(() => successMsg.fadeOut(500, function () { $(this).remove(); }), 3000);
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        for (const field in xhr.responseJSON.errors) {
                            showValidationError('updated-rtt-' + field.replace('.', '-'), xhr.responseJSON.errors[field][0]);
                        }
                    } else {
                        const errorMsg = $('<div class="alert alert-danger">Error updating item. Please try again.</div>');
                        $('#updated-rtt-item-details').before(errorMsg);
                        setTimeout(() => errorMsg.fadeOut(500, function () { $(this).remove(); }), 3000);
                    }
                }
            });
        });


        $('form').on('submit', function (e) {
    clearValidationErrors();
    let isValid = true;

    // Simple required fields
    const requiredFields = [
        'banner_title',
        'banner_description',
        'main_heading',
        'section_1_title',
        'section_1_description',
        'section_2_title',
        'section_2_description',
        'section_2_button',
        'section_3_title',
        'section_3_description'
    ];

    requiredFields.forEach(field => {
        const val = $(`#${field}`).val();
        if (!val || !val.trim()) {
            showValidationError(field, 'This field is required');
            isValid = false;
        }
    });

    // Handle heading_section[] - array input fields
    // const headingInputs = $('input[name="heading_section[]"]');
    // headingInputs.each(function (index) {
    //     const val = $(this).val();
    //     if (!val || !val.trim()) {
    //         const id = $(this).attr('id') || `heading_section_${index}`;
    //         $(this).attr('id', id); // ensure it has an ID
    //         showValidationError(id, 'Heading section field is required');
    //         isValid = false;
    //     }
    // });

    // Image fields (if required or being updated)
    const imageFields = [
        'banner_image_left',
        'banner_image_right',
        'section_3_image'
    ];

    imageFields.forEach(field => {
        const input = document.getElementById(field);
        if (input && input.files.length > 0) {
            const file = input.files[0];
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
        const firstInvalid = $('.is-invalid:first');
        if (firstInvalid.length) {
            $('html, body').animate({
                scrollTop: firstInvalid.offset().top - 100
            }, 200);
        }
    }
});

    });
</script>




@endsection
