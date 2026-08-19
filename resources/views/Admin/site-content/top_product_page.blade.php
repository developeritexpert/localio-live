@extends('admin_layout.master')
@section('content')
    <style>
        .ck-editor__editable_inline {
            min-height: 150px;
        }
        .sub-section-item .ck-editor__editable_inline {
            min-height: 100px;
        }
    </style>

    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between align-items-center mb-4">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Top Rated Page Content</h4>
                <p class="text-muted">Manage header banner, rich text sections (H2 & H3), and FAQs on the Top-Rated page.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ url('admin-dashboard/product-page-update') }}" id="topProductForm" class="form-validate" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="lang_id" value="{{ $lang_id ?? 1 }}">

                    {{-- 1. Header Images & Files (if any) --}}
                    @if(isset($productFiles) && $productFiles->isNotEmpty())
                    <div class="card border mb-4">
                        <div class="card-header bg-light">
                            <h6 class="m-0 fw-bold text-primary">Header Banner Images</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($productFiles as $content)
                                    @if ($content->meta_key == 'banner_image')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="image">Header Image</label>
                                                <input type="file" class="form-control" name="top_pro_banner_image[{{ $content->id }}]">
                                                @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                    <img src="{{ asset($content->meta_value) }}" class="mt-2 rounded" style="width: 100px; height: auto;">
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($content->meta_key == 'banner_bg_image')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="image">Background Image</label>
                                                <input type="file" class="form-control" name="top_pro_banner_bg_image[{{ $content->id }}]">
                                                @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                    <img src="{{ asset($content->meta_value) }}" class="mt-2 rounded" style="width: 100px; height: auto;">
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 2. Basic Header Text Contents --}}
                    @if(isset($topProductContents) && $topProductContents->where('type', '!=', 'json')->isNotEmpty())
                    <div class="card border mb-4">
                        <div class="card-header bg-light">
                            <h6 class="m-0 fw-bold text-primary">Header Texts & Labels</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($topProductContents as $val)
                                    @if(in_array($val->meta_key, ['header_title', 'header_sub_title', 'header_bottom_text', 'learn_more']))
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label text-capitalize">{{ str_replace('_', ' ', $val->meta_key) }}</label>
                                            <input type="text" class="form-control" name="{{ $val->meta_key }}[{{ $val->id }}]" value="{{ $val->meta_value ?? '' }}">
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 3. Dynamic Rich Text Sections Repeater --}}
                    <div class="card border mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="m-0 fw-bold text-primary">Dynamic Content Sections (H2 Headlines & H3 Sub-headlines)</h6>
                                <small class="text-muted">Add customizable rich text sections with an H2 headline, rich editor, and optional H3 sub-headlines with rich editor.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="addTextSectionBtn">
                                <em class="icon ni ni-plus"></em> + Add Section (H2)
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="textSectionsContainer">
                                @if(isset($textSections) && is_array($textSections))
                                    @foreach($textSections as $sIndex => $sec)
                                    <div class="section-card border rounded p-3 mb-3 bg-white" data-section-index="{{ $sIndex }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <h6 class="fw-bold mb-0 text-dark">Section {{ $sIndex + 1 }}</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-section-btn">Delete Section</button>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Headline (H2)</label>
                                                <input type="text" class="form-control" name="text_sections[{{ $sIndex }}][h2_title]" value="{{ $sec['h2_title'] ?? '' }}" placeholder="e.g. How Localio ratings work">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Headline Content (Rich Editor)</label>
                                                <textarea class="form-control rich-editor" rows="4" name="text_sections[{{ $sIndex }}][h2_text]" placeholder="Enter main content for this H2 section...">{{ $sec['h2_text'] ?? '' }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold text-secondary mb-0">Sub-sections (H3 Sub-headlines)</label>
                                                    <button type="button" class="btn btn-xs btn-outline-primary add-sub-section-btn" data-section-index="{{ $sIndex }}">+ Add Sub-headline (H3)</button>
                                                </div>
                                                <div class="sub-sections-container ps-3 border-start">
                                                    @if(isset($sec['sub_sections']) && is_array($sec['sub_sections']))
                                                        @foreach($sec['sub_sections'] as $subIndex => $sub)
                                                        <div class="sub-section-item border rounded p-2 mb-2 bg-light" data-sub-index="{{ $subIndex }}">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="small fw-bold text-muted">Sub-headline (H3)</span>
                                                                <button type="button" class="btn btn-xs btn-outline-danger remove-sub-btn">Remove</button>
                                                            </div>
                                                            <input type="text" class="form-control form-control-sm mb-2" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_title]" value="{{ $sub['h3_title'] ?? '' }}" placeholder="e.g. Why are these listings considered top rated?">
                                                            <textarea class="form-control rich-editor" rows="3" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_text]" placeholder="Enter sub-headline content...">{{ $sub['h3_text'] ?? '' }}</textarea>
                                                        </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 4. Dynamic Top-Rated FAQs Repeater --}}
                    <div class="card border mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="m-0 fw-bold text-primary">Top Rated Page FAQs</h6>
                                <small class="text-muted">Add frequently asked questions specific to the top-rated rankings page.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="addFaqBtn">
                                <em class="icon ni ni-plus"></em> + Add FAQ
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="faqsContainer">
                                @if(isset($faqs) && is_array($faqs))
                                    @foreach($faqs as $fIndex => $faq)
                                    <div class="faq-item-card border rounded p-3 mb-3 bg-white" data-faq-index="{{ $fIndex }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <h6 class="fw-bold mb-0 text-dark">FAQ #{{ $fIndex + 1 }}</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-faq-btn">Delete</button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Question</label>
                                                <input type="text" class="form-control" name="top_faqs[{{ $fIndex }}][question]" value="{{ $faq['question'] ?? '' }}" placeholder="Enter FAQ question...">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Answer (Rich Editor)</label>
                                                <textarea class="form-control rich-editor" rows="3" name="top_faqs[{{ $fIndex }}][answer]" placeholder="Enter FAQ answer...">{{ $faq['answer'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span>Update Content</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function initCKEditorForElement(element) {
        if (typeof ClassicEditor === 'undefined' || element.classList.contains('ckeditor-initialized')) return;
        ClassicEditor
            .create(element, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
            })
            .then(editor => {
                element.classList.add('ckeditor-initialized');
                element.ckeditorInstance = editor;
                editor.model.document.on('change:data', () => {
                    element.value = editor.getData();
                });
            })
            .catch(error => {
                console.error("CKEditor Init Error:", error);
            });
    }

    function initAllCKEditors() {
        document.querySelectorAll('textarea.rich-editor').forEach(el => {
            if (!el.classList.contains('ckeditor-initialized')) {
                initCKEditorForElement(el);
            }
        });
    }

    $(document).ready(function() {
        initAllCKEditors();

        let sectionCounter = {{ isset($textSections) ? count($textSections) : 0 }};
        let faqCounter = {{ isset($faqs) ? count($faqs) : 0 }};

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
                        <input type="text" class="form-control" name="text_sections[${sIdx}][h2_title]" placeholder="e.g. How Localio ratings work">
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
                        <div class="sub-sections-container ps-3 border-start"></div>
                    </div>
                </div>
            </div>`;
            $('#textSectionsContainer').append(html);
            initAllCKEditors();
        });

        $(document).on('click', '.remove-section-btn', function() {
            let card = $(this).closest('.section-card');
            card.find('textarea.rich-editor').each(function() {
                if (this.ckeditorInstance) {
                    this.ckeditorInstance.destroy();
                }
            });
            card.remove();
        });

        $(document).on('click', '.add-sub-section-btn', function() {
            let sIdx = $(this).data('section-index');
            let container = $(this).closest('.col-12').find('.sub-sections-container');
            let subIdx = container.children('.sub-section-item').length;
            let subHtml = `
            <div class="sub-section-item border rounded p-2 mb-2 bg-light" data-sub-index="${subIdx}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-muted">Sub-headline (H3)</span>
                    <button type="button" class="btn btn-xs btn-outline-danger remove-sub-btn">Remove</button>
                </div>
                <input type="text" class="form-control form-control-sm mb-2" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_title]" placeholder="e.g. Why are these listings considered top rated?">
                <textarea class="form-control rich-editor" rows="3" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_text]" placeholder="Enter sub-headline content..."></textarea>
            </div>`;
            container.append(subHtml);
            initAllCKEditors();
        });

        $(document).on('click', '.remove-sub-btn', function() {
            let item = $(this).closest('.sub-section-item');
            item.find('textarea.rich-editor').each(function() {
                if (this.ckeditorInstance) {
                    this.ckeditorInstance.destroy();
                }
            });
            item.remove();
        });

        $('#addFaqBtn').on('click', function() {
            let fIdx = faqCounter++;
            let html = `
            <div class="faq-item-card border rounded p-3 mb-3 bg-white" data-faq-index="${fIdx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">FAQ #${fIdx + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-faq-btn">Delete</button>
                </div>
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label fw-bold">Question</label>
                        <input type="text" class="form-control" name="top_faqs[${fIdx}][question]" placeholder="Enter FAQ question...">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Answer (Rich Editor)</label>
                        <textarea class="form-control rich-editor" rows="3" name="top_faqs[${fIdx}][answer]" placeholder="Enter FAQ answer..."></textarea>
                    </div>
                </div>
            </div>`;
            $('#faqsContainer').append(html);
            initAllCKEditors();
        });

        $(document).on('click', '.remove-faq-btn', function() {
            let item = $(this).closest('.faq-item-card');
            item.find('textarea.rich-editor').each(function() {
                if (this.ckeditorInstance) {
                    this.ckeditorInstance.destroy();
                }
            });
            item.remove();
        });

        // Ensure CKEditor data syncs on form submit
        $('#topProductForm').on('submit', function() {
            document.querySelectorAll('textarea.rich-editor').forEach(el => {
                if (el.ckeditorInstance) {
                    el.value = el.ckeditorInstance.getData();
                }
            });
        });
    });
    </script>
@endsection
