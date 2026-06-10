@extends('admin_layout.master')
@section('content')

<?php
use Illuminate\Support\Facades\Redis;
$lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
$lang_id = Redis::get('admin_lang_id');
?>
<div class="nk-block nk-block-lg pages-expert-guide">
    <div class="nk-block-head d-flex justify-content-between">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Expert Guide</h4>
        </div>
    </div>


    <form action="{{ route('expertGuide.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Title Field -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="nk-block">
                        </div>
                        <div>
                            <div>
                                <label for="title"><strong>Title</strong></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $expertGuide->title ?? '') }}" required>
                                @error('title')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <br>
                            <!-- Description Field -->
                            <div>
                                <label for="description"><strong>Description</strong></label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $expertGuide->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <br>
                            <!-- Education Title Field -->
                            <div>
                                <label for="education_title"><strong>Education Title</strong></label>
                                <input type="text" name="education_title" id="education_title" class="form-control @error('education_title') is-invalid @enderror"
                                    value="{{ old('education_title', $expertGuide->education_title ?? '') }}" required>
                                @error('education_title')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <br>
                            <!-- Education Description Field -->
                            <div>
                                <label for="education_description"><strong>Education Description</strong></label>
                                <textarea name="education_description" id="education_description" class="form-control @error('education_description') is-invalid @enderror" required>{{ old('education_description', $expertGuide->education_description ?? '') }}</textarea>
                                @error('education_description')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <br>
                            <div class="col-md-12">
                                <div class="card border">
                                    <div class="card-header mt-3">
                                        <strong> Education </strong>
                                        <button type="button" class="btn btn-success btn-sm float-end"
                                            id="add-education-item">Add
                                            Item</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="image">Image</label>
                                            <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="edu_image" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="title">Title</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="edu_title"
                                                    placeholder="Enter Here Title.." />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="description">Description</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control site_text_input"
                                                    id="edu_desc" placeholder="Enter Here Description..." />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div id="education-items-list" class="popular-items-container">
                                <!-- Popular items will be appended here -->
                            </div>

                            <div id="education-items" class="popular-items-container">
                                <div class="col-md-12 mt-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            Education items
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
                                                <tbody>
                                                    @forelse ($pageTileTranslationEducation as $index => $pageTile)
                                                        @php
                                                            $firstTranslation = $pageTile->translations->first();
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>
                                                                @if ($firstTranslation && $firstTranslation->image)
                                                                    <img src="{{ asset($firstTranslation->image) }}"
                                                                        alt="Item Image"
                                                                        style="width: 100px; height: auto;">
                                                                @else
                                                                    No Image
                                                                @endif
                                                            </td>
                                                            <td>{{ $firstTranslation?->title ?? 'No Title' }}
                                                            </td>
                                                            <td>{{ $firstTranslation?->description ?? 'No Description' }}
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-danger btn-sm"
                                                                    href="{{ route('admin.page_tile_translation.delete', $pageTile->id) }}">Delete</a>
                                                            </td>
                                                            <td>
                                                                @if ($firstTranslation)
                                                                    <button type="button"
                                                                        class="btn btn-success btn-sm update-education-item"
                                                                        data-id="{{ $firstTranslation->id ?? '' }}"
                                                                        data-title="{{ $firstTranslation->title ?? '' }}"
                                                                        data-des="{{ $firstTranslation->description ?? '' }}"
                                                                        data-image="{{ $firstTranslation->image ? asset($firstTranslation->image) : '' }}">
                                                                        Edit
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">No Data</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6">No records found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div id="updated-item-details" class="mt-4 p-3" style="display: none; border: 1px solid #eee; border-radius: 5px;">
                                            <h5>Edit Education Item</h5>
                                            <div class="form-group mt-3">
                                                <label for="updated-es-title"><strong>Title</strong></label>
                                                <input type="hidden" class="form-control" id="updated-es-id" name="ES[id]"
                                                    value="{{ old('id', $pageTileTranslation->id ?? '') }}" />
                                                <input type="text" class="form-control" id="updated-es-title"
                                                    name="ES[title]"
                                                    value="{{ old('title', $pageTileTranslation->title ?? '') }}" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <div class="form-group mt-3">
                                                <label for="updated-es-description"><strong>Description</strong></label>
                                                <input type="text" class="form-control" id="updated-es-description"
                                                    name="ES[description]"
                                                    value="{{ old('description', $pageTileTranslation->description ?? '') }}" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <div class="form-group mt-3">
                                                <label for="update-es-image-input"><strong>Image</strong></label>
                                                <div class="mb-2">
                                                    <img id="updated-es-image" style="width: 100px; height: auto;" />
                                                </div>
                                                <input type="file" id="update-es-image-input" name="ES[image]" class="form-control" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>

                                            <div class="mt-3">
                                                <button type="button" id="save-es-button" class="btn btn-primary">Save Changes</button>
                                                <button type="button" class="btn btn-light" onclick="$('#updated-item-details').hide()">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <!-- Smart Search Title -->
                                <div>
                                    <label for="smart_search"><strong>Smart Search Title</strong></label>
                                    <input type="text" name="smart_search" id="smart_search" class="form-control @error('smart_search') is-invalid @enderror"
                                        value="{{ old('smart_search', $expertGuide->smart_search ?? '') }}" required>
                                    @error('smart_search')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <!-- Smart Search Description -->
                                <div>
                                    <label for="smart_search_description"><strong>Smart Search Description</strong></label>
                                    <textarea name="smart_search_description" id="smart_search_description" class="form-control @error('smart_search_description') is-invalid @enderror"
                                        required>{{ old('smart_search_description', $expertGuide->smart_search_description ?? '') }}</textarea>
                                    @error('smart_search_description')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <!-- How To Check Email (CKEditor) -->
                                <div>
                                    <label for="how_to_check_email"><strong>How To Check Email</strong></label>
                                    <input type="text" name="how_to_check_email" id="how_to_check_email"
                                        class="form-control @error('how_to_check_email') is-invalid @enderror"
                                        value="{{ old('how_to_check_email', $expertGuide->how_to_check_email ?? '') }}" required>
                                    @error('how_to_check_email')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <!-- Overview -->
                                <div>
                                    <label for="overview"><strong>Overview Heading</strong></label>
                                    <input type="text" name="overview" id="overview" class="form-control @error('overview') is-invalid @enderror"
                                        value="{{ old('overview', $expertGuide->overview ?? '') }}" required>
                                    @error('overview')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <div>
                                    <label for="email_description"><strong>Email Description</strong></label>
                                    <textarea name="email_description" id="email_description" class="description form-control @error('email_description') is-invalid @enderror">{{ old('email_description', $expertGuide->email_description ?? '') }}</textarea>
                                    @error('email_description')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <!-- Webmail -->
                                <div>
                                    <label for="webmail"><strong>Webmail</strong></label>
                                    <input type="text" name="webmail" id="webmail" class="form-control @error('webmail') is-invalid @enderror"
                                        value="{{ old('webmail', $expertGuide->webmail ?? '') }}" required>
                                    @error('webmail')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <div>
                                    <label for="webmail_description"><strong>webmail Description</strong></label>
                                    <textarea name="webmail_description" id="webmail_description" class="description form-control @error('webmail_description') is-invalid @enderror">{{ old('webmail_description', $expertGuide->webmail_description ?? '') }}</textarea>
                                    @error('webmail_description')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <!-- Email Application -->
                                <div>
                                    <label for="email_application"><strong>Email Application</strong></label>
                                    <input type="text" name="email_application" id="email_application"
                                        class="form-control @error('email_application') is-invalid @enderror"
                                        value="{{ old('email_application', $expertGuide->email_application ?? '') }}" required>
                                    @error('email_application')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <div>
                                    <label for="email_app_description"><strong>Email Application Description</strong></label>
                                    <textarea name="email_app_description" id="email_app_description" class="description form-control @error('email_app_description') is-invalid @enderror">{{ old('email_app_description', $expertGuide->email_app_description ?? '') }}</textarea>
                                    @error('email_app_description')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <!-- IMAP and POP -->
                                <div>
                                    <label for="imap"><strong>IMAP and POP</strong></label>
                                    <input type="text" name="imap" id="imap" class="form-control @error('imap') is-invalid @enderror"
                                        value="{{ old('imap', $expertGuide->imap ?? '') }}" required>
                                    @error('imap')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <br>
                                <div>
                                    <label for="imap_pop"><strong>IMAP & POP Description</strong></label>
                                    <textarea name="imap_pop" id="imap_pop" class="description form-control @error('imap_pop') is-invalid @enderror">{{ old('imap_pop', $expertGuide->imap_pop ?? '') }}</textarea>
                                    @error('imap_pop')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="col-md-12">
                                <label for="right_tool_heading"><strong>Right Tool Heading</strong></label>
                                <input type="text" name="right_tool_heading" id="right_tool_heading"
                                    class="form-control @error('right_tool_heading') is-invalid @enderror"
                                    value="{{ old('right_tool_heading', $expertGuide->right_tool_heading ?? '') }}" required>
                                @error('right_tool_heading')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <br>
                            <div class="col-md-12">
                                <label for="get_start_button"><strong>Get Start Button</strong></label>
                                <input type="text" name="get_start_button" id="get_start_button"
                                    class="form-control @error('get_start_button') is-invalid @enderror"
                                    value="{{ old('get_start_button', $expertGuide->get_start_button ?? '') }}" required>
                                @error('get_start_button')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <br>
                            <div class="col-md-12">
                                <label for="assistant"><strong>Assistance</strong></label>
                                <textarea name="assistant" id="assistant" class="description form-control @error('assistant') is-invalid @enderror">{{ old('assistant', $expertGuide->assistant ?? '') }}</textarea>
                                @error('assistant')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="card border">
                                    <div class="card-header mt-3">
                                        Right Tools
                                        <button type="button" class="btn btn-success btn-sm float-end"
                                            id="add-Right-item">Add Item</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="image">Image</label>
                                            <div class="form-control-wrap">
                                                <input type="file" class="form-control" id="tool_image" />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="title">Title</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="tool_title"
                                                    placeholder="Enter Here Title.." />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <label class="form-label" for="description">Description</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control site_text_input"
                                                    id="tool_description" placeholder="Enter Here Description..." />
                                                <div class="error-message text-danger mt-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="right_tools_list" class="right-tools-container">
                                <!-- Right tools items will be appended here -->
                            </div>
                            <div id="right_tools" class="right-tools-container">
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
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($pageTileTranslationRightTools as $index => $item)
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
                                                                <td>{{ $translation->title ?? 'No Title' }}
                                                                </td>
                                                                <td>{{ $translation->description ?? 'No Description' }}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-danger btn-sm"
                                                                        href="{{ route('admin.page_tile_translation.delete', $item->id) }}">Delete</a>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-success btn-sm update-rt-item"
                                                                        data-id="{{ $translation->id }}"
                                                                        data-title="{{ $translation->title }}"
                                                                        data-desc="{{ $translation->description }}"
                                                                        data-image="{{ asset($translation->image) }}">Edit</button>
                                                                </td>
                                                        @endforeach
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5">No items available.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="updated-rt-item-details" class="mt-4 p-3" style="display: none; border: 1px solid #eee; border-radius: 5px;">
                                    <h5>Edit Right Tool Item</h5>
                                    <div class="form-group mt-3">
                                        <label for="updated-rt-title"><strong>Title</strong></label>
                                        <input type="hidden" class="form-control" id="updated-rt-id" name="RT[id]"
                                            value="{{ old('title', $item->title ?? '') }}" />
                                        <input type="text" class="form-control" id="updated-rt-title"
                                            name="RT[title]" value="{{ old('title', $item->title ?? '') }}" />
                                        <div class="error-message text-danger mt-1"></div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="updated-rt-description"><strong>Description</strong></label>
                                        <input type="text" class="form-control" id="updated-rt-description"
                                            name="RT[description]"
                                            value="{{ old('description', $item->description ?? '') }}" />
                                        <div class="error-message text-danger mt-1"></div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="update-rt-image-input"><strong>Image</strong></label>
                                        <div class="mb-2">
                                            <img id="updated-rt-image" style="width: 100px; height: auto;" />
                                        </div>
                                        <input type="file" id="update-rt-image-input" name="RT[image]" class="form-control" />
                                        <div class="error-message text-danger mt-1"></div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" id="save-rt-button" class="btn btn-primary">Save Changes</button>
                                        <button type="button" class="btn btn-light" onclick="$('#updated-rt-item-details').hide()">Cancel</button>
                                    </div>
                                </div>
                            </div>
                            <br>
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
                                                    <option value="{{ $language->id }}" {{ $lang_code == $language->lang_code ? 'selected' : '' }}>
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
                                                    value="{{ $expertGuide->permanent_url ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                                            <input type="text" id="meta_title" name="meta_title"
                                                class="form-control @error('meta_title') is-invalid @enderror"
                                                placeholder="Enter meta title"
                                                value="{{ old('meta_title', $expertGuide->meta_title ?? '') }}">
                                            @error('meta_title')
                                                <div class="error text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                                            <textarea id="meta_description" name="meta_description"
                                                class="form-control @error('meta_description') is-invalid @enderror"
                                                rows="3" placeholder="Enter meta description">{{ old('meta_description', $expertGuide->meta_description ?? '') }}</textarea>
                                            @error('meta_description')
                                                <div class="error text-danger">{{ $message }}</div>
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
    // Initialize the items arrays just once
    let educationItems = [];
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

    function clearEducationForm() {
        $('#edu_title, #edu_desc, #edu_image').val('');
        clearValidationErrors();
    }

    function clearRightToolForm() {
        $('#tool_title, #tool_description, #tool_image').val('');
        clearValidationErrors();
    }

    // Education Items
    $('#add-education-item').on('click', function() {
        clearValidationErrors();

        const title = $('#edu_title').val();
        const description = $('#edu_desc').val();
        const imageInput = $('#edu_image')[0];
        const imageFile = imageInput.files[0];
        let isValid = true;

        // Validate title
        if (!title) {
            showValidationError('edu_title', 'Title is required');
            isValid = false;
        }

        // Validate description
        if (!description) {
            showValidationError('edu_desc', 'Description is required');
            isValid = false;
        }

         // Validate image
     if (!imageFile) {
            showValidationError('edu_image', 'Image is required');
            isValid = false;
        } else {
            // Validate image type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(imageFile.type)) {
                showValidationError('edu_image', 'Image must be jpeg, png, jpg, or gif');
                isValid = false;
            }

            // Validate image size (max 2MB)
            if (imageFile.size > 2 * 1024 * 1024) {
                showValidationError('edu_image', 'Image size must be less than 2MB');
                isValid = false;
            }
        }


        if (isValid) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const itemCard = $('<div class="item-card mt-2 p-2 border">');
                itemCard.html(`
                    <h5>${title}</h5>
                    <p>${description}</p>
                    <img src="${e.target.result}" alt="Image" style="width: 100px; height: auto;">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                `);

                $('#education-items-list').append(itemCard);

                const hiddenTitleInput = $('<input type="hidden" name="education_items[title][]" value="' + title + '">');
                const hiddenDescriptionInput = $('<input type="hidden" name="education_items[description][]" value="' + description + '">');
                const hiddenImageInput = $('<input type="hidden" name="education_items[image][]" value="' + e.target.result + '">');

                $('#education-items-list').append(hiddenTitleInput, hiddenDescriptionInput, hiddenImageInput);

                educationItems.push({
                    title: title,
                    description: description,
                    image: e.target.result,
                    card: itemCard,
                    hiddenInputs: [hiddenTitleInput, hiddenDescriptionInput, hiddenImageInput]
                });

                clearEducationForm();
            };
            reader.readAsDataURL(imageFile);
        }
    });

    // Right Tool Items
    $('#add-Right-item').on('click', function() {
        clearValidationErrors();

        const title = $('#tool_title').val();
        const description = $('#tool_description').val();
        const imageInput = $('#tool_image')[0];
        const imageFile = imageInput.files[0];
        let isValid = true;

        // Validate title
        if (!title) {
            showValidationError('tool_title', 'Title is required');
            isValid = false;
        }

        // Validate description
        if (!description) {
            showValidationError('tool_description', 'Description is required');
            isValid = false;
        }

     // Validate image
     if (!imageFile) {
            showValidationError('tool_image', 'Image is required');
            isValid = false;
        } else {
            // Validate image type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(imageFile.type)) {
                showValidationError('tool_image', 'Image must be jpeg, png, jpg, or gif');
                isValid = false;
            }

            // Validate image size (max 2MB)
            if (imageFile.size > 2 * 1024 * 1024) {
                showValidationError('tool_image', 'Image size must be less than 2MB');
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

                $('#right_tools_list').append(itemCard);

                const hiddenTitleInput = $('<input type="hidden" name="right_tools[title][]" value="' + title + '">');
                const hiddenDescriptionInput = $('<input type="hidden" name="right_tools[description][]" value="' + description + '">');
                const hiddenImageInput = $('<input type="hidden" name="right_tools[image][]" value="' + e.target.result + '">');

                $('#right_tools_list').append(hiddenTitleInput, hiddenDescriptionInput, hiddenImageInput);

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

    // Remove item handlers
    $('#education-items-list').on('click', '.remove-item', function() {
        const itemCard = $(this).closest('.item-card');
        const itemIndex = educationItems.findIndex(item => item.card[0] === itemCard[0]);

        if (itemIndex > -1) {
            educationItems[itemIndex].removed = true;
            itemCard.remove();
            educationItems[itemIndex].hiddenInputs.forEach(input => input.remove());
        }
    });

    $('#right_tools_list').on('click', '.remove-item', function() {
        const itemCard = $(this).closest('.item-card');
        const itemIndex = rightToolItems.findIndex(item => item.card[0] === itemCard[0]);

        if (itemIndex > -1) {
            rightToolItems[itemIndex].removed = true;
            itemCard.remove();
            rightToolItems[itemIndex].hiddenInputs.forEach(input => input.remove());
        }
    });

    // Edit Education Item
    $('#education-items').on('click', '.update-education-item', function() {
        let itemId = $(this).data('id');
        let title = $(this).data('title');
        let description = $(this).data('des');
        let imageSrc = $(this).data('image');

        // Populate the form fields with current data
        $('#updated-es-id').val(itemId);
        $('#updated-es-title').val(title);
        $('#updated-es-description').val(description);
        $('#updated-es-image').attr('src', imageSrc);

        // Show the update form
        $('#updated-item-details').show();
    });

    // Handle file input change for image preview - Education
    $('#update-es-image-input').on('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function() {
                $('#updated-es-image').attr('src', reader.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Save updated Education Item
    $('#save-es-button').on('click', function() {
        clearValidationErrors();
        let isValid = true;

        let itemId = $('#updated-es-id').val();
        let title = $('#updated-es-title').val();
        let description = $('#updated-es-description').val();

        // Validate fields
        if (!title) {
            showValidationError('updated-es-title', 'Title is required');
            isValid = false;
        }

        if (!description) {
            showValidationError('updated-es-description', 'Description is required');
            isValid = false;
        }

        if (!isValid) return;

        let imageFile = $('#update-es-image-input')[0].files[0];
        let formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('id', itemId);
        formData.append('title', title);
        formData.append('description', description);

        if (imageFile) {
            formData.append('image', imageFile);
        }

        $.ajax({
            url: '/admin/page-education-translation/update',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const successMsg = $('<div class="alert alert-success">Item updated successfully!</div>');
                    $('#updated-item-details').before(successMsg);
                    setTimeout(() => successMsg.fadeOut(500, function() { $(this).remove(); }), 3000);

                    // Optionally reload the page after a delay
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);

                // Handle validation errors from server
                if (xhr.status === 422 && xhr.responseJSON) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        showValidationError('updated-es-' + field.replace('.', '-'), errors[field][0]);
                    }
                } else {
                    // Show general error
                    const errorMsg = $('<div class="alert alert-danger">Error updating item. Please try again.</div>');
                    $('#updated-item-details').before(errorMsg);
                    setTimeout(() => errorMsg.fadeOut(500, function() { $(this).remove(); }), 3000);
                }
            }
        });
    });

    // Edit Right Tool Item
    $('#right_tools').on('click', '.update-rt-item', function() {
        let itemId = $(this).data('id');
        let title = $(this).data('title');
        let description = $(this).data('desc');
        let imageSrc = $(this).data('image');

        // Populate the form fields with current data
        $('#updated-rt-id').val(itemId);
        $('#updated-rt-title').val(title);
        $('#updated-rt-description').val(description);
        $('#updated-rt-image').attr('src', imageSrc);

        // Show the update form
        $('#updated-rt-item-details').show();
    });

    // Handle file input change for image preview - Right Tool
    $('#update-rt-image-input').on('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function() {
                $('#updated-rt-image').attr('src', reader.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Save updated Right Tool Item
    $('#save-rt-button').on('click', function() {
        clearValidationErrors();
        let isValid = true;

        let itemId = $('#updated-rt-id').val();
        let title = $('#updated-rt-title').val();
        let description = $('#updated-rt-description').val();

        // Validate fields
        if (!title) {
            showValidationError('updated-rt-title', 'Title is required');
            isValid = false;
        }

        if (!description) {
            showValidationError('updated-rt-description', 'Description is required');
            isValid = false;
        }

        if (!isValid) return;

        let imageFile = $('#update-rt-image-input')[0].files[0];
        let formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('id', itemId);
        formData.append('title', title);
        formData.append('description', description);

        if (imageFile) {
            formData.append('image', imageFile);
        }

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
                    $('#updated-rt-item-details').before(successMsg);
                    setTimeout(() => successMsg.fadeOut(500, function() { $(this).remove(); }), 3000);

                    // Optionally reload the page after a delay
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);

                // Handle validation errors from server
                if (xhr.status === 422 && xhr.responseJSON) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        showValidationError('updated-rt-' + field.replace('.', '-'), errors[field][0]);
                    }
                } else {
                    // Show general error
                    const errorMsg = $('<div class="alert alert-danger">Error updating item. Please try again.</div>');
                    $('#updated-rt-item-details').before(errorMsg);
                    setTimeout(() => errorMsg.fadeOut(500, function() { $(this).remove(); }), 3000);
                }
            }
        });
    });

    // Cancel buttons for edit forms
    $('.btn-light').on('click', function() {
        $(this).closest('.mt-4.p-3').hide();
    });

    // Form submission validation
    $('form').on('submit', function(e) {
        clearValidationErrors();
        let isValid = true;

        // Validate required fields
        const requiredFields = [
            'title', 'description', 'education_title', 'education_description',
            'smart_search', 'smart_search_description', 'how_to_check_email',
            'overview', 'webmail', 'email_application', 'imap',
            'meta_title', 'meta_description'
        ];

        requiredFields.forEach(field => {
            const value = $(`#${field}`).val();
            if (!value || !value.trim()) {
                showValidationError(field, 'This field is required');
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.error').first().offset().top - 100
            }, 200);
        }
    });

    // Initialize CKEditor for rich text fields
    if (typeof CKEDITOR !== 'undefined') {
        $('.description').each(function() {
            CKEDITOR.replace($(this).attr('id'));
        });
    }
});
    </script>
@endsection
