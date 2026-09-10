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
            <!-- Quick Fill from JSON Box -->
            <div class="card card-bordered bg-light mb-4" id="jsonPasteBox" style="border: 1.5px dashed #002347 !important; border-radius: 10px; background-color: #f8fafc !important;">
                <div class="card-inner py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-dim bg-primary" style="font-size: 13px;"><em class="icon ni ni-code"></em> Quick JSON Auto-Fill</span>
                            <span class="text-muted small">Paste JSON text below to auto-fill all headlines, descriptions, meta tags, and dynamic sections</span>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-secondary" id="btnToggleJsonCollapse">
                            <em class="icon ni ni-eye" id="toggleJsonIcon"></em> <span id="toggleJsonText">Hide</span>
                        </button>
                    </div>

                    <div id="jsonBodySection">
                        <div id="jsonAlertMessage" style="display: none;" class="mb-2"></div>
                        <div class="form-group mb-2">
                            <textarea id="jsonTextInput" class="form-control font-monospace" rows="7" style="font-size: 12.5px; background: #ffffff; line-height: 1.5;" placeholder='{\n  "page_title": "Best Web Hosting Services with Free Domain ([year])",\n  "description": "<p>Compare leading web hosting providers that include a free domain name...</p>",\n  "top_rated_listing_headline": "Top-Rated Web Hosting Providers",\n  "top_rated_listing_subtext": "<p>Verified user ratings and independent review scores.</p>",\n  "compare_alternatives_headline": "Compare Web Hosting Alternatives",\n  "compare_alternatives_subtext": "<p>See how providers compare side-by-side.</p>",\n  "community_says_headline": "What the Community Says",\n  "community_says_subtext": "<p>Real pros and cons from verified users.</p>",\n  "popular_comparisons_headline": "Popular Web Hosting Comparisons",\n  "popular_comparisons_subtext": "<p>Compare key specs and hosting tiers.</p>",\n  "explore_subcategories_headline": "Explore Related Categories",\n  "explore_subcategories_subtext": "<p>Browse specialized hosting types.</p>",\n  "meta_title": "Best Web Hosting with Free Domain ([year]) - Localio",\n  "meta_description": "Find top web hosting providers offering free domain registration.",\n  "text_sections": [\n    {\n      "h2_title": "Why Choose Hosting with a Free Domain?",\n      "h2_text": "<p>Saving on domain registration fees simplifies setup...</p>",\n      "sub_sections": [\n        {\n          "h3_title": "Domain Renewal Costs",\n          "h3_text": "<p>Ensure renewal rates remain affordable after the first year.</p>"\n        }\n      ]\n    }\n  ]\n}'></textarea>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-primary" id="btnApplyJson" style="background: #002347; border-color: #002347;">
                                <em class="icon ni ni-check-circle me-1"></em> Auto-Fill Form from JSON
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnLoadSampleJson">
                                <em class="icon ni ni-copy me-1"></em> Insert Sample JSON
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light text-muted" id="btnClearJson">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                                        <option value="{{ $cat->id }}" {{ (old('category_id', request('category_id', $selectedCategoryId ?? '')) == $cat->id) ? 'selected' : '' }}>
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
                                        <option value="{{ $feat->id }}" {{ (old('feature_id', request('feature_id', $selectedFeatureId ?? '')) == $feat->id) ? 'selected' : '' }}>
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

                    <!-- Page Title (H1) -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label fw-bold" for="page_title">Page Title (H1 Heading)</label>
                            <div class="form-control-wrap">
                                <input type="text" name="page_title" id="page_title" class="form-control" value="{{ old('page_title') }}" placeholder="e.g. Best Web Hosting Services for the U.S. ([year])">
                            </div>
                            <small class="text-muted d-block mt-1">Main H1 headline shown at the top of the feature page. Supports dynamic variables: <code>[year]</code>, <code>[country1]</code>, <code>[country2]</code>. Defaults to "[Category Name] with [Feature Name]" if left blank.</small>
                        </div>
                    </div>

                    <!-- Header Text / Description -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label fw-bold" for="description">Header Text (Intro Description)</label>
                            <div class="form-control-wrap">
                                <textarea name="description" id="description" class="form-control rich-editor" rows="4" placeholder="Enter introductory description for this category-feature page...">{{ old('description') }}</textarea>
                            </div>
                            <small class="text-muted d-block mt-1">Displayed below the H1 headline. If left blank, the standard category description will be displayed as fallback.</small>
                        </div>
                    </div>

                    <!-- Compare Top-Rated Section -->
                    <div class="col-12">
                        <div class="card card-bordered p-3 bg-light">
                            <h6 class="fw-bold text-primary mb-3">Compare Top-Rated Section</h6>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold" for="top_rated_listing_headline">Compare Top-Rated Headline (H2)</label>
                                <input type="text" name="top_rated_listing_headline" id="top_rated_listing_headline" class="form-control" value="{{ old('top_rated_listing_headline') }}" placeholder="e.g. Top-rated web hosting providers comparison">
                            </div>
                            <div class="form-group">
                                <label class="form-label fw-bold" for="top_rated_listing_subtext">Compare Top-Rated Subtext</label>
                                <textarea name="top_rated_listing_subtext" id="top_rated_listing_subtext" class="form-control rich-editor" rows="3" placeholder="Enter subtext displayed below the Compare Top-Rated headline...">{{ old('top_rated_listing_subtext') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Top 10 Section -->
                    <div class="col-12">
                        <div class="card card-bordered p-3 bg-light">
                            <h6 class="fw-bold text-primary mb-3">Top 10 Section</h6>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold" for="compare_alternatives_headline">Top 10 Headline (H2)</label>
                                <input type="text" name="compare_alternatives_headline" id="compare_alternatives_headline" class="form-control" value="{{ old('compare_alternatives_headline') }}" placeholder="e.g. Top 10 web hosting services compared">
                            </div>
                            <div class="form-group">
                                <label class="form-label fw-bold" for="compare_alternatives_subtext">Top 10 Subtext</label>
                                <textarea name="compare_alternatives_subtext" id="compare_alternatives_subtext" class="form-control rich-editor" rows="3" placeholder="Enter subtext displayed below the Top 10 headline...">{{ old('compare_alternatives_subtext') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pros and Cons Section -->
                    <div class="col-12">
                        <div class="card card-bordered p-3 bg-light">
                            <h6 class="fw-bold text-primary mb-3">Pros and Cons Section</h6>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold" for="community_says_headline">Pros and Cons Headline (H2)</label>
                                <input type="text" name="community_says_headline" id="community_says_headline" class="form-control" value="{{ old('community_says_headline') }}" placeholder="e.g. What the localio community says">
                            </div>
                            <div class="form-group">
                                <label class="form-label fw-bold" for="community_says_subtext">Pros and Cons Subtext</label>
                                <textarea name="community_says_subtext" id="community_says_subtext" class="form-control rich-editor" rows="3" placeholder="Enter subtext displayed below the Pros and Cons headline...">{{ old('community_says_subtext') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Explore Categories Section -->
                    <div class="col-12">
                        <div class="card card-bordered p-3 bg-light">
                            <h6 class="fw-bold text-primary mb-3">Explore Categories Section</h6>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold" for="explore_subcategories_headline">Explore Categories Headline (H2)</label>
                                <input type="text" name="explore_subcategories_headline" id="explore_subcategories_headline" class="form-control" value="{{ old('explore_subcategories_headline') }}" placeholder="e.g. Not sure? explore hosting categories">
                            </div>
                            <div class="form-group">
                                <label class="form-label fw-bold" for="explore_subcategories_subtext">Explore Categories Subtext</label>
                                <textarea name="explore_subcategories_subtext" id="explore_subcategories_subtext" class="form-control rich-editor" rows="3" placeholder="Enter subtext displayed below the Explore Categories headline...">{{ old('explore_subcategories_subtext') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Comparisons Section -->
                    <div class="col-12">
                        <div class="card card-bordered p-3 bg-light">
                            <h6 class="fw-bold text-primary mb-3">Popular Comparisons Section</h6>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold" for="popular_comparisons_headline">Popular Comparisons Headline (H2)</label>
                                <input type="text" name="popular_comparisons_headline" id="popular_comparisons_headline" class="form-control" value="{{ old('popular_comparisons_headline') }}" placeholder="e.g. Compare popular web hosting services">
                            </div>
                            <div class="form-group">
                                <label class="form-label fw-bold" for="popular_comparisons_subtext">Popular Comparisons Subtext</label>
                                <textarea name="popular_comparisons_subtext" id="popular_comparisons_subtext" class="form-control rich-editor" rows="3" placeholder="Enter subtext displayed below the Popular Comparisons headline...">{{ old('popular_comparisons_subtext') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Meta Tags -->
                    <div class="col-12">
                        <div class="card card-bordered p-3 bg-light">
                            <h6 class="fw-bold text-primary mb-3">SEO Meta Tags</h6>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold" for="meta_title">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title') }}" placeholder="Enter SEO meta title">
                            </div>
                            <div class="form-group">
                                <label class="form-label fw-bold" for="meta_description">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="3" placeholder="Enter SEO meta description">{{ old('meta_description') }}</textarea>
                            </div>
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

        // Toggle JSON box collapse
        $('#btnToggleJsonCollapse').on('click', function() {
            let body = $('#jsonBodySection');
            if (body.is(':visible')) {
                body.slideUp(200);
                $('#toggleJsonText').text('Show');
                $('#toggleJsonIcon').removeClass('ni-eye').addClass('ni-eye-off');
            } else {
                body.slideDown(200);
                $('#toggleJsonText').text('Hide');
                $('#toggleJsonIcon').removeClass('ni-eye-off').addClass('ni-eye');
            }
        });

        // Clear JSON button
        $('#btnClearJson').on('click', function() {
            $('#jsonTextInput').val('');
            $('#jsonAlertMessage').slideUp(200);
        });

        // Load sample JSON button
        $('#btnLoadSampleJson').on('click', function() {
            let sample = {
                "page_title": "Best Web Hosting with Free Domain ([year])",
                "description": "<p>Compare top web hosting services offering free domain names on annual signup plans.</p>",
                "top_rated_listing_headline": "Top-Rated Web Hosting Services",
                "top_rated_listing_subtext": "<p>Verified provider ratings compiled by our research team.</p>",
                "compare_alternatives_headline": "Compare Alternatives",
                "compare_alternatives_subtext": "<p>Detailed comparison of pricing, storage, and customer support.</p>",
                "community_says_headline": "What the Community Says",
                "community_says_subtext": "<p>Most mentioned strengths and weaknesses reported by real users.</p>",
                "explore_subcategories_headline": "Explore Hosting Categories",
                "explore_subcategories_subtext": "<p>Find tailored solutions across specific categories.</p>",
                "popular_comparisons_headline": "Popular Hosting Comparisons",
                "popular_comparisons_subtext": "<p>Side-by-side benchmarks of leading providers.</p>",
                "meta_title": "Best Web Hosting with Free Domain ([year]) - Localio",
                "meta_description": "Compare top web hosts that bundle a free domain. Read user ratings, pros & cons, and pricing.",
                "text_sections": [
                    {
                        "h2_title": "Why Choose Hosting with a Free Domain?",
                        "h2_text": "<p>Bundling a free domain reduces initial setup friction and expense.</p>",
                        "sub_sections": [
                            {
                                "h3_title": "First-year Savings",
                                "h3_text": "<p>Get your online presence up and running without immediate registrar fees.</p>"
                            }
                        ]
                    }
                ]
            };
            $('#jsonTextInput').val(JSON.stringify(sample, null, 2));
            showJsonAlert('Sample JSON inserted. Click "Auto-Fill Form from JSON" to test populating all fields.', 'info');
        });

        function showJsonAlert(msg, type) {
            let icon = type === 'success' ? 'ni-check-circle' : (type === 'danger' ? 'ni-cross-circle' : (type === 'info' ? 'ni-info' : 'ni-alert-circle'));
            $('#jsonAlertMessage').html(`
                <div class="alert alert-${type} alert-icon alert-dismissible fade show p-2 mb-2" style="font-size: 13px;">
                    <em class="icon ni ${icon}"></em>
                    <span>${msg}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `).slideDown(200);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        // Apply JSON to Form
        $('#btnApplyJson').on('click', function() {
            let raw = $('#jsonTextInput').val().trim();
            if (!raw) {
                showJsonAlert('Please paste JSON text into the box above before applying.', 'warning');
                return;
            }

            let data;
            try {
                data = JSON.parse(raw);
            } catch (e) {
                showJsonAlert('Invalid JSON Syntax: ' + e.message, 'danger');
                return;
            }

            if (Array.isArray(data)) {
                if (data.length === 0) {
                    showJsonAlert('JSON array is empty.', 'warning');
                    return;
                }
                data = data[0];
            }

            if (typeof data !== 'object' || data === null) {
                showJsonAlert('Invalid format: Expected a JSON object with key-value properties.', 'danger');
                return;
            }

            let filledCount = 0;

            function setVal(selector, val) {
                if (val !== undefined && val !== null && val !== '') {
                    $(selector).val(val);
                    filledCount++;
                }
            }

            function setEditorVal(fieldName, val) {
                if (val !== undefined && val !== null && val !== '') {
                    let $textarea = $(`textarea[name="${fieldName}"]`);
                    if ($textarea.length) {
                        $textarea.val(val);
                    }
                    if (activeEditors && activeEditors[fieldName]) {
                        activeEditors[fieldName].setData(val);
                    }
                    filledCount++;
                }
            }

            // Category, Feature, Language (if provided)
            if (data.category_id) {
                $('#category_id').val(data.category_id).trigger('change');
                filledCount++;
            }
            if (data.feature_id) {
                $('#feature_id').val(data.feature_id).trigger('change');
                filledCount++;
            }
            if (data.lang_id) {
                $('#lang_id').val(data.lang_id).trigger('change');
                filledCount++;
            }

            // Page Title & Description
            setVal('#page_title', data.page_title || data.title);
            setEditorVal('description', data.description || data.desc);

            // Headlines & Subtexts
            setVal('#top_rated_listing_headline', data.top_rated_listing_headline);
            setEditorVal('top_rated_listing_subtext', data.top_rated_listing_subtext);

            setVal('#compare_alternatives_headline', data.compare_alternatives_headline);
            setEditorVal('compare_alternatives_subtext', data.compare_alternatives_subtext);

            setVal('#community_says_headline', data.community_says_headline);
            setEditorVal('community_says_subtext', data.community_says_subtext);

            setVal('#explore_subcategories_headline', data.explore_subcategories_headline);
            setEditorVal('explore_subcategories_subtext', data.explore_subcategories_subtext);

            setVal('#popular_comparisons_headline', data.popular_comparisons_headline);
            setEditorVal('popular_comparisons_subtext', data.popular_comparisons_subtext);

            // SEO Meta
            setVal('#meta_title', data.meta_title || data.metaTitle);
            setVal('#meta_description', data.meta_description || data.metaDescription);

            // Dynamic Text Sections
            let sections = data.text_sections;
            if (typeof sections === 'string') {
                try { sections = JSON.parse(sections); } catch(e){}
            }

            if (Array.isArray(sections) && sections.length > 0) {
                $('#textSectionsContainer').empty();
                sectionCounter = 0;

                sections.forEach((sec) => {
                    let sIdx = sectionCounter++;
                    let secHtml = `
                    <div class="section-card border rounded p-3 mb-3 bg-white" data-section-index="${sIdx}">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark">Section ${sIdx + 1}</h6>
                            <button type="button" class="btn btn-sm btn-danger remove-section-btn">Delete Section</button>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Headline (H2)</label>
                                <input type="text" class="form-control" name="text_sections[${sIdx}][h2_title]" value="${escapeHtml(sec.h2_title || '')}" placeholder="e.g. Why choose shared hosting with noise cancellation?">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Headline Content (Rich Editor)</label>
                                <textarea class="form-control rich-editor" rows="4" name="text_sections[${sIdx}][h2_text]" placeholder="Enter main content for this H2 section...">${sec.h2_text || ''}</textarea>
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

                    $('#textSectionsContainer').append(secHtml);

                    if (Array.isArray(sec.sub_sections)) {
                        let container = $(`#subSectionsContainer_${sIdx}`);
                        sec.sub_sections.forEach((sub, subIdx) => {
                            let subHtml = `
                            <div class="sub-section-item border rounded p-2 mb-2 bg-light" data-sub-index="${subIdx}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-muted">Sub-headline (H3)</span>
                                    <button type="button" class="btn btn-xs btn-outline-danger remove-sub-btn">Remove</button>
                                </div>
                                <input type="text" class="form-control form-control-sm mb-2" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_title]" value="${escapeHtml(sub.h3_title || '')}" placeholder="e.g. Key Features & Highlights">
                                <textarea class="form-control rich-editor" rows="3" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_text]" placeholder="Enter sub-headline content...">${sub.h3_text || ''}</textarea>
                            </div>`;
                            container.append(subHtml);
                        });
                    }
                });

                initAllEditors();
                filledCount += sections.length;
            }

            showJsonAlert(`Successfully populated ${filledCount} field(s) & dynamic section(s) from JSON! Please review below and click "Save Content".`, 'success');
        });

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
