@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head d-flex justify-content-between align-items-center">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Add Category Feature Content</h4>
            <p class="text-muted">Create custom top description and dynamic text sections (H2 & H3) for a specific Category + Feature combination.</p>
        </div>
        <a href="{{ route('admin.category-feature-content.index') }}" class="btn btn-outline-light bg-white text-dark">
            <em class="icon ni ni-arrow-left"></em> Back to List
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-icon mb-3">
            <em class="icon ni ni-cross-circle"></em> {{ session('error') }}
        </div>
    @endif

    <div class="card card-bordered">
        <div class="card-inner">
            <form action="{{ route('admin.category-feature-content.store') }}" method="POST" class="form-validate">
                @csrf
                <div class="row g-4">
                    <!-- Category Selection -->
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="form-label" for="category_id">Applicable Category <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <select name="category_id" id="category_id" class="form-select js-select2" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Feature Selection -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label" for="feature_id">Feature <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <select name="feature_id" id="feature_id" class="form-select js-select2" required>
                                    <option value="">Select Feature</option>
                                    @foreach($features as $feat)
                                        <option value="{{ $feat->id }}" {{ old('feature_id') == $feat->id ? 'selected' : '' }}>
                                            {{ $feat->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('feature_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Language Selection -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label" for="lang_id">Language <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <select name="lang_id" id="lang_id" class="form-select" required>
                                    @foreach($languages as $lang)
                                        <option value="{{ $lang->id }}" {{ (old('lang_id', $lang_id) == $lang->id) ? 'selected' : '' }}>
                                            {{ $lang->lang_code }} - {{ $lang->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('lang_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Top Description -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label" for="description">Top Description (Displayed below H1 headline)</label>
                            <div class="form-control-wrap">
                                <textarea name="description" id="description" class="form-control rich-editor" rows="5" placeholder="Enter introductory description for this category-feature page...">{{ old('description') }}</textarea>
                            </div>
                            <small class="text-muted d-block mt-1">If left blank, the standard category description will be displayed as fallback.</small>
                        </div>
                    </div>

                    <!-- Dynamic Content Sections (H2 & H3) -->
                    <div class="col-12 mt-4">
                        <div class="card card-bordered bg-light">
                            <div class="card-inner py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h5 class="title mb-0 text-primary"><em class="icon ni ni-file-text"></em> Dynamic Content Sections (H2 & H3 Sub-headlines)</h5>
                                        <small class="text-muted">Add rich article sections to appear below the products grid on this specific feature page.</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary" id="addTextSectionBtn" style="background:#002347; border-color:#002347;">
                                        <em class="icon ni ni-plus"></em> Add Section (H2)
                                    </button>
                                </div>
                                <hr class="my-3">
                                <div id="textSectionsContainer">
                                    <!-- Dynamic Sections Append Here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 mt-4">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg px-5" style="background:#F9633B; border-color:#F9633B;">
                                Save Content
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
    let activeEditors = {};

    function initCKEditor(textarea) {
        if (!textarea || textarea.classList.contains('ckeditor-initialized')) return;
        ClassicEditor
            .create(textarea, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
            })
            .then(editor => {
                textarea.classList.add('ckeditor-initialized');
                activeEditors[textarea.name] = editor;
                editor.model.document.on('change:data', () => {
                    textarea.value = editor.getData();
                });
            })
            .catch(error => {
                console.error(error);
            });
    }

    function initAllEditors() {
        document.querySelectorAll('textarea.rich-editor').forEach(el => {
            initCKEditor(el);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initAllEditors();

        let sectionCounter = 0;

        $('#addTextSectionBtn').on('click', function() {
            let sIdx = sectionCounter++;
            let html = `
            <div class="section-card border rounded p-3 mb-3 bg-white" data-section-index="${sIdx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">Section ${sIdx + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-section-btn">Delete Section</button>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Headline (H2)</label>
                        <input type="text" class="form-control" name="text_sections[${sIdx}][h2_title]" placeholder="e.g. Why choose shared hosting with noise cancellation?">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Headline Content (Rich Editor)</label>
                        <textarea class="form-control rich-editor" rows="4" name="text_sections[${sIdx}][h2_text]" placeholder="Enter main content for this H2 section..."></textarea>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-secondary mb-0">Sub-sections (H3 Sub-headlines)</label>
                            <button type="button" class="btn btn-xs btn-outline-primary add-sub-section-btn" data-section-index="${sIdx}">+ Add Sub-headline (H3)</button>
                        </div>
                        <div class="sub-sections-container ps-3 border-start" id="subSectionsContainer_${sIdx}">
                        </div>
                    </div>
                </div>
            </div>`;

            $('#textSectionsContainer').append(html);
            initAllEditors();
        });

        $(document).on('click', '.remove-section-btn', function() {
            $(this).closest('.section-card').remove();
        });

        $(document).on('click', '.add-sub-section-btn', function() {
            let sIdx = $(this).data('section-index');
            let container = $(`#subSectionsContainer_${sIdx}`);
            let subIdx = container.find('.sub-section-item').length;

            let subHtml = `
            <div class="sub-section-item border rounded p-2 mb-2 bg-light" data-sub-index="${subIdx}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-muted">Sub-headline (H3)</span>
                    <button type="button" class="btn btn-xs btn-outline-danger remove-sub-btn">Remove</button>
                </div>
                <input type="text" class="form-control form-control-sm mb-2" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_title]" placeholder="e.g. Key Features & Highlights">
                <textarea class="form-control rich-editor" rows="3" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_text]" placeholder="Enter sub-headline content..."></textarea>
            </div>`;
            container.append(subHtml);
            initAllEditors();
        });

        $(document).on('click', '.remove-sub-btn', function() {
            $(this).closest('.sub-section-item').remove();
        });
    });
</script>
@endpush
@endsection
