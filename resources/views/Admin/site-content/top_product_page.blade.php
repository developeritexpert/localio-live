@extends('admin_layout.master')
@section('content')
    <style>
        .ck-editor__editable_inline {
            min-height: 140px;
        }
        .sub-section-item .ck-editor__editable_inline {
            min-height: 90px;
        }
        .btn-localio {
            background-color: #ff5722 !important;
            border-color: #ff5722 !important;
            color: #ffffff !important;
        }
        .btn-localio:hover {
            background-color: #e64a19 !important;
            border-color: #e64a19 !important;
            color: #ffffff !important;
        }
        .position-badge {
            font-weight: 700;
            color: #64748b;
            font-size: 13px;
        }
        .section-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .section-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .category-pill-tag {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }
        .category-pill-tag:hover {
            background-color: #e2e8f0;
        }
        .category-pill-tag .btn-del-cat {
            border: none;
            background: transparent;
            color: #ef4444;
            cursor: pointer;
            padding: 0;
            font-size: 14px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
        }
        .category-pill-tag .btn-del-cat:hover {
            color: #b91c1c;
        }
    </style>

    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between align-items-center mb-4">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Top Rated Page Content</h4>
                <p class="text-muted">Manage header banner, rich text sections (H2 & H3), and category-based FAQs on the Top-Rated page.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ url('admin-dashboard/product-page-update') }}" id="topProductForm" class="form-validate" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="lang_id" value="{{ $lang_id ?? 1 }}">

                        {{-- 1. Main Top Rated Page Texts (/top-rated) --}}
            <div class="card card-bordered mb-4">
                <div class="card-header bg-light">
                    <h6 class="m-0 fw-bold text-primary"><i class="fa fa-font me-1"></i> Main Top Rated Page Texts (/top-rated)</h6>
                    <small class="text-muted">These fields control the main titles, sub-headlines, and section texts on the primary Top-Rated page.</small>
                </div>
                <div class="card-inner">
                    <div class="row g-4">
                        {{-- Top Rated Main Headline (H1) --}}
                        @php 
                            $hlObj = isset($topProductContents) ? $topProductContents->where('meta_key', 'top_rated_headline')->where('type', '!=', 'json')->first() : null; 
                            $hlId = $hlObj ? $hlObj->id : 'new';
                            $hlVal = $hlObj ? $hlObj->meta_value : '';
                        @endphp
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold">Top Rated Main Headline (H1)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="top_rated_headline[{{ $hlId }}]" value="{{ $hlVal }}" placeholder="Top rated on Localio">
                                </div>
                                <small class="text-muted">Appears as the primary H1 page title at the top of <code>/top-rated</code> (Default: <em>Top rated on Localio</em>).</small>
                            </div>
                        </div>

                        {{-- Top Rated Sub-headline (Rich Text Editor) --}}
                        @php 
                            $subObj = isset($topProductContents) ? $topProductContents->where('meta_key', 'top_rated_subheadline')->where('type', '!=', 'json')->first() : null; 
                            $subId = $subObj ? $subObj->id : 'new';
                            $subVal = $subObj ? $subObj->meta_value : '';
                        @endphp
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold">Top Rated Sub-headline (Rich Text Editor)</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" rows="3" name="top_rated_subheadline[{{ $subId }}]" placeholder="Learn more from our team about software pricing, features and benefits in our Buyers Guide">{!! $subVal !!}</textarea>
                                </div>
                                <small class="text-muted">Appears directly below the "Last updated" date in the header block. Supports formatting, buyer guide links, and bold text.</small>
                            </div>
                        </div>

                        {{-- Top Rated Listing Section Headline (H2) --}}
                        @php 
                            $listHlObj = isset($topProductContents) ? $topProductContents->where('meta_key', 'top_rated_listing_headline')->where('type', '!=', 'json')->first() : null; 
                            $listHlId = $listHlObj ? $listHlObj->id : 'new';
                            $listHlVal = $listHlObj ? $listHlObj->meta_value : '';
                        @endphp
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold">Listings Section Headline (H2)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="top_rated_listing_headline[{{ $listHlId }}]" value="{{ $listHlVal }}" placeholder="Top-rated listings">
                                </div>
                                <small class="text-muted">Appears as the H2 headline on top of the product listings grid on <code>/top-rated</code> (Default: <em>Top-rated listings</em>).</small>
                            </div>
                        </div>

                        {{-- Explore Categories Section --}}
                        @php 
                            $catHlObj = isset($topProductContents) ? $topProductContents->where('meta_key', 'explore_categories_headline')->where('type', '!=', 'json')->first() : null; 
                            $catHlId = $catHlObj ? $catHlObj->id : 'new';
                            $catHlVal = $catHlObj ? $catHlObj->meta_value : '';

                            $catSubObj = isset($topProductContents) ? $topProductContents->where('meta_key', 'explore_categories_subheadline')->where('type', '!=', 'json')->first() : null; 
                            $catSubId = $catSubObj ? $catSubObj->id : 'new';
                            $catSubVal = $catSubObj ? $catSubObj->meta_value : '';
                        @endphp
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">Explore Categories Headline</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="explore_categories_headline[{{ $catHlId }}]" value="{{ $catHlVal }}" placeholder="Explore top-rated categories">
                                </div>
                                <small class="text-muted">Section title for category grid on <code>/top-rated</code> (Default: <em>Explore top-rated categories</em>).</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">Explore Categories Sub-headline</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="explore_categories_subheadline[{{ $catSubId }}]" value="{{ $catSubVal }}" placeholder="Discover top-rated software, services, and solutions across our most popular categories.">
                                </div>
                                <small class="text-muted">Subtitle for category grid on <code>/top-rated</code>.</small>
                            </div>
                        </div>

                        {{-- Main Page FAQ Section --}}
                        @php 
                            $faqHlObj = isset($topProductContents) ? $topProductContents->where('meta_key', 'faq_section_headline')->where('type', '!=', 'json')->first() : null; 
                            $faqHlId = $faqHlObj ? $faqHlObj->id : 'new';
                            $faqHlVal = $faqHlObj ? $faqHlObj->meta_value : '';

                            $faqSubObj = isset($topProductContents) ? $topProductContents->where('meta_key', 'faq_section_subheadline')->where('type', '!=', 'json')->first() : null; 
                            $faqSubId = $faqSubObj ? $faqSubObj->id : 'new';
                            $faqSubVal = $faqSubObj ? $faqSubObj->meta_value : '';
                        @endphp
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">FAQ Section Headline</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="faq_section_headline[{{ $faqHlId }}]" value="{{ $faqHlVal }}" placeholder="Frequently asked questions">
                                </div>
                                <small class="text-muted">Section title for FAQs block on <code>/top-rated</code> (Default: <em>Frequently asked questions</em>).</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">FAQ Section Sub-headline</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" name="faq_section_subheadline[{{ $faqSubId }}]" placeholder="Everything you need to know about Localio top-rated rankings and community reviews.">{!! $faqSubVal !!}</textarea>
                                </div>
                                <small class="text-muted">Subtitle for FAQs block on <code>/top-rated</code>.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Top Rated Dedicated FAQs Page Texts (/top-rated/faqs) --}}
            <div class="card card-bordered mb-4">
                <div class="card-header bg-light">
                    <h6 class="m-0 fw-bold text-primary"><i class="fa fa-question-circle me-1"></i> Top Rated FAQs Page Texts (/top-rated/faqs)</h6>
                    <small class="text-muted">These fields appear on the dedicated Top-Rated FAQs & Knowledge page at <code>/top-rated/faqs</code>.</small>
                </div>
                <div class="card-inner">
                    <div class="row g-3">
                        @php
                            $faqKeys = [
                                'faq_page_main_headline' => ['label' => 'Main Page Title (H1)', 'placeholder' => 'Top rated on Localio FAQs', 'desc' => 'Hero title at top of /top-rated/faqs'],
                                'faq_page_main_subheadline' => ['label' => 'Main Page Sub-title', 'placeholder' => 'Frequently asked questions and answers...', 'desc' => 'Hero subtitle on /top-rated/faqs'],
                                'faq_page_about_headline' => ['label' => 'About Section Headline', 'placeholder' => 'About Top-Rated Rankings', 'desc' => 'About card headline on /top-rated/faqs'],
                                'faq_page_about_subheadline' => ['label' => 'About Section Description', 'placeholder' => 'Learn more about how Localio calculates...', 'desc' => 'About card description on /top-rated/faqs'],
                                'faq_page_search_heading' => ['label' => 'Search Box Heading', 'placeholder' => 'Search Top-Rated FAQs', 'desc' => 'Search bar heading on /top-rated/faqs'],
                                'faq_page_list_headline' => ['label' => 'FAQs List Headline', 'placeholder' => 'Frequently asked questions', 'desc' => 'Headline above the accordion list on /top-rated/faqs'],
                                'faq_page_banner_headline' => ['label' => 'Bottom Banner Headline', 'placeholder' => 'Still have a question...', 'desc' => 'Call-to-action banner heading at the bottom of /top-rated/faqs'],
                                'faq_page_banner_subheadline' => ['label' => 'Bottom Banner Sub-headline', 'placeholder' => 'Ask the Localio community', 'desc' => 'Call-to-action banner subtitle on /top-rated/faqs'],
                                'faq_page_banner_btn_text' => ['label' => 'Bottom Banner Button Text', 'placeholder' => 'Ask a question', 'desc' => 'Button label on bottom banner of /top-rated/faqs']
                            ];
                        @endphp
                        @foreach($faqKeys as $key => $info)
                            @php 
                                $valObj = isset($topProductContents) ? $topProductContents->where('meta_key', $key)->where('type', '!=', 'json')->first() : null; 
                                $id = $valObj ? $valObj->id : 'new';
                                $val = $valObj ? $valObj->meta_value : '';
                            @endphp
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">{{ $info['label'] }}</label>
                                    <div class="form-control-wrap">
                                        @if(in_array($key, ['faq_page_main_subheadline', 'faq_page_about_subheadline']))
                                            <textarea class="form-control rich-editor" name="{{ $key }}[{{ $id }}]" placeholder="{{ $info['placeholder'] }}">{!! $val !!}</textarea>
                                        @else
                                            <input type="text" class="form-control" name="{{ $key }}[{{ $id }}]" value="{{ $val }}" placeholder="{{ $info['placeholder'] }}">
                                        @endif
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $info['desc'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. Dynamic Rich Text Sections Repeater --}}
            <div class="card card-bordered mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="m-0 fw-bold text-primary"><i class="fa fa-file-text-o me-1"></i> Dynamic Content Sections (H2 & H3 Sub-headlines)</h6>
                        <small class="text-muted">Add customizable rich text sections with an H2 headline and optional nested H3 sub-headlines.</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="addTextSectionBtn">
                        <em class="icon ni ni-plus"></em> <span>Add Section (H2)</span>
                    </button>
                </div>
                <div class="card-inner">
                    <div id="textSectionsContainer">
                        @if(isset($textSections) && is_array($textSections))
                            @foreach($textSections as $sIndex => $sec)
                            <div class="section-card p-3 mb-3" data-section-index="{{ $sIndex }}">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary px-2 py-1" style="font-size: 12px;">Section #{{ $sIndex + 1 }}</span>
                                        <span class="fw-bold text-dark small">{{ $sec['h2_title'] ?? 'New Section' }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger remove-section-btn py-1 px-2" title="Delete Section">
                                        <em class="icon ni ni-trash"></em> <span>Delete</span>
                                    </button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold mb-1">Headline (H2)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" name="text_sections[{{ $sIndex }}][h2_title]" value="{{ $sec['h2_title'] ?? '' }}" placeholder="e.g. How Localio ratings work">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold mb-1">Headline Content (Rich Editor)</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control rich-editor" rows="4" name="text_sections[{{ $sIndex }}][h2_text]" placeholder="Enter main content for this H2 section...">{!! $sec['h2_text'] ?? '' !!}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label fw-bold text-secondary mb-0">Sub-sections (H3 Sub-headlines)</label>
                                            <button type="button" class="btn btn-xs btn-primary add-sub-section-btn" data-section-index="{{ $sIndex }}"><em class="icon ni ni-plus"></em> Add Sub-headline (H3)</button>
                                        </div>
                                        <div class="sub-sections-container ps-3 border-start">
                                            @if(isset($sec['sub_sections']) && is_array($sec['sub_sections']))
                                                @foreach($sec['sub_sections'] as $subIndex => $sub)
                                                <div class="sub-section-item border rounded p-3 mb-2 bg-light" data-sub-index="{{ $subIndex }}">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="small fw-bold text-muted">Sub-headline (H3)</span>
                                                        <button type="button" class="btn btn-xs btn-danger remove-sub-btn"><em class="icon ni ni-cross"></em> Remove</button>
                                                    </div>
                                                    <div class="form-group mb-2">
                                                        <input type="text" class="form-control form-control-sm" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_title]" value="{{ $sub['h3_title'] ?? '' }}" placeholder="e.g. Why are these listings considered top rated?">
                                                    </div>
                                                    <div class="form-group mb-2">
                                                        <textarea class="form-control rich-editor" rows="3" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_text]" placeholder="Enter sub-headline content...">{!! $sub['h3_text'] ?? '' !!}</textarea>
                                                    </div>
                                                    
                                                    <div class="row mt-2">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="form-label fw-bold">Image</label>
                                                                <div class="form-control-wrap">
                                                                    <div class="form-file">
                                                                        <input type="file" class="form-file-input" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_image]" accept="image/*">
                                                                        <label class="form-file-label" for="customFile">Choose file</label>
                                                                    </div>
                                                                    @if(isset($sub['h3_image']) && !empty($sub['h3_image']))
                                                                        <div class="mt-2">
                                                                            <img src="{{ asset($sub['h3_image']) }}" alt="Sub-section Image" style="max-height: 50px;">
                                                                            <input type="hidden" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_image_existing]" value="{{ $sub['h3_image'] }}">
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="form-label fw-bold">Image Position</label>
                                                                <div class="form-control-wrap">
                                                                    <select class="form-select" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_image_position]">
                                                                        <option value="Left" {{ (isset($sub['h3_image_position']) && $sub['h3_image_position'] == 'Left') ? 'selected' : '' }}>Left</option>
                                                                        <option value="Right" {{ (isset($sub['h3_image_position']) && $sub['h3_image_position'] == 'Right') ? 'selected' : '' }}>Right</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="form-label fw-bold">Image Width (px or %)</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control form-control-sm" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_image_size]" value="{{ $sub['h3_image_size'] ?? '50px' }}" placeholder="e.g. 50px">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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

            {{-- 4. Manage FAQ Categories Card --}}
            <div class="card card-bordered mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><em class="icon ni ni-tags me-1"></em> Top Rated FAQ Categories</h5>
                    <span class="badge bg-primary" id="faqCategoryCountBadge">0 Categories</span>
                </div>
                <div class="card-inner">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-8 col-sm-12">
                            <label class="form-label fw-bold" for="newCategoryNameInput">New Category Name</label>
                            <input type="text" class="form-control" id="newCategoryNameInput" placeholder="e.g. Rankings & Calculation, Reviews & Community, General">
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <button type="button" class="btn btn-primary btn-localio w-100" id="addCategoryBtn">
                                <em class="icon ni ni-plus"></em> <span>Add Category</span>
                            </button>
                        </div>
                    </div>

                    <div id="faqCategoriesContainer" class="d-flex flex-wrap gap-2 pt-2 border-top">
                        <!-- Category pills dynamically rendered -->
                    </div>
                </div>
            </div>

            {{-- 5. Add / Edit FAQ Form (Matching Business FAQ Page) --}}
            <div class="card card-bordered mb-4" id="faqFormCard">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0" id="faqFormTitle">Add New FAQ</h5>
                </div>
                <div class="card-inner">
                    <div class="row g-gs">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="faqCategorySelect">FAQ Category (Optional)</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="faqCategorySelect">
                                        <option value="General">-- General / Uncategorized --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="faqQuestionInput">Question</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="faqQuestionInput" placeholder="Enter FAQ question">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="faqAnswerInput">Answer</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="faqAnswerInput" rows="4" placeholder="Enter FAQ answer"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-primary btn-localio" id="saveFaqBtn">
                                    <em class="icon ni ni-check"></em>
                                    <span id="saveFaqBtnText">Save FAQ</span>
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancelFaqEditBtn" style="display: none;">
                                    <em class="icon ni ni-cross"></em>
                                    <span>Cancel</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. FAQ List Table (Matching Business FAQ Page) --}}
            <div class="card card-bordered mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="card-title mb-0">Top Rated FAQs</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border me-2" id="faqCountBadge">0 FAQ(s)</span>
                        <button type="button" class="btn btn-sm btn-secondary" id="exportFaqJsonBtn" title="Export current FAQs as JSON">
                            <em class="icon ni ni-download"></em> <span>Export JSON</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#importFaqJsonModal">
                            <em class="icon ni ni-code"></em> <span>Paste JSON</span>
                        </button>
                    </div>
                </div>

                <div class="card-inner p-0" id="faqTableWrapper">
                    <table class="table nk-tb-list nk-tb-ulist mb-0">
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col" style="width: 100px;">Position</th>
                                <th class="nk-tb-col" style="width: 180px;">Category</th>
                                <th class="nk-tb-col" style="width: 30%;">Question</th>
                                <th class="nk-tb-col">Answer</th>
                                <th class="nk-tb-col text-end" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="faqTableBody">
                            <!-- Populated dynamically by JavaScript -->
                        </tbody>
                    </table>

                    <div id="noFaqsPlaceholder" class="p-4 text-center text-muted" style="display: none;">
                        <em class="icon ni ni-help text-muted mb-2" style="font-size: 2.5rem; display: block;"></em>
                        <h6>No FAQs Added Yet</h6>
                        <p class="small text-muted mb-0">Add your first FAQ using the form above or paste JSON content.</p>
                    </div>
                </div>


                {{-- Hidden inputs container for form submission --}}
                <div id="topFaqHiddenInputs"></div>
                <input type="hidden" name="text_sections_present" value="1">
                <input type="hidden" name="top_faqs_present" value="1">

            </div>

            <div class="col-md-12 mt-4 text-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <em class="icon ni ni-save"></em> <span>Update Content</span>
                </button>
            </div>
        </form>

        {{-- Direct Paste JSON Modal --}}
        <div class="modal fade" id="importFaqJsonModal" tabindex="-1" aria-labelledby="importFaqJsonModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <div>
                            <h5 class="modal-title fw-bold text-primary" id="importFaqJsonModalLabel"><em class="icon ni ni-code me-1"></em> Paste FAQs JSON</h5>
                            <p class="text-muted small mb-0">Paste your structured FAQ JSON directly to populate the list.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold mb-0">JSON Data</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-xs btn-secondary" id="loadSampleJsonBtn">Load Sample</button>
                                    <button type="button" class="btn btn-xs btn-light text-muted" id="clearJsonBtn">Clear</button>
                                </div>
                            </div>
                            <textarea id="faqJsonTextarea" class="form-control font-monospace" rows="10" placeholder='[
  {
    "category": "Rankings & Calculation",
    "question": "How are top-rated products chosen?",
    "answer": "Top-rated listings are determined by verified community ratings..."
  },
  {
    "category": "Reviews & Community",
    "question": "How can I submit a review?",
    "answer": "Simply search for the business or visit its page on Localio..."
  }
]' style="font-size: 12.5px;"></textarea>
                            <div id="faqJsonError" class="text-danger small mt-1" style="display: none;"></div>
                        </div>

                        <div class="card bg-light border p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-dark">Supported JSON Format</span>
                                <button type="button" class="btn btn-xs btn-link text-primary p-0 text-decoration-none" id="copySampleJsonBtn"><em class="icon ni ni-copy"></em> Copy Sample</button>
                            </div>
                            <pre class="bg-white p-2 border rounded small mb-0 font-monospace text-muted" style="max-height: 100px; overflow-y: auto; font-size: 11px;">[
  {
    "category": "Rankings & Calculation",
    "question": "How are top-rated products and businesses chosen?",
    "answer": "Top-rated listings are determined by verified community ratings, authentic review scores, and user satisfaction."
  },
  {
    "category": "Reviews & Community",
    "question": "How can I submit a review for a business?",
    "answer": "Simply search for the business or visit its page on Localio, click Write a review, and rate the criteria."
  }
]</pre>
                        </div>

                        <div class="mt-3 d-flex align-items-center gap-3">
                            <label class="form-label fw-bold mb-0">Import Mode:</label>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="faqImportMode" id="importModeReplace" value="replace" checked>
                                <label class="form-check-label" for="importModeReplace">Replace existing FAQs</label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="faqImportMode" id="importModeAppend" value="append">
                                <label class="form-check-label" for="importModeAppend">Append to existing FAQs</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="processFaqJsonBtn">
                            <em class="icon ni ni-check"></em> <span>Import FAQs</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    // Initial FAQs & Categories from database / defaults
    let faqsList = @json($faqs ?? []);
    let faqCategoriesList = [];
    let editingIndex = -1;

    // Collect initial distinct categories from FAQs or defaults
    const defaultInitialCategories = ['Rankings & Calculation', 'Reviews & Community', 'Products & Availability', 'General'];
    defaultInitialCategories.forEach(c => {
        if (!faqCategoriesList.includes(c)) faqCategoriesList.push(c);
    });
    faqsList.forEach(f => {
        if (f.category && f.category.trim() !== '') {
            const catTrim = f.category.trim();
            if (!faqCategoriesList.includes(catTrim)) {
                faqCategoriesList.push(catTrim);
            }
        }
    });

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

    function stripHtml(html) {
        let tmp = document.createElement("DIV");
        tmp.innerHTML = html || '';
        return tmp.textContent || tmp.innerText || "";
    }

    function renderCategoryManager() {
        const container = $('#faqCategoriesContainer');
        const select = $('#faqCategorySelect');
        const currentSelected = select.val() || 'General';

        container.empty();
        select.empty();

        select.append('<option value="General">-- General / Uncategorized --</option>');

        $('#faqCategoryCountBadge').text(faqCategoriesList.length + ' Categories');

        faqCategoriesList.forEach(cat => {
            // Render pill in Category Manager
            const isDefault = cat.toLowerCase() === 'general';
            const pillHtml = `
            <div class="category-pill-tag">
                <span>${$('<div>').text(cat).html()}</span>
                ${!isDefault ? `<button type="button" class="btn-del-cat" data-category="${$('<div>').text(cat).html()}" title="Delete Category"><em class="icon ni ni-cross-sm"></em></button>` : ''}
            </div>`;
            container.append(pillHtml);

            // Render option in Select dropdown
            if (!isDefault) {
                select.append(`<option value="${$('<div>').text(cat).html()}">${$('<div>').text(cat).html()}</option>`);
            }
        });

        select.val(currentSelected);
    }

    function renderFaqTable() {
        const tbody = $('#faqTableBody');
        const hiddenInputs = $('#topFaqHiddenInputs');
        tbody.empty();
        hiddenInputs.empty();

        const total = faqsList.length;
        $('#faqCountBadge').text(total + ' FAQ(s)');

        if (total === 0) {
            $('#noFaqsPlaceholder').show();
            return;
        } else {
            $('#noFaqsPlaceholder').hide();
        }

        faqsList.forEach((faq, idx) => {
            const cat = faq.category ? faq.category.trim() : 'General';
            const q = faq.question ? faq.question.trim() : '';
            const a = faq.answer ? faq.answer.trim() : '';
            const aPreview = stripHtml(a);
            const aShort = aPreview.length > 100 ? aPreview.substring(0, 100) + '...' : aPreview;

            const row = `
            <tr class="nk-tb-item">
                <td class="nk-tb-col">
                    <span class="position-badge">#${idx + 1}</span>
                </td>
                <td class="nk-tb-col">
                    <span class="badge bg-outline-info">${$('<div>').text(cat).html()}</span>
                </td>
                <td class="nk-tb-col">
                    <span class="tb-lead fw-bold text-dark">${$('<div>').text(q).html()}</span>
                </td>
                <td class="nk-tb-col">
                    <span class="tb-sub text-muted">${$('<div>').text(aShort).html()}</span>
                </td>
                <td class="nk-tb-col text-end">
                    <button type="button" class="btn btn-sm btn-icon btn-primary me-1 edit-faq-btn" data-index="${idx}" title="Edit FAQ">
                        <em class="icon ni ni-edit"></em>
                    </button>
                    <button type="button" class="btn btn-sm btn-icon btn-danger delete-faq-btn" data-index="${idx}" title="Delete FAQ">
                        <em class="icon ni ni-trash"></em>
                    </button>
                </td>
            </tr>`;
            tbody.append(row);

            // Generate hidden form input fields for database save
            hiddenInputs.append(`
                <input type="hidden" name="top_faqs[${idx}][category]" value="${$('<div>').text(cat).html()}">
                <input type="hidden" name="top_faqs[${idx}][question]" value="${$('<div>').text(q).html()}">
                <input type="hidden" name="top_faqs[${idx}][answer]" value="${$('<div>').text(a).html()}">
            `);
        });
    }

    function resetFaqForm() {
        editingIndex = -1;
        $('#faqFormTitle').text('Add New FAQ');
        $('#saveFaqBtnText').text('Save FAQ');
        $('#cancelFaqEditBtn').hide();
        $('#faqCategorySelect').val('General');
        $('#faqQuestionInput').val('');
        
        const answerEl = document.getElementById('faqAnswerInput');
        if (answerEl && answerEl.ckeditorInstance) {
            answerEl.ckeditorInstance.setData('');
        } else if (answerEl) {
            answerEl.value = '';
        }
    }

    $(document).ready(function() {
        initAllCKEditors();
        renderCategoryManager();
        renderFaqTable();

        let sectionCounter = {{ isset($textSections) ? count($textSections) : 0 }};

        // Add Category button
        $('#addCategoryBtn').on('click', function() {
            const input = $('#newCategoryNameInput');
            const newCat = input.val().trim();

            if (!newCat) {
                alert('Please enter a category name.');
                input.focus();
                return;
            }

            if (faqCategoriesList.some(c => c.toLowerCase() === newCat.toLowerCase())) {
                alert('Category "' + newCat + '" already exists.');
                input.val('');
                return;
            }

            faqCategoriesList.push(newCat);
            input.val('');
            renderCategoryManager();
            $('#faqCategorySelect').val(newCat);
        });

        // Delete Category Pill
        $(document).on('click', '.btn-del-cat', function() {
            const catToDelete = $(this).data('category');
            if (confirm('Delete category "' + catToDelete + '"? Existing FAQs in this category will default to General.')) {
                faqCategoriesList = faqCategoriesList.filter(c => c !== catToDelete);
                // Update existing FAQs that had this category
                faqsList.forEach(f => {
                    if (f.category === catToDelete) f.category = 'General';
                });
                renderCategoryManager();
                renderFaqTable();
            }
        });

        // Add Text Section (H2)
        $('#addTextSectionBtn').on('click', function() {
            let sIdx = Date.now();
            let html = `
            <div class="section-card p-3 mb-3" data-section-index="${sIdx}">
                <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2 py-1" style="font-size: 12px;">Section #${sIdx + 1}</span>
                        <span class="fw-bold text-dark small">New Section</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger remove-section-btn py-1 px-2" title="Delete Section">
                        <em class="icon ni ni-trash"></em> <span>Delete</span>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold mb-1">Headline (H2)</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" name="text_sections[${sIdx}][h2_title]" placeholder="e.g. How Localio ratings work">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold mb-1">Headline Content (Rich Editor)</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control rich-editor" rows="4" name="text_sections[${sIdx}][h2_text]" placeholder="Enter main content for this H2 section..."></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-secondary mb-0">Sub-sections (H3 Sub-headlines)</label>
                            <button type="button" class="btn btn-xs btn-primary add-sub-section-btn" data-section-index="${sIdx}"><em class="icon ni ni-plus"></em> Add Sub-headline (H3)</button>
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
            let subIdx = Date.now();
            let subHtml = `
            <div class="sub-section-item border rounded p-3 mb-2 bg-light" data-sub-index="${subIdx}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-muted">Sub-headline (H3)</span>
                    <button type="button" class="btn btn-xs btn-danger remove-sub-btn"><em class="icon ni ni-cross"></em> Remove</button>
                </div>
                <div class="form-group mb-2">
                    <input type="text" class="form-control form-control-sm" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_title]" placeholder="e.g. Why are these listings considered top rated?">
                </div>
                <div class="form-group mb-2">
                    <textarea class="form-control rich-editor" rows="3" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_text]" placeholder="Enter sub-headline content..."></textarea>
                </div>
                
                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label fw-bold">Image</label>
                            <div class="form-control-wrap">
                                <div class="form-file">
                                    <input type="file" class="form-file-input" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_image]" accept="image/*">
                                    <label class="form-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label fw-bold">Image Position</label>
                            <div class="form-control-wrap">
                                <select class="form-select" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_image_position]">
                                    <option value="Left" selected>Left</option>
                                    <option value="Right">Right</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label fw-bold">Image Width (px or %)</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control form-control-sm" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_image_size]" value="50px" placeholder="e.g. 50px">
                            </div>
                        </div>
                    </div>
                </div>
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

        // Save / Update FAQ button click
        $('#saveFaqBtn').on('click', function() {
            const cat = $('#faqCategorySelect').val() || 'General';
            const q = $('#faqQuestionInput').val().trim();
            const answerEl = document.getElementById('faqAnswerInput');
            const a = answerEl && answerEl.ckeditorInstance ? answerEl.ckeditorInstance.getData().trim() : $(answerEl).val().trim();

            if (!q) {
                alert('Please enter a question.');
                $('#faqQuestionInput').focus();
                return;
            }

            if (editingIndex >= 0 && editingIndex < faqsList.length) {
                // Update existing
                faqsList[editingIndex] = {
                    category: cat,
                    question: q,
                    answer: a
                };
            } else {
                // Add new
                faqsList.push({
                    category: cat,
                    question: q,
                    answer: a
                });
            }

            resetFaqForm();
            renderFaqTable();
        });

        // Edit FAQ row
        $(document).on('click', '.edit-faq-btn', function() {
            const idx = parseInt($(this).data('index'), 10);
            if (idx >= 0 && idx < faqsList.length) {
                editingIndex = idx;
                const faq = faqsList[idx];

                $('#faqFormTitle').text('Edit FAQ #' + (idx + 1));
                $('#saveFaqBtnText').text('Update FAQ');
                $('#cancelFaqEditBtn').show();
                
                const cat = faq.category || 'General';
                if (!faqCategoriesList.includes(cat)) {
                    faqCategoriesList.push(cat);
                    renderCategoryManager();
                }
                $('#faqCategorySelect').val(cat);
                $('#faqQuestionInput').val(faq.question || '');

                const answerEl = document.getElementById('faqAnswerInput');
                if (answerEl && answerEl.ckeditorInstance) {
                    answerEl.ckeditorInstance.setData(faq.answer || '');
                } else if (answerEl) {
                    answerEl.value = faq.answer || '';
                }

                // Scroll smoothly to the form
                document.getElementById('faqFormCard').scrollIntoView({ behavior: 'smooth' });
            }
        });

        // Cancel FAQ Edit
        $('#cancelFaqEditBtn').on('click', function() {
            resetFaqForm();
        });

        // Delete FAQ row
        $(document).on('click', '.delete-faq-btn', function() {
            const idx = parseInt($(this).data('index'), 10);
            if (idx >= 0 && idx < faqsList.length) {
                if (confirm('Are you sure you want to delete this FAQ?')) {
                    if (editingIndex === idx) {
                        resetFaqForm();
                    } else if (editingIndex > idx) {
                        editingIndex--;
                    }
                    faqsList.splice(idx, 1);
                    renderFaqTable();
                }
            }
        });

        // Sample JSON Template
        const sampleFaqJson = `[
  {
    "category": "Rankings & Calculation",
    "question": "How are top-rated products and businesses chosen?",
    "answer": "Top-rated listings are determined by verified community ratings, authentic review scores, user satisfaction metrics, and overall reliability."
  },
  {
    "category": "Rankings & Calculation",
    "question": "How often are the top-rated rankings updated?",
    "answer": "Our rankings are updated continuously as new community reviews, verified feedback, and rating submissions are received."
  },
  {
    "category": "Reviews & Community",
    "question": "How can I submit a review for a business?",
    "answer": "Simply search for the business or visit its page on Localio, click Write a review, rate the criteria, and share your experience."
  },
  {
    "category": "General & Availability",
    "question": "Are all listed products available in my region?",
    "answer": "Most top-rated products and businesses provide regional availability indicators and localized support information on their detail pages."
  }
]`;

        $('#loadSampleJsonBtn').on('click', function() {
            $('#faqJsonTextarea').val(sampleFaqJson);
            $('#faqJsonError').hide().text('');
        });

        $('#clearJsonBtn').on('click', function() {
            $('#faqJsonTextarea').val('');
            $('#faqJsonError').hide().text('');
        });

        $('#copySampleJsonBtn').on('click', function() {
            navigator.clipboard.writeText(sampleFaqJson).then(function() {
                alert('Sample JSON copied to clipboard!');
            });
        });

        // Process Direct JSON Import
        $('#processFaqJsonBtn').on('click', function() {
            const rawJson = $('#faqJsonTextarea').val().trim();
            const errorDiv = $('#faqJsonError');
            errorDiv.hide().text('');

            if (!rawJson) {
                errorDiv.text('Please paste JSON data in the box above.').show();
                return;
            }

            let parsed;
            try {
                parsed = JSON.parse(rawJson);
            } catch (err) {
                errorDiv.text('Invalid JSON syntax: ' + err.message).show();
                return;
            }

            let normalizedFaqs = [];
            if (Array.isArray(parsed)) {
                parsed.forEach(item => {
                    if (item.category && Array.isArray(item.faqs)) {
                        item.faqs.forEach(sub => {
                            if (sub.question || sub.answer) {
                                const cat = (item.category || 'General').trim();
                                if (!faqCategoriesList.includes(cat)) faqCategoriesList.push(cat);
                                normalizedFaqs.push({
                                    category: cat,
                                    question: sub.question || '',
                                    answer: sub.answer || ''
                                });
                            }
                        });
                    } else if (item.question || item.answer) {
                        const cat = (item.category || 'General').trim();
                        if (!faqCategoriesList.includes(cat)) faqCategoriesList.push(cat);
                        normalizedFaqs.push({
                            category: cat,
                            question: item.question || '',
                            answer: item.answer || ''
                        });
                    }
                });
            } else if (typeof parsed === 'object' && parsed !== null) {
                Object.keys(parsed).forEach(catKey => {
                    const val = parsed[catKey];
                    if (Array.isArray(val)) {
                        const cat = (catKey || 'General').trim();
                        if (!faqCategoriesList.includes(cat)) faqCategoriesList.push(cat);
                        val.forEach(sub => {
                            if (sub.question || sub.answer) {
                                normalizedFaqs.push({
                                    category: cat,
                                    question: sub.question || '',
                                    answer: sub.answer || ''
                                });
                            }
                        });
                    }
                });
            }

            if (normalizedFaqs.length === 0) {
                errorDiv.text('No valid FAQs found in JSON. Expected format: [{"category": "...", "question": "...", "answer": "..."}]').show();
                return;
            }

            const importMode = $('input[name="faqImportMode"]:checked').val();

            if (importMode === 'replace') {
                faqsList = normalizedFaqs;
            } else {
                faqsList = faqsList.concat(normalizedFaqs);
            }

            renderCategoryManager();
            resetFaqForm();
            renderFaqTable();

            const modalEl = document.getElementById('importFaqJsonModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modalInstance.hide();

            alert(`Successfully imported ${normalizedFaqs.length} FAQs into the list! Click "Update Content" below to save.`);
        });

        // Export JSON
        $('#exportFaqJsonBtn').on('click', function() {
            if (faqsList.length === 0) {
                alert('No FAQs to export.');
                return;
            }

            const jsonStr = JSON.stringify(faqsList, null, 2);
            navigator.clipboard.writeText(jsonStr).then(function() {
                alert('All FAQs copied to clipboard as JSON!');
            }).catch(function() {
                $('#faqJsonTextarea').val(jsonStr);
                const modalEl = document.getElementById('importFaqJsonModal');
                const modalInstance = new bootstrap.Modal(modalEl);
                modalInstance.show();
            });
        });

        // Form Submit: Ensure all rich editors sync data
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
