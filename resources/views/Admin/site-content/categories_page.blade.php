@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title mb-1">All Categories Page Content</h4>
                <div class="text-muted small">Manage header banners, headings, global dynamic variables, and category card descriptions displayed on the frontend /categories page.</div>
            </div>
            <div class="nk-block-head-content d-flex align-items-center gap-2 mt-2 mt-md-0">
                <a href="{{ url((getCurrentLocale() ?? 'en-us') . '/categories') }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                    <em class="icon ni ni-external me-1"></em> View Frontend Page
                </a>
                <div class="d-flex align-items-center">
                    <label class="form-label me-2 mb-0 font-weight-bold small text-nowrap">Language:</label>
                    <select class="form-select form-select-sm" id="langSelect" onchange="window.location.href='{{ url('/admin-dashboard/categories-page') }}?lang_id=' + this.value">
                        @if(isset($languages))
                            @foreach ($languages as $lang)
                                <option value="{{ $lang->id }}" {{ (isset($lang_id) && $lang_id == $lang->id) ? 'selected' : '' }}>
                                    {{ $lang->name }} ({{ $lang->lang_code }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        @php
            $lang_code = getCurrentLocale();
        @endphp

        @if (isset($categoryPageContents))
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="nk-block">
                        <form action="{{ url('admin-dashboard/category-page-update') ?? '' }}" class="form-validate"
                            novalidate="novalidate" method="post" enctype="multipart/form-data" id="categoryPageForm">
                            @csrf
                            <input type="hidden" name="lang_id" value="{{ $lang_id ?? 1 }}">

                            <div class="row g-3">
                                @if ($lang_code == 'en-us')
                                    <div class="col-12">
                                        <div class="card border">
                                            <div class="card-header bg-light fw-bold py-2">
                                                Category Header Banner Section
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        @foreach ($categoryPageContents as $content)
                                                            @if ($content->meta_key == 'header_image')
                                                                <div class="form-group">
                                                                    <label class="form-label" for="image">Header Image</label>
                                                                    <div class="dz-message">
                                                                        <input type="file" class="form-control"
                                                                            name="category_header_image[{{ $content->id }}]"
                                                                            id="metaValue" value="{{ $content->value ?? '' }}">
                                                                    </div>
                                                                    @error('category_header_image')
                                                                        <div class="error text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                    @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                        <img src="{{ asset($content->meta_value) }}"
                                                                            alt="{{ $content->meta_key }}"
                                                                            class="mt-2 rounded border"
                                                                            style="width: 100px; height: auto;">
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="col-md-6">
                                                        @foreach ($categoryPageContents as $content)
                                                            @if ($content->meta_key == 'header_bg_image')
                                                                <div class="form-group">
                                                                    <label class="form-label" for="image">Background Image</label>
                                                                    <div class="dz-message">
                                                                        <input type="file" class="form-control"
                                                                            name="category_background_image[{{ $content->id }}]"
                                                                            id="metaValue" value="{{ $content->value ?? '' }}">
                                                                    </div>
                                                                    @error('category_background_image')
                                                                        <div class="error text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                    @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                        <img src="{{ asset($content->meta_value) }}"
                                                                            alt="{{ $content->meta_key }}"
                                                                            class="mt-2 rounded border"
                                                                            style="width: 100px; height: auto;">
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light fw-bold py-2">
                                            Category Heading Section
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                @foreach ($categoryPageContents as $key => $val)
                                                    @if ($val->meta_key == 'heading')
                                                        <div class="form-group col-lg-6">
                                                            <label class="form-label" for="heading_{{ $val->id }}">Heading</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="heading_{{ $val->id }}"
                                                                    name="heading[{{ $val->id }}]"
                                                                    value="{{ $val->meta_value ?? '' }}" />
                                                            </div>
                                                        </div>
                                                    @elseif($val->meta_key === 'description')
                                                        <div class="form-group col-lg-6">
                                                            <label class="form-label" for="description_{{ $val->id }}">Sub Heading</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="description_{{ $val->id }}"
                                                                    name="description[{{ $val->id }}]"
                                                                    value="{{ $val->meta_value ?? '' }}" />
                                                            </div>
                                                        </div>
                                                    @elseif($val->meta_key === 'search_placeholder_text')
                                                        <div class="form-group col-lg-6">
                                                            <label class="form-label" for="search_placeholder_{{ $val->id }}">Search Placeholder text</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="search_placeholder_{{ $val->id }}"
                                                                    name="search_placeholder_text[{{ $val->id }}]"
                                                                    value="{{ $val->meta_value ?? '' }}" />
                                                            </div>
                                                        </div>
                                                    @elseif($val->meta_key === 'main_heading')
                                                        <div class="form-group col-lg-6">
                                                            <label class="form-label" for="main_heading_{{ $val->id }}">Category Page Heading</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="main_heading_{{ $val->id }}"
                                                                    name="main_heading[{{ $val->id }}]"
                                                                    value="{{ $val->meta_value ?? '' }}" />
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Category Card Descriptions Section --}}
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-2 px-3">
                                            <div>
                                                <strong class="text-primary fs-15px">
                                                    <em class="icon ni ni-grid-alt me-1"></em> Category Block Descriptions (Shown on All Categories Page)
                                                </strong>
                                                <div class="text-muted small">
                                                    Add or edit the description shown directly in each category block on the frontend All Categories (/categories) page.
                                                </div>
                                            </div>
                                            <div class="mt-2 mt-md-0" style="min-width: 250px;">
                                                <input type="text" id="categoryFilterInput" class="form-control form-control-sm" placeholder="Search categories..." />
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            @if (isset($parentCategories) && $parentCategories->count() > 0)
                                                <div id="categoriesContainer">
                                                    @foreach ($parentCategories as $parent)
                                                        @php
                                                            $parentDesc = $parent->translations?->all_categories_description ?? '';
                                                        @endphp
                                                        <div class="parent-category-card card border mb-3" data-parent-name="{{ strtolower($parent->name) }}">
                                                            <div class="card-body p-3">
                                                                {{-- Parent's own description --}}
                                                                <div class="category-item-card p-2 bg-light rounded border" data-cat-name="{{ strtolower($parent->name) }}">
                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                        <label class="form-label mb-0 fw-bold small">
                                                                            <em class="icon ni ni-folder me-1 text-primary"></em> {{ $parent->name }} (Parent Category Description)
                                                                        </label>
                                                                        <button type="button" class="btn btn-xs btn-outline-primary btn-save-desc" data-cat-id="{{ $parent->id }}">
                                                                            <span class="btn-text">Quick Save</span>
                                                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                                        </button>
                                                                    </div>
                                                                    <textarea class="form-control form-control-sm cat-desc-textarea"
                                                                        name="all_categories_description[{{ $parent->id }}]"
                                                                        id="cat_desc_{{ $parent->id }}"
                                                                        rows="2"
                                                                        placeholder="Description for {{ $parent->name }} on All Categories page...">{{ $parentDesc }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div id="noCategoriesAlert" class="alert alert-warning d-none text-center py-2 mt-2">
                                                    No categories match your search.
                                                </div>
                                            @else
                                                <p class="text-muted">No categories available.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div class="form-group d-flex align-items-center gap-3">
                                        <button type="submit" class="addCategory btn btn-primary text-center btn-localio">
                                            <em class="icon ni ni-check me-1"></em><span>Update All Content</span>
                                        </button>
                                        <span class="text-muted small">Clicking "Update All Content" saves all page texts, banners, and category descriptions simultaneously.</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        $(document).ready(function() {
            // Real-time category search filter
            $('#categoryFilterInput').on('input', function() {
                var query = $(this).val().toLowerCase().trim();
                var anyVisible = false;

                $('.parent-category-card').each(function() {
                    var parentCard = $(this);
                    var parentName = parentCard.data('parent-name') || '';

                    if (query === '' || parentName.indexOf(query) !== -1) {
                        parentCard.show();
                        anyVisible = true;
                    } else {
                        parentCard.hide();
                    }
                });

                if (!anyVisible && query !== '') {
                    $('#noCategoriesAlert').removeClass('d-none');
                } else {
                    $('#noCategoriesAlert').addClass('d-none');
                }
            });

            // Single category description quick save via AJAX
            $('.btn-save-desc').on('click', function(e) {
                e.preventDefault();
                var btn = $(this);
                var catId = btn.data('cat-id');
                var textarea = $('#cat_desc_' + catId);
                var descVal = textarea.val();
                var langId = $('input[name="lang_id"]').val() || 1;

                var btnText = btn.find('.btn-text');
                var spinner = btn.find('.spinner-border');

                btnText.addClass('d-none');
                spinner.removeClass('d-none');
                btn.prop('disabled', true);

                $.ajax({
                    url: '{{ url("/admin-dashboard/category-all-categories-desc-update") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        category_id: catId,
                        lang_id: langId,
                        description: descVal
                    },
                    success: function(res) {
                        spinner.addClass('d-none');
                        btnText.html('<em class="icon ni ni-check"></em> Saved!').removeClass('d-none');
                        btn.removeClass('btn-outline-primary').addClass('btn-success');
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Category description updated successfully!');
                        }

                        setTimeout(function() {
                            btn.removeClass('btn-success').addClass('btn-outline-primary');
                            btnText.text('Quick Save');
                            btn.prop('disabled', false);
                        }, 2500);
                    },
                    error: function(err) {
                        spinner.addClass('d-none');
                        btnText.text('Error!').removeClass('d-none');
                        btn.removeClass('btn-outline-primary').addClass('btn-danger');
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Failed to save description.');
                        }

                        setTimeout(function() {
                            btn.removeClass('btn-danger').addClass('btn-outline-primary');
                            btnText.text('Quick Save');
                            btn.prop('disabled', false);
                        }, 2500);
                    }
                });
            });
        });
    </script>
@endsection
